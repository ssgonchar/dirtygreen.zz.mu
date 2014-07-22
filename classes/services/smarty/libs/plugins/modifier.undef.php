<?php

/**
 * Заменяет пустое значение на <i>not defined</i>
 * 
 * @param mixed $str
 * @return mixed
 * 
 * @version 20120601, zharkov
 */
function smarty_modifier_undef($str)
{
    if (!isset($str) || empty($str)) return '<i style="color: #999;">not defined</i>';
    
    return $str;
}
