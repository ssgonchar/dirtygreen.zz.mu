<?php
require_once APP_PATH . 'classes/models/paymenttype.class.php';
require_once APP_PATH . 'classes/models/ra.class.php';
require_once APP_PATH . 'classes/models/steelitem.class.php';

class DDT extends Model
{
    function DDT()
    {
        Model::Model('ddt');
    }
    
    /**
     * Возвращает данные конкрентой записи по ID
     * 
     * @param mixed $id
     * 
     * @version 20121102, d10n
     */
    function GetById($id)
    {
        $dataset = $this->FillDDTInfo(array(array('ddt_id' => $id)));
        return isset($dataset) && isset($dataset[0]) && isset($dataset[0]['ddt']) ? $dataset[0] : null;
    }
    
    /**
     * Возвращает линейный список DDTs
     * 
     * @param int $ra_id
     * @param int $page_no
     * @param int $per_page
     * @return array
     * 
     * @version 20121105, d10n
     */
    public function GetList($ra_id = 0, $page_no = 0, $per_page = ITEMS_PER_PAGE)
    {
        $page_no    = $page_no > 0 ? $page_no : 1;
        $per_page   = $per_page < 1 ? ITEMS_PER_PAGE : $per_page;
        $start      = ($page_no - 1) * $per_page;

        $hash       = 'ddt-list' . md5('-ra-id-' . $ra_id . '-page_no' . $page_no . '-start' . $start);
        $cache_tags = array($hash, 'ddts');
        
        $data_set   = $this->_get_cached_data($hash, 'sp_ddt_get_list', array($ra_id, $start, $per_page), $cache_tags);
        if (!isset($data_set[0])) return array('data' => array(), 'count' => 0);
        
        $list       = $this->FillDDTInfo($data_set[0]);
        $rows_count = (isset($data_set[1]) && isset($data_set[1][0]) && isset($data_set[1][0]['rows_count'])) ? $data_set[1][0]['rows_count'] : 0;
        
        return array('data' => $list, 'count' => $rows_count);
    }
    
    
    /**
     * Сохраняет данные по DDT
     * 
     * @param int $id [INT]
     * @param int $ra_id [INT]
     * @param string $number [VARCHAR(20)]
     * @param string $buyer [VARCHAR(500)]
     * @param string $delivery_point [VARCHAR(500)]
     * @param string $date [TIMESTAMP]
     * @param string $iva [VARCHAR(20)]
     * @param int $paymenttype_id [INT]
     * @param int $causale_id [INT]
     * @param int $porto_id [INT]
     * @param string $truck_number [VARCHAR(20)]
     * @param int $transporter_id [INT]
     * @param int $attachment_id [INT]
     * @param int $dest_type_id [TINYINT]
     * 
     * @return array
     * 
     * @version 20121102, d10n
     */
    public function Save($id, $ra_id, $owner_id, $number, $buyer, $delivery_point, $date, $iva,  $paymenttype_id,
        $causale_id, $porto_id, $truck_number, $transporter_id, $attachment_id, $dest_type_id)
    {
        $data_set = $this->CallStoredProcedure('sp_ddt_save', array($this->user_id, $id, $ra_id, $owner_id, $number, $buyer,
            $delivery_point, $date, $iva, $paymenttype_id, $causale_id,
            $porto_id, $truck_number, $transporter_id, $attachment_id, $dest_type_id));
        
        $rowset = isset($data_set) && isset($data_set[0]) && isset($data_set[0][0]) ? $data_set[0][0] : array();
        
        if (isset($rowset['ddt_id'])) Cache::ClearTag('ddt-' . $rowset['ddt_id']);
        Cache::ClearTag('ddts');
        
        return $rowset;
    }
    
    /**
     * Создает новую DDT
     * 
     * @param int $ra_id
     * @param int $owner_id
     * @param string $truck_number
     * 
     * @return array
     */
    public function Create($ra_id, $owner_id, $transporter_id, $truck_number)
    {
        $id             = 0;
        $number         = 0;
        $buyer          = '';
        $delivery_point = '';
        $date           = 'NULL VALUE!';
        $iva            = '';
        $paymenttype_id = 0;
        $causale_id     = 0;
        $porto_id       = 0;
        $attachment_id  = 0;
        $dest_type_id   = 0;
        
        return $this->Save($id, $ra_id, $owner_id, $number, $buyer, $delivery_point, $date, $iva, $paymenttype_id, $causale_id, $porto_id, 
                            $truck_number, $transporter_id, $attachment_id, $dest_type_id);
    }
    
    
    /**
     * Возвращает базовый набор данных по объекту
     * 
     * @param array $rowset Набор IDs array(array('ddt_id' => 1), )
     * @param string $id_fieldname Название поля, в котором хранятся идентификаторы сущности (например ddt_id)
     * @param string $entityname Название сущности = имя выходного массива для каждой строки входного (например, 'ddt')
     * @param string $cache_prefix Префикс для выборки данных из кеша (например, 'ddt')
     * 
     * @version 20121102, d10n
     */
    public function FillDDTMainInfo($rowset, $id_fieldname = 'ddt_id', $entityname = 'ddt', $cache_prefix = 'ddt')
    {
        $rowset = $this->_fill_entity_info($rowset, $id_fieldname, $entityname, $cache_prefix, 'sp_ddt_get_list_by_ids', array('ddts' => '', 'ddt' => 'id', 'ras' => ''), array());
        
        foreach ($rowset as $key => $row)
        {
            if (!isset($row[$entityname])) continue;
            
            $row = $row[$entityname];
            
            if (isset($row['number']))
            {
                $sprintf_format = $row['number'] <= 10 ? '%02d' : '%d';
                $rowset[$key][$entityname]['number_default'] = $row['number'];
                $rowset[$key][$entityname]['number'] = sprintf($sprintf_format, $row['number']) . '/' . date('Y', strtotime($row['created_at']));
                
                $rowset[$key][$entityname]['doc_no'] = 'ddt' . $rowset[$key][$entityname]['number'];
            }
            else
            {
                $rowset[$key][$entityname]['doc_no'] = 'ddt # ' . $row['id'];
            }
        }
        
        return $rowset;
    }
    
    /**
     * Возвращает расширенный набор данных по объекту
     * 
     * @param array $rowset Набор IDs array(array('ddt_id' => 1), )
     * @param string $id_fieldname Название поля, в котором хранятся идентификаторы сущности (например ddt_id)
     * @param string $entityname Название сущности = имя выходного массива для каждой строки входного (например, 'ddt')
     * @param string $cache_prefix Префикс для выборки данных из кеша (например, 'ddt')
     * 
     * @version 20121112, d10n: Добавлена интеграция с данными заказа (если есть в айтемах)
     * @version 20121102, d10n
     */
    public function FillDDTInfo($rowset, $id_fieldname = 'ddt_id', $entityname = 'ddt', $cache_prefix = 'ddt')
    {
        $rowset = $this->FillDDTMainInfo($rowset, $id_fieldname, $entityname, $cache_prefix);

        foreach ($rowset as $key => $row)
        {
            if (!isset($row[$entityname])) continue;
            
            $row = $row[$entityname];
            
            $rowset[$key]['ddt_paymenttype_id'] = $row['paymenttype_id'];
            //$rowset[$key]['ddt_causale_id']     = $row['causale_id'];
            //$rowset[$key]['ddt_porto_id']       = $row['porto_id'];
            $rowset[$key]['ddt_transporter_id'] = $row['transporter_id'];
            $rowset[$key]['ddt_owner_id']       = $row['owner_id'];
            $rowset[$key]['ddt_attachment_id']  = $row['attachment_id'];
            $rowset[$key]['ddt_modifier_id']    = $row['modified_by'];
            $rowset[$key]['ddt_creator_id']     = $row['created_by'];
            $rowset[$key]['ddt_ra_id']          = $row['ra_id'];
        }

        $modelUser          = new User();
        $rowset             = $modelUser->FillUserInfo($rowset, 'ddt_modifier_id', 'ddt_modifier');
        $rowset             = $modelUser->FillUserInfo($rowset, 'ddt_creator_id', 'ddt_creator');
        
        $modelCompany       = new Company();
        $rowset             = $modelCompany->FillCompanyInfo($rowset, 'ddt_transporter_id', 'ddt_transporter');
        $rowset             = $modelCompany->FillCompanyInfo($rowset, 'ddt_owner_id', 'ddt_owner');

        $modelPaymentType   = new PaymentType();
        $rowset             = $modelPaymentType->FillPaymentTypeInfo($rowset, 'ddt_paymenttype_id', 'ddt_paymenttype');
        
        $attachments        = new Attachment();
        $rowset             = $attachments->FillAttachmentInfo($rowset, 'ddt_attachment_id', 'ddt_attachment');
        
        $modelRA            = new RA();
        $rowset             = $modelRA->FillRAInfo($rowset, 'ddt_ra_id', 'ddt_ra');
        
        foreach ($rowset as $key => $row)
        {
            if (isset($row[$entityname]))
            {
                if (isset($row['ddt_ra']))
                {
                    $rowset[$key][$entityname]['ra'] = $row['ddt_ra'];
                    unset($rowset[$key]['ddt_ra']);
                }
                
                if (isset($row['ddt_modifier']))
                {
                    $rowset[$key][$entityname]['modifier'] = $row['ddt_modifier'];
                    unset($rowset[$key]['ddt_modifier']);
                }                

                if (isset($row['ddt_creator']))
                {
                    $rowset[$key][$entityname]['creator'] = $row['ddt_creator'];
                    unset($rowset[$key]['ddt_creator']);
                }
                
                if (isset($row['ddt_transporter']))
                {
                    $rowset[$key][$entityname]['transporter'] = $row['ddt_transporter'];
                    unset($rowset[$key]['ddt_transporter']);
                }

                if (isset($row['ddt_owner']))
                {

                    $rowset[$key][$entityname]['owner'] = $row['ddt_owner'];
                    unset($rowset[$key]['ddt_owner']);
                }

                if (isset($row['ddt_paymenttype']))
                {
                    $rowset[$key][$entityname]['paymenttype'] = $row['ddt_paymenttype'];
                    unset($rowset[$key]['ddt_paymenttype']);
                }
                
                if (isset($row['ddt_attachment']))
                {
                    $rowset[$key][$entityname]['attachment'] = $row['ddt_attachment'];
                    unset($rowset[$key]['ddt_attachment']);
                }
                
                unset($rowset[$key]['ddt_ra_id']);
                unset($rowset[$key]['ddt_creator_id']);
                unset($rowset[$key]['ddt_modifier_id']);
                unset($rowset[$key]['ddt_transporter_id']);
                unset($rowset[$key]['ddt_owner_id']);
                unset($rowset[$key]['ddt_paymenttype_id']);
                unset($rowset[$key]['ddt_attachment_id']);
            }
            
            $rowset[$key][$entityname]['doc_no_short'] = 'ddt' . $row[$entityname]['id'];
            
            $total_qtty             = 0;
            $total_weight           = 0;
            $total_weighed_weight   = 0;
            foreach ($this->GetItems($row[$entityname]['id']) as $item)
            {
                if (!array_key_exists('steelitem', $item)) continue;
                
                $total_qtty             += 1;
                $total_weight           += isset($item['steelitem']['unitweight_ton']) ? $item['steelitem']['unitweight_ton'] : 0;
                $total_weighed_weight   += $item['weighed_weight'];
                
                if (!array_key_exists('order', $item['steelitem'])) continue;
                if (array_key_exists('order', $rowset[$key][$entityname])) continue;
                
                $rowset[$key][$entityname]['order'] = $item['steelitem']['order'];
            }
            
            $rowset[$key][$entityname]['total_qtty']        = $total_qtty;
            $rowset[$key][$entityname]['total_weight']      = $total_weight;
            $rowset[$key][$entityname]['weighed_weight']    = $total_weighed_weight;
            $rowset[$key][$entityname]['total_weight_max']  = $total_weight * 1.06;
            $rowset[$key][$entityname]['dimension_unit']    = 'mm';//isset($item['steelitem']) ? $item['steelitem']['dimension_unit'] : '';
            $rowset[$key][$entityname]['weight_unit']       = 'ton';//isset($item['steelitem']) ? $item['steelitem']['weight_unit'] : '';
            
            $rowset[$key][$entityname]['causale']['doc_no'] = $rowset[$key][$entityname]['causale_id'] == 1 ? 'c/to lavorazione' : 'c/to vendita';
            $rowset[$key][$entityname]['porto']['doc_no']   = $rowset[$key][$entityname]['porto_id'] == 1 ? 'f.co partenza' : 'f.co destino';
            
            // 20130116, zharkov: закоментировал
            // $rowset[$key][$entityname]['delivery_point']    = '';
            
            // срабатывает на этапе создания и/или редактирования нового документа
            if ($rowset[$key][$entityname]['number_default'] == 0)
            {
                // Итеграция с данными Заказа
                if (isset($rowset[$key][$entityname]['order']))
                {
                    if (empty($rowset[$key][$entityname]['buyer']))
                    {
                        $rowset[$key][$entityname]['buyer'] = $rowset[$key][$entityname]['order']['company']['title'] . "\n";
                        $rowset[$key][$entityname]['buyer'] .= $rowset[$key][$entityname]['order']['company']['address'] . ' ' . $rowset[$key][$entityname]['order']['company']['pobox'];
                        if (isset($rowset[$key][$entityname]['order']['company']['city']))
                        {
                            $rowset[$key][$entityname]['buyer'] .= ' ' . $rowset[$key][$entityname]['order']['company']['city']['title'];
                        }
                        if (isset($rowset[$key][$entityname]['order']['company']['region']))
                        {
                            $rowset[$key][$entityname]['buyer'] .= ' (' . $rowset[$key][$entityname]['order']['company']['region']['title'] . ')';
                        }
                    }
                    if (empty($rowset[$key][$entityname]['delivery_point']))
                    {
                        $rowset[$key][$entityname]['delivery_point'] .= $rowset[$key][$entityname]['order']['delivery_town'];
                    }
                    if (empty($rowset[$key][$entityname]['iva']))
                    {
                        $rowset[$key][$entityname]['iva'] = $rowset[$key][$entityname]['order']['company']['vat'];
                    }
                    if ($rowset[$key][$entityname]['paymenttype_id'] <= 0 && $rowset[$key][$entityname]['order']['paymenttype_id'] > 0)
                    {
                        $rowset[$key][$entityname]['paymenttype_id']    = $rowset[$key][$entityname]['order']['paymenttype_id'];
                        $rowset[$key][$entityname]['paymenttype']       = $rowset[$key][$entityname]['order']['paymenttype'];
                    }

                    unset($rowset[$key][$entityname]['order']);
                }

                if (isset($rowset[$key][$entityname]['ra']['dest_stockholder']))
                {
                    $dest_stockholder = $rowset[$key][$entityname]['ra']['dest_stockholder'];

                    $rowset[$key][$entityname]['delivery_point'] .= empty($rowset[$key][$entityname]['delivery_point']) ? '' : ', ';
                    $rowset[$key][$entityname]['delivery_point'] .= $dest_stockholder['title'] . "\n";
                    $rowset[$key][$entityname]['delivery_point'] .= $dest_stockholder['address'] . "\n";
                    $rowset[$key][$entityname]['delivery_point'] .= $dest_stockholder['zip'] . (isset($dest_stockholder['city']) ? ' ' . $dest_stockholder['city']['title'] : '');
                }
            }
        }
        
        return $rowset;
    }
    
    
    /**
     * Возвращает список айтемов для конкретного DDT
     * 
     * @param int $ddt_id
     * @return array
     * 
     * @version 20121102, d10n
     */
    function GetItems($ddt_id)
    {
        $hash       = 'ddt-' . $ddt_id . '-items';
        $cache_tags = array($hash, 'ddts', 'ras', 'ddt-' . $ddt_id);

        $rowset         = $this->_get_cached_data($hash, 'sp_ddt_get_items', array($ddt_id), $cache_tags);
        
        $modelSteelItem = new SteelItem();
        $rowset         = isset($rowset[0]) ? $modelSteelItem->FillSteelItemInfo($rowset[0]) : array();
        
        return $rowset;
    }
    
    /**
     * Обновляет данные об атачменте, связанным с документом
     * 
     * @param int $ddt_id
     * @param int $attachment_id
     * 
     * @version 20121106, d10n
     */
    public function UpdateAttachment($ddt_id, $attachment_id)
    {
        $this->Update($ddt_id, array(
            'attachment_id' => $attachment_id
        ));
        
        Cache::ClearTag('ddt-' . $ddt_id);
        Cache::ClearTag('ddts');
    }
    
    /**
     * Возвращает набор данных
     * отформатированных для генерации Pdf-документа
     * 
     * @param int $id
     * @version 20121106, d10n
     */
    public function GetForPDF($id)
    {
        $ddt = $this->GetById($id);
        
        if (!isset($ddt['ddt']) || empty($ddt['ddt'])) return array();
        
        $ddt = $ddt['ddt'];
        
        $output = array(
            'id'            => $ddt['id'],
            'doc_no'        => $ddt['doc_no'],
            'attachment_id' => $ddt['attachment_id'],
        
            'logo'                  => K_PATH_IMAGES . 'pdf/header/ddt_mam.png',
            'logo_description'      => '',
            'ddt'                   => '(D.P.R. N.472 DEL 14/08/1996)',
            'documento'             => 'DOCUMENTO DI TRASPORTO',
            'tipo'                  => '1',
            'numero'                => $ddt['number'],
            'data'                  => !empty($ddt['date']) && $ddt['date'] > 0 ? date('d.m.y', strtotime($ddt['date'])) : '',
            'pagina'                => '1',
            'destinazione_merce'    => $ddt['delivery_point'],//"Ossilaser Tonello S.p.A.\nVia Dell'Artigianato, 12 33042 Buttrio (UD)",
            'fatturare_a'           => $ddt['buyer'],//"Ossilaser Tonello S.p.A.\nVia Dell'Artigianato, 12 33042 Buttrio (UD)",
            'fatturare_a_desc'      => $ddt['dest_type_id'],// == 1 ? 'FATTURARE A' : 'DESTINATARIO',
            'codice_cliente'        => '',
            'partita_iva'           => $ddt['iva'],
            'agente'                => '',
            'codice_per_fornitore'  => '',
            'pagamento'             => isset($ddt['paymenttype']) ? $ddt['paymenttype']['title'] : '',
            'inizio_trasporto'      => !empty($ddt['date']) && $ddt['date'] > 0 ? date('d.m.y', strtotime($ddt['date'])) : '',
            'causale_trasporto'     => strtoupper($ddt['causale']['doc_no']),
            'trasporto_a_cura_del'  => 'CORRIERE',
            'porto'                 => strtoupper($ddt['porto']['doc_no']),
        
            //'items_header'          => 'Ordine dd 06.09.12',
            'items'                 => array(),
            //'items_description'     => "MERCE GIA IN OSSILASER ARRIVATO CON DDT NR 1300 DD 05.07.11\nOFFICINE MECCANICHE ZOPPELETTO SPA",
            'pl_footer'             => 'PESO VERIFICATO : ' . sprintf("%.3f", round($ddt['weighed_weight'], 2)) . ' TON',// . $rowset['weight_unit'],
        
            'annotazione'           => '',
            'annotazione_line2'     => $ddt['truck_number'],
            'n_colli'               => $ddt['total_qtty'],
            'peso_lordo'            => '',
            'peso_netto'            => $ddt['weighed_weight'] > 0 ? sprintf("%.3f", $ddt['weighed_weight']) . ' Ton' : '',
            'aspetto_esteriore_dei_beni'=> 'FOGLI LAMIERA',
            'vettore'               => '',
            'data_e_ora_ritiro'     => '',
            'firma_conducente'      => '',
            'firma_destinatario'    => '',
            'firma_vettore'         => '',
            'filename_suffix'       => '',
        );
        
        if (isset($ddt['transporter']))
        {
            $output['vettore'] .= $ddt['transporter']['title'] . ' / ' . $ddt['transporter']['address'];
            $output['vettore'] .= isset($ddt['transporter']['city']) ? ' ' . $ddt['transporter']['city']['title'] : '';
            $output['vettore'] .= isset($ddt['transporter']['region']) ? ' (' . $ddt['transporter']['region']['title'] . ')' : '';
            $output['vettore'] .= "\n";
            $output['vettore'] .= $ddt['transporter']['vat'] . (!empty($ddt['transporter']['albo']) ? ' / ' . $ddt['transporter']['albo'] : '');
        }
        
        $items_list = $this->GetItems($id);
        
        if (empty($items_list)) return $ddt;

        // new version 20130802, zharkov 
        // group items by order & in ddt       
        $item_groups = array();
        foreach ($items_list as $item)
        {
            $steelitem  = $item['steelitem'];
            $order      = isset($steelitem['order']) ? $steelitem['order'] : array();
            $order_id   = isset($order['id']) ? $order['id'] : 0;
            
            if (!isset($item_groups[$order_id]))
            {
                $item_groups[$order_id] = array(
                    'order' => $order,
                    'data'  => array()
                );    
            }
            
            $in_ddt_id = 0;
            if (stristr($steelitem['stockholder']['title'], 'ossilaser') !== false)
            {
                $in_ddt_id = $steelitem['in_ddt_id'];
            }
            
            $item_groups[$order_id]['data'][$in_ddt_id][] = $item;
        }

        foreach($item_groups as $order_group)
        {
            $arr = array(
                'header'    => '',
                'data'      => array()
            );


            $arr['header'] = isset($order_group['order']) && !empty($order_group['order'])
                    ? (!empty($order_group['order']['buyer_ref']) ? $order_group['order']['buyer_ref'] : 'dd ' . date('d.m.y', strtotime($order_group['order']['created_at'])))
                    : '';


            foreach ($order_group['data'] as $inddt_group)
            {
                $block              = array();
                $steelitem          = $inddt_group[0]['steelitem'];
                
                if (stristr($steelitem['stockholder']['title'], 'ossilaser') !== false)
                {
                    $block['footer']    = 'MERCE GIA IN ' . $steelitem['stockholder']['doc_no'];
                    
                    if(!empty($steelitem['in_ddt_number']))
                    {
                        $block['footer'] .= ' ARRIVATO CON DDT Nr ' . $steelitem['in_ddt_number'];
                        $block['footer'] .= !empty($steelitem['in_ddt_date']) && $steelitem['in_ddt_date'] > 0 ? "\n" . 'dd ' . date('d.m.y', strtotime($steelitem['in_ddt_date'])) : '';
                        $block['footer'] .= isset($steelitem['in_ddt_company']) ? " " . $steelitem['in_ddt_company']['title'] : '';
                    }                    
                }
                else
                {
                    $block['footer'] = '';
                }
                                
                foreach ($inddt_group as $item)
                {
                    $steelitem  = $item['steelitem'];
                    $params     = explode('.', $steelitem['thickness_mm']);            
                    if (isset($params[1]) && $params[1] > 0)
                    {
                        $thickness_mm = sprintf('%.1f', round($steelitem['thickness_mm'], 1));
                    }
                    else
                    {
                        $thickness_mm = round($steelitem['thickness_mm'], 0);
                    }

                    $block['list'][] = array(
                        'steelgrade'    => strtoupper($steelitem['steelgrade']['title']),
                        'dimensions'    => $thickness_mm . ' x ' . round($steelitem['width_mm'], 0) . ' x ' . round($steelitem['length_mm'], 0) . ' mm' . (!empty($steelitem['guid']) ? ' - ' . $steelitem['guid'] : ''),
                        'um'            => 'Ton',
                        'quantita'      => sprintf('%.3f', round($item['weighed_weight'], 3)),
                    );
                }
                
                $arr['data'][] = $block;
            }
            
            $output['items'][] = $arr;
        }
//dg($output['items']);
/* old version before 20130802        
        foreach ($items_list as $item)
        {
            $steelitem  = $item['steelitem'];
            $order      = isset($steelitem['order']) ? $steelitem['order'] : array();
            $order_id   = array_key_exists('id', $order) ? $order['id'] : 0;
            
            if (!array_key_exists($order_id, $output['items']))
            {
                $header = $order_id > 0
                        ? (!empty($order['buyer_ref']) ? $order['buyer_ref'] : 'dd ' . date('d.m.y', strtotime($order['created_at'])))
                        : '';
                
                $footer = 'MERCE GIA IN ' . $steelitem['stockholder']['doc_no'];

                if (!empty($steelitem['ddt_number']) && $steelitem['ddt_number'] != $ddt['number'])
                {
                    $footer .= ' ARRIVATO CON DDT Nr ' . $steelitem['ddt_number'];
                    $footer .= !empty($steelitem['ddt_date']) && $steelitem['ddt_date'] > 0 ? "\n" . 'dd ' . date('d.m.y', strtotime($steelitem['ddt_date'])) : '';
                    //$footer .= isset($steelitem['ddt_company']) ? "\n" . $steelitem['ddt_company']['title'] : '';
                    $footer .= isset($steelitem['ddt_company']) ? " " . $steelitem['ddt_company']['title'] : '';
                }
                else if(!empty($steelitem['in_ddt_number']))
                {
                    $footer .= ' ARRIVATO CON DDT Nr ' . $steelitem['in_ddt_number'];
                    $footer .= !empty($steelitem['in_ddt_date']) && $steelitem['in_ddt_date'] > 0 ? "\n" . 'dd ' . date('d.m.y', strtotime($steelitem['in_ddt_date'])) : '';
                    //$footer .= isset($steelitem['in_ddt_company']) ? "\n" . $steelitem['in_ddt_company']['title'] : '';
                    $footer .= isset($steelitem['in_ddt_company']) ? " " . $steelitem['in_ddt_company']['title'] : '';
                }
                
                $output['items'][$order_id] = array(
                    'header'    => $header,
                    'list'      => array(),
                    'footer'    => $footer,
                );
            }
            
            $params = explode('.', $steelitem ['thickness_mm']);
            
            if (isset($params[1]) && $params[1] > 0)
            {
                $thickness_mm = sprintf('%.1f', round($steelitem['thickness_mm'], 1));
            }
            else
            {
                $thickness_mm = round($steelitem['thickness_mm'], 0);
            }
            
            $output['items'][$order_id]['list'][] = array(
                'steelgrade'    => strtoupper($steelitem['steelgrade']['title']),
                'dimensions'    => $thickness_mm . ' x ' . round($steelitem['width_mm'], 0) . ' x ' . round($steelitem['length_mm'], 0) . ' mm' . (!empty($steelitem['guid']) ? ' - ' . $steelitem['guid'] : ''),
                'um'            => 'Ton',
                'quantita'      => sprintf('%.3f', round($item['weighed_weight'], 3)),
            );
        }
*/        
        $owner_type = strtoupper(substr(trim($steelitem['owner']['title_trade']), -2));
        
        if ($owner_type == 'UK')
        {
            $output['logo_description'] = "M1, 17Airlie Gardens, London W8 7AN, U.K.\n";
            $output['logo_description'] .= "Tel : +44 (0)207 792 46 66 Fax: +44 (0)870 169 18 58\n";
            $output['logo_description'] .= "plates@steelemotion.com www.steelemotion.com\n";
            $output['logo_description'] .= "VAT: GB 628797873";
            
            $output['annotazione'] = 'Origene merce: Cervignano (UD)-Beni consegnati per conto di un soggetto passivo comunitarion';
            $output['filename_suffix'] = 'uk';
        }
        if ($owner_type == 'IT')
        {
            $output['logo_description'] = "Piazza della Chiesa, 4 San Giorgio di Nogaro, (UD), Italy\n";
            $output['logo_description'] .= "Tel : +44 (0)207 792 46 66 Fax: +44 (0)870 169 18 58\n";
            $output['logo_description'] .= "plates@steelemotion.com www.steelemotion.com\n";
            $output['logo_description'] .= "P.IVA: IT 02636220309";
            
            $output['filename_suffix'] = 'it';
        }
        
        return $output;
    }
    
    /**
     * Помечает документ как Outdated
     * 
     * @param int $id ID конкретного документа
     * @version 20121126, d10n
     * @version 20121128, zharkov: deprecated
     */
    public function deprecated_SetAsOutdated($id)
    {
        $result = $this->Update($id, array('is_outdated' => 1));
        
        Cache::ClearTag('ddt-' . $id);
        Cache::ClearTag('ddts');
        
        return $result;
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
        
        Cache::ClearTag('ddt-' . $id);
        Cache::ClearTag('ddts');
        
        return $result;
    }
    
    /**
     * Удаляет документ
     * 
     * @param int $id
     * @version 20121127, d10n
     * @version 20121128, zharkov: deprecated
     */
    public function deprecated_Remove($id)
    {
        $result = $this->Update($id, array(
            'is_outdated'   => 1,
            'is_deleted'    => 1,
            'modified_at'   => 'NOW()!',
            'modified_by'   => $this->user_id,
        ));
        
        Cache::ClearTag('ddts');
        
        return $result;
    }
    
    /**
     * Снимает отметку "Удален"
     * 
     * @param int $id
     * @version 20121127, d10n
     * @version 20121128, zharkov: deprecated
     */
    public function deprecated_Activate($id)
    {
        $result = $this->Update($id, array(
            'is_outdated'   => 1,
            'is_deleted'    => 0,
            'modified_at'   => 'NOW()!',
            'modified_by'   => $this->user_id,
        ));
        
        Cache::ClearTag('ddts');
        
        return $result;
    }
}