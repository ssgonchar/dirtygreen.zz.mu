<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 *
 * Формирует строку с именем роли пользователя.
 * Должен присутствовать параметр id.
 */

function smarty_function_store_payment_type($params, &$smarty)
{

    if (!isset($params['value']))
    {
        $smarty->trigger_error("eval: missing 'value' parameter");
        return;
    }

    switch ($params['value'])
    {
        case 1 : return 'Наличными'; break;
        default : 'n/a';
    }
}

/* vim: set expandtab: */

?>
