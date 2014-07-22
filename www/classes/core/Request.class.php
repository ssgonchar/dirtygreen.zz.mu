<?php
require_once(APP_PATH . 'classes/services/htmlpurifier/HTMLPurifier.standalone.php');

/**
 * Класс-контейнер методов для обработки параметров объекта $_REQUEST
 *
 * @static
 */
class Request
{
    /**
     * Возвращает информацию, идентифицирующую посетителя сайта
     *
     * @return string строка с набором полей и их значений
     */
    public static function GetVisitorIdInfo()
    {
        $result = array();

        if (array_key_exists('HTTP_USER_AGENT', $_SERVER)) $result['HTTP_USER_AGENT'] = $_SERVER['HTTP_USER_AGENT'];

        if (array_key_exists('HTTP_X_FORWARDED_FOR', $_SERVER)) $result['REMOTE_ADDR'] = $_SERVER['HTTP_X_FORWARDED_FOR'];
        else if (array_key_exists('REMOTE_ADDR', $_SERVER)) $result['REMOTE_ADDR'] = $_SERVER['REMOTE_ADDR'];

        if (isset($_COOKIE['__' . CACHE_PREFIX])) $result['PREV_LOGIN'] = $_COOKIE['__' . CACHE_PREFIX];

        return $result;
    }    

    /**
     * Проверяет, является ли текущий запрос AJAX запросом
     *
     * @return bool true, если AJAX запрос
     */
    public static function IsAjax() 
    {
    	if (isset($_SERVER['HTTP_X_REQUESTED_WITH']))
            if ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')
                return true;
        
        if (isset($_REQUEST['JsHttpRequest']))
            return true;

        return false;
    }
    
    /**
     * Проверяет, является ли текущий запрос от броузера IE.
     *
     * @return bool true, если клиент IE
     */
    public static function IsIE() 
    {
        $pos = strpos($_SERVER['HTTP_USER_AGENT'], "MSIE");        
        
        return !($pos === false);
    }
    
    /**
     * Проверяет, является ли текущий запрос RSS запросом. Признак Rss запроса параметр is_rss='yes' в реквесте
     *
     * @return bool true, если RSS запрос
     */
    public static function IsRss() 
    {
        $is_rss = (isset($_REQUEST['is_rss']) and $_REQUEST['is_rss']=='yes') ? true : false;

        return $is_rss;
    }

    /**
     * Проверяет, является ли текущий запрос Print запросом (простая страница для печати). Признак Print запроса параметр is_print='yes' в реквесте
     *
     * @return bool true, если Print запрос
     */
    public static function IsPrint() 
    {
        $is_print = (isset($_REQUEST['is_print']) and $_REQUEST['is_print']=='yes') ? true : false;

        return $is_print;
    }   
    
    /**
     * Возвращает информацию о текущем запросе для сохранения в логе
     *
     * @return string
     */
    public static function InfoForLog()
    {
        $result = 
            $_SERVER['REMOTE_ADDR'] . ':' . $_SERVER['SERVER_PORT'] . ' ' . 
            $_SERVER['REQUEST_METHOD'] . (Request::IsAjax() ? '(AJAX)' : '') . ' ' .
            (isset($_SERVER['PATH_INFO'])?$_SERVER['PATH_INFO']:$_SERVER["PHP_SELF"]) . '?' . $_SERVER['QUERY_STRING'];

        return $result;
    }

    /**
     * Извлекает параметр числового типа из ассоциативного массива
     *
     * @param string $name искомый параметр
     * @param array $params массив параметров
     * @param numeric $default значение по умолчанию
     * @return numeric
     */
    static function _get_numeric_param($name, $params, $default)
    {
        if (!array_key_exists($name, $params)) return $default;
        if ($params[$name] == '') return $default;

        $value  = trim($params[$name]);
        $dig    = (substr($value, 0, 1) == '-' ? -1. : 1.);
        
        $result = preg_replace('/[^0-9,\.]/', '', $params[$name]);

        if ($result == '') return $default;

        $parts  = preg_split('/(,|\.)/', $result);
        $resnum = 0;

        if (count($parts) == 1 && !empty($parts[0]))
        {
            $resnum = $parts[0];
        }
        else if (count($parts) == 2)
        {
            if (!empty($parts[0])) $resnum = floatval($parts[0]);
            if (!empty($parts[1])) $resnum += 1. * floatval($parts[1]) / pow(10, strlen($parts[1]));
        }

        return $resnum * $dig;
    }

    /**
     * Извлекает параметр числового типа из ассоциативного массива
     *
     * @param string $name искомый параметр
     * @param array $params массив параметров
     * @param numeric $default_arg опциональный, значение по умолчанию
     * @return numeric
     */
    static function GetNumeric($name, $params)
    {
        $default_arg = func_num_args() > 2 ? func_get_arg(2) : 0.;
        $default = is_numeric($default_arg) ? floatval($default_arg) : 0.;

        return Request::_get_numeric_param($name, $params, $default);
    }

    /**
     * Извлекает параметр целого типа из ассоциативного массива
     *
     * @param string $name искомый параметр
     * @param array $params массив параметров
     * @param integer $default значение по умолчанию
     * @return integer
     */
    static function _get_integer_param($name, $params, $default)
    {
        $result = $default;
        
        if (isset($params[$name]))
            if (is_numeric($params[$name]))
                $result = intval($params[$name]);
        
        return ($result > 1000000000 ? 1000000000 : $result);
    }

    /**
     * Извлекает параметр целого типа из ассоциативного массива
     *
     * @param string $name искомый параметр
     * @param array $params массив параметров
     * @param integer $default_arg опциональный, значение по умолчанию
     * @return integer
     */
    static function GetInteger($name, $params)
    {
        $default_arg    = func_num_args() > 2 ? func_get_arg(2) : 0;
        $default        = is_numeric($default_arg) ? intval($default_arg) : 0;
        
        return Request::_get_integer_param($name, $params, $default);
    }

    /**
     * Извлекает параметр булевого типа из ассоциативного массива
     *
     * @param string $name искомый параметр
     * @param array $params массив параметров
     * @param bool $default значение по умолчанию
     * @return bool
     */
    static function _get_boolean_param($name, $params, $default)
    {
        $result = $default;

        if (isset($params[$name]))
            if (boolval($params[$name]))
                $result = true;

        return $result;
    }

    /**
     * Извлекает параметр булевого типа из ассоциативного массива
     *
     * @param string $name искомый параметр
     * @param array $params массив параметров
     * @param bool $default_arg опциональный, значение по умолчанию
     * @return bool
     */
    public static function GetBoolean($name, $params)
    {
        $default_arg = func_num_args() > 2 ? func_get_arg(2) : false;
        $default = $default_arg ? true : false;
        
        return Request::_get_boolean_param($name, $params, $default);
    }

    /**
     * Извлекает параметр строкового типа из ассоциативного массива
     *
     * @param string $name искомый параметр
     * @param array $params массив параметров
     * @param bool $default значение по умолчанию
     * @param integer $length максимальная длина строки
     * @param bool $strip_slashes указывает, нужно ли раскавычивать строку
     * @param bool $strip_tags указывает, нужно ли вырезать тэги
     * @return string
     */
    static function _get_string_param($name, $params, $default, $length, $strip_slashes, $strip_tags, $url_go = false)
    {
        $result = isset($default) ? $default : '';

        if (!isset($params))
        {
            return $result;
        }

        if (!array_key_exists($name, $params))
        {
            return $result;
        }

        $result = trim($params[$name]);

        if ($length > 0)
        {
            $result = mb_substr($result, 0, $length, 'UTF-8');
        }
        
        // 20111103, zharkov: заменяет ссылки на /go/ и изменяет размер картинок
        $result = self::_parse_external_links($result, $url_go);        

        if ($strip_slashes)
            $result = stripslashes($result);

        if ($strip_tags)
            $result = strip_tags($result);

        return $result;
    }

    /**
     * Извлекает параметр строкового типа из ассоциативного массива
     *
     * @param string $name искомый параметр
     * @param array $params массив параметров
     * @param bool $default опциональный, значение по умолчанию
     * @param integer $length опциональный, максимальная длина строки
     * @param bool $strip_tags опциональный, указывает, нужно ли вырезать тэги
     * @return string
     */
    static function GetString($name, $params)
    {
        $default = func_num_args() > 2 ? func_get_arg(2) : '';
        $length = func_num_args() > 3 ? func_get_arg(3) : null;
        $strip_tags = func_num_args() > 4 ? func_get_arg(4) : false;
        $strip_slashes = get_magic_quotes_gpc() ? true : false;

        return self::_get_string_param($name, $params, $default, $length, $strip_slashes, $strip_tags);
    }

    /**
     * Извлекает параметр строкового типа из ассоциативного массива и очищает его от всех тегов кроме разрешенных
     *
     * @param string $name искомый параметр
     * @param array $params массив параметров
     * @param bool $default опциональный, значение по умолчанию
     * @param integer $length опциональный, максимальная длина строки
     * @param bool $strip_tags опциональный, указывает, нужно ли вырезать тэги
     * @return string
     */
    static function GetHtmlString($name, $params, $url_go = false)
    {
        $default        = func_num_args() > 2 ? func_get_arg(2) : '';
        $length         = func_num_args() > 3 ? func_get_arg(3) : null;
        $strip_tags     = func_num_args() > 4 ? func_get_arg(4) : false;
        $strip_slashes  = get_magic_quotes_gpc() ? true : false;

        $text = self::_get_string_param($name, $params, $default, $length, $strip_slashes, $strip_tags, $url_go);

        // очищает ненужные теги
        $text = self::_filter_tags($text);
        
        // находит все iframe youtube
        preg_match_all('#<iframe[^<]*youtube\.com\/embed\/([a-zA-Z0-9_]+)[^<]*><\/iframe>#si', $text, $youtubes);
        foreach ($youtubes[0] as $key => $match) $text = str_replace($match, '{' . $youtubes[1][$key] . '}', $text);
 
        // выравнивает html
        $text = self::_purify_html($text, $auto_paragraph = false, $allow_youtube = false, $url_go);

        // заменяет алиасы youtube на iframe
        foreach ($youtubes[0] as $key => $match) $text = str_replace('{' . $youtubes[1][$key] . '}', $match, $text);
        
        return $text;
    }
    
    /**
     * Очищает вредные теги
     * 
     * @param mixed $text
     * @return mixed
     */
    static function _filter_tags($text)    
    {
        $entries        = array();
        $allow_entries  = array('<a>','<area>','<b>','<big>','<blockquote>','<br>','<caption>','<center>','<dd>','<div>',
                                    '<dl>','<dt>','<em>','<font>','<h1>','<h2>','<h3>','<h4>','<h5>','<h6>','<hr>','<i>','<img>',
                                    '<li>','<map>','<object>','<ol>','<p>','<pre>','<small>','<span>','<strike>','<strong>',
                                    '<style>','<sub>','<sup>','<table>','<tbody>','<td>','<tfoot>','<th>','<thead>','<tr>','<u>',
                                    '<ul>','<iframe>');
        
        preg_match_all('|<(.+)>|U', $text, $matches);
        
        $matches = $matches[1];
        for ($i = 0; $i < count($matches); $i++)
        {
            $parts  = explode(' ', $matches[$i]);

            if (!array_key_exists($matches[$i], $entries))
            {
                $entries[trim($matches[$i])] = str_replace('/', '', $parts[0]);
            }
        }

        // Возможно следует избавиться от этого цикла и все сделать в предыдущем, но там будет лишняя работа из-за избыточности повторяющихся данных
        foreach ($entries as $key => $value)
        {
            if (array_search('<' . $value . '>', $allow_entries) === false)
            {
                $text = str_replace('<' . $key . '>', '', $text);
            }
        }
        
        return $text;        
    }
    
    /**
     * Нормализует html
     * 
     * @param mixed $text
     * @param mixed $auto_paragraph
     * @param mixed $allow_youtube
     * @return mixed
     */
    static function _purify_html($text, $auto_paragraph = false, $allow_youtube = false, $url_go = false)
    {
        $config = HTMLPurifier_Config::createDefault();
        $config->set('Cache.SerializerPath',        APP_CACHE);
        
        $config->set('URI.Host',                    APP_HOST);
		
		if ($url_go)
		{	
			$config->set('URI.Munge',                   '%s'); 
		}
		else
		{
			$config->set('URI.Munge',                APP_HOST . '/go/%s'); 
		}	
		
        $config->set('Core.Encoding',               'UTF-8');
        $config->set('HTML.Doctype',                'XHTML 1.0 Strict');
        $config->set('AutoFormat.AutoParagraph',    $auto_paragraph);
        $config->set('AutoFormat.PurifierLinkify',  true);
        $config->set('AutoFormat.Linkify',          true);
        $config->set('Output.Newline',              "\n");
        
        $config->set('Core.MaintainLineNumbers',    true);
        $config->set('Core.CollectErrors',          true);
        

        if (isset($_SESSION['user']) && ($_SESSION['user']['role_id'] > 0 && $_SESSION['user']['role_id'] <= ROLE_MODERATOR))
        {
            $config->set('Attr.EnableID', true);       
        }
        
        if ($allow_youtube)
        {
            $config->set('Filter.YouTube', true);
        }
        
        // отличная фича, но ломается на кириллице
        //$config->set('Core', 'EscapeInvalidTags', true);
        
        $purifier   = new HTMLPurifier($config);
		
		$result		= $text;
		
		if (!$url_go)
		{	
			$result     = $purifier->purify($result);
			$e = $purifier->context->get('ErrorCollector');
			Log::AddLine(LOG_CUSTOM, "Request::GetHtmlString() errors=" . $e->getHTMLFormatted($config));
		}	
		
        $result     = preg_replace('/\<\/p\>(\\n|\\r)+\</u', '</p><', $result);
        
        Log::AddLine(LOG_CUSTOM, "Request::GetHtmlString() result=" . $result);
        
        return $result;
        
/* 20120719, zharkov : old version
        $config = HTMLPurifier_Config::createDefault();
        $config->set('Cache.SerializerPath',        APP_CACHE);
        $config->set('URI.Host',                    APP_HOST);
        $config->set('Core.Encoding',               'UTF-8');
        $config->set('HTML.Doctype',                'XHTML 1.0 Strict');
        $config->set('AutoFormat.AutoParagraph',    $auto_paragraph);
        $config->set('AutoFormat.PurifierLinkify',  true);
        $config->set('Output.Newline',              "\n");
        
        $config->set('Core.MaintainLineNumbers',    true);
        $config->set('Core.CollectErrors',          true);

        $config->set('URI.Munge',                   APP_HOST . '/go/%s');        
        
        if ($allow_youtube)
        {
            $config->set('Filter.YouTube', true);
        }
        
        // отличная фича, но ломается на кириллице
        //$config->set('Core', 'EscapeInvalidTags', true);
        
        $purifier   = new HTMLPurifier($config);
        $result     = $purifier->purify($text);
        $result     = preg_replace('/\<\/p\>(\\n|\\r)+\</u', '</p><', $result);
        
        Log::AddLine(LOG_CUSTOM, "Request::GetHtmlString() result=" . $result);
        
        $e = $purifier->context->get('ErrorCollector');
        Log::AddLine(LOG_CUSTOM, "Request::GetHtmlString() errors=" . $e->getHTMLFormatted($config));
        
        return $result;
*/        
    }    

    /**
     * Разбивает строку по разделителю (запятая)
     *
     * @param string $str
     */
    function GetStringArray($name, $param)
    {
        $escaped_str  = Request::GetString($name, $param);
        
        $result = str_replace (' ','',$escaped_str);
        //debug('1682', $result);
        $array_thickness = explode(',', $result);
        return $array_thickness;
    }
    
    /**
     * Извлекает информацию о загруженном файле из массива информации о принятых файлах
     *
     * @param array $files массив с информацией о файлах
     * @param string $custom уточнение ключа
     * @param string $custom,... уточнение ключа
     * @return array ассоциативный массив с информацией о файле, включает поля 'name', 'type', 'size', 'tmp_name'
     */
    static function GetFile($files)
    {
        $fields = array('name', 'type', 'size', 'tmp_name');
        $result = array();

        $custom = array();
        for ($i = 1; $i < func_num_args(); $i++)
            $custom[] = func_get_arg($i);

        foreach ($fields as $key)
        {
            $current = $files[$key];
            for ($i = 0; $i < count($custom); $i++)
            {
                if (isset($current[$custom[$i]]))
                    $current = $current[$custom[$i]];
                else
                    break;
            }

            $result[$key] = $current;
        }

        if (count($result) > 0);
            if ($result['size'] > 0)
                return $result;

        return null;
    }
    
    
    /**
     *  Получает строку и преобразовывает ее в дату в нужном формате
     *  @name = Название переменной из _REQUEST
     *  @value = значение переменной из _REQUEST
     *  @default = значение по умолчанию
     */
    static function GetStringDate($name, $value, $default = null, $include_time = false)
    {
        if (!array_key_exists($name, $value)) return null;
        
        if (($timestamp = strtotime($value[$name])) === -1) 
        {
            return $default == null ? now() : $default;
        } 
        else 
        {
            return $include_time ==  false ? date('Y-m-d 00:00:00', $timestamp) : date('Y-m-d h:i:s', $timestamp);
        }
    }
    
    /**
     * Получает дату
     *
     * @param string $param префикс сета параметров ($param . 'Day', $param . 'Month', $param . 'Year')
     * @param integer $Day опциональный, значение дня по умолчанию
     * @param integer $Month опциональный, значение месяца по умолчанию
     * @param integer $Year опциональный, значение года по умолчанию
     * @return string строка даты в виде 'yyyy-dd-mm'
     */
    static function GetDate($param, $params)
    {
        $Day = Request::GetInteger($param . 'Day', $params);
        $Month = Request::GetInteger($param . 'Month', $params);
        $Year = Request::GetInteger($param . 'Year', $params);

        if (!checkdate($Month, $Day, $Year))
        {
            if (func_num_args() > 3)
            {
                $Day = func_get_arg(1);
                $Month = func_get_arg(2);
                $Year = func_get_arg(3);
            }
            else
            {
                $Day = date('d');
                $Month = date('m');
                $Year = date('Y');
            }
        }

        return sprintf('%4d-%02d-%02d', $Year, $Month, $Day);
    }

    /**
     * Получает нечёткую дату
     *
     * День и/или месяц и/или год может принимать нулевое значение
     *
     * @param string $param префикс сета параметров ($param . 'Day', $param . 'Month', $param . 'Year')
     * @param integer $Day опциональный, значение дня по умолчанию
     * @param integer $Month опциональный, значение месяца по умолчанию
     * @param integer $Year опциональный, значение года по умолчанию
     * @return string строка даты в виде 'yyyyddmm'
     */
    static function GetJaggedDate($param)
    {
        $Day = 0;
        $Month = 0;
        $Year = 0;

        if (func_num_args() > 3)
        {
            $Day = func_get_arg(1);
            $Month = func_get_arg(2);
            $Year = func_get_arg(3);
        }

        $Day = Request::GetInteger($param . 'Day', $Day);
        $Month = Request::GetInteger($param . 'Month', $Month);
        $Year = Request::GetInteger($param . 'Year', $Year);

        if ($Year == 0) 
            $Month = 0;
        if ($Month == 0) 
            $Day = 0;

        if (!checkdate(($Month > 0 ? $Month : 1), ($Day > 0 ? $Day : 1), ($Year > 0 ? $Year : 1)))
        {
            $Day = date('d');
            $Month = date('m');
            $Year = date('Y');
        }

        return sprintf('%04d%02d%02d', $Year, $Month, $Day);
    }

    /**
     * Форматирует дату для использования в SQL запросах
     *
     * @param string $param префикс сета параметров ($param . 'Day', $param . 'Month', $param . 'Year')
     * @return string строка даты в прописанном в формате 'Y-m-d H:i:s'
     */
    static function GetDateTime($param, $params)
    {
        $default    = func_num_args() > 2 ? func_get_arg(2) : null;
        $Day        = Request::GetInteger($param . 'Day', $params);
        $Month      = Request::GetInteger($param . 'Month', $params);
        $Year       = Request::GetInteger($param . 'Year', $params);

        if (empty($Day) || empty($Month) || empty($Year)) return $default;
        
        if (checkdate($Month, $Day, $Year))
            $date = mktime(0, 0, 0, $Month, $Day, $Year);
        else
            $date = time();

        return date('Y-m-d H:i:s', $date);
    }
	
    /**
     * Форматирует и фильтрует строку тегов
     *
     * ограничивает количество тегов до 25 штук, до 50 символов каждый
     */
    static function GetTags($name, $params)
    {
        $max_tag_length = 50;
        $max_tag_count  = 25;
        
        $tag_string = Request::GetString($name, $params);       
        $tag_string = preg_replace('/[^a-zA-Zа-яА-Я0-9_\- \.\$\,;]/u', '', $tag_string);
        $tag_string = trim($tag_string);
        $tag_string = mb_strtolower($tag_string, "utf-8");
 
        $result 	= array();
        $tag_array 	= preg_split('/[,;]/u', $tag_string);
		
        for ($i = 0; $i < count($tag_array); $i++)
        {
            $tag = mb_substr(trim($tag_array[$i]), 0, $max_tag_length, 'UTF-8');
            $result[$tag] = $tag;
        }
        
        return implode(',', array_slice(array_keys($result), 0, $max_tag_count));
    }

    /**
     * Форматирует дату для использования в SQL запросах
     * Если дата не корректна возвращается NULL.
     * 
     * @param string $param ключ ассоциативного массива содержащая дату в формате "12.01.2010"
     * @param array $value ассоциативный массив 
     * @return string строка даты в формате 'Y-m-d H:i:s'
     * /
     */
    static function GetDateForDB($name, $params)
    {
        $date  = Request::GetString($name, $params);
        
        $date  = explode('/', $date);
       
        settype($date[0], 'integer');
        settype($date[1], 'integer');
        settype($date[2], 'integer');
/*        
		print_r($date[1]); print_r('<br>');
		print_r($date[0]); print_r('<br>');
		print_r($date[2]); print_r('<br>');
		die();
*/		
        if (checkdate($date[1], $date[0], $date[2]))
        {
            if ($date[2] < 1900)
			{
				$date = null;
			}
			else
			{
				$date = mktime(0, 0, 0, $date[1], $date[0], $date[2]);
				$date = date('Y-m-d H:i:s', $date);			
			}
        }            
        else
        {
            $date = null;
        }            

        return $date;
    }    
    
    /**
     * Парсит сообщение, заменяет ссылки на /go/
     * исправляет картинки и убирает пустые теги
     * 
     * @param mixed $text
     */
    static function _parse_external_links($text, $url_go = false)
    {
    /*  20111103, zharkov: закомментирована обработка картинок
        preg_match_all("/<img[^<]*>/si", $text, $images);
        if (!empty($images))
        {            
            $images = $images[0];
            foreach ($images as $image)
            {
                preg_match("/src=\"([^\"']*)\"/si", $image, $url);
                if (!empty($url))
                {
                    // удаляет все иображения у которых src="data..."
                    if (strpos($url[1], 'data') === 0)
                    {
                        $text = str_replace($image, '', $text);
                        continue;
                    }

                    //style="height: 188px; width: 250px;"
                    preg_match("/style=\"([^\"']*)\"/si", $image, $style);
                    if(!empty($style))
                    {
                        $style = $style['1'];
                        
                        preg_match("/height:(.+?)px;/si", $style, $height);
                        if (!empty($height))
                        {
                            //$height = $height[1] > 500 ? 500 : $height[1];
                            $height = $height[1];
                        }
                        
                        preg_match("/width:(.+?)px;/si", $style, $width);
                        if (!empty($width))
                        {
                            //$width = $width > 500 ? '' : $width;
                            $width = $width[1];
                        }
                        
                        if ($width > $height)
                        {
                            $height = null;
                        }
                        else
                            $width = null;
                        
                        if (!empty($width))
                        {
                            $width = $width > 500 ? 500 : $width;
                        }
                        
                        if (!empty($height))
                        {
                            $height = $height > 500 ? 500 : $height;
                        }
                        
                        $replacement = '<img style="'.(!empty($width) ? 'width:'.$width.'px;' : "" ). (!empty($height) ? 'height:'. $height .'px;' : "") . '"'. ' src="' . $url[1] . '">';
                    }
                    else
                    {
                        preg_match("/height=\"([^\"']*)\"/si", $image, $height);
                        if (!empty($height))
                        {
                            $height = $height[1] > 500 ? 500 : $height[1];
                        }
                        
                        preg_match("/width=\"([^\"']*)\"/si", $image, $width);
                        if (!empty($width))
                        {
                            $width = $width[1] > 500 ? '' : $width[1];
                        }
                        
                        $replacement = '<img src="' . $url[1] . '"' . (!empty($height) ? ' height="' . $height . 'px"' : '') . (!empty($width) ? ' width="' . $width . 'px"' : '') . '>';
                    }
                                        
                    $text = str_replace($image, $replacement, $text);
                }
                else
                {
                    $text = str_replace($image, '', $text);
                }
            }
        }
        */
		
		if (!$url_go)
		{	
			preg_match_all("#href=\"([^\"]*)\"#si", $text, $urls);
			if (!empty($urls))
			{
				foreach($urls[1] as $key => $url)
				{
					preg_match("#http://(www.)*(" . $_SERVER['HTTP_HOST'] . ")#si", $url, $matches);
					if (empty($matches))
					{                    
						$replacement = APP_HOST . '/go/' . str_replace(
										array('%2F', '%26', '%23', '//'),
										array('/', '%2526', '%2523', '/%252F'),
										rawurlencode($urls[1][$key]));
						$text = str_replace($urls[0][$key], 'href="' . $replacement . '"', $text);
					}
				}            
			}
		}
        
        // удаляет пустые теги
        while (preg_match("#<(a|span|div)[^<]*>[\s\n\r\t]*</(a|span|div)>#si", $text, $res)) $text = str_replace($res[0], "", $text);
        //while (preg_match("#<\w+[^<]*>[\s\n\r\t]*</\w+>#si", $text, $res)) $text = str_replace($res[0], "", $text);
        
        // удаляет пустые одинарные теги
        //$text = preg_replace("#<br[^>]*/>#si", "", $text);
        
        return $text;       
    }    
    
}

/** Checks a variable to see if it should be considered a boolean true or false.
 *     Also takes into account some text-based representations of true of false,
 *     such as 'false','N','yes','on','off', etc.
 * @author Samuel Levy <sam+nospam@samuellevy.com>
 * @param mixed $in The variable to check
 * @param bool $strict If set to false, consider everything that is not false to
 *                     be true.
 * @return bool The boolean equivalent or null (if strict, and no exact equivalent)
 */
function boolval($in, $strict = false) {
    $out    = null;
    $in     = (is_string($in) ? strtolower($in) : $in);
    
    // if not strict, we only have to check if something is false
    if (in_array($in, array('false','no', 'n', '0', 'off', false, 0), true) || !$in) 
    {
        $out = false;
    } 
    else if ($strict) 
    {
        // if strict, check the equivalent true values
        if (in_array($in, array('true', 'yes', 'y', '1', 'on', true, 1), true)) 
        {
            $out = true;
        }
    } 
    else 
    {
        // not strict? let the regular php bool check figure it out (will
        //     largely default to true)
        $out = ($in ? true : false);
    }
    
    return $out;
}    
