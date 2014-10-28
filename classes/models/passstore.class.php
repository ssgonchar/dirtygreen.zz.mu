<?php

/**
 * PassStore
 * 
 * Модель для работы с таблицей pass_store, которая хранит
 * информацию о паролях
 * 
 * @autor SG
 */
class PassStore extends Model {

    /**
     * PassStore
     * 
     * Конструктор модели. Устанавливает привязку к таблице БД.
     * 
     * @autor SG
     */
    function PassStore() {
        Model::Model('pass_store');
    }

    /**
     * auth 
     * 
     * Проверка наличия пароля в БД по его md5 хешу и имени пользователя.
     * 
     * @param {string} username имя пользователя
     * @param {string} pass md5 хеш пароля
     * @param {boolean} return_pass флаг, сообщающий о необходимости вернуть пароль
     * @return bool Если return_pass == false
     * @return string Если return_pass == true
     * @autor SG
     */
    public function auth($username, $pass, $return_pass = FALSE) {

        $fields = 'id';
        if ($return_pass)
            $fields .= ', pass_origin';

        $arg_query = array(
            'fields' => array($fields),
            'where' => "pass_imprint = '" . $pass . "' AND nick = '" . $username . "'",
        );

        $rowset = $this->table->SelectList($arg_query);
        foreach ($rowset as $row) {
            $auth[] = array(
                'id' => $row['id'],
                'pass' => $row['pass_origin'],
            );
        }

        $auth_result = FALSE;
        if (count($auth) > 0)
            $auth_result = TRUE;
        if ($return_pass)
            $auth_result = $auth['0']['pass'];

        return $auth_result;
    }

}
