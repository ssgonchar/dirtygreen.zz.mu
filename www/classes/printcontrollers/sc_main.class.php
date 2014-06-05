<?php
require_once APP_PATH . 'classes/components/object.class.php';
require_once APP_PATH . 'classes/core/Pagination.class.php';

require_once APP_PATH . 'classes/models/attachment.class.php';
require_once APP_PATH . 'classes/models/sc.class.php';

class MainPrintController extends ApplicationPrintController
{
    public function MainPrintController()
    {
        ApplicationPrintController::ApplicationPrintController();
        
        $this->authorize_before_exec['index']   = ROLE_STAFF;
        $this->authorize_before_exec['view']     = ROLE_STAFF;
    }

    /**
     * Отображает страницу со списком Sale Confirmations
     * 
     * @version 20120222, d10n
     */
    public function index()
    {
        $sc     = new SC();
        $rowset = $sc->GetList(1, 1000);
        
        $this->_assign('list',          $rowset['data']);
        
        $this->_assign('page_name', 'Sale Confirmations');
        $this->_display('index');
    }
    
    /**
     * Отображает страницу просмотра sc
     * @url: /sc/view/{id}
     * 
     * @version 20120222, d10n
     */
    public function view()
    {
        $sc_id = Request::GetInteger('id', $_REQUEST);
        if (empty($sc_id)) _404();
        
        $scs  = new SC();
        $sc   = $scs->GetById($sc_id);        
        if (empty($sc)) _404();

        $order_id = $sc['sc']['order_id'];
        
        $orders     = new Order();
        $order      = $orders->GetById($order_id);
        $positions  = $scs->GetPositionsFull($sc_id);

        $this->_assign('sc',                    $sc['sc']);
        $this->_assign('order',                 $order['order']);
        $this->_assign('positions',             $positions);
        $this->_assign('special_requirements',  $scs->GetSpecialRequirements($sc_id));

        $total_qtty     = 0;
        $total_weight   = 0;
        $total_value    = 0;
        foreach ($positions as $key => $row)
        {
//TODO d10n: не генерируются qtty. Исправить
            if (!isset($row['qtty'])) continue;
            
            $total_qtty     += $row['qtty'];
            $total_weight   += $row['weight'];
            $total_value    += $row['value'];
        }
                
        $this->_assign('total_qtty',    $total_qtty);
        $this->_assign('total_weight',  $total_weight);
        $this->_assign('total_value',   $total_value);
        
        $objectcomponent = new ObjectComponent();
        $this->_assign('object_stat', $objectcomponent->GetStatistics('sc', $sc_id));
        
        $modelAttachment    = new Attachment();
        $attachments_list   = $modelAttachment->GetListByType('', 'sc', $sc_id);
        $this->_assign('attachments_list', $attachments_list['data']);
        
        $this->_assign('page_name', 'Sale Confirmations No ' . $sc['sc']['doc_no']);
        $this->_display('view');
    }
}