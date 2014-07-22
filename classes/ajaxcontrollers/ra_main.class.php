<?php
require_once APP_PATH . 'classes/models/ra.class.php';

class MainAjaxController extends ApplicationAjaxController
{
    
    function MainAjaxController()
    {
        ApplicationAjaxController::ApplicationAjaxController();
                
        $this->authorize_before_exec['removevariant']   = ROLE_STAFF;
        $this->authorize_before_exec['removeitem']      = ROLE_STAFF;
    }
    
    /**
     * Remove item variant from RA
     * url: /ra/removevariant
     */
    function removevariant()
    {
        $ra_id      = Request::GetInteger('ra_id', $_REQUEST);
        $item_id    = Request::GetInteger('item_id', $_REQUEST);

        $modelRA    = new RA();
        $ra         = $modelRA->GetById($ra_id);
        if (empty($ra)) $this->_send_json(array('result' => 'error'));
        
        if ($ra['ra']['status_id'] != RA_STATUS_OPEN) $this->_send_json(array('result' => 'error'));
        
        $result = $modelRA->ItemRemove($ra_id, $item_id);        
        if (isset($result['ErrorCode'])) $this->_send_json(array('result' => 'error'));
        
        $this->_send_json(array('result' => 'okay'));
    }
    
    /**
     * Remove items (variants) from RA
     * url: /ra/removeitem
     * 
     * @version 20121022, d10n
     */
    function removeitem()
    {
        $ra_id      = Request::GetInteger('ra_id', $_REQUEST);
        $item_id    = Request::GetInteger('item_id', $_REQUEST);
        $parent_id  = Request::GetInteger('parent_id', $_REQUEST);

        $modelRA    = new RA();
        $ra         = $modelRA->GetById($ra_id);
        if (empty($ra)) $this->_send_json(array('result' => 'error'));
        
        if ($ra['ra']['status_id'] != RA_STATUS_OPEN) $this->_send_json(array('result' => 'error'));

        $result = $modelRA->ItemRemove($ra_id, $item_id);        
        if (isset($result['ErrorCode'])) $this->_send_json(array('result' => 'error'));
        
        $this->_send_json(array('result' => 'okay'));
    }
}