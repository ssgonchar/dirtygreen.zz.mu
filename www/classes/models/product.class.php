<?php
require_once APP_PATH . 'classes/models/team.class.php';

class Product extends Model
{
    function Product()
    {
        Model::Model('products');
    }

    /**
     * Возвращает дерево товаров
     * @version 20130518, sasha add $in_biz
     * @param mixed $team_id
     * @param mixed $nesting - означает нужно ли организовывать вложенность дочерних элементов
     * @param mixed $parent_id - ветка дерева продуктов, "-1" все ветки
	 * @param mixed $in_biz - list from bizes
     * @return отсортированный
     */
    function GetTree($team_id = 0, $nesting = false, $parent_id = -1, $in_biz = false)
    {
        if (!$in_biz)
		{	
			$tree = $this->_sort_tree($this->GetList($team_id, $parent_id));        
		}
		else
		{
			$tree = $this->_sort_tree($this->GetListFromBizes($team_id, $parent_id));
		}	
		
		if (!$nesting) return $tree;

        $result = array();
        $key0   = 0;
        foreach ($tree as $key => $row)
        {
            if ($row['product']['level'] == 0)
            {
                $key0           = $key;
                $result[$key0]  = $row;
            }
            else
            {
                $result[$key0]['product']['children'][] = $row;
            }
        }
        
        return $result;
    }
    
    /**
     * Возвращет список без ветки
     * @version 20130518, sasha add in_biz
     * @param mixed $node_id
     */
    function GetTreeWithoutNode($team_id, $node_id, $in_biz = false)
    {
        $list = $this->GetTree($team_id, false, -1, $in_biz);
        
        if ($node_id > 0)
        {
            $except_nodes[] = $node_id;
            
            foreach ($list as $key => $node)
            {
                $node = $node['product'];
                
                if ($node['id'] == $node_id) 
                {
                    unset($list[$key]);
                    continue;
                }
                
                if (in_array($node['parent_id'], $except_nodes))
                {
                    $except_nodes[] = $node['id'];
                    unset($list[$key]);
                }
            }
        }

        return $list;        
    }    
    
    /**
     * Возвращает список продуктов
     * 
     * @param mixed $team_id - принадлежность к команде
     * @param mixed $parent_id - ветка дерева продуктов, "-1" все ветки
     */
    function GetList($team_id = 0, $parent_id = -1)
    {
        $hash       = 'products-team_id-' . $team_id . '-parent_id-' . $parent_id;
        $cache_tags = array($hash, 'products');

        $rowset = $this->_get_cached_data($hash, 'sp_product_get_list', array($team_id, $parent_id), $cache_tags);
        $rowset = isset($rowset[0]) ? $this->FillProductInfo($rowset[0]) : array();
        
        foreach ($rowset as $key => $row)
        {
            $row = $row['product'];
            
            $rowset[$key]['id']         = $row['id'];
            $rowset[$key]['parent_id']  = $row['parent_id'];
            
            $prefix = '';
            for($i = 0; $i < $row['level']; $i++) $prefix .= '&middot;&nbsp;';
            $rowset[$key]['product']['title_list']  = $prefix . $row['title'];
        }        

        return $rowset;
    }
	
	/**
	 * @version 20130518, Sasha
	 * product list for team from bizes
	 * @param type $team_id
	 * @param type $parent_id
	 * @return null|string
	 */
	function GetListFromBizes($team_id, $parent_id = -1)
	{
		if ($team_id < 0) return null;
		
		$hash       = 'products-team_id-' . $team_id . '-parent_id-' . $parent_id . '-bizes';
        $cache_tags = array($hash, 'products');
		
		$rowset = $this->_get_cached_data($hash, 'sp_product_get_list_from_bizes', array($team_id, $parent_id), $cache_tags);
		$rowset = isset($rowset[0]) ? $this->FillProductInfo($rowset[0]) : array();
        
        foreach ($rowset as $key => $row)
        {
            $row = $row['product'];
            
            $rowset[$key]['id']         = $row['id'];
            $rowset[$key]['parent_id']  = $row['parent_id'];
            
            $prefix = '';
            for($i = 0; $i < $row['level']; $i++) $prefix .= '&middot;&nbsp;';
            $rowset[$key]['product']['title_list']  = $prefix . $row['title'];
        }        
	
		return $rowset;
	}

    /**
     * Возвращает product по идентификатору
     *     
     * @param mixed $id
     */
    function GetById($id)
    {
        $dataset = $this->FillProductInfo(array(array('product_id' => $id)));
        return isset($dataset) && isset($dataset[0]) && isset($dataset[0]['product']) ? $dataset[0] : null;
    }
    
    /**
     * Вовращает основную информацию о товаре
     * 
     * @param mixed $rowset
     * @param mixed $id_fieldname
     * @param mixed $entityname
     * @param mixed $cache_prefix
     */
    function FillProductMainInfo($rowset, $id_fieldname = 'product_id', $entityname = 'product', $cache_prefix = 'product')
    {
        return $this->_fill_entity_info($rowset, $id_fieldname, $entityname, $cache_prefix, 'sp_product_get_list_by_ids', array('products' => ''), array());
    }

    /**
     * Возвращет информацию о product
     * 
     * @param mixed $rowset
     * @param mixed $id_fieldname
     * @param mixed $entityname
     * @param mixed $cache_prefix
     */
    function FillProductInfo($rowset, $id_fieldname = 'product_id', $entityname = 'product', $cache_prefix = 'product')
    {
        $rowset = $this->FillProductMainInfo($rowset, $id_fieldname, $entityname, $cache_prefix);
        
        foreach ($rowset as $key => $row)
        {
            if (!isset($row[$entityname])) continue;
            $row = $row[$entityname];
            
            $rowset[$key]['team_id'] = $row['team_id'];
            if ($row['parent_id'] > 0) $rowset[$key]['parent_id'] = $row['parent_id'];
            
            $rowset[$key][$entityname]['doc_no'] = $row['title'];
        }
        
        $teams  = new Team();
        $rowset = $teams->FillTeamMainInfo($rowset);
        $rowset = $this->FillParentProductInfo($rowset, 'parent_id', 'parent');

        foreach ($rowset as $key => $row)
        {
            if (isset($row['team']))
            {
                $rowset[$key][$entityname]['team'] = $row['team'];
                unset($rowset[$key]['team']);
            }
            
            if (isset($row['parent']))
            {
                $rowset[$key][$entityname]['parent'] = $row['parent'];
                unset($rowset[$key]['parent']);                
            }
            
            unset($rowset[$key]['team_id']);
            unset($rowset[$key]['parent_id']);
        }        
        
        return $rowset;
    }
    
    /**
    * put your comment there...
    * 
    * @param mixed $rowset
    * @param mixed $id_fieldname
    * @param mixed $entityname
    * @param mixed $cache_prefix
    */
    function FillParentProductInfo($rowset, $id_fieldname = 'product_id', $entityname = 'product', $cache_prefix = 'product')
    {
        return $this->_fill_entity_info($rowset, $id_fieldname, $entityname, $cache_prefix, 'sp_product_get_list_by_ids', array('products' => ''), array());
    }
        
    /**
     * Сохраняет product
     * 
     * @param mixed $id
     * @param mixed $title
     * @param mixed $description
     */
    function Save($id, $parent_id, $team_id, $title, $description)
    {        
        // проверка существования товара с таким же названием
        $alias = Translit::EncodeAndClear($title);        
        $alias  = md5(strtolower(preg_replace("#[^a-zA-Z0-9]+#", '', $alias)));
        
        $result = $this->CallStoredProcedure('sp_product_save', array($this->user_id, $id, $parent_id, $team_id, $title, $alias, $description));
        $result = isset($result) && isset($result[0]) && isset($result[0][0]) ? $result[0][0] : null;
        
        if (empty($result) || array_key_exists('ErrorCode', $result)) return null;

        Cache::ClearTag('product-' . $id);
        Cache::ClearTag('products');
        
        return $result;
    }
    
    /**
     * Удаляет product
     * 
     * @param mixed $id
     * @return resource
     */
    function Remove($id)
    {        
        $result = $this->CallStoredProcedure('sp_product_remove', array($this->user_id, $id));
        $result = isset($result) && isset($result[0]) && isset($result[0][0]) ? $result[0][0] : null;
        
        if (empty($result) || array_key_exists('ErrorCode', $result)) return null;

        Cache::ClearTag('product-' . $id);
        Cache::ClearTag('products');
        
        return $result;
    }    
    
    /**
     * Сохраняет тарифный код
     * 
     * @param mixed $product_id
     * @param mixed $code
     * @param mixed $description
     */
    function SaveTariffCode($id, $product_id, $code, $description)
    {
        $result = $this->CallStoredProcedure('sp_product_save_tariffcode', array($this->user_id, $id, $product_id, trim($code), trim($description)));
        $result = isset($result) && isset($result[0]) && isset($result[0][0]) ? $result[0][0] : null;
        
        Cache::ClearTag('product-' . $product_id . '-tariffcodes');
        
        if (empty($result) || array_key_exists('ErrorCode', $result)) return null;        
        return $result;        
    }

    /**
     * Удаляет тарифный код
     * 
     * @param mixed $id
     * @return resource
     */
    function RemoveTariffCode($id, $product_id)
    {
        $result = $this->CallStoredProcedure('sp_product_remove_tariffcode', array($this->user_id, $id));
        $result = isset($result) && isset($result[0]) && isset($result[0][0]) ? $result[0][0] : null;
        
        Cache::ClearTag('product-' . $product_id . '-tariffcodes');
        
        if (empty($result) || array_key_exists('ErrorCode', $result)) return null;                
        return $result;        
    }
    
    /**
     * Возвращает список тарифных кодов для товара
     *     
     * @param mixed $product_id
     */
    function GetTariffCodes($product_id)
    {
        $hash       = 'product-' . $product_id . '-tariffcodes';
        $cache_tags = array($hash, 'product-' . $product_id, 'products');

        $rowset     = $this->_get_cached_data($hash, 'sp_product_get_tariffcodes', array($this->user_id, $product_id), $cache_tags);
        return isset($rowset[0]) ? $rowset[0] : array();
    }
    
    function UpdateAlias()
    {
        foreach ($this->GetList() as $row)
        {
            $row    = $row['product'];

            $alias  = Translit::EncodeAndClear($row['title']);
            $alias  = md5(strtolower(preg_replace("#[^a-zA-Z0-9]+#", '', $alias)));
            
            $this->Update($row['id'], array('alias' => $alias));
        }
    }
    
    /**
     * Возвращает список отфильтрованный по ключевому слову<br />
     * 
     * @param string $keyword [VARCHAR(20)]
     * @param int $rows_count Количество записей
     * 
     * @version 20121204, d10n
     */
    public function GetListByKeyword($keyword, $rows_count)
    {
        $hash       = 'product-keyword-' . $keyword . '-rowscount-' . $rows_count;
        $cache_tags = array($hash, 'products');

        $rowset = $this->_get_cached_data($hash, 'sp_product_get_list_by_keyword', array($keyword, $rows_count), $cache_tags);
        $rowset = isset($rowset[0]) ? $this->FillProductInfo($rowset[0]) : array();
        
        return $rowset;
    }
	
	/**
	 * @version 20130521, sasha
	 * get child for product
	 * @param type $parent_id
	 * @return null
	 */
	function GetBranch($parent_id)
	{
		if ($parent_id < 0) return null;
		
		$hash       = 'product-' . $parent_id . '-branch';
        $cache_tags = array($hash, 'products');
		
		$rowset = $this->_get_cached_data($hash, 'sp_product_get_branch', array($parent_id), $cache_tags);
        
        return isset($rowset[0]) && !empty($rowset[0]) ? $rowset[0] : null;
	}
}