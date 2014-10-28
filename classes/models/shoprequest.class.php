<?php
//require_once APP_PATH . 'classes/models/message.class.php';
require_once APP_PATH . 'classes/models/stock.class.php';
require_once APP_PATH . 'classes/models/location.class.php';
require_once APP_PATH . 'classes/models/steelgrade.class.php';
require_once APP_PATH . 'classes/models/user.class.php';
require_once APP_PATH . 'classes/models/deliverytime.class.php';

//define('ACTIVEUSER_OFFLINE_LIMIT', 600);   // 10 min
//define('ACTIVEUSER_AWAY_LIMIT',    1440);   // 24 min

class ShopRequest extends Model
{
    function ShopRequest()
    {
        Model::Model('ws_requests');
    }
      
    /**
     * Возвращает список активных пользователей
     * 
     * @version 20120705, zharkov
     */
    function GetList()
    {
        $hash       = 'shoprequest';
        $cache_tags = array($hash, 'requests');

        $rowset = $this->_get_cached_data($hash, 'sp_active_user_get_list', array(), $cache_tags);
        return isset($rowset[0]) ? $rowset[0] : array();
    }
    
    public function getRequests($arg) {
        //dg($arg);
        /*
        $arg['stock_id'];
        $arg['locations'];
        $arg['deliverytimes'];
        $arg['steelgrades'];
        $arg['created_at'];
        $arg['created_by'];
        /*
        $str_where = '';
        if($arg['stock_id'] > 0) $str_where .= ' AND stock_id="'.$arg['stock_id'].'"';
        if(!empty($arg['date_end'])) $str_where .= ' AND modified_at <= "'.$arg['date_end'].'"';
        if(!empty($arg['date_start'])) $str_where .= ' AND modified_at >= "'.$arg['date_start'].'"';
        if($arg['customer_id'] > 0) $str_where .= ' AND company_id IN ('.$arg['customer_id'].')';
        if($arg['delivery_town'] !== '') $str_where .= ' AND delivery_town LIKE "%'.$arg['delivery_town'].'%"';
        if($arg['steelgrade_id'] > 0) $str_where .= 'AND order_positions.steelgrade_id LIKE "%'.$arg['steelgrade_id'].'%"';
*/
                      

                       
        $arg_query = array (
            'fields' => array('*'),
           /* 'where' => 'status = "co" '
            . $str_where
            ,*/'order' => 'id DESC',
            'limit' => '200',
        );
   
        //dg($arg_query);
        $modelStock = new Stock();
        $modelLocation = new Location();
        $modelSteelgrade = new SteelGrade();
        $modelUser = new User();
        $modelDeliverytime = new DeliveryTime();
        
        $rowset = $this->table->SelectList($arg_query);
        foreach ($rowset as &$row) {
            $row['stock'] = $modelStock->GetById($row['stock_id']);
            $row['autor'] = $modelUser->GetById($row['created_by']);
            
            $arr_locations = explode(',', $row['locations']);
            //$row['locations'] = $arr_locations;
            foreach($arr_locations as $key => $val) {
                $row['locations_list'][] = $modelLocation->GetById($val);
            }            
            
            
            $arr_steelgrades = explode(',', $row['steelgrades']);
            //$row['steelgrades'] = $arr_steelgrades;       
            foreach($arr_steelgrades as $key => $val) {
                $row['steelgrades_list'][] = $modelSteelgrade->GetById($val);
            }             

            $arr_deliverytimes = explode(',', $row['deliverytimes']);
            //$row['deliverytimes'] = $arr_steelgrades;       
            foreach($arr_deliverytimes as $key => $val) {
                $row['deliverytimes_list'][] = $modelDeliverytime->GetById($val);
            }             
            /*foreach($arr_locations as $location_id) {
                $row['locations'][] = $modelLocation->GetById($location_id);
            }*/
            
        }
        /*dg( 'status = "co" '
            . $str_where);*/
        /*foreach ($rowset as $row) {
            $orders[] = $this->GetById($row['id']);
        }   */
        
        return $rowset;
    }    

}