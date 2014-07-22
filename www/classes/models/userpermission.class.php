<?
/**
 * Модель управления таблицей user_permissions.
 *
 * Содержит методы для управления таблицей user_permissions. 
 *
 */
class UserPermission extends Model
{

    /**
     * Конструктор
     *
     */
    function UserPermission()
    {
        Model::Model('user_permissions');
    }

    /**
     * Возвращает результат проверки, доступно ли действие текущему пользователю
     *
     * @param string $object_alias тип объекта
     * @param integer $object_id идентификатор объекта
     * @param string $permission операция
     * @param boolean $strict флаг указывает, проверить ли только указанное разрешение или доступ пользователя к объекту
     * @return boolean результат проверки
     */
    function IsActionAllowedForCurrentUser($object_alias, $object_id, $permission, $strict = false)
    {
        $result = $this->CallStoredProcedure('sp_permission_allowed_for_user', array($this->user_id, $object_alias, $object_id, $permission, $strict ? 1 : 0));
        return count($result[0]) && count($result[0][0]) && isset($result[0][0]['result']) ? $result[0][0]['result'] : false;
    }
    
    /**
    * Проверка возможности комментировать пост текущим пользователем
    * 
    * @param mixed $post_id
    * @param mixed $permission
    * @return boolean
    */
    function IsCurrentUserCanCommentPost($post_id, $permission = 'c')
    {
        $result = $this->CallStoredProcedure('sp_post_comment_allowed_for_user', array($this->user_id, $post_id, $permission));
        return count($result[0]) && count($result[0][0]) && isset($result[0][0]['result']) ? $result[0][0]['result'] : false;        
    }

    /**
     * Возвращает список пользователей, которым разрешена указанная операция
     *
     * @param string $object_alias тип объекта
     * @param integer $object_id идентификатор объекта
     * @param string $permission операция
     * @param boolean $strict флаг указывает, выбирать ли всех пользователей или только тех, для кого явно указано разрешение
     * @return array список пользователей
     */
    function GetUsersAllowedForObject($object_alias, $object_id, $permission, $strict = true)
    {
        $result = $this->CallStoredProcedure('sp_permission_get_users_for_action', array($object_alias, $object_id, $permission, $strict ? 1 : 0));
        return count($result) ? $result[0] : array();    
    }

    /**
     * Возвращает список объектов, над которыми разрешена указанная операция для указанного пользователя
     *
     * @param integer $user_id идентификатор пользователя
     * @param string $object_alias тип объекта
     * @param string $permission операция
     * @return array список объектов
     */
    function GetObjectsAllowedForUser($user_id, $object_alias, $permission)
    {
        return $this->CallStoredProcedure('sp_permission_get_objects_for_user_for_alias', array($user_id, $object_alias, $permission));
    }

    /**
     * Возвращает список разрешений для пользователя
     *
     * @param integer $user_id идентификатор пользователя
     * @return array список объектов
     */
    function GetForUser($user_id)
    {
        $result = $this->CallStoredProcedure('sp_permission_get_list_for_user', array($user_id));
        $actions = count($result) ? $result[0] : array();
        
        $result = array();
        
        foreach ($actions as $action)
        {
            $result[$action['object_alias'] . $action['permission']] = true;
        }
        
        return $result;
    }
    
    /** 
     * Установка разрешения
     *
     * @param integer $user_id идентификатор пользователя
     * @param string $object_alias тип объекта
     * @param integer $object_id  идентификатор объекта
     * @param string $permission операция
     */
    function SetPermission($user_id, $object_alias, $object_id, $permission)
    {    
        $result = $this->CallStoredProcedure('sp_permission_set_for_user', array($this->user_id, $user_id, $object_alias, $object_id, $permission));
        $result = count($result) ? $result[0] : null;
        $errorcode = count($result) ? $result[0] : 0;
        
        return $errorcode;
    }

    /** 
     * Снятие разрешения
     *
     * @param integer $user_id идентификатор пользователя
     * @param string $object_alias тип объекта
     * @param integer $object_id  идентификатор объекта
     * @param string $permission операция
     */
    function KillPermission($user_id, $object_alias, $object_id, $permission)
    {    
        $result = $this->CallStoredProcedure('sp_permission_kill_for_user', array($this->user_id, $user_id, $object_alias, $object_id, $permission));
        $result = count($result) ? $result[0] : null;
        $errorcode = count($result) ? $result[0] : 0;
        
        return $errorcode;
    }
}

