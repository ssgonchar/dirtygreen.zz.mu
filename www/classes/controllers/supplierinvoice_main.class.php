<?php
require_once APP_PATH . 'classes/core/Pagination.class.php';
require_once APP_PATH . 'classes/models/attachment.class.php';
require_once APP_PATH . 'classes/models/company.class.php';
require_once APP_PATH . 'classes/models/supplierinvoice.class.php';

class MainController extends ApplicationController
{
    public function MainController()
    {
        ApplicationController::ApplicationController();
        
        $this->authorize_before_exec['edit']    = ROLE_STAFF;
        $this->authorize_before_exec['delete']  = ROLE_STAFF;
        $this->authorize_before_exec['index']   = ROLE_STAFF;
        $this->authorize_before_exec['view']    = ROLE_STAFF;
        
        $this->breadcrumb   = array('Supplier Invoices' => '/supplierinvoices');
        $this->context      = true;
    }
    
    /**
     * Форма создания и редактирования инвойса
     * url: /supplierinvoice/add
     * url: /supplierinvoice/add/{object_alias}:{object_id}
     * url: /supplierinvoice/{invoice_id}/edit
     * 
     * @version 20130129, zharkov
     */
    function edit()
    {
        $source_doc_alias   = Request::GetString('source_doc',      $_REQUEST);
        $source_doc_id      = Request::GetString('source_doc_id',   $_REQUEST); // может быть несколько через запятую
        $supinvoice_id      = Request::GetInteger('id', $_REQUEST);        

        if ($supinvoice_id > 0)
        {
            $modelSupInvoice    = new SupplierInvoice();
            $supinvoice         = $modelSupInvoice->GetById($supinvoice_id);
            if (empty($supinvoice)) _404();
            
            $supinvoice                     = $supinvoice['supinvoice'];
            $supinvoice['company_title']    = isset($supinvoice['company']) ? $supinvoice['company']['doc_no'] : '';
            
            $items = $modelSupInvoice->GetItems($supinvoice_id);
        }
        else if (in_array($source_doc_alias, array('inddt')) && !empty($source_doc_id))
        {
            if ($source_doc_alias == 'inddt')
            {
                $items      = array();
                $supinvoice = array('owner_id' => 0);
                
                $modelInDDT = new InDDT();
                foreach(explode(',', $source_doc_id) as $inddt_id)
                {
                    $inddt = $modelInDDT->GetById($inddt_id);
                    if (empty($inddt)) continue;
                    
                    $inddt = $inddt['inddt'];
                    $supinvoice['company_id']       = $inddt['company_id'];
                    $supinvoice['company_title']    = isset($inddt['company']) ? $inddt['company']['doc_no'] : '';
                    $supinvoice['owner_id']         = $inddt['owner_id'] > 0 ? $inddt['owner_id'] : $supinvoice['owner_id'];
                    
                    foreach ($modelInDDT->GetItems($inddt_id) as $item)
                    {
                        // можно добавить только айтемы без инвойсов или с отмененными инвофсами от производителя
                        if (empty($item['steelitem']['supplier_invoice_id']) || $item['steelitem']['supplier_invoice']['status_id'] == SUPINVOICE_STATUS_CANCELLED)
                        {
                            $items[$item['steelitem_id']] = $item;    
                        }                        
                    }
                }            
            }
        }
        else
        {
            $items      = array();
            $supinvoice = array(
                'company_id'    => 0,
                'owner_id'      => 0
            );
        }
        
        
        $is_saving          = isset($_REQUEST['btn_save']);
        $is_adding_items    = isset($_REQUEST['btn_additems']);
        
        if ($is_saving || $is_adding_items)
        {
            $form = $_REQUEST['form'];
            
            $company_id     = Request::GetInteger('company_id', $form);
            $company_title  = Request::GetString('company_title', $form);
            $owner_id       = Request::GetInteger('owner_id', $form);
            $delivery_point = Request::GetString('delivery_point', $form);
            $number         = Request::GetString('number', $form);
            $date           = Request::GetDateForDB('date', $form);
            $status_id      = Request::GetInteger('status_id', $form);
            $amount_paid    = Request::GetNumeric('amount_paid', $form);
            $payment_type   = Request::GetInteger('payment_type', $form);
            $payment_days   = Request::GetInteger('payment_days', $form);
            $percent        = Request::GetInteger('percent', $form);
            $currency       = Request::GetString('currency', $form);
            $notes          = Request::GetString('notes', $form);
            
            $modelSupInvoice    = new SupplierInvoice();
            $okay_flag          = true;
            
            if (empty($owner_id))
            {
                $this->_message('Owner must be specified !', MESSAGE_ERROR);
                $okay_flag = false;
            }

            if (empty($delivery_point))
            {
                $this->_message('Delivery point must be specified !', MESSAGE_ERROR);
                $okay_flag = false;                
            }
            
            if ($is_saving)
            {
                if (empty($number))
                {
                    $this->_message('Number must be specified !', MESSAGE_ERROR);
                    $okay_flag = false;
                }
                else if (empty($date))
                {
                    $this->_message('Date must be specified !', MESSAGE_ERROR);
                    $okay_flag = false;
                }
                else if (empty($company_id) && empty($company_title))
                {
                    $this->_message('Company must be specified !', MESSAGE_ERROR);
                    $okay_flag = false;
                }
                else if (empty($payment_type) || empty($payment_days))
                {
                    $this->_message('Payment Terms must be specified !', MESSAGE_ERROR);
                    $okay_flag = false;
                }                
            }
            
            if ($okay_flag)
            {
                $result = $modelSupInvoice->Save($supinvoice_id, $number, $date, $company_id, $owner_id, $delivery_point, $payment_type, $payment_days, 
                                                    $status_id, $amount_paid, $percent, $currency, $notes);
                
                if (empty($result))
                {
                    $this->_message('Error saving supplier invoice !', MESSAGE_ERROR);
                    $okay_flag = false;
                }
            }
            else
            {
                $supinvoice = array_merge($supinvoice, $form);
            }            
            
            $request_items = isset($_REQUEST['items']) ? $_REQUEST['items'] : array();
            
            foreach ($items as $key => $item)
            {
                $steelitem_id = $item['steelitem_id'];
                
                if (isset($request_items[$steelitem_id]))
                {
                    $items[$key]['checked']                     = Request::GetInteger('checked', $request_items[$steelitem_id]);
                    $items[$key]['steelitem']['purchase_price'] = Request::GetNumeric('purchase_price', $request_items[$steelitem_id]);
                    $items[$key]['weight_invoiced']             = Request::GetNumeric('weight_invoiced', $request_items[$steelitem_id]);                    
                }
            }
            
            if ($okay_flag)
            {
                foreach ($request_items as $steelitem_id => $row)
                {
                    if ($supinvoice_id > 0 || isset($row['checked']))
                    {
                        $purchase_price     = Request::GetNumeric('purchase_price', $row);
                        $weight_invoiced    = Request::GetNumeric('weight_invoiced', $row);
                        
                        $modelSupInvoice->SaveItem($result['id'], $steelitem_id, $purchase_price, $weight_invoiced);
                    }                    
                }
                
                if ($is_adding_items)
                {
                    $this->_redirect(array('target', 'supinvoice:' . $result['id'], 'items'), false);
                }
                else
                {
                    $this->_message('Supplier Invoice was successfully saved', MESSAGE_OKAY);
                    $this->_redirect(array('supplierinvoices', 'filter', 'company:' . $company_id), false);
                }
            }            
        }
        

        $modelCompany = new Company();
        $this->_assign('owners', $modelCompany->GetMaMList());
        //$this->_assign('delivery_points', $modelCompany->GetStockholdersList());
        
        $this->page_name    = empty($supinvoice_id) ? 'New Supplier Invoice' : 'Edit Supplier Invoice';
        $this->js           = 'supinvoice_main';
        
        $this->breadcrumb[$this->page_name] = '';
        //debug('1671', $supinvoice);
        $this->_assign('form',          $supinvoice);
        $this->_assign('items',         $items);
        $this->_assign('firstitem',     current($items));
        
        $this->_assign('invoice_id',    $supinvoice_id);       
        $this->_assign('include_ui',    true);        
        
        $this->_display('edit');
    }
   
    /**
     * Список инвойсов
     * url: /invoices
     * url: /invoices/filter/{filter}
     * 
     * @version 20130129, zharkov
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
        
        $owner_id   = Request::GetInteger('owner', $filter_params, -1);
        $company_id = Request::GetInteger('company', $filter_params);
        $date_from  = null;
        $date_to    = null;
        $number     = Request::GetString('number', $filter_params);        
        
        $modelSupInvoice    = new SupplierInvoice();
        $rowset             = $modelSupInvoice->GetList($owner_id, $company_id, $date_from, $date_to, $number, $this->page_no);

        $this->page_name    = 'Supplier Invoices';
        $this->breadcrumb   = array($this->page_name => '');

        $this->_assign('owner_id',      $owner_id);
        $this->_assign('company_id',    $company_id);
        $this->_assign('date_from',     $date_from);
        $this->_assign('date_to',       $date_to);
        $this->_assign('number',        $number);
        
        $this->_assign('list',      $rowset['data']);
        $this->_assign('count',     $rowset['count']);        

        $pager = new Pagination();
        $this->_assign('pager_pages', $pager->PreparePages($this->page_no, $rowset['count']));
        
        $companies = new Company();
        $this->_assign('owners',    $companies->GetMaMList());
        
        $this->_display('index');
    }
    
    /**
     * Удаляет инвойс
     * url: /supplierinvoice/{invoice_id}/delete
     * 
     * @version 20130129, zharkov
     */
    public function delete()
    {
        $id = Request::GetInteger('id',  $_REQUEST);

        if ($id <= 0) _404();

        $modelSupInvoice    = new SupplierInvoice();
        $supinvoice         = $modelSupInvoice->GetById($id);
        if (empty($supinvoice)) _404();
        
        foreach ($modelSupInvoice->GetItems($id) as $row)
        {
            $modelSupInvoice->RemoveItem($id, $row['steelitem_id']);
        }
        
        $modelSupInvoice->Remove($id);
        
        $this->_message('Supplier Invoice was successfully removed', MESSAGE_OKAY);
        
        $this->_redirect(array('supplierinvoices'));
    }
    
    /**
     * Отображает страницу просмотра счета
     * @url /supplierinvoice/{supplierinvoice_id}
     * 
     * @version 20130129, zharkov
     */
    public function view()
    {
        $supinvoice_id = Request::GetInteger('id', $_REQUEST);        

        $modelSupInvoice    = new SupplierInvoice();
        $supinvoice         = $modelSupInvoice->GetById($supinvoice_id);
        if (empty($supinvoice)) _404();

        $objectcomponent    = new ObjectComponent();
        $page_params        = $objectcomponent->GetPageParams('supplierinvoice', $supinvoice_id);

        $this->page_name    = 'Supplier invoice '.$page_params['page_name'];
        $this->breadcrumb   = $page_params['breadcrumb'];
        
        $this->_assign('object_stat',   $page_params['stat']);                
        $this->_assign('form',          $supinvoice['supinvoice']);
        
        $items = $modelSupInvoice->GetItems($supinvoice_id);
        
        $this->_assign('items',         $items);
        $this->_assign('firstitem',     current($items));        
        
        $modelAttachment    = new Attachment();
        $attachments_list   = $modelAttachment->GetListByType('', 'supplierinvoice', $supinvoice_id);
        $this->_assign('attachments_list', $attachments_list['data']);
        
        $this->_display('view');        
    }
}