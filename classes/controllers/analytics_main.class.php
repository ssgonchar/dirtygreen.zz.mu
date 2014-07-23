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
        $this->authorize_before_exec['edit'] = ROLE_STAFF;
        $this->authorize_before_exec['neworder'] = ROLE_STAFF;
        $this->authorize_before_exec['selectitems'] = ROLE_STAFF;
        $this->authorize_before_exec['view'] = ROLE_STAFF;
        $this->authorize_before_exec['unregistered'] = ROLE_STAFF;


        $this->breadcrumb = array('Orders' => '/orders');
        $this->context = true;
    }

    /**
     * Отображает страницу списка заказов
     * url: /stocks
     */
    function index() {
        //$modelOrders = new Order();
        $modelStock = new Stock();
        $stock_id = 2;
        //$stock_id = Request::GetNumeric('stock_id', $_REQUEST);
        $steelgrades = $modelStock->GetItemSteelGrades($stock_id);
        /*  $steelgrade_ids = '';
          foreach ($steelgrades as $key => $row) {
          foreach ($selected_steelgrades as $s_key => $s_steelgrade_id) {
          $s_steelgrade_id = Request::GetInteger($s_key, $selected_steelgrades);

          //PRINT_R($row['steelgrade_id'].'=='.$s_steelgrade_id);
          //debug('1671', $s_steelgrade_id);

          if ($s_steelgrade_id <= 0) {
          continue;
          }

          if ($steelgrades[$key]['steelgrade_id'] == $s_steelgrade_id) {
          $steelgrades[$key]['selected'] = true;
          //dg($steelgrades[$key]);
          break;
          }
          }

          //PRINT_R($steelgrades[0]['steelgrade_id'].'=='.$steelgrades[0]['selected']);
          if (in_array($row['steelgrade_id'], $selected_steelgrades)) {
          $steelgrade_ids = $steelgrade_ids . (empty($steelgrade_ids) ? '' : ',') . $row['steelgrade_id'];
          }
          //print_r($steelgrade_ids.'<br/>');
          } */


        $modelOrders = new Order();
        $orders = $modelOrders->getCompliteOrders();

        /*foreach ($orders as &$row) {
            $orderPositions = $modelOrders->FillOrderInfo($rowset);
                    $row['positions'] = $orderPositions;
        }*/
        $this->_assign('stocks', $modelStock->GetList());
        $this->_assign('include_charts', true);
        $this->_assign('include_ui', true);
        $this->_assign('steelgrades', $steelgrades);
        $this->_assign('orders', $orders);

        $this->js = 'analytics_index';

        $this->_display('index');

        /* if($_REQUEST['btn_submit']) {
          $rowset = $this->search();
          } else {
          $rowset = $modelOrders->GetList($rowset, $modelOrders, $company_id,
          $period_from, $period_to, $status, $steelgrade_id,
          $thickness, $width, $keyword, $type, $page_no, $per_page);
          }
          $this->_assign('orders',    $rowset);
          $this->_display('index'); */
    }

    public function search() {
        $date_start = Request::GetDate($_REQUEST, 'date_start');
        $date_end = Request::GetDate($_REQUEST, 'date_end');
        $stock_id = Request::GetNumeric($_REQUEST, 'stock_id');
        $thickness = Request::GetNumeric($_REQUEST, 'thickness');
        $weight = Request::GetNumeric($_REQUEST, 'weight');
        $width = Request::GetNumeric($_REQUEST, 'width');
        $height = Request::GetNumeric($_REQUEST, 'height');
        $steelgreade_ids = Request::GetNumeric($_REQUEST, 'steelgrade_ids');
        $customer_ids = Request::GetNumeric($_REQUEST, 'customer_ids');
        $sent_location_ids = Request::GetNumeric($_REQUEST, 'sent_location_ids');
        $deliver_location_ids = Request::GetNumeric($_REQUEST, 'deliver_location_ids');
        $stockholder_ids = Request::GetNumeric($_REQUEST, 'stockholder_ids');

        $modelOrders = new Order();

        $rowset = $modelOrders->GetList($rowset, $modelOrders, $company_id, $period_from, $period_to, $status, $steelgrade_id, $thickness, $width, $keyword, $type, $page_no, $per_page);

        return $rowset;
    }

}
