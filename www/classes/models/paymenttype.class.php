<?php

class PaymentType extends Model
{
    function PaymentType()
    {
        Model::Model('paymenttypes');
    }

    /**
     * Возвращает список invoicingtypes
     * 
     */
    function GetList()
    {
        $hash       = 'paymenttypes';
        $cache_tags = array($hash);

        $rowset = $this->_get_cached_data($hash, 'sp_paymenttype_get_list', array(), $cache_tags);
        $rowset = isset($rowset[0]) ? $this->FillPaymentTypeInfo($rowset[0]) : array();

        return $rowset;        
    }
    
    /**
     * Получает id paymenttype
     * 
     * @param mixed $title
     * @return mixed
     */
    function GetPaymentTypeId($title)
    {
        if (empty($title)) return 0;
        
        $paymenttype = $this->GetByTitle($title);

        if (isset($paymenttype)) return $paymenttype['paymenttype']['id'];
        
        $result = $this->Save(0, $title);
        return $result['id'];
    }    
    
    /**
     * Возвращает paymenttype по идентификатору
     *     
     * @param mixed $id
     */
    function GetById($id)
    {
        $dataset = $this->FillPaymentTypeInfo(array(array('paymenttype_id' => $id)));
        return isset($dataset) && isset($dataset[0]) && isset($dataset[0]['paymenttype']) ? $dataset[0] : null;
    }
    
    /**
     * Возвращает paymenttype по названию
     *     
     * @param mixed $title
     */
    function GetByTitle($title)
    {
        $title = trim($title);

        $result = $this->CallStoredProcedure('sp_paymenttype_get_by_title', array($title));
        return isset($result) && isset($result[0]) && isset($result[0][0]) && isset($result[0][0]['paymenttype_id']) ? $this->GetById($result[0][0]['paymenttype_id']) : null;
    }
    
    /**
     * Возвращет информацию о paymenttype
     * 
     * @param mixed $rowset
     * @param mixed $id_fieldname
     * @param mixed $entityname
     * @param mixed $cache_prefix
     */
    function FillPaymentTypeInfo($rowset, $id_fieldname = 'paymenttype_id', $entityname = 'paymenttype', $cache_prefix = 'paymenttype')
    {
        return $this->_fill_entity_info($rowset, $id_fieldname, $entityname, $cache_prefix, 'sp_paymenttype_get_list_by_ids', array('paymenttypes' => '', 'paymenttype' => 'id'), array());
    }
    
    /**
     * Сохраняет paymenttype
     * 
     * @param mixed $id
     * @param mixed $title
     * @param mixed $description
     */
    function Save($id, $title)
    {        
        $result = $this->CallStoredProcedure('sp_paymenttype_save', array($this->user_id, $id, $title));
        $result = isset($result) && isset($result[0]) && isset($result[0][0]) ? $result[0][0] : null;
        
        if (empty($result) || array_key_exists('ErrorCode', $result)) return null;

        Cache::ClearTag('paymenttype-' . $result['id']);
        Cache::ClearTag('paymenttypes');
        
        return $result;
    }    
}
