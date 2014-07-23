<?php
require_once APP_PATH . 'classes/models/company.class.php';
require_once APP_PATH . 'classes/models/contactdata.class.php';
require_once APP_PATH . 'classes/models/ddt.class.php';
require_once APP_PATH . 'classes/models/order.class.php';
require_once APP_PATH . 'classes/models/cmr.class.php';
require_once APP_PATH . 'classes/models/steelitem.class.php';


define('RA_STATUS_OPEN',    1);
define('RA_STATUS_PENDING', 2);
define('RA_STATUS_CLOSED',  3);

class RA extends Model
{
    function RA()
    {
        Model::Model('ra');
    }

    /**
    * Сохраняет для айтема значения DDT RA
    * 
    * @param mixed $steelitem_id
    * @param mixed $ra_id
    */
    function ItemSaveTimeline($steelitem_id, $ra_id)
    {
        $this->CallStoredProcedure('sp_ra_item_save_timeline', array($this->user_id, $steelitem_id, $ra_id));
        //_epd("$steelitem_id, $ra_id");
        Cache::ClearTag('ra-' . $ra_id);
        Cache::ClearTag('ra-' . $ra_id . '-items');
        Cache::ClearTag('ras');
        
        Cache::ClearTag('steelitem-' . $steelitem_id);
        
        Cache::ClearTag('orders');
    }
    
    /**
     * Обновляет данные об атачменте, связанным с документом
     * 
     * @param mixed $qc_id
     * @param mixed $attachment_id
     * 
     * @version 20120813, zharkov
     */
    function UpdateAttachment($ra_id, $attachment_id)
    {
        $this->Update($ra_id, array(
            'attachment_id' => $attachment_id
        ));
        
        Cache::ClearTag('ras');
        Cache::ClearTag('ra-' . $ra_id);
    }    
    
    /**
     * Возвращает список айтемов для RA
     * 
     * @param mixed $ra_id
     * @return mixed
     * 
     * @version 20121018, zharkov
     */
    function GetItemsForPdf($ra_id)
    {
        $items = $this->GetItems($ra_id);
        
        foreach ($items as $key => $row)
        {
            $row = $row['steelitem'];
            
            $items[$key]['guid']        = $row['guid'];
            $items[$key]['steelgrade']  = $row['steelgrade']['title'];
            $items[$key]['thickness']   = $row['thickness'];
            $items[$key]['thickness_mm']= sprintf('%.1f', round($row['thickness_mm'], 1));
            $items[$key]['width']       = $row['width'];
            $items[$key]['width_mm']    = sprintf('%.0f', round($row['width_mm'], 0));
            $items[$key]['length']      = $row['length'];
            $items[$key]['length_mm']   = sprintf('%.0f', round($row['length_mm'], 0));
            $items[$key]['qtty']        = 1;
            //$items[$key]['unitweight']  = $row['unitweight'];
            $items[$key]['ddt']         = '';
            
            $items[$key]['dimension_unit']  = $row['dimension_unit'];
            $items[$key]['weight_unit']     = $row['weight_unit'];
            //для америкосовской системы вес округляем до целого числа
            if($row['weight_unit'] == "lb"){
                $items[$key]['unitweight']  = round($row['unitweight']);
            }else{
                $items[$key]['unitweight']  = $row['unitweight'];
            }
            
            if (!empty($row['ddt_number']))
            {
                $items[$key]['ddt'] = $row['ddt_number'];
                $items[$key]['ddt'] .= !empty($row['ddt_date']) && $row['ddt_date'] > 0 ? /*"\n" . */' dd ' . date('d.m.y', strtotime($row['ddt_date'])) : '';
                $items[$key]['ddt'] .= isset($row['ddt_company']) ? "\n" . $row['ddt_company']['title'] : '';
            }
            else if(!empty($row['in_ddt_number']))
            {
                $items[$key]['ddt'] = $row['in_ddt_number'];
                $items[$key]['ddt'] .= !empty($row['in_ddt_date']) && $row['in_ddt_date'] > 0 ? /*"\n" . */' dd ' . date('d.m.y', strtotime($row['in_ddt_date'])) : '';
                $items[$key]['ddt'] .= isset($row['in_ddt_company']) ? "\n" . $row['in_ddt_company']['title'] : '';
            }
            //_epd($items[$key]['ddt']);
            if (isset($items[$key]['variants']))
            {   
                foreach ($items[$key]['variants'] as $var_key => $var)
                {
                    $var = $var['steelitem'];
                    
                    $items[$key]['variants'][$var_key]['guid']        = $var['guid'];
                    $items[$key]['variants'][$var_key]['steelgrade']  = $var['steelgrade']['title'];
                    $items[$key]['variants'][$var_key]['thickness']   = $var['thickness'];
                    $items[$key]['variants'][$var_key]['thickness_mm']= sprintf('%.1f', round($var['thickness_mm'], 1));
                    $items[$key]['variants'][$var_key]['width']       = $var['width'];
                    $items[$key]['variants'][$var_key]['width_mm']    = sprintf('%.0f', round($var['width_mm'], 0));
                    $items[$key]['variants'][$var_key]['length']      = $var['length'];
                    $items[$key]['variants'][$var_key]['length_mm']   = sprintf('%.0f', round($var['length_mm'], 0));
                    $items[$key]['variants'][$var_key]['qtty']        = 1;
                    $items[$key]['variants'][$var_key]['unitweight']  = $var['unitweight'];
                    $items[$key]['variants'][$var_key]['ddt']         = '';
                    
/** 20130731, zharkov: commented on AA request
                    if (!empty($var['ddt_number']))
                    {
                        $items[$key]['variants']['ddt'] = $var['ddt_number'];
                        $items[$key]['variants']['ddt'] .= !empty($var['ddt_date']) && $var['ddt_date'] > 0 ? ' dd ' . date('d.m.y', strtotime($var['ddt_date'])) : '';
                        $items[$key]['variants']['ddt'] .= isset($var['ddt_company']) ? "\n" . $var['ddt_company']['title'] : '';
                    }
                    else */
                    if(!empty($var['in_ddt_number']))
                    {
                        $items[$key]['variants']['ddt'] = $var['in_ddt_number'];
                        $items[$key]['variants']['ddt'] .= !empty($var['in_ddt_date']) && $var['in_ddt_date'] > 0 ? /*"\n" . */' dd ' . date('d.m.y', strtotime($var['in_ddt_date'])) : '';
                        $items[$key]['variants']['ddt'] .= isset($var['in_ddt_company']) ? "\n" . $var['in_ddt_company']['title'] : '';
                    }
                    
                }
            }
            unset($items[$key]['steelitem']);
        }

        return $items;
    }
    
    /**
     * Удаляет айтем из RA
     * 
     * @param mixed $ra_id
     * @param mixed $item_id
     * 
     * @version 20121018, zharkov
     */
    function ItemRemove($ra_id, $ra_item_id)
    {
        $result = $this->CallStoredProcedure('sp_ra_item_remove', array($this->user_id, $ra_id, $ra_item_id));
        $result = isset($result[0]) && isset($result[0][0]) ? $result[0][0] : array();

        Cache::ClearTag('ras');
        Cache::ClearTag('ra-' . $ra_id);
        Cache::ClearTag('ra_item-' . $ra_item_id);
        Cache::ClearTag('ra_items');
        Cache::ClearTag('ra-' . $ra_id . '-items');
        Cache::ClearTag('ra-' . $ra_id . '-items-variants');
        
        if (isset($result['steelitem_id'])) Cache::ClearTag('steelitem-' . $result['steelitem_id']);
        
        Cache::ClearTag('ddts');
        Cache::ClearTag('ddt_items');
        
        Cache::ClearTag('cmrs');
        Cache::ClearTag('cmr_items');
        
        Cache::ClearTag('orders');
        
        return $result;
    }
    
    /**
     * Возвращает список айтемов для RA
     * 
     * @param mixed $ra_id
     * @return mixed
     * 
     * @version 20121018, zharkov
     */
    function GetItems($ra_id)
    {
        $hash       = 'ra-' . $ra_id . '-items';
        $cache_tags = array($hash, 'ras', 'ra-' . $ra_id);

        $rowset         = $this->_get_cached_data($hash, 'sp_ra_get_items', array($this->user_id, $ra_id), $cache_tags);
        $modelSteelItem = new SteelItem();
        $rowset         = isset($rowset[0]) ? $modelSteelItem->FillSteelItemInfo($rowset[0]) : array();

        $items = array();
        foreach ($rowset as $item)
        {
            if ($item['parent_id'] == 0 && isset($item['steelitem']))
            {
                $items[$item['id']] = $item;
                
                $items[$item['id']]['is_width_too_large'] = (isset($item['steelitem']['width_mm']) && $item['steelitem']['width_mm'] >= 2500);
                
            }
        }
        
        foreach ($rowset as $item)
        {
            if ($item['parent_id'] > 0) 
            {
                if (!isset($items[$item['parent_id']]['variants'])) $items[$item['parent_id']]['variants'] = array();
                $items[$item['parent_id']]['variants'][] = $item;
            }
        }

        return $items;        
    }
    
    /**
     * Возвращает набор данных для конкретной RA (по ID)
     *     
     * @param int $id [INT]
     * @return array
     * 
     * @version 20121009, d10n
     */
    public function GetById($id)
    {
        $data_set = $this->FillRAInfo(array(array('ra_id' => $id)));
        return isset($data_set[0]) && isset($data_set[0]['ra']) ? $data_set[0] : array();
    }
    
    
    /**
     * Возвращает линейный список RAs
     * 
     * @param int $page_no
     * @param int $per_page
     * @return array
     * 
     * @version 20121012, d10n
     */
    public function GetList($page_no = 0, $per_page = ITEMS_PER_PAGE)
    {
        $page_no    = $page_no > 0 ? $page_no : 1;
        $per_page   = $per_page < 1 ? ITEMS_PER_PAGE : $per_page;
        $start      = ($page_no - 1) * $per_page;

        $hash       = 'ra-list' . md5('-page_no' . $page_no . '-start' . $start);
        $cache_tags = array($hash, 'ras');
        
        $data_set   = $this->_get_cached_data($hash, 'sp_ra_get_list', array($start, $per_page), $cache_tags);
        
        if (!isset($data_set[0])) return array('data' => array(), 'count' => 0);
        
        $list       = $this->FillRAInfo($data_set[0]);
        $rows_count = (isset($data_set[1]) && isset($data_set[1][0]) && isset($data_set[1][0]['rows_count'])) ? $data_set[1][0]['rows_count'] : 0;
        
        return array('data' => $list, 'count' => $rows_count);
    }
    
    
    /**
     * Возвращает список связанных с конкретным RA документов (DDTs, CMRs)
     * 
     * @param int $id
     * @param int $is_deleted
     * @return array
     */
    public function GetListOfRelatedDocs($id, $is_deleted = 0)
    {
        $modelDDT = new DDT();
        $rowset = $modelDDT->GetList($id);
        $ddts_list = isset($rowset['data']) ? $rowset['data'] : array();
        
        $modelCMR   = new CMR();
		//print_r('1');
        $rowset   = $modelCMR->GetList($id);
		
        $cmrs_list  = isset($rowset['data']) ? $rowset['data'] : array();
        
        $rowset = array_merge($ddts_list, $cmrs_list);
        //_epd($rowset);
        if (empty($rowset)) return array();
        
        $list = array();
        foreach($rowset as $key => $row)
        {
            $object_alias = '';
            if (array_key_exists('ddt', $row)) $object_alias = 'ddt';
            if (array_key_exists('cmr', $row)) $object_alias = 'cmr';
            
            if (empty($object_alias)) continue;
            
            if (!in_array($is_deleted, array(-1, 0, 1))) continue;
            if ($is_deleted == 0 && $row[$object_alias]['is_deleted'] == 1) continue;
            if ($is_deleted == 1 && $row[$object_alias]['is_deleted'] == 0) continue;
            
            
            $list[$key]                 = $row[$object_alias];
            $list[$key]['object_alias'] = $object_alias;
            $list[$key]['object_id']    = $row[$object_alias]['id'];
            
            if ($row[$object_alias]['owner_id'] == 7117)// companies.id = 7117 - MaM Italy
            {
                $list[$key]['doc_no'] = $row[$object_alias]['number_default'] == 0
                    ? $object_alias . ' NEW MaM_IT'
                    : $object_alias . $row[$object_alias]['number'] . ' it';
            }
            if ($row[$object_alias]['owner_id'] == 5998)// companies.id = 5998 - MaM UKy
            {
                $list[$key]['doc_no'] = $row[$object_alias]['number_default'] == 0
                    ? $object_alias . ' NEW MaM_UK'
                    : $object_alias . $row[$object_alias]['number'] . ' uk';
            }
        }
        
        return $list;
    }
    
    /**
     * Сохраняет данные
     * 
     * @param int $id [INT]
     * @param int $stockholder_id [INT]
     * @param int $company_id [INT]
     * @param int $dest_stockholder_id [INT]
     * @param string $truck_number [VARCHAR(50)]
     * @param string $destination [VARCHAR(200)]
     * @param string $loading_date [VARCHAR(200)]
     * @param string $marking [TEXT]
     * @param string $dunnaging [TEXT]
     * @param int $status_id [TINYINT]
     * @param float $max_weight [DECIMAL(10,4)]
     * @param float $weighed_weight [DECIMAL(10,4)]
     * @param string $ddt_number [VARCHAR(50)]
     * @param string $ddt_date [DATETIME]
     * @param string $ddt_instructions [TEXT]
     * @param string $coupon [TEXT]
     * @param string $notes [TEXT]
     * @param string $consignee [TEXT]
     * @param string $consignee_ref [VARCHAR(200)]
     * @param int $mm_dimensions [TINYINT]
     * * * @return array 
     * 
     * @version 20121114, d10n: Добавление consignee, consignee_ref и mm_dimensions
     * @version 20121012, d10n: Удаление поля ddt_id, добавление dtt_number и ddt_date
     * @version 20121010, d10n
     */
    public function Save($id, $stockholder_id, $dest_stockholder_id, $company_id = 0, $truck_number = '', $destination = '', $loading_date = '', 
                        $marking = '', $dunnaging = '', $status_id = RA_STATUS_OPEN, $max_weight = '', $weighed_weight = '', 
                        $ddt_number = '', $ddt_date = '', $ddt_instructions = '', $coupon = '', $notes = '',
                        $consignee = '', $consignee_ref = '', $mm_dimentions = 0)
    {
        $data_set = $this->CallStoredProcedure('sp_ra_save', array($this->user_id, $id, $stockholder_id, $dest_stockholder_id, $company_id,
                    $truck_number, $destination, $loading_date, $marking, $dunnaging, $status_id,
                    $max_weight, $weighed_weight, $ddt_number, $ddt_date, $ddt_instructions, $coupon, $notes,
                    $consignee, $consignee_ref, $mm_dimentions));
        
        $ra = isset($data_set) && isset($data_set[0]) && isset($data_set[0][0]) ? $data_set[0][0] : array();
        
        Cache::ClearTag('ra-' . $ra['ra_id']);
        Cache::ClearTag('ras');
        
        Cache::ClearTag('ddts');
        Cache::ClearTag('cmrs');
        
        return $ra;
    }
    
    
    
        
    /**
     * Возвращает основную информацию об RA
     * 
     * @param mixed $rowset
     * @param mixed $id_fieldname
     * @param mixed $entityname
     * @param mixed $cache_prefix
     * @return mixed
     * 
     * @version 20121018, zharkov
     */
    function FillRAMainInfo($rowset, $id_fieldname = 'ra_id', $entityname = 'ra', $cache_prefix = 'ra')
    {
        $rowset = $this->_fill_entity_info($rowset, $id_fieldname, $entityname, $cache_prefix, 'sp_ra_get_list_by_ids', array('ras' => '', 'ra' => 'id'), array());

        foreach ($rowset as $key => $row)
        {
            if (!isset($row[$entityname])) continue;
            
            $rowset[$key][$entityname]['doc_no']        = 'ra' . sprintf('%04d', $row[$entityname]['id']) . '/' . date('y', strtotime($row[$entityname]['created_at']));
            $rowset[$key][$entityname]['doc_no_short']  = 'ra' . $row[$entityname]['id'];           
            $rowset[$key][$entityname]['doc_no_full']   = 'ra' . sprintf('%04d', $row[$entityname]['id']) . '/' . date('y', strtotime($row[$entityname]['created_at']));
        }
        
        return $rowset;                
    }
    
    /**
     * Заполняет значения ReleaseAdvice
     * 
     * @param array $rowset
     * @param string $fill_type ['simple']
     * @return array
     * 
     * @version 20121009, d10n
     */
    public function FillRAInfo($rowset, $id_fieldname = 'ra_id', $entityname = 'ra', $cache_prefix = 'ra')
    {
        $rowset = $this->FillRAMainInfo($rowset, $id_fieldname, $entityname, $cache_prefix);

        foreach ($rowset as $key => $row)
        {
            if (!isset($row[$entityname])) continue;

            $row = $row[$entityname];
            
            $rowset[$key]['ra_stockholder_id']  = $row['stockholder_id'];
            $rowset[$key]['ra_company_id']      = $row['company_id'];
            $rowset[$key]['ra_author_id']       = $row['created_by'];
            $rowset[$key]['ra_modifier_id']     = $row['modified_by'];
            $rowset[$key]['ra_dest_stockholder_id'] = $row['dest_stockholder_id'];
            
            if (isset($row['attachment_id']))$rowset[$key]['ra_attachment_id'] = $row['attachment_id'];
        }
        
        
        $attachments    = new Attachment();
        $rowset         = $attachments->FillAttachmentInfo($rowset, 'ra_attachment_id', 'ra_attachment');
        
        $companies      = new Company();
        //$rowset         = $companies->FillCompanyInfoShort($rowset, 'ra_stockholder_id', 'ra_stockholder');
        $rowset         = $companies->FillCompanyInfo($rowset, 'ra_stockholder_id', 'ra_stockholder');
        $rowset         = $companies->FillCompanyInfo($rowset, 'ra_company_id', 'ra_company');
        $rowset         = $companies->FillCompanyInfo($rowset, 'ra_dest_stockholder_id', 'ra_dest_stockholder');
        
        $users          = new User();
        $rowset         = $users->FillUserInfo($rowset, 'ra_author_id',   'ra_author');
        $rowset         = $users->FillUserInfo($rowset, 'ra_modifier_id', 'ra_modifier');
        
        
        $modelCompany       = new Company();
        $modelUser          = new User();
        $modelContactData   = new ContactData();
        
        foreach ($rowset as $key => $row) 
        {
            if (!isset($row[$entityname])) continue;

            if (isset($row['ra_attachment']) && !empty($row['ra_attachment']))
            {
                $rowset[$key][$entityname]['attachment'] = $row['ra_attachment'];
            }            
            unset($rowset[$key]['ra_attachment_id']);
            unset($rowset[$key]['ra_attachment']);

            if (isset($row['ra_author']))
            {
                $rowset[$key][$entityname]['author'] = $row['ra_author'];
            }                
            unset($rowset[$key]['ra_author']);
            unset($rowset[$key]['ra_author_id']);
            
            if (isset($row['ra_modifier']))
            {
                $rowset[$key][$entityname]['modifier'] = $row['ra_modifier'];
            }
            unset($rowset[$key]['ra_modifier']);
            unset($rowset[$key]['ra_modifier_id']);
            
            if (isset($row['ra_stockholder']))
            {
                $rowset[$key][$entityname]['stockholder'] = $row['ra_stockholder'];
            }                
            unset($rowset[$key]['ra_stockholder']);
            unset($rowset[$key]['ra_stockholder_id']);
            
            if (isset($row['ra_company']))
            {
                $rowset[$key][$entityname]['company'] = $row['ra_company'];
            }                
            unset($rowset[$key]['ra_company']);
            unset($rowset[$key]['ra_company_id']);
            
            if (isset($row['ra_dest_stockholder']))
            {
                $rowset[$key][$entityname]['dest_stockholder'] = $row['ra_dest_stockholder'];
            }                
            unset($rowset[$key]['ra_dest_stockholder']);
            unset($rowset[$key]['ra_dest_stockholder_id']);
                        
            // stock_object_alias - для формирование соответетсвующей (MaM/PlatesAhead) шапки pdf-документа
            $rowset[$key][$entityname]['stock_object_alias']    = 'platesahead';
            $rowset[$key][$entityname]['is_large_item_exists']  = FALSE;
            $rowset[$key][$entityname]['variants_are_exist']    = FALSE;
            
            $total_qtty     = 0;
            $total_weight   = 0;
            $max_width      = 0;
            foreach ($this->GetItems($row[$entityname]['id']) as $item)
            {
                if (!array_key_exists('steelitem', $item)) continue;
                
                $total_qtty     += 1;
                $total_weight   += isset($item['steelitem']['unitweight']) ? $item['steelitem']['unitweight'] : 0;
                $max_width      = isset($item['steelitem']['width_mm']) ? MAX($item['steelitem']['width_mm'], $max_width) : $max_width;
                
                if (!isset($item['steelitem']['stockholder']) || $item['steelitem']['stockholder']['country_id'] != 225)//countries.id = 225 USA
                {
                    $rowset[$key][$entityname]['stock_object_alias'] = 'mam';
                }
                
                if (!empty($item['is_width_too_large']))
                {
                    $rowset[$key][$entityname]['is_large_item_exists'] = TRUE;
                }
                
                if (isset($item['variants']))
                {
                    $rowset[$key][$entityname]['variants_are_exist'] = TRUE;
                }
                
                if (!array_key_exists('order', $item['steelitem'])) continue;
                if (array_key_exists('order', $rowset[$key][$entityname])) continue;
                
                $rowset[$key][$entityname]['order'] = $item['steelitem']['order'];
            }
            
            $rowset[$key][$entityname]['max_width']         = $max_width;
            $rowset[$key][$entityname]['total_qtty']        = $total_qtty;
            $rowset[$key][$entityname]['total_weight']      = $total_weight;
            $rowset[$key][$entityname]['total_weight_max']  = $total_weight * 1.06;
            $rowset[$key][$entityname]['dimension_unit']    = isset($item['steelitem']) && isset($item['steelitem']['dimension_unit']) ? $item['steelitem']['dimension_unit'] : '';
            $rowset[$key][$entityname]['weight_unit']       = isset($item['steelitem']) && isset($item['steelitem']['weight_unit']) ? $item['steelitem']['weight_unit'] : '';

            $rowset[$key][$entityname]['total_weightmax_highlight'] = $rowset[$key][$entityname]['total_weight_max'] > ($rowset[$key][$entityname]['stock_object_alias'] == 'platesahead' ? 48000 : 24.5) ? TRUE : FALSE;
            
            
            // Итеграция с данными Заказа
            if (isset($rowset[$key][$entityname]['order'])
                && $rowset[$key][$entityname]['stock_object_alias'] == 'platesahead')
            {
                if (empty($rowset[$key][$entityname]['consignee']))
                {
                    $rowset[$key][$entityname]['consignee'] = $rowset[$key][$entityname]['order']['company']['title'] . "\n";
                    $rowset[$key][$entityname]['consignee'] .= $rowset[$key][$entityname]['order']['company']['address'] . "\n";
                    if (isset($rowset[$key][$entityname]['order']['company']['city']))
                    {
                        $rowset[$key][$entityname]['consignee'] .= $rowset[$key][$entityname]['order']['company']['city']['title'] . ' ';
                    }
                    if (isset($rowset[$key][$entityname]['order']['company']['region']))
                    {
                        $rowset[$key][$entityname]['consignee'] .= $rowset[$key][$entityname]['order']['company']['region']['title'] . ', ';
                    }
                    
                    if (isset($rowset[$key][$entityname]['order']['person'])
                        && !empty($rowset[$key][$entityname]['order']['person']['first_name']))
                    {
                        $rowset[$key][$entityname]['consignee'] .= "\n" . $rowset[$key][$entityname]['order']['person']['title'] . '. ' . $rowset[$key][$entityname]['order']['person']['first_name'] . ' ' . $rowset[$key][$entityname]['order']['person']['last_name'];
                        
                        $contactdatas_list = $modelContactData->GetList('person', $rowset[$key][$entityname]['order']['person']['id']);
                        
                        foreach($contactdatas_list as $_row)
                        {
                            if ($_row['type'] == 'phone')
                            {
                                $rowset[$key][$entityname]['consignee'] .= "\n" . 'Tel: ' . $_row['title'];
                            }
                            if ($_row['type'] == 'fax')
                            {
                                $rowset[$key][$entityname]['consignee'] .= "\n" . 'Fax: ' . $_row['title'];
                            }
                        }
                    }
                    $rowset[$key][$entityname]['consignee'] .= $rowset[$key][$entityname]['order']['company']['pobox'];
                }
                if (empty($rowset[$key][$entityname]['consignee_ref']))
                {
                    $rowset[$key][$entityname]['consignee_ref'] = $rowset[$key][$entityname]['order']['buyer_ref'];
                }
                
                unset($rowset[$key][$entityname]['order']);
            }
        }
        //print_r('1');
        return $rowset;
    }
    
    
    /**
    * Добавляет айтемы к RA
    * 
    * @param mixed $id
    * @param mixed $parent_id
    * @param mixed $ra_id
    * @param string $steelitem_ids
    * @return null
    * 
    * @version 20121103, zharkov
    * @version 20121128, d10n: Добавлено управление DDT информацией при добавлении Айтема
    */
    public function ItemsAdd($parent_id, $ra_id, $steelitem_ids)
    {        
        // $modelSteelItem = new SteelItem();   - 20121128, zharkov: перенесено в хп
        
//        $ra = $this->GetById($ra_id);
//        $ra = $ra['ra'];
        
        foreach(explode(',', $steelitem_ids) as $steelitem_id) 
        {
            $this->CallStoredProcedure('sp_ra_item_add', array($this->user_id, $parent_id, $ra_id, $steelitem_id));
            Cache::ClearTag('steelitem-' . $steelitem_id);
        }
        
        Cache::ClearTag('ras');
        Cache::ClearTag('ra_items');
        Cache::ClearTag('ra-' . $ra_id . '-items');
        Cache::ClearTag('ra-' . $ra_id . '-items-variants');
        
        Cache::ClearTag('ddts');
        Cache::ClearTag('ddt_items');
        
        Cache::ClearTag('cmrs');
        Cache::ClearTag('cmr_items');
        
        Cache::ClearTag('orders');
    }
    
    
    /**
     * Устанавливает конкретный Айтем Основным в наборе Айтем-Варианты
     * 
     * @param int $id [INT] ID (ra.id) конкретного RA
     * @param int $ra_item_id [INT] ID (ra_items.id) Айтема-Варианта, который будет основным
     * 
     * @version 20121022, d10n
     * @version 20121103, zharkov: переделал механизм манипуляций при перестановках
     */
    public function SetPrimaryItem($ra_id, $ra_item_id)
    {
        $result = $this->CallStoredProcedure('sp_ra_set_primary_item', array($this->user_id, $ra_id, $ra_item_id));
        $result = isset($result[0]) && isset($result[0][0]) ? $result[0][0] : array();

        if (isset($result['prev_steelitem_id'])) Cache::ClearTag('steelitem-' . $result['prev_steelitem_id']);
        if (isset($result['new_steelitem_id'])) Cache::ClearTag('steelitem-' . $result['new_steelitem_id']);
        
        Cache::ClearTag('ra-' . $ra_id);
        Cache::ClearTag('ra-' . $ra_id . '-items');
        Cache::ClearTag('ra_items');
        Cache::ClearTag('ras');
        
        Cache::ClearTag('ddts');
        Cache::ClearTag('ddt_items');
        
        Cache::ClearTag('cmrs');
        Cache::ClearTag('cmr_items');
    }
    
    /**
     * Возвращает данные по конкретному Айтему
     * 
     * @param int $ra_item_id [INT] ID (ra_items.id) конкретного ra-айтема
     * @return array
     * 
     * @version 20121022, d10n
     */
    public function GetItemById($ra_item_id)
    {
        $rowset = $this->CallStoredProcedure('sp_ra_item_get_by_id', array($ra_item_id));
        
        if (!isset($rowset[0]) || !isset($rowset[0][0])) return array();
        
        $modelSteelItem = new SteelItem();
        $rowset         = $modelSteelItem->FillSteelItemInfo($rowset[0]);
        
        return isset($rowset[0]) ? $rowset[0] : array();
    }
    
    /**
     * Пересчитывает взвешенный вес айтемов для конкретного RA
     * 
     * @param int $ra_id [INT]
     * @return mixed
     * 
     * @version 20121127, d10n
     * @version 20121128, zharkov: перенесено в хп
     */
    public function deprecated_RecalculateItemsWeighedWeight($ra_id)
    {
        $result = $this->CallStoredProcedure('sp_ra_items_recalculate_ww', array($ra_id));
        
        Cache::ClearTag('ras');
        Cache::ClearTag('ra_items');
        
        Cache::ClearTag('ddts');
        Cache::ClearTag('ddt_items');
        
        Cache::ClearTag('cmrs');
        Cache::ClearTag('cmr_items');
        
        return $result;
    }
    
    /**
     * Устанавливает состояния связанных документов
     * (Удаляет/Активирует)
     * 
     * @param int $ra_id
     * @version 20121127, d10n
     * @version 20121128, zharkov: перенесено в хп
     */
    public function deprecated_SetActivityOfRelatedDocs($ra_id)
    {
        $list = $this->GetListOfRelatedDocs($ra_id, -1);
        
        $modelDDT = new DDT();
        $modelCMR = new CMR();
        
        foreach($list as $doc)
        {
            $object = '';
            if ($doc['object_alias'] == 'cmr') $object = $modelCMR;
            if ($doc['object_alias'] == 'ddt') $object = $modelDDT;
            if (empty($object)) continue;
            
            $object_id = $doc['object_id'];
            
            if ($doc['total_qtty'] == 0)
            {
                $object->Remove($object_id);
                continue;
            }
            
            if ($doc['total_qtty'] > 0 && $doc['is_deleted'] == 1)
            {
                $object->Activate($object_id);
                continue;
            }
        }
    }
    
    /**
     * Помечает связанные документы как Outdated
     * 
     * @param int $ra_id
     * @version 20121127, d10n
     * @version 20121128, zharkov: перенесено в хп
     */
    public function deprecated_SetRelatedDocsAsOutdated($ra_id)
    {
        $list = $this->GetListOfRelatedDocs($ra_id);
        
        $modelDDT = new DDT();
        $modelCMR = new CMR();
        
        foreach($list as $doc)
        {
            $object = '';
            if ($doc['object_alias'] == 'cmr') $object = $modelCMR;
            if ($doc['object_alias'] == 'ddt') $object = $modelDDT;
            if (empty($object)) continue;
            
            $object->SetAsOutdated($doc['object_id']);
        }
    }
    
    /**
     * Возвращает список доступных вариантов для айтема
     * 
     * @param array $ra Ассоциативный массив свойств(данных) RA
     * @param array $primary_item Ассоциативный массив свойств(данных) PromaryItem-а
     * @return array Ассоциативный массив вида array('list'=>array(), 'count'=>int)
     * 
     * @version 20121205, d10n
     */
    public function GetAvailableVariants($ra, $primary_item)
    {
        $hash = 'ra-' . $ra['id'] . '-items-variants';
        $cache_tags = array($hash);
        
        $data_set   = $this->_get_cached_data($hash, 'sp_ra_item_get_available_variants', array($primary_item['id']), $cache_tags);
        $data_set   = isset($data_set[0]) ? $data_set[0] : array();
        
        $modelSteelItem = new SteelItem();
        $data_set       = $modelSteelItem->FillSteelItemInfo($data_set);
        
        $primary_steelitems = array();
        
        foreach ($this->GetItems($ra['id']) as $item)
        {
            if ($item['parent_id'] == 0) $primary_steelitems[] = $item['steelitem_id'];
        }
        
        foreach ($data_set as $key => $row)
        {
            if (in_array($row['steelitem']['status_id'], array(ITEM_STATUS_DELIVERED, ITEM_STATUS_INVOICED)) 
                || $row['steelitem']['id'] == $primary_item['id']
                || in_array($row['steelitem']['id'], $primary_steelitems)
                || $row['steelitem']['stockholder_id'] != $ra['stockholder_id']
            )
            {
                unset($data_set[$key]);
            }
        }
        
        return array('list' => $data_set, /*'count' => 0,*/ );
    }
    
    /**
     * Обновляет значение ra_items.is_theor_weight
     * 
     * @param mixed $ra_id
     * @param mixed $ra_item_id
     * @param mixed $is_theor_weight
     * 
     * @version 20121212, zharkov
     */
    function ItemUpdate($ra_id, $ra_item_id, $is_theor_weight)
    {
        $this->CallStoredProcedure('sp_ra_item_update', array($this->user_id, $ra_item_id, $is_theor_weight));
        
        Cache::ClearTag('ra-' . $ra_id);
        Cache::ClearTag('ra-' . $ra_id . '-items');
        
        Cache::ClearTag('ddts');
        Cache::ClearTag('ddt_items');
        
        Cache::ClearTag('cmrs');
        Cache::ClearTag('cmr_items');        
    }
    
    
    /**
     * Определяет значения ra_items.is_theor_weight = 1
     * 
     * @param int $ra_id [INT] ra.id. RA, для основных айтемов которого производятся манипуляции
     * @param array $ra_item_ids Массив ra_items.id, для которых нужно выставить параметр
     * @param boolean $only_reset Произвести только сброс
     * 
     * @version 20121206, d10n
     * @version 20121212, zharkov: deprecated
     */
    public function deprecated_SetRaItemIsTheorWeight($ra_id, $ra_item_ids = array(), $only_reset = FALSE)
    {
        if (empty($ra_item_ids) && !$only_reset)
        {
            array_walk($ra_item_ids, create_function('&$id','$id = intval($id);'));// приведение ids к целым
            $ra_item_ids = array_filter($ra_item_ids, create_function('$id','return ($id != 0);'));// фильтр от нулевых значений
            
            if (empty($ra_item_ids)) return FALSE;
            
            // check for modification in ra_items.is_theor_weight
            $process_flag = FALSE;
            foreach ($this->GetItems($ra_id) as $item)
            {
                if ((in_array($item['id'], $ra_item_ids) && $item['is_theor_weight'] == 0)
                    || (!in_array($item['id'], $ra_item_ids) && $item['is_theor_weight'] == 1))
                {
                    $process_flag = TRUE;
                }
            }
            
            if (!$process_flag) return FALSE;
        }
        
        
        $this->table->table_name = 'ra_items';
        
        // reset
        $this->UpdateList(array(
            'values'    => array(
                'is_theor_weight'   => 0,
                'modified_at'       => 'NOW()!',
                'modified_by'       => $this->user_id,
            ),
            'where'     => array(
                'conditions'    => 'ra_id = ?',
                'arguments'     => array($ra_id),
            ),
        ));
        
        // define
        if (!$only_reset)
        {
            foreach ($ra_item_ids as $id)
            {
                $this->Update($id, array(
                    'is_theor_weight'   => 1,
                    'modified_at'       => 'NOW()!',
                    'modified_by'       => $this->user_id,
                ));
            }
        }
        
        $this->table->table_name = 'ra';
        
        $this->CallStoredProcedure('sp_ra_items_recalculate_ww', array($ra_id));
        $this->CallStoredProcedure('sp_ra_update_related_docs', array($this->user_id, $ra_id));
        
        Cache::ClearTag('ras');
        Cache::ClearTag('ra-' . $ra_id);
        Cache::ClearTag('ra-' . $ra_id . '-items');
        
        Cache::ClearTag('ddts');
        Cache::ClearTag('ddt_items');
        
        Cache::ClearTag('cmrs');
        Cache::ClearTag('cmr_items');
    }
    
    /**
     * Удалеет RA и связанные документы [CMR/DDT]
     * 
     * @param int $id ra.id
     * @version 20121007, d10n
     * @version 20121009, zharkov: нужно обновить
     */
    public function Remove($id)
    {
        // 1. Удаление айтемов из RA
        foreach ($this->GetItems($id) as $item)
        {
            $this->ItemRemove($id, $item['id']);
        }
        
        // 2. удаление RA и связанных документов, sp_ra_update_related_docs включить в sp_ra_remove
        $this->CallStoredProcedure('sp_ra_remove', array($this->user_id, $id));

        Cache::ClearTag('ras');
        Cache::ClearTag('ra-' . $id);
        Cache::ClearTag('ra-' . $id . '-items');
        
        Cache::ClearTag('ddts');
        Cache::ClearTag('ddt_items');
        
        Cache::ClearTag('cmrs');
        Cache::ClearTag('cmr_items');
    }
    
    /**
     * Обновляет связанные документы
     * 
     * @param mixed $id
     * 
     * @version 20121213, zharkov
     */
    public function UpdateRelatedDocs($id)
    {
        $this->CallStoredProcedure('sp_ra_update_related_docs', array($this->user_id, $id));

        Cache::ClearTag('ras');
        Cache::ClearTag('ra-' . $id);
        Cache::ClearTag('ra-' . $id . '-items');
        
        Cache::ClearTag('ddts');
        Cache::ClearTag('ddt_items');
        
        Cache::ClearTag('cmrs');
        Cache::ClearTag('cmr_items');
    }

    /**
     * Делает перерасчет взвешенного веса
     * 
     * @param mixed $id
     * 
     * @version 20121213, zharkov
     */
    public function ItemsRecalculateWW($id)
    {
        $this->CallStoredProcedure('sp_ra_items_recalculate_ww', array($id));

        Cache::ClearTag('ras');
        Cache::ClearTag('ra-' . $id);
        Cache::ClearTag('ra-' . $id . '-items');
        
        Cache::ClearTag('ddts');
        Cache::ClearTag('ddt_items');
        
        Cache::ClearTag('cmrs');
        Cache::ClearTag('cmr_items');
    }
}
