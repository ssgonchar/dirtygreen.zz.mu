<?php
//require_once APP_PATH . 'classes/models/user.class.php';


define ('TASK_STATUS_WAITING',      1);
define ('ITEM_STATUS_IN_PROCESS',   2);
define ('ITEM_STATUS_COMPLETED',    3);

class IamIdo extends Model
{
    function IamIdo()
    {
        Model::Model('iamido');
    }
    
    function calculate_time_difference($timestampl, $timestamp2, $time_unit)  { 
        // Определяем разницу между двумя датами 
        $timestampl = intval($timestampl); 
        $timestamp2 = intval($timestamp2); 
        if ($timestampl && $timestamp2)  {
            $time_lapse = $timestamp2 - $timestampl;

            $seconds_in_unit = array(
            'second' => 1,
            'minute' => 60,
            'hour' => 3600,
            'day' => 86400,
            'week' => 604800,
            );

            if ($seconds_in_unit[$time_unit])  {
                return floor($time_lapse/$seconds_in_unit[$time_unit]);
            }
        }
        return false; 
    }
    
    /*
     * Возвращает данные о задании по id
     * Если искомого задания не существует - возвращает значение error
     * @param $task_id int
     * @return $current_task array 
     */
    function GetById($task_id)
    {
        $result = $this->CallStoredProcedure('sp_organizer_get_task_by_id', array($this->user_id, $task_id));
        
        $current_task = isset($result) && isset($result[0]) && isset($result[0][0]) ? $result[0][0] : null;
        //если искомого задания не существует - возвращаю ошибку
        if (array_key_exists('ErrorCode', $current_task)){
            $current_task = 'error';
            return $current_task;
        }
        $result = $this->CallStoredProcedure('sp_organizer_get_task', array($this->user_id, $task_id));
        $current_task['task'] = isset($result) && isset($result[0]) && isset($result[0][0]) ? $result[0][0] : null;
        //получаю инфо о BIZ
        $modelBiz = new Biz();
        $biz_id = $current_task['task']['biz_id'];
        $biz_info = $modelBiz->GetById($biz_id);
        $biz_title = $biz_info['biz']['doc_no'];
        //записываю biz_title в инфо о задании
        $current_task['task']['biz_title'] = $biz_title;
        return $current_task;
    }
    
    /*
     * Возвращает список наименований для страницы index по данному юзеру
     * 
     * Сначала выбирает задания из табл. organizer_users по данному юзеру (@param $user_id)
     * Для найденых заданий берет информацию из табл. organizer (@param $task_id)
     * 
     */
    function GetList($user_id)
    {
        $rowset = $this->CallStoredProcedure('sp_organizer_get_list', array($user_id));
        //debug('1682', $rowset);
        $task_list = array();
        foreach ($rowset['0'] as $row) { $task_list[] = $row; }
        
        foreach ($task_list as $key => $row) {
            //получаю даты старта и финиша в формате Timestamp и высчитываю запланированное время
            $date1 = new DateTime($row['data_start']);
            $date2 = new DateTime($row['data_finish']);
            $data_start = $date1->getTimestamp();
            $data_finish = $date2->getTimestamp();
            $time_unit = 'minute';
            $budget_time = $this->calculate_time_difference($data_start, $data_finish, $time_unit);
            $task_list[$key]['budget_time'] = $budget_time;
            //удаляю в датах старта и финиша секунды
            $start = substr($row['data_start'], 0, -3);
            $task_list[$key]['data_start'] = $start;
            $finish = substr($row['data_finish'], 0, -3);
            $task_list[$key]['data_finish'] = $finish;
            //заполняю информацию по заданию
            $task_id = $row['task_id'];
            $task_info = $this->CallStoredProcedure('sp_organizer_get_task', array($user_id, $task_id));
            $task_list[$key]['task'] = $task_info['0']['0'];
        }
        return $task_list;
    }
    
    /*
     * Сохраняет задание
     */
    function Save($task_id, $status_id, $start_data, $finish_data, $title, $biz_id, $description, $personal_notes)
    {
        $result = $this->CallStoredProcedure('sp_organizer_save', array($this->user_id, $task_id, $status_id, $start_data, $finish_data, $title, $biz_id, $description, $personal_notes));
        //debug('1682', $start_data);
        $result = isset($result) && isset($result[0]) && isset($result[0][0]) ? $result[0][0] : null;
        
        if (empty($result) || array_key_exists('ErrorCode', $result)) return null;
        //рассчитываю запланированное время
        //получаю даты старта и финиша в формате Timestamp и высчитываю запланированное время
        $date1 = new DateTime($start_data);
        $date2 = new DateTime($finish_data);
        $data_start = $date1->getTimestamp();
        $data_finish = $date2->getTimestamp();
        $time_unit = 'minute';
        //получаю время, которое надо добавить к заданию в минутах
        $time_difference = $this->calculate_time_difference($data_start, $data_finish, $time_unit);
        //debug('1682', $time_difference);
        $result['budget_time'] = $time_difference;
        return $result;
    }
    
    /**
     * Связывает сообщение с объектами
     * 
     * @param mixed $task_id
     * @param mixed $user_id
     * @param mixed $objects
     */
    function SaveObjects($task_id, $objects)
    {
        foreach ($objects as $object) 
        {
            $this->SaveObject($task_id, $object['alias'], $object['id']);
        }
    }     
    /**
     * Привязывает объект к сообщению
     * 
     * @param mixed $task_id
     * @param mixed $user_id
     * @param mixed $object_alias
     * @param mixed $object_id
     * 
     * @version 20120628, zharkov
     */
    function SaveObject($task_id, $object_alias, $object_id)
    {
        $result = $this->CallStoredProcedure('sp_organizer_save_object', array($this->user_id, $task_id, $object_alias, $object_id));
        //debug('1682', $result);
        
        if (isset($result) && array_key_exists('ErrorCode', $result)) return null;
        return $result;
    }   
    
    /*
     * Удаляет выбраное задание
     */
    function Delete($task_id)
    {
        $result = $this->CallStoredProcedure('sp_organizer_delete', array($this->user_id, $task_id));
        $result = isset($result) && isset($result[0]) && isset($result[0][0]) ? $result[0][0] : null;
        return $result;
    }
    
    /*
     * 
     */
    function CalcCurrentUsedTime($start_data, $finish_data, $used_time)
    {
        //получаю даты старта и финиша в формате Timestamp и высчитываю запланированное время
        $date1 = new DateTime($start_data);
        $date2 = new DateTime($finish_data);
        $data_start = $date1->getTimestamp();
        $data_finish = $date2->getTimestamp();
        $time_unit = 'second';
        //получаю время, которое надо добавить к заданию в секундах
        $time_difference = $this->calculate_time_difference($data_start, $data_finish, $time_unit);
        //если дата старта больше даты финиша, то разница времени равна 0
        if($data_start > $data_finish){
            $time_difference = 0;
        }
        //прибавляю разницу к потраченому времени на данный момент - получаю текущее затраченное время в минутах
        $current_used_time = $used_time + $time_difference;
        return $current_used_time;
    }
    
    /*
     * Сохраняет использованное время
     */
    function SaveUsedTime($task_id, $current_used_time)
    {
        //debug('1682', $current_used_time);
        //проблема в том что ф-я не перезаписывает а прибавляет
        $result = $this->CallStoredProcedure('sp_organizer_update_used_time', array($this->user_id, $task_id, $current_used_time));
        $updated_task = isset($result) && isset($result[0]) && isset($result[0][0]) ? $result[0][0] : null;
        //echo 'boom';
        //debug('1682', $updated_task);
        return $updated_task;
    }
    
    /*
     * 
     */
    function ChangeStatus($task_id, $status_id, $browzer_info)
    {
        $result = $this->CallStoredProcedure('sp_organizer_change_status', array($this->user_id, $task_id, $status_id, $browzer_info));
        $updated_task = isset($result) && isset($result[0]) && isset($result[0][0]) ? $result[0][0] : null;
        //debug('1682', $updated_task);
        return $updated_task;
    }
    
    /* 
     * Возвращает задания с включенным счетчиком (status_id = 4) для пользователя
     */
    function GetActiveTask($user_id, $status_id)
    {
        $result = $this->CallStoredProcedure('sp_organizer_get_auto_count_task', array($user_id, $status_id));
        $auto_count_task = isset($result) && isset($result[0]) && isset($result[0][0]) ? $result[0][0] : null;
        /*if(!empty($auto_count_task)){
            //заполняю информацию по заданию
            $task_id = $auto_count_task['task_id'];
            $result = $this->CallStoredProcedure('sp_organizer_get_task', array($this->user_id, $task_id));
            $task_info = isset($result) && isset($result[0]) && isset($result[0][0]) ? $result[0][0] : null;
            $auto_count_task['task'] = $task_info;
        }*/
            //debug('1682', $auto_count_task);
        return $auto_count_task; 
    }
}
