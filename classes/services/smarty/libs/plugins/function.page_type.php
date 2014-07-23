<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 *
 * Формирует строку с именем роли пользователя.
 * Должен присутствовать параметр id.
 */

function smarty_function_page_type($params, &$smarty)
{

    if (!isset($params['id']))
    {
        $smarty->trigger_error("eval: missing 'id' parameter");
        return;
    }

    $id     = $params['id'];
    $name   = 'n/a';

    if ($id == PAGE_TYPE_IN_MENU)
    {
        $name = 'Страница. Отображается в меню';
    }
    else if ($id == PAGE_TYPE_NOT_IN_MENU)
    {
        $name = 'Страница. Не отображается в меню';    
    }
    else if ($id == PAGE_TYPE_URL)
    {
        $name = 'Ссылка на контроллер. Отображается в меню';
    }

    return $name;
}

/* vim: set expandtab: */

?>
