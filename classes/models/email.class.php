<?php
require_once APP_PATH . 'classes/components/object.class.php';

require_once APP_PATH . 'classes/models/attachment.class.php';
require_once APP_PATH . 'classes/models/biz.class.php';
require_once APP_PATH . 'classes/models/company.class.php';
require_once APP_PATH . 'classes/models/contactdata.class.php';
require_once APP_PATH . 'classes/models/mailbox.class.php';
require_once APP_PATH . 'classes/models/order.class.php';
require_once APP_PATH . 'classes/models/person.class.php';
require_once APP_PATH . 'classes/models/qc.class.php';
require_once APP_PATH . 'classes/models/sc.class.php';
require_once APP_PATH . 'classes/models/stockoffer.class.php';
require_once APP_PATH . 'classes/models/ra.class.php';

define('EMAIL_TYPE_INBOX',  1);
define('EMAIL_TYPE_OUTBOX', 2);
define('EMAIL_TYPE_DRAFT',  3);
define('EMAIL_TYPE_ERROR',  4);     // когда нет адреса, зарегистрированного в mailboxes среди оправителей и получателей письма
define('EMAIL_TYPE_SPAM',   5);     // когда у входящего письма адрес отправителя отсутствует в contactdata

define('EMAIL_RELATION_DRIVER',     1);
define('EMAIL_RELATION_NAVIGATOR',  2);

class Email extends Model
{
    function __construct()
    {
        Model::Model('emails');
    }

    
    /**
    * Помечает письмо как спам
    * 
    * @param mixed $email_id
    * @param mixed $sender_address
    * 
    * @version 20120921
    */
    function Spam($email_id, $sender_address)
    {
        $addresses = $this->ExctractAddress($sender_address);        
        
        if (empty($addresses))
        {
            $address    = ''; 
            $is_strict  = 1;
            
            $this->CallStoredProcedure('sp_email_spam', array($this->user_id, $email_id, $address, $is_strict));
        }
        else
        {
            $modelMailbox = new Mailbox();
            foreach ($addresses as $address)
            {
                // check if sender address is one of the company addresses
                // if so only current email become spam, otherwise all emails from $address
                $mailbox        = $modelMailbox->GetByAddress($address);
                $is_strict      = empty($mailbox) ? 0 : 1;

                $this->CallStoredProcedure('sp_email_spam', array($this->user_id, $email_id, $address, $is_strict));
            }            
        }

        Cache::ClearTag('email-'        . $email_id);            
        Cache::ClearTag('emails-type-'  . EMAIL_TYPE_SPAM);
        Cache::ClearTag('emails-type-'  . EMAIL_TYPE_INBOX);
        Cache::ClearTag('emails-type-'  . EMAIL_TYPE_OUTBOX);
        
        // неудачное решение, но надо чтобы типы писем обновились у всех затронутых в операции
        Cache::ClearTag('emails');
                
        // тут можно обновлять значения ключа 'email-spam-count' количеством обработанных писем, но пока не будем
        Cache::ClearTag('email-spam-count');
    }

    /**
     * Помечает письмо как не спам
     * 
     * @param mixed $email_id
     * @param mixed $sender_address
     * 
     * @version 20120921, zharkov
     */
    function NotSpam($email_id, $sender_address)
    {
        $addresses = $this->ExctractAddress($sender_address);        
        
        if (empty($addresses))
        {
            $address    = ''; 
            $domain     = '';
            $is_strict  = 1;
            
            $this->CallStoredProcedure('sp_email_notspam', array($this->user_id, $email_id, $address, $domain, $is_strict));
        }
        else
        {
            $modelMailbox = new Mailbox();
            foreach ($addresses as $address)
            {
                // check if sender address is one of the company addresses
                // if so only current email become spam, otherwise all emails from $address
                $mailbox        = $modelMailbox->GetByAddress($address);
                $is_strict      = empty($mailbox) ? 0 : 1;

                list($username, $domain) = explode('@', $address);
                
                $this->CallStoredProcedure('sp_email_notspam', array($this->user_id, $email_id, $address, $domain, $is_strict));
            }            
        }
        
        Cache::ClearTag('email-'        . $email_id);
        Cache::ClearTag('emails-type-'  . EMAIL_TYPE_SPAM);
        Cache::ClearTag('emails-type-'  . EMAIL_TYPE_INBOX);
        Cache::ClearTag('emails-type-'  . EMAIL_TYPE_OUTBOX);
        
        // неудачное решение, но надо чтобы типы писем обновились у всех затронутых в операции
        Cache::ClearTag('emails');        
                
        // тут можно обновлять значения ключа 'email-spam-count' количеством обработанных писем, но пока не будем
        Cache::ClearTag('email-spam-count');
    }
    
    /**
     * Помечает письмо как УДАЛЕНО
     * 
     * @param int $email_id
     * 
     * @version 2011024, d10n
     */
    public function DeleteSpam($email_id)
    {
        $this->CallStoredProcedure('sp_email_delete', array($this->user_id, $email_id));
        
        Cache::ClearTag('email-'        . $email_id);
        Cache::ClearTag('emails-type-'  . EMAIL_TYPE_SPAM);
        Cache::ClearTag('emails-type-'  . EMAIL_TYPE_INBOX);
        Cache::ClearTag('emails-type-'  . EMAIL_TYPE_OUTBOX);
        
        // неудачное решение, но надо чтобы типы писем обновились у всех затронутых в операции
        Cache::ClearTag('emails');        
                
        // тут можно обновлять значения ключа 'email-spam-count' количеством обработанных писем, но пока не будем
        Cache::ClearTag('email-spam-count');
    }

    /**
     * Проверяет наличие адреса в списке не-спам адресов
     * 
     * @param mixed $email
     * @return true - если адерс не найден в разрешенных списках, false - если найден
     * 
     * @version 20120921, zharkov
     */
    function CheckIsSpam($email)
    {
        $hash       = 'notspam-' . $email;
        $cache_tags = array($hash);
		
		//@version 29.04.13, Sasha
		list($username, $mail_domain) = explode('@', $email);
		
        $rowset = $this->_get_cached_data($hash, 'sp_email_notspam_check', array($mail_domain), $cache_tags);
        return isset($rowset[0]) && isset($rowset[0][0]) ? false : true;
    }    
    
    /**
     * Извлекает первый адрес из строки вида "Dima Zharkov" <dima.xharkov@gmail.com>, ...
     * 
     * @param mixed $email
     */
    function ExctractAddress($emails)
    {
        preg_match_all('#([a-z0-9_\-]+\.)*[a-z0-9_\-]+@([a-z0-9][a-z0-9\-]*[a-z0-9]\.)+[a-z]{2,4}#is', $emails, $matches);
        return isset($matches) && isset($matches[0]) ? $matches[0] : array();
    }

    /**
     * Возвращает список писем в папке спам
     * @version 20120916, zharkov
     */
    function GetSpamCount()
    {
        if ($count = Cache::GetKey('email-spam-count'))
        {
            return $count;
        }
        else
        {
            $rowset = $this->GetList('', 0, 0, EMAIL_TYPE_SPAM, 0, 0, '', 0);
            //dg($rowset);
            Cache::SetKey('email-spam-count', $rowset['count']);
            
            return $rowset['count'];
        }
    }

    /**
     * Возвращает статистику для объекта
     * 
     * @param mixed $object_alias
     * @param mixed $object_id
     * 
     * @version 20120917, zharkov
     */
    function GetUserStatForObject($object_alias, $object_id)
    {
        $hash       = 'emails-' . md5($this->user_id . '-object_alias-' . $object_alias . '-object_id-' . $object_id . '-stat');
        $cache_tags = array($hash, 'mailboxes', 'emails', 'emails-user-' . $this->user_id . '-stat',   'emails-type-' . EMAIL_TYPE_DRAFT, 'emails-type-' . EMAIL_TYPE_ERROR, 'emails-type-' . EMAIL_TYPE_INBOX, 'emails-type-' . EMAIL_TYPE_OUTBOX, 'emails-type-' . EMAIL_TYPE_SPAM);

        $rowset = $this->_get_cached_data($hash, 'sp_email_get_userstat_for_object', array($this->user_id, $object_alias, $object_id), $cache_tags);
        return isset($rowset[0]) && isset($rowset[0][0]) ? $rowset[0][0] : array();
    }
    
    /**
     * Изменяет тип письма
     * 
     * @param mixed $email_id
     * @param mixed $old_type_id
     * @param mixed $new_type_id
     * 
     * @version 20120914, zharkov
     */
    function ChangeType($email_id, $old_type_id, $new_type_id)
    {
        if ($old_type_id == $new_type_id) return;
        
        $this->CallStoredProcedure('sp_email_change_type', array($this->user_id, $email_id, $new_type_id));
        
        Cache::ClearTag('email-'        . $email_id);
        Cache::ClearTag('emails-type-'  . $old_type_id);
        Cache::ClearTag('emails-type-'  . $new_type_id);
        
        if ($old_type_id == EMAIL_TYPE_SPAM || $new_type_id == EMAIL_TYPE_SPAM)
        {
            $spam_qtty = Cache::GetKey('email-spam-count');
            $spam_qtty = ($old_type_id == EMAIL_TYPE_SPAM ? $spam_qtty-- : $spam_qtty++);
            
            Cache::SetKey('email-spam-count', $spam_qtty);
        }
    }
    
    /**
    * Заполняет список объектов данными
    * 
    * @param mixed $rowset
    */
    function FillObjectInfo($rowset)
    {
        $modelBiz       = new Biz();
        $modelCompany   = new Company();
        $modelCountry   = new Country();
        $modelPerson    = new Person();
        $modelProduct   = new Product();
        $modelRA        = new RA();

        $rowset = $modelPerson->FillPersonMainInfo($modelCompany->FillCompanyInfoShort($modelBiz->FillMainBizInfo($rowset)));
        $rowset = $modelProduct->FillProductInfo($modelCountry->FillCountryInfo($rowset));
//_epd($rowset);
        foreach ($rowset as $key => $row)
        {
            if (!isset($row['alias'])) 
            {
                $rowset[$key]['alias']  = $row['object_alias'];
                $row['alias']           = $row['object_alias'];
            }

            if ($row['alias'] == 'biz')
            {
                $rowset[$key]['title'] = $row['biz']['doc_no_full'];
            }
            else if ($row['alias'] == 'company')
            {
                $rowset[$key]['title'] = $row['company']['doc_no'];
            }
            else if ($row['alias'] == 'person')
            {
                $rowset[$key]['title'] = $row['person']['doc_no'];
            }
            else if ($row['alias'] == 'country')
            {
                $rowset[$key]['title'] = $row['country']['doc_no'];
            }
            else if ($row['alias'] == 'product')
            {
                $rowset[$key]['title'] = $row['product']['doc_no'];
            }
            else if ($row['alias'] == 'order')
            {
                $modelOrder = new Order();
                $order      = $modelOrder->GetById($row['object_id']);
                if (empty($order))
                {
                    unset($rowset[$key]);
                    continue;
                }
                $rowset[$key]['title'] = $order['order']['doc_no_full'];
            }
            else if ($row['alias'] == 'sc')
            {
                $modelSC = new SC();
                $sc      = $modelSC->GetById($row['object_id']);
                
                if (!empty($sc)) $rowset[$key]['title'] = $sc['sc']['doc_no'];
            }
            else if ($row['alias'] == 'qc')
            {
                $modelRA = new QC();
                $qc      = $modelRA->GetById($row['object_id']);
                
                if (!empty($qc)) $rowset[$key]['title'] = $qc['qc']['doc_no'];
            }
            else if ($row['alias'] == 'ra')
            {
                $modelRA = new RA();
                $ra      = $modelRA->GetById($row['object_id']);
                if (empty($ra))
                {
                    unset($rowset[$key]);
                    continue;
                }
                $rowset[$key]['title'] = $ra['ra']['doc_no'];
            }
            else if ($row['alias'] == 'stockoffer')
            {
                $modelStockOffer    = new StockOffer();
                $stockoffer         = $modelStockOffer->GetById($row['object_id']);
                
                if (empty($stockoffer))
                {
                    unset($rowset[$key]);
                    continue;
                }
                
                $rowset[$key]['title'] = $stockoffer['stockoffer']['title'];
            }
        }

        return $rowset;
    }
    
    /**
     * Возвращает список пользователей письма
     * 
     * @param mixed $email_id
     * 
     * @version 20120914, zharkov
     */
    function GetUsersList($email_id)
    {
        $hash       = 'email-' . $email_id . '-users';
        $cache_tags = array($hash, 'emails', 'email-' . 'email-' . $email_id);

        $rowset     = $this->_get_cached_data($hash, 'sp_email_users_get_list', array($email_id), $cache_tags);
        
        $modelUser  = new User();
        return isset($rowset[0]) ? $modelUser->FillUserInfo($rowset[0]) : array();
    }

    /**
     * Возвращает список объектов письма
     * 
     * @param mixed $email_id
     * 
     * @version 20120914, zharkov
     */
    function GetObjectsList($email_id)
    {
        $hash       = 'email-' . $email_id . '-objects';
        $cache_tags = array($hash, 'emails', 'email-' . 'email-' . $email_id);

        $rowset = $this->_get_cached_data($hash, 'sp_email_objects_get_list', array($email_id), $cache_tags);
        $rowset = isset($rowset[0]) ? $rowset[0] : array();
        
        foreach ($rowset as $key => $row) $rowset[$key][$row['object_alias'] . '_id'] = $row['object_id'];
        
        return $rowset;
    }
    
    /**
     * Возвращает список писем
     * 
     * @param mixed $object_alias
     * @param mixed $object_id
     * @param mixed $mailbox_id
     * @param mixed $type_id
     * @param int $doc_type_id
     * @param mixed $is_deleted
     * @param mixed $keyword
     * @param int $approve_by
     * @param mixed $page_no
     * @param mixed $per_page
     * @return mixed
     * 
     * @version 201200912, zharkov
     */
    public function GetList($object_alias, $object_id, $mailbox_id, $type_id, $doc_type_id, $is_deleted, $keyword, $approve_by, $page_no = 0, $per_page = ITEMS_PER_PAGE)
    {
        
        $page_no    = $page_no > 0 ? $page_no : 1;
        $per_page   = $per_page < 1 ? ITEMS_PER_PAGE : $per_page;
        $start      = ($page_no - 1) * $per_page;
        
        $hash       =   'emails-filter-' . md5('object_alias-' . $object_alias . '-object_id-' . $object_id . '-mailbox-' . $mailbox_id .
                        '-type-' . $type_id . '-doctypeid-' . $doc_type_id . '-is_deleted-' . $is_deleted . '-keyword-' . $keyword . '-approve_by-' . $approve_by . '-page-' . $page_no . '-count-' . $per_page);
        
        if (empty($keyword))
        {
            return $this->_get_list($object_alias, $object_id, $mailbox_id, $type_id, $doc_type_id, $is_deleted, $approve_by, $hash, $start, $per_page);
        }
        else
        {
//TODO: реализовать фильтрацию по is_deleted
//TODO: реализовать фильтрацию по approve_by
//TODO: реализовать фильтрацию по doc_type_id

            return $this->_search($object_alias, $object_id, $mailbox_id, $type_id, $keyword, $hash, $start, $per_page);
        }
    }
    
    /**
     * Возвращает список писем по параметрам
     * 
     * @param mixed $object_alias
     * @param mixed $object_id
     * @param mixed $mailbox_id
     * @param mixed $type_id
     * @param mixed $doc_type_id
     * @param mixed $is_deleted
     * @param mixed $approve_by
     * @param mixed $hash
     * @param mixed $start
     * @param mixed $per_page
     * 
     * @version 20120918, zharkov
     */
    function _get_list($object_alias, $object_id, $mailbox_id, $type_id, $doc_type_id, $is_deleted, $approve_by, $hash, $start, $per_page)
    {
       
        $is_admin   = ($this->user_role <= ROLE_ADMIN ? 1 : 0);
        $cache_tags = array($hash, 'emails', 'emails-type-' . $type_id, 'emails-mailbox-' . $mailbox_id, 'emails-mailbox-' . $mailbox_id . '-type-' . $type_id);
        
        $rowset     = $this->_get_cached_data($hash, 'sp_email_get_list', array($this->user_id, $is_admin, 
                        $object_alias, $object_id, $mailbox_id, $type_id, $doc_type_id, $is_deleted, $approve_by, $start, $per_page), $cache_tags);
        
        return array(
            'data'  => isset($rowset[0]) ? $this->FillEmailInfo($rowset[0]) : array(),
            'count' => isset($rowset[1]) && isset($rowset[1][0]) && isset($rowset[1][0]['rows']) ? $rowset[1][0]['rows'] : 0
        );
    }
    
    /**
     * Поиск по почте
     * 
     * @param mixed $object_alias
     * @param mixed $object_id
     * @param mixed $mailbox_id
     * @param mixed $type_id
     * @param mixed $keyword
     * @param mixed $page_no
     * @param mixed $per_page
     * 
     * @version 20120918, zharkov
     */
    function _search($object_alias, $object_id, $mailbox_id, $type_id, $keyword, $hash, $start, $per_page)
    {
		//print_r($type_id);
        $rowset = Cache::GetData($hash);
		//$rowset = null; // test mode
        if (!isset($rowset) || !isset($rowset['data']) || isset($rowset['outdated']))
        {
            if (!empty($keyword)) $keyword = '*' . str_replace('-', '\-', str_replace(' ', '* *', $keyword)) . '*';
            
            $cl = new SphinxClient();
            $cl->SetLimits($start, $per_page);
            $cl->SetMatchMode(SPH_MATCH_ALL);

            $cl->SetFieldWeights(array(
                'sender_address'    => 1000,
                'recipient_address' => 1000,
                'cc_address'        => 1000,
				'bcc_address'       => 1000,    
                'title'             => 100,
                'description'       => 100,
                'attachments'       => 10
            ));
            
			if (!empty($keyword)){
				if ($type_id > 0) $cl->SetFilter('type_id', array($type_id, EMAIL_TYPE_SPAM));
				//dg(array($type_id, EMAIL_TYPE_SPAM));
			}else{
				if ($type_id > 0) $cl->SetFilter('type_id', array($type_id));
				//dg(array($type_id));
			}

            // список мэйлбоксов пользователя
            $modelMailbox           = new Mailbox();
            $user_mailboxes         = array();
            $user_mailboxes_mask    = 0;
            
            foreach($modelMailbox->GetListForUser($this->user_id) as $mailbox)
            {
                $user_mailboxes[]       = $mailbox['mailbox_id'];
                $user_mailboxes_mask    += pow(2, $mailbox['mailbox_id']);
            }
            
            
            if (empty($object_alias) || empty($object_id))
            {
                // если простому пользователю доступен к просмотру заданный ящик, то он показывается, если нет ,то показываются все доступные ящики
                if ($this->user_role > ROLE_ADMIN)
                {
                    if ($mailbox_id > 0 && in_array($mailbox_id, $user_mailboxes))
                    {
                        $cl->SetFilter('mailbox_id', array($mailbox_id));
                    }
                    else
                    {
                        if (empty($user_mailboxes)) $user_mailboxes = array(0);
                        $cl->SetFilter('mailbox_id', $user_mailboxes);
                    }
                    
                }
                else
                {
                    if ($mailbox_id > 0) $cl->SetFilter('mailbox_id', array($mailbox_id));
                }
                
                if (empty($type_id)) $cl->SetFilter('type_id', array(EMAIL_TYPE_INBOX, EMAIL_TYPE_OUTBOX, EMAIL_TYPE_SPAM));
                //dg($cl);
                $indexes = 'ix_mam_emails, ix_mam_emails_delta';
            }
            else
            {
                $cl->SetFilter('object_alias_id',   array(sprintf("%u", crc32($object_alias) & 0xffffffff)));
                $cl->SetFilter('object_id',         array($object_id));
                
                if ($this->user_role > ROLE_ADMIN)
                {
                    $cl->SetSelect('*, mailboxes_bit & ' . $user_mailboxes_mask . ' AS mailbox_flag');
                    $cl->SetFilter('mailbox_flag', array(0), true); // всё, кроме нуля                                                    
                }
                
                $indexes = 'ix_mam_object_emails, ix_mam_object_emails_delta';
            }

            $cl->SetGroupBy('email_id', SPH_GROUPBY_ATTR, 'date_mail DESC');
            $data = $cl->Query($keyword, $indexes);

            if ($data === false)
            {
                Log::AddLine(LOG_ERROR, 'emails::search ' . $cl->GetLastError());
                return null;
            }

            $rowset = array(); 
            if (!empty($data['matches']))
            {
                foreach ($data['matches'] as $id => $extra)
                {
                    $rowset[] = array('email_id' => $extra['attrs']['email_id']);
                }
            }

            $rowset = array(
                $rowset,
                array(array('rows' => $data['total_found']))
            );
            
            Cache::SetData($hash, $rowset, array('emails', 'search', 'emails-type-' . $type_id), CACHE_LIFETIME_MIN);
            
            $rowset = array(
                'data' => $rowset
            );
        }

        return array(
            'data'  => isset($rowset['data'][0]) ? $this->FillEmailInfo($rowset['data'][0]) : array(),
            'count' => isset($rowset['data'][1]) && isset($rowset['data'][1][0]) && isset($rowset['data'][1][0]['rows']) ? $rowset['data'][1][0]['rows'] : 0
        );        
    }
    
    /**
     * Добавляет письмо при импорте писем с почтового сервера
     * 
     * @param mixed $email_raw_id
     * @param mixed $sender_addresses
     * @param mixed $recipient_addresses
     * @param mixed $cc_addresses
     * @param mixed $title
     * @param mixed $description
     * @param mixed $description_html
     * @param mixed $date_mail
     */
    function CreateFromRaw($email_raw)
    {
        if (empty($email_raw)) return false;
        
        $email_raw_id           = $email_raw['id'];
        $sender_addresses       = $email_raw['sender_email']; 
        $recipient_addresses    = $email_raw['recipient_email'];  
        $cc_addresses           = $email_raw['cc'];
        $bcc_addresses          = $email_raw['bcc'];
        $title                  = $email_raw['subject'];  
        $description            = $email_raw['text_plain'];  
        $description_html       = $email_raw['text_html'];  
        $date_mail              = $email_raw['date']; // было: $email_raw['date_mail']; -- date_mail - вообще непонятно что за дата

        // определяет тип письма
        $mailboxes              = new Mailbox();
        $sender_mailboxes       = $mailboxes->FindInString($sender_addresses);
        $recipient_mailboxes    = $mailboxes->FindInString($recipient_addresses . ',' . $cc_addresses . ',' . $bcc_addresses);
        
        $sender_title           = $sender_addresses;

        //SG:Дополнительная проверка спама
        // парсит контент, формирует список объектов для связи с письмом
        $objects    = array();
        $component  = new ObjectComponent();
        
        list($title, $matched_objects) = $component->ParseContent($title, false);
        $objects = array_merge($objects, $matched_objects);
        
        list($description, $matched_objects) = $component->ParseContent($description, false);
        $objects = array_merge($objects, $matched_objects);

        
        
        
        if (empty($sender_mailboxes) && empty($recipient_mailboxes))
        {
            $email_type_id = EMAIL_TYPE_ERROR;
        }
        else if (date('Ymd', strtotime($date_mail)) > date('Ymd', strtotime(date('Y-m-d H:i:s'))))  // дата письма не может быть больше текущей даты
        {
            $email_type_id = EMAIL_TYPE_ERROR;
        }
        else
        {
            
            if (!empty($sender_mailboxes))
            {
                $email_type_id = EMAIL_TYPE_OUTBOX;
            }
            else if (!empty($objects)){
                //$objects=null;
                $is_spam = false;
            }
            else
            {
                //$objects=null;
                $sender_title       = '';
                
                $modelContactData   = new ContactData();
                $is_spam            = true;

                foreach (explode(',', $sender_addresses) as $email)
                {
                    //SG: получается, что все письма у которых отправитель не числется в нашем справочнике контактов - спам?!
                    // 20120918, zharkov: важно искать по точному совпадению
                    $result         = $modelContactData->FindEmail($email, 0, 10, true);
                    $email_title    = '';
                                        
                    if (empty($result))
                    {
                        $is_spam = $this->CheckIsSpam($email);
                    }
                    else
                    {
                        if (isset($result[0]))
                        {
                            $result = $result[0];
                            if (isset($result['person']) && isset($result['person']['doc_no_short']) && !empty($result['person']['doc_no_short']))
                            {
                                $email_title = $result['person']['doc_no_short'];
                            }
                            else if (isset($result['company']) && isset($result['company']['doc_no']) && !empty($result['company']['doc_no']))
                            {
                                $email_title = $result['company']['doc_no'];
                            }
                        }
                        
                        $is_spam = false;                        
                    }
                    
                    $sender_title .= (empty($email_title) ? $email : ('"' . trim($email_title) . '" <' . trim($email) . '>')) . ', ';
                }
                
                $email_type_id = $is_spam ? EMAIL_TYPE_SPAM : EMAIL_TYPE_INBOX;
            }
        }
        
        $sender_title = trim($sender_title, ", ");

        // парсит контент, формирует список объектов для связи с письмом
        $objects    = array();
        $component  = new ObjectComponent();
        
        list($title, $matched_objects) = $component->ParseContent($title, false);
        $objects = array_merge($objects, $matched_objects);
        
        list($description, $matched_objects) = $component->ParseContent($description, false);
        $objects = array_merge($objects, $matched_objects);        

        // формирует список идентификаторов mailboxes
        $mailbox_ids = implode(',', array_values(array_merge(array_flip($sender_mailboxes), array_flip($recipient_mailboxes))));

        // сохраняет письмо и связывает с mailboxes
        $result = $this->CallStoredProcedure('sp_email_create_from_raw', array($email_raw_id, $email_type_id, $sender_title, $recipient_addresses, 
                                                $cc_addresses, $title, $description, $description_html, $date_mail, $mailbox_ids));
        $result = isset($result) && isset($result[0]) && isset($result[0][0]) ? $result[0][0] : null;

        if (empty($result) || isset($result['ErrorCode'])) return false;

        // парсит адреса для нахождения и связи с объектами
        $this->SaveAddressObjects($result['email_id'], $sender_addresses . ',' . $recipient_addresses . ',' . $cc_addresses);
        
        // сохраняет связи с объектами
        $this->_save_objects($result['email_id'], $objects);
        
        // сбрасывает список
        Cache::ClearTag('emails-type-' . $email_type_id);
        Cache::ClearTag('emails');
        Cache::ClearTag('email-bizes-for-menu');
        
        if ($email_raw_id == EMAIL_TYPE_SPAM)
        {
            Cache::ClearTag('email-spam-count');
        }
        
        //return $result['email_id'];
        return $result;
    }
    
    /**
     * Возвращает доступные типы для документов
     * @return type
     */
    public function GetDocTypesList()
    {
        return array(
            array('id' => '1', 'name' => 'BL'),
            array('id' => '2', 'name' => 'CIM'),
            array('id' => '3', 'name' => 'Claim'),
            array('id' => '4', 'name' => 'CMR'),
            array('id' => '5', 'name' => 'CN'),
            array('id' => '6', 'name' => 'DN'),
            array('id' => '7', 'name' => 'Enquiry'),
            array('id' => '8', 'name' => 'FCR'),
            array('id' => '9', 'name' => 'INV'),
            array('id' => '10', 'name' => 'Offer'),
            array('id' => '11', 'name' => 'PL'),
            array('id' => '12', 'name' => 'PO'),
            array('id' => '13', 'name' => 'QC'),
            array('id' => '14', 'name' => 'RA'),
            array('id' => '15', 'name' => 'SC'),
            
        );
    }
    
    /**
     * Возвращает название типа докупента по id-типа
     * @param type $doc_type_id
     * @version 2012-09-03 d10n
     */
    public function GetDocTypeById($doc_type_id)
    {
        switch($doc_type_id)
        {
            case 1: return 'BL';
            case 2: return 'CIM';
            case 3: return 'Claim';
            case 4: return 'CMR';
            case 5: return 'CN';
            case 6: return 'DN';
            case 7: return 'Enquiry';
            case 8: return 'FCR';
            case 9: return 'INV';
            case 10: return 'Offer';
            case 11: return 'PL';
            case 12: return 'PO';
            case 13: return 'QC';
            case 14: return 'RA';
            case 15: return 'SC';
                
            default: return 'n/a';
        }
    }
    
    /**
     * Устанавливает статус письма ПРОЧИТАНО
     * @param type $email_id
     * @return boolean
     */
    public function SetAsRead($email_id)
    {
        if ($email_id <= 0) return FALSE;
        
        $this->CallStoredProcedure('sp_email_mark_as_read', array($this->user_id, $email_id));
        
        Cache::ClearTag('emailuserdata-' . $this->user_id . '-' . $email_id);
        Cache::ClearTag('mailboxes-' . $this->user_id . '-stat');
        Cache::ClearTag('emails-user-' . $this->user_id . '-stat');        
        Cache::ClearTag('email-' . $email_id);
    }
    
    /**
     * Устанавливае статус письма НЕ ПРОЧИТАНО
     * (удаляет запись)
     * 
     * @param int $email_id
     * @return boolean
     * 
     * @version 20121024, d10n
     */
    public function SetAsUnread($email_id)
    {
        if ($email_id <= 0) return FALSE;
        
        $this->CallStoredProcedure('sp_email_mark_as_unread', array($this->user_id, $email_id));
        
        Cache::ClearTag('emailuserdata-' . $this->user_id . '-' . $email_id);
        Cache::ClearTag('mailboxes-' . $this->user_id . '-stat');
        Cache::ClearTag('emails-user-' . $this->user_id . '-stat');        
        Cache::ClearTag('email-' . $email_id);
    }

    /**
    * Получает объекты из email-адресов и связывает с письмом
    * 
    * @param mixed $email_id
    * @param mixed $email_addresses
    */
    function SaveAddressObjects($email_id, $email_addresses)
    {
        $email_addresses = trim(preg_replace('#\s+#i', '', $email_addresses), ',');
        
        
        // получает список mailboxes чтобы исключить из проверки
        $mailbox    = new Mailbox();
        $mailboxes  = array();
        foreach ($mailbox->GetList(false) as $row)
        {
            if(isset($row['mailbox'])) $mailboxes[] = $row['mailbox']['address'];
        }
        

        $contactdata    = new ContactData();
        $persons        = new Person();
        $objects        = array();

        foreach (explode(',', $email_addresses) as $email)
        {
            $email = trim($email);
            if (empty($email)) continue;
            
            // эта проверка нужна, потому что адрес может быть в виде "Офис" <office@kvadrosoft.com>, 
            preg_match_all('#([a-z0-9_\-]+\.)*[a-z0-9_\-]+@([a-z0-9][a-z0-9\-]*[a-z0-9]\.)+[a-z]{2,4}#i', $email, $matches);

            if (!empty($matches) && isset($matches[0]) && !empty($matches[0]))
            {
                $email = $matches[0][0];
                
                // mailboxes не обрабатываются
                if (in_array($email, $mailboxes)) continue;
                
                foreach ($contactdata->GetByTypeAndTitle('email', $email) as $cd)
                {
                    $objects[] = array('alias' => $cd['object_alias'], 'id' => $cd['object_id']);
                    
                    if ($cd['object_alias'] == 'person')
                    {
                        $person = $persons->GetById($cd['object_id']);
                        if (!empty($person))
                        {
                            $objects[] = array('alias' => 'company', 'id' => $person['person']['company_id']);
                        }
                    }
                }
            }
        }

        return $this->_save_objects($email_id, $objects);
    }

    /**
     * Связывает письмо с объектами
     * 
     * @param mixed $email_id
     * @param mixed $objects
     * 
     * @version 20120720, zharkov
     */
    function SaveObjects($email_id, $object_alias, $object_id)
    {
        if (empty($object_alias) || $object_alias == 'email' || empty($object_id)) return;

        $objects[] = array('alias' => $object_alias, 'id' => $object_id);

        if ($object_alias == 'qc')
        {
            $qcs        = new QC();
            $qc         = $qcs->GetById($object_id);                                            
            $qc         = $qc['qc'];

            if (!empty($qc['order_id']))    $objects[] = array('alias' => 'order',      'id' => $qc['order_id']);
            if (!empty($qc['biz_id']))      $objects[] = array('alias' => 'biz',        'id' => $qc['biz_id']);
            if (!empty($qc['customer_id'])) $objects[] = array('alias' => 'company',    'id' => $qc['customer_id']);
        }
        else if ($object_alias == 'sc')
        {
            $scs        = new SC();
            $sc         = $scs->GetById($object_id);                                            
            $sc         = $sc['sc'];
            
            $orders     = new Order();
            $order      = $orders->GetById($sc['order_id']);
            $order      = $order['order'];
            
            $objects[]  = array('alias' => 'order',    'id' => $order['id']);
            $objects[]  = array('alias' => 'biz',      'id' => $order['biz_id']);
            $objects[]  = array('alias' => 'company',  'id' => $order['company']['id']);
            $objects[]  = array('alias' => 'person',   'id' => $sc['person']['id']);
            
        }
        else if ($object_alias == 'person')
        {
            $persons    = new Person();
            $person     = $persons->GetById($object_id);
            
            $objects[]  = array('alias' => 'company', 'id' => $person['person']['company_id']);
        }            

        return $this->_save_objects($email_id, $objects);
    }
    
    /**
     * Связывает объекты с письмом
     * 
     * @param mixed $email_id
     * @param mixed $objects
     * 
     * @version 20120804, zharkov
     */
    function _save_objects($email_id, $objects)
    {
        foreach ($objects as $object) 
        {
            if (empty($object['id'])) continue;
            
            $result = $this->SaveObject($email_id, $object['alias'], $object['id']);            
            if (empty($result)) return null;
        }
        
        return $email_id;        
    }
    
    /**
     * Возвращает список активных аккаунтов
     * 
     * @version 20120803, zharkov
     */
    function GetMailboxList()
    {
        $result = $this->CallStoredProcedure('sp_mailbox_get_list', array());
        $result = isset($result) && isset($result[0]) ? $result[0] : array();
        
        foreach ($result as $key => $row)
        {
            //$result[$key]['address'] = $row['username'] . '@' . $row['mail_domain'];
            $result[$key]['address'] = $row['title'];
        }
        
        return $result;
    }
    
    /**
     * Заполняет связи с обхектами к письмам
     * 
     * @param mixed $rowset
     */
    function FillEmailObjects($rowset)
    {
        $entityname     = 'emailobjects';
        $cache_prefix   = 'emailobjects';
        $rowset         = $this->_fill_entity_array_info($rowset, 'email_id', $entityname, $cache_prefix, 'sp_email_get_objects_by_ids', array('emails' => '', 'email' => 'email_id'), array());
        

        $bizes      = new Biz();
        $companies  = new Company();
        $persons    = new Person();
        
        foreach($rowset as $key => $row)
        {
            if (isset($row[$entityname]) && !empty($row[$entityname]))
            {
                foreach ($row[$entityname] as $index => $obj)
                {
                    $row[$entityname][$index][$obj['object_alias'] . '_id'] = $obj['object_id'];
                }

                $rowset[$key]['email']['objects'] = $persons->FillPersonMainInfo($companies->FillCompanyInfoShort($bizes->FillMainBizInfo($row[$entityname])));
                unset($rowset[$key][$entityname]);
            }
        }

        return $rowset;        
    }
    
    /**
     * Возвращает список писем для объекта
     *     
     * @param mixed $object_alias
     * @param mixed $object_id
     * @param mixed $message_id
     * @param int $type_id [TINYINT] ID типа ящика EMAIL_TYPE_
     * @param int $is_deleted [TINYINT] 0/1 Неудален/Удален соответственно
     * @param mixed $page_no
     * @param mixed $per_page
     * @return mixed
     * 
     * @version 20121024, d10n: добавлен параметр для фильтра $is_deleted 
     * @version 20120831, d10n: добавлен параметр для фильтра $type_id
     * @version 20120721, zharkov
     */
    function GetListForObject($object_alias, $object_id, $email_id = 0, $type_id = -1, $is_deleted = 0, $page_no = 0, $per_page = ITEMS_PER_PAGE)
    {
        $page_no    = $page_no > 0 ? $page_no : 1;
        $per_page   = $per_page < 1 ? ITEMS_PER_PAGE : $per_page;
        $start      = ($page_no - 1) * $per_page;
        
        $hash       = 'emails-' . md5('user_id-' . $this->user_id . '-user_role-' . $this->user_role . 
                                        '-obj-' . $object_alias . '-objid-' . $object_id . 
                                        '-email_id-' . $email_id . '-type_id-' . $type_id . '-is_deleted-' . $is_deleted . '-pageno-' . $page_no . '-perpage-' . $per_page);
        
        $cache_tags = array($hash, 'emails-obj-' . $object_alias . '-objid-' . $object_id, 'emails');
        $rowset     = $this->_get_cached_data($hash, 'sp_email_get_list_for_object', array($this->user_id, $this->user_role, $object_alias, $object_id, $email_id, $type_id, $is_deleted, $start, $per_page), $cache_tags);
        
        return array(
            'data'  => isset($rowset[0]) ? $this->FillEmailInfo($rowset[0]) : array(),
            'count' => isset($rowset[1]) && isset($rowset[1][0]) && isset($rowset[1][0]['count']) ? $rowset[1][0]['count'] : 0
        );
    }
    
//    /**
//     * Возвращает список писем для конкретнго пользователя
//     * @param int $user_id [INT] ID users.id пользователя системы
//     * @param int $mailbox_id [INT] ID mailboxes.id Ящика компании
//     * @param int $type_id [TINYINT] ID типа ящика EMAIL_TYPE_
//     * @param int $is_deleted [TINYINT]
//     * @param int $page_no
//     * @param int $per_page
//     * 
//     * @version 20121024, d10n: Add param $is_deleted
//     */
//    public function GetListForUser($user_id, $mailbox_id, $type_id, $is_deleted, $page_no = 0, $per_page = ITEMS_PER_PAGE)
//    {
//        $page_no    = $page_no > 0 ? $page_no : 1;
//        $per_page   = $per_page < 1 ? ITEMS_PER_PAGE : $per_page;
//        $start      = ($page_no - 1) * $per_page;
//        
//        $hash       = 'emails-' . md5('user_id-' . $user_id . '-mailbox_id-' . $mailbox_id . '-type_id-' . $type_id . '-is_deleted-' . $is_deleted . '-pageno-' . $page_no . '-perpage-' . $per_page);
//        $cache_tags = array($hash, 'emails-user-' . $user_id, 'emails');
//        $rowset     = $this->_get_cached_data($hash, 'sp_email_get_list_for_user', array($user_id, $mailbox_id, $type_id, $is_deleted, $start, $per_page), $cache_tags);
//        
//        return array(
//            'data'  => isset($rowset[0]) ? $this->FillEmailInfo($rowset[0]) : array(),
//            'count' => isset($rowset[1]) && isset($rowset[1][0]) && isset($rowset[1][0]['count']) ? $rowset[1][0]['count'] : 0
//        );
//    }
    
    /**
     * Возвращает список писем для <= ROLE_ADMIN
     * @param int $mailbox_id [INT] ID mailboxes.id Ящика компании
     * @param int $type_id [TINYINT] ID типа ящика EMAIL_TYPE_
     * @param int $page_no
     * @param int $per_page
     * @return array
     * @version 2012-09-04 d10n
     */
    public function GetListForAdmin($mailbox_id, $type_id, $page_no = 0, $per_page = ITEMS_PER_PAGE)
    {
        $page_no    = $page_no > 0 ? $page_no : 1;
        $per_page   = $per_page < 1 ? ITEMS_PER_PAGE : $per_page;
        $start      = ($page_no - 1) * $per_page;
        
        $hash       = 'emails-' . md5('mailbox_id-' . $mailbox_id . '-type_id-' . $type_id . '-pageno-' . $page_no . '-perpage-' . $per_page);
        $cache_tags = array($hash, 'emails-mailbox-' . $mailbox_id, 'emails');
        $rowset     = $this->_get_cached_data($hash, 'sp_email_get_list_for_admin', array($mailbox_id, $type_id, $start, $per_page), $cache_tags);
        
        return array(
            'data'  => isset($rowset[0]) ? $this->FillEmailInfo($rowset[0]) : array(),
            'count' => isset($rowset[1]) && isset($rowset[1][0]) && isset($rowset[1][0]['count']) ? $rowset[1][0]['count'] : 0
        );
    }
    
    
    /**
     * Связывает письмо с объектом
     * 
     * @param mixed $email_id
     * @param mixed $object_alias
     * @param mixed $object_id
     * 
     * @version 20120720, zharkov
     */
    function SaveObject($email_id, $object_alias, $object_id)
    {
        $result = $this->CallStoredProcedure('sp_email_save_object', array($this->user_id, $email_id, $object_alias, $object_id));
        $result = isset($result) && isset($result[0]) && isset($result[0][0]) ? $result[0][0] : null;
        
        if (isset($result) && array_key_exists('ErrorCode', $result)) return null;

        // обновляет последнее сообщение для объекта
        //Email::SetLastEmailId($email_id, $object_alias, $object_id);

        // обновляет список сообщений для объекта
        Cache::ClearTag('emails-obj-' . $object_alias . '-objid-' . $object_id);
        Cache::ClearTag($object_alias . '-' . $object_id . '-blog');
        //dg($rowset);
        return $email_id;
    }

    /**
     * Возвращает количество писем для объекта
     * 
     * @param mixed $object_alias
     * @param mixed $object_id
     */
    function GetCountByObject($object_alias, $object_id)
    {
        $hash       = 'email-obj-' . $object_alias . '-objid-' . $object_id . '-count';
        $cache_tags = array('emails', 'email-obj-' . $object_alias . '-objid-' . $object_id);

        $rowset = $this->_get_cached_data($hash, 'sp_email_get_count_by_object', array($this->user_id, $this->user_role, $object_alias, $object_id), $cache_tags);
        return isset($rowset) && isset($rowset[0]) && isset($rowset[0][0]) && isset($rowset[0][0]['count']) ? $rowset[0][0]['count'] : 0;
    }
    
    /**
     * Сохраняет драфт письма
     * 
     * @param mixed $id
     * @param mixed $sender_email
     * @param mixed $recipient_email
     * @param mixed $to
     * @param mixed $attention
     * @param mixed $subject
     * @param mixed $our_ref
     * @param mixed $your_ref
     * @param mixed $title
     * @param mixed $description
     * @param mixed $signature
     * @param mixed $approve_by
     * @param mixed $approve_deadline
     * @param mixed $doc_type
     * @param mixed $seek_response
     * @return resource
     * 
     * @version 20120720, zharkov
     */
    function Save($id, $object_alias, $object_id, $sender_email, $recipient_email, $cc_email, $bcc_email, $to, $attention, $subject, $our_ref, 
                    $your_ref, $title, $description, $signature, $approve_by, $approve_deadline, $doc_type, $seek_response, $mailbox_id, 
                    $parent_id = 0, $signature2 = '', $signature3 = '')
    {
                
        $recipient_email = trim($recipient_email, ", ");
        
        $result = $this->CallStoredProcedure('sp_email_save', array($this->user_id, $id, $object_alias, $object_id, $sender_email, 
                $recipient_email, $cc_email, $bcc_email, $to, $attention, $subject, $our_ref, $your_ref, $title, $description, $signature,
                $approve_by, $approve_deadline, $doc_type, $seek_response, $mailbox_id, $parent_id, $signature2, $signature3));
        $result = isset($result) && isset($result[0]) && isset($result[0][0]) ? $result[0][0] : null;
        
        if (empty($result) || array_key_exists('ErrorCode', $result)) return null;

        Cache::ClearTag('emails-mailbox-' . $mailbox_id . '-type-' . EMAIL_TYPE_DRAFT);
        Cache::ClearTag('email-' . $result['id']);
        Cache::ClearTag('email-dfa-list-' . $id); 
        
        // в хп обновляется номер последнего письма в таблице users при создании новго письма
        if (empty($id)) Cache::ClearTag('user-' . $this->user_id);

        // связывает объекты с письмом                    
        $this->SaveObjects($result['id'], $object_alias, $object_id);

        // получает объекты из адресов и связывает с письмом
        $this->SaveAddressObjects($result['id'], $recipient_email . ',' . $cc_email . ',' . $bcc_email);        
        
        return $result;        
    }
    
    /**
     * Возвращает последнее письмо
     * 
     */
    function GetLast()
    {
        $result = $this->CallStoredProcedure('sp_email_get_last', array($this->user_id));
        return isset($result) && isset($result[0]) && isset($result[0][0]) ? $result[0][0] : array();
    }
    
    /**
     * Возвращает письмо по идентификатору
     *     
     * @param mixed $id
     */
    function GetById($id)
    {
        $dataset = $this->FillEmailObjects($this->FillEmailInfo(array(array('email_id' => $id))));
        return isset($dataset) && isset($dataset[0]) && isset($dataset[0]['email']) ? $dataset[0] : null;
    }
    
    /**
     * заполняет основные данные письма
     * 
     * @param mixed $rowset
     * @param mixed $id_fieldname
     * @param mixed $entityname
     * @param mixed $cache_prefix
     * @return mixed
     */
    function FillEmailMainInfo($rowset, $id_fieldname = 'email_id', $entityname = 'email', $cache_prefix = 'email')
    {
        return $this->_fill_entity_info($rowset, $id_fieldname, $entityname, $cache_prefix, 'sp_email_get_list_by_ids', array('emails' => ''), array());
    }
    
    /**
     * Заполняет набор данных информацией о письме
     * 
     * @param array $rowset
     * @param mixed $id_fieldname
     * @param mixed $entityname
     * @param mixed $cache_prefix
     * @return array
     */
    function FillEmailInfo($rowset, $id_fieldname = 'email_id', $entityname = 'email', $cache_prefix = 'email')
    {
        $rowset = $this->FillEmailMainInfo($rowset, $id_fieldname, $entityname, $cache_prefix);
         
        foreach ($rowset as $key => $row)
        {
            if (!isset($rowset[$key][$entityname])) continue;
            
            $row = $row[$entityname];
            $rowset[$key]['eauthor_id']         = $row['created_by'];
            $rowset[$key]['emodifier_id']       = $row['modified_by'];
            $rowset[$key]['esender_id']         = $row['sent_by'];
            $rowset[$key]['eapprover_id']       = $row['approve_by'];
            
            $rowset[$key][$entityname]['is_today']  = (date('Y-m-d', strtotime($row['date_mail'])) == date('Y-m-d') ? 1 : 0);
            $rowset[$key][$entityname]['doc_no']    = $row['title'];
            
            if (!empty($row['parent_id'])) $rowset[$key]['eparent_id'] = $row['parent_id'];                
        }
        
        $rowset     = $this->FillUserInfo($rowset, $id_fieldname, 'userdata', 'emailuserdata');
        
        $users      = new User();
        $rowset     = $users->FillUserInfo($rowset, 'eauthor_id',   'eauthor');
        $rowset     = $users->FillUserInfo($rowset, 'emodifier_id', 'emodifier');
        $rowset     = $users->FillUserInfo($rowset, 'esender_id',   'esender');
        $rowset     = $users->FillUserInfo($rowset, 'eapprover_id', 'eapprover');
        
        $rowset     = $this->FillEmailObjects($rowset);
        
        // родительское письмо
        $rowset     = $this->FillEmailMainInfo($rowset, 'eparent_id', 'eparent');

		$personModel = new Person();        

        foreach ($rowset as $key => $row) 
        {
            if (!isset($rowset[$key][$entityname])) continue;
            
            if (isset($row['eparent']))
            {
                $rowset[$key][$entityname]['parent'] = $row['eparent'];
                unset($rowset[$key]['eparent']);
            }                

            if (isset($row['eauthor']))
            {
                $rowset[$key][$entityname]['author'] = $row['eauthor'];
                unset($rowset[$key]['eauthor']);
            }                
            
            if (isset($row['emodifier']))
            {
                $rowset[$key][$entityname]['modifier'] = $row['emodifier'];
                unset($rowset[$key]['emodifier']);
            }

            if (isset($row['esender']))
            {
                $rowset[$key][$entityname]['sender'] = $row['esender'];
                unset($rowset[$key]['esender']);
            }
            
            if (isset($row['eapprover']))
            {
                $rowset[$key][$entityname]['approver'] = $row['eapprover'];
                unset($rowset[$key]['eapprover']);
            }
            
			$biz_tags=array();
			$navigator=array();
			$driver='';
			$is_biz_tag_exists = false;
            // Проверка письма на наличие BIZ-тегов
            if (isset($row[$entityname]['objects']))
            {
               
                foreach ($row[$entityname]['objects'] as $object)
                {
                    if ($object['object_alias'] != 'biz') continue;
                    
                    $is_biz_tag_exists = true;
                    $biz_tags[] = $object;
                    break;
                }

                foreach ($biz_tags as $biz_item)
                {
                    if ($biz_item['biz']['driver_id'] > 0) {
                    	$driver = $personModel->GetById($biz_item['biz']['driver_id']);
                    }
					//dg($biz_item['biz']['navigators']);
					$navigators=explode("/", $biz_item['biz']['navigators']);
                    for($i=0; $i<count($navigators); $i++) 
					{
                    	if($navigators[$i]>0) $navigator[] = $personModel->GetById($navigators[$i]);
						//print_r($navigator[$i]);
                    }
                }				
                $rowset[$key][$entityname]['is_biz_tag_exists'] = $is_biz_tag_exists;
                $rowset[$key][$entityname]['biz_tags'] = $biz_tags;
				$rowset[$key][$entityname]['driver'] = $driver;
				$rowset[$key][$entityname]['navigator'] = $navigator;
                //if(count($navigator)>0) dg($navigators);
                unset($rowset[$key][$entityname]['objects']);
            }

            $sender_address = str_replace(' ', '', str_replace('>', '', str_replace('<', '', $row[$entityname]['sender_address'])));
            $sender_address = explode('@', $sender_address);

            if ($row[$entityname]['type_id'] == EMAIL_TYPE_DRAFT)
            {
                $rowset[$key][$entityname]['type_alias']    = 'draft';
                $rowset[$key][$entityname]['sender_domain'] = $sender_address[1];
            }
            else if ($row[$entityname]['type_id'] == EMAIL_TYPE_INBOX)
            {
                $rowset[$key][$entityname]['type_alias'] = 'inbox';
            }
            else if ($row[$entityname]['type_id'] == EMAIL_TYPE_OUTBOX)
            {
                $rowset[$key][$entityname]['type_alias']    = 'outbox';
                $rowset[$key][$entityname]['sender_domain'] = $sender_address[1]; 
            }            
            else if ($row[$entityname]['type_id'] == EMAIL_TYPE_ERROR)
            {
                $rowset[$key][$entityname]['type_alias'] = 'error';
            }
            
            if (!empty($row[$entityname]['doc_type']))
            {
                $rowset[$key][$entityname]['doc_type_name'] = $this->GetDocTypeById($row[$entityname]['doc_type']);
            }
                
            unset($rowset[$key]['eauthor_id']);
            unset($rowset[$key]['emodifier_id']);            
            unset($rowset[$key]['esender_id']);            
            unset($rowset[$key]['eapprover_id']);            
        }        
        
        $attachments    = new Attachment();
        $rowset         = $attachments->FillObjectAttachments($rowset, $entityname, $id_fieldname);
        
        foreach ($rowset as $key => $row)
        {
            if (isset($row[$entityname]) && isset($row[$entityname]['attachments']) && !empty($row[$entityname]['attachments']))
            {
                $attached = array();
                
                foreach ($row[$entityname]['attachments'] as $attachment)
                {
                    if (!array_key_exists('attachment', $attachment)) continue;
                    
                    $ext = $attachment['attachment']['ext'];
                    if (array_key_exists($ext, $attached))
                    {
                        $attached[$ext] = $attached[$ext] + 1;
                    }
                    else
                    {
                        $attached[$ext] = 1;
                    }
                }
                
                $attached_str = '';
                foreach ($attached as $ext => $qtty)
                {
                    $attached_str .= $qtty . ' x ' . strtoupper($ext) . ', ';
                }
                
                $rowset[$key][$entityname]['attached'] = trim($attached_str, ", ");
            }
        }
        //dg($rowset);
        return $rowset;
    }
    
    /**
     * Заполняет данные пользователя
     * 
     * @param mixed $rowset
     * @param mixed $id_fieldname
     * @param mixed $entityname
     * @param mixed $cache_prefix
     * 
     * @version 20120830, d10n
     */
    function FillUserInfo($rowset, $id_fieldname = 'email_id', $entityname = 'userdata', $cache_prefix = 'emailuserdata')
    {
        $rowset = $this->_fill_entity_info($rowset, $id_fieldname, $entityname, $cache_prefix . '-' . $this->user_id, 'sp_email_get_userdata_by_ids', array('emails' => '', 'email' => 'id'), array($this->user_id));
        
        foreach ($rowset as $key => $row)
        {
            if (isset($row['email']) && isset($row[$entityname]) && !empty($row[$entityname]))
            {
                $rowset[$key]['email'][$entityname] = $row[$entityname];
                unset($rowset[$key][$entityname]);
            }
        }
        
        return $rowset;
    }
    
    /**
     * 
     * @param type $dfa_id
     * @param type $sender_address
     * @param type $email_id
     * @param type $type_id
     */
    function MarkAsSent($dfa_id, $sender_address, $email_id, $type_id = EMAIL_TYPE_OUTBOX)
    {
        $this->CallStoredProcedure('sp_email_mark_as_sent', array($this->user_id, $dfa_id, $sender_address, $type_id));
        $this->Update($email_id, array('is_sent' => '2'));
        
        Cache::ClearTag('email-' . $email_id);
        Cache::ClearTag('emails-type-' . EMAIL_TYPE_DRAFT);
        Cache::ClearTag('emails-type-' . $type_id);

        Cache::ClearTag('emails-user-' . $this->user_id . '-stat');        
        Cache::ClearTag('mailboxes-' . $this->user_id . '-stat');
    }
    
    /**
     * Изменяет/Обновляет параметры конкретного письма<br />
     * а именно: $approve_by, $approve_deadline, $doc_type, $seek_response
     * 
     * @param int $id [ID]
     * @param int $approve_by [INT]
     * @param string $approve_deadline [TIMESTAMP]
     * @param int $doc_type [TINYINT]
     * @param string $seek_response [TIMESTAMP]
     * 
     * @return array();
     * @version 20121024, d10n
     */
    public function UpdateInboxMail($id, $approve_by, $approve_deadline, $doc_type, $seek_response)
    {
        $result = $this->Update($id, array(
            'approve_by'        => $approve_by,
            'approve_deadline'  => $approve_deadline,
            'doc_type'          => $doc_type,
            'seek_response'     => $seek_response,
            'modified_at'       => 'NOW()!',
            'modified_by'       => $this->user_id,
        ));
        
//TODO: Определить более рациональный сброс кеша
        Cache::ClearTag('emails');
        return $result ? $this->GetById($id) : array();
    }
    
    
// START MAILBOXES methods
    /**
     * Возвращает список необработанных email-сообщений
     * @param int $is_active
     * @return array
     */
    public function GetMailboxesList($is_active)
    {
        $this->table->table_name = 'mailboxes';
        
        $data_set = $this->SelectList(array(
            'fields'    => '*, id AS mailbox_id',
            'where'     => array(
                'conditions'    => '(is_active=? OR ?=-1)',
                'arguments'     => array($is_active, $is_active),
            ),
        ));
        
        $this->table->table_name = 'emails';
        
        return $data_set;
    }
    
    public function GetMailboxByEmail($email)
    {
        $this->table->table_name = 'mailboxes';
        
        list($username, $mail_domain) = explode('@', $email);
        
        $data_set = $this->SelectList(array(
            'fields'    => '*',
            'where'     => array(
                'conditions'    => 'username=? AND mail_domain=?',
                'arguments'     => array($username, $mail_domain),
            ),
        ));
        
        $this->table->table_name = 'emails';
        
        return isset($data_set[0]) ? $data_set[0] : array();
    }
// END MAILBOXES methods
    
    
// START EMAILS_RAW_CORRUPTED methods
    /**
     * Возвращает запись по ID
     * @param int $email_raw_corrupted_id
     * @return array
     */
    public function GetEmailRawCorruptedById($email_raw_corrupted_id)
    {
        $this->table->table_name = 'emails_raw_corrupted';
        
        $data_set = $this->SelectSingle(array(
//            'fields'    => '*',
            'where'     => array(
                'conditions'    => '(id=?)',
                'arguments'     => array($email_raw_corrupted_id),
            ),
//            'order' => array('RAND()'),
//            'limit' => array('lower' => 0, 'number' => 1),
        ));
        
        $this->table->table_name = 'emails';
        
        return $data_set ? $data_set : array();
    }
    
    /**
     * Сохраняет данные по EmailRawCorrupted
     * @param string $username [VARCHAR(200)]
     * @param string $mailbox_name [VARCHAR(200)]
     * @param int $message_num [INT]
     * @param string $error_message [TEXT]
     * @return array
     */
    public function SaveEmailRawCorrupted($username, $mailbox_name, $message_num, $error_message)
    {
        $this->table->table_name = 'emails_raw_corrupted';
        
        $data_set = $this->Insert(array(
            'username'      => $username,
            'mailbox_name'  => $mailbox_name,
            'message_num'   => $message_num,
            'error_message' => $error_message,
            'created_at'    => 'NOW()!',
            'created_by'    => 0,
            'modified_at'   => 'NOW()!',
            'modified_by'   => 0,
        ));
        
        $this->table->table_name = 'emails';
        
        return $data_set ? $this->GetEmailRawCorruptedById($data_set) : array();
    }
// END EMAILS_RAW_CORRUPTED methods
    
    
// START EMAILS_RAW methods
    /**
     * Возвращает запись необработанного email-сообщения по ID
     * @param int $email_raw_id
     * @return array
     */
    public function GetEmailRawById($email_raw_id)
    {
        $this->table->table_name = 'emails_raw';
        
        $data_set = $this->SelectSingle(array(
//            'fields'    => '*',
            'where'     => array(
                'conditions'    => '(id=?)',
                'arguments'     => array($email_raw_id),
            ),
//            'order' => array('RAND()'),
//            'limit' => array('lower' => 0, 'number' => 1),
        ));
        
        $this->table->table_name = 'emails';
        
        return $data_set ? $data_set : array();
    }
    
    /**
     * Сохраняет данные
     * @param string $username [VARCHAR(200)]
     * @param string $mailbox_name [VARCHAR(1000)]
     * @param int $message_num [INT(11)]
     * @param string $messace_id [VARCHAR(1000)]
     * @param string $sender_email[VARCHAR(1000)]
     * @param string $recipient_email [VARCHAR(1000)]
     * @param string $cc [VARCHAR(1000)]
     * @param string $bcc [VARCHAR(1000)]
     * @param string $reply_to [VARCHAR(1000)]
     * @param string $sender [VARCHAR(1000)]
     * @param string $date [DATETIME]
     * @param string $date_mail [DATETIME]
     * @param int $udate [INT(11)]
     * @param string $subject [VARCHAR(1000)]
     * @param string $text_plain [MEDIUMTEXT]
     * @param string $text_html [MEDIUMTEXT]
     * @param int $size_plain [INT(11)]
     * @param int $size_html [INT(11)]
     * @param int $recent [TINYINT(4)]
     * @param int $unseen [TINYINT(4)]
     * @param int $flagged [TINYINT(4)]
     * @param int $answered [TINYINT(4)]
     * @param int $deleted [TINYINT(4)]
     * @param int $draft [TINYINT(4)]
     * @param int $is_parsed [TINYINT(4)]
     * @param int $has_attachments [TINYINT(4)]
     * @param int $status_id [TINYINT(4)]
     * @return array
     */
    public function SaveEmailRaw($username, $mailbox_name, $message_num, $message_id, $sender_email, $recipient_email, $cc, $bcc, $reply_to, $sender,
            $date, $date_mail, $udate,
            $subject, $text_plain, $text_html, $size_plain, $size_html,$recent, $unseen, $flagged, $answered, $deleted, $draft,
            $is_parsed, $has_attachments, $status_id)
    {
        //if ($this->IsEmailRawExists($username, $mailbox_name, $message_num, $message_id) == TRUE) return array();//чтобы не выполнять лишний раз длинные запросы на вставку записи
        
        $data_set = $this->CallStoredProcedure('sp_email_raw_save', array(0, $username, $mailbox_name, $message_num, $message_id,
            $sender_email, $recipient_email, $cc, $bcc, $reply_to, $sender, $date, $date_mail, $udate,
            $subject, $text_plain, $text_html, $size_plain, $size_html,
            $recent, $unseen, $flagged, $answered, $deleted, $draft,
            $is_parsed, $has_attachments, $status_id));
        
        return isset($data_set[0]) && isset($data_set[0][0]) ? $data_set[0][0] : array();
    }
    
    /**
     * Возвращает список необработанных email-сообщений
     * 
     * @param int $count
     * @return array
     * 
     * @version 20121029, d10n: получение данных переложено на ХП
     * @version 20120913, zharkov: убрал ключ $is_parsed из-за изменивейся структуры, теперь выбираются позиции без признака is_parsed
     */
    public function GetEmailRawList($count = ITEMS_PER_PAGE)
    {
        $user_id    = 0;
        $is_parsed  = 0;
        
        $data_set = $this->CallStoredProcedure('sp_email_raw_get_list_for_parsing', array($user_id, $is_parsed, $count));
        
        return isset($data_set[0]) ? $data_set[0] : array();
//        $this->table->table_name = 'emails_raw';
//        
//        $data_set = $this->SelectList(array(
//            'fields'    => '*',
//            'limit'     => $count,
//            'where'     => array(
//                'conditions'    => 'is_parsed = ?',
//                'arguments'     => array(0),
//            ),
//            'order'     => 'id'
//        ));
//        
//        $this->table->table_name = 'emails';
//        
//        return $data_set;
    }
    
    /**
     * Обновляет IsParsed
     * @param int $email_raw_id
     * @param int $is_parsed
     * @return array
     * 
     * @deprecated 20120913, zharkov: перенесена в sp_email_create_from_raw
     */
    public function deprecated_UpdateEmailRawIsParsed($email_raw_id, $is_parsed)
    {
        $this->table->table_name = 'emails_raw';
        
         $data_set = $this->UpdateList(array(
            'values'    => array(
                'is_parsed'     => $is_parsed,
                'modified_at'   => 'NOW()!',
                'modified_by'   => 0,
            ),
            'where'     => array(array(
                'conditions'    => 'id = ?',
                'arguments'     => array($email_raw_id),
            )),
        ));
        
        $this->table->table_name = 'emails';
        
        return $this->GetEmailRawById($email_raw_id);
    }
    /**
     * Возвращает из БД дату самого "свежего" необработанног email-сообщения
     * @param string $username [VARCHAR(200)]
     * @param string $mailbox_name [VARCHAR(200)]
     * @param int $is_parsed [TINYINT]
     * @return string Дата в формате 'YYYY-MM-DD H:i:s'.
     * Пустую строку в случае неудачи
     */
    public function GetEmailRawLastDate($username, $mailbox_name, $is_parsed)
    {
        $this->table->table_name = 'emails_raw';
        
        if ($is_parsed == -1)
        {
            $conditions = 'username=? AND mailbox_name=?';
            $arguments  = array($username, $mailbox_name);
        }
        else
        {
            $conditions = 'username=? AND mailbox_name=? AND is_parsed=?';
            $arguments  = array($username, $mailbox_name, $is_parsed);
        }
        
        $data_set = $this->SelectSingle(array(
            'fields'    => 'date_mail as date',
            'where'     => array(
                'conditions'    => $conditions,
                'arguments'     => $arguments
            ),
            'order'     => 'date_mail DESC',
            'limit'     => 1
        ));

        $this->table->table_name = 'emails';
        
        return $data_set ? $data_set['date'] : '';
    }
    /**
     * Удаляет запись по параметрам
     * @param string $username  [VARCHAR(200)]
     * @param string $mailbox_name [VARCHAR(200)]
     * @param int $message_num [INT]
     */
    public function DeleteEmailRaw($username, $mailbox_name, $message_num)
    {
        $this->table->table_name = 'emails_raw';
        
        $this->DeleteList(array(
            //'fields'    => 'MAX(date) as date',
            'where'     => array(
                'conditions'    => '(username=?) AND (mailbox_name=?) AND (message_num=?)',
                'arguments'     => array($username, $mailbox_name, $message_num),
            ),
        ));
        
        $this->table->table_name = 'emails';
        
        return TRUE;
    }
    
    /**
     * Проверяет существует ли запись с указанными параметрами
     * Облегченная альтернатива проверки в ХП sp_email_raw_save
     * @param string $username
     * @param string $mailbox_name
     * @param int $message_num
     * @param string $message_id
     * @return boolean
     */
    public function IsEmailRawExists($username, $mailbox_name, $message_num, $message_id)
    {
        $this->table->table_name = 'emails_raw';
        
        $data_set = $this->SelectSingle(array(
            'fields'    => 'id',
            'where'     => array(
                'conditions'    => 'username = ? AND mailbox_name = ? AND (message_num=?) AND (message_id=?)',
                'arguments'     => array($username, $mailbox_name, $message_num, $message_id),
            ),
        ));
        
        $this->table->table_name = 'emails';
        
        return isset($data_set) && isset($data_set['id']) ? TRUE : FALSE;
    }
// END EMAILS_RAW methods
    
    
// END EMAIL_OBJECTS methods
    /**
     * Возвращает данные об email-объесте по ID
     * @param type $email_object_id
     * @return type
     */
    public function GetEmailObjectById($email_object_id)
    {
        $this->table->table_name = 'email_objects';
        
        $data_set = $this->SelectSingle(array(
//            'fields'    => '*',
            'where'     => array(
                'conditions'    => 'id=?',
                'arguments'     => array($email_object_id),
            ),
//            'order' => array('RAND()'),
//            'limit' => array('lower' => 0, 'number' => 1),
        ));
        
        $this->table->table_name = 'emails';
        
        return $data_set;
    }
    
    /**
     * Сохраняет данные по EmailObject
     * @param int $email_id [INT] ID emails.id
     * @param int $object_alias [VARCHAR(20)] [biz,company,person]
     * @param int $object_id [INT]
     * @return array()
     */
    public function SaveEmailObject($email_id, $object_alias, $object_id)
    {
        $this->table->table_name = 'email_objects';
        
        $data_set = $this->SelectSingle(array(
            'where'     => array(
                'conditions'    => 'email_id=? AND object_alias=? AND object_id=?',
                'arguments'     => array($email_id, $object_alias, $object_id),
            ),
        ));
        
        if ($data_set && array_key_exists('id', $data_set))
        {
            return $this->GetEmailObjectById($data_set['id']);
        }
        
        $data_set = $this->Insert(array(
            'email_id'      => $email_id,
            'object_alias'  => $object_alias,
            'object_id'     => $object_id,
            'created_at'    => 'NOW()!',
            'created_by'    => 0,
        ));
        
        $this->table->table_name = 'emails';
        
        return $data_set ? $this->GetEmailObjectById($data_set) : array();
    }
    
    /**
     * Сохраняет объекты письма (BIZ/COMPANY/PERSON)
     * @param type $email_id
     * @param type $objects_list
     * @return boolean
     */
    public function SaveEmailObjects($email_id, $objects_list)
    {
        //TODO: Определить более рациональный сброс кеша
        Cache::ClearTag('emails');
        
        $query = "";
        //$query .= "DELETE FROM `email_objects` WHERE `email_id` = '" . $email_id . "';\n";
        $this->table->_exec_raw_query("DELETE FROM `email_objects` WHERE `email_id` = '" . $email_id . "';");
        
        if (empty($objects_list))
        {
            //return $this->table->_exec_raw_query($query);
            return TRUE;
        }
        
        $query .= "INSERT IGNORE INTO `email_objects` (`email_id`,`object_alias`,`object_id`,`created_at`,`created_by`)\n";
        $query .= "VALUES ";
            
        foreach ($objects_list as $item)
        {
            $query .= "('" . $email_id . "','" . $item['object_alias'] . "','" . $item['object_id'] . "',NOW(),'" . $this->user_id . "'),\n";
            Cache::ClearTag($item['object_alias'] . '-' . $item['object_id'] . '-blog');
        }
        
        $query = rtrim($query, ",\n");
        $query .= ";\n";
        
        return $this->table->_exec_raw_query($query);
    }
    
    /**
     * Возвращает список связанных в письмом объектов
     * @param type $email_id
     * @return type
     * 
     * @deprecated 20120914, zharkov: перенес в хп
     */
    public function deprecated_GetEmailObjectsList($email_id)
    {
        if ($email_id <= 0) return array();
        
        $this->table->table_name = 'email_objects';
        
        $data_set = $this->SelectList(array(
            'fields'    => '*,
                CASE `email_objects`.object_alias
                    WHEN \'person\' THEN (SELECT CONCAT(p.`last_name`, \' \', p.`first_name`) FROM `persons` AS p WHERE p.`id` = `email_objects`.object_id)
                    WHEN \'biz\' THEN (SELECT b.`title` FROM `bizes` AS b WHERE b.`id` = `email_objects`.object_id)
                    WHEN \'company\' THEN (SELECT c.`title` FROM `companies` AS c WHERE c.`id` = `email_objects`.object_id)
                    ELSE \'\'
                END AS object_title',
            'where'     => array(
                'conditions'    => 'email_id=?',
                'arguments'     => array($email_id),
            ),
//            'order' => array('RAND()'),
//            'limit' => array('lower' => 0, 'number' => 1),
        ));
        
        $this->table->table_name = 'emails';
        
        return $data_set;
    }
// END EMAIL_OBJECTS methods


// START EMAIL_USERS methods
    /**
     * Сохраняет пользователей письма (Driver/Navigators)
     * @param type $email_id
     * @param type $users_list
     * @return boolean
     */
    public function SaveEmailUsers($email_id, $users_list)
    {
        //TODO: Определить более рациональный сброс кеша
        Cache::ClearTag('emails');
        
        $query = "";
        //$query .= "DELETE FROM `email_users` WHERE `email_id` = '" . $email_id . "';\n";
        $this->table->_exec_raw_query("DELETE FROM `email_users` WHERE `email_id` = '" . $email_id . "';");
        
        if (empty($users_list))
        {
            //return $this->table->_exec_raw_query($query);
            return TRUE;
        }
        
        $query .= "INSERT IGNORE INTO `email_users` (`email_id`,`user_id`,`relation_id`,`created_at`,`created_by`)\n";
        $query .= "VALUES ";
            
        foreach ($users_list as $item)
        {
            $query .= "('" . $email_id . "','" . $item['user_id'] . "','" . $item['relation_id'] . "',NOW(),'" . $this->user_id . "'),\n";
        }
        
        $query = rtrim($query, ",\n");
        $query .= ";\n";
        
        return $this->table->_exec_raw_query($query);
    }
    
    /**
     * Возвращает сисок пользователей письма
     * @param type $email_id
     * @return type
     */
    public function deprecated_GetEmailUsersList($email_id)
    {
        if ($email_id <= 0) return array();
        
        $this->table->table_name = 'email_users';
        
        $data_set = $this->SelectList(array(
            'fields'    => '*
                ,IFNULL((SELECT CONCAT(u.`login`, (CASE WHEN u.`nickname` != "" THEN CONCAT(\' (\',u.`nickname`,\')\') ELSE \'\' END)) FROM `users` AS u WHERE u.`id` = `email_users`.user_id), \'\') AS full_login',
            'where'     => array(
                'conditions'    => 'email_id=?',
                'arguments'     => array($email_id),
            ),
//            'order' => array('RAND()'),
//            'limit' => array('lower' => 0, 'number' => 1),
        ));
        
        $this->table->table_name = 'emails';
        
        return $data_set;
    }
// END EMAIL_USERS methods
    
    
    
// START EMAIL_DELIVERED methods

    
    /**
     * Удаляет письмо (Корзина)
     * 
     * @param int $email_id
     * @param string $object_alias
     * @param int $object_id
     * 
     * @version 20121225, d10n
     */
    public function DeleteByUser($email_id, $object_alias = '', $object_id = 0)
    {
        $this->CallStoredProcedure('sp_email_delete_by_user', array($this->user_id, $email_id));
        
        Cache::ClearTag('emails-deleted-userid-' . $this->user_id);
        Cache::ClearTag('emails-deleted-count-userid-' . $this->user_id . '-objectalias-' . $object_alias  . '-objectid-'. $object_id);
        
        Cache::ClearTag('mailboxes-' . $this->user_id . '-stat');
        
        Cache::ClearTag('email-'            . $email_id);
        Cache::ClearTag('emails-type-'      . EMAIL_TYPE_SPAM);
        Cache::ClearTag('emails-type-'      . EMAIL_TYPE_INBOX);
        Cache::ClearTag('emails-type-'      . EMAIL_TYPE_OUTBOX);
        Cache::ClearTag('email-dfa-list-'   . $email_id); 
        
        Cache::ClearTag('emails');
    }
    
    /**
     * Восстанавливает письмо из удаленных
     * 
     * @param int $user_id
     * @param int $email_id
     * @param string $object_alias
     * @param int $object_id
     * 
     * @version 20130116, d10n
     */
    public function RestoreByUser($user_id, $email_id, $object_alias = '', $object_id = 0)
    {
        $this->CallStoredProcedure('sp_email_restore_deleted_by_user', array($user_id, $email_id));
        
        Cache::ClearTag('emails-deleted-userid-' . $user_id);
        Cache::ClearTag('emails-deleted-count-userid-' . $user_id . '-objectalias-' . $object_alias  . '-objectid-'. $object_id);
        
        Cache::ClearTag('mailboxes-' . $user_id . '-stat');
        
        Cache::ClearTag('email-'        . $email_id);
        Cache::ClearTag('emails-type-'  . EMAIL_TYPE_SPAM);
        Cache::ClearTag('emails-type-'  . EMAIL_TYPE_INBOX);
        Cache::ClearTag('emails-type-'  . EMAIL_TYPE_OUTBOX);
        
        Cache::ClearTag('emails');
    }
    
    /**
     * Возвращает список удаленных писем (Корзина)
     * @param int $user_id
     * @param string $object_alias
     * @param int $object_id
     * @param int $page_no
     * @param int $per_page
     * @return array
     */
    public function GetDeletedByUserList($user_id, $object_alias, $object_id, $page_no = 0, $per_page = ITEMS_PER_PAGE)
    {
        $page_no    = $page_no > 0 ? $page_no : 1;
        $per_page   = $per_page < 1 ? ITEMS_PER_PAGE : $per_page;
        $start      = ($page_no - 1) * $per_page;
        
        $hash       =   'emails-deleted-filter-' . md5('userid-' . $user_id . '-objectalias-' . $object_alias . '-objectid-' . $object_id . '-page-' . $page_no . '-count-' . $per_page);
        
        $cache_tags = array($hash, 'emails', 'emails-deleted', 'emails-deleted-userid-' . $user_id);
        
        $rowset     = $this->_get_cached_data($hash, 'sp_email_get_list_deleted_by_user', array($user_id, $object_alias, $object_id, $start, $per_page), $cache_tags);
        
        return array(
            'data'  => isset($rowset[0]) ? $this->FillEmailInfo($rowset[0]) : array(),
            'count' => isset($rowset[1]) && isset($rowset[1][0]) && isset($rowset[1][0]['rows']) ? $rowset[1][0]['rows'] : 0
        );
    }
    
    /**
     * Возвращает количество удаленных писем (Корзина)
     * @return int
     * 
     * @version 20121226, d10n
     */
    public function GetDeletedByUserCount($user_id, $object_alias, $object_id) 
    {
        if ($count = Cache::GetKey('emails-deleted-count-userid-' . $user_id . '-objectalias-' . $object_alias  . '-objectid-'. $object_id))
        {
            return $count;
        }
        else
        {
            $rowset = $this->GetDeletedByUserList($user_id, $object_alias, $object_id);
            
            Cache::SetKey('emails-deleted-count-userid-' . $user_id . '-objectalias-' . $object_alias  . '-objectid-'. $object_id, $rowset['count']);
            
            return $rowset['count'];
        }
    }
// END EMAIL_DELIVERED methods
    
    /**
     * Возвращает список СПАМ-писем для удаления из системы<br />
     * @version 20121227, d10n
     */
    public function GetListForErase()
    {
        $data_set = $this->CallStoredProcedure('sp_email_get_list_for_erase', array());
        $data_set = isset($data_set[0]) ? $this->FillEmailInfo($data_set[0]) : array();
        
        return $data_set;
    }
    
    /**
     * Удаляет СПАМ-письма из системы по рассписанию (по истечении 30 дней)
     * @version 20121227, d10n
     */
    public function Erase($email_id)
    {
        $data_set = $this->CallStoredProcedure('sp_email_erase', array($email_id));
        $data_set = isset($data_set[0]) ? $data_set[0] : array();
        
        $modelAttachment    = new Attachment();
//        $attachments        = $modelAttachment->FillAttachmentInfo($data_set);
        
        foreach ($data_set as $row)
        {
            $modelAttachment->Remove($row['attachment_id']);
        }
        
        
        Cache::ClearTag('email-'        . $email_id);
        Cache::ClearTag('emails-type-'  . EMAIL_TYPE_SPAM);
    }
    
    /**
     * Возвращает список СПАМ-писем для удаления<br />
     * (для установки параметра emails.is_deleted = 1)
     * 
     * @version 20121227, d10n
     */
    public function GetListForDelete()
    {
        $data_set = $this->CallStoredProcedure('sp_email_get_list_for_delete', array());
        $data_set = isset($data_set[0]) ? $this->FillEmailInfo($data_set[0]) : array();
        
        return $data_set;
    }
    
    
    /**
     * Возвращает количество dfa писем
     * @param int $user_id
     * @param string $object_alias
     * @param int $object_id
     * @return int
     * 
     * @version 20130121, d10n
     */
    public function GetDfaCount($user_id, $object_alias, $object_id)
    {
        $is_admin       = ($this->user_role <= ROLE_ADMIN ? 1 : 0);
        $object_alias   = $object_alias;
        $object_id      = $object_id;
        $mailbox_id     = 0;
        $type_id        = EMAIL_TYPE_DRAFT;
        $doc_type_id    = 0;
        $is_deleted     = 0;
        $approve_by     = $user_id;
        $hash           = 'emails-dfa-count-' . md5('objectalias-' . $object_alias . '-objectid-' . $object_id . '-approveby-' . $approve_by);
        $start          = 0;
        $per_page       = 1000000;
        $cache_tags     = array($hash, 'emails');
        
        $rowset     = $this->_get_cached_data($hash, 'sp_email_get_list', array($this->user_id, $is_admin, $object_alias, $object_id, $mailbox_id, $type_id, $doc_type_id, $is_deleted, $approve_by, $start, $per_page), $cache_tags);
       
        return isset($rowset[1]) && isset($rowset[1][0]) && isset($rowset[1][0]['rows']) ? $rowset[1][0]['rows'] : 0;
    }
    
    /**
     * Возвращает ранжированный список бизнесов,
     * которые учавствуют в письмах за последние 30 дней<br />
     * Примечание: для бокового меню в списке писем
     * @return array
     * 
     * @version 20130121, d10n
     */
    public function GetLastBizes()
    {
        $hash = 'email-bizes-for-menu-userid' . $this->user_id;
        $cache_tags = array($hash, 'email-bizes-for-menu');
        
        $rowset     = $this->_get_cached_data($hash, 'sp_email_get_last_bizes', array($this->user_id), $cache_tags);
        
        $modelBIZ   = new BIZ();
        $rowset     = isset($rowset[0]) ? $modelBIZ->FillBizInfo($rowset[0]) : array();
        
        return array(
            'data'  => $rowset,
            'count' => count($rowset),
        );
    }
    
    /**
     * Возвращает список doc_type-ов<br />
     * Примечание: для бокового меню в списке писем
     * 
     * @param type $object_alias
     * @param type $object_id
     * @return array
     * 
     * @version 20130121, d10n
     */
    public function GetDocTypeForMenu($object_alias, $object_id)
    {
        $rowset     = $this->GetList($object_alias, $object_id, 0, 0, 0, 0, '', 0, 1, 10000);
        
        if (!isset($rowset['data']) || empty($rowset['data'])) return array();
        
        $data_set = array();
        foreach ($rowset['data'] as $email)
        {
            $doc_type = $email['email']['doc_type'];
            
            if ($doc_type <= 0) continue;
            
            $data_set[$doc_type] = true;
        }
        
        $doctype_ids = array_keys($data_set);
        
        $data_set = array();
        foreach ($doctype_ids as $doc_type_id)
        {
            $data_set[] = array(
                'id'    => $doc_type_id,
                'name'  => $this->GetDocTypeById($doc_type_id),
            );
        }
        
        return $data_set;
    }
 
    /**
     * return dfa list for email
     * 
     * @param type $email_id
     * @return type
     * 
     * @version 20130813, sasha
     */
    public function GetHistory($email_id)
    {
        $hash = 'email-dfa-list-' . $email_id; 
        $cache_tags = array($hash, 'email-history-for-email');
        
        $result = $this->_get_cached_data($hash, 'sp_email_get_history', array($email_id), $cache_tags);
        $result = isset($result) && isset($result[0]) ? $result[0] : null;

        if (!isset($result) || (isset($result[0]) && isset($result[0]['ErrorCode']))) return null;

        $modelUser = new User();        
        return $modelUser->FillUserInfo($result, 'created_by');
    }
    
    /**
     * emails attach to biz
     * 
     * @return type
     * 
     * @verion 20130819, sasha
     */
    function SentEmailsAttachToBiz()
    {
        $rowset = $this->CallStoredProcedure('sp_email_get_list_attach_to_biz', array());
        
        $rowset = isset($rowset[0]) && !empty($rowset[0]) ? $rowset[0] : null;
        
        if (empty($rowset)) return "0 rows was attached";
      
        $rowset = $this->FillEmailInfo($rowset);
        
        $modelComponent = new ObjectComponent();
        
        foreach ($rowset as $row)
        {
            if ($row['email']['id'] > 0)
            {
                $objects = array();

                list($row['email']['title'], $matched_objects) = $modelComponent->ParseContent($row['email']['title'], false);
                $objects = array_merge($objects, $matched_objects);

                list($row['email']['description'], $matched_objects) = $modelComponent->ParseContent($row['email']['description'], false);
                $objects = array_merge($objects, $matched_objects);        
                
                $this->SaveAddressObjects($row['email']['id'], $row['email']['sender_address'] . ',' . $row['email']['recipient_address'] . ',' . $row['email']['cc_address']);

                // save objects
                $this->_save_objects($row['email']['id'], $objects); 
            }
            
            $email_id = $row['email']['id'];
            
            Cache::ClearTag('email-' . $email_id . '-objects');
        }
        
        $model = new Model('service');
        $model->Update(1, array('sentemail_id' => $email_id));
        
        Cache::ClearTag('emails');
        Cache::ClearTag('email-bizes-for-menu');
        Cache::ClearTag('emails-type-' . EMAIL_TYPE_OUTBOX);
        Cache::ClearTag('emails-type-' . EMAIL_TYPE_DRAFT);
        
        return count($rowset) . ' rows was attached, last email_id = ' . $email_id;
    }
    
    
    //Закрываем открытые теги
   function close_tags($html)
    {
    $single_tags = array('meta','img','br','link','area','input','hr','col','param','base');
    foreach ($single_tags as $tag) {
      preg_match_all('#<'.$tag.'[^>]*>#Usi', $html, $m);
      foreach ($m[0] as $t) {
        if (!preg_match('#/\s*>#Usi', $t)) {
          $t1 = str_replace('>', ' />', $t);
          $html = str_replace($t, $t1, $html);     
        }  
      }
    }
    return $html;
    }
    
    /**
     * Ниже следует блок методов предназначенный для новой версии Emailmanager
     * @author Sergey Gonchar <fingercrew2@gmail.com>
     */
      
    public function newGetEmails($start_id, $count_rows)
    {       
        $query = "SELECT * FROM emails LIMIT $start_id, $count_rows";
        
        $resource = $this->table->_exec_raw_query($query);
        $email_array = $this->table->_fetch_array($resource);
        
        return $email_array;
    }
}
