<?php
    require_once 'settings.php';
    
    $_SERVER['HTTP_HOST'] = 'mam.kvadrosoft.com';
    $_SERVER['HTTP_USER_AGENT'] = 'cron';
    
    if (!isset($argv[1]) || empty($argv[1])) die();
    
    switch ($argv[1])
    {
        case 'grab':
            $_REQUEST['arg'] = 'email/service/grab';
            break;
        
        case 'parse':
            $_REQUEST['arg'] = 'email/service/parse';
            break;
        
        default:
            die();
    }
    
    require_once APP_PATH . 'index.php';