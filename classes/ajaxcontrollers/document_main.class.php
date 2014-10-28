<?php
require_once APP_PATH . 'classes/models/inddt.class.php';
require_once APP_PATH . 'classes/models/invoice.class.php';
require_once APP_PATH . 'classes/models/oc.class.php';
require_once APP_PATH . 'classes/models/order.class.php';
require_once APP_PATH . 'classes/models/preorder.class.php';
require_once APP_PATH . 'classes/models/qc.class.php';
require_once APP_PATH . 'classes/models/ra.class.php';
require_once APP_PATH . 'classes/models/stockoffer.class.php';
require_once APP_PATH . 'classes/models/supplierinvoice.class.php';


class MainAjaxController extends ApplicationAjaxController
{    

    function MainAjaxController()
    {
        ApplicationAjaxController::ApplicationAjaxController();
                
        $this->authorize_before_exec['additems']        = ROLE_STAFF;
        $this->authorize_before_exec['addpositions']    = ROLE_STAFF;
        $this->authorize_before_exec['removeitem']      = ROLE_STAFF;
    }
        
    /**
     * add positions into document
     * url: /document/addpositions
     * 
     * @version 20121023, d10n: RA added
     * @version 20120822, zharkov
     */
    function addpositions()
    {
        $doc_alias      = Request::GetString('doc_alias', $_REQUEST);
        $doc_id         = Request::GetString('doc_id', $_REQUEST);
        $stock_id       = Request::GetInteger('stock_id', $_REQUEST);
        $item_ids       = Request::GetString('item_ids', $_REQUEST);
        $position_ids   = Request::GetString('position_ids', $_REQUEST);
        $href           = '';

        // stock is undefined, throw error // не указан склад, выдаем ошибку
        if (empty($stock_id)) $this->_send_json(array('result' => 'error'));            

        // positions or items undefined, throw error // не выбраны позиции или айтемы
        if (empty($item_ids) && empty($position_ids)) $this->_send_json(array('result' => 'error'));            

        
        if ($doc_alias == 'order')
        {
            $orders = new Order();            
            $order  = $orders->GetById($doc_id);
            
            // cannot add into cancelled or completed or undefined order // нельзя добавлять в несуществующий, отмененный, выполненный заказ            
            if (empty($order) || in_array($order['order']['status'], array('ca', 'co'))) $this->_send_json(array('result' => 'error'));            
            
            // create positions list for add with qtty and put items into order // формирует список добавляемых позиций с количеством и добавляет айтемы в заказ
            $positions_list = array();            
            
            // positions list need to be refresh // список позиций, которые необходимо обновить
            $positions_to_update = array();
            
            if (!empty($item_ids))
            {
                $items_list     = array();
                $steelitems     = new SteelItem();
                foreach(explode(',', $item_ids) as $item_id)
                {
                    $item           = $steelitems->GetById($item_id);
                    $position_id    = $item['steelitem']['steelposition_id'];
                    
                    // check item availability // проверка свободности айтема, чтобы нельзя было добавить айтем, который уже в заказе
                    if ($item['steelitem']['is_locked'] > 0) continue;
                    
                    if (array_key_exists($position_id, $positions_list))
                    {
                        $positions_list[$position_id]['qtty'] = $positions_list[$position_id]['qtty'] + 1;
                    }
                    else
                    {
                        $positions_list[$position_id] = array('qtty' => 1);
                    }
                    
                    // put item into order // добавляет айтем в заказ
                    $res = $orders->AddItem($doc_id, $position_id, $item_id);
                    // 20120904, zharkov
                    $positions_to_update = array_merge($positions_to_update, $res);                    
                }
            }
            
            // 20120904, zharkov
            $positions_to_update = array_flip($positions_to_update);            

            if (!empty($position_ids) || !empty($positions_list))
            {
                if (!empty($position_ids))
                {
                    // if positions ids specified // если заданы идентификаторы добавляемых позиций
                    $steepositions = new SteelPosition();
                    foreach(explode(',', $position_ids) as $position_id)
                    {
                        if (!array_key_exists($position_id, $positions_list))
                        {
                            $steeposition                   = $steepositions->GetById($position_id);
                            $positions_list[$position_id]   = array('qtty' => $steeposition['steelposition']['qtty']);
                        }
                    }                    
                }
                
                // add position to order // добавляет позиции к заказу
                $orders = new Order();
                foreach($positions_list as $position_id => $options)
                {
                    //20120830, zharkov: check position available qtty // проверка доступности количества позиции, чтобы нельзя было добавить больше чем есть
                    $result = $orders->TestPositionQtty($doc_id, $position_id, $options['qtty'], $order['order']['status']);
                    if ($result['available'] == false) continue;
                    
                    // add position to order // добавляет позицию в заказ
                    $orders->PositionAddFromStock($doc_id, $position_id, $options['qtty']);
                    
                    $modelSteelPosition = new SteelPosition();
                    $position           = $modelSteelPosition->GetById($position_id);
                    $position           = $position['steelposition'];
                    
                    // on position add, existing positions must be stored in the session // при добавлении позиций из заказа, текущие позиции заказа сохраняются в сессии, и данные о добавляемых нужно сохранить
                    if (isset($_SESSION['order-' . $doc_id]) && isset($_SESSION['order-' . $doc_id]['positions']))
                    {
                        foreach ($_SESSION['order-' . $doc_id]['positions'] as $key => $row)
                        {
                            if ($row['position_id'] == $position_id)
                            {
                                $qtty   = $row['qtty'] + $options['qtty'];
                                $value  = $row['unitweight'] * $row['price'] * $qtty;
                                
                                if ($position['weight_unit'] == 'lb' && $position['price_unit'] == 'cwt')
                                {
                                    $value = $value / 100;
                                }
                                
                                $_SESSION['order-' . $doc_id]['positions'][$key]['qtty']      = $qtty;
                                $_SESSION['order-' . $doc_id]['positions'][$key]['weight']    = $row['unitweight'] * $qtty;
                                $_SESSION['order-' . $doc_id]['positions'][$key]['value']     = $value;
                                
                                break;
                            }
                        }
                    }
                    
                    // 20120904, zharkov
                    unset($positions_to_update[$position_id]);                    
                }
            }
            
            // 20120904, zharkov: update related items // обновляет косвенные позиции, те, которые формально не ушли в заказ (с родственными айтемами)
            $steepositions = new SteelPosition();
            foreach ($positions_to_update as $position_id => $row)
            {
                $steepositions->UpdateQtty($position_id);
            }            

            $href = '/order/' . $doc_id . '/edit';
        }
        else if ($doc_alias == 'neworder')
        {
            $preorder = new PreOrder();
                        
            // add items to order // добавляет айтемы к заказу
            if (!empty($item_ids))
            {
                $positions = $preorder->ItemsAddFromStock($doc_id, $item_ids);

                foreach ($positions as $row) $position_ids .= ',' . $row['steelposition_id'];
                $position_ids = trim($position_ids, ',');
            }
            
            // add positions to order // добавляет позиции к заказу
            if (!empty($position_ids))
            {
                $preorder->PositionsAddFromStock($doc_id, $position_ids);
            }            
            
            $href = '/order/neworder/' . $doc_id;
        }
        else if ($doc_alias == 'ra')
        {
            // throw error if no item or position specified // если не выбран ни айтем, ни позиция - выводим ошибку
            if (empty($item_ids) && empty($position_ids))
            {
                $this->_send_json(array('result' => 'error'));
            }
            
            $modelSteelPosition = new SteelPosition();
            foreach (explode(',', $position_ids) AS $position_id)
            {
                foreach ($modelSteelPosition->GetItems($position_id, true) as $row)
                {
                    $item_ids .= ',' . $row['steelitem_id'];
                }
            }
            
            $item_ids = trim($item_ids, ',');
            if (count(explode(',', $item_ids)) > 20)
            {
                $this->_send_json(array('result' => 'error', 'message' => 'To many items selected'));
            }
            
            $modelRA = new RA();
            $modelRA->ItemsAdd(0, $doc_id, $item_ids);
            
            $href = '/ra/' . $doc_id . '/edit';
        }
        else if ($doc_alias == 'newra')
        {
            
            // throw error if no item or position specified // если не выбран ни айтем, ни позиция - выводим ошибку
            if (empty($item_ids) && empty($position_ids))
            {
                $this->_send_json(array('result' => 'error'));
            }
            
            $modelSteelPosition = new SteelPosition();
            foreach (explode(',', $position_ids) AS $position_id)
            {
                foreach ($modelSteelPosition->GetItems($position_id, true) as $row)
                {
                    $item_ids .= ',' . $row['steelitem_id'];
                }
            }
            
            $item_ids = trim($item_ids, ',');
            if (count(explode(',', $item_ids)) > 20)
            {
                $this->_send_json(array('result' => 'error', 'message' => 'To many items selected'));
            }
            
            $steelitems = array();
            foreach (explode(',',$item_ids) as $item_id)
            {
                $steelitems[] = array('steelitem_id' => $item_id);
            }
            
            $modelSteelItem = new SteelItem();
            $steelitems     = $modelSteelItem->FillSteelItemInfo($steelitems);
            //debug('1682', $steelitems);
            
            // filter items by stockholder // фильтр айтемов по стокхолдерам
            $stockholders   = array();
            foreach ($steelitems as $row)
            {
                $row            = $row['steelitem'];
                $stockholder_id = $row['stockholder_id'];
                
                if (empty($stockholder_id) || empty($row['owner_id']) || $row['status_id'] >= ITEM_STATUS_RELEASED)
                {
                    continue;    
                }

                if (!isset($stockholders[$stockholder_id]))
                    {
                        $stockholders[$stockholder_id] = array(
                            'stock_object_alias'    => (isset($row['stockholder']) && $row['stockholder']['country_id'] == 225 ? 'platesahead' : 'mam'),
                            'items'                 => array(),
                        );
                    }
                
                $stockholders[$stockholder_id]['items'][] = $row;
            }
            
            if (empty($stockholders))
            {
                $this->_send_json(array('result' => 'error', 'message' => 'Owner or location is missing or items were already released !'));
            }
            
            
            $modelRA = new RA();
            // save // сохранение
            foreach ($stockholders as $stockholder_id => $row)
            {
                //print_r($row);
                $notes = $row['stock_object_alias'] == 'platesahead'
                    ? 'Please be so kind to state actual dimensions in your bill of lading as well as plate ID'
                    : 'Please send to us DDT & weighbridge ticket as soon as issued';
                //debug('1682', $row['items']);
                
                $result     = $modelRA->Save(0, $stockholder_id, 0, 0, '', '', '', '', '', RA_STATUS_OPEN, '', '', '', '', '', '', $notes);
                $item_ids   = '';
                foreach ($row['items'] as $item)
                {
                    $item_ids .= ',' . $item['id'];
                }
                
                $item_ids = trim($item_ids, ',');
                $modelRA->ItemsAdd(0, $result['ra_id'], $item_ids);
                
                //костыль - записываю в табл. ra_items owner_id выбранных итемов
                foreach ($row['items'] as $item)
                {
                    $steelitem_id = $item['id'];
                    $owner_id = $item['owner_id'];
                    $ra_id = $result['ra_id'];
                    //сохраняю ids  в БД
                    $query  = '';
                    $query .= "UPDATE `ra_items` ";
                    $query .= "SET `owner_id`     = '{$owner_id}' ";
                    $query .= "WHERE `ra_id`      = '{$ra_id}' ";
                    $query .= "AND `steelitem_id` = '{$steelitem_id}'";
                    $modelRA->table->_exec_raw_query($query);
                }
            }
            
            $href = '/ra/' . $result['ra_id'] . '/edit';
        }
        else if ($doc_alias == 'stockoffer')
        {
            // throw error if no position specified // если не выбраны позиции - выводим ошибку
            if (empty($position_ids))
            {
                $this->_send_json(array('result' => 'error'));
            }
            
            $position_ids   = trim($position_ids, ',');
            $exploded_ids   = explode(',', $position_ids);

            $modelStockOffer = new StockOffer();
            foreach ($exploded_ids as $position_id)
            {
                $modelStockOffer->SavePosition($doc_id, $position_id);
            }
            
            $href = '/stockoffer/' . $doc_id . '/edit';
        }
        else if ($doc_alias == 'alias')
        {
            // throw error if no item or position specified // если не выбран ни айтем, ни позиция - выводим ошибку
            if (empty($item_ids) && empty($position_ids))
            {
                $this->_send_json(array('result' => 'error'));
            }
            
            $item_ids       = trim($item_ids, ',');
            $position_ids   = trim($position_ids, ',');
            $items          = array();
            
            // select only selected items from position // запрещенные позиции: если выбрана позиция и не все айтемы из нее, то остальные айтемы позиции игнорируются
            $forbidden_positions    = array();
            
            $modelSteelItem = new SteelItem();
            foreach (explode(',', $item_ids) as $item_id)
            {
                $steelitem = $modelSteelItem->GetById($item_id);
                if (isset($steelitem) && isset($steelitem['steelitem']))
                {
                    $items_array[]          = $item_id;
                    $forbidden_positions[]  = $steelitem['steelitem']['steelposition_id'];
                }                
            }

            $modelSteelPosition = new SteelPosition();
            foreach (explode(',', $position_ids) AS $position_id)
            {
                if (in_array($position_id, $forbidden_positions)) continue;
                
                foreach ($modelSteelPosition->GetItems($position_id, true) as $row)
                {
                    $items_array[] = $row['steelitem_id'];
                }
            }
                        
            $href = '/item/createalias/' . implode(',', array_unique($items_array));
        }            
        else if ($doc_alias == 'newqc')
        {
            
            // throw error if no item or position specified // если не выбран ни айтем, ни позиция - выводим ошибку
            if (empty($item_ids) && empty($position_ids))
            {
                $this->_send_json(array('result' => 'error'));
            }
            
            $modelSteelPosition = new SteelPosition();
            foreach (explode(',', $position_ids) AS $position_id)
            {
                foreach ($modelSteelPosition->GetItems($position_id, true) as $row)
                {
                    $item_ids .= ',' . $row['steelitem_id'];
                }
            }
            
            $item_ids = trim($item_ids, ',');
            if (count(explode(',', $item_ids)) > 50)
            {
                $this->_send_json(array('result' => 'error', 'message' => 'To many items selected'));
            }
            
            $steelitems = array();
            foreach (explode(',',$item_ids) as $item_id)
            {
                $steelitems[] = array('steelitem_id' => $item_id);
            }
            
            $modelSteelItem = new SteelItem();
            $steelitems     = $modelSteelItem->FillSteelItemInfo($steelitems);
            
            $dimensions     = '';
            if (isset($steelitems[0]) && isset($steelitems[0]['steelitem']))
            {
                $item       = $steelitems[0]['steelitem']; 
                $dimensions = $item['dimension_unit'] . '/' . $item['weight_unit'];
            }

            $modelQC    = new QC();
            $result     = $modelQC->Save(0, '', 0, '', 0, 0, '', 0, '', '', '', 
                    '', '', '', '', '', 
                    '', '', '', '', '', '', 
                    '', '', '', '', 0, 0, $dimensions);
            
            foreach ($steelitems as $item)
            {
                $modelQC->SaveItem($result['id'], $item['steelitem_id']);
            }
            
            $href = '/qc/' . $result['id'] . '/edit';
        }        
        
        $this->_send_json(array(
            'result'    => 'okay', 
            'href'      => $href
        ));
    }
    
    /**
     * Add items into document
     * url: /document/additems
     */
    function additems()
    {
        $doc_alias  = Request::GetString('doc_alias', $_REQUEST);
        $doc_id     = Request::GetInteger('doc_id', $_REQUEST);
        $stock_id   = Request::GetInteger('stock_id', $_REQUEST);
        $item_ids   = Request::GetString('item_ids', $_REQUEST);
        
        // не указан склад, выдаем ошибку
        //if (empty($stock_id)) $this->_send_json(array('result' => 'error'));  2130317, zharkov

        // positions or items undefined
        if (empty($item_ids)) $this->_send_json(array('result' => 'error'));            

        
        if ($doc_alias == 'qc')
        {
            $modelQC = new QC();
            foreach (explode(',', $item_ids) as $item_id) $modelQC->SaveItem($doc_id, $item_id);
        }
        else if($doc_alias == 'invoice')
        {
            $modelInvoice = new Invoice();
            foreach (explode(',', $item_ids) as $item_id) $modelInvoice->SaveItem($doc_id, $item_id);
        }
        else if($doc_alias == 'supinvoice')
        {
            $modelSupInvoice = new SupplierInvoice();
            foreach (explode(',', $item_ids) as $item_id) $modelSupInvoice->SaveItem($doc_id, $item_id, 0);
            
            $doc_alias = 'supplierinvoice';
        }
        else if($doc_alias == 'inddt')
        {
            $modelSteelItem = new SteelItem();            
            $modelInDDT     = new InDDT();
            foreach (explode(',', $item_ids) as $item_id) 
            {
                $item           = $modelSteelItem->GetById($item_id);
                $position_id    = (isset($item) ? $item['steelitem']['steelposition_id'] : 0);

                $modelInDDT->SaveItem($doc_id, $item_id, 0, 0, $position_id);
            }
        }
        else if ($doc_alias == 'ra')
        {
            $item_ids = trim($item_ids, ',');
            if (count(explode(',', $item_ids)) > 20)
            {
                $this->_send_json(array('result' => 'error', 'message' => 'To many items selected'));
            }
            
            if (empty($doc_id))
            {
                $steelitems = array();
                foreach (explode(',',$item_ids) as $item_id)
                {
                    $steelitems[] = array('steelitem_id' => $item_id);
                }
                
                $modelSteelItem = new SteelItem();
                $steelitems     = $modelSteelItem->FillSteelItemInfo($steelitems);

                // groupping items by stockholders // группировка айтемов по стокхолдерам
                $stockholders   = array();
                foreach ($steelitems as $row)
                {
                    $row            = $row['steelitem'];
                    $stockholder_id = $row['stockholder_id'];
                    
                    if (empty($stockholder_id) || empty($row['owner_id']) || $row['status_id'] >= ITEM_STATUS_RELEASED)
                    {
                        continue;
                    }
                                        
                    if (!isset($stockholders[$stockholder_id]))
                        {
                            $stockholders[$stockholder_id] = array(
                                'stock_object_alias'    => (isset($row['stockholder']) && $row['stockholder']['country_id'] == 225 ? 'platesahead' : 'mam'),
                                'items'                 => array(),
                            );
                        }
                    
                    $stockholders[$stockholder_id]['items'][] = $row;
                }
                
                if (empty($stockholders))
                {
                    $this->_send_json(array('result' => 'error', 'message' => 'Owner or location is missing or items were already released !'));
                }
                
                
                // create RA for each stockholder // создание нового RA для каждого stockholder
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
                
                $doc_id = $result['ra_id'];
            }
            else
            {
                $modelRA = new RA();
                $modelRA->ItemsAdd(0, $doc_id, $item_ids);                
            }
                        
            $href = '/ra/' . $doc_id . '/edit';
        }
        else if($doc_alias == 'oc')
        {
            $modelOC = new OC();
            foreach (explode(',', $item_ids) as $item_id) $modelOC->SaveItem($doc_id, $item_id, 0, 0);
        }

        $this->_send_json(array(
            'result'    => 'okay', 
            'href'      => '/' . $doc_alias . '/' . (empty($doc_id) ? 'add' : $doc_id . '/edit')
        ));
    }
    
    /**
     * remove item from document // Удаляет item из документа
     * url: /document/removeitem
     * 
     * @version 20121213, d10n
     * @deprecated 20121218, zharkov
     * @version 20130215, d10n: undepricated
     */
    public function removeitem()
    {
        $doc_alias      = Request::GetString('doc_alias', $_REQUEST);
        $doc_id         = Request::GetInteger('doc_id', $_REQUEST);
        $item_id        = Request::GetInteger('item_id', $_REQUEST);
        
        if($doc_alias == 'supinvoice')
        {
            $modelSupInvoice = new SupplierInvoice();
            $modelSupInvoice->RemoveItem($doc_id, $item_id);
        }
        else if ($doc_alias == 'oc')
        {
            $modelOC = new OC();
            $modelOC->RemoveItem($doc_id, $item_id);
        }
        else if ($doc_alias == 'qc')
        {
            $modelQC = new QC();
            $modelQC->RemoveItem($doc_id, $item_id);
        }
        
        $this->_send_json(array(
            'result'    => 'okay'
        ));        
    }
    
    /**
     * remove position from document // Удаляет position из документа
     * url: /document/removeposition
     * 
     * @version 20130228, d10n
     */
    public function removeposition()
    {
        $doc_alias      = Request::GetString('doc_alias', $_REQUEST);
        $doc_id         = Request::GetInteger('doc_id', $_REQUEST);
        $position_id    = Request::GetInteger('position_id', $_REQUEST);
        
        if ($doc_alias == 'stockoffer')
        {
            $modelStockOffer = new StockOffer();
            $modelStockOffer->RemovePosition($doc_id, $position_id);
        }
        
        $this->_send_json(array('result' => 'okay'));
    }
}