<?php

require_once APP_PATH . 'classes/models/attachment.class.php';
require_once APP_PATH . 'classes/models/biz.class.php';
require_once APP_PATH . 'classes/models/message.class.php';

class MainAjaxController extends ApplicationAjaxController {

    function MainAjaxController() {
        ApplicationAjaxController::ApplicationAjaxController();

        $this->authorize_before_exec['addmessage'] = ROLE_STAFF;
        $this->authorize_before_exec['markasdone'] = ROLE_STAFF;
        $this->authorize_before_exec['getnewmessages'] = ROLE_STAFF;
        $this->authorize_before_exec['soundplayed'] = ROLE_STAFF;
        $this->authorize_before_exec['getmessage'] = ROLE_STAFF;
        $this->authorize_before_exec['getmodalbox'] = ROLE_STAFF;
        $this->authorize_before_exec['getcountpendings'] = ROLE_STAFF;
    }

    /**
     * get chat add message modal window
     * url: /chat/getmodalbox
     * 
     * @version 20130324, zharkov
     */
    public function getmodalbox() {
        $object_alias = Request::GetString('object_alias', $_REQUEST);
        $object_id = Request::GetInteger('object_id', $_REQUEST);

        $this->_assign('object_alias', $object_alias);
        $this->_assign('object_id', $object_id);

        $modelUser = new User();
        $users = $modelUser->GetListForChatSeparated();

        $modalTeam = new Team();
        $this->_assign('teams', $modalTeam->GetList());

        $message_model = new Message();

        $temporary_message = $message_model->GetTemporary($object_alias, $object_id, $this->user_id);

        if (!empty($temporary_message)) {
            if (isset($users['staff']) && !empty($users['staff'])) {
                foreach ($users['staff'] as $key => $row) {
                    if (!empty($temporary_message['recipients']) && array_key_exists($row['user_id'], $temporary_message['recipients'])) {
                        $users['staff'][$key]['recipient_type'] = "r";
                    } else if (!empty($temporary_message['cc']) && array_key_exists($row['user_id'], $temporary_message['cc'])) {
                        $users['staff'][$key]['recipient_type'] = "c";
                    }
                }
            }

            if (isset($users['partners']) && !empty($users['partners'])) {
                foreach ($users['partners'] as $key => $row) {
                    if (!empty($temporary_message['recipients']) && array_key_exists($row['user_id'], $temporary_message['recipients'])) {
                        $users['partners'][$key]['recipient_type'] = "r";
                    } else if (!empty($temporary_message['cc']) && array_key_exists($row['user_id'], $temporary_message['cc'])) {
                        $users['partners'][$key]['recipient_type'] = "c";
                    }
                }
            }

            $this->_assign('temporary_message', $temporary_message);

            $attachments_guid = 'attachments-' . $this->_get_newmessage_attachments_guid($object_alias, $object_id);
            if (isset($_SESSION[$attachments_guid])) {
                $attachment_list = array();
                foreach ($_SESSION[$attachments_guid] as $key => $row) {
                    $attachment_list[] = array('attachment_id' => $key);
                }

                $modelAttachments = new Attachment();
                $this->_assign('attachment_list', $modelAttachments->FillAttachmentInfo($attachment_list));
            }
        } else if ($object_alias == 'biz' && $object_id > 0) {
            $modelBiz = new Biz();
            $biz = $modelBiz->GetById($object_id);

            if (isset($biz) && isset($biz['biz'])) {
                $biz = $biz['biz'];
                $title = (isset($biz['team']) ? $biz['team']['title'] : '') . '.' . $biz['doc_no'] . ' : ';

                $this->_assign('chat_message_title', $title);
            }

            $this->_assign('biz_id', $object_id);
        }

        $this->_assign('users', $users);

        $this->_assign('include_mce', true);
        $this->_assign('include_ui', true);
        $this->_assign('include_upload', true);

        $this->_send_json(array(
            'result' => 'okay',
            'content' => $this->smarty->fetch('templates/controls/message_modal.tpl')
        ));
    }

    /**
     * get message
     * url: /chat/getmessage
     * 
     * @version 20120708, zharkov
     */
    function getmessage() {
        $message_id = Request::GetInteger('message_id', $_REQUEST);

        $messages = new Message();
        $message = $messages->GetById($message_id);

        if (isset($message)) {
            $this->_assign('message', $message);
            $this->_send_json(array(
                'result' => 'okay',
                'content' => $this->smarty->fetch('templates/html/chat/control_chat_message_view.tpl')
            ));
        }

        $this->_send_json(array('result' => 'error'));
    }

    /**
     * check message as done
     * url: /chat/markasdone
     * 
     * @version 20120703, zharkov
     */
    function markasdone() {
        $message_id = Request::GetInteger('message_id', $_REQUEST);

        $messages = new Message();
        $result = $messages->MarkAsDone($message_id);

        if (isset($result['ErrorCode'])) {
            $this->_send_json(array('result' => 'error', 'code' => $result['ErrorCode']));
        }

        $this->_send_json(array('result' => 'okay'));
    }

    /**
     * add message to chat
     * url: /chat/addmessage
     */
    function addmessage() {
        $object_alias = Request::GetString('object_alias', $_REQUEST);
        $object_id = Request::GetInteger('object_id', $_REQUEST);
        $title = Request::GetHtmlString('title', $_REQUEST);
        $description = Request::GetHtmlString('description', $_REQUEST);
        $description = preg_replace("/ id=\".*\"/i", "", $description);  // cleanup data from 3rd part source
        $type = Request::GetInteger('type', $_REQUEST);
        $recipient = trim(Request::GetString('recipient', $_REQUEST), ',');
        $cc = trim(Request::GetString('cc', $_REQUEST), ',');
        $alert = Request::GetInteger('alert', $_REQUEST);
        $pending = Request::GetInteger('pending', $_REQUEST);
        $deadline = Request::GetDateForDB('deadline', $_REQUEST);
        $parent_id = Request::GetInteger('parent_id', $_REQUEST);
        $role_id = ROLE_STAFF;
        $target = Request::GetString('target', $_REQUEST);

//		print_r($_REQUEST);
//		die();

        if (isset($deadline) && strtotime($deadline) < strtotime(date('Y-m-d', time()))) {
            $this->_send_json(array('result' => 'error', 'code' => 'Wrong deadline specified !'));
        }

        // set message role by max receiver role // устанавливает роль получателя сообщения по максимальной из всех получателей
        $users = new User();
        foreach (explode(',', $recipient . ',' . $cc) as $recipient_id) {
            $user = $users->GetById($recipient_id);
            if (!empty($user) && isset($user['user'])) {
                if ($user['user']['role_id'] > $role_id) {
                    $role_id = $user['user']['role_id'];
                }
            }
        }

        // check online status for current user & set is online if it is away or offline // проверяет онлайн статус пользователя и если он away или offline, выводит его в онлайн
        $current_status = Cache::GetKey('onlinestatus-' . $this->user_id) ? Cache::GetKey('onlinestatus-' . $this->user_id) : '';
        if ($current_status == 'online') {
            $messages = new Message();
            $messages->AlertOnline($this->user_id);

            Cache::SetKey('onlinestatus-' . $this->user_id, 'online');
        }

        // post message // отправляет сообщение
        $messages = new Message();
        $result = $messages->Add($type, $role_id, $this->user_id, $recipient, $cc, $title, $description, $parent_id, $deadline, $alert, $pending);



        // remove temp message
        $messages->RemoveTemporary($object_alias, $object_id);

        if (isset($result['ErrorCode'])) {
            $this->_send_json(array('result' => 'error', 'code' => $result['ErrorCode']));
        }

        // link message with objects // связывает сообщение с объектом
        if (!empty($object_alias) && $object_alias != 'chat') {
            $messages->SaveObject($result['id'], $type, $role_id, $this->user_id, $object_alias, $object_id);
        }

        // add attached files to message // если есть приаттаченные файлы, добавляет их к сохраненному сообщению
        $attachments = new Attachment();
        $attachments_guid = $this->_get_newmessage_attachments_guid($object_alias, $object_id);
        $attachments->AssignUploaded($attachments_guid, 'message', $result['id']);

        $message = $messages->GetById($result['id']);

        if ($object_alias == 'chat' || $object_alias == 'to') {
            $this->_assign('message', $message);
            /*
              if($_SESSION['user']['id']!=='1671') {
              $content = $this->smarty->fetch('templates/html/chat/control_chat_messagemod.tpl');
              }else{
              $content = $this->smarty->fetch('templates/html/chat/control_chat_message.tpl');
              } */
            $content = $this->smarty->fetch('templates/html/chat/control_chat_messagemod.tpl');
        } else {
            $this->_assign('row', $message);
            $content = $this->smarty->fetch('templates/controls/blog_message.tpl');
        }

        $this->_send_json(array(
            'result' => 'okay',
            'content' => $content,
            'message_id' => $result['id']
        ));
    }

    /**
     * send chat alert
     * @url /chat/sendtla
     * @version 20130227, d10n
     */
    public function sendtla() {
        $form = isset($_REQUEST['form']) ? $_REQUEST['form'] : array();
        $attachment_ids = isset($_REQUEST['att_ids']) ? $_REQUEST['att_ids'] : array();

        $description = Request::GetString('message', $form);
        $biz_title = Request::GetString('biz_title', $form);
        $biz_id = Request::GetInteger('biz_id', $form);
        $object_alias = Request::GetString('object_alias', $_REQUEST);
        $object_id = Request::GetInteger('object_id', $_REQUEST);

        if (empty($attachment_ids)) {
            $this->_send_json(array('result' => 'error', 'message' => 'Files Not Found !'));
        }

        $modelBiz = new Biz();
        $biz = $modelBiz->GetById($biz_id);

        if (!isset($biz['biz'])) {
            $this->_send_json(array('result' => 'error', 'message' => 'Biz Not Found !'));
        }

        $biz = $biz['biz'];

        if (empty($description)) {
            $description = 'Please see shared files !';
        }

        $modelMessage = new Message();
        $message = $modelMessage->Add(MESSAGE_TYPE_NORMAL, ROLE_STAFF, $this->user_id, MAM_USER, '', $biz['doc_no'] . ' Shared files', $description, 0, '', 0, 0);
        $object = $modelMessage->SaveObject($message['id'], MESSAGE_TYPE_NORMAL, ROLE_STAFF, $this->user_id, 'biz', $biz['id']);

        $modelAttachment = new Attachment();

        foreach ($attachment_ids as $id) {
            if ($id <= 0)
                continue;

            $attachment = $modelAttachment->GetById($id);

            if (!isset($attachment['id']))
                continue;
            if ($attachment['object_alias'] != $object_alias)
                continue;
            if ($attachment['object_id'] != $object_id)
                continue;


            $modelAttachment->LinkToObject($id, 'message', $message['id']);
        }

        $this->_send_json(array('result' => 'okay',));
    }

    /**
     * save temporary message
     * @version 22.04.13, Sasha 
     */
    function savetemporarymessage() {
        $object_alias = Request::GetString('object_alias', $_REQUEST);
        $object_id = Request::GetInteger('object_id', $_REQUEST);
        $title = Request::GetHtmlString('title', $_REQUEST);
        $description = Request::GetHtmlString('description', $_REQUEST);
        $description = preg_replace("/ id=\".*\"/i", "", $description);  // cleanup data from 3rd part source
        $recipient = trim(Request::GetString('recipient', $_REQUEST), ',');
        $cc = trim(Request::GetString('cc', $_REQUEST), ',');
        $alert = Request::GetInteger('alert', $_REQUEST);
        $pending = Request::GetInteger('pending', $_REQUEST);
        $deadline = Request::GetDateForDB('deadline', $_REQUEST, null);

        $model_message = new Message();
        $model_message->SaveTemporary($object_alias, $object_id, $alert, $pending, $title, $description, $recipient, $cc, $deadline);

        $this->_send_json(array('result' => 'okay',));
    }

    /**
     * Get new message attachments guid
     * 
     * @param mixed $object_alias
     * @param mixed $object_id
     */
    private function _get_newmessage_attachments_guid($object_alias, $object_id) {
        return $object_alias . (empty($object_id) ? '' : $object_id) . 'message-' . $this->user_id;
    }

    /*
     * Get count pendings
     */

    function getcountpendings() {
        $messages = new Message();
        $rowset = $messages->GetPendings($this->page_no);
        //debug("1682", $rowset);
        //$this->_assign('count', $rowset['count']);
        $this->_send_json(array(
            'result' => 'okay',
            'count' => $rowset['count'],
        ));
    }

    /**
      /**
     * send chat alert
     * @url /chat/search
     * @version 20140920, phpdev
     */
    function searchmsg() {

        $keyword = Request::GetString('keyword', $_REQUEST);
        //print_r($keyword);
        $date_from = Request::GetDateForDB('date_from', $_REQUEST);
        $date_to = Request::GetDateForDB('date_to', $_REQUEST);
        $sender_title = Request::GetString('sender_title', $_REQUEST);
        $sender_id = Request::GetInteger('sender_id', $_REQUEST);
        $recipient_title = Request::GetString('recipient_title', $_REQUEST);
        $recipient_id = Request::GetInteger('recipient_id', $_REQUEST);
        $search_type = Request::GetString('search_type', $_REQUEST);
        
        $start = Request::GetInteger('start', $_REQUEST);
        if(empty($start) || $start === 0) $start = 0;
        $limit = Request::GetInteger('limit', $_REQUEST);
        if(empty($limit) || $limit === 0) {
            $limit = 20;
        }
        
        $is_dialogue = Request::GetBoolean('is_dialogue', $_REQUEST);
        //($is_dialogue == 'on') ? $is_dialogue=1 : $is_dialogue=0;
        //$is_mam = Request::GetInteger('is_mam', $_REQUEST);
        //($is_mam == 'on') ? $is_mam=1 : $is_mam=0;
        $is_phrase = Request::GetBoolean('is_phrase', $_REQUEST);
        $msg_ids = Request::GetString('msg_ids', $_REQUEST);
        /* */


        /**/
        $message_model = new Message();
        //$where = "WHERE 1=1";

        if ($is_dialogue) {
            ($sender_id > 0) ? $where .= " AND (message_users.`sender_id` = '{$sender_id}' OR message_users.`user_id` = '{$sender_id}')" : $where;
            ($recipient_id > 0) ? $where .= " AND (message_users.`sender_id` = '{$recipient_id}' OR message_users.`user_id` = '{$recipient_id}')" : $where;
        } elseif (!$is_dialogue) {
            ($sender_id > 0) ? $where .= " AND message_users.`sender_id` = '{$sender_id}'" : $where;
            ($recipient_id > 0) ? $where .= " AND message_users.`user_id` = '{$recipient_id}'" : $where;
        }
        if ($keyword !== '')
            $where .= " AND (messages.`title` LIKE '%{$keyword}%' OR messages.`description` LIKE '%{$keyword}%')";
        if ($date_from !== null)
            $where .= " AND messages.`created_at` > '{$date_from}'";
        if ($date_to !== null)
            $where .= " AND messages.`created_at` < '{$date_to}'";
        if ($msg_ids !== '')
            $where .= " AND messages.id IN ('{$msg_ids}')";
        $where = substr($where, 4);
        $arg = array(
            'fields' => 'DISTINCT messages.id AS message_id',
            'join' => array(
                'table' => 'message_users',
                'conditions' => 'messages.id = message_users.message_id',
            ),
            'where' => $where,
            'order' => 'messages.id DESC LIMIT '.$start.','.$limit,
        );
        $arg_for_count = array(
            'fields' => 'COUNT(DISTINCT messages.id) AS count_ids',
            'join' => array(
                'table' => 'message_users',
                'conditions' => 'messages.id = message_users.message_id',
            ),
            'where' => $where,
            'order' => 'messages.id DESC',
        );        
        $message_arr = $message_model->table->SelectList($arg);
        $message_arr_count = $message_model->table->SelectList($arg_for_count);
        //dg($message_arr);
        foreach($message_arr as $row) {
            $message_ids_arr[] = $row['message_id'];
        }
        //dg($message_ids_arr);
        //$message_ids = implode(', ', $message_ids_arr);
        /*
        $sql = "SELECT "
                . "GROUP_CONCAT(DISTINCT me.id SEPARATOR ',') "
                . "FROM `messages` AS me "
                . "JOIN `message_users` AS mu ON me.id = mu.`message_id`"
                . "WHERE 1=1";
        if ($is_dialogue) {
            ($sender_id > 0) ? $sql .= " AND (mu.`sender_id` = '{$sender_id}' OR mu.`user_id` = '{$sender_id}')" : $sql;
            ($recipient_id > 0) ? $sql .= " AND (mu.`sender_id` = '{$recipient_id}' OR mu.`user_id` = '{$recipient_id}')" : $sql;
        } elseif (!$is_dialogue) {
            ($sender_id > 0) ? $sql .= " AND mu.`sender_id` = '{$sender_id}'" : $sql;
            ($recipient_id > 0) ? $sql .= " AND mu.`user_id` = '{$recipient_id}'" : $sql;
        }
        if ($keyword !== '')
            $sql .= " AND (me.`title` LIKE '%{$keyword}%' OR me.`description` LIKE '%{$keyword}%')";
        if ($date_from !== null)
            $sql .= " AND me.`created_at` > '{$date_from}'";
        if ($date_to !== null)
            $sql .= " AND me.`created_at` < '{$date_to}'";
        if ($msg_ids !== '')
            $sql .= " AND me.id IN ('{$msg_ids}')";

        $messages = new Message();
        $resource = $messages->table->_exec_raw_query($sql);
        $rowset = $messages->table->_fetch_array($resource);
        //dg($rowset);
        foreach ($rowset[0] as $key => $row) {
            $messages_ids = $rowset[0][$key];
        }

        $arr_ids = explode(',', $messages_ids);
        /*
        if (count($arr_ids) > 0) {
            $arr_msg = array();
            foreach ($arr_ids as $row) {
                $arr_msg[] = $messages->GetById($row);
            }
        }
         * */
        //if (count($message_ids_arr) > 0) {
        //dg(count($message_ids_arr));
            $arr_msg = array();
      
            foreach ($message_ids_arr as $row_id) {
                //dg($message_model->GetById($row_id));
                $message = $message_model->GetById($row_id);
                $arr_msg[] = $message;
                //$count++;
                //dg($message);

            }
           // dg($arr_msg);
              //$arr_msg = $message_model->FillMessageInfo($message_ids_arr);
               
        //}        
//dg($arr_msg);

        //$html_chat = 'test msg';
        $total_pages = ceil($message_arr_count[0]['count_ids']/$limit);
        if($current_page >= $total_pages) $current_page = $total_pages;
        $current_page = ($start*$limit)/$limit;
        if($current_page == 0) $current_page = 1;
         //dg($arr_msg);
        
          $this->_assign('list', $arr_msg);
        $this->_assign('current_page', $current_page);
        $html_chat = $this->smarty->fetch('templates/html/chat/control_chat_messages.tpl');  
        
        $msg_ids = implode(', ', $message_ids_arr)    ;
        $this->_send_json(array(
            'result' => 'okay',
            'html' => $html_chat,
            'count' => $message_arr_count[0]['count_ids'],
            'start' => $start + $limit,
            'start_in' => $start,
            //'messages' => $arr_msg,
            'ids' => $msg_ids,
            'current_page' => $current_page,
            'limit' => $limit,
            'total_pages' => $total_pages,
            'arg' => $arg,
            'count_in_iteration' => count($message_ids_arr),
            //$start*$limit - колличество уже показанных сообщений
            //общее количество страниц ($count = 43, $limit = 10) $total_pages = ceil($count/$limit) = 43/10 = 4
            //1) $start = 0 $total_pages - $start = 4 - 0 = 4
            //2) $start = 10 $start - номер первой записи на странице, ($current_page = 2)$current_page*$limit - $limit = 2*10-10 = 10
            //3) $start = 20 $current_page*$limit - $limit = 3*10-10 = 20
            //'ids' => $messages_ids,
        ));
    }

}
