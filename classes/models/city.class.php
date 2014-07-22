<?php

class City extends Model
{
    function City()
    {
        Model::Model('cities');
    }

    /**
     * Возвращает регион по идентификатору
     * 
     * @param mixed $id
     */
    function GetById($id)
    {
        $dataset = $this->FillCityInfo(array(array('city_id' => $id)));
        return isset($dataset) && isset($dataset[0]) && isset($dataset[0]['city']) ? $dataset[0] : null;
    }    
    
    private function _get_list($region_id)
    {
        $hash       = 'region-' . $region_id . '-cities';
        $cache_tags = array($hash, 'cities', 'region-' . $region_id);

        return $this->_get_cached_data($hash, 'sp_city_get_list', array($region_id), $cache_tags);        
    }

    /**
     * Вощвращает список регионов
     * 
     * @param mixed $country_id
     */
    function GetList($region_id)
    {
        $rowset     = $this->_get_list($region_id);
        $rowset     = isset($rowset[0]) ? $this->FillCityInfo($rowset[0]) : array();
        
        return $rowset;        
    }

    /**
     * Returns cities list wwith main info
     * 
     * @param mixed $country_id
     */
    function GetListShort($region_id)
    {
        $rowset     = $this->_get_list($region_id);
        $rowset     = isset($rowset[0]) ? $this->FillCityMainInfo($rowset[0]) : array();
        
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
    function Save($id, $country_id, $region_id, $title, $title1, $title2, $dialcode)
    {        
        $alias  = $this->_get_title_src($title);
        
        $result = $this->CallStoredProcedure('sp_city_save', array($this->user_id, $id, $country_id, $region_id, $title, $alias, $title1, $title2, $dialcode));
        $result = isset($result) && isset($result[0]) && isset($result[0][0]) ? $result[0][0] : null;
        
        if (empty($result) || array_key_exists('ErrorCode', $result)) return null;

        Cache::ClearTag('city-' . $result['id']);
        Cache::ClearTag('cities');
        
        return $result;
    }    
    
    /**
     * Возвращает главную информацию о городе
     * 
     * @param mixed $rowset
     * @param mixed $id_fieldname
     * @param mixed $entityname
     * @return mixed
     */
    function FillCityMainInfo($rowset, $id_fieldname = 'city_id', $entityname = 'city')
    {
        return $this->_fill_entity_info($rowset, $id_fieldname, $entityname, 'city', 'sp_city_get_list_by_ids', array('cities' => '', 'city' => 'id'), array());
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
    function FillCityInfo($rowset, $id_fieldname = 'city_id', $entityname = 'city')
    {
        $rowset = $this->FillCityMainInfo($rowset, $id_fieldname, $entityname);
        
        foreach ($rowset as $key => $row)
        {
            if (isset($row[$entityname]))
            {
                $rowset[$key]['citymodifier_id'] = $row[$entityname]['modified_by'];
            }            
        }

        $users  = new User();
        $rowset = $users->FillUserInfo($rowset, 'citymodifier_id', 'citymodifier');
        $rowset = $this->_fill_entity_info($rowset, $id_fieldname, $entityname . 'quick', 'cityquick', 'sp_city_get_quick_by_ids', array('cityquick' => 'id', 'cities' => '', 'city' => 'id'), array());

        foreach ($rowset AS $key => $row)
        {
            if (isset($row[$entityname]))
            {
                if (isset($row[$entityname . 'quick'])) $rowset[$key][$entityname]['quick'] = $row[$entityname . 'quick'];
                if (isset($row['citymodifier'])) $rowset[$key][$entityname]['modifier'] = $row['citymodifier'];
            }
            
            unset($rowset[$key]['citymodifier']);
            unset($rowset[$key]['citymodifier_id']);
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
        $result = $this->CallStoredProcedure('sp_city_remove', array($this->user_id, $id));
        $result = isset($result) && isset($result[0]) && isset($result[0][0]) ? $result[0][0] : null;
        
        if (empty($result) || array_key_exists('ErrorCode', $result)) return null;

        Cache::ClearTag('city-' . $result['id']);
        Cache::ClearTag('cities');
        
        return $result;
    }    
}