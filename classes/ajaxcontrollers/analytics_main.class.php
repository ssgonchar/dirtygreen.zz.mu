<?php

require_once APP_PATH . 'classes/models/stock.class.php';
require_once APP_PATH . 'classes/models/steelposition.class.php';

class MainAjaxController extends ApplicationAjaxController {

    function __construct() {
        ApplicationAjaxController::ApplicationAjaxController();

        $this->authorize_before_exec['bindsearch'] = ROLE_STAFF;
    }

    function bindsearch() {
        $stock_id = Request::GetNumeric('stock_id', $_REQUEST);
        $modelStock = new Stock();

        $steelgrades = $modelStock->GetItemSteelGrades($stock_id);
        $this->_assign('steelgrade_list', $modelStock->GetSteelgrades($stock_id));
        $steelgrades_tpl = $this->smarty->fetch('templates/html/analytics/control_steelgrades.tpl');

        $arr_locations = $modelStock->GetPositionLocations($stock_id);
        $arr_stockholders = $modelStock->GetItemLocations($stock_id);
        $this->_assign('locations', $arr_locations);
        $html_locations = $this->smarty->fetch('templates/html/analytics/control_locations.tpl');

        $this->_assign('list', $arr_stockholders);
        $stockholders_position_tpl = $this->smarty->fetch('templates/html/analytics/control_stockholders_position.tpl');

        $this->_send_json(array(
            'result' => 'okay',
            'stock' => $stock_id,
            'steelgrades' => $steelgrades_tpl,
            'locations' => $html_locations,
            'locations_arr' => $arr_locations,
            'stockholders' => $stockholders_position_tpl,
        ));
    }

    /**
     * Get stock locations // Возвращает locations для склада
     * url: /stock/getlocations
     */
    function getlocations($stock_id) {
        $stocks = new Stock();
        $stock = $stocks->GetById($stock_id);

        if (empty($stock))
            $this->_send_json(array('result' => 'error'));


        $rowset = $stocks->GetLocations($stock_id);
//	/	dg($rowset);
        $locations = array();
        foreach ($rowset as $row) {
            $locations[] = array('id' => $row['company']['id'], 'name' => $row['company']['doc_no'] . ' (' . $row['company']['stocklocation']['title'] . ')');
        }

        RETURN $locations;
    }

}
