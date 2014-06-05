<?php
require_once APP_PATH . 'classes/models/message.class.php';

class MainAjaxController extends ApplicationAjaxController
{    

    function MainAjaxController()
    {
        ApplicationAjaxController::ApplicationAjaxController();
    }
    
    /**
     * Get content of chat receivers block // Возвращает контент блока получателей чата
     * url: /service/getchatrecipients
     * 
     * @version 20120728, zharkov
     */
    function getchatrecipients()
    {
        $modelUser = new User();
        
        $this->_assign('users',     $modelUser->GetListForChatSeparated());
        $this->_assign('readonly',  true);
        
        $this->_send_json(array(
            'result'    => 'okay', 
            'content'   => $this->smarty->fetch('templates/html/chat/control_recipients.tpl')
        ));
    }
    
    /**
     * Get content of chat receivers block // Возвращает контент блока получателей чата
     * url: /service/getchatcustomers
     * 
     * @version 20120728, zharkov
     */
    function getchatcustomers()
    {
        $modelUser = new User();
        
        $this->_assign('users',     $modelUser->GetListForChatSeparated());
        $this->_assign('readonly',  true);
        
        $this->_send_json(array(
            'result'    => 'okay', 
            'content'   => $this->smarty->fetch('templates/html/chat/control_customers.tpl')
        ));
    }
    
    /**
     * Get last chat message list // Возвращает список последних сообщений для пользователя
     * url: /service/getchatmessages
     * 
     * @version 20120703, zharkov  этот метод должен быть тут, а не в chat_main.class.php потому что выполняется автоматически
     */
    function getchatmessages()
    {                
        $is_chat_page   = Request::GetBoolean('chat_updater', $_REQUEST);    // если флаг установлен, то пользователь находится на странице чата
        $message_id     = Request::GetInteger('message_id', $_REQUEST);
        $object_alias   = Request::GetString('object_alias', $_REQUEST);
        $object_id      = Request::GetInteger('object_id', $_REQUEST);
        
        $messages       = new Message();
        
        if (!empty($object_alias) && $object_id > 0)
        {
            $rowset = $messages->GetListForObject($object_alias, $object_id, $message_id);
            $rowset = $rowset['data'];
        }
        else
        {
            $rowset = $messages->GetList($message_id);
        }
        

        $alert          = '';
        $my_count       = 0;
        $last_id        = 0;
        $list           = array();
        $user_status    = array();
        $update_icons   = false;    // flag upating receivers list // флаг обновления списка получателей

        foreach($rowset as $row)        
        {
            $message = $row['message'];
            
            if ($is_chat_page)
            {                
                if (in_array($message['type_id'], array(MESSAGE_TYPE_LOGIN, MESSAGE_TYPE_ONLINE, MESSAGE_TYPE_LOGOUT, MESSAGE_TYPE_AWAY)) && !isset($user_status[$message['sender_id']]))
                {
                    $user_status[$message['sender_id']] = ($message['type_id'] == MESSAGE_TYPE_LOGIN || $message['type_id'] == MESSAGE_TYPE_ONLINE ? 'online' : ($message['type_id'] == MESSAGE_TYPE_LOGOUT ? 'offline' : 'away'));
                }
                
                // remove from messages list "gone away", "back online" // из ленты исключаются сообщения "gone away", "back online"
                if (!in_array($message['type_id'], array(MESSAGE_TYPE_ONLINE, MESSAGE_TYPE_AWAY)))
                {
                    if ($object_alias == 'chat')
                    {
                        $this->_assign('message', $row);
                        $list[] = $this->smarty->fetch('templates/html/chat/control_chat_message.tpl');                                            
                    }
                    else
                    {
                        $this->_assign('row', $row);
                        $list[] = $this->smarty->fetch('templates/html/blog/control_blog_message.tpl');                        
                    }
                }
            }

            // redraw receivers list on login or logout // при логине и логоуте перерисовывает список получателей
            if (in_array($message['type_id'], array(MESSAGE_TYPE_LOGIN, MESSAGE_TYPE_LOGOUT)))
            {
                $update_icons = true;
            }
            
            // current user messages are not handled // свои сообщения не обрабатываются
            if ($message['sender_id'] == $this->user_id) continue;

            // select count message for current user
            $is_recipient = false;
            if (isset($message['recipient']))
            {
                foreach ($message['recipient'] as $recipient)
                {
                    if ($recipient['user_id'] == $this->user_id)
                    {
                        $is_recipient   = true;
                        $my_count       += 1;
                    }
                }                
            }
            
            if (!$is_recipient && isset($message['cc']))
            {
                foreach ($message['cc'] as $recipient)
                {
                    if ($recipient['user_id'] == $this->user_id) $my_count += 1;
                }                
            }
            
                        
            // alerts only for unreaded messages // алерты только для неполученных сообщений            
            if ($message['is_alert'] > 0 && (!isset($message['userdata']) || empty($message['userdata']['delivered_at'])))
            {
                if ($message['type_id'] == MESSAGE_TYPE_ORDER)
                {
                    $alert = 'alert_order';
                }
                else if ($message['type_id'] == MESSAGE_TYPE_ENQUIRY)
                {
                    $alert = 'alert_enquiry';
                }
                else if ($message['type_id'] == MESSAGE_TYPE_LOGIN)
                {
                    $alert = 'alert_enter';
                }
                else if ($message['type_id'] == MESSAGE_TYPE_BIRTHDAY)
                {
                    $alert = 'alert_birthday';
                }
                else if ($is_recipient)
                {
                    $alert = 'houston';
                }
            }                            
        }
        
        $result = array(
            'result'        => 'okay',
            'alert'         => $alert,
            'mycount'       => $my_count,
            'message_id'    => (isset($rowset[0]) && isset($rowset[0]['message']) && isset($rowset[0]['message']['id']) ? $rowset[0]['message']['id'] : 0)
        );

        if ($update_icons)
        {
            $result['update_icons'] = true;
        }
        
        if ($is_chat_page) 
        {
            $result['messages'] = array_reverse($list);
            $result['statuses'] = $user_status;
        }

        $this->_send_json($result);
    }    
    
    /**
     * Mark message as read for current user // Помечает сообщения как проигранные для пользователя
     * url: /service/chatmessagedelivered
     * 
     * @version 20120704, zharkov this function MUST be placed here and not in chat_main.class.php because of automatic call // этот метод должен быть тут, а не в chat_main.class.php потому что выполняется автоматически
     */
    function chatmessagedelivered()
    {
        $message_id = Request::GetInteger('message_id', $_REQUEST);
        
        $messages   = new Message();
        $rowset     = $messages->MassDelivered($message_id);
    }
    
    /**
     * Pinger
     * url: /service/pinger
     * 
     * @version 20120704, zharkov
     */
    function pinger()
    {
        // store datetime of last ping from user browser // отмечает дату последнего пинга от браузера пользователя
        if (!empty($this->user_id)) Cache::SetKey('online-' . $this->user_id, time());

        // check for new messages in chat // проверяет появились ли новые сообщения в чате
        $chat_object_alias  = Request::GetString('chat_object_alias', $_REQUEST);
        $chat_object_id     = Request::GetInteger('chat_object_id', $_REQUEST);        
        $last_message_id    = Request::GetInteger('last_message_id', $_REQUEST);        
        $result             = array();

        $messages               = new Message();
        $last_chat_message_id   = $messages->GetChatLastMessageId($chat_object_alias, $chat_object_id);

        if ($last_chat_message_id > $last_message_id) 
        {
            $result['new_messages'] = true;
        }
                
        $this->_send_json($result);
    }
    
    function savesettings()
    {
        $module = Request::GetString('set_module', $_REQUEST);
        $action = Request::GetString('set_action', $_REQUEST);
        $value = Request::GetString('set_value', $_REQUEST);
        
        $_SESSION['user_settings'][$module][$action]=$value;
        
        $this->_send_json(array('result'=>'okey', 'value' => $_SESSION));
    }
}
