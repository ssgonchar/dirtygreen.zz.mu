<?php
require_once APP_PATH . 'classes/components/object.class.php';
require_once APP_PATH . 'classes/models/attachment.class.php';
require_once APP_PATH . 'classes/models/qc.class.php';
require_once APP_PATH . 'classes/models/qc_pdf.class.php';
require_once APP_PATH . 'classes/models/stock.class.php';

class MainController extends ApplicationController
{
    function MainController()
    {
        ApplicationController::ApplicationController();
        
        $this->authorize_before_exec['index']   = ROLE_STAFF;
        $this->authorize_before_exec['edit']    = ROLE_STAFF;
        $this->authorize_before_exec['remove']  = ROLE_STAFF;
        $this->authorize_before_exec['view']    = ROLE_STAFF;
        
        $this->breadcrumb   = array('QCs' => '/qc');
        $this->context      = true;               
    }

    /**
     * Remove QC
     * 
     */
    function remove()
    {
        $qc_id = Request::GetInteger('id', $_REQUEST);        
        
        $modelQC    = new QC();
        $result     = $modelQC->Remove($qc_id);
        
        if (empty($result)) 
        {
            $this->_message('QC was not removed!', MESSAGE_ERROR);
        }
        else
        {
            $this->_message('QC was removed successfully', MESSAGE_OKAY);
        }
        
        $this->_redirect(array('qc'));
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
    
    /**
     * QC View Page
     * url: /qc/{id}
     * 
     * @version 20120812, zharkov
     */
    function view()
    {
        $qc_id = Request::GetInteger('id', $_REQUEST);        
        if (empty($qc_id)) _404();
        
        $qcs  = new QC();
        $qc   = $qcs->GetById($qc_id);        
        if (empty($qc)) _404();
        
        $qc     = $qc['qc'];
        $items  = $qcs->GetItems($qc_id);
        
        $dimension_units    = array();
        $weight_units       = array();
        foreach ($items as $key => $item)
        {
            $item = $item['steelitem'];
            
            if ($item['nominal_thickness_mm'] > 0) $items[$key]['steelitem']['thickness_mm'] = $item['nominal_thickness_mm'];
            if ($item['nominal_width_mm'] > 0) $items[$key]['steelitem']['width_mm'] = $item['nominal_width_mm'];
            if ($item['nominal_length_mm'] > 0) $items[$key]['steelitem']['length_mm'] = $item['nominal_length_mm'];
            
            $dimension_units[$item['dimension_unit']]   = $item['dimension_unit'];
            $weight_units[$item['weight_unit']]         = $item['weight_unit'];
        }

        // more then one unit in QC
        if ($qc['dim_unit'] == 'in' && isset($dimension_units['mm']))
        {
            $this->_assign('multiunits', true);
        }        

        $this->_assign('qc',    $qc);
        $this->_assign('items', $items);
        
        $this->_assign_total($items);
        
        $objectcomponent    = new ObjectComponent();
        $page_params        = $objectcomponent->GetPageParams('qc', $qc_id);
        
        $this->page_name    = $page_params['page_name'];
        $this->breadcrumb   = $page_params['breadcrumb'];
        
        $this->_assign('object_stat', $page_params['stat']);
        
        $modelAttachment    = new Attachment();
        $attachments_list   = $modelAttachment->GetListByType('', 'qc', $qc_id);
        $this->_assign('attachments_list', $attachments_list['data']);

        $this->_display('view');
    }
    
    /**
     * QCs List Page
     * url: /qcs
     */
    function index()
    {        
        $this->page_name    = 'Quality Certificates';
        $this->breadcrumb   = array($this->page_name => '');

        $qcs    = new QC();
        $list   = $qcs->GetList();

        $this->_assign('list',  $list);
        $this->_assign('count', count($list));

        if (!empty($list)) $this->_assign('filter', true);
        
        $this->context = true;
        
        $this->_display('index');
    }    
    
    /**
     * Shows QC Add/Edit Page
     * 
     * url: /qc/{id}/edit
     * url: /qc/add/{object_alias}:{object_id}
     * url: /qc/add
     * 
     * @version 20130116, zharkov
     */
    function edit()
    {
        $source_doc_alias   = Request::GetString('source_doc',      $_REQUEST);
        $source_doc_id      = Request::GetInteger('source_doc_id',  $_REQUEST);
        $qc_id              = Request::GetInteger('id',             $_REQUEST);        

        if ($qc_id > 0)
        {
            $modelQC    = new QC();
            $qc         = $modelQC->GetById($qc_id);
            
            if (empty($qc)) _404();
            $qc = $qc['qc'];
        }
        else if (in_array($source_doc_alias, array('order')) && $source_doc_id > 0)
        {
            if ($source_doc_alias == 'order')
            {
                $modelOrder = new Order();
                $order      = $modelOrder->GetById($source_doc_id);
                
                if (empty($order)) _404();
                
                $order  = $order['order'];
                $qc     = array(
                    'order_id'      => $source_doc_id,
                    'order'         => $order,
                    'biz'           => (isset($order['biz']) ? $order['biz']['doc_no'] : null),
                    'biz_id'        => $order['biz_id'],
                    'customer'      => (isset($order['company']) ? $order['company']['doc_no'] : null),
                    'customer_id'   => $order['company_id'],
                    'mam_co'        => $order['order_for']
                );
                
                if ($order['order_for'] == 'mam')
                {
                    $qc['units']        = 'mm/mt';
                    $qc['dim_unit']     = 'mm';
                    $qc['wght_unit']    = 'mt';
                }
                else
                {
                    $qc['units']        = 'in/lb';
                    $qc['dim_unit']     = 'in';
                    $qc['wght_unit']    = 'lb';                    
                }                
            }
        }
        else
        {
            $qc = array(
                'order_id'  => 0,
                'mam_co'    => 'mam',
                'units'     => 'mm/mt',
                'dim_unit'  => 'mm',
                'wght_unit' => 'mt'
            );
        }
        
        $is_saving          = isset($_REQUEST['btn_save']);
        $is_adding_items    = isset($_REQUEST['btn_additems']);
        $is_edit_items      = isset($_REQUEST['btn_edititems']);

        if ($is_saving || $is_adding_items || $is_edit_items)
        {
            $form = $_REQUEST['form'];
            
            $mam_co                     = Request::GetString('mam_co', $form);
            $stock_id                   = Request::GetInteger('stock_id', $form);
            $biz                        = Request::GetString('biz', $form);
            $biz_id                     = Request::GetInteger('biz_id', $form);
            $order                      = Request::GetString('order', $form);
            $order_id                   = Request::GetInteger('order_id', $form);
            $certification_standard     = Request::GetString('certification_standard', $form);
            $commodity_name             = Request::GetString('commodity_name', $form);
            $standard                   = Request::GetString('standard', $form);
            $customer                   = Request::GetString('customer', $form);
            $customer_id                = Request::GetInteger('customer_id', $form);
            $customer_order_no          = Request::GetString('customer_order_no', $form);
            $manufacturer               = Request::GetString('manufacturer', $form);
            $country_of_origin          = Request::GetString('country_of_origin', $form);
            $delivery_conditions        = Request::GetString('delivery_conditions', $form);
            $steelmaking_process        = Request::GetString('steelmaking_process', $form);
            $ultrasonic_test            = Request::GetString('ultrasonic_test', $form);
            $marking                    = Request::GetString('marking', $form);
            $visual_inspection          = Request::GetString('visual_inspection', $form);
            $flattening                 = Request::GetString('flattening', $form);
            $stress_relieving           = Request::GetString('stress_relieving', $form);
            $surface_quality            = Request::GetString('surface_quality', $form);
            $tolerances_on_thickness    = Request::GetString('tolerances_on_thickness', $form);
            $tolerances_on_flatness     = Request::GetString('tolerances_on_flatness', $form);
            $ce_mark                    = Request::GetBoolean('ce_mark', $form);
            $no_weld_repair             = Request::GetBoolean('no_weld_repair', $form);
            $units                      = Request::GetString('units', $form);
            $test_ref                   = Request::GetString('test_ref', $form);
            $elongation_in              = ''; //Request::GetString('elongation_in', $form);
            $smaple_direction_in        = ''; //Request::GetString('smaple_direction_in', $form);
            
            $modelQC    = new QC();
            $result     = $modelQC->Save($qc_id, $mam_co, $stock_id, $biz, $biz_id, $order_id, $customer, $customer_id, $certification_standard, $commodity_name, 
                                    $standard, $customer_order_no, $manufacturer, $country_of_origin, $surface_quality, 
                                    $tolerances_on_thickness, $tolerances_on_flatness, $steelmaking_process, $delivery_conditions, 
                                    $ultrasonic_test, $marking, $visual_inspection, $flattening, $stress_relieving, 
                                    $elongation_in, $smaple_direction_in, $ce_mark, $no_weld_repair, $units, $test_ref);
            
            // save items
            if (empty($qc_id) && isset($_REQUEST['item']))
            {
                foreach ($_REQUEST['item'] as $item_id => $row)
                {
                    if (empty($item_id)) continue;
                    $modelQC->SaveItem($result['id'], $item_id);
                }
            }

            // generate PDF on save
            if ($is_saving)
            {                

/*          there is no sense in Stock, all QC are from MaM
                $modelStock = new Stock();
                $stock      = $modelStock->GetById($stock_id);
                $order_for  = $stock['stock']['order_for'];
*/
                // generate pdf
                $modelQCPdf = new QCPdf();
                $modelQCPdf->Generate($result['id']);

                $this->_message('Certificate Of Quality was sucessfully saved ', MESSAGE_OKAY);
                $this->_redirect(array('qc', $result['id']));                
            }
            else if ($is_edit_items)
            {
                $items_ids = '';
                foreach ($modelQC->GetItems($result['id']) as $item)
                {
                    $items_ids .= (empty($items_ids) ? '' : ',') . $item['steelitem_id'];
                }
                
                $this->_redirect(array('target', 'qc:' . $result['id'], 'item', 'edit', $items_ids), false);
            }
            else
            {
                $this->_redirect(array('target', 'qc:' . $result['id'], 'items'), false);
            }
        }

        
        // items list
        if ($qc_id > 0)
        {
            $items = $modelQC->GetItems($qc_id);
        }
        else if (!empty($source_doc_alias) && !empty($source_doc_id))
        {
            if ($source_doc_alias == 'order')
            {
                $modelOrder = new Order();
                $status_id  = ITEM_STATUS_INVOICED + 1;     // 20130620, zharkov: all statuses
                $items      = $modelOrder->GetOrdersItems($source_doc_id, $status_id);
            }
        }
        else
        {
            $items = array();
        }
        

        if($qc_id > 0)
        {
            $this->breadcrumb['Certificate Of Quality No ' . $qc['doc_no']] = '/qc/' . $qc_id;
            $this->page_name = 'Edit Certificate Of Quality';
        }
        else
        {
            $this->page_name = 'New Certificate Of Quality';
        }

        $this->breadcrumb[$this->page_name] = '';
        
        $dimension_units    = array();
        $weight_units       = array();
        foreach ($items as $key => $item)
        {
            $item = $item['steelitem'];
            
            if ($item['nominal_thickness_mm'] > 0) $items[$key]['steelitems']['thickness_mm'] = $item['nominal_thickness_mm'];
            if ($item['nominal_width_mm'] > 0) $items[$key]['steelitems']['width_mm'] = $item['nominal_width_mm'];
            if ($item['nominal_length_mm'] > 0) $items[$key]['steelitems']['length_mm'] = $item['nominal_length_mm'];
            
            $dimension_units[$item['dimension_unit']]   = $item['dimension_unit'];
            $weight_units[$item['weight_unit']]         = $item['weight_unit'];
        }

        // show dimensions type select
        if (isset($dimension_units['in']))
        {
            $this->_assign('include_dimensions_select', true);
        }
        
        // more then one unit in QC
        if ($qc['dim_unit'] == 'in' && isset($dimension_units['mm']))
        {
            $this->_assign('multiunits', true);
        }

        $this->_assign('form',          $qc);
        $this->_assign('items',         $items);
        $this->_assign('include_ui',    true);
        $this->_assign('qc_id',         $qc_id);
        
        $this->_assign_total($items);
        
        $this->js = 'qc_edit';
        
        $this->_display('edit');        
    }
}
