<?php
require_once APP_PATH . 'classes/models/iam_ido.class.php';
require_once APP_PATH . 'classes/common/parser.class.php';
require_once APP_PATH . 'classes/models/biz.class.php';
require_once APP_PATH . 'classes/models/user.class.php';

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
        $biz_title       = Request::GetString('biz_title', $_REQUEST);
        $description     = Request::GetString('description', $_REQUEST);
        $personal_notes  = Request::GetString('personal_notes', $_REQUEST);
        //если обе даты не указаны правильно - ошибка
        if (strtotime($finish_data)<=0 || strtotime($finish_data)<=0){
            $this->_send_json(array('result' => 'date_not_exists'));
            return;
        }
        //если время окончания задания меньше времени начала - ошибка
        if (strtotime($finish_data) < strtotime($start_data)){
            $this->_send_json(array('result' => 'wrong_time'));
            return;
        }
        //если не заполнен title или description - ошибка
        if (empty($title) && empty($description)){
            $this->_send_json(array('result' => 'empty_fields'));
            return;
        }
        
        $modelIamIdo = new IamIdo();
        $saved_task = $modelIamIdo->Save($task_id, $status_id, $start_data, $finish_data, $title, $biz_id, $description, $personal_notes);
        //получаю biz для сохранения
        $biz             = Parser::GetBiz($biz_title);
        //debug('1682', $biz);
        if(!empty($biz)) $modelIamIdo->SaveObjects($saved_task['param_task_id'], $biz);
        
        $this->_send_json(array(
            'result'      => 'okay',
            'saved_task'  => $saved_task
        ));
    }
        
    /*
     * Удаляет задание
     * @param $task_id
     */
    function deletetask()
    {
        //удалять задания могут только с правами не ниже администратора
        if($_SESSION['user']['role_id'] > 2){
            $this->_send_json(array('result' => 'permissions', 'code' => 'You do not have permissions to delete tasks!'));
            return;
        }
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
     * Возвращает инфо о задании 
     * @param $task_id
     */
    function updatetask()
    {
        $task_id = Request::GetInteger('task_id', $_REQUEST);
        //$user_id придет с пустым значением или со значением своего id, если редактируем свое задание
        $user_id = Request::GetInteger('user_id', $_REQUEST);
        
        //редактировать чужие задания могут только с правами не ниже администратора
        if($user_id !== '' && $user_id !== $this->user_id){
            if($_SESSION['user']['role_id'] > 2){
                $this->_send_json(array('result' => 'permissions', 'code' => "You don`t have permissions to update someone else's tasks!"));
                return;
            }
        }
        
        $modelIamIdo = new IamIdo();
        $current_task = $modelIamIdo->GetById($task_id);
        //если ощибка значит такого задания нет
        if ($current_task == 'error'){
            $this->_send_json(array(
                'result'        => 'wrong_task'
            ));
        }
        //нельзя редактировать завершенные задания
        elseif ($current_task['status_id'] == '3') {
            $this->_send_json(array(
                'result'        => 'wrong_status'
            ));
        }
        else {
            $this->_assign('current_task', $current_task);
            $this->_send_json(array(
                'result'        => 'okay',
                'current_task'  => $current_task
            )); 
        }
        
    }
    
    /*
     * Сохраняет использованное время указанное вручную
     * @param $task_id
     * @param $start_data дата начала
     * @param $finish_data дата окончания
     */
    function manualsaveusedtime()
    {
        $task_id     = Request::GetInteger('task_id', $_REQUEST);
        $start_data  = Request::GetString('start_data', $_REQUEST);
        $finish_data = Request::GetString('finish_data', $_REQUEST);
        //если обе даты существуют
        if (strtotime($finish_data)>0 && strtotime($finish_data)>0){
            //если время окончания задания больше времени начала
            if (strtotime($finish_data) > strtotime($start_data)){
                $modelIamIdo = new IamIdo();
                $task_info   = $modelIamIdo->GetById($task_id);
                if ($task_info == 'error'){
                    $this->_send_json(array(
                        'result' => 'wrong_task'
                    ));
                }
                //нельзя сохранять время у выполненных заданий
                elseif ($task_info['status_id'] == '3') {
                    $this->_send_json(array(
                        'result' => 'wrong_status'
                    ));
                }
                else {
                    $used_time = $task_info['used_time'];
                    $current_used_time = $modelIamIdo->CalcCurrentUsedTime($start_data, $finish_data, $used_time);
                    //сохраняю текущее затраченное время по текущему заданию в БД 
                    $updated_task = $modelIamIdo->SaveUsedTime($task_id, $current_used_time);
                    //$updated_task['current_used_time'] = $current_used_time;
                    $this->_send_json(array(
                        'result'        => 'okay',
                        'updated_task'  => $updated_task
                    ));
                    //debug('1682', $updated_task);
                } 
            }  else {$this->_send_json(array('result' => 'wrong_time'));}
        }  else {$this->_send_json(array('result' => 'date_not_exist'));}
    }
    
    /*
     * Изменяет статус задания
     * @param $task_id
     * @param $status_id
     */
    function changestatus()
    {
        $task_id   = Request::GetString('task_id', $_REQUEST);
        $status_id = Request::GetInteger('status_id', $_REQUEST);
        
        $user_id = $this->user_id;
        $browzer_info = '0';
        
        $modelIamIdo  = new IamIdo();
        $task_info   = $modelIamIdo->GetById($task_id);
        //если искомого задания не существует - возвращаю ошибку wrong_task
        if ($task_info == 'error'){
            $this->_send_json(array(
                'result' => 'wrong_task', 'code' => 'You don`t have task with ID = '.$task_id.'!'
            ));
        }
        else {
            //если статус искомого задания "завершено" - ошибка wrong_status
            $current_status = $task_info['status_id'];
            if($current_status == '3'){
                $this->_send_json(array(
                    'result' => 'wrong_status'
                ));
            }
            else {
                //меняю статус
                if($status_id == '4') $browzer_info = $_SERVER['HTTP_USER_AGENT'];
                $updated_task = $modelIamIdo->ChangeStatus($task_id, $status_id, $browzer_info);
                
                //Если меняю статус на активный, то получаю список заданий для юзера.
                //Проверяю, если есть активное задание со статусом 2 или 4
                $changed_task = 'notask';
                if($status_id == '2' || $status_id == '4'){
                    $task_list = $modelIamIdo->GetList($user_id);
                    foreach ($task_list as $row) {
                        if($row['status_id'] == '2' || $row['status_id'] == '4'){
                            $finded_task_id = $row['task_id'];
                            //при условии что это не искомое, меняю его статус на 1 (waiting)
                            if($task_id !== $finded_task_id){
                                $status_waiting = '1';
                                $browzer_info = '0';
                                $result = $modelIamIdo->ChangeStatus($finded_task_id, $status_waiting, $browzer_info);
                                $changed_task = $result['param_task_id'];
                            }
                        }
                    }
                }
                $this->_send_json(array(
                    'result'             => 'okay',
                    'updated_task'       => $updated_task,
                    'changed_task' => $changed_task
                ));
            }
        }
    }
    
    /*
     * Возвращает id задания, у которого включен автоматический счетчик времени, т.е. status_id равен 4
     */
    function getautocountingtask()
    {
        $user_id = $this->user_id;
        $status_id = '4';
        $modelIamIdo  = new IamIdo();
        $auto_count_task   = $modelIamIdo->GetActiveTask($user_id, $status_id);
        //если есть задание с включенным счетчиком
        if(!empty($auto_count_task)){
            //получаю id этого задания
            $task_id = $auto_count_task['task_id'];
            //получаю использованное время
            $used_time = $auto_count_task['used_time'];
            //инфо о браузере, в котором включили счетчик (записывается в базу для идентификации)
            $browzer_info = $auto_count_task['browzer_info'];
            //проверяю, является ли текущий браузер тем, из которого включили счетчик 
            //(защита от начисления времени из всех браузеров в которых загружен наш сайт)
            if($_SERVER['HTTP_USER_AGENT'] == $browzer_info){
                //проверяю, есть ли в сессии время последнего сохранения и id задания
                if (array_key_exists('current_task', $_SESSION)) {
                    //совпадает ли id задания в сессии с тем что нашли (в сессии может лежать запись с предыдущего задания)
                    if ($_SESSION['current_task']['task_id'] == $task_id){
                        //если есть, то получаю разницу текущего времени и времени последнего сохранения
                        $time_difference = time() - $_SESSION['current_task']['time_of_save'];
                        if($time_difference >= 60){
                            //перезаписываю время последнего сохранения
                            $_SESSION['current_task']['time_of_save'] = time();

                            $current_used_time = $used_time + $time_difference;
                            //здесь вызываю функцию сохранения
                            $updated_task = $modelIamIdo->SaveUsedTime($task_id, $current_used_time);
                            //debug('1682', $_SESSION);
                            $this->_send_json(array(
                                'result'          => 'okay',
                                'updated_task'    => $updated_task,
                                'time_difference' => $time_difference,
                                'variant'         => 'id задания в сессии совпадает с найденным в базе'
                            ));
                        }
                    }
                    //если записи есть, но по другому заданию, то записываю в сессию время последнего сохранения и id найденного задания
                    else {
                        //если нет то записываю в сессию время последнего сохранения и id задания
                        $_SESSION['current_task']['time_of_save'] = time();
                        $_SESSION['current_task']['task_id'] = $task_id;
                        $current_used_time = $used_time + 60;
                        //здесь вызываю функцию сохранения
                        $updated_task = $modelIamIdo->SaveUsedTime($task_id, $current_used_time);
                        $this->_send_json(array(
                            'result'       => 'okay',
                            'updated_task' => $updated_task,
                            'variant'      => 'id задания в сессии есть, но не совпадает с найденным в базе'
                        ));
                    }
                }
                else {
                    //если нет то записываю в сессию время последнего сохранения и id найденного задания
                    $_SESSION['current_task']['time_of_save'] = time();
                    $_SESSION['current_task']['task_id'] = $task_id;
                    $current_used_time = $used_time + 60;
                    //здесь вызываю функцию сохранения
                    $updated_task = $modelIamIdo->SaveUsedTime($task_id, $current_used_time);
                    $this->_send_json(array(
                        'result'       => 'okay',
                        'updated_task' => $updated_task,
                        'variant'      => 'в сессии нет id задания'
                    ));
                }
            }
        }
    }
    
    /*
     * Возвращает список заданий для указанного пользователя
     */
    function getuserlist()
    {
        $user_id = Request::GetInteger('user_id', $_REQUEST);
        $modelIamIdo  = new IamIdo();
        $modelBiz = new Biz();
        $task_list = $modelIamIdo->GetList($user_id);
        foreach ($task_list as $key => $row) {
            $biz_id = $row['task']['biz_id'];
            $biz_info = $modelBiz->GetById($biz_id);
            $biz_title = $biz_info['biz']['doc_no']; 
            $task_list[$key]['task']['biz_title'] = $biz_title;
        }
        if(!empty($task_list)){
            $this->_assign('user_task_list', $task_list);
            $this->_send_json(array(
                'result'       => 'okay',
                'user_task_list' => $this->smarty->fetch('templates/html/iamido/control_index.tpl')
            ));
        }
        else {$this->_send_json(array('result' => 'empty'));}
    }
    
    /*
     * Возвращает список активных заданий для каждого юзера
     */
    function getusersactivetasks()
    { 
        $modelIamIdo = new IamIdo();
        $modelUser   = new User();
        $users_list  = $modelUser->GetListForChatSeparated();
        //леплю новый массив для отображения активных заданий по всем юзерам
        $active_tasks_list = array();
        foreach ($users_list['staff'] as $key => $row) {
            //данные о юзере
            $active_tasks_list[$key]['user_id']     = $row['user']['id'];
            $active_tasks_list[$key]['user_name']   = $row['user']['person']['doc_no_short'];
            $active_tasks_list[$key]['img_src']     = $row['user']['person']['picture']['original_name'];
            $active_tasks_list[$key]['active_task'] = 'No active task';
            //получаю задания для каждого юзера
            $user_task_list = $modelIamIdo->GetList($row['user']['id']);
            //если есть активные - записываю в массив по данному юзверю
            foreach ($user_task_list as $subkey => $subrow) {
                //если у юзера есть активное задание - добавляю его
                if($subrow['status_id'] == '2' || $subrow['status_id'] == '4') $active_tasks_list[$key]['active_task'] = $subrow;
            }
        }
        //получаю
        $modelBiz    = new Biz();
        foreach ($active_tasks_list as $key => $row) {
            if($row['active_task'] !== 'No active task'){
                $biz_id = $row['active_task']['task']['biz_id'];
                $biz_info = $modelBiz->GetById($biz_id);
                $biz_title = $biz_info['biz']['doc_no']; 
                $active_tasks_list[$key]['active_task']['biz_title'] = $biz_title;
            }
        }
        //debug('1682', $active_tasks_list); 
        $this->_assign('active_tasks_list', $active_tasks_list);
        $this->_send_json(array(
            'result'       => 'okay',
            'active_tasks_list' => $this->smarty->fetch('templates/html/iamido/control_active_tasks.tpl')
        ));
        //debug('1682', $our_team);
    }
}
?>