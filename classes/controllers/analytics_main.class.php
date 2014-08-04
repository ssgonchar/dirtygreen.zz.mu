<?php

require_once APP_PATH . 'classes/components/object.class.php';
require_once APP_PATH . 'classes/core/Pagination.class.php';
require_once APP_PATH . 'classes/mailers/stockmailer.class.php';
require_once APP_PATH . 'classes/models/attachment.class.php';
require_once APP_PATH . 'classes/models/biz.class.php';
require_once APP_PATH . 'classes/models/company.class.php';
require_once APP_PATH . 'classes/models/invoicingtype.class.php';
require_once APP_PATH . 'classes/models/order.class.php';
require_once APP_PATH . 'classes/models/paymenttype.class.php';
require_once APP_PATH . 'classes/models/preorder.class.php';
require_once APP_PATH . 'classes/models/qc.class.php';
require_once APP_PATH . 'classes/models/sc.class.php';
require_once APP_PATH . 'classes/models/steelgrade.class.php';
require_once APP_PATH . 'classes/models/steelposition.class.php';
require_once APP_PATH . 'classes/models/stock.class.php';

class MainController extends ApplicationController {

    function MainController() {
        ApplicationController::ApplicationController();

        $this->authorize_before_exec['index'] = ROLE_STAFF;
        $this->authorize_before_exec['view'] = ROLE_STAFF;



        $this->breadcrumb = array('Orders' => '/orders');
        $this->context = true;
    }

    /**
     * Отображает страницу списка заказов
     * url: /stocks
     */
    function index() {
        $modelStock = new Stock();
        $this->_assign('stocks', $modelStock->GetList());
        

        
        $this->_assign('include_charts', true);
        $this->_assign('include_ui', true);
        
        
        $this->js = 'analytics_index';
        


        if (isset($_REQUEST['BTN_SUBMIT'])) {
            $filter = $this->createFilter($_REQUEST['form']);
            $this->_redirect($filter);
        } elseif (isset($_REQUEST['filter'])) {
        if(isset($_SESSION['analytics']['delivery_town']) && $_SESSION['analytics']['delivery_town'] !== '') $this->_assign('delivery_town', $_SESSION['analytics']['delivery_town']);
            $orders = $this->search($_REQUEST['filter']);
            $this->_assign('orders', $orders);
            $this->_display('index');
            return;
        }

        $this->_display('index');
    }

    private function createFilter($form) {
        //$str = 'blabla';
        //dg($form);
        if (count($form < 1)) {
            $filter = array();
            $filter[] = 'analytics';
        }

        $filter = array();
        $filter[] = 'analytics';
        $filter[] = 'filter';


        $location_ids = $this->createFilterFromArray($form['location_ids'], ',');
     
        $stockholder_ids = $this->createFilterFromArray($form['stockholders_ids'], ',');
        $steelgrade_ids = $this->createFilterFromArray($form['steelgrade_ids'], ',');
        $customer_id = Request::GetNumeric('customer_id', $form);
        $deliverypoint_title = Request::GetString('deliverypoint_title', $form);
        $thickness = $this->createFilterFromArray($form['thickness'], ',');
        $width = $this->createFilterFromArray($form['width'], ',');
        $lenght = $this->createFilterFromArray($form['lenght'], ',');
        $weight = $this->createFilterFromArray($form['weight'], ',');
        

        $filter_params = array();
        if ($_REQUEST['form']['stock_id'] > 0) {
            $filter_params[] = 'stock_id:' . Request::GetNumeric('stock_id', $_REQUEST['form']);
        }
        if ($form['date_start'] > 0)
            $filter_params[] = 'date_start:' . $_REQUEST['form']['date_start'];
        if ($form['date_end'] > 0)
            $filter_params[] = 'date_end:' . $_REQUEST['form']['date_end'];
        if ($location_ids)
            $filter_params[] = 'location_ids:' . $location_ids;
        if ($stockholder_ids)
            $filter_params[] = 'stockholder_ids:' . $stockholder_ids;
          if ($steelgrade_ids)
            $filter_params[] = 'steelgrade_ids:' . $steelgrade_ids;
          
         if ($form['thickness'] > 0)
            $filter_params[] = 'thickness:' . $_REQUEST['form']['thickness'];
         if ($form['width'] > 0)
            $filter_params[] = 'width:' . $_REQUEST['form']['width'];
         if ($form['lenght'] > 0)
            $filter_params[] = 'lenght:' . $_REQUEST['form']['lenght'];
         if ($form['weight'] > 0)
            $filter_params[] = 'weight:' . $_REQUEST['form']['weight'];
          
         
                
        if (isset($customer_id))
            $filter_params[] = 'customer_id:' . $customer_id;
        if (isset($deliverypoint_title)) {
            $_SESSION['analytics']['delivery_town'] = $deliverypoint_title;
            $filter_params[] = 'deliverypoint_title:' . $deliverypoint_title;
        }

       //dg($filter_params);
        $filter[] = implode(';', $filter_params);
        return $filter;
    }

    /*
     * createFilterFromArray
     * Преобразует масив в строку,
     * при этом фильтруя данные согласно указанному типу
     * 
     */

    private function createFilterFromArray($arr, $delimeter, $date = FALSE) {
        if (count($arr) < 1) {
            return false;
        }

        $arr_cleared = array();
        $type_data = '';

        foreach ($arr as $key => $row) {
            if ($date) {
                $type_data = 'date';
            } else if (ctype_digit(strval($arr[$key]))) {
                $type_data = 'integer';
            } else {
                $type_data = gettype($arr[$key]);
            }

            switch ($type_data) {
                case 'integer':
                    $arr_cleared[] = Request::GetNumeric($key, $arr);
                    break;
                case 'double':
                    $arr_cleared[] = Request::GetNumeric($key, $arr);
                    break;
                case 'string':
                    $arr_cleared[] = Request::GetString($key, $arr);
                    break;
                case 'boolean':
                    $arr_cleared[] = Request::GetBoolean($key, $arr);
                    break;
                case 'date' :
                    $arr_cleared[] = Request::GetDateTime($key, $arr);
                    break;
                default:
                    $arr_cleared[] = 'Unknown type of variables';
                    break;
            }
        }


        if (count($arr_cleared) > 0) {
            $filter = implode($delimeter, $arr_cleared);
            return $filter;
        }
    }

    public function search($arr_filter) {
       // dg($_REQUEST);
        $arr_raw = explode(';', $arr_filter);

        foreach ($arr_raw as &$row) {
            $arr_tmp = explode(':', $row);
            $key = $arr_tmp[0];
            $val = $arr_tmp[1];
            $arg[$key] = $val;
        }
        if ($arg['stock_id'] < 1 || empty($arg['stock_id']) || !isset($arg['stock_id'])) {
            $this->_message("Please select stock first!", MESSAGE_ERROR);
        } else {
            $date_start = Request::GetDateForDB('date_start', $arg);
            $date_end = Request::GetDateForDB('date_end', $arg);
            
            $arg['date_start'] = $date_start; 
            $arg['date_end'] = $date_end;
            if(isset($_SESSION['analytics']['delivery_town']) && $_SESSION['analytics']['delivery_town'] !== '') $arg['delivery_town'] = $_SESSION['analytics']['delivery_town'];
            
            
            //dg($arg);
            $modelOrder = new Order();
            $orders = $modelOrder->getCompliteOrders($arg);

            return $orders;
        }
    }

    /* public function search() {
      $filter_settings['date_start'] = Request::GetDate($_REQUEST, 'date_start');
      $filter_settings['date_end'] = Request::GetDate($_REQUEST, 'date_end');
      $filter_settings['stock_id'] = Request::GetNumeric($_REQUEST, 'stock_id');
      $filter_settings['thickness'] = Request::GetNumeric($_REQUEST, 'thickness');
      $filter_settings['weight'] = Request::GetNumeric($_REQUEST, 'weight');
      $filter_settings['width'] = Request::GetNumeric($_REQUEST, 'width');
      $filter_settings['height'] = Request::GetNumeric($_REQUEST, 'height');
      $filter_settings['steelgreade_ids'] = Request::GetNumeric($_REQUEST, 'steelgrade_ids');
      $filter_settings['customer_ids'] = Request::GetNumeric($_REQUEST, 'customer_ids');
      $filter_settings['sent_location_ids'] = Request::GetNumeric($_REQUEST, 'sent_location_ids');
      $filter_settings['deliver_location_ids'] = Request::GetNumeric($_REQUEST, 'deliver_location_ids');
      $filter_settings['stockholder_ids'] = Request::GetNumeric($_REQUEST, 'stockholder_ids');

      $modelOrders = new Order();

      $rowset = $modelOrders->GetList($rowset, $modelOrders, $company_id, $period_from, $period_to, $status, $steelgrade_id, $thickness, $width, $keyword, $type, $page_no, $per_page);

      $link = $this->CreateRedirect();
      //return $rowset;
      }

      /* private function CreateRedirect() [

      ] */
}
