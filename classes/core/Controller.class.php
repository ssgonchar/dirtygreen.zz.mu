<?php
    require_once(APP_PATH . 'classes/core/ControllerBase.class.php');

/**
 *  Класс для управления запросами на отображение статических страниц
 *
 *  @version 2008.10.15, 
 *      zharkov: Бахнули _fetch_template
 *      digi: Добавил _before_display
 *
 *  @version 2008.10.29, 
 *      zharkov: Добавил назначение $this->page_alias по умолчанию 
 *
 *
 */
class Controller extends ControllerBase	//дочерний класс Controller наследует все публичные 
					//и защищенные методы из родительского класса ControllerBase
{        
    /**                                 //обьявляю данные (свойства) класса ApplicationController
     * Название шаблона контейнера.
     *
     * @var string
     */
    var $layout = '';

    /**
     * Имя подключаемого файла с яваскриптом (или массива имён)
     *
     * @var string
     */
    var $js = '';

    /**
     * Имя подключаемого файла со стилями (или массива имён)
     *
     * @var string
     */
    var $css = '';

    /**
     * Алиас страницы (думаю это BIZ)
     *
     * @var string
     */
    var $page_alias = '';
    
    /**
     * Массив переменных для построения хлебных крошек
     *
     * @var array
     */
    var $breadcrumb_vars = array();
    
    /**
     * Контекстное меню
     *
     * @var boolean
     */
    var $context = false;

    /**
     * Правая колонка в двухколоночном макете
     *
     * @var boolean
     */
    var $rcontext = false;
    
    /**
     * Алиас объекта
     *
     * @var string
     */
    var $app_object_alias = '';
    
    /**
     * Идентификатор объекта
     *
     * @var int
     */
    var $app_object_id = 0;

    /**
     * Конструктор
     *
     * @param Smarty $smarty экземпляр объекта Smarty
     */
    function Controller()	//объявляю метод Controller() класса Controller
    {
        ControllerBase::ControllerBase();	//вызываю метод ControllerBase() родительского класса ControllerBase

        $this->controller_id    = substr(strtolower(get_class($this)), 0, strpos(strtolower(get_class($this)), 'controller'));

        $this->page_alias       = $this->_get_page_alias();	//получает на основании mappings.php
								//например array('module' => 'nomenclature', 'controller' => 'main', 'action' => 'index')
        $this->layout           = '';
        
        $this->app_object_alias = $this->module_id;	//алиас обьекта равен уникальному id модуля
        $this->app_object_id    = Request::GetInteger('id', $_REQUEST);
    }

    /**
    * Триггер. Выполняется перед отрисовкой страницы.
    *
    * @version 2008.10.15, digi
	* @version 2010.04.21, zharkov
    */
    function _before_display()
    {
        $this->_clear_postback();
    }

    /**
     * Возвращает сгенерированный HTML-код на основе шаблона Smarty
     *
     * @param string $template имя шаблона
     * @return string результирующий HTML шаблона
     */
    function _fetch($template)
    {
        return $this->smarty->fetch($this->_get_template_file_name($template));
    }

    /**
     * Возвращает сгенерированный яваскрипт
     *
     * Включает в себя описание используемых констант и, собственно, файл яваскрипта
     *
     * @param string $jsname название файла яваскрипта
     * @return string результирующий яваскрипт
     */
    function _fetch_js($jsname)
    {
        $params     = explode(',', $jsname);

        $jsmodule   = $params[0];
        $jsname     = $params[1];


        // for security reasons
        if (!(preg_match('/[0-9a-zA-Z]+/', $jsname)))
        {
            return false;
        }

        $filename = $this->_get_js_file_name($jsname, $jsmodule);

        if (file_exists($filename) && ($fd = fopen($filename, 'rb')))
        {
            $contents = '';
            while (!feof($fd))
            {
                $contents .= fread($fd, 8192);
            }
            
            fclose($fd);
            return $contents;
        }
        else
        {
            return null;
        }
    }


    /**
     * Возвращает сгенерированный шаблон страницы
     *
     *
     * @param string $page_name имя файла шаблона страниы
     * @return string сгенерированный шаблон страницы
     */
    function _fetch_page($page_name)
    {
        return $this->smarty->fetch('pages/' . $page_name . '.tpl');
    }


    /**
     * Отправляет данные в стандартный поток вывода
     *
     * @param binary $data данные
     * @param string $content_type тип файла
     */
    function _display_binary($data, $content_type, $cache = true, $filename = null)
    {             
        ob_start();
        if ($cache)
        {
            $gmt_modtime = gmdate('D, d M Y H:i:s', 86400) . ' GMT';

            if (isset($_SERVER['HTTP_IF_MODIFIED_SINCE']))
            {
                $if_modified_since = preg_replace('/;.*$/', '', $_SERVER['HTTP_IF_MODIFIED_SINCE']);
                if ($if_modified_since == $gmt_modtime)
                {
                    header("HTTP/1.1 304 Not Modified");
                }
            }

            $now = time();
            $interval = 7 * 86400;
            header("Content-type: $content_type");
            header('Last-Modified: ' . $gmt_modtime);
            header('Expires: ' . gmdate('D, d M Y H:i:s', $now + $interval) . ' GMT');
            header('Pragma: private');
            header('Cache-Control: private, max-age=' . $interval);
        }
        else
        {
            header("Content-type: $content_type");

            // HTTP/1.0
            header('Expires: Fri, 02 Jan 1970 00:00:00 GMT');
            header("Pragma: no-cache");

            // HTTP/1.1
            header("Cache-Control: no-store, no-cache, max-age=0, s-maxage=0, must-revalidate");
        }

        if (!empty($filename))
        {
            header('Content-Disposition: attachment; filename="' . $filename . '"');        
        }

        echo $data;

        ob_end_flush();
    }

    /**
     * Отправляет данные в стандартный поток вывода. Данные это картинки из папки img/*
     * Для них нужны специальные заголовки.
     *
     * @param string $filename имя файла
     * @param string $content_type тип файла
     */
    function _display_binary_image($filename, $content_type)
    {
        ob_start();
        $gmt_modtime = gmdate('D, d M Y H:i:s', 86400 * 30) . ' GMT';

        if (isset($_SERVER['HTTP_IF_MODIFIED_SINCE']))
        {
            $if_modified_since = preg_replace('/;.*$/', '', $_SERVER['HTTP_IF_MODIFIED_SINCE']);
            if ($if_modified_since == $gmt_modtime)
            {
                header("HTTP/1.1 304 Not Modified");
                exit;
            }
        }

        //$last_modified = gmdate ("M d Y H:i:s", mktime (0,0,0,1,1,2007)) . ' GMT';
        $now = time();
        $interval = 86400 * 30 ;
        header("Content-type: $content_type");
        header('Last-Modified: ' . $gmt_modtime /*. $last_modified*/);
        header('Expires: ' . gmdate('D, d M Y H:i:s', $now + $interval) . ' GMT');
        header('Pragma: private');
        header('Cache-Control: private, max-age=' . $interval);
        readfile ($filename);
        ob_end_flush();
    }
    
    /**
     * Формирует файл для сохранения на диске пользователя
     *
     * @param string $data данные
     * @param string $content_type тип файла
     * @param string $filename имя файла
     */
    function _display_file($data, $content_type, $filename)
    {
        Log::AddLine(LOG_CUSTOM, 'core/controller/_display_file, ' . $filename);

        //ob_start();
        
        if (Request::IsIE())
        {
            $filename_tmp = basename($filename);
            $filename_tmp = str_replace(' ', 'nbspNBSPnbsp', $filename_tmp);
            $filename_tmp = urlencode($filename_tmp);
            $filename_tmp = str_replace('nbspNBSPnbsp', ' ', $filename_tmp);
        }
        else
        {
            //$filename_tmp = "=?UTF-8?b?".base64_encode(basename($filename))."?=";
            $filename_tmp = basename($filename);
        }

        header('Content-Disposition: attachment; filename="'.$filename_tmp.'"');
        
        //header('Content-Length: ' . sizeof($data));
        header("Content-type: " . $content_type);
        
        header('Expires: Fri, 02 Jan 1970 00:00:00 GMT');
        header("Pragma: no-cache");
        header("Cache-Control: no-store, no-cache, max-age=0, s-maxage=0, must-revalidate");
        
        echo $data;
        
        //ob_end_flush();
    }
    

    /**
     * Отправляет HTML в стандартный поток вывода
     *
     * @param string $template название шаблона для генерации HTML
     */
    function _display($template)
    {
        
        $this->_before_display();

        ob_start();

        if (!isset($template))
        {
            header('Location: index.php');
            exit;
        }

        if ($template == "")
        {
            header('Location: index.php');
            exit;
        }

        // HTTP/1.0
        header('Expires: Fri, 02 Jan 1970 00:00:00 GMT');
        header("Pragma: no-cache");

        // HTTP/1.1
        header("Cache-Control: no-store, no-cache, max-age=0, s-maxage=0, must-revalidate");

        //! $_SESSION['messages'] ->      messages  class
        if (isset($_SESSION['messages']))
        {
            $this->_add_messages($_SESSION['messages']);
            unset($_SESSION['messages']);
        }

        $this->_assign('messages', $this->messages);

        if ($this->js != '' || is_array($this->js))
        {
            if (!is_array($this->js))
            {
                $this->js = array($this->js);    
            }
            $this->_assign('controller_js', $this->js);
        }

        if ($this->css != '' || is_array($this->css))
        {
            if (!is_array($this->css))
            {
                $this->css = array($this->css);    
            }
            $this->_assign('controller_css', $this->css);
        }

        $this->_put_request_to_cache();
        
        if ($this->layout != '')
        {
            
            $this->_assign('app_object_alias',  $this->app_object_alias);
            $this->_assign('app_object_id',     $this->app_object_id);            
            
            if (!empty($this->context))
            {    
                                 
                  //print_r($this->_get_context_file_name($template)); 
                
               $context = $this->smarty->fetch($this->_get_context_file_name($template));
                
                //$context = $this->smarty->fetch('1');
                $this->_assign('context', $context);
            }          
             //print_r('$context');
            
	    if (!empty($this->rcontext))
            {
                $this->_assign('rcontext', $this->smarty->fetch($this->_get_rcontext_file_name($template)));
            }
             
            if ($this->topcontext !== false)
            {
                $topcontext = $this->_get_topcontext_file_name($this->topcontext);
                if (!empty($topcontext)) $this->_assign('app_topcontext', $this->smarty->fetch($topcontext));
            }
            
            $content = $this->smarty->fetch($this->_get_template_file_name($template));

            $this->_assign('content', $content);
           
            $this->smarty->display($this->_get_layout_template_file_name());
	    
        }
        else
        {
	    //debug('1671', $template);
            $this->smarty->display($this->_get_template_file_name($template));
        }
              
                             

        ob_end_flush();
    }

    /**
     * Отправляет HTML в стандартный поток вывода
     * (!merge with _display)
     *
     * @param string $content сгенерированный HTML для вывода
     */
    function _display_content($content)
    {

        $this->_before_display();

        ob_start();

        if (!isset($content))
        {
            header('Location: index.php');
            exit;
        }

        if ($content == "")
        {
            header('Location: index.php');
            exit;
        }

        // HTTP/1.0
        header('Expires: Fri, 02 Jan 1970 00:00:00 GMT');
        header("Pragma: no-cache");

        // HTTP/1.1
        header("Cache-Control: no-store, no-cache, max-age=0, s-maxage=0, must-revalidate");

        //! $_SESSION['messages'] ->      messages  class
        if (isset($_SESSION['messages']))
        {
            $this->_add_messages($_SESSION['messages']);
            unset($_SESSION['messages']);
        }

        $this->_assign('messages', $this->messages);

        if ($this->js != '')
        {
            $this->_assign('controller_js', $this->js);
        }

        $this->_assign('module_js', $this->module_id);

        $this->_put_request_to_cache();

        if ($this->layout != '')
        {		
            $this->_assign('content', $content);
            $this->smarty->display($this->_get_layout_template_file_name());
        }

        ob_end_flush();
    }

    /**
     * Перенаправляет браузер на новый адрес
     *
     * @param array $params ассоциативный массив для формирования нового url
     * @param array $encode_value опциональный, флаг указывает, кодировать ли значения
     * @param integer $permanent опциональный, флаг указывает, перманентный редирект или временный
     */
    function _redirect($params, $encode_value = true, $permanent = false)
    {
        $location   = APP_HOST;
        $path       = '';

        foreach ($params as $key => $value)
        {
            $path .= '/' . ($encode_value ? urlencode($value) : $value);
        }
        
        if ($path == '/main/index')
        {
            $path = '/';
        }

        header('Location: ' . $location . $path, true, $permanent ? 301 : 302);
        exit;
    }


    /**
     * Перенаправляет браузер на новый внешний адрес
     *
     * @param string $location новый url
     */
    function _redirect_external($location)
    {
        header('Location: ' . $location);
        exit;
    }

    function _redirect_to_previous_by_part($url_part)
    {        
        $url    = $this->_get_previous_url_by_part($url_part);
        $params = array();
       
        if (!empty($url)) $params = explode('/', $url);

        $this->_redirect($params);
    }
	
	
    function _get_previous_url_by_part($url_part)
    {		
		$session_var    = '__core:request_cache';
		$current_url	= $_REQUEST['arg'];
        $url            = '';
        
        if (!empty($_SESSION[$session_var]) && !empty($url_part))
        {
			if (count($_SESSION[$session_var]) > 1)
			{
				$request_cache 	= $_SESSION[$session_var];
				$url_part		= substr($url_part, 0, 1) == '/' ? substr($url_part, 1) : $url_part;

                for ($i = count($request_cache) - 1; $i >= 0; $i--)
				{					
					$pos = stripos(trim($request_cache[$i], "/"), trim($url_part, "/"));
										
					if ($request_cache[$i] != $current_url && $pos === 0)
					{
						$url = $request_cache[$i];						
					
						break;
					}
				}			
			}
        }

        return trim($url, "/");
    }	
	
    /**
    * Перенаправляет браузер на предыдущую страницу
    *   
    *   param int $offset - глубина возврата, по умолчанию предыдущая страница
    */
    function _redirect_to_previous($offset = null)
    {
        $offset = (empty($offset) ? 2 : abs($offset));

        $url    = $this->_get_previous_url($offset);
        $params = array();
       
        if (!empty($url))
        {
            $params = explode('/', $url);
        }

        $this->_redirect($params);
    }


    /**
    * Возвращает адрес предыдущей страницы
    *   
    *   param int $offset - глубина возврата, по умолчанию предыдущая страница
    */
    function _get_previous_url($offset = null)
    {
        $offset         = (empty($offset) ? 2 : abs($offset));

        $session_var    = '__core:request_cache';
        $url            = '';

        if (!empty($_SESSION[$session_var]))
        {
            $request_cache = $_SESSION[$session_var];

            if (count($request_cache) > 1)
            {
                if (count($request_cache) > $offset)
                {
                    $url = $request_cache[count($request_cache) - $offset];
                }
                else
                {
                    $url = $request_cache[0];
                }
            }
            else
            {
                $url = $request_cache[count($request_cache) - 1];
            }

            $url = str_replace('\\', '/', $url);

            if($url == '/')
            {
                $url = '/main/index';
            }

            if (substr($url, 0, 1) == '/')
            {
                $url = substr($url, 1);
            }

            Log::AddLine(LOG_CUSTOM, 'previous url to redirect to = ' . $url);
        }

        return $url;
    }


    function _get_page_alias()
    {
        if ($this->page_alias == '')
        {
            $module     = Request::GetString('module', $_REQUEST);
            $controller = Request::GetString('controller', $_REQUEST);
            $action     = Request::GetString('action', $_REQUEST);
                        
            return $module . '_' . $controller . '_' . $action;
        }

        return $this->page_alias;
    }

    function _const($name, $param = '')
    {
        global $__constants;
        if (!array_key_exists($name, $__constants))
        {
            Log::AddLine(LOG_ERROR, "Request of undefined const '$name'");
            return 'undefined';
        }

        $lang = Lang::GetLang();
        if (!array_key_exists($lang, $__constants[$name]))
        {
            Log::AddLine(LOG_ERROR, "Request of undefined '$lang' content for const '$name'");
            return 'undefined';
        }

        return $__constants[$name][$lang];    
    }
	
	
	
    /**
    * Сохраняет данные сессию с определенным ключом
	*
    * @param string $key Ключ массива
    * @param $value Значение
    */     
    function _add_to_postback($key, $value)
    {
		Log::AddLine(LOG_CUSTOM, 'POSTBACK : ' . $this->default_page_alias . '_post_back');
		
		$_SESSION[$this->default_page_alias . '_post_back'][$key] = $value;
    }

    /**
    * Возвращает данные сохраненые в сессию с определенным ключом
    *
    * @param string $key Ключ массива
    */     
    function _get_postback_key($key)
    {
        if (array_key_exists($this->default_page_alias . '_post_back', $_SESSION) 
            && array_key_exists($key, $_SESSION[$this->default_page_alias . '_post_back']))
        {
            return $_SESSION[$this->default_page_alias . '_post_back'][$key];
        }
        
        return null;
    }

    
    /**
    * Уничтожает данные сохраненые в сессию с определенным ключом
	*
    * @param string $key Ключ массива
    */     
    function _remove_from_postback($key)
    {
		if (array_key_exists($this->default_page_alias . '_post_back', $_SESSION) 
			&& array_key_exists($key, $_SESSION[$this->default_page_alias . '_post_back']))
		{
			unset($_SESSION[$this->default_page_alias . '_post_back'][$key]);
		}        
    }
    
    /**
    * Уничтожает данные сохраненые в сессию
    *
    */    
    function _clear_postback()
    {		
		Log::AddLine(LOG_CUSTOM, 'CLEAR POSTBACK : ' . $this->default_page_alias . '_post_back');
		
		if (array_key_exists($this->default_page_alias . '_post_back', $_SESSION))
		{
			unset($_SESSION[$this->default_page_alias . '_post_back']);   	
		}        
    }

    
    /**
    * Проверяет существуют ли данные сохраненные в массив
    *
    * @param string $key ключ массива
    * @return boolean;
    */    
    function _is_post_back($key = null)
    {
		if (empty($key))
		{
			return isset($_SESSION[$this->default_page_alias . '_post_back']);
		}
		else
		{
			return isset($_SESSION[$this->default_page_alias . '_post_back'][$key]);
		}
    }    
}