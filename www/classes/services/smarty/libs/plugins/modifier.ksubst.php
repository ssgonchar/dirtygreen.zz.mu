<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */


/**
 * Smarty ksubst modifier plugin
 *
 * Type:     modifier<br>
 * Name:     repeat<br>
 * Purpose:  Replaces %%konst%% statements in string with corresponding constansts
 * @author   digi
 * @param string
 * @param string
 * @return string
 */
function smarty_modifier_ksubst($string)
{
    return preg_replace_callback(
        '/%%([a-zA-Z0-9_]+)%%/',
        create_function(
            '$matches',
            'return K::Get($matches[1]);'
        ),
        $string
    );
}

/* vim: set expandtab: */

?>
