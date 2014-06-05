<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */


/**
 * Smarty repeat function plugin
 *
 * Type:     function<br>
 * Name:     repeat<br>
 * Purpose:  Repeat a string
 *          repeat (Smarty online manual)
 * @author   digi
 * @param string
 * @param integer
 * @return string
 */
function smarty_function_custom_repeat($params, &$smarty)
{
    return str_repeat($params['symbol'], $params['count']);
}