<?php
require_once(APP_PATH . 'classes/models/user.class.php');

class SavedSession extends Model
{
    function SavedSession()
    {
        Model::Model('saved_sessions');

    }

    /**
     * Возвращает сессию по идентификаторуу
     *
     *
     * @param varchar $session_id идентификатор сессии
     * @return array запись из таблицы сохранённых сессий
     */
    function GetBySessionId($session_id)
    {
        return $this->SelectSingle(array(
            'where' => array(
                'conditions' => 'session_id = ?', 
                'arguments' => $session_id
            )
        ));
    }

    /**
     * Восстанавливает сессию пользователя
     *
     * Проверяет идентификатор сессии и клиентскую информацию.
     * Производит логин в случае успешного восстановления сессии
     *
     * @param varchar $session_id идентификатор сессии
     * @param varchar $visitor_params информация о посетителе
     * @return string информация о пользователе
     */
    function RestoreSession($session_id, $visitor_params)
    {
        $session = $this->GetBySessionId($session_id);

        if (!empty($session))
        {
            $modelUser  = new User();
            $user       = $modelUser->SelectSingle(array('where' => array('conditions' => 'id = ?', 'arguments' => $session['user_id'])));

            if (!empty($user))
            {
                $this->Update($session['id'], array('created_at' => date('Y-m-d H:i:s')));
                return $modelUser->Login($user['login'], $user['password'], $visitor_params);
            }
        }

        return false;
    }

    /**
     * Функция для генерации идентификатора сессии
     *
     * @return varchar новый идентификатор сессии (32 символа)
     */
    function _gen_session_id()
    {
        return md5(CACHE_PREFIX . time() . rand(9, 999979999));
    }

    /**
     * Функция для создания и сохранения сессии текущего пользователя
     *
     * Удаляет предыдущие сессии пользователя и старые сессии других пользователей (старее одного месяца)
     *
     * @param varchar $client_info REMOTE_ADDR + HTTP_USER_AGENT
     * @return string идентификатор созданной сессии
     */
    function SaveSession($visitor_params)
    {
        $client_info =
            substr(
                (array_key_exists('REMOTE_ADDR', $visitor_params) ? $visitor_params['REMOTE_ADDR'] : '') . '; ' .
                (array_key_exists('HTTP_USER_AGENT', $visitor_params) ? $visitor_params['HTTP_USER_AGENT'] : ''),
                0, 2048);

        $values                 = array();
        $values['session_id']   = $this->_gen_session_id();
        $values['created_at']   = date('Y-m-d H:i:s');
        $values['user_id']      = $_SESSION['user']['id'];
        $values['client_info']  = $client_info;
        
        $this->DeleteList(array(
            'where' => array(
                'conditions' => 'created_at < DATE_SUB(NOW(), INTERVAL 1 month)', 
            )
        ));

        $this->DeleteList(array(
            'where' => array(
                'conditions' => 'user_id = ? AND client_info = ?', 
                'arguments' => array($values['user_id'], $client_info)
            )
        ));

        if ($this->Insert($values))
        {
            return $values['session_id'];
        }
        else
        {
            return null;
        }
    }
    
    /**
     * Функция для удаления сессий пользователя
     *
     * @param integer $user_id идентификатор пользователя
     */
    function DestroySessionsForUser($user_id)
    {
        $this->DeleteList(array(
            'where' => array(
                'conditions' => 'user_id = ?', 
                'arguments' => $user_id
            )
        ));
    }

    /**
     * Функция для удаления сессии пользователя
     *
     * @param integer $session_id идентификатор сессии
     */
    function DestroySession($session_id)
    {
        $this->DeleteList(array(
            'where' => array(
                'conditions' => 'session_id = ?', 
                'arguments' => $session_id
            )
        ));
    }
}
