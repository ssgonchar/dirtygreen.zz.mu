<?php
/**
 * Форматирует дату
 * Выводит для сегодняшнего дня 'H:i'
 * 'вчера H:i' для вчерашнего дня
 * 'позавчера H:i' для позавчерашнего дня
 * 'd месяц H:i' для последних шести месяцев
 * 'd месяц Y' для более ранних дат
 */
function smarty_modifier_date_human($date, $day_only = true)
{
    $date = strtotime($date);

    $today_from = 20; // hours
    $today_till = 4; // hours

    $time   = date('H:i', $date);
    $now    = time();

    $minutedaydiff  = ($now - $date) / 86400;    
    $daydiff        = mktime(0, 0, 0, date('m', $now), date('d', $now), date('Y', $now)) / 86400 - mktime(0, 0, 0, date('m', $date), date('d', $date), date('Y', $date)) / 86400;
    $yeardiff       = date('Y', $now) - date('Y', $date);

    if ($daydiff == -1)
    {
        $result =  'tomorrow';
    }
    else if ($daydiff == 0)
    {
        $result =  'today';
    }
/*    
    else if ($minutedaydiff < 1)
    {
        $result = (date('H') < $today_till && date('H', $date) > $today_from ? 'today' : 'yesterday');
    }
*/  
    else if ($daydiff == 1)
    {
        $result =  'yesterday';
    }
    else
    {
        $result = date('d', $date) . '/' . date('m', $date) . '/' . date('Y', $date);
    }

    
    if (!$day_only)
    {
        $result = $result . '&nbsp;' . $time;
    }

    return $result;
}