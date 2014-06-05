<?
function smarty_function_moderate_status_select($params, &$smarty)
{
    require_once 'function.moderate_status.php';//$smarty->_get_plugin_filepath('function','moderate_status'); 04.05.2010 alexandr
    
    $name   		= isset($params['name']) ? $params['name'] : 'moderate_status';
    $class  		= isset($params['class']) ? $params['class'] : '';
    $value  		= isset($params['value']) ? $params['value'] : 0;    
	$premoderate	= isset($params['premoderate']) ? $params['premoderate'] : true;
    
	
	$list[0] = '--';
	
	if ($premoderate)
	{
		$list[MODERATE_STATUS_NEW] = smarty_function_moderate_status(array('value' => MODERATE_STATUS_NEW), $smarty);
	}
	
	$list[MODERATE_STATUS_ACTIVE] = smarty_function_moderate_status(array('value' => MODERATE_STATUS_ACTIVE), $smarty);
	$list[MODERATE_STATUS_BANNED] = smarty_function_moderate_status(array('value' => MODERATE_STATUS_BANNED), $smarty);
	$list[MODERATE_STATUS_DELETED] = smarty_function_moderate_status(array('value' => MODERATE_STATUS_DELETED), $smarty);
	
    $html = '<select name="' . $name . '"' . (!empty($class) ? ' class="' . $class . '"' : '') . '>';
    
    foreach ($list as $key => $title)
    {
        $html .= '<option value="' . $key . '"' . ($key == $value ? ' selected' : '') . '>' . $title . '</option>';
    }   

    $html .= '</select>';
    
    return $html;
}

?>
