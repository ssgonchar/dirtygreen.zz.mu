<?php
require_once APP_PATH . 'classes/common/parser.class.php';
require_once APP_PATH . 'classes/components/object.class.php';

/**
 * Модель управления таблицами модуля Message
 * @version 20120620, zharkov
 */
 
define('MESSAGE_TYPE_NORMAL',       0);
define('MESSAGE_TYPE_PRIVATE',      1);
define('MESSAGE_TYPE_PERSONAL',     2);
define('MESSAGE_TYPE_SERVICE',      3);
define('MESSAGE_TYPE_LOGIN',        4);
define('MESSAGE_TYPE_LOGOUT',       5);
define('MESSAGE_TYPE_AWAY',         6);
define('MESSAGE_TYPE_ONLINE',       7);
define('MESSAGE_TYPE_ORDER',        8);
define('MESSAGE_TYPE_ENQUIRY',      9);
define('MESSAGE_TYPE_BIRTHDAY',     10);
define('MESSAGE_TYPE_LOGIN_FAILED', 11);

define('GNOME_USER',    1);
define('MAM_USER',      3);
 
//require_once APP_PATH . 'classes/models/user.class.php';

class MainChatMessage extends Model
{
    function MainChatMessage()
    {
        Model::Model('mainchatmessages');
    }

    /**
     * Возвращает количество сообщений для объекта
     * 
     * @param mixed $object_alias
     * @param mixed $object_id
     */
    function GetCountByObject($object_alias, $object_id)
    {
        $hash       = 'mainchatmessages-obj-' . $object_alias . '-objid-' . $object_id . '-count';
       // $object_id = 'zzzzzz';
		$cache_tags = array('mainchatmessages', 'mainchatmessages-obj-' . $object_alias . '-objid-' . $object_id);

        $rowset = $this->_get_cached_data($hash, 'sp_message_get_count_by_object', array($this->user_id, $this->user_role, $object_alias, $object_id), $cache_tags);
        return isset($rowset) && isset($rowset[0]) && isset($rowset[0][0]) && isset($rowset[0][0]['count']) ? $rowset[0][0]['count'] : 0;
    }

    /**
     * Добавляет сообщение о неудавшемся входе на сайт
     * 
     * @param mixed $login
     * @param mixed $password
     * @param mixed $ip
     * @param mixed $alert
     */
    function AlertLoginFailed($login, $password, $ip, $message, $alert = 0)
    {
        $title  = 'MaM Village authentication failed (' . $message . ') !';
        $text   = '<b>Login</b> : ' . $login . 
                    '<br><b>Password</b> : ' . $password . 
                    '<br><b>IP</b> : ' . $ip;
        
        $this->Add(MESSAGE_TYPE_LOGIN_FAILED, ROLE_STAFF, GNOME_USER, MAM_USER, '', $title, $text, 0, '', $alert, 0);
    }

    /**
     * Добавляет сообщение о возвращении пользователя в онлайн
     * 
     * @param mixed $sender_id
     * @param mixed $domain
     * 
     * @version 20120716, zharkov
     */
    function AlertOnline($sender_id, $text = "I am online .", $alert = 0)
    {
        $this->Add(MESSAGE_TYPE_ONLINE, ROLE_STAFF, $sender_id, MAM_USER, '', $text, '', 0, '', $alert, 0);
    }
    
    /**
     * Осуществляет поиск сообщений 
     * 
     * @param mixed $keyword
     * @param mixed $date_from
     * @param mixed $date_to
     * @param mixed $sender_id
     * @param mixed $recipient_id
     * @param mixed $is_dialogue
     * @param mixed $is_mam
     * @param mixed $page_no
     * @param mixed $per_page
     * 
     * @version 20120711, zharkov
     */
    function Search($keyword, $date_from, $date_to, $sender_id, $recipient_id, $is_dialogue, $is_mam, $search_type, $page_no = 0, $per_page = ITEMS_PER_PAGE)
    {
		//костыль, выключает поиск по кэшу если запущенно службой
		if($keyword == 'SERVICE_CRON') {
			$keyword = '';
			$is_service = true;
		} else {
			$is_service = false;
		}
		
        $page_no    = $page_no > 0 ? $page_no : 1;
        $per_page   = $per_page < 1 ? ITEMS_PER_PAGE : $per_page;
        $start      = ($page_no - 1) * $per_page;

        $hash   =   'message-search-' . md5($keyword . '-datefrom-' . $date_from . '-dateto-' . $date_to . 
                    '-sender-' . $sender_id . '-recipient-' . $recipient_id . 
                    '-isdialogue-' . $is_dialogue . '-ismam-' . $is_mam . '-isphrase-' . $search_type . 
                    '-page' . $page_no . '-' . $per_page);

        $rowset = Cache::GetData($hash);
		
		//$rowset = null;   //test mode
		if($is_service) $rowset = null;   //если запущено сервисом - поиск по базе
 
        if (!isset($rowset) || !isset($rowset['data']) || isset($rowset['outdated']))
        {
            $cl = new SphinxClient();
            $cl->SetLimits($start, $per_page, 5000);
            $cl->SetFieldWeights(array(
                'title'         => 1000,
                'description'   => 1000
            ));

			
            if (empty($search_type) || empty($keyword))
            {
                $search_type = 'all';
            }
            
            if ($search_type == 'exact')
            {
                $cl->SetMatchMode(SPH_MATCH_PHRASE);
            }
            else if ($search_type == 'any')
            {
                if (!empty($keyword)) $keyword = '*' . str_replace('-', '\-', str_replace(' ', '* *', $keyword)) . '*';
                $cl->SetMatchMode(SPH_MATCH_ANY);
            }
            else if ($search_type == 'all')
            {
                 if (!empty($keyword)) $keyword = '*' . str_replace('-', '\-', str_replace(' ', '* *', $keyword)) . '*';
                $cl->SetMatchMode(SPH_MATCH_ALL);
            }            
            
            $cl->SetGroupBy('message_id', SPH_GROUPBY_ATTR, 'message_id DESC');
            $cl->SetIndexWeights(array(
                'ix_mam_messages_delta' => 100, 
                'ix_mam_messages'       => 100
            ));

            if (!empty($date_from) && !empty($date_to))
            {
                // при выборе 24-09-2012 буде выбираться с 24-09-2012 00:00:00 по 24-09-2012 23:59:59
                $date_from  = strtotime($date_from);
                $date_to    = strtotime($date_to) + 60 * 60 * 24 - 1;
            }
            else if (!empty($date_from))
            {
                $date_from  = strtotime($date_from);
                $date_to    = PHP_INT_MAX;
            }
            else if (!empty($date_to))
            {
                $date_from  = 0;
                $date_to    = strtotime($date_to);
            }

            if (!empty($date_from) || !empty($date_to))
            {
                $cl->setFilterRange('created_at', $date_from, $date_to);                
            }

            $senders    = array();
            $recipients = array();            
            if ($sender_id > 0) $senders[] = $sender_id;
            if ($recipient_id > 0) $recipients[] = $recipient_id;
            
            $is_monologue = false;    // when "Dialogues" selected and only one of sender/recipient
            if ($is_dialogue > 0) 
            {
                if ($sender_id > 0) $recipients[] = $sender_id;
                if ($recipient_id > 0) $senders[] = $recipient_id;
                
                if (empty($sender_id) || empty($recipient_id))
                {
                    $is_monologue = true;
                }
            }
			//print_r($keyword);
            if ($is_mam > 0) $recipients[] = MAM_USER;

            // фильтр по отправителю и получателю
            $participants_arg = '';
            if (!empty($senders)) $participants_arg = 'IN(sender_id, ' . implode(',', $senders) . ')';
            if (!empty($recipients)) $participants_arg .= (empty($participants_arg) ? '' : ($is_monologue ? ' OR ' : ' AND ')) . 'IN(recipient_id, ' . implode(',', $recipients) . ')';

            // фильтр по роли пользоватлея
            $roles_arg = '';
            if ($this->user_role <= ROLE_ADMIN)
            {
                $cl->SetFilter('role_id', array(ROLE_ADMIN, ROLE_SUPER_MODERATOR, ROLE_MODERATOR, ROLE_SUPER_STAFF, ROLE_STAFF, ROLE_SUPER_USER, ROLE_USER, ROLE_LIMITED_USER, ROLE_GUEST));
            }
            else if ($this->user_role <= ROLE_STAFF)
            {
                $cl->SetFilter('role_id', array(ROLE_STAFF, ROLE_SUPER_USER, ROLE_USER, ROLE_LIMITED_USER, ROLE_GUEST));
                $roles_arg = 'IF(type_id <> 1, 1, 0) OR sender_id = ' . $this->user_id . ' OR recipient_id = ' . $this->user_id;
            }
            else
            {
                $cl->SetFilter('role_id', array(ROLE_SUPER_USER, ROLE_USER, ROLE_LIMITED_USER, ROLE_GUEST));
                $roles_arg = 'sender_id = ' . $this->user_id . ' OR recipient_id = ' . $this->user_id;
            }

            $select_string = '*';
            if (!empty($participants_arg))
            {
                $select_string .= ', (' . $participants_arg . ') AS participants_arg';
                $cl->SetFilter('participants_arg', array(1));
            }

            if (!empty($roles_arg))
            {
                $select_string .= ', (' . $roles_arg . ') AS roles_arg';
                $cl->SetFilter('roles_arg', array(1));
            }
            
            $cl->SetFilter('type_id', array(
                MESSAGE_TYPE_NORMAL,
                MESSAGE_TYPE_PRIVATE,
                MESSAGE_TYPE_PERSONAL,
                MESSAGE_TYPE_SERVICE,
                MESSAGE_TYPE_LOGIN,
                MESSAGE_TYPE_LOGOUT,
                // MESSAGE_TYPE_AWAY,
                // MESSAGE_TYPE_ONLINE,
                // MESSAGE_TYPE_LOGIN_FAILED,                
                // MESSAGE_TYPE_BIRTHDAY,
                MESSAGE_TYPE_ORDER,
                MESSAGE_TYPE_ENQUIRY
            ));
                            
            $cl->SetSelect($select_string);

            $data = $cl->Query($keyword, 'ix_mam_messages, ix_mam_messages_delta');
//dg($cl);
            if ($data === false)
            {
                Log::AddLine(LOG_ERROR, 'MainChatMessage::search ' . $cl->GetLastError());
                return null;
            }

            $rowset = array(); 
            if (!empty($data['matches']))
            {
                foreach ($data['matches'] as $id => $extra)
                {
                    $rowset[] = array('message_id' => $extra['attrs']['message_id']);
                }
            }

            $rowset = array(
                $rowset,
                array(array('rows' => $data['total_found']))
            );
            
            Cache::SetData($hash, $rowset, array('messages', 'search'), CACHE_LIFETIME_STANDARD);
            
            $rowset = array(
                'data' => $rowset
            );
        }

        return array(
            'data'  => isset($rowset['data'][0]) ? $this->FillMessageInfo($rowset['data'][0]) : array(),
            'count' => isset($rowset['data'][1]) && isset($rowset['data'][1][0]) && isset($rowset['data'][1][0]['rows']) ? $rowset['data'][1][0]['rows'] : 0
        );        
    }
    
    /**
     * Возвращает список пендингов текущего пользователя
     * 
     * @param mixed $page_no
     * @param mixed $per_page
     * @return mixed
     * 
     * @version 20120711, zharkov
     */
    function GetPendings($page_no = 0, $per_page = ITEMS_PER_PAGE)
    {
        $page_no    = $page_no > 0 ? $page_no : 1;
        $per_page   = $per_page < 1 ? ITEMS_PER_PAGE : $per_page;
        $start      = ($page_no - 1) * $per_page;        
        
        $hash       = 'message-pendings-' . $this->user_id . '-page-' . $page_no . '-perpage-' . $per_page;
        $cache_tags = array($hash, 'message-pendings-' . $this->user_id, 'messages');

        $rowset     = $this->_get_cached_data($hash, 'sp_message_get_pendings', array($this->user_id, $start, $per_page), $cache_tags);
        
        return array(
            'data'  => isset($rowset[0]) ? $this->FillMessageInfo($rowset[0]) : array(),
            'count' => isset($rowset[1]) && isset($rowset[1][0]) && isset($rowset[1][0]['count']) ? $rowset[1][0]['count'] : 0
        );
    }
      
    /**
     * Возвращает список сообщений для текущего пользователя
     * 
     * @param mixed $object_alias
     * @param mixed $object_id
     * @param mixed $message_id
     * @return mixed
     * 
     * @version 20120706, zharkov
     */
    function GetList($message_id = 0)
    {
        $rowset = $this->CallStoredProcedure('sp_message_get_list', array($this->user_id, $this->user_role, $message_id));        
        return isset($rowset[0]) ? $this->FillMessageInfo($rowset[0]) : array();
    }
    
    /**
     * Возвращает список сообщений чата для выбранного объекта
     * 
     * @param mixed $object_alias
     * @param mixed $object_id
     * @param mixed $message_id
     * @param mixed $page_no
     * @param mixed $per_page
     * @return mixed
     */
    function GetListForObject($object_alias, $object_id, $message_id = 0, $page_no = 0, $per_page = ITEMS_PER_PAGE)
    {
        $page_no    = $page_no > 0 ? $page_no : 1;
        $per_page   = $per_page < 1 ? ITEMS_PER_PAGE : $per_page;
        $start      = ($page_no - 1) * $per_page;
        
        $hash       = 'messages-' . md5('user_id-' . $this->user_id . '-user_role-' . $this->user_role . 
                                        'obj-' . $object_alias . '-objid-' . $object_id . 
                                        '-message_id-' . $message_id . '-pageno-' . $page_no . '-perpage-' . $per_page);
        
        $cache_tags = array($hash, 'messages-obj-' . $object_alias . '-objid-' . $object_id, 'messages');
        $rowset     = $this->_get_cached_data($hash, 'sp_message_get_list_for_object', array($this->user_id, $this->user_role, $object_alias, $object_id, $message_id, $start, $per_page), $cache_tags);
        
        return array(
            'data'  => isset($rowset[0]) ? $this->FillMessageInfo($rowset[0]) : array(),
            'count' => isset($rowset[1]) && isset($rowset[1][0]) && isset($rowset[1][0]['count']) ? $rowset[1][0]['count'] : 0
        );
    }
    
    /**
     * Обновляет последнее сообщение чата
     * 
     * @param mixed $message_id
     * 
     * @version 20120704, zharkov
     * @version 20120729, zharkov : изменил на использование тегов
     */
    function SetChatLastMessageId($message_id, $chat_object_alias = '', $chat_object_id = 0)
    {
        if (!empty($chat_object_alias) && !empty($chat_object_id))
        {           
            $tag = 'chat-last-message-obj-' . $chat_object_alias . '-objid-' . $chat_object_id;
        }
        else
        {
            $tag = 'chat-last-message';
        }
        
        Cache::ClearTag($tag);

        
/*  20120729, zharkov : OLD VERSION
        $cache_key = (!empty($chat_object_alias) && !empty($chat_object_id) ? 'chat-lastmessage-obj-' . $chat_object_alias . '-objid-' . $chat_object_id : 'chat-lastmessage');
        Cache::SetKey($cache_key, $message_id);
*/        
    }
    
    /**
     * Возвращает последнее полученное сообщение для пользователя
     * 
     * @version 20120704, zharkov
     * @version 20120729, zharkov : изменил на использование GetData вместо GetKey
     */
    function GetChatLastMessageId($chat_object_alias, $chat_object_id)
    {
        if (!empty($chat_object_alias) && !empty($chat_object_id))
        {
            $hash   = 'chat-last-message-user-' . $this->user_id . '-role-' . $this->user_role . '-obj-' . $chat_object_alias . '-objid-' . $chat_object_id;
            $tags   = array('chat-last-message-obj-' . $chat_object_alias . '-objid-' . $chat_object_id);
        }
        else
        {
            $hash   = 'chat-last-message-user-' . $this->user_id . '-role-' . $this->user_role;            
            $tags   = array('chat-last-message');
        }

        $data = Cache::GetData($hash);

        if (!isset($data) || !isset($data['data']) || isset($data['outdated']))
        {
            $rowset     = $this->CallStoredProcedure('sp_message_get_last_id', array($this->user_id, $this->user_role, $chat_object_alias, $chat_object_id));
            $message_id = isset($rowset[0]) && isset($rowset[0][0]) ? $rowset[0][0]['id'] : 0;
            
            Cache::SetData($hash, $message_id, $tags);
        }
        else
        {
            $message_id = $data['data'];
        }
        
        return $message_id;
        
/*      20120729, zharkov : OLD VERSION WITH KEY        
        $cache_key = (!empty($chat_object_alias) && !empty($chat_object_id) ? 'chat-lastmessage-obj-' . $chat_object_alias . '-objid-' . $chat_object_id : 'chat-lastmessage');
        
        if (!($message_id = Cache::GetKey($cache_key)))
        {
            $rowset     = $this->CallStoredProcedure('sp_message_get_last_id', array($this->user_id, $this->user_role, $chat_object_alias, $chat_object_id));
            $message_id = isset($rowset[0]) && isset($rowset[0][0]) ? $rowset[0][0]['id'] : 0;
            
            Cache::SetKey($cache_key, $message_id);
        }
        
        return $message_id;
*/        
    }
        
    /**
     * Помечает сообщения как доставленные
     * 
     * @param mixed $message_id
     * 
     * @version 20120704, zharkov
     */
    function MassDelivered($message_id)
    {
        $result = $this->CallStoredProcedure('sp_message_mass_delivered', array($this->user_id, $message_id));
        $result = isset($result[0]) ? $result[0] : array();
        
        foreach($result as $row)
        {
            Cache::ClearTag('message-' . $row['id']);
            Cache::ClearTag('messageuserdata-' . $this->user_id . '-' . $row['id']);
        }
    }
 
    /**
     * Добавляет сервисное сообщение
     * 
     * @param mixed $snder_id
     * @param mixed $text
     * @param mixed $sound
     * @param mixed $pending
     * 
     * @version 20120703, zharkov
     */
    function Service($sender_id, $text, $alert = 0, $pending = 0)
    {
        $result = $this->Add(MESSAGE_TYPE_SERVICE, ROLE_STAFF, $sender_id, MAM_USER, '', $text, '', 0, '', $alert, $pending);
    }
    
    /**
     * Отправляет сервисное сообщение о том что пользователь сменил статус на "Away"
     * 
     * @param mixed $sender_id
     * @param mixed $text
     * @param mixed $alert
     * 
     * @version 20120705, zharkov
     */
    function AlertAway($sender_id, $text = "I away .", $alert = 0)
    {
        $this->Add(MESSAGE_TYPE_AWAY, ROLE_STAFF, $sender_id, MAM_USER, '', $text, '', 0, '', $alert, 0);
    }    
    
    /**
     * Добавляет сообщение о выходе пользователя из системы
     * 
     * @param mixed $sender_id
     * @param mixed $text
     * @param mixed $alert
     * 
     * @version 20120705, zharkov
     */
    function AlertLogout($sender_id, $title = "", $alert = 0)
    {
        $this->Add(MESSAGE_TYPE_LOGOUT, ROLE_STAFF, $sender_id, MAM_USER, '', $title, '', 0, '', $alert, 0);
    }
    
    /**
     * Добавляет сообщение о заходе клиента на сайт
     * 
     * @param mixed $sender_id
     * @param mixed $domain
     * 
     * @version 20120716, zharkov
     */
    function AlertLogin($sender, $ip, $alert = 0)
    {
        $title  = 'MaM Village';
        if ($sender['role_id'] > ROLE_STAFF)
        {
            $text = (isset($sender['person']) ?  '<b>Person</b> : <a href="/person/' . $sender['person']['id'] . '">' . $sender['person']['full_name'] . '</a>' : '') . 
                        (isset($sender['person']['company']) ?  '<br><b>Company</b> : <a href="/company/' . $sender['person']['company']['id'] . '">' . $sender['person']['company']['title'] . '</a>' : '') . 
                        '<br><b>IP</b> : ' . $ip;            
        }
        else
        {
            $text = '';
        }
                        
        $this->Add(MESSAGE_TYPE_LOGIN, ROLE_STAFF, $sender['id'], MAM_USER, '', $title, $text, 0, '', $alert, 0);
    }
    
    /**
     * Помечает сообщение как прочитанное
     * 
     * @param mixed $message_id
     * 
     * @version 20120703, zharkov
     */
    function MarkAsDone($message_id)
    {
        $result = $this->CallStoredProcedure('sp_message_mark_as_done', array($this->user_id, $message_id));
        $result = isset($result) && isset($result[0]) && isset($result[0][0]) ? $result[0][0] : array();
        
        Cache::ClearTag('message-pendings-' . $this->user_id);
        Cache::ClearTag('messageuserdata-' . $this->user_id . '-' . $message_id);
        
//        Cache::ClearTag('message-' . $message_id);
//        Cache::ClearTag('messages');
        
        // отсылает уведомление отправителю сообщения что задание выполнено
        // ...
        
        return $result;        
    }
    
    /**
     * Заполняет данные пользователя
     * 
     * @param mixed $rowset
     * @param mixed $id_fieldname
     * @param mixed $entityname
     * @param mixed $cache_prefix
     * 
     * @version 20120701, zharkov
     */
    function FillUserInfo($rowset, $id_fieldname = 'message_id', $entityname = 'userdata', $cache_prefix = 'messageuserdata')
    {
        $rowset = $this->_fill_entity_info($rowset, $id_fieldname, $entityname, $cache_prefix . '-' . $this->user_id, 'sp_message_get_userdata_by_ids', array('messages' => '', 'message' => 'id'), array($this->user_id));

        foreach ($rowset as $key => $row)
        {
            if (isset($row['message']) && isset($row[$entityname]) && !empty($row[$entityname]))
            {
                $rowset[$key]['message'][$entityname] = $row[$entityname];
                unset($rowset[$key][$entityname]);
            }
        }
        
        return $rowset;
    }
    
    /**
     * Заполняет список получателей сообщения
     * 
     * @param array $rowset
     * @param mixed $id_fieldname
     * @param mixed $entityname
     * @param mixed $cache_prefix
     * @return array
     * 
     * @version 20120630, zharkov
     */
    function FillMessageRecipients($rowset, $id_fieldname = 'message_id', $entityname = 'messagerecipients', $cache_prefix = 'messagerecipients')
    {
        $rowset = $this->_fill_entity_array_info($rowset, $id_fieldname, $entityname, $cache_prefix, 'sp_message_get_recipients_by_ids', array('messages' => '', 'message' => 'id'), array());

        $users  = new User();
        foreach($rowset as $key => $row)
        {
            $is_pending_recipient = false;
            
            if (isset($row[$entityname]) && !empty($row[$entityname]))
            {
                foreach ($users->FillUserInfo($row[$entityname]) as $row1)
                {
                    if ($row1['relation'] == 'r')
                    {
                        $rowset[$key]['message']['recipient'][] = $row1;
                    }
                    else if ($row1['relation'] == 'c')
                    {
                        $rowset[$key]['message']['cc'][] = $row1;
                    }

                    // пендинг получают только прямые получатели сообщения
                    if (($row1['user_id'] == 3 && $this->user_role <= ROLE_STAFF) || $row1['user_id'] == $this->user_id) $is_pending_recipient = true;
                }
                
                unset($rowset[$key][$entityname]);
            }
            
            if ($is_pending_recipient && isset($row['message'])) $rowset[$key]['message']['is_pending_recipient'] = true;
        }

        return $rowset;
    }
    
    /**
     * Заполняет данные сообщения
     * 
     * @param mixed $rowset
     * @param mixed $id_fieldname
     * @param mixed $entityname
     * @param mixed $cache_prefix
     */
    function FillMessageInfo($rowset, $id_fieldname = 'message_id', $entityname = 'message', $cache_prefix = 'message')
    {
        $rowset = $this->_fill_entity_info($rowset, $id_fieldname, $entityname, $cache_prefix, 'sp_message_get_list_by_ids', array('messages' => '', 'message' => 'id'), array());

        foreach ($rowset as $key => $row)
        {
            if (isset($row[$entityname])) $rowset[$key]['sender_id'] = $row[$entityname]['sender_id'];            
        }
        
        $users  = new User();
        $rowset = $users->FillFullUserInfo($rowset, 'sender_id', 'sender');

        foreach ($rowset as $key => $row)
        {
            if (isset($row['sender']))
            {
                $rowset[$key][$entityname]['sender'] = $row['sender'];
                unset($rowset[$key]['sender']);
            }
            unset($rowset[$key]['sender_id']);
        }
        
        $attachments = new Attachment();        
        return $attachments->FillObjectAttachments($this->FillUserInfo($this->FillMessageRecipients($rowset)), 'message', 'message_id');
    }
        
    /**
     * Возвразает сообщение по идентификатору
     * 
     * @param mixed $message_id
     */
    function GetById($message_id)
    {
        $rowset = $this->FillMessageInfo(array(array('message_id' => $message_id)));
        return isset($rowset) && isset($rowset[0]) && isset($rowset[0]['message']) ? $rowset[0] : null;        
    }
    
    /**
     * Добавляет новое сообщение чата
     * 
     * @param mixed $type_id
     * @param mixed $role_id
     * @param mixed $sender_id
     * @param mixed $recipient
     * @param mixed $cc
     * @param mixed $title
     * @param mixed $description
     * @param mixed $parent_id
     * @param mixed $deadline
     */
    function Add($type_id, $role_id, $sender_id, $recipient, $cc, $title_source, $description_source, $parent_id, $deadline, $alert, $pending, $encode_content = true)
    {

        // new version 20130728, zharkov
        $title                  = $title_source;
        $title_matches          = Parser::GetObjects($title_source);
        
        $description            = $description_source;
        $description_matches    = Parser::GetObjects($description_source);
        
/*      old version
        list($title, $title_matches)                = $this->parse_content($title_source, $encode_content);
        list($description, $description_matches)    = $this->parse_content($description_source, true); // 20121009 zharkov: по требованию АА убрал парсинг бизнесов в тексте сообщения
*/

        // if sender unknown message will not added
        if (empty($sender_id)) return array('ErrorCode' => -4);
                
        // сохраняет сообщение
        $result = $this->save($type_id, $role_id, $sender_id, $title, $title_source, $description, $description_source, $parent_id, $deadline, $alert, $pending);
        if (empty($result)) return array('ErrorCode' => -1);
        
        $message_id = $result['id'];

        // сохраняет прямых получателей сообщения        
        // 20120717, zharkov: старая версия без получателей сообщения if (empty($recipient) && in_array($type_id, array(MESSAGE_TYPE_NORMAL, MESSAGE_TYPE_PRIVATE, MESSAGE_TYPE_PERSONAL))) return array('ErrorCode' => -2);
        // 20120717, zharkov: все сообщения должны иметь получателей
        if (empty($recipient)) return array('ErrorCode' => -2);
        $result = $this->save_users($message_id, $type_id, $role_id, $pending, $sender_id, 'r', $recipient);
        
        // сохраняет косвенных получателей сообщения
        $result = $this->save_users($message_id, $type_id, $role_id, $pending, $sender_id, 'c', $cc);
        
        // связывает сообщение с объектами
        foreach ($description_matches as $key => $row)
        {
            if (!isset($title_matches[$key])) $title_matches[$key] = $row;
        }
        $result = $this->SaveObjects($message_id, $type_id, $role_id, $sender_id, $title_matches);
        if (empty($result)) return array('ErrorCode' => -3);
        
        // Обновляет ключ кеша идентификатором сообщения
        MainChatMessage::SetChatLastMessageId($message_id);
        

		
        return array('id' => $message_id);
    }
    
    /**
     * Связывает сообщение с объектами
     * 
     * @param mixed $message_id
     * @param mixed $objects
     * 
     * @version 20120628, zharkov
     */
    function SaveObjects($message_id, $type_id, $role_id, $sender_id, $objects)
    {
        foreach ($objects as $object) 
        {
            $result = $this->SaveObject($message_id, $type_id, $role_id, $sender_id, $object['alias'], $object['id']);
            if (empty($result)) return null;
        }
     
        return $message_id;
    }
    
    /**
     * Привязывает объект к сообщению
     * 
     * @param mixed $message_id
     * @param mixed $object_alias
     * @param mixed $object_id
     * 
     * @version 20120628, zharkov
     */
    function SaveObject($message_id, $type_id, $role_id, $sender_id, $object_alias, $object_id)
    {
        $result = $this->CallStoredProcedure('sp_message_save_object', array($this->user_id, $message_id, $type_id, $role_id, $sender_id, $object_alias, $object_id));
        $result = isset($result) && isset($result[0]) && isset($result[0][0]) ? $result[0][0] : null;
        
        if (isset($result) && array_key_exists('ErrorCode', $result)) return null;

        // обновляет последнее сообщение для объекта
        MainChatMessage::SetChatLastMessageId($message_id, $object_alias, $object_id);

        // обновляет список сообщений для объекта
        Cache::ClearTag('messages-obj-' . $object_alias . '-objid-' . $object_id);
        Cache::ClearTag($object_alias . '-' . $object_id . '-blog');
        
        return $message_id;
    }
    
    /**
     * Находит вхождения значеий справочников в сообщении
     * 
     * @param mixed $content
     * @return array($parsed_content, $matches) 
     *      $parsed_content - контент в котором вхождения заменены на ссылки, 
     *      $matches - массив найенных объектов вида {'alias' => $alias, 'id' => $id}
     */
    private function parse_content($content, $encode_content = true)
    {
        // found objects
        $objects = array();
      
        // parse message references
        //<message_id=7>Ref. your 2012/07/08 10:01:24</ref> : 
        preg_match_all('#&lt;ref message_id=(\d+)&gt;#i', $content, $matches);
        if (!empty($matches))
        {
            foreach ($matches[1] as $key => $ref_id)
            {
                $content = str_replace('&lt;ref message_id=' . $ref_id . '&gt;', '<a href="javascript: void(0);" onclick="show_chat_message(' . $ref_id . ');">', $content);
                $content = str_replace('&lt;/ref&gt;', '</a>', $content);
            }
        }

        // parse email references
        //<email_id=7>Ref. your 2012/07/08 10:01:24</ref> : 
        preg_match_all('#&lt;ref email_id=(\d+)&gt;#i', $content, $matches);
        if (!empty($matches))
        {
            foreach ($matches[1] as $key => $ref_id)
            {
                $content = str_replace('&lt;ref email_id=' . $ref_id . '&gt;', '<a href="javascript: void(0);" onclick="show_email_message(' . $ref_id . ');">', $content);
                $content = str_replace('&lt;/ref&gt;', '</a>', $content);
            }
        }
        
        // parse content
        $componentObject = new ObjectComponent();
        return $componentObject->ParseContent($content, $encode_content);
    }
    
    /**
     * Сохраняет сообщение
     * 
     * @param mixed $type_id
     * @param mixed $role_id
     * @param mixed $sender_id
     * @param mixed $title
     * @param mixed $title_source
     * @param mixed $description
     * @param mixed $description_source
     * @param mixed $parent_id
     * @param mixed $deadline
     * @return resource
     * 
     * @version 20120628, zharkov
     */
    private function save($type_id, $role_id, $sender_id, $title, $title_source, $description, $description_source, $parent_id, $deadline, $alert, $pending)
    {
        $result = $this->CallStoredProcedure('sp_message_save', array($this->user_id, $type_id, $role_id, $sender_id, $title, $title_source, $description, $description_source, $parent_id, $deadline, $alert, $pending));
        $result = isset($result) && isset($result[0]) && isset($result[0][0]) ? $result[0][0] : null;
        
        if (empty($result) || array_key_exists('ErrorCode', $result)) return null;

        Cache::ClearTag('message-' . $result['id']);
        Cache::ClearTag('messages');
        
        return $result;
    }

    /**
     * Сохраняет читателей сообщения
     *   
     * @param mixed $message_id
     * @param mixed $relation
     * @param mixed $user_ids
     * @return resource
     * 
     * @version 20120628, zharkov
     */
    private function save_users($message_id, $type_id, $role_id, $pending, $sender_id, $relation, $user_ids)
    {
        if (empty($user_ids)) return;
        
        $result = $this->CallStoredProcedure('sp_message_save_users', array($this->user_id, $message_id, $type_id, $role_id, $pending, $sender_id, $relation, $user_ids));
        $result = isset($result) && isset($result[0]) && isset($result[0][0]) ? $result[0][0] : null;
        
        if (isset($result) && isset($result['ErrorCode'])) return null;

        Cache::ClearTag('message-' . $message_id . '-users');
//        Cache::ClearTag('message-' . $message_id);
//        Cache::ClearTag('messages');
        
        return $message_id;
    }
    
	/**
	 * @version 23.04.13, Sasha save temporary message 
	 * @param type $object_alias
	 * @param type $object_id
	 * @param type $alert
	 * @param type $pending
	 * @param type $title
	 * @param type $description
	 * @param type $recipient
	 * @param type $cc
	 * @param type $deadline
	 */
	function SaveTemporary($object_alias, $object_id, $alert, $pending, $title, $description, $recipient, $cc, $deadline)
	{
		$this->CallStoredProcedure('sp_message_save_temporary', array($this->user_id, $object_alias, $object_id, $alert, $pending, $title, $description, $recipient, $cc, $deadline));
	
		Cache::ClearTag('temporary-message-' . $object_alias . '-object-' . $object_id . '-user-' . $this->user_id);
	}
	
	/**
	 * @version 24.03.13, Sasha get temporary message
	 * @param type $object_alias
	 * @param type $object_id
	 * @param type $user_id
	 * @return type
	 */
	function GetTemporary($object_alias, $object_id, $user_id)
	{
		$hash       = 'temporary-message-' . $object_alias . '-object-' . $object_id . '-user-' . $user_id;
        
        $cache_tags = array($hash, 'messages');
        $result     = $this->_get_cached_data($hash, 'sp_message_get_temporary', array($object_alias, $object_id, $user_id), $cache_tags);
		
		$result		= isset($result[0][0]) && !empty($result[0][0]) ? $result[0][0] : null;
		
		if (isset($result['recipients']) && !empty($result['recipients']))
		{
			$result['recipients'] = explode(',', $result['recipients']);
			$result['recipients'] = array_flip($result['recipients']);
		}
		
		if (isset($result['cc']) && !empty($result['cc']))
		{
			$result['cc'] = explode(',', $result['cc']);
			$result['cc'] = array_flip($result['cc']);
		}
		
		return $result;
	}
	
	/**
	 * @version 24.03.13, Sasha remove from db temporary message
	 * @param type $object_alias
	 * @param type $object_id
	 * @param type $user_id
	 */
	function RemoveTemporary($object_alias, $object_id)
	{
		$this->CallStoredProcedure('sp_message_remove_temporary', array($object_alias, $object_id, $this->user_id));
	
		Cache::ClearTag('temporary-message-' . $object_alias . '-object-' . $object_id . '-user-' . $this->user_id);
	}
}
