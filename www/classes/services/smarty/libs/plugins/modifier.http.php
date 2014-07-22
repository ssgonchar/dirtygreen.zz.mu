<?php

function smarty_modifier_http($url)
{
    if (strpos($url, 'http') === 0 || strpos($url, 'https') === 0) return $url;

    return 'http://' . $url;
}