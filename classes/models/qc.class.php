<?php
require_once APP_PATH . 'classes/models/attachment.class.php';
require_once APP_PATH . 'classes/models/biz.class.php';
require_once APP_PATH . 'classes/models/company.class.php';
require_once APP_PATH . 'classes/models/order.class.php';
require_once APP_PATH . 'classes/models/steelitem.class.php';
require_once APP_PATH . 'classes/models/stock.class.php';

class QC extends Model
{
    function QC()
    {
        Model::Model('qc');
    }

    /**
     * Remove QC
     * 
     * @param mixed $qc_id
     */
    function Remove($qc_id)
    {
        if ($qc_id <= 0) return null;
        
        // remove items from qc
        foreach($this->GetItems($qc_id) as $item)
        {
            $this->RemoveItem($qc_id, $item['steelitem_id']);
        }
        
        Cache::ClearTag('qcs');
        Cache::ClearTag('qc-' . $qc_id);
        
        //remove qc
        return $this->DeleteSingle($qc_id);
    }
    
    /**
     * Подготавливает айтемы для вывода в сертификате, подменяет характеристики twin и cut айтемов на родительские
     *     
     * @param mixed $rowset
     * 
     * @version 20120830, zharkov
     */
    function _prepare_items($rowset)
    {
        foreach ($rowset as $key => $row)
        {
            $row = $row['steelitem'];            
            if (isset($row['parent'])) 
            {
                $rowset[$key]['steelitem']['properties']    = $row['parent']['properties'];
                $rowset[$key]['steelitem']['guid']          = $row['parent']['guid'];
            }
        }

        return $rowset;
    }
    
    
    /**
     * Список сертификатов для заказа
     * 
     * @param mixed $order_id
     */
    function GetListByOrder($order_id)
    {
        $hash       = 'qc-order-' . $order_id;
        $cache_tags = array($hash, 'qcs');        
        
        $rowset     = $this->_get_cached_data($hash, 'sp_qc_get_list_by_order', array($this->user_id, $order_id), $cache_tags);
        $rowset     = isset($rowset[0]) ? $this->FillQCInfo($rowset[0]) : array();

        return $rowset;
    }

    
    /**
     * Возвращает список сертификатов качества
     * 
     * @version 20120814, zharkov
     */
    function GetList($stock_id = 0)
    {
        $hash       = 'qcs-stock-' . $stock_id;
        $cache_tags = array($hash, 'qcs');

        $rowset = $this->_get_cached_data($hash, 'sp_qc_get_list', array($this->user_id, $stock_id), $cache_tags);
        return isset($rowset[0]) ? $this->FillQCInfo($rowset[0]) : array();
    }    
    
    /**
     * Возвращает список айтемов привязанных к сертификату
     * 
     * @param mixed $qc_id
     * @return mixed
     * 
     * @version 20120813, zharkov
     */
    function _get_items($qc_id)
    {
        $hash       = 'qc-' . $qc_id . '-items';
        $cache_tags = array($hash, 'qcs', 'qc-' . $qc_id);

        $rowset = $this->_get_cached_data($hash, 'sp_qc_get_items', array($this->user_id, $qc_id), $cache_tags);
        return isset($rowset[0]) ? $rowset[0] : array();        
    }
    
    /**
     * Возвращает список айтемов сертификата для вывода в ПДФ
     * 
     * @param mixed $qc_id
     * @return array
     * 
     * @version 20120813, zharkov
     */
    function GetItemsForPdf($qc_id)
    {
        $steelitems = new SteelItem();
        $rows       = $steelitems->FillSteelItemInfo($this->_get_items($qc_id));
        $rows       = $this->_prepare_items($rows);
        
        $result     = array();
        
        foreach ($rows as $key => $item)
        {
            if (isset($item['steelitem']))
            {
                $item = $item['steelitem'];
                
                $item['steelgrade_title']   = isset($item['steelgrade']) ? $item['steelgrade']['title'] : '';
                $item['qtty']               = 1;
                
                foreach ($item['properties'] as $property => $value)
                {
                    if ($property == 'elongation')
                    {
                        $value = sprintf('%.1f', $value);
                    }
                    
                    $item['property_' . $property] = $value;    
                }
                
                if (isset($item['nominal_thickness_mm']) && $item['nominal_thickness_mm'] > 0)  $item['thickness_mm'] = $item['nominal_thickness_mm'];
                if (isset($item['nominal_width_mm']) && $item['nominal_width_mm'] > 0)  $item['width_mm'] = $item['nominal_width_mm'];
                if (isset($item['nominal_length_mm']) && $item['nominal_length_mm'] > 0)  $item['length_mm'] = $item['nominal_length_mm'];
            }
            
            $result[] = $item;
        }

        return $result;
    }
    
    
    /**
     * Обновляет данные об атачменте, связанным с документом
     * 
     * @param mixed $qc_id
     * @param mixed $attachment_id
     * 
     * @version 20120813, zharkov
     */
    function UpdateAttachment($qc_id, $attachment_id)
    {
        $this->Update($qc_id, array(
            'attachment_id' => $attachment_id
        ));
    }
    
    /**
     * Созраняет айтемы сертификата
     * 
     * @param mixed $qc_id
     * @param mixed $item_id
     * 
     * @version 20120811, zharkov
     */
    function SaveItem($qc_id, $item_id)
    {
        $result = $this->CallStoredProcedure('sp_qc_save_item', array($this->user_id, $qc_id, $item_id));        
        
        Cache::ClearTag('steelitem-' . $item_id);
        Cache::ClearTag('qc-' . $qc_id . '-items');        
    }

    /**
     * Remove Item from QC
     * 
     * @param mixed $qc_id
     * @param mixed $item_id
     */
    function RemoveItem($qc_id, $item_id)
    {
        $result = $this->CallStoredProcedure('sp_qc_remove_item', array($this->user_id, $qc_id, $item_id));        
        
        Cache::ClearTag('qc-' . $qc_id);
        Cache::ClearTag('qc-' . $qc_id . '-items');
        
        Cache::ClearTag('steelitem-' . $item_id);        
    }
    
    /**
     * Возвращает список айтемов сертификата
     * 
     * @param mixed $qc_id
     * @return array
     * 
     * @version 20120810, zharkov
     */
    function GetItems($qc_id)
    {
        $rowset     = $this->_get_items($qc_id);

        $steelitems = new SteelItem();
        $rowset     = $steelitems->FillSteelItemInfo($rowset);
        $rowset     = $this->_prepare_items($rowset);

        return $rowset;
    }

    /**
     * Возвращает qc по идентификатору
     *     
     * @param mixed $id
     * 
     * @version 20120809, zharkov
     */
    function GetById($id)
    {
        $dataset = $this->FillQCInfo(array(array('qc_id' => $id)));
        return isset($dataset) && isset($dataset[0]) && isset($dataset[0]['qc']) ? $dataset[0] : null;
    }
    
    /**
     * Возвращет информацию о qc
     * 
     * @param mixed $rowset
     * @param mixed $id_fieldname
     * @param mixed $entityname
     * @param mixed $cache_prefix
     * 
     * @version 20120809, zharkov
     */
    function FillQCInfo($rowset, $id_fieldname = 'qc_id', $entityname = 'qc', $cache_prefix = 'qc')
    {
        $rowset = $this->_fill_entity_info($rowset, $id_fieldname, $entityname, $cache_prefix, 'sp_qc_get_list_by_ids', array('qcs' => '', 'qc' => 'id'), array());
        
        foreach ($rowset as $key => $row)
        {
            if (!isset($row[$entityname])) continue;
            
            $row = $row[$entityname];
            
            $rowset[$key][$entityname]['doc_no']    = 'QC' . $row['year'] . '.' . substr((10000 + $row['number']), 1);
            $rowset[$key]['qc_attachment_id']       = $row['attachment_id'];
            $rowset[$key]['qc_company_id']          = $row['customer_id'];
            $rowset[$key]['qc_biz_id']              = $row['biz_id'];
            $rowset[$key]['qc_order_id']            = $row['order_id'];
            $rowset[$key]['qc_author_id']           = $row['created_by'];
            $rowset[$key]['qc_modifier_id']         = $row['modified_by'];
            $rowset[$key]['qc_stock_id']            = $row['stock_id'];
            
            $units = explode('/', $row['units']);
            $rowset[$key][$entityname]['dim_unit']  = $units[0];
            $rowset[$key][$entityname]['wght_unit'] = $units[1];
        }

        $attachments    = new Attachment();
        $rowset         = $attachments->FillAttachmentInfo($rowset, 'qc_attachment_id', 'qc_attachment');
        
        $bizes          = new Biz();
        $rowset         = $bizes->FillMainBizInfo($rowset, 'qc_biz_id', 'qc_biz');
        
        $companies      = new Company();
        $rowset         = $companies->FillCompanyInfoShort($rowset, 'qc_company_id', 'qc_company');
        
        $orders         = new Order();
        $rowset         = $orders->FillOrderMainInfo($rowset, 'qc_order_id', 'qc_order');
        
        $users          = new User();
        $rowset         = $users->FillUserInfo($rowset, 'qc_author_id', 'qc_author');
        $rowset         = $users->FillUserInfo($rowset, 'qc_modifier_id', 'qc_modifier');
        
        $stocks         = new Stock();
        $rowset         = $stocks->FillStockInfo($rowset, 'qc_stock_id', 'qc_stock');

        foreach ($rowset as $key => $row)
        {
            if (isset($row['qc_attachment']) && !empty($row['qc_attachment']))
            {
                $rowset[$key][$entityname]['attachment'] = $row['qc_attachment'];
            }
            
            unset($rowset[$key]['qc_attachment_id']);
            unset($rowset[$key]['qc_attachment']);
            
            if (isset($row['qc_biz']) && !empty($row['qc_biz']))
            {
                $rowset[$key][$entityname]['qcbiz'] = $row['qc_biz'];
            }
            
            unset($rowset[$key]['qc_biz_id']);
            unset($rowset[$key]['qc_biz']);
            
            if (isset($row['qc_company']) && !empty($row['qc_company']))
            {
                $rowset[$key][$entityname]['company'] = $row['qc_company'];
            }
            
            unset($rowset[$key]['qc_company_id']);
            unset($rowset[$key]['qc_company']);
            
            if (isset($row['qc_order']) && !empty($row['qc_order']))
            {
                $rowset[$key][$entityname]['order'] = $row['qc_order'];
            }
            
            unset($rowset[$key]['qc_order_id']);
            unset($rowset[$key]['qc_order']);

            if (isset($row['qc_author']) && !empty($row['qc_author']))
            {
                $rowset[$key][$entityname]['author'] = $row['qc_author'];
            }
            
            unset($rowset[$key]['qc_author_id']);
            unset($rowset[$key]['qc_author']);

            if (isset($row['qc_modifier']) && !empty($row['qc_modifier']))
            {
                $rowset[$key][$entityname]['modifier'] = $row['qc_modifier'];
            }
            
            unset($rowset[$key]['qc_modifier_id']);
            unset($rowset[$key]['qc_modifier']);

            if (isset($row['qc_stock']) && !empty($row['qc_stock']))
            {
                $rowset[$key][$entityname]['stock'] = $row['qc_stock'];
            }
            
            unset($rowset[$key]['qc_stock_id']);
            unset($rowset[$key]['qc_stock']);
            
        }
        
        return $rowset;
    }
    
    /**
     * Сохраняет qc
     * 
     * @param mixed $id
     * @param mixed $title
     * @param mixed $description
     * 
     * @version 20120809, zharkov
     */
    function Save($id, $mam_co, $stock_id, $biz, $biz_id, $order_id, $customer, $customer_id, $certification_standard, $commodity_name, $standard, 
                    $customer_order_no, $manufacturer, $country_of_origin, $surface_quality, $tolerances_on_thickness, 
                    $tolerances_on_flatness, $steelmaking_process, $delivery_conditions, $ultrasonic_test, $marking, $visual_inspection, 
                    $flattening, $stress_relieving, $elongation_in, $sample_direction_in, $ce_mark, $no_weld_repair, $dimensions,
                    $test_ref = '')
    {        
        $result = $this->CallStoredProcedure('sp_qc_save', array($this->user_id, $id, $mam_co, $stock_id, $biz, $biz_id, $order_id, $customer, $customer_id, 
                    $certification_standard, $commodity_name, $standard, $customer_order_no, $manufacturer, $country_of_origin, 
                    $surface_quality, $tolerances_on_thickness, $tolerances_on_flatness, $steelmaking_process, $delivery_conditions, 
                    $ultrasonic_test, $marking, $visual_inspection, $flattening, $stress_relieving, $elongation_in, $sample_direction_in, 
                    $ce_mark, $no_weld_repair, $dimensions, $test_ref));
        
        $qc = isset($result) && isset($result[0]) && isset($result[0][0]) ? $result[0][0] : array();

        Cache::ClearTag('qc-' . $qc['id']);
        Cache::ClearTag('qcs');
        
/* deprecated 201300510, zharkov
        $items = isset($result) && isset($result[1]) ? $result[1] : array();
                
        foreach ($items as $item)
        {
            Cache::ClearTag('steelitem-' . $item['steelitem_id']);
        }        
*/        
        return $qc;
    }    
}
