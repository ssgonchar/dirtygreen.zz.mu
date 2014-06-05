<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 *
 */

function smarty_function_ckeditor($params, &$smarty)
{
    if (!isset($params['name']))
    {
        $smarty->trigger_error("eval: missing 'name' parameter");
        return;
    }

    $name   = $params['name'];
    $id     = isset($params['id']) ? $params['id'] : $name;
    $height = isset($params['height']) ? $params['height'] : '100';
    $width  = isset($params['width']) ? $params['width'] : '100%';
    $type   = isset($params['type']) ? $params['type'] : 'simple';
    $lang   = isset($params['lang']) ? $params['lang'] : 'ru';
    $value  = isset($params['value']) ? htmlspecialchars($params['value']) : '';

    if (!in_array($type, array('simple', 'extended', 'banner')))
    {
        $type = 'simple';
    }
    
    $html = '<textarea id="' . $id . '" name="' . $name . '">' . $value . '</textarea>';
    $html .= '<script type="text/javascript">';
    
    $obj_name = str_replace('[', '_', str_replace(']', '', $name));
    $html     .= 'var ' . $obj_name . ' = CKEDITOR.replace(\'' . $name . '\', 
                                            { 
                                                height : \'' . $height . '\', 
                                                width : \'' . $width . '\', 
                                                language : \'' . $lang . '\', 
                                                customConfig : \'/js/ck/ckeditor/cfg_' . $type . '.js\',
                                                on :
                                                {
                                                    instanceReady : function( ev )
                                                    {
                                                        var tags = [\'p\', \'ol\', \'ul\', \'li\', \'br\'];
                                                        for (var key in tags) 
                                                        {
                                                            this.dataProcessor.writer.setRules(tags[key],
                                                                {
                                                                    indent : false,
                                                                    breakBeforeOpen : false,
                                                                    breakAfterOpen : false,
                                                                    breakBeforeClose : false,
                                                                    breakAfterClose : false
                                                                });
                                                        }                                                            
                                                    }
                                                }
                                            });';

    if ($type == 'extended' || $type == 'banner')
    {
        $html .= 'CKFinder.SetupCKEditor( ' . $obj_name . ', { BasePath : \'/js/ck/ckfinder\', RememberLastFolder : false } );';         
    }
                                            
    $html .= '</script>';
    
    return $html;    
}

/* vim: set expandtab: */

?>
