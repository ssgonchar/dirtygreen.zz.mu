<?php
require_once APP_PATH . 'classes/models/country.class.php';

class MainAjaxController extends ApplicationAjaxController
{    

    function MainAjaxController()
    {
        ApplicationAjaxController::ApplicationAjaxController();
                
        $this->authorize_before_exec['action']  = ROLE_STAFF;
        $this->authorize_before_exec['remove']  = ROLE_STAFF;
        $this->authorize_before_exec['save']    = ROLE_STAFF;
    }
    
    /**
     * remove country
     * url: /country/remove
     */
    function remove()
    {
        $country_id = Request::GetInteger('country_id', $_REQUEST);
        
        $countries  = new Country();
        $country    = $countries->GetById($country_id);

        if (empty($country)) $this->_send_json(array('result' => 'error'));
        
        $result = $countries->Remove($country_id);
        if (empty($result)) $this->_send_json(array('result' => 'error'));

        $this->_send_json(array('result' => 'okay'));
    }
    
    /**
     * get coutry edit row
     * url: /country/edit
     */
    function action()
    {
        $country_id = Request::GetInteger('country_id', $_REQUEST);
        $mode       = Request::GetString('mode', $_REQUEST);

        if (!in_array($mode, array('edit', 'view'))) $this->_send_json(array('result' => 'error'));
        
        
        $countries  = new Country();
        $country    = $countries->GetById($country_id);
        
        if (empty($country)) $this->_send_json(array('result' => 'error'));
        
        $this->_assign('country', $country['country']);
        $this->_send_json(array('result' => 'okay', 'content' => $this->smarty->fetch('templates/html/directory/control_country_' . $mode . '.tpl')));
    }    
    
    /**
     * save country
     * url: /country/save
     */
    function save()
    {
        $country_id = Request::GetInteger('country_id', $_REQUEST);
        $title      = Request::GetString('title', $_REQUEST, '', 250);
        $title1     = Request::GetString('title1', $_REQUEST, '', 250);
        $title2     = Request::GetString('title2', $_REQUEST, '', 250);
        $alpha2     = Request::GetString('alpha2', $_REQUEST, '', 2);
        $alpha3     = Request::GetString('alpha3', $_REQUEST, '', 3);
        $code       = Request::GetString('code', $_REQUEST);
        $dialcode   = Request::GetString('dialcode', $_REQUEST);
        $is_primary = Request::GetString('is_primary', $_REQUEST);
        
        $countries  = new Country();
        if ($country_id > 0)
        {            
            $country = $countries->GetById($country_id);            
            if (empty($country)) $this->_send_json(array('result' => 'error', 'message' => 'Incorrect Country Id !'));
        }
        
        $result = $countries->Save($country_id, $title, $title1, $title2, $alpha2, $alpha3, $code, $dialcode, $is_primary);
        
        if (empty($result)) $this->_send_json(array('result' => 'error', 'message' => 'Unknown error while saving country !'));
        if (isset($result['ErrorCode']))
        {
            if ($result['ErrorCode'] == -1) $this->_send_json(array('result' => 'error', 'message' => 'Such country already exists !'));
            if ($result['ErrorCode'] == -2) $this->_send_json(array('result' => 'error', 'message' => 'Such country does not exists !'));
        }
        
        $country = $countries->GetById($result['id']);

        $this->_assign('country', $country['country']);
        $this->_send_json(array('result' => 'okay', 'country_id' => $country['country']['id'], 'content' => $this->smarty->fetch('templates/html/directory/control_country_view.tpl')));
    }
}
