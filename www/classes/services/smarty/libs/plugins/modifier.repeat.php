<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */


/**
 * Smarty repeat modifier plugin
 *
 * Type:     modifier<br>
 * Name:     repeat<br>
 * Purpose:  Repeat a string
 *          repeat (Smarty online manual)
 * @author   digi
 * @param string
 * @param integer
 * @return string
 */
function smarty_modifier_repeat($string, $multiplier)
{
    return str_repeat($string, $multiplier);
}

/* vim: set expandtab: */

?>
