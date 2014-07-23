<?php
require_once APP_PATH . 'classes/models/contactdata.class.php';
require_once APP_PATH . 'classes/models/preorder.class.php';
require_once APP_PATH . 'classes/models/stock.class.php';

/**
 * Контроллер приложения
 */
class ApplicationController extends Controller	//дочерний класс MainController наследует все публичные 
		//и защищенные методы из родительского класса Controller
{		//обьявляю данные (свойства) класса ApplicationController
    var $user               = array();
    var $user_id            = 0;
    var $user_login         = '';
    var $user_role          = ROLE_GUEST;
    var $user_status        = USER_ALL;

    var $page_name          = '';
    var $page_title         = '';
    var $page_alias         = '';
    
    var $pager_path         = '';
    var $page_no            = 0;
    
    var $name_space         = '';
    var $session_id         = '';
    
    var $meta_keywords      = '';
    var $meta_description   = '';
    
    var $breadcrumb         = array();
    
    var $lang               = DEFAULT_LANG;

    /*
     * Получучаем историю посещений для меню
     * 
     */
    /*
    public function GetVisitHistory()
    {
        $arr_history = $_SESSION['__core:request_cache'];
        
        
        
        debug('1671', $arr_history);
        
        if(count($arr_history)<1) {
            return;
        }
        
        foreach ($arr_history as $row) {
            if((strripos($row, '/') === 0)) {
                return;            
            } else {
                $arr_path[] = explode('/', $row);
                
                if(count($arr_path) > 2) {
                    $arr_settings['title'][] = $arr_path['1'];
                    $arr_parameters = explode(';', $arr_path['2']);                    
                }
            }
        }
        
        return $arr_settings;
    }
    
     * 
     */
    function ApplicationController()	//объявляю метод ApplicationController() класса ApplicationController
    {
        Controller::Controller();	//вызываю метод Controller() родительского класса Controller
        
        if (array_key_exists('user', $_SESSION))
        {
            $this->user_id      = Request::GetInteger('id',         $_SESSION['user'], 0);
            $this->user_login   = Request::GetString('login',       $_SESSION['user'], '');
            $this->user_role    = Request::GetInteger('role_id',    $_SESSION['user'], ROLE_GUEST);
            $this->user_status  = Request::GetInteger('status_id',  $_SESSION['user'], USER_ALL);
        }
        
        $this->page_alias   = Request::GetString('page_alias', $_REQUEST, '');
        $this->name_space   = Request::GetString('name_space', $_REQUEST, '');
        $this->session_id   = Request::GetString('session_id', $_SESSION, '', 50);
        
        $this->page_no      = Request::GetInteger('page_no', $_REQUEST, 1);
        $this->pager_path   = Request::GetString('pager_path', $_REQUEST);         // устанавливается в Core.Mappings->_get_query_string();   

        $this->lang         = Request::GetString('lang', $_REQUEST, '', 2);        
        $this->layout       = 'main';
        
/*        
        $contactdata = new ContactData();
        $result = $contactdata->FindEmail('denn');
        dg($result);
*/
/*
        $modelContactData = new ContactData();
        $result = $modelContactData->FindEmail('stefano@ossilaser.it', 0, 10, true); // 'stefano@ossilaser.it'
        dg($result);
*/        
/*
        $modelBiz   = new Biz();
        $result     = $modelBiz->GetListByTitle('kovint', 100);
dg($result);
*/
/*
        $modelEmail = new Email();
        $result = $modelEmail->testdatetime();
        
        dg($result['created_at']);
*/
        $_SESSION['app_settings'] = array('anonymous_comments' => true);
/*        
        echo time();
        echo '<br>' . strtotime('2012-09-22 22:27:00');
        echo '<br>' . (time() - strtotime('2012-09-22'));
        die();        
*/        
/*
        $email = 'zharko.dima@gmail.com';
        $email = preg_replace('#([a-z0-9_\-]+\.)*[a-z0-9_\-]+@([a-z0-9][a-z0-9\-]*[a-z0-9]\.)+[a-z]{2,4}#i', '', $email);

        dg($email);
*/        
        
        //if(($_SESSION['user']['id'] == '1671') || ($_SESSION['user']['id'] == '1682') || ($_SESSION['user']['id'] == '1705')) {//1682 serg; 1705 evgeniy
        //debug("1682", $_SESSION['user']['role_id']);
        if(($_SESSION['user']['role_id'] == "2")) {
            //echo 'done';
            ini_set ('display_errors', 'on');
            ini_set ('log_errors', 'on');
            ini_set ('display_startup_errors', 'on');
            ini_set ('error_reporting', E_ALL);            
        }
    }
    

    /**
     * Перегруженный метод из Controller->_before_display()
     * Выполняется перед отрисовкой страницы
     * 
     */
    function _before_display()
    {               
        $this->_bind_page_settings();
        $this->_bind_stat();
        $this->_reload_user();

//TODO d10n 20130220: Реализовать рациональное подключение данных плагинов
        $this->_assign('include_ui',            true);
        $this->_assign('include_upload',        true);
        $this->_assign('include_prettyphoto',   true);
        
        $this->_assign('include_mce',           true);        
    }

    /**
     * Проверяет изменился ли профиль пользователя, если изменился, перегружаем его
     * 
     */
    function _reload_user()
    {
        if (Cache::GetKey('reload-user-' . $this->user_id))
        {
            $users  = new User();
            $user   = $users->GetById($this->user_id);

            if (!empty($user)) $_SESSION['user'] = $user['user'];
            
            Cache::ClearKey('reload-user-' . $this->user_id);
        }
    }
    
    /**
     * Добавляет статистику человека
     * 
     */
    function _bind_stat()
    {
        $preorders  = new PreOrder();
        $list       = $preorders->GetList();        
        $this->_assign('stat_preorders_count', count($list));
        
/*      20120906, zharkov: пока убрал эту функциональность
        $stocks = new Stock();
        $this->_assign('stat_stocks', $stocks->GetList());
*/        
        // обновляет дату последнего посещения страниц сайта        
        if (!empty($this->user_id) && $_REQUEST['module'] != 'service') Cache::SetKey('activeuser-' . $this->user_id, time());
        
        // последнее сообщение чата
        $activeuser = new ActiveUser();
        $this->_assign('user_last_chat_message_id', $activeuser->GetLastMessage());
        
        // 20121113, zharkov: текущий статус пользователя
        $this->_assign('onlinestatus', Cache::GetKey('onlinestatus-' . $this->user_id));
    }
    
    /**
     * Выводит настройки страницы
     * 
     */
    function _bind_page_settings()
    {
        if (!empty($this->page_name))
        {
            $this->_assign('page_name',  $this->page_name);
        }

        if (!empty($this->page_title))
        {
            $this->_assign('page_title',  $this->page_title);
        }
        else
        {
            if (!empty($this->page_name))
            {
                $this->_assign('page_title',  $this->page_name);
            }        
        }
        
        $this->_assign('page_alias',        $this->page_alias);
        $this->_assign('name_space',        $this->name_space);        
        $this->_assign('pager_path',        $this->pager_path);
        $this->_assign('page_no',           $this->page_no);        
        
        $breadcrumb = array();
        foreach ($this->breadcrumb as $key => $value) $breadcrumb[] = array('name' => $key, 'url' => $value);
        $this->_assign('breadcrumb', $breadcrumb);
        
        $this->_assign('current_lang', $this->lang);
    }    
}
