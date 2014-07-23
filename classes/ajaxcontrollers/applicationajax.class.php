<?php

class ApplicationAjaxController extends AjaxController
{
    var $user_id        = 0;
    var $user_login     = '';
    var $user_role      = ROLE_ALL;
    var $session_id     = '';
    
    var $lang           = DEFAULT_LANG;
    var $app_settings   = array();
    
    function ApplicationAjaxController()
    {
        AjaxController::AjaxController();
        
        if (array_key_exists('user', $_SESSION))
        {
            $this->user_id      = Request::GetInteger('id',         $_SESSION['user'], 0);
            $this->user_login   = Request::GetString('login',       $_SESSION['user'], '');
            $this->user_role    = Request::GetInteger('role_id',    $_SESSION['user'], ROLE_ALL);
        }
        
        $this->session_id   = Request::GetString('session_id', $_SESSION, '', 50);        
        $this->lang         = Request::GetString('lang', $_REQUEST, '', 2);
        $this->app_settings = isset($_SESSION['app_settings']) ? $_SESSION['app_settings'] : array();
        
        // update user access time        
        if (!empty($this->user_id) && $_REQUEST['module'] != 'service') Cache::SetKey('activeuser-' . $this->user_id, time());
    }
    
    /**
     * prepare list for output
     * 
     * @param mixed $entityname
     * @param mixed $id_field
     * @param mixed $name_field
     */
    function _prepare_list($rowset, $entity, $id_field = 'id', $name_field = 'title')
    {
        $result = array();                
        foreach($rowset as $row) 
        {
            if ($row[$entity][$id_field] > 0) $result[] = array('id' => $row[$entity][$id_field], 'name' => $row[$entity][$name_field]);
        }
        
        return $result;
    }    
}