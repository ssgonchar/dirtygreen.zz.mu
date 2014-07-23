<?php
function smarty_modifier_str_pad($string, $pad_length, $pad_string = ' ', $pad_type = 'r')
{
    return $string . str_repeat('&nbsp;', $pad_length - strlen($string));
}