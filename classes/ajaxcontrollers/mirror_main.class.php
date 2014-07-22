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
        $positions          = $modelSteelPosition->GetList();
        //debug("1682", $positions);
        //список складов:
        $modelStock = new Stock();
        $stocks = $modelStock->GetList();
        //список возможных location для складов:
        $eur_stock_id = 1;
        $usa_stock_id = 2;
        $locations_eur = $modelStock->GetPositionLocations($eur_stock_id);
        foreach ($locations_eur as $key => $row){ $ids_eur[] = $locations_eur[$key]['location_id']; }
        
        $locations_usa = $modelStock->GetPositionLocations($usa_stock_id);
        foreach ($locations_usa as $key => $row){ $ids_usa[] = $locations_usa[$key]['location_id']; }
        //debug("1682", $ids_usa);
        //список возможных deliverytimes для складов:
        $deliverytimes_eur = $modelStock->GetPositionDeliveryTimes($eur_stock_id);
        $deliverytimes_usa = $modelStock->GetPositionDeliveryTimes($usa_stock_id);
        
        $mirrors = array();
        //получаем список mirrors по $position_id
        $modelMirror = new Mirror();
        $mirrors = $modelMirror->GetListByPositionId($position_id);
        
        if(!empty($mirrors)){
            //для каждой позиции mirror добавляем position инфо
            foreach ($mirrors as $key => $row){ $mirrors[$key]['position'] = $position; }
            //по location_id определяем stock_id
            foreach ($mirrors as $key => $row)
            {
                if(array_search($mirrors[$key]['mirror']['location_id'], $ids_eur)>-1){
                    $mirrors[$key]['mirror']['stock_id'] = "1";
                }
                elseif(array_search($mirrors[$key]['mirror']['location_id'], $ids_usa)>-1){
                    $mirrors[$key]['mirror']['stock_id'] = "2";
                }
            }
        }
        $this->_assign('position',          $position['steelposition']);    //данные по позиции
        $this->_assign('stocks',            $stocks);                         //список складов
        $this->_assign('locations_eur',     $locations_eur);
        $this->_assign('locations_usa',     $locations_usa);
        $this->_assign('deliverytimes_eur', $deliverytimes_eur);
        $this->_assign('deliverytimes_usa', $deliverytimes_usa);
        
        $this->_assign('mirrorlist', $mirrors);
        $this->_send_json(array(
            'result'  => 'okay',
            'content' => $this->smarty->fetch('templates/html/mirror/control_mirror.tpl'),
            'ids_eur' => $ids_eur,
            'ids_usa' => $ids_usa
        )); 
        //если по данной позиции mirrors нет - создаем новый
        /*if(!empty($mirrors)){
            
        }
        else {
            $this->_send_json(array(
                'result'    => 'new_mirror',
                'content'   => $this->smarty->fetch('templates/html/mirror/control_mirror_new.tpl')
            )); 
        }*/
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
    
    /* Сохраняет строку в таблице mirrors
     * 
     * @param mirror_id
     * @param position_id
     * @param stock_id
     * @param location_id
     * @param deliverytime_id
     * @param price
     * @return array() В случае перезаписи или добавления новой строки возвращает её ID
     * @version 20140605 
     * @author Uskov
     */
    function savemirror()
    {
        //получаем данные
        $mirror_id = Request::GetInteger('mirror_id', $_REQUEST);
        $position_id = Request::GetInteger('position_id', $_REQUEST);
        $stock_id = Request::GetInteger('stock_id', $_REQUEST);
        $location_id = Request::GetInteger('location_id', $_REQUEST);
        $deliverytime_id = Request::GetInteger('deliverytime_id', $_REQUEST);
        $price = Request::GetInteger('price', $_REQUEST);
        
        $modelMirror = new Mirror();
        $saved_id = $modelMirror->Save($mirror_id, $position_id, $location_id, $deliverytime_id, $price);
        //debug('1682', $saved_mirror_id);
        if (!empty($saved_id))
        {
            $this->_send_json(array(
                'result'    => 'okay',
                'object'   => $saved_id
            ));
        }
    }
    function deletemirror()
    {
        //получаем данные
        $mirror_id = Request::GetInteger('mirror_id', $_REQUEST);
        
        $modelMirror = new Mirror();
        $deleted_id = $modelMirror->Remove($mirror_id);
        
        //debug('1682', $deleted_id);
        if (!empty($deleted_id))
        {
            $this->_send_json(array(
                'result'    => 'okay',
                'object'   => $deleted_id
            ));
        }
    }
    
    /*
     * Проверяет по position_id наличие в БД mirrors с price = 0.00 и удаляет их
     * @param $position_id
     * @return array $null_price_mirrors_ids
     * 
     * @version 20140622
     * @author Uskov
     */
    function getnullpricemirrors()
    {
        $position_id = Request::GetInteger('position_id', $_REQUEST);
        //находим в базе mirror c price = 0.00 и возвращаем их id
        //получаем список mirrors по $position_id
        $modelMirror = new Mirror();
        $mirrors = $modelMirror->GetListByPositionId($position_id);
        //массив для id
        $null_price_mirrors_ids = array();
        
        foreach ($mirrors as $key => $row)
	{
            if ($mirrors[$key]['mirror']['price'] == 0.00){
                $null_price_mirrors_ids[] = $mirrors[$key]['mirror']['id'];
            }
	}
        if(!empty($null_price_mirrors_ids)){
            foreach ($null_price_mirrors_ids as $key => $row)
            {
                $modelMirror->Remove($null_price_mirrors_ids[$key]);
            }
            $this->_send_json(array(
                'result'    => 'okay',
                'content'   => $null_price_mirrors_ids
            ));
        }else{
            $this->_send_json(array(
                'result'    => 'no empty mirrors'
            ));
        }
        //debug("1682", $test);
    }
}