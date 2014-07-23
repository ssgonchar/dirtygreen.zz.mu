<?php

class SteelGrade extends Model
{
    function SteelGrade()
    {
        Model::Model('steelgrades');
    }

    /**
     * Возвращает список steelgrade
     * 
     */
    function GetList()
    {
        $hash       = 'steelgrades';
        $cache_tags = array($hash);

        $rowset = $this->_get_cached_data($hash, 'sp_steelgrade_get_list', array(), $cache_tags);
        $rowset = isset($rowset[0]) ? $this->FillSteelGradeInfo($rowset[0]) : array();

        return $rowset;
    }

    /**
     * Возвращает steelgrade по идентификатору
     *     
     * @param mixed $id
     */
    function GetById($id)
    {
        $dataset = $this->FillSteelGradeInfo(array(array('steelgrade_id' => $id)));
        return isset($dataset) && isset($dataset[0]) && isset($dataset[0]['steelgrade']) ? $dataset[0] : null;
    }
    
    /**
     * Возвращет информацию о steelgrade
     * 
     * @param mixed $recordset
     * @param mixed $id_fieldname
     * @param mixed $entityname
     * @param mixed $cache_prefix
     */
    function FillSteelGradeInfo($recordset, $id_fieldname = 'steelgrade_id', $entityname = 'steelgrade', $cache_prefix = 'steelgrade')
    {
        $recordset = $this->_fill_entity_info($recordset, $id_fieldname, $entityname, $cache_prefix, 'sp_steelgrade_get_list_by_ids', array('steelgrades' => ''), array());
        return $this->FillQuickInfo($recordset, $id_fieldname, $entityname);
    }
    
    /**
     * Возвращает быстроменяющуюся информацию по steelgrade
     * 
     * @param array $recordset
     * @param mixed $id_fieldname
     * @param mixed $entityname
     */
    function FillQuickInfo($recordset, $id_fieldname = 'steelgrade_id', $entityname = 'steelgrade')
    {
        $recordset = $this->_fill_entity_info($recordset, $id_fieldname, $entityname . 'quick', 'steelgradequick', 'sp_steelgrade_get_quick_by_ids', array('steelgradesquick' => '', 'steelgrades' => '', 'steelgrade' => 'id'), array());

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
     * Сохраняет steelgrade
     * 
     * @param mixed $id
     * @param mixed $title
     * @param mixed $description
     */
    function Save($id, $title, $alias, $bgcolor, $color = '', $description = '')
    {        
        $alias  = empty($alias) ? $title : $alias;
        
        $result = $this->CallStoredProcedure('sp_steelgrade_save', array($this->user_id, $id, $title, $alias, $bgcolor, $color, $description));
        $result = isset($result) && isset($result[0]) && isset($result[0][0]) ? $result[0][0] : null;
        
        if (empty($result) || array_key_exists('ErrorCode', $result)) return null;

        Cache::ClearTag('steelgrade-' . $id);
        Cache::ClearTag('steelgrades');
        
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
        $result = $this->CallStoredProcedure('sp_steelgrade_remove', array($this->user_id, $id));
        $result = isset($result) && isset($result[0]) && isset($result[0][0]) ? $result[0][0] : null;
        
        if (empty($result) || array_key_exists('ErrorCode', $result)) return null;

        Cache::ClearTag('steelgrade-' . $id);
        Cache::ClearTag('steelgrades');
        
        return $result;
    }    
}
