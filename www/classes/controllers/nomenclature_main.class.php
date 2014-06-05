<?php
require_once APP_PATH . 'classes/models/nomenclature.class.php';
require_once APP_PATH . 'classes/models/nomenclature_category.class.php';
require_once APP_PATH . 'classes/models/user.class.php';

//обьявляю класс MainController
class MainController extends ApplicationController	//дочерний класс MainController наследует все публичные 
														//и защищенные методы из родительского класса ApplicationController
{
	function MainController()	//объявляю метод MainController() класса MainController
    {
        ApplicationController::ApplicationController();
        //вызываю метод ApplicationController() класса ApplicationController
        $this->authorize_before_exec['index']       = ROLE_STAFF;
        
        $this->breadcrumb = array('Nomenclature' => '/nomenclature');
    }
	
	/*
	* Отображает страницу списка номенклатур
	* url: /nomenclature
	*/
    function index()
    {
        $this->page_name = 'Nomenclature';
        
        $nomenclatures  = new Nomenclature();
        $this->_assign('list', $nomenclatures->GetList());
		  
        $nomenclaturecategories = new NomenclatureCategory();
        $this->_assign('categorylist', $nomenclaturecategories->GetSortedList());
        $this->js       = 'nomenclature_index';			
		  	
        $this->context  = true;        
        $this->_display('index');
	
	/******************************** Сохранение номенклатуры *******************************/
	
	if (isset($_REQUEST['btn_save']))	//если в массиве $_REQUEST есть ячейка 'btn_save'
        {
	    $form           = $_REQUEST['form'];	//получаем массив $form из глобального массива $_REQUEST

	    $nomenclature_id = Request::GetHtmlString('id', $form);		//достаем данные из массива $form
            $title           = Request::GetHtmlString('title', $form);
            $description     = Request::GetHtmlString('description', $form);
	    $category_id     = Request::GetHtmlString('category_id', $form);
	    //echo $nomenclature_id;
            
            if (empty($title))	//проверка заполнен ли title
            {
                $this->_message('Title must be specified !', MESSAGE_ERROR);
            }
            else		//если title заполнен
            {
                $nomenclatures    = new Nomenclature();	//создаем новый обьект $nomenclatures класса Nomenclature()
                $result           = $nomenclatures->Save($nomenclature_id, $title, $description, $category_id);	//сохраняем данные формы в БД nomenclature
		//dg($result);
            }
        } 
	/*****************************************************************************************/
    }
	 
	/**
	* Отображает страница добавления новой строки в таблицу
	* url: /nomenclature/add
	
    function add()
    {
        $this->edit();
    }*/
		
	/**
     * Отображает страницу просмотра позиций категории
     * url: /nomenclature/{id}
     
    function view()
    {				
	
	$this->page_name = 'Nomenclature';
        	
        $nomenclatures = new Nomenclature();
        //$this->_assign('list', $nomenclatures->GetListByCategoryId());
		  
        $nomenclaturecategories = new NomenclatureCategory();
        $this->_assign('categorylist', $nomenclaturecategories->GetSortedList());
				
        $this->js = 'nomenclature_index';			
        $this->context = true;        
        $this->_display('index');
	
    }*/	
		
	
	/**
     * Отображает страницу редактирования позиции номенклатуры
     * url: /nomenclature/edit/{id}
     
    function edit()
    {							//получаем id позиции из массива $_REQUEST
        $nomenclature_id = Request::GetInteger('id', $_REQUEST);

        if ($nomenclature_id > 0)	//если id > 0
        {
		$nomenclatures   = new Nomenclature();	//создаем новый обьект $nomenclatures класса Nomenclature()
		$nomenclature    = $nomenclatures->GetById($nomenclature_id);	//по id возвращаем название позиции
		if (empty($nomenclature)) _404();	
        }        
        
        if (isset($_REQUEST['btn_save']))	//если в массиве $_REQUEST есть ячейка 'btn_save'
        {
            $form           = $_REQUEST['form'];	//данные формы забиваем в массив $form

            $title          = Request::GetHtmlString('title', $form);		//достаем ячейку $title из массива $form
            $description    = Request::GetHtmlString('description', $form);	//достаем ячейку $description из массива $form
	    $category_id    = Request::GetHtmlString('category_id', $form);	//достаем ячейку $category_id из массива $category_id
						
            
            if (empty($title))	//проверка заполнен ли title
            {
                $this->_message('Title must be specified !', MESSAGE_ERROR);
            }
            else								//если title заполнен
            {
                $nomenclatures    = new Nomenclature();	//создаем новый обьект $nomenclatures класса Nomenclature()
                $result           = $nomenclatures->Save($nomenclature_id, $title, $description, $category_id);	//сохраняем данные формы в БД nomenclature
									
                if (empty($result) || isset($result['ErrorCode']))
                {
			$this->_message('Error while saving nomenclature !', MESSAGE_ERROR);
                }
                else
                { 
			$this->_message('Nomenclature was successfully saved !', MESSAGE_OKAY);
			$this->_redirect(array('nomenclature'));
                }
            }
        } 
        else if ($nomenclature_id > 0)
        {
          $form = $nomenclature['nomenclature'];
        }
        else
        {
          $form = array();
        }
        $nomenclaturecategories = new NomenclatureCategory();
        $this->_assign('categorylist', $nomenclaturecategories->GetSortedList());
	
        $this->page_name = 'Edit Nomenclature';
        $this->breadcrumb[$this->page_name] = '';
				
        $this->_assign('form', $form);	//передаем данные формы в Smarty
        
        $this->context   = true;
        $this->_display('edit');        
    }*/
	 
	 /**
     * Удаляет строку в таблице
     * url: /nomenclature/remove/{id}
     */
    function remove()
    {
        $id = Request::GetInteger('id', $_REQUEST);
        
        $nomenclatures    = new Nomenclature();
        $result           = $nomenclatures->Remove($id);
        
        if (empty($result) || isset($result['ErrorCode']))
        {
            $this->_message('Nomenclature is used', MESSAGE_ERROR);
        }
        else
        {
            $this->_message('Nomenclature was successfully removed', MESSAGE_OKAY);
        }
        
        $this->_redirect(array('nomenclature', ''));
    }
}
?>