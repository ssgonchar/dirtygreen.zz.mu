<?php

class DeliveryTime extends Model
{
    function DeliveryTime()
    {
        Model::Model('deliverytimes');
    }

    /**
    * Возвращает список
    * 
    */
    function GetList()
    {
        $hash       = 'deliverytimes';
        $cache_tags = array($hash);

        $rowset = $this->_get_cached_data($hash, 'sp_deliverytime_get_list', array(), $cache_tags);
        return isset($rowset[0]) ? $this->FillDeliveryTimeInfo($rowset[0]) : array();
    }
    
    /**
     * Получает id deliverytime
     * 
     * @param mixed $title
     * @return mixed
     */
    function GetDeliveryTimeId($title)
    {
        if (empty($title)) return 0;
        
        $deliverytime = $this->GetByTitle($title);
        
        if (isset($deliverytime)) return $deliverytime['deliverytime']['id'];
        
        $result = $this->Save(0, $title);
        return $result['id'];
    }    
    
    /**
     * Возвращает deliverytime по идентификатору
     *     
     * @param mixed $id
     */
    function GetById($id)
    {
        $dataset = $this->FillDeliveryTimeInfo(array(array('deliverytime_id' => $id)));
        return isset($dataset) && isset($dataset[0]) && isset($dataset[0]['deliverytime']) ? $dataset[0] : null;
    }
    
    /**
     * Возвращает deliverytime по названию
     *     
     * @param mixed $title
     */
    function GetByTitle($title)
    {
        $alias = $this->_get_title_src($title);

        $result = $this->CallStoredProcedure('sp_deliverytime_get_by_alias', array($alias));
        return isset($result) && isset($result[0]) && isset($result[0][0]) && isset($result[0][0]['id']) ? $this->GetById($result[0][0]['id']) : null;
    }
    
    /**
     * Возвращет информацию о deliverytime
     * 
     * @param mixed $rowset
     * @param mixed $id_fieldname
     * @param mixed $entityname
     * @param mixed $cache_prefix
     */
    function FillDeliveryTimeInfo($rowset, $id_fieldname = 'deliverytime_id', $entityname = 'deliverytime', $cache_prefix = 'deliverytime')
    {
        return $this->_fill_entity_info($rowset, $id_fieldname, $entityname, $cache_prefix, 'sp_deliverytime_get_list_by_ids', array('deliverytimes' => '', 'deliverytime' => 'id'), array());
    }
    
    /**
     * Сохраняет deliverytime
     * 
     * @param mixed $id
     * @param mixed $title
     * @param mixed $description
     */
    function Save($id, $title)
    {        
        $alias = $this->_get_title_src($title);
        
        $result = $this->CallStoredProcedure('sp_deliverytime_save', array($this->user_id, $id, $title, $alias));
        $result = isset($result) && isset($result[0]) && isset($result[0][0]) ? $result[0][0] : null;
        
        if (empty($result) || array_key_exists('ErrorCode', $result)) return null;

        Cache::ClearTag('deliverytime-' . $result['id']);
        Cache::ClearTag('deliverytimes');
        
        return $result;
    }    
}
