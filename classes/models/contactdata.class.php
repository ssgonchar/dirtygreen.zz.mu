<?php
require_once APP_PATH . 'classes/models/company.class.php';
require_once APP_PATH . 'classes/models/person.class.php';


class ContactData extends Model
{
    function ContactData()
    {
        Model::Model('contactdata');
    }

    /**
     * Возвращает контактные данные по типу и заголовку
     * 
     * @param mixed $type
     * @param mixed $title
     * 
     * @version 20120804, zharkov
     */
    function GetByTypeAndTitle($type, $title)
    {
        $hash       = 'contactdata-type-' . $type . '-title-' . $title;
        $cache_tags = array($hash, 'contactdata');

        $rowset     = $this->_get_cached_data($hash, 'sp_contactdata_get_by_type_and_title', array($this->user_id, $type, $title), $cache_tags);

        return isset($rowset[0]) ? $rowset[0] : array();
    }
 
    /**
     * Возвращает данные по идентификатору
     * 
     * @param mixed $id
     * 
     * @version 20120803, zharkov
     */
    function GetById($id)
    {
        $dataset = $this->FillContactdataInfo(array(array('contactdata_id' => $id)));
        return isset($dataset) && isset($dataset[0]) && isset($dataset[0]['contactdata']) ? $dataset[0] : null;
        
    }
    
    /**
     * Заполняет контактные данные
     * 
     * @param mixed $rowset
     * @param mixed $id_fieldname
     * @param mixed $entityname
     * @return array
     * 
     * @version 20120803, zharkov
     */
    function FillContactdataInfo($rowset, $id_fieldname = 'contactdata_id', $entityname = 'contactdata')
    {
        return $this->_fill_entity_info($rowset, $id_fieldname, $entityname, 'contactdata', 'sp_contactdata_get_list_by_ids', array('contactdata' => ''), array());        
    }
    
    /**
     * Ищет адреса email по имени объекта
     * 
     * @param mixed $keyword
     * @param mixed $page_no
     * @param mixed $per_page
     * @param mixed $strict - ищет точное соответствие
     * @return mixed
     */
    function FindEmail($keyword, $page_no = 0, $per_page = ITEMS_PER_PAGE, $strict = false)
    {
        $page_no    = $page_no > 0 ? $page_no : 1;
        $per_page   = $per_page < 1 ? ITEMS_PER_PAGE : $per_page;
        $start      = ($page_no - 1) * $per_page;

        $hash       =   'contactdata-emails-' . md5($keyword . '-page' . $page_no . '-' . $per_page . '-strict-' . (empty($strict) ? '0' : 1));
        $rowset     = Cache::GetData($hash);

        if (!isset($rowset) || !isset($rowset['data']) || isset($rowset['outdated']))
        {
            $cl = new SphinxClient();
            $cl->SetLimits($start, $per_page);
            $cl->SetFieldWeights(array(
                'email'         => 100,
                'person_title'  => 10,
                'company_title' => 1
            ));

            if (empty($strict))
            {
                $cl->SetMatchMode(SPH_MATCH_ALL);
                if (!empty($keyword)) $keyword = '*' . str_replace('-', '\-', str_replace(' ', '* *', $keyword)) . '*';
            }
            else
            {
                $cl->SetMatchMode(SPH_MATCH_PHRASE);
                if (!empty($keyword)) $keyword = '^' . $cl->EscapeString($keyword) . '$';
            }

            $data = $cl->Query($keyword, 'ix_mam_contact_emails, ix_mam_contact_emails_delta');

            if ($data === false)
            {
                Log::AddLine(LOG_ERROR, 'Message::search ' . $cl->GetLastError());
                return null;
            }

            $rowset = array(); 
            if (!empty($data['matches']))
            {
                foreach ($data['matches'] as $id => $extra)
                {
                    $rowset[] = array(
                        'id'                => $extra['attrs']['contactdata_id'],
                        'contactdata_id'    => $extra['attrs']['contactdata_id'],
                        'person_id'         => $extra['attrs']['person_id'],
                        'company_id'        => $extra['attrs']['company_id']
                    );
                }
            }

            Cache::SetData($hash, $rowset, array('contactdata'), CACHE_LIFETIME_STANDARD);            
            $rowset = array('data' => $rowset);
        }

        $persons    = new Person();
        $companies  = new Company();
        $rowset     = isset($rowset['data']) ? $persons->FillPersonMainInfo($this->FillContactdataInfo($rowset['data'])) : array();
        
        foreach ($rowset as $key => $row)
        {
            if (isset($row['person']))
            {
                $rowset[$key]['title']      = '"' . $row['person']['full_name'] . '" <' . trim($row['contactdata']['title']) . '>';
                $rowset[$key]['company_id'] = $row['person']['company_id'];
            }
            else if (isset($row['company']))
            {
                $rowset[$key]['title'] = '"' . $row['company']['title'] . '" <' . trim($row['contactdata']['title']) . '>';
            }
            else
            {
                $rowset[$key]['title'] = $row['contactdata']['title'];
            }
        }
       
        return $companies->FillCompanyInfoShort($rowset);
    }
    
    /**
     * Сохраняет список контактных данных
     * 
     * @param mixed $object_alias
     * @param mixed $object_id
     * @param mixed $data
     * 
     * @version 20120530, zharkov
     */
    function SaveList($object_alias, $object_id, $rowset)
    {
        $flag = false;
        if (empty($rowset)) $flag = true;
        
        // Удаление ненужных значений
        foreach ($this->GetList($object_alias, $object_id) as $row)
        {
            $remove_flag = true;            
            foreach ($rowset as $row1)
            {
                if ($row['id'] == $row1['id'])
                {
                    $remove_flag = false;
                    break;                    
                }
            }
            
            if ($remove_flag || $flag) $this->Remove($row['id']);
        }
        
        // Добавление новых значений
        foreach ($rowset as $row)
        {
            if (!empty($row['title']))
			{
				$id             = Request::GetInteger('id', $row);
				$type           = Request::GetString('type', $row);
				$title          = Request::GetString('title', $row);
				$description    = Request::GetString('description', $row);

				$this->Save($id, $object_alias, $object_id, $type, $title, $description);
			}
        }        
    }
    
    /**
     * Возвращает список контактных данных
     * 
     * @param mixed $object_alias
     * @param mixed $object_id
     * 
     * @version 20120502, zharkov
     */
    function GetList($object_alias, $object_id)
    {
        $hash       = 'contactdata-objectalias-' . $object_alias . '-objectid-' . $object_id;
        $cache_tags = array($hash, 'contactdata', $object_alias . '-' . $object_id);

        $rowset     = $this->_get_cached_data($hash, 'sp_contactdata_get_list', array($this->user_id, $object_alias, $object_id), $cache_tags);
        $rowset     = isset($rowset[0]) ? $rowset[0] : array();
        
        foreach ($rowset as $key => $row)
        {
            if ($row['type'] == 'www')
            {
                $type_text = 'Website';
            }
            else if ($row['type'] == 'email')
            {
                $type_text = 'Email';
            }
            else if ($row['type'] == 'icq')
            {
                $type_text = 'ICQ';
            }
            else if ($row['type'] == 'skype')
            {
                $type_text = 'Skype';
            }
            else if ($row['type'] == 'msn')
            {
                $type_text = 'MSN';
            }
            else if ($row['type'] == 'aim')
            {
                $type_text = 'AIM';
            }
            else if ($row['type'] == 'gt')
            {
                $type_text = 'Google Talk';
            }
            else if ($row['type'] == 'phone')
            {
                $type_text = 'Phone';
            }
            else if ($row['type'] == 'pfax')
            {
                $type_text = 'Phone / Fax';
            }
            else if ($row['type'] == 'fax')
            {
                $type_text = 'Fax';
            }
            else if ($row['type'] == 'telex')
            {
                $type_text = 'Telex';
            }
            else if ($row['type'] == 'cell')
            {
                $type_text = 'Cell Phone';
            }
            else if ($row['type'] == 'ttype')
            {
                $type_text = 'Teletype';
            }
            else if ($row['type'] == 'fb')
            {
                $type_text = 'FaceBook';
            }
            else if ($row['type'] == 'bbm')
            {
                $type_text = 'BBM';
            }
            else if ($row['type'] == 'qq')
            {
                $type_text = 'QQ';
            }
            
            $rowset[$key]['type_text'] = $type_text;
        }
        
        return $rowset;
    }    
    
    /**
     * Сохраняет контактную информацию
     * 
     * @param mixed $id
     * @param mixed $object_alias
     * @param mixed $object_id
     * @param mixed $type
     * @param mixed $title
     * @param mixed $description
     * 
     * @version 20120502, zharkov
     */
    function Save($id, $object_alias, $object_id, $type, $title, $description = '', $is_private = 0)
    {        
        $this->CallStoredProcedure('sp_contactdata_save', array($this->user_id, $id, $object_alias, $object_id, $type, $title, $description, $is_private));

        Cache::ClearTag('contactdata-objectalias-' . $object_alias . '-objectid-' . $object_id);
        Cache::ClearTag($object_alias . '-' . $object_id);
        Cache::ClearTag('contactdata-' . $id);
        Cache::ClearTag('contactdata');
    }    
    
    /**
     * Удаляет контактную информацию
     * 
     * @param mixed $id
     * 
     * @version 20120502, zharkov
     */
    function Remove($id)
    {        
        $result = $this->CallStoredProcedure('sp_contactdata_remove', array($this->user_id, $id));
        $result = isset($result[0]) && isset($result[0][0]) ? $result[0][0] : array();
        
        if (!empty($result))
        {
            Cache::ClearTag('contactdata-objectalias-' . $result['object_alias'] . '-objectid-' . $result['object_id']);
            Cache::ClearTag($result['object_alias'] . '-' . $result['object_id']);
            Cache::ClearTag('contactdata');
        }
    }
}
