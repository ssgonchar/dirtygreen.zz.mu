<?php

function smarty_function_exectime($params, &$smarty)
{
    if (!isset($params['starttime']))
    {
        $smarty->trigger_error("eval: missing 'starttime' parameter");
        return;
    }

    if($params['starttime'] == '')
    {
        return;
    }

    list($usec, $sec) = explode(' ', microtime()); 
    return (((float)$usec + (float)$sec) - $params['starttime']) . ' s';
}

?>
