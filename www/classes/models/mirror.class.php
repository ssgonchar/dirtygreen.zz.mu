<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
require_once APP_PATH . 'classes/models/steelposition.class.php';
require_once APP_PATH . 'classes/models/location.class.php';
require_once APP_PATH . 'classes/models/stock.class.php';
/**
 * Description of mirror
 *
 * @author Gonchar
 */
class Mirror extends Model {

    function Mirror() {
        Model::Model('mirrors');
    }

    /**
     * Возвращает полный список наименований для mirrors / Returns fuul list of mirrors
     * @return $array()
     * 
     */
    function GetList() {
        $hash = 'mirror'; //ключ кеша = 'nomenclature'
        $cache_tags = array($hash);  //теги ключа кеша (что это?)

        $rowset = $this->_get_cached_data($hash, 'sp_mirror_get_list', array(), $cache_tags);
        //debug('1682', $rowset);
        $rowset = isset($rowset[0]) ? $this->FillInfo($rowset[0]) : array();
        
        return $rowset;
    }
    
    /* Возвращает список mirrors по id позиции / Returns mirrors list for current position id
     * 
     * @param $position_id
     * @return $array()
     * @version 20140605 
     * @author Uskov
     */
    function GetListByPositionId($position_id)
    {
	$hash = 'mirror';
        $cache_tags = array($hash);

        $rowset = $this->_get_cached_data($hash, 'sp_mirror_get_list', array(), $cache_tags);
        
        $rowset = isset($rowset[0]) ? $this->FillInfo($rowset[0]) : array();
        
        foreach ($rowset as $key => $row)
	{
	    if($rowset[$key]['mirror']['position_id'] == $position_id)
	    {
	        $sorted_list[] = $row;
	    } 
	}
        //debug('1682', $sorted_list);
        return $sorted_list;
        
    }
    
    function FillInfo($rowset, $id_fieldname = 'id', $entityname = 'mirror', $cache_prefix = 'mirror') {
        $rowset = $this->FillMainInfo($rowset, $id_fieldname, $entityname, $cache_prefix);
        
        return $rowset;
    }
    //По ID заполняет строку таблицы по всем пунктам
    function FillMainInfo($rowset, $id_fieldname = 'id', $entityname = 'mirror', $cache_prefix = 'mirror') {
        return $this->_fill_entity_info($rowset, $id_fieldname, $entityname, $cache_prefix, 'sp_mirror_get_list_by_ids', array('mirror' => ''), array());
    }

    function Remove($id) {
        $result = $this->CallStoredProcedure('sp_mirror_remove', array($this->user_id, $id));
        $result = isset($result) && isset($result[0]) && isset($result[0][0]) ? $result[0][0] : null;

        if (empty($result) || array_key_exists('ErrorCode', $result)) {
            return null;
        }

        Cache::ClearTag('mirror-' . $id);
        Cache::ClearTag('mirror');

        return $result;
    }
    
    /**
     * Сохраняет mirror в базу данных / Saves mirror in database
     * Сохранение одной строки mirror
     * @param mixed $id
     * @param mixed $position_id
     * @param mixed $location_id
     * @param mixed $deliverytime_id
     * @param mixed $price
     * @param mixed $status_id
     * @version 20140605 
     * @author Uskov
     */
    function Save($id, $position_id, $location_id, $deliverytime_id, $price)
    {        
        $result = $this->CallStoredProcedure('sp_mirror_save', array($this->user_id, $id, $position_id, $location_id, $deliverytime_id, $price));
        $result = isset($result) && isset($result[0]) && isset($result[0][0]) ? $result[0][0] : null;
        
        if (empty($result) || array_key_exists('ErrorCode', $result)) return null;

        Cache::ClearTag('mirror-' . $result['id']);
        Cache::ClearTag('mirror');
        
        return $result;
    }
}