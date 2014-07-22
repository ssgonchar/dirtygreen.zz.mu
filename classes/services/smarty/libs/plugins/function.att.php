<?php
    require_once APP_PATH . 'classes/common/userpicture.class.php';

/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 * 
 * Формирует тег атачмента
 */
function smarty_function_att($params, &$smarty)
{    
    // в параметре source передается массив данных атачмета из бд, если он установлен, то secretcode и filename берутся из него
    if (!isset($params['source']) || empty($params['source']))
    {
        $smarty->trigger_error("eval: 'source' is not defined");
        return;        
    }

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

    
    $url   = UserPicture::GetUrlPrefix() . '/file/' . $source['secret_name'] . '/' . $source['original_name'];    
    $result     = '';
    
    if ($source['type'] == 'image' || in_array(strtolower($source['ext']), array('jpeg', 'jpg', 'png', 'gif', 'tif', 'pjpeg', 'bmp')))
    {
        $result = '<a href="' . $url . '" id="attachment-' . $source['id'] . '" class="attachment-' . strtolower($source['ext']) . '" rel="prettyPhoto[' . $source['object_alias'] . '-' . $source['object_id'] . ']">' . $source['original_name'] . '</a> '
                . '<a target="_blank" href="' . $url . '" class="external-small" id="attachment-' . $source['id'] . '-external"></a>';
        
/*
        $result = '<a href="/picture/default/' . $source['secret_name'] . '/g/' . $source['original_name'] . '" id="attachment-' . $source['id'] . '" class="attachment-' . strtolower($source['ext']) . '" rel="prettyPhoto[' . $source['object_alias'] . '-' . $source['object_id'] . ']">' . $source['original_name'] . '</a> '
                . '<a target="_blank" href="' . $url . '" class="external-small" id="attachment-' . $source['id'] . '-external"></a>';
*/                
    }
    else
    {
        $result = '<a target="_blank" href="' . $url . '" class="attachment-' . strtolower($source['ext']) . '" id="attachment-' . $source['id'] . '">' . $source['original_name'] . '</a>';
    }
    
    
    return $result;    
}