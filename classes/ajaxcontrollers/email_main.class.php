<?php
require_once APP_PATH . 'classes/models/contactdata.class.php';
require_once APP_PATH . 'classes/models/company.class.php';
require_once APP_PATH . 'classes/models/country.class.php';
require_once APP_PATH . 'classes/models/biz.class.php';
require_once APP_PATH . 'classes/models/order.class.php';
require_once APP_PATH . 'classes/models/person.class.php';
require_once APP_PATH . 'classes/models/product.class.php';


class MainAjaxController extends ApplicationAjaxController
{    

    function MainAjaxController()
    {
        ApplicationAjaxController::ApplicationAjaxController();
                
        $this->authorize_before_exec['getdata']         = ROLE_STAFF;
        $this->authorize_before_exec['getrecipients']   = ROLE_STAFF;
        $this->authorize_before_exec['getobjectslist']  = ROLE_STAFF;
        $this->authorize_before_exec['getmessage']      = ROLE_STAFF;
    }

    function getmessage()
    {
        $email_id = Request::GetInteger('email_id', $_REQUEST);
        
        $modelEmail = new Email();
        $email      = $modelEmail->GetById($email_id);
        
        if (isset($email))
        {
            $this->_assign('email', $email);
            $this->_send_json(array(
                'result'    => 'okay',
                'content'   => $this->smarty->fetch('templates/html/email/control_email_view.tpl')
            ));
        }

        $this->_send_json(array('result' => 'error'));
    }
    
    function getemailobj()
    {
        $email_id = Request::GetInteger('email_id', $_REQUEST);
        
        $modelEmail = new Email();
        $email      = $modelEmail->GetById($email_id);
        
        if (isset($email))
        {
            //$this->_assign('email', $email);
            $this->_send_json(array(
                'result'    => 'okay',
                'email'   => $email
            ));
        }

        $this->_send_json(array('result' => 'error'));
    }    
    
    /**
     * get data by contact data id // Возвращает даные по идентификатору контактных данных
     * url: /email/getdata
     */
    function getdata()
    {
        $contactdata_id = Request::GetInteger('contactdata_id', $_REQUEST);
        
        $contactdata    = new ContactData();
        $rowset         = $contactdata->GetById($contactdata_id);

        if (empty($rowset)) $this->_send_json(array('result' => 'error'));
        
        $rowset = $rowset['contactdata'];
        $result = array();
        
        if ($result['object_alias'] == 'person')
        {
            $persons    = new Person();
            $person     = $persons->GetById($result['object_id']);
            
            if (!empty($person)) 
            {                
                $result['person']   = $person['person']['full_name'];
                $company_id         = $person['person']['company_id'];
            }
        }
        else if ($result['object_alias'] == 'company')
        {
            $company_id = $result['object_id'];
        }
        
        if (!empty($company_id))
        {
            $companies  = new Company();
            $company    = $companies->GetById($company_id);
            
            if (!empty($company)) $result['company'] = $company['company']['title'];            
        }
        
        
        $this->_send_json(array('result' => 'okay', 'data' => $result));        
    }
    
    /**
     * get recipients list
     * url: /email/getrecipients
     */
    function getrecipients()
    {
        $maxrows    = Request::GetInteger('maxrows', $_REQUEST);
        $keyword    = Request::GetString('keyword', $_REQUEST);
        
        $contactdata = new ContactData();
        $this->_send_json(array('result' => 'okay', 'list' => $contactdata->FindEmail($keyword, 0, $maxrows)));
    }
    
    /**
     * get object list
     * url: /email/getobjectslist
     */
    public function getobjectslist()
    {
        $object_type_alias = Request::GetString('type_alias', $_REQUEST, '', 10);
        $title = Request::GetString('title', $_REQUEST);
        
        if (!in_array($object_type_alias, array('biz', 'company', 'person', 'order', 'country', 'product')))
        {
            $this->_send_json(array('result' => 'okay'));
        }
        
        if (mb_strlen($title) < 3)
        {
            $this->_send_json(array('result' => 'okay'));
        }
        
        $page_no        = 0;
        $per_page       = 20;
        $rows_count     = 20;
        
        switch ($object_type_alias)
        {
            case 'biz':
                $modelBiz = new Biz();
        
                $search_string  = $title;
                $company_id     = 0;

                //$data_set = $modelBiz->Search($search_string, $company_id, $page_no, $per_page);
                //$list = array_key_exists('data', $data_set) ? $data_set['data'] : array();
                $list = $modelBiz->GetListByTitle($title, $rows_count);
                break;
            
            case 'person':
                $modelPerson = new Person();
                $list = $modelPerson->GetListByFIO($title, $rows_count);
                break;
            
            case 'company':
                $modelCompany = new Company();
                $list = $modelCompany->GetListByTitle($title, $rows_count);
                break;
            
            case 'order':
                $modelOrder = new Order();
                $list = $modelOrder->GetListByKeyword($title, $rows_count);
                break;
            
            case 'country':
                $modelCountry = new Country();
                $list = $modelCountry->GetListByKeyword($title, $rows_count);
                break;
            
            case 'product':
                $modelProduct = new Product();
                $list = $modelProduct->GetListByKeyword($title, $rows_count);
                break;
            
            default:
                $list = array();
        }
        
        $this->_send_json(array('result' => 'okay', 'list' => $list));
    }
	/*
	function getCorrespondence()
	{
		$biz_id = Request::GetString('biz_id', $_REQUEST);
		$modelEmail = new Email();
		$object_alias = 'biz';
		$object_id = $biz_id;
		$modelEmail->GetList($object_alias, $object_id, $mailbox_id, $type_id, $doc_type_id, $is_deleted, $keyword, $approve_by, $this->page_no);
	}*/
    
     public function setstarred() {
         $email_id = Request::GetInteger('email_id', $_REQUEST);
         $starr = Request::GetBoolean('starred', $_REQUEST);
         
         $modelEmail = new Email();
         $result = $modelEmail->setStarr($email_id, $starr);
         if($result) {
             $this->_send_json(array('result' => 'okay'));
         } else {
             $this->_send_json(array('result' => 'error', 'msg' => $result));
         }
     }
}
