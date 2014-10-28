<?php

require_once APP_PATH . 'classes/components/object.class.php';
require_once APP_PATH . 'classes/core/Pagination.class.php';
require_once APP_PATH . 'classes/mailers/emailmailer.class.php';
require_once APP_PATH . 'classes/models/attachment.class.php';
require_once APP_PATH . 'classes/models/biz.class.php';
require_once APP_PATH . 'classes/models/company.class.php';
require_once APP_PATH . 'classes/models/email.class.php';
require_once APP_PATH . 'classes/models/emailmanager.class.php';
require_once APP_PATH . 'classes/models/mailbox.class.php';
require_once APP_PATH . 'classes/models/message.class.php';
require_once APP_PATH . 'classes/models/person.class.php';
require_once APP_PATH . 'classes/models/qc.class.php';
require_once APP_PATH . 'classes/models/sc.class.php';
require_once APP_PATH . 'classes/models/stockoffer.class.php';
require_once APP_PATH . 'classes/models/ra.class.php';
require_once APP_PATH . 'classes/models/user.class.php';

class MainController extends ApplicationController { 

    /**
     * Атачменты к письму
     * 
     * @var mixed
     */
    var $attachments = array();

    function MainController() {
        ApplicationController::ApplicationController();

        $this->authorize_before_exec['compose'] = ROLE_STAFF;
        $this->authorize_before_exec['index'] = ROLE_STAFF;
        $this->authorize_before_exec['edit'] = ROLE_STAFF;
        $this->authorize_before_exec['gethtml'] = ROLE_STAFF;
        $this->authorize_before_exec['view'] = ROLE_STAFF;
        $this->authorize_before_exec['reply'] = ROLE_STAFF;
        $this->authorize_before_exec['spam'] = ROLE_STAFF;
        $this->authorize_before_exec['notspam'] = ROLE_STAFF;
        $this->authorize_before_exec['sendagain'] = ROLE_STAFF;
        $this->authorize_before_exec['inedit'] = ROLE_STAFF;
        $this->authorize_before_exec['deletedbyuser'] = ROLE_STAFF;
        //$this->authorize_before_exec['erase']           = ROLE_MODERATOR;

        $this->context = true;
    }

    /**
     * Подготавливает письмо к отправке еще раз
     * url: /email/{id}/sendagain
     * 
     * @version 20120920, zharkov
     */
    function sendagain() {
        $email_id = Request::GetInteger('id', $_REQUEST);
        if (empty($email_id))
            _404();


        $modelEmail = new Email();
        $email = $modelEmail->GetById($email_id);
        if (empty($email))
            _404();

        $email = $email['email'];
        if ($email['is_deleted'] == 1)
            _404();
        if ($email['type_id'] != EMAIL_TYPE_OUTBOX)
            _404();

        $_REQUEST['object_alias'] = $email['object_alias'];
        $_REQUEST['object_id'] = $email['object_id'];

        if (!isset($_REQUEST['btn_save']) && !isset($_REQUEST['btn_send'])) {
            $sender_address = $modelEmail->ExctractAddress($email['sender_address']);
            $sender_address = isset($sender_address[0]) ? $sender_address[0] : '';    // 20130618, zharkov: new version of ExctractAddress

            if (empty($email['to'])) {
                $recipient_address = $modelEmail->ExctractAddress($email['recipient_address']);
                $recipient_address = isset($recipient_address[0]) ? $recipient_address[0] : '';    // 20130618, zharkov: new version of ExctractAddress

                $modelContactData = new ContactData();
                $contactdata = $modelContactData->FindEmail($recipient_address, 0, 10, true);  // ищет по точному совпадению email-адреса

                $to = '';
                $attention = '';

                if (!empty($contactdata)) {
                    $contactdata = $contactdata[0];
                    $to = isset($contactdata['company']) ? $contactdata['company']['doc_no'] : '';
                    $attention = isset($contactdata['person']) ? $contactdata['person']['doc_no'] : '';
                }
            } else {
                $to = $email['to'];
                $attention = $email['attention'];
                $your_ref = $email['your_ref'];
            }


            $subject = '';
            $title = '';
            $description = '';
            $our_ref = '';

            foreach ($modelEmail->FillObjectInfo($modelEmail->GetObjectsList($email_id)) as $row) {
                if ($row['object_alias'] == 'biz' && isset($row['biz']) && isset($row['biz']['doc_no'])) {
                    $our_ref = $row['biz']['doc_no'];
                    break;
                }
            }


            $user_email = $modelEmail->GetLast();
            $signature = 'With our best regards,';
            $email_no = 1;
            if (!empty($user_email)) {
                $email_no = $user_email['user_emails_count'] + 1;
                $signature = $user_email['signature'];
            } else {
                $modelUser = new User();
                $user = $modelUser->GetById($this->user_id);
                if (isset($user) && isset($user['user'])) {
                    $email_no = $user['user']['user_emails_count'] + 1;
                }
            }

            $our_ref .= (empty($our_ref) ? '' : ', ') . strtolower($_SESSION['user']['nickname']) . substr((10000 + $email_no), 1);



            $_REQUEST['form'] = array(
                'sender_address' => $sender_address,
                'recipient_address' => $email['recipient_address'],
                'to' => $to,
                'attention' => $attention,
                'title' => $title,
                'description' => $email['description'],
                'our_ref' => $our_ref,
                'signature' => $signature
            );

            $_REQUEST['objects'] = $modelEmail->GetObjectsList($email_id);
        }

        //unset($_REQUEST['id']);   @version 20130813, sasha

        $this->_assign('page', 'sendagain');
        $this->_assign('backurl', '/emailmanager/' . $email_id);

        $this->edit();
    }

    /**
     * Помечает письмо как спам
     * url: /email/{id}/notspam
     * 
     * @version 20120914, zharkov
     */
    function notspam() {
        $email_id = Request::GetInteger('id', $_REQUEST);
        if (empty($email_id))
            _404();

        $modelEmail = new Email();
        $email = $modelEmail->GetById($email_id);
        if (empty($email))
            _404();

        $email = $email['email'];
        if ($email['is_deleted'] == 1)
            _404();
        if ($email['type_id'] != EMAIL_TYPE_SPAM)
            _404();


        $modelEmail->NotSpam($email_id, $email['sender_address']);

        $modelMailbox = new Mailbox();
        $mailbox_id = 0;
        foreach ($modelMailbox->FindInString($email['recipient_address']) as $id => $address) {
            $mailbox_id = $id;
            break;
        }

        $this->_redirect(array('emailmanager/filter/mailbox:' . $mailbox_id . ';type:' . EMAIL_TYPE_INBOX), false);
    }

    /**
     * Помечает письмо как спам
     * url: /email/{id}/spam
     * 
     * @version 20120914, zharkov
     */
    function spam() {
        $email_id = Request::GetInteger('id', $_REQUEST);
        if (empty($email_id))
            _404();

        $modelEmail = new Email();
        $email = $modelEmail->GetById($email_id);
        if (empty($email))
            _404();

        $email = $email['email'];
        if ($email['is_deleted'] == 1)
            _404();
        if (!in_array($email['type_id'], array(EMAIL_TYPE_INBOX, EMAIL_TYPE_OUTBOX)))
            _404();

        $modelEmail->Spam($email_id, $email['sender_address']);

        $this->_redirect(array('emailmanager/filter/type:' . EMAIL_TYPE_SPAM), false);
    }

    /**
     * Формирует форму ответа на письмо
     * url: /email/{email_id}/reply
     * 
     */
    function reply() {
        $email_id = Request::GetInteger('id', $_REQUEST);
        if (empty($email_id))
            _404();


        $modelEmail = new Email();
        $email = $modelEmail->GetById($email_id);
        if (empty($email))
            _404();

        $email = $email['email'];
        /*if ($email['is_deleted'] == 1)
            _404();*/
        if ($email['type_id'] != EMAIL_TYPE_INBOX)
            _404();

        $_REQUEST['object_alias'] = $email['object_alias'];
        $_REQUEST['object_id'] = $email['object_id'];

        if (!isset($_REQUEST['btn_save']) && !isset($_REQUEST['btn_send'])) {
            $modelMailbox = new Mailbox();
            $sender_address = '';

            foreach ($modelMailbox->FindInString($email['recipient_address']) as $key => $mailbox) {
                $sender_address = $mailbox;
                break;
            }

            $sender_addresses = $modelEmail->ExctractAddress($email['sender_address']);
            $sender_addresses = isset($sender_addresses[0]) ? $sender_addresses[0] : ''; // 20130618, zharkov: new version of ExctractAddress            

            $modelContactData = new ContactData();
            $contactdata = $modelContactData->FindEmail($sender_addresses, 0, 10, true);  // ищет по точному совпадению email-адреса

            $to = '';
            $attention = '';

            if (!empty($contactdata)) {
                $contactdata = $contactdata[0];
                $to = isset($contactdata['company']) ? $contactdata['company']['doc_no'] : '';
                $attention = isset($contactdata['person']) ? $contactdata['person']['doc_no'] : '';
            }

            $subject = '';
            $your_ref = '';
            $our_ref = '';
            $description = date('d.m.Y', strtotime($email['date_mail']))
                    . '<br>From : ' . str_replace('>', '&gt;', str_replace('<', '&lt;', $email['sender_address']))
                    . '<br>To : ' . str_replace('>', '&gt;', str_replace('<', '&lt;', $email['recipient_address']))
                    . '<br>Subject : ' . $email['title']
                    . '<br><blockquote class="email-answer">'
                    . str_replace("\n", '<br>', $email['description'])
                    . '</blockquote>'
            ;

            $our_ref = '';
            foreach ($modelEmail->FillObjectInfo($modelEmail->GetObjectsList($email_id)) as $row) {
                if ($row['object_alias'] == 'biz' && isset($row['biz']) && isset($row['biz']['doc_no'])) {
                    $our_ref = $row['biz']['doc_no'];
                    break;
                }
            }


            $user_email = $modelEmail->GetLast();
            $signature = 'With our best regards,';
            $email_no = 1;
            if (!empty($user_email)) {
                $email_no = $user_email['user_emails_count'] + 1;
                $signature = $user_email['signature'];
            } else {
                $modelUser = new User();
                $user = $modelUser->GetById($this->user_id);
                if (isset($user) && isset($user['user'])) {
                    $email_no = $user['user']['user_emails_count'] + 1;
                }
            }

            $our_ref .= (empty($our_ref) ? '' : ', ') . strtolower($_SESSION['user']['nickname']) . substr((10000 + $email_no), 1);



            $_REQUEST['form'] = array(
                'sender_address' => $sender_address,
                'recipient_address' => $email['sender_address'],
                'to' => $to,
                'attention' => $attention,
                'title' => 'Re: ' . $email['title'],
                'description' => '',
                'text' => $description,
                'our_ref' => $our_ref,
                'signature' => $signature,
                'parent_id' => $email['id']
            );

            $_REQUEST['objects'] = $modelEmail->GetObjectsList($email_id);
        }

        unset($_REQUEST['id']);

        $this->_assign('page', 'reply');
        $this->_assign('backurl', '/emailmanager/' . $email_id);

        $_REQUEST['form']['parent_id'] = $email_id;
        $_REQUEST['form']['parent'] = $email;

        $this->edit();
    }

    /**
     * Возвращает html контент письма
     * url: /email/gethtml/{email_id}
     * 
     * @version 20120801, zharkov
     */
    function gethtml() {
        $email_id = Request::GetInteger('id', $_REQUEST);
        if (empty($email_id))
            echo 'Error !';

        $emails = new Email();
        $email = $emails->GetById($email_id); 
        if (empty($email))
            echo 'Error receiving email content!';
        if ($email['email']['is_deleted'] == 1)
            echo 'Error receiving email content!';
        
        $this->layout = 'emailmanager/modal';
        echo $email['email']['description_html'];
    }

    /**
     * Отображает страницу просмотра письма
     * url: /email/{id}
     */
    function view() {
        $this->css = 'style-email';

        $object_alias = Request::GetString('object_alias', $_REQUEST);
        $object_id = Request::GetInteger('object_id', $_REQUEST);
        $id = Request::GetInteger('id', $_REQUEST);
        $token = Request::GetString('token', $_REQUEST);
        if (empty($id))
            _404();

        $emails = new Email();
        $email = $emails->GetById($id);
        if (empty($email))
            _404();

        $email = $email['email'];

        /*if ($email['is_deleted'] == 1)
            _404();*/

        //dfa list for email
        $email_history = $emails->GetHistory($id);

        // устанавливает флаг прочитано
        if (in_array($email['type_id'], array(EMAIL_TYPE_INBOX, EMAIL_TYPE_SPAM)) && (!isset($email['userdata']) || !isset($email['userdata']['read_at']))) {
            $emails->SetAsRead($email['id']);
        }
        //получаю информацию об адресатах из табл. email_recipients
            //чтобы получатели не слетали при обновлении страницы
            $modelEmailmanager = new EmailManager();
            $recipients = $modelEmailmanager->GetRecipients($this->user_id);
            if(count($recipients) > 0) {
                $this->_assign('recipients_from_controller', $recipients);
            }
        // атачменты письма
        $attachments = new Attachment();
        $rowset = $attachments->GetListByType('', 'email', $id);
        $this->attachments = $rowset['data'];

        // заменяет адрес отправителя на полный адрес почтового ящика
        if ($email['type_id'] == EMAIL_TYPE_DRAFT) {
            $modelMailbox = new Mailbox();
            $mailbox = $modelMailbox->GetByAddress($email['sender_address']);

            if (!empty($mailbox))
                $email['sender_address'] = $mailbox['full_address'];
        }

        // отправляет письмо        
        if (isset($_REQUEST['btn_send']) && $email['type_id'] == EMAIL_TYPE_DRAFT) {
            // отправка письма
            $emailmailer = new EmailMailer();
            $result = $emailmailer->Send($email, $this->attachments);

            //save dfa
            $dfa_id = $emails->Save($email['id'], $email['object_alias'], $email['object_id'], $email['sender_address'], $email['recipient_address'], $email['cc_address'], $email['bcc_address'], $email['to'], $email['attention'], $email['subject'], $email['our_ref'], $email['your_ref'], $email['title'], $email['description'], $email['signature'], $email['approve_by'], $email['approve_deadline'], $email['doc_type'], $email['seek_response'], $mailbox['id'], $email['parent_id'], $email['signature2'], $email['signature3']);

            // уcтановка флага об отправке в базе
            $emails->MarkAsSent($dfa_id['id'], $email['sender_address'], $email['id']);

            $this->_message('Email was successfully sent !', MESSAGE_OKAY);

            if (empty($object_alias) || empty($object_id)) {
                $modelMailbox = new Mailbox();
                $mailbox = $modelMailbox->GetByAddress($email['sender_address']);

                if (empty($mailbox)) {
                    $this->_redirect(array('emailmanager', 'filter', 'type:' . EMAIL_TYPE_OUTBOX, '#email-' . $id), false);
                } else {
                    $this->_redirect(array('emailmanager', 'filter', 'mailbox:' . $mailbox['id'] . ';type:' . EMAIL_TYPE_OUTBOX, '#email-' . $id), false);
                }
            } else {
                $this->_redirect(array($object_alias, $object_id, 'emailmanager', 'filter', 'type:' . EMAIL_TYPE_OUTBOX, '#email-' . $id), false);
            }
        }

        // формирует список пользователей письма
        $email_users_list = $emails->GetUsersList($email['id']);
        if (!empty($email_users_list)) {
            foreach ($email_users_list as $key => $item) {
                if ($item['relation_id'] != EMAIL_RELATION_DRIVER)
                    continue;

                $email['driver'] = $item;
                unset($email_users_list[$key]);
            }
        }

        $this->_assign('email_users_list', $email_users_list);

        $this->_assign('objects', $emails->FillObjectInfo($emails->GetObjectsList($email['id'])));

        $this->_assign('email', $email);
        $this->_assign('date', date("d.m.Y"));
        $this->_assign('object_alias', $object_alias);
        $this->_assign('object_id', $object_id);

        $this->page_name = !empty($email['title']) ? $email['title'] : '(No subject)';

        $backurl = '/emailmanager';
        if (!empty($token)) {
            $value = Cache::GetKey($token);
            if (!empty($value))
                $backurl = $value;
        }

        $backurl .= '#email-' . $id;

        $this->breadcrumb['emailmanager'] = $backurl;
        $this->breadcrumb[$this->page_name] = '';
        //_epd( $this->attachments);
        $this->_assign('attachments', $this->attachments);
        $this->_assign('backurl', $backurl);
        $this->_assign('email_history', $email_history);
        $this->_assign('include_prettyphoto', true);

        $this->js[] = 'emailmanager_index';
        $this->js[] = 'emailmanager_view';
        $this->topcontext = 'view';

        $this->_display('view');
    }

    /**
     * Отображает страницу со списком писем
     * 
     * url: /{object_alias}/{object_id}/emails
     * url: /emails
     * 
     * @version 20120912, zharkov
     */
    function info() {
        phpinfo();
    }
    function index() {
        //phpinfo();
        //return;
        $modelEmail = new Email();
        //dg($_REQUEST);
        $object_alias = Request::GetString('object_alias', $_REQUEST);
        $object_id = Request::GetInteger('object_id', $_REQUEST);
        $filter = Request::GetString('filter', $_REQUEST);
        $is_dfa = Request::GetBoolean('is_dfa', $_REQUEST);
        $is_dfa_other = Request::GetBoolean('is_dfa_other', $_REQUEST);
        $this->_assign('object_alias', $object_alias);
        $this->_assign('object_id', $object_id);

//        if (isset($_REQUEST['btn_find'])) {
//            $form = $_REQUEST['form'];
//            //debug('1671', $form);   
//            $keyword = Request::GetString('keyword', $form);
//            $sorter = Request::GetString('sorter', $form);
//            $filter = urldecode($filter);
//            $filter_params = array();
//
//            if (!empty($keyword)) {
//                $keyword_id = md5($keyword);
//                $_SESSION[$keyword_id] = $keyword;
//                $filter .= (empty($filter) ? '' : ';') . 'keyword:' . $keyword_id;
//            }
//            
//            if (!empty($sorter)) {
//                $sorter_id = md5($sorter);
//                $_SESSION[$sorter_id] = $sorter;
//                $filter .= (empty($filter) ? '' : ';') . 'sorter:' . $sorter_id;
//            }
//            
//            //not to duplicate keyword
//            if (!empty($filter)) {
//                $filter = explode(';', $filter);
//                foreach ($filter as $row) {
//                    if (empty($row))
//                        continue;
//
//                    $param = explode(':', $row);
//                    $filter_params[$param[0]] = Request::GetHtmlString(1, $param);
//                }
//            }
//
//            $filter_params = array_unique($filter_params);
//            $filter = '';
//
//            if (empty($keyword) && isset($filter_params['keyword']))
//                unset($filter_params['keyword']);
//            
//            if (empty($sorter) && isset($filter_params['sorter']))
//                unset($filter_params['sorter']);
//
//            if (!empty($filter_params)) {
//                foreach ($filter_params as $key => $row) {
//                    $filter .= $key . ":" . $row . ";";
//                }
//            }
//
//            if (empty($object_alias) || empty($object_id)) {
//                //correct redirect
//
//                if ($is_dfa)
//                    $this->_redirect(array('emailmanager', 'dfa', 'filter', str_replace(' ', '+', $filter)), false);
//                else if ($is_dfa_other)
//                    $this->_redirect(array('emailmanager', 'dfa', 'other', 'filter', str_replace(' ', '+', $filter)), false);
//                else
//                    $this->_redirect(array('emailmanager', 'filter', str_replace(' ', '+', $filter)), false);
//            }
//            else {
//                $this->_redirect(array($object_alias, $object_id, 'emailmanager', 'filter', str_replace(' ', '+', $filter)), true);
//            }
//        }

//        $selected_ids = isset($_REQUEST['selected_ids']) ? $_REQUEST['selected_ids'] : array();
//        if (isset($_REQUEST['btn_is_spam'])) {
//            foreach ($selected_ids AS $email_id) {
//                $email = $modelEmail->GetById($email_id);
//                if (empty($email))
//                    continue;
//
//                $email = $email['email'];
//                $modelEmail->Spam($email_id, $email['sender_address']);
//            }
//        }
//        if (isset($_REQUEST['btn_as_read'])) {
//            foreach ($selected_ids AS $email_id) {
//                $modelEmail->SetAsRead($email_id);
//            }
//        }
//        if (isset($_REQUEST['btn_as_unread'])) {
//            foreach ($selected_ids AS $email_id) {
//                $modelEmail->SetAsUnread($email_id);
//            }
//        }
//        if (isset($_REQUEST['btn_is_not_spam'])) {
//            foreach ($selected_ids AS $email_id) {
//                $email = $modelEmail->GetById($email_id);
//                if (empty($email))
//                    continue;
//
//                $email = $email['email'];
//                $modelEmail->NotSpam($email_id, $email['sender_address']);
//            }
//        }
        if (isset($_REQUEST['btn_delete_spam'])) {
            if ($this->user_role > ROLE_MODERATOR)
                _404();

            foreach ($selected_ids AS $email_id) {
                $modelEmail->DeleteSpam($email_id);
            }
        }
        if (isset($_REQUEST['delete_by_user'])) {
            /* foreach($selected_ids AS $email_id)
              {
              $modelEmail->DeleteByUser($email_id, $object_alias, $object_id);
              } */
            $email_id = Request::GetNumeric('email_id', $_REQUEST);
            $modelEmail->DeleteByUser($email_id, $object_alias, $object_id);
        }

//        $filter = urldecode($filter);
//        $filter_params = array();
//
//        $filter = explode(';', $filter);
//        foreach ($filter as $row) {
//            if (empty($row))
//                continue;
//
//            $param = explode(':', $row);
//            $filter_params[$param[0]] = Request::GetHtmlString(1, $param);
//        }
//
//        $mailbox_id = Request::GetInteger('mailbox', $filter_params);
//        $type_id = Request::GetInteger('type', $filter_params, EMAIL_TYPE_INBOX);
//        $doc_type_id = Request::GetInteger('doctype', $filter_params);
//        $keyword = Request::GetString('keyword', $filter_params);
//        $sorter = Request::GetString('sorter', $filter_params);
//        $is_deleted = 0;
//
//        $approve_by = $is_dfa ? $this->user_id : -1;
//        // при $approve_by < 0, в ХП будут выбираться записили с любым emails.approve_by
//
//
//        if ($is_dfa || $is_dfa_other) {
//            $type_id = EMAIL_TYPE_DRAFT;
//            $approve_by = $is_dfa_other ? 0 : $approve_by;
//            // при $approve_by = 0, в ХП будут выбираться записи только с emails.approve_by <= 0
//        }
//
//        if (isset($_SESSION[$keyword])) {
//            $keyword = $_SESSION[$keyword];
//            unset($_SESSION[$keyword]);
//        }
//        
//        if (isset($_SESSION[$sorter])) {
//                $sorter = $_SESSION[$sorter];
//            unset($_SESSION[$sorter]);
//        }
//
//        $rowset = $modelEmail->GetList($object_alias, $object_id, $mailbox_id, $type_id, $doc_type_id, $is_deleted, $keyword, $approve_by, $this->page_no, $sorter);
//                        
//        $pager = new Pagination();
//        //print_r($rowset['count']);
//        $this->_assign('pager_pages', $pager->PreparePages($this->page_no, $rowset['count']));
//
//        $this->_assign('object_stat', array('emails' => $rowset['count']));
//        $this->_assign('list', $rowset['data']);
//
//        $this->_assign('keyword', $keyword);
//        $this->_assign('sorter', $sorter);
//        
//        //for link in right block
//        if (!empty($keyword)) {
//            $this->_assign('keyword_md5', md5($keyword));
//        }
//        
//        //for link in right block
//        if (!empty($sorter)) {
//            $this->_assign('sorter_md5', md5($sorter));
//        }

//        $this->_assign('mailbox_id', $mailbox_id);
//
//        $this->_assign('type_id', $type_id);
//        $this->_assign('doc_type_id', $doc_type_id);
//        $this->_assign('is_dfa', $is_dfa);
//        $this->_assign('is_dfa_other', $is_dfa_other);

        $this->_assign('object_alias', $object_alias);
        $this->_assign('object_id', $object_id);

        $this->page_name = 'Email manager';

        if (empty($object_alias) || empty($object_id)) {
            $this->breadcrumb['Email manager'] = '/emailmanager';
            $modelMailbox = new Mailbox();

            if ($mailbox_id > 0) {
                $mailbox = $modelMailbox->GetById($mailbox_id);
                if (!empty($mailbox)) {
                    $this->page_name = $mailbox['mailbox']['title'];
                    $this->breadcrumb[$mailbox['mailbox']['title']] = '/emailmanager/filter/mailbox:' . $mailbox_id;
                }

                if ($type_id > 0 && !empty($filter[0])) {
                    switch ($type_id) {
                        case EMAIL_TYPE_DRAFT : $this->page_name = $this->page_name . ' Drafts';
                            break;
                        case EMAIL_TYPE_INBOX : $this->page_name = $this->page_name . ' Inbox';
                            break;
                        case EMAIL_TYPE_OUTBOX : $this->page_name = 'Sent from ' . $this->page_name;
                            break;
                        case EMAIL_TYPE_SPAM : $this->page_name = $this->page_name . ' Spam';
                            break;
                    }
                }
            }

            $mailboxes = $this->user_login <= ROLE_ADMIN ? $modelMailbox->GetList() : $modelMailbox->GetListForUser($this->user_id);
            $mailboxes = $modelMailbox->FillUserStat($mailboxes);
            //print_r('1');
            $this->_assign('mailboxes', $mailboxes);
            $this->_assign('spam_count', $modelEmail->GetSpamCount());

            
        } else {
            $objectcomponent = new ObjectComponent();
            $page_params = $objectcomponent->GetPageParams($object_alias, $object_id);
            //_epd($page_params);
            $this->page_name = 'emailmanager'; //$page_params['page_name'];
            array_pop($page_params['breadcrumb']);
            $this->breadcrumb = $page_params['breadcrumb'];

            if ($type_id > 0) {
                $this->breadcrumb['emailmanager'] = '/' . $object_alias . '/' . $object_id . '/emails';
            }


            $modelEmail = new Email();
            $this->_assign('stat', $modelEmail->GetUserStatForObject($object_alias, $object_id));
            $this->_assign('doctypes_list', $modelEmail->GetDocTypeForMenu($object_alias, $object_id));
        }

        $this->_assign('deleted_by_user_count', $modelEmail->GetDeletedByUserCount($this->user_id, $object_alias, $object_id));
        $this->_assign('dfa_count', $modelEmail->GetDfaCount($this->user_id, $object_alias, $object_id));
        $this->_assign('dfa_count_other', $modelEmail->GetDfaCount(0, $object_alias, $object_id));

        if ($type_id > 0 && !empty($filter[0]) && empty($mailbox_id)) {
            switch ($type_id) {
                case EMAIL_TYPE_DRAFT : $this->page_name = 'Drafts';
                    break;
                case EMAIL_TYPE_ERROR : $this->page_name = 'Corrupted';
                    break;
                case EMAIL_TYPE_INBOX : $this->page_name = 'Inbox';
                    break;
                case EMAIL_TYPE_OUTBOX : $this->page_name = 'Sent eMails';
                    break;
                case EMAIL_TYPE_SPAM : $this->page_name = 'Spam';
                    break;
            }
        } elseif ($type_id == 0 && !empty($filter[0]) && empty($mailbox_id) && empty($object_alias)) {
            $this->page_name = 'Email manager';
        }

        if ($is_dfa) {
            $this->page_name = 'My drafts';
        }

        if ($is_dfa_other) {
            $this->page_name = 'Drafts of other users';
        }
        $this->_assign('bizes_list', $modelEmail->GetLastBizes());
        $this->breadcrumb[$this->page_name] = '';
        //_epd($this->breadcrumb);
        $this->js[] = 'emailmanager_index';
        $this->js[] = 'emailmanager_jsonmap';
        $this->css[] = 'jqgridbootstrap';
        //$this->context = true;
        //$this->rcontext = true;
        //$this->layout   = 'rcolumn';

        $token = md5(date('YmdHis'));
        Cache::SetKey($token, $this->pager_path . ($this->page_no > 1 ? '/~' . $this->page_no : ''));
        $this->_assign('token', $token);

        $page = empty($filter_params) && (!$is_dfa && !$is_dfa_other) ? 'all_emails' : '';

        $this->_assign('page', $page);

//        спец лейаут с правой колонкой (контентные зоны с прокруткой)
//        $this->layout = 'email';
//        $hcontext = $this->smarty->fetch('/templates/html/email/hcontext_index.tpl');
//        $this->_assign('hcontext', $hcontext);
        // dg($rowset['data']);
        /* if($_SESSION['user']['id'] == '1671') {
          $this->_display('indexmod');
          } else { */
        //dg($_SESSION);
        		
                $biz_model = new Biz();
                $bizes_data = $biz_model->GetDataFromBizes();
                $this->_assign('data',		$bizes_data);
                
                $biz_menu = $biz_model->GetBizMenu();
                $this->_assign('biz_menu', $biz_menu);
        $this->_display('index'); 
        /* } */
    }

    /**
     * Отображает страницу создания нового письма
     * 
     * url: /email/compose
     * url: /{object_alias}/{object_id}/email/compose
     * 
     * @version 20120803, zharkov: fnnfxvtyns
     */
    function compose() {
        if (!isset($_REQUEST['btn_save']) && !isset($_REQUEST['btn_send'])) {
            $object_alias = Request::GetString('object_alias', $_REQUEST);
            $object_id = Request::GetInteger('object_id', $_REQUEST);
            
            $params = array();
            $objects = array();
                        
            if (!empty($object_alias) && !empty($object_id)) {
                if ($object_alias == 'order') {
                    $modelOrder = new Order();
                    $order = $modelOrder->GetById($object_id);
                    if (empty($order))
                        _404();

                    $order = $order['order'];

                    if (isset($order['biz']))
                        $biz = $order['biz'];
                    if (isset($order['company']))
                        $company = $order['company'];
                    if (isset($order['person']))
                        $person = $order['person'];
                    
                    // (SU) получаю список документов, привязанных к заказу
                    $related_docs_list = array();
                    $modelAttachment = new Attachment();
                    $attachments_list = $modelAttachment->GetListByType('', $object_alias, $object_id);
                    if($attachments_list['count'] > 0){
                        foreach ($attachments_list['data'] as $key => $value) {
                            $this->attachments[]['attachment_id'] = $value['attachment']['id'];
                        }
                    }
                    
                    $your_ref = $order['buyer_ref'];
                    $objects[]  = array('object_alias' => 'order', 'object_id' => $object_id, 'order_id' => $object_id, 'title' => $order['doc_no']);
                }
                else if ($object_alias == 'qc') {
                    $qcs = new QC();
                    $qc = $qcs->GetById($object_id);
                    if (empty($qc))
                        _404();

                    $qc = $qc['qc'];
                    //debug('1682', $qc);
                    if (isset($qc['attachment']))
                        $this->attachments[] = array('attachment_id' => $qc['attachment']['id']);

                    $objects[] = array('object_alias' => 'qc', 'object_id' => $object_id, 'qc_id' => $object_id);

                    if (empty($qc['order_id'])) {
                        $person = array();
                    } else {
                        $orders = new Order();
                        $order = $orders->GetById($qc['order_id']);
                        $order = $order['order'];
                        $person = $order['person'];
                        
                        $objects[] = array('object_alias' => 'order', 'object_id' => $qc['order_id'], 'order_id' => $qc['order_id']);
                    }

                    $params['your_ref'] = $qc['customer_order_no'];

                    if (isset($qc['qcbiz']))
                        $biz = $qc['qcbiz'];
                    if (isset($qc['company']))
                        $company = $qc['company'];
                }
                else if ($object_alias == 'sc') {
                    $scs = new SC();
                    $sc = $scs->GetById($object_id);
                    if (empty($sc))
                        _404();

                    $sc = $sc['sc'];
                    $objects[] = array('object_alias' => 'sc', 'object_id' => $object_id, 'sc_id' => $object_id);

                    if (isset($sc['attachment']))
                        $this->attachments[] = array('attachment_id' => $sc['attachment']['id']);

                    $orders = new Order();
                    $order = $orders->GetById($sc['order_id']);
                    $order = $order['order'];

                    $objects[] = array('object_alias' => 'order', 'object_id' => $sc['order_id'], 'order_id' => $sc['order_id']);

                    $your_ref = $order['buyer_ref'];

                    $biz = $order['biz'];
                    $company = $order['company'];
                    $person = $sc['person'];
                }
                else if ($object_alias == 'ra') {
                    $modelRA = new RA();
                    $ra = $modelRA->GetById($object_id);
                    if (empty($ra))
                        _404();

                    $ra = $ra['ra'];

                    if (isset($ra['attachment']))
                        $this->attachments[] = array('attachment_id' => $ra['attachment']['id']);

                    $objects[] = array('object_alias' => 'ra', 'object_id' => $object_id, 'ra_id' => $object_id, 'title' => $ra['doc_no'],);

                    $ra_items = $modelRA->GetItems($ra['id']);

                    $your_ref = '';
                    foreach ($ra_items as $item) {
                        $steelitem = $item['steelitem'];

                        if (isset($steelitem['order'])) {
                            $objects[] = array('object_alias' => 'order', 'object_id' => $steelitem['order']['id'], 'order_id' => $object_id, 'title' => $steelitem['order']['doc_no'],);
                            $your_ref .= isset($steelitem['order']['buyer_ref']) && !empty($steelitem['order']['buyer_ref']) ? $steelitem['order']['buyer_ref'] . ', ' : '';
                        }
                    }

                    $biz = array();
                    if (isset($ra['stockholder'])) {
                        $company = $ra['stockholder'];
                        $person = isset($ra['stockholder']['key_contact']) ? $ra['stockholder']['key_contact'] : array();
                    }

                    $params['subject'] = 'HR Plates Release Advice ' . $ra['doc_no'];
                    $params['our_ref'] = $ra['doc_no'];
                } else if ($object_alias == 'biz') {
                    $bizes = new Biz();
                    $biz = $bizes->GetById($object_id);
                    if (empty($biz))
                        _404();
                    /*$biz_driver = $biz['biz']['driver_id'];
                    if($biz_driver !== '0' || $biz_driver !== ''){
                        $modelUser = new User();
                        $driver_info = $modelUser->GetById($biz_driver);
                        $driver_name = $driver_info['user']['login'].' ('.$driver_info['user']['nickname'].')';
                        $biz['biz']['driver_name'] = $driver_name;
                    }*/
                    //debug('1682', $biz);
                    $biz = $biz['biz'];
                }
                else if ($object_alias == 'company') {
                    $companies = new Company();
                    $company = $companies->GetById($object_id);
                    if (empty($company))
                        _404();

                    $company = $company['company'];

                    $person_id = $company['key_contact_id'];
                    $persons = new Person();
                    $person = $persons->GetById($person_id);
                    if (!empty($person))
                        $person = $person['person'];
                }
                else if ($object_alias == 'person') {
                    $persons = new Person();
                    $person = $persons->GetById($object_id);
                    if (empty($person))
                        _404();

                    $person = $person['person'];

                    if (!empty($person['company_id'])) {
                        $companies = new Company();
                        $company = $companies->GetById($person['company_id']);
                        if (!empty($company))
                            $company = $company['company'];
                    }
                }

                else if ($object_alias == 'stockoffer') {
                    $modelStockOffer = new StockOffer();
                    $stockoffer = $modelStockOffer->GetById($object_id);
                    if (empty($stockoffer))
                        _404();

                    $stockoffer = $stockoffer['stockoffer'];
                    $objects[] = array('object_alias' => 'stockoffer', 'object_id' => $object_id);

                    if (isset($stockoffer['pdf_attachment']))
                        $this->attachments[] = array('attachment_id' => $stockoffer['pdf_attachment']['id']);
                    /*
                      $orders = new Order();
                      $order  = $orders->GetById($sc['order_id']);
                      $order  = $order['order'];

                      $objects[] = array('object_alias' => 'order', 'object_id' => $sc['order_id'], 'order_id' => $sc['order_id']);

                      $your_ref   = $order['buyer_ref'];

                      $biz        = $order['biz'];
                      $company    = $order['company'];
                      $person     = $sc['person'];
                     */
                }
            }
            
            //получаю информацию об адресатах из табл. email_recipients
            //чтобы получатели не слетали при обновлении страницы
            $modelEmailmanager = new EmailManager();
            $recipients = $modelEmailmanager->GetRecipients($this->user_id);
            if(count($recipients) > 0) {
                $this->_assign('recipients_from_controller', $recipients);
            }
            
            $email_no = 1;
            $title = '';
            $signature = "With our best regards,";

            $modelEmail = new Email();
            $user_email = $modelEmail->GetLast();
            //dg($user_email);
            if (!empty($user_email)) {
                $email_no = $user_email['user_emails_count'] + 1;
                $signature = $user_email['signature'];
                $form['sender_address'] = $user_email['sender_address'];
            } else {
                $modelUser = new User();
                $user = $modelUser->GetById($this->user_id);
                if (isset($user) && isset($user['user'])) {
                    $email_no = $user['user']['user_emails_count'] + 1;
                }
            }

            $email_no = strtolower($_SESSION['user']['nickname']) . substr((10000 + $email_no), 1);

            if (isset($biz) && !empty($biz)) {
                $params['our_ref'] = $biz['doc_no'] . ', ' . $email_no;
                $title .= $biz['doc_no'] . '.';
                $driver_name = $biz['driver']['login'].' ('.$biz['driver']['nickname'].')';
                $objects[] = array('object_alias' => 'biz', 'object_id' => $biz['id'], 'biz_id' => $biz['id'], 'biz_driver' => $biz['driver_id'], 'biz_driver_name' => $driver_name);
            }

            if (isset($company) && !empty($company)) {
                $params['to'] = $company['title'];
                $co_title = isset($company['doc_no']) ? $company['doc_no'] : $company['title'];
                $title .= empty($co_title) ? '' : $co_title . '.';

                $objects[] = array('object_alias' => 'company', 'object_id' => $company['id'], 'company_id' => $company['id']);
            }

            if (isset($person) && !empty($person)) {
                $contactdata = new ContactData();
                $email = '';

                foreach ($contactdata->GetList('person', $person['id']) as $row) {
                    if ($row['type'] == 'email') {
                        $email .= '"' . $person['full_name'] . '" <' . $row['title'] . '>, ';
                    }
                }

                $params['attention'] = $person['full_name'];
                $params['recipient_address'] = $email;

                $objects[] = array('object_alias' => 'person', 'object_id' => $person['id'], 'person_id' => $person['id']);
            }
            //dg($params['our_ref']);
            if(empty($params['our_ref'])) $params['our_ref'] = $email_no;
            $params['your_ref']  = isset($your_ref) ? $your_ref : '';
            $params['title']     = ' (' . $title . $email_no . ')';
            $params['signature'] = $signature;

            $attachments = new Attachment();
            $attachments->SetUploadedIds('newemail-' . $this->user_id, $this->attachments);

            $distinct_objects = array();
            foreach ($objects as $key => $row) {
                if (isset($distinct_objects[$row['object_alias'] . '-' . $row['object_id']])) {
                    unset($objects[$key]);
                } else {
                    $distinct_objects[$row['object_alias'] . '-' . $row['object_id']] = true;
                }
            }

            $_REQUEST['form'] = $params;
            $_REQUEST['objects'] = $objects;
            $_REQUEST['object_alias'] = $object_alias;
            $_REQUEST['object_id'] = $object_id;
        }
//        dg($params['our_ref']);
        $this->_assign('include_mce', true);
        $this->_assign('page', 'compose');
        $this->edit();
    }

    /**
     * Отображает страницу редактирования
     * url: /email/{id}/edit
     * 
     * @version 01.10.2014 Uskov Sergey Добавлено:
     *  - отправка аттачментов, добавленных из системы (присоединяются к общему массиву с аттачами);
     *  - отправка письма получателям, добавленным из системы;
     */
    function edit() {
        //$emails = new Email();
        $object_alias = Request::GetString('object_alias', $_REQUEST);
        $object_id    = Request::GetInteger('object_id', $_REQUEST);
        $email_id     = Request::GetInteger('id', $_REQUEST);
        //debug('1682', $_REQUEST);
        if ($email_id > 0) {
            $emails = new Email();
            $email = $emails->GetById($email_id);
            if (empty($email))
                _404();

            $email = $email['email'];
            //dg($email);
            /*if ($email['is_deleted'] == 1)
                _404();*/

            $object_alias = Request::GetString('object_alias', $email);
            $object_id = Request::GetInteger('object_id', $email);
        }
        else {
            $email = array('approve_by' => 0);

            $object_alias = Request::GetString('object_alias', $_REQUEST);
            $object_id = Request::GetInteger('object_id', $_REQUEST);
        }


        $uploader_object_alias = empty($email_id) ? 'newemail' : 'email';
        $uploader_object_id = empty($email_id) ? $this->user_id : $email_id;

        if (isset($_REQUEST['btn_save']) || isset($_REQUEST['btn_send'])) {
            $form = $_REQUEST['form'];
            $sender_address    = Request::GetString('sender_address', $form);
            $recipient_address = Request::GetString('recipient_address', $form);
            $cc_address        = Request::GetString('cc_address', $form);
            $bcc_address       = Request::GetString('bcc_address', $form);
            $title             = Request::GetString('title', $form);
            $to                = Request::GetString('to', $form);
            $attention         = Request::GetString('attention', $form);
            $subject           = Request::GetString('subject', $form);
            $our_ref           = Request::GetString('our_ref', $form);
            $your_ref          = Request::GetString('your_ref', $form);
            $description       = Request::GetHtmlString('description', $form, true);
            $signature         = Request::GetString('signature', $form);
            $signature2        = Request::GetString('signature2', $form);
            $signature3        = Request::GetString('signature3', $form);

            $approve_by        = Request::GetInteger('approve_by', $form);
            $approve_deadline  = Request::GetString('approve_deadline', $form);
            $driver            = Request::GetInteger('driver', $form);
            $navigator         = Request::GetInteger('navigator', $form);
            $doc_type          = Request::GetInteger('doc_type', $form);
            $seek_response     = Request::GetDateForDB('seek_response', $form);
            $driver_id         = Request::GetInteger('driver_id', $form);
            $email_users_list  = isset($_REQUEST['navigators']) ? $_REQUEST['navigators'] : array();
            $objects           = isset($_REQUEST['objects']) ? $_REQUEST['objects'] : array();
            $parent_id         = Request::GetInteger('parent_id', $form);
//            _epd($form['description'], false);
//            _epd($description);
            $arr[]=$uploader_object_alias;
            $arr[]=$uploader_object_id;
            $attachments = new Attachment();
            $this->attachments = $attachments->GetUploadedIds($uploader_object_alias . '-' . $uploader_object_id, $this->attachments);
            
            //(SU) id вложенных документов из системы добавляю в массив с вложениями к письму 
            $shared_doc_ids = $_SESSION['doc_ids'];
            if(count($shared_doc_ids) > 0){
                foreach ($shared_doc_ids as $key => $value) {
                    $this->attachments[count($this->attachments)]['attachment_id'] = $value;
                }
            }
            
            // преобразовывает массив объектов письма к нормальному виду
            $objects_list = array();
            foreach ($objects as $object => $id) {
                $object = explode('-', $object);
                $objects_list[] = array('object_alias' => $object[0], 'object_id' => $object[1], $object[0] . '_id' => $object[1]);
            }
            $objects = $objects_list;

            // начало проверок и сохранения

            if (empty($sender_address)) {
                $this->_message('Team must be specified !', MESSAGE_ERROR);
            }
            /*          else if (empty($recipient_address))
              {

              $this->_message('To must be specified !', MESSAGE_ERROR);
              }
              else if (empty($title))
              {
              $this->_message("Subject must be specified !", MESSAGE_ERROR);
              }
              else if (empty($to))
              {
              $this->_message("'To' must be specified !", MESSAGE_ERROR);
              }
              else if (empty($attention))
              {
              $this->_message("'Attention' must be specified !", MESSAGE_ERROR);
              }
              else if (empty($subject))
              {
              $this->_message("'Subject' must be specified !", MESSAGE_ERROR);
              }
              else if (empty($our_ref))
              {
              $this->_message("'Our Ref.' must be specified !", MESSAGE_ERROR);
              }
              else if (empty($your_ref))
              {
              $this->_message("'Your Ref.' must be specified !", MESSAGE_ERROR);
              }
              else if (empty($description))
              {
              $this->_message("Text must be specified !", MESSAGE_ERROR);
              }
              else if (empty($signature))
              {
              $this->_message("Signature must be specified !", MESSAGE_ERROR);
              }
              else if (empty($signature3))
              {
              $this->_message("Author signature must be specified !", MESSAGE_ERROR);
              }
              /*
              else if (empty($driver_id))
              {
              $this->_message('Driver must be specified !', MESSAGE_ERROR);
              }
             */
            /*           else if ($approve_by > 0 && empty($approve_deadline))
              {
              $this->_message('Approve deadline must be specified !', MESSAGE_ERROR);
              } */ else {

                $modelMailbox = new Mailbox();
                $mailbox = $modelMailbox->GetByAddress($sender_address);
                $mailbox_id = $mailbox['id'];
                
//СОХРАНЕНИЕ ЧЕРНОВИКА
                $emails = new Email();
                if ($email_id > 0 && ($object_alias != $email['object_alias'] || $object_id != $email['object_id'] ||
                        $sender_address != $email['sender_address'] || $recipient_address != $email['recipient_address'] ||
                        $cc_address != $email['cc_address'] || $bcc_address != $email['bcc_address'] || $to != $email['to'] || $attention != $email['attention'] ||
                        $subject != $email['subject'] || $our_ref != $email['our_ref'] || $your_ref != $email['your_ref'] || $title != $email['title'] || $description != $email['description'] ||
                        $signature != $email['signature'] || $approve_by != $email['approve_by'] || $doc_type != $email['doc_type'] ||
                        $parent_id != $email['parent_id'] || $signature2 != $email['signature2'] ||
                        $signature3 != $email['signature3']) || isset($_REQUEST['btn_send'])) 
                {
                    //сохранение письма при отправке:
                    $result = $emails->Save($email_id, $object_alias, $object_id, $sender_address, $recipient_address, $cc_address, $bcc_address, $to, $attention, $subject, $our_ref, $your_ref, $title, $description, $signature, $approve_by, $approve_deadline, $doc_type, $seek_response, $mailbox_id, $parent_id, $signature2, $signature3, $driver, $navigator);
                    
                    //Пересохраняю получателей из системы в табл. email_recipients (object_alias, object_id)
                    $modelEmailmanager = new EmailManager();
                    $modelEmailmanager->UpdateRecipientsForSend($result['id']);
                    
                    //debug('1682', $result['id']);
                } 
                //когда выполняется этот расклад пока неясно :(
                else if (empty($email_id)) {
                    $result = $emails->Save($email_id, $object_alias, $object_id, $sender_address, $recipient_address, $cc_address, $bcc_address, $to, $attention, $subject, $our_ref, $your_ref, $title, $description, $signature, $approve_by, $approve_deadline, $doc_type, $seek_response, $mailbox_id, $parent_id, $signature2, $signature3, $driver, $navigator);
                } else{
                    $result['id'] = $email_id;
                }

                if (empty($result)) {
                    $this->_message("Error while saving email!", MESSAGE_ERROR);
                }
                else {
                    //сохраняет пользователей текущего письма
                    $email_users = array(array('user_id' => $driver_id, 'relation_id' => EMAIL_RELATION_DRIVER));

                    foreach ($email_users_list as $item) {
                        $email_users[] = array('user_id' => (int) $item['user_id'], 'relation_id' => EMAIL_RELATION_NAVIGATOR);
                    }

                    $emails->SaveEmailUsers($result['id'], $email_users);

                    // parse email content
                    $matched_object_list = array();
                    $title_objects = array();
                    $description_objects = array();
                    $subject_objects = array();
                    $our_ref_objects = array();

                    $component = new ObjectComponent();
                    list($title, $title_objects) = $component->ParseContent($title, false);
                    list($description, $description_objects) = $component->ParseContent($description, false);
                    list($subject, $subject_objects) = $component->ParseContent($subject, false);
                    list($our_ref, $our_ref_objects) = $component->ParseContent($our_ref, false);

                    // merge found objects
                    $matched_object_list = array_merge($title_objects, $description_objects);
                    $matched_object_list = array_merge($matched_object_list, $subject_objects);
                    $matched_object_list = array_merge($matched_object_list, $our_ref_objects);

                    // converts an array of emails to the normal view
                    $matched_objects = array();
                    foreach ($matched_object_list as $object => $row) {
                        $matched_objects[] = array('object_alias' => $row['alias'], 'object_id' => $row['id'], $row['alias'] . '_id' => $row['id']);
                    }

                    if (empty($objects) && !empty($matched_objects)) {
                        $objects = $matched_objects;
                    } else if (!empty($objects) && !empty($matched_objects)) {
                        // merge found objects and already saved
                        $objects = array_merge($objects, $matched_objects);
                    }

                    // сохраняет объекты письма
                    $emails->SaveEmailObjects($result['id'], $objects);
                    
                    // если есть приаттаченные файлы, добавляет их к сохраненному письму
                    $attachments = new Attachment();
                    $attachments->AssignUploaded($uploader_object_alias . '-' . $uploader_object_id, 'email', $result['id']);

                    // формирование сообщения в TL для проверяющего черновик
                    if ($approve_by > 0 && $email['approve_by'] != $approve_by) {
                        /*
                          $modelUser          = new User();
                          $approver           = $modelUser->GetById($approve_by);
                         */
                        $modelMessage = new Message();
                        $subject = empty($title) ? '(no subject)' : $title;
                        $type_id = MESSAGE_TYPE_NORMAL;
                        $role_id = ROLE_STAFF;  //  $approver['user']['role_id'];
                        $sender_id = $this->user_id;
                        $recipient = $approve_by;
                        $cc = '';
                        $title_source = htmlspecialchars($subject);
                        $description_source = 'Please check my DFA : <a href="/emailmanager/' . $result['id'] . '">' . htmlspecialchars($subject) . '</a>';
                        $parent_id = 0;
                        $deadline = $approve_deadline;
                        $alert = 0;
                        $pending = 1;

                        $message = $modelMessage->Add($type_id, $role_id, $sender_id, $recipient, $cc, $title_source, $description_source, $parent_id, $deadline, $alert, $pending, false);

                        if (isset($message['id'])) {
                            $message_id = $message['id'];
                            $type_id = MESSAGE_TYPE_NORMAL;
                            $role_id = ROLE_STAFF; //$approver['user']['role_id'];
                            $sender_id = $approve_by;

                            $objects_list[] = array('object_alias' => 'email', 'object_id' => $result['id']);
                            foreach ($objects_list as $key => $row) {
                                $objects_list[$key]['alias'] = $row['object_alias'];
                                $objects_list[$key]['id'] = $row['object_id'];
                            }

                            $mobject = $modelMessage->SaveObjects($message_id, $type_id, $role_id, $sender_id, $objects_list);
                        }
                    }
//ОТПРАВКА ПИСЬМА
                    if (isset($_REQUEST['btn_send'])) {
                        $email = $emails->GetById($result['id']);
                        $email = $email['email'];
                        //письмо перед отправкой сохраняется, и при отправке у него уже есть ID
                        //в массив $email добавляю получателей из системы
                        //получаю информацию о адресатах из табл. email_recipients
                        
                        $modelEmailmanager = new EmailManager();
                        $recipients = $modelEmailmanager->GetRecipients($result['id']);
                            //debug('1682', $recipients);
                        //формирую строку с получателями из системы, если они есть
                        $recipients_addresses = '';
                        if(count($recipients) > 0) {
                            foreach ($recipients as $key => $value) {
                                //заменяю в адресе "&lt;" и "&gt;" на "<" и ">"
                                $adress = str_replace("&lt;", "<", $value['email_adress']);
                                $adress = str_replace("&gt;", ">", $adress);
                                $recipients_addresses .= $adress.', ';
                            }
                        }
                        
                        //временно сохраняю имеющихся получателей
                        $tmp_recipient_address = $email['recipient_address'];
                        $email['recipient_address'] = $recipients_addresses.$tmp_recipient_address;
                        
                        $emailmailer = new EmailMailer();
                        $emailmailer->Send($email, $attachments->FillAttachmentInfo($this->attachments));

                        // уcтановка флага об отправке в базе
                        $emails->MarkAsSent($result['id'], $email['sender_address'], $email['id']);
                        
                        // обновляю родительское окно и вывожу уведомление об отправке, закрываю окно редактирования.
                        print "<script> window.opener.location.reload(); </script>";
                        print "<script> window.opener.alert('Email was sent.'); </script>";
                        print "<script> window.close(); </script>";
                        //$this->_message('Email was successfully sent !', MESSAGE_OKAY);
                    } else {
                        // обновляю родительское окно и вывожу уведомление об отправке, закрываю окно редактирования.
                        print "<script> window.opener.location.reload(); </script>";
                        print "<script> window.opener.alert('Email was saved.'); </script>";
                        print "<script> window.close(); </script>";
                        //$this->_message("Email was saved successfully !", MESSAGE_OKAY);
                    }

                    if (empty($object_alias) || empty($object_id)) {
                        $token = md5(date('YmdHis'));
                        Cache::SetKey($token, '/emails/filter/mailbox:' . $mailbox_id . ';type:' . EMAIL_TYPE_DRAFT);
                        //$this->_redirect(array('emailmanager', $result['id'] . '~tid' . $token), false);
                        //$this->_message('Email was successfully sent !', MESSAGE_OKAY);
                    } else {
                        if (isset($_REQUEST['btn_save'])) {
                            $token = md5(date('YmdHis'));
                            Cache::SetKey($token, '/emails/filter/mailbox:' . $mailbox_id . ';type:' . EMAIL_TYPE_DRAFT);
                            //$this->_redirect(array('emailmanager', $result['id'] . '~tid' . $token), false);
                        } else {
                            //$this->_redirect(array($object_alias, $object_id, 'email', $result['id']));
                            print "<script> window.opener.alert('Email was sent.'); </script>";
                            print "<script> window.close(); </script>";
                            //$this->_redirect(array($object_alias, $object_id, 'blog'));
                        }
                    }
                }
            }
        } 
//РЕДАКТИРОВАНИЕ ПИСЬМА
        else if ($email_id > 0) {
            $form = $email;
            $attachments = new Attachment();
            $result = $attachments->GetIdsForObject('email', $email_id);
            $this->attachments = $result['data'];

            $modelEmail = new Email();
            $objects = $modelEmail->GetObjectsList($email_id);
            $email_users = $emails->GetUsersList($email_id);
            
            //получаю информацию об адресатах из табл. email_recipients
            $modelEmailmanager = new EmailManager();
            $recipients = $modelEmailmanager->GetRecipients($email_id);
            if(count($recipients) > 0) {
                $this->_assign('recipients_from_controller', $recipients);
            }
            
        } 
//REPLY
        else {
            $form = isset($_REQUEST['form']) ? $_REQUEST['form'] : array();
            $form['driver_id'] = $this->user_id;
            $form['doc_type'] = 0;
            //dg($form['parent']['description']);

            $objects = isset($_REQUEST['objects']) ? $_REQUEST['objects'] : array();
            /*
              $object_list        = isset($_REQUEST['objects']) ? $_REQUEST['objects'] : array();
              $objects            = array();
              foreach ($object_list as $object)
              {
              $object         = explode('-', $object);
              $objects[]      = array('object_alias' => $object[0], 'object_id' => $object[1], $object[0] . '_id' => $object[1]);
              }
             */

            $email_users = array();
        }

        $attachments = new Attachment();
        $attachments->SetUploadedIds($uploader_object_alias . '-' . $uploader_object_id, $this->attachments);

        // пользователи письма
        $modelUser = new User();
        $mam_list  = $modelUser->GetDriversList();
        // team list
        $modelPerson = new Person();
        $team_list   = $modelPerson->GetMamList();

        if (!empty($email_users)) {
            foreach ($mam_list as $key => $row) {
                foreach ($email_users as $row1) {
                    if (isset($row1['relation_id']) && $row1['relation_id'] == EMAIL_RELATION_DRIVER) {
                        $form['driver_id'] = $row1['user_id'];
                        continue;
                    }

                    if ($row['user']['id'] == $row1['user_id'])
                        $mam_list[$key]['selected'] = 1;
                }
            }
        }

        $this->_assign('mam_list', $mam_list);
        $this->_assign('team_list', $team_list);

        // типы писем
        $modelEmail = new Email();
        $this->_assign('objects', $modelEmail->FillObjectInfo($objects));
        $this->_assign('doctypes_list', $modelEmail->GetDocTypesList());


        $this->page_name = $email_id > 0 ? 'Edit eMail' : 'New eMail';

        if (empty($object_alias) || empty($object_id)) {
            $this->breadcrumb = array(
                'eMails' => '/emails',
                $this->page_name => ''
            );
        } else {
            $objectComponent = new ObjectComponent();
            $data = $objectComponent->GetPageParams($object_alias, $object_id, $this->page_name);

            $this->breadcrumb = $data['breadcrumb'];
        }

        $this->_assign('object_alias', $object_alias);
        $this->_assign('object_id', $object_id);

        $this->_assign('uploader_object_alias', $uploader_object_alias);
        $this->_assign('uploader_object_id', $uploader_object_id);

        $this->_assign('include_mce', true);
        $this->_assign('include_ui', true);
        $this->_assign('include_upload', true);

        $this->_assign('form', $form);
        $this->_assign('email', $email);
        $this->_assign('email_id', $email_id);

        if (isset($form['sender_address']) && !empty($form['sender_address'])) {
            $sender_address = explode('@', $form['sender_address']);
            $this->_assign('sender_domain', $sender_address[1]);
        }

        $attachments = new Attachment();
        $this->_assign('attachments', $attachments->FillAttachmentInfo($this->attachments));
        $countries = new Country();
        $this->_assign('countries', $countries->GetListShort());
        
        if (isset($country_id) && !empty($country_id))
        {
            $regions = new Region();
            $this->_assign('regions', $regions->GetList($country_id));
        }

        if (isset($region_id) && !empty($region_id))
        {
            $cities = new City();
            $this->_assign('cities', $cities->GetList($region_id));
        }
        
        $activities = new Activity();
        $this->_assign('industries', $activities->GetList(0));        

        if (isset($industry_id) && !empty($industry_id))
        {
            $this->_assign('activities', $activities->GetList($industry_id));
        }

        if (isset($activity_id) && !empty($activity_id))
        {
            $this->_assign('specialities', $activities->GetList($activity_id));
        }
                
        $products = new Product();
        $this->_assign('products',  $products->GetTree());
        
        $modelMailbox = new Mailbox();
        $mailboxes = $this->user_role > ROLE_ADMIN ? $modelMailbox->GetListForUser($this->user_id) : $modelMailbox->GetList();
//        $mailboxes = $modelMailbox->GetListForUser($this->user_id);
        //dg($mailboxes);
        
        $modelCompany = new Company();
        $this->_assign('co_types_list',     $modelCompany->GetCoTypesList());
        $this->_assign('co_statuses_list',  $modelCompany->GetCoStatusesList());
        $this->_assign('co_relations_list', $modelCompany->GetCoRelationsList());
        $this->_assign('mailboxes', $mailboxes);

        $this->js[] = 'emailmanager_edit';
        $this->js[] = 'emailmanager_us_edit';
        $this->js[] = 'emailmanager_index';
        $this->layout = 'emailmanager/modal';
        $this->_display('edit');
    }

    /**
     * Отображает форму редактирования входящего письма
     * @version undefined, d10n
     */
    public function inedit() {
        $object_alias = Request::GetString('object_alias', $_REQUEST);
        $object_id = Request::GetInteger('object_id', $_REQUEST);

        $id = Request::GetInteger('id', $_REQUEST, -1);

        if ($id <= 0)
            _404();

        $modelEmail = new Email();
        $email = $modelEmail->GetById($id);
        $email = $email['email'];

        if (empty($email) || $email['type_id'] != EMAIL_TYPE_INBOX)
            _404();

        if ($email['is_deleted'] == 1)
            _404();

        if (isset($_REQUEST['btn_save'])) {
            $form = isset($_REQUEST['form']) ? $_REQUEST['form'] : array();

            $approve_by = Request::GetInteger('approve_by', $form);
            $approve_deadline = Request::GetDateForDB('approve_deadline', $form);
            $doc_type = Request::GetInteger('doc_type', $form);
            $seek_response = Request::GetDateForDB('seek_response', $form);
            $driver_id = Request::GetInteger('driver_id', $form);
            $email_users_list = isset($_REQUEST['navigators']) ? $_REQUEST['navigators'] : array();
            $objects = isset($_REQUEST['objects']) ? $_REQUEST['objects'] : array();

            // преобразовывает массив объектов письма к нормальному виду
            $objects_list = array();
            foreach ($objects as $object => $value) {
                $object = explode('-', $object);
                $objects_list[] = array('object_alias' => $object[0], 'object_id' => $object[1], $object[0] . '_id' => $object[1]);
            }
            $objects = $objects_list;

            if ($approve_by > 0 && empty($approve_deadline)) {
                $this->_message('Approve deadline must be specified !', MESSAGE_ERROR);
            } else {
                $modelMailbox = new Mailbox();
                $mailbox = $modelMailbox->GetByAddress($email['sender_address']);

                $seek_response = $seek_response ? $seek_response : 'NULL VALUE!';
                $approve_deadline = $approve_deadline ? $approve_deadline : 'NULL VALUE!';

                $result = $modelEmail->UpdateInboxMail($id, $approve_by, $approve_deadline, $doc_type, $seek_response);

                if (empty($result)) {
                    $this->_message("Error while saving email !", MESSAGE_ERROR);
                } else {
                    $result = $result['email'];

                    //сохраняет пользователей текущего письма
                    $email_users = array(array('user_id' => $driver_id, 'relation_id' => EMAIL_RELATION_DRIVER));

                    foreach ($email_users_list as $item) {
                        $email_users[] = array('user_id' => (int) $item['user_id'], 'relation_id' => EMAIL_RELATION_NAVIGATOR);
                    }

                    $modelEmail->SaveEmailUsers($result['id'], $email_users);

                    // сохраняет объекты письма
                    $modelEmail->SaveEmailObjects($result['id'], $objects);

                    if ($approve_by > 0 && ($email['approve_by'] != $approve_by || $email['approve_deadline'] != $approve_deadline)) {
                        $modelMessage = new Message();
                        $subject = empty($email['title']) ? '(no subject)' : $email['title'];
                        $type_id = MESSAGE_TYPE_NORMAL;
                        $role_id = ROLE_STAFF;  //  $approver['user']['role_id'];
                        $sender_id = $this->user_id;
                        $recipient = $approve_by;
                        $cc = '';
                        $title_source = htmlspecialchars($subject);
                        $description_source = 'Please check my DFA : <a href="/emailmanager/' . $email['id'] . '">' . htmlspecialchars($email['title']) . '</a>';
                        $parent_id = 0;
                        $deadline = $approve_deadline;
                        $alert = 0;
                        $pending = 1;

                        $message = $modelMessage->Add($type_id, $role_id, $sender_id, $recipient, $cc, $title_source, $description_source, $parent_id, $deadline, $alert, $pending, false);

                        if (isset($message['id'])) {
                            $message_id = $message['id'];
                            $type_id = MESSAGE_TYPE_NORMAL;
                            $role_id = ROLE_STAFF; //$approver['user']['role_id'];
                            $sender_id = $approve_by;

                            $objects_list[] = array('object_alias' => 'email', 'object_id' => $email['id']);
                            foreach ($objects_list as $key => $row) {
                                $objects_list[$key]['alias'] = $row['object_alias'];
                                $objects_list[$key]['id'] = $row['object_id'];
                            }

                            $mobject = $modelMessage->SaveObjects($message_id, $type_id, $role_id, $sender_id, $objects_list);
                        }
                    }

                    $this->_message("Email was saved successfully !", MESSAGE_OKAY);

                    if (empty($object_alias) || empty($object_id)) {
                        $token = md5(date('YmdHis'));
                        Cache::SetKey($token, '/emails/filter/' . (isset($mailbox['id']) ? 'mailbox:' . $mailbox['id'] . ';' : '') . 'type:' . EMAIL_TYPE_INBOX);

                        $this->_redirect(array('email', $result['id'] . '~tid' . $token), false);
                    } else {
                        $this->_redirect(array($object_alias, $object_id, 'email', $result['id']));
                    }
                }
            }
        } else {
            $form = $email;
        }


        $objects = $modelEmail->GetObjectsList($email['id']);
        $email_users = $modelEmail->GetUsersList($email['id']);

        // пользователи письма
        $modelUser = new User();
        $mam_list = $modelUser->GetMamList();

        if (!empty($email_users)) {
            foreach ($mam_list as $key => $row) {
                foreach ($email_users as $row1) {
                    if (isset($row1['relation_id']) && $row1['relation_id'] == EMAIL_RELATION_DRIVER) {
                        $form['driver_id'] = $row1['user_id'];
                        continue;
                    }

                    if ($row['user']['id'] == $row1['user_id'])
                        $mam_list[$key]['selected'] = 1;
                }
            }
        }

        $this->_assign('mam_list', $mam_list);


        // типы писем
        $this->_assign('objects', $modelEmail->FillObjectInfo($objects));
        $this->_assign('doctypes_list', $modelEmail->GetDocTypesList());


        $modelAttachment = new Attachment();
        $this->_assign('attachments', $modelAttachment->FillAttachmentInfo($this->attachments));

        $this->page_name = 'Edit eMail';
        $this->breadcrumb = array(
            'eMails' => '/emails',
            $this->page_name => ''
        );

        $this->js = 'emailmanager_edit';

        $this->_assign('include_ui', true);
        $this->_assign('form', $form);
        $this->_assign('email', $email);

        $this->_display('inedit');
    }

    /**
     * Отображает список удаленных файлов (Корзина)
     * @url /emails/deleted
     * @version 20121227, d10n
     */
    public function deletedbyuser() {
        $object_alias = Request::GetString('object_alias', $_REQUEST);
        $object_id = Request::GetInteger('object_id', $_REQUEST);
        $filter = Request::GetString('filter', $_REQUEST);

        if (isset($_REQUEST['btn_find'])) {
            $form = $_REQUEST['form'];
            $keyword = Request::GetString('keyword', $form);

            if (empty($keyword))
                $this->_redirect(array('emails', 'deleted'));

            $keyword_id = md5($keyword);
            $_SESSION[$keyword_id] = $keyword;
            $filter = 'keyword:' . $keyword_id;

            if (empty($object_alias) || empty($object_id)) {
                $this->_redirect(array('emails', 'deleted', 'filter', str_replace(' ', '+', $filter)), false);
            } else {
                $this->_redirect(array($object_alias, $object_id, 'emails', 'deleted', 'filter', str_replace(' ', '+', $filter)), true);
            }
        }

        $modelEmail = new Email();

        $selected_ids = isset($_REQUEST['selected_ids']) ? $_REQUEST['selected_ids'] : array();

        if (isset($_REQUEST['delete_by_user'])) {
            foreach ($selected_ids AS $email_id) {
                $modelEmail->DeleteByUser($email_id, $object_alias, $object_id);
            }
        }
        if (isset($_REQUEST['restore_by_user'])) {
            foreach ($selected_ids AS $email_id) {
                $modelEmail->RestoreByUser($this->user_id, $email_id, $object_alias, $object_id);
            }
        }


        $filter = urldecode($filter);
        $filter_params = array();

        $filter = explode(';', $filter);
        foreach ($filter as $row) {
            if (empty($row))
                continue;

            $param = explode(':', $row);
            $filter_params[$param[0]] = Request::GetHtmlString(1, $param);
        }

        $keyword = Request::GetString('keyword', $filter_params);

        if (isset($_SESSION[$keyword])) {
            $keyword = $_SESSION[$keyword];
            unset($_SESSION[$keyword]);
        }

        $rowset = $modelEmail->GetDeletedByUserList($this->user_id, $object_alias, $object_id, $this->page_no);

        $pager = new Pagination();
        $this->_assign('pager_pages', $pager->PreparePages($this->page_no, $rowset['count']));

        $this->_assign('object_stat', array('emails' => $rowset['count']));
        $this->_assign('list', $rowset['data']);

        $this->_assign('keyword', $keyword);

        //for link in right block
        if (!empty($keyword)) {
            $this->_assign('keyword_md5', md5($keyword));
        }

        $this->_assign('mailbox_id', 0);
        $this->_assign('type_id', 0);

        $this->_assign('object_alias', $object_alias);
        $this->_assign('object_id', $object_id);

        $this->page_name = 'Deleted By Me';

        if (empty($object_alias) || empty($object_id)) {
            $this->breadcrumb['eMails'] = '/emails';
            $modelMailbox = new Mailbox();

            $mailboxes = $this->user_login <= ROLE_ADMIN ? $modelMailbox->GetList() : $modelMailbox->GetListForUser($this->user_id);
            $mailboxes = $modelMailbox->FillUserStat($mailboxes);

            $this->_assign('mailboxes', $mailboxes);
            $this->_assign('spam_count', $modelEmail->GetSpamCount());
            //$this->_assign('bizes_list',    $modelEmail->GetLastBizes());
        } else {
            $objectcomponent = new ObjectComponent();
            $page_params = $objectcomponent->GetPageParams($object_alias, $object_id);

            $this->page_name = 'Deleted By Me'; //$page_params['page_name'];
            array_pop($page_params['breadcrumb']);
            $this->breadcrumb = $page_params['breadcrumb'];
            $this->breadcrumb['eMails'] = '/' . $object_alias . '/' . $object_id . '/emails';

            $this->_assign('stat', $modelEmail->GetUserStatForObject($object_alias, $object_id));
        }

        $this->_assign('deleted_by_user_count', $modelEmail->GetDeletedByUserCount($this->user_id, $object_alias, $object_id));
        $this->_assign('dfa_count', $modelEmail->GetDfaCount($this->user_id, $object_alias, $object_id));
        $this->_assign('dfa_count_other', $modelEmail->GetDfaCount(0, $object_alias, $object_id));

        $this->breadcrumb[$this->page_name] = '';

        $this->js = 'emailmanager_index';
        $this->context = true;
        $this->rcontext = true;
        $this->layout = 'rcolumn';

        $this->_assign('page', 'deleted_by_user');

        $token = md5(date('YmdHis'));
        Cache::SetKey($token, $this->pager_path . ($this->page_no > 1 ? '/~' . $this->page_no : ''));
        $this->_assign('token', $token);

        $this->_display('index');
    }

    /**
     * CRON
     * Удаляет СПАМ письма из системы (связи и аттачменты)
     * 
     * @url /email/erase/spam/{$key}
     * @version 20121227, d10n
     */
    public function erase() {
        $key = Request::GetString('key', $_REQUEST, '', 24);

        if ($key != 'a23ef5kf9')
            die();

        $modelEmail = new Email();

        $emails = $modelEmail->GetListForErase();

        foreach ($emails as $email) {
            $email = $email['email'];

            $modelEmail->Erase($email['id']);
        }

        die('ok');
    }

    /**
     * CRON
     * Удаляет СПАМ письма (помечает emails.is_deleted = 1)
     * 
     * @url /email/delete/spam/{$key}
     * @version 20121227, d10n
     */
    public function deletefromspam() {
        $key = Request::GetString('key', $_REQUEST, '', 24);

        if ($key != 'a23ef5kf9')
            die();

        $modelEmail = new Email();
        $emails = $modelEmail->GetListForDelete();

        foreach ($emails as $email) {
            $email = $email['email'];

            $modelEmail->DeleteSpam($email['id']);
        }

        die('ok');
    }
    
    
    public function getmd() {
        $str = 'mamvillageSE13';
        echo md5($str);
    }    

    public function rafix() {
        $ids_str = "2892, 3027, 3046, 3047, 3048, 3049, 3231, 3232, 3297, 3298, 3299, 3300, 9697, 3296, 11514, 16992, 17581, 18490, 19614, 21092, 21127, 21128, 21129, 21130, 21131, 21132, 21133, 21134, 21135, 21154, 21155, 21156, 21157, 21158, 21159, 20666, 21225, 21229, 21040, 21041, 21042, 21043, 8053, 12043, 13497, 13708, 17106, 17107, 17327, 17524, 17555, 17558, 17560, 17561, 17563, 17564, 17578, 18287, 18397, 18728, 18736, 18753, 18756, 18757, 18758, 18759, 19276, 19542, 19599, 19600, 19968, 19970, 19974, 19975, 19976, 19977, 19979, 19980, 19981, 19982, 247, 263, 20154, 20400, 20403, 20577, 20578, 20579, 20580, 20581, 20619, 20624, 20663, 20664, 20669, 20672, 20720, 20721, 20765, 20766, 20767, 20778, 20940, 20942, 21035, 21044, 21046, 21049, 21052, 21053, 21055, 21057, 21059, 21061, 21067, 21072, 21078, 21093, 21126, 21136, 21137, 21138, 21139, 21140, 21141, 21153, 21191, 21208, 17551, 17557, 18044, 18286, 18752, 19496, 19507, 19511, 19539, 19540, 18340, 18395, 18396, 18477, 18479, 18285, 18478, 17184, 20043, 20665, 20670, 20671, 21210, 21212, 20673, 20678, 19526, 12102, 20444, 20445, 20446, 20447, 20448, 20449, 20450, 20451, 20452, 20453, 20454, 20455, 20456, 20457, 20458, 20459, 20460, 20461, 20502, 20503, 20513, 20515, 20545, 20553, 20554, 20555, 20556, 20557, 20569, 20570, 18404, 19315, 19396, 19393, 20784, 20786, 20951, 20978, 20979, 20980, 20981, 20982, 20983, 20984, 20986, 20987, 20995, 20996, 20997, 20998, 20999, 20508, 20785, 20787, 20914, 20915, 20918, 20921, 20923, 20943, 20946, 20985, 20988, 20989, 20990, 20991, 20992, 20993, 19615, 18856, 6192, 7917, 7918, 7975, 7976, 7982, 7987, 8007, 8598, 9692, 9758, 10012, 10243, 11753, 11756, 18076, 18077, 18078, 18080, 18081, 18110, 18112, 18113, 18161, 18375, 18380, 18381, 18382, 18888, 18890, 18892, 18896, 18897, 18929, 18930, 18996, 19124, 19133, 19141, 19142, 19152, 19164, 19167, 19170, 19172, 19178, 19179, 19180, 19181, 19194, 19197, 19198, 19264, 19280, 19281, 19288, 19299, 19300, 19301, 19302, 19303, 19312, 19316, 19361, 19377, 19388, 19392, 19395, 19397, 19401, 19403, 19405, 19406, 19410, 19413, 19414, 19415, 19416, 19417, 21031, 19334, 18088, 18160, 18853, 18891, 19109, 19330, 19337, 20514, 20542, 20632, 20633, 20634, 20635, 20636, 20637, 20638, 20639, 20643, 11748, 11749, 11750, 11751, 11752";
        $ids_arr = explode(', ', $ids_str);
        
        $modelEmail = new Email();
        
        foreach ($ids_arr as $row) {
            $q_select_status = "SELECT status_id AS status FROM steelitem_timeline WHERE steelitem_id ='{$row}' ORDER BY id DESC LIMIT 1";
            $res_status = $modelEmail->table->_exec_raw_query($q_select_status);
            $arr_status = $modelEmail->table->_fetch_array($res_status);
            $status = $arr_status['0'];
            if(empty($status)) $status = 0;
            //$q_update_status = "UPDATE steelitems SET status_id = '{$status}' WHERE id = '{$row}'";
            //$res_status = $modelEmail->table->_exec_raw_query($q_update_status);
        }
        echo 'Okey!';
        //dg($ids_arr);
    }
}
