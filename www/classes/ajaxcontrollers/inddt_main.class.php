<?php
require_once APP_PATH . 'classes/models/inddt.class.php';

class MainAjaxController extends ApplicationAjaxController
{    

    function MainAjaxController()
    {
        ApplicationAjaxController::ApplicationAjaxController();
                
        $this->authorize_before_exec['removeitem'] = ROLE_STAFF;
    }
    
    /**
     * remove item from In DDT
     * url: /inddt/removeitem
     */
    function removeitem()
    {
        $inddt_item_id  = Request::GetInteger('inddt_item_id', $_REQUEST);
        if (empty($inddt_item_id)) $this->_send_json(array('result' => 'error'));
        
        $modelInDDT     = new InDDT();
        $result         = $modelInDDT->RemoveItem($inddt_item_id);
        if (empty($result)) $this->_send_json(array('result' => 'error'));
        
        $this->_send_json(array('result' => 'okay'));
    }
}
