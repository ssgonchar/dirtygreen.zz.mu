<?
require_once APP_PATH . 'classes/models/order.class.php';

class MainPrintController extends ApplicationPrintController
{
    function MainPrintController()
    {
        ApplicationPrintController::ApplicationPrintController();
        
        $this->authorize_before_exec['view'] = ROLE_STAFF;
    }
    
    
    /**
    * Отображает страницу просмотра заказа
    * 
    * @link /order/{$oder_id}/~print
    */
    public function view()
    {
        $order_id = Request::GetInteger('id', $_REQUEST);
        
        if (empty($order_id)) _404();
        
        $modelOrder = new Order();
        $order      = $modelOrder->GetById($order_id);
        
        if (empty($order) || (empty($order['order']['status']) && $order['order']['created_by'] != $this->user_id)) _404();
        
        // для неподтвержденных заказов созданных со склада, переводим на страницу редактирования
        if ($order['order']['status'] == 'nw') $this->_redirect(array('order', $order_id, 'edit'));
        

        $positions = $modelOrder->GetPositions($order_id);

        $total_qtty         = 0;
        $total_weight       = 0;
        $total_value        = 0;
        $conflicted_items   = array();
        
        foreach ($positions as $key => $position)
        {
            $total_qtty   += $position['qtty'];
            $total_weight += $position['weight'];
            $total_value  += $position['value'];
            
            // поиск конфликтных айтемов
            if (isset($position['steelitems']))
            {
                foreach ($position['steelitems'] as $item)
                {
                    $item = $item['steelitem'];
                    if ($item['order_id'] == $order_id && !empty($item['is_conflicted']))
                    {
                        $conflicted_items[] = $item;
                        $positions[$key]['is_conflicted'] = true;
                    }
                }
            }
        }

        $this->_assign('order',             $order['order']);
        $this->_assign('positions',         $positions);
                
        $this->_assign('conflicted_items',  $conflicted_items);
        $this->_assign('total_qtty',        $total_qtty);
        $this->_assign('total_weight',      $total_weight);
        $this->_assign('total_value',       $total_value);

        $modelObjectComponent   = new ObjectComponent();
        $page_params            = $modelObjectComponent->GetPageParams('order', $order_id);
        
        $this->page_name    = 'Order No ' . $page_params['page_name'];
        
        $this->_assign('object_stat', $page_params['stat']);
        $this->_assign('page_name', $this->page_name);
        
        $this->_display('view');
    }
    
    
    
    function index()
    {
        if (isset($_REQUEST['btn_select']))
        {
            $form = $_REQUEST['form'];
            
            $order_for      = Request::GetString('order_for', $form);
            $biz_title      = Request::GetString('biz_title', $form);
            $biz_id         = Request::GetInteger('biz_id', $form);
            $company_title  = Request::GetString('company_title', $form);
            $company_id     = Request::GetInteger('company_id', $form);
            $keyword        = Request::GetString('keyword', $form);
            $period_from    = Request::GetDateForDB('period_from', $form);
            $period_to      = Request::GetDateForDB('period_to', $form);
            $status         = Request::GetString('status', $form);
            $steelgrade_id  = Request::GetInteger('steelgrade_id', $form);
            $thickness      = Request::GetString('thickness', $form);
            $width          = Request::GetString('width', $form);
            $type           = Request::GetString('type', $form);
            

            $filter     = (empty($order_for) ? '' : 'orderfor:' . $order_for . ';')
                        . (empty($biz_title) || empty($biz_id) ? '' : 'biz:' . $biz_id . ';')
                        . (empty($company_title) || empty($company_id) ? '' : 'company:' . $company_id . ';')
                        . (empty($keyword) ? '' : 'keyword:' . $keyword . ';')
                        . (empty($period_from) ? '' : 'periodfrom:' . str_replace('00:00:00', '', $period_from) . ';')
                        . (empty($period_to) ? '' : 'periodto:' . str_replace('00:00:00', '', $period_to) . ';')
                        . (empty($status) ? '' : 'status:' . $status . ';')
                        . (empty($steelgrade_id) ? '' : 'steelgrade:' . $steelgrade_id . ';')
                        . (empty($thickness) ? '' : 'thickness:' . $thickness . ';')
                        . (empty($width) ? '' : 'width:' . $width . ';')
                        . (empty($type) ? '' : 'type:' . $type . ';');
            
            if (empty($filter)) 
            {
                $this->_redirect(array('orders~filter'));
            }
            else
            {
                $this->_redirect(array('orders', 'filter', str_replace(' ', '+', $filter)) . '~filter', false);
            }
        }
//        else if (isset($_REQUEST['btn_create_ra']) && isset($_REQUEST['selected_ids']))
//        {
//            $seleted_ids = $_REQUEST['selected_ids'];
//            $this->_redirect(array('ra', 'add', implode(',', $seleted_ids)));
//        }
        
        $filter         = Request::GetString('filter', $_REQUEST);
        $filter         = urldecode($filter);
        $filter_params  = array();
        
        if (empty($filter))
        {
            $this->page_name = 'Orders';
//            $this->breadcrumb[$this->page_name] = '/orders';
        }
        else
        {
            $this->page_name = 'Filtered Orders';
//            
//            $this->breadcrumb['Orders']         = '/orders';
//            $this->breadcrumb[$this->page_name] = $this->pager_path;
            
            $filter = explode(';', $filter);
            foreach ($filter as $row)
            {
                if (empty($row)) continue;
                
                $param = explode(':', $row);
                $filter_params[$param[0]] = Request::GetHtmlString(1, $param);
            }
            
            $this->_assign('filter', true);
        }
        
        $order_for      = Request::GetString('orderfor', $filter_params);
        $biz_id         = Request::GetInteger('biz', $filter_params);
        $company_id     = Request::GetInteger('company', $filter_params);
        $period_from    = Request::GetString('periodfrom', $filter_params);
        $period_from    = !(preg_match('/\d{4}-\d{2}-\d{2}/', $period_from)) ? null : $period_from . ' 00:00:00';
        $period_to      = Request::GetString('periodto', $filter_params);
        $period_to      = !(preg_match('/\d{4}-\d{2}-\d{2}/', $period_to)) ? null : $period_to . ' 00:00:00';
        $status         = Request::GetString('status', $filter_params);
        $steelgrade_id  = Request::GetInteger('steelgrade', $filter_params);
        $thickness      = Request::GetString('thickness', $filter_params);
        $width          = Request::GetString('width', $filter_params);
        $keyword        = Request::GetString('keyword', $filter_params);
        $type           = Request::GetString('type', $filter_params);

        
        $orders = new Order();
        $rowset = $orders->GetList($order_for, $biz_id, $company_id, $period_from, $period_to, $status, 
                                    $steelgrade_id, $thickness, $width, $keyword, $type, 1, 1000);

        if ($order_for != '')
        {
            $this->_assign('show_total', true);
            
            if ($order_for == 'pa')
            {
                $this->_assign('weight_unit',   'lb');
                $this->_assign('currency',      'usd');
            }
            else
            {
                $this->_assign('weight_unit',   'ton');
                $this->_assign('currency',      'eur');
            }
            
            //dg($list);
            $total_qtty   = 0;
            $total_weight = 0;
            $total_value  = 0;
            foreach ($rowset['data'] as $order)
            {
                $total_qtty   += $order['order']['quick']['qtty'];
                $total_weight += $order['order']['quick']['weight'];
                $total_value  += $order['order']['quick']['value'];
            }

            $this->_assign('total_qtty',    $total_qtty);
            $this->_assign('total_weight',  $total_weight);
            $this->_assign('total_value',   $total_value);
        }
        
        if ($biz_id > 0)
        {
            $bizes  = new Biz();
            $biz    = $bizes->GetById($biz_id);
            
            if (!empty($biz))
            {
                $this->_assign('biz_id',    $biz_id);
                $this->_assign('biz_title', $biz['biz']['number_output']);
            }
        }
        
        if ($company_id > 0)
        {
            $companies  = new Company();
            $company    = $companies->GetById($company_id);
            
            if (!empty($company))
            {
                $this->_assign('company_id',    $company_id);
                $this->_assign('company_title', $company['company']['title']);
            }
        }        
        
        $this->_assign('order_for',     $order_for);
        $this->_assign('company_id',    $company_id);
        $this->_assign('period_from',   $period_from);
        $this->_assign('period_to',     $period_to);
        $this->_assign('status',        $status);
        $this->_assign('steelgrade_id', $steelgrade_id);
        $this->_assign('thickness',     $thickness);
        $this->_assign('width',         $width);
        $this->_assign('keyword',       $keyword);
        $this->_assign('type',          $type);
                        
        $this->_assign('count',     $rowset['count']);
        $this->_assign('list',      $rowset['data']);
        
        $has_in_processing = false;
        foreach ($rowset['data'] as $order)
        {
            if ($order['order']['status'] != 'co' && $order['order']['status'] != 'ca')
            {
                $has_in_processing = true;
                break;
            }
        }
        
        if ($has_in_processing) $this->_assign('has_in_processing', true);
        
//        $pager = new Pagination();
//        $this->_assign('pager_pages', $pager->PreparePages($this->page_no, $rowset['count']));
        
        
        $companies = new Company();
        $this->_assign('companies',     $companies->GetMaMList());
        $this->_assign('steelgrades',   $orders->GetSteelgrades());
//        $this->_assign('include_ui',    true);
//
//        $this->js = 'order_index';
        $this->_assign('page_name', $this->page_name);
        $this->_display('index');
    }
}