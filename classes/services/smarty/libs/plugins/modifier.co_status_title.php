<?php
function smarty_modifier_co_status_title($co_status_id)
{
    if (!isset($co_status_id)) return '<i style="color: #999;">not defined</i>';
    
    require_once APP_PATH . 'classes/models/company.class.php';
    
    switch ($co_status_id)
    {
        case CO_STATUS_BANKRUPT: return 'Bankrupt';
        case CO_STATUS_BLACK_LIST: return 'Black List';
        case CO_STATUS_CONTRACT: return 'Contract';
        case CO_STATUS_DONT_WANT_US: return 'Don\'t Want Us';
        case CO_STATUS_GONE_AWAY: return 'Gone Away';
        case CO_STATUS_KEY_PARTNER: return 'Key Partner';
        case CO_STATUS_LIQUIDATED: return 'Liquidated';
        case CO_STATUS_NEGOTIATION: return 'Negotiation';
        case CO_STATUS_NOT_DIALOG_YET: return 'No Dialogue Yet';
            
        default: return '<i style="color: #999;">not defined</i>';
    }
}