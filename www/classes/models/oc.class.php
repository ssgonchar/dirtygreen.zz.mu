<?php
require_once APP_PATH . 'classes/models/attachment.class.php';
require_once APP_PATH . 'classes/models/oc_standard.class.php';
require_once APP_PATH . 'classes/models/steelitem.class.php';

define('OC_KIND_QUALITY',       1);
define('OC_KIND_UST',           2);
define('OC_KIND_TEST_REPORT',   3);

define('OC_STATE_OF_SUPPLY_NORMALIZED', 1);
define('OC_STATE_OF_SUPPLY_AS_ROLLED',  2);

class OC extends Model
{
    public function OC()
    {
        Model::Model('ocs');
    }
    
    /**
     * Возвращает данные по ID записи
     * 
     * @param int $id
     * @return array
     * 
     * @version 20130214, d10n
     */
    public function GetById($id)
    {
        $rowset = $this->FillOCInfo(array(array('oc_id' => $id)));
        return isset($rowset) && isset($rowset[0]) && isset($rowset[0]['oc']) ? $rowset[0] : null;
    }
    
    /**
    * Сохраняет OC
    * 
    * @param int $id
    * @param string $number
    * @param string $date
    * @param int $company_id
    * @param int $kind
    * @param int $standard_id
    * @param int $state_of_supply
    * @return array
    * 
    * @version 20130214, d10n
    */
    public function Save($id, $number, $date, $company_id, $kind, $standard_id, $state_of_supply)
    {
        $rowset = $this->CallStoredProcedure('sp_oc_save', array($this->user_id, $id, $number, $date, $company_id, $kind, $standard_id, $state_of_supply));
        $rowset = isset($rowset) && isset($rowset[0]) && isset($rowset[0][0]) ? $rowset[0][0] : null;
        
        if (empty($rowset) || array_key_exists('ErrorCode', $rowset)) 
        {
            Log::AddLine(LOG_ERROR, 'sp_oc_save : ' . var_export($rowset, true));
            return array();
        }
        
        Cache::ClearTag('ocs');
        if ($id > 0) Cache::ClearTag('oc-' . $id);

        return isset($rowset['oc_id']) ? $this->GetById($rowset['oc_id']) : array();
    }

    /**
    * Добавляет айтем
    * 
    * @param int $oc_id
    * @param int $steelitem_id
    * @return array
    * 
    * @version 20130214, d10n
    */
    public function SaveItem($oc_id, $steelitem_id)
    {
        $rowset = $this->CallStoredProcedure('sp_oc_save_item', array($this->user_id, $oc_id, $steelitem_id));
        
        Cache::ClearTag('oc-' . $oc_id);
        Cache::ClearTag('oc-' . $oc_id . '-items');
        
        return $rowset;
    }
    
    /**
    * Удаляет айтем
    * 
    * @param int $oc_id
    * @param int $steelitem_id
    * 
    * @version 20130214, d10n
    */
    public function RemoveItem($oc_id, $steelitem_id)
    {
        $this->CallStoredProcedure('sp_oc_remove_item', array($this->user_id, $oc_id, $steelitem_id));
        
        Cache::ClearTag('oc-' . $oc_id);
        Cache::ClearTag('oc-' . $oc_id . '-items');
        
        return true;
    }
    
    /**
    * Возвращает список айтемов
    * 
    * @param int $oc_id
    * 
    * @version 20130214, d10n
    */
    public function GetItems($oc_id)
    {
        $hash       = 'oc-' . $oc_id . '-items';
        $cache_tags = array($hash, 'ocs', 'oc-' . $oc_id);

        $rowset         = $this->_get_cached_data($hash, 'sp_oc_get_items', array($this->user_id, $oc_id), $cache_tags);

        $modelSteelItem = new SteelItem();
        $rowset         = isset($rowset[0]) ? $modelSteelItem->FillSteelItemInfo($rowset[0]) : array();
        
        return $rowset;
    }
    
    /**
    * Список OC
    *     
    * @param int $company_id
    * @param string $date_from
    * @param string $date_to
    * @param string $number
    * @param int $page_no
    * @param int $per_page
    * @return array
    * 
    * @version 20130214, d10n
    */
    public function GetList($company_id, $date_from, $date_to, $number, $plate_id, $page_no = 0, $per_page = ITEMS_PER_PAGE)
    {
        $page_no    = $page_no > 0 ? $page_no : 1;
        $per_page   = $per_page < 1 ? ITEMS_PER_PAGE : $per_page;
        $start      = ($page_no - 1) * $per_page;

		//$plate_id = '0';
        $hash       = 'ocs' . md5('-company' . $company_id . '-date_from' . $date_from . '-date_to' . $date_to . '-number' . $number . '-plate_id' . $plate_id . '-page_no' . $page_no . '-start' . $start);
        $cache_tags = array($hash, 'ocs');
        

		$data_set   = $this->_get_cached_data($hash, 'sp_ocs_get_list_test', array($company_id, $date_from, $date_to, $number, $plate_id, $start, $per_page), $cache_tags);
		//print_r(isset($data_set[0]));
        if (empty($data_set) && !isset($data_set[0])) {
			//print_r('if');
			return array('data' => array(), 'count' => 0);
			}
        
        $list       = $this->FillOCInfo($data_set[0]);
		
        $rows_count = (isset($data_set[1]) && isset($data_set[1][0]) && isset($data_set[1][0]['rows_count'])) ? $data_set[1][0]['rows_count'] : 0;
        //print_r($data_set[1]);
		//print_r($list);
        return array('data' => $list, 'count' => $rows_count);
		
    }
    
    /**
     * Удаляет OC
     * 
     * @param int $oc_id
     * 
     * @version 20130214, d10n
     */
    public function Remove($oc_id)
    {
        $result = $this->CallStoredProcedure('sp_oc_remove', array($oc_id));
        
        Cache::ClearTag('ocs');
        Cache::ClearTag('oc-' . $oc_id);
        Cache::ClearTag('oc-' . $oc_id . '-items');
        
        return true;
    }
    
    /**
     * Наполняет OC данными
     * 
     * @param array $rowset
     * @param string $id_fieldname
     * @param string $entityname
     * @param string $cache_prefix
     * @return array
     * 
     * @version 20130214, d10n
     */
    public function FillOCInfo($rowset, $id_fieldname = 'oc_id', $entityname = 'oc', $cache_prefix = 'oc')
    {
        return $this->FillOCMainInfo($rowset, $id_fieldname, $entityname, $cache_prefix);
    }
    
    /**
     * Наполняет OC основными данными
     * 
     * @param array $rowset
     * @param string $id_fieldname
     * @param string $entityname
     * @param string $cache_prefix
     * @return array
     * 
     * @version 20130214, d10n
     */
    private function FillOCMainInfo($rowset, $id_fieldname = 'oc_id', $entityname = 'oc', $cache_prefix = 'oc')
    {
        $rowset = $this->_fill_entity_info($rowset, $id_fieldname, $entityname, $cache_prefix, 'sp_oc_get_list_by_ids', array('ocs' => '', 'oc' => 'id'), array());

        foreach ($rowset as $key => $row)
        {
            if (isset($row[$entityname]))
            {
                $row    = $row[$entityname];
                $doc_no = 'OrigQC' . (empty($row['number']) ? ' # ' . substr((10000 + $row['id']), 1) : $row['number']);
                
                $rowset[$key][$entityname]['date']          = !empty($row['date']) && $row['date'] > 0 ? $row['date'] : '';

                $rowset[$key][$entityname]['doc_no']        = $doc_no;
                $rowset[$key][$entityname]['doc_no_full']   = empty($row['number']) ? $doc_no : $doc_no . ($row['date'] > 0 ? ' dd ' . date_format(date_create($row['date']), 'd/m/Y') : '');
                
                $rowset[$key]['oc_company_id']      = $row['company_id'];
                $rowset[$key]['oc_author_id']       = $row['created_by'];
                $rowset[$key]['oc_modifier_id']     = $row['modified_by'];
                $rowset[$key]['oc_standard_id']     = $row['standard_id'];
                
                $rowset[$key][$entityname]['items_list']         = $this->GetItems($row['id']);
                
            }
        }
        
        $modelCompany   = new Company();
        $rowset         = $modelCompany->FillCompanyInfoShort($rowset, 'oc_company_id', 'oc_company');
        
        $modelOCStandart= new OCStandard();
        $rowset         = $modelOCStandart->FillOCStandardInfo($rowset, 'oc_standard_id', 'oc_standard');
        
        $modelUser      = new User();
        $rowset         = $modelUser->FillUserInfo($rowset, 'oc_author_id', 'oc_author');
        $rowset         = $modelUser->FillUserInfo($rowset, 'oc_modifier_id', 'oc_modifier');
        
        foreach ($rowset as $key => $row)
        {
            if (isset($row['oc_standard']) && !empty($row['oc_standard']))
            {
                $rowset[$key][$entityname]['standard'] = $row['oc_standard'];
            }
            unset($rowset[$key]['oc_standard_id']);
            unset($rowset[$key]['oc_standard']);
            
            if (isset($row['oc_company']) && !empty($row['oc_company']))
            {
                $rowset[$key][$entityname]['company'] = $row['oc_company'];
            }
            unset($rowset[$key]['oc_company_id']);
            unset($rowset[$key]['oc_company']);
            
            
            if (isset($row['oc_author']) && !empty($row['oc_author']))
            {
                $rowset[$key][$entityname]['author'] = $row['oc_author'];
            }
            unset($rowset[$key]['oc_author_id']);
            unset($rowset[$key]['oc_author']);

            if (isset($row['oc_modifier']) && !empty($row['oc_modifier']))
            {
                $rowset[$key][$entityname]['modifier'] = $row['oc_modifier'];
            }
            unset($rowset[$key]['oc_modifier_id']);
            unset($rowset[$key]['oc_modifier']);
            
            if (isset($row[$entityname]))
            {
                $row = $row[$entityname];
                
                switch ($row['kind'])
                {
                    case OC_KIND_QUALITY :
                        $kind_title = 'Quality';
                        break;
                    
                    case OC_KIND_UST :
                        $kind_title = 'UST';
                        break;
                    
                    case OC_KIND_TEST_REPORT :
                        $kind_title = 'Test Report';
                        break;
                    
                    default: $kind_title = '';
                }
                $rowset[$key][$entityname]['kind_title'] = $kind_title;
                
                
                switch ($row['state_of_supply'])
                {
                    case OC_STATE_OF_SUPPLY_NORMALIZED :
                        $state_of_supply_title = 'Normalized';
                        break;
                    
                    case OC_STATE_OF_SUPPLY_AS_ROLLED :
                        $state_of_supply_title = 'As Rolled';
                        break;
                    
                    default: $state_of_supply_title = '';
                }
               $rowset[$key][$entityname]['state_of_supply_title'] = $state_of_supply_title;
            }
        }
        $modelAttachment = new Attachment();
        return $modelAttachment->FillObjectAttachments($rowset, $entityname, $id_fieldname);
    }
    
    /**
     * Добавляет атачмент документа к айтемам
     * 
     * @param int $oc_id
     */
    public function LinkAttachmentToItems($oc_id)
    {
        foreach ($this->GetItems($oc_id) as $item)
        {
            $this->CallStoredProcedure('sp_oc_link_attachments_to_item', array($this->user_id, $oc_id, $item['steelitem_id']));
            Cache::ClearTag('attachments-aliasoc-id' . $oc_id);
        }
    }
	
	/**
	 * Полнотекстовый поиск
	 * 2014-03-19
	 * Гончар
	 */
	 
	/**
	 * @param string $search_string
	 * @param type $company_ids
	 * @param type $page_no
	 * @param type $per_page
	 * @return null
	 */
	function Search($search_string, $company_ids = array(), $roles = array(), $objective_id = 0, $team_id = 0, $product_id = 0, $status = 0, $market_id = 0, $user_id = 0, $page_no = 0, $per_page = ITEMS_PER_PAGE)
	{
		//die($search_string);
		$page_no    = $page_no > 0 ? $page_no : 1; 
        $per_page   = $per_page < 1 ? ITEMS_PER_PAGE : $per_page;
        $start      = ($page_no - 1) * $per_page;
		
		$hash   =   'bizes-search-' . md5($search_string . '-companies-' . serialize($company_ids) . '-role-' . serialize($roles) . 
					'-objective-' . $objective_id . 
                    '-team-' . $team_id . '-product-' . $product_id . '-status-' . $status . 
                    '-product' . $product_id . '-status-' . $status . 
                    '-market-' . $market_id . 
                    '-driver-' . $user_id . 
                    '-page-' . $page_no . '-' . $per_page);
        
        $rowset = Cache::GetData($hash);
		
		if (!isset($rowset) || !isset($rowset['data']) || isset($rowset['outdated']))
		{
			/*
			 * the intersection of arrays
			 */
			if (!empty($company_ids))
			{	
				
				foreach ($company_ids as $key => $row)
				{	
					$rowset[] = $this->GetListByCompanyAndRole($row['company_id'], isset($roles[$key]) ? $roles[$key] : 0);
				}

				if (!empty($rowset) && count($company_ids) == 1) 
				{	
					$bizes_ids = $rowset[0];
				} 
				else if (isset($rowset) && !empty($rowset))
				{
					$bizes_ids = $rowset[0];

					foreach ($rowset as $key => $value)
					{
						if (empty($value))
						{	
							$bizes_ids = array(0);
							break;
						}

						if ($key > 0)
						{	
							$bizes_ids = array_intersect($bizes_ids, $value); 
						}
					}
				}
			}
	
			if (empty($company_ids) || (!empty($company_ids) && !empty($bizes_ids)))
			{	
				$cl = new SphinxClient();
				$cl->SetLimits($start, $per_page, 5000);
                $cl->SetFieldWeights(array(
                    'full_number'           => 1000,
                    'biz_full_number'       => 1000,
                    'biz_title'             => 1000,
                    'description'           => 100
                ));                
                
				$cl->SetMatchMode(SPH_MATCH_ALL);
                
                $preg           = '/^\d+$/';
                $query_string   = str_replace('biz', '', $search_string);
                preg_match($preg, $query_string, $matches);

                if (empty($matches))
                {
                    $cl->SetGroupBy('biz_id', SPH_GROUPBY_ATTR, '@weight DESC');
                    $cl->SetSortMode(SPH_SORT_ATTR_DESC, 'last_access_at');
                }
                else
                {
                    $cl->SetGroupBy('biz_id', SPH_GROUPBY_ATTR, 'number ASC, suffix ASC');
                }
                
                if (!empty($search_string)) $search_string = '*' . str_replace('-', '\-', str_replace(' ', '* *', $search_string)) . '*';

				$product_ids = array();
			
				if ($product_id > 0) 
				{
					$product_model = new Product();
					$product_list = $product_model->GetBranch($product_id);
					
					foreach($product_list as $row)
					{
						if (isset($row['product_id']) && $row['product_id'] > 0) $product_ids[] = $row['product_id']; 
					}
						
					$product_ids[] = $product_id;
				}			
				
				/*filter*/
				if (isset($bizes_ids)) $cl->SetFilter('biz_id', $bizes_ids);
				if ($objective_id > 0) $cl->SetFilter('objective_id', array($objective_id));
				if ($team_id > 0) $cl->SetFilter('team_id', array($team_id));
				if (!empty($product_ids)) $cl->SetFilter('product_id', $product_ids);
				if ($market_id > 0) $cl->SetFilter('market_id', array($market_id));
				if ($user_id > 0) $cl->SetFilter('user_id', array($user_id));
				if (!empty($status)) $cl->SetFilter('status_id', array(sprintf("%u", crc32($status) & 0xffffffff))); 

                
				if ($user_id > 0)
				{
					$data = $cl->Query($search_string, 'ix_mam_biz_search_users, ix_mam_biz_search_users_delta');
				}
				else
				{
					$data = $cl->Query($search_string, 'ix_mam_biz_search, ix_mam_biz_search_delta');
				}

				if ($data === false)
				{
					Log::AddLine(LOG_ERROR, 'biz::search ' . $cl->GetLastError());
					return null;
				 }

				$rowset = array(); 
				if (!empty($data['matches']))
				{
					foreach ($data['matches'] as $id => $extra)
					{
						$rowset[] = array('biz_id' => $extra['attrs']['biz_id']);
					}
				}

				$rowset = array(
				    $rowset,
				    array(array('rows' => $data['total_found']))
				);

				Cache::SetData($hash, $rowset, array('bizes', 'filter'), CACHE_LIFETIME_30S);   // CACHE_LIFETIME_STANDARD

				$rowset = array(
				    'data' => $rowset
				);
			}
		}
		
		$result = array();
        $result['data']  = isset($rowset['data'][0]) ? $this->FillBizInfo($rowset['data'][0]) : null;
        $result['count'] = isset($rowset['data'][1]) && isset($rowset['data'][1][0]) && isset($rowset['data'][1][0]['rows']) ? $rowset['data'][1][0]['rows'] : 0;
       
        //sasha add active orders
        if (!empty($result['data']))
		{
			foreach ($result['data'] as $key => $row)
			{
				$result['data'][$key]['biz']['orders'] = $this->GetOrders($row['biz']['id']);
			}	
		}
    
		return $result;
	}
}