<?php
require_once APP_PATH . 'classes/components/object.class.php';
require_once APP_PATH . 'classes/core/Pagination.class.php';

require_once APP_PATH . 'classes/models/attachment.class.php';
require_once APP_PATH . 'classes/models/order.class.php';
require_once APP_PATH . 'classes/models/qctype.class.php';
require_once APP_PATH . 'classes/models/sc.class.php';
require_once APP_PATH . 'classes/models/sc_pdf.class.php';

class MainController extends ApplicationController
{
    function MainController()
    {
        ApplicationController::ApplicationController();
        
        $this->authorize_before_exec['edit']    = ROLE_STAFF;
        $this->authorize_before_exec['index']   = ROLE_STAFF;
        $this->authorize_before_exec['view']    = ROLE_STAFF;
        
        $this->context = true;
    }

    /**
     * Отображает страницу со списком Sale Confirmations
     * 
     * @version 20120722, zharkov
     */
    function index()
    {
        $this->page_name                    = 'Sale Confirmations';
        $this->breadcrumb[$this->page_name] = '';
        
        $sc     = new SC();
        $rowset = $sc->GetList($this->page_no);
        
        $pager = new Pagination();
        $this->_assign('pager_pages',   $pager->PreparePages($this->page_no, $rowset['count']));
        $this->_assign('count',         $rowset['count']);

        $this->_assign('list',          $rowset['data']);
        $this->_assign('filter',        true);
        
        $this->_display('index');
    }
    
    /**
     * Отображает страницу редактирования sc
     * url: /sc/add/{order_id}
     * url: /sc/edit/{id}
     */
    function edit()
    {
        $order_id   = Request::GetInteger('order_id', $_REQUEST);
        $sc_id      = Request::GetInteger('id', $_REQUEST);
        
        if (empty($order_id) && empty($sc_id)) _404();
        
        if ($sc_id > 0)
        {
            $scs  = new SC();
            $sc   = $scs->GetById($sc_id);
            
            if (empty($sc)) _404();
            
            $order_id = $sc['sc']['order_id'];
        }
        
        $orders     = new Order();
        $order      = $orders->GetById($order_id);        
        $is_collection = in_array($order['order']['delivery_point'], array('col', 'exw', 'fca'));
        
        $this->breadcrumb = array(
            'Orders'                => '/orders',
            'Order # ' . $order_id  => '/order/view/' . $order_id            
        );
        
        if (isset($_REQUEST['btn_dont_save']))
        {
            if ($sc_id > 0)
            {
                $this->_redirect(array('sc', $sc_id));
            }
            else
            {
                $this->_redirect(array('order', 'view', $order_id));
            }
        }
        else if (isset($_REQUEST['btn_save']))
        {
            $form               = $_REQUEST['form'];
            $selected_positions = isset($_REQUEST['position']) ? $_REQUEST['position'] : array();
            
            $person_id      = Request::GetInteger('person_id', $form);
            $delivery_point = Request::GetString('delivery_point', $form);
            $delivery_date  = Request::GetString('delivery_date', $form);
            $qctype_id      = Request::GetInteger('qctype_id', $form);
            $qctype_new     = Request::GetString('qctype_new', $form);            
            
            if (empty($person_id))
            {
                $this->_message('Person must be specified !', MESSAGE_ERROR);
            }
            else if (empty($delivery_point))
            {
                $this->_message($is_collection ? 'Collection Address must be specified !' : 'Delivery Point must be specified !', MESSAGE_ERROR);
            }
            else if (empty($delivery_date))
            {
                $this->_message($is_collection ? 'Load Readiness must be specified !' : 'Delivery Date must be specified !', MESSAGE_ERROR);                
            }            
            else if (empty($selected_positions))
            {
                $this->_message('Positions must be selected !', MESSAGE_ERROR);                
            }
/*
            else if (empty($qctype_id) && empty($qctype_new))
            {
                $this->_message('Quality Certificate must be specified !', MESSAGE_ERROR);                
            }
*/            
            else
            {
                $chemical_composition       = Request::GetString('chemical_composition', $form);
                $tolerances                 = Request::GetString('tolerances', $form);
                $hydrogen_control           = Request::GetString('hydrogen_control', $form);
                $surface_quality            = Request::GetString('surface_quality', $form);
                $surface_condition          = Request::GetString('surface_condition', $form);
                $side_edges                 = Request::GetString('side_edges', $form);
                $marking                    = Request::GetString('marking', $form);
                $packing                    = Request::GetString('packing', $form);
                $stamping                   = Request::GetString('stamping', $form);
                $ust_standard               = Request::GetString('ust_standard', $form);
                $dunnaging_requirements     = Request::GetString('dunnaging_requirements', $form);
                $documents_supplied         = Request::GetString('documents_supplied', $form);
                $front_and_back_ends        = Request::GetString('front_and_back_ends', $form);
                $origin                     = Request::GetString('origin', $form);
                $inspection                 = Request::GetString('inspection', $form);
                $delivery_form              = Request::GetString('delivery_form', $form);
                $reduction_of_area          = Request::GetString('reduction_of_area', $form);
                $testing                    = Request::GetString('testing', $form);
                $delivery_cost              = Request::GetString('delivery_cost', $form);
                $notes                      = Request::GetString('notes', $form);
                $transport_mode             = Request::GetString('transport_mode', $form);
                
                if (empty($qctype_id))
                {
                    $qctypes     = new QCType();
                    $qctype_id   = $qctypes->GetQCTypeId($qctype_new);
                }
                
                $scs    = new SC();
                $result = $scs->Save($sc_id, $order_id, $person_id, $delivery_point, $delivery_date, $chemical_composition, 
                                        $tolerances, $hydrogen_control, $surface_quality, $surface_condition, $side_edges, $marking, 
                                        $packing, $stamping, $ust_standard, $dunnaging_requirements, $documents_supplied, $front_and_back_ends, 
                                        $origin, $inspection, $delivery_form, $reduction_of_area, $testing, $delivery_cost, $qctype_id, 
                                        $notes, $transport_mode);
                
                if (empty($result) || isset($result['ErrorCode']))
                {
                    $this->_message('Error was occured when saving SC !', MESSAGE_ERROR);
                }
                else
                {
                    // сохраняет позиции
                    $scs->ClearPositions($result['id']);                    
                    foreach ($selected_positions as $key => $position_id) $scs->SavePosition($result['id'], $position_id);
                    
                    // формирует pdf
                    $scpdf = new SCPdf();
                    $scpdf->Generate($result['id']);
                    
                    $this->_message('Sale Confirmation was successfully saved !', MESSAGE_OKAY);
                    $this->_redirect(array('sc', $result['id']));
                }                
            }            
        }
        else if ($sc_id > 0)
        {
            $this->page_name                    = 'Edit ' . $sc['sc']['doc_no'];
            $this->breadcrumb[$this->page_name] = '/sc/' . $sc_id . '/edit';
            
            $form       = $sc['sc'];
            $order_id   = $form['order_id'];
            
            $scs  = new SC();
            $selected_positions = $scs->GetPositions($sc_id);
        }
        else
        {
            $this->page_name                    = 'New Sale Confirmation';
            $this->breadcrumb[$this->page_name] = '/sc/add/' . $order_id;

            $orders     = new Order();
            $order      = $orders->GetById($order_id);
            $order      = $order['order'];
            $positions  = $orders->GetPositions($order_id);

            
            // delivery point            
            if ($is_collection)
            {
                $delivery_points = array();

                foreach ($positions as $position)
                {
                    $stock_holders = array();
                    if (isset($position['steelitems']) && !empty($position['steelitems']))
                    {
                        foreach ($position['steelitems'] as $item)
                        {
                            $stock_holders[$item['steelitem']['stockholder_id']] = $item['steelitem']['stockholder_id'];
                        }                        
                    }

                    foreach ($stock_holders as $stock_holder_id)
                    {
                        $delivery_points[$stock_holder_id][] = array('position' => $position, 'qtty' => (count($stock_holders) > 1 ? '?' : $position['qtty']));                        
                    }
                }

                if (count($delivery_points) > 1)
                {                    
                    $delivery_point = '';
                    foreach ($delivery_points as $stock_holder_id => $dp_positions)
                    {
                        if ($stock_holder_id > 0)
                        {
                            $companies          = new Company();
                            $company            = $companies->GetById($stock_holder_id);
                            
                            $delivery_point     .= $company['company']['full_address'] . "\n";
                        }
                        else
                        {
                            $delivery_point     .= 'Unknown Stockholder' . "\n";
                        }                        

                        foreach ($dp_positions as $row)
                        {
                            $delivery_point .= $row['position']['steelgrade']['title'] . ' ' . $row['position']['thickness'] . ' X ' . $row['position']['width'] . ' X ' . $row['position']['length'] . "\n";
                        }
                        $delivery_point .= "\n";
                    }
                }
                else
                {
                    $stock_holder       = array_keys($delivery_points);
                    $stock_holder_id    = $stock_holder[0];
                    
                    $companies          = new Company();
                    $company            = $companies->GetById($stock_holder_id); //dg($company);
                    $delivery_point     = $company['company']['full_address'];
                }                
            }
            else
            {
                $delivery_point = $order['delivery_point_title'] . ' ' . $order['delivery_town'];
            }
            
            
            // delivery time
            $delivery_times = array();
            foreach ($positions as $position)
            {
                $sub_delivery_times = explode(';', (empty($position['deliverytime']) ? $order['delivery_date'] : $position['deliverytime']));                
                foreach ($sub_delivery_times as $delivery_time)
                {
                    $delivery_times[$delivery_time][] = array('position' => $position, 'qtty' => count($sub_delivery_times) > 1 ? '?' : $position['qtty']); 
                }
            }
            
            
            if (count($delivery_times) > 1)
            {                    
                $delivery_time = '';
                foreach ($delivery_times as $title => $positions)
                {
                    $delivery_time .= $title . "\n";
                    foreach ($positions as $row)
                    {
                        $delivery_time .= $row['position']['steelgrade']['title'] . ' ' . $row['position']['thickness'] . ' X ' . $row['position']['width'] . ' X ' . $row['position']['length'] . "\n";
                    }
                    $delivery_time .= "\n";
                }
            }
            else
            {
                $delivery_times = array_keys($delivery_times);
                $delivery_time  = $delivery_times[0];
            }
            
            
            // transport mode
            $max_width  = 0;
            $max_length = 0;
            foreach ($positions as $position)
            {
                $max_width  = $position['width_mm'] > $max_width ? $position['width_mm'] : $max_width;
                $max_length = $position['length_mm'] > $max_length ? $position['length_mm'] : $max_length;
            }
            
            $transport_mode = '';
            if ($is_collection)
            {
                $transport_mode = 'Truck must be of an open platform type or with movable side-walls/roof.';
                if ($max_width > 2400) $transport_mode = 'Only flatbed (open platform) truck is acceptable for loading.';
                
                $transport_mode .= " " . 'Truck number must be advised and collection date/time agreed min 24 hours in advance.';
            }
            
            
            $form = array(
                'person_id'         => $order['person_id'],
                'delivery_point'    => $delivery_point,
                'delivery_date'     => $delivery_time,
                'delivery_cost'     => $order['delivery_cost'],
                'transport_mode'    => $transport_mode,
                'tolerances'        => "Thickness: Class A, EN10029(1991) \nWidth: +70/-0 mm \nLength: +200/-0 mm \nFlatness: Class N, EN 10029 (1991)"
            );
        }
                
        
        
        $orders     = new Order();
        $positions  = $orders->GetPositions($order_id);
        $order      = $orders->GetById($order_id);
        $order      = $order['order'];
        
        $total_qtty     = 0;
        $total_weight   = 0;
        $total_value    = 0;
        foreach ($positions as $key => $row)
        {
            if (isset($selected_positions))
            {
                $positions[$key]['checked'] = (array_key_exists($row['position_id'], $selected_positions) ? 1 : 0);
            }
            else
            {
                $positions[$key]['checked'] = 1;
            }
            
            if ($positions[$key]['checked'])
            {
                $total_qtty     += $positions[$key]['qtty'];
                $total_weight   += $positions[$key]['weight'];
                $total_value    += $positions[$key]['value'];                
            }
        }
        
        $this->_assign('form',      $form);
        $this->_assign('order',     $order);
        $this->_assign('positions', $positions);
        
        $companies  = new Company();
        $persons    = $companies->GetPersons($order['company_id']);
        $this->_assign('persons', $persons['data']);
        
        $qctypes = new QCType();
        $this->_assign('qctypes', $qctypes->GetList());
        
        $this->_assign('total_qtty',    $total_qtty);
        $this->_assign('total_weight',  $total_weight);
        $this->_assign('total_value',   $total_value);
        
        $this->js = 'sc_edit';
        
        $this->_display('edit');
    }

    /**
     * Отображает страницу просмотра sc
     * url: /sc/view/{id}
     */
    function view()
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
        
                        
        $this->page_name    = $sc['sc']['doc_no'];
        
        $this->breadcrumb   = array(
            'Sale Confirmations'    => '/sc',
            $this->page_name        => ''
        );
        
/*        
        $this->breadcrumb   = array(
            'Orders'                => '/orders',
            'Order # ' . $order_id  => '/order/view/' . $order_id,
            $this->page_name        => ''
        );
*/        
        
        $objectcomponent = new ObjectComponent();
        $this->_assign('object_stat', $objectcomponent->GetStatistics('sc', $sc_id));        
        
        $modelAttachment    = new Attachment();
        $attachments_list   = $modelAttachment->GetListByType('', 'sc', $sc_id);
        $this->_assign('attachments_list', $attachments_list['data']);
        
        $this->js = 'sc_view';        
        $this->_display('view');
    }    
}
