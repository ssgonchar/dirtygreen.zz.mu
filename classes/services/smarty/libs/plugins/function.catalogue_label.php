<?php

function smarty_function_catalogue_label($params, &$smarty)
{

    $number = isset($params['value']) ? $params['value'] : '';
    $label  = isset($params['label']) ? $params['label'] : '';

    $ending = '';
    if ($number % 10 == 0)
    {
        $ending = 'ов';
    }
    else if ($number % 100 > 10 && $number % 100 < 20)
    {
        $ending = 'ов';
    }
    else if ($number % 10 >= 5)
    {
        $ending = 'ов';
    }
    else if ($number % 10 == 1)
    {
        $ending = '';
    }
    else
    {
        $ending = 'а';
    }

    return $number . ' ' . $label . $ending;
}

/* vim: set expandtab: */

?>
