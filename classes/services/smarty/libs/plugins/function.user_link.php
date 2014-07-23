<?
function smarty_function_html_select_user_role($params, &$smarty)
{
    $login = null;
    $t1 = null;
    $t2 = null;
    $t3 = null;
    $t4 = null;
    $t5 = null;
                
    foreach ($params as $_key => $_value)
    {
        switch ($_key)
        {
            case 'login':
            case 't1':
            case 't2':
            case 't3':
            case 't4':
            case 't5':
                $$_key = (int)$_value;
                break;
            default:
                $smarty->trigger_error("eval: missing 'login' parameter", E_USER_NOTICE);
                break;
        }
    }
    
    return $title;
}
?>