<?php

/**
 * Заменяет пустое значение на <i>not defined</i>
 * 
 * @param mixed $str
 * @return mixed
 * 
 * @version 20120601, zharkov
 */
function smarty_modifier_smartfloat($value, $precision = 2)
{
    $params = explode('.', $value); 

    if (isset($params[1]) && $params[1] > 0)
    {
        return sprintf('%.' . $precision . 'f', round($value, $precision));
    }
    
    return round($value, 0);
}
