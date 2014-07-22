<?php

function smarty_modifier_wunit($weight_unit)
{
    if ($weight_unit == 'mt')
    {
        return 'Ton';
    }

    return $weight_unit;
}
