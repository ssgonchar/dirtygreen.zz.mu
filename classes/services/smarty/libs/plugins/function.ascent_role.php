<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 *
 * Формирует строку с именем категории маршрута.
 * Должен присутствовать параметр id.
 */

function smarty_function_ascent_role($params, &$smarty)
{

    if (!isset($params['id']))
    {
        $smarty->trigger_error("eval: missing 'id' parameter");
        return;
    }

    $id     = $params['id'];
    $name   = 'n/a';

    if ($id == 1) $name = 'участник';
    else if ($id == 2) $name = 'руководитель';
    else if ($id == 3) $name = 'инструктор';
    else if ($id == 4) $name = 'в двойке';
    else if ($id == 5) $name = 'в составе спасотряда';

    return $name;

}

/* vim: set expandtab: */

?>
