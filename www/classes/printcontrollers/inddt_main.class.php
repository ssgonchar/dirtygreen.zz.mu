<?php
require_once APP_PATH . 'classes/models/attachment.class.php';
require_once APP_PATH . 'classes/models/inddt.class.php';

class MainPrintController extends ApplicationPrintController
{
    function MainPrintController()
    {
        ApplicationPrintController::ApplicationPrintController();
        
        $this->authorize_before_exec['index']   = ROLE_STAFF;
        $this->authorize_before_exec['view']    = ROLE_STAFF;
    }
        
    /**
     * Страница линейного списка документов
     * url: /inddt~print
     * 
     * @version 20121221, d10n
     */
    public function index()
    {        
        $modelInDDT = new InDDT();
        $rowset     = $modelInDDT->GetList(1, 1000);
        
        $this->_assign('list', $rowset['data']);
        
        $this->_assign('page_name', 'IN DDTs');
        $this->_display('index');
    }
    
    /**
     * Страница просмотра детальной информации
     * url: /inddt/{id}~print
     * 
     * @version 20121221, d10n
     */
    public function view()
    {
        $inddt_id = Request::GetInteger('id', $_REQUEST); 
        if ($inddt_id <= 0) _404();
        
        $modelInDDT = new InDDT();
        $inddt      = $modelInDDT->GetById($inddt_id);
        if (!isset($inddt['inddt'])) _404();
        
        $inddt = $inddt['inddt'];
        
        $this->_assign('form',  $inddt);
        $this->_assign('items', $modelInDDT->GetItems($inddt_id));
        
        $modelAttachment    = new Attachment();
        $attachments_list   = $modelAttachment->GetListByType('', 'inddt', $inddt_id);
        $this->_assign('attachments_list', $attachments_list['data']);
        
        $objectcomponent    = new ObjectComponent();
        $page_params        = $objectcomponent->GetPageParams('inddt', $inddt_id);
        
        $this->_assign('object_stat', $page_params['stat']);
        $this->_assign('page_name', 'InDDT No ' . $page_params['page_name']);
        $this->_display('view');
    }
}