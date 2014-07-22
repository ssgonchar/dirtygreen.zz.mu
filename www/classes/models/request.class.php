<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of request
 *
 * @author Serg
 */
class Request extends Model
{
    function Request()
    {
        Model::Model('ws_requests');
    }

	/**
     * Возвращает список наименований для index
     * 
     */
    function GetList()
    {
        $hash       = 'request';	//ключ кеша = 'nomenclature'
        $cache_tags = array($hash);		//теги ключа кеша (что это?)
	
        $rowset = $this->_get_cached_data($hash, 'sp_request_get_list', array(), $cache_tags);
        
	$rowset = isset($rowset[0]) ? $this->FillRequestInfo($rowset[0]) : array();
        //dg($rowset);
        return $rowset;
    }
    
    
    /**
     * Возвращает сообщение по идентификатору
     * 
     * @param mixed $message_id
     */
    function GetById($id)
    {
        $dataset = $this->FillRequestInfo(array(array('request_id' => $id)));
        return isset($dataset) && isset($dataset[0]) && isset($dataset[0]['request']) ? $dataset[0] : null;        
    }
    
   /**
     * Возвращает данные таблицы nomenclature
     * 
     * @param mixed $rowset
     * @param mixed $id_fieldname
     * @param mixed $entityname
     * @param mixed $cache_prefix
     */
    function FillRequestMainInfo($rowset, $id_fieldname = 'request_id', $entityname = 'request', $cache_prefix = 'request')
    {	
        /* По ID заполняет строку таблицы по всем пунктам */
	return $this->_fill_entity_info($rowset, $id_fieldname, $entityname, $cache_prefix, 'sp_request_get_list_by_ids', array('request' => ''), array());
    }
    
    /**
     * Возвращает данные таблицы nomenclature и добавляет массив modifier, в котором инфо о юзере.
     * Инфо заполняется на основании user_id из modified_by основной таблицы nomenclature
     * 
     * @param mixed $rowset
     * @param mixed $id_fieldname
     * @param mixed $entityname
     * @param mixed $cache_prefix
     */
    function FillRequestInfo($rowset, $id_fieldname = 'request_id', $entityname = 'request', $cache_prefix = 'request')
    {
	$rowset = $this->FillRequestMainInfo($rowset, $id_fieldname, $entityname, $cache_prefix);
	
	foreach ($rowset as $key => $row)	//перебираю массив $rowset
	{
	    if(isset($entityname))	//$entityname - это массив с данными таблицы nomenclature
	    {
		$row = $row[$entityname];	//вырезаем массив с данными таблицы  в массив $row
		//? добавляю в общий массив $rowset позицию nomenclature_modifier_id = modified_by из табл. nomenclature
		$rowset[$key]['request_autor_id']     = $row['created_by'];
	    }
	}
	
	$user   = new User();	//создаю обект $user класса User()
	//заполняю данные о пользователе по id в поле nomenclature_modifier
        $rowset = $user->FillUserInfo($rowset, 'request_modifier_id', 'nomenclature_modifier');
	
	foreach ($rowset as $key => $row)	//снова перебираю $rowset
        {
            if (isset($row[$entityname]))	//$row[$entityname] - массив с данными таблицы  nomenclature
            {	//записываю в $entityname массив modifier со информацией о userе, полученной с помощью FillUserInfo
                if (isset($row['nomenclature_modifier']) && !empty($row['nomenclature_modifier'])) $rowset[$key][$entityname]['modifier'] = $row['nomenclature_modifier'];
                unset($rowset[$key]['nomenclature_modifier']);	//удаляю промежуточные ячейки
                unset($rowset[$key]['nomenclature_modifier_id']);
            }
        }
	return($rowset);
    }	
    
   
	/**
     * Удаляет позицию в таблице
     * 
     * @param mixed $id
     * @return resource
     */
    function Remove($id)
    {        
        $result = $this->CallStoredProcedure('sp_nomenclature_remove', array($this->user_id, $id));
        $result = isset($result) && isset($result[0]) && isset($result[0][0]) ? $result[0][0] : null;
        
        if (empty($result) || array_key_exists('ErrorCode', $result)) return null;

        Cache::ClearTag('nomenclature-' . $id);
        Cache::ClearTag('nomenclature');
        
        return $result;
    }    
		
    /**
     * Сохраняет nomenclature в базу данных
     * 
     * @param mixed $id
     * @param mixed $title
     * @param mixed $description
     * @param mixed $category_id
     */
    function Save($id, $title, $description, $category_id)
    {        
        $result = $this->CallStoredProcedure('sp_nomenclature_save', array($this->user_id, $id, $title, $description, $category_id));
        $result = isset($result) && isset($result[0]) && isset($result[0][0]) ? $result[0][0] : null;
        
        if (empty($result) || array_key_exists('ErrorCode', $result)) return null;

        Cache::ClearTag('nomenclature-' . $result['id']);
        Cache::ClearTag('nomenclature');
        
        return $result;
    }
}
