<?php
require_once APP_PATH . 'classes/models/invoice.class.php';

class MainAjaxController extends ApplicationAjaxController
{    

    function MainAjaxController()
    {
        ApplicationAjaxController::ApplicationAjaxController();
                
        $this->authorize_before_exec['getlist']     = ROLE_STAFF;
        $this->authorize_before_exec['removeitem']  = ROLE_STAFF;
    }
    
    /**
     * get invoices list // Возвращает список счетов
     * url: /invoice/getlist
     */
    function getlist()
    {
        $owner_id       = Request::GetInteger('owner_id', $_REQUEST);

        $modelInvoice   = new Invoice();
        $list           = $modelInvoice->GetList(0, $owner_id, '', '', 0, 100);
        
        $this->_send_json(array(
            'result' => 'okay', 
            'list' => $this->_prepare_list($list['data'], 'invoice', 'id', 'doc_no')
        ));
    }
    
    /**
     * remove item from invoice // Удаляет айтем из Invoice
     * url: /inddt/removeitem
     */
    public function removeitem()
    {
        $invoice_id     = Request::GetInteger('invoice_id', $_REQUEST);
        $steelitem_id   = Request::GetInteger('steelitem_id', $_REQUEST);
        
        if ($invoice_id <= 0 || $steelitem_id <= 0) $this->_send_json(array('result' => 'error'));
        
        $modelInvoice   = new Invoice();
        $modelInvoice->RemoveItem($invoice_id, $steelitem_id);
        
        $this->_send_json(array('result' => 'okay'));
    }
}
