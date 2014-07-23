<?php
function smarty_modifier_co_relation_title($co_relation_id)
{
    if (!isset($co_relation_id)) return '<i style="color: #999;">not defined</i>';
    
    require_once APP_PATH . 'classes/models/company.class.php';
    
    switch ($co_relation_id)
    {
        case CO_RELATION_MUST_HAVE : return 'Must Have Customer';
        case CO_RELATION_COMPETITOR : return 'Competitor';
        case CO_RELATION_LIVE_CUSTOMER : return 'Live Customer';
        case CO_RELATION_NOT_POTENTIAL_CUSTOMER : return 'Not a Potential Customer';
        case CO_RELATION_POTENTIAL_CUSTOMER : return 'Potential Customer';
        case CO_RELATION_SERVICE_PROVIDER : return 'Service Provider';
        case CO_RELATION_STOCK_AGENT : return 'Stock Agent';
        case CO_RELATION_SUPPLIER :return 'Supplier';
            
        default: return '<i style="color: #999;">not defined</i>';
    }
}