<?php
require_once APP_PATH . 'classes/models/user.class.php';

class MainController extends ApplicationController
{
    function MainController()
    {
        ApplicationController::ApplicationController();
        
        $this->authorize_before_exec['index']   = ROLE_MODERATOR;
        $this->authorize_before_exec['view']    = ROLE_MODERATOR;
        
        $this->breadcrumb   = array('Persons' => '/persons');
    }

    /**
     * Отображает список запросов на регистрацию
     * url: /regrequests
     */
    function index()
    {        
        $this->page_name = 'Registration Requests';
        $this->breadcrumb[$this->page_name] = '';

        $users = new User();
        $this->_assign('list', $users->RequestsGetList());
        
        $this->context = true;       
        $this->_display('index');
    }    
    
    /**
     * Отображает страница добавления новой компании
     * url: /regrequest/{id}
     */
    function view()
    {
        $id = Request::GetInteger('id', $_REQUEST);
        
        $this->page_name = 'View Request';
        $this->breadcrumb['Registration Requests']  = 'regrequests';
        $this->breadcrumb[$this->page_name]         = '';

        $users = new User();
        $this->_assign('list', $users->RequestsGetList());
        
        $this->context = true;       
        $this->_display('view');
    }
}