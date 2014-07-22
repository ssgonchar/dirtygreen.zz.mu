<?php
require_once APP_PATH . 'classes/models/activeuser.class.php';
require_once APP_PATH . 'classes/models/attachment.class.php';
require_once APP_PATH . 'classes/models/country.class.php';
require_once APP_PATH . 'classes/models/message.class.php';
require_once APP_PATH . 'classes/models/person.class.php';
require_once APP_PATH . 'classes/models/visitlog.class.php';

class User extends Model
{
    function User()
    {
        Model::Model('users');
    }

    /**
     * Set status Online for user
     * 
     * @param mixed $user_id
     * 
     * @version 20121113, zharkov
     * @version 20130412, zharkov: post message on request
     */    
    function SetStatusOnline($user_id, $post_message = true)
    {
        if ($post_message)
        {
            $messages = new Message();
            $messages->AlertOnline($user_id);            
        }
        
        Cache::SetKey('onlinestatus-' . $user_id, 'online');
    }

    /**
     * Set status Away for user
     * 
     * @param mixed $user_id
     * 
     * @version 20121113, zharkov
     * @version 20130412, zharkov: post message on request
     */
    function SetStatusAway($user_id, $post_message = true)
    {
        if ($post_message)
        {
            $modelMessage = new Message();
            $modelMessage->AlertAway($user_id);
        }
                
        Cache::SetKey('onlinestatus-' . $user_id, 'away');
    }
    
    /**
     * Возвращает список запросов на регистрацию
     * 
     */
    function RequestsGetList()
    {
        $result = $this->CallStoredProcedure('sp_user_request_get_list', array());
        
        $country = new Country();
        return isset($result) && isset($result[0]) ? $country->FillCountryInfo($result[0]) : array();
        
    }
    
    /**
     * Возвращает список пользователей по логину
     *     
     * @param mixed $title
     * @param mixed $rows_count
     */
    function GetListByLogin($login, $rows_count)
    {
        $hash       = 'users-login-' . $login . '-rowscount-' . $rows_count;
        $cache_tags = array($hash, 'users');

        $rowset = $this->_get_cached_data($hash, 'sp_user_get_list_by_login', array($login, $rows_count), $cache_tags);
        $rowset = isset($rowset[0]) ? $this->FillUserInfo($rowset[0]) : array();
        
        usort($rowset, '_cmp_login');
        
        return $rowset;
    }
        
    /**
     * Get chat recipients list
     * 
     */
    function GetListForChat()
    {
        $hash       = 'users-chat';
        $cache_tags = array($hash, 'users');

        $rowset = $this->_get_cached_data($hash, 'sp_user_get_list_for_chat', array(), $cache_tags);
        $rowset = isset($rowset[0]) ? $rowset[0] : array();
        
        $activeusers = new ActiveUser();
        //$list           = $activeusers->GetList(); dg($list);

        // Дополняет список пользователей доступных для чата списко активных пользователей
        foreach ($activeusers->GetList() as $activeuser)
        {
            $add_new_record = true;
            foreach ($rowset as $key => $row)
            {
                if ($row['user_id'] == $activeuser['user_id'])
                {
                    $rowset[$key]['activeuser'] = $activeuser;
                    $add_new_record             = false;
                    
                    break;
                }
            }
            
            if ($add_new_record)
            {
                $rowset[] = array(
                    'user_id'       => $activeuser['user_id'],
                    'activeuser'    => $activeuser
                );
            }
        }
        
        // Устанавливает статусы пользователей
        foreach($rowset as $key => $row)
        {
            $user_id = $row['user_id'];

            // 20121116, zharkov: проверка статуса установленного пользователем
            $chat_status = Cache::GetKey('onlinestatus-' . $user_id);
            
            if (!isset($chat_status) || $chat_status != 'away')
            {
                $chat_status    = 'online';                
                $last_ping      = Cache::GetKey('online-' . $user_id); 
                
                if ($last_ping === false && isset($row['activeuser']))
                {
                    $last_ping = $row['activeuser']['online_at'];
                }
                
                if (!isset($last_ping) || $last_ping === false || time() - $last_ping > ACTIVEUSER_OFFLINE_LIMIT)
                {
                    $chat_status = 'offline';
                }

                if ($chat_status != 'offline')
                {
                    $last_visit = Cache::GetKey('activeuser-' . $user_id);
                    if ($last_visit === false && isset($row['activeuser']))
                    {
                        $last_visit = $row['activeuser']['visited_at'];
                    }
                    
                    if (!isset($last_visit) || $last_visit === false || time() - $last_visit > ACTIVEUSER_AWAY_LIMIT)
                    {
                        $chat_status = 'away';
                    }                
                }                
            }
                        
            $rowset[$key]['chat_status'] = $chat_status;
        }

        $rowset = $this->FillFullUserInfo($rowset);

        // прячутся другие пользователи для не-мам пользователей
        if ($this->user_role > ROLE_STAFF)
        {
            foreach ($rowset as $key => $row)
            {
                if ($row['user']['role_id'] > ROLE_STAFF && $row['user_id'] != $this->user_id)
                {
                    unset($rowset[$key]);
                }
            }
        }

        usort($rowset, '_cmp_login');

        return $rowset;
    }
    
    /**
     * Get chat recipients list separated on staff/partners
     * @version 20130409, zharkov
     */
    function GetListForChatSeparated()
    {
        $result = array(
            'staff'     => array(),
            'partners'  => array()
        );     
        
        foreach ($this->GetListForChat() as $row)
        {
            if ($row['user']['role_id'] > ROLE_STAFF)
            {
                $result['partners'][] = $row;
            }
            else
            {
                $result['staff'][] = $row;
            }
        }

        return $result;   
    }
    
    /**
     * Сохраняет профиль пользователя
     * 
     * @param mixed $id
     * @param mixed $login
     * @param mixed $nickname
     * @param mixed $password
     * @param mixed $email
     * @param mixed $role_id
     * @param mixed $status_id
     * @param mixed $person_id
     * @param mixed $color
     * @return resource
     * 
     * @version 20120501, zharkov
     */
    function Save($id, $login, $nickname, $password, $email, $role_id, $status_id, $person_id, $color, $se_access, $pa_access, $chat_icon_park, $driver, $last_email_number = 0)
    {
        $is_mam = ($role_id > 0 && $role_id <= ROLE_STAFF ? 1 : 0);
        $result = $this->CallStoredProcedure('sp_user_save', array($this->user_id, $id, $login, $nickname, $password, $email, 
                                                $role_id, $status_id, $person_id, $color, $is_mam, $se_access, $pa_access, 
                                                $chat_icon_park, $driver, $last_email_number));
                                                
        $result = isset($result) && isset($result[0]) && isset($result[0][0]) ? $result[0][0] : array();
        
        if (empty($result) || isset($result['ErrorCode'])) return $result;

        Cache::ClearTag('user-' . $result['id']);
        Cache::ClearTag('users-mam');
        Cache::ClearTag('users');
        
        Cache::SetKey('reload-user-' . $result['id'], true);

        return $result;
    }

    /**
    * Возвращает пользователя по перcоне
    * 
    * @param mixed $person_id
    * @version 20120501, zharkov
    */
    function GetByPersonId($person_id)
    {
        $hash       = 'users-person-' . $person_id;
        $cache_tags = array($hash, 'users', 'person-' . $person_id);

        $rowset = $this->_get_cached_data($hash, 'sp_user_get_by_person', array($person_id), $cache_tags);
        $rowset = isset($rowset[0]) ? $this->FillUserInfo($rowset[0]) : null;

        return isset($rowset) && isset($rowset[0]) ? $rowset[0] : null;
    }

    /**
     * Возвращает список пользователей, которые являются работниками MaM
     * 
     */
    function GetMamList()
    {
        $hash       = 'users-mam';
        $cache_tags = array($hash, 'users');

        $rowset = $this->_get_cached_data($hash, 'sp_user_get_mam_list', array(), $cache_tags);
        $rowset = isset($rowset[0]) ? $this->FillFullUserInfo($rowset[0]) : array();

        return $rowset;        
        
    }
	 
	 /**
     * Возвращает список пользователей, которые являются работниками MaM и у которых атр. driver = 1
     * 
     */
    function GetDriversList()
    {
        $hash       = 'users-drivers';
        $cache_tags = array($hash, 'users');

        $rowset = $this->_get_cached_data($hash, 'sp_user_get_drivers_list', array(), $cache_tags);
        $rowset = isset($rowset[0]) ? $this->FillFullUserInfo($rowset[0]) : array();

        return $rowset;        
        
    }
    
    /**
     * Возвращает пользователя по идентификатору
     * 
     * @param mixed $id
     */
    function GetById($id)
    {
        $rowset = $this->FillFullUserInfo(array(array('user_id' => $id)));
        return isset($rowset) && isset($rowset[0]) ? $rowset[0] : null;
    }
    
    /**
    * Врзвращает учетную запись пользователя по его login
    *     
    * @param mixed $login
    * @return array
    */
    function GetByLogin($login)
    {
        $rowset = $this->CallStoredProcedure('sp_user_get_by_login', array($login));
        $rowset = $this->FillFullUserInfo(isset($rowset) ? $rowset[0] : $rowset);
        
        return isset($rowset) && isset($rowset[0]) ? $rowset[0] : null;
    }

    /**
     * Выполняет аутентификацию пользователя
     * 
     * @param mixed $login
     * @param mixed $password
     * @param mixed $visitor_info
     * @param mixed $remember
     * @return array
     */
    function Login($login, $password, $visitor_params, $remember = false)
    {        
        $result = $this->SelectSingle(array(
            'fields' => array(
                $this->table->table_name . '.*'
            ),
            'where' => array(
                'conditions' => '(login = ? OR nickname = ?) and password = ?',
                'arguments' => array(trim($login), trim($login), trim($password))
            )
        ));
        //debug("1682", $result);
        $messages = new Message();
        
        // пользователь не найден
        if (empty($result)) 
        {
            $messages->AlertLoginFailed($login, $password, $visitor_params['REMOTE_ADDR'], 'user not found');
            return array('ErrorCode' => -1);
        }
        
        // пользователь не подтвердил email
        if ($result['status_id'] == USER_INITIAL) 
        {
            $messages->AlertLoginFailed($login, $password, $visitor_params['REMOTE_ADDR'], 'reg. email not confirmed');
            return array('ErrorCode' => -2, 'user' => $result);
        }
        
        // пользователь заблокирован
        if ($result['status_id'] == USER_BLOCKED) 
        {
            $messages->AlertLoginFailed($login, $password, $visitor_params['REMOTE_ADDR'], 'account blocked');
            return array('ErrorCode' => -3, 'user' => $result);
        }
        
        $ban = $this->IsBanned($result['id']);
        // пользователь забанен
        if (isset($ban) && !empty($ban)) 
        {
            $messages->AlertLoginFailed($login, $password, $visitor_params['REMOTE_ADDR'], 'account banned');
            return array('ErrorCode' => -4, 'ban' => $ban);
        }
        
        // недостаточно прав
        if (empty($result['role_id']) || $result['role_id'] > ROLE_STAFF)
        {
            $messages->AlertLoginFailed($login, $password, $visitor_params['REMOTE_ADDR'], 'access denied');
            return array('ErrorCode' => -5, 'ban' => $ban);
        }
        

        $visitlog = new VisitLog();
        
        // получает дату последнего посещения
        $last_visited_at = $visitlog->GetLastVisit($result['id']);
        
        // добовляет информацию в журнал посещений
        $values = array('user_id' => $result['id'], 'created_at' => date('Y-m-d H:i:s'));
        if (array_key_exists('HTTP_USER_AGENT', $visitor_params)) $values['http_user_agent'] = substr($visitor_params['HTTP_USER_AGENT'], 0, 2048);
        if (array_key_exists('REMOTE_ADDR', $visitor_params)) $values['remote_addr'] = substr($visitor_params['REMOTE_ADDR'], 0, 1024);
        if (array_key_exists('PREV_LOGIN', $visitor_params)) $values['prev_login'] = (intval($visitor_params['PREV_LOGIN']) - 13) / 1428;
        $visit_log_id = $visitlog->Insert($values);
        
        // обновляет дату последнего посещение для пользователя
        $this->Update($result['id'], array('last_visited_at' => $visitlog->GetLastVisit($result['id'])));
  
        
        $user = $this->GetById($result['id']);
        $user['user']['last_visited_at'] = $last_visited_at;
        
        $_SESSION['user'] = $user['user'];
        //debug("1682", $_SESSION['user']);
        
        // очищает счетчик попыток аутентификации
        if (isset($_SESSION['login_attempts'])) unset($_SESSION['login_attempts']);

        //сохранение сессии в куки
        if ($remember)
        {
            $ss = new SavedSession();
            $session_id = $ss->SaveSession($visitor_params);
            setcookie(CACHE_PREFIX . 'sid', $session_id, time() + 60 * 60 * 24 * 30, '/', APP_DOMAIN);
            setcookie('__' . CACHE_PREFIX, $result['id'] * 1428 + 13, time() + 60 * 60 * 24 * 30 * 6, '/', APP_DOMAIN);
        }
        
        // Регистрирует пользователя как активного
        $activeusers = new ActiveUser();
        $activeusers->Add($user['user'], $visitor_params['REMOTE_ADDR']);
        
        return $result;
    }
    
    /**
    * Удаляет сессию пользовтеля
    * 
    */
    function Logout()
    {
        // Удаляет сессию
        if (isset($_COOKIE[CACHE_PREFIX . 'sid']))
        {
            if (array_key_exists('user', $_SESSION))
            {
                $ss = new SavedSession();
                $ss->DestroySession($_COOKIE[CACHE_PREFIX . 'sid']);                   
            }

            setcookie(CACHE_PREFIX . 'sid', '', time() - 3600, '/', APP_DOMAIN);
        }

        // Разлоговывает пользователя
        $this->CallStoredProcedure('sp_user_logout', array($this->user_id));
        Cache::ClearTag('user-' . $this->user_id);
        Cache::ClearKey('online-' . $this->user_id);
        Cache::ClearKey('activeuser-' . $this->user_id);
        Cache::ClearKey('activeusers');
        
        
        // Добавляет сообщение в чат
        $messages = new Message();
        $messages->AlertLogout($this->user_id);
        
        unset($_SESSION['user']);
    }
    
    /**
    * Подтверждает регистрацию пользователя и присваивает новые статус и роль
    * 
    * @param mixed $guid
    * @param mixed $new_role
    * @param mixed $new_status
    * @return array
    */
    function RegisterConfirm($guid, $new_role = USER_ROLE, $new_status = USER_ACTIVE)
    {
        $result = $this->CallStoredProcedure('sp_user_register_confirm', array($guid, $new_role, $new_status));
        $result = isset($result) && isset($result[0]) && isset($result[0][0]) ? $result[0][0] : array();
        
        if (empty($result) || isset($result['ErrorCode'])) return $result;

        Cache::ClearTag('user-' . $result['id']);
        Cache::ClearTag('users');
        
        $result = $this->FilFulllUserInfo(array(array('user_id' => $result['id'])));
        return $result[0]['user'];

    }
    
    /**
    * Сохраняет профайл пользователя
    *     
    * @param mixed $user_id
    * @param mixed $login
    * @param mixed $email
    * @param mixed $role_id
    * @param mixed $status_id
    * @param mixed $first_name
    * @param mixed $last_name
    * @return array
    */
    function SaveRegistration($id, $login, $password, $email, $first_name, $last_name, $pub_email, $phone, $skype, $status_id, $role_id, $birthday, $region_id, $company, $mood)
    {
        $result = $this->CallStoredProcedure('sp_user_save', array($this->user_id, $id, $login, $password, $email, $first_name, $last_name, $pub_email, $phone, $skype, $status_id, $role_id, $birthday, $region_id, $company, $mood));
        $result = isset($result) && isset($result[0]) && isset($result[0][0]) ? $result[0][0] : array();
        
        if (empty($result) || isset($result['ErrorCode'])) return $result;

        Cache::ClearTag('user-' . $result['id']);
        Cache::ClearTag('users');

        $result = $this->FillFullUserInfo(array(array('user_id' => $result['id'])));
        return $result[0]['user'];        
    }
    
    /**
    * Обновляет пароль пользователя
    * 
    * @param mixed $user_id
    * @param mixed $password
    * @return integer
    */
    function UpdatePassword($user_id, $password)
    {
        Cache::ClearTag('user-' . $user_id);
        return $this->Update($user_id, array('password' => $password));
    }    
        
    /**
    * Обновляет email пользователя
    * 
    * @param mixed $user_id
    * @param mixed $email
    * @return integer
    */
    function UpdateEmail($user_id, $email)
    {
        Cache::ClearTag('user-' . $user_id);
        return $this->Update($user_id, array('email' => $email));
    }
    
    
    /**
    * Изменяет статус пользователя
    * 
    * @param mixed $user_id
    * @param mixed $status_id
    * @return integer
    */
    function ChangeStatus($user_id, $status_id)
    {
        Cache::ClearTag('user-' . $user_id);
        Cache::ClearTag('users');
        
        return $this->Update($user_id, array('status_id' => $status_id));
    }
    
    /**
     * Банит пользователя
     * 
     * @param mixed $user_id
     * @param mixed $period
     * @param mixed $clear_history - если установлен. то пользователь и вся его активность удаляется
     * @param mixed $description
     */
    function Ban($user_id, $period, $description)
    {
        $result = $this->CallStoredProcedure('sp_user_ban', array($this->user_id, $user_id, $period, $description));    
        
        Cache::ClearTag('user-' . $user_id);
        Cache::ClearTag('users');

        Cache::SetKey('ban-' . $user_id, $result[0][0], $period * 60);
    }
    
    /**
     * Разбанивает пользователя
     * 
     * @param mixed $user_id
     */
    function Unban($user_id)
    {
        $this->CallStoredProcedure('sp_user_unban', array($user_id));        
        Cache::ClearKey('ban-' . $user_id);
    }
    
    /**
     * Проверяет забанен ли пользователь
     * 
     * @param mixed $user_id
     */
    function IsBanned($user_id)
    {
        return false;
        if (!Cache::GetKey('ban_list_flag'))
        {
            $rowset = $this->GetBannedList(0, 1000);
            
            if (isset($rowset) && $rowset['data'])
            {
                foreach ($rowset['data'] as $row)
                {
                    Cache::SetKey('ban-' . $row['user_id'], $row, $row['minutes_left'] * 60);
                }
            }
            
            Cache::SetKey('ban_list_flag', true);
        }
        
        return Cache::GetKey('ban-' . $user_id);
    }

    /**
     * Проверяет забанен ли IP адрес
     * 
     * @param mixed $ip
     */
    function IsBannedIP($ip)
    {
        $rowset = $this->GetBannedList(0, 1000);        
        if (isset($rowset) && $rowset['data'])
        {
            foreach ($rowset['data'] as $row)
            {
                if ($ip == $row['ip']) return true;
            }
        }
        
        return false;
    }
    
    /**
     * Удаляет всю активность пользователя
     * 
     * @param mixed $user_id
     */
    function ClearActivity($user_id)
    {
        $this->CallStoredProcedure('sp_user_clear_activity', array($user_id));
        
        // очистить ключи списков        
    }
    
    /**
     * Для каждой строки выборки на основании поля 'user_id' выбирается подробная информация о пользователе из таблицы users.
     * Информация о посте сохраняется в новом поле 'user'.
     *
     * @param array $rowset ассоциативный массив, выборка данных, включающая поле 'user_id'
     * @return array входной масcив с полем 'user' содержащим информацию о посте с кодом из поля 'user_id'
     */
    function FillUserInfo($rowset, $id_fieldname = 'user_id', $entityname = 'user')
    {
        $rowset = $this->_fill_entity_info($rowset, $id_fieldname, $entityname, 'user', 'sp_user_get_list_by_ids');
        
        foreach ($rowset as $key => $row)
        {
            if (isset($row[$entityname]) && !empty($row[$entityname]))
            {
                $row = $row[$entityname];
                $rowset[$key][$entityname]['full_login'] = $row['login'] . (empty($row['nickname']) ? '' : ' (' . $row['nickname'] . ')');                
            }
        }
        
        return $rowset;
    }    

    /**
     * Заполняет полную информацию о пользователе
     * 
     * @param array $rowset
     * @param mixed $id_fieldname
     * @param mixed $entityname
     * @return array
     */
    function FillFullUserInfo($rowset, $id_fieldname = 'user_id', $entityname = 'user')
    {
        $rowset = $this->FillUserInfo($rowset, $id_fieldname, $entityname);

        foreach ($rowset as $key => $row)
        {
            if (isset($row[$entityname]))
            {
                $rowset[$key]['userperson_id']  = $row[$entityname]['person_id'];
            }
        }

        $persons    = new Person();
        $rowset     = $persons->FillPersonInfo($rowset, 'userperson_id', 'userperson');

        foreach ($rowset as $key => $row)
        {
            if (isset($row[$entityname]))
            {
                if (isset($row['userperson']) && !empty($row['userperson'])) $rowset[$key][$entityname]['person'] = $row['userperson'];
                unset($rowset[$key]['userperson']);
                unset($rowset[$key]['userperson_id']);                
            }
        }

        return $rowset;
    }    
    
    
    /**
     * Для каждой строки выборки на основании поля 'user_id' выбирается подробная информация о бане
     * Информация о посте сохраняется в новом поле 'ban'.
     *
     * @param array $rowset ассоциативный массив, выборка данных, включающая поле 'user_id'
     * @return array входной масcив с полем 'ban' содержащим информацию о посте с кодом из поля 'user_id'
     */
    function FillBanInfo($rowset)
    {
        foreach ($rowset as $key => $row)
        {            
            $ban = $this->IsBanned($row['user_id']);
            if (isset($ban) && !empty($ban))
            {
                $rowset[$key]['ban'] = $ban;
            }
        }
        
        return $rowset;
    }    
    
    /**
     * Регистрация действия пользователя, нуждающегося в подтверждении
     * 
     * @param mixed $user_id
     * @param mixed $alias
     * @return array
     */
    function ActionSave($user_id, $alias)
    {
        $result = $this->CallStoredProcedure('sp_user_action_save', array($user_id, $alias));
        return count($result) && count($result[0]) ? $result[0][0] : array();        
    }

    /**
     * Возвращает действие пользователя по алиасу
     * 
     * @param mixed $user_id
     * @param mixed $alias
     * @return int
     */
    function ActionGetByAlias($user_id, $alias)
    {
        $result = $this->CallStoredProcedure('sp_user_action_get_by_alias', array($user_id, $alias));
        return count($result) && count($result[0]) ? $result[0][0] : array();
    }
    
    /**
     * Возвращает действие пользователя по секретному коду
     * 
     * @param mixed $secretcode
     * @return array
     */
    function ActionGetBySecretCode($secretcode)
    {
        $result = $this->CallStoredProcedure('sp_user_action_get_by_secretcode', array($secretcode));
        return count($result) && count($result[0]) ? $result[0][0] : array();
    }
    
    /**
     * Сохраняет посещение страницы
     * 
     * @param mixed $session_id
     * @param mixed $ip
     * @param mixed $url
     * 
     * @return array() - статистика по сайту
     */
    function SaveVisit($session_id, $ip, $url)
    {
        Cache::ClearTag('statistics');

        $result = $this->CallStoredProcedure('sp_user_save_visit', array($this->user_id, $session_id, $ip, $url));
        return count($result) && count($result[0]) ? $result[0][0] : array();
    }    
    
    /**
     * Возвращает статистику по сайту
     * 
     */
    function GetStatistics()
    {
        $hash       = 'statistics';
        $cache_tags = array($hash);
        
        $rowset = $this->_get_cached_data($hash, 'sp_get_statistics', array(), $cache_tags, CACHE_LIFETIME_SHORT);
        return isset($rowset) && isset($rowset[0]) && isset($rowset[0][0]) ? $rowset[0][0] : array();
    }
    
    
    
// START USER_MAILBOXES methods
    /**
     * Сохраняет набор доступных МеилБоксов для конкретногопользователя
     * @param type $user_id
     * @param type $mailboxes_list
     * @return boolean
     * @version 212-09-03 d10n
     */
    public function SaveUserMailboxes($user_id, $mailboxes_list)
    {
        $query = "";
        //$query .= "DELETE FROM `user_mailboxes` WHERE `user_id` = '" . $user_id . "';\n";
        $this->table->_exec_raw_query("DELETE FROM `user_mailboxes` WHERE `user_id` = '" . $user_id . "';");
        
        Cache::ClearTag('emails-user-' . $user_id);
        
        if (empty($mailboxes_list))
        {
            //return $this->table->_exec_raw_query($query);
            return TRUE;
        }
        
        $query .= "INSERT IGNORE INTO `user_mailboxes` (`user_id`,`mailbox_id`,`created_at`,`created_by`)\n";
        $query .= "VALUES ";
            
        foreach ($mailboxes_list as $item)
        {
            $query .= "('" . $user_id . "','" . $item['mailbox_id'] . "',NOW(),'" . $this->user_id . "'),\n";
        }
        
        $query = rtrim($query, ",\n");
        $query .= ";\n";
        
        Cache::ClearTag('mailboxes-' . $user_id . '-stat');
        Cache::ClearTag('user-' . $user_id . '-mailboxes');
        
        return $this->table->_exec_raw_query($query);
    }
    
    /**
     * Возвращает список МаилБоксов для пользователе
     * @param type $user_id
     * @param type $mailbox_state
     * @return type
     * @version 2012-08-20 d10n
     */
    public function GetMailboxesList($user_id, $mailbox_state = 1)
    {
        if ($user_id <= 0) return array();
        
        $this->table->table_name = 'user_mailboxes';
        
        $data_set = $this->SelectList(array(
            'fields'    => 'user_mailboxes.*,mb.title',
            'where'     => array(
                'conditions'    => 'user_id=? AND (mb.is_active = ? OR ? = -1)',
                'arguments'     => array($user_id, $mailbox_state, $mailbox_state),
            ),
            'join'      => array(
                'table'         => 'mailboxes AS mb',
                'type'          => 'LEFT',
                'conditions'    => 'mb.id = mailbox_id',
                'arguments'     => array(),
            ),
//            'order' => array('RAND()'),
//            'limit' => array('lower' => 0, 'number' => 1),
        ));
        
        $this->table->table_name = 'users';
        
        return $data_set;
    }
// END USER_MAILBOXES methods
}

/**
 * callback function for sorting by login
 * 
 * @param mixed $a
 * @param mixed $b
 * @return mixed
 */
function _cmp_login($a, $b)
{
    if ($a['user']['login'] == $b['user']['login']) return 0;
    return ($a['user']['login'] > $b['user']['login']) ? 1 : -1;
}
