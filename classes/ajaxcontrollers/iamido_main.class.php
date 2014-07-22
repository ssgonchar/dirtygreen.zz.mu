<?php
require_once APP_PATH . 'classes/models/iam_ido.class.php';

class MainAjaxController extends ApplicationAjaxController
{    

    function MainAjaxController()
    {
        ApplicationAjaxController::ApplicationAjaxController();
                
        $this->authorize_before_exec['get_category_list']      = ROLE_STAFF;
    }
    
    /*
     * Сохраняет задание
     * url: /iamido/savetask
     * 
     * @version 20140517, uskov
     */
    function savetask()
    {
        $task_id         = Request::GetInteger('task_id', $_REQUEST);
        $status_id       = Request::GetInteger('status_id', $_REQUEST);
        $start_data      = Request::GetString('start_data', $_REQUEST);
        $finish_data     = Request::GetString('finish_data', $_REQUEST);
        $title           = Request::GetString('title', $_REQUEST);
        $biz_id          = Request::GetInteger('biz_id', $_REQUEST);
        $description     = Request::GetString('description', $_REQUEST);
        $personal_notes  = Request::GetString('personal_notes', $_REQUEST);
        
        $modelIamIdo = new IamIdo();
        $saved_task = $modelIamIdo->Save($task_id, $status_id, $start_data, $finish_data, $title, $biz_id, $description, $personal_notes);
        //debug("1682", $saved_task);
    }
    
    /*
     * 
     */
    function deletetask()
    {
        $task_id = Request::GetInteger('task_id', $_REQUEST);
        
        $modelIamIdo = new IamIdo();
        $deleted_task = $modelIamIdo->Delete($task_id);
        //при ошибке процедуры в массиве 2 ячейки, при успехе 1
        if (count($deleted_task) < 2)
        {
	    $this->_send_json(array(
		'result'    => 'okay',
		'deleted_task'   => $deleted_task
	    )); 
        }  else {
            $this->_send_json(array(
		'result'    => 'error'
	    )); 
        }
    }
    
    /*
     * 
     */
    function updatetask()
    {
        $task_id = Request::GetInteger('task_id', $_REQUEST);
        
        $modelIamIdo = new IamIdo();
        $current_task = $modelIamIdo->GetById($task_id);
        //debug('1682', $current_task);
        //$current_task = array();
        $this->_assign('current_task', $current_task);
        $this->_send_json(array(
            'result'        => 'okay',
            //'current_task'  => $this->smarty->fetch('templates/html/iamido/control_edit.tpl')
            'current_task'  => $current_task
        )); 
    }
}
?>