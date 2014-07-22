<?php
require_once APP_PATH . 'classes/models/email.class.php';

class MainController extends ApplicationController 
{   
    //function MainController()
    public function __construct()
    {
        ApplicationController::ApplicationController();

        $this->authorize_before_exec['index']           = ROLE_STAFF;
        
        //$this->context = true; 
    }

    /**
     * Отображает страницу со списком писем
     * 
     * url: /emailmanager
     * 
     * @version 20120912, zharkov
     */
    public function index()
    {

        $this->_assign('tableeditor', true);
        $this->_assign('rowset', $rowset);
        $this->layout = 'emailmanager/emailmanager';
        $this->_display('index');
    }
}
