<?php
class NomenclatureCategory extends Model
{
    function NomenclatureCategory()
    {
        Model::Model('nomenclaturecategory');
    }
    
    
    /*
     * Возвращает номенклатуру по page_alias
     * @param $page_alias
     * 
     * @version 20140619, uskov
     */
    function Search($page_alias)
    {
        //$page_alias     = Request::GetString('page_alias', $_REQUEST);
        $list = $this->GetSortedList();
        
        debug("1682", $list);
        foreach ($list as $key => $row)
        {
            foreach($row as $sub_key => $sub_row)
            {
                if($sub_row["page_alias"] == $page_alias){
                    $current_id = $sub_row["id"];
                }
            }
        }
        $current_nomenclature = $this->GetById($current_id);
        return $current_nomenclature;
        //debug("1682", $current_nomenclature);
    }
    
    /**
     * ���������� ������ ���������
     * 
     */
    function GetList()
    {
      $hash       = 'category';	//���� ���� = 'category'
      $cache_tags = array($hash);		//���� ����� ���� (��� ���?)
	
      $rowset = $this->_get_cached_data($hash, 'sp_category_get_list', array(), $cache_tags);
        
		$rowset = isset($rowset[0]) ? $this->FillCategoryInfo($rowset[0]) : array();
		return $rowset;
    }
	 
    /**
     * ��������� ������, ������������ ������� GetList()
     * 
     * @param 
     */
    function GetSortedList()
    {
        $rowset = $this->GetList();

        foreach ($rowset as $key => $row)	//��������� ������ $rowset
        {
                    if($rowset[$key]['category']['parent_id']==0) {
                             $category[] = $row;
                    } elseif($rowset[$key]['category']['parent_id']>0) {
                             $sub_category[] = $row;
                    }
        }
        //dg($category);
        foreach($category as $key => &$row)
        {
            //print_r($category[$key]['category_id'].'<br>');
            foreach($sub_category as $sub_key => $sub_row)
            {
                //print_r($sub_category[$sub_key]['category']['parent_id'].'<br>');
                if($category[$key]['category_id']==$sub_category[$sub_key]['category']['parent_id']) 
                {
                        $category[$key]['sub_categories'][]=$sub_category[$sub_key];
                        //print_r($sub_category[$sub_key]['category']['parent_id']);
                }
            }	
        }

       return $category;
    }
	  
	  /**
     * ���������� ��������� �� ��������������
     * 
     * @param mixed $category_id
     */
    function GetById($id)
    {
        $dataset = $this->FillCategoryInfo(array(array('category_id' => $id)));
        return isset($dataset) && isset($dataset[0]) && isset($dataset[0]['category']) ? $dataset[0] : null;        
    }
    
   /**
     * ��������� ������ ���������
     * 
     * @param mixed $rowset
     * @param mixed $id_fieldname
     * @param mixed $entityname
     * @param mixed $cache_prefix
     */
    function FillCategoryInfo($rowset, $id_fieldname = 'category_id', $entityname = 'category', $cache_prefix = 'category')
    {
                 return $this->_fill_entity_info($rowset, $id_fieldname, $entityname, $cache_prefix, 'sp_category_get_list_by_ids', array('category' => ''), array());
    }
   
    function Save($id, $description)
    {        
        $result = $this->CallStoredProcedure('sp_nomenclature_save', array($this->user_id, $id, $description));
        $result = isset($result) && isset($result[0]) && isset($result[0][0]) ? $result[0][0] : null;
        
        if (empty($result) || array_key_exists('ErrorCode', $result)) return null;

        Cache::ClearTag('nomenclature-' . $result['id']);
        Cache::ClearTag('nomenclature');
        
        return $result;
    }
}
