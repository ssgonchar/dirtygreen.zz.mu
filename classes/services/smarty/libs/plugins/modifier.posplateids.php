<?php
function smarty_modifier_posplateids($str)
{
    $str = trim($str, ',');
    $str = str_replace(',', ', ', $str);
    
    return $str;
}
