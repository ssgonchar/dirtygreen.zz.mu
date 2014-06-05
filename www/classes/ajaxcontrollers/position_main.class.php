<?php

require_once APP_PATH . 'classes/models/stock.class.php';
require_once APP_PATH . 'classes/models/steelgrade.class.php';
require_once APP_PATH . 'classes/models/steelposition.class.php';
require_once APP_PATH . 'classes/models/steelitem.class.php';

class MainAjaxController extends ApplicationAjaxController
{
    function MainAjaxController()
    {
        ApplicationAjaxController::ApplicationAjaxController();        
        
        $this->authorize_before_exec['quickcancel'] = ROLE_STAFF;
        $this->authorize_before_exec['quickedit']   = ROLE_STAFF;
        $this->authorize_before_exec['quicksave']   = ROLE_STAFF;
        $this->authorize_before_exec['remove']      = ROLE_STAFF;
        $this->authorize_before_exec['removeitems'] = ROLE_STAFF;
        $this->authorize_before_exec['find']        = ROLE_STAFF;
        $this->authorize_before_exec['additem']     = ROLE_STAFF;
        $this->authorize_before_exec['getcontext']  = ROLE_STAFF;
        $this->authorize_before_exec['getitems']    = ROLE_STAFF;
        $this->authorize_before_exec['getsuitable'] = ROLE_STAFF;
    }

    /**
     * Get suitable positions by item params // Подбирает подходящие позиции по параметрам айтема
     * url: /position/getsuitable
     */
    function getsuitable()
    {
        $stock_id       = Request::GetInteger('stock_id', $_REQUEST);
        $stockholder_id = Request::GetInteger('stockholder_id', $_REQUEST);
        $steelgrade_id  = Request::GetInteger('steelgrade_id', $_REQUEST);
        $thickness      = Request::GetNumeric('thickness', $_REQUEST);
        $width          = Request::GetNumeric('width', $_REQUEST);
        $length         = Request::GetNumeric('length', $_REQUEST);
        
        $modelStock = new Stock();
        $stock      = $modelStock->GetById($stock_id);
        
        if (empty($stock)) $this->_send_json(array('result' => 'error'));
        $stock = $stock['stock'];

        $width   = $stock['dimension_unit'] == 'in' ? $width * 25.4 : $width;
        $length  = $stock['dimension_unit'] == 'in' ? $length * 25.4 : $length;        
        
        if ($width > 0) $width = ($width - 50) . '-' . ($width + 50);
        if ($length > 0) $length = ($length - 100) . '-' . ($length + 100);

        $companies  = new Company();
        $company    = $companies->GetById($stockholder_id);
        $company    = $company['company'];
        
        $modelSteelPosition = new SteelPosition();       
        $result             = array();
        
        foreach ($modelSteelPosition->GetList(0, $stock_id, $company['location_id'], '', $steelgrade_id, $thickness, $width, $length, 0, '', '', '') as $row)
        {
            $row        = $row['steelposition'];
            $result[]   = array(
                'value' => $row['id'],
                'name'  => $row['id'] . ' : ' . $row['steelgrade']['title'] . ' ' . $row['thickness'] . ' x ' . $row['width'] . ' x ' . $row['length'] . ' ' . $row['deliverytime']['title']
            );
        }

        $this->_send_json(array(
            'result'    => 'okay', 
            'positions' => $result
        ));                
    }
    
    /**
     * Get position items list // Возвращает список айтемов позиции
     * url: /position/getitems
     */
    function getitems()
    {
        $position_id = Request::GetInteger('position_id', $_REQUEST);	//получаем данные из аякс запроса show_items()
        $is_revision = Request::GetInteger('is_revision', $_REQUEST);
        
        $modelSteelPosition = new SteelPosition();
        $position           = $modelSteelPosition->GetById($position_id);       //возвращает список позиция по Id
        if (empty($position)) $this->_send_json(array('result' => 'error'));	//если список пустой отсылаем ошибку
        
	    $steelitems=$modelSteelPosition->GetItems($position_id, true);	// Возвращает список айтемов для позиции
		
	    $modelSteelItem = new SteelItem();		//создаем обьект класса SteelItem()
	    for($i=0; $i<count($steelitems); $i++)	//циклом проходим массив со списком итемов
	    {						//Возвращаем список связанных документов
	        $docs = $modelSteelItem->GetRelatedDocs($steelitems[$i]['steelitem_id']);  
	        $steelitems[$i]['doc']=$docs;		//записываем этот список в массив "doc"
	    }
		//передаем данные в smarty
        $this->_assign('position',      $position['steelposition']);	//данные о позиции
        $this->_assign('is_revision',   $is_revision);	//номер ревизии склада, т.е. сохраненное состояние склада на определенный момент. (!не использовать, !!не проверено)
        $this->_assign('items',         $steelitems);			//список итемов + данные по ним
		//dg($steelitems);
        $this->_send_json(array(					//отправляем данные по текущему ajax запросу
            'result'    => 'okay', 	//fetch('путь к шаблону')
            'content'   => $this->smarty->fetch('templates/html/position/control_items.tpl')	//json.content
        ));
    }
    
    /**
     * Get position context // Возвращает контекстную информацию о позиции
     * url: /position/getcontext
     */
    function getcontext()
    {
        $position_id = Request::GetInteger('position_id', $_REQUEST);
        $is_revision = Request::GetInteger('is_revision', $_REQUEST);
        
        $modelSteelPosition = new SteelPosition();
        $position           = $modelSteelPosition->GetById($position_id);        //возвращает список позиция по Id
        if (empty($position)) $this->_send_json(array('result' => 'error'));
        
        $this->_assign('position',      $position['steelposition']);
        $this->_assign('is_revision',   $is_revision);
        
        //d10n 20130102: item pictures // Возвращает список айтемов для позиции
        $items = $modelSteelPosition->GetItems($position_id, true);
        if (!empty($items))	//если итемов в позиции нет:
        {
            $modelPicture       = new Picture();
            $attachments_list   = array();
            foreach ($items as $steelitem)
            {
                if (!isset($steelitem['steelitem'])) continue;
                
                $attachments = $modelPicture->GetList('item', $steelitem['steelitem_id']);

                if (!isset($attachments['data'])) continue;
                
                $attachments_list = array_merge($attachments_list, $attachments['data']);                
            }

            $this->_assign('attachments', $attachments_list);
        }
	    //отправляем данные по текущему ajax запросу
        $this->_send_json(array(
            'result'    => 'okay', 	
            'content'   => $this->smarty->fetch('templates/html/position/control_context.tpl')
        ));        
    }
    
    
    /**
     * Get position quick edit block // Возвращает блок быстрого редактирования позиции
     * url: /position/quickedit/{position_id}
     */
    function quickedit()
    {
        $position_id = Request::GetInteger('position_id', $_REQUEST);
        if (empty($position_id)) $this->_send_json(array('result' => 'error'));        
                
        $positions  = new SteelPosition();
        $position   = $positions->GetById($position_id);
        if (empty($position)) $this->_send_json(array('result' => 'error'));
        
        SteelPosition::Lock($position_id);

        $this->_assign('position', $position);

        $steelgrades = new SteelGrade();
        $this->_assign('steelgrades', $steelgrades->GetList());
        
        $this->_send_json(array(
            'result'    => 'okay', 
            'content'   => $this->smarty->fetch('templates/html/position/control_quickedit.tpl')
        ));
    }    
    
    /**
     * Cancel quick edit
     * url: /position/quickcancel/{position_id}
     */
    function quickcancel()
    {
        $position_id = Request::GetInteger('position_id', $_REQUEST);
        if (empty($position_id)) $this->_send_json(array('result' => 'error'));        
                
        $positions  = new SteelPosition();
        $position   = $positions->GetById($position_id);
        if (empty($position)) $this->_send_json(array('result' => 'error'));

        SteelPosition::Unlock($position_id);
        
        $this->_assign('position', $position);
        $this->_send_json(array(
            'result'    => 'okay', 
            'content'   => $this->smarty->fetch('templates/html/position/control_quickview.tpl')
        ));
    }    

    /**
     * Quick save position
     * url: /position/quicksave/{position_id}
     */
    function quicksave()
    {
        $position_id    = Request::GetInteger('position_id', $_REQUEST);
        $steelgrade_id  = Request::GetInteger('steelgrade_id', $_REQUEST);
        $thickness      = Request::GetNumeric('thickness', $_REQUEST);
        $width          = Request::GetNumeric('width', $_REQUEST);
        $length         = Request::GetNumeric('length', $_REQUEST);
        $unitweight     = Request::GetNumeric('unitweight', $_REQUEST);
        $weight         = Request::GetNumeric('weight', $_REQUEST);
        $price          = Request::GetNumeric('price', $_REQUEST);
        $value          = Request::GetNumeric('value', $_REQUEST);
        $delivery_time  = Request::GetString('delivery_time', $_REQUEST);
        $notes          = Request::GetString('notes', $_REQUEST);
        $internal_notes = Request::GetString('internal_notes', $_REQUEST);

        if (empty($position_id)) $this->_send_json(array('result' => 'error'));        
                
        $modelSteelPosition = new SteelPosition();
        $position           = $modelSteelPosition->GetById($position_id);

        if (empty($position)) $this->_send_json(array('result' => 'error'));

        $result = $modelSteelPosition->Save($position_id, $position['steelposition']['stock_id'], $position['steelposition']['product_id'], 
                    $position['steelposition']['biz_id'], 0, 0, $position['steelposition']['dimension_unit'], 
                    $position['steelposition']['weight_unit'], $position['steelposition']['price_unit'], 
                    $position['steelposition']['currency'], $steelgrade_id, $thickness, 
                    $width, $length, $unitweight, $position['steelposition']['qtty'], $weight, $price, $value, 
                    $delivery_time, $notes, $internal_notes);
        
        $position = $modelSteelPosition->GetById($position_id);
        
        foreach ($position['steelposition']['items'] as $item)
        {
            Cache::ClearTag('steelitem-' . $item['steelitem_id']);
        }
        
        SteelPosition::Unlock($position_id);
        
        $this->_assign('position', $position);        
        $this->_send_json(array(
            'result'    => 'okay', 
            'content'   => $this->smarty->fetch('templates/html/position/control_quickview.tpl'),
            'position'  => $position['steelposition']
        ));
    }    

    /**
     * @deprecated
     * 
     * Quick remove position
     * url: /position/remove/{position_id}
     */
    function deprecated_remove()
    {
        $position_id = Request::GetInteger('position_id', $_REQUEST);

        if (empty($position_id)) $this->_send_json(array('result' => 'error'));        
                
        $positions  = new SteelPosition();
        $position   = $positions->GetById($position_id);

        if (empty($position)) $this->_send_json(array('result' => 'error'));

        $result = $positions->Remove($position_id);
        if (empty($result)) $this->_send_json(array('result' => 'error'));
        
        $this->_send_json(array('result' => 'okay'));
    }    
    
    /**
     * Remove selected items // Удаляет выделенные айтемы
     * url: /position/removeitems
     * 
     * @version 20120429, zharkov: remove link to position // убрал привязку к позиции
     * @version 20121217, zharkov: cannot remove real items // запрет на удаление реальных айтемов
     */
    function removeitems()
    {
        $ids = Request::GetString('ids', $_REQUEST);
        if (empty($ids)) $this->_send_json(array('result' => 'error'));

        $ids = explode(',', $ids);

        $updated_positions  = array();
        $removed_items      = array();
        
        $steelitems = new SteelItem();
        $positions  = new SteelPosition();
        foreach($ids as $id)
        {
            $result = $steelitems->GetById($id);
            
            // 20121217, zharkov: cannot remove real items // запрет на удаление реальных айтемов
            if (empty($result) || $result['steelitem']['is_eternal']) 
            {
                continue;
            }
            
            $result = $steelitems->Remove($id);
            
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
    }
    
    /**
     * Get position search result
     * /position/find
     */
    function find()
    {
        $item_id = Request::GetInteger('item_id', $_REQUEST);
        if (empty($item_id)) $this->_send_json(array('result' => 'error', 'code' => -1));
        
        $stockholder_id = Request::GetInteger('location_id', $_REQUEST);
        $steelgrade_id  = Request::GetInteger('location_id', $_REQUEST);
        $thickness      = Request::GetNumeric('thickness', $_REQUEST);
        $width          = Request::GetNumeric('width', $_REQUEST);
        $length         = Request::GetNumeric('length', $_REQUEST);
        
        $modelItem  = new SteelItem();
        $item       = $modelItem->GetById($item_id);
        if (empty($item)) $this->_send_json(array('result' => 'error', 'code' => -2));
        
        $item = $item['steelitem'];

        if ($item['dimension_unit'] == 'mm' && $item['weight_unit'] == 'mt')
        {
            $unitweight = $thickness * $width * $length * 0.000000008;
        }
        else if ($item['dimension_unit'] == 'in' && $item['weight_unit'] == 'lb')
        {
            $unitweight = $thickness * $width * $length * 0.2836;
        }
        
        $modelPosition  = new SteelPosition();
        $position       = $modelPosition->GetById($item['steelposition_id']);
        if (empty($position)) $this->_send_json(array('result' => 'error', 'code' => -3));
        
        $position = $position['steelposition'];
        
        $modelCompany = new Company();
        $company    = $modelCompany->GetById($stockholder_id);
        $company    = $company['company'];        
        
        if ($width > 0) $width = ($width - 50) . '-' . ($width + 50);
        if ($length > 0) $length = ($length - 100) . '-' . ($length + 100);


        $postions   = $modelPosition->GetList(0, $position['stock_id'], $company['location_id'], '', $steelgrade_id, $thickness, $width, $length, 0, '', '', '');
        $list       = array();
        foreach ($postions as $position)
        {
            $position   = $position['steelposition'];
            $list[]     = array(
                'id'    => $position['id'],
                'name'  => ($position['steelgrade']['title'] . ' ' . $position['thickness'] . ' x ' . $position['width'] . ' x ' . $position['length'] . ' ' . $position['qtty'] . ' pcs')
            );
        }

        
        $this->_send_json(array(
            'result'    => 'okay', 
            'positions' => $list
        ));        
    }
    
    /**
     * Add items into position edit form // Добавляет айтемы в таблицу айтемов при редактировании позиции
     * url: /position/additem
     */
    function additem()
    {
        $position_id    = Request::GetInteger('position_id', $_REQUEST);
        $next_row_index = Request::GetInteger('next_row_index', $_REQUEST);
        
        $positions  = new SteelPosition();
        $position   = $positions->GetById($position_id);
        
        if (empty($position)) $this->_send_json(array('result' => 'error'));
        
        $this->_assign('position',          $position);
        $this->_assign('next_row_index',    $next_row_index);
        
        if ($position['steelposition']['dimension_unit'] == 'in')
        {
            $this->_assign('include_nominal', true);
        }
        
        $steelgrades = new SteelGrade();
        $this->_assign('steelgrades', $steelgrades->GetList());
        
        $stocks = new Stock();
        $this->_assign('locations', $stocks->GetLocations($position['steelposition']['stock_id']));
        
        $bizes = new Biz();
        $this->_assign('suppliers', $bizes->GetCompanies($position['steelposition']['biz_id'], 'prod'));
        
        $companies = new Company();
        $this->_assign('owners', $companies->GetMaMList());        
        
        $this->_send_json(array(
            'result'        => 'okay', 
            'main'          => $this->smarty->fetch('templates/html/position/control_item_main.tpl'),
            'location'      => $this->smarty->fetch('templates/html/position/control_item_location.tpl'),
            'status'        => $this->smarty->fetch('templates/html/position/control_item_status.tpl'),
            'chemical'      => $this->smarty->fetch('templates/html/position/control_item_chemical.tpl'),
            'mechanical'    => $this->smarty->fetch('templates/html/position/control_item_mechanical.tpl')
        ));        
    }
    
    /**
     * get steelgrades for stock
     * 
     * @link /position/getsteelgrades
     * 
     * @version 20130816, sasha
     */
    function deprecated_getsteelgrades()
    {
        $stock_id = Request::GetInteger('stock_id', $_REQUEST);
        
        $modelStock         = new Stock();
        $steelgrade_list    = $modelStock->GetSteelgrades($stock_id);
        
        $this->_assign('steelgrade_list', $steelgrade_list);
        
        if (!empty($steelgrade_list)) 
        {
            $this->_send_json(array('result' => 'okay', 'content' => $this->smarty->fetch('templates/html/position/control_steelgrades.tpl'))); 
        }
        else
        {
            $this->_send_json(array('result' => 'error'));
        }    
    }
    
    /**
     *changevisibility
     *
     *Вызывает метод ChangeVisibility модели Steelposition
     *
     *@version 20140521
     *@author Gonchar
     */
    function changevisibility()
    {
        $position_id = Request::GetInteger('position_id', $_REQUEST);
        $hidden = Request::GetBoolean('hidden', $_REQUEST);
	
	$modelSteelposition = new SteelPosition();
	
	($hidden == 'true') ? $hidden = '1' : $hidden = '0';
	
	$result = $modelSteelposition->ChangeVisibility($position_id, $hidden);
	
	//dg($result);
	
	$this->_send_json(array('result' => 'okay', 'content' => array($position_id, $result)));
    }    
    
    function saveprice()
    {
        $position_id = Request::GetInteger('position_id', $_REQUEST);
        $price = Request::GetString('price', $_REQUEST);        
        
        $modelPosition = new SteelPosition();
        $modelPosition->ChangePrice($position_id, $price);
    }
    
}
