<?php
ini_set('session.name',             'mam');
ini_set('session.use_cookies',      true);
ini_set('session.use_trans_sid',    false);
ini_set('session.gc_maxlifetime',   300);   // 5 min
ini_set('url_rewriter.tags',        '');
//_503();
session_start(); 
/*
if ($_SERVER['HTTP_HOST'] == 'mam.kvadrosoft.com')
{    
    if ((!isset($_SERVER['PHP_AUTH_USER']) || $_SERVER['PHP_AUTH_USER'] != 'kvadro' || $_SERVER['PHP_AUTH_PW'] != 'zovd!')
    && !isset($_REQUEST['ksecron'])) 
    {
        header('WWW-Authenticate: Basic realm=""');
        header('HTTP/1.0 401 Unauthorized');
        echo 'Hello World!';
        exit;
    }
} 
*/
require_once('settings/constants.php');

// для пингера не добавляется запись в лог                                                  
if ((!isset($_SERVER['QUERY_STRING']) || $_SERVER['QUERY_STRING'] != 'arg=service/pinger') && (!isset($_SERVER['HTTP_USER_AGENT']) || $_SERVER['HTTP_USER_AGENT'] != 'sespider'))
{
    Log::AddLine(LOG_REQUEST, Request::InfoForLog());
    Log::AddLine(LOG_CUSTOM, "COOKIES : " . var_export($_COOKIE, TRUE));    
}

require_once(APP_PATH . 'classes/core/Model.class.php');
require_once('settings/mappings.php');

$module             = isset($_REQUEST['module'])        ? $_REQUEST['module']           : 'main';
$controller_name    = isset($_REQUEST['controller'])    ? $_REQUEST['controller']       : 'main';
$action             = isset($_REQUEST['action'])        ? $_REQUEST['action']           : 'index';
$page_id            = isset($_REQUEST['page_id'])       ? intval($_REQUEST['page_id'])  : 0;

if (preg_match('/^[a-z0-9]{1,64}$/', $controller_name) && preg_match('/^[a-z0-9]{1,64}$/', $action))
{
    // pinger
    if ($controller_name == 'service' && $action == 'pinger') exit;
    
    // авторизация из cookie
    if ($action != 'login' && $action != 'logout')
    {
        auto_login();
    }    
    
    require_once(APP_PATH . 'classes/core/Controller.class.php');
    require_once(APP_PATH . 'classes/core/AjaxController.class.php');
    require_once(APP_PATH . 'classes/core/PrintController.class.php');
    require_once(APP_PATH . 'classes/core/RssController.class.php');    

    require_once(APP_PATH . 'classes/controllers/application.class.php');
    require_once(APP_PATH . 'classes/ajaxcontrollers/applicationajax.class.php');
    require_once(APP_PATH . 'classes/printcontrollers/applicationprint.class.php');
    require_once(APP_PATH . 'classes/rsscontrollers/application.class.php');
    $controller_file_path = APP_PATH . 'classes/' . ( Request::IsAjax() ? 'ajaxcontrollers' : (Request::IsRss() ? 'rsscontrollers' : (Request::IsPrint() ? 'printcontrollers' : 'controllers') ) ) . '/' . $module . '_' . $controller_name . '.class.php';
    $controller_classname = $controller_name . ( Request::IsAjax() ? 'ajax' : (Request::IsRss() ? 'rss' : (Request::IsPrint() ? 'print' : '') ) ) . 'controller';

    

    // run class
    if (file_exists($controller_file_path))
    {   
        
        
        require_once($controller_file_path);

        if (class_exists($controller_classname))
        {
            $controller = new $controller_classname();
        }
        else
        {
            //no such class
            error('no such class:' . $controller);
        }

        if (method_exists($controller, $action))
        {
            $controller->_exec($action);
        }
        else
        {
            //no such method in class
            error('no such method in class: ' . $action);
        }
        
    }
    else
    {
        //class file not exists
        error('class file not exists: ' . $controller_name . ' (' . $controller_file_path . ')');
    }
}
else
{
    //controller name or action not acceptable
    error('controller name or action not acceptable');
}

function debug($user_id, $var)
{
    if ($user_id == $_SESSION['user']['id']) {
        dg($var);
    }
    //dg($_SESSION);
}



function auto_login()
{    
    require_once(APP_PATH . 'classes/models/savedsession.class.php');
    
    /* пока не используется
    if (!empty($_SESSION['user']))
    {
        if (!isset($_SESSION['user']['status_id'])) unset($_SESSION['user']);
        else Cache::SetKey('online-' . $_SESSION['user']['id'], CACHE_LIFETIME_ONLINE);
    }
    */

    if (!isset($_SESSION['user']) && !empty($_COOKIE[CACHE_PREFIX . 'sid']))
    {
        $modelSavedSession  = new SavedSession();
        $result             = $modelSavedSession->RestoreSession($_COOKIE[CACHE_PREFIX . 'sid'], Request::GetVisitorIdInfo());
        
        if (empty($result)) 
        {
            header('Location: ' . APP_HOST . '/logout', true, 302);
            exit;           
        }
/* 20121116, zharkov: сессия заполняется внутри RestoreSession через modelUser::Login
        else
        {
            $_SESSION['user'] = $result;
        }
*/        
    }
}

function error($die_message)
{
    die($die_message);
    Log::AddLine(LOG_ERROR, $die_message);

    if (DEVELOPMENT == 'yes')
    {
        die($die_message);
    }
    else
    {
		print_r($die_message);
        _404();
    }
}


function user_error_handler($severity, $message, $filename, $linenumber)
{
    $error = '';

    switch ($severity)
    {
        case E_USER_ERROR:
            $error = 'FATAL ERROR';
            break;

        case E_USER_WARNING:
            $error = 'WARNING';
            break;

        case E_USER_NOTICE:
            $error = 'NOTICE';
            break;

        default:
            $error = 'UNKNOWN ERROR';
            break;
    }

    $error .= " [$severity] $message\nline $linenumber of file '$filename'";
    //Log::AddLine(LOG_ERROR, $error);

    if (DEVELOPMENT == 'yes')
    {
        echo $error . "<br><br>";
    }
    else if (MAILER_ENABLED == 'yes' && $severity == E_USER_ERROR)
    {
        $from       = ROBOT_ADDRESS;
        $to         = TECHNICIAN_ADDRESS;
        $subject    = APP_NAME . ": Ошибка приложения.";
        $body       = str_replace("\n", "<br>", $error) . "<br><pre>" . var_export($_REQUEST, TRUE) . "<br><br>" . (isset($_SESSION['user']) ? var_export($_SESSION['user'], TRUE) : 'под незарегистрированным пользователем');
        
        $headers    = "From: " . $from . "\r\n";
        $headers    .= "Content-type: text/html; charset=\"utf-8\"\r\n";
        $subject    = "=?UTF-8?b?" . base64_encode($subject) . "?=";

        mail($to, $subject, $body, $headers);                
        
        die('Error');
    }
}

function user_exception_handler($severity, $message, $filename, $linenumber)
{
       
    $error = '';

    switch ($severity)
    {
        case E_USER_ERROR:
            $error = 'FATAL ERROR';
            break;

        case E_USER_WARNING:
            $error = 'WARNING';
            break;

        case E_USER_NOTICE:
            $error = 'NOTICE';
            break;

        default:
            $error = 'UNKNOWN ERROR';
            break;
    }

/* @var $linenumber type */
    $error .= " [$severity] $message\nline $linenumber of file '$filename'";
//TODO: закомментировать _epd()
    //_epd($error);
    throw new Exception($error);
}


function _404($message = '')
{
    header("HTTP/1.0 404 Not Found");
    header("Status: 404 Not Found");
    
    echo '<html>';
    echo '<head>';
	echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
	echo '<meta charset="UTF-8">';
    echo '<meta name="lang" content="eng">';
    echo '<meta name="title" content="' . APP_NAME . '">';
    echo '<meta name="keywords" content="" />';
    echo '<meta name="description" content="" />';
    echo '<title>404. Page not found</title>';
    echo '<link rel="icon" href="/favicon.ico" type="image/icon.ico">';
	echo '<link rel="stylesheet" href="/css/style.' .CSS_VERSION . '.css" type="text/css" media="screen, projection" />';
    echo '</head>';
    echo '<body>';
	echo '<div id="header">';
	echo '<a href="/"><img src="/img/layout/logo.png" alt="logo M-a-M"></img></a>';
	echo '</div>';
    echo '<div style="padding-left: 100px; padding-top: 50px;">';
	echo '<h1 style="clear: both; margin: 10px; font-size: 25px; line-height: 30px; font-weight: normal; border: 0;">404. Page not found</h1>';
	
    if (!empty($message))
    {
		echo '<p style="clear: both; margin: 10px; font-size: 16px;">' . $message . '</p>';
    }
	
	echo '<a style="margin: 10px; font-size: 16px;" href="/">Dashboard</a><br>';
    echo '</div>';
	echo '<div id="footer"></div>';
    echo '</body>';
    echo '</html>';
    exit;
}

function _503($message = '')
{
    header("HTTP/1.1 503 Service Unavailable");
    header("Status: 503 Service Unavailable");
    header('Retry-After: 3600');
    
    echo '<html>';
    echo '<head>';
	echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
	echo '<meta charset="UTF-8">';
    echo '<meta name="lang" content="eng">';
    echo '<meta name="title" content="' . APP_NAME . '">';
    echo '<meta name="keywords" content="" />';
    echo '<meta name="description" content="" />';
    echo '<title>503. Service unavailable</title>';
    echo '<link rel="icon" href="/favicon.ico" type="image/icon.ico">';
	echo '<link rel="stylesheet" href="/css/style.' .CSS_VERSION . '.css" type="text/css" media="screen, projection" />';
    echo '</head>';
    echo '<body>';
	echo '<div id="header">';
	echo '<a href="/"><img src="/img/layout/logo.png" alt="logo M-a-M"></img></a>';
	echo '</div>';
    echo '<div style="padding-left: 100px; padding-top: 50px;">';
	echo '<h1 style="clear: both; margin: 10px; font-size: 25px; line-height: 30px; font-weight: normal; border: 0;">503. Service unavailable</h1>';
    
	if (empty($message))
    {
        echo '<p style="clear: both; margin: 10px; font-size: 16px;">' . "We're in process of updating site.</p>";
		echo '<p style="clear: both; margin: 10px; font-size: 16px;">Sorry for inconveniences.</p>';
		echo '<p style="clear: both; margin: 10px; font-size: 16px;">Be back in 5 minutes. </p>';
    }
    else
    {
        echo '<p style="clear: both; margin: 10px; font-size: 16px;">' . $message . '</p>';
    }
	
	echo '<i style="margin: 10px; font-size: 1.2em;" href="/">STEELemotion team</i><br>';
    echo '</div>';
	echo '<div id="footer"></div>';
    echo '</body>';
    echo '</html>';
    exit;
}

function dg($var = null)
{
    header("Content-Type: text/html;charset=utf-8");
    if(!isset($var)) die('Empty $var');
    echo "<pre>";
    print_r($var);
    die();
}
/**
 * d10n debug function<br />
 * (echo print die)
 *
 * @param mixed $var
 * @param boolean $die
 * @param string $file
 * @param string $line
 * @param string $mode May be changed to 'var_dump'
 * @param string $charset
 */
function _epd($var = null, $die = TRUE, $file = NULL, $line = NULL, $mode = 'print_r', $charset = "utf-8")
{
    //if ($die) header("Content-Type: text/html;charset=" . $charset);

    if (isset($var) !== TRUE) die('DIE. Variable is not set.');
    
    
    echo"<pre>";
    if (isset($file)) echo "File: " . $file . "\n";
    if (isset($line)) echo "Line: " . $line . "\n";
    switch ($mode)
    {
        case 'var_dump' :
            var_dump($var);
            break;

        default : print_r($var);
    }

    if ($die === TRUE) die();
}

function _is_bot()
{
    if (defined('IS_BOT')) return IS_BOT;
    
    $engines = array(
        'Yandex',
        'YaDirectBot',
        'Google',
        'msnbot',
        'bingbot',
        'Rambler',
        'Yahoo',
        'AbachoBOT',
        'accoona',
        'AcoiRobot',
        'ASPSeek',
        'CrocCrawler',
        'Dumbot',
        'FAST-WebCrawler',
        'GeonaBot',
        'Gigabot',
        'Lycos',
        'MSRBOT',
        'Scooter',
        'AltaVista',
        'WebAlta',
        'IDBot',
        'eStyle',
        'Mail.Ru',
        'Scrubby',
        'Aport',
    );

    $result = false;
    
    $user_agent = $_SERVER['HTTP_USER_AGENT'];
    foreach ($engines as $engine)
        if (strstr($user_agent, $engine))
            $result = true;            
    
    define(IS_BOT, $result);
    return $result;
}