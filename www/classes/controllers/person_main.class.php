<?php
require_once APP_PATH . 'classes/components/object.class.php';
require_once APP_PATH . 'classes/core/Pagination.class.php';

require_once APP_PATH . 'classes/models/attachment.class.php';
require_once APP_PATH . 'classes/models/city.class.php';
require_once APP_PATH . 'classes/models/company.class.php';
require_once APP_PATH . 'classes/models/contactdata.class.php';
require_once APP_PATH . 'classes/models/country.class.php';
require_once APP_PATH . 'classes/models/department.class.php';
require_once APP_PATH . 'classes/models/jobposition.class.php';
require_once APP_PATH . 'classes/models/mailbox.class.php';
require_once APP_PATH . 'classes/models/person.class.php';
require_once APP_PATH . 'classes/models/region.class.php';
require_once APP_PATH . 'classes/models/user.class.php';

require_once APP_PATH . 'classes/models/email.class.php';

class MainController extends ApplicationController
{
    function MainController()
    {
        ApplicationController::ApplicationController();
        
        $this->authorize_before_exec['index']       = ROLE_STAFF;
        $this->authorize_before_exec['add']         = ROLE_STAFF;
        $this->authorize_before_exec['edit']        = ROLE_STAFF;        
        $this->authorize_before_exec['view']        = ROLE_STAFF;
        $this->authorize_before_exec['regrequests'] = ROLE_STAFF;
        $this->authorize_before_exec['staff']       = ROLE_STAFF;
        
        $this->breadcrumb   = array('Persons' => '/persons');
        $this->context      = 'true';
    }

    /**
     * Отображает страницу списка запросов на регистрацию
     * url: /person/regrequests
     * 
     * @version 20120904, zharkov
     */
    function regrequests()
    {
        $this->page_name                    = 'Registration Requests';
        $this->breadcrumb[$this->page_name] = '';
        
        $modelUser = new User();
        $this->_assign('list', $modelUser->RequestsGetList());
        
        $this->_display('regrequests');
    }

    /**
     * Возвращает список работников компании
     * url: /persons/staff
     */
    function staff()
    {
        $this->page_name = 'MaM Staff';
        $this->breadcrumb[$this->page_name] = '/persons/staff';
        
        $persons    = new Person();
        $rowset     = $persons->GetMamList();
        $count      = count($rowset);
        
        $this->_assign('count', $count);
        $this->_assign('list',  $rowset);
        
        $pager = new Pagination();
        $this->_assign('pager_pages', $pager->PreparePages($this->page_no, $count));
        
        $this->_display('staff');        
    }
    
    /**
     * Отображает индексную страницу регистра людей
     * url: /persons
     */
    function index()
    {
        if (isset($_REQUEST['btn_select']))
        {
            $form       = $_REQUEST['form'];            
            $keyword    = Request::GetString('keyword', $form);
            $filter     = 'keyword:' . $keyword;
            $this->_redirect(array('persons', 'filter', str_replace(' ', '+', $filter)), false);
        }
        
        $filter         = Request::GetString('filter', $_REQUEST);
        $filter         = urldecode($filter);
        $filter_params  = array();
        
        if (empty($filter))
        {
            $this->page_name = 'Persons';
            $this->breadcrumb[$this->page_name] = '/persons';
        }
        else
        {
            $this->page_name = 'Filtered Persons';
            
            $this->breadcrumb['Persons']        = '/persons';
            $this->breadcrumb[$this->page_name] = $this->pager_path;
            
            $filter = explode(';', $filter);
            foreach ($filter as $row)
            {
                if (empty($row)) continue;
                
                $param = explode(':', $row);
                $filter_params[$param[0]] = Request::GetHtmlString(1, $param);
            }
            
            $this->_assign('filter', true);
        }
        
        
        $keyword    = Request::GetString('keyword', $filter_params);
        $company    = Request::GetInteger('company', $filter_params);
        
        if (!empty($keyword) || !empty($company))
        {
            $persons    = new Person();
            $rowset     = $persons->Search($keyword, $company, $this->page_no);            
        }
        else
        {
            $rowset = array(
                'data'  => array(),
                'count' => 0
            );
        }
        
        $this->_assign('keyword',   $keyword);
        
        $this->_assign('count',     $rowset['count']);
        $this->_assign('list',      $rowset['data']);
        
        $pager = new Pagination();
        $this->_assign('pager_pages', $pager->PreparePages($this->page_no, $rowset['count']));
        
        
        $this->_display('index');
    }    
    
    /**
     * add new person
     * 
     * url: /company/{company_id}/person/add
     * url: /person/add
     * 
     * @version 20130807, sasha
     */
    function add()
    {
        $this->edit();
    }
    
    /**
     * person edit
     * 
     * url: /person/edit/{id}
     * 
     * @version 20130807, sasha
     */
    
    
    function edit()
    {
        //debug('1671', $_SESSION);
        //debug('1671', $_SESSION);
        $person_id  = Request::GetInteger('id', $_REQUEST);

        if ($person_id > 0)
        {
            $persons    = new Person();
            $person     = $persons->GetById($person_id);
            if (empty($person)) _404();
        }
        
        if (isset($_REQUEST['btn_cancel']))
        {
            if (empty($person_id))
            {
                $company_id = Request::GetInteger('company_id', $_REQUEST);
                if ($company_id > 0)
                {
                    $this->_redirect(array('company', $company_id));                    
                }
            }
            
            $this->_redirect(array('person', $person_id));
        }
        else if (isset($_REQUEST['btn_save']))
        {
            $form               = $_REQUEST['person'];
            $user               = $_REQUEST['user'];
            $contactdata        = isset($_REQUEST['contactdata']) ? $_REQUEST['contactdata'] : array();
            
            $title              = Request::GetString('title', $form);
            $first_name         = Request::GetString('first_name', $form);
            $middle_name        = Request::GetString('middle_name', $form);
            $last_name          = Request::GetString('last_name', $form);
            $birthday           = Request::GetDateForDB('birthday', $form);
            $form['birthday']   = $birthday;
            $name_for_label     = Request::GetString('name_for_label', $form);
            $languages          = Request::GetString('languages', $form);
            $company_id         = Request::GetInteger('company_id', $form);
            $department_id      = Request::GetInteger('department_id', $form);
            $jobposition_id     = Request::GetInteger('jobposition_id', $form);
            $country_id         = Request::GetInteger('country_id', $form);
            $region_id          = Request::GetInteger('region_id', $form);
            $city_id            = Request::GetInteger('city_id', $form);
            $zip                = Request::GetString('zip', $form);
            $address            = Request::GetString('address', $form);
            $notes              = Request::GetHtmlString('notes', $form);
            $key_contact        = Request::GetInteger('key_contact', $form);

            $user_id            = Request::GetString('id', $user);
            $login              = Request::GetString('login', $user);
            $password           = Request::GetString('password', $user);
            $role_id            = Request::GetInteger('role_id', $user);
            $status_id          = Request::GetInteger('status_id', $user);
            $nickname           = Request::GetString('nickname', $user);
            $color              = Request::GetString('color', $user);
            $reg_email          = Request::GetString('email', $user);
            $se_access          = Request::GetInteger('se_access', $user);
            $pa_access          = Request::GetInteger('pa_access', $user);
            $chat_icon_park     = Request::GetInteger('chat_icon_park', $user);
            $driver             = Request::GetInteger('driver', $user);
            $last_email_number  = Request::GetInteger('last_email_number', $user);
            
            $mailboxes_list = isset($_REQUEST['mailboxes_ids']) ? $_REQUEST['mailboxes_ids'] : array();

            if (empty($role_id) || empty($status_id))
            {
                $role_id        = 0;
                $status_id      = 0;
                $chat_icon_park = 0;
                $driver         = 0;
            }            
            
            if (empty($title))
            {
                $this->_message('Title must be specified !', MESSAGE_ERROR);
            }
            else if (empty($first_name) && empty($last_name))
            {
                $this->_message('First Name or Last Name must be specified !', MESSAGE_ERROR);
            }
            else
            {
                $no_errors = true;
                if ($user_id > 0 || !empty($login))
                {
                    if (strlen($login) < 1 || strlen($login) > 32)
                    {
                        $this->_message('Login must be between 1-21 symbols length', MESSAGE_ERROR);
                        $no_errors = false;
                    }           
                    else if (!empty($login) && !preg_match("/^[0-9a-zA-Z]+$/", $login))
                    {
                        $this->_message('Login must contain only letters or numbers', MESSAGE_ERROR);
                        $no_errors = false;
                    }                
                    else if (strlen($password) < 3 || strlen($password) > 32)
                    {
                        $this->_message('Password must be between 3-32 symbols length', MESSAGE_ERROR);
                        $no_errors = false;
                    }
/*
                    else if (empty($role_id))
                    {
                        $this->_message('User Role must be specified !', MESSAGE_ERROR);
                        $no_errors = false;
                    }
*/                    
                    else if ($role_id == ROLE_USER && empty($se_access) && empty($pa_access))
                    {
                        $this->_message('I forgot to specify sites user can access !', MESSAGE_ERROR);
                        $no_errors = false;
                    }
                    else if ($role_id > 0 && empty($status_id))
                    {
                        $this->_message('Account Status must be specified !', MESSAGE_ERROR);
                        $no_errors = false;
                    }
                    else if (empty($person_id) && !preg_match("/^[0-9a-zA-Z_\\.\\-]+@[0-9a-zA-Z_\\.\\-]+?\\.[a-zA-Z]+$/", $reg_email))
                    {
                        $this->_message('Reg. Email must be specified !', MESSAGE_ERROR);
                        $no_errors = false;
                    }
                }
                //print_r($no_errors);
                if ($no_errors)
                {
                    $persons    = new Person();
                    $result     = $persons->Save($person_id, $title, $first_name, $middle_name, $last_name, $name_for_label, 
                                                    $company_id, $department_id, $jobposition_id, $country_id, $region_id, 
                                                    $city_id, $zip, $address, $languages, $notes, $birthday);
                    
                    if ($user_id > 0 || !empty($login))
                    {
                        if (empty($role_id)) $status_id = 0;
                        
                        $users      = new User();
                        $saved_user = $users->Save($user_id, $login, $nickname, $password, $reg_email, $role_id, $status_id, $result['id'], $color, 
                                        $se_access, $pa_access, $chat_icon_park, $driver, $last_email_number);
                        
						//dg($saved_user);
                        // start сохранения списка доступных MailBoxes
                        //if (array_key_exists('id', $saved_user) && $this->user_role <= ROLE_MODERATOR)
                        if (array_key_exists('id', $saved_user))
                        {
                            $mboxes_list = array();
                            foreach ($mailboxes_list as $item)
                            {
                                $mboxes_list[] = array('user_id' => $this->user_id, 'mailbox_id' => intval($item));
                            }
							//dg($mboxes_list);
                            //$mboxes_list = ($role_id < ROLE_USER && $role_id > 0) ? $mboxes_list : array();
                            if($user_id > 0) {
								$users->SaveUserMailboxes($user_id, $mboxes_list);
							}else{
								$users->SaveUserMailboxes($saved_user['id'], $mboxes_list);
							}
                        }
                        // end сохранения списка доступных MailBoxes
                    }
                    
                    $contactdatas = new ContactData();                    
                    foreach ($contactdatas->GetList('person', $result['id']) as $row)
                    {
                        $delete_flag = true;
                        foreach ($contactdata as $key => $row1)
                        {
                            if ($row['id'] == $row1['id'] && !empty($row1['id']['title']))
                            {
                                $delete_flag = false;
                            }
                        }
                        
                        if ($delete_flag) $contactdatas->Remove($row['id']);
                    }
                    
                    foreach ($contactdata as $row)
                    {
                        $id     = Request::GetInteger('id', $row);
                        $type   = Request::GetString('type', $row);
                        $title  = Request::GetString('title', $row);
                        
                        if (empty($id) && empty($title)) continue;                    
                        $contactdatas->Save($id, 'person', $result['id'], $type, $title);
                    } 
                    
                    // обновление Company Key Contact
                    if ($company_id > 0 && $key_contact > 0)
                    {
                        $companies = new Company();
                        $companies->UpdateKeyContact($company_id, $result['id']);
                    }                    
                    
                    $this->_message('Person was saved successfully !', MESSAGE_OKAY);
                    $this->_redirect(array('person', $result['id']));
                }
            }
        }
        else if ($person_id > 0)
        {
            $form   = $person['person'];
            
            $users  = new User();
            $user   = $users->GetByPersonId($person_id);
            $user   = isset($user['user']) ? $user['user'] : array();
            
            $contactdatas   = new ContactData();
            $contactdata    = $contactdatas->GetList('person', $person_id);
            
            $this->_assign('cd_index', count($contactdata));
        }
        else
        {
            $company_id = Request::GetInteger('company_id', $_REQUEST);
            
            $form           = array('company_id' => $company_id);
            $user           = array();
            $contactdata    = array();
        }
        
        if (empty($person_id))
        {
            $this->page_name = 'New Person';
        }
        else
        {
            $this->page_name = 'Edit Person';
        }
        $this->breadcrumb[$this->page_name] = '';
        
        
        $countries = new Country();
        $this->_assign('countries', $countries->GetList());
        
        if (isset($form['country_id']) && !empty($form['country_id']))
        {
            $regions = new Region();
            $this->_assign('regions', $regions->GetList($form['country_id']));
        }

        if (isset($form['region_id']) && !empty($form['region_id']))
        {
            $cities = new City();
            $this->_assign('cities', $cities->GetList($form['region_id']));
        }

        if (isset($form['company_id']) && !empty($form['company_id']))
        {
            $companies  = new Company();
            $company    = $companies->GetById($form['company_id']);
            $this->_assign('company', $company['company']);
        }
        
        $departments = new Department();
        $this->_assign('departments', $departments->GetList());
        
        $jobpositions = new JobPosition();
        $this->_assign('jobpositions', $jobpositions->GetList());
        
        // start формирование списка MailBoxes
        if ($this->user_role <= ROLE_MODERATOR)
        {
            $modelEmail = new Email();
            $modelUser  = new User();
            $mailboxes_list = $modelEmail->GetMailboxesList(1);
            $user_mailboxes = array_key_exists('id', $user) ? $modelUser->GetMailboxesList($user['id']) : array();
            
            foreach ($mailboxes_list as $key => $item)
            {
                foreach ($user_mailboxes as $_key => $_item)
                {
                    if ($_item['mailbox_id'] != $item['id']) continue;
                    
                    $mailboxes_list[$key]['selected'] = TRUE;
                }
            }
            $this->_assign('mailboxes_list', $mailboxes_list);
        }
        
        // end формирование списка MailBoxes

        $this->_assign('person',        $form);
        $this->_assign('user',          $user);
        $this->_assign('contactdata',   $contactdata); 
        $this->_assign('include_ui',    true);        
        
        $this->js[] = 'person_edit';
        $this->js[] = 'colpick';
		
		$this->css[] = 'colpick';
		$this->css[] = 'style';
        
     //dg($modelEmail);
                if($_SESSION['user']['id']=='1682') {
                    $this->_display('editnew');
                    //$this->_display('edit');
                }else{
                    $this->_display('edit');
                }
    }

    /**
     * Отображает страницу просмотра карточки сотрудника
     * url: /person/{id}
     */
    function view()
    {
		//print_r('1');
        $person_id = Request::GetInteger('id', $_REQUEST);
        if (empty($person_id)) _404();
        
        $persons    = new Person();
        $person     = $persons->GetById($person_id);
        if (empty($person)) _404();

        $person = $person['person'];
        
        if (isset($_REQUEST['btn_upload_image']) && isset($_FILES['person_picture']))
        {
            $file_object = Request::GetFile($_FILES['person_picture']);

            if (!empty($file_object))
            {                
                $modelPicture   = new Picture();
                $attachment_id  = $modelPicture->Save(0, 'person', $person_id, $file_object);
                
                if (isset($attachment_id)) 
                {
                    $modelPicture->SetAsMain($attachment_id);
                    $persons->UpdatePicture($person_id, $attachment_id);
                }
            }
            
            $this->_redirect(array('person', $person_id));
        }
        
        $this->page_name = $person['full_name'];
        $this->breadcrumb[$this->page_name] = '';
        
        $users  = new User();
        $user   = $users->GetByPersonId($person_id);

        if (!empty($user) && isset($user['user']))
        {
            $this->_assign('user', $user['user']);
            
            $mailboxes = new Mailbox();
            $this->_assign('mailboxes', $mailboxes->GetListForUser($user['user']['id']));
        } 
        
        $this->_assign('person', $person); 
        
        $contactdatas = new ContactData();
        $this->_assign('contactdata', $contactdatas->GetList('person', $person_id));

        $objectcomponent = new ObjectComponent();
        $this->_assign('object_stat', $objectcomponent->GetStatistics('person', $person_id));
        
        $modelAttachment    = new Attachment();
        $attachments_list   = $modelAttachment->GetListByType('', 'person', $person_id);
        $this->_assign('attachments_list', $attachments_list['data']);
        
        $this->js = 'person_view';
        $this->_display('view');
    }
}