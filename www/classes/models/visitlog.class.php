<?php

/**
 * Модель управления журналом посещений пользователей. 
 *
 * Содержит методы для управления таблицей visit_log.
 *
 */

class VisitLog extends Model
{

    /**
     * Конструктор
     *
     */
    function VisitLog()
    {
        Model::Model('visit_log');
    }

    /**
     * Возвращает дату последнего посещения
     *
     * @param integer $user_id идентификатор пользователя
     * @param boolean $default_now флаг, если установлен, возвращает текущую дату в случае отсутствия даты последнего посещения в БД
     * @return string дата последнего посещения
     */
    function GetLastVisit($user_id, $default_now = true)
    {
        $result = $this->SelectSingle(array(
            'fields' => array('created_at'),
            'where' => array(
                'conditions' => 'user_id = ?',
                'arguments' => $user_id
            ),
            'order' => 'created_at DESC'
        ));

        if (empty($result))
        {            
            return $default_now ? date('Y-m-d H:i:s') : null;
        }

        return $result['created_at'];
    }
}
