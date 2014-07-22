<?php
require_once APP_PATH . 'classes/models/user.class.php';

class Team extends Model
{
    function Team()
    {
        Model::Model('teams');
    }

    /**
     * Возвращает список team
     * 
     */
    function GetList()
    {
        $hash       = 'teams';
        $cache_tags = array($hash);

        $rowset = $this->_get_cached_data($hash, 'sp_team_get_list', array(), $cache_tags);
        $rowset = isset($rowset[0]) ? $this->FillTeamInfo($rowset[0]) : array();

        return $rowset;
    }

    /**
     * Возвращает team по идентификатору
     *     
     * @param mixed $id
     */
    function GetById($id)
    {
        $dataset = $this->FillTeamInfo(array(array('team_id' => $id)));
        return isset($dataset) && isset($dataset[0]) && isset($dataset[0]['team']) ? $dataset[0] : null;
    }

    /**
     * Возвращает главную информацию о команде
     * 
     * @param mixed $recordset
     * @param mixed $id_fieldname
     * @param mixed $entityname
     * @param mixed $cache_prefix
     */
    function FillTeamMainInfo($recordset, $id_fieldname = 'team_id', $entityname = 'team', $cache_prefix = 'team')
    {
        return $this->_fill_entity_info($recordset, $id_fieldname, $entityname, $cache_prefix, 'sp_team_get_list_by_ids', array('teams' => ''), array());
    }
    
    /**
     * Возвращет информацию о team
     * 
     * @param mixed $recordset
     * @param mixed $id_fieldname
     * @param mixed $entityname
     * @param mixed $cache_prefix
     */
    function FillTeamInfo($recordset, $id_fieldname = 'team_id', $entityname = 'team', $cache_prefix = 'team')
    {
        $recordset = $this->FillTeamMainInfo($recordset, $id_fieldname, $entityname, $cache_prefix);
        
        foreach ($recordset as $key => $row)
        {
            if (isset($row[$entityname]) && isset($row[$id_fieldname]))
            {
                $recordset[$key][$entityname]['members']    = $this->GetUsers($row[$id_fieldname]);
                $recordset[$key][$entityname]['emails']     = explode(',', str_replace(array(';', ' '), array(',', ''), $row[$entityname]['email']));
            }            
        }
        
        return $this->FillQuickInfo($recordset, $id_fieldname, $entityname);
    }
    
    /**
     * Возвращает быстроменяющуюся информацию по team
     * 
     * @param array $recordset
     * @param mixed $id_fieldname
     * @param mixed $entityname
     */
    function FillQuickInfo($recordset, $id_fieldname = 'team_id', $entityname = 'team')
    {
        $recordset = $this->_fill_entity_info($recordset, $id_fieldname, $entityname . 'quick', 'teamquick', 'sp_team_get_quick_by_ids', array('teamsquick' => '', 'teams' => '', 'team' => 'id'), array());

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
     * Сохраняет team
     * 
     * @param mixed $id
     * @param mixed $title
     * @param mixed $description
     */
    function Save($id, $title, $description, $email)
    {        
        $result = $this->CallStoredProcedure('sp_team_save', array($this->user_id, $id, $title, $description, $email));
        $result = isset($result) && isset($result[0]) && isset($result[0][0]) ? $result[0][0] : null;
        
        if (empty($result) || array_key_exists('ErrorCode', $result)) return null;

        Cache::ClearTag('team-' . $id);
        Cache::ClearTag('teams');
        
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
        $result = $this->CallStoredProcedure('sp_team_remove', array($this->user_id, $id));
        $result = isset($result) && isset($result[0]) && isset($result[0][0]) ? $result[0][0] : null;
        
        if (empty($result) || array_key_exists('ErrorCode', $result)) return null;

        Cache::ClearTag('team-' . $id);
        Cache::ClearTag('teams');
        
        return $result;
    }
    
    /**
     * Возвращает список пользователей в команде
     * 
     * @param mixed $team_id
     */
    function GetUsers($team_id)
    {
        $hash       = 'team-' . $team_id . '-users';
        $cache_tags = array($hash, 'team-' . $team_id, 'teams');

        $rowset = $this->_get_cached_data($hash, 'sp_team_get_users', array($team_id), $cache_tags);
        
        $users  = new User();
        $rowset = isset($rowset[0]) ? $users->FillUserInfo($rowset[0]) : array();

        return $rowset;        
    }
    
    /**
     * Сохраняет пользователя в команде
     * 
     * @param mixed $team_id
     * @param mixed $user_id
     */
    function AddUser($team_id, $user_id)
    {
        $result = $this->CallStoredProcedure('sp_team_add_user', array($this->user_id, $team_id, $user_id));
        $result = isset($result) && isset($result[0]) && isset($result[0][0]) ? $result[0][0] : null;
        
        if (empty($result) || array_key_exists('ErrorCode', $result)) return null;

        Cache::ClearTag('team-' . $team_id . '-users');
        
        return $result;        
    }
    
    /**
     * Убирает пользователя из команды
     * 
     * @param mixed $team_id
     * @param mixed $user_id
     */
    function RemoveUser($team_id, $user_id)
    {
        $result = $this->CallStoredProcedure('sp_team_remove_user', array($team_id, $user_id));
        $result = isset($result) && isset($result[0]) && isset($result[0][0]) ? $result[0][0] : null;
        
        if (empty($result) || array_key_exists('ErrorCode', $result)) return null;

        Cache::ClearTag('team-' . $team_id . '-users');
        
        return $result;        
    }
}
