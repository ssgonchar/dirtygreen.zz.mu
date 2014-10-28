<?php

    define('SLOW_QUERY_TIME',       1); // s
    define('MAX_LENGTH_PER_PARAM',  100);

/**
 * Класс для взаимодействия с базой данных
 *
 * Синглтон
 *
 * digi, 2010.05.26 MultiQuery updated
 * digi, 2010.06.20 Slow queries log
 *                  connection error email notifications
 * digi, 2010.06.25 настройки кодировок и глубины рекурсии вынесены в конфиг MySQL
 * digi, 2010.08.16 OpenConnection
 */
class DatabaseConnection
{
    /**
     * Подключение к базе данных
     *
     * @var resource
     */
    var $connection;

    /**
     * Имя текущей базы данных
     *
     * @var string
     */
    var $db;
    
    /**
     * Флаг, устанавливается при соединении с БД
     *
     * @var boolean
     */
    var $is_connected = false;

    /**
     * Данные для подключения
     *
     * @var array
     */
    var $connection_settings;

    /**
     * Конструктор
     *
     * @param array $connection_settings параметры подключения к базе данных
     * @see DatabaseConnection::$db
     */
    function DatabaseConnection($connection_settings)
    {
		//die(print_r($connection_settings));
        $this->connection_settings = $connection_settings;
    }
    
    /**
     * Открывает соединение с БД
     * Явный вызов помогает избежать обязательной коннекции при инициализации класса
     */
    function OpenConnection()
    {
        if ($this->is_connected) return;
        
        $this->is_connected = true;
		
        $this->_connect();
    }

    /**
     * Пробует установить соединение с базой данных
     *
     * @param array $connection_settings параметры подключения к базе данных
     * Массив, каждый элемент которого представляет собой ассоциативный массив:
     *  dbhost: сервер
     *  dbname: имя базы данных
     *  dbuser: имя пользователя
     *  dbpass: пароль
     *  charset: набор символов
     * @see DatabaseConnection::$db
     */
    function _connect()
    {
        //$this->connection_settings['charset'] = 'utf8';
        Log::AddLine(LOG_CUSTOM, "DB connection {$this->connection_settings['dbhost']}, {$this->connection_settings['dbuser']}, {$this->connection_settings['dbpass']}, {$this->connection_settings['dbname']}, {$this->connection_settings['charset']}");
        $this->connection = mysqli_connect($this->connection_settings['dbhost'], $this->connection_settings['dbuser'], $this->connection_settings['dbpass']/*, true*/);
		//print_r($this->connection);
        if (!$this->connection)
        {
            //die('Could not connect ' . $connection_settings['dbhost']);
            //die('Can\'t connect database');
            trigger_error("Can't connect MySQL server, " . mysqli_connect_error(), E_USER_ERROR);
            _503('Service unavailable');
			//echo('if');
        }
        
        if (!mysqli_select_db($this->connection, $this->connection_settings['dbname']))
        {
            //die('Can\'t select database : ' . mysqli_error($this->connection));
            //die('Can\'t select database');
            trigger_error("Can't connect database " . $this->connection_settings['dbname'] . "<br>" . mysqli_errno($this->connection) . ": " . mysqli_error($this->connection) . "\n", E_USER_ERROR);
            _503('Service unavailable');
        }

       // Log::AddLine(LOG_CUSTOM, "DB connection charset {$connection_settings['charset']}");
        $this->db = $this->connection_settings['dbname'];

        /**
         * @link http://dev.livelib.ru/drupal/node/190
         *
         * 2010.06.25 digi, теперь все эти настройки в my.cfg
         */     
        if ($this->connection_settings['charset'] != '')
        {
            mysqli_query($this->connection, 'SET CHARSET ' . $this->connection_settings['charset']);
            mysqli_query($this->connection, 'SET NAMES ' . $this->connection_settings['charset']);
            
           // mysqli_query($this->connection, 'SET CHARSET ' . 'utf8');
            //mysqli_query($this->connection, 'SET NAMES ' . 'utf8');
        }

          

        // установка часового пояса
        if (isset($this->connection_settings['time_zone']) && !empty($this->connection_settings['time_zone']))
        {
			//print_r('fuck off');
            mysqli_query($this->connection, 'SET time_zone="' . $this->connection_settings['time_zone'] . '"');            
        }
        
        mysqli_query($this->connection, 'SET @ENABLE_TRIGGERS = TRUE');
        mysqli_query($this->connection, 'SET @@max_sp_recursion_depth = 250');
        
   
 




		//print_r('1111');
    }

    /**
     * Закрывает соединение с базой данных
     */
    function _disconnect()
    {
        if ($this->connection) 
        {
            mysqli_close($this->connection);
            $this->connection = false;
        }
    }

    /**
     * Деструктор
     */
    function Destructor()
    {
        if ($this->connection)
        {
            mysqli_close($this->connection);
            $this->connection = false;
        }
    }

    /**
     * Фабричный метод
     *
     * Создаёт экземпляр класса
     * 
     * @param array $connection_settings параметры подключения к базе данных
     * @see DatabaseConnection::_connect()
     * @return DatabaseConnection 
     */
    public static function & Create($connection_settings)
    {
        $key = $connection_settings['dbhost'] . $connection_settings['dbname'] . $connection_settings['dbuser'] . $connection_settings['dbpass'] . $connection_settings['charset'];

        static $instance;
        if (!isset($instance))
        {
            $instance = array($key => new DatabaseConnection($connection_settings));
        }
        else if (!array_key_exists($key, $instance))
        {
            $instance[$key] = new DatabaseConnection($connection_settings);
        }

        return $instance[$key];
    }
    
    /**
     * Выполняет запрос
     *
     * Создаёт экземпляр класса
     * 
     * @param string $str SQL запрос
     * @return resource ссылка на ресурс
     */
    function Query($str)
    {
		//print_r($str);
		//die();
        $timer = Timer::Start();
        
        $resource = mysqli_query($this->connection, $str);
        if (!$resource)
        {
            // 1205: Lock wait timeout exceeded; try restarting transaction
            // 1213: Deadlock found when trying to get lock; try restarting transaction
            $error_no = mysqli_errno($this->connection);
            if ($error_no == 1213 || $error_no == 1205)
            {
                // вторая попытка
                sleep(3);
                $resource = mysqli_query($this->connection, $str);
            }
            
            if (!$resource)
            {
                trigger_error("Error executing query<br><b>$str</b><br>" . $error_no . ": " . mysqli_error($this->connection) . "\n" . $str, E_USER_ERROR);
                //die('Error');
                _503('Error');
            }
        }

        $this->_log_query($str, $timer->Stop());

        return $resource;
    }

    /**
     * Выполняет запрос мульти, и возвращает набор рекордсетов
     *
     * Создаёт экземпляр класса
     * 
     * @param string $str SQL запрос
     * @return resource ссылка на ресурс
     */
    //function MultiQuery($str)
    
    /**
     * Выполняет запрос мульти, и возвращает набор рекордсетов
     *
     * Создаёт экземпляр класса
     * 
     * @param QueryBuilder $queryBuilder
     * @param array $params
     * @return array
     * 
     * @version 20130211, d10n: добавлена возможность обрезки значений параметров для логирования
     */
    function MultiQuery(QueryBuilder $queryBuilder, $params = array())
    {
		//die();
        // 20130211 d10n : подготовка строки запроса
        $str = $queryBuilder->Prepare($params);
        //print_r($str);
        $timer = Timer::Start();
        
        $mqresult = mysqli_multi_query($this->connection, $str);
		//print_r($mqresult);
		//die();
        if (!$mqresult)
        {
			
			print_r("Error executing query<br><b>$str</b><br>" . mysqli_errno($this->connection) . ": " . mysqli_error($this->connection) . "\n" . $str);
            trigger_error("Error executing multi query (rowset #1) <br><b>$str</b><br>" . mysqli_errno($this->connection) . ": " . mysqli_error($this->connection) . "\n" . $str, E_USER_ERROR);
            //print_r("Error executing multi query (rowset #1) <br><b>$str</b><br>" . mysqli_errno($this->connection) . ": " . mysqli_error($this->connection) . "\n" . $str);
            
			//print_r("Error executing multi query (rowset #1) <br><b>$str</b><br>" . mysqli_errno($this->connection) . ": " . mysqli_error($this->connection) . "\n" . $str, E_USER_ERROR);
            _503('Error');
        }

        $res            = array();
        $more_result    = true;
        $counter        = 1;

        do
        {
            $counter++;
            
            if ($result = mysqli_store_result($this->connection))
            {
                $set = array();
                
                while ($row = mysqli_fetch_assoc($result))
                {
                    $set[] = $row;
                }
                mysqli_free_result($result);

                $res[] = $set;
            }
            
            $more_result = mysqli_more_results($this->connection);
            
            if ($more_result)
            {
                if (!mysqli_next_result($this->connection))
                {
					//print_r("Error executing query<br><b>$str</b><br>" . mysqli_errno($this->connection) . ": " . mysqli_error($this->connection) . "\n" . $str);
                    trigger_error("Error executing multi query (rowset #$counter) <br><b>$str</b><br>" . mysqli_errno($this->connection) . ": " . mysqli_error($this->connection) . "\n" . $str, E_USER_ERROR);
                    _503('Error');
                }
            }           
        } 
        while ($more_result);
        
        
        // Если необхдимо то производим обрезку значений параметров ХП для логирования
        // Константы для внешних settings
        // CUT_LOG yes/no
        // MAX_LENGTH_PER_PARAM > 100
        if (CUT_LOG == 'yes')
        {
            foreach ($params['values'] as $key => $value)
            {
                $value = (string) $value;

                if (mb_strlen($value) > MAX_LENGTH_PER_PARAM)
                {
                    $params['values'][$key] = mb_strcut($value, 0, MAX_LENGTH_PER_PARAM) . ' ...';
                }
            }
            $str = $queryBuilder->Prepare($params);
        }
        
        $this->_log_query($str, $timer->Stop());

		//print_r('1');
        return $res;
    }

    /**
     * Возвращает id последней добавленной записи
     *
     * @return integer
     */
    function Identity()
    {
        return mysqli_insert_id($this->connection);
    }

    /**
     * Возвращает количество задействованных записей в результате выполнения последнего запроса
     *
     * @return integer
     */
    function AffectedRows()
    {
        return mysqli_affected_rows($this->connection);
    }

    /**
     * Возвращает количество записей в результате выполнения последнего запроса, без учета LIMIT установок!
     * Запрос должен содержать параметр SQL_CALC_FOUND_ROWS @see QueryBuilder::Prepare()
     * Если в запросе не был установлен параметр SQL_CALC_FOUND_ROWS возвращаемое число может быть некорректным.
     *
     * it will soon be deprecated
     *
     * @return integer
     */
    function FoundRows()
    {
        $str = "SELECT FOUND_ROWS()";
        $resource = mysqli_query($this->connection, $str);
        if (!$resource)
        {
			//print_r("Error executing query<br><b>$str</b><br>" . mysqli_errno($this->connection) . ": " . mysqli_error($this->connection) . "\n" . $str);
            trigger_error("Error executing query<br><b>$str</b><br>" . mysqli_errno($this->connection) . ": " . mysqli_error($this->connection) . "\n" . $str, E_USER_ERROR);
            die('Error');
        }
        
        if ( $line = mysqli_fetch_array($resource) )
        {
            return $line[0];
        }
        else
        {
            return 0;
        }
    }

    /**
     * Добавляет информацию о выполнении запроса в лог
     *
     * @param string $str SQL запрос
     * @param string $time длительность выполнения запроса
     */
    function _log_query($str, $time)
    {
        $affected_rows = $this->AffectedRows();
        Log::AddLine(LOG_QUERY, $this->db . ": $str\nAffected rows: $affected_rows\n$time s");
        
        if ($time > SLOW_QUERY_TIME)
        {
            Log::AddLine(LOG_SLOW_QUERIES, $this->db . ": $str\nAffected rows: $affected_rows\n$time s");
        }
    }
}

