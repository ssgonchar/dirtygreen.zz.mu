<?php
require_once APP_PATH . 'classes/components/object.class.php';
require_once APP_PATH . 'classes/models/attachment.class.php';
require_once APP_PATH . 'classes/models/qc.class.php';

class MainPrintController extends ApplicationPrintController
{
    public function MainPrintController()
    {
        ApplicationPrintController::ApplicationPrintController();
        
        $this->authorize_before_exec['index']   = ROLE_STAFF;
        $this->authorize_before_exec['view']     = ROLE_STAFF;
    }

    
    /**
     * Отображает страницу просмотра сертификата
     * @url: /qc/{id}~print
     * 
     * @version 20130222, d10n
     */
    public function view()
    {
        $qc_id = Request::GetInteger('id', $_REQUEST);        
        if ($qc_id <= 0) _404();
        
        $qcs  = new QC();
        $qc   = $qcs->GetById($qc_id);
        if (empty($qc)) _404();

        $items = $qcs->GetItems($qc_id);
        
        $this->_assign('qc',    $qc['qc']);
        $this->_assign('items', $items);
        
        $this->_assign_total($items);
        
        $objectcomponent    = new ObjectComponent();
        $page_params        = $objectcomponent->GetPageParams('qc', $qc_id);
        
        $this->_assign('object_stat', $page_params['stat']);
        
        $modelAttachment    = new Attachment();
        $attachments_list   = $modelAttachment->GetListByType('', 'qc', $qc_id);
        $this->_assign('attachments_list', $attachments_list['data']);
        
        $this->_assign('page_name', 'Quality Certificate No ' . $page_params['page_name']);
        $this->_display('view');
    }
    
    /**
     * Список сертификатов
     * @url: /qcs~print
     * 
     * @version 20130222, d10n
     */
    public function index()
    {        
        $qcs    = new QC();
        $list   = $qcs->GetList();
        $this->_assign('list', $list);
        
        $this->_assign('page_name', 'Quality Certificates');
        $this->_display('index');
    }    
 
    /**
     * Подсчитывает и выводи тотал айтемов
     * 
     * @param mixed $items
     * 
     * @version 20120812, zharkov
     */
    function _assign_total($items)
    {
        $total_qtty         = 0;
        $total_weight       = 0;
        $total_weight_ton   = 0;
        $total_value        = 0;
        
        foreach ($items as $item)
        {
            $total_qtty         += 1;
            $total_weight       += $item['steelitem']['unitweight'];
            $total_weight_ton   += $item['steelitem']['unitweight_ton'];
            $total_value        += $item['steelitem']['value'];
        }
        
        $this->_assign('total_qtty',        $total_qtty);
        $this->_assign('total_weight',      $total_weight);
        $this->_assign('total_weight_ton',  $total_weight_ton);
        $this->_assign('total_value',       $total_value);
    }
}