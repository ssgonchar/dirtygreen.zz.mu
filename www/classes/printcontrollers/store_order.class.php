<?
require_once APP_PATH . 'classes/models/order.class.php';

class OrderPrintController extends ApplicationPrintController
{
    function OrderPrintController()
    {
        ApplicationPrintController::ApplicationPrintController();
        
        $this->authorize_before_exec['view'] = ROLE_MODERATOR;
    }
    
    
    /**
    * Отображает страницу просмотра заказа
    * 
    * @link /store/order/view/{$oder_id}/~print
    */
    function view()
    {
        $order_id = Request::GetInteger('id', $_REQUEST);
        
        $orders     = new Order();
        $order      = $orders->GetById($order_id);
        $order      = $order['order'];
        
        if (empty($order)) _404();
        
        
        $product = new Product();
        $this->_assign('products',  $product->GetListByOrder($order_id));
        $this->_assign('order',     $order);        
        
        $this->_display('view');        
    }
}
