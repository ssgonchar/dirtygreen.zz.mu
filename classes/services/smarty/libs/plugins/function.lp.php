<?php
require_once APP_PATH . 'classes/models/lang.class.php';
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 *
 * Возвращает префикс текущего языка 
 */

function smarty_function_lp($params, &$smarty)
{
    /*
    $current_lang = Request::GetString('lang', $_REQUEST, '', 2);
    
    $lang   = new Lang();
    $langs  = $lang->GetList();
    
    if (count($langs) > 0) return '/' . $current_lang;
    */
    return '';
}
