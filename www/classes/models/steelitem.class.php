<?php
//require_once APP_PATH . 'classes/models/clame.class.php';
require_once APP_PATH . 'classes/models/cmr.class.php';
require_once APP_PATH . 'classes/models/company.class.php';
require_once APP_PATH . 'classes/models/country.class.php';
require_once APP_PATH . 'classes/models/deliverytime.class.php';
require_once APP_PATH . 'classes/models/ddt.class.php';
require_once APP_PATH . 'classes/models/inddt.class.php';
require_once APP_PATH . 'classes/models/invoice.class.php';
require_once APP_PATH . 'classes/models/location.class.php';
require_once APP_PATH . 'classes/models/order.class.php';
require_once APP_PATH . 'classes/models/ra.class.php';
require_once APP_PATH . 'classes/models/supplierinvoice.class.php';


define ('ITEM_STATUS_PRODUCTION',   1);
define ('ITEM_STATUS_TRANSFER',     2);
define ('ITEM_STATUS_STOCK',        3);

define ('ITEM_STATUS_ORDERED',      4);
define ('ITEM_STATUS_RELEASED',     5);
define ('ITEM_STATUS_DELIVERED',    6);
define ('ITEM_STATUS_INVOICED',     7);

define ('MAMIT_OWNER_ID',           7117);
define ('MAMUK_OWNER_ID',           5998);
define ('PLATESAHEAD_OWNER_ID',     11980);

class SteelItem extends Model
{
    /**
     * Текущая ревизия склада
     * 
     * @var mixed
     */
    var $revision = '';
    
    
    function SteelItem()
    {
        Model::Model('steelitems');
        
        $this->revision = Request::GetString('stock_revision', $_REQUEST, '', 12);        
    }

    /**
     * Get list without stockholder
     * 
     */
    public function GetListWithoutStockholder()
    {
         $rowset = $this->SelectList(array(
            'fields'    => 'steelitems.id AS steelitem_id',
            'where'     => array(
                'conditions'    => "steelitems.stockholder_id = ? AND steelitems.guid NOT IN ('')",
                'arguments'     => array(0),
            ),
            'order' => 'thickness_mm, width_mm, steelgrades.alias',
            'limit' => 1000,
            'join'  => array(
                array(
                    'type'          => 'LEFT',
                    'table'         => 'steelgrades',
                    'conditions'    => 'steelitems.steelgrade_id = steelgrades.id',
                    'arguments'     => array(),
                ),
            ),
        ));

        return $this->FillSteelItemInfo($rowset);        
    }
    
    /**
     * Get list without owner
     * 
     */
    public function GetListWithoutOwner()
    {
         $rowset = $this->SelectList(array(
            'fields'    => 'steelitems.id AS steelitem_id',
            'where'     => array(
                'conditions'    => "steelitems.owner_id = ? AND steelitems.guid NOT IN ('')",
                'arguments'     => array(0),
            ),
            'order' => 'thickness_mm, width_mm, steelgrades.alias',
            'limit' => 1000,
            'join'  => array(
                array(
                    'type'          => 'LEFT',
                    'table'         => 'steelgrades',
                    'conditions'    => 'steelitems.steelgrade_id = steelgrades.id',
                    'arguments'     => array(),
                ),
            ),
        ));

        return $this->FillSteelItemInfo($rowset);
    }    

	public function GetListStockAudit($stockholder_id, $on_date)
    {
		 $rowset = $this->SelectList(array(
		'fields'    => 'sh.id AS id',
		'where'     => "sh.`is_deleted` = 0 AND sh.stockholder_id IN ({$stockholder_id}) AND sh.is_available=1 AND t2.id IS NOT NULL AND sh.guid <> '' AND sh.status_id <> 7 AND sh.order_id = 0",
		'order' => 'sh.steelitem_id',
		'from' => 'steelitems_history sh',
		'join'  => array(
			array(
				'type'          => 'LEFT',
				'table'         => " (SELECT t2.steelitem_id, MAX(t2.id) AS id
									  FROM steelitems_history t2
									  WHERE t2.modified_at<='{$on_date}'
									  GROUP BY t2.steelitem_id
									) t2",
				'group'			=> array("t2.steelitem_id"),
				'conditions'    => 't2.id=sh.id',
				'arguments'     => array(0),
			),
		),
        ));

		$count = 0;
		$total_valuation_price = 0;
		foreach ($rowset as $steelitem) {

			 $steelitem_tmp = $this->SelectList(array(
			'fields'    => '*',
			'where'     => "sh.id = '{$steelitem['id']}'",
			'order' => 'sh.steelitem_id DESC LIMIT 1',
			'from' => 'steelitems_history sh',
			));			
			
			//position_data
			$steelplate_tmp = $this->SelectList(array(
			'fields'    => 'sp.*',
			'where'     => "sp.steelposition_id = '{$steelitem_tmp[0]['steelposition_id']}' AND sp.modified_at<='{$on_date}'",
			'order' => 'sp.id DESC LIMIT 1',
			'from' => 'steelpositions_history sp',
			));	
			
			//steelgrade_data
			$steelgrade_tmp = $this->SelectList(array(
			'fields'    => 'title',
			'where'     => "id = '{$steelitem_tmp[0]['steelgrade_id']}'",
			'from' => 'steelgrades',
			));	
			
			//stockholder_data
			$stockholder_tmp = $this->SelectList(array(
			'fields'    => 'CONCAT(c.title_short, " (", c.int_location_title, ")") AS location',
			'where'     => "c.id='{$steelitem_tmp[0]['stockholder_id']}'",
			'from' => 'companies c',
			));	
			
			//on stock last year
			$item_on_stock_last_year_tmp = $this->SelectList(array(
			'fields'    => 'COUNT(id) as count',
			'where'     => "YEAR(modified_at) <= (YEAR('{$steelitem_tmp[0]['modified_at']}')-1) AND steelitem_id = '{$steelitem_tmp[0]['steelitem_id']}'",
			'from' => 'steelitems_history',
			));	
			
			//status now
			$status_now_tmp = $this->SelectList(array(
			'fields'    => 'status_id',
			'where'     => "steelitem_id='{$steelitem_tmp[0]['steelitem_id']}'",
			'from' => 'steelitems_history',
			'order' => 'id DESC LIMIT 1'
			));
			
			//supplier_invoice info
			$supplier_invoice = $this->SelectList(array(
			'fields'    => 'supplier_id, supplier_invoice_id, t2.title AS supplier_title, t3.number AS supplier_number, t3.date AS supplier_date, t3.currency AS currency',
			'where'     => "steelitems.id='{$steelitem_tmp[0]['steelitem_id']}'",
			'from' => 'steelitems',
			'join'  => array(
				array(
					'type'          => 'LEFT',
					'table'         => " companies t2",
					'conditions'    => 't2.id=supplier_id',
					'arguments'     => array(0),
				),
				array(
					'type'          => 'LEFT',
					'table'         => " supplier_invoices t3",
					'conditions'    => 't3.id=supplier_invoice_id',
					'arguments'     => array(0),
				),				
			),			
			));		
			
			//order_info
			$order = $this->SelectList(array(
			'fields'    => '*',
			'where'     => "position_id = '{$steelitem_tmp[0]['steelposition_id']}'",
			'from' => 'order_positions',
			));
			
			switch($status_now_tmp[0]['status_id']) {
				case 1: $status_now='production'; break;
				case 2: $status_now='transfer'; break;
				case 3: $status_now='stock'; break;
				case 4: $status_now='ordered'; break;
				case 5: $status_now='released'; break;
				case 6: $status_now='delivered'; break;
				case 7: $status_now='invoiced'; break;
				default: $status_now='undefined'; break;
			}
			
			//ddt date
			$ddt_date_tmp = $this->SelectList(array(
			'fields'    => 'date',
			'where'     => "number='{$steelitem_tmp[0]['in_ddt_number']}'",
			'from' => 'in_ddt',
			));	

			//ddt date
			$purchase_price_tmp = $this->SelectList(array(
			'fields'    => 'purchase_price',
			'where'     => "steelitem_id='{$steelitem_tmp[0]['steelitem_id']}'",
			'from' => 'steelitems_history',
			'order' => 'id DESC LIMIT 1'
			));				
		
			$count++;
			
			$result_list[$count]['id'] = $steelitem_tmp[0]['steelitem_id'];
			$result_list[$count]['plate_id'] = $steelitem_tmp[0]['guid'];
			$result_list[$count]['ddt_nr'] = $steelitem_tmp[0]['in_ddt_number'];
			//$result_list[$count]['ddt_date'] = $ddt_date_tmp[0]['date'];
			$result_list[$count]['ddt_date'] = (strtotime($ddt_date_tmp[0]['date']))?date('d/m/Y', strtotime($ddt_date_tmp[0]['date'])):null;
			//$result_list[$count]['ddt_date'] = strtotime($steelitem_tmp[0]['in_ddt_date']);
			//$result_list[$count]['ddt_date'] = $steelitem_tmp[0]['in_ddt_date'];
			//?$result_list['invoice_nr'] = $steelitem_tmp[0]['in_ddt_date'];
			$result_list[$count]['steelgrade'] = $steelgrade_tmp[0]['title'];
			$result_list[$count]['thick'] = $steelitem_tmp[0]['thickness'];
			$result_list[$count]['width'] = $steelitem_tmp[0]['width'];
			$result_list[$count]['length'] = $steelitem_tmp[0]['length'];
			$result_list[$count]['weight'] = $steelitem_tmp[0]['unitweight'];
			$result_list[$count]['last_year'] = (($item_on_stock_last_year_tmp[0]['count']>0)?'Y':'N');
			//$result_list[$count]['last_year'] = $item_on_stock_last_year_tmp[0]['count'];
			$result_list[$count]['status_now'] = $status_now;
			$result_list[$count]['purchase_price'] = round($purchase_price_tmp[0]['purchase_price'],2);
			$result_list[$count]['location'] = $stockholder_tmp[0]['location'];
			$result_list[$count]['notes'] = $steelitem_tmp[0]['internal_notes'];
			$result_list[$count]['supplier'] = $supplier_invoice[0]['supplier_title'];
			$result_list[$count]['supplier_invoice'] = $supplier_invoice[0]['supplier_number'];
			$result_list[$count]['supplier_invoice_currency'] = $supplier_invoice[0]['currency'];
			$result_list[$count]['supplier_date'] = (strtotime($supplier_invoice[0]['supplier_date']))?date('d/m/Y', strtotime($supplier_invoice[0]['supplier_date'])):null;
			$result_list[$count]['stock_price'] = $steelplate_tmp[0]['price'];
			$result_list[$count]['order_price'] = $order[0]['price'];
			$result_list[$count]['internal_notes'] = $steelitem_tmp[0]['internal_notes'];
			$result_list[$count]['dimension_unit'] = $steelplate_tmp[0]['dimension_unit'];
			$result_list[$count]['weight_unit'] = $steelplate_tmp[0]['weight_unit'];
			$result_list[$count]['price_unit'] = $steelplate_tmp[0]['currency'];
			if($result_list[$count]['weight_unit'] == 'mt') $result_list[$count]['weight_unit']='Ton';
			//valuetion price
			$valuetion_price=null;
			if($status_now_tmp[0]['status_id']>=4) {
				//($order['price']>$result_list[$count]['purchase_price'])?$valuetion_price=$result_list[$count]['purchase_price']:$valuetion_price=$order['price'];
				($order[0]['price']>$result_list[$count]['purchase_price'])?
					$valuetion_price=$result_list[$count]['purchase_price']:
					$valuetion_price=$order[0]['price'];
			} elseif($status_now_tmp[0]['status_id']<4 && $status_now_tmp[0]['status_id']!==0) {
				($result_list[$count]['stock_price']>$result_list[$count]['purchase_price'])?
					$valuetion_price=$result_list[$count]['purchase_price']:
					$valuetion_price=$result_list[$count]['stock_price'];
			}
			$result_list[$count]['valuetion_price']=$valuetion_price;
			
			if($result_list[$count]['valuetion_price']>0)
			{
				$total_valuation_price += $result_list[$count]['valuetion_price'];
			}
			
			/*if($result_list[$count]['valuetion_price']>0 && ($result_list[$count]['weight']>0))
			{*/
				$total_valuation_price += ($result_list[$count]['valuetion_price']*$result_list[$count]['weight']);
				$total_weight += $result_list[$count]['weight'];
				$total_number++;
			/*}		*/	
			//if($steelitem_tmp[0]['steelitem_id']=='18199') dg($result_list[$count]);
			
		}
		
		$result_list['total_valuation_price']=$total_valuation_price;
		$result_list['total_weight']=$total_weight;
		$result_list['total_number']=$total_number;
		//dg($result_list);
		return $result_list;
	}

    /**
     * Return cout of steelitems without meaningfull data
     * 
     */
    function GetBadDataStat()
    {
        $hash       = 'steelitems-bad-stat';
        $cache_tags = array($hash, 'reports');

        $rowset = $this->_get_cached_data($hash, 'sp_steelitem_get_bad_data_stat', array($this->user_id), $cache_tags);
        return isset($rowset[0]) && isset($rowset[0][0]) ? $rowset[0][0] : array();
    }
    
    /**
     * Get data for In Out Report
     * 
     * @param mixed $owner
     * @param mixed $stockholder_id
     */
    function GetDataForInOutReport($owner = '', $stockholder_id = 0)
    {
        if ($owner == 'mam')
        {
            $owner = MAMIT_OWNER_ID . ',' . MAMUK_OWNER_ID;
        }
        else if ($owner == 'mamit')
        {
            $owner = MAMIT_OWNER_ID;
        }
        else if ($owner == 'mamuk')
        {
            $owner = MAMUK_OWNER_ID;
        }
        else if ($owner == 'pa')
        {
            $owner = PLATESAHEAD_OWNER_ID;
        }
        
        $result['stockholders'] = $this->GetStockholdersList($owner);
        $result['suppliers']    = $this->GetSuppliersList($owner, $stockholder_id);
        $result['countries']    = $this->GetCountriesList($owner, $stockholder_id);
        $result['steelgrades']  = $this->GetSteelgradesList($owner, $stockholder_id);
        
        return $result;
    }

    /**
     * Remove cutted item, update position
     * 
     * @param mixed $item_id
     */
    function RemoveCutted($item_id)
    {
        $this->UpdateSingle($item_id, array('is_deleted' => 1));
    }
    
    /**
     * Возвращает список связанных документов
     * 
     * @param mixed $steelitem_id
     */
    function GetRelatedDocs($steelitem_id)
    {
        $rowset = $this->CallStoredProcedure('sp_steelitem_get_related_docs', array($this->user_id, $steelitem_id));
        $rowset = isset($rowset[0]) ? $rowset[0] : array();
        
        foreach ($rowset as $key => $row)
        {
            if (empty($row['object_id'])) 
            {
                unset($rowset[$key]);
                continue;
            }
            
            $rowset[$key][$row['object_alias'] . '_id'] = $row['object_id'];
        }

        $modelCMR       = new CMR();
        $rowset         = $modelCMR->FillCMRMainInfo($rowset, 'cmr_id');

        $modelDDT       = new DDT();
        $rowset         = $modelDDT->FillDDTMainInfo($rowset, 'ddt_id');

        $modelInDDT     = new InDDT();
        $rowset         = $modelInDDT->FillInDDTMainInfo($rowset, 'inddt_id');

        $modelInvoice   = new Invoice();
        $rowset         = $modelInvoice->FillInvoiceInfo($rowset, 'invoice_id');

        $modelOC        = new OC();
        $rowset         = $modelOC->FillOCInfo($rowset, 'oc_id');
        
        $modelQC        = new QC();
        $rowset         = $modelQC->FillQCInfo($rowset, 'qc_id');

        $modelRA        = new RA();
        $rowset         = $modelRA->FillRAMainInfo($rowset, 'ra_id');

        $modelSInvoice  = new SupplierInvoice();
        $rowset         = $modelSInvoice->FillSupplierInvoiceMainInfo($rowset, 'supplierinvoice_id');

        $modelOrder     = new Order;
        $rowset         = $modelOrder->FillOrderMainInfo($rowset, 'order_id');
        
        $modelUser      = new User();
        $rowset         = $modelUser->FillUserInfo($rowset, 'created_by', 'author');
        
        
        foreach ($rowset as $key => $row)
        {
            if ($row['object_alias'] == 'cmr')
            {
                $rowset[$key]['object']         = $row['cmr'];
                $rowset[$key]['object_title']   = 'CMR';
                
                if (isset($rowset[$key]['object']['company_id'])) $rowset[$key]['company_id'] = $rowset[$key]['object']['company_id'];
                
                unset($rowset[$key]['cmr']);
            }
            else if ($row['object_alias'] == 'ddt')
            {
                $rowset[$key]['object']         = $row['ddt'];
                $rowset[$key]['object_title']   = 'DDT';
                
                if (isset($rowset[$key]['object']['company_id'])) $rowset[$key]['company_id'] = $rowset[$key]['object']['company_id'];
                
                unset($rowset[$key]['ddt']);
            }
            else if ($row['object_alias'] == 'inddt')
            {
                $rowset[$key]['object']         = $row['inddt'];
                $rowset[$key]['object_title']   = 'In DDT';
                
                if (isset($rowset[$key]['object']['company_id'])) $rowset[$key]['company_id'] = $rowset[$key]['object']['company_id'];
                
                unset($rowset[$key]['inddt']);
            }
            else if ($row['object_alias'] == 'invoice')
            {
                $rowset[$key]['object']         = $row['invoice'];
                $rowset[$key]['object_title']   = 'Invoice';
                
                if (isset($rowset[$key]['object']['company_id'])) $rowset[$key]['company_id'] = $rowset[$key]['object']['company_id'];
                
                unset($rowset[$key]['invoice']);
            }
            else if ($row['object_alias'] == 'oc')
            {
                $rowset[$key]['object']         = $row['oc'];
                $rowset[$key]['object_title']   = 'Original Certificate';
                
                if (isset($rowset[$key]['object']['company_id'])) $rowset[$key]['company_id'] = $rowset[$key]['object']['company_id'];
                
                unset($rowset[$key]['oc']);
            }
            else if ($row['object_alias'] == 'qc')
            {
                $rowset[$key]['object']         = $row['qc'];
                $rowset[$key]['object_title']   = 'Quality Certificate';
                
                if (isset($rowset[$key]['object']['company_id'])) $rowset[$key]['company_id'] = $rowset[$key]['object']['company_id'];
                
                unset($rowset[$key]['qc']);
            }
            else if ($row['object_alias'] == 'ra')
            {
                $rowset[$key]['object']         = $row['ra'];
                $rowset[$key]['object_title']   = 'Release Advice';
                
                if (isset($rowset[$key]['object']['company_id'])) $rowset[$key]['company_id'] = $rowset[$key]['object']['company_id'];
                
                unset($rowset[$key]['ra']);
            }
            else if ($row['object_alias'] == 'order')
            {
                $rowset[$key]['object']         = $row['order'];
                $rowset[$key]['object_title']   = 'Order';
                
                if (isset($rowset[$key]['object']['company_id'])) $rowset[$key]['company_id'] = $rowset[$key]['object']['company_id'];
                
                unset($rowset[$key]['order']);
            }
            else if ($row['object_alias'] == 'supplierinvoice')
            {
                $rowset[$key]['object']         = $row['supinvoice'];
                $rowset[$key]['object_title']   = 'Supplier Invoice';
                
                if (isset($rowset[$key]['object']['company_id'])) $rowset[$key]['company_id'] = $rowset[$key]['object']['company_id'];
                
                unset($rowset[$key]['supinvoice']);
            }
        }
        
        $modelCompany   = new Company();
        $rowset         = $modelCompany->FillCompanyInfoShort($rowset);
        
        return $rowset;
    }
    
    
    
    /**
     * Создает алиас для айтема
     * 
     * @param mixed $steelitem_id
     * @param mixed $steelgrade_id
     * @param mixed $thickness
     * @param mixed $width
     * @param mixed $length
     * @param mixed $unitweight
     * @param mixed $price
     * @param mixed $delivery_time
     * @param mixed $notes
     * @param mixed $internal_notes
     * @param mixed $position_id
     * 
     * @version 20130216, zharkov
     */
    function CreateAlias($stock_id, $stockholder_id, $steelitem_id, $steelgrade_id, $thickness, $width, $length, $unitweight, 
                            $price, $delivery_time, $notes, $internal_notes, $position_id)
    {
        $modelStock = new Stock();
        $stock      = $modelStock->GetById($stock_id);
        
        if (empty($stock)) return false;
        
        $stock = $stock['stock'];
        
        if ($stock['dimension_unit'] == 'in')
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
        
        if ($stock['weight_unit'] == 'lb')
        {
            $unitweight_ton = $unitweight / 2200;
        }
        else
        {
            $unitweight_ton = $unitweight;
        }
        
        
        $modelDeliveryTime  = new DeliveryTime();
        $delivery_time_id   = $modelDeliveryTime->GetDeliveryTimeId($delivery_time);
        
        $rowset = $this->CallStoredProcedure('sp_steelitem_create_alias', array($this->user_id, $stock_id, $stockholder_id, $steelitem_id, 
            $steelgrade_id, $thickness, $thickness_mm, $width, $width_mm, $length, $length_mm, $unitweight, $unitweight_ton, 
            $price, $delivery_time_id, $notes, $internal_notes, $position_id));
        
        $rowset = isset($rowset[0]) && isset($rowset[0][0]) ? $rowset[0][0] : array();        
        if (empty($rowset) || isset($rowset['ErrorCode'])) return false;
                    
        $modelSteelPosition = new SteelPosition();
        $modelSteelPosition->UpdateQtty($rowset['position_id']);
    }
    
    /**
     * Возвращает список дочерних айтемов
     * 
     * @param mixed $steelitem_id
     * @version 20130112, zharkov
     */
    function GetChildren($steelitem_id)
    {
        $rowset = $this->CallStoredProcedure('sp_steelitem_get_children', array($this->user_id, $steelitem_id));
        return isset($rowset[0]) ? $this->FillSteelItemInfo($rowset[0]) : array();
    }

    /**
     * Возвращает список стокхолдеров для отчета
     * 
     * @param mixed $stock_id
     * @param string $stock_date
     * @return mixed
     */
    function GetStockholdersForReportAudit($stock_id, $stock_date = null)
    {
        if (empty($stock_date)) $stock_date = date('Y-01-01 00:00:00');

        $rowset = $this->CallStoredProcedure('sp_steelitem_get_stockholders_for_report_audit', array($stock_id, $stock_date));
        
        $modelCompany = new Company();
        return isset($rowset[0]) ? $modelCompany->FillCompanyInfoShort($rowset[0], 'stockholder_id') : array();
    }
    
    /**
     * Возвращает список реальных айтемов для отчета "Аудит"
     * 
     * @version 20121126, zharkov
     */
    function GetListForReportAudit($stockholder_id = 0, $owner_id = 0, $stock_date = null, $calculate_date = null)
    {
        if (empty($stock_date)) $stock_date = date('Y-01-01 00:00:00');

        $rowset = $this->CallStoredProcedure('sp_steelitem_get_list_for_report_audit', array($stockholder_id, $owner_id, $stock_date));
        
        $modelInvoices      = new Invoice();
        $modelSteelGrade    = new SteelGrade();
        
        return isset($rowset[0]) ? $modelSteelGrade->FillSteelGradeInfo($modelInvoices->FillInvoiceInfo($rowset[0])) : array();
    }

    /**
     * Возвращает список реальных айтемов для формирования отчета
     * 
     * @version 20121116, zharkov
     */
    function GetListForReportCurrent()
    {
        $rowset = $this->CallStoredProcedure('sp_steelitem_get_list_for_report_current', array());        
        return isset($rowset[0]) ? $this->FillSteelItemInfo($rowset[0]) : array();
    }
    
    /**
     * Отрезает кусок от айтема
     * 
     * @param mixed $item_id
     * @param mixed $guid
     * @param mixed $width
     * @param mixed $length
     * @param mixed $unitweight
     * @param mixed $notes
     * @param mixed $position_id
     * @param mixed $location_id
     * 
     * @version 20120921, zharkov
     */
    function CutItem($id, $item_id, $guid, $width, $length, $unitweight, $notes, $position_id, $location_id)
    {
        $item   = $this->GetById($item_id);
        $item   = $item['steelitem'];
        
        if (empty($item['is_available'])) return null;

        
        $width_mm       = ($item['dimension_unit'] == 'in' ? $width * 25.4 : $width);
        $length_mm      = ($item['dimension_unit'] == 'in' ? $length * 25.4 : $length);
        $unitweight_ton = ($item['weight_unit'] == 'lb' ? $unitweight / 2200 : $unitweight);
        $alias          = empty($guid) ? '' : $this->_get_title_src($guid);

        
        $result = $this->CallStoredProcedure('sp_steelitem_cut', array(
            $this->user_id, 
            $id, 
            $item_id, 
            $location_id, 
            $position_id,
            $width, 
            $width_mm, 
            $length, 
            $length_mm, 
            $unitweight, 
            $unitweight_ton, 
            $guid,
            $alias,
            $notes
        ));
        
        $result = isset($result) && isset($result[0]) && isset($result[0][0]) ? $result[0][0] : null;        
        if (empty($result) || array_key_exists('ErrorCode', $result)) return null;        
        
        Cache::ClearTag('steelitems');
        Cache::ClearTag('reports');
        Cache::ClearTag('steelitems-filter');
        
        // обновляет количество позиции
        $steelpositions = new SteelPosition();
        $steelpositions->UpdateQtty($result['position_id']);
        
        return $result;
    }
    
    /**
     * Преобразовывает виртуальный айтем в реальный
     * 
     * @param mixed $id
     * @return resource
     * 
     * @version 20120820, zharkov
     */
    function ConvertToReal($id)
    {
        $item   = $this->GetById($id);
        $item   = $item['steelitem'];
        
        if (empty($item['parent_id'])) return null;
        
        $result = $this->CallStoredProcedure('sp_steelitem_convert_to_real', array($this->user_id, $id));
        $result = isset($result) && isset($result[0]) ? $result[0] : array();
        
        if (isset($result[0]) && isset($result[0]['ErrorCode'])) return null;
        
        $steelpositions = new SteelPosition();
        foreach ($result as $row)
        {
            $steelpositions->UpdateQtty($row['steelposition_id']);
            
            Cache::ClearTag('steelitemproperties-' . $row['steelitem_id']);
            Cache::ClearTag('steelitem-' . $row['steelitem_id']);
            
            
            
            Cache::ClearTag('order-' . $row['order_id']);
            Cache::ClearTag('orderquick-' . $row['order_id']);
            Cache::ClearTag('orderpositions-' . $row['order_id']);            
        }
        
        Cache::ClearTag('reports');
        Cache::ClearTag('steelitems');
        Cache::ClearTag('steelitems-filter');
        
        return $result;
    }
    
    /**
     * Вовращает список кофликтных айтемов
     * 
     * @param mixed $item_id
     * 
     * @version 20120726, zharkov
     */
    function GetConflicted($item_id)
    {
        $hash       = 'steelitem-' . $item_id . '-conflicted';
        $cache_tags = array($hash, 'steelitem-' . $item_id, 'steelitems');

        $rowset = $this->_get_cached_data($hash, 'sp_steelitem_get_conflicted', array($item_id), $cache_tags);
        return isset($rowset[0]) ? $this->FillSteelItemInfo($rowset[0]) : array();
    }
    
    /**
     * Возвращает ревизию айтема
     * 
     * @param mixed $item_id
     * @param mixed $item_history_id
     * @param mixed $item_properties_history_id
     */
    function GetHistoryRevision($item_id, $item_history_id, $item_properties_history_id)
    {
        $hash       = 'steelitem-' . md5($item_id . '-revision-' . $item_history_id . '-properties-' . $item_properties_history_id); 
        $cache_tags = array($hash, 'steelitem-' . $item_id, 'steelitems');
                    
        $rowset     = $this->_get_cached_data($hash, 'sp_steelitem_get_history_revision', array($item_id, $item_history_id, $item_properties_history_id), $cache_tags);
        $history    = isset($rowset[0]) ? $rowset[0] : array();
        $properties = isset($rowset[1]) ? $rowset[1] : array();

        
        $steelgrades    = new SteelGrade();
        $history        = $steelgrades->FillSteelGradeInfo($history);
        
        $locations  = new Location();
        $history    = $locations->FillLocationInfo($history);
        
        $bizes      = new Biz();
        $history    = $bizes->FillBizInfo($history);
        
        $deliverytimes  = new DeliveryTime();
        $history        = $deliverytimes->FillDeliveryTimeInfo($history);
        
        $users          = new User();
        $history        = $users->FillUserInfo($history, 'record_by');        
        
        $companies  = new Company();
        $history    = $companies->FillCompanyInfo($history, 'supplier_id', 'supplier');
        $history    = $companies->FillCompanyInfo($history, 'owner_id', 'owner');
        $history    = $companies->FillCompanyInfo($history, 'stockholder_id', 'stockholder');

        return array('history' => $history, 'properties' => $properties);
    }

    /**
     * Возвращает список айтемов
     * 
     * @param mixed $stock_id
     * @param mixed $location_ids
     * @param mixed $deliverytime_ids
     * @param mixed $types
     * @param mixed $steelgrade_id
     * @param mixed $thickness
     * @param mixed $width
     * @param mixed $length
     * @param mixed $weight
     * @param mixed $keyword
     * @param mixed $plate_id
     * @param mixed $dimension_unit
     * @param mixed $weight_unit
     * @param mixed $order_id
     */
    function GetList($stock_id, $location_ids, $deliverytime_ids, $is_real, $is_virtual, $is_twin, $is_cut, $steelgrade_id, 
                        $thickness, $width, $length, $weight, $keyword, $plate_id, $available, $dimension_unit, $weight_unit, $order_id = 0)
    {

        $hash       = 'steelitems-' . md5('stock-' . $stock_id . '-locations-' . $location_ids . '-deliverytimes-' . $deliverytime_ids 
                    . '-is_real-' . $is_real . '-is_virtual-' . $is_virtual . '-is_twin-' . $is_twin . '-is_cut-' . $is_cut
                    . '-steelgrade-' . $steelgrade_id . '-thickness-' . $thickness . '-width-' . $width . '-length-' . $length 
                    . '-weight-' . $weight . '-keyword-' . $keyword . '-plateid-' . $plate_id . '-available-' . $available . '-orderid-' . $order_id
                    . '-rev-' . $this->revision);
        $cache_tags = array($hash, 'steelitems-stock-' . $stock_id, 'steelitems');
                    
        $thickness  = $this->_get_interval($thickness);
        $width      = $this->_get_interval($width);
        $length     = $this->_get_interval($length);
        $weight     = $this->_get_interval($weight);

        if ($dimension_unit == 'in')
        {
            $thickness['from']  = $thickness['from'] * 25.4;
            $thickness['to']    = $thickness['to'] * 25.4;
            $width['from']      = $width['from'] * 25.4;
            $width['to']        = $width['to'] * 25.4;
            $length['from']     = $length['from'] * 25.4;
            $length['to']       = $length['to'] * 25.4;
            
        }
        
        if ($weight_unit == 'lb')
        {
            $weight['from'] = $weight['from'] / 2200;
            $weight['to']   = $weight['to'] / 2200;
        }
        
        
        $rowset = $this->_get_cached_data($hash, 'sp_steelitem_get_list', array($this->user_id, $stock_id, $location_ids, $deliverytime_ids, 
                                            $is_real, $is_virtual, $is_twin, $is_cut, $steelgrade_id, 
                                            $thickness['from'], $thickness['to'], $width['from'], $width['to'], 
                                            $length['from'], $length['to'], $weight['from'], $weight['to'], 
                                            $keyword, $plate_id, $available, $order_id, $this->revision), $cache_tags);

        $rowset = isset($rowset[0]) ? $this->FillSteelItemInfo($rowset[0]) : array();

        return $rowset;        
    }
    
    /**
     * Сохраняет item
     * 
     * @param mixed $id
     * @param mixed $guid
     * @param mixed $product_id
     * @param mixed $biz_id
     * @param mixed $location_id
     * @param mixed $dimension_unit
     * @param mixed $weight_unit
     * @param mixed $price_unit
     * @param mixed $currency
     * @param mixed $steelgrade_id
     * @param mixed $thickness
     * @param mixed $thickness_measured
     * @param mixed $width
     * @param mixed $width_measured
     * @param mixed $width_max
     * @param mixed $length
     * @param mixed $length_measured
     * @param mixed $length_max
     * @param mixed $unitweight
     * @param mixed $price
     * @param mixed $value
     * @param mixed $delivery_time
     * @param mixed $notes
     * @param mixed $internal_notes
     * @param mixed $supplier_id
     * @param mixed $supplier_invoice_no
     * @param mixed $supplier_invoice_date
     * @param mixed $purchase_price
     * @param mixed $purchase_value
     * @param mixed $in_ddt_number
     * @param mixed $in_ddt_date
     * @param mixed $out_ddt_number
     * @param mixed $out_ddt_date     * 
     * @param mixed $owner_id
     * @param mixed $status
     * @param mixed $is_virtual
     * @param mixed $mill
     * @param mixed $system
     * @param mixed $unitweight_measured
     * @param mixed $unitweight_weighed
     * @param mixed $current_cost
     * @param mixed $pl
     * @param mixed $load_ready
     * @param mixed $stockholder_id
     * @param mixed $purchase_currency
     * @return resource
     */
    function Save($id, $position_id, $guid, $product_id, $biz_id, $stockholder_id, $dimension_unit, $weight_unit, $price_unit, $currency, $steelgrade_id, 
                    $thickness, $thickness_measured, $width, $width_measured, $width_max, $length, $length_measured, $length_max,
                    $unitweight, $price, $value, $delivery_time, $notes, $internal_notes, 
                    $supplier_id = 0, $supplier_invoice_no = '', $supplier_invoice_date = null, $purchase_price = 0, 
                    $purchase_value = 0, $in_ddt_number = '', $in_ddt_date = null, $out_ddt_number = '', $out_ddt_date = null, $owner_id = 0, $status_id = 0, 
                    $is_virtual = 1, $mill = '', $system = '', $unitweight_measured = 0, $unitweight_weighed = 0, $current_cost = 0, 
                    $pl = 0, $load_ready = '', $purchase_currency = '', $in_ddt_company_id = 0, $ddt_company_id = 0, 
                    $nominal_thickness_mm = 0, $nominal_width_mm = 0, $nominal_length_mm = 0, 
                    $is_ce_mark = 0, $is_mec_prop_not_required = 0)
    {

        $supplier_invoice_date  = empty($supplier_invoice_date) ? null : $supplier_invoice_date;
        $ddt_date               = empty($ddt_date) ? null : $ddt_date;
        $alias                  = empty($guid) ? '' : $this->_get_title_src($guid);
        
        $thickness_mm   = ($dimension_unit == 'in' ? $thickness * 25.4 : $thickness);
        $width_mm       = ($dimension_unit == 'in' ? $width * 25.4 : $width);
        $length_mm      = ($dimension_unit == 'in' ? $length * 25.4 : $length);

        if ($weight_unit == 'lb')
        {
            $unitweight_ton = $unitweight / 2200;
        }
        else
        {
            $unitweight_ton = $unitweight;
        }
        
        $deliverytimes      = new DeliveryTime();
        $deliverytime_id    = $deliverytimes->GetDeliveryTimeId($delivery_time);

        $result = $this->CallStoredProcedure('sp_steelitem_save', array($this->user_id, $id, $position_id, $guid, $alias, 
                    $product_id, $biz_id, $stockholder_id, $dimension_unit, $weight_unit, $price_unit, $currency, $steelgrade_id, 
                    $thickness, $thickness_mm, $thickness_measured, $width, $width_mm, $width_measured, $width_max, 
                    $length, $length_mm, $length_measured, $length_max, $unitweight, $unitweight_ton,
                    $price, $value, $supplier_id, $supplier_invoice_no, $supplier_invoice_date, $purchase_price, $purchase_value, 
                    $in_ddt_number, $in_ddt_date, $out_ddt_number, $out_ddt_date, $deliverytime_id, $notes, $internal_notes, $owner_id, 
                    $status_id, $is_virtual, $mill, $system, $unitweight_measured, $unitweight_weighed, $current_cost, $pl, $load_ready,
                    $purchase_currency, $in_ddt_company_id, $ddt_company_id, 
                    $nominal_thickness_mm, $nominal_width_mm, $nominal_length_mm, 
                    $is_ce_mark, $is_mec_prop_not_required));
        $result = isset($result) && isset($result[0]) && isset($result[0][0]) ? $result[0][0] : null;

        if (empty($result) || isset($result['ErrorCode'])) return $result;
        

        Cache::ClearTag('steelitem-' . $result['id']);
        Cache::ClearTag('steelitems-filter');
        Cache::ClearTag('steelitems');
        Cache::ClearTag('reports');        
        
        if (empty($id))
        {
            Cache::ClearTag('steelpositionquick-' . $position_id);
        }

        // если создается новый айтем, то для него добавляются свойства
        if (empty($id)) $this->SaveProperties($result['id']);
        

        return $result;
    }
    
    /**
     * Сохраняет параметры айтема
     * 
     * @param mixed $id
     * @param mixed $item_id
     * @param mixed $heat_lot
     * @param mixed $c
     * @param mixed $si
     * @param mixed $mn
     * @param mixed $p
     * @param mixed $s
     * @param mixed $cr
     * @param mixed $ni
     * @param mixed $cu
     * @param mixed $al
     * @param mixed $mo
     * @param mixed $nb
     * @param mixed $v
     * @param mixed $n
     * @param mixed $ti
     * @param mixed $sn
     * @param mixed $b
     * @param mixed $ceq
     * @param mixed $tensile_sample_direction
     * @param mixed $tensile_strength
     * @param mixed $yeild_point
     * @param mixed $elongation
     * @param mixed $reduction_of_area
     * @param mixed $test_temp
     * @param mixed $impact_strength
     * @param mixed $hardness
     * @param mixed $ust
     * @param mixed $sample_direction
     * @param mixed $stress_relieving_temp
     * @param mixed $heating_rate_per_hour
     * @param mixed $holding_time
     * @param mixed $cooling_down_rate
     * @return resource
     */
    function SaveProperties($item_id, $heat_lot = '', $c = 0, $si = 0, $mn = 0, $p = 0, $s = 0, $cr = 0, $ni = 0, $cu = 0, $al = 0, 
                            $mo = 0, $nb = 0, $v = 0, $n = 0, $ti = 0, $sn = 0, $b = 0, $ceq = 0, $tensile_sample_direction = '', $tensile_strength = 0, $yeild_point = 0, 
                            $elongation = 0, $reduction_of_area = 0, $test_temp = 0, $impact_strength = '', $hardness = 0, 
                            $ust = '', $sample_direction = '', $stress_relieving_temp = 0, $heating_rate_per_hour = 0, 
                            $holding_time = 0, $cooling_down_rate = 0, $condition = '', $normalizing_temp = '')
    {
        
        $result = $this->CallStoredProcedure('sp_steelitem_properties_save', array($this->user_id, $item_id, $heat_lot, $c, $si, 
                            $mn, $p, $s, $cr, $ni, $cu, $al, $mo, $nb, $v, $n, $ti, $sn, $b, $ceq, $tensile_sample_direction,
                            $tensile_strength, $yeild_point, $elongation, $reduction_of_area, $test_temp, $impact_strength, $hardness, 
                            $ust, $sample_direction, $stress_relieving_temp, $heating_rate_per_hour, $holding_time, $cooling_down_rate, 
                            $condition, $normalizing_temp));
        $result = isset($result) && isset($result[0]) && isset($result[0][0]) ? $result[0][0] : null;
        
        if (empty($result) || array_key_exists('ErrorCode', $result)) return null;        
        Cache::ClearTag('steelitemproperties-' . $item_id);
        
        return $result;
    }
    
    /**
     * Fills main steelitem info
     * 
     * @param mixed $rowset
     */
    function FillSteelItemMainInfo($rowset)
    {
        $rowset = $this->_fill_entity_info($rowset, 'steelitem_id', 'steelitem', 'steelitem', 'sp_steelitem_get_list_by_ids', array('steelitems' => '', 'steelitem' => 'id'), array($this->revision));

        foreach ($rowset as $key => $row)
        {
            if (!isset($row['steelitem'])) continue;

            $row = $row['steelitem'];            
            
            // установка флага использования айтема
            if (self::IsLocked($row['id']))
            {
                $rowset[$key]['steelitem']['inuse']     = true;
                $rowset[$key]['steelitem']['inuse_by']  = self::LockedBy($row['id']);
            }
            else
            {
                $rowset[$key]['steelitem']['inuse'] = false;
            }
            
            if (isset($row['steelgrade_id'])) $rowset[$key]['item_steelgrade_id'] = $row['steelgrade_id'];
            if (isset($row['stockholder_id'])) $rowset[$key]['item_stockholder_id'] = $row['stockholder_id'];
            if (isset($row['location_id'])) $rowset[$key]['item_location_id'] = $row['location_id'];
            if (isset($row['supplier_id'])) $rowset[$key]['item_supplier_id'] = $row['supplier_id'];
            if (isset($row['deliverytime_id'])) $rowset[$key]['item_deliverytime_id'] = $row['deliverytime_id'];
            if (isset($row['biz_id'])) $rowset[$key]['item_biz_id'] = $row['biz_id'];
            if (isset($row['owner_id'])) $rowset[$key]['item_owner_id'] = $row['owner_id'];
            if (isset($row['order_id'])) $rowset[$key]['item_order_id'] = $row['order_id'];
            if (isset($row['supplier_invoice_id'])) $rowset[$key]['item_supplier_invoice_id'] = $row['supplier_invoice_id'];
            if (isset($row['in_ddt_id'])) $rowset[$key]['item_in_ddt_id'] = $row['in_ddt_id'];
            
            $rowset[$key]['steelitem']['days_on_stock'] = (!isset($row['ddt_days_on_stock']) ? $row['days_on_stock'] : $row['ddt_days_on_stock']);
            $rowset[$key]['steelitem']['doc_no']        = (empty($row['guid']) ? 'Item # ' . $row['id'] : $row['guid']);
            
            $rowset[$key]['item_author_id']             = $row['created_by'];
            $rowset[$key]['item_modifier_id']           = $row['modified_by'];
                        
            if ($row['parent_id'] > 0)
            {
                $rowset[$key]['steelitem']['doc_no'] = 'alias ' . (empty($row['parent_guid']) ? '# ' . $row['parent_id'] : $row['parent_guid']);
            }
            
            if (!empty($row['guid']) || $row['in_ddt_id'] > 0 || $row['supplier_invoice_id'] > 0 || $row['status_id'] >= ITEM_STATUS_RELEASED)
            {
                $rowset[$key]['steelitem']['is_eternal'] = true;
            }
            else
            {
                $rowset[$key]['steelitem']['is_eternal'] = false;
            }            
        }
        
        $steelgrades    = new SteelGrade();
        $rowset         = $steelgrades->FillSteelGradeInfo($rowset, 'item_steelgrade_id', 'item_steelgrade');
        
        $locations      = new Location();
        $rowset         = $locations->FillLocationInfo($rowset, 'item_location_id', 'item_location');
        
        $users          = new User();
        $rowset         = $users->FillUserInfo($rowset, 'item_author_id',   'item_author');
        $rowset         = $users->FillUserInfo($rowset, 'item_modifier_id', 'item_modifier');        

        foreach ($rowset as $key => $row)
        {
            if (!isset($row['steelitem'])) continue;
            
            if (isset($row['item_steelgrade'])) 
            {
                $rowset[$key]['steelitem']['steelgrade'] = $row['item_steelgrade'];
                unset($rowset[$key]['item_steelgrade']);
            }
            
            if (isset($row['item_location'])) 
            {
                $rowset[$key]['steelitem']['location'] = $row['item_location'];
                unset($rowset[$key]['item_location']);
            }
            
            if ($rowset[$key]['steelitem']['status_id'] == ITEM_STATUS_DELIVERED) $rowset[$key]['steelitem']['status_title'] = 'Delivered';
            else if ($rowset[$key]['steelitem']['status_id'] == ITEM_STATUS_INVOICED) $rowset[$key]['steelitem']['status_title'] = 'Invoiced';
            else if ($rowset[$key]['steelitem']['status_id'] == ITEM_STATUS_ORDERED) $rowset[$key]['steelitem']['status_title'] = 'Ordered';
            else if ($rowset[$key]['steelitem']['status_id'] == ITEM_STATUS_PRODUCTION) $rowset[$key]['steelitem']['status_title'] = 'In Production';
            else if ($rowset[$key]['steelitem']['status_id'] == ITEM_STATUS_RELEASED) $rowset[$key]['steelitem']['status_title'] = 'Released';
            else if ($rowset[$key]['steelitem']['status_id'] == ITEM_STATUS_STOCK) $rowset[$key]['steelitem']['status_title'] = 'On Stock';
            else if ($rowset[$key]['steelitem']['status_id'] == ITEM_STATUS_TRANSFER) $rowset[$key]['steelitem']['status_title'] = 'Transfer To Stock';
            else $rowset[$key]['steelitem']['status_title'] = '';
            
            if (isset($row['item_author']))
            {
                $rowset[$key]['steelitem']['author'] = $row['item_author'];
                unset($rowset[$key]['item_author']);
            }                
            
            if (isset($row['item_modifier']))
            {
                $rowset[$key]['steelitem']['modifier'] = $row['item_modifier'];
                unset($rowset[$key]['item_modifier']);
            }            
            
            unset($rowset[$key]['item_author_id']);
            unset($rowset[$key]['item_modifier_id']);
            unset($rowset[$key]['item_steelgrade_id']);
            unset($rowset[$key]['item_location_id']);            
        }        
        
        return $rowset;
    }
  
 	/**
     * Заполняет значения SteelItem
     * 
     * @param mixed $rowset
     * @return array
     */
    function FillSteelItemInfo($rowset)
    {
        $rowset = $this->FillSteelItemMainInfo($rowset);

        $companies          = new Company();
        $rowset             = $companies->FillCompanyInfoShort($rowset, 'item_stockholder_id', 'item_stockholder');
        $rowset             = $companies->FillCompanyInfoShort($rowset, 'item_owner_id', 'item_owner');

        $deliverytimes      = new DeliveryTime();
        $rowset             = $deliverytimes->FillDeliveryTimeInfo($rowset, 'item_deliverytime_id', 'item_deliverytime');
        
        $bizs               = new Biz();
        $rowset             = $bizs->FillMainBizInfo($rowset, 'item_biz_id', 'item_biz');
        
        $modelOrder         = new Order();
        $rowset             = $modelOrder->FillOrderInfo($rowset, 'item_order_id', 'item_order');

        $modelInDDT         = new InDDT();
        $rowset             = $modelInDDT->FillInDDTMainInfo($rowset, 'item_in_ddt_id', 'item_in_ddt');

        $modelSupInvoice    = new SupplierInvoice();
        $rowset             = $modelSupInvoice->FillSupplierInvoiceMainInfo($rowset, 'item_supplier_invoice_id', 'item_supplier_invoice');
        
        
        foreach ($rowset as $key => $row)
        {

            if (!isset($row['steelitem'])) continue;

            if (isset($row['item_stockholder'])) 
            {
                $rowset[$key]['steelitem']['stockholder'] = $row['item_stockholder'];
                unset($rowset[$key]['item_stockholder']);
            }
            
            if (isset($row['item_owner'])) 
            {
                $rowset[$key]['steelitem']['owner'] = $row['item_owner'];
                unset($rowset[$key]['item_owner']);
            }
            
            if (isset($row['item_deliverytime'])) 
            {
                $rowset[$key]['steelitem']['deliverytime'] = $row['item_deliverytime'];
                unset($rowset[$key]['item_deliverytime']);
            }
            
            if (isset($row['item_biz'])) 
            {
                $rowset[$key]['steelitem']['biz'] = $row['item_biz'];
                unset($rowset[$key]['item_biz']);
            }
            
            if (isset($row['item_order'])) 
            {
                $rowset[$key]['steelitem']['order'] = $row['item_order'];
                unset($rowset[$key]['item_order']);
            }
            
            if (isset($row['item_supplier_invoice'])) 
            {
                $rowset[$key]['steelitem']['supplier_invoice'] = $row['item_supplier_invoice'];
                unset($rowset[$key]['item_supplier_invoice']);
            }

            if (isset($row['item_in_ddt'])) 
            {
                $rowset[$key]['steelitem']['in_ddt'] = $row['item_in_ddt'];
                unset($rowset[$key]['item_in_ddt']);
            }            

            if (isset($row['item_ddt'])) 
            {
                $rowset[$key]['steelitem']['ddt'] = $row['item_ddt'];
                unset($rowset[$key]['item_ddt']);
            }
            
            if ($row['steelitem']['parent_id'] > 0)
            {
                $parent = $this->GetById($row['steelitem']['parent_id']);
                $rowset[$key]['steelitem']['parent'] = $parent['steelitem'];
            }            
            
            unset($rowset[$key]['item_supplier_id']);
            unset($rowset[$key]['item_stockholder_id']);
            unset($rowset[$key]['item_deliverytime_id']);
            unset($rowset[$key]['item_biz_id']);
            unset($rowset[$key]['item_owner_id']);
            unset($rowset[$key]['item_order_id']);
            unset($rowset[$key]['item_supplier_invoice_id']);
            unset($rowset[$key]['item_in_ddt_id']);
            unset($rowset[$key]['item_ddt_id']);
        }

        $rowset = $this->FillSteelItemPropertyInfo($rowset);
        
        $attachments    = new Attachment();
        $rowset         = $attachments->FillObjectAttachments($rowset, 'item', 'steelitem_id');
        
        return $rowset;
    }
    
    /**
     * Добавляет свойства листа
     *     
     * @param mixed $rowset
     * @return array
     */
    function FillSteelItemPropertyInfo($rowset)
    {
        $rowset = $this->_fill_entity_info($rowset, 'steelitem_id', 'steelitemproperties', 'steelitemproperties', 'sp_steelitem_property_get_list_by_ids', array('steelitems' => '', 'steelitem' => 'id'), array($this->revision));
        
        foreach ($rowset as $key => $row)
        {
            if (isset($row['steelitem']) && isset($row['steelitemproperties'])) 
            {
                $rowset[$key]['steelitem']['properties'] = $row['steelitemproperties'];
            }
            
            unset($rowset[$key]['steelitemproperties']);
        }
        
        return $rowset;
    }
    
    /**
     * Возвращает айтем по id
     * 
     * @param mixed $id
     * @return array
     */
    function GetById($id)
    {
        $rowset = $this->FillSteelItemInfo(array(array('steelitem_id' => $id)));
        return isset($rowset) && isset($rowset[0]) && isset($rowset[0]['steelitem']) ? $rowset[0] : null;
    }    

    /**
     * Возвращает список айтемов по id
     * 
     * @param mixed $ids
     * @return array
     */
    function GetByIds($ids)
    {
        return $this->FillSteelItemInfo($ids);
    }
    
    /**
     * Возвращает айтем по guid
     * 
     * @param mixed $guid
     */
    function CheckGuid($id, $guid)
    {
        // 20130820, zharkov: remove cache
        // $rowset = $this->_get_cached_data('steelitem-id-' . $id . '-guid-' . $guid, 'sp_steelitem_check_guid', array($id, $guid), array('steelitem' => 'id', ''));
        $rowset = $this->CallStoredProcedure('sp_steelitem_check_guid', array($id, $guid));

        return isset($rowset) && isset($rowset[0]) && isset($rowset[0][0]) ? true : false;
    }
    
    /**
    * Удаляет Item
    * 
    * @param mixed $id
    * @param mixed $update_position_qtty
    * @return resource
    */
    function Remove($id)
    {
        $item   = $this->GetById($id);
        $item   = $item['steelitem'];
        
        // 20130227, zharkov: добавил проверку на неудаляемость айтема
        if (empty($item['is_available']) || $item['is_eternal']) return null;
        
        $result = $this->CallStoredProcedure('sp_steelitem_remove', array($this->user_id, $id));
        $result = isset($result) && isset($result[0]) ? $result[0] : array();
        
        if (isset($result[0]) && isset($result[0]['ErrorCode'])) return null;
        
        Cache::ClearTag('steelitem-' . $id);
        
        $steelpositions = new SteelPosition();
        foreach ($result as $row)
        {
            $steelpositions->UpdateQtty($row['steelposition_id']);
            
            Cache::ClearTag('steelitemproperties-' . $row['steelitem_id']);
            Cache::ClearTag('steelitem-' . $row['steelitem_id']);
            
            Cache::ClearTag('order-' . $row['order_id']);
            Cache::ClearTag('orderquick-' . $row['order_id']);
            Cache::ClearTag('orderpositions-' . $row['order_id']);            
        }
        
        Cache::ClearTag('steelitems-filter');
        Cache::ClearTag('steelitems');
        Cache::ClearTag('reports');
        
        return $result;
    }
    
    /**
     * Переносит айтем из позиции в позицию
     * 
     * @param mixed $item_id
     * @param mixed $dest_position_id
     * @param mixed $source_position_id
     */
    function Move($item_id, $dest_stockholder_id, $dest_position_id, $source_position_id = 0)
    {
        $result = $this->CallStoredProcedure('sp_steelitem_move', array($this->user_id, $item_id, $dest_stockholder_id, $dest_position_id, $source_position_id));
        $result = isset($result) && isset($result[0]) ? $result[0] : array();
        
        if (!isset($result[0]) || empty($result[0]) || isset($result[0]['ErrorCode'])) return null;
                
        Cache::ClearTag('steelitem-' . $item_id);
        Cache::ClearTag('reports');
        Cache::ClearTag('steelitems');
        Cache::ClearTag('steelitems-filter');
        
        $steelpositions = new SteelPosition();
        // update qtty for previous positions
        foreach ($result as $row) $steelpositions->UpdateQtty($row['steelposition_id']);
        // update qtty for new position
        $steelpositions->UpdateQtty($dest_position_id);
        
        return $item_id;
    }
    
    /**
     * Создает близнеца для позиции
     * 
     * @param mixed $item_id
     * @param mixed $location_id
     * @param mixed $position_id
     */
    function Twin($item_id, $stockholder_id, $position_id)
    {
        $item   = $this->GetById($item_id);
        $item   = $item['steelitem'];
        
        if (empty($item['is_available'])) return null;
        
        
        $result = $this->CallStoredProcedure('sp_steelitem_twin', array($this->user_id, $item_id, $stockholder_id, $position_id));
        $result = isset($result) && isset($result[0]) && isset($result[0][0]) ? $result[0][0] : null;
        
        if (empty($result) || array_key_exists('ErrorCode', $result)) return null;        
        
        Cache::ClearTag('reports');
        Cache::ClearTag('steelitems');
        Cache::ClearTag('steelitems-filter');
        
        // update qtty for new position
        $steelpositions = new SteelPosition();
        $steelpositions->UpdateQtty($position_id);
        
        return $item_id;
    }
    
    /**
	 * The history of changes item
	 * 
	 * @param type $item_id
	 * @return string
	 * 
	 * @version 20130528, sasha: add action_status
	 */
    function GetHistory($item_id)
    {
        $rowset = $this->CallStoredProcedure('sp_steelitem_get_history', array($this->user_id, $item_id));
        $rowset = $rowset[0];

        $steelgrades = new SteelGrade();
        $rowset = $steelgrades->FillSteelGradeInfo($rowset);
        
        $locations = new Location();
        $rowset = $locations->FillLocationInfo($rowset);
        
        $bizes = new Biz();
        $rowset = $bizes->FillBizInfo($rowset);
        
        $deliverytimes = new DeliveryTime();
        $rowset = $deliverytimes->FillDeliveryTimeInfo($rowset);

        $users = new User();
        $rowset = $users->FillUserInfo($rowset, 'history_record_by');
		
		$modelOrder         = new Order();
        $rowset             = $modelOrder->FillOrderInfo($rowset);
		
        $companies = new Company();
        $rowset = $companies->FillCompanyInfo($rowset, 'supplier_id', 'supplier');
        $rowset = $companies->FillCompanyInfo($rowset, 'owner_id', 'owner');
        $rowset = $companies->FillCompanyInfo($rowset, 'stockholder_id', 'stockholder');
        
        foreach ($rowset as $key => $row)
        {
            $rowset[$key]['currency_sign'] = $row['currency'] == 'usd' ? '$' : ($row['currency'] == 'eur' ? '&euro;' : '');
        
			//@version 20130527, sasha
			if (!empty($row['tech_action']))
			{
				switch ($row['tech_action'])
				{
					case 'add':
						$rowset[$key]['action_status'] = 'Created';
						break;
					case 'delete':
						$rowset[$key]['action_status'] = 'Removed';
						break;
					case 'toorder':
						$rowset[$key]['action_status'] = 'Put to order';
						break;
					case 'fromorder':
						$rowset[$key]['action_status'] = 'Remove from order';
						break;
					case 'move':
						$rowset[$key]['action_status'] = 'Move to position';
						break;	
				}
				
				if ($row['tech_action'] == 'edit')
				{
					if (!isset($rowset[$key + 1]))
					{	
						$rowset[$key]['edit'] = 'edit';
						break;   
					}
					
					//dimensions
					if 
					(
						isset($row['thickness_measured']) && $row['thickness_measured']		!= $rowset[$key + 1]['thickness_measured'] ||
						isset($row['width_measured']) && $row['width_measured']				!= $rowset[$key + 1]['width_measured'] ||
						isset($row['width_max']) && $row['width_max']						!= $rowset[$key + 1]['width_max'] ||
						isset($row['length_measured']) && $row['length_measured']			!= $rowset[$key + 1]['length_measured'] ||
						isset($row['length_max']) && $row['length_max']						!= $rowset[$key + 1]['length_max'] ||                
						isset($row['unitweight_measured']) && $row['unitweight_measured']	!= $rowset[$key + 1]['unitweight_measured'] ||
						isset($row['unitweight_weighed']) && $row['unitweight_weighed']		!= $rowset[$key + 1]['unitweight_weighed'] ||
                        isset($row['thickness']) && $row['thickness']                       != $rowset[$key + 1]['thickness'] ||
                        isset($row['width']) && $row['width']                               != $rowset[$key + 1]['width'] ||
                        isset($row['length']) && $row['length']                             != $rowset[$key + 1]['length'] ||
                        isset($row['unitweight']) && $row['unitweight']                     != $rowset[$key + 1]['unitweight']                        
					)   
					{
						$rowset[$key]['edit_status'][] = 'Dimensions was changed';
					}
					
					//status
					if 
					(
						isset($row['supplier_id']) && $row['supplier_id']			!= $rowset[$key + 1]['supplier_id'] ||
						isset($row['supplier_invoice_no'])	&& $row['supplier_invoice_no']	!= $rowset[$key + 1]['supplier_invoice_no'] ||
						isset($row['supplier_invoice_date']) && $row['supplier_invoice_date']	!= $rowset[$key + 1]['supplier_invoice_date'] ||
						isset($row['purchase_price']) && $row['purchase_price']			!= $rowset[$key + 1]['purchase_price'] ||
						isset($row['purchase_value']) && $row['purchase_value']			!= $rowset[$key + 1]['purchase_value'] ||
						isset($row['in_ddt_number']) && $row['in_ddt_number']			!= $rowset[$key + 1]['in_ddt_number'] ||
						isset($row['in_ddt_date']) && $row['in_ddt_date']			!= $rowset[$key + 1]['in_ddt_date'] ||
						isset($row['ddt_number']) && $row['ddt_number']				!= $rowset[$key + 1]['ddt_number'] ||
						isset($row['ddt_date']) && $row['ddt_date']				!= $rowset[$key + 1]['ddt_date'] ||
						isset($row['notes']) && $row['notes']					!= $rowset[$key + 1]['notes'] ||
						isset($row['internal_notes']) && $row['internal_notes']			!= $rowset[$key + 1]['internal_notes'] ||
						isset($row['owner_id']) && $row['owner_id']				!= $rowset[$key + 1]['owner_id'] ||
						isset($row['status_id']) && $row['status_id']				!= $rowset[$key + 1]['status_id'] ||
						isset($row['mill'])	&& $row['mill']					!= $rowset[$key + 1]['mill'] ||
						isset($row['system']) && $row['system']					!= $rowset[$key + 1]['system'] ||
						isset($row['current_cost']) && $row['current_cost']			!= $rowset[$key + 1]['current_cost'] ||
						isset($row['pl']) && $row['pl']						!= $rowset[$key + 1]['pl'] ||
						isset($row['load_ready']) && $row['load_ready']				!= $rowset[$key + 1]['load_ready']            
					)
					{	
						$rowset[$key]['edit_status'][] = 'Status was changed';
					}	
					
					//chemical
					if
					(
						isset($row['heat_lot']) && $row['heat_lot']	!= $rowset[$key + 1]['heat_lot'] ||
						isset($row['c']) && $row['c']				!= $rowset[$key + 1]['c'] ||
						isset($row['si']) && $row['si']				!= $rowset[$key + 1]['si'] ||
						isset($row['mn']) && $row['mn']				!= $rowset[$key + 1]['mn'] ||
						isset($row['p']) && $row['p']				!= $rowset[$key + 1]['p'] ||
						isset($row['s']) && $row['s']				!= $rowset[$key + 1]['s'] ||
						isset($row['cr']) && $row['cr']				!= $rowset[$key + 1]['cr'] ||
						isset($row['ni']) && $row['ni']				!= $rowset[$key + 1]['ni'] ||
						isset($row['cu']) && $row['cu']				!= $rowset[$key + 1]['cu'] ||
						isset($row['al']) && $row['al']				!= $rowset[$key + 1]['al'] ||
						isset($row['mo']) && $row['mo']				!= $rowset[$key + 1]['mo'] ||
						isset($row['nb']) && $row['nb']				!= $rowset[$key + 1]['nb'] ||
						isset($row['v']) && $row['v']				!= $rowset[$key + 1]['v'] ||
						isset($row['n']) && $row['n']				!= $rowset[$key + 1]['n'] ||
						isset($row['ti']) && $row['ti']				!= $rowset[$key + 1]['ti'] ||
						isset($row['sn']) && $row['sn']				!= $rowset[$key + 1]['sn'] ||
						isset($row['b']) && $row['b']				!= $rowset[$key + 1]['b'] ||
						isset($row['ceq']) && $row['ceq']			!= $rowset[$key + 1]['ceq']
					) 
					{	
						$rowset[$key]['edit_status'][] = 'Chemical analysis was changed';
					}	
            
					//mechanical
					if 
					(
						isset($row['tensile_sample_direction']) && $row['tensile_sample_direction']	!= $rowset[$key + 1]['tensile_sample_direction'] ||
						isset($row['tensile_strength']) && $row['tensile_strength']					!= $rowset[$key + 1]['tensile_strength'] ||
						isset($row['yeild_point']) && $row['yeild_point']							!= $rowset[$key + 1]['yeild_point'] ||
						isset($row['elongation']) && $row['elongation']								!= $rowset[$key + 1]['elongation'] ||
						isset($row['reduction_of_area']) && $row['reduction_of_area']				!= $rowset[$key + 1]['reduction_of_area'] ||
						isset($row['test_temp']) && $row['test_temp']								!= $rowset[$key + 1]['test_temp'] ||
						isset($row['impact_strength']) && $row['impact_strength']					!= $rowset[$key + 1]['impact_strength'] ||
						isset($row['hardness'])	&& $row['hardness']									!= $rowset[$key + 1]['hardness'] ||
						isset($row['ust']) && $row['ust']											!= $rowset[$key + 1]['ust'] ||
						isset($row['sample_direction']) && $row['sample_direction']					!= $rowset[$key + 1]['sample_direction'] ||
						isset($row['stress_relieving_temp']) && $row['stress_relieving_temp']		!= $rowset[$key + 1]['stress_relieving_temp'] ||
						isset($row['heating_rate_per_hour']) && $row['heating_rate_per_hour']		!= $rowset[$key + 1]['heating_rate_per_hour'] ||
						isset($row['holding_time']) && $row['holding_time']							!= $rowset[$key + 1]['holding_time'] ||
						isset($row['cooling_down_rate']) && $row['cooling_down_rate']				!= $rowset[$key + 1]['cooling_down_rate'] ||
						isset($row['condition']) && $row['condition']								!= $rowset[$key + 1]['condition'] ||
						isset($row['normalizing_temp']) && $row['normalizing_temp']					!= $rowset[$key + 1]['normalizing_temp']

					)
					{	
						$rowset[$key]['edit_status'][] = 'Mechanical properties was changed';
					}	
					
					//other
					if (isset($row['guid']) && $row['guid']						!= $rowset[$key + 1]['guid']) $rowset[$key]['edit_status'][]			= 'Plate Id was changed';
					if (isset($row['steelgrade_id']) && $row['steelgrade_id']	!= $rowset[$key + 1]['steelgrade_id']) $rowset[$key]['edit_status'][]	= 'Steelgrade was changed';
					if (isset($row['location_id']) && $row['location_id']		!= $rowset[$key + 1]['location_id']) $rowset[$key]['edit_status'][]		= 'Location was changed';
					if (isset($row['owner_id']) && $row['owner_id']				!= $rowset[$key + 1]['owner_id']) $rowset[$key]['edit_status'][]		= 'Owner was changed';
					//if (isset($row['order_id']) && $row['order_id']				!= $rowset[$key + 1]['order_id']) $rowset[$key]['edit_status'][]		= 'Add to order';
				}
			}
			
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
     * Закрывает айтем для редактирования
     * 
     * @param mixed $item_id
     */
    public static function Lock($item_id)
    {
        $data   = Cache::GetData('inuse-item-' . $item_id);
        $login  = isset($_SESSION['user']) ? Request::GetString('login', $_SESSION['user']) : '';
        
        if (!isset($data) || !isset($data['data']) || isset($data['outdated']))
        {
            Cache::SetData('inuse-item-' . $item_id, $login, array(), CACHE_LIFETIME_ONLINE);    
        }        
    }
    
    /**
     * Проверяет закрыт ли айтем от редактирования
     * 
     * @param mixed $item_id
     */
    public static function IsLocked($item_id)
    {
        $data   = Cache::GetData('inuse-item-' . $item_id);
        $login  = isset($_SESSION['user']) ? Request::GetString('login', $_SESSION['user']) : '';
        
        return isset($data) && isset($data['data']) && !isset($data['outdated']) && $data['data'] != $login;
    }
    
    /**
     * Разблокирует айтем для редактирования
     * 
     * @param mixed $item_id
     */
    public static function Unlock($item_id)
    {
        $data   = Cache::GetData('inuse-item-' . $item_id);
        $login  = isset($_SESSION['user']) ? Request::GetString('login', $_SESSION['user']) : '';
        
        if (isset($data) && isset($data['data']) && !isset($data['outdated']) && $data['data'] == $login)
        {
            Cache::ClearKey('inuse-item-' . $item_id);
        }
    }
    
    /**
    * Возвращает кто закрыл позицию для редактирования
    * 
    * @param mixed $item_id
    */
    public static function LockedBy($item_id)
    {
        $data = Cache::GetData('inuse-item-' . $item_id);
        
        if (isset($data) && isset($data['data']) && !isset($data['outdated']))
        {
            return $data['data'];
        }        
        
        return null;
    }
    
    /**
     * Устанавливает/Апдейтит значение поля steelitems.status_id
     * 
     * @param int $id [INT]
     * @param int $status_id [TINYINT]
     * @return boolean
     * 
     * @version 20121012, d10n
     */
    public function SetStatus($item_id, $status_id)
    {
        $result = $this->CallStoredProcedure('sp_steelitem_set_status', array($this->user_id, $item_id, $status_id));
        $result = isset($result[0]) && isset($result[0][0]) ? $result[0][0] : array();
        
        Cache::ClearTag('steelitem-' . $item_id);        
        if (isset($result['order_id']) && $result['order_id'] > 0)
        {
            Cache::ClearTag('order-' . $result['order_id']);
            Cache::ClearTag('orders');            
        }
        
        Cache::ClearTag('reports');

        return $result;
    }
    
    /**
     * Список TimeLine для конкретного Айтема
     * 
     * @param int $steelitem_id
     * @return array
     * 
     * @version 20121218, d10n
     */
    public function TimelineGetList($steelitem_id)
    {
        $rowset = $this->CallStoredProcedure('sp_steelitem_timeline_get_list', array($steelitem_id));
        $rowset = isset($rowset[0]) ? $rowset[0] : array();
        
        foreach ($rowset as $key => $row)
        {
            if (!empty($row['object_alias']))
            {
                $rowset[$key][$row['object_alias'] . '_id'] = $row['object_id'];
            }            
        }
        
        $modelInDDT = new InDDT();
        $rowset = $modelInDDT->FillInDDTInfo($rowset);

        $modelOrder = new Order();
        $rowset = $modelOrder->FillOrderMainInfo($rowset);

        $modelRA = new RA();
        $rowset = $modelRA->FillRAMainInfo($rowset);

        $modelCMR = new CMR();
        $rowset = $modelCMR->FillCMRMainInfo($rowset);
        
        $modelDDT = new DDT();
        $rowset = $modelDDT->FillDDTMainInfo($rowset);
/*
        $modelInvoice = new Invoice();
        $rowset = $modelInvoice->FillInvoiceInfo($rowset, 'invoice_id', 'object');
*/        
        foreach ($rowset as $key => $row)
        {
            if(isset($row[$row['object_alias']])) 
            {
                $rowset[$key]['object'] = $row[$row['object_alias']];
                
                unset($rowset[$key][$row['object_alias']]);
            }            
            $rowset[$key]['no'] = count($rowset) - $key;
        }

        $result = array(
            'left'  => array(),
            'right' => array()
        );
        
        
        $rowset     = $this->FillSteelItemInfo($rowset);
        
        $modelUser  = new User();
        $rowset     = $modelUser->FillUserInfo($rowset, 'modified_by', 'modifier');
        $rowset     = $modelUser->FillUserInfo($rowset, 'created_by', 'author');
        
        $right   = count($rowset) % 2 ? 'left' : 'right';
        $left  = count($rowset) % 2 ? 'right' : 'left';
        
        foreach ($rowset as $key => $row)
        {
            $side_alias = $key % 2 ? $left : $right;
            
            if ($row['object_alias'] == '') $row['title'] = 'Added to MaM DB';
            if ($row['object_alias'] == 'inddt') $row['title'] = 'In DDT';
            if ($row['object_alias'] == 'order') $row['title'] = 'Order';
            if ($row['object_alias'] == 'ra') $row['title'] = 'RA';
            if ($row['object_alias'] == 'cmr') $row['title'] = 'CMR';
            if ($row['object_alias'] == 'ddt') $row['title'] = 'DDT';
            if ($row['object_alias'] == 'invoice') $row['title'] = 'Invoice';
            if ($row['object_alias'] == 'claim') $row['title'] = 'Claim';

            $result[$side_alias][] = $row;
        }
        
        return $result;
    }
    
    /**
     * Возвращает информацию по TimeLine-событию
     * 
     * @param array $timeline
     * @return array
     * array('description'=> 'There is no description', 'doc_href' => '',)
     * 
     * @version 20121218, d10n
     */
    private function _get_timeline_event(&$timeline = array())
    {
        $object_alias = $timeline['object_alias'];
        
        $default = array(
            'description'   => 'There is no description',
            'doc_href'      => '',
        );
        
        switch ($object_alias)
        {
            case 'clame' :
                $instance       = new Clame();
                $description    = 'Put into Clame';
                break;
            
            case 'cmr' :
                $instance       = new CMR();
                $description    = 'Put into CMR';
                break;
                
            case 'ddt' :
                $instance       = new DDT();
                $description    = 'Put into DDT';
                break;
                
            case 'inddt' :
                $instance       = new InDDT();
                $description    = 'Put into Incoming DDT';
                break;
                
            case 'invoice' :
                $instance       = new Invoice();
                $description    = 'Put intoInvoice';
                break;
                
            case 'order' :
                $instance       = new Order();
                $description    = 'Put into Order';
                break;
                
            case 'ra' :
                $instance       = new RA();
                $description    = 'Put into RA';
                break;
                
            default:
                $description    = 'Added to DB';
                $doc_href       =  '/item/' . $timeline['steelitem_id'] . '/view';
        }
        
        if (!empty($object_alias))
        {
            $object = $instance->GetById($timeline['object_id']);
            $object = $object[$object_alias];
            
            $timeline['object'] = $object;
            
            $description    = $description . ' No ' . $object['doc_no'];
            $doc_href       = '/' . $object_alias . '/' . $object['id'];
        }
        
        return array(
            'description'   => $description,
            'doc_href'      => $doc_href,
        );
    }
    
    /**
     * Обновляет данные об айтеме
     * @version 20130113, zharkov
     */
    function UpdateSingle($id, $params)
    {
        $params['modified_at'] = date('Y-m-d H:i:s');
        $params['modified_by'] = $this->user_id;
        
        $this->Update($id, $params);
        
        Cache::ClearTag('steelitem-' . $id);
        Cache::ClearTag('steelitems');
        Cache::ClearTag('reports');
    }
    
    /**
     * Возвращает список всех уникальных стокхолдеров в айтемах
     * @return array
     * 
     * @version 20130214, d10n
     */
    public function GetStockholdersList($owner)
    {
        $data_set = $this->SelectList(array(
            'fields'    => 'DISTINCT stockholder_id, 
                IFNULL((SELECT CONCAT(companies.title_short, title_trade, title) FROM companies WHERE companies.id = steelitems.stockholder_id), "") AS stockholder_doc_no',
            'where'     => array(
                'conditions'    => 'guid NOT IN (\'\') AND stockholder_id NOT IN (0)' . (empty($owner) ? '' : ' AND owner_id IN (' . $owner . ')'),
                'arguments'     => array(),
            ),
            'order'  => 'stockholder_doc_no',
        ));
        
        $modelCompany = new Company();
        return !empty($data_set) ? $modelCompany->FillCompanyInfo($data_set, 'stockholder_id', 'stockholder') : array();
    }
    
    /**
     * Возвращает список всех уникальных поставщиков в айтемах
     * @return array
     * 
     * @version 20130214, d10n
     */
    public function GetSuppliersList($owner, $stockholder_id)
    {
        $data_set = $this->SelectList(array(
            'fields'    => 'DISTINCT supplier_id, 
                IFNULL((SELECT CONCAT(companies.title_short, title_trade, title) FROM companies WHERE companies.id = steelitems.supplier_id), "") AS stockholder_doc_no',
            'where'     => array(
                'conditions'    => 'guid NOT IN (\'\') AND supplier_id NOT IN (0)' . ($stockholder_id > 0 ? ' AND stockholder_id = ' . $stockholder_id : '') . (empty($owner) ? '' : ' AND owner_id IN (' . $owner . ')'),
                'arguments'     => array(),
            ),
            'order'  => 'stockholder_doc_no',
        ));
        
        $modelCompany = new Company();
        return !empty($data_set) ? $modelCompany->FillCompanyInfo($data_set, 'supplier_id', 'supplier') : array();
    }
    
    /**
     * Возвращает список всех уникальных стран айтемов (steelitems.order_id -> orders.company_id -> companies.country_id)
     * @return array
     * 
     * @version 20130214, d10n
     */
    public function GetCountriesList($owner, $stockholder_id)
    {
        $data_set = $this->SelectList(array(
            'fields'    => 'DISTINCT companies.country_id, 
                IFNULL((SELECT countries.title FROM countries WHERE countries.id = companies.country_id), "") AS country_title',
            'where'     => array(
                'conditions'    => 'steelitems.guid NOT IN (\'\') AND order_id NOT IN(0) AND companies.country_id NOT IN (0)' . ($stockholder_id > 0 ? ' AND stockholder_id = ' . $stockholder_id : '') . (empty($owner) ? '' : ' AND owner_id IN (' . $owner . ')'),
                'arguments'     => array(),
            ),
            'join'      => array(
                array(
                    'type'          => 'LEFT',
                    'table'         => 'orders',
                    'conditions'    => 'steelitems.order_id = orders.id',
                    'arguments'     => array(),
                ),
                array(
                    'type'          => 'LEFT',
                    'table'         => 'companies',
                    'conditions'    => 'orders.company_id = companies.id',
                    'arguments'     => array(),
                ),
            ),
            'order'  => 'country_title',
        ));
        
        $modelCountry = new Country();
        return !empty($data_set) ? $modelCountry->FillCountryInfo($data_set, 'country_id', 'country') : array();
    }
    
    /**
     * Get item steelgrades 
     * 
     * @param mixed $owner
     * @param mixed $stockholder_id
     * @return array
     */
    public function GetSteelGradesList($owner, $stockholder_id)
    {
        $data_set = $this->SelectList(array(
            'fields'    => 'DISTINCT steelgrade_id,
                IFNULL((SELECT title FROM steelgrades WHERE steelgrades.id = steelitems.steelgrade_id), "") AS steelgrade_title',
            'where'     => array(
                'conditions'    => 'guid NOT IN (\'\') AND steelgrade_id NOT IN (0)' . ($stockholder_id > 0 ? ' AND stockholder_id = ' . $stockholder_id : '') . (empty($owner) ? '' : ' AND owner_id IN (' . $owner . ')'),
                'arguments'     => array(),
            ),
            'order'  => 'steelgrade_title',
        ));
        
        $modelSteelGrade = new SteelGrade();
        return !empty($data_set) ? $modelSteelGrade->FillSteelGradeInfo($data_set) : array();
    }
        
    /**
     * Возвращает список всех покупателей айтемов (steelitems.order_id -> orders.company_id)
     * 
     * @param int $country_id ID страны в которой куплен айтем
     * @return array
     * 
     * @version 20130214, d10n
     */
    public function GetBuyersList($owner, $stockholder_id, $country_id = 0)
    {
        $data_set = $this->SelectList(array(
            'fields'    => 'DISTINCT orders.company_id, companies.title AS company_title',
            'where'     => array(
                'conditions'    => 'order_id NOT IN(0)' 
                . ($country_id > 0 ? ' AND companies.country_id = ' . $country_id : '') 
                . ($stockholder_id > 0 ? ' AND stockholder_id = ' . $stockholder_id : '') 
                . (empty($owner) ? '' : ' AND owner_id IN (' . $owner . ')')
            ),
            //'order'  => 'stockholder_id',
            'join'      => array(
                array(
                    'type'          => 'LEFT',
                    'table'         => 'orders',
                    'conditions'    => 'steelitems.order_id = orders.id',
                    'arguments'     => array(),
                ),
                array(
                    'type'          => 'LEFT',
                    'table'         => 'companies',
                    'conditions'    => 'orders.company_id = companies.id',
                    'arguments'     => array(),
                ),
            ),
            'order'  => 'company_title'
        ));
        
        $modelCountry = new Country();
        return !empty($data_set) ? $modelCountry->FillCountryInfo($data_set, 'buyer_id', 'buyer') : array();
    }    
}
