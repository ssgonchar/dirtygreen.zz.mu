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
    {	//�������� �������� category_url �� ������� ajax
        $category_id = Request::GetInteger('category_url', $_REQUEST);	
	
	$nomenclature   = new Nomenclature();	//�������� ������ �� ������� ����������� �� ���������	
        $nomenclatures    = $nomenclature->GetListByCategoryId($category_id);
	
	$this->_assign('list', $nomenclatures);
	
        if (count($nomenclatures) != 0)
        {
	    $this->_send_json(array(	//���������� ������ �� �������� ajax �������
		'result'    => 'okay', 	//fetch('���� � �������') - ����� ��� ��������� ��������� ������ ������������� �������
		'content'   => $this->smarty->fetch('templates/html/nomenclature/control_nomenclature.tpl')	//json.content
	    )); 
        }
	else
	{
	    $this->_send_json(array(	//���������� ������ �� �������� ajax �������
		'result'    => 'error', 	//fetch('���� � �������') - ����� ��� ��������� ��������� ������ ������������� �������
		'message'   => 'no nomenclature'
	    )); 
	}
    }
    
    /**
     * ��������� �������� �������������� ������� ������������
     * url: /nomenclature/edit
     */
    function edit()
    {	//�������� id ������� �� ������� $_REQUEST
        $nomenclature_id = Request::GetInteger('nomenclature_id', $_REQUEST);

        if ($nomenclature_id > 0)	//���� id > 0
        {
	    $nomenclature   = new Nomenclature();	//������� ����� ������ $nomenclatures ������ Nomenclature()
	    $nomenclatures    = $nomenclature->GetById($nomenclature_id);	//�� id ���������� �������� �������
	    if (empty($nomenclature)) _404();
	    
	    $this->_assign('editlist', $nomenclatures);   
        }        
	if (isset($nomenclatures))
        {
	    $this->_send_json(array(	//���������� ������ �� �������� ajax �������
		'result'    => 'okay', 	//fetch('���� � �������') - ����� ��� ��������� ��������� ������ ������������� �������
		'content'   => $this->smarty->fetch('templates/html/nomenclature/control_edit.tpl')	//json.content
	    ));
	    //dg($nomenclatures);
        }
    }
}
?>