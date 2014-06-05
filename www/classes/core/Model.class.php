<?php
    require_once(APP_PATH . 'classes/core/Table.class.php');

    /**
     * @deprecated
     */
    define ('RELATION_ONE_TO_ONE', 1);

    /**
     * @deprecated
     */
    define ('RELATION_MANY_TO_ONE', 2);

    /**
     * @deprecated
     */
    define ('RELATION_ONE_TO_MANY', 3);

    /**
     * @deprecated
     */
    define ('RELATION_MANY_TO_MANY', 4);

/**
 * Обёртка для класса Table
 *
 * digi, 2010.03.21 CallStoredFunction
 * digi, 2010.05.04 Replace
 * digi, 2010.05.08 _get_cached_data
 * digi, 2010.06.02 $arg = array() i/o $arg = func_num_args() > 0 ? func_get_arg(0) : array();
 * digi, 2010.06.23 $object_alias, ClearTagById()
 * @version 20110623, zharkov: SetConnectionTimeZone($time_zone)
 * @version 20111108, zharkov: вызов методов sp
 */
class Model
{
    /**
     * Параметры для формирования запроса 
     *
     * @var mixed
     */
    var $params;


    /**
     * Оборачиваемая таблица
     *
     * @var Table
     */
    var $table;

    /**
     * Текущий пользователь
     *
     * @var integer
     */
    var $user_id    = 0;
    var $user_role  = ROLE_GUEST;
    var $user_login = '';
    var $session_id = '';

    /**
     * Массив информационных сообщений
     *
     * @var array
     */
    var $messages;

    /**
     * Алиас объекта
     *
     * @var integer
     */
    var $object_alias = '';
    
    /**
     * Текущий язык приложения
     * 
     * @var mixed
     */
    var $lang = DEFAULT_LANG;

    /**
     * Конструктор.
     * 
     * @param string $table_name имя таблицы базы данных
     * @param string $connection_settings опциональный, параметры соединения с базой данных, описание 
     * @see DatabaseConnection::Create()
     */
    function Model($table_name)
    {
        $default_connection_settings =
            array('dbhost' => APP_DBHOST,
                'dbname' => APP_DBNAME,
                'dbuser' => APP_DBUSER,
                'dbpass' => APP_DBPASS,
                'charset' => 'utf8'
            );

        $connection_settings = func_num_args() > 1 ? func_get_arg(1) : $default_connection_settings;

        if (!is_array($connection_settings))
        {
            $connection_settings = $default_connection_settings;
        }

        $this->table = new Table($table_name, $connection_settings);

        $this->messages = array();

        if (array_key_exists('user', $_SESSION))
        {
            $this->user_id      = Request::GetInteger('id',         $_SESSION['user'], 0);
            $this->user_login   = Request::GetString('login',       $_SESSION['user'], '');
            $this->user_role    = Request::GetString('role_id',     $_SESSION['user'], ROLE_GUEST);
        }
        
        $this->session_id = session_id();
        
        $this->lang = isset($_REQUEST['lang']) ? Request::GetString('lang', $_REQUEST, '', 2) : $this->lang;
    }


    /**
     * Триггер, срабатывает перед запросом на выборку
     */
    function _before_select(){}


    /**
     * Триггер, проверяет, допустим ли запрос на выборку
     *
     * @return bool
     */
    function _validate_select(){ return true; }


    /**
     * Триггер, срабатывает после запроса на выборку
     */
    function _on_select(){}


    /**
     * Триггер, срабатывает перед запросом на вставку
     */
    function _before_insert(){}


    /**
     * Триггер, проверяет, допустим ли запрос на вставку
     *
     * @return bool
     */
    function _validate_insert($values){ return true; }


    /**
     * Триггер, срабатывает после запроса на вставку
     */
    function _on_insert(){}

    /**
     * Триггер, срабатывает перед запросом на вставку списка
     */
    function _before_insert_list(){}

    /**
     * Триггер, проверяет, допустим ли запрос на вставку списка
     *
     * @return bool
     */
    function _validate_insert_list($fields, $values){ return true; }

    /**
     * Триггер, срабатывает после запроса на вставку списка
     */
    function _on_insert_list(){}

    /**
     * Триггер, срабатывает перед запросом на удаление
     */
    function _before_delete(){}


    /**
     * Триггер, проверяет, допустим ли запрос на удаление
     *
     * @return bool
     */
    function _validate_delete(){ return true; }


    /**
     * Триггер, срабатывает после запроса на удаление
     */
    function _on_delete(){}


    /**
     * Триггер, срабатывает перед запросом на обновление
     */
    function _before_update(){}


    /**
     * Триггер, проверяет, допустим ли запрос на обновление
     *
     * @return bool
     */
    function _validate_update($values){ return true; }


    /**
     * Триггер, проверяет, допустим ли запрос на обновление списка
     *
     * @return bool
     */
    function _validate_update_list(){ return true; }


    /**
     * Триггер, срабатывает после запроса на обновление
     */
    function _on_update(){}


    /**
     * Триггер, проверяет, допустим ли запрос на вставку
     *
     * @param string $message информационное сообщение
     * @param integer $status статус сообщения
     */
    function _add_message($message, $status)
    {
        if (isset($message))
            if ($message != '')
                $this->messages[] = array('text' => $message, 'status' => $status);
    }

    /**
     * Выбирает набор записей из таблицы $table
     *
     * @param mixed $params опциональный, параметры для запроса
     * @return array
     */
    function SelectList($arg = array())
    {
        //$arg = func_num_args() > 0 ? func_get_arg(0) : array();

        if (is_string($arg))
        {
            $this->params = array('where' => array('conditions' => $arg));
        }
        else if (!is_array($arg))
        {
            $this->params = array();
        }
        else
        {
            $this->params = $arg;
        }

        if ($this->_validate_select())
        {
            $this->_before_select();

            $result = $this->table->SelectList($this->params);
    
            $this->_on_select();
        }

        return $result;
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
    function Select($id)
    {
        $this->params = array('where' => array('conditions' => 'id=?', 'arguments' => $id));

        if ($this->_validate_select())
        {
            $this->_before_select();

            $result = $this->table->Select($id);

            $this->_on_select();
        }

        return $result;
    }


    /**
     * Выбирает одну запись из таблицы $table
     *
     * Возвращает запись в виде ассоциативного массива.
     * Если в результате выполнения запроса получено более одной записи, то возвращается первая;
     * если не получено ни одной записи, возвращается null.
     * Если не задан параметр 'order', то записи сортируются по умолчанию в соответствии со значением поля {@link $order_by}.
     *
     * @param mixed $params опциональный, параметры для запроса
     * @return array
     */
    function SelectSingle($arg = array())
    {
        $this->params = $arg;//func_num_args() > 0 ? func_get_arg(0) : array();

        $result = $this->table->SelectSingle($this->params);

        return $result;
    }


    /**
     * Возвращает количество записей, соответствующих указанным условиям
     *
     * @param mixed $params опциональный, параметры для запроса
     * @return integer
     */
    function Count($arg = array())
    {
        $this->params = $arg; //func_num_args() > 0 ? func_get_arg(0) : array();

        return $this->table->Count($this->params);
    }


    /**
     * Возвращает количество записей в предидущем запросе, так как будто в предидущем запросе не было LIMIT условия 
     * @see DatabaseConnection::FoundRows()
     *
     * gonna soon be derecated
     *
     * @return integer
     */
    function FoundRows()
    {
        return $this->table->FoundRows();
    }


    /**
     * Удаляет записи, соответствующие указанным условиям
     *
     * @param mixed $params опциональный, параметры для запроса
     * @return integer количество задействованных записей
     */
    function DeleteList($arg = array())
    {
        $this->params = $arg; //func_num_args() > 0 ? func_get_arg(0) : array();

        $result = 0;

        if ($this->_validate_select())
        {
            $this->_before_delete();

            $result = $this->table->DeleteList($this->params);

            $this->_on_delete();
        }

        return $result;
    }


    /**
     * Удаляет запись, для которой значение индексного поля равно параметру $id
     *
     * @param integer $id значение индексного поля
     * @return integer количество задействованных записей
     */
    function DeleteSingle($id)
    {
        $this->params = array('where' => array('conditions' => 'id=?', 'arguments' => $id));

        $result = 0;

        if ($this->_validate_delete())
        {
            $this->_before_delete();

            $result = $this->table->Delete($id);

            $this->_on_delete();
        }

        return $result;
    }


    /**
     * Проверяет, существует ли запись (или записи), соответствующая указанным условиям
     *
     * @param mixed $params опциональный, параметры для запроса
     * @return bool
     */
    function Exists($arg = array())
    {
        $this->params = $arg; //func_num_args() > 0 ? func_get_arg(0) : array();

        return $this->table->Exists($this->params);
    }


    /**
     * Добавляет новую запись в базу данных
     *
     * @param array $values ассоциативный массив, ключами являются названия полей, значениями - соответствующие значения
     * @param boolean $ignore_unique_error игнорировать ошибки при повторной вставке уникального ключа
     * @return integer id новой записи
     * @version 2009.05.05 by digi
     */
    function Insert($values, $ignore_unique_error = false)
    {
        if ($this->_validate_insert($values))
        {
            $this->_before_insert();
        
            $result = $this->table->Insert($values, $ignore_unique_error);

            $this->_on_insert();
        }
        else
        {
            $result = 0;
        }

        return $result;
    }

    /**
     * Добавляет набор записей в базу данных
     *
     * @param array $fields массив названий полей
     * @param array $values массив массивов значений для вставки
     * @param boolean $ignore_unique_error игнорировать ошибки уникального ключа
     */
    function InsertList($fields, $values, $ignore_unique_error = false)
    {
        if ($this->_validate_insert_list($fields, $values))
        {
            $this->_before_insert();
        
            $this->table->InsertList($fields, $values, $ignore_unique_error);

            $this->_on_insert();
        }
    }

    /**
     * Добавляет новую запись в базу данных
     *
     * @param array $values ассоциативный массив, ключами являются названия полей, значениями - соответствующие значения
     * @return integer id новой записи
     * @version 2010.05.04 by digi
     */
    function Replace($values)
    {
        if ($this->_validate_insert($values))
        {
            $this->_before_insert();
        
            $result = $this->table->Replace($values);

            $this->_on_insert();
        }
        else
        {
            $result = 0;
        }

        return $result;
    }

    /**
     * Добавляет набор записей в базу данных
     *
     * @param array $fields массив названий полей
     * @param array $values массив массивов значений для вставки
     * @version 2010.05.04 by digi
     */
    function ReplaceList($fields, $values)
    {
        if ($this->_validate_insert_list($fields, $values))
        {
            $this->_before_insert();
        
            $this->table->ReplaceList($fields, $values);

            $this->_on_insert();
        }
    }

    /**
     * Обновляет значения записи, для которой значение индексного поля равно параметру $id
     *
     * @param integer $id значение индексного поля
     * @param array $values ассоциативный массив, ключами являются названия полей, значениями - соответствующие значения
     * @param boolean $ignore_unique_error игнорировать ошибки уникального ключа
     */
    function Update($id, $values, $ignore_unique_error = false)
    {
        $this->params = array('values' => $values, 'where' => array('conditions' => 'id=?', 'arguments' => $id));

        if ($this->_validate_update($values))
        {
            $this->_before_update();

            $this->table->Update($id, $values, $ignore_unique_error);

            $this->_on_update();
        }
        else
        {
            $id = 0;
        }

        return $id;     
    }


    /**
     * Обновляет значения записей
     *
     * @param mixed $params параметры для запроса
     * @param boolean $ignore_unique_error игнорировать ошибки уникального ключа
     */
    function UpdateList($params, $ignore_unique_error = false)
    {
        if ($this->_validate_update_list())
        {
            $this->_before_update();

            $result = $this->table->UpdateList($params, $ignore_unique_error);

            $this->_on_update();
        }

        return $result;
    }


    function ExecuteQuery($sql_text)
    {
        if (trim($sql_text) != '')
        {
            return $this->table->db->query($sql_text);
        }
    }

    /**
     * Вызывает хранимую процедуру
     *
     * @version 20111108, zharkov: если существет метод в модели sp, то вызывается он
     * 
     * @param string $name имя хранимой процедуры
     * @param array $values параметры для хранимой процедуры
     */
    function CallStoredProcedure($name, $values)
    {        
      require_once(APP_PATH . 'classes/models/sp.class.php');
        
		///print_r($name);
		//print_r($values);
		
      $sp = new Sp();
		//print_r('c');
      if (method_exists($sp, $name))
      {
			//print_r('a');
        return $sp->$name($values);
      }
      else
      {
			//print_r('b');		
        return $this->table->CallStoredProcedure($name, $values);
			}
    }

    /**
     * Возвращает результат выполнения хранимой функции
     *
     * @param string $name имя хранимой функции
     * @param array $values параметры для хранимой функции
     */
    function CallStoredFunction($name, $values)
    {
        return $this->table->CallStoredFunction($name, $values);
    }
    
    /**
     * Сбрасывает тег для указанного объекта
     *
     * @param integer $id идентификатор объекта
     */
    function ClearTagById($id)
    {
        if (!empty($this->object_alias)) Cache::ClearTag($this->object_alias . '-' . $id);
    }
    
    /**
     * Инкапсулирует изменение поля $table->connection_time_zone
     * 
     * @param mixed $time_zone
     */
    function SetConnectionTimeZone($time_zone)
    {
        if (!empty($time_zone)) $this->table->connection_time_zone = $time_zone;
    }

    /**
     * Входной массив преобразовывается в структурированный вид
     * 
     * Элементы, имеющие имя вида $data['_entity_field_name'] = 'value'
     * преобразуются в подмассив вида $data['entity']['field_name'] = 'value'
     *
     * @param array $data входной массив
     * @return array более структурированный массив
     */
    function _extract_entities($data)
    {
        for ($i = 0; $i < count($data); $i++)
        {
            foreach ($data[$i] as $key => $value)
            {
                $pos1 = strpos($key, '_');

                if ($pos1 !== false && $pos1 === 0)
                {
                    $pos2 = strpos($key, '_', 1);

                    if ($pos2 !== false)
                    {
                        $entity = substr($key, 1, $pos2 - 1);
                        $newkey = substr($key, $pos2 + 1);
                        if (!array_key_exists($entity, $data[$i])) $data[$i][$entity] = array();
                        $data[$i][$entity][$newkey] = $value;
                        unset($data[$i][$key]);
                    }
                }
            }
        }

        return $data;
    }
        
    /**
     * Возвращает данные из кеша
     * 
     * Если данных нет или данные устарели, пытается перестроить кеш, обратившись к указанной хранимой процедуре
     * Предварительно ключ блокируется
     *
     * @param string $hash ключ кеша
     * @param string $sp_name имя название хранимой процедуры
     * @param array $sp_params параметры, передаваемые хранимой процедуре
     * @param array $cache_tags теги ключа кеша
     * @param int $lifetime время жизни ключа
     */
    function _get_cached_data($hash, $sp_name, $sp_params, $cache_tags = array(), $lifetime = CACHE_LIFETIME_STANDARD)
    {

        $rowset = Cache::GetData($hash);
        //if ($sp_name == 'sp_post_get_list') dg($hash);
       
        // возврат актуальных существующих данных из кеша
	if (isset($rowset) && isset($rowset['data']) && !isset($rowset['outdated'])) {
            return $rowset['data'];
        }
         
        // попытка перестроения кеша
        $iamlocker = Cache::SetLock($hash);
		
	if ($iamlocker) {
            $rowset = $this->CallStoredProcedure($sp_name, $sp_params);
            Cache::SetData($hash, $rowset, $cache_tags, $lifetime);
            Cache::ClearLock($hash);		
            return $rowset;
        }

        $has_old_data = isset($rowset['data']);
        $counter = 0;
        do 
        {
            $counter++;
            
            // через 3 секунды возвращаются устаревшие данные, если они доступны
            if ($counter == 4 && $has_old_data)
            {
                return $rowset['data'];
            }
            
            // через 15 секунд ожидания возвращается пустой результат
            if ($counter == 16)
            {
                return null;
            }
            
            sleep(1);
            
        } while ($locked = (Cache::IsLocked($hash)));
        
        // если блокировка снята, получение данных из кеша
        if (!$locked)
        {
            $rowset = Cache::GetData($hash);
            return isset($rowset) && isset($rowset['data']) ? $rowset['data'] : null;
        }
       
        return null;
    }

    /**
     * Добавляет в набор данных объекта массив значений
     * 
     * @param mixed $recordset
     * @param mixed $id_fieldname
     * @param mixed $entityname
     * @param mixed $cache_prefix
     * @param mixed $sp_name
     * @param mixed $tags
     * @param mixed $sp_params
     * 
     * @version 20120630, zharkov
     */
    function _fill_entity_array_info($recordset, $id_fieldname, $entityname, $cache_prefix, $sp_name, $tags = null, $sp_params = null)
    {
        if (!isset($recordset) || empty($recordset) || !is_array($recordset)) return $recordset;

        $first_key = key($recordset);
        if (!isset($recordset[$first_key]) || !is_array($recordset[$first_key]))
        {
            return $recordset;
        }

        $entity_ids = array();
        foreach ($recordset as $key => $row)
        {
            if (isset($recordset[$key][$id_fieldname])) $entity_ids[] = $recordset[$key][$id_fieldname];
        }

        $list = $this->_get_entities_array_by_ids($cache_prefix, $sp_name, $entity_ids, $tags, $sp_params, $id_fieldname);

        foreach ($recordset as $key => $row)
        {
            if (isset($recordset[$key][$id_fieldname]) && isset($list[$recordset[$key][$id_fieldname]]) && !empty($list[$recordset[$key][$id_fieldname]]))
            {
                $recordset[$key][$entityname] = $this->_adjust_date($list[$recordset[$key][$id_fieldname]]);
            }
        }        

        return $recordset;
    }
    
    /**
     * Возвращает массив значений для объекта по идентификатору
     * 
     * @param mixed $cache_prefix
     * @param mixed $sp_name
     * @param mixed $ids
     * @param mixed $tags
     * @param mixed $sp_params
     * 
     * @version 20120630, zharkov
     */
    function _get_entities_array_by_ids($cache_prefix, $sp_name, $ids, $tags, $sp_params = null, $id_fieldname = 'id')
    {
        $result             = array();
        $ids_not_in_cache   = array();

        foreach ($ids as $id)
        {
            $object = Cache::GetData($cache_prefix . '-' . $id);

            if (!empty($object) && isset($object['data']) && empty($object['outdated']))
            {
                $result[$id] = $object['data'];
            }
            else
            {
                if (!in_array($id, $ids_not_in_cache) && $id > 0)   // 20101219, zharkov: добавил проверку $id > 0, чтобы исключить обращения к бд за данными с id=0
                {
                    $ids_not_in_cache[] = $id;
                }
            }
        }

        // получение недостающих данных из БД
        // для этого формируются строки вида 'id1, id2, id3, ...' (не более 100 идентификаторов при одном обращении к БД) GS: заменил на 50
        // полученные данные кладутся в кеш
        $ids_strs = array();

        $hundreds = -1;
        for ($i = 0; $i < count($ids_not_in_cache); $i++)
        {
            //if ($i <= 50)
            if ($i % 100 == 0)
            {
                $hundreds++;
                $ids_strs[$hundreds] = '';
            }
            
            $ids_strs[$hundreds] .= intval($ids_not_in_cache[$i]) . ',';
        }

        foreach ($ids_strs as $ids_str)
        {
            $params = array();
            $params[] = trim($ids_str, ',');

            if (!empty($sp_params))
            {
                if (is_array($sp_params))
                {
                    foreach ($sp_params as $param)
                    {
                        array_push($params, $param);
                    }
                }
                else
                {
                    array_push($params, $sp_params);
                }
            }
        
            $rowset = $this->CallStoredProcedure($sp_name, $params);

            if (isset($rowset[0]))
            {
                foreach ($rowset[0] as $row)
                {
                    if (isset($row[$id_fieldname]))
                    {
                        $id_fieldname_id = $row[$id_fieldname];
                        
                        if (!isset($result[$id_fieldname_id])) $result[$id_fieldname_id] = array();
                        
                        $result[$id_fieldname_id][] = $row;
                    }                    
                }
            }
        }

        // добавляет результаты в кеш
        foreach($result as $id => $row)
        {
            $cache_id = $cache_prefix . '-' . $id;
            
            $cachetags = array();
            $cachetags[] = $cache_id;
            if (!empty($tags))
            {
                foreach ($tags as $tagname => $fieldname)
                {
                    if (empty($fieldname))
                    {
                        $cachetags[] = $tagname;
                    }
                    else if (isset($row[$fieldname]))
                    {
                        $cachetags[] = $tagname . '-' . $row[$fieldname];
                    }
                }
            }
            //dg($cachetags);
            Cache::SetData($cache_id, $row, $cachetags, CACHE_LIFETIME_STANDARD);            
        }

        return $result;    
    }
    

    
    /**
     * Для каждой строки выборки на основании поля $id_fieldname получается информация о книге из справочника книг.
     * Информация о книге сохраняется в новом поле $entityname.
     *
     * @param array $recordset ассоциативный массив, выборка данных, включающая поле $id_fieldname
     * @param string $id_fieldname название поля, в котором хранятся идентификаторы сущности (например, 'book_id')
     * @param string $entityname название сущности = имя выходного массива для каждой строки входного (например, 'book')
     * @param string $cache_prefix префикс для выборки данных из кеша (например, 'book')
     * @param string $sp_name имя хранимой процедуры (например, 'sp_book_get_list_by_ids')
     * @param array tags массив устанавливаемых тегов в формате 'fieldname' => 'tagname' (например, {'book' => 'edition_id', 'author' => 'author_id'})
     * @param array $sp_params дополнительные параметры для передачи в хранимую процедуру
     * @return array набор данных
     * 
     * @version 20120815, zharkov: добавил проверку чтобы не добавлялся пустой массив при id = 0
     * @version 20120521, zharkov: изменил способ перебора массива
     */
    function _fill_entity_info($recordset, $id_fieldname, $entityname, $cache_prefix, $sp_name, $tags = null, $sp_params = null)
    {
        if (!isset($recordset) || empty($recordset) || !is_array($recordset)) return $recordset;
		
        $first_key = key($recordset);
        if (!isset($recordset[$first_key]) || !is_array($recordset[$first_key]))
        {
            return $recordset;
        }
		
        $entity_ids = array();
        foreach ($recordset as $key => $row)
        {
            if (isset($recordset[$key][$id_fieldname])) {
                $entity_ids[] = $recordset[$key][$id_fieldname];
            }
        }

        $list = $this->_get_entities_by_ids($cache_prefix, $sp_name, $entity_ids, $tags, $sp_params);

        foreach ($recordset as $key => $row)
        {
            // 20120815, zharkov: чтобы не добавлялся $entityname в массив при id = 0
            if (isset($recordset[$key][$id_fieldname]) && isset($list[$recordset[$key][$id_fieldname]]))
            {
                $recordset[$key][$entityname] = $this->_adjust_date($list[$recordset[$key][$id_fieldname]]);
            }
            //$recordset[$key][$entityname] = isset($recordset[$key][$id_fieldname]) && isset($list[$recordset[$key][$id_fieldname]]) ? $this->_adjust_date($list[$recordset[$key][$id_fieldname]]) : null;
        }        
	
        //if($sp_name == 'sp_steelitem_get_list_by_ids_arhiv') print_r($recordset);
        return $recordset;

        /* Старая версия до 20120521
        if (!isset($recordset) || !isset($recordset[0]) || !is_array($recordset[0]))
        {
            return $recordset;
        }

        $entity_ids = array();        
        for ($i = 0; $i < count($recordset); $i++)
        {
            if (isset($recordset[$i][$id_fieldname])) $entity_ids[] = $recordset[$i][$id_fieldname];
        }
        
        $list = $this->_get_entities_by_ids($cache_prefix, $sp_name, $entity_ids, $tags, $sp_params);

        for ($i = 0; $i < count($recordset); $i++)
        {            
            $recordset[$i][$entityname] = isset($recordset[$i][$id_fieldname]) && isset($list[$recordset[$i][$id_fieldname]]) ? $list[$recordset[$i][$id_fieldname]] : null;
        }

        return $recordset;        
        */        
    }
    

    /**
     * Возвращает список записей.
     * Пытается получить данные из кеша.
     * Обращается к указанной хранимой процедуре для получения недостающих данных.
     * 
     * @param array $ids массив идентификаторов
     * @param string $sp_name имя хранимой процедуры
     * @param array tags массив устанавливаемых тегов в формате 'fieldname' => 'tagname' (например, {'book' => 'edition_id', 'author' => 'author_id'})
     * @param array $sp_params параметры для хранимой процедуры, добавяются после ids в указанном порядке
     * @param string $cache_prefix префикс для выборки данных из кеша
     * @return array набор данных
     */     
    function _get_entities_by_ids($cache_prefix, $sp_name, $ids, $tags, $sp_params = null)
    {
        $result = array();
		
        //! реализовать мульти-выбор из кеша, Get($array)
        // получение данных из кеша
        $ids_not_in_cache = array();       

        foreach ($ids as $id)
        {
			
            $object = Cache::GetData($cache_prefix . '-' . $id);
            if (!empty($object) && !empty($object['data']) && empty($object['outdated']))
            {
                $result[$id] = $object['data'];
            }
            else
            {
                
                if (!in_array($id, $ids_not_in_cache) && $id > 0)
                {
		    //if ($sp_name == 'sp_steelitem_get_list_by_ids_arhiv') //dg('1');
                    $ids_not_in_cache[] = $id;
                }
            }
        }

        // получение недостающих данных из БД
        // для этого формируются строки вида 'id1, id2, id3, ...' (не более 100 идентификаторов при одном обращении к БД)
        // полученные данные кладутся в кеш
        $ids_strs = array();

        $hundreds = -1;
        for ($i = 0; $i < count($ids_not_in_cache); $i++)
        {
            if ($i % 100 == 0)
            
            {
                $hundreds++;
                $ids_strs[$hundreds] = '';
            }
            $ids_strs[$hundreds] .= intval($ids_not_in_cache[$i]) . ',';
        }
        
        foreach ($ids_strs as $ids_str)
        {
            $params = array();
            $params[] = trim($ids_str, ',');
        
            // 2010.07, zharkov: некорректно обрабатывался массив дополнительных параметров ХП
            // добавлялся как подмассив, чем вызывал ошибку в QueryBuilder->_prepare_argument($argument)
            //if (!empty($sp_params)) array_push($params, $sp_params);
            
            if (!empty($sp_params))
            {
                if (is_array($sp_params))
                {
                    foreach ($sp_params as $param)
                    {
                        array_push($params, $param);
                    }
                }
                else
                {
                    array_push($params, $sp_params);
                }
            }

            $rowset = $this->CallStoredProcedure($sp_name, $params);

            if (isset($rowset[0]))
            {
                foreach ($rowset[0] as $row)
                {
                    if (!isset($row['id'])) continue;
                    
                    $cache_id = $cache_prefix . '-' . $row['id'];
                    
                    $cachetags = array();
                    $cachetags[] = $cache_id;
                    if (!empty($tags))
                    {
                        foreach ($tags as $tagname => $fieldname)
                        {
                            if (empty($fieldname))
                            {
                                $cachetags[] = $tagname;
                            }
                            else if (isset($row[$fieldname]))
                            {
                                $cachetags[] = $tagname . '-' . $row[$fieldname];
                            }
                        }
                    }
                    
                    $row = $this->_adjust_row($row);
                    Cache::SetData($cache_id, $row, $cachetags, CACHE_LIFETIME_STANDARD);
                    $result[$row['id']] = $row;
                }
            }
        }

        return $result;    
    }
    
    /**
    * Производит манипуляции с элементами массива $row, может быть перегружена в моделях
    * 
    * @param mixed $row
    * @return array()
    */
    function _adjust_row($row)
    {        
        return $row;
    }
    
    /**
     * Сортирует дерево
     * Для сортировки используются поля id, parent_id
     *
     * @param array $data набор записей
     * @return отсортированный массив
     */
    function _sort_tree($data)
    {        
        if (empty($data)) return $data;

        $result = array();        
        $count  = count($data);        
        $exists = true;
        $minpid = PHP_INT_MAX;

        for ($i = 0; $i < $count; $i++)
        {
            if ($data[$i]['id'] < PHP_INT_MAX)
            {
                $minpid = $data[$i]['parent_id'];
                if ($minpid == 0) break;
            }
        }

        $pidstack = array($minpid);
        
        do
        {
            if (!count($pidstack)) break;
            
            $parent_id  = $pidstack[count($pidstack) - 1];
            $exists     = false;
            
            for ($i = 0; $i < $count; $i++)
            {
                if (!isset($data[$i]['handled']) && ($data[$i]['id'] == $parent_id || $data[$i]['parent_id'] == $parent_id))
                {
                    array_push($pidstack, $data[$i]['id']);
                    $result[]               = $data[$i];
                    $data[$i]['handled']    = true;
                    $exists                 = true;
                    break;
                }
            }

            if (!$exists) array_pop($pidstack);
            
        } while ($exists || count($pidstack));
        
        return $result;
    }    
    
    /**
     * Создает хэш наименования объекта для исключения добавления дублирующих значений.
     * Значения "(число)-(число)" сохраняются для обеспечения уникальности значений "1-2 days" и "12 days"
     * если это не исключено паттерном.
     * 
     * @param mixed $title
     * @param mixed $pattern
     * @return string
     */
    function _get_title_src($title, $pattern = '')
    {
        $pattern    = empty($pattern) ? "[^a-zA-Z0-9-]+" : $pattern;
        
        $title  = strtolower(Translit::Encode($title));
        $title  = preg_replace("#" . $pattern . "#us", '', $title);
        
        preg_match_all("#(\d+-\d+)+#us", $title, $matches);        
        if (!empty($matches))
        {
            foreach ($matches[0] AS $match)
            {
                $title = str_replace($match, str_replace('-', '&', $match), $title);
            }
        }
        
        return md5(str_replace('&', '-', str_replace('-', '', $title)));
    }
    
    /**
     * Обнуляет нулевые даты, потому что иногда приходят неадекватные даты вида 0000-00-00 00:00:00
     * которые сложно обрабатывать в шаблоне
     * 
     * @param mixed $row
     */
    function _adjust_date($row)
    {
        foreach (array('created_at', 'modified_at', 'deleted_at', 'birthday', 'deadline', 'done_at', 'alert_date') as $key)
        {
            if (isset($row[$key]) && ($row[$key] == '0000-00-00 00:00:00' || $row[$key] == '01.01.0001 0:00:00')) $row[$key] = '';
        }        

        return $row;
    }
    
    /**
     * Фабрика моделей
     * 
     * @param string $model_name название модели. Названия могут содержать заглавные буквы, имена файлов - только строчные
     * @return \model_name
     */
    public static function Factory($model_name)
    {
        require_once(APP_PATH . 'classes/models/' . strtolower($model_name) . '.class.php');

        return new $model_name();
    }
}