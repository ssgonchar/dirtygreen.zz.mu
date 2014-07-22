<?php

/**
 * Класс - построитель SQL запросов
 *
 * Синглтон
 *
 * @version
 * 2009.02.09 digi _prepare_group_clause
 * 2009.05.05 digi ignore
 * 2010.03.21 digi function
 * 2010.05.04 digi replace
 */
class QueryBuilder
{
    /**
     * Параметры для формирования запроса
     *
     * @var array
     */
    var $params;

    /**
     * Соединение с базой данных
     *
     * @var resource
     */
    var $db_connection;

    /**
     * Конструктор
     */
    protected function QueryBuilder($db_connection)
    {
        $this->db_connection = $db_connection;
    }

    /**
     * Фабричный метод
     *
     * Создаёт экземпляр класса
     * 
     * @return QueryBuilder
     */

    public static function Create($db_connection)
    {
        static $instance;
        if (!isset($instance))
        {
            $instance = new QueryBuilder($db_connection);
        }
        return $instance;
    }

    /**
     * Возвращает готовый SQL запрос
     *
     * В зависимости от типа запроса создаёт один из объектов, собственных наследников
     *
     * Пример массива входных параметров:
     * <code>
     *      array(
     *          'fields' => array(
     *              'locale_constants.*',
     *              'locale_constants.name',
     *              'locale_constants.description',
     *              'locale_constants.short_name as locale',
     *              'locale_constants.language'
     *          ),
     *          'join' => array(
     *              array(
     *                  'table' => 'constants',
     *                  'conditions' => 'constants.id = locale_constants.constant_id'
     *              ),
     *              array(
     *                  'table' => 'locales,
     *                  'conditions' => 'locales.id = locale_constants.locale_id'
     *              )
     *          ),
     *          'where' => array(
     *              'conditions' => 'constants.name like ?',
     *              'arguments' => array('c\_%')
     *          )
     *      )
     * </code>
     *
     * @param array $param ассоциативный массив параметров для формирования запроса.
     *   Содержит поля
     *     query string тип запроса, допустимы значения 'select' (по умолчанию), 'update', 'insert', 'delete', 'count'
     *     table string имя таблицы ('select', 'update', 'insert')
     *     procedure string имя хранимой процедуры ('call')
     *     ignore bool добавляет параметр IGNORE
     *     SQL_CALC_FOUND_ROWS тип select запроса. принимаемые значения true, false. по умолчанию false.
     *                         данный параметр необходим чтобы избежать повторного запроса 
     *                         для вычисления кол-ва результатов без LIMIT
     *     from array имена таблиц, поле может заменяться полем 'table' ('select', 'delete', 'count')
     *     fields array одномерный массив, поля для выборки ('select', 'count')
     *     values array ассоциативный массив, поля для вставки или обновления ('update', 'insert', 'call')
     *     join array массив, каждый элемент которого включает ассоциативный массив:
     *       'table' string имя присоединяемой таблицы
     *       'type' string тип происединения. Например, LEFT
     *       'conditions' string условия присоединения 
     *       ('select')
     *     where mixed массив, каждый элемент которого включает ассоциативный массив:
     *       'conditions' string логические условия, где в качестве параметров могут использоваться символы '?'
     *       'arguments' array значения параметров, представленных в 'conditions' вопросительными знаками
     *       ('select', 'update', 'delete', 'count')
     *       Количество вопросительных знаков в 'conditions' должно совпадать c количеством элементовв 'arguments'.
     *       Если 'arguments' не нужны, то поле 'where' может быть представлено обычной строкой.
     *     group array массив полей, по которым производится группировка ('select')
     *     order array массив полей, по которым производится сортировка ('select', 'update', 'delete')
     *     limit mixed определяет количество записей для выборки
     *       Может быть указано одно число - количество записей начиная с первой или 
     *       массив из двух чисел - номер записи, с которой начинать выборку и количество записей
     *       ('select', 'update', 'delete')
     *
     * @return string
     */
    function Prepare($params)
    {
        $this->params   = $params;
        
        $this->_check_query_param();
        
        switch ($this->params['query'])
        {
            case 'select': 
                $q = SelectQueryBuilder::Create($this->db_connection);
                break;

            case 'update': 
                $q = UpdateQueryBuilder::Create($this->db_connection);
                break;

            case 'insert': 
                $q = InsertQueryBuilder::Create($this->db_connection);
                break;

            case 'delete':
                $q = DeleteQueryBuilder::Create($this->db_connection);
                break;

            case 'count':
                $q = CountQueryBuilder::Create($this->db_connection);
                break;

            case 'call':
                $q = CallQueryBuilder::Create($this->db_connection);                
                break;

            case 'function':
                $q = FunctionQueryBuilder::Create($this->db_connection);
                break;

            case 'replace':
                $q = ReplaceQueryBuilder::Create($this->db_connection);
                break;

            default:
                $q = SelectQueryBuilder::Create($this->db_connection);
                break;
        }

        return $q->Prepare($this->params);
    }

    /**
     * Проверяет все параметры
     */
    function _check_params()
    {
        if ($this->params['query'] == 'call')
        {
            $this->_check_procedure_param();
        }
        else if ($this->params['query'] == 'function')
        {
            $this->_check_function_param();
        }
        else
        {
            $this->_check_table_param();
            $this->_check_from_param();
        }

        $this->_check_fields_param();
        $this->_check_values_param();
        $this->_check_join_param();
        $this->_check_where_param();
        $this->_check_having_param();
        $this->_check_group_param();
        $this->_check_order_param();
        $this->_check_limit_param();
    }

    /**
     * Проверяет параметр 'query'
     */
    function _check_query_param()
    {
        $clause = 'query';

        if (!is_array($this->params))
        {
            $this->params = array();
        }

        if (!array_key_exists($clause, $this->params))
        {
            $this->params[$clause] = 'select';
        }

        if (!in_array($this->params[$clause], array('select', 'update', 'insert', 'delete', 'count', 'call', 'function', 'replace')))
        {
            $this->params[$clause] = 'select';
        }
    }

    /**
     * Проверяет параметр 'fields'
     */
    function _check_fields_param()
    {
    }

    /**
     * Проверяет параметр 'table'
     */
    function _check_table_param()
    {
        $this->_assure_is_string('table');
        $this->_assure_is_not_empty('table');
    }

    /**
     * Проверяет параметр 'procedure'
     */
    function _check_procedure_param()
    {
        $this->_assure_is_string('procedure');
        $this->_assure_is_not_empty('procedure');
    }

    /**
     * Проверяет параметр 'function'
     */
    function _check_function_param()
    {
        $this->_assure_is_string('function');
        $this->_assure_is_not_empty('function');
    }

    /**
     * Проверяет параметр 'from'
     */
    function _check_from_param()
    {
        $this->_assure_is_array('from');

        if (!count($this->params['from']))
        {
            $this->params['from'] = array($this->params['table']);
        }
    }

    /**
     * Проверяет параметр 'where'
     */
    function _check_where_param()
    {
        $clause = 'where';

        $this->_assure_is_array($clause);

        if (array_key_exists('conditions', $this->params[$clause]))
        {
            $this->params[$clause] = array($this->params[$clause]);
        }
        
        $length = count($this->params[$clause]);
        for ($i = 0; $i < $length; $i++)
        {
            if (!is_array($this->params[$clause][$i]))
            {
                $this->params[$clause][$i] = array('conditions' => $this->params[$clause][$i]);
            }

            if (!array_key_exists('arguments', $this->params[$clause][$i]))
            {
                $this->params[$clause][$i]['arguments'] = array();
            }

            if (!is_array($this->params[$clause][$i]['arguments']))
            {
                $this->params[$clause][$i]['arguments'] = array($this->params[$clause][$i]['arguments']);
            }

            if (preg_match_all('(\?)', $this->params[$clause][$i]['conditions'], $res) != count($this->params[$clause][$i]['arguments']))
            {
                trigger_error('Wrong number of arguments for WHERE condition. Params: ' . var_export($this->params, true));
            }
        }
    }

    /**
     * Проверяет параметр 'having'
     */
    function _check_having_param()
    {
        $clause = 'having';

        $this->_assure_is_array($clause);

        if (array_key_exists('conditions', $this->params[$clause]))
        {
            $this->params[$clause] = array($this->params[$clause]);
        }
        
        $length = count($this->params[$clause]);
        for ($i = 0; $i < $length; $i++)
        {
            if (!is_array($this->params[$clause][$i]))
            {
                $this->params[$clause][$i] = array('conditions' => $this->params[$clause][$i]);
            }

            if (!array_key_exists('arguments', $this->params[$clause][$i]))
            {
                $this->params[$clause][$i]['arguments'] = array();
            }

            if (!is_array($this->params[$clause][$i]['arguments']))
            {
                $this->params[$clause][$i]['arguments'] = array($this->params[$clause][$i]['arguments']);
            }

            if (preg_match_all('(\?)', $this->params[$clause][$i]['conditions'], $res) != count($this->params[$clause][$i]['arguments']))
            {
                trigger_error('Wrong number of arguments for HAVING condition. Params: ' . var_export($this->params, true));
            }
        }
    }

    /**
     * Проверяет параметр 'join'
     */
    function _check_join_param()
    {
        $clause = 'join';

        $this->_assure_is_array($clause);

        if (array_key_exists('table', $this->params[$clause]))
        {
            $this->params[$clause] = array($this->params[$clause]);
        }
        
        $length = count($this->params[$clause]);
        for ($i = 0; $i < $length; $i++)
        {
            if (!array_key_exists('table', $this->params[$clause][$i]))
            {
                trigger_error('No table provided for JOIN clause. Params: ' . var_export($this->params, true));
            }

            if (is_array($this->params[$clause][$i]['table']))
            {
                trigger_error('Array passed for table parameter where string expected for JOIN clause. Params: ' . var_export($this->params, true));
            }

            if (!is_array($this->params[$clause][$i]))
            {
                $this->params[$clause][$i] = array('conditions' => $this->params[$clause][$i]);
            }

            if (!array_key_exists('type', $this->params[$clause][$i]))
            {
                $this->params[$clause][$i]['type'] = 'INNER';
            }

            if (!array_key_exists('arguments', $this->params[$clause][$i]))
            {
                $this->params[$clause][$i]['arguments'] = array();
            }

            if (!is_array($this->params[$clause][$i]['arguments']))
            {
                $this->params[$clause][$i]['arguments'] = array($this->params[$clause][$i]['arguments']);
            }

            if (preg_match_all('(\?)', $this->params[$clause][$i]['conditions'], $res) != count($this->params[$clause][$i]['arguments']))
            {
                trigger_error('Wrong number of arguments for JOIN condition. Params: ' . var_export($this->params, true));
            }
        }
    }

    /**
     * Проверяет параметр 'values'
     */
    function _check_values_param()
    {
        $this->_assure_is_array('values');
    }

    /**
     * Проверяет параметр 'order'
     */
    function _check_order_param()
    {
        $this->_assure_is_array('order');
    }

    /**
     * Проверяет параметр 'group'
     */
    function _check_group_param()
    {
        $this->_assure_is_array('group');
    }

    /**
     * Проверяет параметр 'limit'
     */
    function _check_limit_param()
    {
        $clause = 'limit';
        $lower = 'lower';
        $number = 'number';

        $this->_assure_is_array($clause);

//      if (!array_key_exists($clause, $this->params))
//      {
//          $this->params[$clause] = array();
//      }

        if (!array_key_exists($number, $this->params[$clause]))
        {
            if (count($this->params[$clause]) > 1)
            {
                if (array_key_exists(0, $this->params[$clause]))
                    $this->params[$clause][$lower] = intval($this->params[$clause][0]);
                else
                    $this->params[$clause][$lower] = 0;

                if (array_key_exists(1, $this->params[$clause]))
                    $this->params[$clause][$number] = intval($this->params[$clause][1]);
                else
                    $this->params[$clause][$number] = 0;
            }
            else if (count($this->params[$clause]) > 0)
            {
                $this->params[$clause][$number] = intval($this->params[$clause][0]);
            }
            else
            {
                $this->params[$clause][$number] = 0;
                $this->params[$clause][$lower] = 0;
            }
        }

        $this->params[$clause][$number] = intval($this->params[$clause][$number]);
        if ($this->params[$clause][$number] < 0)
            $this->params[$clause][$number] = 0;

        if (!array_key_exists($lower, $this->params[$clause]))
        {
            $this->params[$clause][$lower] = 0;
        }
        $this->params[$clause][$lower] = intval($this->params[$clause][$lower]);
        if ($this->params[$clause][$lower] < 0)
            $this->params[$clause][$lower] = 0;

        $this->params[$clause] = array(
            $lower => $this->params[$clause][$lower],
            $number => $this->params[$clause][$number]);
    }

    /**
     * Проверяет, является ли входной параметр строкой
     *
     * Если не является, то генерируется исключение
     *
     * @param string $param проверяемый параметр
     */
    function _assure_is_string($param)
    {
        if (!array_key_exists($param, $this->params))
        {
            $this->params[$param] = '';
        }

        if (is_array($this->params[$param]))
        {
            trigger_error("Array passed for '$param' parameter where string expected. Params: '" . var_export($this->params, true));
        }
    }

    /**
     * Проверяет, является ли входной параметр непустым
     *
     * Если входной параметр строка, то проверяется, что длина строки ненулевая;
     * если входной параметр массив, то проверяется, что массив содержит элементы                                                                            
     *
     * В случае несоответствия генерируется исключение
     *
     * @param mixed $param проверяемый параметр
     */
    function _assure_is_not_empty($param)
    {
        if (is_string($this->params[$param]))
        {
            if ($this->params[$param] == '')
            {
                trigger_error("Value passed for '$param' parameter is empty. Params: " . var_export($this->params, true));
            }
        }
        else if (is_array($this->params[$param]))
        {
            if (count($this->params[$param]) == 0)
            {
                trigger_error("Array passed for '$param' parameter is empty. Params: " . var_export($this->params, true));
            }
        }
    }

    /**
     * Проверяет, является ли входной параметр массивом
     *
     * Если не является, то создаётся новый массив, 
     * входной параметр помещается в него единственным элементом,
     * и замещается новым массивом
     *
     * @param mixed $param проверяемый параметр
     */
    function _assure_is_array($param)
    {
        if (!array_key_exists($param, $this->params))
        {
            $this->params[$param] = array();
        }

        if (!is_array($this->params[$param]))
        {
            $this->params[$param] = array($this->params[$param]);
        }
    }

    /**
     * В условиях замещает символы '?' на соответствующие аргументы
     *
     * @param array $value ассоциативный массив, включающий строку 'conditions' и массив аргументов 'arguments'
     * @return string
     */
    function _parse_condition($value)
    {
        $result = '';

        $explode = explode('?', $value['conditions']);
        for ($i = 0; $i < count($explode); $i++)
        {
            $result .= $explode[$i];
            if ($i < count($explode) - 1)
            {
                $result .= $this->_prepare_argument($value['arguments'][$i]);
            }
        }
        
        return $result;
    }

    /**
     * Подготавливает аргумент для SQL запроса
     *
     * @param string $value аргумент
     * @return string
     */
    function _prepare_argument($argument)
    {
        if ($argument === 'NULL VALUE!')
        {
            return 'NULL';
        }
        
        if ($argument === 'NOW()!')
        {
            return 'NOW()';
        }
        
        return '\'' . mysqli_real_escape_string($this->db_connection, $argument) . '\'';
    }
    
    /**
     * Подготавливает параметр 'fields'
     */
    function _prepare_fields_clause()
    {
        $query = '';

        $length = count($this->params['fields']);
        for ($i = 0; $i < $length; $i++)
        {
            $query .= $this->params['fields'][$i];

            if ($i < $length - 1)
            {
                $query .= ', ';
            }
            else
            {
                $query .= ' ';
            }
        }

//        Log::AddLine(LOG_CUSTOM, 'QueryBuilder::_prepare_fields_clause ' . $query);

        return $query;
    }

    /**
     * Подготавливает параметр 'table'
     */
    function _prepare_table_clause()
    {
        //Log::AddLine(LOG_CUSTOM, 'QueryBuilder::_prepare_table_clause ' . $this->params['table']);

        return $this->params['table'] . ' ';
    }

    /**
     * Подготавливает параметр 'procedure'
     */
    function _prepare_procedure_clause()
    {
        return $this->params['procedure'] . ' ';
    }

    /**
     * Подготавливает параметр 'function'
     */
    function _prepare_function_clause()
    {
        return $this->params['function'] . ' ';
    }

    /**
     * Подготавливает параметр 'from'
     */
    function _prepare_from_clause()
    {
        $query = '';

        if (count($this->params['from']))
        {
            $query .= 'FROM ';

            $length = count($this->params['from']);
            for ($i = 0; $i < $length; $i++)
            {
                $query .= $this->params['from'][$i];
    
                if ($i < $length - 1)
                {
                    $query .= ', ';
                }
                else
                {
                    $query .= ' ';
                }
            }           
        }

        return $query;
    }

    /**
     * Подготавливает параметр 'values'
     */
    function _prepare_values_clause()
    {
        $query = '';
        $clause = 'values';

        if (count($this->params[$clause]))
        {
            $query .= 'SET ';

            $flag = false;
            foreach ($this->params[$clause] as $key => $value)
            {
                if ($flag)
                {
                    $query .= ', ';
                }

                $query .= '`' . $key . '`' . ' = ' . $this->_prepare_argument($value);

                $flag = true;
            }

            $query .= ' ';
        }

        return $query;
    }

    /**
     * Подготавливает параметр 'where'
     */
    function _prepare_where_clause()
    {
        $query = '';

        if (count($this->params['where']))
        {
            $query .= 'WHERE ';

            $flag = false;
            foreach ($this->params['where'] as $value)
            {
                if ($flag)
                {
                    $query .= ' AND ';
                }

                $query .= '(' . $this->_parse_condition($value) . ')';

                $flag = true;
            }

            $query .= ' ';
        }

        return $query;
    }

    /**
     * Подготавливает параметр 'having'
     */
    function _prepare_having_clause()
    {
        $query = '';

        if (count($this->params['having']))
        {
            $query .= 'HAVING ';

            $flag = false;
            foreach ($this->params['having'] as $value)
            {
                if ($flag)
                {
                    $query .= ' AND ';
                }

                $query .= '(' . $this->_parse_condition($value) . ')';

                $flag = true;
            }

            $query .= ' ';
        }

        return $query;
    }
    
    /**
     * Подготавливает параметр 'join'
     */
    function _prepare_join_clause()
    {
        $query = '';

        if (count($this->params['join']))
        {
            foreach ($this->params['join'] as $value)
            {
                $query .= $value['type'] . ' JOIN ' . $value['table'] . ' ON (' . $this->_parse_condition($value) . ') ';
            }
        }

        return $query;
    }
    
    /**
     * Подготавливает параметр 'order'
     */
    function _prepare_order_clause()
    {
        $query = '';

        if (count($this->params['order']))
        {
            $query .= 'ORDER BY ';

            $length = count($this->params['order']);
            for ($i = 0; $i < $length; $i++)
            {
                $query .= $this->params['order'][$i];
    
                if ($i < $length - 1)
                {
                    $query .= ', ';
                }
                else
                {
                    $query .= ' ';
                }
            }
        }

        return $query;
    }

    /**
     * Подготавливает параметр 'group'
     */
    function _prepare_group_clause()
    {
        $query = '';

        if (count($this->params['group']) && !empty($this->params['group'][0]))
        {
            $query .= 'GROUP BY ';

            $length = count($this->params['group']);
            for ($i = 0; $i < $length; $i++)
            {
                $query .= $this->params['group'][$i];
    
                if ($i < $length - 1)
                {
                    $query .= ', ';
                }
                else
                {
                    $query .= ' ';
                }
            }
        }

        return $query;
    }

    /**
     * Подготавливает параметр 'limit'
     */
    function _prepare_limit_clause()
    {
        $query = '';

        if (count($this->params['limit']))
        {
            $lower = $this->params['limit']['lower'];
            $number = $this->params['limit']['number'];

            if ($lower > 0 || $number > 0)
            {
                $query .= 'LIMIT ';
                if ($lower == 0)
                {
                    $query .= $number;
                }
                else
                {
                    $query .= "$lower, $number";
                }
            }
        }
    
        return $query;
    }
}

/**
 * Класс - построитель SQL запросов типа 'select'
 *
 * Синглтон
 */
class SelectQueryBuilder extends QueryBuilder
{
    /**
     * Фабричный метод
     *
     * Создаёт экземпляр класса
     * 
     * @return SelectQueryBuilder
     */
    public static function Create($db_connection)
    {
        static $instance;
        if (!isset($instance))
        {
            $instance = new SelectQueryBuilder($db_connection);
        }
        return $instance;
    }

    /**
     * Подготавливает параметры для формирования SQL запроса
     */
    function Prepare($params)
    {
        $this->params = $params;

        $this->_check_params();

//echo '<pre>';
//print_r($this->params);

        $query = 'SELECT ';
        
        // Murin: так надо параметр дополнительный сделать SQL_CALC_FOUND_ROWS см хелп по мускулю. 
        // сие позволяет избежать второго запроса для того чтобы узнать кол-во результатов без LIMIT
        //mysql> SELECT SQL_CALC_FOUND_ROWS * FROM tbl_name
        //      -> WHERE id > 100 LIMIT 10;
        //mysql> SELECT FOUND_ROWS();
        if (isset($params['SQL_CALC_FOUND_ROWS']) && $params['SQL_CALC_FOUND_ROWS'])
        {
            $query .= ' SQL_CALC_FOUND_ROWS ';
        }

        $query .= $this->_prepare_fields_clause();
        $query .= $this->_prepare_from_clause();
        $query .= $this->_prepare_join_clause();
        $query .= $this->_prepare_where_clause();
        $query .= $this->_prepare_group_clause();
        $query .= $this->_prepare_having_clause();
        $query .= $this->_prepare_order_clause();
        $query .= $this->_prepare_limit_clause();

        return $query;
    }

    /**
     * Проверяет параметр 'fields'
     */
    function _check_fields_param()
    {
        $this->_assure_is_array('fields');

        if (!count($this->params['fields']))
        {
            $this->params['fields'][] = ($this->params['table'] != '' ? $this->params['table'] . '.' : '') . '*';
        }
    }
}

/**
 * Класс - построитель SQL запросов типа 'select'
 *
 * Синглтон
 */
class UpdateQueryBuilder extends QueryBuilder
{
    /**
     * Фабричный метод
     *
     * Создаёт экземпляр класса
     * 
     * @return QueryBuilder
     */
    public static function Create($db_connection)
    {
        static $instance;
        if (!isset($instance))
        {
            $instance = new UpdateQueryBuilder($db_connection);
        }
        return $instance;
    }

    /**
     * Подготавливает параметры для формирования SQL запроса
     */
    function Prepare($params)
    {
        $this->params = $params;

        $this->_check_params();

//echo '<pre>';
//print_r($this->params);

        $query = 'UPDATE ';

        // 2009.05.06 digi, не выдаёт ошибок при попытке вставки дубликата
        if (!empty($params['ignore']))
        {
            $query .= 'IGNORE ';
        }

        $query .= $this->_prepare_table_clause();
        $query .= $this->_prepare_values_clause();
        $query .= $this->_prepare_where_clause();
        $query .= $this->_prepare_order_clause();
        $query .= $this->_prepare_limit_clause();

        return $query;
    }
}

/**
 * Класс - построитель SQL запросов типа 'insert'
 *
 * Синглтон
 */
class InsertQueryBuilder extends QueryBuilder
{
    var $type;
    /**
     * Фабричный метод
     *
     * Создаёт экземпляр класса
     * 
     * @return QueryBuilder
     */
    public static function Create($db_connection)
    {
        static $instance;
        if (!isset($instance))
        {
            $instance = new InsertQueryBuilder($db_connection);
        }
        return $instance;
    }

    /**
     * Подготавливает параметры для формирования SQL запроса
     */
    function Prepare($params)
    {
        $this->params = $params;

        $this->_check_params();

//echo '<pre>';
//print_r($this->params);
        
        $fields = $this->_prepare_fields_clause();

        $query = 'INSERT ';

        // 2009.05.06 digi, не выдаёт ошибок при попытке вставки дубликата
        if (!empty($params['ignore']))
        {
            $query .= 'IGNORE ';
        }

        if ($fields == '()') // single record. INSERT {table name} SET {field}={value},...
        {
            $this->type = 'single';

            $query .= $this->_prepare_table_clause();
            $values = $this->_prepare_values_clause();
            $query .= $values != '' ? $values : 'VALUES()';        
        }
        else // multiple records. INSERT {table name} ({field list}) VALUES ({value list}), ...
        {
            $this->type = 'multiple';

            $query .= $this->_prepare_table_clause();
            $query .= $this->_prepare_fields_clause();
            $query .= $this->_prepare_values_clause();
        }       

        return $query;
    }

    /**
     * Проверяет параметр 'fields'
     */
    function _check_fields_param()
    {
        $this->_assure_is_array('fields');
    }

    /**
     * Подготавливает параметр 'fields'
     */
    function _prepare_fields_clause()
    {
        $query = '(';

        $length = count($this->params['fields']);
        for ($i = 0; $i < $length; $i++)
        {
            $query .= '`' . $this->params['fields'][$i] . '`';

            if ($i < $length - 1)
            {
                $query .= ', ';
            }
            else
            {
                $query .= ' ';
            }
        }

        $query .= ')';

        return $query;
    }


    /**
     * Подготавливает параметр 'values'
     */
    function _prepare_values_clause()
    {
        if ($this->type == 'single')
        {
            return parent::_prepare_values_clause();
        }
        else if ($this->type == 'multiple')
        {
            return $this->_prepare_values_multiple_clause();
        }
    }

    /**
     * Подготавливает параметр 'values' для списка вставляемых записей
     */
    function _prepare_values_multiple_clause()
    {
        $query = '';
        $clause = 'values';

        if (count($this->params[$clause]))
        {
            $query .= ' VALUES ';

            for ($i = 0; $i < count($this->params[$clause]); $i++)
            {            
                $values = $this->params[$clause][$i];

                if ($i > 0)
                {
                    $query .= ',';
                }

                $query .= '(';

                $flag = false;
                foreach ($values as $value)
                {
                    if ($flag)
                    {
                        $query .= ',';
                    }

                    $query .= $this->_prepare_argument($value);

                    $flag = true;
                }

                $query .= ')';
            }
        }

        //Log::AddLine(LOG_CUSTOM, 'QueryBuilder::_prepare_values_multiple ' . $query);

        return $query;
    }
}

/**
 * Класс - построитель SQL запросов типа 'replace'
 *
 * Синглтон
 */
class ReplaceQueryBuilder extends QueryBuilder
{
    var $type;
    /**
     * Фабричный метод
     *
     * Создаёт экземпляр класса
     * 
     * @return QueryBuilder
     */
    public static function Create($db_connection)
    {
        static $instance;
        if (!isset($instance))
        {
            $instance = new ReplaceQueryBuilder($db_connection);
        }
        return $instance;
    }

    /**
     * Подготавливает параметры для формирования SQL запроса
     */
    function Prepare($params)
    {
        $this->params = $params;

        $this->_check_params();

//echo '<pre>';
//print_r($this->params);
        
        $fields = $this->_prepare_fields_clause();

        $query = 'REPLACE ';

        if ($fields == '()') // single record. INSERT {table name} SET {field}={value},...
        {
            $this->type = 'single';

            $query .= $this->_prepare_table_clause();
            $values = $this->_prepare_values_clause();
            $query .= $values != '' ? $values : 'VALUES()';        
        }
        else // multiple records. INSERT {table name} ({field list}) VALUES ({value list}), ...
        {
            $this->type = 'multiple';

            $query .= $this->_prepare_table_clause();
            $query .= $this->_prepare_fields_clause();
            $query .= $this->_prepare_values_clause();
        }       

        return $query;
    }

    /**
     * Проверяет параметр 'fields'
     */
    function _check_fields_param()
    {
        $this->_assure_is_array('fields');
    }

    /**
     * Подготавливает параметр 'fields'
     */
    function _prepare_fields_clause()
    {
        $query = '(';

        $length = count($this->params['fields']);
        for ($i = 0; $i < $length; $i++)
        {
            $query .= '`' . $this->params['fields'][$i] . '`';

            if ($i < $length - 1)
            {
                $query .= ', ';
            }
            else
            {
                $query .= ' ';
            }
        }

        $query .= ')';

        return $query;
    }


    /**
     * Подготавливает параметр 'values'
     */
    function _prepare_values_clause()
    {
        if ($this->type == 'single')
        {
            return parent::_prepare_values_clause();
        }
        else if ($this->type == 'multiple')
        {
            return $this->_prepare_values_multiple_clause();
        }
    }

    /**
     * Подготавливает параметр 'values' для списка вставляемых записей
     */
    function _prepare_values_multiple_clause()
    {
        $query = '';
        $clause = 'values';

        if (count($this->params[$clause]))
        {
            $query .= ' VALUES ';

            for ($i = 0; $i < count($this->params[$clause]); $i++)
            {            
                $values = $this->params[$clause][$i];

                if ($i > 0)
                {
                    $query .= ',';
                }

                $query .= '(';

                $flag = false;
                foreach ($values as $value)
                {
                    if ($flag)
                    {
                        $query .= ',';
                    }

                    $query .= $this->_prepare_argument($value);

                    $flag = true;
                }

                $query .= ')';
            }
        }

        //Log::AddLine(LOG_CUSTOM, 'QueryBuilder::_prepare_values_multiple ' . $query);

        return $query;
    }
}

/**
 * Класс - построитель SQL запросов типа 'select'
 *
 * Синглтон
 */
class DeleteQueryBuilder extends QueryBuilder
{
    /**
     * Фабричный метод
     *
     * Создаёт экземпляр класса
     * 
     * @return QueryBuilder
     */
    public static function Create($db_connection)
    {
        static $instance;
        if (!isset($instance))
        {
            $instance = new DeleteQueryBuilder($db_connection);
        }
        return $instance;
    }

    /**
     * Подготавливает параметры для формирования SQL запроса
     */
    function Prepare($params)
    {
        $this->params = $params;

        $this->_check_params();

//echo '<pre>';
//print_r($this->params);

        $query = 'DELETE ';

        $query .= $this->_prepare_from_clause();
        $query .= $this->_prepare_where_clause();
        $query .= $this->_prepare_order_clause();
        $query .= $this->_prepare_limit_clause();

        return $query;
    }
}

/**
 * Класс - построитель SQL запросов типа 'select'
 *
 * Синглтон
 */
class CountQueryBuilder extends QueryBuilder
{
    /**
     * Фабричный метод
     *
     * Создаёт экземпляр класса
     * 
     * @return QueryBuilder
     */
    public static function Create($db_connection)
    {
        static $instance;
        if (!isset($instance))
        {
            $instance = new CountQueryBuilder($db_connection);
        }
        return $instance;
    }

    /**
     * Подготавливает параметры для формирования SQL запроса
     */
    function Prepare($params)
    {
        $this->params = $params;

        $this->_check_params();

//echo '<pre>';
//print_r($this->params);

        $query = 'SELECT ';

        $query .= $this->_prepare_fields_clause();
        $query .= $this->_prepare_from_clause();
        $query .= $this->_prepare_where_clause();

        return $query;
    }

    /**
     * Проверяет параметр 'fields'
     */
    function _check_fields_param()
    {
        $this->params['fields'] = array('count(*) as rows');
    }
}

/**
 * Класс - построитель SQL запросов типа 'call'
 *
 * Синглтон
 */
class CallQueryBuilder extends QueryBuilder
{
    /**
     * Фабричный метод
     *
     * Создаёт экземпляр класса 
     * 
     * @return QueryBuilder
     */
    public static function Create($db_connection)
    {
        static $instance;
        if (!isset($instance))
        {
            $instance = new CallQueryBuilder($db_connection);
        }
        return $instance;
    }

    /**
     * Подготавливает параметры для формирования SQL запроса
     */
    function Prepare($params)
    {
        $this->params = $params;

        $this->_check_params();

//echo '<pre>';
//print_r($this->params);

        $query = 'CALL ';

        $query .= $this->_prepare_procedure_clause();
        $query .= $this->_prepare_values_clause();

        return $query;
    }

    /**
     * Подготавливает параметр 'values' как параметров хранимой процедуры
     */
    function _prepare_values_clause()
    {
        $query = '(';
        $clause = 'values';

        if (count($this->params[$clause]))
        {
            for ($i = 0; $i < count($this->params[$clause]); $i++)
            {            
                if ($i > 0)
                {
                    $query .= ',';
                }

                $query .= $this->_prepare_argument($this->params[$clause][$i]);
            }
        }

        $query .= ')';

        return $query;
    }
}

/**
 * Класс - построитель SQL запросов типа 'select sf_name'
 *
 * Синглтон
 */
class FunctionQueryBuilder extends QueryBuilder
{
    /**
     * Фабричный метод
     *
     * Создаёт экземпляр класса 
     * 
     * @return QueryBuilder
     */
    public static function Create($db_connection)
    {
        static $instance;
        if (!isset($instance))
        {
            $instance = new FunctionQueryBuilder($db_connection);
        }
        return $instance;
    }

    /**
     * Подготавливает параметры для формирования SQL запроса
     */
    function Prepare($params)
    {
        $this->params = $params;

        $this->_check_params();

//echo '<pre>';
//print_r($this->params);

        $query = 'SELECT ';

        $query .= $this->_prepare_function_clause();
        $query .= $this->_prepare_values_clause();

        $query .= ' AS result';

        return $query;
    }

    /**
     * Подготавливает параметр 'values' как параметров хранимой процедуры
     */
    function _prepare_values_clause()
    {
        $query = '(';
        $clause = 'values';

        if (count($this->params[$clause]))
        {
            for ($i = 0; $i < count($this->params[$clause]); $i++)
            {            
                if ($i > 0)
                {
                    $query .= ',';
                }

                $query .= $this->_prepare_argument($this->params[$clause][$i]);
            }
        }

        $query .= ')';

        return $query;
    }
}
