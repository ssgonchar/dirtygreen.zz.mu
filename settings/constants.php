<?php
    set_error_handler('user_error_handler');

    setlocale(LC_ALL,       'en_US.UTF-8');
    setlocale(LC_NUMERIC,   'en_US.UTF-8');

    ini_set('max_execution_time',           '120');
    ini_set('zend.ze1_compatibility_mode',  0);    
    //production server
    if ($_SERVER['HTTP_HOST'] == 'home.steelemotion.com' || $_SERVER['HTTP_HOST'] == 'www.home.steelemotion.com')
    {
        require_once('/home/mam/home.steelemotion.com/settings/app/settings.php');
    }
    //development server
    else if ($_SERVER['HTTP_HOST'] == 'home.steelemotion.local' || $_SERVER['HTTP_HOST'] == 'www.home.steelemotion.local')
    {
        require_once('/home/mam/home.steelemotion.local/settings/app/settings.php');
    }    
    else if ($_SERVER['HTTP_HOST'] == 'mam.kvadrosoft.com' || $_SERVER['HTTP_HOST'] == 'www.mam.kvadrosoft.com')
    {
        require_once('/home/mam/mam.kvadrosoft.com/settings/app/settings.php');
    }
    else if ($_SERVER['HTTP_HOST'] == 'test.mam.kvadrosoft.com' || $_SERVER['HTTP_HOST'] == 'www.test.mam.kvadrosoft.com')
    {
        require_once('/home/mam/test.mam.kvadrosoft.com/settings/app/settings.php');
    }
    else if (isset($_SERVER['SERVER_ADMIN']) && $_SERVER['SERVER_ADMIN'] == 'fatman@localhost')
    {   
        require_once '/PHP.Settings/mam/app/settings.php';
    }
    else if (isset($_SERVER['SERVER_ADMIN']) && $_SERVER['SERVER_ADMIN'] == 'd10n@linux')
    {   
        require_once '/home/work/www.settings/mam/app/settings.php';
    }
	else if (isset($_SERVER['SERVER_ADMIN']) && $_SERVER['SERVER_ADMIN'] == 'dev@linux')
    {   
        require_once '/home/work/www.settings/mam/app/settings.php';
    }
    else
    {
        die('Good by!');
    }   


    if (DEVELOPMENT == 'yes')
    {
        error_reporting(E_ALL);
    }
    else
    {
        error_reporting(0);
    }
    
    set_error_handler('user_error_handler');

    
    define('MEMCACHE_HOST', 'localhost');
    define('MEMCACHE_PORT', '11211');
    
    
    define('SMARTY_TEMPLATES_PATH', APP_PATH);

    define('ROBOT_ADDRESS',         'fingercrew2@gmail.com');
    //define('TECHNICIAN_ADDRESS',    'dima.zharkov@gmail.com');
    define('TECHNICIAN_ADDRESS',    'fingercrew2@gmail.com');

    define ('MESSAGE_OKAY',     0);
    define ('MESSAGE_ERROR',    1);
    define ('MESSAGE_WARNING',  2);
    

    date_default_timezone_set('Europe/London'); 
    define('JS_VERSION',    '119');
    define('CSS_VERSION',   '119');


    define('LOCALE_DEFAULT',    'en-US');
    define('DB_TIME_ZONE',      '');
    //define('DB_TIME_ZONE',      '+1:00');   // костыль, т.к. не установлен Time zone description tables в MySql
    //define('DB_TIME_ZONE',      'Europe/London');
    //define('DB_TIME_ZONE',      'Etc/GMT-1');


    require_once(APP_PATH . 'classes/core/Request.class.php');
    require_once(APP_PATH . 'classes/core/Log.class.php');
    require_once(APP_PATH . 'classes/core/Cache.class.php');    
    require_once(APP_PATH . 'classes/core/DatabaseConnection.class.php');
    require_once(APP_PATH . 'classes/core/Timer.class.php');
    require_once(APP_PATH . 'classes/services/smarty/libs/Smarty.class.php');
    require_once(APP_PATH . 'classes/services/json/json.php');


    //  Roles
    define('ROLE_ALL',                  0);
    define('ROLE_SUPER_ADMIN',          1);
    define('ROLE_ADMIN',                2);
    define('ROLE_SUPER_MODERATOR',      3);
    define('ROLE_MODERATOR',            4);
    define('ROLE_SUPER_STAFF',          5);
    define('ROLE_STAFF',                6);
    define('ROLE_SUPER_USER',           7);
    define('ROLE_USER',                 8);
    define('ROLE_LIMITED_USER',         9);
    define('ROLE_GUEST',                10);

    
    //  User Statuses
    define('USER_ALL',                  0);
    define('USER_INITIAL',              1);
    define('USER_PENDING',              2);
    define('USER_ACTIVE',               3);
    define('USER_BLOCKED',              4);


    //  Moderate Status
    define('MODERATE_STATUS_NEW',       1);
    define('MODERATE_STATUS_ACTIVE',    2);
    define('MODERATE_STATUS_BANNED',    3);
    define('MODERATE_STATUS_DELETED',   4);

    
    // Object types
    define('OBJECT_TYPE_POST',          1);


    // Other settings
    define('NULL_DATE',                 '1900-01-01 00:00:00');
    define('ITEMS_PER_PAGE',            20);
    
    define('DEFAULT_LANG',              'en');
    define('DEFAULT_LANG_TITLE',        'English');
