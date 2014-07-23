<?
function smarty_function_select_user_role($params, &$smarty)
{
    require_once 'function.user_role.php'; //$smarty->_get_plugin_filepath('function','user_role'); 04.05.2010 alexandr
    
    $value      = isset($params['value'])       ? $params['value'] : -1;
    $name       = isset($params['name'])        ? $params['name'] : 'user_role';
    $class      = isset($params['class'])       ? $params['class'] : '';
    $start_role = isset($params['start_role'])  ? $params['start_role'] : ROLE_ALL;
    $user_roles = array(
//                            -1                      => '--',
                            ROLE_ADMIN              => smarty_function_user_role(array('id' => ROLE_ADMIN), $smarty),
                            ROLE_SUPER_MODERATOR    => smarty_function_user_role(array('id' => ROLE_SUPER_MODERATOR), $smarty),
                            ROLE_MODERATOR          => smarty_function_user_role(array('id' => ROLE_MODERATOR), $smarty),
                            ROLE_CONTENT_MANAGER    => smarty_function_user_role(array('id' => ROLE_CONTENT_MANAGER), $smarty),
                            ROLE_STORE_MANAGER      => smarty_function_user_role(array('id' => ROLE_STORE_MANAGER), $smarty),
                            ROLE_SUPER_USER         => smarty_function_user_role(array('id' => ROLE_SUPER_USER), $smarty),
                            ROLE_USER               => smarty_function_user_role(array('id' => ROLE_USER), $smarty),
                            ROLE_LIMITED_USER       => smarty_function_user_role(array('id' => ROLE_LIMITED_USER), $smarty),
                            ROLE_GUEST              => smarty_function_user_role(array('id' => ROLE_GUEST), $smarty)
                       );
    
    $html =  '<select name="' . $name . '"' . (!empty($class) ? ' class="' . $class . '"' : '') . '>';    
    $html .= '<option value="-1"' . ($value == -1 ? ' selected' : '') . '>--</option>';
    
    foreach ($user_roles as $key => $title)
    {
        if ($start_role == ROLE_ALL || ($start_role > ROLE_ALL && $key >= $start_role))
        {
            $html .= '<option value="' . $key . '"' . ($key == $value ? ' selected' : '') . '>' . $title . '</option>';
        }
    }   

    if ($start_role == ROLE_ALL) $html .= '<option value="' . ROLE_ALL . '"' . ($value == ROLE_ALL ? ' selected' : '') . '>' . K::Get('role_all') . '</option>';
    $html .= '</select>';
    
    return $html;
}

?>
