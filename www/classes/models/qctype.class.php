<?php

class QCType extends Model
{
    function QCType()
    {
        Model::Model('qctypes');
    }

    /**
     * Возвращает список qctypes
     * 
     */
    function GetList()
    {
        $hash       = 'qctypes';
        $cache_tags = array($hash);

        $rowset = $this->_get_cached_data($hash, 'sp_qctype_get_list', array(), $cache_tags);
        $rowset = isset($rowset[0]) ? $this->FillQCTypeInfo($rowset[0]) : array();

        return $rowset;        
    }
    
    /**
     * Получает id qctype
     * 
     * @param mixed $title
     * @return mixed
     */
    function GetQCTypeId($title)
    {
        if (empty($title)) return 0;
        
        $qctype = $this->GetByTitle($title);
        
        if (isset($qctype)) return $qctype['qctype']['id'];
        
        $result = $this->Save(0, $title);
        return $result['id'];
    }    
    
    /**
     * Возвращает qctype по идентификатору
     *     
     * @param mixed $id
     */
    function GetById($id)
    {
        $dataset = $this->FillQCTypeInfo(array(array('qctype_id' => $id)));
        return isset($dataset) && isset($dataset[0]) && isset($dataset[0]['qctype']) ? $dataset[0] : null;
    }
    
    /**
     * Возвращает qctype по названию
     *     
     * @param mixed $title
     */
    function GetByTitle($title)
    {
        $title = trim($title);

        $result = $this->CallStoredProcedure('sp_qctype_get_by_title', array($title));
        return isset($result) && isset($result[0]) && isset($result[0][0]) && isset($result[0][0]['id']) ? $this->GetById($result[0][0]['id']) : null;
    }
    
    /**
     * Возвращет информацию о qctype
     * 
     * @param mixed $rowset
     * @param mixed $id_fieldname
     * @param mixed $entityname
     * @param mixed $cache_prefix
     */
    function FillQCTypeInfo($rowset, $id_fieldname = 'qctype_id', $entityname = 'qctype', $cache_prefix = 'qctype')
    {
        return $this->_fill_entity_info($rowset, $id_fieldname, $entityname, $cache_prefix, 'sp_qctype_get_list_by_ids', array('qctypes' => '', 'qctype' => 'id'), array());
    }
    
    /**
     * Сохраняет qctype
     * 
     * @param mixed $id
     * @param mixed $title
     * @param mixed $description
     */
    function Save($id, $title)
    {        
        $title  = trim($title);
        
        $result = $this->CallStoredProcedure('sp_qctype_save', array($this->user_id, $id, $title));
        $result = isset($result) && isset($result[0]) && isset($result[0][0]) ? $result[0][0] : null;
        
        if (empty($result) || array_key_exists('ErrorCode', $result)) return null;

        Cache::ClearTag('qctype-' . $result['id']);
        Cache::ClearTag('qctypes');
        
        return $result;
    }    
}
