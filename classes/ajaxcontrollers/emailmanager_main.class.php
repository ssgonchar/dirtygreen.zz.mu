<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor. 
 */
require_once APP_PATH . 'classes/models/mailbox.class.php';
require_once APP_PATH . 'classes/models/email.class.php';
require_once APP_PATH . 'classes/models/emailfilter.class.php';
require_once APP_PATH . 'classes/models/contactdata.class.php';

class MainAjaxController extends ApplicationAjaxController {

    public function __construct() {
        ApplicationAjaxController::ApplicationAjaxController();

        $this->authorize_before_exec['index'] = ROLE_STAFF;
    }

    public function index() {
        
    }

    public function getmailboxes() {
        $modelMailbox = new Mailbox();
        $modelEmail = new Email();
        $mailboxes = $modelMailbox->GetListForUser($this->user_id);

        /*
          foreach ($mailboxes as &$row) {
          $arg = array(
          ''
          );
          $count = $modelEmail->Count($arg);
          }
         */

        $this->_assign('mailboxes', $mailboxes);
        $this->_send_json(array(
            'result' => 'okay',
            'content' => $this->smarty->fetch('templates/html/emailmanager/control_mailboxes.tpl') //json.content
        ));
    }

    public function getemails() {
        $firstvisiblerow = $_GET['recordstartindex'];
        $lastvisiblerow = $_GET['recordendindex'];
        $rowscount = $lastvisiblerow - $firstvisiblerow;

        $modelEmail = new Email();

        $arg = array(
            'limit' => array(
                'lower' => $firstvisiblerow,
                'number' => $rowscount,
            ),
            //'where' => 'id IN (13,15,17)',
            'order' => 'id DESC'
        );

        $rowset = $modelEmail->table->SelectList($arg);

        // get data and store in a json array
        foreach ($rowset as $row) {
            $emails[] = array(
                'OrderID' => $row['email_raw_id'],
                'OrderDate' => $row['created_at'],
                'ShippedDate' => $row['date_mail'],
                'ShipName' => $row['title'],
                'ShipAddress' => $row['recipient_address'],
                'ShipCity' => $row['type_id'],
                'ShipCountry' => $row['id']
            );
        }

        $data[] = array(
            'TotalRows' => 999,
            'Rows' => $emails
        );
        echo json_encode($data);
    }

    /**
     * findcompany
     * 
     * Получает информацию из $_REQUEST. Ищет компании по входящим параметрам. Возвращает массив с результатами поиска
     * 
     * @link /emailmanager/findcompany
     * @return array
     * @author SU
     */
    public function findcompany() {
        //данные формы поиска:
        $keyword       = Request::GetString('keyword', $_REQUEST);
        $company_id    = Request::GetInteger('company_id', $_REQUEST);
        $country_id    = Request::GetInteger('country_id', $_REQUEST);
        $region_id     = Request::GetInteger('region_id', $_REQUEST);
        $city_id       = Request::GetInteger('city_id', $_REQUEST);
        $industry_id   = Request::GetInteger('industry_id', $_REQUEST);
        $activity_id   = Request::GetInteger('activity_id', $_REQUEST);
        $speciality_id = Request::GetInteger('speciality_id', $_REQUEST);
        $product_id    = Request::GetInteger('product_id', $_REQUEST);
        $feedstock_id  = Request::GetInteger('feedstock_id', $_REQUEST);
        $relation_id   = Request::GetString('relation_id', $_REQUEST);
        $status_id     = Request::GetString('status_id', $_REQUEST);

        $companies = new Company();
        $contactdatas = new ContactData();
        
        if($company_id > 0){
            $emails = array();
            //получаю адреса компании (если таковые имеются)
            $rowset[] = array('company_id' => $company_id);
            $company_contacts    = $companies->FillCompanyContacts($rowset);
            $company_contacts    = isset($company_contacts['0']['companycontacts']) ? $company_contacts['0']['companycontacts'] : array();
            if(!empty($company_contacts)){
                foreach ($company_contacts as $key => $row) {
                    if($row['type'] == 'email'){
                        $emails['company_contacts'][] = $row['title'];
                    }
                }
            }
            //получаю список сотрудников (employees) и их имэйлы (если таковые имеются)
            $sorted_companies_list = array();
            $persons = $companies->GetPersons($company_id);
            if($persons['count'] > 0){
                foreach ($persons['data'] as $subkey => $subrow) {
                    $person_id = $subrow['person']['id'];
                    $person_name = $subrow['person']['doc_no'];
                    /*if(isset($subrow['person']['jobposition']) && !empty($subrow['person']['jobposition']['title'])){
                        $person_name .= ' ('.$subrow['person']['jobposition']['title'].')';
                    }*/
                    
                    $cd = $contactdatas->GetList('person', $person_id);
                    $addresses = array();
                    foreach ($cd as $cd_key => $cd_value) {
                        if($cd_value['type'] == 'email'){
                            $addresses[] = $cd_value['title'];
                        }
                    }
                    if(!empty($addresses)){
                        $person_info['name'] = $person_name;
                        $person_info['emails'] = $addresses;
                        $emails['employees_contacts'][] = $person_info;
                    }
                }
                if(!empty($emails['employees_contacts'])){
                    $emails['company_id'] = $company_id;
                    $emails['company_title'] = $keyword;
                    //Формирую массив с необходимыми данными
                    $sorted_companies_list[] = $emails;
                }
            }
        }
        else{
            $sorted_companies_list = array();
            $companies_list = $companies->Search($keyword, $country_id, $region_id, $city_id, $industry_id, $activity_id, $speciality_id, $product_id, $feedstock_id, $relation_id, $status_id);
            if ($companies_list['count'] > 0) {
                foreach ($companies_list['data'] as $key => $row) {
                    $emails = array();
                    //проверяю наличие адресов компании
                    if(isset($row['companycontacts'])){
                        foreach ($row['companycontacts'] as $subkey => $subrow){
                            if($subrow['type'] == 'email'){
                                $emails['company_contacts'][] = $subrow['title'];
                            }
                        }
                    }
                    //проверяю наличие адресов персон
                    if(isset($row['company']['key_contact'])){
                        $person_name = $row['company']['key_contact']['full_name'];
                        $jobposition = isset($row['company']['key_contact']['jobposition']) ? '('.$row['company']['key_contact']['jobposition']['title'].')' : '';
                        foreach ($row['company']['key_contact_contacts'] as $subkey => $subrow) {
                            //проверка на наличие собаки в адресе и на повторение адреса
                            $is_email = strrpos($subrow['title'], "@");
                            if($is_email !== false && in_array($subrow['title'], $emails['personal_contacts'][$person_name]) == false){
                                $emails['personal_contacts']['name'] = $person_name.' '.$jobposition;
                                $emails['personal_contacts']['email'][] = $subrow['title'];
                            }
                        }
                    }
                    //Фильтрую массив - пропускаю компании, у которых нет емайл адреса
                    if(count($emails) < 1) {
                        continue;
                    }

                    $emails['company_id'] = $row['company_id'];
                    $emails['company_title'] = $row['company']['title'];

                    //Формирую массив с необходимыми данными
                    $sorted_companies_list[] = $emails;
                }
                //доп. фильтр - проверяю имя компании и имейл адреса на наличие совпадения с keyword
                //снова перебираю массив $sorted_companies_list
                foreach ($sorted_companies_list as $key => $row) {
                    $delete_this_row = TRUE;   //переменная-индикатор наличия совпадений по keyword

                    //ищу совпадения по ключевому слову в подмассиве company_title
                    $subject = $row['company_title'];
                    $pattern = '/'.$keyword.'/i';
                    preg_match($pattern, $subject, $matches);
                    if (!empty($matches)) $delete_this_row = FALSE;

                    //ищу совпадения по ключевому слову в имейлах компаний
                    $keyword = preg_replace('/M\s*-\s*a\s*-\s*M/ui', 'mam', $keyword);  //если ключевое слово "M-a-M" -  заменяю на mam
                    //привожу ключевое слово к нижнему регистру для поиска по адресам
                    $keyword = mb_strtolower($keyword);
                    foreach ($row['company_contacts'] as $row_cc) {
                        $subject = $row_cc;
                        $pattern = '/'.$keyword.'/i';
                        preg_match($pattern, $subject, $matches);
                        if (!empty($matches)) $delete_this_row = FALSE;

                    }
                    //нужно проверить на совпадение персональные адреса
                    foreach ($row['personal_contacts']['email'] as $row_pc) {
                        $subject = $row_pc;
                        $pattern = '/'.$keyword.'/i';
                        preg_match($pattern, $subject, $matches);
                        if (!empty($matches)) $delete_this_row = FALSE;

                    }
                    //удаляю компанию, если совпадений не найдено
                    if($delete_this_row == TRUE){
                        unset($sorted_companies_list[$key]);
                    }
                }
            } else {
                $this->_send_json(array(
                    'result' => 'error', 'code' => 'No emails found'
                ));
            }
        }
        //debug('1682', $sorted_companies_list);
        if(count($sorted_companies_list) > 0){
            $this->_assign('companies_list', $sorted_companies_list);
            $this->_send_json(array(
                'result' => 'okay',
                'companies_list' => $this->smarty->fetch('templates/html/emailmanager/control_companies.tpl')
            ));
        }  else {
            $this->_send_json(array(
                'result' => 'error', 'code' => 'No emails found'
            ));
        }
    }
    
    /**
     * Формирует массив и заполняет таблицу поиска адресатов (по person name).
     * 
     * @param string $person_name имя персоны
     * @param string $company_name имя компании
     * @link /emailmanager/getrecipient
     * @author Sergey Uskov <archimba8578@gmail.com>
     */
    public function getrecipient()
    {
        $person_name   = Request::GetString('person_name', $_REQUEST);
        $company_name  = Request::GetString('company_name', $_REQUEST);
        $person_email  = Request::GetString('person_email', $_REQUEST);
        
        //вычисляю Id компании по её имени
        $modelEmail = new Email();
        $query  = '';
        $query .= "SELECT * ";
        $query .= "FROM `companies` ";
        $query .= "WHERE title = '{$company_name}'";
        $resource = $modelEmail->table->_exec_raw_query($query);
        $result   = $modelEmail->table->_fetch_array($resource);
        
        $company_id = $result['0']['id'];
        
        $sorted_companies_list = array();
        $personal_contacts = array();
        
        //формирую массив, аналогичный массиву поиска email адресов по имени компании
//        $personal_contacts[$person_name] = array('0' => $person_email);
        $personal_contacts['name']    = $person_name;
        $personal_contacts['email'][] = $person_email;
        
        $sorted_companies_list['0']['personal_contacts'] = $personal_contacts;
        $sorted_companies_list['0']['company_id'] = $company_id;
        $sorted_companies_list['0']['company_title'] = $company_name;
        //debug('1682', $sorted_companies_list);
        
        $this->_assign('companies_list', $sorted_companies_list);
        $this->_send_json(array(
            'result' => 'okay',
            'companies_list' => $this->smarty->fetch('templates/html/emailmanager/control_companies.tpl')
        ));
    }
    
    /**
     * getshareddocs
     * 
     * Получает информацию из $_REQUEST. Ищет вложенные документы. Возвращает массив с результатами поиска.
     * 
     * @link /emailmanager/getshareddocs
     * @param string $id идентификатор обьекта для поиска
     * @param string $alias идентификатор для определения ресурса для поиска
     * @return array
     * @author Sergey Uskov <archimba8578@gmail.com>
     */
    function getshareddocs() {
        $id = Request::GetInteger('id', $_REQUEST);
        $alias = Request::GetString('alias', $_REQUEST);
        //нужно передать в функцию GetListByType аргумент 'biz' если алиас = 'task-biz'
        $alias = ($alias == 'task-biz' ? 'biz' : $alias);
        $modelAttachment = new Attachment();
        $attachments_list = $modelAttachment->GetListByType('', $alias, $id);
        //debug('1682', $attachments_list);
        if ($attachments_list['count'] !== '0') {
            $this->_assign('attachments_list', $attachments_list['data']);
            $this->_send_json(array(
                'alias' => $alias,
                'result' => 'okay',
                'attachments_list' => $this->smarty->fetch('templates/html/emailmanager/object_shared_files.tpl')
            ));
        } else {
            $this->_send_json(array('result' => 'error', 'code' => 'Files not found'));
        }
    }

    /**
     * addobject
     * 
     * Получает информацию из $_REQUEST. Устанавливает привязку email к biz. Возвращает строку для таблицы emails
     * 
     * @link /emailmanager/addobject
     * @return string
     * @author SG
     */
    public function addobject() {
        $email_id = Request::GetInteger('email_id', $_REQUEST);
        $object_id = Request::GetInteger('object_id', $_REQUEST);
        $object_alias = Request::GetString('object_alias', $_REQUEST);

        Cache::ClearTag('emails');

        $modelEmail = new Email();

        $query .= "INSERT IGNORE INTO `email_objects` (`email_id`,`object_alias`,`object_id`)";
        $query .= " VALUES ('{$email_id}', 'biz', '{$object_id}')";

        $result = $modelEmail->table->_exec_raw_query($query);

        $email = $modelEmail->GetById($email_id);

        $this->_assign('row', $email);
        $row = $this->smarty->fetch('templates/html/emailmanager/control_objects.tpl');

        $this->_send_json(array(
            'result' => 'ok',
            'objects' => $savedObjects,
            'email' => $email,
            'html' => $row,
                )
        );
    }

    /**
     * delobject
     * 
     * Получает информацию из $_REQUEST. Удаляет привязку email к biz. Возвращает строку для таблицы emails
     * 
     * @link /emailmanager/delobject
     * @return string
     * @author SG
     */
    public function delobject() {
        $email_id = Request::GetInteger('email_id', $_REQUEST);
        $object_id = Request::GetInteger('object_id', $_REQUEST);
        $object_alias = Request::GetString('object_alias', $_REQUEST);



        $modelEmail = new Email();

        $query .= "DELETE FROM `email_objects` ";
        $query .= " WHERE email_id = '{$email_id}' AND object_alias='biz' AND object_id='{$object_id}'";

        $result = $modelEmail->table->_exec_raw_query($query);
        Cache::ClearTag('emails');
        $modelEmail = new Email();
        $email = $modelEmail->GetById($email_id);

        $this->_assign('row', $email);
        $row = $this->smarty->fetch('templates/html/emailmanager/control_objects.tpl');

        $this->_send_json(array(
            'result' => 'ok',
            'objects' => $savedObjects,
            'email' => $email,
            'html' => $row,
                )
        );
    }

    /**
     * getfilters
     * 
     * Получает информацию из $_REQUEST. Возвращает заполненный шаблон таблицы фильтров.
     * 
     * @link /emailmanager/getfilter
     * @return string
     * @author SG
     */
    public function getfilters() {
        $modelEmailFilter = new EmailFilter();
        $filters = $modelEmailFilter->GetListAll();

        $this->_assign('filter_list', $filters['data']);
        $html = $this->smarty->fetch('templates/html/emailmanager/control_filters.tpl');

        $this->_send_json(array(
            'result' => 'ok',
            'request' => $_REQUEST,
            'filters' => $filters,
            'html' => $html,
                )
        );
    }

    /**
     * getfilter
     * 
     * Получает информацию из $_REQUEST. Возвращает заполненный шаблон формы редактирования фильтра.
     * 
     * @link /emailmanager/getfilter
     * @return string
     * @author SG
     */
    public function getfilter() {
        $filter_id = Request::GetInteger('filter_id', $_REQUEST);

        $modelEmailFilter = new EmailFilter();
        $filter = $modelEmailFilter->GetById($filter_id);

        $this->_assign('item', $filter);
        $html = $this->smarty->fetch('templates/html/emailmanager/control_filter_edit.tpl');

        $this->_send_json(array(
            'result' => 'ok',
            'request' => $_REQUEST,
            'filter' => $filter,
            'html' => $html,
                )
        );
    }

    /**
     * editfilter
     * 
     * Обновляет запись о фильтре в БД. Получает информацию из $_REQUEST
     * 
     * @link /emailmanager/editfilter
     * @return string
     * @author SG
     */
    public function editfilter() {
        $filter_id = Request::GetNumeric('filter_id', $_REQUEST);

        $modelEmailFilter = new EmailFilter();
        $filter = $modelEmailFilter->GetById($filter_id);

        $params = Request::GetString('params', $_REQUEST);
        $tags = Request::GetString('tags', $_REQUEST);
        $is_sheduled = Request::GetString('is_sheduled', $_REQUEST);
        $today = date('Y-m-d h:m:s');
        $autor = $_SESSION['user']['id'];

        $query .= "UPDATE `efilters` "
                . "SET `params` = '{$params}', "
                . "`tags` = '{$tags}', "
                . "`is_scheduled` = '{$is_sheduled}', "
                . "`modified_at` = '{$today}', "
                . "`modified_by` = '{$autor}' "
                . "WHERE id = '{$filter_id}'";

        $result = $modelEmailFilter->table->_exec_raw_query($query);

        Cache::ClearTag('efilters');
        if ($filter_id > 0)
            Cache::ClearTag('efilter-' . $filter_id);
        $modelEmailFilter = new EmailFilter();
        $filter = $modelEmailFilter->GetById($filter_id);

        $this->_assign('item', $filter);
        $html = $this->smarty->fetch('templates/html/emailmanager/control_filter_edit.tpl');
        $this->_send_json(array(
            'result' => 'okay',
            'query' => $query,
            'html' => $html,
        ));
    }

    /**
     * addfilter
     * 
     * Добавляет запись о фильтре в БД. Получает информацию из $_REQUEST
     * 
     * @link /emailmanager/editfilter
     * @return string
     * @author SG
     */
    public function addfilter() {
        $filter_id = Request::GetNumeric('filter_id', $_REQUEST);

        $modelEmailFilter = new EmailFilter();

        $params = Request::GetString('params', $_REQUEST);
        $tags = Request::GetString('tags', $_REQUEST);
        $is_sheduled = Request::GetString('is_sheduled', $_REQUEST);
        $today = date('Y-m-d h:m:s');
        $autor = $_SESSION['user']['id'];

        $query .= "INSERT INTO `efilters` "
                . "SET `params` = '{$params}', "
                . "`tags` = '{$tags}', "
                . "`is_scheduled` = '{$is_sheduled}', "
                . "`modified_at` = '{$today}', "
                . "`modified_by` = '{$autor}' ";

        $result = $modelEmailFilter->table->_exec_raw_query($query);

        $arg = array(
            'fields' => 'MAX(id) AS maxid',
            'where' => 'modified_by = "' . $autor . '"',
        );

        $rowset = $modelEmailFilter->table->SelectList($arg);

        foreach ($rowset as $row) {
            $filter_id = $row['maxid'];
        }

        Cache::ClearTag('efilters');
        if ($filter_id > 0)
            Cache::ClearTag('efilter-' . $filter_id);
        $modelEmailFilter = new EmailFilter();
        $filter = $modelEmailFilter->GetById($filter_id);

        $this->_assign('item', $filter);
        $html = $this->smarty->fetch('templates/html/emailmanager/control_filter_edit.tpl');
        $this->_send_json(array(
            'result' => 'okay',
            'query' => $query,
            'html' => $html,
        ));
    }

    /**
     * deletefilter
     * 
     * удаляет запись о фильтре в БД. Получает информацию из $_REQUEST
     * 
     * @link /emailmanager/deletefilter
     * @return string
     * @author SG
     */
    public function deletefilter() {
        $filter_id = Request::GetNumeric('filter_id', $_REQUEST);

        $modelEmailFilter = new EmailFilter();

        $query = "DELETE FROM efilters WHERE id = '{$filter_id}'";

        $result = $modelEmailFilter->table->_exec_raw_query($query);
        Cache::ClearTag('efilters');
        if ($filter_id > 0)
            Cache::ClearTag('efilter-' . $filter_id);    
        $modelEmailFilter = new EmailFilter();
        $filters = $modelEmailFilter->GetListAll();

        $this->_assign('filter_list', $filters['data']);
        $html = $this->smarty->fetch('templates/html/emailmanager/control_filters.tpl');

        $this->_send_json(array(
            'result' => 'ok',
            'request' => $_REQUEST,
            'filters' => $filters,
            'html' => $html,
                )
        );        
    }
    
    /**
     * Cохраняет выбранные аттачменты в массив $_SESSION, удаляет невыбранные. Выводит список аттачей на страницу редактирования письма.
     * 
     * @param string $uploader_object_alias newemail или email
     * @param string $uploader_object_id user_id или email_id
     * @param array $attached_ids Массив с id аттачментов из регистра
     * @param array $not_attached_ids Массив с id  аттачментов которые нужно удалить из сессии
     * 
     * @author Sergey Uskov <archimba8578@gmail.com>
     */
    function saveshareddocs()
    {
        $attached_ids            = $_REQUEST['attached_ids'];
        $not_attached_ids        = $_REQUEST['not_attached_ids'];
        $object_alias   = Request::GetString('uploader_object_alias', $_REQUEST);
        $object_id      = Request::GetString('uploader_object_id', $_REQUEST); 
        $attachments = array();
        
        $modelAttachment = new Attachment();
        $modelEmail = new Email();
        
        //удаляю невыбранные shared docs из сессии и из БД
        foreach ($not_attached_ids as $key => $value) {
            //удаляю невыбранные id документов из сессии если они там есть
            if(isset($_SESSION['attachments-'.$object_alias.'-'.$object_id][$value])){
                unset($_SESSION['attachments-'.$object_alias.'-'.$object_id][$value]);
            }
            
            $query  = '';
            $query .= "DELETE FROM `attachment_objects` ";
            $query .= "WHERE attachment_id = '{$value}' ";
            $query .= "AND object_alias = '{$object_alias}' ";
            $query .= "AND object_id = '{$object_id}'";
            $result = $modelEmail->table->_exec_raw_query($query);
        }
        //debug('1682', $_SESSION);
        foreach ($attached_ids as $key => $value) {
            //если в сессии уже есть такой аттач - пропускаем
            if(array_key_exists($value, $_SESSION['attachments-'.$object_alias.'-'.$object_id])){
                continue;
            }
            //записываю в сессию id выбранных аттачей
            $_SESSION['attachments-'.$object_alias.'-'.$object_id][$value] = 1;
            
            $attachment = $modelAttachment->GetById($value);
            $attachments[] = $attachment;
            //задача: записать аттачменты в БД в табл. attachments_objects аналогично добавленным вручную
            $type          = $attachment['type'];
            $user_id       = $this->user_id;
            
            //сохраняю id  в БД
            $query  = '';
            $query .= "INSERT INTO `attachment_objects` (`attachment_id`, `type`, `object_alias`, `object_id`, `created_by`) ";
            $query .= "VALUES ('{$value}', '{$type}', '{$object_alias}', '{$object_id}', '{$user_id}')";
            $result = $modelEmail->table->_exec_raw_query($query);
        }
        $this->_assign('attachments', $attachments);
        //шаблон подходит для всех видов файлов которые привязаны в системе
        $content = $this->smarty->fetch('templates/html/emailmanager/attachment_text.tpl');
        $this->_send_json(array('content' => $content));
    }
    
    /**
     * Удаляет аттачмент из сессии  и из БД
     * 
     * @param $attachment_id
     * @link /emailmanager/removeshareddoc
     * @author Sergey Uskov <archimba8578@gmail.com>
     */
    function removeshareddoc()
    {
        $attachment_id  = Request::Getinteger('attachment_id', $_REQUEST);
        $object_alias   = Request::GetString('uploader_object_alias', $_REQUEST);
        $object_id      = Request::GetString('uploader_object_id', $_REQUEST); 
        
        unset($_SESSION['attachments-'.$object_alias.'-'.$object_id][$attachment_id]);
        
        $modelEmail = new Email();
        $query  = '';
        $query .= "DELETE FROM `attachment_objects` ";
        $query .= "WHERE attachment_id = '{$attachment_id}' ";
        $query .= "AND object_alias = '{$object_alias}' ";
        $query .= "AND object_id = '{$object_id}'";
        $result = $modelEmail->table->_exec_raw_query($query);
        $this->_send_json(array('result' => 'okay'));
    }
    
    /**
     * Возвращает кол-во аттачей из сессии
     * 
     * @param $attachment_id 
     * @link /emailmanager/getattachcount
     * @author Sergey Uskov <archimba8578@gmail.com>
     */
    function getattachcount()
    {
        $uploader_object_alias   = Request::GetString('uploader_object_alias', $_REQUEST);
        $uploader_object_id      = Request::GetString('uploader_object_id', $_REQUEST); 
        $count = 0;
        if(is_array($_SESSION['attachments-'.$uploader_object_alias.'-'.$uploader_object_id])){
            $count = count($_SESSION['attachments-'.$uploader_object_alias.'-'.$uploader_object_id]);
        }
        $this->_send_json(array(
            'result' => 'okay',
            'attach_count' => $count
        ));
    }
    
    /**
     * Возвращает количество добавленных получателей письма
     * 
     * @author Sergey Uskov <archimba8578@gmail.com>
     */
    function recipientscounter()
    {
        $object_alias   = Request::GetString('uploader_object_alias', $_REQUEST);
        $object_id      = Request::GetString('uploader_object_id', $_REQUEST); 
        $count = 0;
        
        //
        $modelEmail = new Email();
        $query  = '';
        $query .= "SELECT * ";
        $query .= "FROM `email_recipients` ";
        $query .= "WHERE object_alias = '{$object_alias}' ";
        $query .= "AND object_id = '{$object_id}'";
        $resource = $modelEmail->table->_exec_raw_query($query);
        $result   = $modelEmail->table->_fetch_array($resource);
        $count = count($result);
        $this->_send_json(array(
            'result' => 'okay',
            'recipients_count' => $count
        ));
        
    }
    
    /**
     * При создании нового сообщения удаляет ключи из сессии и аттачи из БД
     * 
     * @author SU
     */
    function deletenewemailobjects()
    {
        $_SESSION['attachments-newemail-'.$this->user_id] = '';
        $_SESSION['emails_from_companies'] = '';
        $_SESSION['emails_array'] = '';
        
        $modelEmail = new Email();
        //удаляю аттачи, выбранные из системы
        $query  = '';
        $query .= "DELETE FROM `attachment_objects` ";
        $query .= "WHERE `object_alias` = 'newemail' ";
        $query .= "AND `object_id` = '{$this->user_id}'";
        $result = $modelEmail->table->_exec_raw_query($query);
        
        //удаляю адреса получателей, добавленных из системы
        $query  = '';
        $query .= "DELETE FROM `email_recipients` ";
        $query .= "WHERE `object_alias` = 'newemail' ";
        $query .= "AND `object_id` = '{$this->user_id}'";
        $result = $modelEmail->table->_exec_raw_query($query);
    }
    
    /**
     * Удаляет невыбранные адреса из БД. Сохраняет получателей письма в БД (табл. email_recipients)
     * 
     * @param array $checked_emails   (выбранные адреса получателей)
     * @param array $unchecked_emails (невыбранные адреса получателей)
     * @param string $object_alias (newemail или email)
     * @param string $object_id (если новое письмо - то id юзера, если не новое - id письма)
     * 
     * @author Sergey Uskov <archimba8578@gmail.com>
     */
    function saverecipients()
    {
        $checked_emails     = $_REQUEST['checked_emails'];
        $unchecked_emails   = $_REQUEST['unchecked_emails'];
        //debug('1682', $unchecked_emails);
        $object_alias       = Request::GetString('object_alias', $_REQUEST);
        $object_id          = Request::GetInteger('object_id', $_REQUEST);
        $user_id            = $this->user_id;
        $modelEmail = new Email();
        
        //удаляю невыбранные адреса из БД
        foreach ($unchecked_emails as $key => $value) {
            $query  = '';
            $query .= "DELETE FROM `email_recipients` ";
            $query .= "WHERE object_alias = '{$object_alias}' ";
            $query .= "AND object_id = '{$object_id}' ";
            $query .= "AND company_id = '{$value}' ";
            $query .= "AND email_adress = '{$key}'";
            $result = $modelEmail->table->_exec_raw_query($query);
        }
        foreach ($checked_emails as $key => $value) {
            //проверяю, существует ли текущая запись в БД
            $query  = '';
            $query .= "SELECT * ";
            $query .= "FROM `email_recipients` ";
            $query .= "WHERE object_alias = '{$object_alias}' ";
            $query .= "AND object_id = '{$object_id}' ";
            $query .= "AND company_id = '{$value}' ";
            $query .= "AND email_adress = '{$key}'";
            $resource = $modelEmail->table->_exec_raw_query($query);
            $result   = $modelEmail->table->_fetch_array($resource);
            
            if(count($result) < 1){
                //сохраняю адрес в БД
                $query  = '';
                $query .= "INSERT INTO `email_recipients` (`object_alias`, `object_id`, `company_id`, `email_adress`, `created_by`) ";
                $query .= "VALUES ('{$object_alias}', '{$object_id}', '{$value}', '{$key}', '{$user_id}')";
                $result = $modelEmail->table->_exec_raw_query($query);
            }else{
                //удаляю из массива значения, которые уже существуют в БД (чтоб не дублировать строки на странице редактирования)
                unset($checked_emails[$key]);
            }
        }
        $this->_assign('recipients', $checked_emails);
        $content = $this->smarty->fetch('templates/html/emailmanager/control_recipients.tpl');
        $this->_send_json(array('content' => $content));
    }
        
    /**
     * Удаляет аттачмент из сессии  и из БД
     * 
     * @param $attachment_id
     * @link /emailmanager/removeshareddoc
     * 
     * @author Sergey Uskov <archimba8578@gmail.com>
     */
    function removerecipient()
    {
        $recipient_adress = Request::GetString('recipient_adress', $_REQUEST);
        $object_alias     = Request::GetString('uploader_object_alias', $_REQUEST);
        $object_id        = Request::GetString('uploader_object_id', $_REQUEST); 
        
        $modelEmail = new Email();
        $query  = '';
        $query .= "DELETE FROM `email_recipients` ";
        $query .= "WHERE email_adress = '{$recipient_adress}' ";
        $query .= "AND object_alias = '{$object_alias}' ";
        $query .= "AND object_id = '{$object_id}'";
        $result = $modelEmail->table->_exec_raw_query($query);
        
        $this->_send_json(array('result' => 'okay'));
    }
    
    /**
     * Удаляет письмо из БД
     * 
     * @param int $email_id 
     * @author Sergey Uskov <archimba8578@gmail.com>
     */
    public function eraseemail() 
    {
        $email_id   = Request::GetNumeric('email_id', $_REQUEST);
        
        $modelEmail = new Email();
        // удаляю из БД
        $result = $modelEmail->Erase($email_id);
        if(isset($result['0']['ErrorCode'])) {
            $this->_send_json(array('result' => 'error', 'code' => 'Email was not deleted.'));
        }
        else {
            $this->_send_json(array('result' => 'okay'));
        }
    } 
    
    /**
     * Помечает письмо как удалённое
     * 
     * @param int $email_id 
     * @author Sergey Uskov <archimba8578@gmail.com>
     * @modified 27.10.2014 Sergey Uskov использую процедуру DeleteByUser();
     */
    public function deleteemailtotrash() 
    {
        //params
        $email_id   = Request::GetNumeric('email_id', $_REQUEST);
        $is_deleted = 1;
        $user_id    = $this->user_id;
        $user_role    = $this->user_role;
        $now = date("m-d-y h:i:s");
        
        $emails = new Email();
        $email = $emails->GetById($email_id);
        $email = $email['email'];
        $type = $email['type_id'];
        $created_by = $email['created_by'];
        $modified_by = $email['modified_by'];
        $approve_by = $email['approve_by'];
        //проверки безопасности
        //если роль юзера админ или выше:
        $ok_flag = ($user_role  >= 0 && $user_role  < 3) ? true : false;
        //если ниже чем админ, но не ниже чем персонал:
        if($user_role > 2 && $user_role  < 7) {
            //если черновик
            if($type == '3'){
                if($modified_by == $user_id || $approve_by == $user_id) $ok_flag = true;
            }
            else{
                if($modified_by == $user_id) $ok_flag = true;
            }
        }
        if($ok_flag){
            $modelEmail = new Email();
            $result = $modelEmail->DeleteByUser($email_id);
            //debug('1682', $result);
            /*$query  = '';
            $query  = "UPDATE `emails` ";
            $query .= "SET is_deleted = '{$is_deleted}', ";
            $query .= "deleted_at = '{$now}', ";
            $query .= "deleted_by = '{$user_id}' ";
            $query .= "WHERE id = '{$email_id}'";

            $result = $modelEmail->table->_exec_raw_query($query);*/

            if(is_array($result)) {
                //Cache::ClearTag('emails');
                $this->_send_json(array('result' => 'okay'));
            }
            else {
                $this->_send_json(array('result' => 'error', 'code' => 'Email was not deleted.'));
            }
        }else{
            $this->_send_json(array('result' => 'error', 'code' => 'You have no permissions to delete this email!'));
        }
    }
}
