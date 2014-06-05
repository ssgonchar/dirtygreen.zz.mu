<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 *
 * Повторяет $symbol заданное $count раз.
 */

function smarty_function_repeat($params, &$smarty)
{
    $result = '';

    $symbol = isset($params['symbol']) ? $params['symbol'] : '';
    $count  = isset($params['count']) ? $params['count'] : 0;
    
    for ($i = 0; $i < $count; $i++)
    {
        $result = $result . $symbol;
    }

    return $result;
}

/* vim: set expandtab: */

?>
