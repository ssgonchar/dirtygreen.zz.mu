<?php
require_once APP_PATH . 'classes/models/activity.class.php';
require_once APP_PATH . 'classes/models/biz.class.php';
require_once APP_PATH . 'classes/models/city.class.php';
require_once APP_PATH . 'classes/models/contactdata.class.php';
require_once APP_PATH . 'classes/models/country.class.php';
require_once APP_PATH . 'classes/models/location.class.php';
require_once APP_PATH . 'classes/models/order.class.php';
require_once APP_PATH . 'classes/models/person.class.php';
require_once APP_PATH . 'classes/models/product.class.php';
require_once APP_PATH . 'classes/models/region.class.php';
require_once APP_PATH . 'classes/models/user.class.php';

// типы компаний
define('CO_TYPE_ALL',       -1);// для выборки списков
define('CO_TYPE_HOFFICE',   1);
define('CO_TYPE_OFFICE',    2);
define('CO_TYPE_PLANT',     3);
define('CO_TYPE_SUBSIDIARY',4);

// статусы компаний
define('CO_STATUS_ALL',             -1);// для выборки списков
define('CO_STATUS_BANKRUPT',        1);
define('CO_STATUS_BLACK_LIST',      2);
define('CO_STATUS_CONTRACT',        3);
define('CO_STATUS_DONT_WANT_US',    4);
define('CO_STATUS_GONE_AWAY',       5);
define('CO_STATUS_KEY_PARTNER',     6);
define('CO_STATUS_LIQUIDATED',      7);
define('CO_STATUS_NEGOTIATION',     8);
define('CO_STATUS_NOT_DIALOG_YET',  9);

// связи компаний
define('CO_RELATION_ALL',                       -1);// для выборки списков
define('CO_RELATION_MUST_HAVE',                 1);
define('CO_RELATION_COMPETITOR',                2);
define('CO_RELATION_LIVE_CUSTOMER',             3);
define('CO_RELATION_NOT_POTENTIAL_CUSTOMER',    4);
define('CO_RELATION_POTENTIAL_CUSTOMER',        5);
define('CO_RELATION_SERVICE_PROVIDER',          6);
define('CO_RELATION_STOCK_AGENT',               7);
define('CO_RELATION_SUPPLIER',                  8);


class Company extends Model
{
    function Company()
    {
        Model::Model('companies');
    }

    /**
     * Возвращает список типов
     * @return array
     * @version 2012-09-05 d10n
     */
    public function GetCoTypesList()
    {
        return array(
            array('id' => CO_TYPE_HOFFICE,      'name' => 'Head Office'),
            array('id' => CO_TYPE_OFFICE,       'name' => 'Office'),
            array('id' => CO_TYPE_PLANT,        'name' => 'Plant'),
            array('id' => CO_TYPE_SUBSIDIARY,   'name' => 'Subsidiary'),
        );
    }
    
    /**
     * Проверяет существование определенных CO_TYPE_
     * @param int $co_type_id
     * @return boolean
     * @version 2012-09-05 d10n
     */
    public function IsCoTypeExists($co_type_id)
    {
        switch ($co_type_id)
        {
            case CO_TYPE_HOFFICE:
            case CO_TYPE_OFFICE:
            case CO_TYPE_PLANT:
            case CO_TYPE_SUBSIDIARY:
                return TRUE;
                
            default:
                return FALSE;
        }
    }
    
    /**
     * Возвращает список статусов
     * @return array
     * @version 2012-09-05 d10n
     */
    public function GetCoStatusesList()
    {
        return array(
            array('id' => CO_STATUS_BANKRUPT,       'name' => 'Bankrupt'),
            array('id' => CO_STATUS_BLACK_LIST,     'name' => 'Black List'),
            array('id' => CO_STATUS_CONTRACT,       'name' => 'Contract'),
            array('id' => CO_STATUS_DONT_WANT_US,   'name' => 'Don\'t Want Us'),
            array('id' => CO_STATUS_GONE_AWAY,      'name' => 'Gone Away'),
            array('id' => CO_STATUS_KEY_PARTNER,    'name' => 'Key Partner'),
            array('id' => CO_STATUS_LIQUIDATED,     'name' => 'Liquidated'),
            array('id' => CO_STATUS_NEGOTIATION,    'name' => 'Negotiation'),
            array('id' => CO_STATUS_NOT_DIALOG_YET, 'name' => 'Not Dialogue Yet'),
        );
    }
    
    /**
     * Возвращает список связей
     * @return array
     * @version 2012-09-05 d10n
     */
    public function GetCoRelationsList()
    {
        return array(
            array('id' => CO_RELATION_MUST_HAVE,                'name' => 'Must Have Customer'),
            array('id' => CO_RELATION_COMPETITOR,               'name' => 'Competitor'),
            array('id' => CO_RELATION_LIVE_CUSTOMER,            'name' => 'Live Customer'),
            array('id' => CO_RELATION_NOT_POTENTIAL_CUSTOMER,   'name' => 'Not a Potential Customer'),
            array('id' => CO_RELATION_POTENTIAL_CUSTOMER,       'name' => 'Potential Customer'),
            array('id' => CO_RELATION_SERVICE_PROVIDER,         'name' => 'Service Provider'),
            array('id' => CO_RELATION_STOCK_AGENT,              'name' => 'Stock Agent'),
            array('id' => CO_RELATION_SUPPLIER,                 'name' => 'Supplier'),
        );
    }
    
    /**
     * Добавляет список контактов компании
     * 
     * @param mixed $rowset
     * @param mixed $id_fieldname
     * @param mixed $entityname
     * @param mixed $cache_prefix
     */
    function FillCompanyContacts($rowset, $id_fieldname = 'company_id', $entityname = 'companycontacts', $cache_prefix = 'companycontacts')
    {
        $rowset = $this->_fill_entity_array_info($rowset, $id_fieldname, $entityname, $cache_prefix, 'sp_company_get_contacts_by_ids', array('companies' => '', 'campany' => 'id'), array());
        
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
     * Добавляет список активностей компании
     * 
     * @param mixed $rowset
     * @param mixed $id_fieldname
     * @param mixed $entityname
     * @param mixed $cache_prefix
     * @return mixed
     */
    function FillCompanyActivities($rowset, $id_fieldname = 'company_id', $entityname = 'companyactivity', $cache_prefix = 'companyactivity')
    {
        $rowset = $this->_fill_entity_array_info($rowset, $id_fieldname, $entityname, $cache_prefix, 'sp_company_get_activities_by_ids', array('companies' => '', 'campany' => 'id'), array());
        
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
     * Возвращает список компаний
     * 
     * @param mixed $keyword
     * @param mixed $country_id
     * @param mixed $region_id
     * @param mixed $city_id
     * @param mixed $industry_id
     * @param mixed $activity_id
     * @param mixed $speciality_id
     * @param mixed $product_id
     * @param mixed $feedstock_id
     * @param mixed $relation
     * @param mixed $status
     * @param mixed $page_no
     * @param mixed $per_page
     * 
     * @version 20120610, zharkov
     */
    function Search($search_string, $country_id, $region_id, $city_id, $industry_id, $activity_id, $speciality_id, $product_id, $feedstock_id, $relation, $status, $page_no = 0, $per_page = ITEMS_PER_PAGE)
    {
        $page_no    = $page_no > 0 ? $page_no : 1;
        $per_page   = $per_page < 1 ? ITEMS_PER_PAGE : $per_page;
        $start      = ($page_no - 1) * $per_page;

        $hash   =   'companies-search-' . md5($search_string . '-country' . $country_id . '-' . $region_id . '-' . $city_id . 
                    '-activity' . $industry_id . '-' . $activity_id . '-' . $speciality_id . 
                    '-product' . $product_id . '-feedstock' . $feedstock_id . 
                    '-relation' . $relation . 
                    '-status' . $status . 
                    '-page' . $page_no . '-' . $per_page);
        
        $rowset = Cache::GetData($hash);
        if (!isset($rowset) || !isset($rowset['data']) || isset($rowset['outdated']))
        {
            $cl = new SphinxClient();
            $cl->SetLimits($start, $per_page);
            $cl->SetFieldWeights(array(
                'company_id'    => 1000,
                'title'         => 1000,
                'title_native'  => 1000,
                'title_short'   => 1000,
                'title_trade'   => 1000,
                'data_labels'   => 100,
                'notes'         => 100,
                'bank_data'     => 100,
                'reg_data'      => 100, 
            ));
            // 201200612, zharkov: подгонка рещультатов
            $search_string = preg_replace('/M\s*-\s*a\s*-\s*M/ui', 'MaM', $search_string);
            
            if (!empty($search_string)) $search_string = '*' . str_replace('-', '\-', str_replace(' ', '* *', $search_string)) . '*';

            $cl->SetMatchMode(SPH_MATCH_ALL);
            $cl->SetIndexWeights(array(
                'ix_mam_companies_strict_delta' => 100, 
                'ix_mam_companies_strict'       => 100, 
                'ix_mam_companies_delta'        => 10,
                'ix_mam_companies'              => 10 
            ));
            
            $activity_id = ($speciality_id > 0 ? $speciality_id : ($activity_id > 0 ? $activity_id : $industry_id));
            
            if ($country_id > 0) $cl->SetFilter('country_id', array($country_id));
            if ($region_id > 0) $cl->SetFilter('region_id', array($region_id));
            if ($city_id > 0) $cl->SetFilter('city_id', array($city_id));
            if ($activity_id > 0) $cl->SetFilter('activity_id', array($activity_id));
            if ($product_id > 0) $cl->SetFilter('product_id', array($product_id));
            if ($feedstock_id > 0) $cl->SetFilter('feedstock_id', array($feedstock_id));
            if (!empty($status) && $status > 0) $cl->SetFilter('status_id', array($status));
            if (!empty($relation) && $relation > 0) $cl->SetFilter('relation_id', array($relation));

            $data = $cl->Query($search_string, 'ix_mam_companies, ix_mam_companies_strict, ix_mam_companies_delta, ix_mam_companies_strict_delta');

            if ($data === false)
            {
                Log::AddLine(LOG_ERROR, 'company::search ' . $cl->GetLastError());
                return null;
            }

            $rowset = array(); 
            if (!empty($data['matches']))
            {
                foreach ($data['matches'] as $id => $extra)
                {
                    $rowset[] = array('company_id' => $id);
                }
            }

            $rowset = array(
                $rowset,
                array(array('rows' => $data['total_found']))
            );
            
            Cache::SetData($hash, $rowset, array('companies', 'search'), CACHE_LIFETIME_STANDARD);
            
            $rowset = array(
                'data' => $rowset
            );
        }

        $result = array(
            'data'  => isset($rowset['data'][0]) ? $this->FillCompanyContacts($this->FillCompanyActivities($this->FillCompanyInfo($rowset['data'][0]))) : array(),
            'count' => isset($rowset['data'][1]) && isset($rowset['data'][1][0]) && isset($rowset['data'][1][0]['rows']) ? $rowset['data'][1][0]['rows'] : 0
        );
        
        if (!empty($result['data']))
        {
            foreach ($result['data'] as $key => $row)
			{
				$result['data'][$key]['company']['orders'] = $this->GetOrders($row['company']['id']);
			}	
        }
        return $result;
    }
    
    /**
     * Возвращает список открытых заказов компании
     * 
     * @param mixed $company_id
     * 
     * @version 20120603, zharkov
     */
    function GetOrders($company_id)
    {
        $hash       = 'company-' . $company_id . '-orders';
        $cache_tags = array($hash, 'companies', 'company-' . $company_id);

        $rowset = $this->_get_cached_data($hash, 'sp_company_get_orders', array($this->user_id, $company_id), $cache_tags);
        
        $orders = new Order();
        return isset($rowset[0]) ? $orders->FillOrderInfo($rowset[0]) : array();
    }

    /**
     * Возвращает список активных бизнесов для компании
     * 
     * @param mixed $company_id
     * 
     * @version 20120603, zharkov
     */
    function GetBizes($company_id)
    {
        $hash       = 'company-' . $company_id . '-bizes';
        $cache_tags = array($hash, 'companies', 'company-' . $company_id);

        $rowset = $this->_get_cached_data($hash, 'sp_company_get_bizes', array($company_id), $cache_tags);
        $result = array();
        
        $bizes  = new Biz();
        $result['data']     = isset($rowset[0]) ? $bizes->FillMainBizInfo($rowset[0]) : array();
        $result['count']    = isset($rowset[1]) && isset($rowset[1][0]) ? $rowset[1][0]['count'] : 0;
        
        return $result;
    }
    
    /**
     * Обновляет ключевой контакт компании
     * 
     * @param mixed $company_id
     * @param mixed $person_id
     * 
     * @version 20120601, zharkov
     */
    function UpdateKeyContact($company_id, $person_id)
    {
        $this->Update($company_id, array(
            'key_contact_id' => $person_id
        ));
        
        Cache::ClearTag('company-' . $company_id);
    }
    
    /**
     * Раскладывает список продуктов по составляющим
     * 
     * @param mixed $rowset
     * @version 20120531, zharkov
     */
    function FillProducts($rowset)
    {
        if (empty($rowset)) return $rowset;
        
        $products   = new Product();
        $result     = array();

        foreach ($rowset as $row)
        {
            $newrow     = array();
            $product    = $products->GetById($row['product_id']);
            $product    = $product['product'];

            while ($product['level'] > 0)
            {
                if ($product['level'] == 1)
                {
                    $newrow['product']       = $product;
                    $newrow['product_id']    = $product['id'];
                }

                $product    = $products->GetById($product['parent_id']);
                $product    = $product['product'];
            }
            
            $newrow['group']        = $product;
            $newrow['group_id']     = $product['id'];
            $newrow['id']           = $row['id'];
            $newrow['object_id']    = $row['product_id'];
            
            $result[] = $newrow;            
        }
        
        return $result;
    }
    
    /**
     * Раскладывает список активности компании по составляющим
     * 
     * @param mixed $activities
     * @version 20120531, zharkov
     */
    function FillActivities($rowset)
    {
        if (empty($rowset)) return $rowset;
        
        $activities = new Activity();
        $result     = array();

        foreach ($rowset as $row)
        {
            $newrow     = array();
            $activity   = $activities->GetById($row['activity_id']);
            $activity   = $activity['activity'];

            while ($activity['level'] > 0)
            {
                if ($activity['level'] == 2)
                {
                    $newrow['speciality']       = $activity;
                    $newrow['speciality_id']    = $activity['id'];
                }
                else if ($activity['level'] == 1)
                {
                    $newrow['activity']       = $activity;
                    $newrow['activity_id']    = $activity['id'];
                }

                $activity   = $activities->GetById($activity['parent_id']);
                $activity   = $activity['activity'];
            }
            
            $newrow['industry']     = $activity;
            $newrow['industry_id']  = $activity['id'];
            $newrow['id']           = $row['id'];
            $newrow['object_id']    = $row['activity_id'];
            
            $result[] = $newrow;            
        }
        
        return $result;
    }
    
    /**
     * Сохраняет продукт для компании
     * 
     * @param mixed $company_id
     * @param mixed $alias       p - product, f - feedstock
     * @param mixed $product_id
     * 
     * @version 20120531, zharkov
     */
    function SaveProduct($company_id, $alias, $product_id)
    {        
        $this->CallStoredProcedure('sp_company_save_product', array($this->user_id, $company_id, $alias, $product_id));
        Cache::ClearTag('company-' . $company_id . '-products-' . $alias);
    }    

    /**
     * Удаляет активити компании
     * 
     * @param mixed $company_id
     * @param mixed $alias          p - product, f - feedstock
     * @param mixed $id
     * 
     * @version 20120531, zharkov
     */
    function RemoveProduct($company_id, $alias, $id)
    {        
        $this->CallStoredProcedure('sp_company_remove_product', array($this->user_id, $id));
        Cache::ClearTag('company-' . $company_id . '-products-' . $alias);
    }    
    
    /**
     * Сохраняет список продуктов для компании
     * 
     * @param mixed $company_id
     * @param mixed $alias          p - product, f - feedstock
     * @param mixed $rowset
     * 
     * @version 20120531, zharkov
     */
    function SaveProducts($company_id, $alias, $rowset)
    {  
        $flag = false;
        if (empty($rowset)) $flag = true;

        // Удаление ненужных значений
        foreach ($this->GetProducts($company_id, $alias) as $row)
        {
            $remove_flag = true;            
            foreach ($rowset as $key => $row1)
            {
                if ($row['id'] == $row1['id'])
                {
                    $remove_flag = false;
                    unset($rowset[$key]);
                    
                    break;                    
                }
            }

            if ($remove_flag || $flag) $this->RemoveProduct($company_id, $alias, $row['id']);
        }
        
        // Добавление новых значений
        foreach ($rowset as $row) 
        {    
            if ($row['product_id'] > 0)
            {    
                $this->SaveProduct($company_id, $alias, $row['product_id']);
            }
        }    
    }
    
    /**
    * Возвращает список продуктов компании
    * 
    * @param mixed $company_id
    * @param mixed $alias   p - product, f - feedstock
    * 
    * @version 20120531, zharkov
    */
    function GetProducts($company_id, $alias)
    {
        $hash       = 'company-' . $company_id . '-products-' . $alias;
        $cache_tags = array($hash, 'companies', 'company-' . $company_id);

        $rowset = $this->_get_cached_data($hash, 'sp_company_get_products', array($company_id, $alias), $cache_tags);
        $rowset = isset($rowset[0]) ? $rowset[0] : array();

        return $rowset;
    }
    
    
    /**
     * Добавляет активити в компанию
     * 
     * @param mixed $company_id
     * @param mixed $activity_id
     * 
     * @version 20120530, zharkov
     */
    function SaveActivity($company_id, $activity_id)
    {        
        $this->CallStoredProcedure('sp_company_save_activity', array($this->user_id, $company_id, $activity_id));
        Cache::ClearTag('company-' . $company_id . '-activities');
    }    

    /**
     * Удаляет активити компании
     * 
     * @param mixed $id
     * 
     * @version 20120530, zharkov
     */
    function RemoveActivity($company_id, $id)
    {        
        $this->CallStoredProcedure('sp_company_remove_activity', array($this->user_id, $id));
        Cache::ClearTag('company-' . $company_id . '-activities');
    }    
    
    /**
     * Сохраняет список активити для компании
     * 
     * @param mixed $company_id
     * @param mixed $rowset
     * 
     * @version 20120530? zharkov
     */
    function SaveActivities($company_id, $rowset)
    {
        $flag = false;
        if (empty($rowset)) $flag = true;
        
        // Удаление ненужных значений
        foreach ($this->GetActivities($company_id) as $row)
        {
            $remove_flag = true;            
            foreach ($rowset as $key => $row1)
            {
                if ($row['id'] == $row1['id'])
                {
                    $remove_flag = false;
                    unset($rowset[$key]);
                    
                    break;                    
                }
            }
            
            if ($remove_flag || $flag) $this->RemoveActivity($company_id, $row['id']);
        }
        
        // Добавление новых значений
        foreach ($rowset as $row)
		{	
			if (!empty($row['activity_id'])) $this->SaveActivity($company_id, $row['activity_id']);
		}	
    }
    
    /**
     * Возвращает список активити для компании
     * 
     * @param mixed $company_id
     * 
     * @version 20120530, zharkov
     */
    function GetActivities($company_id)
    {
        $hash       = 'company-' . $company_id . '-activities';
        $cache_tags = array($hash, 'companies', 'company-' . $company_id);

        $rowset = $this->_get_cached_data($hash, 'sp_company_get_activities', array($company_id), $cache_tags);
        $rowset = isset($rowset[0]) ? $rowset[0] : array();

        return $rowset;
    }
    
    /**
     * Возвращает список компаний по названию
     * 
     */
    function GetListByTitle($title, $rows_count)
    {
        $hash       = 'companies-title-' . $title . '-rowscount-' . $rows_count;
        $cache_tags = array($hash, 'companies');

        $rowset = $this->_get_cached_data($hash, 'sp_company_get_list_by_title', array($title, $rows_count), $cache_tags);
        $rowset = isset($rowset[0]) ? $this->FillCompanyInfoShort($rowset[0]) : array();
		
        return $rowset;
    }
    
    /**
     * Возвращает список company
     * 
     */
    function GetList()
    {
        $hash       = 'companies';
        $cache_tags = array($hash);

        $rowset = $this->_get_cached_data($hash, 'sp_company_get_list', array(), $cache_tags);
        $rowset = isset($rowset[0]) ? $this->FillCompanyInfo($rowset[0]) : array();

        return $rowset;
    }
    
    /**
     * Сохраняет Stock Location для компании
     * 
     * @param mixed $company_id
     * @param mixed $location
     * @return resource
     */
    function SaveLocation($company_id, $location, $int_location_title)
    {
        if (empty($int_location_title)) $int_location_title = $location;
        
        $result = $this->CallStoredProcedure('sp_company_save_location', array($this->user_id, $company_id, $location, $int_location_title));
        $result = isset($result) && isset($result[0]) && isset($result[0][0]) ? $result[0][0] : null;
        
        Cache::ClearTag('company-' . $company_id);
        Cache::ClearTag('stockholders');
        Cache::ClearTag('steelpositions');
        Cache::ClearTag('steelitems');
        
        return $result;        
    }    
    
    /**
     * Убирает Stock location из компании
     * 
     * @param mixed $company_id
     */
    function RemoveLocation($company_id)
    {
        $result = $this->CallStoredProcedure('sp_company_remove_location', array($company_id));
        $result = isset($result) && isset($result[0]) && isset($result[0][0]) ? $result[0][0] : null;
        
        Cache::ClearTag('company-' . $company_id);
        Cache::ClearTag('stockholders');
        
        return $result;        
    }
    
    /**
     * Возвращает список держателей складов
     * 
     */
    function GetStockholdersList()
    {
        $hash       = 'stockholders';
        $cache_tags = array($hash, 'companies');

        $rowset = $this->_get_cached_data($hash, 'sp_company_get_stockholders_list', array(), $cache_tags);
        $rowset = isset($rowset[0]) ? $this->FillCompanyInfo($rowset[0]) : array();
        
        foreach ($rowset as $key => $row)
        {
            if (isset($row['company']) && !empty($row['company']['location_id']))
            {
                $rowset[$key]['companylocation_id'] = $row['company']['location_id'];
            }
        }
        
        $locations  = new Location();
        $rowset     = $locations->FillLocationInfo($rowset, 'companylocation_id', 'companylocation');

        foreach ($rowset as $key => $row)
        {
            if (isset($row['company']) && isset($row['companylocation']))
            {
                $rowset[$key]['company']['location'] = $row['companylocation'];
                
                unset($rowset[$key]['companylocation_id']);
                unset($rowset[$key]['companylocation']);
            }
        }        
        
        return $rowset;        
    }
    
    /**
     * Возвращает список компаний MaM
     * 
     */
    function GetMaMList()
    {
        $hash       = 'mamcompanies';
        $cache_tags = array($hash, 'companies');

        $rowset = $this->_get_cached_data($hash, 'sp_company_get_mam_list', array(), $cache_tags);
        $rowset = isset($rowset[0]) ? $this->FillCompanyInfo($rowset[0]) : array();

        return $rowset;
    }

    /**
     * Возвращает компанию по алиасу
     * 
     * @param mixed $alias
     */
    function GetByAlias($alias)
    {
        $hash       = 'company-alias-' . $alias;
        $cache_tags = array($hash, 'companies');

        $rowset = $this->_get_cached_data($hash, 'sp_company_get_by_alias', array($alias), $cache_tags);
        $rowset = isset($rowset[0]) ? $this->FillCompanyInfo($rowset[0]) : array();

        return isset($rowset[0]) ? $rowset[0] : null;
    }

    /**
     * Возвращает company по идентификатору
     *     
     * @param mixed $id
     */
    function GetById($id)
    {
        $dataset = $this->FillCompanyInfo(array(array('company_id' => $id)));
        return isset($dataset) && isset($dataset[0]) && isset($dataset[0]['company']) ? $dataset[0] : null;
    }
    
    /**
     * Заполняет базовую информацию о компании
     * 
     * @param mixed $rowset
     * @param mixed $id_fieldname
     * @param mixed $entityname
     * @param mixed $cache_prefix
     */
    function FillCompanyInfoShort($rowset, $id_fieldname = 'company_id', $entityname = 'company', $cache_prefix = 'company')
    {
        $rowset = $this->_fill_entity_info($rowset, $id_fieldname, $entityname, $cache_prefix, 'sp_company_get_list_by_ids', array('companies' => ''), array());

        foreach ($rowset as $key => $row)
        {
            if (!isset($row[$entityname])) continue;
            
            $row = $row[$entityname];
            
            if (!empty($row['title_short']) && $row['title_short'] != '--')
            {
                $rowset[$key][$entityname]['doc_no'] = $row['title_short']; 
            }
            else if (!empty($row['title_trade']) && $row['title_trade'] != '--')
            {
                $rowset[$key][$entityname]['doc_no'] = $row['title_trade'];
            }
            else
            {
                $rowset[$key][$entityname]['doc_no'] = $row['title'];
            }
            
            $rowset[$key][$entityname . 'city_id'] = $row['city_id'];
            $rowset[$key][$entityname . 'location_id'] = $row['location_id'];
        }
        
        $modelCity  = new City();
        $rowset     = $modelCity->FillCityMainInfo($rowset, $entityname . 'city_id', $entityname . 'city');
        
        $locations  = new Location();
        $rowset     = $locations->FillLocationInfo($rowset, $entityname . 'location_id', $entityname . 'location');        
        
        foreach ($rowset as $key => $row)
        {
            if (isset($rowset[$key][$entityname]))
            {
                if (isset($row[$entityname . 'city']))
                {
                    $rowset[$key][$entityname]['city'] = $row[$entityname . 'city'];                
                }
                
                if (isset($row[$entityname . 'location']))
                {
                    $rowset[$key][$entityname]['location'] = $row[$entityname . 'location'];                
                }                
            }
                        
            unset($rowset[$key][$entityname . 'city_id']);
            unset($rowset[$key][$entityname . 'city']);
            
            unset($rowset[$key][$entityname . 'location_id']);
            unset($rowset[$key][$entityname . 'location']);            
        }        
       
        return $rowset;
    }
    
    /**
     * Возвращет информацию о company
     * 
     * @param mixed $recordset
     * @param mixed $id_fieldname
     * @param mixed $entityname
     * @param mixed $cache_prefix
     */
    function FillCompanyInfo($rowset, $id_fieldname = 'company_id', $entityname = 'company', $cache_prefix = 'company')
    {
        $rowset = $this->FillCompanyInfoShort($rowset, $id_fieldname, $entityname, $cache_prefix);

        foreach($rowset as $key => $row)
        {
            if (!isset($rowset[$key][$entityname])) continue;   // важно

            $row = $row[$entityname];

            $rowset[$key]['co_author_id']       = $row['created_by'];
            $rowset[$key]['co_modifier_id']     = $row['modified_by'];
            $rowset[$key]['co_country_id']      = $row['country_id'];
            $rowset[$key]['co_region_id']       = $row['region_id'];
            $rowset[$key]['co_city_id']         = $row['city_id'];
            $rowset[$key]['co_parent_id']       = $row['parent_id'];            
            $rowset[$key]['co_mam_genius_id']   = $row['mam_genius_id'];
            $rowset[$key]['co_key_contact_id']  = $row['key_contact_id'];                
        } 

        $users  = new User();
        $rowset = $users->FillUserInfo($rowset, 'co_mam_genius_id', 'co_mam_genius');
        $rowset = $users->FillUserInfo($rowset, 'co_author_id', 'co_author');
        $rowset = $users->FillUserInfo($rowset, 'co_modifier_id', 'co_modifier');

        $persons    = new Person();
        $rowset     = $persons->FillPersonInfo($rowset, 'co_key_contact_id', 'co_key_contact');

        $countries  = new Country();
        $rowset     = $countries->FillCountryInfoShort($rowset, 'co_country_id', 'co_country');

        $regions    = new Region();
        $rowset     = $regions->FillRegionMainInfo($rowset, 'co_region_id', 'co_region');

        $cities     = new City();
        $rowset     = $cities->FillCityMainInfo($rowset, 'co_city_id', 'co_city');

        //$rowset     = $this->FillCompanyInfoShort($rowset, 'co_parent_id', 'co_parent');
        
        foreach ($rowset as $key => $row)
        {            
            if (!isset($rowset[$key][$entityname])) continue;   // важно
                        
            if (isset($row['co_key_contact']) && !empty($row['co_key_contact'])) 
            {

                $cd = new ContactData();
                $rowset[$key][$entityname]['key_contact_contacts']  = $cd->GetList('person', $row['co_key_contact_id']);
                $rowset[$key][$entityname]['key_contact']           = $row['co_key_contact'];                
            }
            unset($rowset[$key]['co_key_contact']);
            
            if (isset($row['co_mam_genius'])) 
            {
                $rowset[$key][$entityname]['mam_genius'] = $row['co_mam_genius'];    
                unset($rowset[$key]['co_mam_genius']);
            }

            if (isset($row['co_author'])) 
            {
                $rowset[$key][$entityname]['author'] = $row['co_author'];    
                unset($rowset[$key]['co_author']);
            }

            if (isset($row['co_modifier'])) 
            {
                $rowset[$key][$entityname]['modifier'] = $row['co_modifier'];    
                unset($rowset[$key]['co_modifier']);
            }

            if (isset($row['co_country'])) 
            {
                $rowset[$key][$entityname]['country'] = $row['co_country'];    
                unset($rowset[$key]['co_country']);
            }

            if (isset($row['co_region'])) 
            {
                $rowset[$key][$entityname]['region'] = $row['co_region'];    
                unset($rowset[$key]['co_region']);
            }

            $city_title = '';
            if (isset($row['co_city'])) 
            {
                $rowset[$key][$entityname]['city']  = $row['co_city'];
                $city_title                         = ' (' . $row['co_city']['title'] . ')';
                
                unset($rowset[$key]['co_city']);
            }

            if (isset($row['co_parent'])) 
            {
                $rowset[$key][$entityname]['parent'] = $row['co_parent'];    
                unset($rowset[$key]['co_parent']);
            }
            

            $row = $rowset[$key][$entityname];

            $rowset[$key][$entityname]['full_address'] = (!empty($row['address']) ? $row['address'] . ', ' : '')
                                            . (isset($row['city']) ? $row['city']['title'] . ', ' : '')
                                            . (isset($row['region']) ? $row['region']['title'] . ', ' : '')
                                            . (isset($row['country']) ? $row['country']['title'] : '');                                            

            
//            if ($row['type_id'] == CO_TYPE_HOFFICE) $rowset[$key][$entityname]['location_title'] = 'Head Office';
//            if ($row['type_id'] == CO_TYPE_OFFICE) $rowset[$key][$entityname]['location_title'] = 'Office';
//            if ($row['type_id'] == CO_TYPE_PLANT) $rowset[$key][$entityname]['location_title'] = 'Plant';
//            if ($row['type_id'] == CO_TYPE_SUBSIDIARY) $rowset[$key][$entityname]['location_title'] = 'Subsidiary';
//            if ($row['type_id'] == 0)  $rowset[$key][$entityname]['location_title'] = empty($row['parent_id']) ? 'Head Office' : '';
//
//            if ($row['status'] == '') $rowset[$key][$entityname]['status_title'] = '';
//            if ($row['status'] == 'bankrupt') $rowset[$key][$entityname]['status_title'] = 'Bankrupt';
//            if ($row['status'] == 'blacklist') $rowset[$key][$entityname]['status_title'] = 'Black List';
//            if ($row['status'] == 'contract') $rowset[$key][$entityname]['status_title'] = 'Contract';
//            if ($row['status'] == 'dontwant') $rowset[$key][$entityname]['status_title'] = 'Don\'t Want Us';
//            if ($row['status'] == 'goneaway') $rowset[$key][$entityname]['status_title'] = 'Gone Away';
//            if ($row['status'] == 'key') $rowset[$key][$entityname]['status_title'] = 'Key Partner';
//            if ($row['status'] == 'liquidated') $rowset[$key][$entityname]['status_title'] = 'Liquidated';
//            if ($row['status'] == 'negotiation') $rowset[$key][$entityname]['status_title'] = 'Negotiation';
//            if ($row['status'] == 'nodialogue') $rowset[$key][$entityname]['status_title'] = 'Not Dialogue Yet';
//            
//            if ($row['relation'] == '') $rowset[$key][$entityname]['relation_title'] = '';
//            if ($row['relation'] == 'musthave') $rowset[$key][$entityname]['relation_title'] = 'Must Have';
//            if ($row['relation'] == 'competitor') $rowset[$key][$entityname]['relation_title'] = 'Competitor';
//            if ($row['relation'] == 'live') $rowset[$key][$entityname]['relation_title'] = 'Live Customer';
//            if ($row['relation'] == 'notpotintial') $rowset[$key][$entityname]['relation_title'] = 'Not a Potential Customer';
//            if ($row['relation'] == 'potential') $rowset[$key][$entityname]['relation_title'] = 'Potential Customer';
//            if ($row['relation'] == 'service') $rowset[$key][$entityname]['relation_title'] = 'Service Provider';
//            if ($row['relation'] == 'stock') $rowset[$key][$entityname]['relation_title'] = 'Stock Agent';
//            if ($row['relation'] == 'supplier') $rowset[$key][$entityname]['relation_title'] = 'Supplier';
            
            
            unset($rowset[$key]['co_mam_genius_id']);
            unset($rowset[$key]['co_author_id']);
            unset($rowset[$key]['co_modifier_id']);
            unset($rowset[$key]['co_country_id']);
            unset($rowset[$key]['co_region_id']);
            unset($rowset[$key]['co_city_id']);
            unset($rowset[$key]['co_parent_id']);
            unset($rowset[$key]['co_key_contact_id']);
            
            
            $rowset[$key][$entityname]['doc_no_full'] = $rowset[$key][$entityname]['doc_no'] . $city_title;
            
        }

        return $rowset;
    }
    
    /**
     * Возвращает быстроменяющуюся информацию по company
     * 
     * @param array $recordset
     * @param mixed $id_fieldname
     * @param mixed $entityname
     */
    function FillQuickInfo($recordset, $id_fieldname = 'company_id', $entityname = 'company')
    {
        $recordset = $this->_fill_entity_info($recordset, $id_fieldname, $entityname . 'quick', 'companyquick', 'sp_company_get_quick_by_ids', array('companiesquick' => '', 'companies' => '', 'company' => 'id'), array());

        foreach ($recordset AS $key => $row)
        {

            if (isset($row[$entityname]) && isset($row[$entityname . 'quick']))
            {
                $recordset[$key][$entityname]['quick'] = $row[$entityname . 'quick'];                
            }
            
            unset($recordset[$key][$entityname . 'quick']);            
        }
        
        return $recordset;
    }

    /**
     * Сохраняет компанию
     * 
     * @param mixed $id
     * @param mixed $title
     * @param mixed $title_native
     * @param mixed $title_short
     * @param mixed $title_trade
     * @param mixed $parent_id
     * @param mixed $type_id
     * @param mixed $status_id
     * @param mixed $relation
     * @param mixed $key_contact
     * @param mixed $mam_genius
     * @param mixed $country_id
     * @param mixed $region_id
     * @param mixed $city_id
     * @param mixed $zip
     * @param mixed $address
     * @param mixed $pobox
     * @param mixed $delivery_address
     * @param mixed $data_labels
     * @param mixed $notes
     * @param mixed $bank_data
     * @param mixed $reg_data
     * @return resource
     * 
     * @version 20120530, zharkov
     */
    function Save($id, $title, $title_native, $title_short, $title_trade, $parent_id, $type_id, $status_id,
                    $relation, $key_contact, $mam_genius, $country_id, $region_id, $city_id, $zip, $address,
                    $pobox, $delivery_address, $data_labels, $notes, $bank_data, $reg_data, $rail_access,
                    $vat, $albo, $handling_cost, $storage_cost, $currency)
    {        
        $result = $this->CallStoredProcedure('sp_company_save', array($this->user_id, $id, $title, $title_native, 
                    $title_short, $title_trade, $parent_id, $type_id, $status_id,
                    $relation, $key_contact, $mam_genius, $country_id, $region_id, $city_id, $zip, $address,
                    $pobox, $delivery_address, $data_labels, $notes, $bank_data, $reg_data, $rail_access, 
                    $vat, $albo, $handling_cost, $storage_cost, $currency));
        $result = isset($result) && isset($result[0]) && isset($result[0][0]) ? $result[0][0] : null;
        
        if (empty($result) || array_key_exists('ErrorCode', $result)) return null;

        Cache::ClearTag('company-' . $result['id']);
        Cache::ClearTag('companies');
        
        return $result;
    }
	
    /**
     * Удаляет марку стали
     * 
     * @param mixed $id
     * @return resource
     */
    function Remove($id)
    {        
        $result = $this->CallStoredProcedure('sp_company_remove', array($id));
        $result = isset($result) && isset($result[0]) && isset($result[0][0]) ? $result[0][0] : null;
        
        if (empty($result) || array_key_exists('ErrorCode', $result)) return null;

        Cache::ClearTag('company-' . $id);
        Cache::ClearTag('companies');
        
        return $result;
    }
    
    /**
     * Возвращает список людей компании
     * 
     * @param mixed $company_id
     * @return mixed
     */
    function GetPersons($company_id, $limit = 0)
    {
        $hash       = 'company-persons-' . $company_id . '-limit-' . $limit;
        $cache_tags = array($hash, 'company-' . $company_id, 'persons');
        
        $rowset     = $this->_get_cached_data($hash, 'sp_company_get_persons', array($this->user_id, $company_id, $limit), $cache_tags);
        $result     = array();
        
        $persons    = new Person();
        $result['data']     = isset($rowset[0]) ? $persons->FillPersonInfo($rowset[0]) : array();
        $result['count']    = isset($rowset[1]) && isset($rowset[1][0]) ? $rowset[1][0]['count'] : 0;
        
        return $result;
    }
    
    public function GetDataForChart($company_id)
    {
        $result = $this->CallStoredProcedure('sp_company_get_data_for_chart', array($company_id));
        $result = isset($result[0]) ? $result[0] : array();
        //_epd($result);
        $matrix = array();
        $hAxis  = array();
        $vAxis  = array();
        
        foreach ($result as $row)
        {
            $year = date('Y', strtotime($row['created_at']));
            
            if ($row['currency'] == 'gbp')
            {
                $currency_sign = "£";
            }
            else if ($row['currency'] == 'eur')
            {
                $currency_sign = "€";
            }
            else
            {
                $currency_sign = '$';
            }
            
            if ($row['weight_unit'] == 'mt')
            {
                $weight_unit = "tons";
            }
            else if ($row['weight_unit'] == 'cwt')
            {
                $weight_unit = "lbs";
                $row['value'] = $row['value'] * 100;
            }
            else
            {
                $weight_unit = $row['weight_unit'] . 's';
            }
            
            
            if (!isset($matrix[$year]))
            {
                // на графике не более 3-х лет
                //if (count($data) >= 3) continue;
                
                $matrix[$year] = array(
                    'year'          => $year,
                    'sales_value'   => 0,
                    'total_weight'  => 0,
                    'currency'      => $row['currency'],
                    'currency_sign' => $currency_sign,
                    'weight_unit'   => $weight_unit,
                );
                
                $hAxis = array('title' => 'Year', );
                $vAxis = array('title' => 'Sales Value, ' . $matrix[$year]['currency']);
            }
            
            
            
            $matrix[$year]['sales_value']   += $row['value'];
            $matrix[$year]['total_weight']  += $row['weight'];
        }
        
        $data = array(
            'matrix'    => $matrix,
            'hAxis'     => $hAxis,
            'vAxis'     => $vAxis,
        );
        
        return $data;
    }
	
	/**
	 * @version 26.04.13 Sasha keeps the price change 
	 * 
	 * @param type $id
	 * @param type $company_id
	 * @param type $handling_cost
	 * @param type $storage_cost
	 * @param type $currency
	 * @param type $date
	 */
	function SavePrices($id, $company_id, $handling_cost, $storage_cost, $currency, $date)
	{
		$this->CallStoredProcedure('sp_company_save_prices', array($this->user_id, $id, $company_id, $handling_cost, $storage_cost, $currency, $date));
		
		Cache::ClearTag('company-prices-' . $company_id);
		Cache::ClearTag('company-' . $company_id);
	}
	
	/**
	 * @version 26.04.13 Sasha get prices for company
	 * @param type $company_id
	 * @return type
	 */
	function GetPricesForCompany($company_id)
	{
		$hash       = 'company-prices-' . $company_id;
        $cache_tags = array($hash, 'companies');
        
        $result     = $this->_get_cached_data($hash, 'sp_company_get_prices_for_company', array($company_id), $cache_tags);
		
		$result = isset($result[0]) && !empty($result[0]) ? $result[0] : null;
		
		if (!empty($result))
		{
			$model_user = new User();
			$result = $model_user->FillUserInfo($result, 'created_by');
		}	
		
		return $result; 
	}
	
	/**
	 * @version 29.04.13, Sasha remove prices
	 * @param type $id
	 * @return type
	 */
	function RemovePrices($id)
	{
		$result = $this->CallStoredProcedure('sp_company_remove_prices', array($id));
		
		$result = isset($result[0][0]) ? $result[0][0] : null;
		
		if (isset($result['company_id']))
		{
			 Cache::ClearTag('company-prices-' . $result['company_id']);
			 Cache::ClearTag('company-' . $result['company_id']);
		}	
		
		return $result;
	}
    
    /**
     * return company list, that have active orders 
     * 
     * @return type
     * 
     * @version 20130828, sasha
     */
    function GetListWithActiveOrders($page_no = 0, $per_page = ITEMS_PER_PAGE)
    {
        $page_no    = $page_no > 0 ? $page_no : 1;
        $per_page   = $per_page < 1 ? ITEMS_PER_PAGE : $per_page;
        $start      = ($page_no - 1) * $per_page;
        
        $hash       = 'company-list-with-active-orders-' . md5('page-no-' . $page_no . '-per-page-' . $per_page);
        $cache_tags = array($hash, 'companies', 'company-list-with-active-orders');
        
        $rowset     = $this->_get_cached_data($hash, 'sp_company_get_list_with_active_orders', array($start, $per_page), $cache_tags);
        
        $result     = array(
                        'data'  => isset($rowset[0][0]) && !empty($rowset[0][0]) ? $this->FillCompanyContacts($this->FillCompanyActivities($this->FillCompanyInfo($rowset[0]))) : array(),
                        'count' => isset($rowset[1][0]) && $rowset[1][0]['count'] > 0 ? $rowset[1][0]['count'] : 0
        );
        
        if (!empty($result['data']))
        {
            foreach ($result['data'] as $key => $row)
			{
				$result['data'][$key]['company']['orders'] = $this->GetOrders($row['company']['id']);
			}	
        }    
        
        return $result;
    }
}
