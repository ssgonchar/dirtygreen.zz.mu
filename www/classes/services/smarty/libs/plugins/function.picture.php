<?php
    require_once APP_PATH . 'classes/common/userpicture.class.php';

/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 * 
 * Формирует тег картинки <img src=""> или просто url картинки вида /picture/user/{secretname}
 * Использование в шаблоне {picture type="" size="" secretname=""}
 */
function smarty_function_picture($params, &$smarty)
{
    // type
    $type = null;
    if (isset($params['type']))
    {
        $type = $params['type'];
        unset($params['type']);
    }
    
    // size
    $size = null;
    if (isset($params['size']))
    {
        $size = $params['size'];
        unset($params['size']);
    }
    else
    {
        $smarty->trigger_error("eval: incorrect 'size' value");
        return;        
    }
    
    // в параметре source передается массив данных атачмета из бд, если он установлен, то secretcode и filename берутся из него
    if (isset($params['source']) && !empty($params['source']))
    {
        if (!is_array($params['source'])) 
        {
            $smarty->trigger_error("eval: 'source' is not array");
            return;
        }
        
        $source = $params['source'];
        if (!isset($source['secret_name']) || !isset($source['original_name']))
        {
            $smarty->trigger_error("eval: incorrect 'source'");
            return;            
        }

        $params['secretcode']   = $source['secret_name'];
        $params['filename']     = $source['original_name'];
    }
    
    $secretcode = null;
    if (isset($params['secretcode']))
    {
        $secretcode = $params['secretcode'];
        unset($params['secretcode']);
        
//        return '<img src="/nopicture/' . $type . '/' . $size . '.png" title="Нет картинки">';
    }
    
    if (empty($type)) $type = $source['object_alias'];
    
    return UserPicture::GetHtml($type, $size, $secretcode, $params);
}

/* vim: set expandtab: */
