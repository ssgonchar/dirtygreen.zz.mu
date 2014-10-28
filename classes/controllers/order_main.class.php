<?php
require_once APP_PATH . 'classes/components/object.class.php';
require_once APP_PATH . 'classes/core/Pagination.class.php';
require_once APP_PATH . 'classes/mailers/stockmailer.class.php';
require_once APP_PATH . 'classes/models/attachment.class.php';
require_once APP_PATH . 'classes/models/biz.class.php';
require_once APP_PATH . 'classes/models/company.class.php';
require_once APP_PATH . 'classes/models/invoicingtype.class.php';
require_once APP_PATH . 'classes/models/order.class.php';
require_once APP_PATH . 'classes/models/paymenttype.class.php';
require_once APP_PATH . 'classes/models/preorder.class.php';
require_once APP_PATH . 'classes/models/qc.class.php';
require_once APP_PATH . 'classes/models/sc.class.php';
require_once APP_PATH . 'classes/models/steelgrade.class.php';
require_once APP_PATH . 'classes/models/steelposition.class.php';
require_once APP_PATH . 'classes/models/stock.class.php';
require_once APP_PATH . 'classes/models/nomenclature.class.php';
require_once APP_PATH . 'classes/models/nomenclature_category.class.php';
class MainController extends ApplicationController
{
    function MainController()
    {
        ApplicationController::ApplicationController();
        
        $this->authorize_before_exec['index']           = ROLE_STAFF;
        $this->authorize_before_exec['edit']            = ROLE_STAFF;
        $this->authorize_before_exec['neworder']        = ROLE_STAFF;
        $this->authorize_before_exec['selectitems']     = ROLE_STAFF;
        $this->authorize_before_exec['view']            = ROLE_STAFF;
        $this->authorize_before_exec['unregistered']    = ROLE_STAFF;        
        
        
        $this->breadcrumb   = array('Orders' => '/orders');
        $this->context      = true;                
    }

    /**
     * Отображает страницу со списком не
     * 
     */
    function unregistered()
    {
        $this->page_name = 'My Unregistered Orders';
        $this->breadcrumb[$this->page_name] = '';
        
        $preorders = new PreOrder();
        $this->_assign('list', $preorders->GetList());
        
        $this->_display('unregistered');
    }
    
    /**
     * Отображает страницу редактирования предзаказа
     * url: /order/neworder
     * url: /order/neworder/{guid}
     */
    function neworder()
    {
        $guid = Request::GetString('guid', $_REQUEST);        
        if (empty($guid)) $this->_redirect(array('order', 'neworder', md5(date('Y-m-d H:i:s') . $this->user_id)));        

        if (isset($_REQUEST['btn_cancel']))
        {
            $preorders  = new PreOrder();
            $preorder   = $preorders->GetByGuid($guid);
            
            if (!empty($preorder)) $preorders->Remove($guid);
                
            $this->_redirect(array('orders'));
        }        
        else if (isset($_REQUEST['btn_add_from_stock']) || isset($_REQUEST['btn_save']))
        {
            $form               = $_REQUEST['form'];
            $positions          = isset($_REQUEST['position']) ? $_REQUEST['position'] : array();

            $order_for          = Request::GetString('order_for', $form);
            $biz_id             = Request::GetString('biz_id', $form);
            $company_id         = Request::GetInteger('company_id', $form);
            $person_id          = Request::GetInteger('person_id', $form);
            $buyer_ref          = Request::GetString('buyer_ref', $form);
            $supplier_ref       = Request::GetString('supplier_ref', $form);
            $delivery_point     = Request::GetString('delivery_point', $form);
            $delivery_town      = Request::GetString('delivery_town', $form);            
            $delivery_date      = Request::GetString('delivery_date', $form);
            //$delivery_date      = Request::GetDateForDB('delivery_date', $form);
            $alert_date         = Request::GetDateForDB('alert_date', $form);
            $delivery_cost      = Request::GetString('delivery_cost', $form);
            $invoicingtype_id   = Request::GetInteger('invoicingtype_id', $form);
            $invoicingtype_new  = Request::GetString('invoicingtype_new', $form);
            $paymenttype_id     = Request::GetInteger('paymenttype_id', $form);
            $paymenttype_new    = Request::GetString('paymenttype_new', $form);
            $status             = Request::GetString('status', $form);
            $description        = Request::GetString('description', $form);
            
            $dimension_unit     = Request::GetString('dimension_unit', $form);
            $weight_unit        = Request::GetString('weight_unit', $form);
            $price_unit         = Request::GetString('price_unit', $form);
            $currency           = Request::GetString('currency', $form);
            
            if (empty($invoicingtype_id))
            {
                $invoicingtypes     = new InvoicingType();
                $invoicingtype_id   = $invoicingtypes->GetInvoicingTypeId($invoicingtype_new);
            }

            if (empty($paymenttype_id))
            {
                $paymenttypes     = new PaymentType();
                $paymenttype_id   = $paymenttypes->GetPaymentTypeId($paymenttype_new);
            }
            
            if (isset($_REQUEST['btn_add_from_stock']))
            {
                $modelPreOrder = new PreOrder();
                $modelPreOrder->Save($guid, $order_for, $biz_id, $company_id, $person_id, $buyer_ref, $supplier_ref,
                                        $delivery_point, $delivery_town, $delivery_cost, $delivery_date, $alert_date, 
                                        $invoicingtype_id, $paymenttype_id, $status, $description);
                                    
                foreach ($positions as $row)
                {
                    $position_id    = Request::GetInteger('position_id', $row);                    
                    $is_deleted     = Request::GetInteger('is_deleted', $row);
                    $qtty           = Request::GetInteger('qtty', $row);
                    $steelgrade_id  = Request::GetInteger('steelgrade_id', $row);
                    $thickness      = Request::GetNumeric('thickness', $row);
                    $width          = Request::GetNumeric('width', $row);
                    $length         = Request::GetNumeric('length', $row);
                    $unitweight     = Request::GetNumeric('unitweight', $row);
                    $weight         = Request::GetNumeric('weight', $row);
                    $price          = Request::GetNumeric('price', $row);
                    $value          = Request::GetNumeric('value', $row);
                    $deliverytime   = Request::GetString('deliverytime', $row);
                    $internal_notes = Request::GetString('internal_notes', $row);

                    if ($is_deleted > 0 || empty($qtty))
                    {
                        $modelPreOrder->RemovePosition($guid, $position_id);
                    }
                    else
                    {
                        $modelPreOrder->SavePosition($guid, $position_id, $biz_id, $dimension_unit, $weight_unit, $price_unit, $currency, 
                            $steelgrade_id, $thickness, $width, $length, $unitweight, $qtty, $weight, $price, $value, $deliverytime,
                            $internal_notes, $status);
                    }
                }
                
                $redirect_url = 'target/neworder:' . $guid . '/positions/filter/stock:' . ($order_for == 'pa' ? 2 : 1);
                $this->_redirect(explode('/', $redirect_url), false);
            }
            else
            {
                if (empty($order_for))
                {
                    $this->_message('Order for must be specified !', MESSAGE_ERROR);
                }
                else if (empty($biz_id))
                {
                    $this->_message('Biz must be specified !', MESSAGE_ERROR);
                }
                else if (empty($company_id))
                {
                    $this->_message('Company must be specified !', MESSAGE_ERROR);
                }
                else if (empty($delivery_point))
                {
                    $this->_message('Delivery basis must be specified !', MESSAGE_ERROR);
                }
                else if (!in_array($delivery_point, array('col', 'exw', 'fca')) && empty($delivery_town))
                {
                    $this->_message('Destination must be specified !', MESSAGE_ERROR);
                }
                else if (empty($delivery_date))
                {
                    $this->_message('Delivery date must be specified !', MESSAGE_ERROR);
                }
                else if (empty($invoicingtype_id) && empty($invoicingtype_new))
                {
                    $this->_message('Invoicing basis must be specified !', MESSAGE_ERROR);
                }
                else if (empty($paymenttype_id) && empty($paymenttype_new))
                {
                    $this->_message('Payment type must be specified !', MESSAGE_ERROR);
                }
                else if (empty($positions))
                {
                    $this->_message('Positions must be specified !', MESSAGE_ERROR);
                }
                else
                {
                    $orders         = new Order();
                    $steelpositions = new SteelPosition();
                    
                    // проверка допустимого количества позиций
                    $position_error = false;                    
                    foreach ($positions as $key => $position)
                    {
                        if ($position['position_id'] > 0)
                        {
                            $steelposition = $steelpositions->GetById($position['position_id']);
                            $steelposition = $steelposition['steelposition'];
                            
                            if (empty($position['is_deleted']) && $steelposition['qtty'] < $position['qtty'])
                            {
                                $position_error = true;
                                $positions[$key]['qtty_error']      = true;
                                $positions[$key]['qtty_available']  = $steelposition['qtty'];
                            }
                        }
                    }
                    
                    if ($position_error)
                    {
                        $this->_message('Incorrect position qtty was specified !', MESSAGE_ERROR);
                    }
                    else
                    {
                        // сохраняет заказ
                        $order = $orders->Save(0, $order_for, $biz_id, $company_id, $person_id, $buyer_ref, $supplier_ref, $delivery_point, $delivery_town,
                                        $delivery_cost, $delivery_date, $alert_date, $invoicingtype_id, $paymenttype_id, $status, $description);
                        
                        
                        // добавляет к заказу айтемы
                        $preorders          = new PreOrder();
                        $updated_positions  = $preorders->ItemsMoveToOrder($guid, $order['id']);
                        
                        
                        // сохраняет позиции заказа
                        foreach ($positions as $row)
                        {
                            $position_id    = Request::GetInteger('position_id', $row);
                            
                            $is_deleted     = Request::GetInteger('is_deleted', $row);
                            $qtty           = Request::GetInteger('qtty', $row);
                            $steelgrade_id  = Request::GetInteger('steelgrade_id', $row);
                            $thickness      = Request::GetNumeric('thickness', $row);
                            $width          = Request::GetNumeric('width', $row);
                            $length         = Request::GetNumeric('length', $row);
                            $unitweight     = Request::GetNumeric('unitweight', $row);
                            $weight         = Request::GetNumeric('weight', $row);
                            $price          = Request::GetNumeric('price', $row);
                            $value          = Request::GetNumeric('value', $row);
                            $deliverytime   = Request::GetString('deliverytime', $row);
                            $internal_notes = Request::GetString('internal_notes', $row);

                            if (empty($is_deleted) && !empty($qtty))
                            {
                                $result = $orders->SavePosition($order['id'], $position_id, $biz_id, $dimension_unit, $weight_unit, $price_unit, $currency, 
                                            $steelgrade_id, $thickness, $width, $length, $unitweight, $qtty, $weight, $price, $value, 
                                            $deliverytime, $internal_notes, $status);
                                            
                                // если позиция не обновилась автоматически, добавляется в список обновляемых позиций
                                if (!$result && !in_array($position_id, $updated_positions)) $updated_positions[] = $position_id;
                            }
                        }
                        
                        // обновляет количество позиций на складе
                        $steelpositions = new SteelPosition();
                        foreach ($updated_positions as $position_id)
                        {
                            $steelpositions->UpdateQtty($position_id);    
                        }
                        
                        // удаляет временный заказ
                        $preorders->Remove($guid);
                        
                        // обновляет статус заказа на In Processing
                        $orders->UpdateStatus($order['id'], 'ip');
                        
                        $this->_message('Order was saved successfully', MESSAGE_OKAY);
                        $this->_redirect(array('order', $order['id']));
                    }                
                }                
            }
            
            $modelSteelPosition = new SteelPosition();
            $positions          = $modelSteelPosition->FillSteelPositionInfo($positions, true, 'position_id');
        }
        else
        {
            $preorders  = new PreOrder();
            $form       = $preorders->GetByGuid($guid);
            $positions  = $preorders->GetPositions($guid);
        }
        
        $this->page_name = 'New Order';
        $this->breadcrumb['Unregistered']   = '/orders/unregistered';
        $this->breadcrumb[$this->page_name] = '';
        
        $invoicingtypes = new InvoicingType();
        $this->_assign('invoicingtypes', $invoicingtypes->GetList());

        $paymenttypes = new PaymentType();
        $this->_assign('paymenttypes', $paymenttypes->GetList());

        $steelgrades = new SteelGrade();
        $this->_assign('steelgrades', $steelgrades->GetList());
        
        $companies = new Company();
        $this->_assign('mam_companies', $companies->GetMaMList());
        
        $this->_assign('include_ui',    true);
        $this->_assign('form',          $form);
        $this->_assign('positions',     $positions);
        
        $total_qtty     = 0;
        $total_weight   = 0;
        $total_value    = 0;
        $price_units    = array();
        
        foreach ($positions as $position)
        {
            $total_qtty   += $position['qtty'];
            $total_weight += $position['weight'];
            $total_value  += $position['value'];
            
            $price_unit                 = $position['steelposition']['price_unit'];
            $price_units[$price_unit]   = $price_unit;
        }

        $this->_assign('total_qtty',    $total_qtty);
        $this->_assign('total_weight',  $total_weight);
        $this->_assign('total_value',   $total_value);

        if (count($price_units) == 1)
        {
            $price_units = array_keys($price_units);
            $this->_assign('price_unit', $price_units[0]);
        }
        
        $preorders  = new PreOrder();
        $preorder   = $preorders->GetByGuid($guid);
        $this->_assign('order', $preorder);
        
        // если заказ со склада, то фирма не меняется, под нее выбираются бизнесы
        if (isset($preorder['type']) && $preorder['type'] == 'so')
        {
            $bizes = new Biz();
            $this->_assign('bizes', $bizes->GetListByCompany($preorder['company_id'], 'buyer'));
        }
        
        if (isset($form['biz_id']) && $form['biz_id'] > 0)
        {
            $bizes = new Biz();
            $this->_assign('companies', $bizes->GetCompanies($form['biz_id'], 'buyer'));
        }
        
        if (isset($form['company_id']) && $form['company_id'] > 0)
        {
            $companies  = new Company();
            $persons    = $companies->GetPersons($form['company_id']);
            
            $this->_assign('persons', $persons['data']);
        }
        
        $this->_assign('show_cancel_button', true);
        
        $this->js = 'order_edit';
        $this->_display('edit');
    }
    
    /**
     * Отображает страницу выбора айтемов для заказа
     * url: /order/selectitems/{order_id}/position:{position_id}
     */
    function selectitems()
    {
        $order_id       = Request::GetInteger('order_id', $_REQUEST);
        $position_id    = Request::GetInteger('position_id', $_REQUEST);        
        if (empty($order_id) || empty($position_id)) _404();

        $orders = new Order();
        $order  = $orders->GetById($order_id);        
        if (empty($order)) _404();

        // для отмененного заказа нет возможности просмотреть айтемы, потому что их нет, они все на складе
        if ($order['order']['status'] == 'ca') 
        {
            $this->_message('This order is cancelled ', MESSAGE_WARNING);
            $this->_redirect(array('order', $order_id));
        }
        
        $position = array();
        foreach ($orders->GetPositions($order_id) as $row)
        {
            if ($row['position_id'] == $position_id)
            {
                $position = $row;
                break;
            }
        }
        if (empty($position)) 
        {
            $this->_message('Position # ' . $position_id . ' is not found in this order ', MESSAGE_WARNING);
            $this->_redirect(array('order', $order_id));            
        }
        
        $steelpositions = new SteelPosition();
        $steelposition  = $steelpositions->GetById($position_id);        
        
        if ($order['order']['status'] == 'co')
        {
            $items = $orders->GetPositionItems($order_id, $position_id);
        }
        else
        {
            $items = $steelpositions->GetItems($position_id, false);
        }

        
        if (empty($items))
        {
            $this->_message('There are no Items for position # ' . $position_id, MESSAGE_WARNING);
            $this->_redirect(array('order', $order_id));            
        }
        
        
        if (isset($_REQUEST['btn_save']) && $order['order']['status'] != 'ca')
        {
            $selected_items = isset($_REQUEST['selected_items']) ? $_REQUEST['selected_items'] : array();
            $item_status    = isset($_REQUEST['item_status']) ? $_REQUEST['item_status'] : array();

            // отвязывает неиспользуемые айтемы от заказа
            foreach ($items as $item)
            {
                $item = $item['steelitem'];
                if ($item['order_id'] == $order_id)
                {
                    if (!in_array($item['id'], $selected_items))
                    {
                        // удаляет привязку айтема к заказу, но оставляет позицию в заказе ($leace_history = 'true'), 
                        // если позиция окажется пустая, она удалится ниже при UpdatePositionQtty
                        $orders->RemoveItem($order_id, $item['id'], true);
                    }
                    else
                    {
/* 20121213, zharkov: статус нельзя установить вручную, теперь все изменения статуса только через документы
                        $item_status_id = Request::GetInteger($item['id'], $item_status);
                        $orders->UpdateItemStatus($order_id, $item['id'], $item_status_id);
*/                                            
                        unset($selected_items[$item['id']]);
                    }                    
                }
            }

            // добавляет новые айтемы
            foreach ($selected_items as $index => $item_id)
            {
                // добавляет айтем к заказу
                $orders->AddItem($order_id, $position_id, $item_id);
/* 20121213, zharkov: статус нельзя установить вручную, теперь все изменения статуса только через документы                
                // сохраняет статус айтема
                $item_status_id = Request::GetInteger($item_id, $item_status);
                $orders->UpdateItemStatus($order_id, $item_id, $item_status_id);                
*/                
            }
            
            // обновляет количество позиции в заказе
            $orders->UpdatePositionQtty($order_id, $position_id);
            
            // обновляет количество для позиции на складе
            $steelposition = new SteelPosition();
            $steelposition->UpdateQtty($position_id);
            
            $this->_redirect(array('order', $order_id));
        }
        
        
        $this->_assign('order',     $order['order']);
        $this->_assign('position',  $position);
                
        $this->_assign('steelposition', $steelposition['steelposition']);
        $this->_assign('items',         $items);

        $this->page_name = 'Ordered Items';
        $this->breadcrumb[$order['order']['doc_no']] = '/order/' . $order_id;
        $this->breadcrumb[$this->page_name] = '';
        
        $this->js = 'order_selectitems';
        
        $this->_display('selectitems');
    }
    
    /**
     * Отображает страницу просмотра заказа
     * url: /order/{order_id}
     */
    function view()
    {
        $order_id = Request::GetInteger('id', $_REQUEST);        
        if (empty($order_id)) _404();
        
        $orders = new Order();
        $order  = $orders->GetById($order_id);
        if (empty($order) || (empty($order['order']['status']) && $order['order']['created_by'] != $this->user_id)) _404();
        
        // для неподтвержденных заказов созданных со склада, переводим на страницу редактирования
        if ($order['order']['status'] == 'nw') $this->_redirect(array('order', $order_id, 'edit'));
        

        $positions = $orders->GetPositions($order_id);

        $total_qtty         = 0;
        $total_weight       = 0;
        $total_value        = 0;
        $conflicted_items   = array();
        $price_units        = array();
        
        foreach ($positions as $key => $position)
        {
            $total_qtty     += $position['qtty'];
            $total_weight   += $position['weight'];
            $total_value    += $position['value'];
/*
    $modelSteelPosition = new SteelPosition();
            
    $steelitems=$modelSteelPosition->GetItems($position_id, true);
    
    $modelSteelItem = new SteelItem();
    for($i=0; $i<count($steelitems); $i++)
    {
       $docs = $modelSteelItem->GetRelatedDocs($steelitems[$i]['steelitem_id']);
       $steelitems[$i]['doc']=$docs;
    }            
*/
            
            $price_unit                 = $position['steelposition']['price_unit'];
            $price_units[$price_unit]   = $price_unit;
            
            // поиск конфликтных айтемов
            if (isset($position['steelitems']))
            {
                $modelSteelItem = new SteelItem();
                $i=0;
                foreach ($position['steelitems'] as $item_main)
                {
                    
                    $item = $item_main['steelitem'];
                    if ($item['order_id'] == $order_id && !empty($item['is_conflicted']))
                    {
                        $conflicted_items[] = $item;
                        $positions[$key]['is_conflicted'] = true;
                    }
               /*     
                    $docs_item = $modelSteelItem->GetRelatedDocs($item['id']);
                    $docs[$i]['documents']['plate_id'] = $item['guid'];
                    $docs[$i]['documents'] = array_push($docs[$i], $docs_item);
                    $i++;
                    //print_r($position['steelitems'][$key]['steelitem_id']);
                    //$position['steelitems'][$key]['document']=$docs;
                */
                }

            }
        }

        $this->_assign('order',             $order['order']);
        //$this->_assign('document',             $docs);
        $this->_assign('positions',         $positions);
                
        $this->_assign('conflicted_items',  $conflicted_items);
        $this->_assign('total_qtty',        $total_qtty);
        $this->_assign('total_weight',      $total_weight);
        $this->_assign('total_value',       $total_value);

        if (count($price_units) == 1)
        {
            $price_units = array_keys($price_units);
            $this->_assign('price_unit', $price_units[0]);
        }
        
        $this->js = 'order_view';
        
        $related_docs_list = $orders->GetListOfRelatedDocs($order_id);
        $this->_assign('related_docs_list', $related_docs_list);
//        $sc         = new SC();
//        $sc_list    = $sc->GetListByOrder($order_id);
//        $this->_assign('sc_list', $sc_list);
//        
//        $qc         = new QC();
//        $qc_list    = $qc->GetListByOrder($order_id);
//        $this->_assign('qc_list', $qc_list);

        $objectcomponent    = new ObjectComponent();
        $page_params        = $objectcomponent->GetPageParams('order', $order_id);
        
        $this->page_name    = $page_params['page_name'];
        $this->breadcrumb   = $page_params['breadcrumb'];
        
        $this->_assign('object_stat', $page_params['stat']);
        
        $modelAttachment    = new Attachment();
        $attachments_list   = $modelAttachment->GetListByType('', 'order', $order_id);
        $this->_assign('attachments_list', $attachments_list['data']);
        
        $page_alias       = Request::GetString('page_alias', $_REQUEST);
        //debug('1682', $page_alias);
        //$modelNomenclatureCategory = new NomenclatureCategory();
        //$help_text = $modelNomenclatureCategory->Search($page_alias);
        //$this->_assign('help', $help_text);
        $this->_display('view');        
    }
    
    /**
     * Отображает страницу списка заказов
     * url: /stocks
     */
    function index()
    {
		//print_r("I'm here");
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
                $this->_redirect(array('orders'));
            }
            else
            {
                $this->_redirect(array('orders', 'filter', str_replace(' ', '+', $filter)), false);
            }
        }
        else if (isset($_REQUEST['btn_create_ra']) && isset($_REQUEST['selected_ids']))
        {
            $seleted_ids = $_REQUEST['selected_ids'];
            $this->_redirect(array('ra', 'add', implode(',', $seleted_ids)));
        }
        
        $filter         = Request::GetString('filter', $_REQUEST);
        $filter         = urldecode($filter);
        $filter_params  = array();
        
        if (empty($filter))
        {
            $this->page_name = 'Orders';
            $this->breadcrumb[$this->page_name] = '/orders';
        }
        else
        {
            $this->page_name = 'Filtered Orders';
            
            $this->breadcrumb['Orders']         = '/orders';
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
                                    $steelgrade_id, $thickness, $width, $keyword, $type, $this->page_no);
		//dg($rowset);
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
			//dg($rowset['data']);
            $this->_assign('total_qtty',    $total_qtty);
            $this->_assign('total_weight',  $total_weight);
            $this->_assign('total_value',   $total_value);
        }
		foreach ($rowset['data'] as &$order)
		{
			//print_r(); 
			//$order['orderid']=$order['order_id'];
			$order['order']['all_items']=$orders->GetItems($order['order_id']);
			foreach($order['order']['all_items'] as &$item)
			{
				$modelSteelGrade = new SteelGrade();
				$steelgrade = $modelSteelGrade->GetById($item['steelgrade_id']);
				$steelgrade_title = $steelgrade['steelgrade']['title'];
				$steelgrade_color = $steelgrade['steelgrade']['bgcolor'];
				$item['steelgrade_title']=$steelgrade_title;
				$item['steelgrade_color']=$steelgrade_color;
				//print_r($steelgrade_color);
			}
		}
		//dg($rowset['data']);
        if ($biz_id > 0)
        {
            $bizes  = new Biz();
            $biz    = $bizes->GetById($biz_id);
            
            if (!empty($biz))
            {
                $this->_assign('biz_id',    $biz_id);
                $this->_assign('biz_title', $biz['biz']['doc_no_full']);
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
        //dg($rowset);
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
                        
        $this->_assign('count',         $rowset['count']);
        $this->_assign('list',          $rowset['data']);
        $this->_assign('filter',        true);
        
        $has_in_processing = false;
		
		/*
        foreach ($rowset['data'] as $order)
        {
			
			print_r($order);
            if ($order['order']['status'] != 'co' && $order['order']['status'] != 'ca')
            {
                $has_in_processing = true;
                break;
            }
			
        }
		*/
        
        if ($has_in_processing) $this->_assign('has_in_processing', true);
        
        $pager = new Pagination();
        $this->_assign('pager_pages', $pager->PreparePages($this->page_no, $rowset['count']));
        
        $steelgrades = $orders->GetSteelgrades();
        
        //$steelgrades_sorted = array_multisort($steelgrades[]["steelgrade"]["title"], SORT_ASC, SORT_STRING);
        $companies = new Company();
        $this->_assign('companies',     $companies->GetMaMList());
        $this->_assign('steelgrades',   $steelgrades);
        //$this->_assign('steelgrades',   $orders->GetSteelgrades());
        $this->_assign('include_ui',    true);

        $this->js = 'order_index';
        
        //получаем справку для текущей страницы (параметр page_alias)
        $page_alias       = Request::GetString('page_alias', $_REQUEST);
        $modelNomenclatureCategory = new NomenclatureCategory();
        //$help_text = $modelNomenclatureCategory->Search($page_alias);
        //$this->_assign('help', $help_text);	
		//$this->_assign('list',          $rowset['data']);
       
        $this->_display('index');
        
            }
        
    /**
     * Отображает страницу редактирования заказа
     * url: /order/{id}/edit
     */
    function edit()
    {
        $order_id = Request::GetInteger('id', $_REQUEST);        
        if (empty($order_id)) _404();
        
        $orders = new Order();
        $order  = $orders->GetById($order_id);
        if (empty($order) || isset($order['ErrorCode'])) _404();
        $order = $order['order'];

        //debug('1682', $order);
        // запрещено редактировать completed или cancelled
        if (in_array($order['status'], array('ca', 'co'))) $this->_redirect(array('order', $order_id));
                
        $balance = $orders->GetBalanceToDeliver($order_id);

        if (isset($_REQUEST['btn_add_from_stock']))
        {
            $form       = $_REQUEST['form'];
            $position   = isset($_REQUEST['position']) ? $_REQUEST['position'] : array();
            
            // добавляет заказ в сессию
            $_SESSION['order-' . $order_id]['form']         = $form;
            $_SESSION['order-' . $order_id]['positions']    = array();

            foreach ($position as $row) {
                $_SESSION['order-' . $order_id]['positions'][] = $row;
            }


            // переход к складу
            $order_for      = Request::GetString('order_for', $form);
            $redirect_url   = 'target/order:' . $order_id . '/positions/filter/stock:' . ($order_for == 'pa' ? 2 : 1);

            $this->_redirect(explode('/', $redirect_url), false);
        }
        else if (isset($_REQUEST['btn_cancel']))
        {
            $form       = isset($_REQUEST['form']) ? $_REQUEST['form'] : array();
            $positions  = isset($_REQUEST['position']) ? $_REQUEST['position'] : array();            
            
            // отменить можно только новый заказ со склада или заказ по которому еще не было поставок
            if ($this->user_role <= ROLE_MODERATOR && ($order['status'] == 'nw' || ($order['status'] == 'ip' && !$balance['delivered'])))
            {
                $orders->CancelOrder($order_id);
                
                if ($order['type'] == 'so')
                {
                    // clear reserved positions
                    $modelSteelPosition = new SteelPosition();
                    $modelSteelPosition->ReserveRemoveByOrder($order['id']);
                    
                    $stocks = new Stock();
                    $stock  = $stocks->GetById($order['stock_id']);

                    $stcokmaiiler = new StockMailer();
                    $stcokmaiiler->SendOrderCancelNotice($stock['stock'], $order);
                }
                
                $this->_redirect(array('order', $order_id));
            }
            else
            {
                $msg = '<br/><br/><p>Your role in the system is the <b>MaM Staff</b>.</p><p>Only users who have the role of a <b>MaM Admin</b> or <b>MaM Moderator</b> may cancel the order<p>';
                $this->_message('Error access permissions!'.$msg, MESSAGE_ERROR);      
            }
        }
        else if (isset($_REQUEST['btn_save']))
        {
            $form       = isset($_REQUEST['form']) ? $_REQUEST['form'] : array();
            $positions  = isset($_REQUEST['position']) ? $_REQUEST['position'] : array();
            
            $order_for          = Request::GetString('order_for', $form);
            $biz_id             = Request::GetString('biz_id', $form);
            $company_id         = Request::GetInteger('company_id', $form);
            $person_id          = Request::GetInteger('person_id', $form);
            $buyer_ref          = Request::GetString('buyer_ref', $form);
            $supplier_ref       = Request::GetString('supplier_ref', $form);
            $delivery_point     = Request::GetString('delivery_point', $form);
            $delivery_town      = Request::GetString('delivery_town', $form);            
            $delivery_date      = Request::GetString('delivery_date', $form);
            $alert_date         = Request::GetDateForDB('alert_date', $form);
            $delivery_cost      = Request::GetString('delivery_cost', $form);
            $invoicingtype_id   = Request::GetInteger('invoicingtype_id', $form);
            $invoicingtype_new  = Request::GetString('invoicingtype_new', $form);
            $paymenttype_id     = Request::GetInteger('paymenttype_id', $form);
            $paymenttype_new    = Request::GetString('paymenttype_new', $form);
            $status             = Request::GetString('status', $form, 'ip');    // если статус не указан, ставим "in processing"
            $description        = Request::GetString('description', $form);
            
            $dimension_unit     = Request::GetString('dimension_unit', $form);
            $weight_unit        = Request::GetString('weight_unit', $form);
            $price_unit         = Request::GetString('price_unit', $form);
            $currency           = Request::GetString('currency', $form);
            $total_price        = Request::GetNumeric('price_equivalent', $form);
            /*            
            if (empty($total_price)) {
                $this->_message('Price equivalent must be specified !', MESSAGE_ERROR);
            }*/
            if (empty($order_for))
            {
                $this->_message('Order for must be specified !', MESSAGE_ERROR);
            }
            else if (empty($biz_id))
            {
                $this->_message('Biz must be specified !', MESSAGE_ERROR);
            }
            else if (empty($company_id))
            {
                $this->_message('Company must be specified !', MESSAGE_ERROR);
            }
            else if (empty($delivery_point))
            {
                $this->_message('Delivery basis must be specified !', MESSAGE_ERROR);
            }
            else if (!in_array($delivery_point, array('col', 'exw', 'fca')) && empty($delivery_town))
            {
                $this->_message('Destination must be specified !', MESSAGE_ERROR);
            }
            else if (empty($delivery_date))
            {
                $this->_message('Delivery date must be specified !', MESSAGE_ERROR);
            }
            else if (empty($invoicingtype_id) && empty($invoicingtype_new))
            {
                $this->_message('Invoicing basis must be specified !', MESSAGE_ERROR);
            }
            else if (empty($paymenttype_id) && empty($paymenttype_new))
            {
                $this->_message('Payment type must be specified !', MESSAGE_ERROR);
            }
            else if ($order['status'] != 'nw' && $status == 'nw')  // нельзя вернуться к статусу New
            {
                $this->_message('Error changing status !', MESSAGE_ERROR);
            }
            else if (empty($positions))
            {
                $this->_message('Positions must be specified !', MESSAGE_ERROR);
            }
            else
            {
                // проверка допустимого количества позиций
                $position_error = false;
                
                // если заказ отменяется, то проверка не нужна
                foreach ($positions as $key => $position)
                {
                    if ($position['position_id'] > 0)
                    {
                        if (empty($position['is_deleted']))
                        {
                            $result = $orders->TestPositionQtty($order_id, $position['position_id'], $position['qtty'], $status);
                            if (!$result['available'])
                            {
                                $position_error = true;
                                $positions[$key]['qtty_error']      = true;
                                $positions[$key]['qtty_available']  = $result['qtty'];
                            }                            
                        }
                    }
                }                    
                
                if ($position_error)
                {
                    $this->_message('Incorrect position qtty was specified !', MESSAGE_ERROR);
                }
                else
                {
                    
                    $check_items = false; // флаг, если установлен, значит нужно проверить конфликт айтемов и обновить количество позиций

                    // сохраняет позиции заказа
                    foreach ($positions as $row)
                    {
                        $position_id = Request::GetInteger('position_id', $row);
                        
                        $is_deleted     = Request::GetInteger('is_deleted', $row);
                        $qtty           = Request::GetInteger('qtty', $row);
                        $steelgrade_id  = Request::GetInteger('steelgrade_id', $row);
                        $thickness      = Request::GetNumeric('thickness', $row);
                        $width          = Request::GetNumeric('width', $row);
                        $length         = Request::GetNumeric('length', $row);
                        $unitweight     = Request::GetNumeric('unitweight', $row);
                        $weight         = Request::GetNumeric('weight', $row);
                        $price          = Request::GetNumeric('price', $row);
                        $value          = Request::GetNumeric('value', $row);
                        $deliverytime   = Request::GetString('deliverytime', $row);
                        $internal_notes = Request::GetString('internal_notes', $row);

                        if ($is_deleted > 0 || empty($qtty))
                        {
                            $orders->RemovePosition($order_id, $position_id);                        
                        }
                        else
                        {
                            $orders->SavePosition($order_id, $position_id, $biz_id, $dimension_unit, $weight_unit, $price_unit, $currency, 
                                                                $steelgrade_id, $thickness, $width, $length, $unitweight, $qtty, 
                                                                $weight, $price, $value, $deliverytime, $internal_notes, $status);
                        }
                    }

                                        
                    if (empty($invoicingtype_id))
                    {
                        $invoicingtypes     = new InvoicingType();
                        $invoicingtype_id   = $invoicingtypes->GetInvoicingTypeId($invoicingtype_new);
                    }

                    if (empty($paymenttype_id))
                    {
                        $paymenttypes     = new PaymentType();
                        $paymenttype_id   = $paymenttypes->GetPaymentTypeId($paymenttype_new);
                    }
                    
                    // сохраняет заказ
                    $orders->Save($order_id, $order_for, $biz_id, $company_id, $person_id, $buyer_ref, $supplier_ref, $delivery_point, $delivery_town,
                                    $delivery_cost, $delivery_date, $alert_date, $invoicingtype_id, $paymenttype_id, $status, $description, $total_price);
                    
                    // при изменении заказа, сделанного через Web Stock, отправляется уведомление заказчику
                    if ($order['type'] == 'so')
                    {
                        if ($order['status'] == 'nw')
                        {
                            // clear reserved positions
                            $modelSteelPosition = new SteelPosition();
                            $modelSteelPosition->ReserveRemoveByOrder($order['id']);
                        }
                        
                        // заказ изменен
                                                
                        // заказ доставлен
                        
                        // заказ выполнен
                    }
                    
                    unset($_SESSION['order-' . $order_id]);                    

                    $this->_message('Order was saved successfully', MESSAGE_OKAY);
                    $this->_redirect(array('order', $order_id));
                }                
            }
            
            $modelSteelPosition = new SteelPosition();
            $positions          = $modelSteelPosition->FillSteelPositionInfo($positions, true, 'position_id');
        }
        else
        {
            $form       = $order;
            $positions  = $orders->GetPositions($order_id);

            if (isset($_SESSION['order-' . $order_id]))
            {
                // form
                foreach ($_SESSION['order-' . $order_id]['form'] as $key => $value) $form[$key] = $value;
                
                // positions
                $session_positions  = $_SESSION['order-' . $order_id]['positions'];
                foreach ($positions as $i => $pos)
                {
                    foreach ($session_positions as $j => $spos)
                    {
                        if ($pos['position_id'] == $spos['position_id'])
                        {
                            // обновляет позицию данными из сессии
                            foreach ($spos as $key => $value) $positions[$i][$key] = $value;
                            
                            // исключаем обработанную позицию
                            unset($session_positions[$j]);
                            break;
                        }
                    }                
                }
                
                // добавляет позиции, которых нет в списке
                foreach ($session_positions as $row) $positions[] = $row;
            }            
        }
        
        $this->page_name = 'Edit Order';
        
        $this->breadcrumb[$order['doc_no']] = '/order/' . $order_id;
        $this->breadcrumb[$this->page_name] = '';
        
        $invoicingtypes = new InvoicingType();
        $this->_assign('invoicingtypes', $invoicingtypes->GetList());

        $paymenttypes = new PaymentType();
        $this->_assign('paymenttypes', $paymenttypes->GetList());

        $steelgrades = new SteelGrade();
        $this->_assign('steelgrades', $steelgrades->GetList());
        
        $companies = new Company();
        $this->_assign('mam_companies', $companies->GetMaMList());
        
        $this->_assign('include_ui',    true);
        $this->_assign('form',          $form);
        $this->_assign('positions',     $positions);
        
        $total_qtty     = 0;
        $total_weight   = 0;
        $total_value    = 0;
        $price_units    = array();
        
        foreach ($positions as $position)
        {
            $total_qtty   += $position['qtty'];
            $total_weight += $position['weight'];
            $total_value  += $position['value'];
            
            $price_unit                 = $position['steelposition']['price_unit'];
            $price_units[$price_unit]   = $price_unit;                
        }
        
        $balance_to_deliver_qtty = $total_qtty;

        $this->_assign('total_qtty',    $total_qtty);
        $this->_assign('total_weight',  $total_weight);
        $this->_assign('total_value',   $total_value);
        $this->_assign('order',         $order);
        
        if (count($price_units) == 1)
        {
            $price_units = array_keys($price_units);
            $this->_assign('price_unit', $price_units[0]);
        }
        
        // если заказ со склада, то фирма не меняется, под нее выбираются бизнесы
        if ($order['type'] == 'so')
        {
            $bizes = new Biz();
            $this->_assign('bizes', $bizes->GetListByCompany($order['company_id'], 'buyer'));
        }
        
        if ($form['biz_id'] > 0)
        {
            $bizes = new Biz();
            $this->_assign('companies', $bizes->GetCompanies($form['biz_id'], 'buyer'));
        }
        
        if ($form['company_id'] > 0)
        {
            $companies  = new Company();
            $persons    = $companies->GetPersons($form['company_id']);
            $this->_assign('persons', $persons['data']);
        }

        // возможность отмены есть только у новых заказов со склада и у заказов в процессе, по которым не было поставок
        if ($order['status'] == 'nw' || ($order['status'] == 'ip' && $balance_to_deliver_qtty == $total_qtty))
        {
            $this->_assign('show_cancel_button', true);
        }
        
        $this->js = 'order_edit';
        $page_alias       = Request::GetString('page_alias', $_REQUEST);
        //$modelNomenclatureCategory = new NomenclatureCategory();
        //$help_text = $modelNomenclatureCategory->Search($page_alias);
        //$this->_assign('help', $help_text);	
        $this->_display('edit');
    }    
}
