<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 *
 * Формирует строку с именем роли пользователя.
 * Должен присутствовать параметр id.
 */

function smarty_function_user_role($params, &$smarty)
{

    if (!isset($params['id']))
    {
        $smarty->trigger_error("eval: missing 'id' parameter");
        return;
    }

    $id     = $params['id'];
    $name   = 'n/a';

    if ($id == ROLE_SUPER_ADMIN)
    {
        $name = 'Супер админ';
    }
    else if ($id == ROLE_ADMIN)
    {
        $name = 'Админ';
    }
    else if ($id == ROLE_SUPER_MODERATOR)
    {
        $name = 'Супер модератор';
    }
    else if ($id == ROLE_MODERATOR)
    {
        $name = 'Модератор';
    }
    else if ($id == ROLE_CONTENT_MANAGER)
    {
        $name = 'Контент менеджер';
    }
    else if ($id == ROLE_STORE_MANAGER)
    {
        $name = 'Менеджер магазина';
    }
    else if ($id == ROLE_SUPER_USER)
    {
        $name = 'Супер пользователь';
    }
    else if ($id == ROLE_USER)
    {
        $name = 'Пользователь';
    }
    else if ($id == ROLE_LIMITED_USER)
    {
        $name = 'Ограниченный пользователь';
    }
    else if ($id == ROLE_GUEST)
    {
        $name = 'Гость';
    }

    return $name;
}

/* vim: set expandtab: */

?>
