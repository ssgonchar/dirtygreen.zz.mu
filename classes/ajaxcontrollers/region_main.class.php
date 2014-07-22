<?php
require_once APP_PATH . 'classes/models/region.class.php';

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
     * Get regions list for select
     * url: /region/getlist/{country_id}
     */
    function getlist()
    {
        $country_id = Request::GetInteger('country_id', $_REQUEST);

        $regions = new Region();
        $this->_send_json(array('result' => 'okay', 'list' => $this->_prepare_list($regions->GetList($country_id), 'region')));        
    }
    
    /**
     * Remove region
     * url: /region/remove
     */
    function remove()
    {
        $region_id = Request::GetInteger('region_id', $_REQUEST);
        
        $regions  = new Region();
        $region    = $regions->GetById($region_id);

        if (empty($region)) $this->_send_json(array('result' => 'error'));
        
        $result = $regions->Remove($region_id);
        if (empty($result)) $this->_send_json(array('result' => 'error'));

        $this->_send_json(array('result' => 'okay'));
    }
    
    /**
     * Get region row for edit
     * url: /region/edit
     */
    function action()
    {
        $region_id  = Request::GetInteger('region_id', $_REQUEST);
        $mode       = Request::GetString('mode', $_REQUEST);

        if (!in_array($mode, array('edit', 'view'))) $this->_send_json(array('result' => 'error'));
        
        
        $regions  = new Region();
        $region    = $regions->GetById($region_id);
        
        if (empty($region)) $this->_send_json(array('result' => 'error'));
        
        $this->_assign('region', $region['region']);
        $this->_send_json(array('result' => 'okay', 'content' => $this->smarty->fetch('templates/html/directory/control_region_' . $mode . '.tpl')));
    }    
    
    /**
     * Save region
     * url: /region/save
     */
    function save()
    {
        $region_id  = Request::GetInteger('region_id', $_REQUEST);
        $country_id = Request::GetInteger('country_id', $_REQUEST);
        $title      = Request::GetHtmlString('title', $_REQUEST, '', 250);
        $title1     = Request::GetHtmlString('title1', $_REQUEST, '', 250);
        $title2     = Request::GetHtmlString('title2', $_REQUEST, '', 250);
        
        $regions  = new Region();
        if ($region_id > 0)
        {            
            $region = $regions->GetById($region_id);            
            if (empty($region)) $this->_send_json(array('result' => 'error', 'message' => 'Incorrect Region Id !'));
        }
        
        $result = $regions->Save($region_id, $country_id, $title, $title1, $title2);
        
        if (empty($result)) $this->_send_json(array('result' => 'error', 'message' => 'Unknown error while saving region !'));
        if (isset($result['ErrorCode']))
        {
            if ($result['ErrorCode'] == -1) $this->_send_json(array('result' => 'error', 'message' => 'Such region already exists !'));
            if ($result['ErrorCode'] == -2) $this->_send_json(array('result' => 'error', 'message' => 'Such region does not exists !'));
        }
        
        $region = $regions->GetById($result['id']);

        $this->_assign('region', $region['region']);
        $this->_send_json(array('result' => 'okay', 'region_id' => $region['region']['id'], 'content' => $this->smarty->fetch('templates/html/directory/control_region_view.tpl')));
    }
}
