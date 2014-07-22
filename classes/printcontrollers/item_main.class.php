<?php
require_once APP_PATH . 'classes/models/biz.class.php';
require_once APP_PATH . 'classes/models/location.class.php';
require_once APP_PATH . 'classes/models/order.class.php';
require_once APP_PATH . 'classes/models/qc.class.php';
require_once APP_PATH . 'classes/models/steelgrade.class.php';
require_once APP_PATH . 'classes/models/steelitem.class.php';
require_once APP_PATH . 'classes/models/steelposition.class.php';
require_once APP_PATH . 'classes/models/stock.class.php';
require_once APP_PATH . 'classes/models/supplierinvoice.class.php';

class MainPrintController extends ApplicationPrintController
{
    function MainPrintController()
    {
        ApplicationPrintController::ApplicationPrintController();
        
        $this->authorize_before_exec['index']           = ROLE_STAFF;
    }
    
    /**
     * Отображает страницу со списоком items
     * url: /items
     * url: /items/filter/{filter}
     * 
     * @version 20120808, zharkov: убрал выбор deliverytime потому что его нет у айтема, только у позиции, и в этом списке фильтрация по нему не нужна
     */
    function index()
    {
        // 20120812, zharkov: привязка к документу
        $target_doc     = Request::GetString('target_doc', $_REQUEST);
        $target_doc_id  = Request::GetInteger('target_doc_id', $_REQUEST);
        

        if (isset($_REQUEST['btn_select']))
        {
            $form = $_REQUEST['form'];
            
            $locations  = '';
            if (isset($form['stockholder']))
            {
                foreach ($form['stockholder'] as $key => $location_id)
                {
                    $location_id = Request::GetInteger($key, $form['stockholder']);
                    if ($location_id <= 0) continue;
                    
                    $locations = $locations . (empty($locations) ? '' : ',') . $location_id;
                }
            }

            $deliverytimes  = '';
            if (isset($form['deliverytime']))
            {
                foreach ($form['deliverytime'] as $key => $deliverytime_id)
                {
                    $deliverytime_id = Request::GetInteger($key, $form['deliverytime']);
                    if ($deliverytime_id <= 0) continue;
                    
                    $deliverytimes = $deliverytimes . (empty($deliverytimes) ? '' : ',') . $deliverytime_id;
                }
            }

            $types  = '';
            if (isset($form['type']))
            {
                foreach ($form['type'] as $key => $type_id)
                {
                    $type_id = Request::GetString($key, $form['type']);
                    if (empty($type_id)) continue;
                    
                    $types = $types . (empty($types) ? '' : ',') . $type_id;
                }
            }
            
            
            $stock_id       = Request::GetInteger('stock_id', $form);
            $steelgrade_id  = Request::GetInteger('steelgrade_id', $form);
            $plate_id       = str_replace(' ', '&nbsp;', Request::GetString('plate_id', $form));
            $thickness      = Request::GetString('thickness', $form);
            $width          = Request::GetString('width', $form);
            $length         = Request::GetString('length', $form);
            $weight         = Request::GetString('weight', $form);
            $notes          = Request::GetString('notes', $form);
            $rev_date       = Request::GetDateForDB('rev_date', $form);
            $rev_time       = Request::GetString('rev_time', $form);
            $rev_id         = null;
            $available      = Request::GetInteger('available', $form);
            $order_id       = Request::GetInteger('order_id', $form);
            
            if (!empty($rev_date))
            {
                $stocks = new Stock();
                $rev_id = $stocks->CheckRevision($rev_date, $rev_time);
            }
            
            // Если param_order_id > 0 нужно игнорировать фильтры из "Location" и "Type".
            if ($order_id > 0)
            {
                $locations  = '';
                $types      = '';
            }
            

            $filter     = (empty($stock_id) ? '' : 'stock:' . $stock_id . ';')
                        . (empty($locations) ? '' : 'location:' . $locations . ';')
                        . (empty($deliverytimes) ? '' : 'deliverytime:' . $deliverytimes . ';')
                        . (empty($types) ? '' : 'type:' . $types . ';')
                        . (empty($plate_id) ? '' : 'plateid:' . $plate_id . ';')
                        . (empty($steelgrade_id) ? '' : 'steelgrade:' . $steelgrade_id . ';')
                        . (empty($thickness) ? '' : 'thickness:' . $thickness . ';')
                        . (empty($width) ? '' : 'width:' . $width . ';')
                        . (empty($length) ? '' : 'length:' . $length . ';')
                        . (empty($weight) ? '' : 'weight:' . $weight . ';')
                        . (empty($notes) ? '' : 'notes:' . $notes . ';')
                        . (empty($available) ? '' : 'available:' . $available . ';')
                        . (empty($order_id) ? '' : 'order:' . $order_id . ';');
            
            
            $redirect = array();
            
            if (!empty($target_doc) && !empty($target_doc_id))
            {
                $redirect[] = 'target';
                $redirect[] = $target_doc . ':' . $target_doc_id;
            }
            
            $redirect[] = 'items';
            $redirect[] = 'filter';
            $redirect[] = preg_replace('#\s+#i', '', $filter);
            
            if (!empty($rev_id))
            {
                $redirect[] = '~rev' . $rev_id;
            }

            $this->_redirect($redirect, false);
        }
        
        
        $filter         = Request::GetString('filter', $_REQUEST);
        $filter         = urldecode($filter);
        $filter_params  = array();
        
        $stocks = new Stock();
        $this->_assign('stocks', $stocks->GetList());        
        
        
        if (empty($filter))
        {
            $this->page_name = 'Items';
        }
        else
        {
            $this->page_name            = 'Filtered Items';            
            $this->breadcrumb['Items']  = '/items';
            
            $filter = explode(';', $filter);
            foreach ($filter as $row)
            {
                if (empty($row)) continue;
                
                $param = explode(':', $row);
                $filter_params[$param[0]] = Request::GetHtmlString(1, $param);
            }
            
            $this->_assign('filter', true);
        }
        
        if (!empty($target_doc))
        {
            if ($target_doc == 'qc') $this->page_name = 'Items for Certificate Of Quality';
            if ($target_doc == 'invoice') $this->page_name = 'Items for Invoice';
        }
                    
        $this->breadcrumb[$this->page_name] = '';
        

        $product_id             = Request::GetInteger('product', $filter_params);
        $stock_id               = Request::GetInteger('stock', $filter_params);
        $selected_locations     = isset($filter_params['location']) ? explode(',', $filter_params['location']) : array();
        $selected_deliverytimes = isset($filter_params['deliverytime']) ? explode(',', $filter_params['deliverytime']) : array();
        $selected_types         = isset($filter_params['type']) ? explode(',', $filter_params['type']) : array();
        $steelgrade_id          = Request::GetInteger('steelgrade', $filter_params);
        $plate_id               = Request::GetString('plateid', $filter_params);
        $thickness              = Request::GetString('thickness', $filter_params);
        $width                  = Request::GetString('width', $filter_params);
        $length                 = Request::GetString('length', $filter_params);
        $weight                 = Request::GetString('weight', $filter_params);
        $notes                  = Request::GetString('notes', $filter_params);
        $available              = Request::GetString('available', $filter_params);
        $revision               = Request::GetString('stock_revision', $_REQUEST, '', 12);
        $order_id               = Request::GetInteger('order', $filter_params);
        
        if (!empty($revision))
        {
            $this->_assign('rev_date',      substr($revision, 6, 2) . '/' . substr($revision, 4, 2) . '/' . substr($revision, 0, 4));
            $this->_assign('rev_time',      substr($revision, 8, 2) . ':' . substr($revision, 10, 2));            
        }
        
        // Если param_order_id > 0 нужно игнорировать фильтры из "Location" и "Type".
        if ($order_id > 0)
        {
            $selected_locations = array();
            $selected_types     = array();
        }
        
        $this->_assign('is_revision', (empty($revision) ? 0 : 1));
        
        // start костылеr для определения stock_id (есть стокхолдер, но нет склада)
        if ($stock_id <= 0 && !empty($selected_locations))
        {
            $modelStock         = new Stock();
            $stock_locations    = $modelStock->GetLocations(0, false);
            foreach ($stock_locations as $location)
            {
                if (in_array($location['company_id'], $selected_locations))
                {
                    $stock_id = $location['stock_id'];
                    break;                    
                }
            }
        }
        
        
        if ($stock_id > 0)
        {
            $stock = $stocks->GetById($stock_id);
            if (!empty($stock))
            {
                $stock                  = $stock['stock'];
                $stock_locations        = $stocks->GetItemLocations($stock_id);
                //$stock_deliverytimes    = $stocks->GetItemDeliveryTimes($stock_id);   20120808, zharkov: закоментировал потому что со страницы убрал фильтр по deliverytime
                $stock_deliverytimes    = array();
                $stock_steelgrades      = $stocks->GetItemSteelGrades($stock_id);
                
                //$orders = $stocks->GetItemOrders($stock_id);
                $modelOrder     = new Order();
                $orders         = $modelOrder->GetListForStock($stock_id);

                // locations
                $location_ids   = '';
                $locations      = array();
                foreach ($stock_locations as $key => $row)
                {
                    foreach ($selected_locations as $s_key => $s_location_id)
                    {
                        $s_location_id = Request::GetInteger($s_key, $selected_locations);
                        if ($s_location_id <= 0) continue;
                        
                        if ($row['stockholder_id'] == $s_location_id) 
                        {
                            $stock_locations[$key]['selected'] = true;
                            break;
                        }
                    }
                    
                    if (in_array($row['stockholder_id'], $selected_locations)) $location_ids = $location_ids . (empty($location_ids) ? '' : ',') . $row['stockholder_id'];
                }
                                
                // delivery times
                $deliverytime_ids   = '';
                $deliverytimes      = array();
                foreach ($stock_deliverytimes as $key => $row)
                {
                    foreach ($selected_deliverytimes as $s_key => $s_deliverytime_id)
                    {
                        $s_deliverytime_id = Request::GetInteger($s_key, $selected_deliverytimes);
                        if ($s_deliverytime_id <= 0) continue;
                        
                        if ($row['deliverytime_id'] == $s_deliverytime_id) 
                        {
                            $stock_deliverytimes[$key]['selected'] = true;
                            break;
                        }
                    }
                    
                    if (in_array($row['deliverytime_id'], $selected_deliverytimes)) $deliverytime_ids = $deliverytime_ids . (empty($deliverytime_ids) ? '' : ',') . $row['deliverytime_id'];
                }

                
                // types
                $is_real = 0; $is_virtual= 0; $is_twin = 0; $is_cut = 0;
                foreach ($selected_types as $type)
                {
                    if ($type == 'r') $is_real = 1;
                    if ($type == 'v') $is_virtual = 1;
                    if ($type == 't') $is_twin = 1;
                    if ($type == 'c') $is_cut = 1;
                    $this->_assign('type_' . $type, true);
                }

                $items  = new SteelItem();
                $list   = $items->GetList($stock_id, $location_ids, $deliverytime_ids, $is_real, $is_virtual, $is_twin, $is_cut, 
                                            $steelgrade_id, $thickness, $width, $length, $weight, $notes, $plate_id, $available,
                                            $stock['dimension_unit'], $stock['weight_unit'], $order_id);
                
                $this->_assign('stock',         $stock);
                $this->_assign('locations',     $stock_locations);
                $this->_assign('deliverytimes', $stock_deliverytimes);
                $this->_assign('steelgrades',   $stock_steelgrades);
                $this->_assign('orders',        $orders);
                $this->_assign('list',          $list);
                
                $total_qtty             = 0;
                $total_weight           = 0;
                $total_value            = 0;
                $total_purchase_value   = 0;
                
                foreach ($list as $item)
                {
                    $total_qtty             += 1;
                    $total_weight           += $item['steelitem']['unitweight'];
                    $total_value            += $item['steelitem']['unitweight'] * $item['steelitem']['price'];
                    $total_purchase_value   += $item['steelitem']['unitweight'] * $item['steelitem']['purchase_price'];
                }
                
                $this->_assign('total_qtty',            $total_qtty);
                $this->_assign('total_weight',          $total_weight);
                $this->_assign('total_value',           $total_value);
                $this->_assign('total_purchase_value',  $total_purchase_value);
                                
            }
        }

        $this->_assign('product_id',    $product_id);
        $this->_assign('stock_id',      $stock_id);
        $this->_assign('steelgrade_id', $steelgrade_id);
        $this->_assign('plate_id',      $plate_id);
        $this->_assign('thickness',     $thickness);
        $this->_assign('width',         $width);
        $this->_assign('length',        $length);
        $this->_assign('weight',        $weight);
        $this->_assign('notes',         $notes);
        $this->_assign('available',     $available);
        $this->_assign('order_id',      $order_id);
        $this->_assign('include_ui',    true);
        
        if (!empty($thickness) || !empty($width) || !empty($length) || !empty($steelgrade_id) || !empty($weight) || !empty($notes) || !empty($order_id))
        {
            $this->_assign('params', true);
        }
        
        $this->js       = 'item_index';
        $this->context  = true;
        
        // 20120812, zharkov: формирует название документа
        if (!empty($target_doc))
        {
            $back_title = '';
            $save_title = 'Add Selected';
            
            if ($target_doc == 'qc')
            {
                $back_title         = 'Go to QC';
                $this->page_name    = 'Items for QC';
            }
            else if ($target_doc == 'invoice')
            {
                $back_title         = 'Go to Invoice';
                $this->page_name    = 'Items for Invoice';
                
                $modelInvoice = new Invoice();
                $this->_assign('invoice', $modelInvoice->GetById($target_doc_id));
            }
            else if ($target_doc == 'supinvoice')
            {
                $back_title         = 'Go to Invoice';
                $this->page_name    = 'Items for Supplier Invoice';
                
                $modelSupInvoice = new SupplierInvoice();
                $this->_assign('supinvoice', $modelSupInvoice->GetById($target_doc_id));
            }
            else if ($target_doc == 'inddt')
            {
                $back_title         = 'Go to DDT';
                $this->page_name    = 'Items for Incoming DDT';
            }
            else if ($target_doc == 'ra')
            {
                $back_title         = 'Go to RA';
                $this->page_name    = 'Items for RA';
                
                $modelRA = new RA();
                $this->_assign('ra', $modelRA->GetById($target_doc_id));                
            }
            else if ($target_doc == 'oc')
            {
                $back_title         = 'Go to OC';
                $this->page_name    = 'Items for Original Certificate';
            }
            
            $this->_assign('back_title', $back_title);
            $this->_assign('save_title', $save_title);
            
            $this->_assign('target_doc', $target_doc);
            $this->_assign('target_doc_id', $target_doc_id);            
        }
        
        $this->_assign('page_name', $this->page_name);
        $this->_display('index');
    }
}