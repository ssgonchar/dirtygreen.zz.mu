<?php
require_once APP_PATH . 'classes/models/product.class.php';

class MainAjaxController extends ApplicationAjaxController
{    

    function MainAjaxController()
    {
        ApplicationAjaxController::ApplicationAjaxController();
                
        $this->authorize_before_exec['getlist'] = ROLE_STAFF;
    }
    
    /**
     * Get products list // Возвращает список продуктов
     * url: /product/getlist
     */
    function getlist()
    {
        $parent_id  = Request::GetInteger('parent_id', $_REQUEST);

        $products = new Product();
        $this->_send_json(array('result' => 'okay', 'list' => $this->_prepare_list($products->GetList(0, $parent_id), 'product')));
    }
}
