<?php
function smarty_modifier_biz_format($string, $suffix = '', $remove = false)
{
    if (empty($suffix) && !$remove) return $string;
    
    $regex  = '#<a[^>]+href=([^ >]+)[^>]*>(.*?)</a>#si';
    preg_match_all($regex, $string, $matches);
    
    foreach ($matches[0] as $key => $match)
    {
        if ($remove)
        {
            $string = str_replace($match, $matches[2][$key], $string);
        }
        else
        {
            $string = str_replace($match, '<a href="' . str_replace(array('"', '\''), '', $matches[1][$key]) . (empty($suffix) ? '' : '/' . $suffix) . '">' . $matches[2][$key] . '</a>', $string);
        }
    }
    
    return $string;
}