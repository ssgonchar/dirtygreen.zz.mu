<?php

class InvoicingType extends Model
{
    function InvoicingType()
    {
        Model::Model('invoicingtypes');
    }

    /**
     * Возвращает список invoicingtypes
     * 
     */
    function GetList()
    {
        $hash       = 'invoicingtypes';
        $cache_tags = array($hash);

        $rowset = $this->_get_cached_data($hash, 'sp_invoicingtype_get_list', array(), $cache_tags);
        $rowset = isset($rowset[0]) ? $this->FillInvoicingTypeInfo($rowset[0]) : array();

        return $rowset;        
    }
    
    /**
     * Получает id invoicingtype
     * 
     * @param mixed $title
     * @return mixed
     */
    function GetInvoicingTypeId($title)
    {
        if (empty($title)) return 0;
        
        $invoicingtype = $this->GetByTitle($title);
        
        if (isset($invoicingtype)) return $invoicingtype['invoicingtype']['id'];
        
        $result = $this->Save(0, $title);
        return $result['id'];
    }    
    
    /**
     * Возвращает invoicingtype по идентификатору
     *     
     * @param mixed $id
     */
    function GetById($id)
    {
        $dataset = $this->FillInvoicingTypeInfo(array(array('invoicingtype_id' => $id)));
        return isset($dataset) && isset($dataset[0]) && isset($dataset[0]['invoicingtype']) ? $dataset[0] : null;
    }
    
    /**
     * Возвращает invoicingtype по названию
     *     
     * @param mixed $title
     */
    function GetByTitle($title)
    {
        $title = trim($title);

        $result = $this->CallStoredProcedure('sp_invoicingtype_get_by_title', array($title));
        return isset($result) && isset($result[0]) && isset($result[0][0]) && isset($result[0][0]['invoicingtype_id']) ? $this->GetById($result[0][0]['invoicingtype_id']) : null;
    }
    
    /**
     * Возвращет информацию о invoicingtype
     * 
     * @param mixed $rowset
     * @param mixed $id_fieldname
     * @param mixed $entityname
     * @param mixed $cache_prefix
     */
    function FillInvoicingTypeInfo($rowset, $id_fieldname = 'invoicingtype_id', $entityname = 'invoicingtype', $cache_prefix = 'invoicingtype')
    {
        return $this->_fill_entity_info($rowset, $id_fieldname, $entityname, $cache_prefix, 'sp_invoicingtype_get_list_by_ids', array('invoicingtypes' => '', 'invoicingtype' => 'id'), array());
    }
    
    /**
     * Сохраняет invoicingtype
     * 
     * @param mixed $id
     * @param mixed $title
     * @param mixed $description
     */
    function Save($id, $title)
    {        
        $title  = trim($title);
        
        $result = $this->CallStoredProcedure('sp_invoicingtype_save', array($this->user_id, $id, $title));
        $result = isset($result) && isset($result[0]) && isset($result[0][0]) ? $result[0][0] : null;
        
        if (empty($result) || array_key_exists('ErrorCode', $result)) return null;

        Cache::ClearTag('invoicingtype-' . $result['id']);
        Cache::ClearTag('invoicingtypes');
        
        return $result;
    }    
}
