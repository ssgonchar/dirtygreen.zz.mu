<?php
require_once APP_PATH . 'classes/components/object.class.php';
require_once APP_PATH . 'classes/core/Pagination.class.php';
require_once APP_PATH . 'classes/models/message.class.php';
require_once APP_PATH . 'classes/models/team.class.php';
require_once APP_PATH . 'classes/models/user.class.php';
require_once APP_PATH . 'classes/models/biz.class.php';
require_once APP_PATH . 'classes/models/attachment.class.php';

class MainController extends ApplicationController
{
    function MainController()
    {
        ApplicationController::ApplicationController();
        
        $this->authorize_before_exec['index']       = ROLE_STAFF;
        $this->authorize_before_exec['pendings']    = ROLE_STAFF;
        $this->authorize_before_exec['search']      = ROLE_STAFF;
        
        $this->breadcrumb = array('TouchLine' => '/touchline');
        
        // 20121001, zharkov: новая версия 24 - 24 покажет все сообщения за 24
        $yesterday = date("Y-m-d", time() - 60 * 60 * 24);
        $this->_assign('yesterday_filter', 'datefrom:' . $yesterday . ';dateto:' . $yesterday . ';page:yesterday');        
        //$this->_assign('yesterday_filter', 'datefrom:' . date("Y-m-d", time() - 60 * 60 * 24) . ';dateto:' . date("Y-m-d") . ';page:yesterday');        
    }

    /**
     * Отображает страницу поиска сообщений чата
     * url: /touchline/search
     * 
     * @version 20120711, zharkov
     */
    function search()
    {

        if (isset($_REQUEST['btn_find']))
        {
            $keyword            = Request::GetString('keyword', $_REQUEST);
            //print_r($keyword);
            $date_from          = Request::GetDateForDB('date_from', $_REQUEST);
            $date_to            = Request::GetDateForDB('date_to', $_REQUEST);
            $sender_title       = Request::GetString('sender_title', $_REQUEST);
            $sender_id          = Request::GetInteger('sender_id', $_REQUEST);
            $recipient_title    = Request::GetString('recipient_title', $_REQUEST);
            $recipient_id       = Request::GetInteger('recipient_id', $_REQUEST);
            $search_type        = Request::GetString('search_type', $_REQUEST);
            $is_dialogue        = Request::GetInteger('is_dialogue', $_REQUEST);
            $is_mam             = Request::GetInteger('is_mam', $_REQUEST);
            $is_phrase          = Request::GetInteger('is_phrase', $_REQUEST);
			//dg($is_phrase);
            $filter = (!empty($keyword) ? 'keyword:' . str_replace(';', ',', $keyword) . ';' : '');
            $filter .= (!empty($date_from) ? 'datefrom:' . trim(str_replace('00:00:00', '', $date_from)) . ';' : '');
            $filter .= (!empty($date_to) ? 'dateto:' . trim(str_replace('00:00:00', '', $date_to)) . ';' : '');
            $filter .= (!empty($sender_id) && !empty($sender_title) ? 'sender:' . $sender_id . ';' : '');
            $filter .= (!empty($recipient_id) && !empty($recipient_title) ? 'recipient:' . $recipient_id . ';' : '');
            $filter .= (!empty($is_dialogue) ? 'is_dialogue:1;' : '');
            $filter .= (!empty($is_mam) ? 'is_mam:1;' : '');
            $filter .= (!empty($is_phrase) ? 'is_phrase:1;' : '');
            $filter .= (!empty($search_type) ? 'search_type:' . $search_type . ';' : '');
          // dg()
            $this->_redirect(array('touchline', 'search', 'filter', str_replace(' ', '+', $filter)), false);
        }
        
        $filter         = Request::GetString('filter', $_REQUEST);
        $filter         = urldecode($filter);
        $filter_params  = array();

        if (!empty($filter))
        {
            $filter = explode(';', $filter);
            foreach ($filter as $row)
            {
                if (empty($row)) continue;
                
                $param = explode(':', $row);
                $filter_params[$param[0]] = Request::GetHtmlString(1, $param);
            }

            $keyword        = Request::GetString('keyword', $filter_params);
            $date_from      = Request::GetString('datefrom', $filter_params); 
            $date_from      = !(preg_match('/\d{4}-\d{2}-\d{2}/', $date_from)) ? null : $date_from . ' 00:00:00';
            $date_to        = Request::GetString('dateto', $filter_params);
            $date_to        = !(preg_match('/\d{4}-\d{2}-\d{2}/', $date_to)) ? null : $date_to . ' 00:00:00';
            $sender_id      = Request::GetInteger('sender', $filter_params);
            $recipient_id   = Request::GetInteger('recipient', $filter_params);
            $is_dialogue    = Request::GetInteger('is_dialogue', $filter_params);
            $is_mam         = Request::GetInteger('is_mam', $filter_params);
            $is_phrase      = Request::GetInteger('is_phrase', $filter_params);
            $search_type    = Request::GetString('search_type', $filter_params);
            //$page           = Request::GetString('page', $filter_params, 'search');

			if($is_phrase=='1') {
				$search_type = 'exact';
			}
			
            $messages   = new Message();
            //$rowset     = $messages->Search($keyword, $date_from, $date_to, $sender_id, $recipient_id, $is_dialogue, $is_mam, $search_type, 0, 999999);
            $rowset     = $messages->Search($keyword, $date_from, $date_to, $sender_id, $recipient_id, $is_dialogue, $is_mam, $search_type, $this->page_no);

            $pager = new Pagination();
            $this->_assign('pager_pages',   $pager->PreparePages($this->page_no, $rowset['count']));
            $this->_assign('count',         $rowset['count']);

            $this->_assign('list',          $rowset['data']);
            
            $this->_assign('keyword',       $keyword);
            $this->_assign('date_from',     $date_from);
            $this->_assign('date_to',       $date_to);            
            $this->_assign('sender_id',     $sender_id);
            $this->_assign('recipient_id',  $recipient_id);
            $this->_assign('is_dialogue',   $is_dialogue);
            $this->_assign('is_mam',        $is_mam);
            $this->_assign('search_type',   $search_type);
            $this->_assign('is_phrase',   $is_phrase);
           
            $users  = new User();
            $user = $users->GetById($sender_id);
            if (isset($user['user'])) $this->_assign('sender_title', $user['user']['full_login']);

            $user = $users->GetById($recipient_id);            
            if (isset($user['user'])) $this->_assign('recipient_title', $user['user']['full_login']);

            
            if (($sender_id + $recipient_id + $is_dialogue + $is_mam) > 0 || !empty($date_from) || !empty($date_to))
            {
                $this->_assign('params', true);
            }
                                    
            $this->_assign('filter', true);
        }
        else
        {
            $page = 'search';
        }
         
        $users = new User();
        $this->_assign('users', $users->GetListForChatSeparated());          
        $this->js       = 'chat_search';
        $this->context  = true;

        $this->page_name                    = 'TouchLine. Search';
        $this->breadcrumb[$this->page_name] = '/touchline/search';
        $this->rcontext = true;
        $this->layout   = 'rcolumnmod';        
        
        $this->_assign('include_ui', true);        
        //$this->_assign('page', $page);
        
        //        $var = $_REQUEST;
        //debug('1671', $var);
        
        $this->_display('search');        
    }

    /**
     * Отображает страницу со списком пендингов пользователя
     * url: /touchline/mustdo
     * 
     * @version 20120711, zharkov
     */
    function pendings()
    {
        $messages   = new Message();
        $rowset     = $messages->GetPendings($this->page_no);
        
        $users = new User();
        $this->_assign('users', $users->GetListForChatSeparated());            
        
        $this->js[]='app';
        $this->_assign('count', $rowset['count']);
        $this->_assign('list',  $rowset['data']);                        
        
        //$pager = new Pagination();
        
        //$this->_assign('pager_pages', $pager->PreparePages($this->page_no, $rowset['count']));
        $this->rcontext = true;
        $this->layout   = 'rcolumnmod';       
        $this->context  = true;

        $this->page_name                    = 'TouchLine. MustDo!';
        $this->breadcrumb[$this->page_name] = '/touchline/mustdo';
        
        $this->_display('pendings');        
    }

    /**
     * Отображает страницу со списком сообщений чата
     * url: /touchline
     * url: /{object_alias}/{object_id}/touchline
     */
    function index()
    {        
        $object_alias   = Request::GetString('object_alias', $_REQUEST);
        $object_id      = Request::GetInteger('object_id', $_REQUEST);

        if (!empty($object_alias) && !empty($object_id))
        {
            $objectcomponent    = new ObjectComponent();
            $page_params        = $objectcomponent->GetPageParams($object_alias, $object_id, 'Chat');
            
            $this->page_name    = $page_params['page_name'];
            $this->breadcrumb   = $page_params['breadcrumb'];
            
            $this->_assign('object_stat', $page_params['stat']);
        }
        else
        {
            $this->page_name = 'TouchLine';
        }        
        
        $users = new User();
        $this->_assign('users', $users->GetListForChatSeparated());
        
        $messages   = new Message();

        if (!empty($object_alias) && !empty($object_id))
        {
            //$rowset = $messages->GetListForObject($object_alias, $object_id, 0, $this->page_no);
            $rowset = $messages->GetListForObject($object_alias, $object_id, 0, 0, 999999);
            $list   = $rowset['data'];

            //$pager = new Pagination();
            //$this->_assign('pager_pages',   $pager->PreparePages($this->page_no, $rowset['count']));
            //$this->_assign('count',         $rowset['count']);            
        }
        else
        {
            $list = $messages->GetList();
        }

        if (!empty($list))
        {
            $message = $list[0];
            if (isset($message['message']) && isset($message['message']['id']))
            {
                $activeusers = new ActiveUser();
                $activeusers->SetLastMessage($message['message']['id']);
            }
        }
        
        $this->_assign('list', $list);
        
        //$this->context  = true;
        $this->rcontext = true;
        $this->layout   = 'rcolumnmod';
        
        $this->_assign('include_ui',        true);
        $this->_assign('include_mce',       true);
        $this->_assign('include_upload',    true);        
        
        $this->_assign('chat_object_alias', $object_alias);
        $this->_assign('chat_object_id',    $object_id);
        
        // очищает список приаттаченных файлов к сообщению
        if (isset($_SESSION['attachments-message-0'])) unset($_SESSION['attachments-message-0']);
        $this->js[] = 'app';	
        // список команд для блока выбора бизнеса
        $teams = new Team();
        $this->_assign('teams', $teams->GetList());
        
        $this->_display('index');
    }    
    
    /**
     * Объявляет параметры объекта
     * 
     * @version 20120721, zharkov
     */
    private function _bind_object_params($object_alias, $object_id)
    {
        if ($object_alias == 'biz')
        {
            $bizes  = new Biz();
            $biz    = $bizes->GetById($object_id);
            
            $this->page_name    = 'Biz TouchLine';
            $this->breadcrumb   = array(
                'Bizes'                 => '/bizes',
                $biz['biz']['doc_no_full']   => '/biz/' . $object_id,
                $this->page_name        => ''
            );                
        }
        else if ($object_alias == 'company')
        {
            $companies  = new Company();
            $company    = $companies->GetById($object_id);
            
            $this->page_name    = 'Company TouchLine';
            $this->breadcrumb   = array(
                'Companies'                     => '/companies',
                $company['company']['title']    => '/company/' . $object_id,
                $this->page_name                => ''
            );                
        }
        else if ($object_alias == 'person')
        {
            $persons    = new Person();
            $person     = $persons->GetById($object_id);
            
            $this->page_name    = 'Person TouchLine';
            $this->breadcrumb   = array(
                'Persons'                       => '/persons',
                $company['person']['full_name'] => '/person/' . $object_id,
                $this->page_name                => ''
            );                
        }            
        else if ($object_alias == 'order')
        {
            $orders    = new Order();
            $order     = $orders->GetById($object_id);
            
            $this->page_name    = 'Order TouchLine';
            $this->breadcrumb   = array(
                'Orders'                    => '/orders',
                $order['order']['doc_no']   => '/order/' . $object_id,
                $this->page_name            => ''
            );                
        }            
        
        $objectcomponent = new ObjectComponent();
        $this->_assign('object_stat', $objectcomponent->GetStatistics($object_alias, $object_id));        
    }
    
    /**
     * Отображает страницу просмотра архива чата
     * url: /touchline/archive
     * url: /touchline/archive/{yyyy-mm-dd}
     */
    public function archive()
    {
        $date_to = Request::GetString('date_to', $_REQUEST);
        
        $filter_params  = array();
        if (!empty($date_to))
        {
            if($date_to === date("Y-m-d")) {
                $this->_redirect(array('touchline'));
            }
            $filter_params = array(
                'dateto' => $date_to,
            );
        }
        else
        {
            //$this->_assign('date_to', date("Y-m-d"));
            $this->_redirect(array('touchline'));
        }
        $users = new User();
        $this->_assign('users', $users->GetListForChatSeparated());        
        if (!empty($filter_params))
        {
            
            $keyword        = '';
            $date_from      = !(preg_match('/\d{4}-\d{2}-\d{2}/', $date_to)) ? null : $date_to . ' 00:00:00';
            $date_to        = !(preg_match('/\d{4}-\d{2}-\d{2}/', $date_to)) ? null : $date_to . ' 00:00:00';
            $sender_id      = 0;
            $recipient_id   = 0;
            $is_dialogue    = 0;
            $is_mam         = 0;
            $is_phrase      = 0;
            $page           = 'archive';
            
            $messages   = new Message();
            //$rowset     = $messages->Search($keyword, $date_from, $date_to, $sender_id, $recipient_id, $is_dialogue, $is_mam, $is_phrase, $this->page_no);
            $rowset     = $messages->Search($keyword, $date_from, $date_to, $sender_id, $recipient_id, $is_dialogue, $is_mam, $is_phrase, 0, 999999);
            //debug('1671', $rowset);
            //$pager = new Pagination();
            //$this->_assign('pager_pages',   $pager->PreparePages($this->page_no, $rowset['count']));
            //$this->_assign('count',         $rowset['count']);
            
            $this->_assign('date_to',       $date_to);
            $this->_assign('list',          $rowset['data']);
        }
        else
        {
            $page = 'archive';
        }
        
        $this->js[]       = 'chat_archive';
        //$this->js[]       = 'jquery.highlight';
        //$this->context  = true;

        $this->page_name                    = 'TouchLine. Archive on '.date('d/m/Y', strtotime($date_to));
        $this->breadcrumb[$this->page_name] = '/touchline/arhive';
        
        $this->rcontext = true;
        $this->layout   = 'rcolumnmod';
        
        $this->_assign('include_ui', true);
        //$this->_assign('page', $page);
        
        $this->_display('archive');
    }
	
	/**
     * get touchline add message new window
     * url: /newmessage/{object_alias}/{object_id}
     * 
     * @version 20130324, zharkov
     */
    public function newmessage()
    {	
		$object_alias   = Request::GetString('object_alias', $_REQUEST);
        $object_id      = Request::GetInteger('object_id', $_REQUEST);
        
        $message_id     = Request::GetInteger('message_id', $_REQUEST);
		//print_r($message_id);
		$this->css[]		= 'bootstrap';
        $this->layout	= 'message';
		$this->js[]		= 'newmessage';
		$this->js[]		= 'bootstrap';
		//$this->css[]		= 'bootstrap-my';
		//$this->css[]		= 'style';
		//$this->js[]		= '';
		//$this->js[]		= '';
		$this->_assign('object_alias',  $object_alias);
        $this->_assign('object_id',     $object_id);
        
        $modelUser = new User();
		$users = $modelUser->GetListForChatSeparated();
		$users_here = $modelUser->GetById($this->user_id);
		
        
        $modalTeam = new Team();
        $this->_assign('teams', $modalTeam->GetList());        
        $this->_assign('colordesc', $users_here['user']['color']);        

        if ($message_id > 0)        
        {
			
            $modelMessage   = new Message();
            $message        = $modelMessage->GetById($message_id);
            
            if (isset($message))
            {
                $message = $message['message'];

                if (isset($users['staff']) && !empty($users['staff']))
                {
                    foreach ($users['staff'] as $key => $row)
                    {   
                        if ($row['user_id'] == $message['sender_id'] && $row['user_id'] != $this->user_id)
                        {
                            $users['staff'][$key]['recipient_type'] = "r";
                            continue;
                        }

                        if (isset($message['recipient']) && !empty($message['recipient']))
                        {
                            foreach ($message['recipient'] as $message_recipient)
                            {
                                if ($row['user_id'] == $message_recipient['user_id'] && $row['user_id'] != $this->user_id)
                                {
                                    $users['staff'][$key]['recipient_type'] = "c";
                                }
                            }                            
                        }
                        
                        if (isset($message['cc']) && !empty($message['cc']))
                        {
                            foreach ($message['cc'] as $message_cc)
                            {
                                if ($row['user_id'] == $message_cc['user_id'] && $row['user_id'] != $this->user_id)
                                {
                                    $users['staff'][$key]['recipient_type'] = "c";
                                }
                            }                            
                        }
                    }    
                }
                
                if (isset($users['partners']) && !empty($users['partners']))
                {
                    foreach ($users['partners'] as $key => $row)
                    {
                        if ($row['user_id'] == $message['sender_id'] && $row['user_id'] != $this->user_id)
                        {
                            $users['partners'][$key]['recipient_type'] = "r";
                            continue;
                        }

                        if (isset($message['recipient']) && !empty($message['recipient']))
                        {
                            foreach ($message['recipient'] as $message_recipient)
                            {
                                if ($row['user_id'] == $message_recipient['user_id'] && $row['user_id'] != $this->user_id)
                                {
                                    $users['partners'][$key]['recipient_type'] = "c";
                                }
                            }                            
                        }
                        
                        if (isset($message['cc']) && !empty($message['cc']))
                        {
                            foreach ($message['cc'] as $message_cc)
                            {
                                if ($row['user_id'] == $message_cc['user_id'] && $row['user_id'] != $this->user_id)
                                {
                                    $users['partners'][$key]['recipient_type'] = "c";
                                }
                            }                            
                        }
                    }    
                }
                
                // trick for old tl messages
                $title_prefix = '';
                if ($message['tl_biz'] > 0)
                {
                    $modelBiz   = new Biz();
                    $biz        = $modelBiz->GetById($message['tl_biz']);

                    if (isset($biz))
                    {
                        $biz            = $biz['biz'];
                        $title_prefix   = (isset($biz['team']) ? $biz['team']['title'] : '') . '.' . $biz['doc_no'] . ' : ';
                    }                    
                }
                
                $this->_assign('chat_newmessage', array(
                    'title'         => $title_prefix . $message['title_source'],
                    //'description'   => '<ref message_id=' . $message['id'] . '>Ref. ' . date("d/m/Y H:i:s", strtotime($message['created_at'])). '</ref> : '
                    'description'   => '&lt;ref message_id=' . $message['id'] . '&gt;Ref. ' . date("d/m/Y H:i:s", strtotime($message['created_at'])). '&lt;/ref&gt; : '
                ));
            }            
        }
        else if ($object_alias != '' && $object_id > 0)
        {
			
            if ($object_alias == 'biz')
            {
                $modelBiz   = new Biz();
                $biz        = $modelBiz->GetById($object_id);
                
                if (isset($biz))
                {
                    $biz = $biz['biz'];

                    $this->_assign('biz_id',    $object_id);
                    $this->_assign('biz_title', $biz['doc_no']);

                    $this->_assign('chat_newmessage', array(
                        'title' => (isset($biz['team']) ? $biz['team']['title'] : '') . '.' . $biz['doc_no'] . ' : '
                    ));                            
                } 
            }
            
            $this->_assign('object_alias',  $object_alias);
            $this->_assign('object_id',     $object_id);
        }

        $this->_assign('users', $users);
        
/* 20130825, zharkov: old version with temp message

    $attachments_guid = 'attachments-' .  $object_alias . (empty($object_id) ? '' : $object_id) . 'message-' . $this->user_id;
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

*/        
        
		$this->_assign('include_mce',           true);
        $this->_assign('include_ui',            true);
        $this->_assign('include_upload',        true);
        
        $this->page_name = 'New Message';
		
		$this->_display('newmessage');
    }
}
