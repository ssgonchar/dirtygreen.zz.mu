<?php

class Region extends Model
{
    function Region()
    {
        Model::Model('regions');
    }

    /**
     * Возвращает регион по идентификатору
     * 
     * @param mixed $id
     */
    function GetById($id)
    {
        $dataset = $this->FillRegionInfo(array(array('region_id' => $id)));
        return isset($dataset) && isset($dataset[0]) && isset($dataset[0]['region']) ? $dataset[0] : null;
    }    

    /**
     * Returns regions list
     * 
     * @param mixed $country_id
     */
    function _get_list($country_id)
    {
        $hash       = 'country-' . $country_id . '-regions';
        $cache_tags = array($hash, 'regions', 'country-' . $country_id);

        return $this->_get_cached_data($hash, 'sp_region_get_list', array($country_id), $cache_tags);
    }
    
    /**
     * Вощвращает список регионов
     * 
     * @param mixed $country_id
     */
    function GetList($country_id)
    {
        $rowset     = $this->_get_list($country_id);
        $rowset     = isset($rowset[0]) ? $this->FillRegionInfo($rowset[0]) : array();
        
        return $rowset;        
    }
    
    /**
    * Returns regions list with main info
    * 
    * @param mixed $country_id
    */
    function GetListShort($country_id)
    {
        $rowset     = $this->_get_list($country_id);
        $rowset     = isset($rowset[0]) ? $this->FillRegionMainInfo($rowset[0]) : array();
        
        return $rowset;        
    }
    
    /**
     * Returns main region info
     *  
     * @param mixed $rowset
     * @param mixed $id_fieldname
     * @param mixed $entityname
     * @return mixed
     */
    function FillRegionMainInfo($rowset, $id_fieldname = 'region_id', $entityname = 'region')
    {
        return $this->_fill_entity_info($rowset, $id_fieldname, $entityname, 'region', 'sp_region_get_list_by_ids', array('regions' => '', 'region' => 'id'), array());
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
    function Save($id, $country_id, $title, $title1, $title2)
    {        
        $alias  = $this->_get_title_src($title);
        
        $result = $this->CallStoredProcedure('sp_region_save', array($this->user_id, $id, $country_id, $title, $alias, $title1, $title2));
        $result = isset($result) && isset($result[0]) && isset($result[0][0]) ? $result[0][0] : null;
        
        if (empty($result) || array_key_exists('ErrorCode', $result)) return null;

        Cache::ClearTag('region-' . $result['id']);
        Cache::ClearTag('regions');
        
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
    function FillRegionInfo($rowset, $id_fieldname = 'region_id', $entityname = 'region')
    {
        $rowset = $this->FillRegionMainInfo($rowset, $id_fieldname = 'region_id', $entityname);
        
        foreach ($rowset as $key => $row)
        {
            if (isset($row[$entityname]))
            {
                $rowset[$key]['regionmodifier_id'] = $row[$entityname]['modified_by'];
            }            
        }

        $users  = new User();
        $rowset = $users->FillUserInfo($rowset, 'regionmodifier_id', 'regionmodifier');
        $rowset = $this->_fill_entity_info($rowset, $id_fieldname, $entityname . 'quick', 'regionquick', 'sp_region_get_quick_by_ids', array('regionquick' => 'id', 'regions' => '', 'region' => 'id'), array());

        foreach ($rowset AS $key => $row)
        {
            if (isset($row[$entityname]))
            {
                if (isset($row[$entityname . 'quick'])) $rowset[$key][$entityname]['quick'] = $row[$entityname . 'quick'];
                if (isset($row['regionmodifier'])) $rowset[$key][$entityname]['modifier'] = $row['regionmodifier'];
            }
            
            unset($rowset[$key]['regionmodifier']);
            unset($rowset[$key]['regionmodifier_id']);
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
        $result = $this->CallStoredProcedure('sp_region_remove', array($this->user_id, $id));
        $result = isset($result) && isset($result[0]) && isset($result[0][0]) ? $result[0][0] : null;
        
        if (empty($result) || array_key_exists('ErrorCode', $result)) return null;

        Cache::ClearTag('region-' . $result['id']);
        Cache::ClearTag('regions');
        
        return $result;
    }    
}