<?php
require_once(APP_PATH . 'classes/core/MailerBase.class.php');

class EmailMailer extends MailerBase
{
    function EmailMailer()
    {
        MailerBase::MailerBase();

        $this->path = 'email'; 
    }
   
    /**
     * ���������� ����������� ������
     * 
     * @param mixed $email
     * @param mixed $attachments
     */
    function Send($email, $attachments = array())
    {            
        $parameters         = $email;
        $parameters['date'] = date("d.m.Y");
//dg($parameters);
        return $this->_send($email['sender_address'], $email['recipient_address'], $email['cc_address'], $email['bcc_address'], 'default', $parameters, $attachments);
    }
}