<?php
require_once APP_PATH . 'classes/core/Pagination.class.php';
require_once APP_PATH . 'classes/models/attachment.class.php';
require_once APP_PATH . 'classes/models/email.class.php';
require_once APP_PATH . 'classes/models/emailfilter.class.php';

class FilterController extends ApplicationController
{
    public function __construct()
    {
        parent::ApplicationController();
        
        $this->authorize_before_exec['add']             = ROLE_STAFF;
        $this->authorize_before_exec['delete']          = ROLE_STAFF;
        $this->authorize_before_exec['edit']            = ROLE_STAFF;
        $this->authorize_before_exec['index']           = ROLE_STAFF;
        $this->authorize_before_exec['view']            = ROLE_STAFF;
        
        $this->context = true;
        $this->js = 'efilter_main';
        
        $this->breadcrumb   = array(
            'eMails'    => '/emails',
            'Filters'   => '/email/filters',
        );
    }
    
    /**
     * Списко фильтров
     * @url /email/filter(s)
     */
    public function index()
    {
        $this->page_name = 'Filters';
        $this->context = 'efindex';
        
        $modelEmailFilter = new EmailFilter();
        $data_set = $modelEmailFilter->GetList(-1, $this->page_no);
        
        $corePagination = new Pagination();
        $this->_assign('pager_pages', $corePagination->PreparePages($this->page_no, $data_set['count']));
        
        $this->_assign('list', $data_set['data']);
        $this->_display('findex');
    }
    
    /**
     * Добавление нового фильтра
     * @url /email/filter/add
     */
    public function add()
    {
        $this->edit();
    }
    
    /**
     * Редактирование фильтра
     * @url /email/{$id}/edit
     * @url /email/addfromemail/{email_id}
     * 
     * @version 20130125, zharkov
     */
    public function edit()
    {
        $id         = Request::GetInteger('id', $_REQUEST);
        $email_id   = Request::GetInteger('email_id', $_REQUEST);
        
        $this->page_name    = $id > 0 ? 'Edit' : 'New Filter';
        
        $modelEmailFilter   = new EmailFilter();                
        $form               = array();
        
        if ($id > 0)
        {
            $efilter = $modelEmailFilter->GetById($id);            
            if (!isset($efilter['efilter'])) _404();
            
            $efilter    = $efilter['efilter'];            
            $form       = $modelEmailFilter->ParamsStringToArray($efilter['params']);
            
            $form['is_scheduled'] = $efilter['is_scheduled'];            
        }
        else if ($email_id > 0)
        {
            $modelEmail = new Email();
            $email      = $modelEmail->GetById($email_id);            
            if (empty($email)) _404();
            
            $email      = $email['email'];
            $pattern    = '/[a-zA-Z0-9._-]+@[a-zA-Z0-9.]*[a-zA-Z]{2,6}/i';

            preg_match_all($pattern, $email['sender_address'], $matches);
            $form['from']       = isset($matches[0]) && isset($matches[0][0]) ? $matches[0][0] : '';
            preg_match_all($pattern, $email['recipient_address'], $matches);
            $form['to']         = isset($matches[0]) && isset($matches[0][0]) ? $matches[0][0] : '';
            $form['subject']    = $email['title'];            
            if (isset($email['objects']) && !empty($email['objects'])) $form['attachment'] = 'yes';
            
            $this->_assign('fromemail', $email_id);
        }
        
        if (isset($_REQUEST['btn_save']))
        {
            $form = isset($_REQUEST['form']) ? $_REQUEST['form'] : array();
            
            $from       = Request::GetString('from',            $form);
            $to         = Request::GetString('to',              $form);
            $subject    = Request::GetString('subject',         $form);
            $text       = Request::GetString('text',            $form);
            $attachment = Request::GetString('attachment',      $form);
            $is_scheduled = Request::GetBoolean('is_scheduled', $form);
            
            
            $objects    = isset($_REQUEST['objects']) ? $_REQUEST['objects'] : array();
            
            if (empty($from) && empty($to) && empty($subject) && empty($text))
            {
                $this->_message('Filter criteria must be specified!', MESSAGE_ERROR);
            }
            else if (empty($objects))
            {
                $this->_message('Tags must be specified!', MESSAGE_ERROR);
            }
            else
            {
                $id             = $id > 0 ? $id : 0;
                $filter_params  = $modelEmailFilter->ParamsArrayToString($form);
                $tags           = $modelEmailFilter->TagsArrayToString($objects);
                
                $result = $modelEmailFilter->Save($id, $filter_params, $tags, $is_scheduled);
                
                if (!isset($result['efilter']))
                {
                    $this->_message('Saving error', MESSAGE_ERROR);
                }
                else
                {
                    if ($id > 0)
                    {
                        $filter_old = $efilter;
                        $filter_new = $result['efilter'];

                        if ($modelEmailFilter->IsDiffExists($filter_old, $filter_new))
                        {
                            $modelEmailFilter->UnlinkAllEmails($efilter);
                        }
                    }
                    
                    $this->_message('Filter successfully saved', MESSAGE_OKAY);
                    $this->_redirect(array('email', 'filters'));
                }
            }
        }
        
        if ($id > 0)
        {
        }
        else if ($email_id > 0)
        {
            
        }
        
        if (isset($efilter['id']))
        {
            $this->_assign('tags', $efilter['tags_array']);
        }
        $this->_assign('form', $form);
        
        $this->breadcrumb[$this->page_name] = '';
        $this->_display('fedit');
    }
    
    /**
     * Просмотр деталей (ПОКА отображает страницу редактирования)
     * @url /email/filter/[:id]/view
     */
    public function view()
    {
        $this->edit();
    }
    
    /**
     * Удаление фильтра
     * @url /email/filter/[:id]/delete
     */
    public function delete()
    {
        $id = Request::GetInteger('id', $_REQUEST);
        
        $modelEmailFilter = new EmailFilter();
        
        $efilter = $modelEmailFilter->GetById($id);
        
        if (!isset($efilter['efilter'])) _404();
        
        $efilter = $efilter['efilter'];
        
        $modelEmailFilter->Remove($efilter);
        
        $this->_message('Filter was successfully deleted', MESSAGE_OKAY);
        $this->_redirect(array('email', 'filters'));
    }
    
    /**
     * CRON TASK<br />
     * Применяет фильтры к уже существующим фильтрам
     * @url /email/filter/cronapply/8258fad689feb9ee9fa7fa08b9cd4ca1
     */
    public function applyforexisting()
    {
        $id = Request::GetString('id', $_REQUEST);//8258fad689feb9ee9fa7fa08b9cd4ca1
        
        if ($id != md5('ksecron')) die();
        
        $modelEmailFilter = new EmailFilter();
        $efilters = $modelEmailFilter->GetList(1, 1, 1000);
		//dg($efilters);
        $efilters = $efilters['data'];
        
        if (empty($efilters)) die();
        
        $emails_per_time = 1000;
        
        foreach ($efilters as $efilter)
        {
            $efilter = $efilter['efilter'];
            
            $emails = $modelEmailFilter->GetEmailsForFiltering($efilter['id'], $emails_per_time);
            //dg($emails);
            foreach ($emails['data'] as $email)
            {
                $modelEmailFilter->Apply($efilter, $email);
            }
            
            if ($emails['count'] <= $emails_per_time)
            {
                $modelEmailFilter->RemoveFromSchedule($efilter['id']);
            }
        }
        
        die('end');
    }
}