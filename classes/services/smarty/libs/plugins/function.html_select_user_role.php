<?
function smarty_function_html_select_user_role($params, &$smarty)
{
    require_once 'function.html_options.php'; //$smarty->_get_plugin_filepath('function','html_options'); 04.05.2010 alexandr
    /* Default values. */
    
    $role_user = array (
        ROLE_ALL             => K::Get('role_all'),
        ROLE_ADMIN           => K::Get('role_admin'),
        ROLE_SUPER_MODERATOR => K::Get('role_super_moderator'),
        ROLE_MODERATOR       => K::Get('role_moderator'),
        ROLE_CONTENT_MANAGER => K::Get('role_content_manager'),
        ROLE_STORE_MANAGER   => K::Get('role_store_manager'),
        ROLE_SUPER_USER      => K::Get('role_super_user'),
        ROLE_USER            => K::Get('role_user'),
        ROLE_LIMITED_USER    => K::Get('role_limited_user'),
        ROLE_GUEST           => K::Get('role_guest'),
    );
    
    $field_array    = null;
    $prefix         = 'user_';
    
//    $select_name    = 'form[user_role]';
    $selected_value = -1;
    $start_role     = 0;
    
    foreach ($params as $_key => $_value)
    {
        switch ($_key)
        {
//            case 'select_name':
            case 'prefix':
            case 'field_array':
                $$_key = (string)$_value;
                break;
            case 'selected_value':
            case 'start_role':
                $$_key = (int)$_value;
                break;
        }
    }
    
    $display_roles = array();
    if ($start_role == 0)
    {
        $display_roles = $role_user;
    }
    elseif($start_role > 0 && $start_role < count($role_user))
    {
        foreach ($role_user as $role_id => $role_name)
        {
            if($role_id >= $start_role)
            {
                $display_roles[$role_id] = $role_name;
            }
        }
    }

    if(!empty($display_roles))
    {
        $role_ids    = array_keys($display_roles);
        $role_values = array_values($display_roles);
        
        $role_result = "\n" . '<select name=';
        
        if (null !== $field_array){
            $role_result .= '"' . $field_array . '[' . $prefix . 'role]"';
        } else {
            $role_result .= '"' . $prefix . 'role"';
        }
        
        $role_result .= '>' . "\n";
        
        $role_result .= smarty_function_html_options(array( 'output'        => $role_values,
                                                            'values'        => $role_ids,
                                                            'selected'      => (in_array($selected_value, $role_ids)) ? $selected_value : min($role_ids),
                                                            'print_result'  => false),
                                                      $smarty);
        $role_result .= '</select>' . "\n";
    }
    
    $html_result = $role_result;
    
    return $html_result;
}

?>
