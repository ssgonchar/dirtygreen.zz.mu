<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 *
 * Формирует строку с именем роли пользователя.
 * Должен присутствовать параметр id.
 */

function smarty_function_moderate_status($params, &$smarty)
{

    if (!isset($params['value']))
    {
        $smarty->trigger_error("eval: missing 'value' parameter");
        return;
    }

    $value  = $params['value'];
    $name   = 'undefined!';

    if ($value == MODERATE_STATUS_NEW)
    {
        $name = 'Новая';
    }
    else if ($value == MODERATE_STATUS_ACTIVE)
    {
        $name = 'Активная';
    }
    else if ($value == MODERATE_STATUS_BANNED)
    {
        $name = 'Заблокированная';
    }
    else if ($value == MODERATE_STATUS_DELETED)
    {
        $name = 'Удаленная';
    }

    return $name;
}

/* vim: set expandtab: */

?>
