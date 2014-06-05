<?php
require_once APP_PATH . 'classes/models/nomenclature_category.class.php';
require_once APP_PATH . 'classes/models/nomenclature.class.php';
require_once APP_PATH . 'classes/models/user.class.php';

class MainAjaxController extends ApplicationAjaxController
{    

    function MainAjaxController()
    {
        ApplicationAjaxController::ApplicationAjaxController();
                
        $this->authorize_before_exec['get_category_list']      = ROLE_STAFF;
    }
    
    function getdictionary()
    {
        $modelNomanclature = new Nomenclature();
        $dictionary = $modelNomanclature->GetList();
        $this->_send_json(array('result' => 'okay', 'dictionary'=>$dictionary));
    }
}
?>