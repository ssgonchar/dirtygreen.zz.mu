<?php
/**
 * Smarty plugin
 *
 * @package Smarty
 * @subpackage PluginsModifier
 */
 
/**
 * Smarty custom_number_format modifier plugin
 * 
 * Type:     modifier<br>
 * Name:     custom_number_format<br>
 * Purpose:  Format a number with grouped thousands.
 * 
 * @link http://smarty.php.net/manual/en/language.modifier.truncate.php truncate (Smarty online manual)
 * @param float $number <p>The number being formatted.</p>
 * @param int $decimals [optional] <p>Sets the number of decimal points.</p>
 * @param string $dec_point [optional] <p>Sets the separator for the decimal point.</p>
 * @param string $thousands_sep [optional] <p>Sets the thousands separator.</p>
 * @return string A formatted version of <i>number</i>.
 */
function smarty_modifier_custom_number_format($number, $decimals = 0, $dec_point = '.', $thousands_sep = ',') {
    
    return number_format ($number, $decimals, $dec_point, $thousands_sep);
}