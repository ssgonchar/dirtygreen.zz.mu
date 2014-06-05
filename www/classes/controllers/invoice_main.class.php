<?php
require_once APP_PATH . 'classes/core/Pagination.class.php';
require_once APP_PATH . 'classes/models/attachment.class.php';
require_once APP_PATH . 'classes/models/biz.class.php';
require_once APP_PATH . 'classes/models/company.class.php';
require_once APP_PATH . 'classes/models/invoice.class.php';
require_once APP_PATH . 'classes/models/order.class.php';

class MainController extends ApplicationController
{
    public function MainController()
    {
        ApplicationController::ApplicationController();
        
        $this->authorize_before_exec['edit']    = ROLE_STAFF;
        $this->authorize_before_exec['delete']  = ROLE_STAFF;
        $this->authorize_before_exec['index']   = ROLE_STAFF;
        $this->authorize_before_exec['view']    = ROLE_STAFF;
        
        $this->breadcrumb   = array('Invoices' => '/invoices');
        $this->context      = true;
    }
    
    /**
     * Форма создания и редактирования инвойса
     * url: /invoice/add
     * url: /invoice/add/{object_alias}:{object_id}
     * url: /invoice/{invoice_id}/edit
     * 
     * @version 20130115, zharkov
     */
    function edit()
    {
        $source_doc_alias   = Request::GetString('source_doc',      $_REQUEST);
        $source_doc_id      = Request::GetInteger('source_doc_id',  $_REQUEST);
        $invoice_id         = Request::GetInteger('id',             $_REQUEST);        

        if ($invoice_id > 0)
        {
            $modelInvoice   = new Invoice();
            $invoice        = $modelInvoice->GetById($invoice_id);
            
            if (empty($invoice)) _404();
            $invoice = $invoice['invoice'];
        }
        else if (in_array($source_doc_alias, array('order')) && $source_doc_id > 0)
        {
            if ($source_doc_alias == 'order')
            {
                $modelOrder = new Order();
                $order      = $modelOrder->GetById($source_doc_id);
                
                if (empty($order)) _404();
                $order      = $order['order'];
                $invoice    = array(
                    'biz'           => (isset($order['biz']) ? $order['biz'] : null),
                    'customer_id'   => $order['company_id'],                    
                    'is_closed'     => 0,
                    'owner_id'      => -1,
                    'order_id'      => $source_doc_id
                );
            }
        }
        else
        {
            $invoice = array(
                'is_closed' => 0,
                'owner_id'  => -1
            );
        }
        
        
        $is_saving          = isset($_REQUEST['btn_save']);
        $is_adding_items    = isset($_REQUEST['btn_additems']);
        
        
        if ($is_saving || $is_adding_items)
        {
            $form = $_REQUEST['form'];
            
            $owner_id           = Request::GetInteger('owner_id',       $form, -1);
            $biz_title          = Request::GetString('biz_title',       $form);
            $biz_id             = Request::GetInteger('biz_id',         $form);
            $customer_id        = Request::GetInteger('customer_id',    $form);
            $number             = trim(Request::GetString('number',     $form));
            $date               = Request::GetDateForDB('date',         $form);
            $due_date           = Request::GetDateForDB('due_date',     $form);
            $status_id          = Request::GetInteger('status_id',      $form);
            $amount_received    = Request::GetString('amount_received', $form);
            $is_closed          = Request::GetInteger('is_closed',      $form);
            $is_closed          = ($is_closed > 0 && !empty($number));
            
            $modelInvoice   = new Invoice();
            $okay_flag      = true;
            
            if (empty($invoice_id) && $owner_id < 0)
            {
                $this->_message('Type must be specified !', MESSAGE_ERROR);
                $okay_flag = false;
            }

            if ($is_saving)
            {
                if (empty($invoice['is_closed']) && $customer_id <= 0)
                {
                    $this->_message('Customer must be specified !', MESSAGE_ERROR);
                    $okay_flag = false;
                }
                else if (!empty($number) && empty($date))
                {
                    $this->_message('Date must be specified !', MESSAGE_ERROR);
                    $okay_flag = false;
                }                
            }
            
            if ($okay_flag)
            {
                $order_id   = $source_doc_alias == 'order' ? $source_doc_id : 0;
                $result     = $modelInvoice->Save($invoice_id, $order_id, $owner_id, $biz_id, $customer_id, $number, $date, $due_date, $status_id, $amount_received);
                
                if (empty($result))
                {
                    $this->_message('Error saving invoice !', MESSAGE_ERROR);
                    $okay_flag = false;
                }
            }
            else
            {
                $invoice = array_merge($invoice, $form);
            }            
            
            if ($okay_flag)
            {
                if (empty($invoice_id))
                {
                    $steelitems = isset($_REQUEST['steelitems']) ? $_REQUEST['steelitems'] : array();
                    foreach ($steelitems as $steelitem_id)
                    {
                        $modelInvoice->SaveItem($result['id'], $steelitem_id);
                    }
                }
                
                if ($is_adding_items)
                {
                    $this->_redirect(array('target', 'invoice:' . $result['id'], 'items'), false);
                }
                else
                {
                    if ($is_closed) $modelInvoice->CloseInvoice($result['id']);

                    $this->_message('Invoice was successfully saved', MESSAGE_OKAY);
                    $this->_redirect(array('invoices', 'filter', 'owner:' . $invoice['owner_id']), false);
                }
            }            
        }

        
        // список айтемов
        if ($invoice_id > 0)
        {
            $steelitems = $modelInvoice->GetItems($invoice_id);
        }
        else if (!empty($source_doc_alias) && !empty($source_doc_id))
        {
            if ($source_doc_alias == 'order')
            {
                $item_status    = ITEM_STATUS_INVOICED + 1; // include Invoiced Items
                $modelOrder     = new Order();
                $steelitems     = $modelOrder->GetOrdersItems($source_doc_id, $item_status);
            }
        }
        else
        {
            $steelitems = array();
        }
        

        // формирование списка OWNERs
        $modelCompany = new Company();
        $this->_assign('owners_list', $modelCompany->GetMaMList());
        
        // формирование списка CUSTOMERs
        $customers_list = array();
        if (isset($invoice['biz']) && $invoice['biz']['id'] > 0)
        {
            $modelBiz       = new Biz();
            $customers_list = $modelBiz->GetCompanies($invoice['biz']['id'], 'buyer');
            
        }
        $this->_assign('customers_list', $customers_list);
        
        $this->page_name    = empty($invoice_id) ? 'New Invoice' : 'Edit Invoice';
        $this->js           = 'invoice_main';
        
        $this->breadcrumb[$this->page_name] = '';
        
        $this->_assign('form',              $invoice);
        $this->_assign('steelitems',        $steelitems);
        $this->_assign('invoice_id',        $invoice_id);
        
        $this->_assign('allow_add_items',   ($invoice_id > 0 || empty($source_doc_alias)) && empty($invoice['is_closed']));
        
        $this->_assign('include_ui',        true);        
        
        $this->_display('edit');
    }
   
    /**
     * Список инвойсов
     * url: /invoices
     * url: /invoices/filter/{filter}
     * 
     */
    public function index()
    {
        $filter         = Request::GetString('filter', $_REQUEST);
        $filter         = urldecode($filter);
        $filter_params  = array();
        
        $filter = explode(';', $filter);
        foreach ($filter as $row)
        {
            if (empty($row)) continue;
            
            $param = explode(':', $row);
            $filter_params[$param[0]] = Request::GetHtmlString(1, $param);
        }
        
        $order_id   = Request::GetInteger('order', $filter_params);
        $owner_id   = Request::GetInteger('owner', $filter_params, -1);
        $iva_number = Request::GetString('iva', $filter_params);
        $number     = Request::GetString('number', $filter_params);
        
        $this->page_name    = 'Invoices';
        $this->breadcrumb   = array($this->page_name => '');

        $modelInvoice   = new Invoice();
        $rowset         = $modelInvoice->GetList($owner_id, $this->page_no);

        $this->_assign('list',      $rowset['data']);
        $this->_assign('owner_id',  $owner_id);
        $this->_assign('count',     $rowset['count']);        

        $pager = new Pagination();
        $this->_assign('pager_pages', $pager->PreparePages($this->page_no, $rowset['count']));
        
        $companies = new Company();
        $this->_assign('owners',    $companies->GetMaMList());
        
        $this->_display('index');
    }
    
    /**
     * Удаляет инвойс
     * url: /invoice/{invoice_id}/delete
     * 
     */
    public function delete()
    {
        $id = Request::GetInteger('id',  $_REQUEST);
        
        if ($id <= 0) _404();
        
        $modelInvoice = new Invoice();
        $invoice = $modelInvoice->GetById($id);
        
        if (!isset($invoice['invoice'])) _404();
        
        $invoice = $invoice['invoice'];
        
        if ($invoice['is_closed'] == 1) _404();
        
        $steelitems_list = $modelInvoice->GetItems($id);
        
        foreach ($steelitems_list as $steelitem)
        {
            $modelInvoice->RemoveItem($id, $steelitem['steelitem']['id']);
        }
        
        $modelInvoice->RemoveInvoice($id);
        
        $this->_message('Invoice was successfully removed', MESSAGE_OKAY);
        $this->_redirect(array('invoice'));
    }
    
    public function view()
    {
        $id = Request::GetInteger('id',  $_REQUEST);
        
        $modelAttachment    = new Attachment();
        $attachments_list   = $modelAttachment->GetListByType('', 'invoice', $id);
        $this->_assign('attachments_list', $attachments_list['data']);
        
        $this->edit();
    }
}