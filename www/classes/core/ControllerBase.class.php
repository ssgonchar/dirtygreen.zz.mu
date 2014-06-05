<?php

require_once(APP_PATH . 'classes/core/SmartyWrapper.class.php');

/**
 * Базовый класс для контроллера статических и AJAX страниц
 * 
 * @version 20110220, zharkov: добавлено поле $skin, изменен _get_layout_template_file_name
 */
class ControllerBase		//обьявляю класс ControllerBase
{
    /**
    * Экземпляр класса Smarty
    *
    * @var Smarty
    */
    var $smarty;


    /**
    * Уникальный id модуля
    *
    * Формируется на основании запроса пользователя, может переопределяться в файле ~/mappings.php
    *
    * @var string
    */
    var $module_id;

    /**
    * Уникальный id контроллера
    *
    * Формируется на основании имени класса и типа контроллера
    *
    * @var string
    */
    var $controller_id;

    /**
     * Имя экшена
     * 
     * @var mixed
     */
    var $action_id;
    
    /**
    * Массив сообщений
    *
    * Заполняется по ходу выполнения метода контроллера
    *
    * @var array
    */
    var $messages;


    /**
    * Массив констант данного контроллера
    *
    * @var array
    */
    var $const;


    /**
    * Массив имён методов, для которых необходимо вызвать метод авторизации перед выполнением
    *
    * @var array
    */
    var $authorize_before_exec;


    /**
    * Алиас страницы полученный на основе правил Mappings
    *
    * @var string
    */    
    var $default_page_alias;
    
    
    /**
    * Идентификатор скина
    * 
    * @var mixed
    */
    var $skin;
    
    /**
     * Контекстное верхнее меню
     *
     * @var boolean
     */
    var $topcontext = '';    
    
    /**
    * Конструктор
    *
    * @param Smarty $smarty экземпляр объекта Smarty
    */
    function ControllerBase()		//метод ControllerBase()
    {
        $this->smarty                   = SmartyWrapper::Create();	//метод конструктор Create() класса SmartyWrapper
        $this->messages                 = array();
        $this->authorize_before_exec    = array();
         
        $this->module_id                = isset($_REQUEST['module']) ? $_REQUEST['module'] : 'main';
        $this->action_id                = isset($_REQUEST['action']) ? $_REQUEST['action'] : '';
        $this->default_page_alias       = Request::GetString('module', $_REQUEST, '') . '_' . Request::GetString('controller', $_REQUEST, '') . '_' . Request::GetString('action', $_REQUEST, '');
        $this->skin                     = Request::GetString('skin', $_REQUEST, 'default');
    }


    /**
    * Выполняет метод контроллера
    *
    * @param string $action имя метода
    */
    function _exec($action)
    {
        if (array_key_exists($action, $this->authorize_before_exec))
        {
            if (!$this->_authorize($this->authorize_before_exec[$action]))
            {
                if (!empty($_SESSION['user']))
                {
                    if ($_SESSION['user']['status_id'] == USER_INITIAL)
                    {
                        $this->_redirect(array('user', 'initial'));
                    }
                    else if ($_SESSION['user']['status_id'] == USER_PENDING)
                    {
                        $this->_redirect(array('user', 'pending'));
                    }
                    else if ($_SESSION['user']['status_id'] == USER_BLOCKED)
                    {
                        $this->_redirect(array('profile', 'blocked'));
                    }
                    else
                    {
                        $_SESSION['_permissiondenied'] = true;
                        $this->_redirect(array('permissiondenied'));
                    }                    
                }
                else
                {
                    $this->_put_request_to_cache();	//Добавляет запрос в кэш запросов.
                    
                    // если пользователь не залогован, но пытается зайти на страницу,  требующую авторизации, он перенаправляется
                    $this->_redirect_unauthentificated_user();
                }
            }
            
            // бан проверяется только для закрытых разделов, то, что в открытом доступе остается доступно            
            $this->_check_user_ban();
        }

        $this->_init_first();

        $this->$action();

        $this->_init_last();
    }

    /**
    * Перенаправляет неаутентифицированного пользователя
    * Может быть перегружена
    */
    function _redirect_unauthentificated_user()
    {
        $this->_redirect(array('login'));    
    }
    
    /**
    * Проверяет забанен ли пользователь
    * Может быть перегружена
    */
    function _check_user_ban()
    {
        
    }


    /**
     * Проверяет, можно ли выполнять запрошенный метод
     *
     * @param string $page_access_role минимальная роль пользователя имеющего доступ к странице
     * @return bool true, если метод разрешено выполнять
     */
    function _authorize($page_access_role)
    {
        if (!isset($page_access_role))
        {
            $page_access_role = ROLE_GUEST;
        }

        if (!isset($_SESSION['user']))
        {
            return false;
        }
        else 
        {
            $user_role = !isset($_SESSION['user']['role_id']) ? ROLE_GUEST : $_SESSION['user']['role_id'];

            return  empty($user_role) || $user_role > $page_access_role ? false : true;
        }
    }


    /**
    * Триггер. Выполняется перед запуска запрошенного метода.
    */
    function _init_first()
    {
    }


    /**
    * Триггер. Выполняется после запуска запрошенного метода.
    */
    function _init_last()
    {
    }


    /**
    * Возвращает значение константы
        *
        * @param string $const_name имя константы
        * @param string $param опциональный, значение параметра, определённого как %s в константе.
        * @return string значение константы
    */
    function _const($const_name, $param = '')
    {
        // must be overriden
    }


    /**
    * Определяет параметр смарти
        *
        * @param string $key имя параметра
        * @param string $value значение параметра
    */
    function _assign($key, $value)
    {
		//if($key=='list') dg($value);
        if (isset($_SESSION[$this->default_page_alias . '_post_back'][$key]))
        {
            $value = $this->_bind_with_postback($value, $key);
        }
        
        $this->smarty->assign($key, $value);
    }


    /**
    * Добавляет одно сообщение в массив сообщений контроллера
        *
        * @param array $message ассоциативный массив, где 'text' - текст сообщения, 'status' - статус сообщения
    */
    function _add_message($message)
    {
        if (isset($message))
            if ($message != '')
                $this->messages[] = $message;
    }


    /**
    * Добавляет набор сообщений в массив сообщений контроллера
        *
        * @param array $messages массив, где каждый элемент является ассоциативным массивом, для которого 'text' - текст сообщения, 'status' - статус сообщения
    */
    function _add_messages($messages)
    {
        if (isset($messages))
            if (is_array($messages))
                for ($i = 0; $i < count($messages); $i++)
                    $this->messages[] = $messages[$i];
    }


    /**
    * Выводит одно сообщение в переменную сессии 'messages'
        *
        * @param string $message текст сообщения
        * @param integer $status статус сообщения
    */
    function _message($message, $status)
    {
        if (!isset($_SESSION['messages']))
            $_SESSION['messages'] = array();

        $_SESSION['messages'][] = array('text' => $message, 'status' => $status);
    }


    /**
    * Выводит массив сообщений в переменную сессии 'messages'
        *
        * @param array $messages массив, где каждый элемент является ассоциативным массивом, для которого 'text' - текст сообщения, 'status' - статус сообщения
    */
    function _messages($messages)
    {
        if (isset($messages))
            if (is_array($messages))
                for ($i = 0; $i < count($messages); $i++)
                    $this->_message($messages[$i]['text'], $messages[$i]['status']);
    }


    /**
    * Возвращает сгенерированный HTML-код на основе шаблона Smarty
        *
        * @param string $template имя шаблона
        * @return string результирующий HTML шаблона
    */
    function _fetch($template)
    {
        // must be overriden
    }


    /**
    * Возвращает сгенерированный HTML-код на основе шаблона Smarty
    *
        * Причём, шаблона, который располагается в каталоге контролов.
        *
        * @param string $template имя шаблона
        * @return string результирующий HTML шаблона
    */
    function _fetch_control($template)
    {
        return $this->smarty->fetch($this->_get_control_template_file_name($template));
    }


    /**
    * Формирует абсолютный путь к файлу шаблона Smarty для контекстного меню
    *
    * @param string $template имя шаблона
    * @return string путь к шаблону Smarty
    */
    function _get_context_file_name($template)
    {
        return $this->_get_skinned_file_name('html/' . $this->module_id . '/context_' . $template . '.tpl');
    }

    /**
    * Формирует абсолютный путь к шаблону правой колонки
    * 
    * @param mixed $template
    */
    function _get_rcontext_file_name($template)
    {
        return $this->_get_skinned_file_name('html/' . $this->module_id . '/rcontext_' . $template . '.tpl');
    }
    

    /**
    * Формирует абсолютный путь к файлу шаблона Smarty для верхнего контекстного меню
    *
    * @param string $template имя шаблона
    * @return string путь к шаблону Smarty
    * 
    * @version 20130224, zharkov
    */
    function _get_topcontext_file_name($template)
    {
        if ($template == '')
        {
            if ($this->action_id == 'view')
            {
                $template = 'view';
            }
            else if (in_array($this->action_id, array('index', 'search', 'list')))
            {
                $template = 'index';
            }
            
            if (!empty($template)) return $this->_get_skinned_file_name('layouts/controls/control_topcontext_' . $template . '.tpl');    

            return false;            
        }
        
        return $this->_get_skinned_file_name('html/' . $this->module_id . '/topcontext_' . $template . '.tpl');
    }
    
    /**
    * Формирует абсолютный путь к файлу шаблона Smarty
    *
    * @param string $template имя шаблона
    * @return string путь к шаблону Smarty
    */
    function _get_template_file_name($template)
    {
        $filename = $this->_get_skinned_file_name('html/' . $this->module_id . '/' . $this->controller_id . '_' . $template . '.tpl');
        //debug('1671', $filename);
        return $filename;
    }
    
    /**
      * Формирует абсолютный путь к файлу шаблона Smarty для rss канала
      *
      * @param string $template имя шаблона
      * @return string путь к шаблону Smarty
      */
    function _get_rss_template_file_name($template)
    {
        return $this->_get_skinned_file_name('rss/' . $this->module_id . '/' . $this->controller_id . '_' . $template . '.tpl');
    }

    /**
      * Формирует абсолютный путь к файлу шаблона Smarty для print контролера
      *
      * @param string $template имя шаблона
      * @return string путь к шаблону Smarty
      */
    function _get_print_template_file_name($template)
    {
        return $this->_get_skinned_file_name('print/' . $this->module_id . '/' . $this->controller_id . '_' . $template . '.tpl');        
    }

    /**
     * Формирует абсолютный путь к файлу шаблона Smarty
     *
     * Причём, к файлу шаблона, который располагается в каталоге слоёв
     *
     * @param string $layout_name имя шаблона, по умолчанию берётся $this->layout
     * @return string путь к шаблону Smarty
     * 
     * @version 20110220, zharkov: в путь добавлен идентификатор скина
     */
    function _get_layout_template_file_name()
    {
        return $this->_get_skinned_file_name('layouts/' . $this->layout . '.tpl');
    }


    /**
    * Формирует абсолютный путь к файлу шаблона Smarty
        *
    * Причём, к файлу шаблона, который располагается в каталоге контролов
        *
        * @param string $template имя шаблона
        * @return string путь к шаблону Smarty
    */
    function _get_control_template_file_name($template)
    {
        return $this->_get_skinned_file_name('controls/' .$template . '.tpl');
    }


    /**
    * Формирует абсолютный путь к файлу яваскрипта
        *
        * @param string $jsname имя шаблона
        * @return string путь к файлу яваскрипта
    */
    function _get_js_file_name($jsname)
    {
        return $this->_get_skinned_file_name('js/' . $this->module_id . '_' . $jsname . '.js');
    }
    
    
    /**
     * Если выбран скин, то проверяет, существует ли шаблон для выбранного скина
     *
     * @param string $tpl_filename относительный путь к файлу шаблона
     */
    protected function _get_skinned_file_name($tpl_filename)
    {
        if ($this->skin != 'default' && $this->smarty->template_exists('skins/' . $this->skin . '/' . $tpl_filename))
        {
            return 'skins/' . $this->skin . '/' . $tpl_filename;
        }
        
        return 'templates/' . $tpl_filename;
    }
    

    /*
        Добавляет запрос в кэш запросов.
    */
    function _put_request_to_cache()
    {
        $session_var = '__core:request_cache';

        if (!Request::IsAjax() && isset($_REQUEST['arg']))
        {
            $arg = $_REQUEST['arg'];

            if (substr($arg, -1) == '/' && $arg != '/')
            {
                $arg = substr($arg, 0, -1);
            }

            /*  защита от запоминания    */
            if (strpos($arg, 'error404') === false)
            {
                $url_cache      = (array_key_exists($session_var, $_SESSION) ? $_SESSION[$session_var] : array());
                $cache_count    = count($url_cache);

                if ($cache_count > 0)
                {
                    /*  защита от рефреша, чтобы одна и та же страница не запоминалась подряд несколько раз */
                    if ($url_cache[$cache_count - 1] != $arg)
                    {
                        if ($cache_count > 4)
                        {
                            array_shift($url_cache);
                        }

                        array_push($url_cache, $arg);
                    }
                }
                else
                {
                    array_push($url_cache, $arg);
                }

                $_SESSION[$session_var] = $url_cache;
//                Log::AddLine( LOG_CUSTOM, "REQUEST CACHE : " . var_export($url_cache, TRUE));
            }
        }
    }
    
    /**
    * Обновляет данные из БД, данными сохраненными в сессии
    *
    * @param array  $original_data массива
    * @param string $alias ключ массива
    * @return array Обновленные данные
    */
    function _bind_with_postback($original_data, $alias = 'form')
    {
        $session = $_SESSION[$this->default_page_alias . '_post_back'];
        
//        echo '<pre>'; print_r($session); die();
        // $original_data is not array
        if (!is_array($original_data))
        {
            return !empty($session[$alias]) ? $session[$alias] : $original_data;
        }
        
        
        // $original_data is array
        $keys        = array_keys($original_data);
        $keys_is_int = false;
                
        foreach($keys as $value)
        {
            if (is_int($value)) 
            {
                $keys_is_int = true;
                break;
            }
        }

        if ($keys_is_int)
        {            
            foreach($original_data as $key1 => $value)
            {
                if (isset($original_data[$key1]['id']))
                {
                    foreach($session[$alias] as $session_key1 => $session_value1)
                    {
                        if (isset($original_data[$key1]['id']))
                        {
                            if ($original_data[$key1]['id'] == $session[$alias][$session_key1]['id'])
                            {                                
                                foreach($session[$alias][$session_key1] as $session_key2 => $session_value2)
                                {
                                    $original_data[$key1][$session_key2] = $session[$alias][$session_key1][$session_key2]; 
                                }
                            }   
                        }
                    }
                }
            }
        }
        else
        {
            if (!empty($session[$alias]))
            {
                foreach($session[$alias] as $key => $value)
                {
                    $original_data[$key] = $value;
                }       
            }        
        }

        return $original_data;
    }    
}
