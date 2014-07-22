<?php
require_once(APP_PATH . 'classes/core/MailerBase.class.php');

class UserMailer extends MailerBase
{
    function UserMailer()
    {
        MailerBase::MailerBase();

        $this->path = 'account'; 
    }
   
    function SendRegisterConfirmation($user)
    {            
        $this->_send(ROBOT_ADDRESS, $user['email'], '', '', 'registerconfirmation', $user);
    }

    /**
    * Отправляет регистрационные данные
    * 
    * @param mixed $email
    * @param mixed $list
    */
    function SendRemindInstructions($email, $list)
    {            
        $this->_send(ROBOT_ADDRESS, $email, '', '', 'remindinstructions', $list);
    }

    function SendProfileUpdateNotice($user)
    {            
        $this->_send(ROBOT_ADDRESS, $user['email'], '', '', 'profileupdate', $user);
    }	
}