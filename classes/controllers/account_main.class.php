<?php
require_once(APP_PATH . 'classes/models/user.class.php');
require_once(APP_PATH . 'classes/models/savedsession.class.php');

class MainController extends ApplicationController
{
    function MainController()
    {
        ApplicationController::ApplicationController();        
    }
    
    /**
     * User Logon from security token
     *  
     */
    function autologin()
    {
        $token      = Request::GetString('token', $_REQUEST);
        $user_id    = Request::GetInteger('user_id', $_REQUEST);
        
        if (md5('TAKeNiT00ZEmyn655-%63&BASK' . $user_id) == $token)
        {
            $modelUser  = new User();
            $user       = $modelUser->GetById($user_id);
            
            if (isset($user['user']))
            {
                $this->_login($user['user']['login'], $user['user']['password'], 1);
            }
        }

        _404();        
    }
    
    /**
     * Показывает страницу входа на сайт
     * 
     * url: /login
     */
    function login()
    {
        if (array_key_exists('btn_login', $_REQUEST))
        {
            // фиксирует количество попыток логина пользователя, после трех показывается каптча
            $login_attempts = Request::GetInteger('login_attempts', $_SESSION);
            $_SESSION['login_attempts'] = $login_attempts + 1;
            
            $login      = Request::GetString('login', $_REQUEST, '');
            $password   = Request::GetString('password', $_REQUEST, '');
            $remember   = Request::GetBoolean('remember', $_REQUEST, 1);

            if (empty($login))
            {
                $this->_message('Login must be specified !', MESSAGE_ERROR);
                $this->_redirect(array('login'));
            }

            if (empty($password))
            {
                $this->_message('Password must be specified !', MESSAGE_ERROR);
                $this->_redirect(array('login'));
            }
            
            $this->_login($login, $password, $remember);

        }
        else
        {
            $this->page_name    = 'Enter';
            $this->breadcrumb   = array($this->page_name => '/login');
            
            $this->layout   = 'login';
            $this->js       = 'account_login';
            
            $this->_display('login');        
        }        
    }
    
    /**
     * User Logon
     * 
     * @param mixed $login
     * @param mixed $password
     * @param mixed $remember
     */
    private function _login($login, $password, $remember)
    {
        $visitor_params = Request::GetVisitorIdInfo();

        $user   = new User();
        $result = $user->Login($login, $password, $visitor_params, $remember);
        
        // ошибки аутентификации
        if (isset($result['ErrorCode']))
        {
            if ($result['ErrorCode'] == -1)
            {
                $this->_message('Unknown user !', MESSAGE_ERROR);
                $this->_redirect(array('login'));                    
            }
            
            if ($result['ErrorCode'] == -2)
            {
                $this->_message('Account blocked !', MESSAGE_ERROR);
                $this->_redirect(array('login'));                    
            }

            if ($result['ErrorCode'] == -3)
            {
                $this->_message('Account blocked !', MESSAGE_ERROR);
                $this->_redirect(array('login'));                    
            }

            if ($result['ErrorCode'] == -4)
            {
                $this->_message('Account blocked !', MESSAGE_ERROR);
                $this->_redirect(array('login'));                    
            }
            
            if ($result['ErrorCode'] == -5)
            {
                $this->_message('Access denied !', MESSAGE_ERROR);
                $this->_redirect(array('login'));                    
            }                
        }
        
        // перенаправление
        $redirect_url = $this->_get_previous_url();            
        if ($redirect_url == 'login') 
        {
            $this->_redirect(array());
        }
        else
        {
            $this->_redirect_to_previous();
        }        
    }
    
    
    /**
    * Выход из аккаунта
    * 
    * @link /logout
    */
    function logout()
    {
        $users = new User();
        $users->Logout();
        
        $this->_redirect(array(''));        
    }    
    
    /**
    * Показывает страницу заблокированного аккаунта
    * 
    * @link /account/blocked
    */
    function blocked()
    {
        if (!isset($_SESSION['accountblocked'])) $this->_redirect(array(''));
        unset($_SESSION['accountblocked']);
        
        $this->page_name = 'Account Blocked !';
        $this->_display('blocked');
    }    
    
    /**
    * Показывает страницу забаненого аккаунта
    * 
    * @link /account/banned
    */
    function banned()
    {
        if (!isset($_SESSION['accountbanned'])) $this->_redirect(array(''));
        
        $this->_assign('ban', $_SESSION['accountbanned']);
        
        unset($_SESSION['accountbanned']);
        
        $this->page_name = 'Ваш профиль забанен';
        $this->breadcrumb   = array('Профиль забанен' => '/account/banned');
        
        $this->_display('banned');
    }
}
