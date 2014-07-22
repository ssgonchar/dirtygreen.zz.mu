<?php

class OCStandard extends Model
{
    public function OCStandard()
    {
        Model::Model('oc_standards');
    }

    /**
     * Возвращает список oc_standards
     * @return array
     * 
     * @version 210130215, d10n
     */
    public function GetList()
    {
        $hash       = 'oc_standards';
        $cache_tags = array($hash);

        $rowset = $this->_get_cached_data($hash, 'sp_oc_standard_get_list', array(), $cache_tags);
        $rowset = isset($rowset[0]) ? $this->FillOCStandardInfo($rowset[0]) : array();

        return $rowset;
    }
    
    /**
     * Получает ID по title oc_standard
     * 
     * @param string $title
     * @return int
     * 
     * @version 210130215, d10n
     */
    public function GetOCStandardId($title)
    {
        if (empty($title)) return 0;
        
        $oc_standard = $this->GetByTitle($title);
        
        if (isset($oc_standard)) return $oc_standard['oc_standard']['id'];
        
        $result = $this->Save(0, $title);
        return $result['id'];
    }    
    
    /**
     * Возвращает oc_standard по ID
     * 
     * @param mixed $id
     * @version 210130215, d10n
     */
    public function GetById($id)
    {
        $dataset = $this->FillOCStandardInfo(array(array('oc_standard_id' => $id)));
        return isset($dataset) && isset($dataset[0]) && isset($dataset[0]['oc_standard']) ? $dataset[0] : null;
    }
    
    /**
     * Возвращает oc_standard по названию
     * 
     * @param mixed $title
     * @version 210130215, d10n
     */
    public function GetByTitle($title)
    {
        $title = trim($title);

        $result = $this->CallStoredProcedure('sp_oc_standard_get_by_title', array($title));
        return isset($result) && isset($result[0]) && isset($result[0][0]) && isset($result[0][0]['oc_standard_id']) ? $this->GetById($result[0][0]['oc_standard_id']) : null;
    }
    
    /**
     * Возвращет информацию о oc_standard
     * 
     * @param mixed $rowset
     * @param mixed $id_fieldname
     * @param mixed $entityname
     * @param mixed $cache_prefix
     * 
     * @version 210130215, d10n
     */
    public function FillOCStandardInfo($rowset, $id_fieldname = 'oc_standard_id', $entityname = 'oc_standard', $cache_prefix = 'oc_standard')
    {
        return $this->_fill_entity_info($rowset, $id_fieldname, $entityname, $cache_prefix, 'sp_oc_standard_get_list_by_ids', array('oc_standard' => '', 'oc_standard' => 'id'), array());
    }
    
    /**
     * Сохраняет oc_standard
     * 
     * @param int $id
     * @param string $title
     * @param mixed $description
     * 
     * @version 210130215, d10n
     */
    public function Save($id, $title)
    {        
        $title  = trim($title);
        
        $result = $this->CallStoredProcedure('sp_oc_standard_save', array($this->user_id, $id, $title));
        $result = isset($result) && isset($result[0]) && isset($result[0][0]) ? $result[0][0] : null;
        
        if (empty($result) || array_key_exists('ErrorCode', $result)) return null;

        Cache::ClearTag('oc_standard-' . $result['id']);
        Cache::ClearTag('oc_standards');
        
        return $result;
    }
}