<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 *
 * Формирует строку с нужным окончанием.
 * Пример: 0 комментариев, 1 комментарий, 2 комментария
 * Должны присутствовать три параметра e0, e1, e2, соответственно для окончаний при нуле, единице и двух.
 * Если указан параметр zero, то результирующая строка при нуле примет это значение
 */

function smarty_function_number($params, &$smarty)
{
    if (!isset($params['e0']))
    {
        $smarty->trigger_error("eval: missing 'e0' parameter");
        return;
    }

    if (!isset($params['e1']))
    {
        $smarty->trigger_error("eval: missing 'e1' parameter");
        return;
    }

    if (!isset($params['e2']))
    {
        $smarty->trigger_error("eval: missing 'e2' parameter");
        return;
    }

    if (!isset($params['value']))
    {
        if (!isset($params['default']))
        {
            $smarty->trigger_error("eval: missing 'value' parameter");
            return;
        }
        else
        {
            $params['value'] = $params['default'];
        }
    }

    $number = $params['value'];    
    $zero = isset($params['zero']) ? $params['zero'] : '';

    if ($number == 0 && isset($params['zero']))
    {
        return $zero;
    }

    $base = isset($params['base']) ? $params['base'] : '';
    $prefix = isset($params['prefix']) ? $params['prefix'] : '';
    $postfix = isset($params['postfix']) ? $params['postfix'] : '';

    $e0 = $params['e0'];
    $e1 = $params['e1'];
    $e2 = $params['e2'];

    $ending = '';
    if ($number % 10 == 0)
    {
        $ending = $e0;
    }
    else if ($number % 100 > 10 && $number % 100 < 20)
    {
        $ending = $e0;
    }
    else if ($number % 10 >= 5)
    {
        $ending = $e0;
    }
    else if ($number % 10 == 1)
    {
        $ending = $e1;
    }
    else
    {
        $ending = $e2;
    }

    return $prefix . $number . ' ' . $base . $ending . $postfix;
}

/* vim: set expandtab: */

?>
