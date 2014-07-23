<?php
require_once(APP_PATH . 'classes/core/MailerBase.class.php');

class StockMailer extends MailerBase
{
    function StockMailer()
    {
        MailerBase::MailerBase();

        $this->path = 'stock'; 
    }
   
    /**
     * Send notification to customer about cancelling the order
     * 
     * @param mixed $stock
     * @param mixed $order
     */
    function SendOrderCancelNotice($stock, $order)
    {            
        $parameters = array(
            'stock' => $stock,
            'order' => $order
        );

        //$stock['email_for_orders']
        $this->_send(ROBOT_ADDRESS, $order['author']['email'], '', '', 'ordercancelled', $parameters);
        $this->_send($order['author']['email'], TECHNICIAN_ADDRESS, '', '', 'ordercancelled', $parameters);        
    }
}