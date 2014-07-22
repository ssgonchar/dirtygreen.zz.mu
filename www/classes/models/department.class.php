<?php

class Department extends Model
{
    function Department()
    {
        Model::Model('departments');
    }

    /**
     * Возвращает список department
     * 
     */
    function GetList()
    {
        $hash       = 'departments';
        $cache_tags = array($hash);

        $rowset = $this->_get_cached_data($hash, 'sp_department_get_list', array(), $cache_tags);
        $rowset = isset($rowset[0]) ? $this->FillDepartmentInfo($rowset[0]) : array();

        return $rowset;
    }

    /**
     * Возвращает department по идентификатору
     *     
     * @param mixed $id
     */
    function GetById($id)
    {
        $dataset = $this->FillDepartmentInfo(array(array('department_id' => $id)));
        return isset($dataset) && isset($dataset[0]) && isset($dataset[0]['department']) ? $dataset[0] : null;
    }
    
    /**
     * Возвращет информацию о department
     * 
     * @param mixed $recordset
     * @param mixed $id_fieldname
     * @param mixed $entityname
     * @param mixed $cache_prefix
     */
    function FillDepartmentInfo($recordset, $id_fieldname = 'department_id', $entityname = 'department', $cache_prefix = 'department')
    {
        return $this->_fill_entity_info($recordset, $id_fieldname, $entityname, $cache_prefix, 'sp_department_get_list_by_ids', array('departments' => ''), array());
        //return $this->FillQuickInfo($recordset, $id_fieldname, $entityname);
    }
    
    /**
     * Возвращает быстроменяющуюся информацию по department
     * 
     * @param array $recordset
     * @param mixed $id_fieldname
     * @param mixed $entityname
     */
    function FillQuickInfo($recordset, $id_fieldname = 'department_id', $entityname = 'department')
    {
        $recordset = $this->_fill_entity_info($recordset, $id_fieldname, $entityname . 'quick', 'departmentquick', 'sp_department_get_quick_by_ids', array('departmentsquick' => '', 'departments' => '', 'department' => 'id'), array());

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
     * Сохраняет department
     * 
     * @param mixed $id
     * @param mixed $title
     * @param mixed $description
     */
    function Save($id, $title)
    {        
        $result = $this->CallStoredProcedure('sp_department_save', array($this->user_id, $id, $title));
        $result = isset($result) && isset($result[0]) && isset($result[0][0]) ? $result[0][0] : null;
        
        if (empty($result) || array_key_exists('ErrorCode', $result)) return null;

        Cache::ClearTag('department-' . $id);
        Cache::ClearTag('departments');
        
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
        $result = $this->CallStoredProcedure('sp_department_remove', array($this->user_id, $id));
        $result = isset($result) && isset($result[0]) && isset($result[0][0]) ? $result[0][0] : null;
        
        if (empty($result) || array_key_exists('ErrorCode', $result)) return null;

        Cache::ClearTag('department-' . $id);
        Cache::ClearTag('departments');
        
        return $result;
    }    
}
