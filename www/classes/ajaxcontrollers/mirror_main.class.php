<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require_once APP_PATH . 'classes/models/mirror.class.php';
require_once APP_PATH . 'classes/models/steelposition.class.php';
require_once APP_PATH . 'classes/models/location.class.php';
require_once APP_PATH . 'classes/models/stock.class.php';
require_once APP_PATH . 'classes/models/steelitem.class.php';

/**
 * Description of mirror_main
 *
 * @author Gonchar
 */
class MainAjaxController extends ApplicationAjaxController
{
    
    function MainAjaxController()
    {
        ApplicationAjaxController::ApplicationAjaxController();        
        
        $this->authorize_before_exec['getmirror'] = ROLE_STAFF;
    }    
    
    /* Функция возвращает инфо о mirror для позиции / Returns info about mirror for current position
     * Если по данной позиции есть mirror -  получаем инфу и редактируем, иначе создаем новый mirror
     * url: /mirror/getmirror
     * @param $position_id
     * 
     * @version 20140605 
     * @author Uskov
     */
    function getmirror()
    {
        $position_id = Request::GetInteger('position_id', $_REQUEST);
        //данные по позиции:
        $modelSteelPosition = new SteelPosition();
        $position           = $modelSteelPosition->GetById($position_id);       //возвращает список позиция по Id
        
        //список складов:
        $modelStock = new Stock();
        $stocks = $modelStock->GetList();
        //список возможных location для данного склада:
        $locations = $modelStock->GetPositionLocations($position['steelposition']['stock_id']);
        //список возможных deliverytimes для данного склада:
        $deliverytimes = $modelStock->GetPositionDeliveryTimes($position['steelposition']['stock_id']);
        
        //получаем список mirrors по $position_id
        $modelMirror = new Mirror();
        $mirrors = $modelMirror->GetListByPositionId($position_id);
        
        //для каждой позиции mirror добавляем position инфо
        foreach ($mirrors as $key => $row)
	{
            $mirrors[$key]['position'] = $position;
	}
        $this->_assign('position',      $position['steelposition']);    //данные по позиции
        $this->_assign('stocks',      $stocks);                         //список складов
        $this->_assign('locations',      $locations);           //список возможных location для данного склада
        $this->_assign('deliverytimes',      $deliverytimes);   //список возможных deliverytimes для данного склада
        //если по данной позиции mirrors нет - создаем новый
        if(empty($mirrors))
        {                       
            $this->_send_json(array(
                'result'    => 'new_mirror',
                'content'   => $this->smarty->fetch('templates/html/position/control_mirror_new.tpl')
            )); 
        }
        else    //если есть - получаем инфу и редактируем
        {
            $this->_assign('mirrorlist', $mirrors);
            $this->_send_json(array(
                'result'    => 'edit_mirror',
                'content'   => $this->smarty->fetch('templates/html/position/control_mirror_edit.tpl'),
                'obj'   => $mirrors
            )); 
        }     
    }
    
    function addrow()
    {
        $stock_id = Request::GetInteger('stock_id', $_REQUEST); 
                
        $modelStock = new Stock();
        
        $locations = $modelStock->GetPositionLocations($stock_id);
        $deliverytimes = $modelStock->GetPositionDeliveryTimes($stock_id);        
        
        if (!isset($locations))
        {
            $locations = array('id' => 0, 'title'=> '');
        }       

        if (!isset($deliverytimes))
        {
            $deliverytimes = array('id' => 0, 'title'=> '');
        }       

        $this->_send_json(array(
            'result'    => 'okay',
            'locations'   => $locations,
            'deliverytimes'   => $deliverytimes,
        ));  
    }
    
    /*
     * Для начала делаю для кнопки Save
     */
    function savemirrors()
    {
        
    }
}