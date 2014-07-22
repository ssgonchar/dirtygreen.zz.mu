<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 *
 * Формирует строку с названием статус объекта недвижимости.
 * Должен присутствовать параметр id.
 */

function smarty_function_realty_status($params, &$smarty)
{

    if (!isset($params['id']))
    {
        $smarty->trigger_error("eval: missing 'id' parameter");
        return;
    }

    $id     = $params['id'];

   	switch ($id)
   	{
   		case REALTY_STATUS_ACTIVE:
               $name = 'Продается';
               break;
           case REALTY_STATUS_SOLD:
               $name = 'Продан';
               break;
           case REALTY_STATUS_DELETED:
               $name = 'Удалён';
               break;
           default:
               $name = 'Н/Д';
               break;
   	}

   	return $name;
}

?>
