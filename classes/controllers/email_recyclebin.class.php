<?php
require_once APP_PATH . 'classes/core/Pagination.class.php';
require_once APP_PATH . 'classes/models/email.class.php';

class RecyclebinController extends ApplicationController
{
    public function RecyclebinController()
    {
        ApplicationController::ApplicationController();
        
        $this->authorize_before_exec['addemail']        = ROLE_STAFF;
        $this->authorize_before_exec['removeemail']     = ROLE_STAFF;
        
        $this->context = true;
        
        $this->breadcrumb = array(
            'eMails'        => '/emails',
            'Recycle Bin'   => '',
        );
    }
    
    /**
     * @url /email/recyclebin/addemail/{$email_id}
     */
    public function addemail()
    {
        $email_id = Request::GetInteger('id', $_REQUEST);
        
        if ($email_id <= 0) _404();
        
        $modelEmail = new Email();
        $email = $modelEmail->GetById($email_id);
        
        if (!isset($email['email'])) _404();
        
        $email = $email['email'];
        
        if (isset($email['recycle_bin']) && !empty($email['recycle_bin'])) _404();
        
        $modelEmail->MoveToRecycleBin($email_id);
        
        $this->_message('Email was added to recycled bin !', MESSAGE_OKAY);
        $this->_redirect(array('emails', 'filter', 'type:' . EMAIL_TYPE_RECYCLEBIN));
    }
    
    /**
     * @url /email/recyclebin/removeemail/{$email_id}
     */
    public function removeemail()
    {
        $email_id = Request::GetInteger('id', $_REQUEST);
        
        if ($email_id <= 0) _404();
        
        $modelEmail = new Email();
        $email = $modelEmail->GetById($email_id);
        
        if (!isset($email['email'])) _404();
        
        $email = $email['email'];
        
        if (!isset($email['recycle_bin']) || empty($email['recycle_bin'])) _404();
        
        $modelEmail->RemoveFromRecycleBin($email_id);
        
        $this->_message('Email was removed from recycled bin !', MESSAGE_OKAY);
        $this->_redirect(array('emails', 'filter', 'type:' . EMAIL_TYPE_RECYCLEBIN));
    }
}