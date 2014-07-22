<?php
require_once APP_PATH . 'classes/models/attachment.class.php';
require_once APP_PATH . 'classes/models/oc.class.php';

class MainPrintController extends ApplicationPrintController
{
    public function MainPrintController()
    {
        ApplicationPrintController::ApplicationPrintController();
        
        $this->authorize_before_exec['index']   = ROLE_STAFF;
        $this->authorize_before_exec['view']     = ROLE_STAFF;
    }
    
    /**
     * Список OC
     * url: /oc~print
     * url: /oc/filter/{filter}~print
     * 
     * @version 20130221, d10n
     */
    public function index()
    {
        $filter         = Request::GetString('filter', $_REQUEST);
        $filter         = urldecode($filter);
        $filter_params  = array();
        
        $filter = explode(';', $filter);
        foreach ($filter as $row)
        {
            if (empty($row)) continue;
            
            $param = explode(':', $row);
            $filter_params[$param[0]] = Request::GetHtmlString(1, $param);
        }
        
        $company_id = Request::GetInteger('company', $filter_params);
        $date_from  = null;
        $date_to    = null;
        $number     = Request::GetString('number', $filter_params);
        
        $modelOC    = new OC();
        $rowset     = $modelOC->GetList($company_id, $date_from, $date_to, $number, 1, 1000);
        $this->_assign('list',  $rowset['data']);
        
        $this->_assign('company_id',    $company_id);
        $this->_assign('date_from',     $date_from);
        $this->_assign('date_to',       $date_to);
        $this->_assign('number',        $number);
        
        $this->_assign('page_name', 'Original Certificates');
        $this->_display('index');
    }
    
    
    /**
     * Отображает страницу просмотра OC
     * @url /oc/{oc_id}~print
     * 
     * @version 20130221, d10n
     */
    public function view()
    {
        $oc_id = Request::GetInteger('id', $_REQUEST);

        $modelOC    = new OC();
        $oc         = $modelOC->GetById($oc_id);
        if (empty($oc)) _404();
        $oc = $oc['oc'];
        
        $this->_assign('form', $oc);
        
        $modelAttachment    = new Attachment();
        $attachments_list   = $modelAttachment->GetListByType('', 'oc', $oc_id);
        $this->_assign('attachments_list', $attachments_list['data']);
        
        $this->_assign('items',         $oc['items_list']);
        $this->_assign('firstitem',     current($oc['items_list']));
        
        $objectcomponent    = new ObjectComponent();
        $page_params        = $objectcomponent->GetPageParams('oc', $oc_id);
        
        $this->_assign('object_stat',   $page_params['stat']);
        $this->_assign('page_name', 'Original Certificate No ' . $page_params['page_name']);
        $this->_display('view');
    }
}