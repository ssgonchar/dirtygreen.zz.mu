<?php
require_once APP_PATH . 'classes/models/user.class.php';
require_once APP_PATH . 'classes/models/iam_ido.class.php';

//обьявляю класс MainController
class MainController extends ApplicationController	//дочерний класс MainController наследует все публичные 
							//и защищенные методы из родительского класса ApplicationController
{
    function MainController()	//объявляю метод MainController() класса MainController
    {
        ApplicationController::ApplicationController();
        //вызываю метод ApplicationController() класса ApplicationController
        $this->authorize_before_exec['index']       = ROLE_STAFF;
        
        $this->breadcrumb = array('I_am_i_do' => '/i_am_i_do');
    }
    /*
     * 
     */
    function index()
    {
        //$this->page_name = 'I am - I do';
        $modelIamIdo = new IamIdo();
        $task_list = $modelIamIdo->GetList();
        $this->_assign('tasklist', $task_list);
        
        
        $this->js       = 'iam_ido';
        $this->context  = true;        
        $this->_display('index');
    }
}
?>