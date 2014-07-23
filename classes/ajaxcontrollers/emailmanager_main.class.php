<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
require_once APP_PATH . 'classes/models/mailbox.class.php';
require_once APP_PATH . 'classes/models/email.class.php';

class MainAjaxController extends ApplicationAjaxController {

    public function __construct() {
        ApplicationAjaxController::ApplicationAjaxController();

        $this->authorize_before_exec['index'] = ROLE_STAFF;
    }

    public function index() {
        
    }

    public function getmailboxes() {
        $modelMailbox = new Mailbox();
        $modelEmail = new Email();
        $mailboxes = $modelMailbox->GetListForUser($this->user_id);

        /*
          foreach ($mailboxes as &$row) {
          $arg = array(
          ''
          );
          $count = $modelEmail->Count($arg);
          }
         */

        $this->_assign('mailboxes', $mailboxes);
        $this->_send_json(array(
            'result' => 'okay',
            'content' => $this->smarty->fetch('templates/html/emailmanager/control_mailboxes.tpl') //json.content
        ));
    }

    public function getemails() {
        $firstvisiblerow = $_GET['recordstartindex'];
        $lastvisiblerow = $_GET['recordendindex'];
        $rowscount = $lastvisiblerow - $firstvisiblerow;

        $modelEmail = new Email();
        
        $arg = array (
            'limit' => array(
                'lower' => $firstvisiblerow, 
                'number' => $rowscount,
            ),
            //'where' => 'id IN (13,15,17)',
            'order' => 'id DESC'
        );
        
        $rowset = $modelEmail->table->SelectList($arg);
        
        // get data and store in a json array
        foreach ($rowset as $row) {
            $emails[] = array(
                'OrderID' => $row['email_raw_id'],
                'OrderDate' => $row['created_at'],
                'ShippedDate' => $row['date_mail'],
                'ShipName' => $row['title'],
                'ShipAddress' => $row['recipient_address'],
                'ShipCity' => $row['type_id'],
                'ShipCountry' => $row['id']
            );
        }

        $data[] = array(
            'TotalRows' => 999,
            'Rows' => $emails
        );
        echo json_encode($data);
    }

}
