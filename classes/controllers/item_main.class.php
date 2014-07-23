<?php
require_once APP_PATH . 'classes/models/biz.class.php';
require_once APP_PATH . 'classes/models/location.class.php';
require_once APP_PATH . 'classes/models/order.class.php';
require_once APP_PATH . 'classes/models/qc.class.php';
require_once APP_PATH . 'classes/models/steelgrade.class.php';
require_once APP_PATH . 'classes/models/steelitem.class.php';
require_once APP_PATH . 'classes/models/steelposition.class.php';
require_once APP_PATH . 'classes/models/stock.class.php';
require_once APP_PATH . 'classes/models/supplierinvoice.class.php';

class MainController extends ApplicationController
{
    function MainController()
    {
        ApplicationController::ApplicationController();
        
        $this->authorize_before_exec['badlist']         = ROLE_STAFF;
        $this->authorize_before_exec['index']           = ROLE_STAFF;
        $this->authorize_before_exec['edit']            = ROLE_STAFF;
        $this->authorize_before_exec['move']            = ROLE_STAFF;
        $this->authorize_before_exec['twin']            = ROLE_STAFF;
        $this->authorize_before_exec['cut']             = ROLE_MODERATOR;
        $this->authorize_before_exec['history']         = ROLE_STAFF;
        $this->authorize_before_exec['revision']        = ROLE_STAFF;
        $this->authorize_before_exec['conflicted']      = ROLE_STAFF;
        
        $this->authorize_before_exec['history1']        = ROLE_STAFF;
        $this->authorize_before_exec['timeline']        = ROLE_STAFF;
        $this->authorize_before_exec['createalias']     = ROLE_STAFF;
        $this->authorize_before_exec['view']            = ROLE_STAFF;
        
        $this->authorize_before_exec['removefromorder'] = ROLE_ADMIN;
        
        $this->breadcrumb   = array('Stocks' => '/stocks');
    }
	/*
	*
	*/
	function stockaudit()
	{
		$page = Request::GetString('page', $_REQUEST);
		$modelSteelItem = new SteelItem();
		$list = $modelSteelItem->GetListStockAudit();
        $this->_assign('list', $list);
        //dg($list);
        $total_qtty                 = 0;
        $total_weight               = 0;
        $total_value                = 0;
        $total_purchase_value       = 0;

        $item_weight_unit           = '';
        $item_weight_unit_count     = 0;
        $item_dimension_unit        = '';
        $item_dimension_unit_count  = 0;
        $item_currency              = '';
        $item_currency_count        = 0;
        $item_price_unit            = '';
        $item_price_unit_count      = 0;
        
        foreach ($list as $item)
        {
            $item = $item['steelitem'];
            
            $total_qtty     += 1;
            $total_weight   += $item['unitweight'];
            $item_value     = $item['unitweight'] * $item['price'];
            
            if ($item['weight_unit'] = 'lb' && $item['price_unit'] == 'cwt')
            {
                $item_value = $item_value / 100;
            }
            
            $total_value            += $item_value;
            $total_purchase_value   += $item['unitweight'] * $item['purchase_price'];
            
            if ($item_weight_unit != $item['weight_unit'] && !empty($item['weight_unit']))
            {
                $item_weight_unit = $item['weight_unit'];
                $item_weight_unit_count++;
            }
            
            if ($item_dimension_unit != $item['dimension_unit'] && !empty($item['dimension_unit']))
            {
                $item_dimension_unit = $item['dimension_unit'];
                $item_dimension_unit_count++;
            }

            if ($item_price_unit != $item['price_unit'] && !empty($item['price_unit']))
            {
                $item_price_unit = $item['price_unit'];
                $item_price_unit_count++;
            }            
            
            if ($item_currency != $item['currency'] && !empty($item['currency']))
            {
                $item_currency = $item['currency'];
                $item_currency_count++;
            }                
        }
        
        $this->_assign('item_weight_unit',          $item_weight_unit);
        $this->_assign('item_weight_unit_count',    $item_weight_unit_count);
        $this->_assign('item_dimension_unit',       $item_dimension_unit);
        $this->_assign('item_dimension_unit_count', $item_dimension_unit_count);
        $this->_assign('item_currency',             $item_currency);
        $this->_assign('item_currency_count',       $item_currency_count);
        $this->_assign('item_price_unit',           $item_dimension_unit);
        $this->_assign('item_price_unit_count',     $item_dimension_unit_count);
        
        $this->_assign('total_qtty',            $total_qtty);
        $this->_assign('total_weight',          $total_weight);
        $this->_assign('total_value',           $total_value);
        $this->_assign('total_purchase_value',  $total_purchase_value);            
        
        $this->_assign('page', $page);
        

        $this->breadcrumb = array(
           'Items'          => '/items',
           $this->page_name => ''
        );

        $this->js       = 'item_index';
        $this->context  = 'main_index';
        
        $this->_display('index');	
	}
	
    /**
     * Display list of steelitems without meaningfull data
     * 
     */
    function badlist()
    {
        $page = Request::GetString('page', $_REQUEST);        
        if (!in_array($page, array('ownerless', 'stockholderless'))) _404();
        
        $modelSteelItem = new SteelItem();
        if ($page == 'ownerless')
        {
            $this->page_name = 'Items without Owner';
            $list = $modelSteelItem->GetListWithoutOwner();
        }
        else
        {
            $this->page_name = 'Items without Stockholder';
            $list = $modelSteelItem->GetListWithoutStockholder();
        }
        
        $this->_assign('list', $list);
        
        $total_qtty                 = 0;
        $total_weight               = 0;
        $total_value                = 0;
        $total_purchase_value       = 0;

        $item_weight_unit           = '';
        $item_weight_unit_count     = 0;
        $item_dimension_unit        = '';
        $item_dimension_unit_count  = 0;
        $item_currency              = '';
        $item_currency_count        = 0;
        $item_price_unit            = '';
        $item_price_unit_count      = 0;
        
        foreach ($list as $item)
        {
            $item = $item['steelitem'];
            
            $total_qtty     += 1;
            $total_weight   += $item['unitweight'];
            $item_value     = $item['unitweight'] * $item['price'];
            
            if ($item['weight_unit'] = 'lb' && $item['price_unit'] == 'cwt')
            {
                $item_value = $item_value / 100;
            }
            
            $total_value            += $item_value;
            $total_purchase_value   += $item['unitweight'] * $item['purchase_price'];
            
            if ($item_weight_unit != $item['weight_unit'] && !empty($item['weight_unit']))
            {
                $item_weight_unit = $item['weight_unit'];
                $item_weight_unit_count++;
            }
            
            if ($item_dimension_unit != $item['dimension_unit'] && !empty($item['dimension_unit']))
            {
                $item_dimension_unit = $item['dimension_unit'];
                $item_dimension_unit_count++;
            }

            if ($item_price_unit != $item['price_unit'] && !empty($item['price_unit']))
            {
                $item_price_unit = $item['price_unit'];
                $item_price_unit_count++;
            }            
            
            if ($item_currency != $item['currency'] && !empty($item['currency']))
            {
                $item_currency = $item['currency'];
                $item_currency_count++;
            }                
        }
        
        $this->_assign('item_weight_unit',          $item_weight_unit);
        $this->_assign('item_weight_unit_count',    $item_weight_unit_count);
        $this->_assign('item_dimension_unit',       $item_dimension_unit);
        $this->_assign('item_dimension_unit_count', $item_dimension_unit_count);
        $this->_assign('item_currency',             $item_currency);
        $this->_assign('item_currency_count',       $item_currency_count);
        $this->_assign('item_price_unit',           $item_dimension_unit);
        $this->_assign('item_price_unit_count',     $item_dimension_unit_count);
        
        $this->_assign('total_qtty',            $total_qtty);
        $this->_assign('total_weight',          $total_weight);
        $this->_assign('total_value',           $total_value);
        $this->_assign('total_purchase_value',  $total_purchase_value);            
        
        $this->_assign('page', $page);
        

        $this->breadcrumb = array(
           'Items'          => '/items',
           $this->page_name => ''
        );

        $this->js       = 'item_index';
        $this->context  = 'main_index';
        
        $this->_display('index');
    }
    
    /**
     * Отображает страницу просмотра айтема
     * url: /item/{$item_id}
     * 
     * @version 20130324, zharkov
	 * @version 20130527, sasha: item history
     */
    function view()
    {
        $item_id = Request::GetInteger('id', $_REQUEST);
        if (empty($item_id)) _404();
        
        $modelSteelItem = new SteelItem();
        $item           = $modelSteelItem->GetById($item_id);
	
        if (empty($item)) _404();
		
		$history = $modelSteelItem->GetHistory($item_id);
	
        $item = $item['steelitem'];
        
        $this->context      = true;
        $this->page_name    = $item['doc_no'];
        $this->breadcrumb   = array(
            'Items'             => '/items',
            $this->page_name    => ''
        );
        
        $this->_assign('form', $item);

        $modelSteelPosition = new SteelPosition();
        $position           = $modelSteelPosition->GetById($item['steelposition_id']);
        
        if (!empty($position))
        {
            $this->_assign('position', $position['steelposition']);
        }
        
        if (!empty($item['parent_id']))
        {
            $parent = $modelSteelItem->GetById($item['parent_id']);
            if (!empty($parent))
            {
                $this->_assign('parent', $parent['steelitem']);
            }            
        }
        
        $modelAttachment    = new Attachment();
        $attachments_list   = $modelAttachment->GetListByType('', 'item', $item_id);
        $this->_assign('attachments_list', $attachments_list['data']);        
        
        $this->_assign('related_docs', $modelSteelItem->GetRelatedDocs($item_id));
        $this->_assign('history', $history);
		
        $this->_display('view');
    }
    
    /**
     * Форма создания алиасов айтемов
     * url: /item/createalias/{item_ids}
     * 
     * @version 20130214, zharkov
     */
    function createalias()
    {
        $ids        = Request::GetString('ids', $_REQUEST);
        
        $ids        = array_unique(explode(',', $ids));
        $steelitems = array();
        
        foreach ($ids as $key => $id)
        {
            $id = Request::GetInteger($key, $ids);
            $steelitems[] = array('steelitem_id' => $id);    
        }
        
        $modelSteelItem     = new SteelItem();
        $steelitems         = $modelSteelItem->FillSteelItemInfo($steelitems);

        if (empty($steelitems)) 
        {
            $this->_message('Undefined Items !', MESSAGE_ERROR);
            $this->_redirect(array('positions'));
        }
        
        $steelposition_id   = isset($steelitems[0]) && isset($steelitems[0]['steelitem']) ? $steelitems[0]['steelitem']['steelposition_id'] : 0;

        $modelSteelPosition = new SteelPosition();
        $position           = $modelSteelPosition->GetById($steelposition_id);

        if (empty($position) || empty($position['steelposition']) || empty($position['steelposition']['stock_id']))
        {
            $this->_message('Wrong Stock !', MESSAGE_ERROR);
            $this->_redirect(array('positions'));            
        }
        
        $modelSteelposition = new SteelPosition();
        
        $stock_id           = $position['steelposition']['stock_id'];
        $stockholder_id     = 0;
        $location_id        = 0;
        
        $items = array();
        foreach ($steelitems as $row)
        {
            $row    = $row['steelitem'];
            $id     = $row['id'];
            $items[$id] = array(
                'id'                => $id,
                'steelgrade_id'     => $row['steelgrade_id'], 
                'thickness'         => $row['thickness'], 
                'width'             => $row['width'], 
                'length'            => $row['length'], 
                'unitweight'        => $row['unitweight'], 
                'price'             => '', 
                'delivery_time'     => '', 
                'notes'             => '', 
                'internal_notes'    => '',
                'position_id'       => 0,
                'biz_title'         => '',
                'biz_id'            => ''
            );
            
            $position = $modelSteelPosition->GetById($row['steelposition_id']);
            if (isset($position['steelposition']))
            {
                $position = $position['steelposition']; 
                
                $items[$id]['price']         = $position['price'];
                $items[$id]['delivery_time'] = isset($position['deliverytime']) ? $position['deliverytime']['title'] : '';
            }
        }
        

        if (isset($_REQUEST['btn_save']))
        {
            $request_items  = isset($_REQUEST['items']) ? $_REQUEST['items'] : array();
            $stock_id       = Request::GetInteger('stock_id', $_REQUEST);
            $stockholder_id = Request::GetInteger('stockholder_id', $_REQUEST); 
            
            $location_id    = 0;
            $companies      = new Company();
            $company        = $companies->GetById($stockholder_id);

            if (!empty($company)) $location_id = $company['company']['location_id'];
            
            
            $okay_flag = true;
            
            if ($stock_id <= 0)
            {
                $this->_message('I forgot to specify stock !', MESSAGE_ERROR);
                $okay_flag = false;
            }
            else if ($stockholder_id <= 0)
            {
                $this->_message('I forgot to specify location !', MESSAGE_ERROR);
                $okay_flag = false;
            }
            
            if ($okay_flag)
            {
                $modelSteelItem = new SteelItem();
                foreach($request_items as $item)
                {
                    $err_fields     = '';                    
                    $steelitem_id   = Request::GetInteger('id', $item);                    
                    $steelgrade_id  = Request::GetInteger('steelgrade_id', $item);
                    $thickness      = Request::GetNumeric('thickness', $item);
                    $width          = Request::GetNumeric('width', $item);
                    $length         = Request::GetNumeric('length', $item);
                    $unitweight     = Request::GetNumeric('unitweight', $item);
                    $price          = Request::GetNumeric('price', $item);
                    $delivery_time  = Request::GetString('delivery_time', $item);
                    
                    $steelitem = $modelSteelItem->GetById($steelitem_id);
                    if (empty($steelitem)) 
                    {
                        $this->_message('Item ' . $steelitem_id . ' undefined !', MESSAGE_ERROR);
                        $okay_flag = false;                        
                        break;                        
                    }
                    
                    if ($steelgrade_id <= 0 || $thickness <= 0 || $width <= 0 || $length <= 0 || $unitweight <= 0 || $price <= 0 || empty($delivery_time))
                    {
                        $this->_message('Item ' . $steelitem_id . ' : Steel Grade, Thickness, Width, Length, Unit Weight, Price, Delivery Time must be specified !', MESSAGE_ERROR);
                        $okay_flag = false;
                    }
                    
                    $steelitem = $steelitem['steelitem'];
                    
                    if ($stockholder_id == $steelitem['stockholder_id'] && $steelitem['steelgrade_id'] == $steelgrade_id && $steelitem['thickness'] == $thickness && $steelitem['width'] == $width && $steelitem['length'] == $length)
                    {
                        $this->_message('Item ' . $steelitem_id . ' : Location or Steel Grade or Thickness or Width or Length for alias must differ !', MESSAGE_ERROR);
                        $okay_flag = false;                        
                    }
                }                
            }
            
            if ($okay_flag)
            {
                $modelSteelItem = new SteelItem();
                foreach($request_items as $item)
                {
                    $steelitem_id   = Request::GetInteger('id', $item);
                    $steelgrade_id  = Request::GetInteger('steelgrade_id', $item);
                    $thickness      = Request::GetNumeric('thickness', $item);
                    $width          = Request::GetNumeric('width', $item);
                    $length         = Request::GetNumeric('length', $item);
                    $unitweight     = Request::GetNumeric('unitweight', $item);
                    $price          = Request::GetNumeric('price', $item);
                    $delivery_time  = Request::GetString('delivery_time', $item);
                    $notes          = Request::GetString('notes', $item);
                    $internal_notes = Request::GetString('internal_notes', $item);
                    $position_id    = Request::GetInteger('position_id', $item);
                    
                    $modelSteelItem->CreateAlias($stock_id, $stockholder_id, $steelitem_id, $steelgrade_id, 
                                                    $thickness, $width, $length, $unitweight, $price,
                                                    $delivery_time, $notes, $internal_notes, $position_id);
                }

                $this->_message('Aliases was successfully created .', MESSAGE_OKAY);
                $this->_redirect(array('positions', 'filter', 'stock:' . $stock_id . ';location:' . $location_id), false);
            }
            
            
            $items = array_replace_recursive($items, $request_items);
        }
        
        
        $modelStock = new Stock();
        $stock      = $modelStock->GetById($stock_id);
        $stock      = $stock['stock'];
        
        $modelSteelPosition = new SteelPosition();
        foreach ($items as $key => $row)
        {
            $width   = $stock['dimension_unit'] == 'in' ? $row['width'] * 25.4 : $row['width'];
            $length  = $stock['dimension_unit'] == 'in' ? $row['length'] * 25.4 : $row['length'];
            
            if ($width > 0) $width = ($width - 50) . '-' . ($width + 50);
            if ($length > 0) $length = ($length - 100) . '-' . ($length + 100);            

            $items[$key]['positions'] = $modelSteelPosition->GetList(0, $stock_id, $location_id, '', $row['steelgrade_id'], $row['thickness'], $width, $length, 0, '', '', '');
        }            

        
        $this->_assign('stocks',            $modelStock->GetList());        
        $this->_assign('locations',         $modelStock->GetLocations($stock_id, false));
        $this->_assign('stock',             $stock);
        $this->_assign('stockholder_id',    $stockholder_id);
        $this->_assign('items',             $items);
        
        $modelSteelGrade = new SteelGrade();
        $this->_assign('steelgrades', $modelSteelGrade->GetList());
        
        $this->breadcrumb   = array(
            'Items'             => '/items',
            $this->page_name    => ''            
        );
        $this->page_name    = 'New Aliases';
        $this->js           = 'item_doalias';
        $this->context      = true;
        
        $this->_display('createalias');
    }
    
    /**
     * Возвращает айтем из заказа на склад
     * /item/removefromorder
     * 
     * @version 20130207, zharkov
     */
    function removefromorder()
    {
        // только для DJ
        if ($this->user_id != 303) _404();
        
        if (isset($_REQUEST['btn_commit']))
        {
            $steelitem_id   = Request::GetInteger('steelitem_id', $_REQUEST);
            
            if ($steelitem_id <= 0)
            {
                $this->_message('Incorrect item id !', MESSAGE_ERROR);
            }
            else
            {
                $this->_assign('steelitem_id', $steelitem_id);

                $modelSteelItem = new SteelItem();
                $steelitem      = $modelSteelItem->GetById($steelitem_id);
                
                if (empty($steelitem))
                {
                    $this->_message('Item not found ! Please check & re-enter system Item Id', MESSAGE_ERROR);
                }
                else
                {
                    $steelitem      = $steelitem['steelitem'];                    
                    $order_id       = $steelitem['order_id'];
                    $position_id    = $steelitem['steelposition_id'];

                    $modelOrder     = new Order();
                    
                    if ($order_id > 0)
                    {                    
                        // если в заказе, в котором находится айтем, есть позиция, в которой находится айтем,
                        // то пользователь перенаправляется на страницу редактирования айтемов заказа
                        foreach ($modelOrder->GetPositions($order_id) as $row)
                        {
                            if ($row['position_id'] == $position_id)
                            {
                                $this->_message('To remove item from order please uncheck item on this page & press "Save" .', MESSAGE_WARNING);
                                $this->_redirect(array('order', 'selectitems', $order_id, 'position:' . $position_id), false);
                                
                                exit;
                            }
                        }

                        $modelOrder->RemoveItem($order_id, $steelitem_id);
                        
                        $modelSteelPosition  = new SteelPosition();        
                        $modelSteelPosition->UpdateQtty($position_id);
                        
                        $this->_message('Item was successfully removed from order !', MESSAGE_OKAY);
                        $this->_assign('steelitem_id', '');
                    }
                    else
                    {
                        $this->_message('This item is not ordered !', MESSAGE_ERROR);
                    }                    
                }
            }
        }
        
        $this->page_name = 'Remove Item From Order';
        $this->_display('removefromorder');
    }
    
    function contexttest()
    {
        $id = Request::GetInteger('id', $_REQUEST);
        
        $modelSteelItem = new SteelItem();
        $item           = $modelSteelItem->GetById($id);        
        $this->_assign('item', $item['steelitem']);
        
        $modelAttachment    = new Attachment();
        $attachments        = $modelAttachment->GetList('steelitem', $id);
        $this->_assign('attachments', $attachments['data']);

        $this->_display('contexttest');        
    }
    
    /**
     * Показывает страницу создания обрезков
     * url: /position/{position_id}/item/cut/{item_id}
     * 
     * @version 20120921, zharkov
     */
    function cut()
    {
        $item_id        = Request::GetInteger('id', $_REQUEST);
        $position_id    = Request::GetInteger('position_id', $_REQUEST);
        
        $modelItem      = new SteelItem();
        $item           = $modelItem->GetById($item_id);
        if (empty($item)) _404();
        
        $item = $item['steelitem'];
        
        // 20130112, zharkov: нельзя порезать виртуальный, заказанный или удаленный айтем
        if ($item['parent_id'] > 0 || $item['order_id'] > 0 || $item['is_deleted'] > 0) _404();
        
        $modelPosition  = new SteelPosition();
        $position       = $modelPosition->GetById($item['steelposition_id']);
        $position       = $position['steelposition'];
                
        if (isset($_REQUEST['btn_save']))
        {
            $pieces     = $_REQUEST['pieces'];            
            $no_errors  = true;
            
            foreach ($pieces as $key => $row)
            {
                $width  = Request::GetNumeric('width', $row);
                $length = Request::GetNumeric('length', $row);
                
                if (empty($width) || empty($length))
                {
                    $no_errors = false;
                    break;
                }
            }
            
            //check guid
            $guid_error = true;
            foreach($pieces as $row)
            {
                $guid   = Request::GetString('guid', $row); 
                
                if ($modelItem->CheckGuid($item_id, $guid))
                {
                   $no_errors = false;
                   $guid_error = false;
                   $this->_message('Plate ID "'. $guid .'" already exists, please specify another Plate ID!', MESSAGE_ERROR);
                   break;
                }
            }    
           
            if ($no_errors)
            { 
                $position_ids = '';
                foreach ($pieces as $key => $row)
                {
                    $id                 = Request::GetInteger('id', $row);
                    $guid               = Request::GetString('guid', $row);
                    $width              = Request::GetNumeric('width', $row);
                    $length             = Request::GetNumeric('length', $row);
                    $unitweight         = Request::GetNumeric('unitweight', $row);
                    $notes              = Request::GetString('notes', $row);
                    $item_location_id   = Request::GetInteger('location_id', $row);
                    $item_position_id   = Request::GetInteger('position_id', $row);

                    $result = $modelItem->CutItem($id, $item_id, $guid, $width, $length, $unitweight, $notes, $item_position_id, $item_location_id);
                    if (empty($result))
                    {
                        $no_errors  = false;
                        break;
                    }
                    else
                    {
                        if (empty($item_position_id)) $position_ids .= $result['position_id'] . ',';                        
                    }
                }
                
                if ($no_errors)                
                {
                    $this->_message('Plate was cut successfully', MESSAGE_OKAY);
                    
                    // remove cutted item
                    $modelItem->RemoveCutted($item_id);
                    
                    // update cutted item position qtty
                    $modelPosition->UpdateQtty($position['id']);
                    
                    if (!empty($position_ids))
                    {
                        $this->_redirect(array('position', 'groupedit', trim($position_ids, ',')), false);    
                    }
                    else
                    {
                        $this->_redirect(array('items'), false);    
                    }
                }
                else
                {
                    $this->_message('Error while cutting plate was occured !', MESSAGE_ERROR);
                }                
            }
            else if ($guid_error)
            {
                $this->_message('Width & length must be specified !', MESSAGE_ERROR);
            }
        }
        else
        {
            $pieces = array();
            $index  = 1;
            foreach ($modelItem->GetChildren($item_id) as $row)
            {

                $row        = $row['steelitem'];                
                $pieces[]   = array(
                    'id'            => $row['id'],
                    'guid'          => empty($item['guid']) ? '' : $item['guid'] . '-' . $index,
                    'width'         => $row['width'],
                    'length'        => $row['length'],
                    'unitweight'    => $row['unitweight'],
                    'notes'         => $item['notes'],
                    'location_id'   => $item['stockholder_id'],
                    'position_id'   => 0,
                    'order_id'      => $row['order_id'],
                    'status_title'  => $row['status_title']
                );
                
                $index++;
            }
            
            for($i = $index; $i <= 2; $i++)
            {
                $pieces[$i] = array(
                    'id'            => 0,
                    'guid'          => empty($item['guid']) ? '' : $item['guid'] . '-' . $i,
                    'width'         => $item['width'],
                    'length'        => $item['length'],
                    'unitweight'    => $item['unitweight'],
                    'notes'         => $item['notes'],
                    'location_id'   => $item['stockholder_id'],
                    'position_id'   => 0,
                    'order_id'      => 0,
                    'status_title'  => ''
                );
            }            
        }

        $modelStock = new Stock();
        $locations  = $modelStock->GetLocations($position['stock_id']);
        
        foreach ($pieces as $key => $row)
        {
            $pieces[$key]['locations'] = $locations;
            if ($row['location_id'] > 0 && $row['width'] > 0 && $row['length'] > 0)
            {
                $pieces[$key]['positions'] = $modelPosition->GetList(0, $position['stock_id'], $row['location_id'], '', $item['steelgrade_id'], $item['thickness'], 0, 0, $row['width'], 0, 0, $row['length'], 0, 0, 0, 0, 0, '', '', '');
            }
            else
            {
                $pieces[$key]['positions'] = array();
            }
        }
        
        $this->_assign('item',      $item);
        $this->_assign('pieces',    $pieces);
        
        $this->page_name    = 'Plate to be cut';
        $this->breadcrumb   = array(
            'Items'             => '/items',
            $this->page_name    => ''
        );
        
        $this->context  = true;
        $this->js       = 'item_cut';
                
        $this->_display('cut');
    }

    /**
     * Отображает страницу группового редактирования айтемов
     * url: /item/edit/{ids}
     * 
     * @version 20120817, zharkov
     */
    function edit()
    {
        $target_doc     = Request::GetString('target_doc', $_REQUEST);
        $target_doc_id  = Request::GetInteger('target_doc_id', $_REQUEST);        
        $ids            = Request::GetString('ids', $_REQUEST);

        if (empty($ids)) _404();

        
        $rowset = array();
        foreach (explode(',', $ids) as $key => $item_id)
        {
            if ($key == 60)
            {
                $this->_message('To many items selected. Only 20 items can be edited at the same time.', MESSAGE_ERROR);
                break;
            }
            
            $rowset[] = array('steelitem_id' => $item_id);                
            SteelItem::Lock($item_id);
        }
        
        $steelitems     = new SteelItem();
        $items          = $steelitems->FillSteelItemInfo($rowset);

        $steelpositions = new SteelPosition();
        $stocks         = new Stock();
        $bizes          = new Biz();
        $companies      = new Company();
        $owners         = $companies->GetMaMList();
                
        $dimension_units    = array();       
        $weight_units       = array();
        $currencies         = array();
//dg($items);
        foreach ($items as $key => $item)
        {
            $item       = $item['steelitem'];                
            $items[$key]['locations']   = $stocks->GetLocations($item['stock_id'], false);
            $items[$key]['suppliers']   = $bizes->GetCompanies($item['biz_id'], 'producer');
            $items[$key]['owners']      = $owners;
            $dimension_units[$item['dimension_unit']]   = $item['dimension_unit'];
            $weight_units[$item['weight_unit']]         = $item['weight_unit'];
            $currencies[$item['currency']]              = $item['currency'];
        }
        
        if (isset($dimension_units['in'])) $this->_assign('include_nominal', true);
        if (count($dimension_units) > 1) $this->_assign('multi_dimensions', true);
        if (count($weight_units) > 1) $this->_assign('multi_weights', true);

        // возврат обратно
        if ($target_doc == 'qc')
        {
            $this->page_name    = 'QC Items';
            $back_url           = 'qc/' . (empty($target_doc_id) ? 'add' : $target_doc_id . '/edit');
            
            $this->breadcrumb   = array('QCs' => '/qc');
            
            if (empty($target_doc_id))
            {
                $this->breadcrumb['New QC']  = '/qc/add';
            }
            else
            {
                $qcs    = new QC();
                $qc     = $qcs->GetById($target_doc_id);
                
                if (empty($qc)) _404();
                
                $this->breadcrumb[$qc['qc']['doc_no']] = '/qc/' . $target_doc_id . '/edit';
            }
        }
        else if ($target_doc == 'ra')
        {
            $modelRA    = new RA();
            $ra         = $modelRA->GetById($target_doc_id);            
            if (empty($ra)) _404();

            $this->page_name    = 'RA Items Edit';
            $back_url           = 'ra/' . $target_doc_id;
            $this->breadcrumb   = array(
                'RA'                => '/ra',
                $ra['ra']['doc_no'] => '/' . $back_url
            );            
        }
        else if ($target_doc == 'position')
        {
            $positions  = new SteelPosition();
            $position   = $positions->GetById($target_doc_id);
            $position   = $position['steelposition'];
            
            $this->page_name    = 'Items Edit';
            $back_url           = 'positions/filter/stock:' . $position['stock_id'] . ';#position-' . $target_doc_id;            
            $this->breadcrumb   = array(
                'Stocks'    => '/stocks',
                'Positions' => '/' . $back_url
            );            
        }
        else
        {
            $this->page_name    = 'Items Edit';
            $back_url           = 'items';
            $this->breadcrumb   = array(
                'Stocks'    => '/stocks',
                'Items'     => '/' . $back_url
            );
        }
        
        $this->breadcrumb[$this->page_name] = '';
        
        
        if (isset($_REQUEST['btn_cancel']))
        {
            // освобождает айтемы
            foreach ($items as $item) SteelItem::Unlock($item['steelitem']['id']);

            $this->_redirect(explode('/', $back_url), false);            
        }
        else if (isset($_REQUEST['btn_save']))
        {
            if (!isset($_REQUEST['item'])) _404();
            if (!isset($_REQUEST['item_property'])) _404();

            // проверка plate id для каждого айтема
            $steelitems = new SteelItem();
            $no_errors  = true;
            foreach($_REQUEST['item'] as $item)
            {                
                $guid       = Request::GetString('guid', $item);                
                $item_id    = Request::GetInteger('id', $item);

                if (!empty($guid) && $steelitems->CheckGuid($item_id, $guid))
                {
                    $this->_message("Plate ID '" . $guid . "' has already allocated for another item !", MESSAGE_ERROR);
                    $no_errors = false;
                }
            }

            // сохранение 
            if ($no_errors)
            {
                $no_errors = true;
                foreach($_REQUEST['item'] as $key => $item)
                {
                    $item_id                = Request::GetInteger('id', $item);
                    $position_id            = Request::GetInteger('steelposition_id', $item);
                    $product_id             = Request::GetInteger('product_id', $item);
                    $biz_id                 = Request::GetInteger('biz_id', $item);
                    $dimension_unit         = Request::GetString('dimension_unit', $item);                    
                    $weight_unit            = Request::GetString('weight_unit', $item);
                    $price_unit             = Request::GetString('price_unit', $item);
                    $currency               = Request::GetString('currency', $item);
                    $price                  = Request::GetNumeric('price', $item);
                    $guid                   = Request::GetString('guid', $item);
                    $is_deleted             = Request::GetInteger('is_deleted', $item);
                    $steelgrade_id          = Request::GetInteger('steelgrade_id', $item);
                    $thickness              = Request::GetNumeric('thickness', $item);
                    $width                  = Request::GetNumeric('width', $item);
                    $length                 = Request::GetNumeric('length', $item);
                    $unitweight             = Request::GetNumeric('unitweight', $item);
                    $thickness_measured     = Request::GetNumeric('thickness_measured', $item);
                    $width_measured         = Request::GetNumeric('width_measured', $item);
                    $length_measured        = Request::GetNumeric('length_measured', $item);
                    $unitweight_measured    = Request::GetNumeric('unitweight_measured', $item);
                    $width_max              = Request::GetNumeric('width_max', $item);
                    $length_max             = Request::GetNumeric('length_max', $item);
                    $unitweight_weighed     = Request::GetNumeric('unitweight_weighed', $item);
                    $is_virtual             = Request::GetInteger('is_virtual', $item);
                    $location_id            = Request::GetInteger('location_id', $item);
                    $supplier_id            = Request::GetInteger('supplier_id', $item);
                    $mill                   = Request::GetString('mill', $item);
                    $system                 = Request::GetString('system', $item);
                    $supplier_invoice_no    = Request::GetString('supplier_invoice_no', $item);
                    $supplier_invoice_date  = Request::GetDateForDB('supplier_invoice_date', $item);
                    $purchase_price         = Request::GetNumeric('purchase_price', $item);
                    $purchase_currency      = Request::GetString('purchase_currency', $item);
                    $current_cost           = Request::GetNumeric('current_cost', $item);
                    $pl                     = Request::GetNumeric('pl', $item);
                    $in_ddt_number          = Request::GetString('in_ddt_number', $item);
                    $in_ddt_date            = Request::GetDateForDB('in_ddt_date', $item);
                    $in_ddt_company         = Request::GetString('in_ddt_company', $item);
                    $in_ddt_company_id      = Request::GetInteger('in_ddt_company_id', $item);                    
                    $ddt_number             = Request::GetString('ddt_number', $item);
                    $ddt_date               = Request::GetDateForDB('ddt_date', $item);
                    $ddt_company            = Request::GetString('ddt_company', $item);
                    $ddt_company_id         = Request::GetInteger('ddt_company_id', $item);                    
                    $status_id              = Request::GetInteger('status_id', $item);
                    $delivery_time          = Request::GetString('delivery_time', $item);
                    $load_ready             = Request::GetString('load_ready', $item);
                    $owner_id               = Request::GetInteger('owner_id', $item);
                    $notes                  = Request::GetString('notes', $item);
                    $internal_notes         = Request::GetString('internal_notes', $item);
                    
                    $nominal_thickness_mm   = Request::GetNumeric('nominal_thickness_mm', $item);
                    $nominal_width_mm       = Request::GetNumeric('nominal_width_mm', $item);
                    $nominal_length_mm      = Request::GetNumeric('nominal_length_mm', $item);
                    
                    $is_ce_mark                 = Request::GetInteger('is_ce_mark', $item);
                    $is_mec_prop_not_required   = Request::GetInteger('is_mec_prop_not_required', $item);
                    
                    $in_ddt_company_id      = empty($in_ddt_company_id) || empty($in_ddt_company) ? 0 : $in_ddt_company_id;
                    $ddt_company_id         = empty($ddt_company_id) || empty($ddt_company) ? 0 : $ddt_company_id;                    

                    // сохраняет айтем                        
                    $result = $steelitems->Save($item_id, $position_id, $guid, $product_id, $biz_id, $location_id, $dimension_unit, $weight_unit, $price_unit, $currency, 
                                                $steelgrade_id, $thickness, $thickness_measured, $width, $width_measured, $width_max, 
                                                $length, $length_measured, $length_max, $unitweight, $price, $unitweight * $price, $delivery_time, $notes, 
                                                $internal_notes, $supplier_id, $supplier_invoice_no, $supplier_invoice_date, 
                                                $purchase_price, 0, $in_ddt_number, $in_ddt_date, $ddt_number, $ddt_date, $owner_id, $status_id, $is_virtual, $mill, $system, 
                                                $unitweight_measured, $unitweight_weighed, $current_cost, $pl, $load_ready, $purchase_currency, 
                                                $in_ddt_company_id, $ddt_company_id, 
                                                $nominal_thickness_mm, $nominal_width_mm, $nominal_length_mm, 
                                                $is_ce_mark, $is_mec_prop_not_required);
                    
                    if (empty($result) || isset($result['ErrorCode']))
                    {
                        if (isset($result['ErrorCode']))
                        {
                            if ($result['ErrorCode'] == -1)
                            {
                                $this->_message("Plate ID '" . $guid . "' has already allocated for another item !", MESSAGE_ERROR);    
                            }
                            else
                            {
                                $this->_message("Item with ID = '" . $item_id . "' is not exists !", MESSAGE_ERROR);    
                            }
                        }
                        else
                        {
                            $this->_message('We got an error when saving item !', MESSAGE_ERROR);    
                        }

                        $no_errors = false;
                        break;
                    }
                    else
                    {
                        $item_property = $_REQUEST['item_property'][$key];
                        
                        $heat_lot                   = Request::GetString('heat_lot', $item_property);
                        $c                          = Request::GetNumeric('c', $item_property);
                        $si                         = Request::GetNumeric('si', $item_property);
                        $mn                         = Request::GetNumeric('mn', $item_property);
                        $p                          = Request::GetNumeric('p', $item_property);
                        $s                          = Request::GetNumeric('s', $item_property);
                        $cr                         = Request::GetNumeric('cr', $item_property);
                        $ni                         = Request::GetNumeric('ni', $item_property);
                        $cu                         = Request::GetNumeric('cu', $item_property);
                        $al                         = Request::GetNumeric('al', $item_property);
                        $mo                         = Request::GetNumeric('mo', $item_property);
                        $nb                         = Request::GetNumeric('nb', $item_property);
                        $v                          = Request::GetNumeric('v', $item_property);
                        $n                          = Request::GetNumeric('n', $item_property);
                        $ti                         = Request::GetNumeric('ti', $item_property);
                        $sn                         = Request::GetNumeric('sn', $item_property);
                        $b                          = Request::GetNumeric('b', $item_property);
                        $ceq                        = Request::GetNumeric('ceq', $item_property);
                        $tensile_sample_direction   = Request::GetString('tensile_sample_direction', $item_property);
                        $tensile_strength           = Request::GetInteger('tensile_strength', $item_property);
                        $yeild_point                = Request::GetInteger('yeild_point', $item_property);
                        $elongation                 = Request::GetNumeric('elongation', $item_property);
                        $reduction_of_area          = Request::GetNumeric('reduction_of_area', $item_property);
                        $test_temp                  = Request::GetInteger('test_temp', $item_property);
                        $impact_strength            = Request::GetString('impact_strength', $item_property);
                        $hardness                   = Request::GetInteger('hardness', $item_property);
                        $ust                        = Request::GetString('ust', $item_property);
                        $sample_direction           = Request::GetString('sample_direction', $item_property);
                        $stress_relieving_temp      = Request::GetInteger('stress_relieving_temp', $item_property);
                        $heating_rate_per_hour      = Request::GetInteger('heating_rate_per_hour', $item_property);
                        $holding_time               = Request::GetInteger('holding_time', $item_property);
                        $cooling_down_rate          = Request::GetInteger('cooling_down_rate', $item_property);
                        $normalizing_temp           = Request::GetInteger('normalizing_temp', $item_property);
                        $condition                  = Request::GetString('condition', $item_property);
                        
                        // сохраняет свойства позиции
                        $result =  $steelitems->SaveProperties($result['id'], $heat_lot, $c, $si, $mn, $p, $s, $cr, $ni, $cu, $al, 
                                                                $mo, $nb, $v, $n, $ti, $sn, $b, $ceq, $tensile_sample_direction, $tensile_strength, $yeild_point, 
                                                                $elongation, $reduction_of_area, $test_temp, $impact_strength, $hardness, 
                                                                $ust, $sample_direction, $stress_relieving_temp, $heating_rate_per_hour, 
                                                                $holding_time, $cooling_down_rate, $condition, $normalizing_temp);

                        if (empty($result))
                        {
                            $this->_message('We got an error when saving item properties !', MESSAGE_ERROR);
                            $no_errors = false;
                            break;                            
                        }
                    }                    
                }
                
                if ($no_errors) $this->_redirect(explode('/', $back_url), false);
            }             

            if (isset($_REQUEST['item']))
            {
                foreach ($items as $i => $item)
                {
                    $item = $item['steelitem'];
                    
                    foreach ($_REQUEST['item'] as $j => $pb_item)
                    {
                        if ($item['id'] == $pb_item['id'])
                        {
                            foreach ($pb_item as $pb_item_key => $pb_item_value)
                            {
                                if ($pb_item_key == 'supplier_invoice_date' || $pb_item_key == 'ddt_date' || $pb_item_key == 'in_ddt_date')
                                {
                                    $pb_item_value = Request::GetDateForDB($pb_item_key, $pb_item);
                                }
                                else
                                {
                                    $pb_item_value = Request::GetString($pb_item_key, $pb_item);
                                }
                                
                                $items[$i]['steelitem'][$pb_item_key] = $pb_item_value;
                            }
                            
                            foreach ($_REQUEST['item_property'][$j] as $pb_item_property_key => $pb_item_property_value)
                            {
                                $items[$i]['steelitem']['properties'][$pb_item_property_key] = Request::GetString($pb_item_property_key, $_REQUEST['item_property'][$j]);
                            }
                        }
                    }
                }
                
                foreach ($_REQUEST['item'] as $i => $pb_item)
                {
                    if ($pb_item['id'] != 0) continue;
                    
                    $pb_item['properties'] = $_REQUEST['item_property'][$i];
                    $items[] = array(
                        'steelitem_id'  => 0,
                        'steelitem'     => $pb_item
                    );
                }
            }            
        }
        
        $this->_assign('items', $items);
        
        $dimension_unit = array_values($dimension_units);
        $dimension_unit = array_shift($dimension_unit);
        $this->_assign('dimension_unit', $dimension_unit);
        
        $weight_unit = array_keys($weight_units);
        $weight_unit = array_shift($weight_unit);
        $this->_assign('weight_unit', $weight_unit);
        
        $currency = array_keys($currencies);
        $currency = array_shift($currencies);
        $this->_assign('currency', $currency);
        
        $this->_assign('include_ui', true);        
        
        $steelgrades = new SteelGrade();
        $this->_assign('steelgrades', $steelgrades->GetList());        
        
        $this->js = 'item_index';	
        $this->context = true;
        $this->_display('groupedit');
    }

    /**
     * Отображает страницу со списком конфликтующих айтемов
     * /item/{id}/conflicted
     * 
     * @version 20120726, zharkov
     */
    function conflicted()
    {
        $item_id    = Request::GetInteger('id', $_REQUEST);
        
        $steelitems = new SteelItem();
        $item       = $steelitems->GetById($item_id);
        
        if (empty($item)) _404();
        
        $item = $item['steelitem'];
        if (empty($item['is_conflicted'])) $this->_redirect(array('item', $item_id, 'edit'));
        
        $list   = $steelitems->GetConflicted($item_id);
        $units  = array();
        
        foreach ($list as $row)
        {
            if (!in_array($row['steelitem']['dimension_unit'], $units))
            {
                $units[] = $row['steelitem']['dimension_unit'];
            }
        }
        
        $this->_assign('list',              $list);
        $this->_assign('item',              $item);
        $this->_assign('different_units',   count($units) > 1);
        
        $this->page_name    = 'Conflicted Items';
        $this->breadcrumb   = array(
            'Items'             => '/items',
            $item['title']      => '/item/' . $item_id . '/edit',
            $this->page_name    => ''
        );
        
        $this->_display('conflicted');
    }
    
    /**
     * Отображает страницу с ревизией айтема
     * url: /item/{item_id}/revision/{revision_guid}
     * url: /position/{position_id}/item/{item_id}/revision/{revision_guid}
     */
    function revision()
    {
        $ref            = Request::GetString('ref', $_REQUEST);
        $position_id    = Request::GetInteger('position_id', $_REQUEST);        
        
        $item_id        = Request::GetInteger('id', $_REQUEST);
        $revision       = Request::GetString('revision', $_REQUEST);        
        if (empty($revision)) _404();
        
        $revision   = explode('-', base64_decode($revision));
        if (count($revision) < 4 || $revision[2] != 'itemrevision') _404();

        $item_history_id            = $revision[0];
        $item_properties_history_id = $revision[1];
        $revision_no                = $revision[3];
        
        $steelitems = new SteelItem();
        $rowset     = $steelitems->GetHistoryRevision($item_id, $item_history_id, $item_properties_history_id);
//dg($rowset);
        $this->_assign('item',          $rowset['history']);
        $this->_assign('properties',    $rowset['properties']);
        
        if ($ref == 'positions' && $position_id > 0)
        {
            $positions  = new SteelPosition();
            $position   = $positions->GetById($position_id);
            
            if (empty($position)) _404();
            
            $back_url = $this->_get_previous_url_by_part('positions/filter');
            $back_url = empty($back_url) ? 'positions' : $back_url;
            
            $this->breadcrumb = array(
                'Positions'             => '/positions',
                'Filtered Positions'    => '/' . $back_url,
                'Item History'          => '/position/' . $position_id . '/item/history/' . $item_id
            );
        }
        else
        {
            $back_url = $this->_get_previous_url_by_part('items/filter');
            $back_url = empty($back_url) ? 'items' : $back_url;
            
            $this->breadcrumb = array(
                'Items'             => '/items',
                'Filtered Items'    => '/' . $back_url,
                'Item History'      => '/item/history/' . $item_id
            );            
        }

        $this->page_name = 'Item Revision # ' . $revision_no;
        $this->breadcrumb[$this->page_name] = '';
        
        
        $this->_display('revision');
    }
    
    /**
     * Отображает страницу со списоком items
     * url: /items
     * url: /items/filter/{filter}
     * 
     * @version 20120812, zharkov: добавлена привязка к документу, если передан документ, то все действия с айтемами ведутся по этому документу
     * @version 20120808, zharkov: убрал выбор deliverytime потому что его нет у айтема, только у позиции, и в этом списке фильтрация по нему не нужна
     */
    function index()
    {
        // 20120812, zharkov: привязка к документу
        $target_doc     = Request::GetString('target_doc', $_REQUEST);
        $target_doc_id  = Request::GetInteger('target_doc_id', $_REQUEST);
        

        if (isset($_REQUEST['btn_select']))
        {
            $form = $_REQUEST['form'];
            
            $locations  = '';
            if (isset($form['stockholder']))
            {
                foreach ($form['stockholder'] as $key => $location_id)
                {
                    $location_id = Request::GetInteger($key, $form['stockholder']);
                    if ($location_id <= 0) continue;
                    
                    $locations = $locations . (empty($locations) ? '' : ',') . $location_id;
                }
            }

            $deliverytimes  = '';
            if (isset($form['deliverytime']))
            {
                foreach ($form['deliverytime'] as $key => $deliverytime_id)
                {
                    $deliverytime_id = Request::GetInteger($key, $form['deliverytime']);
                    if ($deliverytime_id <= 0) continue;
                    
                    $deliverytimes = $deliverytimes . (empty($deliverytimes) ? '' : ',') . $deliverytime_id;
                }
            }

            $types  = '';
            if (isset($form['type']))
            {
                foreach ($form['type'] as $key => $type_id)
                {
                    $type_id = Request::GetString($key, $form['type']);
                    if (empty($type_id)) continue;
                    
                    $types = $types . (empty($types) ? '' : ',') . $type_id;
                }
            }
            
            
            $stock_id       = Request::GetInteger('stock_id', $form);
            $steelgrade_id  = Request::GetInteger('steelgrade_id', $form);
            $plate_id       = str_replace(' ', '&nbsp;', Request::GetString('plate_id', $form));
            $thickness      = Request::GetString('thickness', $form);
            $width          = Request::GetString('width', $form);
            $length         = Request::GetString('length', $form);
            $weight         = Request::GetString('weight', $form);
            $notes          = Request::GetString('notes', $form);
            $rev_date       = Request::GetDateForDB('rev_date', $form);
            $rev_time       = Request::GetString('rev_time', $form);
            $rev_id         = null;
            $available      = Request::GetInteger('available', $form);
            $order_id       = Request::GetInteger('order_id', $form);
            
            if (!empty($rev_date))
            {
                $stocks = new Stock();
                $rev_id = $stocks->CheckRevision($rev_date, $rev_time);
            }
            
            // Если param_order_id > 0 нужно игнорировать фильтры из "Location" и "Type".
            if ($order_id > 0)
            {
                $locations  = '';
                $types      = '';
            }
            

            $filter     = (empty($stock_id) ? '' : 'stock:' . $stock_id . ';')
                        . (empty($locations) ? '' : 'location:' . $locations . ';')
                        . (empty($deliverytimes) ? '' : 'deliverytime:' . $deliverytimes . ';')
                        . (empty($types) ? '' : 'type:' . $types . ';')
                        . (empty($plate_id) ? '' : 'plateid:' . $plate_id . ';')
                        . (empty($steelgrade_id) ? '' : 'steelgrade:' . $steelgrade_id . ';')
                        . (empty($thickness) ? '' : 'thickness:' . $thickness . ';')
                        . (empty($width) ? '' : 'width:' . $width . ';')
                        . (empty($length) ? '' : 'length:' . $length . ';')
                        . (empty($weight) ? '' : 'weight:' . $weight . ';')
                        . (empty($notes) ? '' : 'notes:' . $notes . ';')
                        . (empty($available) ? '' : 'available:' . $available . ';')
                        . (empty($order_id) ? '' : 'order:' . $order_id . ';');
            
            
            $redirect = array();
            
            if (!empty($target_doc) && !empty($target_doc_id))
            {
                $redirect[] = 'target';
                $redirect[] = $target_doc . ':' . $target_doc_id;
            }
            
            $redirect[] = 'items';
            $redirect[] = 'filter';
            $redirect[] = preg_replace('#\s+#i', '', $filter);
            
            if (!empty($rev_id))
            {
                $redirect[] = '~rev' . $rev_id;
            }

            $this->_redirect($redirect, false);
        }
        
        
        $filter         = Request::GetString('filter', $_REQUEST);
        $filter         = urldecode($filter);
        $filter_params  = array();
        
        $stocks = new Stock();
        $this->_assign('stocks', $stocks->GetList());        
        
        
        if (empty($filter))
        {
            $this->page_name = 'Items';
        }
        else
        {
            $this->page_name            = 'Filtered Items';            
            $this->breadcrumb['Items']  = '/items';
            
            $filter = explode(';', $filter);
            foreach ($filter as $row)
            {
                if (empty($row)) continue;
                
                $param = explode(':', $row);
                $filter_params[$param[0]] = Request::GetHtmlString(1, $param);
            }
            
            $this->_assign('filter', true);
        }
        
        if (!empty($target_doc))
        {
            if ($target_doc == 'qc') $this->page_name = 'Items for Certificate Of Quality';
            if ($target_doc == 'invoice') $this->page_name = 'Items for Invoice';
        }
                    
        $this->breadcrumb[$this->page_name] = '';
        

        $product_id             = Request::GetInteger('product', $filter_params);
        $stock_id               = Request::GetInteger('stock', $filter_params);
        $selected_locations     = isset($filter_params['location']) ? explode(',', $filter_params['location']) : array();
        $selected_deliverytimes = isset($filter_params['deliverytime']) ? explode(',', $filter_params['deliverytime']) : array();
        $selected_types         = isset($filter_params['type']) ? explode(',', $filter_params['type']) : array();
        $steelgrade_id          = Request::GetInteger('steelgrade', $filter_params);
        $plate_id               = Request::GetString('plateid', $filter_params);
        $thickness              = Request::GetString('thickness', $filter_params);
        $width                  = Request::GetString('width', $filter_params);
        $length                 = Request::GetString('length', $filter_params);
        $weight                 = Request::GetString('weight', $filter_params);
        $notes                  = Request::GetString('notes', $filter_params);
        $available              = Request::GetString('available', $filter_params);
        $revision               = Request::GetString('stock_revision', $_REQUEST, '', 12);
        $order_id               = Request::GetInteger('order', $filter_params);
        
        if (!empty($revision))
        {
            $this->_assign('rev_date',      substr($revision, 6, 2) . '/' . substr($revision, 4, 2) . '/' . substr($revision, 0, 4));
            $this->_assign('rev_time',      substr($revision, 8, 2) . ':' . substr($revision, 10, 2));            
        }
        
        // Если param_order_id > 0 нужно игнорировать фильтры из "Location" и "Type".
        if ($order_id > 0)
        {
            $selected_locations = array();
            $selected_types     = array();
        }
        
        $this->_assign('is_revision', (empty($revision) ? 0 : 1));
        
        // start костылеr для определения stock_id (есть стокхолдер, но нет склада)
        if ($stock_id <= 0 && !empty($selected_locations))
        {
            $modelStock         = new Stock();
            $stock_locations    = $modelStock->GetLocations(0, false);
            foreach ($stock_locations as $location)
            {
                if (in_array($location['company_id'], $selected_locations))
                {
                    $stock_id = $location['stock_id'];
                    break;                    
                }
            }
        }
        
        
        $dimension_unit         = 'mm';
        $weight_unit            = 'mt';
        $stock_locations        = array();
        $stock_deliverytimes    = array();
        $stock_steelgrades      = array();
        $orders                 = array();
        
        $location_ids           = '';
        $deliverytime_ids       = '';
        
        
        if ($stock_id > 0)
        {
            $stock = $stocks->GetById($stock_id);
            if (!empty($stock))
            {
                $stock                  = $stock['stock'];
                $stock_locations        = $stocks->GetItemLocations($stock_id);
                $stock_deliverytimes    = array();
                $stock_steelgrades      = $stocks->GetItemSteelGrades($stock_id);
                
                $modelOrder             = new Order();
                $orders                 = $modelOrder->GetListForStock($stock_id);

                // locations
                $location_ids   = '';
                $locations      = array();
                foreach ($stock_locations as $key => $row)
                {
                    foreach ($selected_locations as $s_key => $s_location_id)
                    {
                        $s_location_id = Request::GetInteger($s_key, $selected_locations);
                        if ($s_location_id <= 0) continue;
                        
                        if ($row['stockholder_id'] == $s_location_id) 
                        {
                            $stock_locations[$key]['selected'] = true;
                            break;
                        }
                    }
                    
                    if (in_array($row['stockholder_id'], $selected_locations)) $location_ids = $location_ids . (empty($location_ids) ? '' : ',') . $row['stockholder_id'];
                }
                                
                // delivery times
                $deliverytime_ids   = '';
                $deliverytimes      = array();
                foreach ($stock_deliverytimes as $key => $row)
                {
                    foreach ($selected_deliverytimes as $s_key => $s_deliverytime_id)
                    {
                        $s_deliverytime_id = Request::GetInteger($s_key, $selected_deliverytimes);
                        if ($s_deliverytime_id <= 0) continue;
                        
                        if ($row['deliverytime_id'] == $s_deliverytime_id) 
                        {
                            $stock_deliverytimes[$key]['selected'] = true;
                            break;
                        }
                    }
                    
                    if (in_array($row['deliverytime_id'], $selected_deliverytimes)) $deliverytime_ids = $deliverytime_ids . (empty($deliverytime_ids) ? '' : ',') . $row['deliverytime_id'];
                }
                
                $dimension_unit = $stock['dimension_unit'];
                $weight_unit    = $stock['weight_unit'];
                
                $this->_assign('stock', $stock);                
            }
        }

        
        if ($stock_id > 0 || !empty($plate_id))        
        {
            // types
            $is_real = 0; $is_virtual= 0; $is_twin = 0; $is_cut = 0;
            foreach ($selected_types as $type)
            {
                if ($type == 'r') $is_real = 1;
                if ($type == 'v') $is_virtual = 1;
                if ($type == 't') $is_twin = 1;
                if ($type == 'c') $is_cut = 1;
                $this->_assign('type_' . $type, true);
            }

            $items  = new SteelItem();
            $list   = $items->GetList($stock_id, $location_ids, $deliverytime_ids, $is_real, $is_virtual, $is_twin, $is_cut, 
                                        $steelgrade_id, $thickness, $width, $length, $weight, $notes, $plate_id, $available,
                                        $dimension_unit, $weight_unit, $order_id);

            $this->_assign('locations',     $stock_locations);
            $this->_assign('deliverytimes', $stock_deliverytimes);
            $this->_assign('steelgrades',   $stock_steelgrades);
            $this->_assign('orders',        $orders);
            $this->_assign('list',          $list);
            
            $total_qtty                 = 0;
            $total_weight               = 0;
            $total_value                = 0;
            $total_purchase_value       = 0;

            $item_weight_unit           = '';
            $item_weight_unit_count     = 0;
            $item_dimension_unit        = '';
            $item_dimension_unit_count  = 0;
            $item_currency              = '';
            $item_currency_count        = 0;
            $item_price_unit            = '';
            $item_price_unit_count      = 0;
            
            
            foreach ($list as $item)
            {
                $item = $item['steelitem'];
                
                $total_qtty     += 1;
                $total_weight   += $item['unitweight'];                
                $item_value     = $item['unitweight'] * $item['price'];
                
                if ($item['weight_unit'] == 'lb' && $item['price_unit'] == 'cwt')
                {
                    $item_value = $item_value / 100;
                }
                
                $total_value            += $item_value;
                $total_purchase_value   += $item['unitweight'] * $item['purchase_price'];
                
                if ($item_weight_unit != $item['weight_unit'] && !empty($item['weight_unit']))
                {
                    $item_weight_unit = $item['weight_unit'];
                    $item_weight_unit_count++;
                }
                
                if ($item_dimension_unit != $item['dimension_unit'] && !empty($item['dimension_unit']))
                {
                    $item_dimension_unit = $item['dimension_unit'];
                    $item_dimension_unit_count++;
                }
                
                if ($item_currency != $item['currency'] && !empty($item['currency']))
                {
                    $item_currency = $item['currency'];
                    $item_currency_count++;
                }                
                
                if ($item_price_unit != $item['price_unit'] && !empty($item['price_unit']))
                {
                    $item_price_unit = $item['price_unit'];
                    $item_price_unit_count++;
                }                
            }
            
            $this->_assign('item_weight_unit',          $item_weight_unit);
            $this->_assign('item_weight_unit_count',    $item_weight_unit_count);
            $this->_assign('item_dimension_unit',       $item_dimension_unit);
            $this->_assign('item_dimension_unit_count', $item_dimension_unit_count);
            $this->_assign('item_currency',             $item_currency);
            $this->_assign('item_currency_count',       $item_currency_count);
            $this->_assign('item_price_unit',           $item_price_unit);
            $this->_assign('item_price_unit_count',     $item_price_unit_count);
            
            $this->_assign('total_qtty',                $total_qtty);
            $this->_assign('total_weight',              $total_weight);
            $this->_assign('total_value',               $total_value);
            $this->_assign('total_purchase_value',      $total_purchase_value);            
        }
        

        $this->_assign('product_id',    $product_id);
        $this->_assign('stock_id',      $stock_id);
        $this->_assign('steelgrade_id', $steelgrade_id);
        $this->_assign('plate_id',      $plate_id);
        $this->_assign('thickness',     $thickness);
        $this->_assign('width',         $width);
        $this->_assign('length',        $length);
        $this->_assign('weight',        $weight);
        $this->_assign('notes',         $notes);
        $this->_assign('available',     $available);
        $this->_assign('order_id',      $order_id);
        $this->_assign('include_ui',    true);
        
        if (!empty($thickness) || !empty($width) || !empty($length) || !empty($steelgrade_id) || !empty($weight) || !empty($notes) || !empty($order_id))
        {
            $this->_assign('params', true);
        }
        
        $this->js       = 'item_index';
        $this->context  = true;
        
        // 20120812, zharkov: формирует название документа
        if (!empty($target_doc))
        {
            $back_title = '';
            $save_title = 'Add Selected';
            
            if ($target_doc == 'qc')
            {
                $back_title         = 'Go to QC';
                $this->page_name    = 'Items for QC';
            }
            else if ($target_doc == 'invoice')
            {
                $back_title         = 'Go to Invoice';
                $this->page_name    = 'Items for Invoice';
                
                $modelInvoice = new Invoice();
                $this->_assign('invoice', $modelInvoice->GetById($target_doc_id));
            }
            else if ($target_doc == 'supinvoice')
            {
                $back_title         = 'Go to Invoice';
                $this->page_name    = 'Items for Supplier Invoice';
                
                $modelSupInvoice = new SupplierInvoice();
                $this->_assign('supinvoice', $modelSupInvoice->GetById($target_doc_id));
            }
            else if ($target_doc == 'inddt')
            {
                $back_title         = 'Go to DDT';
                $this->page_name    = 'Items for Incoming DDT';
            }
            else if ($target_doc == 'ra')
            {
                $back_title         = 'Go to RA';
                $this->page_name    = 'Items for RA';
                
                $modelRA = new RA();
                $this->_assign('ra', $modelRA->GetById($target_doc_id));                
            }
            else if ($target_doc == 'oc')
            {
                $back_title         = 'Go to Original QC';
                $this->page_name    = 'Items for Original Certificate';
            }
            
            $this->_assign('back_title', $back_title);
            $this->_assign('save_title', $save_title);
            
            $this->_assign('target_doc', $target_doc);
            $this->_assign('target_doc_id', $target_doc_id);            
        }
        
        $modelSteelItem = new SteelItem();
        $this->_assign('baddatastat', $modelSteelItem->GetBadDataStat());
        
        if ($_SESSION['user']['id'] == '1682' || $_SESSION['user']['id'] == '1705' || $_SESSION['user']['id'] == '1671') {
            $this->_display('indexmod');  
        } else {
            $this->_display('index');  
        }
    
     //   $this->_display('indexmod');
}
    
        
    
    /**
     * Переносит айтемы из одной позиции в другую
     * url: /item/move/{ids}
     * url: /position/{position_id}/item/move/{ids}
     * 
     * @version 20121217, zharkov: запрет на перемещение реальных айтемов
     */
    function move()
    {
        $ref            = Request::GetString('ref', $_REQUEST);
        $position_id    = Request::GetInteger('position_id', $_REQUEST);
        $stock_id       = 0;
        $location_id    = 0;
        $steelgrade_id  = 0;

        if ($ref == 'positions' && $position_id > 0)
        {
            $positions  = new SteelPosition();
            $position   = $positions->GetById($position_id);
            
            if (empty($position)) _404();
            
            $back_url = $this->_get_previous_url_by_part('positions/filter');
            $back_url = empty($back_url) ? 'positions' : $back_url;
            
            $this->breadcrumb = array(
                'Positions'             => '/positions',
                'Filtered Positions'    => '/' . $back_url
            );
        }
        else
        {
            $back_url = $this->_get_previous_url_by_part('items/filter');
            $back_url = empty($back_url) ? 'items' : $back_url;
            
            $this->breadcrumb = array(
                'Items'             => '/items',
                'Filtered Items'    => '/' . $back_url
            );            
        }

        $item_ids = Request::GetString('id', $_REQUEST);
        $item_ids = explode(',', $item_ids);

        if (empty($item_ids)) _404();

        $ids = array();
        foreach ($item_ids as $item_id) $ids[] = array('steelitem_id' => $item_id);
        
        $items  = new SteelItem();
        $list   = $items->GetByIds($ids);

        if (isset($_REQUEST['btn_move']))
        {
            $form = $_REQUEST['form'];
                        
            $selected_items     = isset($form['items']) ? $form['items'] : null;
            $new_position_id    = Request::GetInteger('new_position_id', $form);
            $location_id        = Request::GetInteger('location_id', $form);
            $stock_id           = Request::GetInteger('stock_id', $form);
            $steelgrade_id      = Request::GetInteger('steelgrade_id', $form);
            $thickness          = Request::GetNumeric('thickness', $form);
            $width              = Request::GetNumeric('width', $form);
            $length             = Request::GetNumeric('length', $form);
            
            $this->_assign('form', $form);
            
            if (empty($stock_id))
            {
                $this->_message('Stock must be specified !', MESSAGE_ERROR);
            }
            else if (empty($location_id))
            {
                $this->_message('Location must be specified !', MESSAGE_ERROR);
            }
            else if (empty($selected_items))
            {
                $this->_message('Items must be selected !', MESSAGE_ERROR);
            }
            else
            {
                // testing items here
                $no_errors = true;
                foreach ($selected_items as $item_id)
                {
                    $item = $items->GetById($item_id);
                    if (empty($item))
                    {
                        $this->_message('Item # ' . $item_id . ' undefined !', MESSAGE_ERROR);
                        $no_errors = false;
                        
                        break;                        
                    }
                    
                    $item = $item['steelitem'];
                    
                    if ($item['status_id'] >= ITEM_STATUS_RELEASED)
                    {
                        $this->_message('Cannot move Item # ' . $item_id . ' !', MESSAGE_ERROR);
                        $no_errors = false;                        
                        
                        break;
                    }
                }                    

                if ($no_errors && empty($new_position_id))
                {                   
                    $new_position   = $_REQUEST['new_position'];
                
                    $steelgrade_id  = Request::GetInteger('steelgrade_id', $new_position);
                    $biz_id         = Request::GetInteger('biz_id', $new_position);
                    $supplier_id    = Request::GetInteger('supplier_id', $new_position);
                    $product_id     = 92;
                    
                    $thickness      = Request::GetNumeric('thickness', $new_position);
                    $width          = Request::GetNumeric('width', $new_position);
                    $length         = Request::GetNumeric('length', $new_position);
                    $unitweight     = Request::GetNumeric('unitweight', $new_position);
                    $qtty           = 0; // 20120108, zharkov: потому что в ХП прибавляется количество айтемов Request::GetInteger('qtty', $new_position);
                    $weight         = Request::GetNumeric('weight', $new_position);
                    $price          = Request::GetNumeric('price', $new_position);
                    $value          = Request::GetNumeric('value', $new_position);
                    $delivery_time  = Request::GetString('delivery_time', $new_position);
                    $notes          = Request::GetString('notes', $new_position);
                    $internal_notes = Request::GetString('internal_notes', $new_position);
                    
                    if (empty($steelgrade_id))
                    {
                        $this->_message('I forgot to specify Steel Grade !', MESSAGE_ERROR);
                        $no_errors = false;
                    }
                    else if (empty($biz_id))
                    {
                        $this->_message('I forgot to specify Biz Id !', MESSAGE_ERROR);
                        $no_errors = false;
                    }
                    else
                    {
                        $stocks = new Stock();
                        $stock  = $stocks->GetById($stock_id);
                        $stock  = $stock['stock'];
                        
                        
                        $modelSteelPosition = new SteelPosition();
                        $result             = $modelSteelPosition->Save(0, $stock_id, $product_id, $biz_id, $location_id, $supplier_id, 
                                                        $stock['dimension_unit'], $stock['weight_unit'], $stock['price_unit'], 
                                                        $stock['currency'], $steelgrade_id, $thickness, $width, $length, $unitweight, $qtty, 
                                                        $weight, $price, $value, $delivery_time, $notes, $internal_notes);
                        if (empty($result))
                        {
                            $this->_message('Error creating new position !', MESSAGE_ERROR);
                            $no_errors = false;
                        }
                        else
                        {
                            $new_position_id = $result['id'];                        
                        }                        
                    }
                }
                

                if ($no_errors)
                {
                    $no_errors = true;
                    foreach ($selected_items as $item_id)
                    {
                        $result = $items->Move($item_id, $location_id, $new_position_id, $position_id);    
                        
                        if (empty($result)) 
                        {                            
                            $no_errors = false;
                            break;
                        }
                    }
                    
                    if (!$no_errors)
                    {
                        $this->_message('Error moving selected items !', MESSAGE_ERROR);
                    }
                }

                if ($no_errors)
                {
                    $this->_message('Items was successfully moved !', MESSAGE_OKAY);                    
                    $this->_redirect(explode('/', $back_url), false);
                }                
            }
        }

        
        $thickness_min  = 999999;
        $thickness_max  = 0;
        $width_min      = 999999;
        $width_max      = 0;
        $length_min     = 999999;
        $length_max     = 0;
        
        $released_count = 0;
        $alias_count    = 0;
        foreach ($list as $item)
        {
            $item = $item['steelitem'];
            
            $thickness_min = min($thickness_min, $item['thickness_mm']);
            $thickness_max = max($thickness_max, $item['thickness_mm']);
            
            $width_min = min($width_min, $item['width_mm']);
            $width_max = max($width_max, $item['width_mm']);

            $length_min = min($length_min, $item['length_mm']);
            $length_max = max($length_max, $item['length_mm']);            
            
            if ($item['stock_id'] >= ITEM_STATUS_RELEASED) $released_count++;
            if ($item['parent_id'] > 0) $alias_count++;
        }
        
        $this->_assign('released_count', $released_count);
        $this->_assign('alias_count', $alias_count);
        
        $thickness_min = str_replace('.0', '', number_format($thickness_min, 1, '.', ','));
        $thickness_max = str_replace('.0', '', number_format($thickness_max, 1, '.', ','));        
        $this->_assign('items_thickness', ($thickness_min != $thickness_max ? $thickness_min . ' - ' . $thickness_max : $thickness_min));

        $width_min = str_replace('.0', '', number_format(($width_min - 50), 1, '.', ','));
        $width_max = str_replace('.0', '', number_format(($width_max + 50), 1, '.', ','));        
        $this->_assign('items_width', ($width_min . ' - ' . $width_max));
        
        $length_min = str_replace('.0', '', number_format(($length_min - 100), 1, '.', ','));
        $length_max = str_replace('.0', '', number_format(($length_max + 100), 1, '.', ','));        
        $this->_assign('items_length', ($length_min . ' - ' . $length_max));
        
        $this->_assign('list',              $list);
        $this->_assign('items_count',       count($list));               
        
        $modelStock = new Stock();
        $this->_assign('stocks',        $modelStock->GetList());
        $this->_assign('steelgrades',   $modelStock->GetSteelgrades($stock_id));
        $this->_assign('locations',     $modelStock->GetLocations($stock_id, true));
        
        if ($stock_id > 0)
        {
            $stock = $modelStock->GetById($stock_id);
            if (!empty($stock)) $this->_assign('stock', $stock['stock']);
            
            //postback
            $modelSteelPosition = new SteelPosition();
            $this->_assign('positions', $modelSteelPosition->GetList(0, $stock_id, '', '', $steelgrade_id, $thickness, 0, 0, $width, 0, 0, $length, 0, 0, 0, 0, 0, '', '', ''));
        }
        
        //postback
        if (isset($_REQUEST['new_position']))
        {
			$new_position = $_REQUEST['new_position'];

            $this->_assign('steelgrade_id',     Request::GetInteger('steelgrade_id',    $new_position));
            $this->_assign('thickness',         Request::GetNumeric('thickness',        $new_position));
            $this->_assign('width',             Request::GetNumeric('width',            $new_position));
            $this->_assign('length',            Request::GetNumeric('length',           $new_position));
            $this->_assign('unitweight',        Request::GetNumeric('unitweight',       $new_position));
            $this->_assign('price',             Request::GetNumeric('price',            $new_position));
            $this->_assign('weight',            Request::GetNumeric('weight',           $new_position));
            $this->_assign('value',             Request::GetNumeric('value',            $new_position));
            $this->_assign('delivery_time',     Request::GetString('delivery_time',     $new_position));
            $this->_assign('notes',             Request::GetString('notes',             $new_position));
            $this->_assign('internal_notes',    Request::GetString('internal_notes',    $new_position));
            $this->_assign('biz_title',         Request::GetString('biz_title',         $new_position));
            $this->_assign('biz_id',            Request::GetString('biz_id',            $new_position));
        }
        
        $this->_assign('stock_id',      $stock_id);
        $this->_assign('location_id',   $location_id);
       
        $this->page_name = 'Move Items';
        $this->breadcrumb[$this->page_name] = '';
        
        $this->_assign('position_id',   $position_id);        
        $this->_assign('back_url',      $back_url);
        $this->_assign('include_ui',    true);
        
        $this->context  = true;
        $this->js       = 'item_move';
        
        $this->_display('move');        
    }
    
    /**
     * Отображает страницу с историей изменения айтема
     * url: /item/history/{item_id}
     */
    function history()
    {
        $ref            = Request::GetString('ref', $_REQUEST);
        $position_id    = Request::GetInteger('position_id', $_REQUEST);        
        $item_id        = Request::GetInteger('id', $_REQUEST);
        
        $steelitems = new SteelItem();
        $list       = $steelitems->GetHistory($item_id);
        
        foreach ($list as $key => $row)
        {
            $list[$key]['revision'] = base64_encode($row['item_history_id'] . '-' . $row['item_properties_history_id'] . '-itemrevision-' . (count($list) - $key));
            
            if (!isset($list[$key + 1])) break;            
            if 
            (
                $row['thickness_measured'] != $list[$key + 1]['thickness_measured'] ||
                $row['width_measured'] != $list[$key + 1]['width_measured'] ||
                $row['width_max'] != $list[$key + 1]['width_max'] ||
                $row['length_measured'] != $list[$key + 1]['length_measured'] ||
                $row['length_max'] != $list[$key + 1]['length_max'] ||                
                //$row['price'] != $list[$key + 1]['price'] ||
                //$row['unitweight'] != $list[$key + 1]['unitweight'] ||
                $row['unitweight_measured'] != $list[$key + 1]['unitweight_measured'] ||
                $row['unitweight_weighed'] != $list[$key + 1]['unitweight_weighed']
                
            )   $list[$key]['changes_in_dimensions'] = true;
            
            if 
            (
                $row['supplier_id'] != $list[$key + 1]['supplier_id'] ||
                $row['supplier_invoice_no'] != $list[$key + 1]['supplier_invoice_no'] ||
                $row['supplier_invoice_date'] != $list[$key + 1]['supplier_invoice_date'] ||
                $row['purchase_price'] != $list[$key + 1]['purchase_price'] ||
                $row['purchase_value'] != $list[$key + 1]['purchase_value'] ||
                $row['in_ddt_number'] != $list[$key + 1]['in_ddt_number'] ||
                $row['in_ddt_date'] != $list[$key + 1]['in_ddt_date'] ||
                $row['ddt_number'] != $list[$key + 1]['ddt_number'] ||
                $row['ddt_date'] != $list[$key + 1]['ddt_date'] ||
                $row['notes'] != $list[$key + 1]['notes'] ||
                $row['internal_notes'] != $list[$key + 1]['internal_notes'] ||
                $row['owner_id'] != $list[$key + 1]['owner_id'] ||
                $row['status_id'] != $list[$key + 1]['status_id'] ||
                //$row['is_virtual'] != $list[$key + 1]['is_virtual'] ||
                $row['mill'] != $list[$key + 1]['mill'] ||
                $row['system'] != $list[$key + 1]['system'] ||
                $row['current_cost'] != $list[$key + 1]['current_cost'] ||
                $row['pl'] != $list[$key + 1]['pl'] ||
                $row['load_ready'] != $list[$key + 1]['load_ready']            
            )   $list[$key]['changes_in_status'] = true;

            if
            (
                $row['heat_lot'] != $list[$key + 1]['heat_lot'] ||
                $row['c'] != $list[$key + 1]['c'] ||
                $row['si'] != $list[$key + 1]['si'] ||
                $row['mn'] != $list[$key + 1]['mn'] ||
                $row['p'] != $list[$key + 1]['p'] ||
                $row['s'] != $list[$key + 1]['s'] ||
                $row['cr'] != $list[$key + 1]['cr'] ||
                $row['ni'] != $list[$key + 1]['ni'] ||
                $row['cu'] != $list[$key + 1]['cu'] ||
                $row['al'] != $list[$key + 1]['al'] ||
                $row['mo'] != $list[$key + 1]['mo'] ||
                $row['nb'] != $list[$key + 1]['nb'] ||
                $row['v'] != $list[$key + 1]['v'] ||
                $row['n'] != $list[$key + 1]['n'] ||
                $row['ti'] != $list[$key + 1]['ti'] ||
                $row['sn'] != $list[$key + 1]['sn'] ||
                $row['b'] != $list[$key + 1]['b'] ||
                $row['ceq'] != $list[$key + 1]['ceq']
            )   $list[$key]['changes_in_chemical'] = true;
            
            if 
            (
                $row['tensile_sample_direction'] != $list[$key + 1]['tensile_sample_direction'] ||
                $row['tensile_strength'] != $list[$key + 1]['tensile_strength'] ||
                $row['yeild_point'] != $list[$key + 1]['yeild_point'] ||
                $row['elongation'] != $list[$key + 1]['elongation'] ||
                $row['reduction_of_area'] != $list[$key + 1]['reduction_of_area'] ||
                $row['test_temp'] != $list[$key + 1]['test_temp'] ||
                $row['impact_strength'] != $list[$key + 1]['impact_strength'] ||
                $row['hardness'] != $list[$key + 1]['hardness'] ||
                $row['ust'] != $list[$key + 1]['ust'] ||
                $row['sample_direction'] != $list[$key + 1]['sample_direction'] ||
                $row['stress_relieving_temp'] != $list[$key + 1]['stress_relieving_temp'] ||
                $row['heating_rate_per_hour'] != $list[$key + 1]['heating_rate_per_hour'] ||
                $row['holding_time'] != $list[$key + 1]['holding_time'] ||
                $row['cooling_down_rate'] != $list[$key + 1]['cooling_down_rate'] ||
                $row['condition'] != $list[$key + 1]['condition'] ||
                $row['normalizing_temp'] != $list[$key + 1]['normalizing_temp']
                
            )   $list[$key]['changes_in_mechanical'] = true;
            
            if ($row['guid'] != $list[$key + 1]['guid']) $list[$key]['guid_changed'] = true;
            if ($row['steelgrade_id'] != $list[$key + 1]['steelgrade_id']) $list[$key]['steelgrade_changed'] = true;
            if ($row['thickness'] != $list[$key + 1]['thickness']) $list[$key]['thickness_changed'] = true;
            if ($row['width'] != $list[$key + 1]['width']) $list[$key]['width_changed'] = true;
            if ($row['length'] != $list[$key + 1]['length']) $list[$key]['length_changed'] = true;
            if ($row['unitweight'] != $list[$key + 1]['unitweight']) $list[$key]['unitweight_changed'] = true;
        }
        
//        dg($list);
        

        //$stocks = new Stock();
        //$stock  = $stocks->GetById($list[0]['stock_id']);        
        
        if ($ref == 'positions' && $position_id > 0)
        {
            $positions  = new SteelPosition();
            $position   = $positions->GetById($position_id);
            
            //if (empty($position)) _404();
            
            $back_url = $this->_get_previous_url_by_part('positions/filter');
            $back_url = empty($back_url) ? 'positions' : $back_url;
            
            $this->breadcrumb = array(
                'Positions'             => '/positions',
                'Filtered Positions'    => '/' . $back_url
            );
        }
        else
        {
            $back_url = $this->_get_previous_url_by_part('items/filter');
            $back_url = empty($back_url) ? 'items' : $back_url;
            
            $this->breadcrumb = array(
                'Items'             => '/items',
                'Filtered Items'    => '/' . $back_url
            );            
        }

        $this->page_name = 'Item History';
        $this->breadcrumb[$this->page_name] = '';
        
        $this->_assign('list',      $list);
        $this->_assign('back_url',  $back_url);
        
        $this->context = true;
        
        $this->_display('history');
    }    
    
    
    function history1()
    {
        $this->_display('history1');
    }
    
    /**
     * Выводит историю жизни айтема
     * url: /item/{id}/timeline
     * 
     * @version 20121218, d10n
     */
    public function timeline()
    {
        $steelitem_id = Request::GetInteger('id', $_REQUEST);        
        if ($steelitem_id <= 0) _404();
        
        $modelSteelItem = new SteelItem();        
        $steelitem      = $modelSteelItem->GetById($steelitem_id);        
        if (!isset($steelitem['steelitem'])) _404();
        
        $steelitem = $steelitem['steelitem'];
        
        $list = $modelSteelItem->TimelineGetList($steelitem['id']);
        
        $this->_assign('timeline', $list);
        
        $this->breadcrumb = array(
            'Items'                 => '/items',
//            $steelitem['doc_no']    => '/item/' . $steelitem['id'],
        );

        $this->page_name = $steelitem['doc_no'] . ' Timeline';
        $this->breadcrumb[$this->page_name] = '';        
        
        $this->context = true;
        $this->_display('timeline');
    }
	
	/**
	* Выводит данные по складу из истории изменений
	* url: /item/audit/filter/on_date:{on_date};location:{location};
	*
	* @version 20140331, Sergey Gonchar
	*/
	public function audit()
	{
        if (isset($_REQUEST['btn_select']))
        {
            $form = $_REQUEST['form'];
            
            $locations  = '';
            if (isset($form['stockholder']))
            {
                foreach ($form['stockholder'] as $key => $location_id)
                {
                    $location_id = Request::GetInteger($key, $form['stockholder']);
                    if ($location_id <= 0) continue;
                    
                    $location_ids = $location_ids . (empty($location_ids) ? '' : ',') . $location_id;
                }
            }
			
			$dateto = '';
			if(isset($form['dateto'])) $dateto = Request::GetDateForDB('dateto', $form);
			
			if($location_ids == '') $this->_message('Please select location(s)');
			if($dateto == '') $this->_message('Please select date to');
			$modelSteelItem = new SteelItem(); 
			//dg($location_ids);
			$list = $modelSteelItem->GetListStockAudit($location_ids, $dateto);
		} else {
			$list = array();
		}
		
		$stocksModel = new Stock;
		$stocks = $stocksModel->GetList();
		
		if(!empty($form['stock_id'])) {
			$stock_locations    = $stocksModel->GetLocations($form['stock_id'], false);
			for($i = 0; $i <= count($stock_locations); $i++)
			{
                foreach ($form['stockholder'] as $key => $location_id)
                {
                    $location_id = Request::GetInteger($key, $form['stockholder']);
                    if ($location_id <= 0) continue;
                    
					if($stock_locations[$i]['company']['id']==$location_id) $stock_locations[$i]['company']['selected']=true;           
                }
			}
			//dg($stock_locations);
			$this->_assign('locations', $stock_locations);
		}
		
		$today = date('d/m/Y');
        $modelCompany = new Company();
        $this->_assign('owners', $modelCompany->GetMaMList());		
		//dg($modelCompany->GetMaMList());
		$this->_assign('stocks', $stocks);
		
		//$stock_id = $form['stock_id'];
		$this->_assign('stock_id', $form['stock_id']);
		$this->_assign('date_to', $form['dateto']);
		$this->_assign('today', $today);
		
		$this->_assign('list', $list);
		$this->_display('audit');
	}
	
}
