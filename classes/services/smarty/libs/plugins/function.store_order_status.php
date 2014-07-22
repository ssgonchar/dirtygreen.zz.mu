<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 *
 * Формирует строку с именем роли пользователя.
 * Должен присутствовать параметр id.
 */

function smarty_function_store_order_status($params, &$smarty)
{

    if (!isset($params['value']))
    {
        $smarty->trigger_error("eval: missing 'value' parameter");
        return;
    }

    $text = '';
    switch ($params['value'])
    {
        case ORDER_STATUS_BASKET        : $text = 'Корзина'; break;
        case ORDER_STATUS_NEW           : $text = 'Новый'; break;
        case ORDER_STATUS_IN_PROCESS    : $text = 'На выполнении'; break;
        case ORDER_STATUS_PAID          : $text = 'Оплачен'; break;
        case ORDER_STATUS_DELIVERY      : $text = 'Доставляется'; break;
        case ORDER_STATUS_COMPLETED     : $text = 'Завершен'; break;
        case ORDER_STATUS_CANCELLED     : $text = 'Отменен'; break;       
        default : $text = 'n/a';
    }
    
    if (isset($params['lower']))
    {
        if (function_exists('mb_strtolower')) 
        {
            return mb_strtolower($text);
        } 
        else 
        {
            return strtolower($text);
        }         
    }
    
    return $text;
    
}

/* vim: set expandtab: */

?>
