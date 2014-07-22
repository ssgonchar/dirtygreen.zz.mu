<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */

function smarty_function_const($params, &$smarty)
{
    if (!isset($params['name']))
    {
        $smarty->trigger_error("eval: missing 'name' parameter");
        return;
    }

	$name = $params['name'];
    $assigned_vars = $smarty->_tpl_vars;

    if (!array_key_exists('constants', $assigned_vars))
    {
    	return 'undefined';
    }

    if (!is_array($assigned_vars['constants']))
    {
    	return 'undefined';    
    }

    $levels = explode('.', $name);
	
	$current = $assigned_vars['constants'];
	$level_count = count($levels);
	for ($i = 0; $i < $level_count; $i++)
	{
    	if (!array_key_exists($levels[$i], $current))
	    {
    		return 'undefined';
	    }

		if ($i == $level_count - 1)
		{
			if ( isset($params['param1']) )
			{
			    //print_r($params['param']);
				return sprintf( $current[$levels[$i]], $params['param1'] );
			}
			else
			{
				return $current[$levels[$i]];
			}
		}

		$current = $current[$levels[$i]];
	}
}

/* vim: set expandtab: */

?>
