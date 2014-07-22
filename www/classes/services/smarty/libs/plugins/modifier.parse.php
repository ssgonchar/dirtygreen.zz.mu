<?php
    require_once APP_PATH . 'classes/common/parser.class.php';

/**
 * Заменяет пустое значение на <i>not defined</i>
 * 
 * @param mixed $str
 * @return mixed
 * 
 * @version 20120601, zharkov
 */
function smarty_modifier_parse($content, $bizmode = 1, $target = '_blank')
{
    //debug('1671', 'bizmode: '.$bizmode);
    return Parser::Decode($content, $bizmode, $target);
}
