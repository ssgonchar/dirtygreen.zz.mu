<?php
/**
 * Преобразовывает идентификатор заказ в номер документа
 * 
 * @param mixed $order_id
 * 
 * @version 20120820, zharkov
 */
function smarty_modifier_human_email($addresses)
{    
    $arr    = explode(',', $addresses);
    $names  = array();
    
    foreach ($arr as $email)
    {
        $name = preg_replace('#([a-z0-9_\-]+\.)*[a-z0-9_\-]+@([a-z0-9][a-z0-9\-]*[a-z0-9]\.)+[a-z]{2,4}#i', '', $email);
        $name = trim(str_replace(array('<', '>', '"', '"'), '', $name), ' ');
        
        $names[] = empty($name) ? $email : $name;
    }

    return implode(", ", $names);
}
