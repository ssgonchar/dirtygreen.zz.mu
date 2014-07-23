<?php
require_once APP_PATH . 'classes/models/order.class.php';
require_once APP_PATH . 'classes/models/steelitem.class.php';

define('SUPINVOICE_STATUS_PPAID',      1);
define('SUPINVOICE_STATUS_PAID',       2);
define('SUPINVOICE_STATUS_CANCELLED',  3);

define('SUPINVOICE_PAYMENT_IDD',       1);
define('SUPINVOICE_PAYMENT_EOM',       2);

class SupplierInvoice extends Model
{
    public function SupplierInvoice()
    {
        Model::Model('supplier_invoices');
    }
    
    /**
     * Возвращает данные по ID записи
     * 
     * @param int $id
     * @return array
     * 
     * @version 20130129, zharkov
     */
    public function GetById($id)
    {
        $rowset = $this->FillSupplierInvoiceInfo(array(array('supplier_invoice_id' => $id)));
        return isset($rowset) && isset($rowset[0]) && isset($rowset[0]['supinvoice']) ? $rowset[0] : null;
    }
    
    /**
    * Сохраняет инвойс
    * 
    * @param mixed $id
    * @param mixed $number
    * @param mixed $date
    * @param mixed $company_id
    * @param mixed $owner_id
    * @param mixed $payment_type
    * @param mixed $payment_days
    * @param mixed $status_id
    * @param mixed $amount
    * @return resource
    * 
    * @version 20130129, zharkov
    */
    public function Save($id, $number, $date, $company_id, $owner_id, $delivery_point_id, $payment_type, $payment_days, $status_id, $amount_paid, $percent, $currency, $notes)
    {
        $rowset = $this->CallStoredProcedure('sp_supplier_invoice_save', array($this->user_id, $id, $number, $date, $company_id, $owner_id, $delivery_point_id, $payment_type, $payment_days, $status_id, $amount_paid, $percent, $currency, $notes));
        $rowset = isset($rowset) && isset($rowset[0]) && isset($rowset[0][0]) ? $rowset[0][0] : null;
        
        if (empty($rowset) || array_key_exists('ErrorCode', $rowset)) 
        {
            Log::AddLine(LOG_ERROR, 'sp_supplier_invoice_save : ' . var_export($rowset, true));
            return null;
        }
        
        Cache::ClearTag('supinvoices');
        if ($id > 0) Cache::ClearTag('supinvoice-' . $id);

        return $rowset;
    }

    /**
    * Добавляет айтем в счет
    * 
    * @param mixed $supplier_invoice_id
    * @param mixed $steelitem_id
    * @param mixed $purchase_price
    * @return mixed
    * 
    * @version 20130129, zharkov
    */
    public function SaveItem($supplier_invoice_id, $steelitem_id, $purchase_price, $weight_invoiced = 0)
    {
        $rowset = $this->CallStoredProcedure('sp_supplier_invoice_save_item', array($this->user_id, $supplier_invoice_id, $steelitem_id, $purchase_price, $weight_invoiced));
        
        Cache::ClearTag('supinvoice-' . $supplier_invoice_id);
        Cache::ClearTag('supinvoice-' . $supplier_invoice_id . '-items');
        
        Cache::ClearTag('steelitem-' . $steelitem_id);
        
        return $rowset;
    }
    
    /**
    * Удаляет айтем из счета
    * 
    * @param mixed $supplier_invoice_id
    * @param mixed $steelitem_id
    * 
    * @version 20130129, zharkov
    */
    public function RemoveItem($supplier_invoice_id, $steelitem_id)
    {
        $this->CallStoredProcedure('sp_supplier_invoice_remove_item', array($this->user_id, $supplier_invoice_id, $steelitem_id));
        
        Cache::ClearTag('supinvoice-' . $supplier_invoice_id);
        Cache::ClearTag('supinvoice-' . $supplier_invoice_id . '-items');
        
        Cache::ClearTag('steelitem-' . $steelitem_id);
        
        return true;
    }
    
    /**
    * Возвращает список айтемов
    * 
    * @param mixed $supplier_invoice_id
    * 
    * @version 20130129, zharkov
    */
    public function GetItems($supplier_invoice_id)
    {
        $hash       = 'supinvoice-' . $supplier_invoice_id . '-items';
        $cache_tags = array($hash, 'supinvoices', 'supinvoice-' . $supplier_invoice_id);

        $rowset         = $this->_get_cached_data($hash, 'sp_supplier_invoice_get_items', array($this->user_id, $supplier_invoice_id), $cache_tags);

        $modelSteelItem = new SteelItem();
        $rowset         = isset($rowset[0]) ? $modelSteelItem->FillSteelItemInfo($rowset[0]) : array();
        
        return $rowset;
    }
    
    /**
    * Список счетов
    *     
    * @param mixed $owner_id
    * @param mixed $company_id
    * @param mixed $date_from
    * @param mixed $date_to
    * @param mixed $number
    * @param mixed $page_no
    * @param mixed $per_page
    * @return mixed
    * 
    * @version 20130129, zharkov
    */
    public function GetList($owner_id, $company_id, $date_from, $date_to, $number, $page_no = 0, $per_page = ITEMS_PER_PAGE)
    {
        $page_no    = $page_no > 0 ? $page_no : 1;
        $per_page   = $per_page < 1 ? ITEMS_PER_PAGE : $per_page;
        $start      = ($page_no - 1) * $per_page;

        $hash       = 'supinvoices' . md5('-owner' . $owner_id . '-company' . $company_id . '-date_from' . $date_from . '-date_to' . $date_to . '-number' . $number . '-page_no' . $page_no . '-start' . $start);
        $cache_tags = array($hash, 'supinvoices');
        
        $data_set   = $this->_get_cached_data($hash, 'sp_supplier_invoice_get_list', array($owner_id, $company_id, $date_from, $date_to, $number, $start, $per_page), $cache_tags);
        
        if (!isset($data_set[0])) return array('data' => array(), 'count' => 0);
        
        $list       = $this->FillSupplierInvoiceInfo($data_set[0]);
        $rows_count = (isset($data_set[1]) && isset($data_set[1][0]) && isset($data_set[1][0]['rows_count'])) ? $data_set[1][0]['rows_count'] : 0;
        
        return array('data' => $list, 'count' => $rows_count);
    }
    
    /**
     * Удаляет счет
     * 
     * @param mixed $supplier_invoice_id
     * 
     * @version 20130129, zharkov
     */
    public function Remove($supplier_invoice_id)
    {
        $this->CallStoredProcedure('sp_supplier_invoice_remove', array($supplier_invoice_id));
        
        Cache::ClearTag('supinvoices');
        Cache::ClearTag('supinvoice-' . $supplier_invoice_id);
        Cache::ClearTag('supinvoice-' . $supplier_invoice_id . '-items');
        
        return true;
    }
    
    /**
     * Наполняет счет данными
     * 
     * @param int $rowset
     * @param mixed $id_fieldname
     * @param mixed $entityname
     * @param mixed $cache_prefix
     * @return int
     * 
     * @version 20130129, zharkov
     */
    public function FillSupplierInvoiceMainInfo($rowset, $id_fieldname = 'supplier_invoice_id', $entityname = 'supinvoice', $cache_prefix = 'supinvoice')
    {
        $rowset = $this->_fill_entity_info($rowset, $id_fieldname, $entityname, $cache_prefix, 'sp_supplier_invoice_get_list_by_ids', array('supinvoices' => '', 'supinvoice' => 'id'), array());

        foreach ($rowset as $key => $row)
        {
            if (!isset($row[$entityname])) continue;
            
            $row    = $row[$entityname];
            $doc_no = empty($row['number']) ? 'SupINV # ' . $row['id'] : $row['number'];
            
            $rowset[$key][$entityname]['doc_no']        = $doc_no;
            $rowset[$key][$entityname]['doc_no_full']   = $doc_no . ($row['date'] > 0 ? ' dd ' . date('d/m/Y', strtotime($row['date'])) : '');
        }
        
        return $rowset;        
    }
    
    /**
     * Наполняет счет основными данными
     * 
     * @param mixed $rowset
     * @param mixed $id_fieldname
     * @param mixed $entityname
     * @param mixed $cache_prefix
     * @return mixed
     * 
     * @version 20130129, zharkov
     */
    private function FillSupplierInvoiceInfo($rowset, $id_fieldname = 'supplier_invoice_id', $entityname = 'supinvoice', $cache_prefix = 'supinvoice')
    {
        $rowset = $this->FillSupplierInvoiceMainInfo($rowset, $id_fieldname, $entityname, $cache_prefix);

        foreach ($rowset as $key => $row)
        {
            if (isset($row[$entityname]))
            {
                $row = $row[$entityname];
               
                $rowset[$key][$entityname]['date']          = !empty($row['date']) && $row['date'] > 0 ? $row['date'] : '';
                $rowset[$key]['supinvoice_company_id']      = $row['company_id'];
                $rowset[$key]['supinvoice_owner_id']        = $row['owner_id'];
                $rowset[$key]['supinvoice_delivery_point']  = $row['delivery_point'];
                $rowset[$key]['supinvoice_author_id']       = $row['created_by'];
                $rowset[$key]['supinvoice_modifier_id']     = $row['modified_by'];
            }
        }
        
        $modelCompany   = new Company();
        $rowset         = $modelCompany->FillCompanyInfoShort($rowset, 'supinvoice_company_id', 'supinvoice_company');
        $rowset         = $modelCompany->FillCompanyInfoShort($rowset, 'supinvoice_owner_id', 'supinvoice_owner');
        
        $modelUser      = new User();
        $rowset         = $modelUser->FillUserInfo($rowset, 'supinvoice_author_id', 'supinvoice_author');
        $rowset         = $modelUser->FillUserInfo($rowset, 'supinvoice_modifier_id', 'supinvoice_modifier');
        
        foreach ($rowset as $key => $row)
        {            
            if (isset($row['supinvoice_company']) && !empty($row['supinvoice_company']))
            {
                $rowset[$key][$entityname]['company'] = $row['supinvoice_company'];
            }
            unset($rowset[$key]['supinvoice_company_id']);
            unset($rowset[$key]['supinvoice_company']);
            
            if (isset($row['supinvoice_owner']) && !empty($row['supinvoice_owner']))
            {
                $rowset[$key][$entityname]['owner'] = $row['supinvoice_owner'];
            }
            unset($rowset[$key]['supinvoice_owner_id']);
            unset($rowset[$key]['supinvoice_owner']);            

            if (isset($row['supinvoice_author']) && !empty($row['supinvoice_author']))
            {
                $rowset[$key][$entityname]['author'] = $row['supinvoice_author'];
            }
            unset($rowset[$key]['supinvoice_author_id']);
            unset($rowset[$key]['supinvoice_author']);

            if (isset($row['supinvoice_modifier']) && !empty($row['supinvoice_modifier']))
            {
                $rowset[$key][$entityname]['modifier'] = $row['supinvoice_modifier'];
            }
            unset($rowset[$key]['supinvoice_modifier_id']);
            unset($rowset[$key]['supinvoice_modifier']);
            
            if (isset($row[$entityname]))
            {
                $row            = $row[$entityname];
                $status_title   = '';
                
                if ($row['status_id'] == SUPINVOICE_STATUS_PPAID)
                {
                    $status_title = 'Partially Paid';
                }
                else if ($row['status_id'] == SUPINVOICE_STATUS_PAID)
                {
                    $status_title = 'Paid';
                }
                else if ($row['status_id'] == SUPINVOICE_STATUS_CANCELLED)
                {
                    $status_title = 'Credited';
                }
                
                $rowset[$key][$entityname]['status_title'] = $status_title;
            }
        }
        //debug('1671', $rowset);
        return $rowset;        
    }
    
    /**
     * Добавляет атачмент документа к айтемам
     * 
     * @param mixed $object_id
     */
    function LinkAttachmentToItems($supplierinvoice_id)
    {
        foreach ($this->GetItems($supplierinvoice_id) as $item)
        {
            $this->CallStoredProcedure('sp_supplier_invoice_link_attachments_to_item', array($this->user_id, $supplierinvoice_id, $item['steelitem_id']));
            Cache::ClearTag('attachments-aliassupplierinvoice-id' . $supplierinvoice_id);
        }        
    }
}
