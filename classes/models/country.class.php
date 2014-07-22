<?php

class Country extends Model
{
    function Country()
    {
        Model::Model('countries');
    }

    /**
     * Возвращает страну по идентификатору
     * 
     * @param mixed $id
     */
    function GetById($id)
    {
        $dataset = $this->FillCountryInfo(array(array('country_id' => $id)));
        return isset($dataset) && isset($dataset[0]) && isset($dataset[0]['country']) ? $dataset[0] : null;
    }    
    
    /**
     * Возвращает список стран
     * 
     * @param mixed $primary_first
     */
    function _get_list($primary_first = true)
    {
        $hash       = 'countries-sort-' . ($primary_first ? 'primary' : 'titl');
        $cache_tags = array($hash);

        return $this->_get_cached_data($hash, 'sp_country_get_list', array($primary_first), $cache_tags);
    }
    
    /**
     * Возвращает список стран
     * 
     */
    function GetList($primary_first = true)
    {
        $rowset = $this->_get_list($primary_first);
        $rowset = isset($rowset[0]) ? $this->FillCountryInfo($rowset[0]) : array();
        
        return $rowset;        
    }
    
    /**
     * Возвращает список стран без quick
     * @version 20120601, zharkov
     */
    function GetListShort($primary_first = true)
    {
        $rowset = $this->_get_list($primary_first);
        $rowset = isset($rowset[0]) ? $this->FillCountryInfoShort($rowset[0]) : array();
        
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
    function Save($id, $title, $title1, $title2, $alpha2, $alpha3, $code, $dialcode, $is_primary)
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
     * Заполняет базовые данные страны
     *
     * @param mixed $rowset
     * @param mixed $id_fieldname
     * @param mixed $entityname
     * @return array
     */
    function FillCountryInfoShort($rowset, $id_fieldname = 'country_id', $entityname = 'country')
    {
        return $this->_fill_entity_info($rowset, $id_fieldname, $entityname, 'country', 'sp_country_get_list_by_ids', array('countries' => '', 'country' => 'id'), array());
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
        $rowset = $this->FillCountryInfoShort($rowset, $id_fieldname, $entityname);
        
        foreach ($rowset as $key => $row)
        {
            if (isset($row[$entityname]))
            {
                $rowset[$key]['countrymodifier_id']     = $row[$entityname]['modified_by'];
                $rowset[$key][$entityname]['doc_no']    = $row[$entityname]['title'];
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
    function Remove($id)
    {        
        $result = $this->CallStoredProcedure('sp_country_remove', array($this->user_id, $id));
        $result = isset($result) && isset($result[0]) && isset($result[0][0]) ? $result[0][0] : null;
        
        if (empty($result) || array_key_exists('ErrorCode', $result)) return null;

        Cache::ClearTag('country-' . $result['id']);
        Cache::ClearTag('countries');
        
        return $result;
    }
    
    /**
     * Возвращает список отфильтрованный по ключевому слову<br />
     * 
     * @param string $keyword [VARCHAR(20)]
     * @param int $rows_count Количество записей
     * 
     * @version 20121204, d10n
     */
    public function GetListByKeyword($keyword, $rows_count)
    {
        $hash       = 'country-keyword-' . $keyword . '-rowscount-' . $rows_count;
        $cache_tags = array($hash, 'countries');

        $rowset = $this->_get_cached_data($hash, 'sp_country_get_list_by_keyword', array($keyword, $rows_count), $cache_tags);
        $rowset = isset($rowset[0]) ? $this->FillCountryInfo($rowset[0]) : array();
        
        return $rowset;
    }
}