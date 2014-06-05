<?php

    $_SERVER['HTTP_HOST'] = 'home.steelemotion.com';
    $_SERVER['HTTP_USER_AGENT'] = 'sespider';
    
    if (!empty($argv[1]))
    switch ($argv[1])
    {
        case 'egrab':
            $_REQUEST['arg'] = 'email/service/grab';
            require_once('/usr/home/mam/home.steelemotion.com/www/index.php');
            break;

		case 'eparse':
            $_REQUEST['arg'] = 'email/service/parse';
            require_once('/usr/home/mam/home.steelemotion.com/www/index.php');
            break;
/*
		case 'egrabbuy':
            $_REQUEST['arg'] = 'email/service/grab/buy';
            require_once('/usr/home/mam/home.steelemotion.com/www/index.php');
            break;

		case 'egrabgang':
            $_REQUEST['arg'] = 'email/service/grab/gang';
            require_once('/usr/home/mam/home.steelemotion.com/www/index.php');
            break;
			
		case 'egrabmarking':
            $_REQUEST['arg'] = 'email/service/grab/marking';
            require_once('/usr/home/mam/home.steelemotion.com/www/index.php');
            break;

		case 'egrabplates':
            $_REQUEST['arg'] = 'email/service/grab/plates';
            require_once('/usr/home/mam/home.steelemotion.com/www/index.php');
            break;

		case 'egrabsteel':
            $_REQUEST['arg'] = 'email/service/grab/steel';
            require_once('/usr/home/mam/home.steelemotion.com/www/index.php');
            break;
*/			
		case 'parsemessages':
            $_REQUEST['arg'] = 'service/main/updatemessages/gga6555skkallLLLJBJCY55NQGGS';
            require_once('/usr/home/mam/home.steelemotion.com/www/index.php');
            break;

		case 'oldattachments':
            $_REQUEST['arg'] = 'service/main/updateattachments/jjshs766dhbHHJHDBhHhd72b22nJbb390917864nn';
            require_once('/usr/home/mam/home.steelemotion.com/www/index.php');
            break;

		case 'clearexpiredreserve':
            $_REQUEST['arg'] = 'service/main/clearexpiredreserve/0826jjdfDSDasd9asd77asdnKDSiudas';
            require_once('/usr/home/mam/home.steelemotion.com/www/index.php');
            break;
			
    }