<?php
function smarty_function_size_type_select($params, &$smarty)
{
    $name       = isset($params['name'])        ? $params['name'] : '';
    $class      = isset($params['class'])       ? $params['class'] : '';
    $style      = isset($params['style'])       ? $params['style'] : '';
    $onchange   = isset($params['onchange'])    ? $params['onchange'] : '';
    $value      = isset($params['value'])       ? $params['value'] : 0;


    $html       = '<select' 
                . (!empty($name) ? ' name="' . $name . '"' : '') 
                . (!empty($class) ? ' class="' . $class . '"' : '') 
                . (!empty($style) ? ' style="' . $style . '"' : '') 
                . (!empty($onchange) ? ' onchange="' . $onchange . '"' : '') 
                . '>';

    $html       .= '<option value="0"' . (empty($value) ? ' selected' : '') . '>--</option>';
    $html       .= '<option value="1"' . ($value == 1 ? ' selected' : '') . '>Размеры обуви</option>';
    $html       .= '<option value="2"' . ($value == 2 ? ' selected' : '') . '>Размеры одежды</option>';

    $html       .= '</select>';

    return $html;
}