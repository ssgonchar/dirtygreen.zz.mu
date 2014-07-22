<?php

function smarty_modifier_quarter($quarter)
{
    if ($quarter == 1)
    {
        return 'JAN - MAR';
    }
    else if ($quarter == 2)
    {
        return 'APR - JUN';
    }
    else if ($quarter == 3)
    {
        return 'JUL - SEP';
    }
    else if ($quarter == 4)
    {
        return 'OCT - DEC';
    }
    
    return $quarter;
}
