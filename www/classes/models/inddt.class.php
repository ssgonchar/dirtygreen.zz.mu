<?php
require_once APP_PATH . 'classes/models/company.class.php';

class InDDT extends Model
{
    public function InDDT()
    {
        Model::Model('in_ddt');
    }
    
    /**
     * Добавляет айтем в In DDT
     * 
     * @param int $inddt_id [INT]
     * @param int $steelitem_id [INT]
     * @return array
     * 
     * @version 20121212, d10n
     * @version 20121218, zharkov
     */
    public function SaveItem($inddt_id, $steelitem_id, $stockholder_id, $status_id, $steelposition_id = 0)
    {
        $rowset   = $this->CallStoredProcedure('sp_in_ddt_item_save', array($this->user_id, $inddt_id, $steelitem_id, $stockholder_id, $status_id));
        $rowset   = isset($rowset[0]) && isset($rowset[0][0]) ? $rowset[0][0] : array();
        
        Cache::ClearTag('inddt-' . $inddt_id);
        Cache::ClearTag('inddt-' . $inddt_id . '-items');        
        Cache::ClearTag('steelitem-' . $steelitem_id);
        
        if (isset($steelposition_id) && $steelposition_id > 0)
        {
            Cache::ClearTag('steelpositionquick-' . $steelposition_id);
            Cache::ClearTag('steelposition-' . $steelposition_id);            
        }
        
        Cache::ClearTag('reports');
    }
    
    /**
     * Удаляет айтем из In DDT
     * 
     * @param mixed $inddt_item_id
     * @return resource
     * 
     * @version 20121218, zharkov 
     */
    public function RemoveItem($inddt_item_id)
    {
        $rowset = $this->CallStoredProcedure('sp_in_ddt_item_remove', array($this->user_id, $inddt_item_id));
        $rowset = isset($rowset[0]) && isset($rowset[0][0]) ? $rowset[0][0] : array();
        
        if (empty($rowset) || isset($rowset['ErrorCode'])) return null;
        
        Cache::ClearTag('inddt-' . $rowset['in_ddt_id']);
        Cache::ClearTag('inddt-' . $rowset['in_ddt_id'] . '-items');
        
        Cache::ClearTag('steelitem-' . $rowset['steelitem_id']);
        Cache::ClearTag('reports');
        
        Cache::ClearTag('steelpositionquick-' . $rowset['steelposition_id']);        
        Cache::ClearTag('steelposition-' . $rowset['steelposition_id']);
        
        return $rowset;
    }
    
    /**
     * Возвращает список айтемов для конкретного документа
     * 
     * @param int $inddt_id [INT]
     * @return array
     * 
     * @version 20121212, d10n
     */
    public function GetItems($inddt_id)
    {
        $hash       = 'inddt-' . $inddt_id . '-items';
        $cache_tags = array($hash, 'inddts', 'inddt-' . $inddt_id);

        $rowset = $this->_get_cached_data($hash, 'sp_in_ddt_item_get_list', array($inddt_id), $cache_tags);
        
        $modelSteelItem = new SteelItem();
        $modelCompany   = new Company();
        return isset($rowset[0]) ? $modelCompany->FillCompanyInfoShort($modelSteelItem->FillSteelItemInfo($rowset[0]), 'stockholder_id', 'stockholder') : array();
    }
    

    /**
     * Сохраняет данные
     * 
     * @param int $id [INT]
     * @param string $number [VARCHAR(50)]
     * @param string $date [TIMESTAMP]
     * @param int $company_id [INT]
     * @param int $owner_id [INT]
     * @param int $status_id [TINYINT]
     * 
     * @return array
     * 
     * @version 20121212, d10n
     * @version 20121218, zharkov
     */
    public function Save($id, $number, $date, $company_id, $owner_id, $status_id)
    {
        $rowset = $this->CallStoredProcedure('sp_in_ddt_save', array($this->user_id, $id, $number, $date, $company_id, $owner_id, $status_id));
        $rowset = isset($rowset) && isset($rowset[0]) && isset($rowset[0][0]) ? $rowset[0][0] : array();
        
        if (isset($rowset['id']))
        {
            Cache::ClearTag('inddt-' . $rowset['id']);
            Cache::ClearTag('inddts');            
        }
        
        return $rowset;
    }
        
    /**
     * Удаляет In DDT
     * 
     * @param mixed $id
     * 
     * @version 20121218, zharkov
     */
    function Remove($id)
    {
        $result = $this->CallStoredProcedure('sp_in_ddt_remove', array($this->user_id, $id));
        $rowset = isset($rowset) && isset($rowset[0]) && isset($rowset[0][0]) ? $rowset[0][0] : array();
        
        if (isset($rowset['ErrorCode'])) return null;
                
        Cache::ClearTag('inddt-' . $id);
        Cache::ClearTag('inddts');
        
        foreach($this->GetItems($id) as $inddt_item)
        {
            $this->RemoveItem($inddt_item['id']);
        }
    }
    
    /**
     * Возвращает данные конкретной записи по ID
     * 
     * @param mixed $id
     * @return array
     * 
     * @version 20121212, d10n
     */
    public function GetById($id)
    {
        $dataset = $this->FillInDDTInfo(array(array('inddt_id' => $id)));
        return isset($dataset) && isset($dataset[0]) && isset($dataset[0]['inddt']) ? $dataset[0] : null;
    }
    
    /**
     * Возвращает базовый набор данных по объекту
     * 
     * @param array $rowset Набор IDs array(array('inddt_id' => 1), )
     * @param string $id_fieldname Название поля, в котором хранятся идентификаторы сущности (например inddt_id)
     * @param string $entityname Название сущности = имя выходного массива для каждой строки входного (например, 'inddt')
     * @param string $cache_prefix Префикс для выборки данных из кеша (например, 'inddt')
     * 
     * @version 20121102, d10n
     * @version 20121218, zharkov
     */
    public function FillInDDTMainInfo($rowset, $id_fieldname = 'inddt_id', $entityname = 'inddt', $cache_prefix = 'inddt')
    {
        $rowset = $this->_fill_entity_info($rowset, $id_fieldname, $entityname, $cache_prefix, 'sp_in_ddt_get_list_by_ids', array('inddts' => '', 'inddt' => 'id'), array());

        foreach ($rowset as $key => $row)
        {
            if (!isset($row[$entityname])) continue;
            
            $row    = $row[$entityname];
            $doc_no = empty($row['number']) ? 'InDDT # ' . $row['id'] : $row['number'];
            
            $rowset[$key][$entityname]['doc_no']        = $doc_no;
            $rowset[$key][$entityname]['doc_no_full']   = $doc_no . ($row['date'] > 0 ? ' dd ' . date('d/m/Y', strtotime($row['date'])) : '');
        }
        
        return $rowset;
    }
    
    /**
     * Возвращает расширенный набор данных по объекту
     * 
     * @param array $rowset Набор IDs array(array('inddt_id' => 1), )
     * @param string $id_fieldname Название поля, в котором хранятся идентификаторы сущности (например inddt_id)
     * @param string $entityname Название сущности = имя выходного массива для каждой строки входного (например, 'inddt')
     * @param string $cache_prefix Префикс для выборки данных из кеша (например, 'inddt')
     * 
     * @version 20121212, d10n
     */
    public function FillInDDTInfo($rowset, $id_fieldname = 'inddt_id', $entityname = 'inddt', $cache_prefix = 'inddt')
    {
        $rowset = $this->FillInDDTMainInfo($rowset, $id_fieldname, $entityname, $cache_prefix);
        
        foreach ($rowset as $key => $row)
        {
            if (!isset($row[$entityname])) continue;
            
            $row = $row[$entityname];
            
            $rowset[$key]['inddt_modifier_id']  = $row['modified_by'];
            $rowset[$key]['inddt_author_id']   = $row['created_by'];
            $rowset[$key]['inddt_company_id']   = $row['company_id'];
            $rowset[$key]['inddt_owner_id']     = $row['owner_id'];
            
        }

        $modelUser      = new User();
        $rowset         = $modelUser->FillUserInfo($rowset, 'inddt_modifier_id', 'inddt_modifier');
        $rowset         = $modelUser->FillUserInfo($rowset, 'inddt_author_id', 'inddt_author');
        
        $modelCompany   = new Company();
        $rowset         = $modelCompany->FillCompanyInfo($rowset, 'inddt_company_id', 'inddt_company');
        $rowset         = $modelCompany->FillCompanyInfo($rowset, 'inddt_owner_id', 'inddt_owner');
        
        foreach ($rowset as $key => $row)
        {
            if (!isset($row[$entityname])) continue;
            
            if (isset($row['inddt_modifier']))
            {
                $rowset[$key][$entityname]['modifier'] = $row['inddt_modifier'];
                unset($rowset[$key]['inddt_modifier']);
            }
            unset($rowset[$key]['inddt_modifier_id']);

            if (isset($row['inddt_author']))
            {
                $rowset[$key][$entityname]['author'] = $row['inddt_author'];
                unset($rowset[$key]['inddt_author']);
            }
            unset($rowset[$key]['inddt_author_id']);

            if (isset($row['inddt_company']))
            {
                $rowset[$key][$entityname]['company'] = $row['inddt_company'];
                unset($rowset[$key]['inddt_company']);
            }
            unset($rowset[$key]['inddt_company_id']);

            if (isset($row['inddt_owner']))
            {
                $rowset[$key][$entityname]['owner'] = $row['inddt_owner'];
                unset($rowset[$key]['inddt_owner']);
            }
            unset($rowset[$key]['inddt_owner_id']);
        }

        $modelAttachment = new Attachment();
        return $modelAttachment->FillObjectAttachments($rowset, $entityname, $id_fieldname);;
    }
    
    /**
     * Возвращает линейный список
     * 
     * @param int $page_no
     * @param int $per_page
     * @return array
     * 
     * @version 20121212, d10n
     */
    public function GetList($page_no = 0, $per_page = ITEMS_PER_PAGE)
    {
        $page_no    = $page_no > 0 ? $page_no : 1;
        $per_page   = $per_page < 1 ? ITEMS_PER_PAGE : $per_page;
        $start      = ($page_no - 1) * $per_page;

        $hash       = 'inddt-list' . md5('-page_no' . $page_no . '-start' . $start);
        $cache_tags = array($hash, 'inddts');
        
        $rowset   = $this->_get_cached_data($hash, 'sp_in_ddt_get_list', array($start, $per_page), $cache_tags);
        
        if (!isset($rowset[0])) return array('data' => array(), 'count' => 0);
        
        $list       = $this->FillInDDTInfo($rowset[0]);
        $rows_count = (isset($rowset[1]) && isset($rowset[1][0]) && isset($rowset[1][0]['rows_count'])) ? $rowset[1][0]['rows_count'] : 0;
        
        return array('data' => $list, 'count' => $rows_count);
    }
    
    /**
     * Связывает аттачмент(ы) со стилайтемами
     * 
     * @param int $inddt_id [INT]
     */
    public function LinkAttachmentToItems($inddt_id)
    {
        foreach ($this->GetItems($inddt_id) as $item)
        {
            $this->CallStoredProcedure('sp_in_ddt_link_attachments_to_item', array($this->user_id, $inddt_id, $item['steelitem_id']));
            Cache::ClearTag('attachments-aliasinddt-id' . $inddt_id);
        }
    }    
}
