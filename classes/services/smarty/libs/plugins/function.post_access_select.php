<?
function smarty_function_post_access_select($params, &$smarty)
{
    require_once 'function.user_role.php';//$smarty->_get_plugin_filepath('function','user_role'); 04.05.2010 alexandr  

    $name       = isset($params['name'])    ? $params['name'] : 'post_access';
    $class      = isset($params['class'])   ? $params['class'] : '';
    $value      = isset($params['value'])   ? $params['value'] : 0;    
    $role       = isset($params['role'])    ? $params['role'] : 0;
    
	if (ANONYMOUS_COMMENT == 'yes')
	{
		$list[ROLE_ALL] = 'Все';
	}    
    
	if ($role <= ROLE_CONTENT_MANAGER)
    {
        $list[ROLE_USER]   			= smarty_function_user_role(array('id' => ROLE_USER), $smarty);
		$list[ROLE_CONTENT_MANAGER] = smarty_function_user_role(array('id' => ROLE_CONTENT_MANAGER), $smarty);		
		$list[ROLE_MODERATOR]   	= smarty_function_user_role(array('id' => ROLE_MODERATOR), $smarty);
        $list[ROLE_ADMIN]       	= smarty_function_user_role(array('id' => ROLE_ADMIN), $smarty);		
    }
    
    $html = '<select name="' . $name . '"' . (!empty($class) ? ' class="' . $class . '"' : '') . '>';
    
    foreach ($list as $key => $title)
    {
        $html .= '<option value="' . $key . '"' . ($key == $value ? ' selected' : '') . '>' . $title . '</option>';
    }   

    $html .= '</select>';
    
    return $html;
}

?>
