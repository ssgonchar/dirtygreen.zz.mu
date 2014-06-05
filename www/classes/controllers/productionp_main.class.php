<?php
require_once APP_PATH . 'classes/models/user.class.php';

class MainController extends ApplicationController
{
    function MainController()
    {
        ApplicationController::ApplicationController();
        
        $this->authorize_before_exec['index'] = ROLE_STAFF;        
    }

    /**
     * Отображает страницу создания Production Possibilities
     * url: /prodpos
     * 
     * @version 20130224, zharkov
     */
    function index()
    {        
        $this->page_name = 'Production Possibilities';
        $this->breadcrumb[$this->page_name] = '';

        $this->js = array('dimension_convert', 'productionp_main');
        
        $this->_display('index');
    }    
}