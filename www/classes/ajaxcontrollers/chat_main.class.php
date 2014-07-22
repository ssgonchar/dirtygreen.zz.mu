<?php
require_once APP_PATH . 'classes/models/attachment.class.php';
require_once APP_PATH . 'classes/models/biz.class.php';
require_once APP_PATH . 'classes/models/message.class.php';

class MainAjaxController extends ApplicationAjaxController
{    

    function MainAjaxController()
    {
        ApplicationAjaxController::ApplicationAjaxController();
                
        $this->authorize_before_exec['addmessage']      = ROLE_STAFF;
        $this->authorize_before_exec['markasdone']      = ROLE_STAFF;
        $this->authorize_before_exec['getnewmessages']  = ROLE_STAFF;
        $this->authorize_before_exec['soundplayed']     = ROLE_STAFF;
        $this->authorize_before_exec['getmessage']      = ROLE_STAFF;
        $this->authorize_before_exec['getmodalbox']     = ROLE_STAFF;  
	$this->authorize_before_exec['getcountpendings']= ROLE_STAFF; 
    }
    
    /**
     * get chat add message modal window
     * url: /chat/getmodalbox
     * 
     * @version 20130324, zharkov
     */
    public function getmodalbox()
    {	
		$object_alias       = Request::GetString('object_alias', $_REQUEST);
        $object_id          = Request::GetInteger('object_id', $_REQUEST);
	
        $this->_assign('object_alias',  $object_alias);
        $this->_assign('object_id',     $object_id);
        
        $modelUser = new User();
		$users = $modelUser->GetListForChatSeparated();
        
        $modalTeam = new Team();
        $this->_assign('teams', $modalTeam->GetList());        

        $message_model        = new Message();
		
        $temporary_message    = $message_model->GetTemporary($object_alias, $object_id, $this->user_id);
            
        if (!empty($temporary_message))
        {
            if (isset($users['staff']) && !empty($users['staff']))
            {
                foreach ($users['staff'] as $key => $row)
                {
                    if (!empty($temporary_message['recipients']) && array_key_exists($row['user_id'], $temporary_message['recipients']))
                    {
                        $users['staff'][$key]['recipient_type'] = "r";
                    }
                    else if (!empty($temporary_message['cc']) && array_key_exists($row['user_id'], $temporary_message['cc']))
                    {
                        $users['staff'][$key]['recipient_type'] = "c";
                    }
                }    
            }
            
            if (isset($users['partners']) && !empty($users['partners']))
            {
                foreach ($users['partners'] as $key => $row)
                {
                    if (!empty($temporary_message['recipients']) && array_key_exists($row['user_id'], $temporary_message['recipients']))
                    {
                        $users['partners'][$key]['recipient_type'] = "r";
                    }
                    else if (!empty($temporary_message['cc']) && array_key_exists($row['user_id'], $temporary_message['cc']))
                    {
                        $users['partners'][$key]['recipient_type'] = "c";
                    }
                }    
            }
            
            $this->_assign('temporary_message', $temporary_message);

            $attachments_guid = 'attachments-' . $this->_get_newmessage_attachments_guid($object_alias, $object_id);
            if (isset($_SESSION[$attachments_guid]))
            {                        
                $attachment_list = array();                    
                foreach ($_SESSION[$attachments_guid] as $key =>$row)
                {    
                    $attachment_list[] = array('attachment_id' => $key); 
                }
                
                $modelAttachments = new Attachment();
                $this->_assign('attachment_list',  $modelAttachments->FillAttachmentInfo($attachment_list));
            } 
        }
        else if ($object_alias == 'biz' && $object_id > 0)
        {
            $modelBiz   = new Biz();
            $biz        = $modelBiz->GetById($object_id);
            
            if (isset($biz) && isset($biz['biz']))
            {
                $biz    = $biz['biz'];
                $title  = (isset($biz['team']) ? $biz['team']['title'] : '') . '.' . $biz['doc_no'] . ' : ';
                
                $this->_assign('chat_message_title', $title);
            } 
            
            $this->_assign('biz_id', $object_id);
        }

		$this->_assign('users', $users);
        
		$this->_assign('include_mce',           true);
        $this->_assign('include_ui',            true);
        $this->_assign('include_upload',        true);
		
        $this->_send_json(array(
            'result'    => 'okay', 
            'content'   => $this->smarty->fetch('templates/controls/message_modal.tpl')
        ));
    }
    
    /**
     * get message
     * url: /chat/getmessage
     * 
     * @version 20120708, zharkov
     */
    function getmessage()
    {
        $message_id = Request::GetInteger('message_id', $_REQUEST);
        
        $messages   = new Message();
        $message    = $messages->GetById($message_id);
        
        if (isset($message))
        {
            $this->_assign('message', $message);
            $this->_send_json(array(
                'result'    => 'okay',
                'content'   => $this->smarty->fetch('templates/html/chat/control_chat_message_view.tpl')
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
    function markasdone()
    {
        $message_id = Request::GetInteger('message_id', $_REQUEST);

        $messages   = new Message();
        $result     = $messages->MarkAsDone($message_id);
        
        if (isset($result['ErrorCode'])) 
        {
            $this->_send_json(array('result' => 'error', 'code' => $result['ErrorCode']));
        }

        $this->_send_json(array('result' => 'okay'));
    }
    
    /**
     * add message to chat
     * url: /chat/addmessage
     */
    function addmessage()
    {
        $object_alias   = Request::GetString('object_alias', $_REQUEST);
        $object_id      = Request::GetInteger('object_id', $_REQUEST);
        $title          = Request::GetHtmlString('title', $_REQUEST);
        $description    = Request::GetHtmlString('description', $_REQUEST);
        $description    = preg_replace("/ id=\".*\"/i", "", $description);  // cleanup data from 3rd part source
        $type           = Request::GetInteger('type', $_REQUEST);
        $recipient      = trim(Request::GetString('recipient', $_REQUEST), ',');
        $cc             = trim(Request::GetString('cc', $_REQUEST), ',');
        $alert          = Request::GetInteger('alert', $_REQUEST);
        $pending        = Request::GetInteger('pending', $_REQUEST);
        $deadline       = Request::GetDateForDB('deadline', $_REQUEST);
        $parent_id      = Request::GetInteger('parent_id', $_REQUEST);
        $role_id        = ROLE_STAFF;
        $target         = Request::GetString('target', $_REQUEST);
		
//		print_r($_REQUEST);
//		die();
		
		if (isset($deadline) && strtotime($deadline) < strtotime(date('Y-m-d', time())))
		{
			 $this->_send_json(array('result' => 'error', 'code' => 'Wrong deadline specified !'));
		}	
		
        // set message role by max receiver role // устанавливает роль получателя сообщения по максимальной из всех получателей
        $users = new User();
        foreach (explode(',' , $recipient . ',' . $cc) as $recipient_id)
        {
            $user = $users->GetById($recipient_id);
            if (!empty($user) && isset($user['user']))
            {
                if ($user['user']['role_id'] > $role_id)
                {
                    $role_id = $user['user']['role_id'];
                }
            }
        }
        
        // check online status for current user & set is online if it is away or offline // проверяет онлайн статус пользователя и если он away или offline, выводит его в онлайн
        $current_status = Cache::GetKey('onlinestatus-' . $this->user_id) ? Cache::GetKey('onlinestatus-' . $this->user_id) : '';
        if ($current_status == 'online')
        {
            $messages = new Message();
            $messages->AlertOnline($this->user_id);
            
            Cache::SetKey('onlinestatus-' . $this->user_id, 'online');                
        }
        
        // post message // отправляет сообщение
        $messages   = new Message();
        $result     = $messages->Add($type, $role_id, $this->user_id, $recipient, $cc, $title, $description, $parent_id, $deadline, $alert, $pending);
        

		
        // remove temp message
        $messages->RemoveTemporary($object_alias, $object_id);
        
        if (isset($result['ErrorCode'])) 
        {
            $this->_send_json(array('result' => 'error', 'code' => $result['ErrorCode']));
        }
       
        // link message with objects // связывает сообщение с объектом
        if (!empty($object_alias) && $object_alias != 'chat')
        {
            $messages->SaveObject($result['id'], $type, $role_id, $this->user_id, $object_alias, $object_id);
        }

        // add attached files to message // если есть приаттаченные файлы, добавляет их к сохраненному сообщению
        $attachments        = new Attachment();
        $attachments_guid   = $this->_get_newmessage_attachments_guid($object_alias, $object_id);
        $attachments->AssignUploaded($attachments_guid, 'message', $result['id']);

        $message = $messages->GetById($result['id']);
        
        if ($object_alias == 'chat' || $object_alias == 'to')
        {
            $this->_assign('message', $message);
            /*
            if($_SESSION['user']['id']!=='1671') {
                $content = $this->smarty->fetch('templates/html/chat/control_chat_messagemod.tpl');
            }else{
                $content = $this->smarty->fetch('templates/html/chat/control_chat_message.tpl');
            }*/
            $content = $this->smarty->fetch('templates/html/chat/control_chat_messagemod.tpl');
        }
        else
        {
            $this->_assign('row', $message);
            $content = $this->smarty->fetch('templates/controls/blog_message.tpl');
        }
		
        $this->_send_json(array(
            'result'        => 'okay', 
            'content'       => $content,
            'message_id'    => $result['id']
        ));        
    }
    
    /**
     * send chat alert
     * @url /chat/sendtla
     * @version 20130227, d10n
     */
    public function sendtla()
    {
        $form           = isset($_REQUEST['form']) ? $_REQUEST['form'] : array();
        $attachment_ids = isset($_REQUEST['att_ids']) ? $_REQUEST['att_ids'] : array();
        
        $description    = Request::GetString('message',         $form);
        $biz_title      = Request::GetString('biz_title',   $form);
        $biz_id         = Request::GetInteger('biz_id',     $form);
        $object_alias   = Request::GetString('object_alias',    $_REQUEST);
        $object_id      = Request::GetInteger('object_id',      $_REQUEST);
        
        if (empty($attachment_ids)) 
        {
            $this->_send_json(array('result' => 'error', 'message' => 'Files Not Found !'));
        }
        
        $modelBiz = new Biz();
        $biz = $modelBiz->GetById($biz_id);
        
        if (!isset($biz['biz']))
        {
            $this->_send_json(array('result' => 'error', 'message' => 'Biz Not Found !'));
        }
        
        $biz = $biz['biz'];
        
        if (empty($description))
        {
            $description = 'Please see shared files !';
        }
        
        $modelMessage = new Message();
        $message = $modelMessage->Add(MESSAGE_TYPE_NORMAL, ROLE_STAFF, $this->user_id, MAM_USER, '', $biz['doc_no'] . ' Shared files', $description, 0, '', 0, 0);
        $object = $modelMessage->SaveObject($message['id'], MESSAGE_TYPE_NORMAL, ROLE_STAFF, $this->user_id, 'biz', $biz['id']);
        
        $modelAttachment = new Attachment();
        
        foreach ($attachment_ids as $id)
        {
            if ($id <= 0) continue;
            
            $attachment = $modelAttachment->GetById($id);
            
            if (!isset($attachment['id'])) continue;
            if ($attachment['object_alias'] != $object_alias) continue;
            if ($attachment['object_id'] != $object_id) continue;
            
            
            $modelAttachment->LinkToObject($id, 'message', $message['id']);
        }
        
        $this->_send_json(array('result' => 'okay', ));
    }
	
	/**
     * save temporary message
	 * @version 22.04.13, Sasha 
	 */
	function savetemporarymessage()
    {
		$object_alias   = Request::GetString('object_alias', $_REQUEST);
        $object_id      = Request::GetInteger('object_id', $_REQUEST);
        $title          = Request::GetHtmlString('title', $_REQUEST);
        $description    = Request::GetHtmlString('description', $_REQUEST);
        $description    = preg_replace("/ id=\".*\"/i", "", $description);  // cleanup data from 3rd part source
        $recipient      = trim(Request::GetString('recipient', $_REQUEST), ',');
        $cc             = trim(Request::GetString('cc', $_REQUEST), ',');
        $alert          = Request::GetInteger('alert', $_REQUEST);
        $pending        = Request::GetInteger('pending', $_REQUEST);
        $deadline       = Request::GetDateForDB('deadline', $_REQUEST, null);

        $model_message = new Message();
		$model_message->SaveTemporary($object_alias, $object_id, $alert, $pending, $title, $description, $recipient, $cc, $deadline);
		
		$this->_send_json(array('result' => 'okay', ));
	}
    
    /**
     * Get new message attachments guid
     * 
     * @param mixed $object_alias
     * @param mixed $object_id
     */
    private function _get_newmessage_attachments_guid($object_alias, $object_id)
    {
        return $object_alias . (empty($object_id) ? '' : $object_id) . 'message-' . $this->user_id;
    }
	/*
	* Get count pendings
	*/
	function getcountpendings()
	{
        $messages   = new Message();
        $rowset     = $messages->GetPendings($this->page_no);
        
        //$this->_assign('count', $rowset['count']);
		$this->_send_json(array(
			'result' => 'okay', 
			'count' => $rowset['count'],
			));
	}	
}