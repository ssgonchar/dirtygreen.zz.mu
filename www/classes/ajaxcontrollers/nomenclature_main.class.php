<?php
require_once APP_PATH . 'classes/models/nomenclature_category.class.php';
require_once APP_PATH . 'classes/models/nomenclature.class.php';
require_once APP_PATH . 'classes/models/user.class.php';

class MainAjaxController extends ApplicationAjaxController
{    

    function MainAjaxController()
    {
        ApplicationAjaxController::ApplicationAjaxController();
                
        $this->authorize_before_exec['get_category_list']      = ROLE_STAFF;
    }
    
    /**
     * get ajax_categorylist
     * url: /nomenclature/get_category_list
     * 
     * @version 20140517, uskov
     */
    function getlistbycategoryid()
    {	//получаем значение category_url из запроса ajax
        $category_id = Request::GetInteger('category_url', $_REQUEST);	
	
	$nomenclature   = new Nomenclature();	//получаем массив со списком номенклатур по категории	
        $nomenclatures    = $nomenclature->GetListByCategoryId($category_id);
	
	$this->_assign('list', $nomenclatures);
	
        if (count($nomenclatures) != 0)
        {
	    $this->_send_json(array(	//отправляем данные по текущему ajax запросу
		'result'    => 'okay', 	//fetch('путь к шаблону') - вроде как заполняет указанный шаблон передаваемыми данными
		'content'   => $this->smarty->fetch('templates/html/nomenclature/control_nomenclature.tpl')	//json.content
	    )); 
        }
	else
	{
	    $this->_send_json(array(	//отправляем данные по текущему ajax запросу
		'result'    => 'error', 	//fetch('путь к шаблону') - вроде как заполняет указанный шаблон передаваемыми данными
		'message'   => 'no nomenclature'
	    )); 
	}
    }
    
    /**
     * Заполняет страницу редактирования позиции номенклатуры
     * url: /nomenclature/edit
     */
    function edit()
    {	//получаем id позиции из массива $_REQUEST
        $nomenclature_id = Request::GetInteger('nomenclature_id', $_REQUEST);

        if ($nomenclature_id > 0)	//если id > 0
        {
	    $nomenclature   = new Nomenclature();	//создаем новый обьект $nomenclatures класса Nomenclature()
	    $nomenclatures    = $nomenclature->GetById($nomenclature_id);	//по id возвращаем название позиции
	    if (empty($nomenclature)) _404();
	    
	    $this->_assign('editlist', $nomenclatures);   
        }        
	if (isset($nomenclatures))
        {
	    $this->_send_json(array(	//отправляем данные по текущему ajax запросу
		'result'    => 'okay', 	//fetch('путь к шаблону') - вроде как заполняет указанный шаблон передаваемыми данными
		'content'   => $this->smarty->fetch('templates/html/nomenclature/control_edit.tpl')	//json.content
	    ));
	    //dg($nomenclatures);
        }
    }
}
?>