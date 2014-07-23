<?php

class Objective extends Model
{
    function Objective()
    {
        Model::Model('objectives');
    }

    /**
     * Возвращает список целей для редактирования в бизнесе
     * 
     * @param mixed $objective_id
     */
    function GetListForBiz($objective_id = 0)
    {
        $hash       = 'objectives-actual';
        $cache_tags = array($hash, 'objectives');

        $rowset = $this->_get_cached_data($hash, 'sp_objective_get_actual_list', array(), $cache_tags);
        $rowset = isset($rowset[0]) ? $rowset[0] : array();
        
        if (empty($rowset)) return $rowset;
        
        if ($objective_id > 0)
        {
            $add_flag = true;
            foreach ($rowset as $row)
            {
                if ($row['objective_id'] == $objective_id)
                {
                    $add_flag = false;
                    break;
                }
            }            
            
            if ($add_flag) $rowset[] = array('objective_id' => $objective_id);
        }
        
        return $this->FillObjectiveInfo($rowset);
    }
    
    /**
     * Возвращает список актуальных целей
     * 
     */
    function GetActualList()
    {
        $hash       = 'objectives-actual';
        $cache_tags = array($hash, 'objectives');

        $rowset = $this->_get_cached_data($hash, 'sp_objective_get_actual_list', array(), $cache_tags);
        $rowset = isset($rowset[0]) ? $this->FillObjectiveInfo($rowset[0]) : array();

        return $rowset;        
    }
 
    /**
     * Возвращает список objective
     * 
     */
    function GetList($year = 0, $quarter = 0)
    {
        $hash       = 'objectives-year' . $year . '-quarter' . $quarter;
        $cache_tags = array($hash, 'objectives');

        $rowset = $this->_get_cached_data($hash, 'sp_objective_get_list', array($year, $quarter), $cache_tags);
        $rowset = isset($rowset[0]) ? $this->FillObjectiveInfo($rowset[0]) : array();

        return $rowset;
    }

    /**
     * Возвращает objective по идентификатору
     *     
     * @param mixed $id
     */
    function GetById($id)
    {
        $dataset = $this->FillObjectiveInfo(array(array('objective_id' => $id)));
        return isset($dataset) && isset($dataset[0]) && isset($dataset[0]['objective']) ? $dataset[0] : null;
    }

    /**
     * Возвращает главную информацию о цели
     * 
     * @param mixed $rowset
     * @param mixed $id_fieldname
     * @param mixed $entityname
     * @param mixed $cache_prefix
     * @return mixed
     */
    function FillObjectiveMainInfo($rowset, $id_fieldname = 'objective_id', $entityname = 'objective', $cache_prefix = 'objective')
    {
        return $this->_fill_entity_info($rowset, $id_fieldname, $entityname, $cache_prefix, 'sp_objective_get_list_by_ids', array('objectives' => ''), array());
    }
    
    /**
     * Возвращет информацию о objective
     * 
     * @param mixed $rowset
     * @param mixed $id_fieldname
     * @param mixed $entityname
     * @param mixed $cache_prefix
     */
    function FillObjectiveInfo($rowset, $id_fieldname = 'objective_id', $entityname = 'objective', $cache_prefix = 'objective')
    {
        $rowset = $this->FillObjectiveMainInfo($rowset, $id_fieldname, $entityname, $cache_prefix);
        $rowset = $this->FillQuickInfo($rowset, $id_fieldname, $entityname);
        
        $current_year       = date("Y");
        $current_month      = date("n");
        $current_quarter    = ($current_month < 4 ? 1 : ($current_month > 3 && $current_month < 7 ? 2 : ($current_month > 6 && $current_month < 10 ? 3 : 4)));
        
        foreach ($rowset as $key => $row)
        {
            if (isset($row[$entityname]))
            {
                $row = $row[$entityname];
                $rowset[$key][$entityname . 'modifier_id'] = $row['modified_by'];
                
                if ($row['year'] < $current_year)
                {
                    $rowset[$key][$entityname]['expired'] = true;
                }
                else if ($row['quarter'] > 0 && $row['year'] == $current_year && $row['quarter'] < $current_quarter)
                {
                    $rowset[$key][$entityname]['expired'] = true;
                }
            }            
        }

        $users  = new User();
        $rowset = $users->FillUserInfo($rowset, $entityname . 'modifier_id', $entityname . 'modifier');

        foreach ($rowset AS $key => $row)
        {
            if (isset($row[$entityname]))
            {
                if (isset($row[$entityname . 'modifier'])) $rowset[$key][$entityname]['modifier'] = $row[$entityname . 'modifier'];
            }
            
            unset($rowset[$key][$entityname . 'modifier']);
            unset($rowset[$key][$entityname . 'modifier_id']);
        }
        
        return $rowset;
    }
    
    /**
     * Возвращает быстроменяющуюся информацию по objective
     * 
     * @param array $rowset
     * @param mixed $id_fieldname
     * @param mixed $entityname
     */
    function FillQuickInfo($rowset, $id_fieldname = 'objective_id', $entityname = 'objective')
    {
        $rowset = $this->_fill_entity_info($rowset, $id_fieldname, $entityname . 'quick', 'objectivequick', 'sp_objective_get_quick_by_ids', array('objectivesquick' => '', 'objectives' => '', 'objective' => 'id'), array());

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
     * Сохраняет objective
     * 
     * @param mixed $id
     * @param mixed $title
     * @param mixed $description
     */
    function Save($id, $year, $quarter, $title, $description)
    {        
        $result = $this->CallStoredProcedure('sp_objective_save', array($this->user_id, $id, $year, $quarter, $title, $description));
        $result = isset($result) && isset($result[0]) && isset($result[0][0]) ? $result[0][0] : null;
        
        if (empty($result) || array_key_exists('ErrorCode', $result)) return null;

        Cache::ClearTag('objective-' . $id);
        Cache::ClearTag('objectives');
        
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
        $result = $this->CallStoredProcedure('sp_objective_remove', array($this->user_id, $id));
        $result = isset($result) && isset($result[0]) && isset($result[0][0]) ? $result[0][0] : null;
        
        if (empty($result) || isset($result['ErrorCode'])) return null;

        Cache::ClearTag('objective-' . $id);
        Cache::ClearTag('objectives');
        
        return $result;
    }    
    
    /**
     * Возвращает список годов для которых есть цели
     * 
     */
    function GetYears()
    {
        return $this->SelectList(array(
            'fields'    => array('DISTINCT `year`'),
            'order'     => '`year` DESC'
        ));
    }
    
    /**
     * Возвращает список кварталов для которых есть цели в выбранном году
     * 
     * @param mixed $year
     * @return array
     */
    function GetQuarters($year)
    {
        if (empty($year)) return array();
        
        return $this->SelectList(array(
            'fields'    => array('DISTINCT `quarter`'),
            'where'     => array('conditions' => '`year` = ? AND `quarter` > 0', 'arguments' => array($year)),
            'order'     => '`quarter`'
        ));
    }    
}
