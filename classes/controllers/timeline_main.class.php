<?php
require_once APP_PATH . 'classes/models/attachment.class.php';

class MainController extends ApplicationController
{
    function MainController()
    {
        ApplicationController::ApplicationController();
        
        $this->authorize_before_exec['index']   = ROLE_STAFF;
        $this->authorize_before_exec['edit']    = ROLE_STAFF;
        $this->authorize_before_exec['add']     = ROLE_STAFF;        
        
        $this->breadcrumb   = array('Countries' => '/countries');
    }

    /**
     * Отображает индексную страницу регистра бизнесов
     * url: /companies
     */
    function index()
    {        
        $this->page_name = 'Countries';
        $this->breadcrumb[$this->page_name] = '';

        $countries = new Country();
        $this->_assign('list', $countries->GetList());
        
        $this->context = true;       
        $this->_display('index');
    }    
    
    /**
     * Отображает страница добавления новой компании
     * url: /company/add
     */
    function add()
    {
        $this->edit();
    }
    
    /**
     * Отображает страницу редактирования компании
     * url: /company/edit/{id}
     */
    function edit()
    {
        $country_id = Request::GetInteger('id', $_REQUEST);

        if ($country_id > 0)
        {
            $countries  = new Country();
            $country    = $countries->GetById($country_id);
            
            if (empty($country)) _404();            
        }        
        

        if (isset($_REQUEST['btn_save']))
        {
            $form = $_REQUEST['form'];
            
        }
        else
        {
            $form = $country_id > 0 ? $country['country'] : array();
        }
        
        $this->page_name = $country_id > 0 ? 'Edit Country' : 'New Country';
        $this->breadcrumb[$this->page_name] = '';
        
        $this->_assign('form', $form);
        
        $this->context = true;       
        $this->_display('edit');        
    }
}