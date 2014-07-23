<?php
require_once APP_PATH . 'classes/models/biz.class.php';
require_once APP_PATH . 'classes/models/company.class.php';
require_once APP_PATH . 'classes/models/deliverytime.class.php';
require_once APP_PATH . 'classes/models/person.class.php';
require_once APP_PATH . 'classes/models/steelitem.class.php';
require_once APP_PATH . 'classes/models/user.class.php';


class SteelPosition extends Model
{
    /**
     * Текущая ревизия склада
     * 
     * @var mixed
     */
    var $revision = '';

    
    function SteelPosition()
    {
        Model::Model('steelpositions');
        
        $this->revision = Request::GetString('stock_revision', $_REQUEST, '', 12);
    }
    
    /**
     * Возвращает список позиций по id
     * 
     * @param mixed $rowset
     * @return mixed
     */
    function GetByIds($rowset)
    {
        return $this->FillSteelPositionInfo($rowset);
    }

    /**
     * Возвращает список айтемов для позиции
     * 
     * @param mixed $steelposition_id
     */
    function GetItems($steelposition_id, $only_active = true)
    {
        $hash       = 'steelposition-items-' . $steelposition_id . '-only_active-' . $only_active . '-rev-' . $this->revision;
        $cache_tags = array($hash, 'steelposition-' . $steelposition_id . '-items', 'steelposition-' . $steelposition_id, 'steelpositions', 'steelitems');
        
        $rowset     = $this->_get_cached_data($hash, 'sp_steelposition_get_items', array($this->user_id, $steelposition_id, $only_active, $this->revision), $cache_tags);

        $steelitems = new SteelItem();
        return isset($rowset[0]) ? $steelitems->FillSteelItemInfo($rowset[0]) : array();
    }
        
    /**
     * Возвращает список позиций
     * 
     * @param mixed $product_id
     * @param mixed $stock_id
     * @param mixed $location_id
     * @param string $deliverytime
     * @param mixed $steelgrade_id
     * @param mixed $thickness
     * @param mixed $width
     * @param mixed $length
     * @param mixed $weight
     * @param mixed $keyword
     * @param mixed $dimension_unit
     * @param mixed $weight_unit
     */
    function GetList($product_id, $stock_id, $location_ids, $stockholder_ids, $deliverytime_ids, $steelgrade_ids, $thickness, $thickness_min, $thickness_max, $width, $width_min, $width_max, 
                    $length, $length_min, $length_max, $weight, $weight_min, $weight_max, $keyword, $dimension_unit, $weight_unit)
    {
        $hash       =   'steelpositions-' . md5('stock-' . $stock_id . '-locations-' . $location_ids . '-stockholder_ids-' . $stockholder_ids .
                        '-deliverytimes-' . $deliverytime_ids . '-steelgrades-' . $steelgrade_ids . '-thickness-' . $thickness . '-thicknessmin-' . $thickness_min . '-thicknessmax-' . 
                        $thickness_max . '-width-' . $width . '-widthmin-' . $width_min . '-widthmax-' . $width_max . '-length-' . $length . '-lengthmin-' . $length_min .
                        '-lengthmax-' . $length_max . '-weight-' . $weight . '-weightmin-' . $weight_min . '-weightmax-' . $weight_max . '-keyword-' . $keyword . '-rev-' . $this->revision);
        $cache_tags = array($hash, 'steelpositions-stock-' . $stock_id, 'steelpositions');

        /* 20130819, sasha
        $thickness  = $this->_get_interval($thickness);
        $width      = $this->_get_interval($width);
        $length     = $this->_get_interval($length);
        $weight     = $this->_get_interval($weight);
        */
        //debug('1682', $thickness);
        if ($thickness > 0)
        {
            $thickness_min  = $thickness;
            $thickness_max  = $thickness;
            //debug('1682', $thickness_max);
        }

        if ($width > 0)
        {
            $width_min  = $width;
            $width_max  = $width;
        }

        if ($length > 0)
        {
            $length_min  = $length;
            $length_max  = $length;
        }

        if ($weight > 0)
        {
            $weight_min  = $weight;
            $weight_max  = $weight;
        }
             
        if ($dimension_unit == 'in')
        {
            $thickness_min  = empty($thickness) ? $thickness_min * 25.4 : $thickness * 25.4;
            $thickness_max  = empty($thickness) ? $thickness_max * 25.4 : $thickness * 25.4;
            $width_min      = empty($width) ? $width_min * 25.4 : $width * 25.4;
            $width_max      = empty($width) ? $width_max * 25.4 : $width * 25.4;
            $length_min     = empty($length) ? $length_min * 25.4 : $length * 25.4;
            $length_max     = empty($length) ? $length_max * 25.4 : $length * 25.4;            
        }
        
        if ($weight_unit == 'lb')
        {
            $weight_min = empty($weight) ? $weight_min / 2200 : $weight / 2200;
            $weight_max = empty($weight) ? $weight_max / 2200 : $weight / 2200;
        }
        
        
        $rowset = $this->_get_cached_data($hash, 'sp_steelposition_get_list', array($this->user_id, $product_id, $stock_id, $location_ids, $stockholder_ids, $deliverytime_ids, $steelgrade_ids, 
                                            $thickness, $thickness_min, $thickness_max, $width_min, $width_max, $length_min, $length_max, 
                                            $weight_min, $weight_max, $keyword, $this->revision), $cache_tags);

        $rowset = isset($rowset[0]) ? $this->FillSteelPositionInfo($rowset[0]) : array();
 
        // помечает позиции, которые отображаются на складе
        if ($stock_id > 0)
        {
            $stocks = new Stock();
            $stock  = $stocks->GetById($stock_id);
            
            if (!empty($stock))
            {
                $deliverytimes = $stock['stock']['ws_deliverytimes'];
                
                foreach ($rowset as $key => $row)
                {
                    if (!isset($row['steelposition']) || empty($row['steelposition'])) continue;                    
                    $rowset[$key]['steelposition']['on_stock'] = in_array($row['steelposition']['deliverytime_id'], $deliverytimes);
                }
            }
        }

        return $rowset;        
    }
    
    
    /**
     * Возвращает список Id позиций
     * 
     * @param mixed $product_id
     * @param mixed $stock_id
     * @param mixed $location_id
     * @param string $deliverytime
     * @param mixed $steelgrade_id
     * @param mixed $thickness
     * @param mixed $width
     * @param mixed $length
     * @param mixed $weight
     * @param mixed $keyword
     * @param mixed $dimension_unit
     * @param mixed $weight_unit
     */
    function GetIdsList($product_id, $stock_id, $location_ids, $stockholder_ids, $deliverytime_ids, $steelgrade_ids, $thickness, $thickness_min, $thickness_max, $width, $width_min, $width_max, 
                    $length, $length_min, $length_max, $weight, $weight_min, $weight_max, $keyword, $dimension_unit, $weight_unit)
    {
        $hash       =   'steelpositions-' . md5('stock-' . $stock_id . '-locations-' . $location_ids . '-stockholder_ids-' . $stockholder_ids .
                        '-deliverytimes-' . $deliverytime_ids . '-steelgrades-' . $steelgrade_ids . '-thickness-' . $thickness . '-thicknessmin-' . $thickness_min . '-thicknessmax-' . 
                        $thickness_max . '-width-' . $width . '-widthmin-' . $width_min . '-widthmax-' . $width_max . '-length-' . $length . '-lengthmin-' . $length_min .
                        '-lengthmax-' . $length_max . '-weight-' . $weight . '-weightmin-' . $weight_min . '-weightmax-' . $weight_max . '-keyword-' . $keyword . '-rev-' . $this->revision);
        $cache_tags = array($hash, 'steelpositions-stock-' . $stock_id, 'steelpositions');
        
        if ($thickness > 0)
        {
            $thickness_min  = $thickness;
            $thickness_max  = $thickness;
            //debug('1682', $thickness_max);
        }

        if ($width > 0)
        {
            $width_min  = $width;
            $width_max  = $width;
        }

        if ($length > 0)
        {
            $length_min  = $length;
            $length_max  = $length;
        }

        if ($weight > 0)
        {
            $weight_min  = $weight;
            $weight_max  = $weight;
        }
             
        if ($dimension_unit == 'in')
        {
            $thickness_min  = empty($thickness) ? $thickness_min * 25.4 : $thickness * 25.4;
            $thickness_max  = empty($thickness) ? $thickness_max * 25.4 : $thickness * 25.4;
            $width_min      = empty($width) ? $width_min * 25.4 : $width * 25.4;
            $width_max      = empty($width) ? $width_max * 25.4 : $width * 25.4;
            $length_min     = empty($length) ? $length_min * 25.4 : $length * 25.4;
            $length_max     = empty($length) ? $length_max * 25.4 : $length * 25.4;            
        }
        
        if ($weight_unit == 'lb')
        {
            $weight_min = empty($weight) ? $weight_min / 2200 : $weight / 2200;
            $weight_max = empty($weight) ? $weight_max / 2200 : $weight / 2200;
        }
        
        
        $rowset = $this->_get_cached_data($hash, 'sp_steelposition_get_list', array($this->user_id, $product_id, $stock_id, $location_ids, $stockholder_ids, $deliverytime_ids, $steelgrade_ids, 
                                            $thickness, $thickness_min, $thickness_max, $width_min, $width_max, $length_min, $length_max, 
                                            $weight_min, $weight_max, $keyword, $this->revision), $cache_tags);
        return $rowset;
    }
    
    /**
     * Возвращает позицию по идентификатору
     *     
     * @param mixed $id
     */
    function GetById($id)
    {
        $dataset = $this->FillSteelPositionInfo(array(array('steelposition_id' => $id)));
        return isset($dataset) && isset($dataset[0]) && isset($dataset[0]['steelposition']) ? $dataset[0] : null;
    }
    
    /**
     * Заполняет данные позиции
     * 
     * @param array $rowset
     * @return array
     */
    function FillSteelPositionInfo($rowset, $only_active = true, $id_fieldname = 'steelposition_id')
    {
        $rowset = $this->_fill_entity_info($rowset, $id_fieldname, 'steelposition', 'steelposition', 'sp_steelposition_get_list_by_ids', array('steelpositions' => ''), array($this->revision));
        $rowset = $this->FillQuickInfo($rowset);
        
        $modelCompany = new Company();
        foreach ($rowset as $key => $row)
        {
            if (isset($row['steelposition'])) 
            {
                if (isset($row['steelposition']['steelgrade_id'])) $rowset[$key]['position_steelgrade_id'] = $row['steelposition']['steelgrade_id'];
                if (isset($row['steelposition']['deliverytime_id'])) $rowset[$key]['position_deliverytime_id'] = $row['steelposition']['deliverytime_id'];
                if (isset($row['steelposition']['biz_id'])) $rowset[$key]['position_biz_id'] = $row['steelposition']['biz_id'];
                
                $rowset[$key]['steelposition']['currency_sign'] = $row['steelposition']['currency'] == 'usd' ? '$' : ($row['steelposition']['currency'] == 'eur' ? '&euro;' : '');
            }
            
            if (isset($row['steelpositionquick']) && !empty($row['steelpositionquick'])) 
            {
                if (isset($row['steelpositionquick']['supplier_ids']) && !empty($row['steelpositionquick']['supplier_ids']))
                {
                    $arr = array();
                    foreach (explode(',', $row['steelpositionquick']['supplier_ids']) as $supplier_id)
                    {
                        $arr[] = array('supplier_id' => $supplier_id);
                    }
                    
                    $row['steelpositionquick']['suppliers'] = $modelCompany->FillCompanyInfoShort($arr, 'supplier_id', 'supplier');
                }

                
                $rowset[$key]['steelposition']['quick'] = $row['steelpositionquick'];
                unset($rowset[$key]['steelpositionquick']);                
            }            
        }

        $steelgrades    = new SteelGrade();
        $rowset         = $steelgrades->FillSteelGradeInfo($rowset, 'position_steelgrade_id', 'position_steelgrade');

        $deliverytimes  = new DeliveryTime();
        $rowset         = $deliverytimes->FillDeliveryTimeInfo($rowset, 'position_deliverytime_id', 'position_deliverytime');

        $bizs   = new Biz();
        $rowset = $bizs->FillMainBizInfo($rowset, 'position_biz_id', 'position_biz');
        
        foreach ($rowset as $key => $row)
        {
            if (isset($row['steelposition']))
            {
                // установка флага использования позиции                
                if (self::IsLocked($row['steelposition']['id']))
                {
                    $rowset[$key]['steelposition']['inuse']     = true;
                    $rowset[$key]['steelposition']['inuse_by']  = self::LockedBy($row['steelposition']['id']);
                }
                else
                {
                    $rowset[$key]['steelposition']['inuse'] = false;
                }
         
         
                if (isset($row['position_deliverytime']))
                {
                    $rowset[$key]['steelposition']['deliverytime'] = $row['position_deliverytime'];
                }                
                
                if (isset($row['position_steelgrade']))
                {
                    $rowset[$key]['steelposition']['steelgrade']    = $row['position_steelgrade'];
                    $rowset[$key]['steelposition']['bgcolor']       = $row['position_steelgrade']['bgcolor'];
                }

                if (isset($row['position_biz']))
                {
                    $rowset[$key]['steelposition']['biz'] = $row['position_biz'];                    
                }
            }
            
            unset($rowset[$key]['position_deliverytime']);
            unset($rowset[$key]['position_deliverytime_id']);            

            unset($rowset[$key]['position_steelgrade']);
            unset($rowset[$key]['position_steelgrade_id']);              
            
            unset($rowset[$key]['position_biz']);
            unset($rowset[$key]['position_biz_id']);            
            
        }
        //dg($rowset);
        return $rowset;
    }
    
    /**
     * Возвращает быстроизменяющиеся данны по позиции
     * 
     * @param array $recordset
     * @return array
     */
    function FillQuickInfo($recordset)
    {
        return $this->_fill_entity_info($recordset, 'steelposition_id', 'steelpositionquick', 'steelpositionquick', 'sp_steelposition_get_quick_by_ids', array('steelpositions' => '', 'steelposition' => 'id'), array());
    }    
    
    
    /**
     * Сохраняет позицию
     * 
     * @param mixed $stock_id
     * @param mixed $product_id
     * @param mixed $biz_id
     * @param mixed $dimension_unit
     * @param mixed $weight_unit
     * @param mixed $price_unit
     * @param mixed $currency
     * @param mixed $thickness
     * @param mixed $thickness_mm
     * @param mixed $width
     * @param mixed $width_mm
     * @param mixed $length
     * @param mixed $length_mm
     * @param mixed $unitweight
     * @param mixed $unitweight_ton
     * @param mixed $qtty
     * @param mixed $weight
     * @param mixed $weight_ton
     * @param mixed $price
     * @param mixed $value
     * @param mixed $delivery_time
     * @param mixed $notes
     * @param mixed $internal_notes
     * @return resource
     */
    function Add($id, $stock_id, $product_id, $biz_id, $location_id, $supplier_id, $dimension_unit, $weight_unit, $price_unit, $currency, 
                    $steelgrade_id, $thickness, $width, $length, $unitweight, $qtty, $weight, $price, $value, 
                    $delivery_time, $notes, $internal_notes)
    {        
        /*
        *   Тут получить location_id из stockholder_id (location_id)
        */
        $result = $this->Save($id, $stock_id, $product_id, $biz_id, 0, $supplier_id, $dimension_unit, $weight_unit, $price_unit, $currency, 
                                $steelgrade_id, $thickness, $width, $length, $unitweight, $qtty, $weight, $price, $value, 
                                $delivery_time, $notes, $internal_notes);
                                
        if (empty($result)) return null;                                
        
        // если создается новая позиция, для нее добавляются айтемы
        if (empty($id))
        {
            $steelitem = new SteelItem();        
            for ($i = 0; $i < $qtty; $i++)
            {
                $item = $steelitem->Save(0, $result['id'], '', $product_id, $biz_id, $location_id, $dimension_unit, $weight_unit, $price_unit, $currency, $steelgrade_id, 
                        $thickness, 0, $width, 0, 0, $length, 0, 0, $unitweight, $price, $unitweight * $price, '', '', '', 
                        $supplier_id);
            }            
        }
        
        return $result;
    }
    
    /**
     * Создает новую позицию без автоматического добавления в нее айтемов
     * 
     * @param mixed $id
     * @param mixed $stock_id
     * @param mixed $product_id
     * @param mixed $biz_id
     * @param mixed $location_id
     * @param mixed $supplier_id
     * @param mixed $dimension_unit
     * @param mixed $weight_unit
     * @param mixed $price_unit
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
     * @param mixed $delivery_time
     * @param mixed $notes
     * @param mixed $internal_notes
     */
    function Save($id, $stock_id, $product_id, $biz_id, $location_id, $supplier_id, $dimension_unit, $weight_unit, $price_unit, $currency, 
                    $steelgrade_id, $thickness, $width, $length, $unitweight, $qtty, $weight, $price, $value, 
                    $delivery_time, $notes, $internal_notes)
    {
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
        
        $deliverytimes      = new DeliveryTime();
        $deliverytime_id    = $deliverytimes->GetDeliveryTimeId($delivery_time);
        
        $result = $this->CallStoredProcedure('sp_steelposition_save', array($this->user_id, $id, $stock_id, $product_id, $biz_id, $dimension_unit, $weight_unit, $price_unit, $currency, 
                    $steelgrade_id, $thickness, $thickness_mm, $width, $width_mm, $length, $length_mm, $unitweight, $unitweight_ton, 
                    $qtty, $weight, $weight_ton, $price, $value, $deliverytime_id, $notes, $internal_notes));
        $result = isset($result) && isset($result[0]) && isset($result[0][0]) ? $result[0][0] : null;

        if (empty($result) || array_key_exists('ErrorCode', $result)) return null;

        Cache::ClearTag('steelpositions-stock-' . $stock_id);
        Cache::ClearTag('steelposition-' . $result['id']);
        Cache::ClearTag('stockquick-' . $stock_id);        
        
        // в хранимой процедуре обновляются значения айтемов
        if ($id > 0)
        {
            $items = $this->GetItems($id);
            foreach ($items as $row) Cache::ClearTag('steelitem-' . $row['steelitem_id']);
        }

        return $result;        
    }
    
    /**
     * Обновляет количество в позиции, если 0 - удаляет позицию
     * 
     * @param mixed $position_id
     * @return resource
     */
    function UpdateQtty($position_id)
    {
        $position = $this->GetById($position_id);
        $position = $position['steelposition'];

        $this->CallStoredProcedure('sp_steelposition_update_qtty_output', array($this->user_id, $position_id));
        
        Cache::ClearTag('steelposition-' . $position_id);
        Cache::ClearTag('steelpositionquick-' . $position_id);
        Cache::ClearTag('steelposition-items-' . $position_id);
        Cache::ClearTag('steelpositions-stock-' . $position['stock_id']);
        Cache::ClearTag('stockquick-' . $position['stock_id']);
        
        Cache::ClearTag('steelposition-' . $position_id . '-items');
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
    * Возвращает историю изменения позиции
    * 
    * @param mixed $position_id
    */
    function GetHistory($position_id)
    {
        $rowset = $this->CallStoredProcedure('sp_steelposition_get_history', array($this->user_id, $position_id));
        $rowset = $rowset[0];
        
        $steelgrades = new SteelGrade();
        $rowset = $steelgrades->FillSteelGradeInfo($rowset);
        
        $stocks = new Stock();
        $rowset = $stocks->FillStockInfo($rowset);
        
        $bizes = new Biz();
        $rowset = $bizes->FillBizInfo($rowset);
        
        $deliverytimes = new DeliveryTime();
        $rowset = $deliverytimes->FillDeliveryTimeInfo($rowset);

        $users = new User();
        $rowset = $users->FillUserInfo($rowset, 'record_by');
        
        return $rowset;       
    }
    
    /**
     * Возвращает список зарезервированных позиций
     * 
     * @param mixed $company_id
     */
    function ReserveGetList($company_id)
    {
        $hash       = 'steelpositions-reserved-company-' . $company_id;
        $cache_tags = array($hash, 'steelpositions-reserved', 'steelpositions');
        
        $rowset     = $this->_get_cached_data($hash, 'sp_steelposition_reserve_get_list', array($this->user_id, $company_id), $cache_tags);
        $rowset     = isset($rowset[0]) ? $this->FillSteelPositionInfo($rowset[0]) : null;
        
        $companies  = new Company();
        $rowset     = isset($rowset) ? $companies->FillCompanyInfo($rowset) : null;

        $persons    = new Person();
        $rowset     = isset($rowset) ? $persons->FillPersonInfo($rowset) : null;
        
        return $rowset;        
    }    
    
    /**
     * Резарвирует позицию
     * 
     * @param mixed $position_id
     * @param mixed $company_id
     * @param mixed $person_id
     * @param mixed $qtty
     */
    function ReserveAdd($position_id, $qtty, $company_id, $person_id, $period, $order_id = 0)
    {
        $result = $this->CallStoredProcedure('sp_steelposition_reserve_add', array($this->user_id, $position_id, $qtty, $company_id, $person_id, $period, $order_id));
        $result = isset($result[0]) && isset($result[0][0]) ? $result[0][0] : array();
        
        Cache::ClearTag('steelpositions-stock-' . $result['stock_id']);
        Cache::ClearTag('steelpositionquick-' . $result['id']);
        Cache::ClearTag('steelposition-' . $result['id']);        
        Cache::ClearTag('steelpositions-reserved');
    }
    
    /**
     * Убирает позицию из резервации
     * 
     * @param mixed $id
     */
    function ReserveRemove($id)
    {
        $result = $this->CallStoredProcedure('sp_steelposition_reserve_remove', array($this->user_id, $id));
        $result = isset($result[0]) && isset($result[0][0]) ? $result[0][0] : array();

        Cache::ClearTag('steelpositions-stock-' . $result['stock_id']);
        Cache::ClearTag('steelpositionquick-' . $result['id']);
        Cache::ClearTag('steelposition-' . $result['id']);
        Cache::ClearTag('steelpositions-reserved');
    }
    
    /**
     * Возвращает список компаний, которые зарезервировали позиции
     * 
     */
    function ReserveCompanies()
    {
        $hash       = 'steelpositions-reserved-companies';
        $cache_tags = array($hash, 'steelpositions-reserved');
        
        $rowset     = $this->_get_cached_data($hash, 'sp_steelposition_reserve_companies', array(), $cache_tags);

        $companies  = new Company();
        $rowset     = isset($rowset[0]) ? $companies->FillCompanyInfo($rowset[0]) : null;
        
        return $rowset;        
    }
    
    /**
     * Закрывает позицию для редактирования
     * 
     * @param mixed $position_id
     */
    public static function Lock($position_id)
    {
        $data   = Cache::GetData('inuse-pos-' . $position_id);
        $login  = isset($_SESSION['user']) ? Request::GetString('login', $_SESSION['user']) : '';
        
        if (!isset($data) || !isset($data['data']) || isset($data['outdated']))
        {
            Cache::SetData('inuse-pos-' . $position_id, $login, array(), CACHE_LIFETIME_ONLINE);    
        }        
    }
    
    /**
     * Проверяет закрыта ли позиция от редактирования
     * 
     * @param mixed $position_id
     */
    public static function IsLocked($position_id)
    {
        $data   = Cache::GetData('inuse-pos-' . $position_id);
        $login  = isset($_SESSION['user']) ? Request::GetString('login', $_SESSION['user']) : '';
        
        return isset($data) && isset($data['data']) && !isset($data['outdated']) && $data['data'] != $login;
    }
    
    /**
     * Разблокирует позицию для редактирования
     * 
     * @param mixed $position_id
     */
    public static function Unlock($position_id)
    {
        $data   = Cache::GetData('inuse-pos-' . $position_id);
        $login  = isset($_SESSION['user']) ? Request::GetString('login', $_SESSION['user']) : '';
        
        if (isset($data) && isset($data['data']) && !isset($data['outdated']) && $data['data'] == $login)
        {
            Cache::ClearKey('inuse-pos-' . $position_id);
        }
    }
    
    /**
    * Возвращает кто закрыл позицию для редактирования
    * 
    * @param mixed $position_id
    */
    public static function LockedBy($position_id)
    {
        $data = Cache::GetData('inuse-pos-' . $position_id);
        
        if (isset($data) && isset($data['data']) && !isset($data['outdated']))
        {
            return $data['data'];
        }        
        
        return null;
    } 
	
	/**
	 * remove steelpositions from reservation
	 * 
	 * @return type
	 * 
	 * @version 20130725, sasha
	 */
	function ClearExpiredReserve()
	{
        $rowset = $this->CallStoredProcedure('sp_steelposition_reserve_get_expired', array());
        $rowset = isset($rowset[0]) ? $rowset[0] : array();
        
        if (!empty($rowset))
        {
            foreach ($rowset as $row)
            {
                $this->ReserveRemove($row['id']);
            }
        }
	}
    
    /**
     * remove ordered positions from reserve
     * 
     * @param mixed $order_id
     * @version 20130727, zharkov
     */
    function ReserveRemoveByOrder($order_id)
    {
        $rowset = $this->CallStoredProcedure('sp_steelposition_reserve_get_by_order', array($order_id));
        $rowset = isset($rowset[0]) ? $rowset[0] : array();
        
        if (!empty($rowset))
        {
            foreach ($rowset as $row)
            {
                $this->ReserveRemove($row['id']);
            }
        }        
    }
    
    /**
     *изменяет видимость позиции на складе
     *
     *@version 20140521
     *@author Gonchar
     */
    function ChangeVisibility($position_id, $hidden)
    {
        $result = $this->Update($position_id, array('hidden_in_stock' => $hidden));

        //Cache::ClearTag('steelpositions-stock-' . $result['stock_id']);
        Cache::ClearTag('steelpositionquick-' . $result);
        Cache::ClearTag('steelposition-' . $result);     
        return $result;
    }
    
    
    /**
     *изменяет видимость позиции на складе
     *
     *@version 20140521
     *@author Gonchar
     */
    function ChangePrice($position_id, $price)
    {
        $result = $this->Update($position_id, array('price' => $price));

        //Cache::ClearTag('steelpositions-stock-' . $result['stock_id']);
        Cache::ClearTag('steelpositionquick-' . $result);
        Cache::ClearTag('steelposition-' . $result);     
        return $result;
    }    
}
