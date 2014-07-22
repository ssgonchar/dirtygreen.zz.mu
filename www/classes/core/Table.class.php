<?php
    require_once(APP_PATH . 'classes/core/QueryBuilder.class.php');

/**
 * Класс инкапсулирует логику работы с определённой таблицей базы данных.
 *
 * digi, 2010.03.21 CallStoredFunction
 * digi, 2010.05.04 Replace
 * digi, 2010.05.26 _fetch_multi_set deprecated
 * @version 20110623, zharkov: $connection_time_zone, ConnectDatabase()
 */
class Table
{
    /**
     * Имя таблицы в базе данных
     *
     * @var string
     */
    var $table_name;


    /**
     * Название индексного поля
     *
     * @var string
     */
    var $index_field;

    /**
     * Поле сортировки по умолчанию в запросах на выборку
     *
     * @var string
     */
    var $order_by;

    /**
     * Соединение с базой данных
     *
     * @var DatabaseConnection
     */
    var $db;

    /**
     * Построитель запросов
     *
     * @var QueryBuilder
     */
    var $query;
    
    /**
     * Данные для подключения к БД
     *
     * @var array
     */
    var $connection_settings;
    
    /**
     * Флаг, устанавливается при соединении с БД
     *
     * @var boolean
     */
    var $is_connected = false;
    
    /**
     * Часовой пояс
     * 
     * @var mixed
     */
    var $connection_time_zone = DB_TIME_ZONE;
    
    /**
     * Конструктор.
     * 
     * @param string $table_name имя таблицы базы данных
     * @param string $connection_settings параметры соединения с базой данных, описание 
     * @see DatabaseConnection::Create()
     */
    function Table($table_name, $connection_settings)
    {
        $this->index_field = 'id';
        $this->name_field = 'name';
        
        $this->table_name = $table_name;
        $this->connection_settings = $connection_settings;
    }
    
    /**
     * Открывает соединение с БД
     * Явный вызов помогает избежать обязательной коннекции при инициализации класса
     * 
     * @version 20110622, zharkov: установка time_zone
     */
    function ConnectDatabase()
    {
        if ($this->is_connected) return;
        //print_r($this->is_connected);
        $this->is_connected = true;
        //print_r($this->is_connected);
        if (!empty($this->connection_time_zone)) $this->connection_settings['time_zone'] = $this->connection_time_zone;
        //print_r($this->connection_time_zone);
        $this->db = DatabaseConnection::Create($this->connection_settings);
			//print_r($this->db);
	   $this->db->OpenConnection();
        
        $this->query = QueryBuilder::Create($this->db->connection);
		//print_r('111');
    }


    /**
     * Извлекает запись из рекордсета в виде ассоциативного массива
     *
     * @param resource $resource
     * @return array
     */
    private function _fetch_row($resource)
    {
        return mysqli_fetch_assoc($resource);
    }


    /**
     * Извлекает все записи из рекордсета и представляет каждую запись в виде ассоциативного массива
     *
     * @param resource $resource
     * @return array
     */
    public function _fetch_array($resource) //!must be private
    {
        $res = array();
        $count = mysqli_num_rows($resource);
        while ($count-- > 0)
        {
            $res[] = mysqli_fetch_assoc($resource);
        }

        return $res;
    }

    /**
     * Извлекает все записи из рекордсета и представляет каждую запись в виде ассоциативного массива
     *
     * @param resource $resource
     * @return array
     */
    private function _fetch_raw_array($resource)
    {
        return mysqli_fetch_array($resource);
    }

    /**
     * Извлекает все наборы записей из ресурса и, представляет каждую запись каждого рекордсета в виде ассоциативного массива
     *
     * @param resource $resource
     * @return array
	 * 
	 * new php 5.3.1 version
     * @deprecated
     */
    function _fetch_multi_set()
    {
        $res            = array();
        $more_result    = true;
        
        do
        {
            if ($result = mysqli_store_result($this->db->connection))
            {
                $set = array();
                
                while ($row = mysqli_fetch_assoc($result))
                {
                    $set[] = $row;
                }
                mysqli_free_result($result);

                $res[] = $set;
            }
            
            $more_result = mysqli_more_results($this->db->connection);
            
            if ($more_result)
            {
                $more_result = mysqli_next_result($this->db->connection);
            }
        } 
        while ($more_result);

        return $res;        
    }
	
	
	/*	old version till 20100201
    function _fetch_multi_set($resource)
    {
        if (!$resource) return null;

        $res = array();

        do
        {
            if ($result = mysqli_store_result($this->db->connection))
            {
                $set = array();
                
                while ($row = mysqli_fetch_assoc($result))
                {
                    $set[] = $row;
                }
                mysqli_free_result($result);

                $res[] = $set;
            }
        } while (mysqli_next_result($this->db->connection));

        return $res;        
    }
	*/

    /**
     * Выполняет запрос
     *
     * @param array $params массив параметров запроса, описание
     * @see QueryBuilder::Prepare()
     * @return resource
     */
    private function _exec_query($params)
    {
    //echo "<p><b>" . $this->query->Prepare($params) . "</b></p>";
        $this->ConnectDatabase();
		//print_r($this->query->Prepare($params));
		//if($sp_name)
        return $this->db->query($this->query->Prepare($params));
    }

    /**
     * Выполняет запрос мульти
     *
     * @param array $params массив параметров запроса, описание
     * @return resource
     */
    private function _exec_multi_query($params)
    {
        //echo "<p><b>" . $this->query->Prepare($params) . "</b></p>";
        //print_r('11');
		$this->ConnectDatabase();
        //return $this->db->multiquery($this->query->Prepare($params));
        
        // 20130211, d10n: добавлена возможность обрезки значений параметров для логирования
        //$tmp = $this->db->multiquery($this->query, $params);
		//print_r('111');
		return $this->db->multiquery($this->query, $params);
    }

    /**
     * Выполняет "голый" запрос
     *
     * @param resource $params массив параметров запроса, описание
     * @return resource
     */
    public function _exec_raw_query($query) //! must be private
    {
		//print_r($query);
        $this->ConnectDatabase();
        return $this->db->query($query);
    }


    /**
     * Проверяет, является ли входной параметр массивом
     *
     * Если переменная не является массивом, то формируется новый массив из одного элемента, 
     * в который помещается переменная
     *
     * @param mixed $params переменная для проверки
     * @return array
     */
    private function _assure_is_array($params)
    {
        if (!is_array($params))
        {
            return array($params);
        }

        return $params;
    }


    /**
     * Выбирает одну запись из таблицы
     *
     * Возвращает запись в виде ассоциативного массива.
     * Если в результате выполнения запроса получено более одной записи, то возвращается первая;
     * если не получено ни одной записи, возвращается null.
     * Если не задан параметр 'order', то записи сортируются по умолчанию в соответствии со значением поля {@link $order_by}.
     *
     * @param mixed $params опциональный, параметры для запроса
     * @return array
     */
    public function SelectSingle($arg = array())
    {
        $params = $arg; //func_num_args() > 0 ? func_get_arg(0) : array();
        $params = $this->_assure_is_array($params);

        $params['table'] = $this->table_name;
        $params['limit'] = 1;
        if (!array_key_exists('order', $params) && isset($this->order_by))
        {
            $params['order'] = $this->order_by;
        }

        $resource = $this->_exec_query($params); 
        $result = $this->_fetch_array($resource);

        if (count($result))
            return $result[0];

        return null;
    }

    
    /**
     * Выбирает набор записей из таблицы
     *
     * Возвращает все записи из рекордсета и представляет каждую запись в виде ассоциативного массива
     * Если не задан параметр 'order', то записи сортируются по умолчанию в соответствии со значением поля {@link $order_by}
     *
     * @param mixed $params опциональный, параметры для запроса
     * @return array
     */
    public function SelectList($arg = array())
    {
        $params = $arg; //func_num_args() > 0 ? func_get_arg(0) : array();
        $params = $this->_assure_is_array($params);

        $params['table'] = $this->table_name;
        if (!array_key_exists('order', $params) && isset($this->order_by))
        {
            $params['order'] = $this->order_by;
        }

        $resource = $this->_exec_query($params);
        return $this->_fetch_array($resource);
    }

    
    /**
     * Выбирает одну запись из таблицы, для которой значение индексного поля равно параметру $id
     *
     * Возвращает запись в виде ассоциативного массива
     * Если запись не найдена, то возвращается null
     *
     * @param integer $id значение индексного поля
     * @return array
     */
    public function Select($id)
    {
        if (!isset($id))
        {
            trigger_error('Parameter "id" must be specified!');
            return;
        }

        $params = array('where' => array('conditions' => $this->table_name . '.' . $this->index_field . '=?', 'arguments' => $id));

        $result = $this->SelectList($params);
    
        if (count($result))
            return $result[0];

        return null;
    }


    /**
     * Выбирает все записи из таблицы
     * 
     * Результат представлен в виде ассоциативного массива, 
     * где ключами массива являются значения индексного поля,
     * а значениями массива являются значения указанного поля.
     *
     * @param mixed $field название поля, значения которого становятся значениями результирующего массива
     * @return array
     */
    public function SelectListAssoc($field, $arg = array())
    {
        $params = $arg; //func_num_args() > 1 ? func_get_arg(1) : array();
        $params = $this->_assure_is_array($params);

        $arr = $this->SelectList($params);

        $assoc = array();

        foreach ($arr as $a)
        {
            $assoc[$a[$this->index_field]] = $a[$field];
        }

        return $assoc;
    }
    

    /**
     * Возвращает количество записей, соответствующих указанным условиям
     *
     * @param mixed $params опциональный, параметры для запроса
     * @return integer
     */
    public function Count($arg = array())
    {
        $params = $arg; //func_num_args() > 0 ? func_get_arg(0) : array();
        $params = $this->_assure_is_array($params);

        $params['query'] = 'count';
        $params['table'] = $this->table_name;

        $resource = $this->_exec_query($params);
        $arr = $this->_fetch_row($resource);
        return $arr['rows'];
    }


    /**
     * Возвращает количество записей в предидущем запросе, так как будто в предидущем запросе не было LIMIT условия 
     * @see DatabaseConnection::FoundRows()
     *
     * @return integer
     */
    public function FoundRows()
    {
        return $this->db->FoundRows();
    }

    
    /**
     * Удаляет записи, соответствующие указанным условиям
     *
     * @param mixed $params опциональный, параметры для запроса
     * @return integer количество задействованных записей
     */
    public function DeleteList($arg = array())
    {
        $params = $arg; //func_num_args() > 0 ? func_get_arg(0) : array();
        $params = $this->_assure_is_array($params);

        $params['query'] = 'delete';
        $params['table'] = $this->table_name;

        $this->_exec_query($params);

        return $this->db->AffectedRows();
    }
    
    /**
     * Удаляет запись, для которой значение индексного поля равно параметру $id
     *
     * @param integer $id значение индексного поля
     * @return integer количество задействованных записей
     */
    public function Delete($id)
    {
        if (!isset($id))
        {
            trigger_error('Parameter "id" must be specified!');
            return;
        }

        $params = array('where' => array('conditions' => 'id=?', 'arguments' => $id));

        return $this->DeleteList($params);
    }

    
    /**
     * Проверяет, существует ли запись (или записи), соответствующая указанным условиям
     *
     * @param mixed $params опциональный, параметры для запроса
     * @return bool
     */
    public function Exists($arg = array())
    {
        $params = $arg; //func_num_args() > 0 ? func_get_arg(0) : array();
        $params = $this->_assure_is_array($params);

        return ($this->Count($params) > 0 ? true : false);
    }


    /**
     * Добавляет новую запись в базу данных
     *
     * @param array $values ассоциативный массив, ключами являются названия полей, значениями - соответствующие значения
     * @param boolean $ignore_unique_error игнорировать ошибки при повторной вставке уникального ключа
     * @return integer id новой записи
     * @version 2009.05.05 by digi
     */
    public function Insert($values, $ignore_unique_error = false)
    {
        $params = array();
        $params['query'] = 'insert';
        $params['table'] = $this->table_name;
        $params['values'] = $values;
        
        if (!empty($ignore_unique_error))
        {
            $params['ignore'] = true;
        }

        $this->_exec_query($params);
        return $this->db->Identity();
    }
    
    /**
     * Добавляет набор записей в базу данных
     *
     * @param array $values ассоциативный массив, ключами являются названия полей, значениями - соответствующие значения
     * @param array $fields массив названий полей
     * @param boolean $ignore_unique_error игнорировать ошибки при повторной вставке уникального ключа
     * @return integer id новой записи
     */
    function InsertList($fields, $values, $ignore_unique_error = false)
    {
        $params = array();
        $params['query'] = 'insert';
        $params['table'] = $this->table_name;
        $params['fields'] = $fields;
        $params['values'] = $values;

        if (!empty($ignore_unique_error))
        {
            $params['ignore'] = true;
        }

        $this->_exec_query($params);
        return $this->db->Identity();
    }
    
    /**
     * Добавляет новую запись в базу данных
     *
     * @param array $values ассоциативный массив, ключами являются названия полей, значениями - соответствующие значения
     * @return integer id новой записи
     * @version 2010.05.04 by digi
     */
    public function Replace($values)
    {
        $params = array();
        $params['query'] = 'replace';
        $params['table'] = $this->table_name;
        $params['values'] = $values;
        
        $this->_exec_query($params);
        return $this->db->Identity();
    }
    
    /**
     * Добавляет набор записей в базу данных
     *
     * @param array $values ассоциативный массив, ключами являются названия полей, значениями - соответствующие значения
     * @param array $fields массив названий полей
     * @return integer id новой записи
     * @version 2010.05.04 by digi
     */
    function ReplaceList($fields, $values)
    {
        $params = array();
        $params['query'] = 'replace';
        $params['table'] = $this->table_name;
        $params['fields'] = $fields;
        $params['values'] = $values;

        $this->_exec_query($params);
        return $this->db->Identity();
    }
    
      
    /**
     * Обновляет значения записи, для которой значение индексного поля равно параметру $id
     *
     * @param integer $id значение индексного поля
     * @param array $values ассоциативный массив, ключами являются названия полей, значениями - соответствующие значения
     * @param boolean $ignore_unique_error игнорировать ошибки при повторной вставке уникального ключа
     */
    public function Update($id, $values, $ignore_unique_error = false)
    {
        if (!isset($id))
        {
            trigger_error('Parameter "id" must be specified!');
            return;
        }
        
        $params = array('where' => array('conditions' => 'id=?', 'arguments' => $id));
        $params['values'] = $values;

        if (!empty($ignore_unique_error))
        {
            $params['ignore'] = true;
        }

        $this->UpdateList($params, $ignore_unique_error);
    }


    /**
     * Обновляет значения записей
     *
     * @param mixed $params параметры для запроса
     * @return integer количество обновлённых записей
     * @param boolean $ignore_unique_error игнорировать ошибки при повторной вставке уникального ключа
     */
    public function UpdateList($params = array(), $ignore_unique_error = false)
    {
        $params = $this->_assure_is_array($params);
        
        $params['query'] = 'update';
        $params['table'] = $this->table_name;

        if (!empty($ignore_unique_error))
        {
            $params['ignore'] = true;
        }

        $this->_exec_query($params);
        
        return $this->db->AffectedRows();
    }

    /**
     * Вызывает хранимую процедуру
     *
     * @param string $name имя хранимой процедуры
     * @param array $values параметры для хранимой процедуры
     */
    function CallStoredProcedure($name, $values)
    {
		//print_r($name);
		//print_r($values);
        $params['query'] = 'call';
        $params['procedure'] = $name;
        $params['values'] = $values;
		//print_r(__FUNCTION__);
		//print_r($values);
        return $this->_exec_multi_query($params);
        //$result = $this->_exec_multi_query($params);        
        //if (!$result) return null;        
        //return $this->_fetch_multi_set();
    }
    
    /**
     * Вызывает хранимую процедуру
     *
     * @param string $name имя хранимой процедуры
     * @param array $values параметры для хранимой процедуры
     */
    function CallStoredFunction($name, $values)
    {
        $params['query']    = 'function';
        $params['function'] = $name;
        $params['values']   = $values;
        
        $resource = $this->_exec_query($params);
        $result = $this->_fetch_array($resource);

        if (count($result)) return $result[0];

        return null;
    }
}

