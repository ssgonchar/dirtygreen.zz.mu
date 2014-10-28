<?php
require_once APP_PATH . 'classes/models/order.class.php';
require_once APP_PATH . 'classes/models/preorder.class.php';
require_once APP_PATH . 'classes/models/steelgrade.class.php';
require_once APP_PATH . 'classes/models/steelitem.class.php';

class MainAjaxController extends ApplicationAjaxController
{    

    function MainAjaxController()
    {
        ApplicationAjaxController::ApplicationAjaxController();
                
        $this->authorize_before_exec['addposition']         = ROLE_STAFF;
        $this->authorize_before_exec['getactivelist']       = ROLE_STAFF;
        $this->authorize_before_exec['putpositionstoorder'] = ROLE_STAFF;
        $this->authorize_before_exec['remove']              = ROLE_STAFF;
        $this->authorize_before_exec['getchart']            = ROLE_STAFF;
        
        $this->modelOrder = new Order();
    }
    
    /**
     * remove order
     * url: /order/remove/{order_id}
     */
    function remove()
    {
        $order_id = Request::GetInteger('order_id', $_REQUEST);
        
        $orders = new Order();
        $order  = $orders->GetById($order_id);

        if (isset($order) && empty($order['order']['status']))
        {
            $orders->Remove($order_id);
            $this->_send_json(array('result' => 'okay'));    
        }
                
        $this->_send_json(array('result' => 'error'));
    }

    /**
     * add positions to order
     * url: /order/putpositionstoorder
     */
    function putpositionstoorder()
    {
        $order_id       = Request::GetInteger('order_id', $_REQUEST);
        $order_guid     = Request::GetString('order_id', $_REQUEST, '', 32);
        $stock_id       = Request::GetInteger('stock_id', $_REQUEST);
        $order_for      = ($stock_id == 1 ? 'mam' : ($stock_id == 2 ? 'pa' : ''));
        $position_ids   = Request::GetString('position_ids', $_REQUEST);
        $item_ids       = Request::GetString('item_ids', $_REQUEST);

        if (empty($position_ids) && empty($item_ids))
        {
            $this->_send_json(array(
                'result' => 'error'
            ));            
        }

        // new order or adding into pre-order // новый заказ или добавление в предзаказ
        if (empty($order_guid) || ('' . $order_id) != $order_guid)
        {
            $preorder = new PreOrder();
            if (empty($order_guid)) 
            {
                $order_guid = md5(date('Y-m-d H:i:s') . $this->user_id);
                $preorder->Save($order_guid, $order_for);
            }
            
            // add item into order // добавляет айтемы к заказу
            if (!empty($item_ids))
            {
                $positions = $preorder->ItemsAddFromStock($order_guid, $item_ids);

                foreach ($positions as $row) $position_ids .= ',' . $row['steelposition_id'];
                $position_ids = trim($position_ids, ',');
            }
            
            // add position to order // добавляет позиции к заказу
            if (!empty($position_ids))
            {
                $preorder->PositionsAddFromStock($order_guid, $position_ids);
            }
            
            $href = '/order/neworder/' . $order_guid;
        }
        else
        {                        
            // generates positions list with qtty and put item into order // формирует список добавляемых позиций с количеством и добавляет айтемы в заказ
            $positions_list = array();
            $orders         = new Order();            
            $order          = $orders->GetById($order_id);
            
            // 20120904, zharkov: cannot put into undefined, cancelled or completed order // нельзя добавлять в несуществующий, отмененный, выполненный заказ            
            if (empty($order) || in_array($order['order']['status'], array('ca', 'co'))) $this->_send_json(array('result' => 'error'));            
            
            // 20120904, zharkov: positions list for refreshing // список позиций, которые необходимо обновить
            $positions_to_update = array();
            
            if (!empty($item_ids))
            {
                $items_list     = array();
                $steelitems     = new SteelItem();
                foreach(explode(',', $item_ids) as $item_id)
                {
                    $item           = $steelitems->GetById($item_id);
                    $position_id    = $item['steelitem']['steelposition_id'];
                                    
                    if (array_key_exists($position_id, $positions_list))
                    {
                        $positions_list[$position_id]['qtty'] = $positions_list[$position_id]['qtty'] + 1;
                    }
                    else
                    {
                        $positions_list[$position_id] = array('qtty' => 1);
                    }
                    
                    // add item to order
                    $res = $orders->AddItem($order_id, $position_id, $item_id);
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
                
                // add positions to order // добавляет позиции к заказу
                $orders = new Order();
                foreach($positions_list as $position_id => $options)
                {
                    $orders->PositionAddFromStock($order_id, $position_id, $options['qtty']);
                    
                    // 20120904, zharkov
                    unset($positions_to_update[$position_id]);
                }
            }
            
            // 20120904, zharkov: refresh related items // обновляет косвенные позиции, те, которые формально не ушли в заказ (с родственными айтемами)
            $steepositions = new SteelPosition();
            foreach ($positions_to_update as $position_id => $row)
            {
                $steepositions->UpdateQtty($position_id);
            }
            
            $href = '/order/' . $order_id . '/edit';
        }
        
        $this->_send_json(array(
            'result'    => 'okay',
            'href'      => $href
        ));
    }
    
    /**
     * Get active orders list // Возвращает список активных заказов
     * url: /order/getactivelist
     */
    function getactivelist()
    {
        $stock_id   = Request::GetInteger('stock_id', $_REQUEST);
        
        $preorders  = new PreOrder();
        $list       = array();        
        foreach ($preorders->GetList() as $row) $list[]['order'] = $row;

        $orders     = new Order();
        foreach ($orders->GetListForStock($stock_id) as $row) $list[] = $row;

        $this->_assign('list', $list);
        $this->_send_json(array(
            'result'    => 'okay', 
            'content'   => $this->smarty->fetch('templates/html/order/control_order_select.tpl')
        ));        
    }
    
    /**
     * Add position to order // Добавляет позицию к заказу
     * url: /order/addposition
     */
    function addposition()
    {
        $next_row_index = Request::GetInteger('next_row_index', $_REQUEST);
        $price_unit     = Request::GetString('price_unit', $_REQUEST);

        $steelgrades = new SteelGrade();
        $this->_assign('steelgrades',   $steelgrades->GetList());
        $this->_assign('row_index',     $next_row_index);
        $this->_assign('price_unit',    $price_unit);

        $this->_send_json(array(
            'result'        => 'okay', 
            'position'      => $this->smarty->fetch('templates/html/order/control_position.tpl')
        ));
    }
    
    /**
     * Get position items list // Возвращает список айтемов заказа
     * url: /order/getitems
     */
    function getitems()
    {
        $order_id = Request::GetInteger('order_id', $_REQUEST);	//получаем данные из аякс запроса show_items()
        
        $modelOrder = new Order();
        
        $order = $modelOrder->GetById($order_id);
        
        //debug('1671', $order);
        
        //$position           = $modelSteelPosition->GetById($position_id);       //возвращает список позиция по Id
        //if (empty($position)) $this->_send_json(array('result' => 'error'));	//если список пустой отсылаем ошибку
        
	    $steelitems=$modelOrder->GetOrderItems($order_id);	// Возвращает список айтемов для заказа
        
        //debug('1671', $steelitems);
      
	    $modelSteelItem = new SteelItem();		//создаем обьект класса SteelItem()
	    for($i=0; $i<count($steelitems); $i++)	//циклом проходим массив со списком итемов
	    {						//Возвращаем список связанных документов
	        $docs = $modelSteelItem->GetRelatedDocs($steelitems[$i]['steelitem_id']);  
	        $steelitems[$i]['doc']=$docs;		//записываем этот список в массив "doc"
	    }
		//передаем данные в smarty
        $this->_assign('position',      $order['order']);	//данные о позиции
        $this->_assign('is_revision',   $is_revision);	//номер ревизии склада, т.е. сохраненное состояние склада на определенный момент. (!не использовать, !!не проверено)
        $this->_assign('items',         $steelitems);			//список итемов + данные по ним
		//dg($steelitems);
        $this->_send_json(array(					//отправляем данные по текущему ajax запросу
            'result'    => 'okay', 	//fetch('путь к шаблону')
            'content'   => $this->smarty->fetch('templates/html/order/control_items.tpl')	//json.content
        ));        
    }
    
  
    
    function saveprice()
    {
        $order_id = Request::GetInteger('order_id', $_REQUEST);
        $price = Request::GetString('price_equivalent', $_REQUEST);        
        //debug("1682", $price);
        $modelOrder = new Order();
        $updated_id = $modelOrder->ChangePrice($order_id, $price);
        //debug("1682", $updated_id);
        
        $this->_send_json(array(
            'result'    => 'okay',
            'object'   => $updated_id
        ));
    }
    
    /*
    * Получает id последнего заказа из таблицы orders
    */
    function getorderslastid()
    {
        $modelOrder = new Order();
        $last_id = $modelOrder->GetLastOrderId();
        foreach ($last_id as $key => $row)
        {
            $last_order_id = $last_id['0'][$key]['MAX(`id`)'];
        }
        $last_order_id_full_info = $modelOrder->GetById($last_order_id);
        //debug('1682', $last_order_id_full_info);
        $last_order_id_info = Array(
            'order_id'         => $last_order_id_full_info['order']['id'],
            'biz_id'           => $last_order_id_full_info['order']['biz_id'],
            'biz_title'        => $last_order_id_full_info['order']['biz']['title'],
            'company_id'       => $last_order_id_full_info['order']['company_id'],
            'company_title'    => $last_order_id_full_info['order']['company']['title'],
            'person_id'        => $last_order_id_full_info['order']['person']['id'],
            'person_full_name' => $last_order_id_full_info['order']['person']['full_name'],
            'order_for'        => $last_order_id_full_info['order']['order_for'],
            'qtty'             => $last_order_id_full_info['order']['quick']['qtty'],
            'weight'           => $last_order_id_full_info['order']['quick']['weight'],
            'currency'         => $last_order_id_full_info['order']['currency'],
            'value'            => round($last_order_id_full_info['order']['quick']['value'], 2),
            'customer'         => $last_order_id_full_info['order']['doc_no_full']            
        );
        
        $this->_send_json(array(
            'result'        => 'okay',
            'object'        => $last_order_id,
            'order_info'    => $last_order_id_info
        ));
    }
    
    
    /**
     * Возвращает список dekivery points
     * url: /order/getdeliverypoints
     */    
    public function getdeliverypoints() {
        $title = Request::GetString('title', $_REQUEST);
        
        //$modelOrder = new Order();
        $delivery_points = $this->modelOrder->getDeliveryPoints($title);
        
        $this->_send_json(array(
            'result'        => 'okay',
            'deliverypoints'    => $delivery_points,
        ));        
    }
    
    /**
     * Возвращает список customers
     * url: /order/getcustomers
     */        
    public function getcustomers() {
        $title = Request::GetString('title', $_REQUEST);
        $where = Request::GetString('where', $_REQUEST);
        $customers = $this->modelOrder->getCustomers($title, $where);
        
        $this->_send_json(array(
            'result'        => 'okay',
            'customers'    => $customers,
        ));          
    }
    
    /**
     * Возвращает список dekivery points
     * url: /order/getdeliverypoints
     */    
    function postnewordermessage()
    {
        //данные:
        $biz_id           = Request::GetInteger('biz_id', $_REQUEST);
        $biz_title        = Request::GetString('biz_title', $_REQUEST);
        $company_id       = Request::GetInteger('company_id', $_REQUEST);
        $company_title    = Request::GetString('company_title', $_REQUEST);
        $person_id        = Request::GetInteger('person_id', $_REQUEST);
        $person_full_name = Request::GetString('person_full_name', $_REQUEST);
        $qtty             = Request::GetInteger('qtty', $_REQUEST);
        $weight           = Request::GetInteger('weight', $_REQUEST);
        $value            = round(Request::GetInteger('value', $_REQUEST), 2);
        $order_id         = Request::GetInteger('order_id', $_REQUEST);
        //debug('1682', $order_id);
        
        $order_for        = Request::GetString('order_for', $_REQUEST);
        $title            = 'WEBSTOCK ORDER #'.$order_id.' (MaM)';
                    
        //проверяю если в базе не существует сообщения с title == WEBSTOCK NEW ORDER #'.$order_id.' (MAM), то постим сообщуху
        $messages = new Message();
        $result = $messages->CheckNewOrderMessage($title);
        if(empty($result)){        
            //сохраняю сообщение о заказе в TL
            // только для МаМ (из PlatesAhead сообщение добавляется сразу при создании)
            if($order_for == 'mam'){
                $messages->AlertOrder($biz_id, $biz_title, $company_id, $company_title, $person_id, $person_full_name, $qtty, $weight, $value, $order_id);
                //debug('1682', '$result');
            }
            $this->_send_json(array(
                'result'        => 'okay',
                'alert'    => 'We have a new order!',
            )); 
        }
    }  
}
