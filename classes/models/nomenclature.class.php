<?php
require_once APP_PATH . 'classes/models/user.class.php';
require_once APP_PATH . 'classes/models/nomenclature_category.class.php';

class Nomenclature extends Model
{
    function Nomenclature()
    {
        Model::Model('nomenclature');
    }
        
    /**
     * Возвращает список наименований для view
     * 
     */
    function GetListByCategoryId($category_id)
    {
	$hash       = 'nomenclature';	//ключ кеша = 'nomenclature'
        $cache_tags = array($hash);
	
        $rowset = $this->_get_cached_data($hash, 'sp_nomenclature_get_list', array(), $cache_tags);
        
	$rowset = isset($rowset[0]) ? $this->FillNomenclatureInfo($rowset[0]) : array();
	
	foreach ($rowset as $key => $row)
	{
	    if($rowset[$key]['nomenclature']['category_id']==$category_id)
	    {
	        $sorted_list[] = $row;
	    } 
	}
        return $sorted_list;
    }
    
    /**
     * Возвращает список наименований для index
     * 
     */
    function GetList()
    {
        $hash       = 'nomenclature';	//ключ кеша = 'nomenclature'
        $cache_tags = array($hash);		//теги ключа кеша (что это?)
	
        $rowset = $this->_get_cached_data($hash, 'sp_nomenclature_get_list', array(), $cache_tags);
        //debug('1682', $rowset);
	$rowset = isset($rowset[0]) ? $this->FillNomenclatureInfo($rowset[0]) : array();
        //dg($rowset);
        return $rowset;
    }
           
    /**
     * Возвращает номенклатуру по идентификатору
     * 
     * @param mixed $id
     */
    function GetByIdMod($id)
    {
        $dataset = $this->FillNomenclatureInfo(array(array('nomenclature_id' => $id)));
        return isset($dataset) && isset($dataset[0]) && isset($dataset[0]['nomenclature']) ? $dataset[0] : null;        
    }
    
    /**
     * Возвращает сообщение по идентификатору
     * 
     * @param mixed $message_id
     */
    function GetById($id)
    {
        $dataset = $this->FillNomenclatureInfo(array(array('nomenclature_id' => $id)));
        return isset($dataset) && isset($dataset[0]) && isset($dataset[0]['nomenclature']) ? $dataset[0] : null;        
    }
    
   /**
     * Возвращает данные таблицы nomenclature
     * 
     * @param mixed $rowset
     * @param mixed $id_fieldname
     * @param mixed $entityname
     * @param mixed $cache_prefix
     */
    function FillNomenclatureMainInfo($rowset, $id_fieldname = 'nomenclature_id', $entityname = 'nomenclature', $cache_prefix = 'nomenclature')
    {	
        /* По ID заполняет строку таблицы по всем пунктам */
	return $this->_fill_entity_info($rowset, $id_fieldname, $entityname, $cache_prefix, 'sp_nomenclature_get_list_by_ids', array('nomenclature' => ''), array());
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
    function FillNomenclatureInfo($rowset, $id_fieldname = 'nomenclature_id', $entityname = 'nomenclature', $cache_prefix = 'nomenclature')
    {
	$rowset = $this->FillNomenclatureMainInfo($rowset, $id_fieldname, $entityname, $cache_prefix);
	
	foreach ($rowset as $key => $row)	//перебираю массив $rowset
	{
	    if(isset($entityname))	//$entityname - это массив с данными таблицы nomenclature
	    {
		$row = $row[$entityname];	//вырезаем массив с данными таблицы  в массив $row
		//? добавляю в общий массив $rowset позицию nomenclature_modifier_id = modified_by из табл. nomenclature
		$rowset[$key]['nomenclature_modifier_id']     = $row['modified_by'];
	    }
	}
	
	$user   = new User();	//создаю обект $user класса User()
	//заполняю данные о пользователе по id в поле nomenclature_modifier
        $rowset = $user->FillUserInfo($rowset, 'nomenclature_modifier_id', 'nomenclature_modifier');
	
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
     * Сохраняет nomenclature в базу данных / Saves nomenclature in database
     * 
     * @param mixed $id
     * @param mixed $title
     * @param mixed $description
     * @param mixed $category_id
     * @version 20140605 
     * @author Uskov
     
    function Save($id, $title, $description, $category_id)
    {        
        $result = $this->CallStoredProcedure('sp_nomenclature_save', array($this->user_id, $id, $title, $description, $category_id));
        $result = isset($result) && isset($result[0]) && isset($result[0][0]) ? $result[0][0] : null;
        
        if (empty($result) || array_key_exists('ErrorCode', $result)) return null;

        Cache::ClearTag('nomenclature-' . $result['id']);
        Cache::ClearTag('nomenclature');
        
        return $result;
    }*/
    /*
    CREATE DEFINER = 'mam'@'localhost'
    PROCEDURE mam_www.sp_nomenclature_save(param_user_id INT, param_id INT, param_title VARCHAR(250), param_description TEXT, param_category_id INT)
    sp:
    BEGIN

        IF EXISTS (SELECT * FROM nomenclature WHERE title = param_title AND id != param_id)
        THEN
            SELECT -1 AS ErrorCode, 'sp_nomenclature_save' AS ErrorAt;
            LEAVE sp;
        END IF;

        IF EXISTS (SELECT * FROM nomenclature WHERE id = param_id)
        THEN

            UPDATE nomenclature
            SET
                title           = param_title,
                description     = param_description,
                modified_at     = NOW(),
                modified_by     = param_user_id,
                category_id     = param_category_id
            WHERE id = param_id;

        ELSE

            INSERT nomenclature
            SET
                title           = param_title,
                description     = param_description,
                modified_at     = NOW(),
                modified_by     = param_user_id,
                category_id     = param_category_id;

        END IF;


        SELECT param_id AS id;

    END
     */
}
