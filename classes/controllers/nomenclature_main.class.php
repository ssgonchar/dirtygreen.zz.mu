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
        $this->page_name = 'Navigation';
        
        //$nomenclatures  = new Nomenclature();
        //$this->_assign('list', $nomenclatures->GetList());
		  
        $nomenclaturecategories = new NomenclatureCategory();
        $this->_assign('categorylist', $nomenclaturecategories->GetSortedList());
        $this->js       = 'nomenclature_index';	
            //debug("1682", $_SESSION);		
		  	
        $this->context  = true;        
        $this->_display('index');
	
	/******************************** Сохранение номенклатуры ******************************
	
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
        } */
	/*****************************************************************************************/
    }
	
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