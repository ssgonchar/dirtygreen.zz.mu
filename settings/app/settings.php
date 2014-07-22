<?php

    define('APP_TEMP',              '/home/mam/home.steelemotion.com/settings/');
    define('APP_LOGS',              APP_TEMP . 'logs/');
    define('APP_CACHE',             APP_TEMP . 'cache/');
    define('SMARTY_COMPILED_PATH',  APP_TEMP . 'compiled/');
    define('ATTACHMENT_PATH',       '/home/mam/attachments/');
    define('BACKUP_DB_PATH',        APP_TEMP . 'backup/');	

    define('APP_NAME',              'M -a- M');
    define('APP_HOST',              'http://' . $_SERVER['HTTP_HOST']);
	define('ATTACHMENT_HOST',       'http://a.steelemotion.com');
    define('APP_DOMAIN',            '.' . $_SERVER['HTTP_HOST']);
    define('APP_PATH',              '/home/mam/home.steelemotion.com/www/');
    
    define('APP_DBHOST',            'localhost');
    define('APP_DBNAME',            'mam_www');
    define('APP_DBUSER',            'mam');
    define('APP_DBPASS',            'vNovom30Vete=');

    define('STATS',                 '');
    define('GASTATS',               '');

    define('DEVELOPMENT',           'no');
    if($_SESSION['user']['id']=='1671') {
		//  define('DEVELOPMENT',           'yes');
	 }
	 
	 define('SMARTY_DEBUG',          'no');
    define('LOG',                   'yes');
	define('CUT_LOG',               'yes');	

    define('MAILER_ENABLED',        'yes');		// Emails
    define('SPHINX_ENABLED',        'yes'); 	// SphinxSearch
    define('CACHE_ENABLED',         'yes'); 	// Memcache

    define('CACHE_PREFIX',          'mam');