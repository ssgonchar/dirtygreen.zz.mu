<?php
function smarty_modifier_co_type_title($co_type_id)
{
    if (!isset($co_type_id)) return '<i style="color: #999;">not defined</i>';
    
    require_once APP_PATH . 'classes/models/company.class.php';
    
    switch ($co_type_id)
    {
        case CO_TYPE_HOFFICE:
            return 'Head Office';
            
        case CO_TYPE_OFFICE:
            return 'Office';
            
        case CO_TYPE_PLANT:
            return 'Plant';
            
        case CO_TYPE_SUBSIDIARY:
            return 'Subsidiary';
            
        default: return '<i style="color: #999;">not defined</i>';
    }
}