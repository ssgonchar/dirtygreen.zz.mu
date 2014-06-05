<?php
require_once APP_PATH . 'classes/models/biz.class.php';
require_once APP_PATH . 'classes/models/company.class.php';
require_once APP_PATH . 'classes/models/invoicingtype.class.php';
require_once APP_PATH . 'classes/models/person.class.php';
require_once APP_PATH . 'classes/models/paymenttype.class.php';
require_once APP_PATH . 'classes/models/steelposition.class.php';
require_once APP_PATH . 'classes/models/steelgrade.class.php';
require_once APP_PATH . 'classes/models/user.class.php';

/**
 * Класс для работы с временным заказом
 * @version 20120422, zharkov
 */
class PreOrder extends Model
{
    function PreOrder()
    {
        Model::Model('preorders');
    }

    /**
     * Закрепляет выбранные айтемы из предзаказа за заказом
     * 
     * @param mixed $guid
     * @param mixed $order_id
     */
    function ItemsMoveToOrder($guid, $order_id)
    {
        $result = $this->CallStoredProcedure('sp_preorder_get_items', array($this->user_id, $guid));
        $result = isset($result) && isset($result[0]) ? $result[0] : array();
        
        $orders     = new Order();
        $positions  = array();
        foreach ($result as $row) 
        {
            $result = $orders->AddItem($order_id, 0, $row['item_id']);
            foreach ($result as $position_id)
            {
                if (!in_array($position_id, $positions)) $positions[] = $position_id;
            }
        }
        
        return $positions;
    }
    
    
    /**
     * Удаляет предзаказ
     * 
     * @param mixed $order_id
     */
    function Remove($guid)
    {
        // удаляет заказ
        $result = $this->CallStoredProcedure('sp_preorder_remove', array($this->user_id, $guid));
        
        Cache::ClearTag('preorder-' . $guid);
        Cache::ClearTag('preorder-' . $guid . '-positions');
        Cache::ClearTag('preorders-user-' . $this->user_id);
    }
        
    /**
     * Добавляет айтемы в предзаказ
     * 
     * @param mixed $guid
     * @param mixed $ids
     */
    function ItemsAddFromStock($guid, $item_ids)
    {
        $result = $this->CallStoredProcedure('sp_preorder_items_add_from_stock', array($this->user_id, $guid, $item_ids));
        Cache::ClearTag('preorder-' . $guid . '-positions');
        
        return isset($result[0]) ? $result[0] : array();
    }

    /**
     * Добавляет айтемы в предзаказ
     * 
     * @param mixed $guid
     * @param mixed $position_ids
     */
    function PositionsAddFromStock($guid, $position_ids)
    {
        foreach (explode(',', $position_ids) as $position_id)
        {
            $result = $this->CallStoredProcedure('sp_preorder_position_add_from_stock', array($this->user_id, $guid, $position_id));
            Cache::ClearTag('preorder-' . $guid . '-positions');            
        }        
    }
    
    /**
     * Возвращает список предзаказов
     * 
     */
    function GetList()
    {
        $hash       = 'preorders-user-' . $this->user_id;
        $cache_tags = array($hash, 'preorders');
        
        $rowset = $this->_get_cached_data($hash, 'sp_preorder_get_list', array($this->user_id), $cache_tags);
        $rowset = isset($rowset[0]) ? $rowset[0] : array();
        
        $companies  = new Company();
        $rowset     = $companies->FillCompanyInfo($rowset);

        $bizes      = new Biz();
        $rowset     = $bizes->FillBizInfo($rowset);
        
        $users      = new User();
        $rowset     = $users->FillUserInfo($rowset, 'created_by',   'author');
        $rowset     = $users->FillUserInfo($rowset, 'modified_by',  'modifier');
        
        $invoicingtypes = new InvoicingType();
        $rowset         = $invoicingtypes->FillInvoicingTypeInfo($rowset);

        $paymenttypes   = new PaymentType();
        $rowset         = $paymenttypes->FillPaymentTypeInfo($rowset);

        foreach ($rowset as $key => $row)
        {
            if (isset($row['biz'])) $rowset[$key]['biz_title'] = $row['biz']['number_output'];
            
            $rowset[$key]['order_for_co'] = $companies->GetByAlias($row['order_for']);
            
            if (!empty($row['delivery_point']))
            {
                if ($row['delivery_point'] == 'col')
                {
                    $rowset[$key]['delivery_point_title'] = 'Collected';
                }
                else if ($row['delivery_point'] == 'del')
                {
                    $rowset[$key]['delivery_point_title'] = 'Delivered';
                }
                else
                {
                    $rowset[$key]['delivery_point_title'] = strtoupper($row['delivery_point']);
                }
            }
            
            $rowset[$key]['qtty']   = 0;
            $rowset[$key]['weight'] = 0;
            $rowset[$key]['value']  = 0;
            
            $positions = $this->GetPositions($row['guid']);
            foreach ($positions as $position)
            {
                $rowset[$key]['qtty']   += $position['qtty'];
                $rowset[$key]['weight'] += $position['weight'];
                $rowset[$key]['value']  += $position['value'];
            }
        }

        return $rowset;
    }

    /**
     * Возвращает айтемы позиции предзаказа
     * 
     * @param mixed $guid
     */
    function GetPositionItems($guid, $position_id)
    {
        $hash           = 'preorder-' . $guid . '-position-' . $position_id . '-items';
        $cache_tags     = array($hash, 'preorder-' . $guid . '-positions', 'preorder-' . $guid, 'position-' . $position_id);
        
        $rowset         = $this->_get_cached_data($hash, 'sp_preorder_get_position_items', array($guid, $position_id), $cache_tags);

        $steelitems     = new SteelItem();
        return isset($rowset[0]) ? $steelitems->FillSteelItemInfo($rowset[0]) : array();
    }

    /**
     * Возвращает список позиций предзаказа
     * 
     * @param mixed $guid
     */
    function GetPositions($guid)
    {
        $hash           = 'preorder-' . $guid . '-positions';
        $cache_tags     = array($hash);
        
        $rowset         = $this->_get_cached_data($hash, 'sp_preorder_get_positions', array($guid), $cache_tags);
        $rowset         = isset($rowset[0]) ? $rowset[0] : array();
        
        if (empty($rowset)) return $rowset;
        
        $steelgrades    = new SteelGrade();
        $rowset         = $steelgrades->FillSteelGradeInfo($rowset);

        foreach ($rowset as $key => $row)
        {
            $steelitems = $this->GetPositionItems($guid, $row['position_id']);
            
            $rowset[$key]['steelposition_id']   = $row['position_id'];
            $rowset[$key]['location']           = array();
            $rowset[$key]['plateid']            = array();
            $rowset[$key]['supplier']           = array();
            $rowset[$key]['stockholder']        = array();

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
            }
            
            $rowset[$key]['steelitems'] = $steelitems;
        } 
        
        $steelpositions = new SteelPosition();
        $rowset         = $steelpositions->FillSteelPositionInfo($rowset);        

        return $rowset;
    }
    
    /**
     * Сохраняет позцию предзаказа
     *  
     * @param mixed $guid
     * @param mixed $position_id
     * @param mixed $biz_id
     * @param mixed $dimension_unit
     * @param mixed $weight_unit
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
     * @param mixed $order_status
     * @return resource
     */
    function SavePosition($guid, $position_id, $biz_id, $dimension_unit, $weight_unit, $price_unit, $currency, 
                            $steelgrade_id, $thickness, $width, $length, $unitweight, $qtty, $weight, $price, $value, 
                            $deliverytime, $internal_notes, $order_status)
    {
        // создает новые позиции
        if (empty($position_id))
        {
            $modelSteelPosition = new SteelPosition();
            $result             = $modelSteelPosition->Add(0, 0, 92, $biz_id, 0, 0, $dimension_unit, $weight_unit, $price_unit, $currency, 
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
        $result = $this->CallStoredProcedure('sp_preorder_save_position', array($this->user_id, $guid, $position_id, 
                            $steelgrade_id, $thickness, $thickness_mm, $width, $width_mm, $length, $length_mm, 
                            $unitweight, $unitweight_ton, $qtty, $weight, $weight_ton, 
                            $price, $value, $deliverytime, $internal_notes, $order_status));
                            
        Cache::ClearTag('preorder-' . $guid);
        Cache::ClearTag('preorder-' . $guid . '-positions');
        Cache::ClearTag('steelpositions');
        Cache::ClearTag('steelitems');
        
        return $result;
        
    }
    
    /**
     * Удаляет позицию из предзаказа
     * 
     * @param mixed $guid
     * @param mixed $position_id
     */
    function RemovePosition($guid, $position_id)
    {
        $result = $this->CallStoredProcedure('sp_preorder_remove_position', array($guid, $position_id));
        $result = isset($result) && isset($result[0]) ? $result[0] : null;
        
        if (isset($result))
        {
            foreach ($result as $row) Cache::ClearTag('steelitem-' . $row['steelitem_id']);
        }
        
        Cache::ClearTag('steelposition-' . $position_id);
        Cache::ClearTag('preorder-' . $guid);
        Cache::ClearTag('preorder-' . $guid . '-positions');
        Cache::ClearTag('steelpositions');
        Cache::ClearTag('steelitems');        
    }

    /**
    * Возвращает предзаказ
    * 
    * @param mixed $guid
    */
    function GetByGuid($guid)
    {
        $hash       = 'preorder-' . $guid;
        $cache_tags = array($hash, 'preorders');
        
        $rowset = $this->_get_cached_data($hash, 'sp_preorder_get_by_guid', array($guid), $cache_tags);
        $rowset = isset($rowset[0]) ? $rowset[0] : array();
        
        $companies  = new Company();
        $rowset     = $companies->FillCompanyInfo($rowset);
        
        $companies  = new Company();
        $rowset     = $companies->FillCompanyInfo($rowset);

        $bizes      = new Biz();
        $rowset     = $bizes->FillBizInfo($rowset);
        
        if (isset($rowset[0]))
        {
            $rowset = $rowset[0];
            if (isset($rowset['biz'])) $rowset['biz_title'] = $rowset['biz']['number_output'];
            
            return $rowset;
        }
        
        return array();
    }
    
    /**
     * Сохраняет предзаказ
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
    function Save($guid, $order_for, $biz_id = 0, $company_id = 0, $person_id = 0, $buyer_ref = '', $supplier_ref = '',
                    $delivery_point = '', $delivery_town = '', $delivery_cost = '', $delivery_date = '', $alert_date = null, 
                    $invoicingtype_id = 0, $paymenttype_id = 0, $status = '', $description = '')
    {        
        if ($order_for == 'pa')
        {
            $dimension_unit = 'in'; 
            $weight_unit    = 'lb';
	$price_unit    = 'cwt';
            $currency       = 'usd';            
        }
        else
        {
            $dimension_unit = 'mm'; 
            $weight_unit    = 'mt';
            $price_unit    = 'mt';
            $currency       = 'eur';
        }
        
        if (empty($alert_date)) $alert_date = date('Y-m-d H:i:s');
        
        $result = $this->CallStoredProcedure('sp_preorder_save', array($this->user_id, $guid, $order_for, $biz_id, 
                    $company_id, $person_id, $buyer_ref, $supplier_ref, $delivery_point, $delivery_town, $delivery_cost, 
                    $delivery_date, $alert_date, $invoicingtype_id, $paymenttype_id, $status, 
                    $dimension_unit, $weight_unit, $price_unit, $currency, $description));
        
        $result = isset($result) && isset($result[0]) && isset($result[0][0]) ? $result[0][0] : null;
        
        if (empty($result) || array_key_exists('ErrorCode', $result)) return null;

        Cache::ClearTag('preorders');
        Cache::ClearTag('preorder-' . $guid);
        Cache::ClearTag('preorders-user-' . $this->user_id);
        
        return $result;
    }    
}
