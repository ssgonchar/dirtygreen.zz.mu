<?php
require_once APP_PATH . 'classes/models/steelitem.class.php';

class MainAjaxController extends ApplicationAjaxController
{    

    function MainAjaxController()
    {
        ApplicationAjaxController::ApplicationAjaxController();
                
        $this->authorize_before_exec['getinoutdata'] = ROLE_STAFF;
    }
    
    /**
     * Get data for In / Out report
     * url: /report/getinoutdata
     */
    function getinoutdata()
    {
        $owner          = Request::GetString('owner', $_REQUEST);
        $stockholder_id = Request::GetInteger('stockholder_id', $_REQUEST);
        
        $dimension_unit = ($owner == 'pa' ? 'in' : 'mm');
        
        $modelSteelItem = new SteelItem();
        $data           = $modelSteelItem->GetDataForInOutReport($owner, $stockholder_id);
        
        if ($stockholder_id > 0)
        {
            $modelCompany   = new Company();
            $company        = $modelCompany->GetById($stockholder_id);
            
            if (isset($company) && isset($company['company']))
            {
                if ($company['company']['country_id'] == 225)   // USA
                {
                    $dimension_unit = 'in';
                }
            }
        }
        
        $this->_send_json(array(
            'result'            => 'okay',
            'stockholders'      => $this->_prepare_list($data['stockholders'], 'stockholder', 'id', 'doc_no_full'),
            'countries'         => $this->_prepare_list($data['countries'], 'country'),
            'steelgrades'       => $this->_prepare_list($data['steelgrades'], 'steelgrade'),
            'suppliers'         => $this->_prepare_list($data['suppliers'], 'supplier'),
            'dimension_unit'    => $dimension_unit
        ));        
    }
}
