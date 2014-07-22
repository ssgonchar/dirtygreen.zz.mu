<?php

class JobPosition extends Model
{
    function JobPosition()
    {
        Model::Model('jobpositions');
    }

    /**
     * Возвращает список jobposition
     * 
     */
    function GetList()
    {
        $hash       = 'jobpositions';
        $cache_tags = array($hash);

        $rowset = $this->_get_cached_data($hash, 'sp_jobposition_get_list', array(), $cache_tags);
        $rowset = isset($rowset[0]) ? $this->FillJobPositionInfo($rowset[0]) : array();

        return $rowset;
    }

    /**
     * Возвращает jobposition по идентификатору
     *     
     * @param mixed $id
     */
    function GetById($id)
    {
        $dataset = $this->FillJobPositionInfo(array(array('jobposition_id' => $id)));
        return isset($dataset) && isset($dataset[0]) && isset($dataset[0]['jobposition']) ? $dataset[0] : null;
    }
    
    /**
     * Возвращет информацию о jobposition
     * 
     * @param mixed $recordset
     * @param mixed $id_fieldname
     * @param mixed $entityname
     * @param mixed $cache_prefix
     */
    function FillJobPositionInfo($recordset, $id_fieldname = 'jobposition_id', $entityname = 'jobposition', $cache_prefix = 'jobposition')
    {
        return $this->_fill_entity_info($recordset, $id_fieldname, $entityname, $cache_prefix, 'sp_jobposition_get_list_by_ids', array('jobpositions' => ''), array());
        //return $this->FillQuickInfo($recordset, $id_fieldname, $entityname);
    }
    
    /**
     * Возвращает быстроменяющуюся информацию по jobposition
     * 
     * @param array $recordset
     * @param mixed $id_fieldname
     * @param mixed $entityname
     */
    function FillQuickInfo($recordset, $id_fieldname = 'jobposition_id', $entityname = 'jobposition')
    {
        $recordset = $this->_fill_entity_info($recordset, $id_fieldname, $entityname . 'quick', 'jobpositionquick', 'sp_jobposition_get_quick_by_ids', array('jobpositionsquick' => '', 'jobpositions' => '', 'jobposition' => 'id'), array());

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
     * Сохраняет jobposition
     * 
     * @param mixed $id
     * @param mixed $title
     * @param mixed $description
     */
    function Save($id, $title)
    {        
        $result = $this->CallStoredProcedure('sp_jobposition_save', array($this->user_id, $id, $title));
        $result = isset($result) && isset($result[0]) && isset($result[0][0]) ? $result[0][0] : null;
        
        if (empty($result) || array_key_exists('ErrorCode', $result)) return null;

        Cache::ClearTag('jobposition-' . $id);
        Cache::ClearTag('jobpositions');
        
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
        $result = $this->CallStoredProcedure('sp_jobposition_remove', array($this->user_id, $id));
        $result = isset($result) && isset($result[0]) && isset($result[0][0]) ? $result[0][0] : null;
        
        if (empty($result) || array_key_exists('ErrorCode', $result)) return null;

        Cache::ClearTag('jobposition-' . $id);
        Cache::ClearTag('jobpositions');
        
        return $result;
    }    
}
