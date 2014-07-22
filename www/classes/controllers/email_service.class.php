<?php
//require_once APP_PATH . 'classes/models/emailraw.class.php';
require_once APP_PATH . 'classes/models/email.class.php';
require_once APP_PATH . 'classes/models/emailfilter.class.php';
require_once APP_PATH . 'classes/models/attachment.class.php';

class ServiceController extends ApplicationController
{
    /**
     * Object
     * @var Email
     */
    private $_modelEmail;
    
    /**
     * Object
     * @var Attachment
     */
    private $_modelAttachment;
    
    
    private $_content_of_parts = array();
    
    
    public function __construct()
    {
        parent::ApplicationController();
        
        $this->init();
    }

    /**
     * Получает письма с почтового сервера по протоколу imap.<br />
     * Разбирает данные, приводик к "человеческому" виду,<br />
     * выделяет вложения (если есть),<br />
     * записывает данные в БД (таблица emails_raw)
     * 
     * url: /email/service/grab
     * url: /email/service/grab/{mailbox_name}
     * 
     * @version 20130318, zharkov
     * @version 20130531, sasha
     */
    public function grab()
    { 
        set_error_handler('user_exception_handler');        
        ini_set('max_execution_time', 1200);      
        
        $this->_grab_mailbox();
        
        echo 'okay';
    }
    
    /**
     * Получает письма для mailbox
     * 
     * @param mixed $mailbox
     * 
     * @version 20130318, zharkov
	 * @version 20130531, sasha
     */
    private function _grab_mailbox()
    {
        $iteration      = 0;
        $proccess_limit = 10;   // emails per iteration

        $mailbox        = '{imap.gmail.com:993/imap/novalidate-cert/ssl}INBOX';
        $user_name      = 'mamvillage@steelemotion.com';
        $password       = 'mamvillageSE13';
        
        $connection_id  = imap_open($mailbox, $user_name, $password);

        if (!$connection_id)
        {
            $err = 'IMAP Error: ' . imap_last_error();
            
            Log::AddLine(LOG_ERROR, $err);
            echo $err;
            
            die();
        }

        $messages       = imap_search($connection_id, 'ALL');
		
        $numbers_array  = array();

        if ($messages !== false && is_array($messages))
		{

			foreach ($messages as $message_num)
			{
				try
				{
					$data = $this->_fetch_email($connection_id, $message_num);
				}
				catch (Exception $objectException)
				{
					$exception_message  = $objectException->getMessage();                    
					Log::AddLine(LOG_EMAIL_GRABBER, $exception_message);
					
					$data = null;
				}

				if (isset($data))
				{
					$data['username']       = $user_name;
					$data['mailbox_name']   = $mailbox;

					$is_emailraw_saved      = $this->_save_email($data);
					//print_r($data);
				}

				$numbers_array[] = $message_num;
				
				$iteration++;
				
				if ($iteration > $proccess_limit)
				{
					echo 'Email grab reached limit (' . $proccess_limit. ' emails/iteration)<br>';
					break;
				}
			}
			
			$numbers = implode(',', $numbers_array);
			imap_mail_move($connection_id, $numbers, '[Gmail]/Bin');
				
		}
		else
		{
			echo 'No emails!<br>';
		}
		
        
        //imap_expunge($connection_id);        
       
        imap_close($connection_id);
        
        echo 'Email grab finished!<br>';
        die();
    }
    
    /**
     * Преобразовывает письма из emails_raw в emails, связывает с объектами и мэйлбоксами
     * вызывается по крону
     * 
     * @version 20120912, zharkov
     */
    public function parse()
    {
        ini_set('max_execution_time', 600);
        
        $emails_raw = $this->_modelEmail->GetEmailRawList(100);
        if (empty($emails_raw)) die('no raw emails');
                
        $modelEmail         = new Email();
        $modelEmailFilter   = new EmailFilter();
        foreach ($emails_raw as $email_raw)
        {
            // заменяет алиасы атачментов на их пути
            $email_raw = $this->_replace_cids($email_raw);
            
            // создает новое письмо на основании сырого
            $email = $modelEmail->CreateFromRaw($email_raw);
            
            // применяет фильтры к новому письму
            if (isset($email['id']))
            {
                $efilters = $modelEmailFilter->GetListAll();
                
                foreach ($efilters['data'] as $efilter)
                {
                    $modelEmailFilter->Apply($efilter['efilter'], $email);
                }
            }
        }
        
        echo 'okay';
    }
    
    
    /**
     * Сохраняет давнные о письме в БД<br />
     * Если есть вложения - физически создаются файлы
     * @param array $data
     * @return boolean
     */
    private function _save_email($data = array())
    {
        $body = array();

        foreach ($data['body'] as $key => $item)
        {
            $subtype = substr($item['mimetype'], 5);
            
            $body[$subtype] = array('content' => $item['content'], 'size' => $item['size'],);
        }
        
        if (!array_key_exists('plain', $body) && array_key_exists('html', $body))
        {
            $content    = preg_replace('/<style.+\<\/style\>/i', ' ', $body['html']['content']);
            $content    = strip_tags($body['html']['content']);
            $content    = preg_replace('/\s+/', ' ', $content);
            $size       = strlen($content);
            
            $body['plain'] = array('content' => $content, 'size' => $size,);
        }

        $username           = $data['username'];
        $mailbox_name       = $data['mailbox_name'];
        $message_num        = $data['headers']['message_num'];
        $message_id         = $data['headers']['message_id'];
        $sender_email       = $data['headers']['sender_email'];
        $recipient_email    = $data['headers']['recipient_email'];
        $cc                 = $data['headers']['cc'];
        $bcc                = $data['headers']['bcc'];
        $reply_to           = $data['headers']['reply_to'];
        $sender             = $data['headers']['sender'];
        $date               = $data['headers']['date'];
        $date_mail          = $data['headers']['date_mail'];    // вообще непонятно какая дата, непонятно откуда берется :)
        $udate              = $data['headers']['udate'];        // а это unixtimestamp от 'date_mail'
        $subject            = $data['headers']['subject'];
        $text_plain         = array_key_exists('plain', $body) ? $body['plain']['content'] : 'NULL VALUE!';
        $text_html          = array_key_exists('html', $body) ? $body['html']['content'] : 'NULL VALUE!';
        $size_plain         = array_key_exists('plain', $body) ? $body['plain']['size'] : 0;
        $size_html          = array_key_exists('html', $body) ? $body['html']['size'] : 0;
        $recent             = $data['headers']['recent'];
        $unseen             = $data['headers']['unseen'];
        $flagged            = $data['headers']['flagged'];
        $answered           = $data['headers']['answered'];
        $deleted            = $data['headers']['deleted'];
        $draft              = $data['headers']['draft'];
        $is_parsed          = 0;
        $has_attachments    = empty($data['attachments']) ? 0 : 1;
        $status_id          = 1;
        
        $email_raw = $this->_modelEmail->SaveEmailRaw($username, $mailbox_name, $message_num, $message_id, $sender_email, $recipient_email, $cc, $bcc, $reply_to, $sender, $date, $date_mail, $udate, $subject, $text_plain, $text_html, $size_plain, $size_html, $recent, $unseen, $flagged, $answered, $deleted, $draft, $is_parsed, $has_attachments, $status_id);
        
        if (!isset($email_raw['id']))
        {
            Log::AddLine(LOG_EMAIL_GRABBER, 
                'Emails_raw already exists message_id: ' . $message_id . ' sender: ' . $sender_email . ' subject:' . $subject
            );
            
            return FALSE;
        }
        
        if (!empty($data['attachments']))
        {
            $this->_save_attachments($email_raw, $data['attachments'], $text_html);
        }
        
        return TRUE;
    }
    
    
    /**
     * Сохраняет вложения
     * @param array $email_raw
     * @param array $attachments
     */
    private function _save_attachments($email_raw, $attachments = array(), $text_html = '')
    {
        foreach ($attachments as $key => $item)
        {
            $dirpath = $this->_create_temporary_dir();
            
            $filename   = Translit::EncodeAndClear($item['filename']);
            
            $pathinfo   = pathinfo($filename);
            $filename   = !empty($pathinfo['filename']) ? $pathinfo['filename'] : 'noname';
            $extension  = array_key_exists('extension', $pathinfo) ? $pathinfo['extension'] : 'txt';
            $filename   = $filename . '.' . $extension;
            //$attachments[$key]['filename'] = $filename;
            
            $filepath   = $dirpath . $filename;
            
            file_put_contents($filepath, $item['content']);
            
            $object_alias   = 'emailraw';
            $object_id      = $email_raw['id'];
            $path           = $filepath;
            $title          = '';
            // 20120919, zharkov: найден косяк по которому в description попадает cid, которого в тексте письма нет, и атачмент пропадал
            $description    = (strpos($text_html, $item['cid']) === false ? '' : $item['cid']);
            $status         = MODERATE_STATUS_ACTIVE;
            
            $att = $this->_modelAttachment->CreateFromFile($object_alias, $object_id, $path, $title, $description, $status);
        }
    }
    
    /**
     * Создает временную директорию для хранения временных файлов
     * @return string|boolean Путь к директории. В случае неудачи FALSE
     */
    private function _create_temporary_dir()
    {
        $temp_dir = ATTACHMENT_PATH . 'tmp/emailgrabber/' . md5(time()) . '/';
        
        if (is_dir($temp_dir)) return $temp_dir;
        
        $oldmask    = umask(0);
        $res        = mkdir($temp_dir, 0777, true);
        umask($oldmask);
        
        if (!$res) return FALSE;
        
        return  $temp_dir;
    }
    
    /**
     * Извлекает содержимое письма (заголовки, контент, вложения)
     * @param int $imap_stream
     * @param int $msg_number
     * @return array вида
     * array('headers' => $headers, 'body' => $body_data['primary'], 'attachments' => $body_data['attachments'],)
     */
    private function _fetch_email($imap_stream, $msg_number)
    {       
        $headers    = $this->_get_headers($imap_stream, $msg_number);
        $body_data  = $this->_get_body($imap_stream, $msg_number);

        $data_set   = array(
            'headers'       => $headers,
            'body'          => $body_data['primary'],
            'attachments'   => $body_data['attachments'],
        );
        
        return $data_set;
    }
    
    /**
     * Возвращает набор данных заголовка
     * @param int $imap_stream
     * @param int $msg_number
     * @return array вида
     * array('sender_email' => 'host1@m.ru', 'recipient_email' => 'host2@m.ru', 'subject' => $subject_decoded,
     *      'date' => 'YYYY-MM-DD H:i:s', 'message_id' => 123, 'is_readed' => TRUE/FALSE,)
     */
    private function _get_headers($imap_stream, $msg_number)
    {
        $headerinfo         = imap_headerinfo($imap_stream, $msg_number);
        $subject_decoded    = isset($headerinfo->subject) ? $this->_mime_header_decode($headerinfo->subject) : '';
        
        $headers = array(
            'message_id'        => isset($headerinfo->message_id) ? $headerinfo->message_id : '',
            'sender_email'      => $this->_get_emails_from_header($headerinfo, 'from'),
            'recipient_email'   => $this->_get_emails_from_header($headerinfo, 'to'),
            'cc'                => $this->_get_emails_from_header($headerinfo, 'cc'),
            'bcc'               => $this->_get_emails_from_header($headerinfo, 'bcc'),
            'reply_to'          => $this->_get_emails_from_header($headerinfo, 'reply_to'),
            'sender'            => $this->_get_emails_from_header($headerinfo, 'sender'),
            'subject'           => $subject_decoded,
            'message_num'       => (int) $headerinfo->Msgno,
            'recent'            => $headerinfo->Recent == 'R' ? TRUE : FALSE,
            'unseen'            => $headerinfo->Unseen == 'U' ? TRUE : FALSE,
            'flagged'           => $headerinfo->Flagged == 'F' ? TRUE : FALSE,
            'answered'          => $headerinfo->Answered == 'A' ? TRUE : FALSE,
            'deleted'           => $headerinfo->Deleted == 'D' ? TRUE : FALSE,
            'draft'             => $headerinfo->Draft == 'X' ? TRUE : FALSE,
            'date'              => date('Y-m-d H:i:s', strtotime($headerinfo->date)),
            'date_mail'         => date('Y-m-d H:i:s', strtotime($headerinfo->MailDate)),
            'udate'             => (int) $headerinfo->udate,
        );
        
        return $headers;
    }
    
    private function _get_emails_from_header($objectStdClassHeaderInfo, $type)
    {
        if (!in_array($type, array('to', 'from', 'cc', 'bcc', 'reply_to', 'sender'))) return 'nombox@nohost';
        
        if (!isset($objectStdClassHeaderInfo->$type))
        {
            return in_array($type, array('to', 'from')) ? 'nombox@nohost' : '';
        }
        
        $emails_array = array();
        foreach ($objectStdClassHeaderInfo->$type as $objectStdClassEmail)
        {
            $mbox = isset($objectStdClassEmail->mailbox) ? $objectStdClassEmail->mailbox : 'nombox';
            $host = isset($objectStdClassEmail->host) ? $objectStdClassEmail->host : 'nohost';
            
            $email  = strtolower($mbox . '@' . $host);
            $email  = preg_replace('/[\[\]\'"!?+*()&<>,]/ui', '', $email);
            
            $emails_array[] = $email;
        }
        $emails_array   = array_unique($emails_array);// фильтруется на уникальные значения
        $emails_string  = implode(', ', $emails_array);
        
        return $emails_string;
    }
    
    /**
     * Возвращает все тело
     * @param string $imap_stream [source]
     * @param string $msg_number
     * @return array Вида array('primary' => array(), 'attachments' => array(),);
     */
    private function _get_body($imap_stream, $msg_number)
    {
        $structure       = imap_fetchstructure($imap_stream, $msg_number);
        $structure_parts = isset($structure->parts)
            ? $structure->parts
            : array($structure);
        
        // Формируется в _iterate_parts
        $this->_content_of_parts = array();
                
        $this->_iterate_parts($structure_parts, $imap_stream, $msg_number);
        
        $primary        = array();
        $attachments    = array();
        
        foreach ($this->_content_of_parts as $part)
        {
            if (!empty($part['attachment']))
            {
                $attachments[] = array(
                    'type'      => $part['attachment']['type'],
                    'cid'       => $part['attachment']['cid'],
                    'filename'  => $part['attachment']['filename'],
                    'size'      => $part['size'],
                    'mimetype'  => $part['mimetype'],
                    'content'   => $part['content'],
                    
                );
                continue;
            }
            
            $primary[] = $part;
        }
        
        $body = array(
            'primary'       => $primary,
            'attachments'   => $attachments,
        );
        
        return $body;
    }
    
    /**
     * Итератор по частям письма
     * @param type $objectStdClassFetchStructure
     * @param type $imap_stream
     * @param type $msg_number
     * @param type $parent_section_number
     */
    private function _iterate_parts($objectStdClassFetchStructure, $imap_stream, $msg_number, $parent_section_number = '')
    {
        $parts_count = count($objectStdClassFetchStructure);
        $parts_count = empty($parent_section_number) && $parts_count == 0 ? 1 : $parts_count;
        
        for ($i = 0; $i < $parts_count; $i++)
        {
            $section_number = empty($parent_section_number) ? ($i + 1) : $parent_section_number . '.' . ($i + 1);
            
//TODO: реализовать обработку данного 'two composite top-level media type' (http://tools.ietf.org/html/rfc2046)
// пока исключим
            if ($objectStdClassFetchStructure[$i]->type == 2) continue; //top-level media type: MESSAGE
            
//TODO: реализовать обработку данного subtype
// пока исключим
            if (strtoupper($objectStdClassFetchStructure[$i]->subtype) == 'APPLEFILE') continue;
            
            if (isset($objectStdClassFetchStructure[$i]->parts))
            {
                $this->_iterate_parts($objectStdClassFetchStructure[$i]->parts, $imap_stream, $msg_number, $section_number);
                continue;
            }
            
            $data = $this->_fetch_part_content($imap_stream, $msg_number, $section_number);
            
            if (is_null($data)) continue;
            
            $this->_content_of_parts[$section_number] = $data;
        }
    }
    
    /**
     * Извлекает "сырой" контент из частей письма.<br />
     * Формирует набор данных о разбираемой части
     * @param int $imap_stream
     * @param int $msg_number
     * @param mixed $section [int, string]
     * @return array вида
     * array('is_attachment' => $is_attachment, 'mimetype' => $mimetype, 'content' => $content, 'size' => $size);
     */
    private function _fetch_part_content($imap_stream, $msg_number, $section)
    {
        $objectStdClassBodyStruct = imap_bodystruct($imap_stream, $msg_number, $section);
        
        // Получение контента вложения
        // и/или IMAGE-контента встроенного в ОсновноеТело
        if (($objectStdClassBodyStruct->ifdisposition && strtoupper($objectStdClassBodyStruct->disposition) == 'ATTACHMENT')
            || ($objectStdClassBodyStruct->type == 5))
        {
            $raw_content    = imap_fetchbody($imap_stream, $msg_number, $section);
            $encoding_id    = $objectStdClassBodyStruct->encoding;
            $content        = $this->_decode_raw_content($raw_content, $encoding_id);
            //$content        = $raw_content;
            
            $mimetype       = $objectStdClassBodyStruct->type == 5 ? strtolower('image/' . $objectStdClassBodyStruct->subtype) : '';
            $is_attachment  = TRUE;
            
            $content_length = mb_strlen($content);
            if ($content_length == 0) return NULL;
            
            $size           = isset($objectStdClassBodyStruct->bytes) ? $objectStdClassBodyStruct->bytes : $content_length;
            
            $raw_filename   = $this->_get_parameter($objectStdClassBodyStruct, 'name');
            
            if (is_null($raw_filename) || empty($raw_filename))
            {
                $raw_filename   = $this->_get_dparameter($objectStdClassBodyStruct, 'filename');
                
                $subtype = strtolower($objectStdClassBodyStruct->subtype);
                
                if ($objectStdClassBodyStruct->type == 5)
                {
                    $extension = $subtype;
                }
                else
                {
                    switch ($subtype)
                    {
                        case 'msword':
                            $extension = 'doc';
                            break;
                        
                        default: 'txt';
                    }
                }
                
                $raw_filename   = (is_null($raw_filename) || empty($raw_filename)) ? 'noname_' . $section . '.' . $extension : $raw_filename;
            }
            
            $filename       = $this->_mime_header_decode($raw_filename);
            $filename       = Translit::EncodeAndClear($filename);
            
            $cid = NULL;
            if ($objectStdClassBodyStruct->ifid)
            {
                $cid = $objectStdClassBodyStruct->id;
                $cid = str_replace('<', '', $cid);
                $cid = str_replace('>', '', $cid);
                $cid = 'cid:' . $cid;
            }
            
            $attachment = array(
                'type'      => $objectStdClassBodyStruct->type == 5 ? 'html' : 'single',
                'cid'       => $cid,
                'filename'  => $filename
            );
        }
        // Получение контента ОсновногоТела
        else if ($objectStdClassBodyStruct->type == 0 && in_array($objectStdClassBodyStruct->subtype, array('HTML', 'PLAIN')))
        {
            $raw_content    = imap_fetchbody($imap_stream, $msg_number, $section);
            
            $encoding_id    = $objectStdClassBodyStruct->encoding;
            $content        = $this->_decode_raw_content($raw_content, $encoding_id);
            
            $encoding_from  = $this->_get_parameter($objectStdClassBodyStruct, 'charset');
            $content        = $this->_convert_content($content, $encoding_from, 'utf-8');
            
            $is_attachment  = FALSE;
            $attachment     = array();
            $mimetype       = strtolower('text/' . $objectStdClassBodyStruct->subtype);
            $size           = isset($objectStdClassBodyStruct->bytes) ? $objectStdClassBodyStruct->bytes : mb_strlen($content);
            $filename       = '';
        }
        
        else
        {
            return NULL;
        }
        
        return array(
            'attachment'    => $attachment,
            'mimetype'      => $mimetype,
            'size'          => $size,
            'content'       => $content,
        );
    }
    
    /**
     * Декодирует строку MIME-HEADER
     * @param string $encoded_string
     * @return string
     */
    private function _mime_header_decode($encoded_string)
    {
        $objects        = imap_mime_header_decode($encoded_string);
        $decoded_string = '';
        
        foreach ($objects as $object)
        {
            $charset    = strtoupper($object->charset);
            $text       = $object->text;
            
            if (!$this->_is_encoding_supported($charset))
            {
                $charset = mb_detect_encoding($text, 'ASCII, UTF-8, CP1251');
                
                if (!in_array(strtoupper($charset), array('ASCII', 'UTF-8', 'CP1251', 'WINDOWS-1251')))
                {
                    $charset = mb_detect_encoding($text, 'ISO-8859-1, ASCII, UTF-8');
                }
            }
            
            $decoded_string .= mb_convert_encoding($text, 'UTF-8', $charset);            
        }
        
        return $decoded_string;
        

/* before 20130618

        $objectStdClassMimeHeaderDecode = imap_mime_header_decode($encoded_string);
        $decoded_string = '';
        
        foreach ($objectStdClassMimeHeaderDecode as $objectItem)
        {
            $objectItem->charset = strtoupper($objectItem->charset);
            
            if (!$this->_is_encoding_supported($objectItem->charset))
            {
                $objectItem->charset = mb_detect_encoding($objectItem->text, 'ASCII, UTF-8, CP1251');
                
                if (!in_array(strtoupper($objectItem->charset), array('ASCII', 'UTF-8', 'CP1251', 'WINDOWS-1251')))
                {
                    $objectItem->charset = mb_detect_encoding($objectItem->text, 'ISO-8859-1, ASCII, UTF-8');
                }
                
                $decoded_string .= mb_convert_encoding($objectItem->text, 'UTF-8', $objectItem->charset);
            }
        }
        
        return $decoded_string;
*/        
    }
    
    
    /**
     * Декодирует "сырой" контент в "человеческий" вид
     * @param string $raw_content
     * @param string $encoding_id
     * @return sting
     */
    private function _decode_raw_content($raw_content, $encoding_id)
    {
        switch ($encoding_id)
        {
            case 0:// body transfer encoding 7BIT
                return $raw_content;
                
            case 1:// body transfer encoding 8BIT
                return $raw_content;
//                return imap_8bit($raw_content);
                
            case 2:// body transfer encoding BINARY
                return imap_binary($raw_content);
                
            case 3:// body transfer encoding BASE64
                return imap_base64($raw_content);
                
            case 4:// body transfer encoding QUOTED-PRINTABLE
                return imap_qprint($raw_content);
                
            case 5:// body transfer encoding OTHER
                return $raw_content;
                
            default:
                return $raw_content;
        }
    }
    
    /**
     * Конвертирует контент в необходимую кодировку (по умольчанию в utf-8)
     * @param string $content
     * @param string $encoding_from
     * @param string $encoding_to
     * @return string Контент
     */
    private function _convert_content($content, $encoding_from, $encoding_to = 'utf-8')
    {
        $encoding_from  = strtoupper($encoding_from);
        $encoding_to    = strtoupper($encoding_to);
        
        if ($encoding_from == $encoding_to) return $content;
        
        if (!$this->_is_encoding_supported($encoding_from))
        {
            $encoding_from = mb_detect_encoding($content, 'ISO-8859-1, ASCII, UTF-8, CP1251');
        }
        
        return mb_convert_encoding($content, $encoding_to, $encoding_from);
    }
    
    /**
     * Возвращает значение параметра по имени
     * @param stdClass $objectStdClass
     * @param string $name
     * @return mixed При неудаче NULL
     */
    private function _get_parameter($objectStdClass, $name)
    {
        if (!($objectStdClass instanceof stdClass)) return NULL;
        
        if (!$objectStdClass->ifparameters) return NULL;
        
        $value  = NULL;
        $name   = strtoupper($name);
        
        foreach ($objectStdClass->parameters as $parameter)
        {
            if (strtoupper($parameter->attribute) != $name) continue;
            
            $value = $parameter->value;
            break;
        }
        
        return $value;
    }
    
    /**
     * Возвращает значение d-параметра по имени
     * @param stdClass $objectStdClass
     * @param string $name
     * @return mixed При неудаче NULL
     */
    private function _get_dparameter($objectStdClass, $name)
    {
        if (!($objectStdClass instanceof stdClass)) return NULL;
        
        if (!$objectStdClass->ifdparameters) return NULL;
        
        $value  = NULL;
        $name   = strtoupper($name);
        
        foreach ($objectStdClass->dparameters as $parameter)
        {
            if (strtoupper($parameter->attribute) != $name) continue;
            
            $value = $parameter->value;
            break;
        }
        
        return $value;
    }
    
    /**
     * Проверяет, поддерживается кодировка библиотекой mb_string
     * @param type $encoding
     * @return boolean
     */
    private function _is_encoding_supported($encoding)
    {
//        $standart_encodings = array('UCS-4', 'UCS-4BE', 'UCS-4LE', 'UCS-2', 'UCS-2BE', 'UCS-2LE',
//            'UTF-32', 'UTF-32BE', 'UTF-32LE', 'UTF-16', 'UTF-16BE', 'UTF-16LE', 'UTF-7', 'UTF7-IMAP', 'UTF-8',
//            'ASCII', 'EUC-JP', 'SJIS', 'eucJP-win', 'SJIS-win', 'ISO-2022-JP', 'ISO-2022-JP-MS', 'CP932','CP51932',
//            'SJIS-mac', 'SJIS-Mobile#DOCOMO', 'SJIS-Mobile#KDDI', 'SJIS-Mobile#SOFTBANK',
//            'UTF-8-Mobile#DOCOMO', 'UTF-8-Mobile#KDDI-A', 'UTF-8-Mobile#KDDI-B', 'UTF-8-Mobile#SOFTBANK', 'ISO-2022-JP-MOBILE#KDDI',
//            'JIS', 'JIS-ms', 'CP50220', 'CP50220raw', 'CP50221', 'CP50222',
//            'ISO-8859-1', 'ISO-8859-2', 'ISO-8859-3', 'ISO-8859-4', 'ISO-8859-5',
//            'ISO-8859-6', 'ISO-8859-7', 'ISO-8859-8', 'ISO-8859-9', 'ISO-8859-10',
//            'ISO-8859-13', 'ISO-8859-14', 'ISO-8859-15',
//            'byte2be', 'byte2le', 'byte4be', 'byte4le', 'BASE64', 'HTML-ENTITIES',
//            '7bit', '8bit', 'EUC-CN', 'CP936', 'GB18030', 'HZ', 'EUC-TW', 'CP950',
//            'BIG-5', 'EUC-KR',
//            'UHC', 'CP949',
//            'ISO-2022-KR',
//            'Windows-1251', 'CP1251',
//            'Windows-1252', 'CP1252',
//            'CP866', 'IBM866',
//            'KOI8-R',
//        );
        $supported_character_encodings = array('UCS-4', 'UCS-4BE', 'UCS-4LE', 'UCS-2', 'UCS-2BE', 'UCS-2LE',
            'UTF-32', 'UTF-32BE', 'UTF-32LE', 'UTF-16', 'UTF-16BE', 'UTF-16LE', 'UTF-7', 'UTF7-IMAP', 'UTF-8',
            'ASCII', 'EUC-JP', 'SJIS', 'EUCJP-WIN', 'SJIS-WIN', 'ISO-2022-JP', 'ISO-2022-JP-MS', 'CP932','CP51932',
            'SJIS-MAC', 'SJIS-MOBILE#DOCOMO', 'SJIS-MOBILE#KDDI', 'SJIS-MOBILE#SOFTBANK',
            'UTF-8-MOBILE#DOCOMO', 'UTF-8-MOBILE#KDDI-A', 'UTF-8-MOBILE#KDDI-B', 'UTF-8-MOBILE#SOFTBANK', 'ISO-2022-JP-MOBILE#KDDI',
            'JIS', 'JIS-MS', 'CP50220', 'CP50220RAW', 'CP50221', 'CP50222',
            'ISO-8859-1', 'ISO-8859-2', 'ISO-8859-3', 'ISO-8859-4', 'ISO-8859-5',
            'ISO-8859-6', 'ISO-8859-7', 'ISO-8859-8', 'ISO-8859-9', 'ISO-8859-10',
            'ISO-8859-13', 'ISO-8859-14', 'ISO-8859-15',
            'BYTE2BE', 'BYTE2LE', 'BYTE4BE', 'BYTE4LE', 'BASE64', 'HTML-ENTITIES',
            '7BIT', '8BIT', 'EUC-CN', 'CP936', 'GB18030', 'HZ', 'EUC-TW', 'CP950',
            'BIG-5', 'EUC-KR',
            'UHC', 'CP949',
            'ISO-2022-KR',
            'WINDOWS-1251', 'CP1251',
            'WINDOWS-1252', 'CP1252',
            'CP866', 'IBM866',
            'KOI8-R',
        );
        $non_standart_encodings = array('DEFAULT', '3D', 'WINDOWS-1250');
        
        $encoding = strtoupper($encoding);
        
        if (in_array($encoding, $supported_character_encodings)) return TRUE;
        
        return FALSE;
    }
    
    
    /**
     * Сохраняет проанализированное "сырое" email-сообщение
     * в таблицу email
     * @param array $email_raw
     * @return array Объект-ассоциативный массив данных об $email
     */
    private function _email_save($email_raw)
    {
        $query = "SELECT * FROM `emails` WHERE `email_raw_id` = '" . $email_raw['id'] ."';";
        $data_set = $this->_modelEmail->table->_exec_raw_query($query);
        $email = isset($data_set[0]) && isset($data_set[0][0]) ? $data_set[0][0] : array();
        
        if (array_key_exists('id', $email)) return $email;
        
        if (!empty($email_raw['text_html'])) $email_raw['text_html'] = $this->_replace_cids($email_raw['id'], $email_raw['text_html']);
        {
            $this->_replace_cids($email_raw);
        }
        
        // start разбор множества email. Получаем только один (первый)
        $sender_address_comaposition    = strpos($email_raw['sender_email'], ',');
        $recipient_address_comaposition = strpos($email_raw['recipient_email'], ',');
        // start разбор множества email. Получаем только один (первый)
        
        $sender_address     = $sender_address_comaposition ? substr($email_raw['sender_email'], 0, $sender_address_comaposition) : $email_raw['sender_email'];
        $recipient_address  = $recipient_address_comaposition ? substr($email_raw['recipient_email'], 0, $recipient_address_comaposition) : $email_raw['recipient_email'];
            
        $sender_email_account       = $this->_modelEmail->GetMailboxByEmail($sender_address);
        $recipient_email_account    = $this->_modelEmail->GetMailboxByEmail($recipient_address);
        
        if (array_key_exists('id', $recipient_email_account))
        {
            $email_type_id = EMAIL_TYPE_INBOX;
        }
        else if (array_key_exists('id', $sender_email_account))
        {
            $email_type_id = EMAIL_TYPE_OUTBOX;
        }
        else
        {
//TODO: Подставить нормальную константу
            $email_type_id = EMAIL_TYPE_ERROR;
        }

        
//TODO: реализовать нахождение информации для 'to', 'attention', 'subject', 'our_ref', 'your_ref'
// из тела письма
        
        
        $insert_row_id = $this->_modelEmail->Insert(array(
            'email_raw_id'      => $email_raw['id'],
            'type_id'           => $email_type_id,
            'object_alias'      => 'NULL VALUE!',
            'object_id'         => 'NULL VALUE!',
            'sender_address'    => $sender_address,
            'recipient_address' => $recipient_address,
            'to'                => '',
            'attention'         => '',
            'subject'           => '',
            'our_ref'           => '',
            'your_ref'          => '',
            'title'             => $email_raw['subject'],
            'description'       => $email_raw['text_plain'],
            'description_html'  => $email_raw['text_html'],
            'signature'         => 'NULL VALUE!',
            'number'            => 'NULL VALUE!',
            'date_mail'         => $email_raw['date_mail'],
            'created_at'        => 'NOW()!',
            'created_by'        => 0,
            'modified_at'       => 'NOW()!',
            'modified_by'       => 0,
            'sent_at'           => 'NULL VALUE!',
            'sent_by'           => 'NULL VALUE!',
        ));
        
        if (!$insert_row_id) return array();
        
        $query = "SELECT * FROM emails WHERE id = " . $insert_row_id .";";
        
        $data_set = $this->_modelEmail->table->_exec_raw_query($query);
        
        $email = isset($data_set[0]) ? (isset($data_set[0][0]) ? $data_set[0][0] : array()) : array();
        
        // связывание аттачментов с новыми объектами 'email' и 'emailhtml'
        $query = "
            INSERT IGNORE INTO `attachment_objects` (`attachment_id`,`type`,`object_alias`,`object_id`,`created_at`,`created_by`)
                SELECT ao.`attachment_id`,ao.`type`,CASE WHEN a.`description` != '' THEN 'emailhtml' ELSE 'email' END,'" . $email['id'] ."',NOW(),'0'
                FROM `attachment_objects` AS ao
                LEFT JOIN `attachments` AS a ON a.`id` = ao.`attachment_id`
                WHERE ao.`type` = 'file' AND ao.`object_alias` = 'emailraw' AND ao.`object_id` = '" . $email_raw['id'] ."'
        ;";
        $this->_modelAttachment->table->_exec_raw_query($query);
        
        return $email;
    }
    
    /**
     * Заменяtт значения атрибута src тега IMG cid->url в html-контенте письма
     * @param array $email_raw Поссылке
     * @return boolean
     * 
     * @version 20120912, zharkod: переделал на "по значению"
     */
    private function _replace_cids($email_raw)
    {
        if (isset($email_raw['id']) && isset($email_raw['text_html']) && !empty($email_raw['text_html']))
        {
            $page_no        = 1;
            $per_page       = 1000;
            
            $data_set       = $this->_modelAttachment->GetList('emailraw', $email_raw['id'], $page_no, $per_page);
            $attachments    = isset($data_set['data']) && !empty($data_set['data']) ? $data_set['data'] : array();
            
            foreach ($attachments as $item)
            {
                $attachment = $item['attachment'];                
                if (!strstr($attachment['description'], 'cid')) continue;
                
                $replace = ATTACHMENT_HOST . '/file/' . $attachment['secret_name'] . '/' . $attachment['original_name'];
                $email_raw['text_html'] = str_replace($attachment['description'], $replace, $email_raw['text_html']);
            }
        }
        
        return $email_raw;        
    }
    
    /**
     * Ананлизирует BIZ-emails и извлекает данные
     * @param array $bizes_list
     * @param array $parsing_object
     */
    private function _parse_bizes($parsing_object)
    {
        $pattern    = '/biz(\d{4}(\.\d{2})?)/ui';
        $subject    = $parsing_object['parsing_string'];
        
        preg_match_all($pattern, $subject, $matches);
        
        if (empty($matches[0])) return FALSE;
        if (!isset($matches[1][0])) return FALSE;
        
        $bizes_numbers = array_unique($matches[1]);
        
        $query = "";
        
        // start поиск бизнесов по всему письму
        foreach ($bizes_numbers as $item)
        {
            $exploded_item = explode('.', $item);
            
            if (!isset($exploded_item[0])) continue;
            
            $number = $exploded_item[0];
            $suffix = isset($exploded_item[1]) ? $exploded_item[1] : 0;
            
            // привязка письма к i-ому бизнесу
            $query .= "INSERT IGNORE INTO `email_objects` (`email_id`,`object_alias`,`object_id`,`created_at`,`created_by`)
                SELECT '" . $parsing_object['id'] . "','biz',b.`id`,NOW(),'0'
                FROM `bizes` AS b
                WHERE b.`number` = '" . $number . "' AND b.`suffix` = '" . $suffix . "';\n";
            
            // привязка письма к пользователям, которые привязаны к i-ому бизнесу
            $query .= "INSERT IGNORE INTO `email_users` (`email_id`,`user_id`,`relation_id`,`created_at`,`created_by`)
                SELECT '" . $parsing_object['id'] . "',bu.`user_id`,(CASE WHEN bu.`is_driver`= '1' THEN '1' ELSE '2' END),NOW(),'0'
                FROM `biz_users` AS bu
                WHERE bu.`biz_id` IN (SELECT b.`id` FROM `bizes` AS b WHERE b.`number` = '" . $number . "' AND b.`suffix` = '" . $suffix . "');\n";
        }
        // end поиск бизнесов по всему письму
        
        $this->_modelEmail->table->_exec_raw_query($query);
    }
    
    /**
     * Анализирует данные email-сообщения
     * Извлекает email-адреса и сохраняет данные
     * @param array $parsing_object
     */
    private function _parse_email_addresses($parsing_object)
    {
        $pattern    = '/[a-zA-Z0-9._-]+@[a-zA-Z0-9.]*[a-zA-Z]{2,6}/i';
        $subject    = strtolower($parsing_object['parsing_string']);
        preg_match_all($pattern, $subject, $matches);
        
        if ($matches && (!isset($matches[0]) || empty($matches[0])))
        {
//            $this->_modelEmail->table->_exec_raw_query($query);
            return TRUE;
        }
        
        $matched_emails  = array_unique($matches[0]);// фильтруется на уникальные значения
        $matched_email  = "'" . implode("','", $matched_emails) . "'";
        
        $query = "";
        
        // все найденные emails
        $query .= "INSERT IGNORE INTO `email_objects` (`email_id`,`object_alias`,`object_id`,`created_at`,`created_by`)
            SELECT '" . $parsing_object['id'] . "',cd.`object_alias`,cd.`object_id`,NOW(),'0'
            FROM `contactdata` AS cd
            WHERE cd.`type` = 'email' AND cd.`title` IN (" . $matched_email . ");\n";
        
        // если среди всех найденных emails есть 'PERSON', то находим компанию для каждого из них
        $query .= "INSERT IGNORE INTO `email_objects` (`email_id`,`object_alias`,`object_id`,`created_at`,`created_by`)
            SELECT '" . $parsing_object['id'] . "','company',IFNULL((SELECT p.`company_id` FROM `persons` AS p WHERE p.`id`=cd.`object_id`),0),NOW(),'0'
            FROM `contactdata` AS cd
            WHERE cd.`type` = 'email' AND cd.`title` IN (" . $matched_email . ") AND cd.`object_alias` = 'person';\n";
        
        // если среди всех найденных emails есть 'MAILBOX', то связываем письмо с ним
        $query .= "INSERT IGNORE INTO `email_mailboxes` (`email_id`,`mailbox_id`,`created_at`,`created_by`)
            SELECT '" . $parsing_object['id'] . "',mb.`id`,NOW(),'0'
            FROM `mailboxes` AS mb
            WHERE mb.`title`  IN (" . $matched_email . ");\n";
        
        $this->_modelEmail->table->_exec_raw_query($query);
    }
    
    
    /**
     * Анализирует данные email-сообщения
     * Извлекает компании и сохраняет данные
     * @param array $companies_list
     * @param array $parsing_object
     */
    private function _parse_companies($companies_list, $parsing_object)
    {
        foreach ($companies_list as $company)
        {
            $word_set_original_string   = $company['title'] . ' ' . $company['title_native'] . ' ' . $company['title_short'] . ' ' . $company['title_trade'];
            //$word_set_original_string   = 'ООО Квадрософт КвадроСофт KSE Kvadrosoft ltd';
            $word_set_original_string   = $this->_cleanup_string($word_set_original_string);

            if (empty($word_set_original_string)) continue;
            
            $company_title_set_exploded = explode(' ', $word_set_original_string);
            
            // start преобразование текста в массив по словам
            $parsing_string = $parsing_object['parsing_string'];
            //$parsing_string = 'Меня зовут Завьялов Денис. Я работаю в "Квадрософт" ООО. В Kvadrosoft я занимаюсь web программированием.    <I> живу в квадрософт.';
            $parsing_string = $this->_cleanup_string($parsing_string);
            $parsing_string_exploded    = explode(' ', $parsing_string);
            // end преобразование текстав в массив по словам
            
            $text       = $parsing_string_exploded;
            $searching  = $company_title_set_exploded;
            
            if (!$this->_is_object_exists($text, $searching)) continue;
            
            $email_id       = $parsing_object['id'];
            $object_alias   = 'company';
            $object_id      = $company['id'];
            $email_object   = $this->_modelEmail->SaveEmailObject($email_id, $object_alias, $object_id);
        }
    }
    
    /**
     * Анализирует данные email-сообщения
     * Извлекает персоны и сохраняет данные
     * @param array $persons_list
     * @param array $parsing_object
     * @return boolean
     */
    private function _parse_persons($persons_list, $parsing_object)
    {
        foreach ($persons_list as $person)
        {
            
            $word_set_original_string   = $person['first_name'] . ' ' . $person['middle_name'] . ' ' . $person['last_name'];
            //$word_set_original_string   = 'ООО Квадрософт КвадроСофт KSE Kvadrosoft ltd';
            $word_set_original_string   = $this->_cleanup_string($word_set_original_string);
            
            if (empty($word_set_original_string)) continue;
            
            $company_title_set_exploded = explode(' ', $word_set_original_string);
            
            // start преобразование текста в массив по словам
            $parsing_string = $parsing_object['parsing_string'];
            //$parsing_string = 'Меня зовут Завьялов Денис. Я работаю в "Квадрософт" ООО. В Kvadrosoft я занимаюсь web программированием.    <I> живу в квадрософт.';
            $parsing_string = $this->_cleanup_string($parsing_string);
            $parsing_string_exploded    = explode(' ', $parsing_string);
            // end преобразование текстав в массив по словам
            
            $text       = $parsing_string_exploded;
            $searching  = $company_title_set_exploded;
            
            if (!$this->_is_object_exists($text, $searching)) continue;
            
            $email_id       = $parsing_object['id'];
            $object_alias   = 'person';
            $object_id      = $person['id'];
            $email_object   = $this->_modelEmail->SaveEmailObject($email_id, $object_alias, $object_id);
            
            if ($person['company_id'] <= 0) continue;
            
            // если персона связана с компанией, связжем компанию с этим письмом
            $email_id       = $parsing_object['id'];
            $object_alias   = 'company';
            $object_id      = $person['company_id'];
            $email_object   = $this->_modelEmail->SaveEmailObject($email_id, $object_alias, $object_id);
        }
    }
    
    /**
     * Проверяет присутствует ли искомая фраза в тексте
     * поиск по сеседям
     * @param array() $text
     * @param type $searching
     * @return boolean
     */
    private function _is_object_exists($text, $searching)
    {
        $intersected_words = array_intersect($text, $searching);
        $intersected_words = array_filter($intersected_words, create_function('$item','return mb_strlen($item) > 3;'));
        
        foreach ($intersected_words as $key => $item)
        {
            $phrase = array($key => $item);
            
            $lneighbors = array();
            $rneighbors = array();

            for ($i = 1; $i <= 3; $i++)
            {
                $ln_index = $key + (-1) * $i;
                $rn_index = $key + (+1) * $i;

                $ln_item    = array_key_exists($ln_index, $text) ? $text[$ln_index] : FALSE;
                $rn_item    = array_key_exists($rn_index, $text) ? $text[$rn_index] : FALSE;
                
                // если в исходном тексте не найдено ни одно совпадение (ни слева, ни справа),
                // переходим к следующему ключевому слову
                if (!$ln_item && !$rn_item) break;
                
                // если слово слева найдено и присутствует в строке поиска
                // и если предидущий сосед тоже был найден, составляется фраза влево
                if ($ln_item && in_array($ln_item, $searching) && (array_key_exists($ln_index+1, $phrase) && $phrase[$ln_index+1]))
                {
                    $phrase[$ln_index] = $ln_item;
                }
                
                // если слово слева найдено и присутствует в строке поиска
                // и если предидущий сосед тоже был найден, составляется фраза вправо
                if ($rn_item && in_array($rn_item, $searching) && (array_key_exists($rn_index-1, $phrase) && $phrase[$rn_index-1]))
                {
                    $phrase[$rn_index] = $rn_item;
                }
            }
            
            ksort($phrase);
            
            // если количество слов во фразе не менее 2, УСПЕХ
            if (count($phrase) >= 2) return TRUE;
        }
        
        return FALSE;
    }
    
    private function _cleanup_string($string)
    {
        $string   = preg_replace('/[\[\]\'"!?+*()&<>,.]/ui', ' ', $string);
        $string   = preg_replace('/\s+/u', ' ', $string);
        $string   = trim($string, ' ');
        $string   = mb_strtolower($string);
        
        return $string;
    }
    
        
    private function init()
    {
        $this->_modelEmail      = new Email();
        $this->_modelAttachment = new Attachment();
    }
}
