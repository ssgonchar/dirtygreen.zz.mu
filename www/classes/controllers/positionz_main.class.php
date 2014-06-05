<?php
require_once APP_PATH . 'classes/models/biz.class.php';
require_once APP_PATH . 'classes/models/location.class.php';
require_once APP_PATH . 'classes/models/order.class.php';
require_once APP_PATH . 'classes/models/steelgrade.class.php';
require_once APP_PATH . 'classes/models/steelposition.class.php';
require_once APP_PATH . 'classes/models/stock.class.php';


class MainController extends ApplicationController
{
	function info()
	{
		phpinfo();
	}
    function MainController()
    {
        ApplicationController::ApplicationController();
        
        $this->authorize_before_exec['index']       = ROLE_STAFF;
        $this->authorize_before_exec['add']         = ROLE_STAFF;
        $this->authorize_before_exec['edit']        = ROLE_STAFF;
        $this->authorize_before_exec['groupedit']   = ROLE_STAFF;
        $this->authorize_before_exec['history']     = ROLE_STAFF;
        $this->authorize_before_exec['reservation'] = ROLE_STAFF;        
        $this->authorize_before_exec['reserved']    = ROLE_STAFF;    

		 $this->authorize_before_exec['revision']       = ROLE_STAFF;
        
        $this->breadcrumb   = array('Stocks' => '/stocks', 'Positionzs' => '/positionzs');
    }

      /**
     * Отображает страницу со списоком позиций
     * url: /positions
     * url: /positions/filter/{filter}
     * 
     * @version 20120822, zharkov: target
     */
    function index()
    {
		
        // 20120822, zharkov: привязка к документу
        $target_doc     = Request::GetString('target_doc', $_REQUEST);
        $target_doc_id  = Request::GetString('target_doc_id', $_REQUEST);   // string потому что для новых заказов передаётся guid        
 
		//print_r($_REQUEST);
		
        if (isset($_REQUEST['btn_setfilter']))
        {
            $form       = $_REQUEST['form'];  
			//print_r($_REQUEST['form']);
            $locations  = '';
            if (isset($form['location']))
            {
                foreach ($form['location'] as $key => $location_id)
                {
                    $location_id = Request::GetInteger($key, $form['location']);
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
            
            
            $stock_id       = Request::GetInteger('stock_id',       $form);
            $thickness      = Request::GetString('thickness',       $form);
            $thickness_min  = Request::GetString('thicknessmin',    $form);
            $thickness_max  = Request::GetString('thicknessmax',    $form);
            $width          = Request::GetString('width',           $form);
            $width_min      = Request::GetString('widthmin',        $form);
            $width_max      = Request::GetString('widthmax',        $form);
            $length         = Request::GetString('length',          $form);
            $length_min     = Request::GetString('lengthmin',       $form);
            $length_max     = Request::GetString('lengthmax',       $form);
            $weight         = Request::GetString('weight',          $form);
            $weight_min     = Request::GetString('weightmin',       $form);
            $weight_max     = Request::GetString('weightmax',       $form);
            $keyword        = Request::GetString('keyword',         $form);
//            $notes          = Request::GetString('notes',           $form);
            $rev_date       = Request::GetDateForDB('rev_date',     $form);
            $rev_time       = Request::GetString('rev_time',        $form);
            $steelgrade_id  = Request::GetInteger('steelgrade',     $form);
            $rev_id         = null;
			//die('test');
            if (!empty($rev_date))
            {
                $stocks = new Stock();
                $rev_id = $stocks->CheckRevision($rev_date, $rev_time);
				//echo $rev_id;
            }
			
             //if max < min
            if (($thickness_max < $thickness_min) && ($thickness_max > 0 && $thickness_min > 0))
            {
                $thickness_min = 0;
                $thickness_max = 0;
                $this->_message('Max thisckness is less than Min thickness', MESSAGE_ERROR);
            }  
            
            if (($width_max < $width_min) && ($width_max > 0 && $width_min > 0))
            {
                $width_min = 0;
                $width_max = 0;
                $this->_message('Max width is less than Min width', MESSAGE_ERROR);
            } 
            
            if (($length_max < $length_min) && ($length_max > 0 && $length_min > 0))
            {
                $length_min = 0;
                $length_max = 0;
                $this->_message('Max length is less than Min length', MESSAGE_ERROR);
            }
            
            if (($weight_max < $weight_min) && ($weight_max > 0 && $weight_min > 0))
            {
                $weight_min = 0;
                $weight_max = 0;
                $this->_message('Max weight is less than Min wieght', MESSAGE_ERROR);
            }
            
            $filter     = (empty($stock_id) ? '' : 'stock:' . $stock_id . ';')
                        . (empty($locations) ? '' : 'location:' . $locations . ';')
                        . (empty($deliverytimes) ? '' : 'deliverytime:' . $deliverytimes . ';')
                        . (empty($thickness) ? '' : 'thickness:' . $thickness . ';')
                        . (empty($width) ? '' : 'width:' . $width . ';')
                        . (empty($width_min) ? '' : 'widthmin:' . $width_min . ';')
                        . (empty($width_max) ? '' : 'widthmax:' . $width_max . ';')
                        . (empty($length) ? '' : 'length:' . $length . ';')
                        . (empty($length_min) ? '' : 'lengthmin:' . $length_min . ';')
                        . (empty($length_max) ? '' : 'lengthmax:' . $length_max . ';')
                        . (empty($weight) ? '' : 'weight:' . $weight . ';')
                        . (empty($weight_min) ? '' : 'weightmin:' . $weight_min . ';')
                        . (empty($weight_max) ? '' : 'weightmax:' . $weight_max . ';')
                        . (empty($keyword) ? '' : 'keyword:' . $keyword . ';')
//                        . (empty($notes) ? '' : 'notes:' . $notes . ';')
                        . (empty($steelgrade_id) ? '' : 'steelgrade:' . $steelgrade_id . ';')
                        . (empty($thickness_min) ? '' : 'thicknessmin:' . $thickness_min . ';')
                        . (empty($thickness_max) ? '' : 'thicknessmax:' . $thickness_max . ';');
;
                        
            $redirect = array();
            
            // 20121107, zharkov: если передан документ, то в фильтр он тоже должен попадать
            if (!empty($target_doc) && !empty($target_doc_id))
            {
                $redirect[] = 'target';
                $redirect[] = $target_doc . ':' . $target_doc_id;
            }
            
            $redirect[] = 'positionzs';
            $redirect[] = 'filter';
            $redirect[] = preg_replace('#\s+#i', '', $filter);
            
            if (!empty($rev_id))
            {
                $redirect[] = '~rev' . $rev_id;
            }
            
            $this->_redirect($redirect, false);
			
        }
        
        $filter         = Request::GetString('filter', $_REQUEST);
        $filter_params  = array();
        
        $stocks = new Stock();
        $this->_assign('stocks', $stocks->GetList());        
        
        if (empty($filter))
        {
            $this->page_name = 'Positionzs';
        }
        else
        {
            $this->page_name = 'Filtered Positions';
            
            $this->breadcrumb['Positionzs']      = '/positionzs';
            $this->breadcrumb[$this->page_name] = $this->pager_path;
            
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
            if ($target_doc == 'order' || $target_doc == 'neworder') $this->page_name = 'Positions for Order';
            if ($target_doc == 'ra') $this->page_name = 'Positions for Release Advice';
        }


        $product_id             = Request::GetInteger('product', $filter_params);
        $stock_id               = Request::GetInteger('stock', $filter_params);
        $selected_locations     = isset($filter_params['location']) ? explode(',', $filter_params['location']) : array();
        $selected_deliverytimes = isset($filter_params['deliverytime']) ? explode(',', $filter_params['deliverytime']) : array();
//        $deliverytime_id        = Request::GetInteger('deliverytime', $filter_params);
        $steelgrade_id          = Request::GetInteger('steelgrade', $filter_params);
        $thickness              = Request::GetString('thickness', $filter_params);
        $thickness_min          = Request::GetString('thicknessmin', $filter_params);
        $thickness_max          = Request::GetString('thicknessmax', $filter_params);
        $width                  = Request::GetString('width', $filter_params);
        $width_min              = Request::GetString('widthmin', $filter_params);
        $width_max              = Request::GetString('widthmax', $filter_params);
        $length                 = Request::GetString('length', $filter_params);
        $length_min             = Request::GetString('lengthmin', $filter_params);
        $length_max             = Request::GetString('lengthmax', $filter_params);
        $weight                 = Request::GetString('weight', $filter_params);
        $weight_min             = Request::GetString('weightmin', $filter_params);
        $weight_max             = Request::GetString('weightmax', $filter_params);
        $keyword                = Request::GetString('keyword', $filter_params);
//        $notes                  = Request::GetString('notes', $filter_params);
        //$revision               = Request::GetString('stock_revision', $_REQUEST, '', 12);
        $revision               = Request::GetString('stock_revision', $filter_params, '', 12);
        
        if (!empty($revision))
        {
            $this->_assign('rev_date',  substr($revision, 6, 2) . '/' . substr($revision, 4, 2) . '/' . substr($revision, 0, 4));
            $this->_assign('rev_time',  substr($revision, 8, 2) . ':' . substr($revision, 10, 2));
        }

		//
        //$this->_assign('is_revision', (empty($revision) ? 0 : 1));
        $this->_assign('is_revision', (empty($revision) ? 0 : 1));
       
        if ($stock_id > 0)
        {
            $stock = $stocks->GetById($stock_id);            
            if (!empty($stock))
            {
                $stock = $stock['stock'];
                
                // locations
                $locations      = $stocks->GetPositionLocations($stock_id);
                $location_ids   = '';
                foreach ($locations as $key => $row)
                {
                    foreach ($selected_locations as $s_key => $s_location_id)
                    {
                        $s_location_id = Request::GetInteger($s_key, $selected_locations);
                        if ($s_location_id <= 0) continue;
                        
                        if ($row['location_id'] == $s_location_id) 
                        {
                            $locations[$key]['selected'] = true;
                            break;
                        }
                    }
                    
                    if (in_array($row['location_id'], $selected_locations)) $location_ids = $location_ids . (empty($location_ids) ? '' : ',') . $row['location_id'];
                }                

                // delivery times
                $deliverytimes      = $stocks->GetPositionDeliveryTimes($stock_id);
                $deliverytime_ids   = '';
                foreach ($deliverytimes as $key => $row)
                {
                    foreach ($selected_deliverytimes as $s_key => $s_deliverytime_id)
                    {
                        $s_deliverytime_id = Request::GetInteger($s_key, $selected_deliverytimes);
                        if ($s_deliverytime_id <= 0) continue;
                        
                        if ($row['deliverytime_id'] == $s_deliverytime_id) 
                        {
                            $deliverytimes[$key]['selected'] = true;
                            break;
                        }
                    }
                    
                    if (in_array($row['deliverytime_id'], $selected_deliverytimes)) $deliverytime_ids = $deliverytime_ids . (empty($deliverytime_ids) ? '' : ',') . $row['deliverytime_id'];                    
                }                


                $positions  = new SteelPosition();
                $list       = $positions->GetList(  $product_id, $stock_id, $location_ids, $deliverytime_ids, $steelgrade_id, 
                                                    $thickness, $thickness_min, $thickness_max, $width, $width_min, $width_max, $length, $length_min, $length_max, $weight, $weight_min,
                                                    $weight_max, $keyword, $stock['dimension_unit'], $stock['weight_unit']);

                
                $total_qtty     = 0;
                $total_weight   = 0;
                $total_value    = 0;
                
                foreach ($list as $position)
                {
                    $total_qtty     += $position['steelposition']['qtty'];
                    $total_weight   += $position['steelposition']['weight'];
                    $total_value    += $position['steelposition']['value'];
                }
                
                $this->_assign('total_qtty',    $total_qtty);
                $this->_assign('total_weight',  $total_weight);
                $this->_assign('total_value',   $total_value);
                
                $this->_assign('stock', $stock);
                $this->_assign('locations', $locations);                
                $this->_assign('deliverytimes', $deliverytimes);
                $this->_assign('list', $list);
                
//                $this->_assign('steelgrades', $stocks->GetSteelgrades($stock_id));
                

            }
        }            

        $this->_assign('product_id',    $product_id);
        $this->_assign('stock_id',      $stock_id);
        $this->_assign('thickness',     $thickness);
        $this->_assign('thicknessmin',  $thickness_min);
        $this->_assign('thicknessmax',  $thickness_max);
        $this->_assign('width',         $width);
        $this->_assign('widthmin',      $width_min);
        $this->_assign('widthmax',      $width_max);
        $this->_assign('length',        $length);
        $this->_assign('lengthmin',     $length_min);
        $this->_assign('lengthmax',     $length_max);
        $this->_assign('weight',        $weight);
        $this->_assign('weightmin',     $weight_min);
        $this->_assign('weightmax',     $weight_max);
        $this->_assign('keyword',       $keyword);
//        $this->_assign('notes',         $notes);
        $this->_assign('steelgrade_id', $steelgrade_id);   
        
        $this->_assign('include_ui',            true);
        $this->_assign('include_prettyphoto',   true);       
       
        $this->context  = true;
        $this->js       = 'position_index';
        
        //get steelgrades for postback
        if ($stock_id > 0)
        {
            $stocks = new Stock();
            $this->_assign('steelgrade_list', $stocks->GetSteelgrades($stock_id));
        }    
        
        // 20120812, zharkov: формирует название документа
        if (!empty($target_doc))
        {
            $target_doc_no = '';
            
            if ($target_doc == 'order' || $target_doc == 'neworder')
            {
                $target_doc_no = 'Order';
            }
            if ($target_doc == 'ra')
            {
                $target_doc_no = 'RA';
            }
            
            if ($target_doc == 'stockoffer')
            {
                $target_doc_no = 'Stock Offer';
            }
            
            $this->_assign('target_doc',    $target_doc);
            $this->_assign('target_doc_id', $target_doc_id);
            $this->_assign('target_doc_no', $target_doc_no);
        }        
        
        $this->_display('index');
    }

	
    /**
     * Отображает страницу добавления items
     * url: /position/add
     * url: /position/add/{stock_id}
     */
    function add()
    {
        $stock_id       = Request::GetInteger('id', $_REQUEST);
        
        $biz_id         = 0;
        $supplier_id    = 0;
        
        if (isset($_REQUEST['btn_save']))
        {            
            $form           = $_REQUEST['form'];
            $positions      = isset($_REQUEST['positions']) ? $_REQUEST['positions'] : array();
                        
            $stock_id       = Request::GetInteger('stock_id', $form);
            $location_id    = Request::GetInteger('location_id', $form);
            $product_id     = Request::GetInteger('product_id', $form);
            $biz_id         = Request::GetInteger('biz_id', $form);
            
            $okay_flag      = true;

            if (empty($stock_id)) 
            {
                $okay_flag = false;
                $this->_message('I forgot to specify stock', MESSAGE_ERROR);
            }
            else if (empty($location_id))
            {
                $okay_flag = false;
                $this->_message('I forgot to specify location', MESSAGE_ERROR);
            }
                                    
            if ($okay_flag)
            {
                $position_qtty = 0;
                foreach ($positions as $key => $row)
                {
                    $steelgrade_id  = Request::GetInteger('steelgrade_id', $row);                    
                    if (empty($steelgrade_id)) continue;

                    $position_qtty++;

                    $qtty           = Request::GetInteger('qtty', $row);
                    $thickness      = Request::GetNumeric('thickness', $row);
                    $width          = Request::GetNumeric('width', $row);
                    $length         = Request::GetNumeric('length', $row);
                    $unitweight     = Request::GetNumeric('unitweight', $row);
                    $weight         = Request::GetNumeric('weight', $row);
                    $price          = Request::GetNumeric('price', $row);
                    $value          = Request::GetNumeric('value', $row);
                    $delivery_time  = Request::GetString('delivery_time', $row);
                    $notes          = Request::GetString('notes', $row);
                    $internal_notes = Request::GetString('internal_notes', $row);
                    
                    $err = array();

                    if ($thickness <= 0) $err[] = 'incorrect thickness';
                    if ($width <= 0) $err[] = 'incorrect width';
                    if ($length <= 0) $err[] = 'incorrect length';
                    if ($unitweight <= 0) $err[] = 'incorrect unitweight';
                    if ($qtty <= 0 || $qtty > 50) $err[] = 'incorrect qtty';
                    if ($weight <= 0) $err[] = 'incorrect weight';
                    if ($price <= 0) $err[] = 'incorrect price';
                    if ($value <= 0) $err[] = 'incorrect value';
                    
                    if (!empty($err))
                    {
                        $okay_flag = false;
                        $this->_message('Position ' . ($key + 1) . ' : ' . implode(', ', $err) . ' !', MESSAGE_ERROR);
                    }
                }
            }
            
            if ($okay_flag && empty($position_qtty))
            {
                $okay_flag = false;
                $this->_message('I forgot to specify positions !', MESSAGE_ERROR);
            }
            
            if ($okay_flag)
            {
                $modelSteelPosition = new SteelPosition();
                
                $modelStock         = new Stock();
                $stock              = $modelStock->GetById($stock_id);
                $stock              = $stock['stock'];

                foreach ($positions as $key => $row)
                {
                    $steelgrade_id  = Request::GetInteger('steelgrade_id', $row);                    
                    $thickness      = Request::GetNumeric('thickness', $row);
                    $width          = Request::GetNumeric('width', $row);
                    $length         = Request::GetNumeric('length', $row);
                    $unitweight     = Request::GetNumeric('unitweight', $row);
                    $qtty           = Request::GetInteger('qtty', $row);
                    $weight         = Request::GetNumeric('weight', $row);
                    $price          = Request::GetNumeric('price', $row);
                    $value          = Request::GetNumeric('value', $row);
                    $delivery_time  = Request::GetString('delivery_time', $row);
                    $notes          = Request::GetString('notes', $row);
                    $internal_notes = Request::GetString('internal_notes', $row);
                    $producer_id    = 0;
                    
                    $modelSteelPosition->Add(0, $stock_id, $product_id, $biz_id, $location_id, $producer_id, 
                                                        $stock['dimension_unit'], $stock['weight_unit'], $stock['price_unit'], $stock['currency'], $steelgrade_id,
                                                        $thickness, $width, $length, $unitweight, $qtty, $weight, $price, $value, 
                                                        $delivery_time, $notes, $internal_notes);                    
                }
                
                $this->_message('Positions were created successfully !', MESSAGE_OKAY);
                $this->_redirect(array('positions', 'filter', 'stock:' . $stock_id . ';location:' . $location_id), false);                
            }            
        }
        else
        {
            $positions = array();
            for ($i = 0; $i < 10; $i++)
            {
                $positions[] = array(
                    'steelgrade_id'     => 0,
                    'thickness'         => 0,
                    'width'             => 0,
                    'length'            => 0,
                    'unitweight'        => 0,
                    'qtty'              => 0,
                    'weight'            => 0,
                    'price'             => 0,
                    'value'             => 0,
                    'delivery_time'     => '',
                    'notes'             => '',
                    'internal_notes'    => ''
                );
            }
            
            $form = array();
        }
        
        $total_qtty     = 0;
        $total_weight   = 0;
        $total_value    = 0;

        foreach($positions as $row)
        {
            $total_qtty += $row['qtty'];
            $total_qtty += $row['weight'];
            $total_qtty += $row['value'];
        }
                
        $this->page_name = 'New Positions';
        $this->breadcrumb[$this->page_name] = '/postion/add';
        
        $stocks = new Stock();
        $this->_assign('stocks', $stocks->GetList());
        
        if ($stock_id > 0)
        {
            $stock = $stocks->GetById($stock_id);
            if (isset($stock) && isset($stock['stock']))
            {
                $this->_assign('stock', $stock['stock']);                
                
                $location   = new Location();
                $locations  = $stocks->GetLocations($stock_id, false);

                $this->_assign('locations', $locations);                 
            }
        }
        
        $steelgrades = new SteelGrade();
        $this->_assign('steelgrades', $steelgrades->GetList());

        $this->_assign('include_ui',    true);
                
        $this->_assign('biz_id',        $biz_id);
        $this->_assign('positions',     $positions);
        $this->_assign('form',          $form);        
        
        $this->_assign('total_qtty',    $total_qtty);
        $this->_assign('total_weight',  $total_weight);
        $this->_assign('total_value',   $total_value);
        
        $this->context  = true;
        $this->js       = 'position_add';

        $this->_display('add');
    }    
    
    /**
     * Отображает страницу редактирования позиции
     * url: /position/{position_id}/edit
     */
    function edit()
    {
        $ref            = Request::GetString('ref', $_REQUEST);
        $order_id       = Request::GetInteger('order_id', $_REQUEST);
        $position_id    = Request::GetInteger('id', $_REQUEST);        
        if (empty($position_id)) _404();
        
        // проверяет существование позиции
        $positions  = new SteelPosition();
        $position   = $positions->GetById($position_id);
        if (empty($position)) _404();
        
        
        if ($ref == 'orderselectitems' && $order_id > 0)
        {
            $orders = new Order();
            $order  = $orders->GetById($order_id);
            
            if (empty($order)) _404();
            
            $back_url = $this->_get_previous_url_by_part('order/selectitems');
            $back_url = empty($back_url) ? 'order/view/' . $order_id : $back_url;
            
            $this->breadcrumb = array(
                'Orders'                => '/orders',
                'Order # ' . $order_id  => '/' . $back_url
            );
            
            $this->_assign('order_id', $order_id);
        }
        else
        {
            $back_url = $this->_get_previous_url_by_part('positions/filter');
            $back_url = empty($back_url) ? 'positions' : $back_url;
            
            $this->breadcrumb = array(
                'Positions'             => '/items',
                'Filtered Positions'    => '/' . $back_url
            );            
        }

                
        // закрывает позицию для редактирования другими пользователями
        if (!SteelPosition::IsLocked($position_id))
        {
            SteelPosition::Lock($position_id);
            $position = $positions->GetById($position_id);
        }
        
        if (isset($_REQUEST['btn_cancel']))
        {
            // освобождает позицию
            SteelPosition::Unlock($position_id);
            
            // освобождает айтемы
            $items = $positions->GetItems($position_id);
            foreach ($items as $item) SteelItem::Unlock($item['steelitem']['id']);
            
            // возврат обратно к списку
            $this->_redirect(explode('/', $back_url), false);
        }
        else if (isset($_REQUEST['btn_save']))
        {
            if (!isset($_REQUEST['position'])) _404();
            if (!isset($_REQUEST['item'])) _404();
            if (!isset($_REQUEST['item_property'])) _404();
            
            // проверка plate id для каждого айтема
            $items      = new SteelItem();
            $no_errors  = true;
            foreach($_REQUEST['item'] as $item)
            {
                $guid               = Request::GetString('guid', $item);                
                $item_id            = Request::GetInteger('id', $item);
                
                if (!empty($guid) && $items->CheckGuid($item_id, $guid))
                {
                    $this->_message("Plate ID '" . $guid . "' has already allocated for another item !", MESSAGE_ERROR);
                    $no_errors = false;
                }                
            }
            
            // сохранение 
            if ($no_errors)
            {                            
                
                $stock_id       = $position['steelposition']['stock_id'];
                $product_id     = $position['steelposition']['product_id'];
                $dimension_unit = $position['steelposition']['dimension_unit'];
                $weight_unit    = $position['steelposition']['weight_unit'];
                $price_unit     = $position['steelposition']['price_unit'];
                $currency       = $position['steelposition']['currency'];
                $biz_id         = Request::GetInteger('biz_id', $_REQUEST['position']);
                $price          = Request::GetNumeric('price', $_REQUEST['position']);
                
                
                $no_errors = true;
                foreach($_REQUEST['item'] as $key => $item)
                {
                    $item_id                = Request::GetInteger('id', $item);
                    $guid                   = Request::GetString('guid', $item);
                    $is_deleted             = Request::GetInteger('is_deleted', $item);
                    $steelgrade_id          = Request::GetInteger('steelgrade_id', $item);
                    $thickness              = Request::GetNumeric('thickness', $item);
                    $width                  = Request::GetNumeric('width', $item);
                    $length                 = Request::GetNumeric('length', $item);
                    $unitweight             = Request::GetNumeric('unitweight', $item);
                    $thickness_measured     = Request::GetNumeric('thickness_measured', $item);
                    $width_measured         = Request::GetNumeric('width_measured', $item);
                    $length_measured        = Request::GetNumeric('length_measured', $item);
                    $unitweight_measured    = Request::GetNumeric('unitweight_measured', $item);
                    $width_max              = Request::GetNumeric('width_max', $item);
                    $length_max             = Request::GetNumeric('length_max', $item);
                    $unitweight_weighed     = Request::GetNumeric('unitweight_weighed', $item);
                    $is_virtual             = Request::GetInteger('is_virtual', $item);
                    $location_id            = Request::GetInteger('location_id', $item);
                    $supplier_id            = Request::GetInteger('supplier_id', $item);
                    $mill                   = Request::GetString('mill', $item);
                    $system                 = Request::GetString('system', $item);
                    $supplier_invoice_no    = Request::GetString('supplier_invoice_no', $item);
                    $supplier_invoice_date  = Request::GetDateForDB('supplier_invoice_date', $item);
                    $purchase_price         = Request::GetNumeric('purchase_price', $item);
                    $purchase_currency      = Request::GetString('purchase_currency', $item);
                    $current_cost           = Request::GetNumeric('current_cost', $item);
                    $pl                     = Request::GetNumeric('pl', $item);
                    $in_ddt_number          = Request::GetString('in_ddt_number', $item);
                    $in_ddt_date            = Request::GetDateForDB('in_ddt_date', $item);
                    $in_ddt_company         = Request::GetString('in_ddt_company', $item);
                    $in_ddt_company_id      = Request::GetInteger('in_ddt_company_id', $item);                    
                    $ddt_number             = Request::GetString('ddt_number', $item);
                    $ddt_date               = Request::GetDateForDB('ddt_date', $item);
                    $ddt_company            = Request::GetString('ddt_company', $item);
                    $ddt_company_id         = Request::GetInteger('ddt_company_id', $item);                    
                    $status_id              = Request::GetInteger('status_id', $item);
                    $delivery_time          = Request::GetString('delivery_time', $item);
                    $load_ready             = Request::GetString('load_ready', $item);
                    $owner_id               = Request::GetInteger('owner_id', $item);
                    $notes                  = Request::GetString('notes', $item);
                    $internal_notes         = Request::GetString('internal_notes', $item);
                    
                    $nominal_thickness_mm   = Request::GetNumeric('nominal_thickness_mm', $item);
                    $nominal_width_mm       = Request::GetNumeric('nominal_width_mm', $item);
                    $nominal_length_mm      = Request::GetNumeric('nominal_length_mm', $item);

                    $is_ce_mark                 = Request::GetInteger('is_ce_mark', $item);
                    $is_mec_prop_not_required   = Request::GetInteger('is_mec_prop_not_required', $item);
                    
                    
                    // удаляет айтем
                    if ($is_deleted > 0) 
                    {
                        $items->Remove($item_id);
                        continue;
                    }
                    
                    $in_ddt_company_id      = empty($in_ddt_company_id) || empty($in_ddt_company) ? 0 : $in_ddt_company_id;
                    $ddt_company_id         = empty($ddt_company_id) || empty($ddt_company) ? 0 : $ddt_company_id;                    

                    // сохраняет айтем                        
                    $result = $items->Save($item_id, $position_id, $guid, $product_id, $biz_id, $location_id, $dimension_unit, 
                                            $weight_unit, $price_unit, $currency, $steelgrade_id, $thickness, $thickness_measured, 
                                            $width, $width_measured, $width_max, $length, $length_measured, $length_max, $unitweight, 
                                            $price, $unitweight * $price, $delivery_time, $notes, $internal_notes, $supplier_id, 
                                            $supplier_invoice_no, $supplier_invoice_date, $purchase_price, 0, $in_ddt_number, 
                                            $in_ddt_date, $ddt_number, $ddt_date, $owner_id, $status_id, $is_virtual, $mill, $system, 
                                            $unitweight_measured, $unitweight_weighed, $current_cost, $pl, $load_ready, $purchase_currency,
                                            $in_ddt_company_id, $ddt_company_id, 
                                            $nominal_thickness_mm, $nominal_width_mm, $nominal_length_mm, 
                                            $is_ce_mark, $is_mec_prop_not_required);
                    
                    if (empty($result) || isset($result['ErrorCode']))
                    {
                        if (isset($result['ErrorCode']))
                        {
                            if ($result['ErrorCode'] == -1)
                            {
                                $this->_message("Plate ID '" . $guid . "' has already allocated for another item !", MESSAGE_ERROR);    
                            }
                            else
                            {
                                $this->_message("Item with ID = '" . $item_id . "' is not exists !", MESSAGE_ERROR);    
                            }
                        }
                        else
                        {
                            $this->_message('We got an error when saving item !', MESSAGE_ERROR);    
                        }

                        $no_errors = false;
                        break;
                    }
                    else
                    {
                        $item_property = $_REQUEST['item_property'][$key];
                        
                        $heat_lot                   = Request::GetString('heat_lot', $item_property);
                        $c                          = Request::GetNumeric('c', $item_property);
                        $si                         = Request::GetNumeric('si', $item_property);
                        $mn                         = Request::GetNumeric('mn', $item_property);
                        $p                          = Request::GetNumeric('p', $item_property);
                        $s                          = Request::GetNumeric('s', $item_property);
                        $cr                         = Request::GetNumeric('cr', $item_property);
                        $ni                         = Request::GetNumeric('ni', $item_property);
                        $cu                         = Request::GetNumeric('cu', $item_property);
                        $al                         = Request::GetNumeric('al', $item_property);
                        $mo                         = Request::GetNumeric('mo', $item_property);
                        $nb                         = Request::GetNumeric('nb', $item_property);
                        $v                          = Request::GetNumeric('v', $item_property);
                        $n                          = Request::GetNumeric('n', $item_property);
                        $ti                         = Request::GetNumeric('ti', $item_property);
                        $sn                         = Request::GetNumeric('sn', $item_property);
                        $b                          = Request::GetNumeric('b', $item_property);
                        $ceq                        = Request::GetNumeric('ceq', $item_property);
                        $tensile_sample_direction   = Request::GetString('tensile_sample_direction', $item_property);
                        $tensile_strength           = Request::GetInteger('tensile_strength', $item_property);
                        $yeild_point                = Request::GetInteger('yeild_point', $item_property);
                        $elongation                 = Request::GetNumeric('elongation', $item_property);
                        $reduction_of_area          = Request::GetNumeric('reduction_of_area', $item_property);
                        $test_temp                  = Request::GetInteger('test_temp', $item_property);
                        $impact_strength            = Request::GetString('impact_strength', $item_property);
                        $hardness                   = Request::GetInteger('hardness', $item_property);
                        $ust                        = Request::GetString('ust', $item_property);
                        $sample_direction           = Request::GetString('sample_direction', $item_property);
                        $stress_relieving_temp      = Request::GetInteger('stress_relieving_temp', $item_property);
                        $heating_rate_per_hour      = Request::GetInteger('heating_rate_per_hour', $item_property);
                        $holding_time               = Request::GetInteger('holding_time', $item_property);
                        $cooling_down_rate          = Request::GetInteger('cooling_down_rate', $item_property);
                        $normalizing_temp           = Request::GetInteger('normalizing_temp', $item_property);
                        $condition                  = Request::GetString('condition', $item_property);
                        
                        // сохраняет свойства позиции
                        $result =  $items->SaveProperties($result['id'], $heat_lot, $c, $si, $mn, $p, $s, $cr, $ni, $cu, $al, 
                                        $mo, $nb, $v, $n, $ti, $sn, $b, $ceq, $tensile_sample_direction, $tensile_strength, $yeild_point, 
                                        $elongation, $reduction_of_area, $test_temp, $impact_strength, $hardness, 
                                        $ust, $sample_direction, $stress_relieving_temp, $heating_rate_per_hour, 
                                        $holding_time, $cooling_down_rate, $condition, $normalizing_temp);

                        if (empty($result))
                        {
                            $this->_message('Error was occured when saving item properties !', MESSAGE_ERROR);
                            $no_errors = false;
                            break;                            
                        }
                    }                    
                }
                

                if ($no_errors)
                {                        
                    // если позиция не используется
                    if (!$position['steelposition']['inuse'])
                    {
                        $pos = $_REQUEST['position'];
                        
                        $steelgrade_id  = Request::GetInteger('steelgrade_id', $pos);
                        $thickness      = Request::GetNumeric('thickness', $pos);
                        $width          = Request::GetNumeric('width', $pos);
                        $length         = Request::GetNumeric('length', $pos);
                        $unitweight     = Request::GetNumeric('unitweight', $pos);
                        $qtty           = Request::GetInteger('qtty', $pos);
                        $weight         = Request::GetNumeric('weight', $pos);
                        $price          = Request::GetNumeric('price', $pos);
                        $value          = Request::GetNumeric('value', $pos);
                        $delivery_time  = Request::GetHtmlString('delivery_time', $pos);
                        $notes          = Request::GetHtmlString('notes', $pos);
                        $internal_notes = Request::GetHtmlString('internal_notes', $pos);
                        $biz_id         = Request::GetInteger('biz_id', $pos);
                                            
                        $result = $positions->Save($position_id, $stock_id, $product_id, 
                                    $biz_id, 0, 0, $dimension_unit, $weight_unit, $price_unit, $currency, 
                                    $steelgrade_id, $thickness, $width, $length, $unitweight, $qtty, $weight, 
                                    $price, $value, $delivery_time, $notes, $internal_notes);

                        // открывает позицию для редактирования
                        SteelPosition::Unlock($position_id);
                        
                        $this->_message('Position & items were saved successfully !', MESSAGE_OKAY);                            
                    }
                    else
                    {
                        $this->_message('Items were saved successfully !', MESSAGE_OKAY);                            
                    }
                                        
                    // освобождает айтемы
                    $items = $positions->GetItems($position_id);
                    foreach ($items as $item) SteelItem::Unlock($item['steelitem']['id']);
                    
                    $this->_redirect(explode('/', $back_url), false);
                }
            }
        }
        
        
        $this->page_name = 'Edit Position';
        $this->breadcrumb[$this->page_name] = '';

        if (isset($_REQUEST['position']))
        {
            foreach ($position['steelposition'] as $key => $value)
            {
                if (isset($_REQUEST['position'][$key]))
                {
                    $position['steelposition'][$key] = Request::GetString($key, $_REQUEST['position']);
                }
            }
        }

        $this->_assign('position',          $position);
        $this->_assign('include_nominal',   ($position['steelposition']['dimension_unit'] == 'in' ? true : false));
        
        
        $position = $position['steelposition'];
        
        $steelgrades = new SteelGrade();
        $this->_assign('steelgrades', $steelgrades->GetList());

        $bizes = new Biz();
        if (!empty($position['biz_id']))
        {
            $biz = $bizes->GetById($position['biz_id']);
            if (!empty($biz))
            {
                $this->_assign('position_biz', $biz['biz']);
            }            
        }

        // список айтемов позиции
        $items = $positions->GetItems($position_id, false);

        if (isset($_REQUEST['item']))
        {
            foreach ($items as $i => $item)
            {
                $item = $item['steelitem'];
                
                foreach ($_REQUEST['item'] as $j => $pb_item)
                {
                    if ($item['id'] == $pb_item['id'])
                    {
                        foreach ($pb_item as $pb_item_key => $pb_item_value)
                        {
                            if ($pb_item_key == 'supplier_invoice_date' || $pb_item_key == 'ddt_date' || $pb_item_key == 'in_ddt_date')
                            {
                                $pb_item_value = Request::GetDateForDB($pb_item_key, $pb_item);
                            }
                            else
                            {
                                $pb_item_value = Request::GetString($pb_item_key, $pb_item);
                            }
                            
                            $items[$i]['steelitem'][$pb_item_key] = $pb_item_value;
                        }
                        
                        foreach ($_REQUEST['item_property'][$j] as $pb_item_property_key => $pb_item_property_value)
                        {
                            $items[$i]['steelitem']['properties'][$pb_item_property_key] = Request::GetString($pb_item_property_key, $_REQUEST['item_property'][$j]);
                        }
                    }
                }
            }
            
            foreach ($_REQUEST['item'] as $i => $pb_item)
            {
                if ($pb_item['id'] != 0) continue;
                
                $pb_item['properties'] = $_REQUEST['item_property'][$i];
                $items[] = array(
                    'steelitem_id'  => 0,
                    'steelitem'     => $pb_item
                );
            }
        }
        else
        {
            // защищает айтемы от редактирования
            foreach ($items as $key => $item) 
            {
                SteelItem::Lock($item['steelitem']['id']);
            }
        }

        
        // locations
        $stocks     = new Stock();
        $locations  = $stocks->GetLocations($position['stock_id'], false);
        
        foreach ($items as $key => $item) 
        {
            $items[$key]['locations'] = $locations;        
        }

        $this->_assign('items',         $items);
        $this->_assign('items_count',   count($items));
        $this->_assign('include_ui',    true);
        $this->_assign('back_url',      $back_url);

        $eternal_qtty = 0;
        foreach ($items as $row)
        {
            if ((isset($row['steelitem']['order_id']) && $row['steelitem']['order_id'] > 0) 
				|| (isset($row['steelitem']['is_eternal']) && $row['steelitem']['is_eternal'])) 
			{	
				$eternal_qtty++;
			}        
        }
        
        if (count($items) == $eternal_qtty) $this->_assign('items_eternal', true);

        $this->context  = true;        
        $this->js       = 'position_edit';
        
        $this->_display('edit');
    }
    
    /**
     * Отображает форму группового редактирования позиций
     * /position/groupedit/{ids}
     */
    function groupedit()
    {
        $ids = Request::GetString('id', $_REQUEST);
        $ids = explode(',', $ids);
        
        if (empty($ids)) _404();

        $positions  = new SteelPosition();
        $rowset     = array();
        $stock_id   = 0;
        foreach ($ids as $id) 
        {
            $position = $positions->GetById($id);
            
            if (empty($position)) continue;            
            if ($stock_id > 0 && $position['steelposition']['stock_id'] != $stock_id) continue;
            
            $stock_id = $position['steelposition']['stock_id'];
            $rowset[] = array('steelposition_id' => $id);
        }
                
        $list = $positions->GetByIds($rowset);        
        if (empty($list)) _404();
        
        $stocks = new Stock();
        $stock  = $stocks->GetById($stock_id);
        $stock  = $stock['stock'];
        
        
        if (isset($_REQUEST['btn_cancel']))
        {
            // освобождает позиции
            foreach ($list as $row) SteelPosition::Unlock($row['steelposition_id']);
            
            // возврат обратно к списку
            $this->_redirect(array('positions', 'filter', 'stock:' . $stock['id']), false);
        }
        else if (isset($_REQUEST['btn_save']))
        {
            $form = $_REQUEST['form'];
            
            $group_price            = Request::GetNumeric('price', $form);
            $group_notes            = Request::GetString('notes', $form);
            $clear_notes            = Request::GetString('clear_notes', $form);
            $group_internal_notes   = Request::GetString('internal_notes', $form);
            $clear_internal_notes   = Request::GetString('clear_internal_notes', $form);
            $group_delivery_time    = Request::GetString('delivery_time', $form);
            $clear_delivery_time    = Request::GetString('clear_delivery_time', $form);
            $group_biz_id           = Request::GetInteger('biz_id', $form);
            $group_supplier_id      = Request::GetInteger('supplier_id', $form);
            
            $has_errors = false;
            foreach ($_REQUEST['position_id'] as $key => $position_id)
            {
                $position_id = Request::GetInteger($key, $_REQUEST['position_id']);
                if (empty($position_id)) continue;
                
                $steelgrade_id  = Request::GetInteger($key, $_REQUEST['steelgrade_id']);
                $thickness      = Request::GetNumeric($key, $_REQUEST['thickness']);
                $width          = Request::GetNumeric($key, $_REQUEST['width']);
                $length         = Request::GetNumeric($key, $_REQUEST['length']);
                $unitweight     = Request::GetNumeric($key, $_REQUEST['unitweight']);
                $qtty           = Request::GetInteger($key, $_REQUEST['qtty']);
                $weight         = Request::GetNumeric($key, $_REQUEST['weight']);                
                $price          = Request::GetNumeric($key, $_REQUEST['price']);
                $value          = Request::GetNumeric($key, $_REQUEST['value']);
                $delivery_time  = Request::GetString($key, $_REQUEST['delivery_time']);
                $notes          = Request::GetString($key, $_REQUEST['notes']);
                $internal_notes = Request::GetString($key, $_REQUEST['internal_notes']);
                $biz_id         = 0;    //Request::GetInteger($key, $_REQUEST['biz_id']);
                $supplier_id    = 0;    //Request::GetInteger($key, $_REQUEST['supplier_id']);
                
                if (!empty($group_price))
                {
                    $price = $group_price;
                    $value = $weight * $price / 100;
                }
                
                if (!empty($clear_notes)) $notes = ''; else if (!empty($group_notes)) $notes = $group_notes;
                if (!empty($clear_internal_notes)) $internal_notes = ''; else if (!empty($group_internal_notes)) $internal_notes = $group_internal_notes;
                if (!empty($clear_delivery_time)) $delivery_time = ''; else if (!empty($group_delivery_time)) $delivery_time = $group_delivery_time;
                
                $result = $positions->Save($position_id, $stock_id, 0, $biz_id, 0, $supplier_id, $stock['dimension_unit'], $stock['weight_unit'], $stock['price_unit'], $stock['currency'], 
                            $steelgrade_id, $thickness, $width, $length, $unitweight, 0, $weight, $price, $value, 
                            $delivery_time, $notes, $internal_notes);
                                
                if (empty($result))
                {
                    $has_errors = true;
                    break;
                }
            }
            
            if ($has_errors)
            {
                $this->_message('Error when saving positions !', MESSAGE_ERROR);
            }
            else
            {
                // открывает позиции для редактирования
                foreach ($list as $row) SteelPosition::Unlock($row['steelposition_id']);
                
                $this->_message('Positions was successfully updated !', MESSAGE_OKAY);
                $this->_redirect(array('positions', 'filter', 'stock:' . $stock_id), false);
            }
        }
        
        
        $this->context      = true;
        $this->page_name    = 'Positions Group Edit';        
        $this->breadcrumb   = array(
            'Stocks' => '/stocks',
            'Positions' => '/positions',
            'Filtered Positions' => '/positions/filter/stock:' . $stock_id,
            $this->page_name    => '/' . $_REQUEST['arg']
        );
        
        
        $this->_assign('stock', $stock);
        
        $steelgrades = new SteelGrade();
        $this->_assign('steelgrades', $steelgrades->GetList());

        //$bizes = new Biz();
        //$this->_assign('bizes', $bizes->GetList('steel'));
        
        $total_qtty     = 0;
        $total_weight   = 0;
        $total_value    = 0;
        foreach ($list as $row)
        {
            $row = $row['steelposition'];
            
            $total_qtty     += $row['qtty'];
            $total_weight   += $row['weight'];
            $total_value    += $row['value'];

            // закрывает для редактирования позицию другими
            SteelPosition::Lock($row['id']);
        }
        
        $this->_assign('list', $list);
        $this->_assign('total_qtty', $total_qtty);
        $this->_assign('total_weight', $total_weight);
        $this->_assign('total_value', $total_value);
        
        $this->_display('groupedit');
    }
    
    
    /**
     * Отображает список зарезервированных позиций
     * url: /positions/reserved/filter/company:{company_id}
     */
    function reserved()
    {
        if (isset($_REQUEST['btn_remove']))
        {
            if (isset($_REQUEST['reserv']))
            {
                $positions = new SteelPosition();
                foreach ($_REQUEST['reserv'] as $id => $value) $positions->ReserveRemove($id);
                
                Cache::ClearTag('steelpositions-reserved');
                
                $this->_message('Positions was removed from reserve', MESSAGE_OKAY);
                $this->_redirect(explode('/', $this->pager_path), false);
            }
        }
        
        $filter         = Request::GetString('filter', $_REQUEST);
        $filter_params  = array();
        
        $this->page_name = 'Reserved Positions';
        
        $this->breadcrumb['Positions']      = '/positions';
        $this->breadcrumb[$this->page_name] = $this->pager_path;

        if (!empty($filter))
        {
            $filter = explode(';', $filter);
            foreach ($filter as $row)
            {
                if (empty($row)) continue;
                
                $param = explode(':', $row);
                $filter_params[$param[0]] = Request::GetHtmlString(1, $param);
            }
            
            $this->_assign('filter', true);            
        }
        

        $company_id = Request::GetInteger('company', $filter_params);

        $positions  = new SteelPosition();
        $list       = $positions->ReserveGetList($company_id);
        $companies  = $positions->ReserveCompanies();
        
        $total_qtty     = 0;
        $total_weight   = 0;
        $total_value    = 0;
        
        $dimension_units    = array();
        $weight_units       = array();
        $price_units        = array();
        $currencies         = array();
        
        foreach ($list as $row)
        {
            $steelposition  = $row['steelposition'];
            
            $total_qtty     += $row['qtty'];
            $total_weight   += $steelposition['unitweight'] * $row['qtty'];
            $total_value    += $steelposition['unitweight'] * $row['qtty'] * $steelposition['price'];
            
            $dimension_unit                     = $steelposition['dimension_unit'];
            $dimension_units[$dimension_unit]   = $dimension_unit;
            
            $weight_unit                        = $steelposition['weight_unit'];
            $weight_units[$weight_unit]         = $weight_unit;

            $price_unit                         = $steelposition['price_unit'];
            $price_units[$price_unit]           = $price_unit;
            
            $currency                           = $steelposition['currency'];
            $currencies[$currency]              = $currency;            
        }
        
        if (count($dimension_units) == 1)
        {
            $dimension_units = array_values($dimension_units);
            $this->_assign('dimension_unit', $dimension_units[0]);
        }

        if (count($weight_units) == 1)
        {
            $weight_units = array_values($weight_units);
            $this->_assign('weight_unit', $weight_units[0]);
        }

        if (count($price_units) == 1)
        {
            $price_units = array_values($price_units);
            $this->_assign('price_unit', $price_units[0]);
        }
        
        if (count($currencies) == 1)
        {
            $currencies = array_values($currencies);
            $this->_assign('currency', $currencies[0]);
        }

        $this->_assign('total_qtty',    $total_qtty);
        $this->_assign('total_weight',  $total_weight);
        $this->_assign('total_value',   $total_value);
        
        $this->_assign('list', $list);
        $this->_assign('companies', $companies); 
        $this->_assign('company_id', $company_id);         
        
        $this->context = true;        
        $this->_display('reserved');
        
    }
    
    /**
     * Отображает страницу резервирования позиций
     * url: /position/reservation
     */
    function reservation()
    {
        $filter = Request::GetString('filter', $_REQUEST);
        if (empty($filter)) _404();
        
        $positions  = array();
        $items      = array();

        foreach(explode(';', $filter) as $row)
        {
            $row = explode(':', $row);
            if ($row[0] == 'positions')
            {
                $positions  = empty($row[1]) ? array() : explode(',', $row[1]);
            }
            
            if ($row[0] == 'items')
            {
                $items  = empty($row[1]) ? array() : explode(',', $row[1]);
            }            
        }

        $steelpositions = new SteelPosition();
        $rowset         = array();
        foreach ($positions as $id) 
        {
            $position = $steelpositions->GetById($id);            
            if (empty($position)) continue;            

            $rowset[$id] = array('steelposition_id' => $id);
        }
        
        $steelitems = new SteelItem();
        foreach ($items as $id)
        {
            $item = $steelitems->GetById($id);
            if (empty($item)) continue;
            
            $item = $item['steelitem'];
            $rowset[$item['steelposition_id']]['steelposition_id'] = $item['steelposition_id'];
            $rowset[$item['steelposition_id']]['qtty'] = (isset($rowset[$item['steelposition_id']]['qtty']) ? $rowset[$item['steelposition_id']]['qtty'] : 0) + 1;
        }
        
        $positions = array();
        foreach($rowset as $row) $positions[] = $row;
        
        $list = $steelpositions->GetByIds($positions);
        if (empty($list)) _404();

        $stock_id   = 0;
        foreach ($list as $key => $row)
        {
            if ($stock_id > 0 && $position['steelposition']['stock_id'] != $stock_id)
            {
                unset($list[$key]);
                continue;
            }
        
            if (isset($row['qtty']))
            {
                $list[$key]['steelposition']['qtty']    = $row['qtty'];
                $list[$key]['steelposition']['weight']  = $row['qtty'] * $row['steelposition']['unitweight'];
                $list[$key]['steelposition']['value']   = $row['qtty'] * $row['steelposition']['unitweight'] * $row['steelposition']['price'];
            }
            
            $stock_id = $row['steelposition']['stock_id'];            
        }
        
        
        $stocks = new Stock();
        $stock  = $stocks->GetById($stock_id);
        $stock  = $stock['stock'];
        
        
        if (isset($_REQUEST['btn_cancel']))
        {            
            // возврат обратно к списку
            $this->_redirect(array('positions', 'filter', 'stock:' . $stock['id']), false);
        }
        else if (isset($_REQUEST['btn_save']))
        {
            $form = $_REQUEST['form'];
            
            $company_id = Request::GetInteger('company_id', $form);
            $person_id  = Request::GetInteger('person_id', $form);
            $period     = Request::GetInteger('period', $form);
            $period     = ($period <= 0 ? 24 : $period);
            
            if (empty($company_id))
            {
                $this->_message('Company must be specified !', MESSAGE_ERROR);
            }
            else
            {
                $reserved = 0;
                foreach ($_REQUEST['position_id'] as $key => $position_id)
                {
                    $position_id = Request::GetInteger($key, $_REQUEST['position_id']);
                    if (empty($position_id)) continue;
                    
                    $qtty = Request::GetInteger($key, $_REQUEST['qtty']);
                    if ($qtty <= 0) continue;
                    
                    $result = $steelpositions->ReserveAdd($position_id, $qtty, $company_id, $person_id, $period);
                    $reserved++;
                }
                
                if ($reserved > 0)
                {
                    $this->_message('Positions was successfully reserved !', MESSAGE_OKAY);
                    $this->_redirect(array('positions', 'reserved', 'filter', 'company:' . $company_id), false);
                }
                else
                {
                    $this->_redirect(array('positions', 'filter', 'stock:' . $stock_id), false);
                }
            }
        }
        
        
        $this->context      = true;
        $this->js           = 'position_reservation';
        
        $this->page_name    = 'Positions reservation';        
        $this->breadcrumb   = array(
            'Stocks' => '/stocks',
            'Positions' => '/positions',
            'Filtered Positions' => '/positions/filter/stock:' . $stock_id,
            $this->page_name    => '/' . $_REQUEST['arg']
        );
        
        
        $this->_assign('include_ui',    true);
        $this->_assign('stock',         $stock);
        
        $steelgrades = new SteelGrade();
        $this->_assign('steelgrades', $steelgrades->GetList());
        
        $total_qtty     = 0;
        $total_weight   = 0;
        $total_value    = 0;
        foreach ($list as $row)
        {
            $row = $row['steelposition'];
            
            $total_qtty     += $row['qtty'];
            $total_weight   += $row['weight'];
            $total_value    += $row['value'];

        }
        
        $this->_assign('list', $list);
        $this->_assign('total_qtty', $total_qtty);
        $this->_assign('total_weight', $total_weight);
        $this->_assign('total_value', $total_value);
        
        $this->_display('reserve');        
    }
    
    /**
     * Отображает страницу с историей изменения позиции
     * url: /position/history/{position_id}
     */
    function history()
    {
        $position_id = Request::GetInteger('id', $_REQUEST);
        
        $steelpositions = new SteelPosition();
        $list           = $steelpositions->GetHistory($position_id);
/*        
        $stocks = new Stock();
        $stock  = $stocks->GetById($list[0]['stock_id']);        
*/        
        $this->page_name = 'Position History';
        $this->breadcrumb = array(
            'Stocks'                => '/stocks',
            'Positions'             => '/positions',
            'Filtered Positions'    => (isset($list[0]) ? '/positions/filter/stock:' . $list[0]['stock_id'] : '/positions') ,
            $this->page_name        => ''
        );
        
        $this->_assign('list', $list);
        $this->_display('history');
    }
}
