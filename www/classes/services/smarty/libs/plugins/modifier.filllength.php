<?php
function smarty_modifier_filllength($string, $count = false)
{
    $length = strlen($string);
    
    if ($include_spaces) return array();

    return preg_match_all("/[^\s]/",$string, $match);
}