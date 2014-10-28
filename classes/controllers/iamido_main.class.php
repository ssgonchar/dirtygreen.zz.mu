<?php
require_once APP_PATH . 'classes/models/user.class.php';
require_once APP_PATH . 'classes/models/iam_ido.class.php';
require_once APP_PATH . 'classes/models/biz.class.php';

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
     * Главная страница - отображает задания для юзера
     */
    function index()
    { 
        //$this->page_name = 'I am - I do';
        $user_id = $this->user_id;
        $modelBiz = new Biz();
        $modelIamIdo = new IamIdo();
         
        $task_list = $modelIamIdo->GetList($user_id);
        //debug('1682', $task_list);
        foreach ($task_list as $key => $row) {
            $biz_id = $row['task']['biz_id'];
            $biz_info = $modelBiz->GetById($biz_id);
            $biz_title = $biz_info['biz']['doc_no']; 
            $task_list[$key]['task']['biz_title'] = $biz_title;
        }
        $this->_assign('tasklist', $task_list);
        $this->_assign('my_user_id', $this->user_id);
        
        $users = new User();
        $users_list = $users->GetListForChatSeparated();
        $this->_assign('users', $users_list);
                
        $this->js       = 'iam_ido';
        $this->context  = true;        
        $this->_display('index');
    }
}
?>