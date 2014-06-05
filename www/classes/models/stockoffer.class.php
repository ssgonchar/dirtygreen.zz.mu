<?php
require_once APP_PATH . 'classes/models/attachment.class.php';
require_once APP_PATH . 'classes/models/picture.class.php';
require_once APP_PATH . 'classes/models/steelposition.class.php';

define('STOCKOFFER_LANG_ALIAS_EN', 'en');
define('STOCKOFFER_LANG_ALIAS_RU', 'ru');
define('STOCKOFFER_LANG_ALIAS_CN', 'cn');
define('STOCKOFFER_LANG_ALIAS_IT', 'it');

define('STOCKOFFER_SORTBY_STEELGRADE',  'steelgrade');
define('STOCKOFFER_SORTBY_THICKNESS',   'thickness');

class StockOffer extends Model
{
    public function StockOffer()
    {
        Model::Model('stockoffers');
    }
    
    /**
     * Возвращает данные по ID записи
     * 
     * @param int $id
     * @return array
     * 
     * @version 20130228, d10n
     */
    public function GetById($id)
    {
        $rowset = $this->FillStockOfferInfo(array(array('stockoffer_id' => $id)));
        return isset($rowset) && isset($rowset[0]) && isset($rowset[0]['stockoffer']) ? $rowset[0] : null;
    }
    
    /**
     * Сохраняет StockOffer
     * 
     * @param int $id
     * @param string $lang
     * @param string $title
     * @param string $description
     * @param string $header_attachment_id
     * @param string $is_show_header_image
     * @param string $delivery_point
     * @param string $delivery_cost
     * @param string $delivery_time
     * @param string $payment_terms
     * @param int $is_colored
     * @param int $sort_by
     * @param string $columns
     * @param string $quality_certificate
     * @param string $validity
     * @param int $banner1_attachment_id
     * @param int $is_show_banner1
     * @param int $banner2_attachment_id
     * @param int $is_show_banner2
     * @param int $footer_attachment_id
     * @param int $is_show_footer_image
     * @param int $pdf_attachment_id
     * @return array
     * 
     * @version 20130228, d10n
     */
    public function Save($id, $lang, $title, $description, $header_attachment_id, $is_show_header_image,
            $delivery_point, $delivery_cost, $delivery_time, $payment_terms,
            $is_colored, $sort_by, $columns, $quality_certificate, $validity,
            $banner1_attachment_id, $is_show_banner1, $banner2_attachment_id, $is_show_banner2,
            $footer_attachment_id, $is_show_footer_image, $pdf_attachment_id)
    {
        $rowset = $this->CallStoredProcedure('sp_stockoffer_save', array($this->user_id, $id, $lang, $title,
            $description, $header_attachment_id, $is_show_header_image, $delivery_point, $delivery_cost,
            $delivery_time, $payment_terms, $is_colored, $sort_by, $columns, $quality_certificate,
            $validity, $banner1_attachment_id, $is_show_banner1, $banner2_attachment_id, $is_show_banner2,
            $footer_attachment_id, $is_show_footer_image, $pdf_attachment_id));
        
        $rowset = isset($rowset) && isset($rowset[0]) && isset($rowset[0][0]) ? $rowset[0][0] : null;
        
        if (empty($rowset) || array_key_exists('ErrorCode', $rowset)) 
        {
            Log::AddLine(LOG_ERROR, 'sp_stockoffer_save : ' . var_export($rowset, true));
            return array();
        }
        
        Cache::ClearTag('stockoffers');
        if ($id > 0) Cache::ClearTag('stockoffer-' . $id);
        
        return isset($rowset['stockoffer_id']) ? $this->GetById($rowset['stockoffer_id']) : array();
    }

    /**
    * Добавляет позицию
    * 
    * @param int $stockoffer_id
    * @param int $steelposition_id
    * @return array
    * 
    * @version 20130228, d10n
    */
    public function SavePosition($stockoffer_id, $steelposition_id)
    {
        $rowset = $this->CallStoredProcedure('sp_stockoffer_save_position', array($this->user_id, $stockoffer_id, $steelposition_id));
        
        Cache::ClearTag('stockoffer-' . $stockoffer_id);
        Cache::ClearTag('stockoffer-' . $stockoffer_id . '-positions');
        
        return $rowset;
    }
    
    /**
    * Удаляет позицию
    * 
    * @param int $stockoffer_id
    * @param int $steelposition_id
    * 
    * @version 20130228, d10n
    */
    public function RemovePosition($stockoffer_id, $steelposition_id)
    {
        $this->CallStoredProcedure('sp_stockoffer_remove_position', array($stockoffer_id, $steelposition_id));
        
        Cache::ClearTag('stockoffer-' . $stockoffer_id);
        Cache::ClearTag('stockoffer-' . $stockoffer_id . '-positions');
        
        return true;
    }
    
    /**
    * Возвращает список позиций
    * 
    * @param int $stockoffer_id
    * 
    * @version 20130228, d10n
    */
    public function GetPositions($stockoffer_id)
    {
        $hash       = 'stockoffer-' . $stockoffer_id . '-positions';
        $cache_tags = array($hash, 'stockoffers', 'stockoffer-' . $stockoffer_id);
        
        $rowset     = $this->_get_cached_data($hash, 'sp_stockoffer_get_positions', array($stockoffer_id), $cache_tags);
        
        $modelSteelPosition = new SteelPosition();
        $rowset             = isset($rowset[0]) ? $modelSteelPosition->FillSteelPositionInfo($rowset[0]) : array();
        
        //dg($rowset);
        
        return $rowset;
    }
    
    /**
    * Список StockOffer
    *     
    * @param int $page_no
    * @param int $per_page
    * @return array
    * 
    * @version 20130228, d10n
    */
    public function GetList($page_no = 0, $per_page = ITEMS_PER_PAGE)
    {
        $page_no    = $page_no > 0 ? $page_no : 1;
        $per_page   = $per_page < 1 ? ITEMS_PER_PAGE : $per_page;
        $start      = ($page_no - 1) * $per_page;

        $hash       = 'stockoffers' . md5('-page_no' . $page_no . '-start' . $start);
        $cache_tags = array($hash, 'stockoffers');
        
        $data_set   = $this->_get_cached_data($hash, 'sp_stockoffer_get_list', array($start, $per_page), $cache_tags);
        
        if (!isset($data_set[0])) return array('data' => array(), 'count' => 0);
        
        $list       = $this->FillStockOfferInfo($data_set[0]);
        $rows_count = (isset($data_set[1]) && isset($data_set[1][0]) && isset($data_set[1][0]['rows_count'])) ? $data_set[1][0]['rows_count'] : 0;
        
        return array('data' => $list, 'count' => $rows_count);
    }
    
    /**
     * Удаляет StockOffer
     * 
     * @param int $stockoffer_id
     * 
     * @version 20130214, d10n
     */
    public function Remove($stockoffer_id)
    {
        $result = $this->CallStoredProcedure('sp_stockoffer_remove', array($stockoffer_id));
        
        Cache::ClearTag('stockoffers');
        Cache::ClearTag('stockoffer-' . $stockoffer_id);
        Cache::ClearTag('stockoffer-' . $stockoffer_id . '-positions');
        
        return true;
    }
    
    /**
     * Наполняет StockOffer данными
     * 
     * @param array $rowset
     * @param string $id_fieldname
     * @param string $entityname
     * @param string $cache_prefix
     * @return array
     * 
     * @version 20130214, d10n
     */
    public function FillStockOfferInfo($rowset, $id_fieldname = 'stockoffer_id', $entityname = 'stockoffer', $cache_prefix = 'stockoffer')
    {
        return $this->FillStockOfferMainInfo($rowset, $id_fieldname, $entityname, $cache_prefix);
    }
    
    /**
     * Наполняет StockOffer основными данными
     * 
     * @param array $rowset
     * @param string $id_fieldname
     * @param string $entityname
     * @param string $cache_prefix
     * @return array
     * 
     * @version 20130228, d10n
     */
    private function FillStockOfferMainInfo($rowset, $id_fieldname = 'stockoffer_id', $entityname = 'stockoffer', $cache_prefix = 'stockoffer')
    {
        $rowset = $this->_fill_entity_info($rowset, $id_fieldname, $entityname, $cache_prefix, 'sp_stockoffer_get_list_by_ids', array('stockoffers' => '', 'stockoffer' => 'id'), array());

        foreach ($rowset as $key => $row)
        {
            if (isset($row[$entityname]))
            {
                $row = $row[$entityname];
                
                //$rowset[$key][$entityname]['number'] = sprintf('%02d', $row['id']) . '/' . date('Y', strtotime($row['created_at']));
                $rowset[$key][$entityname]['doc_no'] = empty($row['title']) ? 'so' . sprintf('%04d', $row['id']) : $row['title'];
                
                $rowset[$key]['stockoffer_author_id']   = $row['created_by'];
                $rowset[$key]['stockoffer_modifier_id'] = $row['modified_by'];
                
                $rowset[$key]['stockoffer_header_attachment_id']    = $row['header_attachment_id'];
                $rowset[$key]['stockoffer_banner1_attachment_id']   = $row['banner1_attachment_id'];
                $rowset[$key]['stockoffer_banner2_attachment_id']   = $row['banner2_attachment_id'];
                $rowset[$key]['stockoffer_footer_attachment_id']    = $row['footer_attachment_id'];
                $rowset[$key]['stockoffer_pdf_attachment_id']       = $row['pdf_attachment_id'];
            }
        }
        
        $modelUser      = new User();
        $rowset         = $modelUser->FillUserInfo($rowset, 'stockoffer_author_id', 'stockoffer_author');
        $rowset         = $modelUser->FillUserInfo($rowset, 'stockoffer_modifier_id', 'stockoffer_modifier');
        
        $modelAttachment = new Attachment();
        $rowset          = $modelAttachment->FillAttachmentInfo($rowset, 'stockoffer_header_attachment_id', 'stockoffer_header_attachment');
        $rowset          = $modelAttachment->FillAttachmentInfo($rowset, 'stockoffer_banner1_attachment_id', 'stockoffer_banner1_attachment');
        $rowset          = $modelAttachment->FillAttachmentInfo($rowset, 'stockoffer_banner2_attachment_id', 'stockoffer_banner2_attachment');
        $rowset          = $modelAttachment->FillAttachmentInfo($rowset, 'stockoffer_footer_attachment_id', 'stockoffer_footer_attachment');
        $rowset          = $modelAttachment->FillAttachmentInfo($rowset, 'stockoffer_pdf_attachment_id', 'stockoffer_pdf_attachment');
        
        foreach ($rowset as $key => $row)
        {
            if (isset($row['stockoffer_author']) && !empty($row['stockoffer_author']))
            {
                $rowset[$key][$entityname]['author'] = $row['stockoffer_author'];
            }
            unset($rowset[$key]['stockoffer_author_id']);
            unset($rowset[$key]['stockoffer_author']);
            
            if (isset($row['stockoffer_modifier']) && !empty($row['stockoffer_modifier']))
            {
                $rowset[$key][$entityname]['modifier'] = $row['stockoffer_modifier'];
            }
            unset($rowset[$key]['stockoffer_modifier_id']);
            unset($rowset[$key]['stockoffer_modifier']);
            
            
            
            if (isset($row['stockoffer_header_attachment']) && !empty($row['stockoffer_header_attachment']))
            {
                $rowset[$key][$entityname]['header_attachment'] = $row['stockoffer_header_attachment'];
            }
            unset($rowset[$key]['stockoffer_header_attachment_id']);
            unset($rowset[$key]['stockoffer_header_attachment']);
            
            if (isset($row['stockoffer_banner1_attachment']) && !empty($row['stockoffer_banner1_attachment']))
            {
                $rowset[$key][$entityname]['banner1_attachment'] = $row['stockoffer_banner1_attachment'];
            }
            unset($rowset[$key]['stockoffer_banner1_attachment_id']);
            unset($rowset[$key]['stockoffer_banner1_attachment']);
            
            if (isset($row['stockoffer_banner2_attachment']) && !empty($row['stockoffer_banner2_attachment']))
            {
                $rowset[$key][$entityname]['banner2_attachment'] = $row['stockoffer_banner2_attachment'];
            }
            unset($rowset[$key]['stockoffer_banner2_attachment_id']);
            unset($rowset[$key]['stockoffer_banner2_attachment']);
            
            if (isset($row['stockoffer_footer_attachment']) && !empty($row['stockoffer_footer_attachment']))
            {
                $rowset[$key][$entityname]['footer_attachment'] = $row['stockoffer_footer_attachment'];
            }
            unset($rowset[$key]['stockoffer_footer_attachment_id']);
            unset($rowset[$key]['stockoffer_footer_attachment']);
            
            if (isset($row['stockoffer_pdf_attachment']) && !empty($row['stockoffer_pdf_attachment']))
            {
                $rowset[$key][$entityname]['pdf_attachment'] = $row['stockoffer_pdf_attachment'];
            }
            unset($rowset[$key]['stockoffer_pdf_attachment_id']);
            unset($rowset[$key]['stockoffer_pdf_attachment']);
        }
        
        return $rowset;
    }
    
    /**
     * Сохраняет аттачметты
     * @param array $stockoffer
     * @param array $files As $_FILES
     * @return int количество затронутых записей при апдейте
     * 
     * @version 20130228, d10n
     * @deprecated 20130306, zharkov - измена функциональность
     */
    public function deprecated_SaveFiles($stockoffer, $files)
    {
        $file_list  = array();
        foreach ($files as $param_name => $param_values)
        {
            foreach($param_values as $field_name => $value)
            {
                $file_list[$field_name][$param_name] = $value;
            }
        }
        
        $attachment_ids     = array();
        //$modelAttachment    = new Attachment();
        $modelAttachment    = new Picture();
        
        foreach ($file_list as $field_name => $file)
        {
            if ($file['error'] != 0) continue;
            
            $attachment_id = $modelAttachment->Save(0, 'stockoffer', $stockoffer['id'], $file);
            
            if (!isset($attachment_id) || $attachment_id < 0) continue;
            
            $attachment_ids[$field_name] = $attachment_id;
        }
        
        $result = $this->Update($stockoffer['id'], array(
            'header_attachment_id'  => isset($attachment_ids['header']) ? $attachment_ids['header'] : $stockoffer['header_attachment_id'],
            //'is_show_header_image'  => !isset($attachment_ids['header']) && $stockoffer['header_attachment_id'] <= 0 ? 0 : $stockoffer['is_show_header_image'],
            'banner1_attachment_id' => isset($attachment_ids['banner1']) ? $attachment_ids['banner1'] : $stockoffer['banner1_attachment_id'],
            //'is_show_banner1'       => !isset($attachment_ids['banner1']) && $stockoffer['banner1_attachment_id'] <= 0 ? 0 : $stockoffer['is_show_banner1'],
            'banner2_attachment_id' => isset($attachment_ids['banner2']) ? $attachment_ids['banner2'] : $stockoffer['banner2_attachment_id'],
            //'is_show_banner2'       => !isset($attachment_ids['banner2']) && $stockoffer['banner2_attachment_id'] <= 0 ? 0 : $stockoffer['is_show_banner2'],
            'footer_attachment_id'  => isset($attachment_ids['footer']) ? $attachment_ids['footer'] : $stockoffer['footer_attachment_id'],
            //'is_show_footer_image'  => !isset($attachment_ids['footer']) && $stockoffer['footer_attachment_id'] <= 0 ? 0 : $stockoffer['is_show_footer_image'],
            'modified_at'           => 'NOW()!',
            'modified_by'           => $this->user_id,
        ));
        
        Cache::ClearTag('stockoffer-' . $stockoffer['id']);
        
        return $result;
    }
    
    /**
     * Возвращает Докупент подготовленный для формирования PDF-файла
     * @param int $stockoffer_id
     * @return array
     * 
     * @version 20130301
     */
    public function GetForPdf($stockoffer_id)
    {
        $stockoffer = $this->GetById($stockoffer_id);
        $positions  = $this->GetPositions($stockoffer_id);        
        $stockoffer = $stockoffer['stockoffer'];
        
        
        $stockoffer['positions'] = array(
            'dimension_unit'    => '',
            'weight_unit'       => '',
            'price_unit'        => '',
            'currency'          => '',
            'positions_list'    => array(),
        );
        
        
        $dimention_units    = array();
        $weight_units       = array();
        $price_units        = array();
        $currencies         = array();
        $positions_list     = array();
        foreach ($positions as $steelposition)
        {
            $steelposition  = $steelposition['steelposition'];
            
            $params         = explode('.', $steelposition['thickness']);
            $thickness      = $steelposition['thickness'];

            $dimention_unit = $steelposition['dimension_unit'];
            $dimention_units[$dimention_unit] = true;
            
            $weight_unit = ($steelposition['weight_unit'] == 'mt' ? 'Ton' : $steelposition['weight_unit']);
            $weight_units[$weight_unit] = true;

            $price_unit = ($steelposition['price_unit'] == 'mt' ? 'Ton' : $steelposition['price_unit']);
            $price_units[$price_unit] = true;
            
            $currency = ($steelposition['currency'] == 'eur' ? '€' : $steelposition['currency']);
            $currencies[$currency] = true;
            
            $bg_color = array();
            if ($stockoffer['is_colored'] == 1 && isset($steelposition['steelgrade']) && !empty($steelposition['steelgrade']['bgcolor']))
            {
                $color = trim($steelposition['steelgrade']['bgcolor'], '#');
                
                if (strtolower($color) == 'yellow') $color = 'FFFF00';
                
                $bg_color = array(
                    'red'   => hexdec(substr($color, 0, 2)),
                    'green' => hexdec(substr($color, 2, 2)),
                    'blue'  => hexdec(substr($color, 4, 2)),
                );
            }
            
            $positions_list[] = array(
                'steelgrade'    => $steelposition['steelgrade']['title'],
                'thickness'     => $thickness,
                'width'         => $steelposition['width'],
                'length'        => $steelposition['length'],
                'dimension_unit'=> $steelposition['dimension_unit'],
                'weight_unit'   => $steelposition['weight_unit'],
                'price_unit'    => $steelposition['price_unit'],
            
                'unitweight'    => sprintf('%.3f', round($steelposition['unitweight'], 3)),
                'qtty'          => $steelposition['qtty'],
                'weight'        => sprintf('%.3f', round($steelposition['weight'], 3)),
                'price'         => sprintf('%.2f', round($steelposition['price'], 2)),
                'value'         => sprintf('%.2f', round($steelposition['value'], 2)),
                'notes'         => $steelposition['notes'],
                'internal_notes'=> $steelposition['internal_notes'],
                'delivery_time' => $steelposition['deliverytime']['title'],
                'location'      => (isset($steelposition['quick']) ? $steelposition['quick']['locations'] : ''),
                'iwish'         => '',
            
                'background_color' => $bg_color,
            );
        }

        if (count($dimention_units) == 1) $stockoffer['positions']['dimension_unit'] = key($dimention_units);
        if (count($weight_units) == 1) $stockoffer['positions']['weight_unit'] = key($weight_units);
        if (count($price_units) == 1) $stockoffer['positions']['price_unit'] = key($price_units);
        if (count($currencies) == 1) $stockoffer['positions']['currency'] = key($currencies);


        // сортировка positions
        $steelgrades = array();
        $thicknesses = array();
        foreach($positions_list as $key => $position)
        {
            $steelgrades[$key] = $position['steelgrade'];
            $thicknesses[$key] = $position['thickness'];
            
            if (empty($stockoffer['positions']['dimension_unit']))
            {
                $positions_list[$key]['thickness'] .= ", " . $position['dimension_unit'];
                $positions_list[$key]['width'] .= ", " . $position['dimension_unit'];
                $positions_list[$key]['length'] .= ", " . $position['dimension_unit'];
            }
            
            if (empty($stockoffer['positions']['weight_unit']))
            {
                $positions_list[$key]['unitweight'] .= ", " . $position['weight_unit'];
                $positions_list[$key]['weight'] .= ", " . $position['weight_unit'];
            }
            
            if (empty($stockoffer['positions']['currency']))
            {
                $positions_list[$key]['value'] .= ", " . $position['currency'];
            }
            
            if (empty($stockoffer['positions']['currency']) || empty($stockoffer['positions']['price_unit']))
            {
                $positions_list[$key]['price'] .= ", " . $position['currency'] . '/' . $position['price_unit'];                
            }
        }

        $stockoffer['positions']['positions_list'] = $positions_list;
        
        return $stockoffer;
    }
    
    /**
     * Обновляет данные об атачменте, связанным с документом
     * 
     * @param int $stockoffer_id
     * @param int $attachment_id
     * 
     * @version 20130301, d10n
     */
    public function UpdateAttachment($stockoffer_id, $attachment_id)
    {
        $this->Update($stockoffer_id, array(
            'pdf_attachment_id'     => $attachment_id,
            'modified_at'           => 'NOW()!',
            'modified_by'           => $this->user_id,
        ));
        
        Cache::ClearTag('stockoffer-' . $stockoffer_id);
        Cache::ClearTag('stockoffers');
    }
}