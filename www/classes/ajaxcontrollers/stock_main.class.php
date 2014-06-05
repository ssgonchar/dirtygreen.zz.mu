<?php
require_once APP_PATH . 'classes/models/biz.class.php';
require_once APP_PATH . 'classes/models/company.class.php';
require_once APP_PATH . 'classes/models/steelgrade.class.php';
require_once APP_PATH . 'classes/models/steelposition.class.php';
require_once APP_PATH . 'classes/models/stock.class.php';

class MainAjaxController extends ApplicationAjaxController
{    

    function MainAjaxController()
    {
        ApplicationAjaxController::ApplicationAjaxController();        
        
        $this->authorize_before_exec['deleteitem']          = ROLE_STAFF;
        $this->authorize_before_exec['getparams']           = ROLE_STAFF;
        $this->authorize_before_exec['getlocations']        = ROLE_STAFF;
        $this->authorize_before_exec['getpositionfilter']   = ROLE_STAFF;
        $this->authorize_before_exec['getitemfilter']       = ROLE_STAFF;
        $this->authorize_before_exec['getsteelgrades']      = ROLE_STAFF;        
    }

    /**
     * Return stock params specified by user
     * 
     */
    function getstockparams()
    {
        $stock_id       = Request::GetInteger('stock_id', $_REQUEST);
        $location_id    = Request::GetInteger('location_id', $_REQUEST);
        $strict         = Request::GetBoolean('strict', $_REQUEST);
        
        $locations      = Request::GetBoolean('locations', $_REQUEST);
        $steelgrades    = Request::GetBoolean('steelgrades', $_REQUEST);

        
        $modelStock = new Stock();
        $stock      = $modelStock->GetById($stock_id);
        
        if (empty($stock)) $this->_send_json(array('result' => 'error'));

        $result = array(
            'result'        => 'okay',
            'stock'         => $stock['stock'],
            'locations'     => array(),
            'steelgrades'   => array()
        );
        
        if ($locations)
        {
            $rowset     = $modelStock->GetLocations($stock_id, $strict);        
            $locations  = array();                
            foreach($rowset as $row) 
            {
                if ($row['company']['location_id'] > 0)
                {
                    $title = $row['company']['doc_no'];
                    
                    if (isset($row['company']['city']) && $row['company']['city']['title'] != $row['company']['stocklocation']['title'])
                    {
                        $title .= ', ' . $row['company']['city']['title'];
                    }
                    
                    $title .= ' (' . $row['company']['stocklocation']['title'] . ')';

                    $locations[] = array('id' => $row['company']['id'], 'name' => $title);    
                }            
            }
            
            $result['locations'] = $locations;
        }
        
        if ($steelgrades)
        {
            $steelgrades            = $modelStock->GetSteelgrades($stock_id, 0);
            $result['steelgrades']  = $this->_prepare_list($steelgrades, 'steelgrade');
        }
        
        $this->_send_json($result);
    }
    
    /**
     * Get stock params // Возвращает параметры склада
     * url: /stock/getparams
     */
    function getparams()
    {
        $stock_id   = Request::GetInteger('stock_id', $_REQUEST);
        $strict     = Request::GetBoolean('strict', $_REQUEST);

        $stocks = new Stock();
        $stock  = $stocks->GetById($stock_id);
        
        if (empty($stock)) $this->_send_json(array('result' => 'error'));

        
        $rowset     = $stocks->GetLocations($stock_id, $strict);        
        $locations  = array();                
        foreach($rowset as $row) 
        {
            if ($row['company']['location_id'] > 0)
            {
                $locations[] = array('id' => $row['company']['id'], 'name' => $row['company']['doc_no'] . (isset($row['company']['city']) ? ', ' . $row['company']['city']['title'] : '') . ' (' . $row['company']['stocklocation']['title'] . ')');    
            }            
        }
        
        
        $this->_send_json(array(
            'result'    => 'okay', 
            'stock'     => $stock['stock'], 
            'locations' => $locations));
    }
    
    /**
     * Get stock locations // Возвращает locations для склада
     * url: /stock/getlocations
     */
    function getlocations()
    {
        $stock_id   = Request::GetInteger('stock_id', $_REQUEST);

        $stocks     = new Stock();
        $stock      = $stocks->GetById($stock_id);
        
        if (empty($stock)) $this->_send_json(array('result' => 'error'));

        
        $rowset     = $stocks->GetLocations($stock_id);
//	/	dg($rowset);
        $locations  = array();                
        foreach($rowset as $row) 
        {
            $locations[] = array('id' => $row['company']['id'], 'name' => $row['company']['doc_no'] . ' (' . $row['company']['stocklocation']['title'] . ')');
        }

        $this->_send_json(array(
            'result' => 'okay', 
            'locations' => $locations
        ));
    }
    
    /**
     * Get stock steelgrades list // Возвращает список марок стали для склада
     * url: /stock/getsteelgrades
     */
    function getsteelgrades()
    {
        $stock_id       = Request::GetInteger('stock_id', $_REQUEST);
        $location_id    = Request::GetInteger('location_id', $_REQUEST);

        $stocks     = new Stock();
        $stock      = $stocks->GetById($stock_id);
        
        if (empty($stock)) $this->_send_json(array('result' => 'error'));
        
        
        $steelgrades = $stocks->GetSteelgrades($stock_id, $location_id);
        $this->_send_json(array('result' => 'okay', 'steelgrades' => $this->_prepare_list($steelgrades, 'steelgrade')));
    }
    
    /**
     * Get data for position filter // Возвращает данные для фильтра позиций
     * url: /stock/getpositionfilter
     */
    function getpositionfilter()
    {
        $stock_id   = Request::GetInteger('stock_id', $_REQUEST);
        $rev_date   = Request::GetDateForDB('rev_date', $_REQUEST);
        $rev_time   = Request::GetString('rev_time', $_REQUEST);

        if (!empty($rev_date))
        {
            $stocks = new Stock();
			
            $_REQUEST['stock_revision'] = $stocks->CheckRevision($rev_date, $rev_time);
			//print_r('create stock_revision');
        }        

        // check if stock exists // Проверка существования склада
        $stocks     = new Stock();
        $stock      = $stocks->GetById($stock_id);        
        if (empty($stock)) $this->_send_json(array('result' => 'error'));


        $this->_assign('name', 'location');
        $this->_assign('list', $this->_prepare_list($stocks->GetPositionLocations($stock_id), 'location'));
        $locations = $this->smarty->fetch('templates/html/stock/control_checkboxes.tpl');

        $this->_assign('name', 'stockholder');
        $this->_assign('list', $this->_prepare_list($stocks->GetItemLocations($stock_id), 'stockholder', 'id', 'doc_no_full'));
        $stockholders      = $this->smarty->fetch('templates/html/stock/control_stockholders_position.tpl');  
        //debug('1671', $stocks->GetItemLocations($stock_id));
        $this->_assign('name', 'deliverytime');
        $this->_assign('list', $this->_prepare_list($stocks->GetPositionDeliveryTimes($stock_id), 'deliverytime'));
        $deliverytimes = $this->smarty->fetch('templates/html/stock/control_checkboxes.tpl');

		$this->_assign('name', 'steelgrade');
        $steelgrades = $stocks->GetSteelgrades($stock_id);
		$this->_assign('steelgrade_list', $stocks->GetSteelgrades($stock_id));
        $steelgrades_tpl=$this->smarty->fetch('templates/html/position/control_steelgrades.tpl');
        //dg($steelgrades_tpl);
		$this->_send_json(array(
            'result'            => 'okay', 
            'locations'         => $locations,
            //'steelgrades'       => $this->_prepare_list($steelgrades, 'steelgrade'),
            'steelgrades'       => $steelgrades_tpl,
            'deliverytimes'     => $deliverytimes,
            'stockholders'      => $stockholders,
        ));
    }
    
    /**
     * Get data for items filter on stock // Возвращает данные для фильтрации айтемов на складе
     * url: /stock/getitemfilter
     * 
     * @version 20120808, zharkov: remove deliverytime select as items have no deliverytimes // убрал выбор deliverytime потому что его нет у айтема, только у позиции
     */
    function getitemfilter()
    {
        $stock_id   = Request::GetInteger('stock_id', $_REQUEST);
        $rev_date   = Request::GetDateForDB('rev_date', $_REQUEST);
        $rev_time   = Request::GetString('rev_time', $_REQUEST);

        $modelStock = new Stock();
        if (!empty($rev_date)) $_REQUEST['stock_revision'] = $modelStock->CheckRevision($rev_date, $rev_time);

        // проверка существования склада
        $stock = $modelStock->GetById($stock_id);        
        if (empty($stock)) $this->_send_json(array('result' => 'error'));
        
        
        $this->_assign('name', 'stockholder');
        $this->_assign('list', $this->_prepare_list($modelStock->GetItemLocations($stock_id), 'stockholder', 'id', 'doc_no_full'));
        $locations      = $this->smarty->fetch('templates/html/stock/control_checkboxes_div.tpl');        
        
        $deliverytimes  = '';
        
        $modelOrder = new Order();
        $this->_send_json(array(
            'result'            => 'okay', 
            'locations'         => $locations,
            'deliverytimes'     => $deliverytimes,
            'steelgrades'       => $this->_prepare_list($modelStock->GetItemSteelGrades($stock_id), 'steelgrade'),
            'orders'            => $this->_prepare_list($modelOrder->GetListForStock($stock_id), 'order', 'id', 'doc_no_full'),
        ));
    }
    
    /**
     * Remove item from position // Удаляет item ищ позиции
     * url: /stock/deleteitem 
     */
    function deleteitem()
    {
        $item_id = Request::GetInteger('stock_id', $_REQUEST);
    }
    
    
    /**
     * Get positions list // Возвращает список позиций
     * url: /stock/getpositions
     */
    function getpositions()
    {
        $stock_id       = Request::GetInteger('stock_id', $_REQUEST);
        $stockholder_id = Request::GetInteger('location_id', $_REQUEST);
        $steelgrade_id  = Request::GetInteger('steelgrade_id', $_REQUEST);
        $thickness      = Request::GetString('items_thickness', $_REQUEST);
        $thickness      = str_replace(',', '', $thickness);
        $width          = Request::GetString('items_width', $_REQUEST);
        $width          = str_replace(',', '', $width);
        $length         = Request::GetString('items_length', $_REQUEST);
        $length         = str_replace(',', '', $length);
        $items_count    = Request::GetInteger('items_count', $_REQUEST);
        $prefix         = Request::GetString('prefix', $_REQUEST, 'form');
        $position_id    = Request::GetInteger('position_id', $_REQUEST);
/*        
        if ($width > 0) $width = ($width - 50) . '-' . ($width + 50);
        if ($length > 0) $length = ($length - 100) . '-' . ($length + 100);
*/
        $stocks     = new Stock();
        $stock      = $stocks->GetById($stock_id);
        
        if (empty($stock)) $this->_send_json(array('result' => 'error'));

        $this->_assign('prefix',        $prefix);
        $this->_assign('stock',         $stock['stock']);
        $this->_assign('items_count',   $items_count);
        $this->_assign('position_id',   $position_id);
        
        $companies  = new Company();
        $company    = $companies->GetById($stockholder_id);
        $company    = $company['company'];
        
        $positions = new SteelPosition();
        $this->_assign('positions', $positions->GetList(0, $stock_id, $company['location_id'], '', $steelgrade_id, $thickness, 0, 0, $width, 0, 0, $length, 0, 0, 0, 0, 0, '', '', ''));

        $steelgrades = new SteelGrade();
        $this->_assign('steelgrades', $steelgrades->GetList());
        
        $bizes = new Biz();
        //$this->_assign('bizes', $bizes->GetList('steel'));        
        $this->_assign('bizes', array());        
        
        $this->_send_json(array(
            'result'            => 'okay', 
            'positions'         => $this->smarty->fetch('templates/html/item/control_positions.tpl')
        ));
    }    
}
