<?php
require_once APP_PATH . 'classes/models/message.class.php';

define('ACTIVEUSER_OFFLINE_LIMIT', 600);   // 10 min
define('ACTIVEUSER_AWAY_LIMIT',    1440);   // 24 min

class ActiveUser extends Model
{
    function ActiveUser()
    {
        Model::Model('active_users');
    }
   
    /**
     * Устанавливает последнее полученное сообщение для пользователя
     * 
     * @param mixed $message_id
     * 
     * @version 20120705, zharkov
     */
    function SetLastMessage($message_id)
    {
        Cache::SetKey('lastmessage-' . $this->user_id, $message_id);
        Cache::ClearTag('activeuser-' . $this->user_id);
        
        $result = $this->UpdateList(array(
            'values' => array(
                'last_message_id' => $message_id
            ),
            'where' => array(
                'conditions'    => 'user_id = ?',
                'arguments'     => $this->user_id
            )
        ));
    }    
    
    /**
     * Возвращает последнее сообщение полученное текущим пользователем
     * 
     * @param mixed $user_id
     * 
     * @version 20120705, zharkov
     */
    function GetLastMessage()
    {
        if (!($message_id = Cache::GetKey('lastmessage-' . $this->user_id)))
        {
            $rowset     = $this->CallStoredProcedure('sp_active_user_get_last_message', array($this->user_id));
            $message_id = isset($rowset[0]) && isset($rowset[0][0]) ? $rowset[0][0]['last_message_id'] : 0;

            Cache::SetKey('lastmessage-' . $this->user_id, $message_id);
        }
        
        return $message_id;        
    }
    
    /**
    * Обновляет дату последнего пинга браузера пользователя
    * 
    * @param mixed $user_id
    * @param mixed $timestamp
    * 
    * @version 20120705, zharkov
    */
    function UpdateOnlineAt($user_id, $timestamp)
    {
        Cache::ClearTag('activeusers');        
        
        $result = $this->UpdateList(array(
            'values' => array(
                'online_at' => $timestamp
            ),
            'where' => array(
                'conditions'    => 'user_id = ?',
                'arguments'     => $user_id
            )
        ));
    }    
    
    /**
     * Обновляет последнее сообщение полученное активным пользователем
     * 
     * @param mixed $user_id
     * @param mixed $message_id
     * 
     * @version 20120705, zharkov
     */
    function UpdateLastMessage($user_id, $message_id)
    {
        Cache::ClearTag('activeusers');        
        
        $result = $this->UpdateList(array(
            'values' => array(
                'last_message_id' => $message_id,
            ),
            'where' => array(
                'conditions'    => 'user_id = ?',
                'arguments'     => $user_id
            )
        ));
    }
    
    /**
     * Удаляет пользователя из списка активных
     * 
     * @param mixed $user_id
     * @return resource
     * 
     * @version 20120705, zharkov
     */
    function Remove($user_id)
    {
        Cache::ClearTag('activeusers');
        Cache::ClearKey('online-' . $this->user_id);
        Cache::ClearKey('activeuser-' . $this->user_id);

        $this->DeleteList(array(
            'where' => array(
                'conditions'    => 'user_id = ?', 
                'arguments'     => array($user_id)
            )
        ));        
        
        // сообщение в чат
        $messages = new Message();
        $messages->AlertLogout($user_id);
    }  

    /**
     * Проверяет есть ли пользователь в списке активных, если нет - добавляет
     * 
     * @param mixed $user_id
     * @return resource
     * 
     * @version 20120704, zharkov
     */
    function Add($user, $ip)
    {
        $user_id = $user['id'];
        
        Cache::ClearTag('activeusers');
        
        Cache::SetKey('activeuser-' . $user_id, time());
        Cache::SetKey('online-' . $user_id,     time());
        
        $result = $this->CallStoredProcedure('sp_active_user_add', array($user_id, time(), time()));
        $result = isset($result) && isset($result[0]) && isset($result[0][0]) ? Request::GetBoolean('already_active', $result[0][0]) : true;
        
        // если пользватель только что вошел, отправляется сообщение в чат
        if ($result)
        {
            $messages = new Message();
            $messages->AlertLogin($user, $ip);
        }
    }
    
    /**
     * Возвращает список активных пользователей
     * 
     * @version 20120705, zharkov
     */
    function GetList()
    {
        $hash       = 'activeusers';
        $cache_tags = array($hash, 'users');

        $rowset = $this->_get_cached_data($hash, 'sp_active_user_get_list', array(), $cache_tags);
        return isset($rowset[0]) ? $rowset[0] : array();
    }

}