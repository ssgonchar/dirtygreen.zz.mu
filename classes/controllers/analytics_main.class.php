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

class MainController extends ApplicationController
{
    function MainController()
    {
        ApplicationController::ApplicationController();
        
        $this->authorize_before_exec['index']           = ROLE_STAFF;
        $this->authorize_before_exec['edit']            = ROLE_STAFF;
        $this->authorize_before_exec['neworder']        = ROLE_STAFF;
        $this->authorize_before_exec['selectitems']     = ROLE_STAFF;
        $this->authorize_before_exec['view']            = ROLE_STAFF;
        $this->authorize_before_exec['unregistered']    = ROLE_STAFF;        
        
        
        $this->breadcrumb   = array('Orders' => '/orders');
        $this->context      = true;                
    }

    
  
    
    /**
     * Отображает страницу списка заказов
     * url: /stocks
     */
    function index()
    {
        $orders = new Order();
       
        $this->_assign('include_charts', true);
        $this->_assign('include_ui',    true);

        $this->js = 'analytics_index';

        $this->_display('index');
    }
}
