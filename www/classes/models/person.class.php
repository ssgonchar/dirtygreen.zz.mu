<?php
require_once APP_PATH . 'classes/models/city.class.php';
require_once APP_PATH . 'classes/models/company.class.php';
require_once APP_PATH . 'classes/models/country.class.php';
require_once APP_PATH . 'classes/models/department.class.php';
require_once APP_PATH . 'classes/models/jobposition.class.php';
require_once APP_PATH . 'classes/models/picture.class.php';
require_once APP_PATH . 'classes/models/region.class.php';
require_once APP_PATH . 'classes/models/user.class.php';

if (!class_exists('SphinxClient'))
{
    require_once APP_PATH . 'classes/services/sphinx/sphinxapi.php';
}

class Person extends Model
{
    function Person()
    {
        Model::Model('persons');
    }

    /**
     * Возвращает список работников компании
     * 
     */
    function GetMamList()
    {
        $users  = new User();
        return $users->GetMamList();
    }
    
    /**
     * Заполняет контакты человека
     * 
     * @param mixed $rowset
     * @param mixed $id_fieldname
     * @param mixed $entityname
     * @param mixed $cache_prefix
     */
    function FillPersonContacts($rowset, $id_fieldname = 'person_id', $entityname = 'personcontacts', $cache_prefix = 'personcontacts')
    {
        $rowset = $this->_fill_entity_array_info($rowset, $id_fieldname, $entityname, $cache_prefix, 'sp_person_get_contacts_by_ids', array('persons' => '', 'person' => 'id'), array());
        
        $activities = new Activity();
        foreach($rowset as $key => $row)
        {
            if (isset($row[$entityname]) && !empty($row[$entityname]))
            {
                $rowset[$key][$entityname] = $activities->FillActivityBaseInfo($row[$entityname]);
            }
        }
        
        return $rowset;
    }    
    
    /**
     * Обновляет аватарку человека
     * 
     * @param mixed $person_id
     * @param mixed $params
     */
    function UpdatePicture($person_id, $picture_id)
    {
        $this->Update($person_id, array('picture_id' => $picture_id));
        Cache::ClearTag('person-' . $person_id);
        
        $users  = new User();
        $user   = $users->GetByPersonId($person_id);

        if (isset($user) && isset($user['user']))
        {            
            Cache::SetKey('reload-user-' . $user['user']['id'], true);
        }
    }
    
    /**
     * Возвращает список людей
     * 
     * @param mixed $keyword
     * @version 20120503, zharkov
     */
    function Search($search_string, $company_id, $page_no = 0, $per_page = ITEMS_PER_PAGE)
    {
        $page_no    = $page_no > 0 ? $page_no : 1;
        $per_page   = $per_page < 1 ? ITEMS_PER_PAGE : $per_page;
        $start      = ($page_no - 1) * $per_page;

        $hash   = 'persons-search-' . md5($search_string . 'company-' . $company_id . '-' . $page_no . '-' . $per_page);
        $rowset = Cache::GetData($hash);

        if (!isset($rowset) || !isset($rowset['data']) || isset($rowset['outdated']))
        {
            $cl = new SphinxClient();
            $cl->SetLimits($start, $per_page);
            $cl->SetFieldWeights(array(
                'first_name'        => 10, 
                'last_name'         => 100, 
                'company_title'     => 100, 
                'contactdata'       => 10, 
                'department_title'  => 10, 
                'jobposition_title' => 10, 
                'country_title'     => 100, 
                'region_title'      => 100, 
                'city_title'        => 100
            ));
            
            $cl->SetMatchMode(SPH_MATCH_ALL);
            $cl->SetIndexWeights(array('ix_mam_persons_morphology' => 100, 'ix_mam_persons' => 10, 'ix_mam_persons_morphology_delta' => 100, 'ix_mam_persons_delta' => 10));
            
            if (!empty($company_id)) $cl->SetFilter('company_id', array($company_id));
            if (!empty($search_string)) $search_string = '*' . str_replace(' ', '* *', $search_string) . '*';
            
            $data = $cl->Query($search_string, 'ix_mam_persons, ix_mam_persons_delta, ix_mam_persons_morphology, ix_mam_persons_morphology_delta');

            if ($data === false)
            {
                Log::AddLine(LOG_ERROR, 'product::search ' . $cl->GetLastError());
                return null;
            }
            
            $rowset = array(); 
            if (!empty($data['matches']))
            {
                foreach ($data['matches'] as $id => $extra)
                {
                    $rowset[] = array('person_id' => $id);
                }
            }
            
            $rowset = array(
                $rowset,
                array(array('rows' => $data['total_found']))
            );
            
            Cache::SetData($hash, $rowset, array('persons', 'search'), CACHE_LIFETIME_STANDARD);
            
            $rowset = array(
                'data' => $rowset
            );
        }

        $result = array();
        $result['data']  = isset($rowset['data'][0]) ? $this->FillPersonContacts($this->FillPersonInfo($rowset['data'][0])) : null;
        $result['count'] = isset($rowset['data'][1]) && isset($rowset['data'][1][0]) && isset($rowset['data'][1][0]['rows']) ? $rowset['data'][1][0]['rows'] : 0;
        
        return $result;
    }
    
    /**
     * Сохраняет человека
     * 
     * @param mixed $id
     * @param mixed $title
     * @param mixed $first_name
     * @param mixed $middle_name
     * @param mixed $last_name
     * @param mixed $name_for_label
     * @param mixed $company_id
     * @param mixed $department_id
     * @param mixed $jobposition_id
     * @param mixed $phone1
     * @param mixed $phone2
     * @param mixed $phone3
     * @param mixed $fax
     * @param mixed $email
     * @param mixed $skype
     * @param mixed $facebook
     * @param mixed $country_id
     * @param mixed $region_id
     * @param mixed $city_id
     * @param mixed $zip
     * @param mixed $address
     * @param mixed $languages
     * @param mixed $notes
     * @param mixed $birthday
     * @return resource
     * 
     * @version 20120501, zharkov
     */
    function Save($id, $title, $first_name, $middle_name, $last_name, $name_for_label, $company_id, $department_id, 
                    $jobposition_id, $country_id, $region_id, $city_id, $zip, $address, $languages, $notes, $birthday, 
                    $gender = '')
    {
        $alias = $this->_get_title_src($first_name . $last_name, "[^a-zA-Z]+");
        
        $result = $this->CallStoredProcedure('sp_person_save', array($this->user_id, $id, $alias, $title, 
                    $first_name, $middle_name, $last_name, $name_for_label, $company_id, $department_id, 
                    $jobposition_id, $country_id, $region_id, $city_id, $zip, $address, $languages, 
                    $notes, $birthday, $gender));
        $result = isset($result) && isset($result[0]) && isset($result[0][0]) ? $result[0][0] : null;
        
        if (empty($result) || array_key_exists('ErrorCode', $result)) return null;

        Cache::ClearTag('person-' . $result['id']);
        Cache::ClearTag('persons');
        
        return $result;        
    }

    /**
     * Возвращает человека по идентификатору
     *     
     * @param mixed $id
     */
    function GetById($id)
    {
        $rowset = $this->FillPersonInfo(array(array('person_id' => $id)));
        return isset($rowset) && isset($rowset[0]) && isset($rowset[0]['person']) ? $rowset[0] : null;
    }
    
    /**
     * Заполняет основную информацию о человеке
     * 
     * @param mixed $rowset
     * @param mixed $id_fieldname
     * @param mixed $entityname
     * @param mixed $cache_prefix
     */
    function FillPersonMainInfo($rowset, $id_fieldname = 'person_id', $entityname = 'person', $cache_prefix = 'person')
    {
        $rowset = $this->_fill_entity_info($rowset, $id_fieldname, $entityname, $cache_prefix, 'sp_person_get_list_by_ids', array('persons' => ''), array());
        
        foreach ($rowset as $key => $row)
        {
            if (isset($row[$entityname]))
            {
                $row = $row[$entityname];                
                $rowset[$key][$entityname]['full_name']     = ucfirst($row['title']) . ' ' . $row['first_name'] . ' ' . $row['last_name'];
                $rowset[$key][$entityname]['doc_no']        = ucfirst($row['title']) . ' ' . $row['first_name'] . ' ' . $row['last_name'];
                $rowset[$key][$entityname]['doc_no_short']  = $row['first_name'] . ' ' . $row['last_name'];
            }
        }
        
        return $rowset;        
    }
    
    /**
     * Возвращет информацию о человеке
     * 
     * @param mixed $recordset
     * @param mixed $id_fieldname
     * @param mixed $entityname
     * @param mixed $cache_prefix
     */
    function FillPersonInfo($rowset, $id_fieldname = 'person_id', $entityname = 'person', $cache_prefix = 'person')
    {
        //$rowset = $this->_fill_entity_info($rowset, $id_fieldname, $entityname, $cache_prefix, 'sp_person_get_list_by_ids', array('persons' => ''), array());
        $rowset = $this->FillPersonMainInfo($rowset, $id_fieldname, $entityname, $cache_prefix);
        
        foreach ($rowset as $key => $row)
        {
            if (isset($row[$entityname]))
            {
                $row = $row[$entityname];
                
                $rowset[$key][$entityname]['full_name'] = ucfirst($row['title']) . ' ' . $row['first_name'] . ' ' . $row['last_name'];
                $rowset[$key]['person_company_id']      = $row['company_id'];
                $rowset[$key]['person_department_id']   = $row['department_id'];
                $rowset[$key]['person_jobposition_id']  = $row['jobposition_id'];
                $rowset[$key]['person_country_id']      = $row['country_id'];
                $rowset[$key]['person_region_id']       = $row['region_id'];
                $rowset[$key]['person_city_id']         = $row['city_id'];
                if (!empty($row['created_by'])) $rowset[$key]['person_creater_id']      = $row['created_by'];
                $rowset[$key]['person_modifier_id']     = $row['modified_by'];
                $rowset[$key]['person_picture_id']      = $row['picture_id'];
            }
        }

        $company        = new Company();
        $rowset         = $company->FillCompanyInfoShort($rowset, 'person_company_id', 'person_company');

        $department     = new Department();
        $rowset         = $department->FillDepartmentInfo($rowset, 'person_department_id', 'person_department');
        
        $jobposition    = new JobPosition();
        $rowset         = $jobposition->FillJobPositionInfo($rowset, 'person_jobposition_id', 'person_jobposition');

        $country        = new Country();
        $rowset         = $country->FillCountryInfo($rowset, 'person_country_id', 'person_country');

        $region         = new Region();
        $rowset         = $region->FillRegionInfo($rowset, 'person_region_id', 'person_region');

        $city           = new City();
        $rowset         = $city->FillCityInfo($rowset, 'person_city_id', 'person_city');

        $user           = new User();
        $rowset         = $user->FillUserInfo($rowset, 'person_creater_id', 'person_creater');
        $rowset         = $user->FillUserInfo($rowset, 'person_modifier_id', 'person_modifier');
                
        $pictures       = new Picture();
        $rowset         = $pictures->FillAttachmentInfo($rowset, 'person_picture_id', 'person_picture');

        foreach ($rowset as $key => $row)
        {
            if (isset($row[$entityname]))
            {
                if (isset($row['person_company']) && !empty($row['person_company'])) 
                {
                    $rowset[$key][$entityname]['company']       = $row['person_company'];
                    $rowset[$key][$entityname]['key_contact']   = $row['person_company']['key_contact_id'] == $row[$entityname]['id'] ? 1 : 0;
                    
                }
                unset($rowset[$key]['person_company']);                                    
                unset($rowset[$key]['person_company_id']);

                if (isset($row['person_department']) && !empty($row['person_department'])) $rowset[$key][$entityname]['department'] = $row['person_department'];
                unset($rowset[$key]['person_department']);
                unset($rowset[$key]['person_department_id']);

                if (isset($row['person_jobposition']) && !empty($row['person_jobposition'])) $rowset[$key][$entityname]['jobposition'] = $row['person_jobposition'];
                unset($rowset[$key]['person_jobposition']);
                unset($rowset[$key]['person_jobposition_id']);
                
                if (isset($row['person_country']) && !empty($row['person_country'])) $rowset[$key][$entityname]['country'] = $row['person_country'];
                unset($rowset[$key]['person_country']);
                unset($rowset[$key]['person_country_id']);

                if (isset($row['person_region']) && !empty($row['person_region'])) $rowset[$key][$entityname]['region'] = $row['person_region'];
                unset($rowset[$key]['person_region']);
                unset($rowset[$key]['person_region_id']);
                
                if (isset($row['person_city']) && !empty($row['person_city'])) $rowset[$key][$entityname]['city'] = $row['person_city'];
                unset($rowset[$key]['person_city']);
                unset($rowset[$key]['person_city_id']);                
                
                if (isset($row['person_creater']) && !empty($row['person_creater'])) $rowset[$key][$entityname]['author'] = $row['person_creater'];
                unset($rowset[$key]['person_creater']);
                unset($rowset[$key]['person_creater_id']);                

                if (isset($row['person_modifier']) && !empty($row['person_modifier'])) $rowset[$key][$entityname]['modifier'] = $row['person_modifier'];
                unset($rowset[$key]['person_modifier']);
                unset($rowset[$key]['person_modifier_id']);                

                if (isset($row['person_picture']) && !empty($row['person_picture'])) $rowset[$key][$entityname]['picture'] = $row['person_picture'];
                unset($rowset[$key]['person_picture']);
                unset($rowset[$key]['person_picture_id']);
                
            }
        }

        //        $rowset = $this->FillQuickInfo($rowset, $id_fieldname, $entityname);
        
        return $rowset;
    }
    
    
    /**
     * Возвращает список персон по Фамилии Имени Отчеству
     * 
     */
    public function GetListByFIO($fio, $rows_count)
    {
        $hash       = 'persons-fio-' . $fio . '-rowscount-' . $rows_count;
        $cache_tags = array($hash, 'persons');

        $rowset = $this->_get_cached_data($hash, 'sp_person_get_list_by_fio', array($fio, $rows_count), $cache_tags);
        $rowset = isset($rowset[0]) ? $this->FillPersonInfo($rowset[0]) : array();

        return $rowset;
    }
}
