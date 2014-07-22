<?php
require_once APP_PATH . 'classes/models/biz.class.php';
require_once APP_PATH . 'classes/models/location.class.php';
require_once APP_PATH . 'classes/models/order.class.php';
require_once APP_PATH . 'classes/models/steelgrade.class.php';
require_once APP_PATH . 'classes/models/steelposition.class.php';
require_once APP_PATH . 'classes/models/stock.class.php';


class MainPrintController extends ApplicationPrintController
{
    function MainPrintController()
    {
        ApplicationPrintController::ApplicationPrintController();
        
        $this->authorize_before_exec['index']       = ROLE_STAFF;
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
        
        if (isset($_REQUEST['btn_setfilter']))
        {
            $form       = $_REQUEST['form'];                        
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
            
            
            $stock_id   = Request::GetInteger('stock_id', $form);
            $thickness  = Request::GetString('thickness', $form);
            $width      = Request::GetString('width', $form);
            $length     = Request::GetString('length', $form);
            $weight     = Request::GetString('weight', $form);
            $notes      = Request::GetString('notes', $form);
            $rev_date   = Request::GetDateForDB('rev_date', $form);
            $rev_time   = Request::GetString('rev_time', $form);
            $rev_id     = null;
            
            if (!empty($rev_date))
            {
                $stocks = new Stock();
                $rev_id = $stocks->CheckRevision($rev_date, $rev_time);
            }
            

            $filter     = (empty($stock_id) ? '' : 'stock:' . $stock_id . ';')
                        . (empty($locations) ? '' : 'location:' . $locations . ';')
                        . (empty($deliverytimes) ? '' : 'deliverytime:' . $deliverytimes . ';')
                        . (empty($thickness) ? '' : 'thickness:' . $thickness . ';')
                        . (empty($width) ? '' : 'width:' . $width . ';')
                        . (empty($length) ? '' : 'length:' . $length . ';')
                        . (empty($weight) ? '' : 'weight:' . $weight . ';')
                        . (empty($notes) ? '' : 'notes:' . $notes . ';');
                        
            $redirect = array();
            
            // 20121107, zharkov: если передан документ, то в фильтр он тоже должен попадать
            if (!empty($target_doc) && !empty($target_doc_id))
            {
                $redirect[] = 'target';
                $redirect[] = $target_doc . ':' . $target_doc_id;
            }
            
            $redirect[] = 'positions';
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
            $this->page_name = 'Positions';
        }
        else
        {
            $this->page_name = 'Filtered Positions';
            
            $this->breadcrumb['Positions']      = '/positions';
//            $this->breadcrumb[$this->page_name] = $this->pager_path;
            
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
        $width                  = Request::GetString('width', $filter_params);
        $length                 = Request::GetString('length', $filter_params);
        $weight                 = Request::GetString('weight', $filter_params);
        $notes                  = Request::GetString('notes', $filter_params);
        $revision               = Request::GetString('stock_revision', $_REQUEST, '', 12);
        
        if (!empty($revision))
        {
            $this->_assign('rev_date',  substr($revision, 6, 2) . '/' . substr($revision, 4, 2) . '/' . substr($revision, 0, 4));
            $this->_assign('rev_time',  substr($revision, 8, 2) . ':' . substr($revision, 10, 2));
        }

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
               /* $list       = $positions->GetList($product_id, $stock_id, $location_ids, $deliverytime_ids, $steelgrade_id, 
                                                    $thickness, $width, $length, $weight, $notes, 
                                                    $stock['dimension_unit'], $stock['weight_unit']);*/
                $list       = $positions->GetList($product_id, $stock_id, $location_ids, $stockholder_ids, $deliverytime_ids, $steelgrade_ids, $thickness, $thickness_min, $thickness_max, $width, $width_min, $width_max, $length, $length_min, $length_max, $weight, $weight_min, $weight_max, $keyword, $stock['dimension_unit'], $stock['weight_unit']);
               // debug('1671', $list);
                
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
        $this->_assign('width',         $width);
        $this->_assign('length',        $length);
        $this->_assign('weight',        $weight);
        $this->_assign('notes',         $notes);        
        
        $this->_assign('include_ui',            true);
        $this->_assign('include_prettyphoto',   true);       
        
        $this->context  = true;
        $this->js       = 'position_index';
        
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
            
            $this->_assign('target_doc',    $target_doc);
            $this->_assign('target_doc_id', $target_doc_id);
            $this->_assign('target_doc_no', $target_doc_no);
        }        
        $this->_assign('page_name', $this->page_name);
        $this->_display('index');
    }
}