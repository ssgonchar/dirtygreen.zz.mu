<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 *
 * Формирует строку с названием статус продаж.
 * Должен присутствовать параметр id.
 */

function smarty_function_topic_status($params, &$smarty)
{

    if (!isset($params['id']))
    {
        $smarty->trigger_error("eval: missing 'id' parameter");
        return;
    }
    
    $id     = $params['id'];
    switch ($id)
    {
        case TOPIC_STATUS_OPEN:
            $name = 'Тема открыта';
            break;
        case TOPIC_STATUS_CLOSED:
            $name = 'Тема закрыта';
            break;
        case TOPIC_STATUS_DELETED:
            $name = 'Тема удалена';
            break;
        default:
            $name = 'Error!';
            break;
            
    }
    return $name;
}

?>