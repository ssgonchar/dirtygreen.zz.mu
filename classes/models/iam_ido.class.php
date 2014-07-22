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
     * @param $task_id int
     * @return $current_task array
     */
    function GetById($task_id)
    {
        $rowset = $this->CallStoredProcedure('sp_organizer_get_task_by_id', array($this->user_id, $task_id));
        $current_task = array();
        foreach ($rowset['0'] as $row) { $current_task[] = $row; }
                
        $task_info = $this->CallStoredProcedure('sp_organizer_get_task', array($this->user_id, $task_id));
        $current_task['0']['task'] = $task_info['0']['0'];
        return $current_task;
    }
    
    /*
     * Возвращает список наименований для страницы index по данному юзеру
     * 
     * Сначала выбирает задания из табл. organizer_users по данному юзеру (@param $user_id)
     * Для найденых заданий берет информацию из табл. organizer (@param $task_id)
     * 
     */
    function GetList()
    {
        //$hash       = 'organizer';	//ключ кеша = 'nomenclature'
        //$cache_tags = array($hash);		//теги ключа кеша (что это?)
	
        //$rowset = $this->_get_cached_data($hash, 'sp_organizer_get_list', array($this->user_id), $cache_tags);
        $rowset = $this->CallStoredProcedure('sp_organizer_get_list', array($this->user_id));
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
            $task_info = $this->CallStoredProcedure('sp_organizer_get_task', array($this->user_id, $task_id));
            $task_list[$key]['task'] = $task_info['0']['0'];
        }
        //debug('1682', $task_list);
        return $task_list;
    }
    
    /*
     * Сохраняет задание
     */
    function Save($task_id, $status_id, $start_data, $finish_data, $title, $biz_id, $description, $personal_notes)
    {
        $result = $this->CallStoredProcedure('sp_organizer_save', array($this->user_id, $task_id, $status_id, $start_data, $finish_data, $title, $biz_id, $description, $personal_notes));
        
        $result = isset($result) && isset($result[0]) && isset($result[0][0]) ? $result[0][0] : null;
        
        if (empty($result) || array_key_exists('ErrorCode', $result)) return null;

        Cache::ClearTag('organizer-' . $result['id']);
        Cache::ClearTag('organizer');
        Cache::ClearTag('organizer_users');
        return $result;
    }
    
    /*
     * Удаляет выбраное задание
     */
    function Delete($task_id)
    {
        $result = $this->CallStoredProcedure('sp_organizer_delete', array($this->user_id, $task_id));
        
        Cache::ClearTag('organizer-' . $result['id']);
        Cache::ClearTag('organizer');
        $result = isset($result) && isset($result[0]) && isset($result[0][0]) ? $result[0][0] : null;
        return $result;
    }
    
}
