<?php

class Geoname extends Model
{
    function Geoname()
    {
        Model::Model('countries');
    }

    /**
     * Возвращает список стран
     * 
     */
    function GetCountriesList()
    {
        $hash       = 'countries';
        $cache_tags = array($hash);

        $rowset     = $this->_get_cached_data($hash, 'sp_country_get_list', array(), $cache_tags);
        $rowset     = isset($rowset[0]) ? $this->FillCountryInfo($rowset[0]) : array();
        
        return $rowset;        
    }
    
    /**
     * Сохраняет страну
     * 
     * @param array $rowset
     * @param mixed $id_fieldname
     * @param mixed $entityname
     * @param mixed $cache_prefix
     * @return array
     */
    function SaveCountry($id, $title, $title1, $title2, $alpha2, $alpha3, $code, $dialcode, $is_primary)
    {        
        $alias  = $this->_get_title_src($title);
        
        $result = $this->CallStoredProcedure('sp_country_save', array($this->user_id, $id, $title, $alias, $title1, $title2, $alpha2, $alpha3, $code, $dialcode, $is_primary));
        $result = isset($result) && isset($result[0]) && isset($result[0][0]) ? $result[0][0] : null;
        
        if (empty($result) || array_key_exists('ErrorCode', $result)) return null;

        Cache::ClearTag('country-' . $result['id']);
        Cache::ClearTag('countries');
        
        return $result;
    }    
    
    /**
     * Заполняет данные страны
     * 
     * @param array $rowset
     * @param mixed $id_fieldname
     * @param mixed $entityname
     * @param mixed $cache_prefix
     * @return array
     */
    function FillCountryInfo($rowset, $id_fieldname = 'country_id', $entityname = 'country')
    {
        $rowset = $this->_fill_entity_info($rowset, $id_fieldname, $entityname, 'country', 'sp_country_get_list_by_ids', array('countries' => '', 'country' => 'id'), array());
        
        foreach ($rowset as $key => $row)
        {
            if (isset($row[$entityname]))
            {
                $rowset[$key]['countrymodifier_id'] = $row[$entityname]['modified_by'];
            }            
        }

        $users  = new User();
        $rowset = $users->FillUserInfo($rowset, 'countrymodifier_id', 'countrymodifier');
        $rowset = $this->_fill_entity_info($rowset, $id_fieldname, $entityname . 'quick', 'countryquick', 'sp_country_get_quick_by_ids', array('countryquick' => 'id', 'countries' => '', 'country' => 'id'), array());

        foreach ($rowset AS $key => $row)
        {
            if (isset($row[$entityname]))
            {
                if (isset($row[$entityname . 'quick'])) $rowset[$key][$entityname]['quick'] = $row[$entityname . 'quick'];
                if (isset($row['countrymodifier'])) $rowset[$key][$entityname]['modifier'] = $row['countrymodifier'];
            }
            
            unset($rowset[$key]['countrymodifier']);
            unset($rowset[$key]['countrymodifier_id']);
            unset($rowset[$key][$entityname . 'quick']);
        }
        
        return $rowset;
    }

    /**
     * Удаляет страну
     * 
     * @param mixed $id
     * @return resource
     */
    function RemoveCountry($id)
    {        
        $result = $this->CallStoredProcedure('sp_country_remove', array($this->user_id, $id));
        $result = isset($result) && isset($result[0]) && isset($result[0][0]) ? $result[0][0] : null;
        
        if (empty($result) || array_key_exists('ErrorCode', $result)) return null;

        Cache::ClearTag('country-' . $result['id']);
        Cache::ClearTag('countries');
        
        return $result;
    }    
    
    
    
    
    
    
    
    
    
    /**
     * Возвращает список регионов
     * 
     * @param mixed $country_id
     */
    function RegionsList($country_id)
    {
        $hash       = 'country-' . $country_id . '-regions';
        $cache_tags = array($hash, 'regions', 'country-' . $country_id, 'countries');

        $rowset     = $this->_get_cached_data($hash, 'sp_region_get_list', array($country_id), $cache_tags);
        
        return $rowset;        
    }

    
    /**
     * Заполняет данные региона
     * 
     * @param mixed $rowset
     * @param mixed $id_fieldname
     * @param mixed $entityname
     * @param mixed $cache_prefix
     * @return array
     */
    function FillRegionInfo($rowset, $id_fieldname = 'region_id', $entityname = 'region', $cache_prefix = 'region')
    {
        return $this->_fill_entity_info($rowset, $id_fieldname, $entityname, $cache_prefix, 'sp_region_get_list_by_ids', array('regions' => '', 'region' => 'id'), array());
    }
    
    
    /**
     * Возвращает список городов
     * 
     * @param mixed $region_id
     */
    function GetCitiesList($region_id)
    {
        $hash       = 'region-' . $region_id . '-cities';
        $cache_tags = array($hash, 'cities', 'region-' . $region_id, 'regions', 'countries');

        $rowset     = $this->_get_cached_data($hash, 'sp_city_get_list', array($region_id), $cache_tags);
        
        return $rowset;        
    }
    
    
    /**
     * Заполняет данные города
     * 
     * @param mixed $rowset
     * @param mixed $id_fieldname
     * @param mixed $entityname
     * @param mixed $cache_prefix
     * @return array
     */
    function FillCityInfo($rowset, $id_fieldname = 'city_id', $entityname = 'city', $cache_prefix = 'city')
    {
        return $this->_fill_entity_info($rowset, $id_fieldname, $entityname, $cache_prefix, 'sp_city_get_list_by_ids', array('cities' => '', 'city' => 'id'), array());
    }
    
    
    
    
    function GetListByTitle($title, $rows_count)
    {
        $hash       = 'biz-title-' . $title . '-rowscount-' . $rows_count;
        $cache_tags = array($hash, 'bizes');

        $rowset = $this->_get_cached_data($hash, 'sp_biz_get_list_by_title', array($title, $rows_count), $cache_tags);
        $rowset = isset($rowset[0]) ? $this->FillBizInfo($rowset[0]) : array();

        return $rowset;
    }
    
    /**
     * Возвращает список бизнесов
     * 
     */
    function GetList($team = '')
    {
        $hash       = 'bizes-' . $team;
        $cache_tags = array($hash, 'bizes');

        $rowset = $this->_get_cached_data($hash, 'sp_biz_get_list', array($team), $cache_tags);
        $rowset = isset($rowset[0]) ? $this->FillBizInfo($rowset[0]) : array();

        return $rowset;
    }
    
    /**
     * Возвращает список бизнесов для компании
     * 
     * @param mixed $company_id
     * @param mixed $role
     */
    function GetListByCompany($company_id, $role)
    {
        $hash       = 'bizes-company-' . $company_id . '-role-' . $role;
        $cache_tags = array($hash, 'bizes', 'company-' . $company_id);

        $rowset     = $this->_get_cached_data($hash, 'sp_biz_get_list_by_company', array($company_id, $role), $cache_tags);
        $rowset     = isset($rowset[0]) ? $this->FillBizInfo($rowset[0]) : array();

        return $rowset;        
    }

    /**
     * Возвращает бизнес по идентификатору
     *     
     * @param mixed $id
     */
    function GetById($id)
    {
        $dataset = $this->FillBizInfo(array(array('biz_id' => $id)));
        return isset($dataset) && isset($dataset[0]) && isset($dataset[0]['biz']) ? $dataset[0] : null;
    }
    
    /**
     * Возвращает быстроменяющуюся информацию по бизнесу
     * 
     * @param array $rowset
     * @param mixed $id_fieldname
     * @param mixed $entityname
     */
    function FillQuickInfo($rowset, $id_fieldname = 'biz_id', $entityname = 'biz')
    {
        $rowset = $this->_fill_entity_info($rowset, $id_fieldname, $entityname . 'quick', 'bizquick', 'sp_biz_get_quick_by_ids', array('bizesquick' => '', 'bizes' => '', 'biz' => 'id'), array());

        foreach ($rowset AS $key => $row)
        {
            if (isset($row[$entityname]) && isset($row[$entityname . 'quick']))
            {
                $rowset[$key][$entityname]['quick'] = $row[$entityname . 'quick'];
            }
            
            unset($rowset[$key][$entityname . 'quick']);            
        }
        
        return $rowset;
    }

    /**
     * Сохраняет бизнес
     * 
     * @param mixed $id
     * @param mixed $team
     * @param mixed $number
     * @param mixed $suffix
     * @param mixed $title
     * @param mixed $description
     * @return resource
     */
    function Save($id, $team, $number, $suffix, $title, $description = '')
    {        
        $result = $this->CallStoredProcedure('sp_biz_save', array($this->user_id, $id, $team, $number, $suffix, $title, $description));
        $result = isset($result) && isset($result[0]) && isset($result[0][0]) ? $result[0][0] : null;
        
        if (empty($result) || array_key_exists('ErrorCode', $result)) return null;

        Cache::ClearTag('biz-' . $id);
        Cache::ClearTag('bizes-' . $team);
        Cache::ClearTag('bizes');
        
        return $result;
    }
    
    /**
     * Удаляет бизнес
     * 
     * @param mixed $id
     * @return resource
     */
    function Remove($id)
    {        
        $result = $this->CallStoredProcedure('sp_biz_remove', array($id));
        $result = isset($result) && isset($result[0]) && isset($result[0][0]) ? $result[0][0] : null;
        
        if (empty($result) || array_key_exists('ErrorCode', $result)) return null;

        Cache::ClearTag('biz-' . $id);
        Cache::ClearTag('bizes');
        
        return $result;
    }    
}
