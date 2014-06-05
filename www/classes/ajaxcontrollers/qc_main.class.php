<?php
require_once APP_PATH . 'classes/models/qc.class.php';

class MainAjaxController extends ApplicationAjaxController
{    

    function MainAjaxController()
    {
        ApplicationAjaxController::ApplicationAjaxController();
                
        $this->authorize_before_exec['getlist']         = ROLE_STAFF;
        $this->authorize_before_exec['addpositions']    = ROLE_STAFF;
    }
    
    /**
     * Add positions into QC
     * url: /qc/addpositions
     */
    function addpositions()
    {
        $qc_id          = Request::GetInteger('qc_id', $_REQUEST);
        $stock_id       = Request::GetInteger('stock_id', $_REQUEST);
        $position_ids   = Request::GetString('position_ids', $_REQUEST);
        $item_ids       = Request::GetString('item_ids', $_REQUEST);
        
        // stock was not specified // не указан склад, выдаем ошибку
        if (empty($stock_id)) $this->_send_json(array('result' => 'error'));            

        // positions or items are undefined // не выбраны позиции или айтемы
        if (empty($position_ids) && empty($item_ids)) $this->_send_json(array('result' => 'error'));            

        $qc = new QC();
        
        // remove positions from session if user click "create new" // если пользователь нажал на кнопку "создать новый" очищаются позиции из сессии
        if ($qc_id == -1) 
        {
            unset($_SESSION['qc-new-' . $this->user_id . '-items']);
            $qc_id = 0;
        }
        
        // регистрирует новый сертификат качества
        // if (empty($qc_id)) $qc->RegisterNew($stock_id);

        // create positions list with qtty and add items into QC // формирует список добавляемых позиций с количеством и добавляет айтемы в заказ
        $items_list = explode(',', $item_ids);
        
        // add items into qc // добавляет айтемы в сертификат качества
        foreach ($items_list as $item_id)
        {
            $qc->AddItem($qc_id, $item_id);
        }
        
        // add all items from selected positions into QC // добавляет все айтемы выбранных позиций в сертификат качества
        $positions = new SteelPosition();
        foreach (explode(',', $position_ids) as $position_id)
        {
            foreach ($positions->GetItems($position_id) as $item)
            {
                if (!in_array($item['steelitem']['id'], $items_list)) $qc->AddItem($qc_id, $item['steelitem']['id']);
            }
        }

        
        if (!empty($item_ids))
        {
            $href = '/qc/add/istock:' . $stock_id;
        }
        else if (!empty($position_ids))
        {
            $href = '/qc/add/pstock:' . $stock_id;
        }
        else if (empty($qc_id))
        {
            $href = '/qc/add/stock:' . $stock_id;
        }
        else
        {
            $href = '/qc/' . $qc_id . '/edit';
        }
        
        $this->_send_json(array(
            'result'    => 'okay',
            'href'      => $href,
        ));
    }
    
    /**
     * Get QC list
     * url: /order/getactivelist
     */
    function getlist()
    {
        $stock_id = Request::GetInteger('stock_id', $_REQUEST);
        
        $qcs = new QC();
        $this->_assign('list', $qcs->GetList($stock_id));
        
        $this->_send_json(array(
            'result'    => 'okay', 
            'content'   => $this->smarty->fetch('templates/html/qc/control_qc_select.tpl')
        ));        
    }    
}
