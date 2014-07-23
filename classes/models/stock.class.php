<?php
require_once APP_PATH . 'classes/models/company.class.php';
require_once APP_PATH . 'classes/models/deliverytime.class.php';
require_once APP_PATH . 'classes/models/invoicingtype.class.php';
require_once APP_PATH . 'classes/models/location.class.php';
require_once APP_PATH . 'classes/models/order.class.php';
require_once APP_PATH . 'classes/models/paymenttype.class.php';
require_once APP_PATH . 'classes/models/steelgrade.class.php';

define ('STOCK_EU',   1);
define ('STOCK_US',   2);

class Stock extends Model
{
    /**
     * Текущая ревизия склада
     * 
     * @var mixed
     */
    var $revision = '';
    
    
    function Stock()
    {
        Model::Model('stocks');
        
        $this->revision = Request::GetString('stock_revision', $_REQUEST, '', 12);
    }

    /**
     * Возвращает список складов
     * 
     */
    function GetList()
    {
        $hash       = 'stocks';
        $cache_tags = array($hash);

        $rowset = $this->_get_cached_data($hash, 'sp_stock_get_list', array(), $cache_tags);
        $rowset = isset($rowset[0]) ? $this->FillStockInfo($rowset[0]) : array();

        return $rowset;
    }

    /**
     * Возвращает склад по идентификатору
     *     
     * @param mixed $id
     */
    function GetById($id)
    {
        $dataset = $this->FillStockInfo(array(array('stock_id' => $id)));
        return isset($dataset) && isset($dataset[0]) && isset($dataset[0]['stock']) ? $dataset[0] : null;
    }
    
    /**
     * Возвращет информацию о складе
     * 
     * @param mixed $rowset
     * @param mixed $id_fieldname
     * @param mixed $entityname
     * @param mixed $cache_prefix
     */
    function FillStockInfo($rowset, $id_fieldname = 'stock_id', $entityname = 'stock', $cache_prefix = 'stock')
    {
        $rowset = $this->_fill_entity_info($rowset, $id_fieldname, $entityname, $cache_prefix, 'sp_stock_get_list_by_ids', array('stocks' => ''), array());

        foreach ($rowset AS $key => $row)
        {
            if (!isset($row[$entityname])) continue;
            
            $row = $row[$entityname];
            $rowset[$key][$entityname]['dimensions']         = $row['dimension_unit'] . '/' . $row['weight_unit'];
            $rowset[$key][$entityname]['currency_sign']      = $row['currency'] == 'usd' ? '$' : ($row['currency'] == 'eur' ? '&euro;' : '');
            $rowset[$key][$entityname]['ws_deliverytimes']   = empty($row['deliverytimes']) ? array() : explode(',', $row['deliverytimes']);
            $rowset[$key][$entityname]['ws_columns']         = empty($row['visible_columns']) ? array() : explode(',', $row['visible_columns']);
            $rowset[$key]['stock_invoicingtype_id']   = $row['invoicingtype_id'];
            $rowset[$key]['stock_paymenttype_id']     = $row['paymenttype_id'];
        }
        
        $invoicingtypes = new InvoicingType();
        $rowset         = $invoicingtypes->FillInvoicingTypeInfo($rowset, 'stock_invoicingtype_id', 'stock_invoicingtype');

        $paymenttypes   = new PaymentType();
        $rowset         = $paymenttypes->FillPaymentTypeInfo($rowset, 'stock_paymenttype_id', 'stock_paymenttype');
        
        foreach ($rowset as $key => $row) 
        {
            if (isset($row[$entityname]))
            {
                if (isset($row['stock_invoicingtype']))
                {
                    $rowset[$key][$entityname]['invoicingtype'] = $row['stock_invoicingtype'];
                    unset($rowset[$key]['stock_invoicingtype']);
                }

                if (isset($row['stock_paymenttype']))
                {
                    $rowset[$key][$entityname]['paymenttype'] = $row['stock_paymenttype'];
                    unset($rowset[$key]['stock_paymenttype']);
                }                
            }
            
            unset($rowset[$key]['stock_invoicingtype_id']);
            unset($rowset[$key]['stock_paymenttype_id']);
        }
        
        return $this->FillQuickInfo($rowset, $id_fieldname, $entityname);
    }
    
    /**
     * Возвращает быстроменяющуюся информацию по складу
     * 
     * @param array $rowset
     * @param mixed $id_fieldname
     * @param mixed $entityname
     */
    function FillQuickInfo($rowset, $id_fieldname = 'stock_id', $entityname = 'stock')
    {
        $rowset = $this->_fill_entity_info($rowset, $id_fieldname, 'stockquick', 'stockquick', 'sp_stock_get_quick_by_ids', array('stocks' => '', 'stock' => 'id'), array());

        foreach ($rowset AS $key => $row)
        {
            if (isset($row[$entityname]) && isset($row['stockquick']))
            {
                $rowset[$key][$entityname]['quick'] = $row['stockquick'];
                unset($rowset[$key]['stockquick']);
            }
        }
        
        return $rowset;
    }

    /**
     * Сохраняет настройки склада
     * 
     * @param mixed $id
     * @param mixed $title
     * @param mixed $description
     * @param mixed $dimension_unit
     * @param mixed $weight_unit
     * @param mixed $currency
     * @param mixed $invoicingtype_id
     * @param mixed $paymenttype_id
     * @param mixed $deliverytimes
     * @param mixed $visible_columns
     * @return resource
     */
    function Save($id, $title, $description, $dimension_unit, $weight_unit, $currency, $invoicingtype_id, $paymenttype_id, 
                    $deliverytimes, $visible_columns, $email_for_orders, $order_for)
    {        
        $price_unit = '';
        $result     = $this->CallStoredProcedure('sp_stock_save', array($this->user_id, $id, $title, $description, $dimension_unit, 
                                                $weight_unit, $price_unit, $currency, $invoicingtype_id, $paymenttype_id, $deliverytimes, 
                                                $visible_columns, $email_for_orders, $order_for));
        $result     = isset($result) && isset($result[0]) && isset($result[0][0]) ? $result[0][0] : null;
        
        if (empty($result) || array_key_exists('ErrorCode', $result)) return null;

        Cache::ClearTag('steelpositions-stock-' . $id);
        Cache::ClearTag('stock-' . $id);
        Cache::ClearTag('stocks');
        
        return $result;
    }
    
    /**
     * Возвращает список марок стали для склада
     * 
     * @param mixed $stock_id
     * @param mixed $location_id
     */
    function GetSteelgrades($stock_id, $location_id = 0)
    {
        $hash       = 'stock-' . md5('steelgrades-' . $stock_id . '-location-' . $location_id);
        $cache_tags = array($hash, 'stocks', 'stock-' . $stock_id, 'stock-' . $stock_id . '-location-' . $location_id, 'stock-steelgrades-' . $stock_id, 'stockquick-' . $stock_id, 'steelitems-filter');

        $rowset     = $this->_get_cached_data($hash, 'sp_stock_get_steelgrades', array($stock_id, $location_id, $this->revision), $cache_tags);
        $rowset     = isset($rowset[0]) ? $rowset[0] : array();
                           
        if (empty($rowset) || (isset($rowset[0]) && isset($rowset[0]['ErrorCode'])))
        {
            return array();
        }

        $steelgrades    = new SteelGrade();
        $rowset         = isset($rowset) ? $steelgrades->FillSteelGradeInfo($rowset) : array();

        return $rowset;        
    }

    /**
     * Возвращает список location для склада
     * 
     * @param mixed $stock_id
     */
    function GetLocations($stock_id, $strict = true)
    {
        $hash       = 'stock-locations-' . $stock_id . '-strict-' . ($strict ? 1 : 0);
        $cache_tags = array($hash, 'stocks', 'stock-' . $stock_id, 'stockquick-' . $stock_id, 'stock-locations-' . $stock_id);

        $rowset     = $this->_get_cached_data($hash, 'sp_stock_get_locations', array($stock_id, $strict), $cache_tags);
        
        $companies  = new Company();
        $rowset     = isset($rowset[0]) ? $companies->FillCompanyInfoShort($rowset[0], 'company_id', 'company') : array();        
        
        foreach ($rowset as $key => $row)
        {
            if (isset($row['company']) && !empty($row['company']['location_id']))
            {
                $rowset[$key]['stocklocation_id'] = $row['company']['location_id'];
            }
        }
        
        $locations  = new Location();
        $rowset     = $locations->FillLocationInfo($rowset, 'stocklocation_id', 'stocklocation');

        foreach ($rowset as $key => $row)
        {
            if (isset($row['company']) && isset($row['stocklocation']))
            {
                $rowset[$key]['company']['stocklocation'] = $row['stocklocation'];
                
                unset($rowset[$key]['stocklocation_id']);
                unset($rowset[$key]['stocklocation']);
            }
        }        
        
        return $rowset;        
        //debug("1682", $rowset);
    }
    
    /**
     * Возвращает список location для позиций
     * 
     * @param mixed $stock_id
     */
    function GetPositionLocations($stock_id)
    {
        $hash       = 'steelpositions-stock-' . md5($stock_id . '-locations-rev-' . $this->revision);
        $cache_tags = array($hash, 'steelpositions', 'steelpositions-stock-' . $stock_id, 'steelitems', 'steelitems-stock-' . $stock_id, 'steelitems-filter');

        $rowset     = $this->_get_cached_data($hash, 'sp_stock_get_position_locations', array($stock_id, $this->revision), $cache_tags);
        
        $locations  = new Location();
        $rowset     = isset($rowset[0]) ? $locations->FillLocationInfo($rowset[0], 'location_id', 'location') : array();        

        return $rowset;        
    }
    
    /**
     * Возвращает список deliverytime для позиций
     * 
     * @param mixed $stock_id
     */
    function GetPositionDeliveryTimes($stock_id)
    {
        $hash       = 'steelpositions-stock-' . md5($stock_id . '-deliverytimes-rev-' . $this->revision);
        $cache_tags = array($hash, 'steelpositions', 'steelpositions-stock-' . $stock_id);

        $rowset     = $this->_get_cached_data($hash, 'sp_stock_get_position_deliverytimes', array($stock_id, $this->revision), $cache_tags);
        
        $deliverytimes  = new DeliveryTime();
        $rowset         = isset($rowset[0]) ? $deliverytimes->FillDeliveryTimeInfo($rowset[0]) : array();

        return $rowset;        
    }
    
    /**
     * Возвращает список stockholders для айтемов
     * 
     * @param mixed $stock_id
     */
    function GetItemLocations($stock_id)
    {
        $hash       = 'steelitems-stock-' . md5($stock_id . '-locations-rev-' . $this->revision);
        $cache_tags = array($hash, 'steelitems', 'steelitems-stock-' . $stock_id, 'steelitems-filter');

        $rowset     = $this->_get_cached_data($hash, 'sp_stock_get_item_locations', array($stock_id, $this->revision), $cache_tags);
        
        $companies  = new Company();
        $rowset     = isset($rowset[0]) ? $companies->FillCompanyInfo($rowset[0], 'stockholder_id', 'stockholder') : array();

        return $rowset;        
    }

    /**
     * Возвращает список deliverytimes для айтемов
     * 
     * @param mixed $stock_id
     */
    function GetItemDeliveryTimes($stock_id)
    {
        $hash       = 'steelitems-stock-' . md5($stock_id . '-deliverytimes-rev-' . $this->revision);
        $cache_tags = array($hash, 'steelitems', 'steelitems-stock-' . $stock_id, 'steelitems-filter');

        $rowset     = $this->_get_cached_data($hash, 'sp_stock_get_item_deliverytimes', array($stock_id, $this->revision), $cache_tags);
        
        $deliverytimes  = new DeliveryTime();
        $rowset         = isset($rowset[0]) ? $deliverytimes->FillDeliveryTimeInfo($rowset[0]) : array();

        return $rowset;        
    }

    /**
     * Возвращает список steelgrades для айтемов
     * 
     * @param mixed $stock_id
     */
    function GetItemSteelGrades($stock_id)
    {
        $hash       = 'steelitems-stock-' . md5($stock_id . '-steelgrades-rev-' . $this->revision);
        $cache_tags = array($hash, 'steelitems', 'steelitems-stock-' . $stock_id, 'steelitems-filter');

        $rowset     = $this->_get_cached_data($hash, 'sp_stock_get_item_steelgrades', array($stock_id, $this->revision), $cache_tags);
        
        $steelgrades    = new SteelGrade();
        $rowset         = isset($rowset[0]) ? $steelgrades->FillSteelGradeInfo($rowset[0]) : array();

        return $rowset;        
    }
    
    /**
     * Возвращает список orders для айтемов
     * 
     * @param mixed $stock_id
     */
    function GetItemOrders($stock_id)
    {
        $hash       = 'steelitems-stock-' . md5($stock_id . '-orders-rev-' . $this->revision);
        $cache_tags = array($hash, 'steelitems', 'steelitems-stock-' . $stock_id, 'steelitems-filter');
        
        $rowset     = $this->_get_cached_data($hash, 'sp_stock_get_item_orders', array($stock_id, $this->revision), $cache_tags);
        
        $modelOrder     = new Order();
        $rowset         = isset($rowset[0]) ? $modelOrder->FillOrderInfo($rowset[0]) : array();
        
        return $rowset;
    }
    
    
    /**
     * Добавляет привязку location к складу
     * 
     * @param mixed $stock_id
     * @param mixed $location_id
     */
    function SaveLocation($stock_id, $company_id)
    {
        $result = $this->CallStoredProcedure('sp_stock_save_location', array($this->user_id, $stock_id, $company_id));
        $result = isset($result) && isset($result[0]) && isset($result[0][0]) ? $result[0][0] : null;

        Cache::ClearTag('stock-locations-' . $stock_id);
    }

    /**
     * Удаляет привязку location к складу
     * 
     * @param mixed $stock_id
     * @param mixed $location_id
     */
    function RemoveLocation($stock_id, $stockholder_id)
    {
        $result = $this->CallStoredProcedure('sp_stock_remove_location', array($this->user_id, $stock_id, $stockholder_id));
        $result = isset($result) && isset($result[0]) && isset($result[0][0]) ? $result[0][0] : null;
        
        Cache::ClearTag('stock-locations-' . $stock_id);
    }
    
    /**
     * Проверяет существует ли ревизия склада за дату и время
     * 
     * @param mixed $rev_date
     * @param mixed $rev_time
     * @return mixed
     */
    function CheckRevision($rev_date, $rev_time)
    {
        $hash   = 'stock-revision-' . md5('date-' . $rev_date . '-time-' . $rev_time);        
        $result = Cache::GetData($hash);

        if (!isset($result) || !isset($result['data']) || isset($result['outdated']))
        {
            $rev_date = str_replace('00:00:00', $rev_time . ':59', $rev_date);            
            
            if ($rev_date >= date('Y-m-d H:i:s')) return null;
            
            $result = $this->CallStoredProcedure('sp_stock_check_revision', array($this->user_id, $rev_date));
            $result = isset($result) && isset($result[0]) && isset($result[0][0]) ? $result[0][0]['revision_id'] : null;
            
            if (empty($result)) return null;
            
            Cache::SetData($hash, $result, null, CACHE_LIFETIME_STANDARD);            
        }
        else
        {
            $result = $result['data'];
        }
        
        return $result;
    }
}
