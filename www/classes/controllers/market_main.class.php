<?php
require_once APP_PATH . 'classes/models/country.class.php';
require_once APP_PATH . 'classes/models/market.class.php';

class MainController extends ApplicationController
{
    function MainController()
    {
        ApplicationController::ApplicationController();
        
        $this->authorize_before_exec['index']   = ROLE_STAFF;
        $this->authorize_before_exec['edit']    = ROLE_STAFF;
        $this->authorize_before_exec['add']     = ROLE_STAFF;
        $this->authorize_before_exec['remove']  = ROLE_STAFF;
        
        $this->breadcrumb   = array('Markets' => '/markets');
        $this->context      = true;               
    }

    /**
     * Отображает индексную страницу регистра бизнесов
     * url: /markets
     */
    function index()
    {        
        $this->page_name = 'Markets';
        $this->breadcrumb[$this->page_name] = '';

        $markets = new Market();
        $this->_assign('list', $markets->GetList());
        
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
    {							//получаем id рынка из массива $_REQUEST
        $market_id = Request::GetInteger('id', $_REQUEST);

        if ($market_id > 0)	//если id > 0
        {
            $markets  = new Market();	//создаем новый обьект $markets класса Market()
            $market    = $markets->GetById($market_id);	//по id возвращаем название маркета
            if (empty($market)) _404();	
        }        
        

        if (isset($_REQUEST['btn_save']))
        {
            $form           = $_REQUEST['form'];
            $countries      = isset($_REQUEST['countries']) ? $_REQUEST['countries'] : array();

            $title          = Request::GetHtmlString('title', $form);
            $description    = Request::GetHtmlString('description', $form);
            $map_data       = Request::GetHtmlString('map_data', $form);
            
            if (empty($title))
            {
                $this->_message('Title must be specified !', MESSAGE_ERROR);
            }
            /*
            else if (empty($countries))
            {
                $this->_message('Countries must be specified !', MESSAGE_ERROR);
            }
            */
            else
            {
                $markets    = new Market();
                $result     = $markets->Save($market_id, $title, $description, $map_data);
                
                if (empty($result) || isset($result['ErrorCode']))
                {
                    $this->_message('Error while saving market !', MESSAGE_ERROR);
                }
                else
                {
                    foreach ($markets->GetCountries($result['id']) as $row)
                    {
                        $delete_flag = true;
                        foreach ($countries as $key => $row1)
                        {
                            if ($row['country_id'] == $row1['country_id']) 
                            {
                                unset($countries[$key]);
                                $delete_flag = false;
                            }
                        }
                        
                        if ($delete_flag) $markets->RemoveCountry($result['id'], $row['country_id']);
                    }
                    
                    foreach ($countries as $row)
                    {
                        $country_id = Request::GetInteger('country_id', $row);
                        if (empty($country_id)) continue;
                        
                        $markets->AddCountry($result['id'], $country_id);
                    }
                    
                    $this->_message('Market was successfully saved !', MESSAGE_OKAY);
                    $this->_redirect(array('markets'));
                }
            }
        }
        else if ($market_id > 0)
        {
            $form       = $market['market'];
            $countries  = $markets->GetCountries($market_id);
        }
        else
        {
            $form       = array();
            $countries  = array();
        }
        
        $this->page_name = $market_id > 0 ? 'Edit Market' : 'New Market';
        $this->breadcrumb[$this->page_name] = '';
        
        $country = new Country();
        $this->_assign('countries_list',    $country->GetList(false));
        $this->_assign('countries',         $country->FillCountryInfo($countries));
        $this->_assign('form',              $form);
        
        $this->js = 'market_edit';
        
        $this->_display('edit');        
    }
    
    /**
     * Отображает страницу просмотра
     * url: /market/{market_id}
     */
    function view()
    {
        $market_id = Request::GetInteger('id', $_REQUEST);
        if (empty($market_id)) _404();
        
        $markets    = new Market();
        $market     = $markets->GetById($market_id);
        $market     = $market['market'];
        if (empty($market)) _404();            

        $country    = new Country();
        $countries  = $markets->GetCountries($market_id);
        
        $this->_assign('form',      $market);
        $this->_assign('countries', $country->FillCountryInfo($countries));
        
        $this->page_name = $market['title'];
        $this->breadcrumb[$this->page_name] = '';
        
        $this->_display('view');        
    }
    
    /**
     * Удаляет рынок
     * 
     * url: /market/{market_id}/remove
     */
    function remove()
    {
        _404();
    }
}