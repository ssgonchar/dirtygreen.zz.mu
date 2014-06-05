<?php

    function smarty_modifier_number_format($number, $decimals = 2, $clip = true, $delimiter = ',')
    {
	    if (!isset($number) || empty($number)) $number = 0;
        
        $number = str_replace(',', '', $number);
    	$arr    = explode('.', $number);
		
//		if (count($arr) > 1 && (int)$arr[1] == 0 && $clip)
		if ($clip && (!isset($arr[1]) || (int)$arr[1] == 0))
		{
			$number 	= $arr[0];
			$decimals	= 0;
		}

		return number_format($number, $decimals, '.', $delimiter);
    }