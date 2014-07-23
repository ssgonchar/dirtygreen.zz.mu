<?php
require_once APP_PATH . 'classes/models/biz.class.php';
require_once APP_PATH . 'classes/models/product.class.php';

class MainAjaxController extends ApplicationAjaxController
{    

    function MainAjaxController()
    {
        ApplicationAjaxController::ApplicationAjaxController();
                
        $this->authorize_before_exec['getproducts'] = ROLE_STAFF;
        $this->authorize_before_exec['getbiz']      = ROLE_STAFF;
    }
        

    /**
     * Get biz list for team // Возвращает список бизнесов команды
     * url: /team/getbiz
     */
    function getbiz()
    {
        $team_id    = Request::GetInteger('team_id', $_REQUEST);

        $bizes      = new Biz();
        $list       = $bizes->GetListByTeam($team_id);

        $this->_send_json(array('result' => 'okay', 'list' => $this->_prepare_list($list, 'biz', 'id', 'doc_no_full')));
    }

    /**
     * Get product list for team // Возвращает список продуктов команды
     * url: /team/getproducts
     */
    function getproducts()
    {
        $team_id        = Request::GetInteger('team_id', $_REQUEST);
        $product_id     = Request::GetInteger('product_id', $_REQUEST);
        $full_branch    = Request::GetBoolean('full_branch', $_REQUEST, false);
		$in_biz			= Request::GetBoolean('in_biz', $_REQUEST, false);
		
        $products   = new Product();
		
		/*
		 * @version 20130518, Sasha
		 * all products for team or products in bizes
		 */
		$list       = $products->GetTreeWithoutNode($team_id, $product_id, $in_biz);
	
        foreach ($list as $key => $row) if ($row['product']['level'] > 0 && !$full_branch) unset($list[$key]);        
        $this->_send_json(array('result' => 'okay', 'products' => $this->_prepare_list($list, 'product', 'id', 'title_list')));
    }
    
}
