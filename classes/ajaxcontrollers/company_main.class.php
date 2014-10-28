<?php
require_once APP_PATH . 'classes/models/company.class.php';
require_once APP_PATH . 'classes/models/location.class.php';
require_once APP_PATH . 'classes/models/steelitem.class.php';
require_once APP_PATH . 'classes/models/country.class.php';

class MainAjaxController extends ApplicationAjaxController
{    

    function MainAjaxController()
    {
        ApplicationAjaxController::ApplicationAjaxController();
                
        $this->authorize_before_exec['getlistbytitle']  = ROLE_STAFF;
        $this->authorize_before_exec['getpersons']      = ROLE_STAFF;
    }
        
    /**
     * get companies list for biz
     * url: /company/getlistbytitle
     */
    function getlistbytitle()
    {
        $rows_count = Request::GetInteger('maxrows', $_REQUEST);    //6
        $title      = Request::GetString('title', $_REQUEST);
        $country_id = Request::GetInteger('country_id', $_REQUEST);
        $list_type  = Request::GetString('list_type', $_REQUEST);
        $title_field = Request::GetString('title_field', $_REQUEST, 'doc_no'); //doc_no
        
        $companies  = new Company();
        $list       = $companies->GetListByTitle($title, $rows_count);
        //dg($list);
	$country = new Country();
		
        if ($list_type == 'sibuyer')
        {
            if ($country_id <= 0)
            {
                $this->_send_json(array('result' => 'okay', 'list' => $list));
                exit;
            }
            
            $modelSteelitem = new SteelItem();
            $buyers_list    = $modelSteelitem->GetBuyersList($country_id);
            
            $tmp_list = array();
            foreach ($list as $lkey => $lvalue)
            {
                if (!isset($lvalue['company'])) continue;
                
                foreach ($buyers_list as $blkey => $blvalue)
                {
                    if ($lvalue['company']['id'] == $blvalue['company_id'])
                    {
                        $tmp_list[$lkey] = $lvalue;
                        unset($buyers_list[$blkey]);
                    }
                }
                
                if (empty($buyers_list)) break;
            }
            
            $list = $tmp_list;
        }
		
        foreach ($list as $key => $row)
        {
        if (isset($row['company']))
        {
            $row = $row['company'];
            $country_info=$country->GetById($row['country_id']);
            $list[$key]['company']['list_title'] = isset($row[$title_field]) ? $row[$title_field] : 'unknown field';
                            $list[$key]['country']=$country_info['country']['title'];
                    }
        }
        //debug('1682', $list);
        $this->_send_json(array('result' => 'okay', 'list' => $list));
    }
    
    /**
     * get persons list for company
     * url: /company/getpersons
     */
    function getpersons()
    {
        $company_id = Request::GetInteger('company_id', $_REQUEST);        
        $companies  = new Company();
        $persons    = $companies->GetPersons($company_id);
        
        $this->_send_json(array(
            'result' => 'okay', 
            'persons' => $this->_prepare_list($persons['data'], 'person', 'id', 'full_name')
        ));
    }
}
