<?php
require_once APP_PATH . 'classes/common/parser.class.php';
require_once APP_PATH . 'classes/models/deliverytime.class.php';
require_once APP_PATH . 'classes/models/message.class.php';
require_once APP_PATH . 'classes/models/user.class.php';
require_once(APP_PATH . 'classes/core/MailerBase.class.php');

class MainController extends ApplicationController
{
    function MainController()
    {
        ApplicationController::ApplicationController();
    }

    /**
     * Update messages with old bizblog data
     * 
     * BEFORE RUN THIS METHOD YOU NEED TO UPDATE mam_www.service.bizblog_message_id WITH MAX(id) FROM messages
     * 
     */
	 
    function updatemessages()
    {

        $guid = Request::GetString('id', $_REQUEST);
        
        if (empty($guid) || $guid != 'gga6555skkallLLLJBJCY55NQGGS')
        {
            die('Hello World!');
        }
/*  test 
        $description = 'Ive <a href="google.com">learnt</a> <a href="/biz/3344">the biz3344.55</a> settings <a href="yandex.ru">how to</ya> check from Google server by Eudora and now there is not a problem anymore.<br/>In addition to my <a href="http://mamvillage.steelemotion.com/TL/ViewMessage.aspx?id=527529" target="_blank" >13:43 22/01/2013</a> :<br/><br/>1. we can use IMAP or POP3 protocol to check eMails.<br/>2. we can read by IMAP directly from the boxes or use GROUPs like alias to distribute eMails and everyone has own copy <br/>+ we may use one account as backup storage. <br/>3. If we use IMAP, we may move eMails received before by Eudora to Google server as well, incl attachments (if link btw eMail and attachment was not broken).<br/><br/>My proposal : <br/>1. We use IMAP protocol<br/>2. We use Google GROUPs. <br/>3. We take 30-days free and if we satisfy by service, we do payment and cancel our collocatiom mail server in DON.<br/><br/>The key question now : if we will check eMails directly from accounts or use GROUPS (like aliases) and everyone will have own copy of eMails. I prefer GROUPs from safety reasons (if use POP protocol, user may delete eMails even from Google server).<br/><br/>If we use GROUPs : <br/><br/><b>Accounts (2 pay): </b> <br/>newborn<br/>nemo<br/>mamkiara<br/>theend<br/>white<br/>admin (for system messages)<br/>alexandr<br/>jk<br/>mariana<br/>ms<br/>backup? <br/><br/>++7 more existent boxes : aspiration, donald, galaxy, mymacho, virtual, radiance, starter.<br/>The question is how many fixed accounts we need now. It depends how many new personnel we may expect in next 3 months.<br/><br/>totally 18 accounts = 90 USD/month or 900/year. Space : 25 Gb/account.<br/>Annual payment will be charged monthly, not whole amount at one time, but with discount already.<br/><br/><b>Groups  (no need to pay) for eMails distribution: </b><br/>buy@PA<br/>direction@PA<br/>direction@SE<br/>emotion<br/>equipment<br/>fax<br/>gang<br/>lamiere<br/>mam<br/>marking<br/>mycareer<br/>news<br/>plates<br/>steel<br/><br/>If you agree with my vision, I will start with accounts registering and instructions for user settings and one weekend we just move to new server. <b>';
        preg_match_all('#<a[^>]*>[^<]*biz3344.55[^<]*</a>#i', str_replace('&amp;', '&', $description), $matches);
        dg($matches);
        exit;
*/
        
        // get last handled messages.id
        $modelService   = new Model('service');
        $service_data   = $modelService->Select(1);

        if (empty($service_data))
        {
            die('Empty service data !');
        }
        
        $modelMessage   = new Message();
        $rowset         = $modelMessage->SelectList(array(
            'fields'    => array('id', 'title', 'title_source', 'description', 'description_source', 'type_id', 'role_id', 'sender_id', 'tl_biz'),
            'order'     => 'id DESC',
            'where'     => array('conditions' => 'id < ?', 'arguments' => array($service_data['bizblog_message_id'])),
            'limit'     => 1000
        ));

        $modelOldBlogs          = new Model('old_bizblogs');
        $modelOldAttachments    = new Model('old_attachments');
        $modelBiz               = new Biz();
        
        foreach ($rowset as $message)
        {
            $description = $message['description_source'];

            // RULES ORDER IS IMPORTANT!
            
            // replace <a href="http://mamvillage.steelemotion.com/Registers/Biz/View/?id=4916" target="_blank">my comment # 571</a>
            preg_match('#<a.*/biz/view/\?id=(\d+)[^>]*>my comment\s*\#\s*(\d+)[^<]*</a>#i', $description, $match);
            
            if (isset($match) && !empty($match))
            {
                // get old blog message
                $oldblog_row = $modelOldBlogs->SelectList(array(
                    'fields'    => array('id', 'description'),
                    'where'     => array('conditions' => 'biz_id = ? AND no = ?', 'arguments' => array($match[1], $match[2]))
                ));

                if (isset($oldblog_row) && isset($oldblog_row[0]) && !empty($oldblog_row[0]))
                {
                    $oldblog_row    = $oldblog_row[0];
                    $description    = $oldblog_row['description'];

                    // target all attachments to message object
                    $oldattachment_rowset = $modelOldAttachments->SelectList(array(
                        'fields'    => array('id'),
                        'where'     => array('conditions' => "object_alias = 'bizblog' AND object_id = ?", 'arguments' => array($oldblog_row['id']))
                    ));
                    
                    if (isset($oldattachment_rowset) && !empty($oldattachment_rowset))
                    {
                        foreach($oldattachment_rowset as $oldattachment_row)
                        {
                            $modelOldAttachments->Update($oldattachment_row['id'], array(
                                'object_alias'  => 'message',
                                'object_id'     => $message['id']
                            ));
                        }
                    }
                }                
            }
            
            
            // replace <a href="http://mamvillage.steelemotion.com/TL/ViewMessage.aspx?id=28931&amp;message_type=Blog" target="_blank">11-02-2013 15:11:08</a>
            preg_match_all('#<a[^>]*tl\/viewmessage\.aspx\?id=(\d+)&message_type=blog[^>]*>(.*?)</a>#i', str_replace('&amp;', '&', $description), $matches);

            if (isset($matches[0]) && !empty($matches[0]))
            {
                $blog_ids   = $matches[1];
                $titles     = $matches[2];
                
                foreach ($matches[0] as $key => $match)
                {                    
                    $related_message_id = 0;
                    
                    // get old blog message by found id
                    $oldblog_row = $modelOldBlogs->SelectList(array(
                        'fields'    => array('id', 'title', 'created_at', 'created_by'),
                        'where'     => array('conditions' => 'id = ?', 'arguments' => array($blog_ids[$key]))
                    ));

                    if (isset($oldblog_row) && isset($oldblog_row[0]) && !empty($oldblog_row[0]))
                    {
                        $oldblog_row = $oldblog_row[0];

                        // trying to get message related to blog message
                        $related_message = $modelMessage->SelectSingle(array(
                            'fields'    => array('id'),
                            'where'     => array(
                                'conditions'    => 'title_source = ? AND sender_id = ? AND created_at = ?', 
                                'arguments'     => array($oldblog_row['title'], $oldblog_row['created_by'], $oldblog_row['created_at'])
                            ),
                            'order' => 'id'
                        ));

                        if (isset($related_message) && !empty($related_message))
                        {
                            $related_message_id = $related_message['id'];
                        }                         
                    }

                    // 20130719, zharkov: $replacement = $related_message_id > 0 ? '<a href="javascript: void(0);" onclick="show_chat_message(' . $related_message_id . ');">' . $titles[$key] . '</a>' : $titles[$key];
                    $replacement = $related_message_id > 0 ? '<ref message_id="' . $related_message_id . '">' . $titles[$key] . '</ref>' : $titles[$key];
                    $description = str_replace($match, $replacement, $description); 
                }
            }
            
            // replace <a href="http://mamvillage.steelemotion.com/TL/ViewMessage.aspx?id=527529" target="_blank">13:43 22/01/2013</a>            
            preg_match_all('#<a[^>]*tl\/viewmessage\.aspx\?id=(\d+)"[^>]*>(.*?)</a>#i', str_replace('&amp;', '&', $description), $matches);

            if (isset($matches[0]) && !empty($matches[0]))
            {
                $message_ids    = $matches[1];
                $titles         = $matches[2];
                
                foreach ($matches[0] as $key => $match)
                {
                    // 20130719, zharkov: $description = str_replace($match, '<a href="javascript: void(0);" onclick="show_chat_message(' . $message_ids[$key] . ');">' . $titles[$key] . '</a>', $description);
                    $description = str_replace($match, '<ref message_id="' . $message_ids[$key] . '">' . $titles[$key] . '</ref>', $description);
                    
                }                
            }            

            // replace http://mamvillage.steelemotion.com/Registers/Biz/View/?id=6535
            preg_match_all('#<a[^>]*\/Registers\/Biz\/View\/\?id=(\d+)[^>]*>(.*?)</a>#i', $description, $matches);

            if (isset($matches[0]) && !empty($matches[0]))
            {
                $biz_ids    = $matches[1];
                $titles     = $matches[2];
                
                foreach ($matches[0] as $key => $match)
                {
                    $description = str_replace($match, '<ref biz_id="' . $biz_ids[$key] . '">' . $titles[$key] . '</ref>', $description);
                }                
            }
                        
            // replace {C10}{C13}
            $description = str_replace('{C10}{C13}', '<br>', $description);

            // replace {C13}{C10}
            $description = str_replace('{C13}{C10}', '<br>', $description);

            // replace {C13} 
            $description = str_replace('{C13}', '<br>', $description);

            // replace {C10} 
            $description = str_replace('{C10}', '<br>', $description);

            // parse message objects
            $title = $message['title_source'];
            
            if ($message['tl_biz'] > 0)
            {
                $message_biz = $modelBiz->GetById($message['tl_biz']);
                if (isset($message_biz) && isset($message_biz['biz']))
                {
                    $message_biz    = $message_biz['biz'];
                    $prefix         = '';
                    if (isset($message_biz['team']))
                    {
                        $prefix = $message_biz['team']['title'] . '.';
                    }
                    
                    $title = $prefix . $message_biz['doc_no'] . ' : ' . $title;
                }
            }
            
            $objects        = Parser::GetObjects($description);
            $title_objects  = Parser::GetObjects($title);
            
            // merge found objects
            foreach ($title_objects as $key => $row)
            {
                if (!isset($objects[$key])) $objects[$key] = $row;                
            }            

            // update message
            $modelMessage->Update($message['id'], array(
                'title'         => $title,
                'description'   => $description
            ));
            
            Cache::ClearTag('message-' . $message['id']);
            
            // save objects
            $modelMessage->SaveObjects($message['id'], $message['type_id'], $message['role_id'], $message['sender_id'], $objects);
        }
        
        // update last handled messages.id
        $modelService->Update(1, array(
            'bizblog_message_id' => $message['id']
        ));
        
        echo('message_id = ' . $message['id']);
    }
    
    
    /**
     * Update deliverytimes
     * 
     * url: /service/updatedeliverytimes
     * 
     * @version 20120914, zharkov
     */
    function updatedeliverytimes()
    {
        $modelDeliveryTime = new DeliveryTime();
        
        foreach ($modelDeliveryTime->GetList() as $key => $row)
        {
            if (isset($row['deliverytime']))
            {
                $row = $row['deliverytime'];
                $modelDeliveryTime->Save($row['id'], $row['title']);
            }
        }
        
        echo 'done';
    }
    
    /**
     * Check user activity & change user status // Проверяет активность пользователей на сайте и изменяет их статусы
     * call every minute // вызывается в кроне раз в минуту
     * 
     * url: /service/checkusers
     * 
     * @version 20120705, zharkov
     */
    function checkusers()
    {
        $activeusers = new ActiveUser();        
        foreach($activeusers->GetList() as $row)
        {
            $user_id        = $row['user_id'];
            $current_status = Cache::GetKey('onlinestatus-' . $user_id) ? Cache::GetKey('onlinestatus-' . $user_id) : 'online';

            // check last ping from browser // проверяет последний пинг браузера пользователя
            if (!($last_ping = Cache::GetKey('online-' . $user_id)))
            {
                $last_ping = $row['online_at'];
            }
            else
            {
                $activeusers->UpdateOnlineAt($user_id, $last_ping);
            }

            // set status "Offline" // если пинг был давно переводит его в статус "Offline"
            if (time() - $last_ping > ACTIVEUSER_OFFLINE_LIMIT)
            {
                $activeusers->Remove($user_id);
                continue;
            }
            
            // check last access to page & set status "Away"    // проверяет последнее посещение странц, если было давно переводит пользователя в статус "Away"
            if (!($last_access = Cache::GetKey('activeuser-' . $user_id)))
            {
                $last_access = $row['visited_at'];
            }

            if (time() - $last_access > ACTIVEUSER_AWAY_LIMIT)
            {
                if ($current_status != 'away')
                {
                    $modelUser      = new User();
                    $post_message   = false;
                    
                    $modelUser->SetStatusAway($user_id, $post_message);
                }
            }            
            else
            {
                if ($current_status != 'online')
                {
                    $modelUser      = new User();
                    $post_message   = false;
                    
                    $modelUser->SetStatusOnline($user_id, $post_message);
                }
            }
        }        
    }
    
    /**
     * Flush Cache  //   Сбрасывает кэш
     * 
     */
    function flushcache()
    {
        $key = Request::GetString('id', $_REQUEST);

        if (empty($key))
        {
            Cache::Flush();
        }
        else
        {
            Cache::ClearKey($key);
        }
        
        echo 'Okay';
    }
    
    /**
     * Redirect to external url //  Перенаправление на указанный адрес
     */
    function go()
    {
        $url = Request::GetString('url', $_REQUEST, '', 2048);        
/*
        $url_to_db  = detectUTF8($url) ? $url : mb_convert_encoding($url, 'UTF-8', 'CP1251');

        $redirects = new Redirect();
        $redirects->Insert(array(
            'url' => $url_to_db,
            'user_id' => !empty($_SESSION['user']) ? $_SESSION['user']['id'] : 0
        ));
*/        
        $this->_redirect_external($url);
    }
    
    
    /**
     * Prepare data for Sphinx  //  Подгатавливает данные для sphinx
     * 
     */
    function getsphinxdata()
    {
        $authkey = Request::GetString('id', $_REQUEST);
        
        if ($authkey == md5('%sph1nx4pr0duct'))   // f8a21fa12583cb0bcc1e7abb1fa85b01
        {
            $products = new Product();
            $products->GetSphinxData();
        }        
    }
    
    /**
     * Register banner show //  Регистрирует показ баннера
     * 
     */
    function promo()
    {
        $guid   = Request::GetString('guid', $_REQUEST, '', 16);
        $pos    = strpos($guid, '-');
        $guid   = $pos !== false ? substr($guid, 0, $pos) : $guid;

        $banners = new AdBanner();
        $banners->RegisterShow($guid, $_SERVER['REMOTE_ADDR'], $_SERVER['HTTP_USER_AGENT']);

        header('Content-type: image/gif');
        echo base64_decode('R0lGODlhAQABAIAAAP///////yH5BAEKAAEALAAAAAABAAEAAAICTAEAOw==');
    }
	
	
    /**
     * Update messages with old bizblog data
     * 
     * BEFORE RUN THIS METHOD YOU NEED TO UPDATE mam_www.service.bizblog_message_id WITH MAX(id) FROM messages
     * 
     */
    
    
    /**
     * Move old attachments into new structure
     * 
     * BEFORE RUN THIS METHOD YOU NEED TO UPDATE mam_www.service.old_attachment_id WITH MAX(id) FROM old_attachments
     * 
	 * @version 20130522, Sasha 
	 */
	function updateattachments()
	{
        $guid = Request::GetString('id', $_REQUEST);
        
        if (empty($guid) || $guid != 'jjshs766dhbHHJHDBhHhd72b22nJbb390917864nn')
        {
            die('Hello World!');
        }		
        
        // get last handled attachments.id
		$modelService   = new Model('service');
        $service_data   = $modelService->Select(1);

        if (empty($service_data))
        {
            die('Empty service data !');
        }
		
		$modelOldAttachments    = new Model('old_attachments');
		
        $rowset         = $modelOldAttachments->SelectList(array(
            'fields'    => array('id', 'object_alias', 'object_id', 'path', 'name', 'type', 'size', 'created_at', 'created_by'),
            'order'     => 'id DESC',
            'where'     => array('conditions' => 'id < ?', 'arguments' => array($service_data['old_attachment_id'])),
            'limit'     => 100
        ));
		
		$model_picture		= new Picture();
		$model_attachment	= new Attachment();
		
		if (!empty($rowset))
		{
			 $server_old_attachment_path = '/usr/home/mam/oldattachments/';
			
			foreach ($rowset as $row)
			{
				$row['path'] = str_replace('d:dropbox', $server_old_attachment_path, $row['path']);
				
				if (!empty($row['type']) && in_array($row['type'], array(".jpeg", ".jpg", ".gif", ".png")))
				{
					$new_attachment_id = $model_picture->CreateFromFile($row['object_alias'], $row['object_id'], $row['path'], '', 'old_attachment', MODERATE_STATUS_ACTIVE, $row['name']);
				}
				else if(!empty($row['type']))
				{
					$new_attachment_id = $model_attachment->CreateFromFile($row['object_alias'], $row['object_id'], $row['path'], '', 'old_attachment', MODERATE_STATUS_ACTIVE, $row['name']);
				}	
				
				if ($new_attachment_id > 0) 
				{
					$model_attachment->Update($new_attachment_id, array(
						'status_by'		=> $row['created_by'],
						'created_at'	=> $row['created_at'],
						'created_by'	=> $row['created_by'],
						'modified_at'	=> $row['created_at'],
						'modified_by'	=> $row['created_by']
					));
				}	
				
				$attachment_id = $row['id'];
			}
			
			// update last handled attachment_id		
			$modelService->Update(1, array(
				'old_attachment_id' => $attachment_id
			));
        
			echo('attachment_id = ' . $attachment_id);	
		}	
	}
	
	/**
	 * remove steelpositions from reservation, cron
	 * 
	 * @link /service/clearreservedpositions
	 * 
	 * @version 20130725, sasha
	 */
	function clearexpiredreserve()
	{
        $guid = Request::GetString('id', $_REQUEST);
        
        if (empty($guid) || $guid != '0826jjdfDSDasd9asd77asdnKDSiudas')
        {
            die('Hello World!');
        }        

		$modelSteelPosition = new SteelPosition();		
		$modelSteelPosition->ClearExpiredReserve();
        
        echo 'okay';
	}
    
    /**
     * emails attach to biz
     * 
     * @link /service/sentemailsattachtobiz
     * 
     * @version 20130819, sasha
     */
    function sentemailsattachtobiz()
    {
        $guid = Request::GetString('id', $_REQUEST);
      
        if (empty($guid) || $guid != '1')
        {
            die('Hello World!');
        }
        
        $modelEmail = new Email();
        $result     = $modelEmail->SentEmailsAttachToBiz();
        
        echo $result;
    }
	
	function sentemailstlarhiv()
	{
        $date_to_title = date("Y-m-d");
        
		$keyword        = 'SERVICE_CRON';
		$date_from      = !(preg_match('/\d{4}-\d{2}-\d{2}/', $date_to_title)) ? null : $date_to_title . ' 00:00:00';
		$date_to        = !(preg_match('/\d{4}-\d{2}-\d{2}/', $date_to_title)) ? null : $date_to_title . ' 00:00:00';
		$sender_id      = 0;
		$recipient_id   = 0;
		$is_dialogue    = 0;
		$is_mam         = 0;
		$is_phrase      = 0;
		$page           = 'archive';
		
		$messages   = new Message();
		$rowset     = $messages->Search($keyword, $date_from, $date_to, $sender_id, $recipient_id, $is_dialogue, $is_mam, $is_phrase, $this->page_no, $per_page);
		$rowset['title']     = 'chat-archive';
		$rowset['date_to']     = $date_to_title;
		//($keyword, $date_from, $date_to, $sender_id, $recipient_id, $is_dialogue, $is_mam, $search_type, $page_no = 0, $per_page = ITEMS_PER_PAGE)
		//print_r($rowset['data']);
		$rowset_str = serialize($rowset);
		//print_r($rowset['data']);
		
		$modelEmail = new MailerBase();
		
		$modelEmail->path = 'chat';
		$from = 'mamvillage@steelemotion.com';
		//$to = 'fingercrew2@yandex.ru';
		$to = 'emotion@steelemotion.com';
		//$cc = 'emotion@steelemotion.com';
		$template = 'chat-archive';
		$parameters=$rowset;
		//$parameters=$this->_assign('list',      $rowset['data']);;
		//$order['author']['email'], TECHNICIAN_ADDRESS, '', '', 'ordercancelled', $parameters
		//$result = $modelEmail->_send($from, $to, $cc, $bcc, $template, $parameters, $attachments = array());
		//die(TECHNICIAN_ADDRESS);
		$result = $modelEmail->_send($from, TECHNICIAN_ADDRESS, '', '', $template, $parameters);
		
		/*print_r($result);
		/**
		 * Отправляет письмо
		 * @param string $from E-Mail отправителя
		 * @param string $to E-Mail получателя
		 * @param string $template Название шаблона письма
		 * @param array $parameters - Парематры шаблона письма
		 * @param array $attachments
		 * @return boolean
		 */		
	}
	
	public function tonel()
	{
/* ##########################

	Devart HttpTunnel v1.71.    

	HTTP tunnel script.    

	This script allows you to manage database server even if the corresponding port is blocked or remote access to database server is not allowed.    

   ##########################

*/
if ( !function_exists('sys_get_temp_dir')) {
    function sys_get_temp_dir() {
        if (!empty($_ENV['TMP'])) { return realpath($_ENV['TMP']); }
        if (!empty($_ENV['TMPDIR'])) { return realpath( $_ENV['TMPDIR']); }
        if (!empty($_ENV['TEMP'])) { return realpath( $_ENV['TEMP']); }
        $tempfile=tempnam(__FILE__,'');
        if (file_exists($tempfile)) {
          unlink($tempfile);
            return realpath(dirname($tempfile));
        }
            return false;
    }
}

$tmp_dir = sys_get_temp_dir();
if(!$tmp_dir){
    define('SYSTEM_TMP_DIR', '');
}
else{
    $last_symbol = substr($tmp_dir, -1);
    if($last_symbol == DIRECTORY_SEPARATOR){
        define('SYSTEM_TMP_DIR', $tmp_dir);
    }
    else{
        define('SYSTEM_TMP_DIR', $tmp_dir . DIRECTORY_SEPARATOR);
    }
}

$SUB_DIRECTORY = SYSTEM_TMP_DIR . 'tunnel_files';
$LOG_FILE_NAME = 'httptunnel_server.log';
$CONN_FILE_NAME = '_connections.id.php';
$LOGFILE = $SUB_DIRECTORY . '/' . $LOG_FILE_NAME;
$CONN_FILE = $SUB_DIRECTORY . '/' . $CONN_FILE_NAME;

$LOG = 1;       // Set to "0" to disable logging
$LOG_DEBUG = 1; // Set to "0" to disable additional debug logging
$LOGFILEHANDLE = 0;
$MAXLOGSIZE = "4000000";
$LIFETIME = 180; // script lifetime in seconds. If script was started and got no client within that time - it exits.
$READ_WRITE_ATTEMPTS = 100;

global $SUB_DIRECTORY;


function checkFunctionExists($functionName) {
  if (!function_exists($functionName)) {
      echo "Required function <b>$functionName</b> does not exist.</br>";
      return false;
  }

  return true;
}

// Creates connection temporary  file if not exists and checks permission to write
function CreateAndCheckConnFile($fileName) {

    global $SUB_DIRECTORY;

  if (file_exists($fileName)){
      $newFile = @fopen($fileName, 'a');
      if($newFile)
         fclose($newFile);
      else
         echo "<b>Error</b>: Failed to open ($fileName) file: Permission denied.";
          
  }
  else{
      if(!is_dir($SUB_DIRECTORY)){
          mkdir($SUB_DIRECTORY);
      }
      $newFile = @fopen($fileName, 'w');
      if($newFile){
         fwrite($newFile, "<?php echo 'Devart HTTP tunnel temporary file.'; exit; ?>\r\n"); // forbid viewing this file through browser
         fclose($newFile);
      }
      else
         echo "<b>Error</b>: Failed to create ($fileName) file: Permission denied.";
      }
        
  if(!$newFile)
    exit;
}

if (!isset($_REQUEST["a"])) {  // query from browser

	echo "Devart HttpTunnel v1.71<br />";

    $functionList = array(
	    "set_time_limit",
        "stream_socket_server",
		"stream_socket_client",
		"stream_socket_get_name",
		"stream_set_blocking",
		"stream_socket_accept",
    );
    
    $exist = true;
    foreach($functionList as $functionName) {
	  $result = checkFunctionExists($functionName);
      $exist = $exist && $result;
    }

    if ($exist)
       CreateAndCheckConnFile($CONN_FILE);
    
    if ($exist){
      echo "Tunnel script is installed correctly. <br />You can establish connections through the HTTP tunnel.";
	  if ($LOG==1) {
			echo "<br /> <br /><b>Loging is enabled.</b><br />Log files are located in the tunnel_files folder, which, in its turn, is located in the temporary folder of the operating system: " . SYSTEM_TMP_DIR;
		};
	}
    else
      echo "Required PHP functions listed above are not available. Tunneling script will not work without these functions. Please read PHP manuals about how to install listed functions.";
    exit;
}

function myErrorHandler($errno, $errstr, $errfile, $errline) {
	switch ($errno) {
	case E_ERROR:
		$errfile=preg_replace('|^.*[\\\\/]|','',$errfile);
		echo $ERRSTR."Error in line $errline of file $errfile: [$errno] $errstr\n";
		exit;
	}
}	

function shutdown () {
	global $ipsock, $rmsock, $outcount, $incount, $td, $te, $sockname, $useunix;

	if (connection_status() & 1) { // ABORTED
		logline ($_SERVER["REMOTE_ADDR"].": Irregular tunnel disconnect -> disconnecting server");
		logline ($_SERVER["REMOTE_ADDR"].": Sent ".$outcount." bytes, received ".$incount." bytes");
	} elseif (connection_status() & 2) { // TIMEOUT
		logline ($_SERVER["REMOTE_ADDR"].": PHP script timeout -> disconnecting server");
		logline ($_SERVER["REMOTE_ADDR"].": Sent ".$outcount." bytes, received ".$incount." bytes");
	}
	
	if ($ipsock) fclose($ipsock);
	if ($rmsock) fclose($rmsock);
}

function logline ($msg) {
  log_line_to_file(0, $msg);
}

function logdebug($msg) {
  log_line_to_file(1, $msg);
}

function logerr($msg) {
  global $ERRSTR;
  
  logline($msg);
  echo $ERRSTR;
  echo $msg;
}

function log_line_to_file ($debug, $msg) {
	global $LOG, $LOG_DEBUG, $MAXLOGSIZE, $LOGFILE, $LOGFILEHANDLE;	
	if ($LOG && ((! $debug) || $LOG_DEBUG)) {
		$LOGFILEHANDLE=fopen ($LOGFILE, "a");
		if ($LOGFILEHANDLE) {			
			fwrite ($LOGFILEHANDLE, date("d.m.Y H:i:s")." - $msg\r\n");
			$lstat=fstat($LOGFILEHANDLE);
			if ($lstat["size"]>$MAXLOGSIZE) rotatelog();
			fclose($LOGFILEHANDLE);
		}
	}
}

function rotatelog() {
	global $LOG, $MAXLOGSIZE, $LOGFILE, $LOGFILEHANDLE;
	if ($LOG) {
     	fwrite ($LOGFILEHANDLE, date("d.m.Y H:i:s")." - Logfile reached maximum size ($MAXLOGSIZE)- rotating.\r\n");
		fclose ($LOGFILEHANDLE);
		rename ($LOGFILE, substr_replace($LOGFILE,md5(microtime()),-3).".log");
		$LOGFILEHANDLE=fopen ($LOGFILE, "a");
		if (!$LOGFILEHANDLE)
    		$LOG=0;
		else 
		    fwrite ($LOGFILEHANDLE, date("d.m.Y H:i:s")." - Opening new Logfile.\r\n");
	}
}

function create_client_socket() {
  global $_REQUEST;
  
    if (!isset($_REQUEST["port"])) {
	  echo $ERRSTR."Port not set.";
	  return 0;
	}
	
	$port = $_REQUEST["port"];
    $client = stream_socket_client("tcp://127.0.0.1:".$port);
	if ($client) {
	  stream_set_blocking($client, 1);
	}
	return $client;
}

function send_server_script_message($command) {
    global $_REQUEST;
	
	$client = create_client_socket();
	if (!$client) {
	  logerr("Failed to create client socket");
	  return FALSE;
	}
	if (fwrite($client, $command, 1) === FALSE) {
	  logerr("Failed to send message to server script.");
	  fclose($client);
	  return FALSE;
	}
	fclose($client);
	return TRUE;
}

function increase_script_lifetime() {
  global $LIFETIME;
  
  set_time_limit($LIFETIME);
  logdebug("Script liftetime incremented with $LIFETIME");
}

function write_to_socket($socket, $buffer, $count) {
  global $READ_WRITE_ATTEMPTS;
  
  $totalCount = 0;
  $retryCount = 0;
  
  do {
    if ($retryCount > 0) {
	    usleep(10000); // 10ms
    }
    
    if (!$socket)
      break;
    
	  $written = fwrite($socket, $buffer, $count);
    $buffer = substr($buffer, $written);
	  $totalCount += $written;
    
    if ($retryCount > 0) {
      logdebug("Attempt to write #".($retryCount + 1)." Write: ".$written);
    }
    
    $retryCount = $retryCount + 1;
  } while($totalCount < $count && $retryCount < $READ_WRITE_ATTEMPTS);
  
  if ($totalCount != $count)
    logline("ERROR: Failed to write to socket $count bytes, $totalCount actually written.");
	
  return $totalCount;
}

// reads specified byte count from socket
function read_from_socket($socket, &$buffer, $count) {
  global $READ_WRITE_ATTEMPTS;
  
  $totalCount = 0;
  $retryCount = 0;
  
  $buffer = "";
  $readBuffer;
  
  do {
    if ($retryCount > 0) {
	    usleep(10000); // 10ms
    }
    
    if (!$socket)
      break;
    
    $readBuffer = fread($socket, $count);
    $read = strlen($readBuffer);
    $buffer = $buffer.$readBuffer;
    
    if ($retryCount > 0) {
      logdebug("Attempt to read #".($retryCount + 1)." Read: ".$read);
    }
    
    $totalCount += $read;
    $retryCount = $retryCount + 1;
    
  } while($totalCount < $count && $retryCount < $READ_WRITE_ATTEMPTS);
  
  if ($totalCount != $count)
    logerr("Failed to read from socket $count bytes, $totalCount actually read.");
	
  return $totalCount;
}

// packet:  size of data count |                      data count | data
// lengths:             1 byte | up to 255 bytes, typically 1 - 5| up to $MaxCount
function write_data_packet($socket, &$buffer, $count) {
    
	$countLength = strlen($count);
	// write length of data count digit
	write_to_socket($socket, $countLength, 1);
	// write data count
	write_to_socket($socket, $count, $countLength);
	// write data
	$writeCount = write_to_socket($socket, $buffer, $count);
	if ($writeCount == $count)
	  return $writeCount;
	else
	  return 0;
}

function read_data_packet($socket, &$buffer) {
    
	// obtain data length digit length
	read_from_socket($socket, $countSize, 1);
	// read data length
	read_from_socket($socket, $readCount, $countSize);
	$expectedReadCount = $readCount;
	// read data
    $readCount = read_from_socket($socket, $buffer, $readCount);
	if ($readCount == $expectedReadCount)
	  return $readCount;
	else
	  return FALSE;
}

// Start of the tunnel script
$isServer = FALSE;

if (version_compare("5.0.0",phpversion())==1) die ("Only PHP 5 or above supported");
error_reporting(0);
set_error_handler("myErrorHandler");
register_shutdown_function ("shutdown");
// no-cache
Header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
Header("Cache-Control: no-cache, must-revalidate");
Header("Pragma: no-cache"); // HTTP/1.1 
Header("Last-Modified: ".gmdate("D, d M Y H:i:s")."GMT");

header("Content-Type: application/octet-stream");

ob_implicit_flush();

// Maximum bytes to read at once
$MaxReadCount = 16*1024;

// operation success identification
$OKSTR = "OK:";
$ERRSTR = "ER:";

$CONN_FILE_MAXSIZE = 100;

// Primary tunnel connection
// need the following REQUEST vars:
// a: "c"
// s: remote server name
// p: remote server port

// every operation output at least first three chars identifying the success of operation: "OK:" if succeeded, "ER:" if not.
//

if ($_REQUEST["a"]=="c") {  // run server script
    $isServer=TRUE;
    // clear log
	if ($LOG_DEBUG) {
	  // truncate log file
	  $logfile = fopen($LOGFILE, 'w');
	  fclose($logfile);
	}

    $dad=$_REQUEST["s"];
	$dpo=$_REQUEST["p"];

	// open the interprocess socket
	$errno = 0;
	$errstr = "";
	$ipsock = stream_socket_server("tcp://127.0.0.1:0", $errno, $errstr);
		
	if (!$ipsock) {
		logerr("stream_socket_server() failed: reason:".$errno." ".$errstr);
		exit;
	}
	
	$port=stream_socket_get_name($ipsock,false);
	$port=preg_replace('/^.*?:/','', $port);
	
	stream_set_blocking($ipsock, 1);
	
    // open the remote socket
	$rmsock = stream_socket_client("tcp://".$dad.":".$dpo, $errno, $errstr);
	
	if (!$rmsock) {
	  logerr("Failed to create remote socket at $dad: $dpo. ".$errno." ".$errstr);
	  exit;
	}
	else {
    if (isset($_REQUEST["nonblock"]))
      $block = 0;
    else
      $block = 1;
    stream_set_blocking($rmsock, $block);
	  logline("Connected to remote  $dad: $dpo");
	}
	
	// write connection identificator to file. Echo'ing is not appropriate in case of antiviral software, it would be blocked until script finishes
	$newConnFile = FALSE;
	$connFileMode = "a";

	if (file_exists($CONN_FILE)) {
	  $connFile = fopen($CONN_FILE, "r");
	  $lstat=fstat($connFile);
	  fclose($connFile);
	  if ($lstat["size"]>$CONN_FILE_MAXSIZE) {
	    $connFileMode = "w";
		$newConnFile = TRUE;
	  }
	}
	else {
	  $newConnFile = TRUE;
	}
	
	$connFile = fopen($CONN_FILE, $connFileMode);
	if ($connFile) {
	    if ($newConnFile) {
	      fwrite($connFile, "<?php echo 'Devart HTTP tunnel temporary file.'; exit; ?>\r\n"); // forbid viewing this file through browser
	    }
        $connectionId = str_replace("_", " ", $_REQUEST["id"]);	
		fwrite ($connFile, $connectionId." ".$port."\r\n");
		fclose($connFile);
	}
	else {
	  logerr("Failed to create connection temporary file.");
	  exit;
	}
	
	set_time_limit($LIFETIME);

	$exit = false;
	$buffer = array();
	$countBuffer = array();
	
	while (!$exit) {
	  logdebug("Waiting for client...");
	  $client = stream_socket_accept($ipsock);
		logline("Client accepted");
		if ($client === FALSE) {
		  logline("ERROR: Bad client.");
		  continue;
		}
	    // read command
		$count = read_from_socket($client, $buffer, 1);
		if ($count == 0) {
		  logline("Error reading client command.");
		  $exit = true;
		}
		
		logdebug("Read from client ($count): ".$buffer[0]);
				
		$command = $buffer[0];
		
		increase_script_lifetime();
		
		if ($command == "x") {  // close
		    logline("Shutting down on client request.");
		    $exit = true;  // shutdown
		}
		else if ($command == "r") { // read
		
		    if (!$rmsock) {
			  logline("ERROR: rmsock is off");
			  $exit = true;
			  break;
			}
			
			$readCount = 0;
			
      $buffer = fread($rmsock, $MaxReadCount);
      if ($buffer === FALSE) {
        logline("ERROR: Remote server disconnected.");
        $exit = true;
        break;
      }
      else {
        $readCount = strlen($buffer);
          logline("Read from remote:($readCount)");
      }
			  
		  if ($readCount >= 0) {
        if ($readCount == 0)
          logline("Nothing to read from remote.");
        
			  $writeCount = write_data_packet($client, $buffer, $readCount);
			  logdebug("Write to client($writeCount): $buffer");
			  if ($readCount > 0 && $writeCount == 0) {
          logerr("Failed to write to client.");
			    $exit = true;
			  }
			}
		}
		else if ($command == "w") { // write
			
		    if (!$rmsock) {
			  logline("ERROR: rmsock is off");
			  $exit = true;
			  break;
			}
			$readCount = read_data_packet($client, $buffer);
		    logline("Write from client: $readCount");
		    if ($readCount > 0) {
			    $writeCount = write_to_socket($rmsock, $buffer, $readCount);
			    logdebug("Write to remote($writeCount): $buffer");
			}
		}
		else if ($command == "l") {   // increment lease time
		  logline("Lease time increased.");
		}
		else if ($command == "t") {  // test connection command
		    $writeCount = write_to_socket($client, $OKSTR, strlen($OKSTR));
			if ($writeCount == 0)
			  $exit = true;
		}
        else {
		  logline("ERROR: Unknown command: $command. Exiting.");
		  $exit = true;
		}
	}
	
	logline("Server script closed.");
	exit;
}

if ($_REQUEST["a"]=="r") {  // read
	
  	$client = create_client_socket();
	if (!$client) {
	  logerr("Failed to connect to server script.");
	  exit;
	}
	
	logdebug("Client: Reading from server script");
		
	if (write_to_socket($client, "r", 1) == 0) { // write "Read" command
	  logerr("Write to server script failed.");
	  fclose($client);
	  exit;
	}
	
	$buffer;
	$readCount = read_data_packet($client, $buffer);
	if ($readCount === FALSE) {
	  logerr("Failed to read response from server script.");
	  fclose($client);
	  exit;
	}
	
	$totalCount = strlen($OKSTR) + $readCount;
	
	$outputStr = $OKSTR.$buffer;
	
	header("Content-Length: ".$totalCount);
	
  logline("Client: Read from server $readCount");
	echo $outputStr;
	
	fclose($client);
	exit;
}

if ($_REQUEST["a"]=="w") {  // write
    $client = create_client_socket();
	if (!$client) {
	  logerr("Failed to connect to server script.");
	  exit;
	}
	  
  $postBody= isset($_POST['base64body'])?base64_decode($_POST['base64body']):file_get_contents("php://input");  // Retrieve RAW POST data	
	$writeData = $postBody;
	$expectedWriteCount = strlen($writeData);
	$writeCount = write_to_socket($client, "w", 1);  // indicate that this is the "Write" command
	if ($writeCount > 0)
	  $writeCount = write_data_packet($client, $writeData, $expectedWriteCount);
	
	if ($writeCount == 0) {
	  logerr("Write to server script failed.");
	  fclose($client);
	  exit;
	}
	  
	logdebug("Client: Written $writeCount");
	
	fclose($client);
	echo $OKSTR;
	exit;
}

if ($_REQUEST["a"]=="x") {  // close
	
	echo $OKSTR."Shutted down.";
	send_server_script_message("x");
	exit;
}

if ($_REQUEST["a"] == "l") { // increment server script lease time
  
  if (send_server_script_message("l"))
    echo $OKSTR."Incremented server script lease time.";
  exit;
}

if ($_REQUEST["a"] == "t") { // test newly created connection
    
	$connectionId = str_replace("_", " ", $_REQUEST["id"]);
	logline($connectionId);
	$connections = file_get_contents($CONN_FILE);
  
    if ($connections === FALSE) {
	   logerr("Failed to open $CONN_FILE.");
	   exit;
	}
	
	$lines = explode("\r\n", $connections);
	
	// skip first line
	for($i = 1; $i < count($lines); ++$i) {
	  $line = $lines[$i];
	  $pos = strpos($line, $connectionId);
	  if ($pos === FALSE)
	    continue;
		
	  if ($pos === 0) {  // starts with
	    $parts = explode(" ", $line);
		if (count($parts) != 3) {
		  echo "Invalid connection record";
		  exit;
		}
		
		echo $OKSTR.$parts[2]."\n"."$LIFETIME\n";
		exit;
	  }
	}
	
	logerr("Connection entry not found.");
	exit;
}

logerr("Invalid tunneling script parameter: ".$_REQUEST["a"]);	
	}
}
