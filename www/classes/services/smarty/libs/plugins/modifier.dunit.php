<?php

function smarty_modifier_dunit($length_unit)
{
    if ($length_unit == 'in')
    {
        return 'In';
    }

    return $length_unit;
}