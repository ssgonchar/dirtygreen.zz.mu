<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 *
 * Формирует строку с названием статус пользователя.
 * Должен присутствовать параметр id.
 */

function smarty_function_user_status($params, &$smarty)
{

    if (!isset($params['id']))
    {
        $smarty->trigger_error("eval: missing 'id' parameter");
        return;
    }

    $id     = $params['id'];
    $name   = 'n/a';

    if ($id == USER_INITIAL)
    {
        $name = 'Не подтвердил email';
    }
    else if ($id == USER_PENDING)
    {
        $name = 'Ожидает активации';
    }
    else if ($id == USER_ACTIVE)
    {
        $name = 'Активный';
    }
    else if ($id == USER_BLOCKED)
    {
        $name = 'Заблокирован';
    }

    return $name;
}

/* vim: set expandtab: */

?>
