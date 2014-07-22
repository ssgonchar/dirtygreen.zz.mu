<?php
/**
 * Преобразовывает идентификатор заказ в номер документа
 * 
 * @param mixed $order_id
 * 
 * @version 20120820, zharkov
 */
function smarty_modifier_order_doc_no($order_id)
{    
    return 'INPO' . substr((10000 + $order_id), 1);
}
