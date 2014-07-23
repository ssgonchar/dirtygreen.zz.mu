<?php
    // время жизни ключа кеша
	
    define('CACHE_LIFETIME_TAG', 1209600);      //14d
    define('CACHE_LIFETIME_LONG', 86400);       //1d
    define('CACHE_LIFETIME_STANDARD', 10800);   //3h
    define('CACHE_LIFETIME_ONLINE', 600);       //10m
    define('CACHE_LIFETIME_SHORT', 300);        //5m
    define('CACHE_LIFETIME_30S', 30);            //30s
    define('CACHE_LIFETIME_MIN', 60);			//1m
    define('CACHE_LIFETIME_LOCK', 10);          //10s
	
	/*
    define('CACHE_LIFETIME_TAG', 1);      //14d
    define('CACHE_LIFETIME_LONG', 1);       //1d
    define('CACHE_LIFETIME_STANDARD', 1);   //3h
    define('CACHE_LIFETIME_ONLINE', 1);       //10m
    define('CACHE_LIFETIME_SHORT', 1);        //5m
	define('CACHE_LIFETIME_30S', 1);            //30s
    define('CACHE_LIFETIME_MIN', 1);			//1m
    define('CACHE_LIFETIME_LOCK', 1);          //10s	
    */
    define('CACHE_TAG_PREFIX', 'ct-');
    define('CACHE_LOCK_PREFIX', 'l-');
    
    define('CACHE_LOG', 'no');
    
/**
 * Класс для кеширования, обёртка для memcache
 *
 * Синглтон
 * 
 * @version 2010.11.01, zharkov: поставил заглушки в публичных методах
 */
class Cache
{
    var $connection;

    /**
     * Конструктор
     *
     * @param array $connection_settings параметры подключения к memcached
     * @see DatabaseConnection::$db
     */
    function Cache($connection_settings)
    {
        if (CACHE_ENABLED == 'no') return; // 2010.11.01, zharkov: заглушка
        $this->_connect($connection_settings);
    }

    /**
     * Пробует установить соединение с сервисом memcache
     *
     * @param array $connection_settings параметры подключения к memcache
     * Массив, каждый элемент которого представляет собой ассоциативный массив:
     *  host: сервер
     *  port: порт
     */
    function _connect($connection_settings)
    {
        $this->connection = new Memcache();
        $is_connected = @$this->connection->connect($connection_settings['host'], $connection_settings['port']);
        
        if (!$is_connected) $this->connection = null;

        return $is_connected;
        
        //if (empty($this->connection))
        //{
        //    return false;
        //    _503('Can\'t connect memcache');
        //}
    }

    /**
     * Фабричный метод
     *
     * Создаёт экземпляр класса
     * 
     * @param array $connection_settings параметры подключения к memcache
     * @see Cache::_connect()
     * @return Cache 
     */
    public static function & Create()
    {
        static $instance;
        if (!isset($instance))
        {
            $instance = new Cache(array('host' => MEMCACHE_HOST, 'port' => MEMCACHE_PORT));
        }

        return $instance;
    }

    /**
     * Сохраняет данные в кеш
     *
     * $key - ключ
     * $value - данные
     */
    public static function SetData($key, $value, $tag_names = array(), $lifetime = CACHE_LIFETIME_STANDARD)
    {
        if (CACHE_ENABLED == 'no') return false; // 2010.11.01, zharkov: заглушка
        $cache = Cache::Create();
        return $cache->_set_data($key, $value, $tag_names, $lifetime);
    }

    function _set_data($key, $value, $tag_names, $lifetime)
    {
        $values = array();
        $values['data'] = $value;
        
        if (!empty($tag_names) && is_array($tag_names))
        {
            $tag_values = $this->connection->get($tag_names);
            
            $tags = array();
            foreach ($tag_names as $tag)
            {
                $tag = CACHE_TAG_PREFIX . $tag;
                
                if (isset($tag_values[$tag]))
                {
                    $tags[$tag] = $tag_values[$tag];
                }
                else
                {
                    $time = $this->_get_key($tag);
                    if (empty($time))
                    {
                        $time = time();
                        $this->_set_key($tag, $time, CACHE_LIFETIME_TAG);
                    }
                    
                    $tags[$tag] = $time;                    
                }
            }
            
            $values['tags'] = $tags;
        }
        
        if (CACHE_LOG == 'yes') Log::AddLine(LOG_CACHE, "set: \t" . var_export($key, true) . "\n" . var_export($values, true));        
        
        return $this->_set_key($key, $values, $lifetime);
    }
    
    /**
     * Устанавливает блокировку ключу
     *
     * $key ключ блокировки
     * $lifetime время жизни блокировки
     */
    public static function SetLock($key, $lifetime = CACHE_LIFETIME_LOCK)
    {
	//print_r($key);
        if (CACHE_ENABLED == 'no') return true; // 2010.11.01, zharkov: заглушка
        
        $cache = Cache::Create();
        return $cache->_add_key(CACHE_LOCK_PREFIX . $key, true, $lifetime);
    }
    
    /**
     * Добавляет ключ в кеш
     * Возвращает true в случае успеха
     * Если ключ был установлен ранее, возвращает false
     */
    function _add_key($key, $value, $lifetime)
    {
        if (CACHE_PREFIX !== '') $key = CACHE_PREFIX . $key;
        
        if (empty($this->connection)) return false;

        //if (!defined('ENABLE_CACHE') OR ENABLE_CACHE != 'yes') return false;
        if (CACHE_LOG == 'yes') Log::AddLine(LOG_CACHE, "add: \t" . $key . ':' . $lifetime);
        
        return $this->connection->add($key, $value, false, $lifetime);
    }
    
    /**
     * Сохраняет ключ в кеш
     */
    function _set_key($key, $value, $lifetime)
    {
        if (CACHE_PREFIX !== '') $key = CACHE_PREFIX . $key;
        
        if (empty($this->connection)) return false;

        //if (!defined('ENABLE_CACHE') OR ENABLE_CACHE != 'yes') return false;
        if (CACHE_LOG == 'yes') Log::AddLine(LOG_CACHE, "set: \t" . $key . ':' . $lifetime);
        
        return $this->connection->set($key, $value, false, $lifetime);
    }
    
    /**
     * Возвращает данные из кеша
     *
     * $key - ключ
     */
    public static function GetData($key)
    {
        if (CACHE_ENABLED == 'no') return null; // 2010.11.01, zharkov: заглушка
        
        $cache = Cache::Create();
        return $cache->_get_data($key);
    }
    
    /**
     * Устанавливает, заблокирован ли ключ
     *
     * $key - ключ
     */
    public static function IsLocked($key)
    {
        if (CACHE_ENABLED == 'no') return false; // 2010.11.01, zharkov: заглушка
        
        $cache = Cache::Create();
        return $cache->_get_key(CACHE_LOCK_PREFIX . $key);
    }
    
    /**
     * Возвращает данные из кеша.
     *
     * Проверяет связанные теги. Очищает устаревшие данные.
     */
    function _get_data($key)
    {
        $result = $this->_get_key($key);
        
        if (empty($result)) return null;
        
        $rowset = array();
        $rowset['data'] = isset($result['data']) ? $result['data'] : null;

        if (isset($result['tags']))
        {
            $cachetags = $result['tags'];
            $tags = $this->_get_key(array_keys($cachetags));

            $outdated = false;
            foreach ($cachetags as $tag => $value)
            {
                $tag = CACHE_PREFIX !== '' ? CACHE_PREFIX . $tag : $tag;
                if (!isset($tags[$tag]) || $tags[$tag] != $value)
                {
                    $outdated = true;
                    break;
                }
            }
            
            if ($outdated)
            {
                $rowset['outdated'] = true;
                //$this->_clear_key($key);
                //$result = null;
            }
        }
        
        if (CACHE_LOG == 'yes') Log::AddLine(LOG_CACHE, "get: \t" . var_export($key, true) . "\n" . var_export($result, true));

        return $rowset;
    }
    
    /**
     * Возвращает из кеша данные для указанного ключа
     */
    function _get_key($key)
    {
		/*$this->ClearKey($key);*/ 
        if (CACHE_PREFIX !== '')
        {
            if (!is_array($key))
            {
                $key = CACHE_PREFIX . $key;
            }
            else
            {
                for ($i = 0; $i < count($key); $i++)
                {
                    $key[$i] = CACHE_PREFIX . $key[$i];                    
                }
            }
        }

        if (empty($this->connection)) return false;
        
        //if (!defined('ENABLE_CACHE') OR ENABLE_CACHE != 'yes') return false;
        
        $result = $this->connection->get($key);
        
        if (CACHE_LOG == 'yes')
        if (is_array($key))
        {
            foreach ($key as $k)
            {
                if (!isset($result[$k]))    Log::AddLine(LOG_CACHE, "miss k: \t" . var_export($k, true));
                else                        Log::AddLine(LOG_CACHE, "hit k: \t" . var_export($k, true));
            }
        }
        else
        {
            if (!isset($result) || $result === false)    Log::AddLine(LOG_CACHE, "miss: \t" . var_export($key, true));
            else                                         Log::AddLine(LOG_CACHE, "hit: \t" . var_export($key, true));
        }
        
        
        return $result;
    }
    
    /**
     * Очищает указанный ключ кеша
     *
     * $key - ключ
     */
    public static function ClearKey($key)
    {        
        if (CACHE_ENABLED == 'no') return false; // 2010.11.01, zharkov: заглушка
        
        $cache = Cache::Create();
        return $cache->_clear_key($key);
    }
    
    /**
     * Устанавливает указанный ключ кеша
     *
     * $key - ключ
     */
    public static function SetKey($key, $value, $lifetime = CACHE_LIFETIME_STANDARD)
    {        
        if (CACHE_ENABLED == 'no') return false; // 2010.11.01, zharkov: заглушка
        
        $cache = Cache::Create();
        return $cache->_set_key($key, $value, $lifetime);
    }
    
    /**
     * Возвращает указанный ключ из кеша
     *
     * $key - ключ
     */
    public static function GetKey($key)
    {        
        if (CACHE_ENABLED == 'no') return null; // 2010.11.01, zharkov: заглушка
        
        $cache = Cache::Create();
        return $cache->_get_key($key);
    }
    
    /**
     * Отменяет блокировку ключа
     */
    public static function ClearLock($key)
    {        
        if (CACHE_ENABLED == 'no') return false; // 2010.11.01, zharkov: заглушка
        
        $cache = Cache::Create();
        return $cache->_clear_key(CACHE_LOCK_PREFIX . $key);
    }
    
    /**
     * Сбрасывает установленный тег
     * 
     * @version 20120920, zharkov: дополнительно сбрасывается установленный ключ
     */
    public static function ClearTag($tag)
    {        
        if (CACHE_ENABLED == 'no') return false; // 2010.11.01, zharkov: заглушка
        
        $cache = Cache::Create();
        $cache->_clear_key($tag);
        return $cache->_clear_key(CACHE_TAG_PREFIX . $tag);
    }
    
    /** 
     * Очищает указанный ключ кеша
     */
    function _clear_key($key)
    {
        if (CACHE_PREFIX !== '') $key = CACHE_PREFIX . $key;
        
        if (empty($this->connection)) return false;

        //if (!defined('ENABLE_CACHE') OR ENABLE_CACHE != 'yes') return false;
        
        if (CACHE_LOG == 'yes') Log::AddLine(LOG_CACHE, "clear: \t" . $key);
        
        return $this->connection->delete($key);
    }
    
    /**
     * Сброс всех данных кеша
     */
    public static function Flush()
    {
        $cache = Cache::Create();
        return $cache->_flush();
    }
    
    function _flush()
    {
        if (empty($this->connection)) return false;
        
        return $this->connection->flush();
    }    
}