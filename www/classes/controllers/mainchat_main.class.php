<?php
require_once APP_PATH . 'classes/components/object.class.php';
require_once APP_PATH . 'classes/core/Pagination.class.php';
require_once APP_PATH . 'classes/models/mainchat_message.class.php';
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
        
        $this->breadcrumb = array('MainChat' => '/mainchat');
        
        // 20121001, zharkov: новая версия 24 - 24 покажет все сообщения за 24
        $yesterday = date("Y-m-d", time() - 60 * 60 * 24);
        $this->_assign('yesterday_filter', 'datefrom:' . $yesterday . ';dateto:' . $yesterday . ';page:yesterday');        
        //$this->_assign('yesterday_filter', 'datefrom:' . date("Y-m-d", time() - 60 * 60 * 24) . ';dateto:' . date("Y-m-d") . ';page:yesterday');        
    }

    /**
     * Отображает страницу со списком сообщений чата
     * url: /mainchat
     * url: /{object_alias}/{object_id}/mainchat
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
            $this->page_name = 'Chat (test_page)';
        }        
        
        $users = new User();
        $this->_assign('users', $users->GetListForChatSeparated());
        
        $mainchatmessages   = new MainChatMessage();    

        if (!empty($object_alias) && !empty($object_id))    //например /$object_alias (mainchat)/$object_id (archive)
        {                                                   
            $rowset = $mainchatmessages->GetListForObject($object_alias, $object_id, 0, $this->page_no);
            $list   = $rowset['data'];

            $pager = new Pagination();
            $this->_assign('pager_pages',   $pager->PreparePages($this->page_no, $rowset['count']));
            $this->_assign('count',         $rowset['count']);            
        }
        else    //на странице чата отображается вест список сообщений независимо от количества
        {
            $list = $mainchatmessages->GetList();   //получает список сообщений
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
        
        $this->context  = true;
        $this->rcontext = true;
        $this->layout   = 'rcolumn';
        
        $this->_assign('include_ui',        true);
        $this->_assign('include_mce',       true);
        $this->_assign('include_upload',    true);        
        
        $this->_assign('chat_object_alias', $object_alias);
        $this->_assign('chat_object_id',    $object_id);
        
        // очищает список приаттаченных файлов к сообщению
        if (isset($_SESSION['attachments-message-0'])) unset($_SESSION['attachments-message-0']);
        $this->js[] = 'app';
        $this->js = 'mainchat_archive'; //js файл для datepickera
        // список команд для блока выбора бизнеса
       
        $teams = new Team();
        $this->_assign('teams', $teams->GetList());
        /***************************** Дальше для архива *************************************************************
        *
        *
        *
        */
        
        
        
        $this->_display('index');
    }
    
    /**
     * Отображает страницу просмотра архива чата
     * url: /touchline/archive
     * url: /touchline/archive/{yyyy-mm-dd}
     */
    public function archive()
    {
        $date_to = Request::GetString('date_to', $_REQUEST);    //получаем значение date_to (если оно есть)
        
        $filter_params  = array();
        if (!empty($date_to))
        {
            $filter_params = array( //если $date_to есть - записываем в параметры фильтра
                'dateto' => $date_to,
            );
        }
        else
        {
            $this->_assign('date_to', date("Y-m-d"));
        }
        
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
            
            $mainchatmessages   = new MainChatMessage(); 
            $rowset     = $mainchatmessages->Search($keyword, $date_from, $date_to, $sender_id, $recipient_id, $is_dialogue, $is_mam, $is_phrase, $this->page_no);
            
            $pager = new Pagination();
            $this->_assign('pager_pages',   $pager->PreparePages($this->page_no, $rowset['count']));
            $this->_assign('count',         $rowset['count']);
            
            $this->_assign('date_to',       $date_to);
            $this->_assign('list',          $rowset['data']);
        }
        else
        {
            $page = 'archive';
        }
        
        $this->js       = 'mainchat_archive';
        $this->context  = true;
        $this->rcontext = true;
        $this->layout   = 'rcolumn';

        $this->page_name                    = 'Archive';
        $this->breadcrumb[$this->page_name] = '/mainchat/arhive';
        
        $this->_assign('include_ui', true);
        $this->_assign('page', $page);
        
        $this->_display('archive');
    }
	
}
