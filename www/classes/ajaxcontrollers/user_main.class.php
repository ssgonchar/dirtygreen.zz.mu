<?php
require_once APP_PATH . 'classes/models/user.class.php';

class MainAjaxController extends ApplicationAjaxController
{    

    function MainAjaxController()
    {
        ApplicationAjaxController::ApplicationAjaxController();
                
        $this->authorize_before_exec['getlistbytitle']  = ROLE_STAFF;
        $this->authorize_before_exec['setstatus']  		= ROLE_STAFF;
    }
        
    /**
     * Change current user status // Изменяет статус текущего пользователя
     * url: /user/setstatus
     * 
     * @version 20121013, zharkov
     */
    function setstatus()
    {
        $status = Request::GetString('status', $_REQUEST);
        
        $modelUser = new User();
        if ($status == 'away')
        {
            $modelUser->SetStatusAway($this->user_id);
        }
        else if ($status == 'online')
        {
            $modelUser->SetStatusOnline($this->user_id);
        }
        
        $this->_send_json(array('result' => 'okay'));
    }    
    
    /**
     * Get users list by login // Возвращает список пользователей по логину
     * url: /user/getlistbytitle
     * 
     * @version 20120711, zharkov
     */
    function getlistbytitle()
    {
        $rows_count = Request::GetInteger('maxrows', $_REQUEST);
        $login      = Request::GetString('login', $_REQUEST);
        //$date_from      = Request::GetDateForDB('date_from', $_REQUEST);
        //$date_to      = Request::GetDateForDB('date_to', $_REQUEST);
        //print_r($date_from);
        $users = new User();
       // $this->_send_json(array('result' => 'okay', 'list' => $users->GetListByLoginAndDate($login, $date_from, $date_to, $rows_count)));
        $data_set = $users->GetListByLogin($login, $rows_count);
        //print_r('1');
        
         $this->_send_json(
         
                    array(
                        'result' => 'okay',
                        'list' => $data_set,
                        )
                );
         
         
    }
}
