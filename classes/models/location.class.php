<?php

class Location extends Model
{
    function Location()
    {
        Model::Model('locations');
    }

    /**
     * Возвращает список location
     * 
     */
    function GetList()
    {
        $hash       = 'locations';
        $cache_tags = array($hash);

        $rowset = $this->_get_cached_data($hash, 'sp_location_get_list', array(), $cache_tags);
        $rowset = isset($rowset[0]) ? $this->FillLocationInfo($rowset[0]) : array();

        return $rowset;
    }

    /**
     * Возвращает location по идентификатору
     *     
     * @param mixed $id
     */
    function GetById($id)
    {
        $dataset = $this->FillLocationInfo(array(array('location_id' => $id)));
        return isset($dataset) && isset($dataset[0]) && isset($dataset[0]['location']) ? $dataset[0] : null;
    }
    
    
    /**
     * Возвращет информацию о location
     * 
     * @param mixed $recordset
     * @param mixed $id_fieldname
     * @param mixed $entityname
     * @param mixed $cache_prefix
     */
    function FillLocationInfo($recordset, $id_fieldname = 'location_id', $entityname = 'location', $cache_prefix = 'location')
    {
        $recordset = $this->_fill_entity_info($recordset, $id_fieldname, $entityname, $cache_prefix, 'sp_location_get_list_by_ids', array('locations' => ''), array());
        return $this->FillQuickInfo($recordset, $id_fieldname, $entityname);
    }
    
    /**
     * Возвращает быстроменяющуюся информацию по location
     * 
     * @param array $recordset
     * @param mixed $id_fieldname
     * @param mixed $entityname
     */
    function FillQuickInfo($recordset, $id_fieldname = 'location_id', $entityname = 'location')
    {
        $recordset = $this->_fill_entity_info($recordset, $id_fieldname, 'locationquick', 'locationquick', 'sp_location_get_quick_by_ids', array('locationsquick' => '', 'locations' => '', 'location' => 'id'), array());

        foreach ($recordset AS $key => $row)
        {
            if (isset($row[$entityname]) && isset($row['locationquick']))
            {
                $recordset[$key][$entityname]['quick'] = $row['locationquick'];
                unset($recordset[$key]['locationquick']);
            }
        }
        
        return $recordset;
    }

    /**
     * Сохраняет location
     * 
     * @param mixed $id
     * @param mixed $title
     * @param mixed $description
     */
    function Save($id, $title, $description)
    {        
        $result = $this->CallStoredProcedure('sp_location_save', array($this->user_id, $id, $title, $description));
        $result = isset($result) && isset($result[0]) && isset($result[0][0]) ? $result[0][0] : null;
        
        if (empty($result) || array_key_exists('ErrorCode', $result)) return null;

        Cache::ClearTag('location-' . $id);
        Cache::ClearTag('locations');
        
        return $result;
    }
    
    /**
     * Удаляет location
     * 
     * @param mixed $id
     * @return resource
     */
    function Remove($id)
    {        
        $result = $this->CallStoredProcedure('sp_location_remove', array($this->user_id, $id));
        $result = isset($result) && isset($result[0]) && isset($result[0][0]) ? $result[0][0] : null;
        
        if (empty($result) || array_key_exists('ErrorCode', $result)) return null;

        Cache::ClearTag('location-' . $id);
        Cache::ClearTag('locations');
        
        return $result;
    }    
}
