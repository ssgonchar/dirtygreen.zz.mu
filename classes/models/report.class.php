<?php
require_once APP_PATH . 'classes/models/steelitem.class.php';

define('REPORT_INOUT_TYPE_IN',      1);
define('REPORT_INOUT_TYPE_OUT',     2);
define('REPORT_INOUT_TYPE_SOLD',    3);

class Report extends Model
{
    public function Report()
    {
        Model::Model('');
    }
    
    /**
     * Возвращает набор данных для отчете Stock In Out
     * 
     * @param array $data Набор параметров
     * @return array
     */
    public function StockInOut($owner, $stockholder, $type, $date_from, $date_to, $steelgrade, $thickness, $width, $supplier, $buyer, $country, $dimension_type)
    {
        $hash       = 'report-stockinout-' . md5('owner-' . $owner . 'stockholder-' . $stockholder . 'type-' . $type . 'datefrom-' . $date_from 
                    . 'dateto-' . $date_to . 'steelgrade-' . $steelgrade . 'thickness-' . $thickness . 'width-' . $width . 'supplier-' . $supplier 
                    . 'buyer-' . $buyer . 'country-' . $country . 'dimensions-' . $dimension_type);
                    
        $cache_tags = array($hash, 'reports', 'steelitems');

        if ($owner == 'mam')
        {
            $owner = MAMIT_OWNER_ID . ',' . MAMUK_OWNER_ID;
        }
        else if ($owner == 'mamit')
        {
            $owner = MAMIT_OWNER_ID;
        }
        else if ($owner == 'mamuk')
        {
            $owner = MAMUK_OWNER_ID;
        }
        else if ($owner == 'pa')
        {
            $owner = PLATESAHEAD_OWNER_ID;
        }
        
        $thickness  = $this->_get_interval($thickness);
        $width      = $this->_get_interval($width);        
        
        if ($dimension_type == 'in')
        {
            $thickness['from']  = $thickness['from'] * 25.4;
            $thickness['to']    = $thickness['to'] * 25.4;
            
            $width['from']      = $width['from'] * 25.4;
            $width['to']        = $width['to'] * 25.4;            
        }
        
        $rowset = $this->_get_cached_data($hash, 'sp_report_stockinout', array($owner, $stockholder, $type, $date_from, $date_to, 
                                            $steelgrade, $thickness['from'], $thickness['to'], $width['from'], $width['to'], 
                                            $supplier, $buyer, $country), $cache_tags);
        $rowset = isset($rowset[0]) ? $rowset[0] : array();
        
        $modelCompany       = new Company();
        $rowset             = $modelCompany->FillCompanyInfoShort($rowset, 'owner_id', 'owner');
        $rowset             = $modelCompany->FillCompanyInfoShort($rowset, 'stockholder_id', 'stockholder');
        $rowset             = $modelCompany->FillCompanyInfoShort($rowset, 'supplier_id', 'supplier');
        $rowset             = $modelCompany->FillCompanyInfoShort($rowset, 'buyer_id', 'buyer');
        
        $modelSteelGrade    = new SteelGrade();
        $rowset             = $modelSteelGrade->FillSteelGradeInfo($rowset);
        
        $modelInDDT         = new InDDT();
        $rowset             = $modelInDDT->FillInDDTMainInfo($rowset, 'in_ddt_id', 'in_ddt');

        $modelUser          = new User();
        $rowset             = $modelUser->FillUserInfo($rowset, 'created_by', 'author');
//        dg($rowset);
        
        return $rowset;
    }
    
    /**
     * explode interval values
     * 
     * @param mixed $value
     * @return mixed
     */
    function _get_interval($value)
    {
        $value = preg_replace('#\s+#i', '', $value);
        if (empty($value)) return array('from' => 0, 'to' => 0);
        
        // 0.89
        preg_match("#^([0-9\.]+)$#si", $value, $matches);
        if (!empty($matches)) return array('from' => floatval($matches[1]), 'to' => floatval($matches[1]));

        // 0.65-0.89
        preg_match("#^([0-9\.]+)-([0-9\.]+)$#si", $value, $matches);
        if (!empty($matches)) return array('from' => floatval($matches[1]), 'to' => floatval($matches[2]));

        // >0.89
        preg_match("#^&gt;([0-9\.]+)$#si", $value, $matches);
        if (!empty($matches)) return array('from' => floatval($matches[1]), 'to' => 0);

        // <0.89
        preg_match("#^&lt;([0-9\.]+)$#si", $value, $matches);
        if (!empty($matches)) return array('from' => 0, 'to' => floatval($matches[1]));
        
        return array('from' => 0, 'to' => 0);
    }    
}