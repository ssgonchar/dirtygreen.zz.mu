<?php
require_once APP_PATH . 'classes/models/company.class.php';
require_once APP_PATH . 'classes/models/report.class.php';
require_once APP_PATH . 'classes/models/steelitem.class.php';
require_once APP_PATH . 'classes/models/stock.class.php';

class MainController extends ApplicationController
{
    
    function MainController()
    {
        ApplicationController::ApplicationController();
        
        $this->authorize_before_exec['index']           = ROLE_STAFF;
        $this->authorize_before_exec['stockvalue']      = ROLE_STAFF;
        $this->authorize_before_exec['stockcurrent']    = ROLE_STAFF;
        $this->authorize_before_exec['stockaudit']      = ROLE_STAFF;
        $this->authorize_before_exec['stockinout']      = ROLE_STAFF;
        
        $this->breadcrumb   = array('Reports' => '/reports');
        $this->context      = true;       
    }

    /**
     * Отображает индексную страницу отчетов
     * url: /reports
     * 
     * @version 20121116, zharkov
     */
    function index()
    {        
        $this->page_name = 'Reports';
        $this->breadcrumb[$this->page_name] = '';
        
        $this->_display('index');
    }
    
    /**
     * Отображает страницу отчета STOCK AUDIT
     * url: /report/stockaudit
     * 
     * @version 20121126, zharkov
     */    
    function stockaudit()
    {
        if (isset($_REQUEST['btn_generate']))
        {
            $usd_eur    = Request::GetNumeric('usd_eur', $_REQUEST);
            $usd_gbp    = Request::GetNumeric('usd_gbp', $_REQUEST);
            $eur_gbp    = Request::GetNumeric('eur_gbp', $_REQUEST);
            $eur_usd    = Request::GetNumeric('eur_usd', $_REQUEST);

            $data       = (empty($usd_eur) ? '' : 'usd_eur:' . $usd_eur . ';')
                        . (empty($usd_gbp) ? '' : 'usd_gbp:' . $usd_gbp . ';')
                        . (empty($eur_gbp) ? '' : 'eur_gbp:' . $eur_gbp . ';')
                        . (empty($eur_usd) ? '' : 'eur_usd:' . $eur_usd . ';');
            
            $this->_redirect(array('report', 'stockaudit', 'data', $data), false);
        }
                
        $data           = Request::GetString('data', $_REQUEST);
        $stockholder_id = 0;
        
        if (!empty($data))
        {
            $data = explode(';', $data);
            
            foreach ($data as $row)
            {
                if (empty($row)) continue;
                
                $param = explode(':', $row);
                
                if ($param[0] == 'stockholder') 
                {
                    $stockholder_id = Request::GetInteger(1, $param);
                    break;
                }

                $currency_rates[$param[0]] = Request::GetNumeric(1, $param);
            }
            
            $this->_assign('data',              $currency_rates);
            $this->_assign('stockholder_id',    $stockholder_id);
            
            $base_url = $this->pager_path;
            
            $modelSteelItem = new SteelItem();
            $this->_assign('stockholders', $modelSteelItem->GetStockholdersForReportAudit(STOCK_EU));
            
            $wo_stockholder_id      = 0;
            $wo_owner_id            = 0;
            $wo_price               = 0;
            $wo_purchase_price      = 0;
            $wo_purchase_currency   = 0;
                            
            $total      = array('qtty' => 0, 'weight' => 0, 'weight_ton' => 0, 'price_sum' => 0, 'p_price_sum' => 0, 'v_price_sum' => 0, 'value' => 0);
            $owners     = array(
                'total' => $total,
                'data'  => array()
            );
            
            $modelCompany   = new Company();
            $companies      = array();
            
            $wo_data = array(); //test

            foreach ($modelSteelItem->GetListForReportAudit($stockholder_id) as $key => $row)
            {
                if (empty($row['price'])) $wo_price++;
                if (empty($row['purchase_price'])) $wo_purchase_price++;
                if (empty($row['purchase_currency'])) $wo_purchase_currency++;
                if (empty($row['owner_id'])) $wo_owner_id++;
                if (empty($row['stockholder_id'])) $wo_stockholder_id++;

                if (empty($row['owner_id']) || empty($row['stockholder_id'])) 
                {
                    $wo_data[] = $row; //test
                    continue;
                }
                
                $stockholder_id     = $row['stockholder_id'];
                $owner_id           = $row['owner_id'];
                $currency           = $row['currency'];
                $purchase_currency  = $row['purchase_currency'];

                if (!isset($companies[$owner_id]))
                {
                    $companies[$owner_id] = $modelCompany->GetById($owner_id);
                }

                if (!isset($companies[$stockholder_id]))
                {
                    $companies[$stockholder_id] = $modelCompany->GetById($stockholder_id);
                }                
                                    
                if (!isset($owners['data'][$owner_id])) $owners['data'][$owner_id] = array(
                    'total'         => $total, 
                    'data'          => array(),
                    'stockholder'   => array()
                );

                if (!isset($owners['data'][$owner_id]['stockholder'][$stockholder_id])) $owners['data'][$owner_id]['stockholder'][$stockholder_id] = array(
                    'total'         => $total
                );
                
                
                if ($row['order_price'] > 0)
                {
                    $row['real_price']          = $row['order_price'];
                    $row['real_currency']       = $row['order_currency'];
                    $row['real_price_unit']     = $row['order_price_unit'];                    
                    $row['real_weight_unit']    = $row['order_weight_unit'];
                }
                else
                {
                    $row['real_price']          = $row['price'];
                    $row['real_currency']       = $row['currency'];
                    $row['real_price_unit']     = $row['price_unit'];
                    $row['real_weight_unit']    = $row['weight_unit'];
                }
                
                
                // unitweight
                $unitweight_ton = $row['unitweight_ton'];
                                
                if ($row['weight_unit'] == 'lb')
                {
                    $unitweight_ton = $row['unitweight'] / 2204;
                }
                
                
                // current price
                $price = $this->_convert_price_to_mt($row['price'], $row['price_unit']);
                
                if (empty($currency))
                {
                    $price_eur = 0;
                }
                else if ($currency != 'eur')
                {
                    $price_eur = $price * $currency_rates[$currency . '_eur'];
                }
                else
                {
                    $price_eur = $price;
                }
                
                $value_eur = $price_eur * $unitweight_ton;
                
                $row['price']       = $price;
                $row['price_eur']   = $price_eur;
                $row['value_eur']   = $value_eur;
                
                
                // real price
                $real_price     = $this->_convert_price_to_mt($row['real_price'], $row['real_price_unit']);
                $real_currency  = $row['real_currency'];
                
                if (empty($real_currency))
                {
                    $real_price_eur = 0;
                }
                else if ($real_currency != 'eur')
                {
                    $real_price_eur = $real_price * $currency_rates[$real_currency . '_eur'];
                }
                else
                {
                    $real_price_eur = $real_price;
                }
                
                $real_value_eur = $real_price_eur * $unitweight_ton;
                
                $row['real_price_eur']   = $real_price_eur;
                $row['real_value_eur']   = $real_value_eur;

                
                if (empty($purchase_currency))
                {
                    $row['purchase_price_eur'] = 0;
                }
                else if ($purchase_currency == 'eur')
                {
                    $row['purchase_price_eur'] = $row['purchase_price'];
                }
                else
                {
                    $row['purchase_price_eur'] = $row['purchase_price'] * $currency_rates[$purchase_currency . '_eur'];
                }

                $row['purchase_value_eur']  = $row['purchase_price_eur'] * $unitweight_ton;
                $row['valuation_price_eur'] = $row['purchase_price_eur'] > 0 ? min($row['real_price_eur'], $row['purchase_price_eur']) : $row['real_price_eur'];    // both prices for MT
                $row['valuation_value_eur'] = $row['valuation_price_eur'] * $unitweight_ton;
                $row['unitweight_ton']      = $unitweight_ton;
//if ($row['steelitem_id'] == 11531) dg($row);
                $owners['data'][$owner_id]['company']               = $companies[$owner_id];
                $owners['data'][$owner_id]['total']['qtty']        += 1;
                $owners['data'][$owner_id]['total']['weight']      += $row['unitweight'];
                $owners['data'][$owner_id]['total']['weight_ton']  += $row['unitweight_ton'];
                $owners['data'][$owner_id]['total']['price_sum']   += $row['real_price_eur'];
                $owners['data'][$owner_id]['total']['p_price_sum'] += $row['purchase_price_eur'];
                $owners['data'][$owner_id]['total']['v_price_sum'] += $row['valuation_price_eur'];
                $owners['data'][$owner_id]['total']['value']       += $row['valuation_value_eur'];
                $owners['data'][$owner_id]['data'][]                = $row;                    
                
                $owners['data'][$owner_id]['stockholder'][$stockholder_id]['company']               = $companies[$stockholder_id];
                $owners['data'][$owner_id]['stockholder'][$stockholder_id]['total']['qtty']        += 1;
                $owners['data'][$owner_id]['stockholder'][$stockholder_id]['total']['weight']      += $row['unitweight'];
                $owners['data'][$owner_id]['stockholder'][$stockholder_id]['total']['weight_ton']  += $row['unitweight_ton'];
                $owners['data'][$owner_id]['stockholder'][$stockholder_id]['total']['price_sum']   += $row['real_price_eur'];
                $owners['data'][$owner_id]['stockholder'][$stockholder_id]['total']['p_price_sum'] += $row['purchase_price_eur'];
                $owners['data'][$owner_id]['stockholder'][$stockholder_id]['total']['v_price_sum'] += $row['valuation_price_eur'];
                $owners['data'][$owner_id]['stockholder'][$stockholder_id]['total']['value']       += $row['valuation_value_eur'];

                $owners['total']['qtty']        += 1;
                $owners['total']['weight']      += $row['unitweight'];
                $owners['total']['weight_ton']  += $row['unitweight_ton'];
                $owners['total']['price_sum']   += $row['real_price_eur'];
                $owners['total']['p_price_sum'] += $row['purchase_price_eur'];
                $owners['total']['v_price_sum'] += $row['valuation_price_eur'];
                $owners['total']['value']       += $row['valuation_value_eur'];                    
            }
/*
            $str_wo_data_ids = '';
            foreach ($wo_data as $row)
            {
                $str_wo_data_ids .= $row['steelitem_id'] . ', ';
            }
            
            //dg($wo_data);
            dg($str_wo_data_ids);
*/
//dg($owners);
            $this->_assign('owners',                $owners);            
            $this->_assign('wo_stockholder_id',     $wo_stockholder_id);
            $this->_assign('wo_owner_id',           $wo_owner_id);
            $this->_assign('wo_price',              $wo_price);
            $this->_assign('wo_purchase_price',     $wo_purchase_price);
            $this->_assign('wo_purchase_currency',  $wo_purchase_currency);
            
            $base_url = str_replace('stockholder:' . $stockholder_id, '', $base_url);
            $base_url = str_replace(';;', ';', $base_url);
            
            $this->_assign('base_url', $base_url);
        }
        
        $this->page_name = 'Stock Audit Report';
        $this->breadcrumb[$this->page_name] = '';

        $this->_display('stockaudit');        
    }
    
    /**
     * Отображает страницу отчета STOCK CURRENT VALUE
     * url: /report/stockcurrent
     * 
     * @version 20121116, zharkov
     */
    function stockcurrent()
    {        
        if (isset($_REQUEST['btn_generate']))
        {
            $usd_eur    = Request::GetNumeric('usd_eur', $_REQUEST);
            $usd_gbp    = Request::GetNumeric('usd_gbp', $_REQUEST);
            $eur_gbp    = Request::GetNumeric('eur_gbp', $_REQUEST);
            $eur_usd    = Request::GetNumeric('eur_usd', $_REQUEST);

            $data       = (empty($usd_eur) ? '' : 'usd_eur:' . $usd_eur . ';')
                        . (empty($usd_gbp) ? '' : 'usd_gbp:' . $usd_gbp . ';')
                        . (empty($eur_gbp) ? '' : 'eur_gbp:' . $eur_gbp . ';')
                        . (empty($eur_usd) ? '' : 'eur_usd:' . $eur_usd . ';');
            
            $this->_redirect(array('report', 'stockcurrent', 'data', $data), false);
        }
                
        $data = Request::GetString('data', $_REQUEST);
        
        if (!empty($data))
        {
            $data = explode(';', $data);
            
            foreach ($data as $row)
            {
                if (empty($row)) continue;
                
                $param = explode(':', $row);
                $currency_rates[$param[0]] = Request::GetNumeric(1, $param);
            }
            
            $this->_assign('data', $currency_rates);
            
            $modelCompany   = new Company();
            $modelSteelItem = new SteelItem();
            
            $total      = array('qtty' => 0, 'weight' => 0, 'weight_ton' => 0, 'price_sum' => 0, 'p_price_sum' => 0, 'v_price_sum' => 0, 'value' => 0);
            
            $locations  = array(
                'total' => $total,
                'data'  => array()
            );
            
            $owners     = array(
                'total' => $total,
                'data'  => array()
            );
            
            $wo_stockholder_id      = 0;
            $wo_owner_id            = 0;
            $wo_price               = 0;
            $wo_purchase_price      = 0;
            $wo_purchase_currency   = 0;
            
            foreach ($modelSteelItem->GetListForReportCurrent() as $key => $row)
            {
                $row = $row['steelitem'];
                unset($row['properties']);
                
                if (empty($row['price'])) $wo_price++;
                if (empty($row['owner_id'])) $wo_owner_id++;
                if (empty($row['purchase_price'])) $wo_purchase_price++;
                if (empty($row['purchase_currency'])) $wo_purchase_currency++;
                
                if (empty($row['stockholder_id'])) 
                {
                    $wo_stockholder_id++;
                    continue;
                }
                
                $stockholder_id     = $row['stockholder_id'];
                $owner_id           = $row['owner_id'];
                $currency           = $row['currency'];
                $purchase_currency  = $row['purchase_currency'];
                
                if (!isset($locations['data'][$stockholder_id])) $locations['data'][$stockholder_id] = array(
                    'total' => $total, 
                    'data'  => array()
                );

//...                
                if ($row['weight_unit'] == 'mt')
                {
                    if (empty($currency))
                    {
                        $row['price_eur'] = 0;
                    }
                    else if ($currency != 'eur')
                    {
                        $row['price_eur'] = $row['price'] * $currency_rates[$currency . '_eur'];
                    }
                    else
                    {
                        $row['price_eur'] = $row['price'];
                    }
                    
                    $row['value_eur'] = $row['price_eur'] * $row['unitweight_ton'];
                }
                else if ($row['weight_unit'] == 'lb')
                {
                    $factor                 = 2204;                    
                    $row['unitweight_ton']  = $row['unitweight'] / $factor;
                    
                    if (empty($currency))
                    {
                        $row['price_eur'] = 0;
                    }
                    else if ($currency == 'eur')
                    {
                        $row['price_eur'] = $row['price'] * $factor;
                    }
                    else
                    {
                        $row['price_eur'] = $row['price'] * $factor * $currency_rates[$currency . '_eur'];                        
                    }
                    
                    $row['value_eur'] = $row['price_eur'] * $row['unitweight_ton'];
                }
                

                if (empty($purchase_currency))
                {
                    $row['purchase_price_eur'] = 0;
                }
                else if ($purchase_currency == 'eur')
                {
                    $row['purchase_price_eur'] = $row['purchase_price'];
                }
                else
                {
                    $row['purchase_price_eur'] = $row['purchase_price'] * $currency_rates[$purchase_currency . '_eur'];
                }
                
                $row['purchase_value_eur']  = $row['purchase_price_eur'] * $row['unitweight_ton'];                
                $row['valuation_price_eur'] = $row['purchase_price_eur'] > 0 ? min($row['price_eur'], $row['purchase_price_eur']) : $row['price_eur'];
                $row['valuation_value_eur'] = $row['valuation_price_eur'] * $row['unitweight_ton'];


                $locations['data'][$stockholder_id]['company']  = $row['stockholder'];
                $locations['data'][$stockholder_id]['data'][]   = $row;
                
                $locations['data'][$stockholder_id]['total']['qtty']        += 1;
                $locations['data'][$stockholder_id]['total']['weight']      += $row['unitweight'];
                $locations['data'][$stockholder_id]['total']['weight_ton']  += $row['unitweight_ton'];
                $locations['data'][$stockholder_id]['total']['price_sum']   += $row['price_eur'];
                $locations['data'][$stockholder_id]['total']['p_price_sum'] += $row['purchase_price_eur'];
                $locations['data'][$stockholder_id]['total']['v_price_sum'] += $row['valuation_price_eur'];
                $locations['data'][$stockholder_id]['total']['value']       += $row['valuation_value_eur'];
                
                $locations['data'][$stockholder_id]['dimension_unit']   = $row['dimension_unit'];
                $locations['data'][$stockholder_id]['weight_unit']      = $row['weight_unit'];
                $locations['data'][$stockholder_id]['currency']         = $row['currency'];

                $locations['total']['qtty']        += 1;
                $locations['total']['weight']      += $row['unitweight'];
                $locations['total']['weight_ton']  += $row['unitweight_ton'];
                $locations['total']['price_sum']   += $row['price_eur'];
                $locations['total']['p_price_sum'] += $row['purchase_price_eur'];
                $locations['total']['v_price_sum'] += $row['valuation_price_eur'];
                $locations['total']['value']       += $row['valuation_value_eur'];
                
                
                if (!isset($row['owner_id']) || empty($row['owner_id']) || $row['owner_id'] <= 0) continue;
				
                if (!isset($owners['data'][$owner_id])) $owners['data'][$owner_id] = array(
                    'total' => $total, 
                    'data'  => array()
                );

                if (!isset($owners['data'][$owner_id]['data'][$stockholder_id])) $owners['data'][$owner_id]['data'][$stockholder_id] = array(
                    'total' => $total, 
                    'data'  => array()
                );
                
                $owners['data'][$owner_id]['data'][$stockholder_id]['company']  = $row['stockholder'];
                $owners['data'][$owner_id]['data'][$stockholder_id]['total']['qtty']        += 1;
                $owners['data'][$owner_id]['data'][$stockholder_id]['total']['weight']      += $row['unitweight'];
                $owners['data'][$owner_id]['data'][$stockholder_id]['total']['weight_ton']  += $row['unitweight_ton'];
                $owners['data'][$owner_id]['data'][$stockholder_id]['total']['price_sum']   += $row['price_eur'];
                $owners['data'][$owner_id]['data'][$stockholder_id]['total']['p_price_sum'] += $row['purchase_price_eur'];
                $owners['data'][$owner_id]['data'][$stockholder_id]['total']['v_price_sum'] += $row['valuation_price_eur'];
                $owners['data'][$owner_id]['data'][$stockholder_id]['total']['value']       += $row['valuation_value_eur'];

                $owners['data'][$owner_id]['company']               = $row['owner'];
                $owners['data'][$owner_id]['total']['qtty']        += 1;
                $owners['data'][$owner_id]['total']['weight']      += $row['unitweight'];
                $owners['data'][$owner_id]['total']['weight_ton']  += $row['unitweight_ton'];
                $owners['data'][$owner_id]['total']['price_sum']   += $row['price_eur'];
                $owners['data'][$owner_id]['total']['p_price_sum'] += $row['purchase_price_eur'];
                $owners['data'][$owner_id]['total']['v_price_sum'] += $row['valuation_price_eur'];
                $owners['data'][$owner_id]['total']['value']       += $row['valuation_value_eur'];

                $owners['total']['qtty']        += 1;
                $owners['total']['weight']      += $row['unitweight'];
                $owners['total']['weight_ton']  += $row['unitweight_ton'];
                $owners['total']['price_sum']   += $row['price_eur'];
                $owners['total']['p_price_sum'] += $row['purchase_price_eur'];
                $owners['total']['v_price_sum'] += $row['valuation_price_eur'];
                $owners['total']['value']       += $row['valuation_value_eur'];
            }

            $this->_assign('locations', $locations);
            $this->_assign('owners',    $owners);

            $this->_assign('wo_stockholder_id', $wo_stockholder_id);
            $this->_assign('wo_owner_id',       $wo_owner_id);
            $this->_assign('wo_price',          $wo_price);
            $this->_assign('wo_purchase_price', $wo_purchase_price);
            $this->_assign('wo_purchase_currency', $wo_purchase_currency);
        }

        $this->page_name = 'Stock Current Value Report';
        $this->breadcrumb[$this->page_name] = '';

        $this->_display('stockcurrent');
    }
    
    /**
     * Отображает страницу отчета STOCK IN OUT
     * url: /report/stockinout
     * 
     * @version 20130213, d10n
     */
    public function stockinout()
    {
        $form_defaults = array(
            'owner'         => '',
            'type'          => 0,
            'stockholder'   => 0,
            'datefrom'      => 0,
            'dateto'        => 0,
            'supplier'      => 0,
            'buyer'         => '',
            'buyer_id'      => 0,
            'country'       => 0,
        );
        
        $form = isset($_REQUEST['form']) ? $_REQUEST['form'] : $form_defaults;
        
        if (isset($_REQUEST['btn_generate']))
        {
            $owner          = Request::GetString('owner', $form);
            $stockholder    = Request::GetInteger('stockholder', $form);
            $type           = Request::GetInteger('type', $form);
            $date_from      = Request::GetDateForDB('datefrom', $form);
            $date_to        = Request::GetDateForDB('dateto', $form);
            $steelgrade     = Request::GetInteger('steelgrade', $form);
            $thickness      = Request::GetString('thickness', $form);
            $width          = Request::GetString('width', $form);
            $supplier       = Request::GetInteger('supplier', $form);
            $buyer          = Request::GetString('buyer', $form);
            $buyer_id       = Request::GetInteger('buyer_id', $form);
            $buyer_id       = empty($buyer) ? 0 : $buyer_id;
            $country        = Request::GetInteger('country', $form);
            $dimensions     = Request::GetString('dimensions', $form);
            
            $data       = ($type > 0 ? 'type:' . $type . ';' : '')
                        . ($stockholder > 0 ? 'stockholder:' . $stockholder . ';' : '')
                        . (empty($date_from) ? '' : 'datefrom:' . date('Y-m-d', strtotime($date_from)) . ';')
                        . (empty($date_to) ? '' : 'dateto:' . date('Y-m-d', strtotime($date_to)) . ';')
                        . ($steelgrade > 0 ? 'steelgrade:' . $steelgrade . ';' : '')
                        . (empty($thickness) ? '' : 'thickness:' . $thickness . ';')
                        . (empty($width) ? '' : 'width:' . $width . ';')
                        . ($supplier > 0 ? 'supplier:' . $supplier . ';' : '')
                        . ($buyer_id > 0 ? 'buyer:' . $buyer_id . ';' : '')
                        . ($country > 0 ? 'country:' . $country . ';' : '')
                        . (empty($owner) ? '' : 'owner:' . $owner . ';')
                        . 'dimensions:' . (in_array($dimensions, array('mm', 'in')) ? $dimensions : 'mm') . ';';
            
            $this->_redirect(array('report', 'stockinout', 'data', $data), false);
        }
        
        $data = Request::GetString('data', $_REQUEST);
        
        if (!empty($data))
        {
            $exploded   = explode(';', $data);
            $data       = array();
            foreach($exploded as $param)
            {
                if (empty($param)) continue;

                list($key, $value) = explode(':', $param);
                $data[$key] = $value;
            }

            $form           = array_merge($form_defaults, $data);
            $report_data    = array();
            
            $owner          = Request::GetString('owner', $data);
            $stockholder    = Request::GetInteger('stockholder', $data);
            $type           = Request::GetInteger('type', $data);
            $date_from      = Request::GetString('datefrom', $data); 
            $date_from      = !(preg_match('/\d{4}-\d{2}-\d{2}/', $date_from)) ? null : $date_from . ' 00:00:00';
            $date_to        = Request::GetString('dateto', $data);
            $date_to        = !(preg_match('/\d{4}-\d{2}-\d{2}/', $date_to)) ? null : $date_to . ' 00:00:00';
            $steelgrade     = Request::GetInteger('steelgrade', $data);
            $thickness      = Request::GetString('thickness', $data);
            $width          = Request::GetString('width', $data);
            $supplier       = Request::GetInteger('supplier', $data);
            $buyer          = Request::GetInteger('buyer', $data);
            $country        = Request::GetInteger('country', $data);
            $dimensions     = Request::GetString('dimensions', $data);
            

            
            if (!in_array($type, array(REPORT_INOUT_TYPE_IN, REPORT_INOUT_TYPE_OUT, REPORT_INOUT_TYPE_SOLD)))
            {
                $this->_message('Please specify report type !', MESSAGE_ERROR);
            }
            else if ($stockholder <= 0 && in_array($type, array(REPORT_INOUT_TYPE_IN, REPORT_INOUT_TYPE_OUT)))
            {
                $this->_message('Please specify stockholder !', MESSAGE_ERROR);
            }
            else
            {
                $modelReport = new Report();
                $report_data = $modelReport->StockInOut($owner, $stockholder, $type, $date_from, $date_to, $steelgrade, 
                                                        $thickness, $width, $supplier, $buyer, $country, $dimensions);

                if ($owner > 0 || $stockholder > 0 || $date_from > 0 || $date_to > 0 || $steelgrade > 0 || !empty($thickness) || !empty($width) || $supplier > 0 || $buyer > 0 || $country > 0)
                {
                    $this->_assign('filter', true);
                }
                                                        
                $dimensions = array();
                $weights    = array();
                $currencies = array();
                $pcurrency  = array();
                $scurrency  = array();
                
                $total      = array();
//dg($report_data);
                foreach ($report_data as $row)
                {
                    $stock_price    = $row['stock_price'];
                    $stock_currency = $row['stock_currency'];
                    
                    if (!empty($stock_currency))
                    {
                        $currencies[$stock_currency] = $stock_currency;    
                    }
                    
                    if (!empty($row['dimension_unit']))
                    {
                        $dimensions[$row['dimension_unit']] = $row['dimension_unit'];
                    }
                    
                    if (!empty($row['weight_unit']))
                    {
                        $weight_unit            = $row['weight_unit'];                        
                        $weights[$weight_unit]  = $weight_unit;
             
                        if (!isset($total[$weight_unit . '-' . $stock_currency])) 
                        {
                            $total[$weight_unit . '-' . $stock_currency] = array(
                                'qtty'      => 0,
                                'price'     => 0,
                                'weight'    => 0,
                                'value'     => 0,
                                'currency'  => ''
                            );
                        }
                                               
                        $total[$weight_unit . '-' . $stock_currency]['qtty']     += 1;
                        $total[$weight_unit . '-' . $stock_currency]['price']    += $stock_price;
                        $total[$weight_unit . '-' . $stock_currency]['weight']   += $row['unitweight'];
                        $total[$weight_unit . '-' . $stock_currency]['value']    += ($row['unitweight'] * $stock_price)/100;
                        $total[$weight_unit . '-' . $stock_currency]['currency'] = $stock_currency;
                        $total[$weight_unit . '-' . $stock_currency]['unit']     = $row['weight_unit'];                        
                    }
                    
                    if (!empty($row['purchase_currency']))
                    {
                        $pcurrency[$row['purchase_currency']] = $row['purchase_currency'];
                    }
                }

                $this->_assign('report_data',   $report_data);
                $this->_assign('total',         array_values($total));
                
                if (count($dimensions) == 1)
                {
                    $dimensions = array_keys($dimensions);
                    $this->_assign('dimension_unit', $dimensions[0]);
                }
                
                if (count($weights) == 1)
                {
                    $weights = array_keys($weights);
                    $this->_assign('weight_unit', $weights[0]);
                }

                if (count($currencies) == 1)
                {
                    $currencies = array_keys($currencies);
                    $this->_assign('currency', $currencies[0]);
                }                
                
                if (count($pcurrency) == 1)
                {                                        
                    $pcurrency = array_keys($pcurrency); 
                    $this->_assign('pcurrency', $pcurrency[0]);
                }                
                
            }
            
            if (isset($data['buyer']) && $data['buyer'] > 0)
            {
                $modelCompany       = new Company();
                $buyer              = $modelCompany->GetById($data['buyer']);
                $form['buyer']      = isset($buyer['company']) ? $buyer['company']['doc_no'] : null;
                $form['buyer_id']   = isset($buyer['company']) ? $buyer['company']['id'] : null;
            }
        }
        
        $modelSteelItem = new SteelItem();
        $data           = $modelSteelItem->GetDataForInOutReport($form['owner'], $form['stockholder']);

        $this->_assign('stockholders_list', $data['stockholders']);
        $this->_assign('suppliers_list',    $data['suppliers']);
        $this->_assign('countries_list',    $data['countries']);
        $this->_assign('steelgrades_list',  $data['steelgrades']);

        $this->page_name = 'In / Out Report';
        $this->breadcrumb[$this->page_name] = '';
        $this->js = 'report_inout';
        
        $this->_assign('include_ui',    true);
        $this->_assign('include_prettyphoto',    true);
        $this->_assign('form', $form);
        
        $this->_assign('baddatastat', $modelSteelItem->GetBadDataStat());
                
        $this->_display('stockinout');
    }    
    
    function _convert_price_to_mt($price, $price_unit)
    {
        if ($price_unit == 'lb')
        {
            return $price * 2204;
        }
        else if ($price_unit == 'cwt')
        {
            return $price * 2204 / 100;
        }
        else
        {
            return $price;
        }
    }
}
