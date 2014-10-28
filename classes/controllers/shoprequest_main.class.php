<?php

require_once APP_PATH . 'classes/components/object.class.php';
require_once APP_PATH . 'classes/core/Pagination.class.php';
require_once APP_PATH . 'classes/models/shoprequest.class.php';

class MainController extends ApplicationController {

    function MainController() {
        ApplicationController::ApplicationController();

        $this->authorize_before_exec['index'] = ROLE_STAFF;
        $this->authorize_before_exec['view'] = ROLE_STAFF;



        $this->breadcrumb = array('Request' => '/shoprequests');
        $this->context = true;
    }

    /**
     * Отображает страницу списка заказов
     * url: /shoprequest
     */
    function index() {
        $modelShopRequest = new ShopRequest();
        $arg = '';
        $this->_assign('list', $modelShopRequest->getRequests($arg));

        $this->_display('index');
    }
}
