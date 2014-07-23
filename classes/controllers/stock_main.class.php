<?php
require_once APP_PATH . 'classes/common/mimetype.class.php';

require_once APP_PATH . 'classes/models/company.class.php';
require_once APP_PATH . 'classes/models/invoicingtype.class.php';
require_once APP_PATH . 'classes/models/location.class.php';
require_once APP_PATH . 'classes/models/paymenttype.class.php';
require_once APP_PATH . 'classes/models/stock.class.php';

require_once APP_PATH . 'classes/services/kcaptcha/kcaptcha.php';

class MainController extends ApplicationController
{
    function MainController()
    {
        ApplicationController::ApplicationController();
        
        $this->authorize_before_exec['index']       = ROLE_STAFF;
        $this->authorize_before_exec['add']         = ROLE_STAFF;
        $this->authorize_before_exec['edit']        = ROLE_STAFF;
        $this->authorize_before_exec['location']    = ROLE_STAFF;
        
        $this->breadcrumb = array('Stocks' => '/stocks');
    }
    
    /**
     * Отображает страницу списка складов
     * url: /stocks
     */
    function index()
    {
        $this->page_name = 'Stocks';
        
        $stocks = new Stock();
        $this->_assign('list', $stocks->GetList());
        
        $this->context = true;        
        $this->_display('index');
    }
    
    /**
     * Отображает страницу редактирования держателей склада
     * /stock/location
     */
    function location()
    {
        $this->page_name = 'Stockholders';
        $this->breadcrumb[$this->page_name] = '/stock/location';
        
        $companies = new Company();
        if (isset($_REQUEST['btn_save']))
        {
            $locations = $_REQUEST['location'];          //dg($locations);
            foreach ($companies->GetStockholdersList() as $key => $row)
            {
                $company_id = $row['company_id'];
                
                foreach ($locations as $index => $location) 
                {
                    if ($location['company_id'] == $company_id) 
                    {
                        // удаляет помеченные на удаление
                        if ($location['deleted'] > 0) 
                        {
                            $companies->RemoveLocation($company_id);
                            unset($locations[$index]);
                        }
                        
                        break;
                    }
                }                        
            }
            
            // обновляет локации
            foreach ($locations as $key => $row)
            {
                $companies->SaveLocation($row['company_id'], $row['location'], $row['int_location_title']);
            }
            
            Cache::ClearTag('stockholders');            
        }        
        
        $this->context  = true;
        $this->js       = 'stock_location';
        
        
        $locations = $companies->GetStockholdersList();
        
        $this->_assign('include_ui',    true);
        $this->_assign('locations',     $locations);
        
        $stock_location_last_id = 0;
        foreach ($locations as $row) if ($row['company_id'] > $stock_location_last_id) $stock_location_last_id = $row['company_id'];
        $this->_assign('stock_location_last_id', $stock_location_last_id);

        $this->_display('location');
    }
    
    /**
     * Отображает страницу добавления склада
     * url: /stock/add
     */
    function add()
    {
        $this->edit();
    }
    
    /**
     * Отображает страницу редактирования склада
     * url: /stock/edit/{id}
     */
    function edit()
    {
        $stock_id = Request::GetInteger('id', $_REQUEST);
        
        if ($stock_id > 0)
        {
            $stocks = new Stock();
            $stock  = $stocks->GetById($stock_id);
            
            if (empty($stock) || isset($stock['ErrorCode'])) _404();
        }
        
        if (isset($_REQUEST['btn_save']))
        {
            //dg($_REQUEST);
            $form = $_REQUEST['form'];
            
            $this->_assign('form', $form);
            
            $title              = Request::GetString('title', $form);
            $description        = Request::GetHtmlString('description', $form);
            $dimensions         = Request::GetString('dimensions', $form, '', 11);
            $currency           = Request::GetString('currency', $form, '', 3);
            $invoicingtype_id   = Request::GetInteger('invoicingtype_id', $form);
            $invoicingtype_new  = Request::GetString('invoicingtype_new', $form);
            $paymenttype_id     = Request::GetInteger('paymenttype_id', $form);
            $paymenttype_new    = Request::GetString('paymenttype_new', $form);
            $email_for_orders   = Request::GetString('email_for_orders', $form);
            $order_for          = Request::GetString('order_for', $form, '', 5);
            
            
            $stock_deliverytimes = '';
            if (isset($_REQUEST['deliverytime']))
            {
                foreach ($_REQUEST['deliverytime'] as $row)
                {
                    $stock_deliverytimes .= (empty($stock_deliverytimes) ? '' : ',') . Request::GetInteger($row, $_REQUEST['deliverytime']);
                }
            }
            
            $stock_columns = '';
            if (isset($_REQUEST['column']))
            {
                foreach ($_REQUEST['column'] as $row)
                {
                    $stock_columns .= (empty($stock_columns) ? '' : ',') . Request::GetString($row, $_REQUEST['column']);
                }
            }
            
            if (empty($title))
            {
                $this->_message('Title must be specified !', MESSAGE_ERROR);
            }
            else if (empty($dimensions))
            {
                $this->_message('Dimensions must be specified !', MESSAGE_ERROR);
            }
            else if (empty($currency))
            {
                $this->_message('Currency must be specified !', MESSAGE_ERROR);
            }
            else if (empty($invoicingtype_id) && empty($invoicingtype_new))
            {
                $this->_message('Invoicing basis must be specified !', MESSAGE_ERROR);
            }
            else if (empty($paymenttype_id) && empty($paymenttype_new))
            {
                $this->_message('Payment type must be specified !', MESSAGE_ERROR);
            }            
            else if (empty($email_for_orders))
            {
                $this->_message('Email for orders must be specified !', MESSAGE_ERROR);
            }            
            else if (empty($order_for))
            {
                $this->_message('Stock For must be specified !', MESSAGE_ERROR);
            }            
            else
            {
                $units = explode('/', $dimensions);
                
                if (empty($invoicingtype_id))
                {
                    $invoicingtypes     = new InvoicingType();
                    $invoicingtype_id   = $invoicingtypes->GetInvoicingTypeId($invoicingtype_new);
                }

                if (empty($paymenttype_id))
                {
                    $paymenttypes     = new PaymentType();
                    $paymenttype_id   = $paymenttypes->GetPaymentTypeId($paymenttype_new);
                }
                
                $stocks = new Stock();
                $result = $stocks->Save($stock_id, $title, $description, $units[0], $units[1], $currency, $invoicingtype_id, $paymenttype_id, 
                                        $stock_deliverytimes, $stock_columns, $email_for_orders, $order_for);
                
                if (empty($result))
                {
                    $this->_message('Error saving stock !', MESSAGE_ERROR);
                }
                else
                {
                    $locations = isset($_REQUEST['location']) ? $_REQUEST['location'] : array();
                    foreach ($stocks->GetLocations($result['id']) as $key => $row)
                    {
                        $company_id = $row['company_id'];                        
                        
                        if (!in_array($company_id, $locations)) $stocks->RemoveLocation($result['id'], $company_id);
                        
                        unset($locations[$company_id]);
                    }
                    
                    // добавляем новые
                    foreach ($locations as $key => $row) $stocks->SaveLocation($result['id'], $key);
                    
                    Cache::ClearTag('stock-locations-' . $stock_id);
                    
                    $this->_redirect(array('stocks'));
                }
            }            
        }
        else if ($stock_id > 0)
        {
            $this->_assign('form', $stock['stock']);
            
            $stock_deliverytimes    = $stock['stock']['deliverytimes'];
            $stock_columns          = $stock['stock']['visible_columns'];
        }
        
        if (empty($stock_id))
        {
            $stock_locations = array();
            
            $this->page_name = 'New Stock';
            $this->breadcrumb[$this->page_name] = '/stock/add';            
        }
        else
        {
            $stocks                 = new Stock();
            $stock_locations        = $stocks->GetLocations($stock_id);
            $deliverytimes          = $stocks->GetPositionDeliveryTimes($stock_id);
            
            $this->page_name = 'Edit Stock';
            $this->breadcrumb[$this->page_name] = '/stock/edit/' . $stock_id;
        }
        
        
        $companies  = new Company();
        $locations  = $companies->GetStockholdersList();

        if (!empty($stock_locations))
        {
            foreach ($locations as $key => $row)
            {
                foreach ($stock_locations as $stock_key => $stock_row)
                {
                    if ($row['company_id'] == $stock_row['company_id'])
                    {
                        $locations[$key]['checked'] = true;
                    }
                }
            }
        }
        $this->_assign('locations', $locations);

        
        if (isset($deliverytimes))
        {
            $stock_deliverytimes = empty($stock_deliverytimes) ? array() : explode(',', $stock_deliverytimes);
            foreach ($deliverytimes as $key => $row)
            {
                
                if (empty($stock_deliverytimes) || in_array($row['deliverytime_id'], $stock_deliverytimes)) 
                {
                    $deliverytimes[$key]['checked'] = true;
                }
            }
            
            $this->_assign('deliverytimes', $deliverytimes);            
        }
        
        if (isset($stock_columns))
        {
            foreach (explode(',', $stock_columns) as $column)
            {
                $this->_assign('column_' . $column, $deliverytimes);
            }
        }
        
        $invoicingtypes = new InvoicingType();
        $this->_assign('invoicingtypes', $invoicingtypes->GetList());

        $paymenttypes = new PaymentType();
        $this->_assign('paymenttypes', $paymenttypes->GetList());
        
        $companies = new Company();
        $this->_assign('mam_companies', $companies->GetMaMList());        
        
        $this->context  = true;
        $this->js       = 'stock_edit';
        
        $this->_display('edit');
    }    
    
    /**
     * Отображает страницу редактирования стоимости доставки для склада
     * url: /stock/deliverycost/{stock_id}
     */
    function deliverycost()
    {
        $stock_id = Request::GetInteger('id', $_REQUEST);
    }
}
