<?php

class Activity extends Model
{
    function Activity()
    {
        Model::Model('activities');
    }

    /**
     * Возвращает все activity в виде дерева
     * 
     */
    function GetTree()
    {
        return $this->_sort_tree($this->GetList());
    }
    
    /**
     * Вовращает список ативностей
     * 
     * @param mixed $parent_id, "-1" - для получения всех аскивностей без группировки
     */
    function GetList($parent_id = -1)
    {
        $hash       = 'activities-parent_id-' . $parent_id;
        $cache_tags = array($hash, 'activities');

        $rowset = $this->_get_cached_data($hash, 'sp_activity_get_list', array($parent_id), $cache_tags);
        $rowset = isset($rowset[0]) ? $this->FillActivityInfo($rowset[0]) : array();
        
        foreach ($rowset as $key => $row)
        {
            $rowset[$key]['id']         = $row['activity']['id'];
            $rowset[$key]['parent_id']  = $row['activity']['parent_id'];
        }        

        return $rowset;
    }

    /**
     * Возвращает activity по идентификатору
     *     
     * @param mixed $id
     */
    function GetById($id)
    {
        $dataset = $this->FillActivityInfo(array(array('activity_id' => $id)));
        return isset($dataset) && isset($dataset[0]) && isset($dataset[0]['activity']) ? $dataset[0] : null;
    }
    
    function FillActivityBaseInfo($rowset, $id_fieldname = 'activity_id', $entityname = 'activity', $cache_prefix = 'activity')
    {
        return $this->_fill_entity_info($rowset, $id_fieldname, $entityname, $cache_prefix, 'sp_activity_get_list_by_ids', array('activities' => ''), array());
    }

    /**
     * Возвращет информацию о activity
     * 
     * @param mixed $rowset
     * @param mixed $id_fieldname
     * @param mixed $entityname
     * @param mixed $cache_prefix
     */
    function FillActivityInfo($rowset, $id_fieldname = 'activity_id', $entityname = 'activity', $cache_prefix = 'activity')
    {
        $rowset = $this->FillActivityBaseInfo($rowset, $id_fieldname, $entityname, $cache_prefix);
        
        foreach ($rowset as $key => $row)
        {
            if (isset($row[$entityname]))
            {
                $rowset[$key]['activitymodifier_id'] = $row[$entityname]['modified_by'];
            }            
        }
        
        $users  = new User();
        $rowset = $users->FillUserInfo($rowset, 'activitymodifier_id', 'activitymodifier');
        $rowset = $this->_fill_entity_info($rowset, $id_fieldname, $entityname . 'quick', 'activityquick', 'sp_activity_get_quick_by_ids', array('activityquick' => 'id', 'activities' => '', 'activity' => 'id'), array());

        foreach ($rowset AS $key => $row)
        {
            if (isset($row[$entityname]))
            {
                if (isset($row[$entityname . 'quick'])) $rowset[$key][$entityname]['quick'] = $row[$entityname . 'quick'];
                if (isset($row['activitymodifier'])) $rowset[$key][$entityname]['modifier'] = $row['activitymodifier'];
            }
            
            unset($rowset[$key]['activitymodifier']);
            unset($rowset[$key]['activitymodifier_id']);
            unset($rowset[$key][$entityname . 'quick']);
        }
        
        return $rowset;        
    }
    
    /**
     * Сохраняет activity
     * 
     * @param mixed $id
     * @param mixed $title
     * @param mixed $description
     */
    function Save($id, $parent_id, $title)
    {        
        $alias  = $this->_get_title_src($title);
        
        $result = $this->CallStoredProcedure('sp_activity_save', array($this->user_id, $id, $parent_id, $title, $alias));
        $result = isset($result) && isset($result[0]) && isset($result[0][0]) ? $result[0][0] : null;
        
        if (empty($result) || array_key_exists('ErrorCode', $result)) return null;

        Cache::ClearTag('activity-' . $id);
        Cache::ClearTag('activities');
        
        return $result;
    }
    
    /**
     * Удаляет activity
     * 
     * @param mixed $id
     * @return resource
     */
    function Remove($id)
    {        
        $result = $this->CallStoredProcedure('sp_activity_remove', array($id));
        $result = isset($result) && isset($result[0]) && isset($result[0][0]) ? $result[0][0] : null;
        
        if (empty($result) || array_key_exists('ErrorCode', $result)) return null;

        Cache::ClearTag('activity-' . $id);
        Cache::ClearTag('activities');
        
        return $result;
    }    
}
