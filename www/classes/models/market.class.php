<?php
require_once APP_PATH . 'classes/models/country.class.php';

class Market extends Model
{
    function Market()
    {
        Model::Model('markets');
    }

    /**
     * Возвращает список market
     * 
     */
    function GetList()
    {
        $hash       = 'markets';
        $cache_tags = array($hash);

        $rowset = $this->_get_cached_data($hash, 'sp_market_get_list', array(), $cache_tags);
        $rowset = isset($rowset[0]) ? $this->FillMarketInfo($rowset[0]) : array();

        return $rowset;
    }

    /**
     * Возвращает market по идентификатору
     *     
     * @param mixed $id
     */
    function GetById($id)
    {
        $dataset = $this->FillMarketInfo(array(array('market_id' => $id)));
        return isset($dataset) && isset($dataset[0]) && isset($dataset[0]['market']) ? $dataset[0] : null;
    }
    
    /**
     * Возвращает главную информацию об рынке
     * 
     * @param mixed $rowset
     * @param mixed $id_fieldname
     * @param mixed $entityname
     * @param mixed $cache_prefix
     * @return mixed
     */
    function FillMarketMainInfo($rowset, $id_fieldname = 'market_id', $entityname = 'market', $cache_prefix = 'market')
    {
        return $this->_fill_entity_info($rowset, $id_fieldname, $entityname, $cache_prefix, 'sp_market_get_list_by_ids', array('markets' => ''), array());        
    }
    
    /**
     * Возвращет информацию о market
     * 
     * @param mixed $rowset
     * @param mixed $id_fieldname
     * @param mixed $entityname
     * @param mixed $cache_prefix
     */
    function FillMarketInfo($rowset, $id_fieldname = 'market_id', $entityname = 'market', $cache_prefix = 'market')
    {
        $rowset = $this->FillMarketMainInfo($rowset, $id_fieldname, $entityname, $cache_prefix);
        
        foreach ($rowset as $key => $row)
        {
            if (isset($row[$entityname]))
            {
                $row = $row[$entityname];   //вырезаем массив с данными таблицы  в массив $row
                
                $rowset[$key]['market_author_id']      = $row['created_by'];
                $rowset[$key]['market_modifier_id']     = $row['modified_by'];
            }
        }

        $user   = new User();
        $rowset = $user->FillUserInfo($rowset, 'market_author_id', 'market_author');
        $rowset = $user->FillUserInfo($rowset, 'market_modifier_id', 'market_modifier');
        
        foreach ($rowset as $key => $row)
        {
            if (isset($row[$entityname]))
            {
                if (isset($row['market_author']) && !empty($row['market_author'])) $rowset[$key][$entityname]['author'] = $row['market_author'];
                unset($rowset[$key]['market_author']);
                unset($rowset[$key]['market_author_id']);

                if (isset($row['market_modifier']) && !empty($row['market_modifier'])) $rowset[$key][$entityname]['modifier'] = $row['market_modifier'];
                unset($rowset[$key]['market_modifier']);
                unset($rowset[$key]['market_modifier_id']);                
            }
        }         
        
        return $this->FillQuickInfo($rowset, $id_fieldname, $entityname);
    }
    
    /**
     * Возвращает быстроменяющуюся информацию по market
     * 
     * @param array $rowset
     * @param mixed $id_fieldname
     * @param mixed $entityname
     */
    function FillQuickInfo($rowset, $id_fieldname = 'market_id', $entityname = 'market')
    {
        $rowset = $this->_fill_entity_info($rowset, $id_fieldname, $entityname . 'quick', 'marketquick', 'sp_market_get_quick_by_ids', array('marketsquick' => '', 'markets' => '', 'market' => 'id'), array());
        
        foreach ($rowset AS $key => $row)
        {
            if (isset($row[$entityname]) && isset($row[$entityname . 'quick']))
            {
                $rowset[$key][$entityname]['quick'] = $row[$entityname . 'quick'];
            }
            
            unset($rowset[$key][$entityname . 'quick']);            
        }
        
        return $rowset;
    }

    /**
     * Сохраняет market
     * 
     * @param mixed $id
     * @param mixed $title
     * @param mixed $description
     */
    function Save($id, $title, $description, $map_data)
    {        
        $result = $this->CallStoredProcedure('sp_market_save', array($this->user_id, $id, $title, $description, $map_data));
        $result = isset($result) && isset($result[0]) && isset($result[0][0]) ? $result[0][0] : null;
        
        if (empty($result) || array_key_exists('ErrorCode', $result)) return null;

        Cache::ClearTag('market-' . $result['id']);
        Cache::ClearTag('markets');
        
        return $result;
    }
    
    /**
     * Удаляет рынок
     * 
     * @param mixed $id
     * @return resource
     */
    function Remove($id)
    {        
        $result = $this->CallStoredProcedure('sp_market_remove', array($this->user_id, $id));
        $result = isset($result) && isset($result[0]) && isset($result[0][0]) ? $result[0][0] : null;
        
        if (empty($result) || array_key_exists('ErrorCode', $result)) return null;

        Cache::ClearTag('market-' . $id);
        Cache::ClearTag('markets');
        
        return $result;
    }
    
    /**
     * Возвращает список стран рынка
     * 
     * @param mixed $market_id
     */
    function GetCountries($market_id)
    {
        $hash       = 'market-' . $market_id . '-countries';
        $cache_tags = array($hash, 'market-' . $market_id, 'markets');

        $rowset = $this->_get_cached_data($hash, 'sp_market_get_countries', array($market_id), $cache_tags);
        
        $countries  = new Country();
        $rowset     = isset($rowset[0]) ? $countries->FillCountryInfo($rowset[0]) : array();

        return $rowset;        
    }
    
    /**
     * Сохраняет страну в рынок
     * 
     * @param mixed $market_id
     * @param mixed $user_id
     */
    function AddCountry($market_id, $country_id)
    {
        $result = $this->CallStoredProcedure('sp_market_add_country', array($this->user_id, $market_id, $country_id));
        $result = isset($result) && isset($result[0]) && isset($result[0][0]) ? $result[0][0] : null;

        Cache::ClearTag('market-' . $market_id . '-countries');
        
        return $result;        
    }
    
    /**
     * Убирает страну из рынка
     * 
     * @param mixed $market_id
     * @param mixed $user_id
     */
    function RemoveCountry($market_id, $country_id)
    {
        $result = $this->CallStoredProcedure('sp_market_remove_country', array($market_id, $country_id));
        $result = isset($result) && isset($result[0]) && isset($result[0][0]) ? $result[0][0] : null;

        Cache::ClearTag('market-' . $market_id . '-countries');
        
        return $result;        
    }
}
