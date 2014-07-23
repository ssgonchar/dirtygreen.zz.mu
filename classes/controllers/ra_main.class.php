<?php
require_once APP_PATH . 'classes/core/Pagination.class.php';
require_once APP_PATH . 'classes/models/attachment.class.php';
require_once APP_PATH . 'classes/models/cmr.class.php';
require_once APP_PATH . 'classes/models/cmr_pdf.class.php';
require_once APP_PATH . 'classes/models/ddt.class.php';
require_once APP_PATH . 'classes/models/ddt_pdf.class.php';
require_once APP_PATH . 'classes/models/order.class.php';
require_once APP_PATH . 'classes/models/steelitem.class.php';
require_once APP_PATH . 'classes/models/steelposition.class.php';
require_once APP_PATH . 'classes/models/stock.class.php';
require_once APP_PATH . 'classes/models/ra.class.php';
require_once APP_PATH . 'classes/models/location.class.php';
require_once APP_PATH . 'classes/models/company.class.php';
require_once APP_PATH . 'classes/models/ra_pdf.class.php';

class MainController extends ApplicationController
{

    function MainController()
    {
        ApplicationController::ApplicationController();
        
        $this->authorize_before_exec['add']         = ROLE_STAFF;
        $this->authorize_before_exec['addvariant']  = ROLE_STAFF;
        $this->authorize_before_exec['delete']      = ROLE_STAFF;
        $this->authorize_before_exec['edit']        = ROLE_STAFF;
        $this->authorize_before_exec['index']       = ROLE_STAFF;
        $this->authorize_before_exec['view']        = ROLE_STAFF;
        
        $this->context      = true;
        $this->breadcrumb   = array('Release Advices' => '/ra');        
    }
    
    /**
     * Отображает страницу со списком
     * url: /ra
     * 
     * @version 20121012, d10n
     */
    function index()
    {
        $this->page_name = 'Release Advices';
        
        $modelRA = new RA();
        $data_set = $modelRA->GetList($this->page_no);
        
        $pager = new Pagination();
        $this->_assign('pager_pages',   $pager->PreparePages($this->page_no, $data_set['count']));
        $this->_assign('count',         $data_set['count']);
        
        $this->_assign('list',          $data_set['data']);
        if (!empty($data_set['data'])) $this->_assign('filter', true);
        
        $this->js = 'ra_index';
        $this->context  = true;
        
        $this->_display('index');
    }
    
    /**
     * Отображает страницу выбора айтемов для RA
     * url: /ra/add/{order_ids}
     * 
     * @version 20121017, zharkov
     */
    function add()
    {
        if (isset($_REQUEST['btn_create']))
        {
            $steelitem_ids = isset($_REQUEST['steelitem_ids']) ? $_REQUEST['steelitem_ids'] : array();
            
            if (empty($steelitem_ids))
            {
                $this->_message('Items must be specified !', MESSAGE_ERROR);
            }
            else
            {
                $steelitems = array();
                foreach ($steelitem_ids as $item_id)
                {
                    $steelitems[] = array('steelitem_id' => $item_id);
                }

                $modelSteelItem = new SteelItem();
                $steelitems     = $modelSteelItem->FillSteelItemInfo($steelitems);

                // фильтр айтемов по стокхолдерам
                $stockholders   = array();
                foreach ($steelitems as $row)
                {
                    $row            = $row['steelitem'];
                    $stockholder_id = $row['stockholder_id'];
                    
                    if (empty($stockholder_id)) continue;
                    if (!isset($stockholders[$stockholder_id]))
                    {
                        $stockholders[$stockholder_id] = array(
                            'stock_object_alias'    => (isset($row['stockholder']) && $row['stockholder']['country_id'] == 225 ? 'platesahead' : 'mam'),
                            'items'                 => array(),
                        );
                    }
                    
                    $stockholders[$stockholder_id]['items'][] = $row;
                }
                
                // сохранение
                $modelRA = new RA();
                foreach ($stockholders as $stockholder_id => $row)
                {
                    $notes = $row['stock_object_alias'] == 'platesahead'
                        ? 'Please be so kind to state actual dimensions in your bill of lading as well as plate ID'
                        : 'Please send to us DDT & weighbridge ticket as soon as issued';
                    
                    $result     = $modelRA->Save(0, $stockholder_id, 0, 0, '', '', '', '', '', RA_STATUS_OPEN, '', '', '', '', '', '', $notes);
                    
                    $item_ids   = '';
                    foreach ($row['items'] as $item)
                    {
                        $item_ids .= ',' . $item['id'];
                    }
                    
                    $item_ids = trim($item_ids, ',');
                    $modelRA->ItemsAdd(0, $result['ra_id'], $item_ids);
                }
                
                $this->_message('Release Advice was created successfully', MESSAGE_OKAY);
                $this->_redirect(array('ra', $result['ra_id'], 'edit'));
            }
        }

        $order_ids = Request::GetString('order_ids', $_REQUEST);
        
        $modelOrder = new Order();
        $steelitems = array();
        foreach (explode(',', $order_ids) as $order_id)
        {
            foreach ($modelOrder->GetItems($order_id) as $item)
            {
                if (!in_array($item['status_id'], array(ITEM_STATUS_RELEASED, ITEM_STATUS_DELIVERED)))  /*    , ITEM_STATUS_INVOICED  */
                {
                    $steelitems[] = array('steelitem_id' => $item['id']);
                }
            }
        }

        $modelSteelItem = new SteelItem();
        $steelitems     = $modelSteelItem->FillSteelItemInfo($steelitems);
        
        $stockholders = array();
        foreach ($steelitems as $row)
        {
            $row            = $row['steelitem'];
            $stockholder_id = $row['stockholder_id'];
            
            if (!isset($stockholders[$stockholder_id]))
            {
                $stockholders[$stockholder_id] = array(
                    'stockholder_id'    => $stockholder_id,
                    'title'             => isset($row['stockholder']) ? ($row['stockholder']['doc_no'] . ', ' . $row['stockholder']['city']['title']) : '',
                    'dimension_unit'    => $row['dimension_unit'],
                    'weight_unit'       => $row['weight_unit'],                    
                    'items'             => array()
                );
            }
            
            $stockholders[$stockholder_id]['items'][] = $row;
        }
        
        $this->_assign('stockholders', $stockholders);
        
        $this->js = 'ra_add';
        $this->page_name                    = 'Items for New RA';
        $this->breadcrumb[$this->page_name] = '';

        $this->_display('add');
    }
    
    /**
     * Отображает страницу просмотра RA
     * url: /ra/{ra_id}
     * 
     * @version 20121018, zharkov
     */
    public function view()
    {
        $ra_id      = Request::GetInteger('id', $_REQUEST);
        if (empty($ra_id)) _404();
        
        $modelRA    = new RA();
        $ra         = $modelRA->GetById($ra_id);
		
        if (empty($ra)) _404();
        
        $ra = $ra['ra'];
        
        if ($ra['is_deleted'] == 1) _404();
        
        $this->_assign('items', $modelRA->GetItems($ra_id));
        
        $objects_list = $modelRA->GetListOfRelatedDocs($ra_id);
		//print_r('1');
        $this->_assign('objects_list', $objects_list);

        foreach ($objects_list as $doc)
        {
            if ($doc['object_alias'] == 'ddt') $ra['has_ddt'] = TRUE;
            if ($doc['object_alias'] == 'cmr') $ra['has_cmr'] = TRUE;
        }
		
        $this->_assign('ra',    $ra);
        
        $this->page_name = $ra['doc_no'];
        $this->breadcrumb[$this->page_name] = '';
        
        $modelAttachment    = new Attachment();
        $attachments_list   = $modelAttachment->GetListByType('', 'ra', $ra_id);
        $this->_assign('attachments_list', $attachments_list['data']);

        $this->js = 'ra_main';
        $this->_display('view');
    }

    /**
     * Отображает страницу редактирования RA
     * url: /ra/{id}/edit
     * 
     * @version 20121018, zharkov
     */
    public function edit()
    {
        $ra_id = Request::GetInteger('id', $_REQUEST);
        if (empty($ra_id)) _404();
        
        $modelRA    = new RA();
        $ra         = $modelRA->GetById($ra_id);
        if (empty($ra)) _404();
        
        $ra = $ra['ra'];
                
        // закрытые можно только просматривать
        if ($ra['status_id'] == RA_STATUS_CLOSED) 
        {
            $this->_redirect(array('ra', $ra_id));
        }
        
        // редактировать можно только открытые или модераторами предзакрытые
        if ($ra['status_id'] != RA_STATUS_OPEN && $this->user_role > ROLE_MODERATOR) 
        {
            $this->_redirect(array('ra', $ra_id));            
        }

        
        if (isset($_REQUEST['btn_add_item']))
        {
            $form = $_REQUEST['form'];
            
            // добавляет RA в сессию
            $_SESSION['ra-' . $ra_id]['form'] = $form;
            
            // переход к складу
            //$redirect_url = 'target/ra:' . $ra_id . '/positions/filter/stock:1';
            $redirect_url = 'target/ra:' . $ra_id . '/items';

            $this->_redirect(explode('/', $redirect_url), false);
        }
        else if (isset($_REQUEST['btn_save']))
        {
            $form = $_REQUEST['form'];
            //debug('1682', $form);
            $company            = Request::GetString('company', $form);
            $company_id         = Request::GetInteger('company_id', $form);
            $truck_number       = Request::GetString('truck_number', $form);
            $dest_stockholder_id= Request::GetInteger('dest_stockholder_id', $form);
            $destination        = Request::GetString('destination', $form);
            $loading_date       = Request::GetString('loading_date', $form);
            $marking            = Request::GetString('marking', $form);
            $dunnaging          = Request::GetString('dunnaging', $form);
            $weighed_weight     = Request::GetNumeric('weighed_weight', $form);
            $ddt_number         = Request::GetString('ddt_number', $form);
            $ddt_date           = Request::GetDateForDB('ddt_date', $form);
            $ddt_instructions   = Request::GetString('ddt_instructions', $form);
            $coupon             = Request::GetString('coupon', $form);
            $notes              = Request::GetString('notes', $form);
            $consignee          = Request::GetString('consignee', $form);
            $consignee_ref      = Request::GetString('consignee_ref', $form);
            $mm_dimensions      = Request::GetBoolean('mm_dimensions', $form);
            
            $stockholder_id     = 0;    // при сохранении не меняется
            
            if (empty($company))
            {
                $company_id         = 0;
                $form['company_id'] = $company_id;
            }
            else
            {
                // адаптация данных для формы
                $form['company'] = array();
                $form['company']['doc_no']  = $company;
                $form['company']['id']      = $company_id;
            }
            
            $form = array_merge($ra, $form);
            $this->_assign('form', $form);
            
            if (empty($dest_stockholder_id) && empty($destination))
            {
                $this->_message('Destination must be specified !', MESSAGE_ERROR);
            }
            else if (!empty($ddt_number) && empty($ddt_date))
            {
                $this->_message('DDT Date must be specified !', MESSAGE_ERROR);
            }
            else if (empty($ddt_number) && !empty($ddt_date))
            {
                $this->_message('DDT Number must be specified !', MESSAGE_ERROR);
            }
            else if (!empty($ddt_number) && $weighed_weight <= 0)
            {
                $this->_message('Weighed weight must be specified !', MESSAGE_ERROR);
            }
            else
            {                
                $status_id      = empty($ddt_number) ? RA_STATUS_OPEN : RA_STATUS_PENDING;
                $destination    = empty($dest_stockholder_id) ? $destination : '';
                
                $result = $modelRA->Save($ra_id, $stockholder_id, $dest_stockholder_id, $company_id, $truck_number, $destination, 
                    $loading_date, $marking, $dunnaging, $status_id, 0, $weighed_weight,
                    $ddt_number, $ddt_date, $ddt_instructions,
                    $coupon, $notes, $consignee, $consignee_ref, $mm_dimensions);
                
                if (empty($result))
                {
                    $this->_message('Error saving RA', MESSAGE_ERROR);
                }
                else
                {
                    // если меняется статус документа, desctination или меняются айтемы на варианты, нужно обновить таймлайн
                    $update_timeline = false;

                    if ($ra['status_id'] != $status_id)
                    {
                        $update_timeline = true;
                    }
                    
                    // если меняется взвешенный вес, теоритический вес, desctination или айтемы меняются на варианты,
                    // нужно обновить связанные доеументы
                    $update_related_docs = false;
                    //debug('1682', $ra);
                    if ($ra['dest_stockholder_id'] != $dest_stockholder_id || $ra['destination'] != $destination)
                    {
                        $update_timeline        = true;
                        $update_related_docs    = true;
                    }
                    
                    if ($ra['weighed_weight'] != $weighed_weight) $update_related_docs = true;
                    

                    // переопределение флага ra_items.is_theor_weight
                    $is_theor_weight_list = isset($form['is_theor_weight']) ? $form['is_theor_weight'] : array();
                    foreach($modelRA->GetItems($ra_id) as $item)
                    {
                        $is_theor_weight = Request::GetInteger($item['id'], $is_theor_weight_list);                        
                        $modelRA->ItemUpdate($ra_id, $item['id'], $is_theor_weight);
                        
                        if ($item['is_theor_weight'] != $is_theor_weight) $update_related_docs = true;
                    }
                                        
                    
                    // переопределение основных Айтемов
                    $primary_items_list = isset($form['primary_items']) ? $form['primary_items'] : array();                   
                    foreach ($primary_items_list as $parent_id => $variant)
                    {
                        $ra_item_id = Request::GetInteger(0, $variant);
                        $parent_id  = intval($parent_id);

                        if ($parent_id != $ra_item_id)
                        {
                            $modelRA->SetPrimaryItem($ra_id, $ra_item_id);
                            
                            $update_timeline        = true;
                            $update_related_docs    = true;                            
                        }                        
                    }
                    
                    // обновить взвешенный вес, обновить связанные документы если изменился главный айтем или теоритический вес
                    if ($update_related_docs)
                    {
                        $modelRA->ItemsRecalculateWW($ra_id);
                        $modelRA->UpdateRelatedDocs($ra_id);
                    }
                    
                    // обновляет timeline для айтемов
                    if ($update_timeline)
                    {
                        foreach($modelRA->GetItems($ra_id) as $item)
                        {
                            if ($item['parent_id'] == 0) $modelRA->ItemSaveTimeline($item['steelitem_id'], $ra_id);
                        }                        
                    }
                    
                    //если указан $dest_stockholder_id - апдейтим итем и ставим $status_id = "3"(on stock)
                    if(($dest_stockholder_id !== 0)){
                        $status_id = "3";
                        //необходимо узнать location_id по $dest_stockholder_id
                        $modelCompany    = new Company();
                        $company_info = $modelCompany->GetById($dest_stockholder_id);
                        $location_id = $company_info[company][location][id];
                        //выковыриваем id итемов из списка RA, которые надо апдейтить.
                        //debug('1682', $modelRA->GetItems($ra_id));
                        $ra_items = $modelRA->GetItems($ra_id);
                        //debug('1682', $ra_items);
                        foreach($ra_items as $key => $row)
                        {
                            $items_list[] = $ra_items[$key]['steelitem_id'];
                        }
                        //необходимо апдейтить stockholder в ra ?????

                        //апдейтим информацию по итемам
                        foreach($items_list as $item_id => $value)
                        {
                            $modelSteelItem    = new SteelItem();
                            $modelSteelItem->ItemLocationUpdate($items_list[$item_id], $dest_stockholder_id, $location_id, $status_id);
                        }
                    }
                    
                    $ra = $modelRA->GetById($ra_id);
                    
                    // формирование PDF-документа
                    $modelRAPdf = new RAPdf($ra['ra']['stock_object_alias']);
                    $modelRAPdf->Generate($ra_id);

                    $this->_message('RA was successfully saved', MESSAGE_OKAY);
                    //$this->_redirect(array('ra'));
                    //
                    $this->_redirect(array('ra'));
                    
                }
            }
        }
        else
        {
            $form = $ra;
        }

        //$this->page_name                    = 'Edit Release Advice';
        $this->page_name                    = 'Editing '.$ra['doc_no'];
        $this->breadcrumb[$ra['doc_no']]    = '/ra/' . $ra['id'];
        $this->breadcrumb[$this->page_name] = '';
        
        $this->_assign('form',          $form);
        $this->_assign('items',         $modelRA->GetItems($ra_id));
        
        $modelStock = new Stock();
        $this->_assign('dest_sholders_list', $modelStock->GetLocations(0, false));
        
        
        $this->_assign('include_ui',    true);
        
        $this->js = 'ra_main';
        
        $this->_display('edit');
    }
    
    /**
     * Добавляет вариант айтема к списку основных айтемов
     * url: /ra/{ra_id}/item/{item_id}/addvariant
     * 
     * @version 20121018, zharkov
     */
    public function addvariant()
    {
        $ra_id      = Request::GetInteger('ra_id', $_REQUEST);
        $ra_item_id = Request::GetInteger('item_id', $_REQUEST);

        if (empty($ra_id)) _404();
        
        $modelRA    = new RA();
        $ra         = $modelRA->GetById($ra_id);
        if (empty($ra)) _404();
        
        $ra                 = $ra['ra'];
        $primary_item       = array();
        foreach ($modelRA->GetItems($ra_id) as $item)
        {
            if ($item['id'] == $ra_item_id)     $primary_item = $item;
        }
        
        // не найден айтем в этом RA
        if (empty($primary_item))
        {
            $this->_message('Item not found', MESSAGE_ERROR);
            $this->_redirect(array('ra', $ra_id, 'edit'));
        }

        if (isset($_REQUEST['btn_save']))
        {
            $selected_ids = isset($_REQUEST['selected_ids']) ? $_REQUEST['selected_ids'] : array();
            
            if (empty($selected_ids))
            {
                $this->_message('Items must be specified!', MESSAGE_ERROR);
            }
            else
            {
                $modelRA->ItemsAdd($ra_item_id, $ra_id, implode(',',$selected_ids));
                
                $this->_message('Variants were successfully added', MESSAGE_OKAY);
                $this->_redirect(array('ra', $ra_id, 'edit'));
            }
        }

        // формирует список доступных айтемов
        $data_set = $modelRA->GetAvailableVariants($ra, $primary_item);
        
        $this->_assign('items', $data_set['list']);
        $this->_assign('ra', $ra);
        
        $this->page_name                    = 'Item Variants';
        $this->breadcrumb[$ra['doc_no']]    = '/ra/' . $ra_id;
        $this->breadcrumb['Edit']           = '/ra/' . $ra_id . '/edit';
        $this->breadcrumb[$this->page_name] = '';
        
        $this->js       = 'ra_addvariant';
        $this->context  = true;
        
        $this->_display('addvariant');
    }
    
    /**
     * Удалеет RA и связанные документы [CMR/DDT] (помечает как удален)
     * url: /ra/{ra_id}/delete
     * 
     * @version 20121207, d10n
     */
    public function delete()
    {
        $ra_id = Request::GetInteger('id', $_REQUEST);        
        if ($ra_id <= 0) _404();
        
        $modelRA    = new RA();
        $ra         = $modelRA->GetById($ra_id);        
        if (!isset($ra['ra']))
        {
            $this->_message('Unknown RA !', MESSAGE_OKAY);
            $this->_redirect(array('ra'));
        }        
                
        $ra = $ra['ra'];
        if ($ra['status_id'] != RA_STATUS_OPEN)
        {
            $this->_message('I can not delete this RA !', MESSAGE_OKAY);
            $this->_redirect(array('ra'));
        }
        
        $modelRA->Remove($ra['id']);
        
        $this->_message('RA was deleted successfully !', MESSAGE_OKAY);
        $this->_redirect(array('ra'));
    }
}