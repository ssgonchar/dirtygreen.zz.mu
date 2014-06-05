<?php
require_once APP_PATH . 'classes/models/activity.class.php';

class MainAjaxController extends ApplicationAjaxController
{    

    function MainAjaxController()
    {
        ApplicationAjaxController::ApplicationAjaxController();
                
        $this->authorize_before_exec['action']  = ROLE_STAFF;
        $this->authorize_before_exec['remove']  = ROLE_STAFF;
        $this->authorize_before_exec['save']    = ROLE_STAFF;
        $this->authorize_before_exec['getlist'] = ROLE_STAFF;
    }
    
    /**
     * remove activities list
     * url: /activity/getlist
     */
    function getlist()
    {
        $parent_id  = Request::GetInteger('parent_id', $_REQUEST);

        $activities = new Activity();
        $this->_send_json(array('result' => 'okay', 'list' => $this->_prepare_list($activities->GetList($parent_id), 'activity')));
    }

    /**
     * remove activity
     * url: /activity/remove
     */
    function remove()
    {
        $activity_id = Request::GetInteger('activity_id', $_REQUEST);
        
        $activities  = new Activity();
        $activity    = $activities->GetById($activity_id);

        if (empty($activity)) $this->_send_json(array('result' => 'error'));
        
        $result = $activities->Remove($activity_id);
        if (empty($result)) $this->_send_json(array('result' => 'error'));

        $this->_send_json(array('result' => 'okay'));
    }
    
    /**
     * get activity row for edit
     * url: /activity/edit
     */
    function action()
    {
        $activity_id    = Request::GetInteger('activity_id', $_REQUEST);
        $mode           = Request::GetString('mode', $_REQUEST);

        if (!in_array($mode, array('edit', 'view'))) $this->_send_json(array('result' => 'error'));
        
        
        $activities     = new Activity();
        $activity       = $activities->GetById($activity_id);
        
        if (empty($activity)) $this->_send_json(array('result' => 'error'));
        
        $this->_assign('activity', $activity['activity']);
        $this->_send_json(array('result' => 'okay', 'content' => $this->smarty->fetch('templates/html/directory/control_activity_' . $mode . '.tpl')));
    }    
    
    /**
     * save activity
     * url: /activity/save
     */
    function save()
    {
        $activity_id    = Request::GetInteger('activity_id', $_REQUEST);        
        $parent_id      = Request::GetInteger('parent_id', $_REQUEST);
        $title          = Request::GetHtmlString('title', $_REQUEST, '', 250);
        
        $activities  = new Activity();
        if ($activity_id > 0)
        {            
            $activity = $activities->GetById($activity_id);            
            if (empty($activity)) $this->_send_json(array('result' => 'error', 'message' => 'Incorrect activity Id !'));
        }
        
        if (empty($title)) $this->_send_json(array('result' => 'error', 'message' => 'Title must be specified !'));
        
        $result = $activities->Save($activity_id, $parent_id, $title);
        
        if (empty($result)) $this->_send_json(array('result' => 'error', 'message' => 'Unknown error while saving activity !'));
        if (isset($result['ErrorCode']))
        {
            if ($result['ErrorCode'] == -1) $this->_send_json(array('result' => 'error', 'message' => 'Such activity already exists !'));
            if ($result['ErrorCode'] == -2) $this->_send_json(array('result' => 'error', 'message' => 'Such activity does not exists !'));
        }
        
        $activity = $activities->GetById($result['id']);

        $this->_assign('activity', $activity['activity']);
        $this->_send_json(array('result' => 'okay', 'activity_id' => $activity['activity']['id'], 'content' => $this->smarty->fetch('templates/html/directory/control_activity_view.tpl')));
    }
}
