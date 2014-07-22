<?php

require_once(APP_PATH . 'classes/core/K.class.php');

/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 *
 * Возвращает контент константы.
 * Должен присутствовать параметр name.
 */

function smarty_function_k($params, &$smarty)
{

    if (!isset($params['name']))
    {
        $smarty->trigger_error("eval: missing 'name' parameter");
        return;
    }

    $alias  = $params['name'];
    $k      = $alias;

    $p1     = empty($params['p1']) ? '' : $params['p1'];
    $p2     = empty($params['p2']) ? '' : $params['p2'];
    $p3     = empty($params['p3']) ? '' : $params['p3'];
    $p4     = empty($params['p4']) ? '' : $params['p4'];
    $p5     = empty($params['p5']) ? '' : $params['p5'];
    $p6     = empty($params['p6']) ? '' : $params['p6'];
    $p7     = empty($params['p7']) ? '' : $params['p7'];
    $p8     = empty($params['p8']) ? '' : $params['p8'];
    $p9     = empty($params['p9']) ? '' : $params['p9'];

    return K::Get($alias, $p1, $p2, $p3, $p4, $p5, $p6, $p7, $p8, $p9);
}

/* vim: set expandtab: */

?>
