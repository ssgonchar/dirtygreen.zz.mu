<?php
require_once APP_PATH . 'classes/models/city.class.php';

class MainAjaxController extends ApplicationAjaxController
{    

    function MainAjaxController()
    {
        ApplicationAjaxController::ApplicationAjaxController();
                
        $this->authorize_before_exec['action']  = ROLE_STAFF;
        $this->authorize_before_exec['getlist'] = ROLE_STAFF;
        $this->authorize_before_exec['remove']  = ROLE_STAFF;
        $this->authorize_before_exec['save']    = ROLE_STAFF;
    }
    
    /**
     * get cities list
     * url: /city/getlist
     * 
     * @version 20120501, zharkov
     */
    function getlist()
    {
        $region_id = Request::GetInteger('region_id', $_REQUEST);

        $cities = new City();
        $this->_send_json(array('result' => 'okay', 'list' => $this->_prepare_list($cities->GetList($region_id), 'city')));        
    }

    /**
     * remove city
     * url: /city/remove
     */
    function remove()
    {
        $city_id = Request::GetInteger('city_id', $_REQUEST);
        
        $cities  = new City();
        $city    = $cities->GetById($city_id);

        if (empty($city)) $this->_send_json(array('result' => 'error'));
        
        $result = $cities->Remove($city_id);
        if (empty($result)) $this->_send_json(array('result' => 'error'));

        $this->_send_json(array('result' => 'okay'));
    }
    
    /**
     * get city edit row
     * url: /city/edit
     */
    function action()
    {
        $city_id    = Request::GetInteger('city_id', $_REQUEST);
        $mode       = Request::GetString('mode', $_REQUEST);

        if (!in_array($mode, array('edit', 'view'))) $this->_send_json(array('result' => 'error'));
        
        
        $cities     = new City();
        $city       = $cities->GetById($city_id);
        
        if (empty($city)) $this->_send_json(array('result' => 'error'));
        
        $this->_assign('city', $city['city']);
        $this->_send_json(array('result' => 'okay', 'content' => $this->smarty->fetch('templates/html/directory/control_city_' . $mode . '.tpl')));
    }    
    
    /**
     * save city
     * url: /city/save
     */
    function save()
    {
        $city_id    = Request::GetInteger('city_id', $_REQUEST);        
        $country_id = Request::GetInteger('country_id', $_REQUEST);
        $region_id  = Request::GetInteger('region_id', $_REQUEST);        
        $title      = Request::GetHtmlString('title', $_REQUEST, '', 250);
        $title1     = Request::GetHtmlString('title1', $_REQUEST, '', 250);
        $title2     = Request::GetHtmlString('title2', $_REQUEST, '', 250);
        $dialcode   = Request::GetHtmlString('dialcode', $_REQUEST);
        
        $cities  = new City();
        if ($city_id > 0)
        {            
            $city = $cities->GetById($city_id);            
            if (empty($city)) $this->_send_json(array('result' => 'error', 'message' => 'Incorrect City Id !'));
        }
        
        if (empty($title)) $this->_send_json(array('result' => 'error', 'message' => 'Title must be specified !'));
        
        $result = $cities->Save($city_id, $country_id, $region_id, $title, $title1, $title2, $dialcode);
        
        if (empty($result)) $this->_send_json(array('result' => 'error', 'message' => 'Unknown error while saving city !'));
        if (isset($result['ErrorCode']))
        {
            if ($result['ErrorCode'] == -1) $this->_send_json(array('result' => 'error', 'message' => 'Such city already exists !'));
            if ($result['ErrorCode'] == -2) $this->_send_json(array('result' => 'error', 'message' => 'Such city does not exists !'));
        }
        
        $city = $cities->GetById($result['id']);

        $this->_assign('city', $city['city']);
        $this->_send_json(array('result' => 'okay', 'city_id' => $city['city']['id'], 'content' => $this->smarty->fetch('templates/html/directory/control_city_view.tpl')));
    }
}
