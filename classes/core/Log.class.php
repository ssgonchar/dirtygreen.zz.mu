<?php
    /**
     * Тип события: запрос на получение страницы
     */
    define ('LOG_REQUEST', 1);

    /**
     * Тип события: ошибка выполнения приложения
     */
    define ('LOG_ERROR', 2);

    /**
     * Тип события: SQL запрос
     */
    define ('LOG_QUERY', 3);

    /**
     * Тип события: пользовательский
     */
    define ('LOG_CUSTOM', 4);

    /**
     * Тип события: кеш
     */
    define ('LOG_CACHE', 5);

    /**
     * Тип события: медленные запросы
     */
    define ('LOG_SLOW_QUERIES', 6);

    /**
     * Тип события: медленные запросы
     */
    define ('LOG_APP_WARNING', 7);
    
    /**
     * Тип события: Граббинг Email-сообщений
     */
    define ('LOG_EMAIL_GRABBER', 8);
    
/**
 * Класс для обслуживания лога событий приложения
 *
 * Синглтон
 *
 * version 2010.03.25, digi: LOG_CACHE, multiple instances
 */
class Log
{
    /**
     * Имя файла, в котором хранятся записи лога
     *
     * @var string
     */
    var $log_file;

    /**
     * Указатель на файл
     *
     * @var integer
     */
    var $fp;

    /**
     * Конструктор.
     * 
     * @param string $log_file имя файла, в котором хранятся записи лога
     */
    function Log($log_file)
    {       
		//die($log_file);
        if (LOG != 'yes')
        {
            return;
        }

        $this->log_file = $log_file;
        $this->fp = fopen($log_file, 'a');
        
        if (!$this->fp)
        {
            chmod($log_file, 0777);    
            $this->fp = fopen($log_file, 'a');
        }
    }

    /**
     * Фабричный метод
     *
     * Создаёт экземпляр класса
     * 
     * @param string $log_file имя файла, в котором хранятся записи лога
     * @return Log
     */
    public static function Create($type, $log_file)
    {
        if ($type == LOG_EMAIL_GRABBER)  $key = 4;
        else if ($type == LOG_SLOW_QUERIES)  $key = 3;
        else if ($type == LOG_CACHE)    $key = 2;
        else                            $key = 1;
        
        static $instance;        
        if (!isset($instance))
        {
            $instance = array($key => new Log($log_file));
        }
        else if (!array_key_exists($key, $instance))
        {
            $instance[$key] = new Log($log_file);
        }
        
        return $instance[$key];
    }

    /**
     * Деструктор
     */
    function Destructor()
    {
        fclose($this->fp);
    }
    
    /**
     * Возвращает текущее время с прописанным в методе форматированием
     */
    function _time()
    {
        return date('Y-m-d H:i:s ');
    }

    /**
     * Формирует запись для лога
     *
     * @param integer $type тип записи
     * @param string $log_file текст записи
     * @return string
     */
    function _formatLine($type, $str)
    {
        if ($type == LOG_ERROR || $type == LOG_APP_WARNING || $type == LOG_EMAIL_GRABBER)
        {
            $result =   '----------------------------------------------------------------------------------------------------------';
            $result .= "\n" . $this->_time() . ' ERROR';
            $result .= "\n" . $str . "\n";
            $result .=  '----------------------------------------------------------------------------------------------------------';
            $result .=  "\n\n";

            /*  20111003, zharkov: отсылка идет из user_error_handler (index.php)
            // отсылка уведомления на почту
            if ($type == LOG_ERROR && MAILER_ENABLED == 'yes')
            {
                $from       = SUPPORT_ADDRESS;
                $to         = USER_RESPONSIBLE;
                $subject    = APP_NAME . ": Ошибка приложения.";
                $body       = str_replace("\n", "<br>", $result) . "<br><pre>" . var_export($_REQUEST, TRUE) . "<br><br>" . (isset($_SESSION['user']) ? var_export($_SESSION['user'], TRUE) : 'под незарегистрированным пользователем');
                
                $headers    = "From: " . $from . "\r\n";
                $headers    .= "Content-type: text/html; charset=\"utf-8\"\r\n";

                Log::AddLine(LOG_CUSTOM, "Mailer._send():: TO:$to\nSubject:$subject\nHEADERS:\n$headers\n\nBODY:\n$body\n\n");

                $subject="=?UTF-8?b?".base64_encode($subject)."?=";

                mail($to, $subject, $body, $headers);                
            }
            */
            return $result;
        }
        else
        {
            $result = $this->_time();

            switch ($type)
            {
                case LOG_REQUEST:
                    $result .= ' REQUEST =============================================================================';
                    break;                                                                                            

                case LOG_QUERY:
                    $result .= ' QUERY';
                    break;
            }

            return $result . "\n" . $str . "\n\n";
        }
    }

    /**
     * Добавляет запись в лог
     *
     * Запись добавляется, если глобальная переменная LOG имеет значение 'yes'
     *
     * @param integer $type тип записи
     * @param string $log_file текст записи
     */
    public static function Bugtruck($str)
    {
		
		$type = LOG_ERROR;
		$filename = APP_LOGS . date('Ymd') . '.addmsg.txt';
        //die($filename);
        $log = Log::Create($type, $filename);
        $log->_addLine($type, $str);
    }
    
    /**
     * Добавляет запись в лог
     *
     * Запись добавляется, если глобальная переменная LOG имеет значение 'yes'
     *
     * @param integer $type тип записи
     * @param string $log_file текст записи
     */
    public static function AddLine($type, $str)
    {
        if (LOG != 'yes')
        {
            return;
        }
        
        if ($type == LOG_CACHE)
        {
            $filename = APP_LOGS . date('Ymd') . '.cache.txt';
        }
        else if ($type == LOG_SLOW_QUERIES)
        {
            $filename = APP_LOGS . date('Ymd') . '.slow.txt';
        }
        else if ($type == LOG_EMAIL_GRABBER)
        {
            $filename = APP_LOGS . date('Ymd') . '.emailgrabber.txt';
        }
        else
        {
            $filename = APP_LOGS . date('Ymd') . '.app.txt';
        }
        
        $log = Log::Create($type, $filename);
        $log->_addLine($type, $str);
    }

    /**
     * Записывает запись лога в файл
     *
     * @param integer $type тип записи
     * @param string $log_file текст записи
     */
    function _addLine($type, $str)
    {
        if (fwrite($this->fp, $this->_formatLine($type, $str)) == -1)
        {
            die('Error writing to log');
        }   
    }
}

?>