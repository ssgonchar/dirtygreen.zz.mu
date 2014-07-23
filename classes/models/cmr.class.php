<?php
require_once APP_PATH . 'classes/models/ra.class.php';
require_once APP_PATH . 'classes/models/steelitem.class.php';

class CMR extends Model
{
    function CMR()
    {
        Model::Model('cmr');
    }
    
    /**
     * Возвращает данные конкрентой записи по ID
     * 
     * @param mixed $id
     * 
     * @version 20121119, d10n
     */
    function GetById($id)
    {
        $dataset = $this->FillCMRInfo(array(array('cmr_id' => $id)));
        
		//Гончар: переписано для удобства чтения кода
		if(isset($dataset) && isset($dataset[0]) && isset($dataset[0]['cmr'])) {
			return $dataset[0];
		}else{
			return null;
		}
		//return isset($dataset) && isset($dataset[0]) && isset($dataset[0]['cmr']) ? $dataset[0] : null;
    }
    
    /**
     * Возвращает линейный список CMRs
     * 
     * @param int $ra_id
     * @param int $page_no
     * @param int $per_page
     * @return array
     * 
     * @version 20121119, d10n
     */
    public function GetList($ra_id = 0, $page_no = 0, $per_page = ITEMS_PER_PAGE)
    {
		//если номер страницы > 0, номер страницы равен номеру страницы
		//если нет, номер страницы равен 1
		//if($page_no > 0) $page_no = $page_no;
		//else $page_no = 1;
        $page_no    = $page_no > 0 ? $page_no : 1;
        
		//if($per_page < 1) $per_page = ITEMS_PER_PAGE;
		//else $per_page = $per_page;
		$per_page   = $per_page < 1 ? ITEMS_PER_PAGE : $per_page;
        
		$start      = ($page_no - 1) * $per_page;
		
        $hash       = 'cmr-list' . md5('-ra-id-' . $ra_id . '-page_no' . $page_no . '-start' . $start);
        $cache_tags = array($hash, 'cmrs');
        
        $data_set   = $this->_get_cached_data($hash, 'sp_cmr_get_list', array($ra_id, $start, $per_page), $cache_tags);
		//print_r('1');
	   if (!isset($data_set[0])) return array('data' => array(), 'count' => 0);
        
        $list       = $this->FillCMRInfo($data_set[0]);
        $rows_count = (isset($data_set[1]) && isset($data_set[1][0]) && isset($data_set[1][0]['rows_count'])) ? $data_set[1][0]['rows_count'] : 0;
        
		
		//print_r($list);
		//die();
		
        return array('data' => $list, 'count' => $rows_count);
    }
    
    
    /**
     * Сохраняет данные по CMR
     * 
     * @param int $id [INT]
     * @param int $ra_id [INT]
     * @param string $number [VARCHAR(20)]
     * @param string $buyer_name [VARCHAR(100)]
     * @param string $buyer_address [VARCHAR(500)]
     * @param string $delivery_point [VARCHAR(500)]
     * @param string $date [TIMESTAMP]
     * @param string $truck_number [VARCHAR(20)]
     * @param int $transporter_id [INT]
     * @param int $attachment_id [INT]
     * @param string $product_name [VARCHAR(50)]
     * 
     * @return array
     * 
     * @version 20121119, d10n
     */
    public function Save($id, $ra_id, $owner_id, $number, $buyer_name, $buyer_address,
            $delivery_point, $date, $truck_number, $transporter_id, $attachment_id, $product_name)
    {
        $data_set = $this->CallStoredProcedure('sp_cmr_save', array($this->user_id, $id, $ra_id, $owner_id, $number, $buyer_name, $buyer_address,
            $delivery_point, $date, $truck_number, $transporter_id, $attachment_id, $product_name));
        
        $rowset = isset($data_set) && isset($data_set[0]) && isset($data_set[0][0]) ? $data_set[0][0] : array();
        
        if (isset($rowset['cmr_id'])) Cache::ClearTag('cmr-' . $rowset['cmr_id']);
        Cache::ClearTag('cmrs');
                
        return $rowset;
    }
    
    /**
     * Создает новую запись
     * 
     * @param int $ra_id
     * $param int $owner_id
     * @return array
     * 
     * @version 20121119, d10n
     */
    public function Create($ra_id, $owner_id, $transporter_id, $truck_number)
    {
        $id             = 0;
        $number         = 0;
        $buyer_name     = '';
        $buyer_address  = '';
        $delivery_point = '';
        $date           = 'NULL VALUE!';
        $attachment_id  = 0;
        $product_name   = 'HOT ROLLED STEEL PLATE';
        
        return $this->Save($id, $ra_id, $owner_id, $number, $buyer_name, $buyer_address, $delivery_point, $date,
            $truck_number, $transporter_id, $attachment_id, $product_name);
    }
    
    
    /**
     * Возвращает базовый набор данных по объекту
     * 
     * @param array $rowset Набор IDs array(array('cmr_id' => 1), )
     * @param string $id_fieldname Название поля, в котором хранятся идентификаторы сущности (например cmr_id)
     * @param string $entityname Название сущности = имя выходного массива для каждой строки входного (например, 'cmr')
     * @param string $cache_prefix Префикс для выборки данных из кеша (например, 'cmr')
     * 
     * @version 20121119, d10n
     */
    public function FillCMRMainInfo($rowset, $id_fieldname = 'cmr_id', $entityname = 'cmr', $cache_prefix = 'cmr')
    {
        $rowset = $this->_fill_entity_info($rowset, $id_fieldname, $entityname, $cache_prefix, 'sp_cmr_get_list_by_ids', array('cmrs' => '', 'cmr' => 'id', 'ras' => ''), array());
        
        foreach ($rowset as $key => $row)
        {
            if (!isset($row[$entityname])) continue;
            
            $row = $row[$entityname];
            
            if (isset($row['number']))
            {
                $sprintf_format = $row['number'] <= 10 ? '%02d' : '%d';
                $rowset[$key][$entityname]['number_default'] = $row['number'];
                $rowset[$key][$entityname]['number'] = sprintf($sprintf_format, $row['number']) . '/' . date('Y', strtotime($row['created_at']));
                
                $rowset[$key][$entityname]['doc_no'] = 'cmr' . $rowset[$key][$entityname]['number'];
            }
            else
            {
                $rowset[$key][$entityname]['doc_no'] = 'cmr # ' . $row['id'];
            }
            
        }
        
        return $rowset;
    }
    
    /**
     * Возвращает расширенный набор данных по объекту
     * 
     * @param array $rowset Набор IDs array(array('cmr_id' => 1), )
     * @param string $id_fieldname Название поля, в котором хранятся идентификаторы сущности (например cmr_id)
     * @param string $entityname Название сущности = имя выходного массива для каждой строки входного (например, 'cmr')
     * @param string $cache_prefix Префикс для выборки данных из кеша (например, 'cmr')
     * 
     * @version 20121119, d10n
     */
    public function FillCMRInfo($rowset, $id_fieldname = 'cmr_id', $entityname = 'cmr', $cache_prefix = 'cmr')
    {
        $rowset = $this->FillCMRMainInfo($rowset, $id_fieldname, $entityname, $cache_prefix);

        foreach ($rowset as $key => $row)
        {
            if (!isset($row[$entityname])) continue;
            
            $row = $row[$entityname];
            
            $rowset[$key]['cmr_ra_id']          = $row['ra_id'];
            $rowset[$key]['cmr_transporter_id'] = $row['transporter_id'];
            $rowset[$key]['cmr_owner_id']       = $row['owner_id'];
            $rowset[$key]['cmr_attachment_id']  = $row['attachment_id'];
            $rowset[$key]['cmr_modifier_id']    = $row['modified_by'];
            $rowset[$key]['cmr_creator_id']     = $row['created_by'];
        }

        $modelUser          = new User();
        $rowset             = $modelUser->FillUserInfo($rowset, 'cmr_modifier_id', 'cmr_modifier');
        $rowset             = $modelUser->FillUserInfo($rowset, 'cmr_creator_id', 'cmr_creator');
        
        $modelCompany       = new Company();
        $rowset             = $modelCompany->FillCompanyInfo($rowset, 'cmr_transporter_id', 'cmr_transporter');
        $rowset             = $modelCompany->FillCompanyInfo($rowset, 'cmr_owner_id', 'cmr_owner');

        $modelAttachment    = new Attachment();
        $rowset             = $modelAttachment->FillAttachmentInfo($rowset, 'cmr_attachment_id', 'cmr_attachment');
        
        $modelRA            = new RA();
        $rowset             = $modelRA->FillRAInfo($rowset, 'cmr_ra_id', 'cmr_ra');
        
        foreach ($rowset as $key => $row)
        {
            if (isset($row[$entityname]))
            {
                if (isset($row['cmr_ra']))
                {
                    $rowset[$key][$entityname]['ra'] = $row['cmr_ra'];
                    unset($rowset[$key]['cmr_ra']);
                }
                
                if (isset($row['cmr_modifier']))
                {
                    $rowset[$key][$entityname]['modifier'] = $row['cmr_modifier'];
                    unset($rowset[$key]['cmr_modifier']);
                }                

                if (isset($row['cmr_creator']))
                {
                    $rowset[$key][$entityname]['creator'] = $row['cmr_creator'];
                    unset($rowset[$key]['cmr_creator']);
                }
                
                if (isset($row['cmr_transporter']))
                {
                    $rowset[$key][$entityname]['transporter'] = $row['cmr_transporter'];
                    unset($rowset[$key]['cmr_transporter']);
                }

                if (isset($row['cmr_owner']))
                {

                    $rowset[$key][$entityname]['owner'] = $row['cmr_owner'];
                    unset($rowset[$key]['cmr_owner']);
                }
                
                if (isset($row['cmr_attachment']))
                {
                    $rowset[$key][$entityname]['attachment'] = $row['cmr_attachment'];
                    unset($rowset[$key]['cmr_attachment']);
                }
                
                unset($rowset[$key]['cmr_ra_id']);
                unset($rowset[$key]['cmr_creator_id']);
                unset($rowset[$key]['cmr_modifier_id']);
                unset($rowset[$key]['cmr_transporter_id']);
                unset($rowset[$key]['cmr_owner_id']);
                unset($rowset[$key]['cmr_attachment_id']);
            }
            
            $rowset[$key][$entityname]['doc_no_short'] = 'cmr' . $row[$entityname]['id'];
            
            $total_qtty             = 0;
            $total_weight           = 0;
            $total_weighed_weight   = 0;
            $total_theor_weight     = 0;
            $total_weighted_weight  = 0;
            foreach ($this->GetItems($row[$entityname]['id']) as $item)
            {
                $total_qtty             += 1;
                $total_weight           += $item['steelitem']['unitweight_ton'];
                $total_weighed_weight   += $item['weighed_weight'];
                $total_theor_weight     += $item['is_theor_weight'] == 1 ? $item['weight'] : 0;
                $total_weighted_weight  += $item['is_theor_weight'] == 0 ? $item['weighed_weight'] : 0;
                
                if (!array_key_exists('order', $item['steelitem'])) continue;
                if (array_key_exists('order', $rowset[$key][$entityname])) continue;
                
                $rowset[$key][$entityname]['cmr_order']         = $item['steelitem']['order'];
            }
            
            $rowset[$key][$entityname]['total_qtty']            = $total_qtty;
            $rowset[$key][$entityname]['total_weight']          = $total_weight;
            $rowset[$key][$entityname]['weighed_weight']        = $total_weighed_weight;
            $rowset[$key][$entityname]['total_theor_weight']    = $total_theor_weight;
            $rowset[$key][$entityname]['total_weighted_weight'] = $total_weighted_weight;
            $rowset[$key][$entityname]['gross_total_weight']    = $total_theor_weight + $total_weighted_weight;
            $rowset[$key][$entityname]['total_weight_max']      = $total_weight * 1.06;
            $rowset[$key][$entityname]['dimension_unit']        = 'mm';//isset($item['steelitem']) ? $item['steelitem']['dimension_unit'] : '';
            $rowset[$key][$entityname]['weight_unit']           = 'ton';//isset($item['steelitem']) ? $item['steelitem']['weight_unit'] : '';
            //
            // Итеграция с данными Заказа
            if ($rowset[$key][$entityname]['number_default'] == 0 && isset($rowset[$key][$entityname]['cmr_order']))
            {
                if (isset($rowset[$key][$entityname]['cmr_order']['company']))
                {
                    if (empty($rowset[$key][$entityname]['buyer_name']))
                    {
                        $rowset[$key][$entityname]['buyer_name'] = $rowset[$key][$entityname]['cmr_order']['company']['title'];
                    }
                    if (empty($rowset[$key][$entityname]['buyer_address']))
                    {
                        $rowset[$key][$entityname]['buyer_address'] = $rowset[$key][$entityname]['cmr_order']['company']['address'] . ' ' . $rowset[$key][$entityname]['cmr_order']['company']['pobox'];
                        if (isset($rowset[$key][$entityname]['cmr_order']['company']['city']))
                        {
                            $rowset[$key][$entityname]['buyer_address'] .= ' ' . $rowset[$key][$entityname]['cmr_order']['company']['city']['title'];
                        }
                        if (isset($rowset[$key][$entityname]['cmr_order']['company']['region']))
                        {
                            $rowset[$key][$entityname]['buyer_address'] .= ' (' . $rowset[$key][$entityname]['cmr_order']['company']['region']['title'] . ')';
                        }
                    }
                }
                
                if (empty($rowset[$key][$entityname]['delivery_point']))
                {
                    $rowset[$key][$entityname]['delivery_point'] = $rowset[$key][$entityname]['cmr_order']['delivery_town'];
                }
                
                unset($rowset[$key][$entityname]['cmr_order']);
            }
        }
        
        return $rowset;
    }
    
    
    /**
     * Возвращает список айтемов для конкретного CMR
     * 
     * @param int $cmr_id
     * @return array
     * 
     * @version 20121119, d10n
     */
    function GetItems($cmr_id)
    {
        $hash       = 'cmr-' . $cmr_id . '-items';
        $cache_tags = array($hash, 'cmrs', 'ras', 'cmr-' . $cmr_id);

        $rowset         = $this->_get_cached_data($hash, 'sp_cmr_get_items', array($cmr_id), $cache_tags);
        
        $modelSteelItem = new SteelItem();
        $rowset         = isset($rowset[0]) ? $modelSteelItem->FillSteelItemInfo($rowset[0]) : array();
        
        return $rowset;
    }
    
    /**
     * Обновляет данные об атачменте, связанным с документом
     * 
     * @param int $cmr_id
     * @param int $attachment_id
     * 
     * @version 20121119, d10n
     */
    public function UpdateAttachment($cmr_id, $attachment_id)
    {
        $this->Update($cmr_id, array(
            'attachment_id' => $attachment_id
        ));
        
        Cache::ClearTag('cmr-' . $cmr_id);
        Cache::ClearTag('cmrs');
    }
    
    /**
     * Возвращает набор данных
     * отформатированных для генерации Pdf-документа
     * 
     * @param int $id
     */
    public function GetForPDF($id)
    {
        $cmr = $this->GetById($id);
        //_epd($cmr);
        if (!isset($cmr['cmr']) || empty($cmr['cmr'])) return array();
        
        $cmr = $cmr['cmr'];
        
        $output = array(
            'id'                => $cmr['id'],
            'doc_no'            => $cmr['doc_no'],
            'international_consignement' => $cmr['number'],
            'sender'            => '',
            'consignee'         => $cmr['buyer_name'] . "\n" . $cmr['buyer_address'],
            'place_of_delivery' => $cmr['ra']['destination'],
            'place_and_date'    => '',
            'carrier'           => '',
            'carrier_ss'        => '',
            'established_in'    => '',
            'established_on'    => '',
            'documents_attached'=> '',//'Packing List No ' . sprintf('%03d', $cmr['id']),
            'marks_nos'         => '',
            'marks_nos_ddt'     => '',
            'gross_weight'      => sprintf('%.2f', round($cmr['gross_total_weight'], 2)) . ' Ton',//sprintf('%.2f', round($cmr['weighed_weight'], 2)) . ' Ton',
            'product_name'      => strtoupper($cmr['product_name']),
            'packing_list'      => array(
                'logo'          => 'pdf/header/se_header_mam.png',
                'logo_description' => '',
                'our_ref'           => '',
                'customer'      => $cmr['buyer_name'],
                'customer_ref'  => '',
                'location'      => '',
                'destination'   => $cmr['delivery_point'],
                'transport_mode'=> 'Truck',
                'loading_date'  => date('d.m.Y', strtotime($cmr['date'])),
                'truck_number'  => $cmr['truck_number'],
                'is_tw_items_exist' => FALSE,// cуществуют ли листы с теоретическим весом (tw) ra_items.is_theor_weight = 1
                'total_weight'      => '',//'WEIGHBRIDGE WEIGHT          ' . sprintf('%.3f', round($cmr['weighed_weight'], 3)) . ' Ton',
            ),
            'filename_suffix'   => '',
        );
        
        if (isset($cmr['owner']))
        {
            $output['sender'] = $cmr['owner']['title'] . "\n";
            $output['sender'] .= $cmr['owner']['address'] . "\n";
            $output['sender'] .= isset($cmr['owner']['city']) ? $cmr['owner']['city']['title'] : '';
            $output['sender'] .= isset($cmr['owner']['region']) ? ', (' . $cmr['owner']['region']['title'] . ')' : '';
            $output['sender'] .= isset($cmr['owner']['country']) ? "\n" . $cmr['owner']['country']['title'] : '';
            
            $output['sender_ss']   = $cmr['owner']['title'];
            
            
            $owner_type = strtoupper(substr(trim($cmr['owner']['title_trade']), -2));
        
            if ($owner_type == 'UK')
            {
                $output['packing_list']['logo_description'] = "M1, 17Airlie Gardens, London W8 7AN, U.K.   Tel : +44 (0)207 792 46 66\n";
                $output['packing_list']['logo_description'] .= "Fax: +44 (0)870 169 18 58 eMail: plates@steelemotion.com www.steelemotion.com";
                $output['filename_suffix'] = 'uk';
            }
            if ($owner_type == 'IT')
            {
                $output['packing_list']['logo_description'] = "Piazza della Chiesa, 4 San Giorgio di Nogaro, (UD), Italy   Tel : +44 (0)207 792 46 66\n";
                $output['packing_list']['logo_description'] .= "Fax: +44 (0)870 169 18 58 eMail: plates@steelemotion.com www.steelemotion.com";
                $output['filename_suffix'] = 'it';
            }
        }
        
        if (isset($cmr['transporter']))
        {
            $output['carrier'] = $cmr['transporter']['title'] . "\n";
            $output['carrier'] .= $cmr['transporter']['address'];
            $output['carrier'] .= !empty($cmr['transporter']['pobox']) ? "\n" . $cmr['transporter']['pobox'] : '';
            $output['carrier'] .= isset($cmr['transporter']['city']) ? "\n" . $cmr['transporter']['city']['title'] : '';
            
            $output['carrier_ss']   = $cmr['transporter']['title'] . "\n\n" . $cmr['truck_number'];
        }
        
        if (isset($cmr['ra']['stockholder']))
        {
            $output['established_in']   = isset($cmr['ra']['stockholder']['city']) ? $cmr['ra']['stockholder']['city']['title'] : '';
            $output['established_on']   = date('d.m.Y', strtotime($cmr['date']));
            
            $output['place_and_date']   = $output['established_in'] . ' - ' .  $output['established_on'];
            
            $stockholder_title = $cmr['ra']['stockholder']['title'];
            if (stristr($stockholder_title, 'ossilaser') !== false)
            {
                $output['marks_nos_ddt']    = !empty($cmr['ra']['ddt_number']) ? 'DDT ' . $cmr['ra']['ddt_number'] . ' dd ' . $output['established_on'] : '';
                $output['marks_nos_ddt']    .= "\n" . $stockholder_title;
            }
            else
            {
                $output['marks_nos_ddt']    = '';                
            }
            
            
            $output['packing_list']['location'] = $output['established_in'];
            $output['packing_list']['location'] .= isset($cmr['ra']['stockholder']['country']) ? ', ' . $cmr['ra']['stockholder']['country']['title'] : '';
            
        }
        
        $items_list = $this->GetItems($id);
        if (empty($items_list)) return $cmr;
        
        $our_refs_list = array();
        foreach ($items_list as $item)
        {
            $steelitem  = $item['steelitem'];
            $order      = isset($steelitem['order']) ? $steelitem['order'] : array();
            $order_id   = array_key_exists('id', $order) ? $order['id'] : 0;
            
            $params = explode('.', $steelitem ['thickness_mm']);
            
            if (isset($params[1]) && $params[1] > 0)
            {
                $thickness_mm = sprintf('%.1f', round($steelitem['thickness_mm'], 1));
            }
            else
            {
                $thickness_mm = round($steelitem['thickness_mm'], 0);
            }
            
            $output['packing_list']['items_list'][] = array(
                'guid'          => strtoupper($steelitem['guid']),
                'steelgrade'    => strtoupper($steelitem['steelgrade']['title']),
                'thickness_mm'  => $thickness_mm,
                'width_mm'      => round($steelitem['width_mm'], 0),
                'length_mm'     => round($steelitem['length_mm'], 0),
                'qtty'          => '1',
                'weight_ton'    => sprintf('%.3f', round(($item['is_theor_weight'] == 1 ? $item['weight'] : $item['weighed_weight']), 3)),
            );
            
            if ($item['is_theor_weight'] == 1)
            {
                $output['packing_list']['is_tw_items_exist'] = TRUE;
            }
            
            if ($order_id > 0 && isset($order['biz']))
            {
                $our_refs_list[$order['biz']['id']] = $order['biz']['doc_no'];
            }
        }
        
        if ($order_id > 0)
        {
            $output['packing_list']['customer_ref'] = $order['buyer_ref'];
            $output['packing_list']['our_ref'] = implode(', ', array_values($our_refs_list));
        }
        
        $items_count = count($items_list);
        $output['marks_nos'] = $items_count . ' ' . ($items_count > 1 ? 'PIECES' : 'PIECE');
        $output['marks_nos'] .= "\n" . 'AS PER ATTACHED';
        $output['marks_nos'] .= "\n" . 'PACKING LIST';
        
        $output['packing_list']['total_weight'] = $output['packing_list']['is_tw_items_exist']
            ? 'TOTAL WEIGHT                      '
            : 'WEIGHBRIDGE WEIGHT          ';
        $output['packing_list']['total_weight'] .= ' ' . sprintf('%.3f', round($cmr['gross_total_weight'], 3)) . ' Ton';
        
        return $output;
    }
        
    /**
     * Помечает документ как Актуальный
     * 
     * @param int $id ID конкретного документа
     * @version 20121126, d10n
     */
    public function SetAsActual($id)
    {
        $result = $this->Update($id, array('is_outdated' => 0));
        
        Cache::ClearTag('cmr-' . $id);
        Cache::ClearTag('cmrs');
        
        return $result;
    }
}