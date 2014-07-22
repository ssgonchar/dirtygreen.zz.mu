<?php

function smarty_modifier_cursign($currency)
{
    if ($currency == 'eur')
    {
        return '&euro;';
    }
    else if ($currency == 'usd')
    {
        return '$';
    }
    else if ($currency == 'gbp')
    {
        return '&pound;';
    }
    
    return $currency;
}
