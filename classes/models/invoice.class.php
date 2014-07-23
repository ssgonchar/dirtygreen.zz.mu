<?php
require_once APP_PATH . 'classes/models/order.class.php';
require_once APP_PATH . 'classes/models/steelitem.class.php';


class Invoice extends Model
{
    public function Invoice()
    {
        Model::Model('invoices');
    }
    
    /**
     * Закрывает инвойс, обновляет связанные документы
     * 
     * @param mixed $invoice_id
     */
    function CloseInvoice($invoice_id)
    {
        $items = $this->GetItems($invoice_id);
        
        if (count($items) > 0)
        {
            // закрывает инвойс
            $this->Update($invoice_id, array('is_closed' => 1));
            
            // обновляет айтемы
            $orders = array();
            foreach ($items as $item)
            {
                if (isset($item['steelitem']))
                {
                    $order_id = $item['steelitem']['order_id'];
                    if ($order_id > 0) $orders[$order_id] = $order_id;
                }
                
                $this->SaveItem($invoice_id, $item['steelitem_id']);
            }
            
            // обновляем заказы
            foreach ($orders as $order_id => $row)
            {
                Cache::ClearTag('order-' . $order_id);
            }
            
            Cache::ClearTag('orders');
            Cache::ClearTag('orders-for-stock');            
        }
    }
    
    /**
     * Возвращает данные по ID записи
     * 
     * @param int $id
     * @return array
     * 
     * @version 20130104, d10n
     */
    public function GetById($id)
    {
        $rowset = $this->FillInvoiceInfo(array(array('invoice_id' => $id)));
        return isset($rowset) && isset($rowset[0]) && isset($rowset[0]['invoice']) ? $rowset[0] : null;
    }
    
    /**
     * Сохраняет данные
     * 
     * @param type $id
     * @param type $order_id
     * @param type $owner_id
     * @param type $biz_id
     * @param type $customer_id
     * @param type $number
     * @param type $date
     * @param type $due_date
     * @param type $status_id
     * @param type $amount_received
     * @param type $is_closed
     * @return array
     * 
     * @version 20130104, d10n
     * @version 20130115, zarkov
     */
    public function Save($id, $order_id, $owner_id, $biz_id, $customer_id, $number, $date, $due_date, $status_id, $amount_received)
    {
        $rowset = $this->CallStoredProcedure('sp_invoice_save', array($this->user_id, $id, $order_id, $owner_id, $biz_id, $customer_id, $number, $date, $due_date, $status_id, $amount_received));
        $rowset = isset($rowset) && isset($rowset[0]) && isset($rowset[0][0]) ? $rowset[0][0] : null;
        
        if (empty($rowset) || array_key_exists('ErrorCode', $rowset)) 
        {
            Log::AddLine(LOG_ERROR, 'sp_invoice_save : ' . var_export($rowset, true));
            return null;
        }
        
        Cache::ClearTag('invoices');
        Cache::ClearTag('invoice-' . $rowset['id']);

        return $rowset;
    }

    /**
     * Сохраняет Айтем сущности
     * 
     * @param int $invoice_id
     * @param int $steelitem_id
     * @return array
     * 
     * @version 20130104, d10n
     */
    public function SaveItem($invoice_id, $steelitem_id)
    {
        $rowset = $this->CallStoredProcedure('sp_invoice_save_item', array($this->user_id, $invoice_id, $steelitem_id));
        
        Cache::ClearTag('invoice-' . $invoice_id);
        Cache::ClearTag('invoice-' . $invoice_id . '-items');
        
        Cache::ClearTag('steelitem-' . $steelitem_id);
        
        $modelSteelItem = new SteelItem();
        $rowset         = isset($rowset[0]) ? $modelSteelItem->FillSteelItemInfo($rowset[0]) : array();
        
        return isset($rowset[0]) ? $rowset[0] : array();
    }
    
    /**
     * Удаляет Айтем из списка айтемов инвойса
     * 
     * @param int $invoice_id
     * @param int $steelitem_id
     * @return array
     * 
     * @version 20130108, d10n
     */
    public function RemoveItem($invoice_id, $steelitem_id)
    {
        $this->CallStoredProcedure('sp_invoice_remove_item', array($this->user_id, $invoice_id, $steelitem_id));
        
        Cache::ClearTag('invoice-' . $invoice_id);
        Cache::ClearTag('invoice-' . $invoice_id . '-items');
        
        Cache::ClearTag('steelitem-' . $steelitem_id);
        
        return true;
    }
    
    /**
     * Возвращает список Айтемов сущности
     * 
     * @param int $invoice_id
     * @return array
     * 
     * @version 20130104, d10n
     */
    public function GetItems($invoice_id)
    {
        $hash       = 'invoice-' . $invoice_id . '-items';
        $cache_tags = array($hash, 'invoices', 'invoice-' . $invoice_id);

        $rowset         = $this->_get_cached_data($hash, 'sp_invoice_get_items', array($this->user_id, $invoice_id), $cache_tags);
        
        $modelSteelItem = new SteelItem();
        $rowset         = isset($rowset[0]) ? $modelSteelItem->FillSteelItemInfo($rowset[0]) : array();
        
        return $rowset;
    }
    
    
    /**
     * Возвращает список записей
     * 
     * @param int $owner_id
     * @param int $page_no
     * @param int $per_page
     * @return array
     * 
     * @version 20130104, d10n
     */
    public function GetList($owner_id, $page_no = 0, $per_page = ITEMS_PER_PAGE)
    {
        $page_no    = $page_no > 0 ? $page_no : 1;
        $per_page   = $per_page < 1 ? ITEMS_PER_PAGE : $per_page;
        $start      = ($page_no - 1) * $per_page;

        $hash       = 'invoice-list' . md5('-owner-id' . $owner_id . '-page_no' . $page_no . '-start' . $start);
        $cache_tags = array($hash, 'invoices');
        
        $data_set   = $this->_get_cached_data($hash, 'sp_invoice_get_list', array($owner_id, $start, $per_page), $cache_tags);
        
        if (!isset($data_set[0])) return array('data' => array(), 'count' => 0);
        
        $list       = $this->FillInvoiceInfo($data_set[0]);
        $rows_count = (isset($data_set[1]) && isset($data_set[1][0]) && isset($data_set[1][0]['rows_count'])) ? $data_set[1][0]['rows_count'] : 0;
        
        return array('data' => $list, 'count' => $rows_count);
    }
    
    /**
     * Удаляет инвойс (удаляется запись из БД)
     * 
     * @param int $id
     * @return array
     * 
     * @version 20130108, d10n
     */
    public function RemoveInvoice($id)
    {
        $this->CallStoredProcedure('sp_invoice_remove', array($id));
        
        Cache::ClearTag('invoices');
        Cache::ClearTag('invoice-' . $id);
        Cache::ClearTag('invoice-' . $id . '-items');
        
        return true;
    }
    
    /**
     * Наполняет сущность расширенными данными
     * 
     * @param array $rowset
     * @param string $id_fieldname
     * @param string $entityname
     * @param string $cache_prefix
     * @return array
     * 
     * @version 20130104, d10n
     */
    public function FillInvoiceInfo($rowset, $id_fieldname = 'invoice_id', $entityname = 'invoice', $cache_prefix = 'invoice')
    {
        $rowset = $this->FillInvoiceMainInfo($rowset, $id_fieldname, $entityname, $cache_prefix);
        
        foreach ($rowset as $key => $row)
        {
            if (isset($row[$entityname]))
            {
                $row    = $row[$entityname];
                $doc_no = 'IVA ' . substr((10000 + $row['iva_number']), 1) . '/' . $row['iva_year'];
                
                $rowset[$key][$entityname]['date']          = !empty($row['date']) && $row['date'] > 0 ? date('d/m/Y', strtotime($row['date'])) : '';
                $rowset[$key][$entityname]['due_date']      = !empty($row['due_date']) && $row['due_date'] > 0 ? date('d/m/Y', strtotime($row['due_date'])) : '';                
                $rowset[$key][$entityname]['doc_no']        = $doc_no;
                $rowset[$key][$entityname]['doc_no_full']   = $doc_no . ($row['date'] > 0 ? ' dd ' . date_format(date_create($row['date']), 'd/m/Y') : '');
                $rowset[$key][$entityname]['is_overdue']    = !empty($row['due_date']) && $row['due_date'] > 0 && $row['due_date'] < date("Y-m-d") ? TRUE : FALSE;
                
                if ($row['owner_id'] == 0)
                {
                    $owner_name = 'IVA';
                }
                else if ($row['owner_id'] == 5998)
                {
                    $owner_name = 'MaM UK';
                }
                else if ($row['owner_id'] == 7117)
                {
                    $owner_name = 'MaM IT';
                }
                else if ($row['owner_id'] == 11980)
                {
                    $owner_name = 'PlatesAhead';
                }
                else
                {
                    $owner_name = '';
                }
                $rowset[$key][$entityname]['owner_name']    =  $owner_name;
                
                $rowset[$key]['invoice_order_id']           = $row['order_id'];
                $rowset[$key]['invoice_biz_id']             = $row['biz_id'];
                $rowset[$key]['invoice_customer_id']        = $row['customer_id'];
                $rowset[$key]['invoice_owner_id']           = $row['owner_id'];
                $rowset[$key]['invoice_author_id']          = $row['created_by'];
                $rowset[$key]['invoice_modifier_id']        = $row['modified_by'];
            }
        }
        
        $modelOrder     = new Order();
        $rowset         = $modelOrder->FillOrderInfo($rowset, 'invoice_order_id', 'invoice_order');
        
        $modelBiz       = new Biz();
        $rowset         = $modelBiz->FillMainBizInfo($rowset, 'invoice_biz_id', 'invoice_biz');
        
        $modelCompany   = new Company();
        $rowset         = $modelCompany->FillCompanyInfoShort($rowset, 'invoice_customer_id', 'invoice_customer');
        $rowset         = $modelCompany->FillCompanyInfoShort($rowset, 'invoice_owner_id', 'invoice_owner');
        
        $modelUser      = new User();
        $rowset         = $modelUser->FillUserInfo($rowset, 'invoice_author_id', 'invoice_author');
        $rowset         = $modelUser->FillUserInfo($rowset, 'invoice_modifier_id', 'invoice_modifier');
        
        foreach ($rowset as $key => $row)
        {
            if (isset($row['invoice_order']) && !empty($row['invoice_order']))
            {
                $rowset[$key][$entityname]['order'] = $row['invoice_order'];
            }
            unset($rowset[$key]['invoice_order_id']);
            unset($rowset[$key]['invoice_order']);
            
            if (isset($row['invoice_biz']) && !empty($row['invoice_biz']))
            {
                $rowset[$key][$entityname]['biz'] = $row['invoice_biz'];
            }
            unset($rowset[$key]['invoice_biz_id']);
            unset($rowset[$key]['invoice_biz']);
            
            if (isset($row['invoice_customer']) && !empty($row['invoice_customer']))
            {
                $rowset[$key][$entityname]['customer'] = $row['invoice_customer'];
            }
            unset($rowset[$key]['invoice_customer_id']);
            unset($rowset[$key]['invoice_customer']);
            
            if (isset($row['invoice_owner']) && !empty($row['invoice_owner']))
            {
                $rowset[$key][$entityname]['owner'] = $row['invoice_owner'];
            }
            unset($rowset[$key]['invoice_owner_id']);
            unset($rowset[$key]['invoice_owner']);


            if (isset($row['invoice_author']) && !empty($row['invoice_author']))
            {
                $rowset[$key][$entityname]['author'] = $row['invoice_author'];
            }
            unset($rowset[$key]['invoice_author_id']);
            unset($rowset[$key]['invoice_author']);

            if (isset($row['invoice_modifier']) && !empty($row['invoice_modifier']))
            {
                $rowset[$key][$entityname]['modifier'] = $row['invoice_modifier'];
            }
            unset($rowset[$key]['invoice_modifier_id']);
            unset($rowset[$key]['invoice_modifier']);
            
            if (isset($row[$entityname]))
            {
                $row = $row[$entityname];
                $rowset[$key][$entityname]['type'] = ($row['owner_id'] == -1 ? '' : (empty($row['owner_id']) ? 'IVA' : $rowset[$key][$entityname]['owner']['title_trade']));
                
                
                $items = $this->GetItems($row['id']);
                $rowset[$key][$entityname]['items_count'] = count($items);
            }
        }
        
        return $rowset;
    }
    
    /**
     * Наполняет сущность основными данными
     * 
     * @param array $rowset
     * @param string $id_fieldname
     * @param string $entityname
     * @param string $cache_prefix
     * @return array
     * 
     * @version 20130104, d10n
     */
    private function FillInvoiceMainInfo($rowset, $id_fieldname = 'invoice_id', $entityname = 'invoice', $cache_prefix = 'invoice')
    {
        return $this->_fill_entity_info($rowset, $id_fieldname, $entityname, $cache_prefix, 'sp_invoice_get_list_by_ids', array('invoices' => '', 'invoice' => 'id'), array());
    }
}