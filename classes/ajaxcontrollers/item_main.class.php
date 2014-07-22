<?php
require_once APP_PATH . 'classes/models/steelitem.class.php';

class MainAjaxController extends ApplicationAjaxController
{    

    function MainAjaxController()
    {
        ApplicationAjaxController::ApplicationAjaxController();
                
        $this->authorize_before_exec['getcontext']      = ROLE_STAFF;
        $this->authorize_before_exec['getdetails']      = ROLE_STAFF;
        $this->authorize_before_exec['converttoreal']   = ROLE_STAFF;
        $this->authorize_before_exec['cutaddpiece']     = ROLE_STAFF;  
        $this->authorize_before_exec['remove']   = ROLE_STAFF;      
    }
    
    /**
     * Add cutting piece to list 
     * url: /item/cutaddpiece
     * 
     * @version 20130112, zharkov
     */
    function cutaddpiece()
    {
        $item_id        = Request::GetInteger('item_id', $_REQUEST);
        $index          = Request::GetInteger('index', $_REQUEST);
        
        $modelItem      = new SteelItem();
        $item           = $modelItem->GetById($item_id);
        $item           = $item['steelitem'];
        
        $modelPosition  = new SteelPosition();
        $position       = $modelPosition->GetById($item['steelposition_id']);
        $position       = $position['steelposition'];
               
        $modelStock     = new Stock();
        
        $this->_assign('index',     $index);
        $this->_assign('item',      $item);
        $this->_assign('locations', $modelStock->GetLocations($position['stock_id']));
        $this->_assign('positions', array());
        
        $this->_send_json(array(
            'result'    => 'okay', 
            'row'       => $this->smarty->fetch('templates/html/item/control_cut_piece.tpl')
        ));
    }
        
    /**
     * Convert item from virtual to real // Конвертирует айтемв из виртуального в реальный
     * url: /item/converttoreal
     * 
     * @version 20121125, zharkov
     */
    function converttoreal()
    {
        $item_id = Reqest::GetInteger('item_id', $_REQUEST);
        
        $steelitems = new SteelItem();
        $result     = $steelitems->ConvertToReal($item_id);
        
        if (empty($result))
        {
            $this->_send_json(array('result' => 'error', 'message' => 'This Item can not be converted to real !'));
        }
        else
        {
            $this->_send_json(array('result' => 'okay', 'message' => 'Item was successfully converted !'));
        }        
    }
    
    /**
     * Get context for item
     * url: /item/getcontext
     * 
     * @version 20121002, zharkov
     */
    function getcontext()
    {
        $item_id        = Request::GetInteger('item_id', $_REQUEST);
        $is_revision    = Request::GetInteger('is_revision', $_REQUEST);
        
        $modelSteelItem = new SteelItem();
        $item           = $modelSteelItem->GetById($item_id);        
        $this->_assign('item', $item['steelitem']);
        
        $modelAttachment    = new Picture();
        $attachments        = $modelAttachment->GetList('item', $item_id);

        $this->_assign('attachments', $attachments['data']);

        if (empty($is_revision))
        {
            $this->_send_json(array(
                'result'    => 'okay', 
                'content'   => $this->smarty->fetch('templates/html/item/control_context.tpl')
            ));
        }
        else
        {
            $this->_send_json(array(
                'result'    => 'error', 
                'message'   => 'Item was successfully converted !'
            ));
        }
    }
    
    /**
     * Get detail info about item
     * url: /item/getdetails
     * 
     * @version 20121019, d10n
     * @deprecated 20121219, zharkov: replaced with get_context
     */
    public function deprecated_getdetails()
    {
        $item_id = Request::GetInteger('item_id', $_REQUEST);
        
        $modelSteelItem = new SteelItem();
        $item           = $modelSteelItem->GetById($item_id);
        
        if (isset($item))
        {
            $this->_assign('steelitem', $item['steelitem']);
            $this->_send_json(array(
                'result'    => 'okay',
                'content'   => $this->smarty->fetch('templates/html/item/control_details_view.tpl')
            ));
        }

        $this->_send_json(array('result' => 'error'));
    }
    
    function remove()
    {
        //разрешаем удалять только модераторам и выше
        if(($_SESSION['user']['role_id'] < 5)){
            $ids = Request::GetString('item_ids', $_REQUEST);
            if (empty($ids)) $this->_send_json(array('result' => 'error'));

            $ids = explode(',', $ids);
            //debug("1682", $ids);

            $updated_positions  = array();
            $removed_items      = array();

            $steelitems = new SteelItem();
            $positions  = new SteelPosition();
            foreach($ids as $id)
            {
                $result = $steelitems->GetById($id);

                // 20121217, zharkov: cannot remove real items // запрет на удаление реальных айтемов
                /*if (empty($result) || $result['steelitem']['is_eternal']) 
                {
                    continue;
                }*/

                $result = $steelitems->Remove($id);
                //debug("1682", $result);
                foreach ($result as $row)
                {
                    $position_id    = $row['steelposition_id'];
                    $position       = $positions->GetById($position_id);

                    $updated_positions[$position_id] = array(
                        'qtty'      => empty($position) ? 0 : $position['steelposition']['qtty'],
                        'weight'    => empty($position) ? 0 : $position['steelposition']['weight'],
                        'value'     => empty($position) ? 0 : $position['steelposition']['value'],
                    );                    

                    $removed_items[$row['steelitem_id']] = $position_id;
                }      
            }

            $this->_send_json(array(
                'result'    => 'okay', 
                'items'     => $removed_items,
                'positions' => $updated_positions
            ));
        }else{
            $this->_send_json(array(
                'result'    => 'error'
            ));
            //$this->_message('You do not have enough rights to delete items !', MESSAGE_ERROR);
        }
        
    }
}
