<?php
require_once APP_PATH . 'classes/models/biz.class.php';
require_once APP_PATH . 'classes/models/company.class.php';
require_once APP_PATH . 'classes/models/cmr.class.php';
require_once APP_PATH . 'classes/models/ddt.class.php';
require_once APP_PATH . 'classes/models/invoice.class.php';
require_once APP_PATH . 'classes/models/invoicingtype.class.php';
require_once APP_PATH . 'classes/models/person.class.php';
require_once APP_PATH . 'classes/models/paymenttype.class.php';
require_once APP_PATH . 'classes/models/qc.class.php';
require_once APP_PATH . 'classes/models/sc.class.php';
require_once APP_PATH . 'classes/models/ra.class.php';
require_once APP_PATH . 'classes/models/steelposition.class.php';
require_once APP_PATH . 'classes/models/steelitem.class.php';
require_once APP_PATH . 'classes/models/steelgrade.class.php';
require_once APP_PATH . 'classes/models/stock.class.php';
require_once APP_PATH . 'classes/models/user.class.php';

define ('ORDER_FOR_MAMIT',  1);
define ('ORDER_FOR_MAMUK',  2);
define ('ORDER_FOR_PA',     3);

class Order extends Model
{
    function Order()
    {
        Model::Model('orders');
    }
    
    /**
    * Обновляет статуса айтемов и статуса заказа
    * 
    * @param mixed $order_id
    * @param mixed $item_id
    * @param mixed $status_id
    */
    function UpdateItemStatus($order_id, $item_id, $status_id)
    {
        $result = $this->CallStoredProcedure('sp_order_update_item_status', array($this->user_id, $order_id, $item_id, $status_id));

        Cache::ClearTag('steelitem-' . $item_id);
        Cache::ClearTag('order-' . $order_id);
        Cache::ClearTag('orders');
        Cache::ClearTag('reports');
        
    }

    /**
     * Отменяет заказ
     * 
     * @param mixed $order_id
     * 
     * @version 20120925, zharkov
     */
    function CancelOrder($order_id)
    {
        $this->CallStoredProcedure('sp_order_cancel', array($this->user_id, $order_id));
        
        Cache::ClearTag('orderpositions-' . $order_id);
        Cache::ClearTag('order-' . $order_id);
        Cache::ClearTag('orders');
        Cache::ClearTag('steelpositions-reserved');
        Cache::ClearTag('reports');

        foreach ($this->GetPositions($order_id) as $position)
        {
            $this->RemovePosition($order_id, $position['steelposition_id'], true);    
        }        
    }
    
    /**
     * Возвращает статистику по заказу
     * 
     * @param mixed $order_id
     * 
     * @version 20120924, zharkov
     */
    function GetBalanceToDeliver($order_id)
    {
        $total_qtty         = 0;
        $total_weight       = 0;
        $delivered_qtty     = 0;
        $delivered_weight   = 0;
        
        foreach ($this->GetItems($order_id) as $item)
        {
            if (isset($item['status_id']) && $item['status_id'] >= ITEM_STATUS_DELIVERED)
            {
                $delivered_qtty++;
                $delivered_weight += $item['unitweight'];
            }
            
            $total_qtty++;
            $total_weight += $item['unitweight'];
        }

        return array(
            'total_qtty'        => $total_qtty,
            'total_weight'      => $total_weight,
            'delivered_qtty'    => $delivered_qtty,
            'delivered_weight'  => $delivered_weight,
            'balance_qtty'      => ($total_qtty - $delivered_qtty),
            'balance_weight'    => ($total_weight - $delivered_weight),
            'delivered'         => $delivered_qtty > 0
        );        
    }
    
    /**
     * Возвращает все айтемы заказа
     * 
     * @param mixed $order_id
     * 
     * @version 20120815, zharkov
     */
    
    function GetItems($order_id)
    {
        $result = $this->CallStoredProcedure('sp_order_get_items', array($this->user_id, $order_id));
        return isset($result) && isset($result[0]) ? $result[0] : array();
    }

    /**
     * Возвращает все айтемы заказа
     * 
     * @param mixed $order_id
     * 
     * @version 20140519, gonchar
     */
    
    function GetOrderItems($order_id)
    {
        //$result = $this->CallStoredProcedure('sp_order_get_items_ids', array($this->user_id, $order_id));
        //return isset($result) && isset($result[0]) ? $result[0] : array();
    
        $hash = 'order-' . $order_id . '-items';
        $cache_tags = array($hash, 'orderitems-' . $order_id, 'order-' . $order_id);
        
        $rowset = $this->_get_cached_data($hash, 'sp_order_get_items_ids', array($order_id), $cache_tags);
        
        $steelitems     = new SteelItem();
        return isset($rowset[0]) ? $steelitems->FillSteelItemInfo($rowset[0]) : array();    
    }    
    
    /**
     * Добавляет позиции к существующему заказу
     * 
     * @param mixed $order_id
     * @param mixed $position_id
     * @param mixed $need_update - означает, что айтемы уже добавлены и нужно обновить позицию на складе
     */
    function PositionAddFromStock($order_id, $position_id, $qtty)
    {
        $result = $this->CallStoredProcedure('sp_order_position_add_from_stock', array($this->user_id, $order_id, $position_id, $qtty));
        $result = isset($result) && isset($result[0]) && isset($result[0][0]) ? $result[0][0] : array();        

        if (empty($result))
        {
            Log::AddLine(LOG_CUSTOM, 'Error adding position from stock !');
            die('Error when trying to add position ' . $position_id . ' to order ' . $order_id);
        }

        $result1 = $this->SavePosition($order_id, $position_id, $result['biz_id'], $result['dimension_unit'], 
                                        $result['weight_unit'], $result['price_unit'], $result['currency'], $result['steelgrade_id'], 
                                        $result['thickness'], $result['width'], $result['length'], $result['unitweight'], 
                                        $result['qtty'], $result['weight'], $result['price'], $result['value'], 
                                        $result['deliverytime'], $result['internal_notes'], $result['order_status']);
        
        if (!$result1)
        {
            $steelpositions = new SteelPosition();
            $steelpositions->UpdateQtty($position_id);            
        }
        
        Cache::ClearTag('steelposition-' . $position_id . '-items');
    }
    
    /**
     * Обновляет статус заказа
     * 
     * @param mixed $order_id
     * @param mixed $status
     */
    function UpdateStatus($order_id, $status)
    {
        $this->Update($order_id, array('status' => $status));
        Cache::ClearTag('order-' . $order_id);
    }
        
    /**
     * Обновляет количество позиции в заказе по количеству айтемов добавленных в заказ
     * если айтемов в заказе нет, удаляет позицию
     * 
     * @param mixed $order_id
     * @param mixed $position_id
     */
    function UpdatePositionQtty($order_id, $position_id)
    {
        $result = $this->CallStoredProcedure('sp_order_update_position_qtty', array($this->user_id, $order_id, $position_id));        
        Cache::ClearTag('orderpositions-' . $order_id);
    }
    
    /**
     * Добавляет айтем к заказу
     * 
     * @param mixed $order_id
     * @param mixed $position_id
     * @param mixed $item_id
     */
    function AddItem($order_id, $position_id, $item_id, $status_id = ITEM_STATUS_ORDERED)
    {
        $result = $this->CallStoredProcedure('sp_order_add_item', array($this->user_id, $order_id, $position_id, $item_id, $status_id));
        $result = isset($result) && isset($result[0]) ? $result[0] : array();
        
        if (isset($result[0]) && isset($result[0]['ErrorCode']))
        {
            Log::AddLine(LOG_ERROR, $result[0]['ErrorAt'] . ' : ' . $result[0]['ErrorCode']);
            die('Error Processing SteelItems !');
        }
                
        $position_ids  = array();
        foreach ($result as $row)
        {
            Cache::ClearTag('steelitem-' . $row['steelitem_id']);
            
            // формирует массив позиций, для которых нужно обновить количество
            if (!in_array($row['steelposition_id'], $position_ids)) 
            {
                $position_ids[] = $row['steelposition_id'];
            }
        }
                
        Cache::ClearTag('steelitem-' . $item_id);    
        Cache::ClearTag('order-' . $order_id);
        Cache::ClearTag('orderquick-' . $order_id);
        Cache::ClearTag('orderpositions-' . $order_id);
        Cache::ClearTag('reports');
        
        return $position_ids;
    }

    /**
     * Удаляет айтем из заказа
     * 
     * @param mixed $order_id
     * @param mixed $position_id
     * @param mixed $item_id
     */
    function RemoveItem($order_id, $item_id, $leave_history = false)
    {
        $result = $this->CallStoredProcedure('sp_order_remove_item', array($this->user_id, $order_id, $item_id, $leave_history));
        $result = isset($result) && isset($result[0]) ? $result[0] : array();
        
        if (isset($result[0]) && isset($result[0]['ErrorCode']))
        {
            Log::AddLine(LOG_ERROR, $result[0]['ErrorAt'] . ' : ' . $result[0]['ErrorCode']);
            die('Error Processing SteelItems !');
        }
        
        $position_ids  = array();
        foreach ($result as $row)
        {
            Cache::ClearTag('steelitem-' . $row['steelitem_id']);
            
            // формирует массив позиций, для которых нужно обновить количество
            if (!in_array($row['steelposition_id'], $position_ids)) 
            {
                $position_ids[] = $row['steelposition_id'];
            }
        }
        
        Cache::ClearTag('steelitem-' . $item_id);    
        Cache::ClearTag('order-' . $order_id);
        Cache::ClearTag('orderquick-' . $order_id);
        Cache::ClearTag('orderpositions-' . $order_id);
        Cache::ClearTag('reports');
        
        return $position_ids;
    }
    
    /**
     * Проверяет доступное количество позиции для заказа
     * 
     * @param mixed $order_id
     * @param mixed $position_id
     * @param mixed $qtty
     * @param mixed $order_new_status
     * @return mixed
     */
    function TestPositionQtty($order_id, $position_id, $qtty, $order_new_status)
    {
        $result = $this->CallStoredProcedure('sp_order_test_position_qtty', array($this->user_id, $order_id, $position_id, $qtty, $order_new_status));
        return isset($result[0]) && isset($result[0][0]) ? $result[0][0] : array('available' => false, 'qtty' => 0);
    }
    
    /**
     * Удаляет заказ
     * 
     * @param mixed $order_id
     */
    function Remove($order_id)
    {
        // удаляет позиции заказа
        $positions = $this->GetPositions($order_id);
        foreach ($positions as $position) $this->RemovePosition($order_id, $position['position_id']);
        
        // удаляет заказ
        $result = $this->CallStoredProcedure('sp_order_remove', array($this->user_id, $order_id));        
        
        Cache::ClearTag('orderpositions-' . $order_id);
        Cache::ClearTag('order-' . $order_id);
        Cache::ClearTag('orders');
        Cache::ClearTag('reports');
    }
    
    /**
     * Возвращает список марок стали которые используются в заказах
     * 
     */
    function GetSteelgrades()
    {
        $hash       = 'orders-steelgrades';
        $cache_tags = array($hash, 'orders', 'steelgrades');
        
        $rowset         = $this->_get_cached_data($hash, 'sp_order_get_steelgrades', array(), $cache_tags);
        $steelgrades    = new SteelGrade();
        
        return isset($rowset[0]) ? $steelgrades->FillSteelGradeInfo($rowset[0]) : array();
    }
    
    /**
     * Возвращает список заказов
     * 
     * @param mixed $order_for
     * @param mixed $biz_id
     * @param mixed $company_id
     * @param mixed $period_from
     * @param mixed $period_to
     * @param mixed $status
     * @param mixed $steelgrade_id
     * @param mixed $thickness
     * @param mixed $width
     * @param mixed $keyword
     */
    function GetList($order_for, $biz_id, $company_id, $period_from, $period_to, $status, $steelgrade_id, $thickness, $width, $keyword, $type, 
                     $page_no = 0, $per_page = ITEMS_PER_PAGE)
    {
        $page_no    = $page_no > 0 ? $page_no : 1;
        $per_page   = $per_page < 1 ? ITEMS_PER_PAGE : $per_page;
        $start      = ($page_no - 1) * $per_page;

        $hash       = 'orders-' . md5('order_for-' . $order_for . '-biz_id-' . $biz_id . '-company_id-' . $company_id .  
                        '-period_from-' . $period_from . '-period_to-' . $period_to . '-status-' . $status . 
                        '-steelgrade_id-' . $steelgrade_id . '-thickness-' . $thickness . '-width-' . $width . 
                        '-keyword-' . $keyword . '-type-' . $type . '-page_no' . $page_no . '-start' . $start);
        $cache_tags = array($hash, 'orders-status-' . $status, 'orders');
                    
        $thickness  = $this->_get_interval($thickness);
        $width      = $this->_get_interval($width);
        
        if ($order_for == 'pa')
        {
            $thickness['from']  = $thickness['from'] * 25.4; 
            $thickness['to']    = $thickness['to'] * 25.4;
            $width['from']      = $width['from'] * 25.4; 
            $width['to']        = $width['to'] * 25.4; 
        }
        
        $rowset = $this->_get_cached_data($hash, 'sp_order_get_list', array($this->user_id, $order_for, $biz_id, $company_id, $period_from, 
                                            $period_to, $status, $steelgrade_id, $thickness['from'], $thickness['to'], 
                                            $width['from'], $width['to'], $keyword, $type, $start, $per_page), $cache_tags);

        return array(
            'data'  => isset($rowset[0]) ? $this->FillOrderInfo($rowset[0]) : array(),
            'count' => isset($rowset[1]) && isset($rowset[1][0]) && isset($rowset[1][0]['rows']) ? $rowset[1][0]['rows'] : 0
        );        
    }
        
    /**
     * Возвращает список отфильтрованный по ключевому слову<br />
     * Search by: orders.number, orders.buyer_ref, companies.title(_trade, _short, _native)
     * 
     * @param string $keyword [VARCHAR(20)]
     * @param int $rows_count Количество записей
     * 
     * @version 20121204, d10n
     */
    function GetListByKeyword($keyword, $rows_count)
    {
        $hash       = 'order-keyword-' . $keyword . '-rowscount-' . $rows_count;
        $cache_tags = array($hash, 'orders');

        $rowset = $this->_get_cached_data($hash, 'sp_order_get_list_by_keyword', array($keyword, $rows_count), $cache_tags);
        $rowset = isset($rowset[0]) ? $this->FillOrderInfo($rowset[0]) : array();

        return $rowset;
    }
    
    /**
     * Возвращает список заказов, для добавления в них позиций со склада
     * 
     * @version 20120815, zharkov
     */
    function GetListForStock($stock_id)
    {
        $hash       = 'orders-for-stock-' . $stock_id;
        $cache_tags = array($hash, 'orders', 'stock-' . $stock_id, 'orders-for-stock');
        
        $rowset = $this->_get_cached_data($hash, 'sp_order_get_list_for_stock', array($this->user_id, $stock_id), $cache_tags);        
        $rowset = isset($rowset[0]) ? $this->FillOrderInfo($rowset[0], 'order_id', 'order', 'order', true) : array();

        return $rowset;
    }
        
    /**
     * Возвращает список позиций заказа
     * 
     * @param mixed $order_id
     * @return mixed
     */
    function GetPositions($order_id)
    {
        $hash           = 'orderpositions-' . $order_id;
        $cache_tags     = array($hash);
        
        $rowset         = $this->_get_cached_data($hash, 'sp_order_get_positions', array($order_id), $cache_tags);
        $rowset         = isset($rowset[0]) ? $rowset[0] : array();
        
        if (empty($rowset)) return $rowset;
        
        $steelgrades    = new SteelGrade();
        $rowset         = $steelgrades->FillSteelGradeInfo($rowset);
                
        foreach ($rowset as $key => $row)
        {
            $steelitems = $this->GetPositionItems($order_id, $row['position_id']);
            
            $rowset[$key]['steelposition_id']   = $row['position_id'];
            $rowset[$key]['location']           = array();
            $rowset[$key]['plateid']            = array();
            $rowset[$key]['supplier']           = array();
            $rowset[$key]['stockholder']        = array();
            
            $total_qtty         = 0;
            $total_weight       = 0;
            $delivered_qtty     = 0;
            $delivered_weight   = 0;

            foreach ($steelitems as $steelitem)
            {
                if (!isset($steelitem['steelitem'])) continue;
                $steelitem = $steelitem['steelitem'];
                
                // plate id
                if (!isset($rowset[$key]['plateid'][$steelitem['guid']]) && !empty($steelitem['guid'])) 
                {
                    $rowset[$key]['plateid'][$steelitem['guid']] = $steelitem['guid'];
                }
                
                // locations
                if (isset($steelitem['location']) && isset($steelitem['location']['title']) && !empty($steelitem['location']['title'])
                    && !isset($row['location'][$steelitem['location']['title']]))
                {
                    $rowset[$key]['location'][$steelitem['location']['title']] = $steelitem['location']['title'];
                }

                // suppliers
                if (isset($steelitem['supplier']) && !empty($steelitem['supplier']) && !isset($row['supplier'][$steelitem['supplier']['id']])) 
                {
                    $rowset[$key]['supplier'][$steelitem['supplier']['id']] = $steelitem['supplier']['title'];
                }
                
                // stockholders
                if (isset($steelitem['stockholder']) && !empty($steelitem['stockholder']) && !isset($row['stockholder'][$steelitem['stockholder']['id']])) 
                {
                    $rowset[$key]['stockholder'][$steelitem['stockholder']['id']] = $steelitem['stockholder']['title'];
                }
                
                $total_qtty         += 1;
                $total_weight       += $steelitem['unitweight'];
                if ($steelitem['status_id'] >= ITEM_STATUS_DELIVERED)
                {
                    $delivered_qtty     += 1;
                    $delivered_weight   += $steelitem['unitweight'];
                }
                
            }
            
            $rowset[$key]['steelitems']         = $steelitems;
            $rowset[$key]['balance_to_deliver'] = array(
                'qtty'      => ($total_qtty - $delivered_qtty),
                'weight'    => ($total_weight - $delivered_weight)
            );            
        } 
        
        $steelpositions = new SteelPosition();
        $rowset         = $steelpositions->FillSteelPositionInfo($rowset);        

        return $rowset;
    }
    
    
    /**
     * Возвращает айтемы позиции
     * 
     * @param mixed $order_id
     * @param mixed $position_id
     */
    function GetPositionItems($order_id, $position_id)
    {
        $hash           = 'order-' . $order_id . '-position-' . $position_id . '-items';
        $cache_tags     = array($hash, 'orderpositions-' . $order_id, 'order-' . $order_id, 'position-' . $position_id);
        
        $rowset         = $this->_get_cached_data($hash, 'sp_order_get_position_items', array($order_id, $position_id), $cache_tags);

        $steelitems     = new SteelItem();
        return isset($rowset[0]) ? $steelitems->FillSteelItemInfo($rowset[0]) : array();
    }
    
    /**
     * Возвращает айтемы заказа
     *
     * @param mixed $order_id
     * @return array
     * @author SG
     * @version 2014-05-19
     **/
    /*
    function GetItems($order_id)
    {
        $hash = 'order-' . $order_id . '-items';
        $cache_tags = array($hash, 'orderitems-' . $order_id, 'order-' . $order_id);
        
        $rowset = $this->_get_cached_data($hash, 'sp_order_get_items', array($order_id), $cache_tags);
        
        $steelitems     = new SteelItem();
        return isset($rowset[0]) ? $steelitems->FillSteelItemInfo($rowset[0]) : array();
    }*/
    
    /**
     * Возвращает позиции на склад
     * 
     * @param mixed $order_id
     * @param mixed $position_id
     */
    function RemovePosition($order_id, $position_id, $leave_history = false)
    {
        $result = $this->CallStoredProcedure('sp_order_remove_position', array($this->user_id, $order_id, $position_id));
        $result = isset($result[0]) ? $result[0] : array();

        Cache::ClearTag('orderpositions-' . $order_id);
        
        foreach ($result as $row)
        {
            $positions = $this->RemoveItem($order_id, $row['item_id'], $leave_history);
        }
        
        if (!isset($positions) || !in_array($position_id, $positions))
        {
            $positions[] = $position_id;
        }
        
        $steelposition = new SteelPosition();
        foreach ($positions as $position_id)
        {
            $steelposition->UpdateQtty($position_id);    
        }
    }
    
    /**
     * Сохраняет позицию в заказе
     * 
     * @param mixed $order_id
     * @param mixed $position_id
     * @param mixed $biz_id
     * @param mixed $dimension_type
     * @param mixed $weight_type
     * @param mixed $currency
     * @param mixed $steelgrade_id
     * @param mixed $thickness
     * @param mixed $width
     * @param mixed $length
     * @param mixed $unitweight
     * @param mixed $qtty
     * @param mixed $weight
     * @param mixed $price
     * @param mixed $value
     * @param mixed $internal_notes
     * 
     * Возвращает TRUE если количество позиции на складе обновлено или FALSE если количество не обновлялось
     */
    function SavePosition($order_id, $position_id, $biz_id, $dimension_unit, $weight_unit, $price_unit, $currency, 
                            $steelgrade_id, $thickness, $width, $length, $unitweight, $qtty, $weight, $price, $value, 
                            $deliverytime, $internal_notes, $order_status)
    {
        // создает новые позиции
        if (empty($position_id))
        {
            $steelpositions = new SteelPosition();
            $result         = $steelpositions->Add(0, 0, 92, $biz_id, 0, 0, $dimension_unit, $weight_unit, $price_unit, $currency, 
                                                    $steelgrade_id, $thickness, $width, $length, $unitweight, $qtty, $width, 
                                                    $price, $value, '', '', $internal_notes);
            
            if (empty($result)) return null;            
            $position_id = $result['id'];
        }
        
        if ($dimension_unit == 'in')
        {
            $thickness_mm   = $thickness * 25.4;
            $width_mm       = $width * 25.4; 
            $length_mm      = $length * 25.4;            
        }
        else
        {
            $thickness_mm   = $thickness;
            $width_mm       = $width; 
            $length_mm      = $length;             
        }
        
        if ($weight_unit == 'lb')
        {
            $unitweight_ton = $unitweight / 2200;
            $weight_ton     = $weight / 2200;
        }
        else
        {
            $unitweight_ton = $unitweight;
            $weight_ton     = $weight;            
        }
        
        
        // сохраняет позицию в заказе
        $result     = $this->CallStoredProcedure('sp_order_save_position', array($this->user_id, $order_id, $position_id, 
                            $steelgrade_id, $thickness, $thickness_mm, $width, $width_mm, $length, $length_mm, 
                            $unitweight, $unitweight_ton, $qtty, $weight, $weight_ton, 
                            $price, $value, $deliverytime, $internal_notes, $order_status));

        $delta_qtty = isset($result) && isset($result[0]) && isset($result[0][0]) && isset($result[0][0]['delta_qtty']) ? $result[0][0]['delta_qtty'] : 0;
        $items      = isset($result) && isset($result[1]) ? $result[1] : array();
        
        Cache::ClearTag('orderpositions-' . $order_id);

        if ($delta_qtty != 0)
        {
            $position_ids = array();
            
            for ($i = 0; $i < abs($delta_qtty); $i++)
            {
                // для позиций созданных из заказа придет пустой рекордсет при добавлении нового айтема
                $item_id = isset($items[$i]) ? $items[$i]['id'] : 0;    

                // увеличивается количество позиции заказа
                if ($delta_qtty > 0)
                {
                    $positions = $this->AddItem($order_id, $position_id, $item_id);
                }
                // уменьшается количество позиции заказа
                else
                {
                    $positions = $this->RemoveItem($order_id, $item_id);
                }
                
                foreach ($positions as $key => $position_id)
                {
                    if (in_array($position_id, $position_ids)) continue;
                    $position_ids[] = $position_id;
                }
            }

            // обновляет количество позиции
            $steelpositions = new SteelPosition();
            foreach ($position_ids as $key => $position_id)
            {
                $steelpositions->UpdateQtty($position_id);
            }
            
            return true;
        }
        
        return false;
    }
        
    /**
     * Сохраняет заказ
     * 
     * @param mixed $id
     * @param mixed $order_for
     * @param mixed $biz_id
     * @param mixed $company_id
     * @param mixed $person_id
     * @param mixed $buyer_ref
     * @param mixed $supplier_ref
     * @param mixed $delivery_point
     * @param mixed $delivery_town
     * @param mixed $delivery_cost
     * @param mixed $delivery_date
     * @param mixed $delivery_date_alt
     * @param mixed $invoicingtype_id
     * @param mixed $paymenttype_id
     * @param mixed $status
     * @param mixed $description
     * @param mixed $commit - важный параметр, если FALSE, то заказу не назначается номер и статус, нужно для сохранения параметров заказа без его регистрации в системе
     * @return resource
     */
    function Save($id, $order_for, $biz_id, $company_id, $person_id, $buyer_ref, $supplier_ref,
                    $delivery_point, $delivery_town, $delivery_cost, $delivery_date, $delivery_date_alt, 
                    $invoicingtype_id, $paymenttype_id, $status, $description)
    {        

        $modelStock = new Stock();
        if ($order_for == 'pa')
        {
            $stock = $modelStock->GetById(STOCK_US);
        }
        else
        {
            $stock = $modelStock->GetById(STOCK_EU);
        }
        
        if (isset($stock))
        {
            $stock = $stock['stock'];
            
            $dimension_unit = $stock['dimension_unit'];
            $weight_unit    = $stock['weight_unit'];
            $price_unit     = $stock['price_unit'];
            $currency       = $stock['currency'];
        }
        else
        {
            $dimension_unit = 'mm'; 
            $weight_unit    = 'mt';
            $price_unit     = 'mt';
            $currency       = 'eur';            
        }
        
        $result = $this->CallStoredProcedure('sp_order_save', array($this->user_id, $id, $order_for, $biz_id, 
                    $company_id, $person_id, $buyer_ref, $supplier_ref, $delivery_point, $delivery_town, $delivery_cost, 
                    $delivery_date, $delivery_date_alt, $invoicingtype_id, $paymenttype_id, $status, 
                    $dimension_unit, $weight_unit, $price_unit, $currency, $description));
        
        $result = isset($result) && isset($result[0]) && isset($result[0][0]) ? $result[0][0] : null;
        
        if (empty($result) || array_key_exists('ErrorCode', $result)) return null;

        Cache::ClearTag('order-' . $result['id']);
        Cache::ClearTag('orderpositions-' . $result['id']);
        Cache::ClearTag('orders');
        Cache::ClearTag('bizquick-' . $biz_id);
        Cache::ClearTag('companyquick-' . $company_id);
        Cache::ClearTag('steelpositions-reserved');
        
        return $result;
    }    
    
    /**
     * Возвращает бизнес по идентификатору
     *     
     * @param mixed $id
     */
    function GetById($id)
    {
        $dataset = $this->FillOrderInfo(array(array('order_id' => $id)));
        return isset($dataset) && isset($dataset[0]) && isset($dataset[0]['order']) ? $dataset[0] : null;
    }
    
    /**
     * Возвращает заказ по номеру
     * 
     * @param int $number 
     * @return array
     * 
     * version 20121204, d10n
     */
    function GetByNumber($number)
    {
        $hash       = 'order-number-' . $number;
        $cache_tags = array($hash, 'orders');

        $rowset     = $this->_get_cached_data($hash, 'sp_order_get_by_number', array($number), $cache_tags);
        $rowset = isset($rowset[0]) ? $this->FillOrderMainInfo($rowset[0]) : null;
        
        return isset($rowset[0]) && isset($rowset[0]['order']) ? $rowset[0]['order'] : null;
    }
    
    
    /**
     * Возвращает основную информацию об заказе
     * 
     * @param array $rowset
     * @param mixed $id_fieldname
     * @param mixed $entityname
     * @param mixed $cache_prefix
     * @param mixed $fill_from_session
     * @return array
     */
    function FillOrderMainInfo($rowset, $id_fieldname = 'order_id', $entityname = 'order', $cache_prefix = 'order', $fill_from_session = false)
    {
        $rowset = $this->_fill_entity_info($rowset, $id_fieldname, $entityname, $cache_prefix, 'sp_order_get_list_by_ids', array('orders' => '', 'order' => 'id'), array());

        foreach ($rowset as $key => $row)
        {
            if (isset($row[$entityname]) && !empty($row[$entityname])) 
            {
                $rowset[$key][$entityname]['doc_no'] = 'INPO' . substr((10000 + $row[$entityname]['id']), 1);
            }
        }

        return $rowset;
    }
    

    /**
     * Возвращет информацию о заказе
     * 
     * @param mixed $rowset
     * @param mixed $id_fieldname
     * @param mixed $entityname
     * @param mixed $cache_prefix
     */
    function FillOrderInfo($rowset, $id_fieldname = 'order_id', $entityname = 'order', $cache_prefix = 'order', $fill_from_session = false)
    {
        $rowset = $this->FillOrderMainInfo($rowset, $id_fieldname, $entityname, $cache_prefix, $fill_from_session);
        $rowset = $this->FillQuickInfo($rowset, $id_fieldname, $entityname);
        
        foreach ($rowset as $key => $row)
        {
            if (isset($row[$entityname]) && isset($row[$entityname]['quick']))
            {
                $rowset[$key][$entityname]['balance_to_deliver'] = array(
                    'qtty'      => ($row[$entityname]['quick']['qtty'] - $row[$entityname]['quick']['qtty_delivered']),
                    'weight'    => ($row[$entityname]['quick']['weight'] - $row[$entityname]['quick']['weight_delivered'])
                );                
            }
            
            if (isset($row[$entityname]) && !empty($row[$entityname])) 
            {
                $order = $row[$entityname];
 
                // удаляет из списка временные заказы других пользователей
                if (empty($order['status']) && $order['created_by'] != $this->user_id)
                {
                    unset($rowset[$key]);    
                    continue;
                }
                
                // заполняет данные заказов несохраненными данными
                if ($fill_from_session)
                {
                    // удаляет созданные заказы без данных
                    if (empty($order['status']) && !isset($_SESSION['order-' . $order['id']]))
                    {
                        unset($rowset[$key]);    
                        continue;
                    }
                    
                    // добавляет к заказу данные временно сохраненные на странице редактирования, в сессии
                    if (isset($_SESSION['order-' . $order['id']]) && isset($_SESSION['order-' . $order['id']]['form']))
                    {
                        foreach($_SESSION['order-' . $order['id']]['form'] as $param => $value)
                        {
                            $rowset[$key][$entityname][$param] = $value;
                            $order[$param] = $value;    // для преобразования ниже
                        }
                    }                    
                }
                
                if ($order['biz_id'] > 0) $rowset[$key]['orderbiz_id'] = $order['biz_id'];
                if (!empty($order['company_id'])) $rowset[$key]['ordercompany_id'] = $order['company_id'];
                if (!empty($order['person_id'])) $rowset[$key]['orderperson_id'] = $order['person_id'];
                
                $rowset[$key]['orderauthor_id']     = $order['created_by'];
                $rowset[$key]['ordermodifier_id']   = $order['modified_by'];
                $rowset[$key]['invoicingtype_id']   = $order['invoicingtype_id'];
                $rowset[$key]['paymenttype_id']     = $order['paymenttype_id'];
            }            
        }

        $bizs       = new Biz();
        $rowset     = $bizs->FillMainBizInfo($rowset, 'orderbiz_id', 'orderbiz');

        $companies  = new Company();
        //$rowset     = $companies->FillCompanyInfoShort($rowset, 'ordercompany_id', 'ordercompany');
        $rowset     = $companies->FillCompanyInfo($rowset, 'ordercompany_id', 'ordercompany');
        
        $perssons   = new Person();
        $rowset     = $perssons->FillPersonMainInfo($rowset, 'orderperson_id', 'orderperson');
        
        $users      = new User();
        $rowset     = $users->FillUserInfo($rowset, 'orderauthor_id',   'orderauthor');
        $rowset     = $users->FillUserInfo($rowset, 'ordermodifier_id', 'ordermodifier');
        
        $invoicingtypes = new InvoicingType();
        $rowset         = $invoicingtypes->FillInvoicingTypeInfo($rowset);

        $paymenttypes   = new PaymentType();
        $rowset         = $paymenttypes->FillPaymentTypeInfo($rowset);

        $companies = new Company();
        foreach ($rowset as $key => $row) 
        {
            if (isset($row[$entityname]) && !empty($row[$entityname]))
            {
                if (isset($row['orderbiz'])) 
                {
                    $rowset[$key][$entityname]['biz']       = $row['orderbiz'];
                    $rowset[$key][$entityname]['biz_title'] = $row['orderbiz']['number_output'];
                    
                    unset($rowset[$key]['orderbiz']);
                }
                
                if (isset($row['ordercompany']))
                {
                    $rowset[$key][$entityname]['company'] = $row['ordercompany'];
                    unset($rowset[$key]['ordercompany']);
                }

                if (isset($row['orderperson']))
                {
                    $rowset[$key][$entityname]['person'] = $row['orderperson'];
                    unset($rowset[$key]['orderperson']);
                }

                if (isset($row['orderauthor']))
                {
                    $rowset[$key][$entityname]['author'] = $row['orderauthor'];
                    unset($rowset[$key]['orderauthor']);
                }                
                
                if (isset($row['ordermodifier']))
                {
                    $rowset[$key][$entityname]['modifier'] = $row['ordermodifier'];
                    unset($rowset[$key]['ordermodifier']);
                }
                
                if (isset($row['invoicingtype']))
                {
                    $rowset[$key][$entityname]['invoicingtype'] = $row['invoicingtype'];
                    unset($rowset[$key]['invoicingtype']);
                }

                if (isset($row['paymenttype']))
                {
                    $rowset[$key][$entityname]['paymenttype'] = $row['paymenttype'];
                    unset($rowset[$key]['paymenttype']);
                }                
            }

            unset($rowset[$key]['orderbiz_id']);
            unset($rowset[$key]['orderperson_id']);
            unset($rowset[$key]['ordercompany_id']);
            unset($rowset[$key]['orderauthor_id']);
            unset($rowset[$key]['ordermodifier_id']);
            
            if (isset($rowset[$key][$entityname]))
            {
                $rowset[$key][$entityname]['doc_no_full'] = $row[$entityname]['doc_no'] . ' ' . (isset($rowset[$key][$entityname]['company']) ? $rowset[$key][$entityname]['company']['doc_no'] : '') . ' ' . $row[$entityname]['buyer_ref'];
            }
            
            
            if (!empty($row[$entityname]['currency']))
            {
                $rowset[$key][$entityname]['currency_sign'] = $row[$entityname]['currency'] == 'usd' ? '$' : ($row[$entityname]['currency'] == 'eur' ? '&euro;' : '');
            }
            
            if (!empty($row[$entityname]['status']))
            {
                if ($row[$entityname]['status'] == 'nw')
                {
                    $rowset[$key][$entityname]['status_title'] = 'New (WebStock)';
                }
                else if ($row[$entityname]['status'] == 'ip')
                {
                    $rowset[$key][$entityname]['status_title'] = 'In Process';
                }
                else if ($row[$entityname]['status'] == 'de')
                {
                    $rowset[$key][$entityname]['status_title'] = 'To be Invoiced';
                }
                else if ($row[$entityname]['status'] == 'co')
                {
                    $rowset[$key][$entityname]['status_title'] = 'Completed';
                }
                else if ($row[$entityname]['status'] == 'ca')
                {
                    $rowset[$key][$entityname]['status_title'] = 'Cancelled';
                }
            }
            
            if (!empty($row[$entityname]['order_for']))
            {
                //$rowset[$key][$entityname]['order_for_co'] = $companies->GetByAlias($row['order']['order_for']);
                $rowset[$key][$entityname]['order_for_title'] = $row[$entityname]['order_for'] == 'mam' ? 'MaM' : 'PlatesAhead';
            }
            
            if (!empty($row[$entityname]['delivery_point']))
            {
                if ($row[$entityname]['delivery_point'] == 'col')
                {
                    $rowset[$key][$entityname]['delivery_point_title'] = 'Collected';
                }
                else if ($row[$entityname]['delivery_point'] == 'del')
                {
                    $rowset[$key][$entityname]['delivery_point_title'] = 'Delivered';
                }
                else
                {
                    $rowset[$key][$entityname]['delivery_point_title'] = strtoupper($row[$entityname]['delivery_point']);
                }
            }
/*
            if (isset($row[$entityname]) && !empty($row[$entityname]))
            {
                $balance = $this->GetBalanceToDeliver($row[$entityname]['id']);
                $rowset[$key][$entityname]['balance_to_deliver'] = array(
                    'qtty'      => $balance['balance_qtty'],
                    'weight'    => $balance['balance_weight']
                );                
            }
*/            
        }

        return $this->FillQuickInfo($rowset, $id_fieldname, $entityname);
    }
    
    /**
     * Возвращает быстроменяющуюся информацию по заказу
     * 
     * @param array $rowset
     * @param mixed $id_fieldname
     * @param mixed $entityname
     */
    function FillQuickInfo($rowset, $id_fieldname = 'order_id', $entityname = 'order')
    {
        $rowset = $this->_fill_entity_info($rowset, $id_fieldname, $entityname . 'quick', 'orderquick', 'sp_order_get_quick_by_ids', array('ordersquick' => '', 'orders' => '', 'order' => 'id'), array());

        foreach ($rowset AS $key => $row)
        {
            if (isset($row[$entityname]) && isset($row[$entityname . 'quick']))
            {
                $rowset[$key][$entityname]['quick'] = $row[$entityname . 'quick'];
            }
            
            unset($rowset[$key][$entityname . 'quick']);            
        }
        
        return $rowset;
    }
    
    /**
     * Разбивает строку значений размеров и веса на интервал
     * 
     * @param mixed $value
     * @return mixed
     */
    function _get_interval($value)
    {

        $value = preg_replace('#\s+#i', '', $value);
        if (empty($value)) return array('from' => 0, 'to' => 0);
        
        // 0.89
        preg_match("#^([0-9\.]+)$#si", $value, $matches);
        if (!empty($matches)) return array('from' => floatval($matches[1]), 'to' => floatval($matches[1]));

        // 0.65-0.89
        preg_match("#^([0-9\.]+)-([0-9\.]+)$#si", $value, $matches);
        if (!empty($matches)) return array('from' => floatval($matches[1]), 'to' => floatval($matches[2]));

        // >0.89
        preg_match("#^&gt;([0-9\.]+)$#si", $value, $matches);
        if (!empty($matches)) return array('from' => floatval($matches[1]), 'to' => 0);

        // <0.89
        preg_match("#^&lt;([0-9\.]+)$#si", $value, $matches);
        if (!empty($matches)) return array('from' => 0, 'to' => floatval($matches[1]));
        
        return array('from' => 0, 'to' => 0);
    }
    
    
    
    /**
     * Items list of specified or less status from orders
     * 
     * @param array $order_ids [VARCHAR(1100)] набор IDs (orders.id) заказов, для которых получаются items
     * @param int $status_id [TINYINT] const ITEM_STATUS_
     * 
     * @version 20121009, d10n
     */
    public function GetOrdersItems($order_ids, $status_id)
    {
        $data_set   = $this->CallStoredProcedure('sp_orders_get_items', array($order_ids, $status_id));
        $result     = isset($data_set) && isset($data_set[0]) ? $data_set[0] : array();
        
        if (empty($result)) return array();
        
        $modelSteelItem = new SteelItem();
        
        return $modelSteelItem->FillSteelItemInfo($result);
    }
    
    /**
     * Возвращает список связанных с конкретным Order документов (DDTs, CMRs, Invoices, RAs, SCs, QCs)
     * 
     * @param int $id
     * @return array
     * 
     * @version 20130116, d10n
     */
    public function GetListOfRelatedDocs($id)
    {
        $rowset = $this->CallStoredProcedure('sp_order_get_related_docs', array($id));
        
        $modelCMR       = new CMR();
        $modelDDT       = new DDT();
        $modelInvoice   = new Invoice();
        $modelSC        = new SC();
        $modelQC        = new QC();
        $modelRA        = new RA();
        
        
        $list = array();
        foreach($rowset as $doc_set)
        {
            if (empty($doc_set)) continue;
            if (!isset($doc_set[0])) continue;
            
            $exploded       = explode('_', key($doc_set[0]));
            $object_alias   = isset($exploded[0]) ? $exploded[0] : '';
            
            if (empty($object_alias)) continue;
            
            switch ($object_alias)
            {
                case 'cmr':
                    $docs_list  = $modelCMR->FillCMRInfo($doc_set);
                    break;
                
                case 'ddt':
                    $docs_list  = $modelDDT->FillDDTInfo($doc_set);
                    break;
                
                case 'invoice':
                    $docs_list  = $modelInvoice->FillInvoiceInfo($doc_set);
                    break;
                
                case 'sc':
                    $docs_list  = $modelSC->FillSCInfo($doc_set);
                    break;
                
                case 'qc':
                    $docs_list  = $modelQC->FillQCInfo($doc_set);
                    break;
                
                case 'ra':
                    $docs_list  = $modelRA->FillRAInfo($doc_set);
                    break;
                
                default: continue;
            }
            
            if (empty($docs_list)) continue;
            
            // адаптация полученного списка документов
            $docs_list_adapted = array();
            foreach ($docs_list as $key => $set)
            {
                if (!array_key_exists($object_alias, $set)) continue;
                
                $docs_list_adapted[$key]                 = $set[$object_alias];
                $docs_list_adapted[$key]['object_alias'] = $object_alias;
                $docs_list_adapted[$key]['object_id']    = $set[$object_alias]['id'];
            }
            
            // добавление новых документов в общий список
            $list = array_merge($list, $docs_list_adapted);
        }
        
        return $list;
    }
}