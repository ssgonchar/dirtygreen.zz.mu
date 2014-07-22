<?php
require_once APP_PATH . 'classes/models/attachment.class.php';
require_once APP_PATH . 'classes/models/ra.class.php';

class MainPrintController extends ApplicationPrintController
{
    function MainPrintController()
    {
        ApplicationPrintController::ApplicationPrintController();
        
        $this->authorize_before_exec['index']   = ROLE_STAFF;
        $this->authorize_before_exec['view']    = ROLE_STAFF;
    }
    
    /**
     * Отображает страницу со списком
     * url: /ra~print
     * 
     * @version 20130222, d10n
     */
    public function index()
    {
        $modelRA = new RA();
        $data_set = $modelRA->GetList(1, 1000);
        
        $this->_assign('list', $data_set['data']);
        
        $this->_assign('page_name', 'Release Advices');
        $this->_display('index');
    }
    
    /**
     * Отображает страницу просмотра RA
     * url: /ra/{ra_id}~print
     * 
     * @version 20130222, d10n
     */
    public function view()
    {
        $ra_id      = Request::GetInteger('id', $_REQUEST);
        if ($ra_id <= 0) _404();
        
        $modelRA    = new RA();
        $ra         = $modelRA->GetById($ra_id);
        if (empty($ra)) _404();
        
        $ra = $ra['ra'];
        
        if ($ra['is_deleted'] == 1) _404();
        
        $this->_assign('items', $modelRA->GetItems($ra_id));
        
        $objects_list = $modelRA->GetListOfRelatedDocs($ra_id);
        $this->_assign('objects_list', $objects_list);

        foreach ($objects_list as $doc)
        {
            if ($doc['object_alias'] == 'ddt') $ra['has_ddt'] = TRUE;
            if ($doc['object_alias'] == 'cmr') $ra['has_cmr'] = TRUE;
        }
        $this->_assign('ra',    $ra);
        
        $modelAttachment    = new Attachment();
        $attachments_list   = $modelAttachment->GetListByType('', 'ra', $ra_id);
        $this->_assign('attachments_list', $attachments_list['data']);

        $this->_assign('page_name', 'Release Advice No ' . $ra['doc_no']);
        $this->_display('view');
    }

    /**
     * Отображает страницу редактирования RA
     * url: /ra/{id}/edit
     * 
     * @version 20121018, zharkov
     */
    public function edit()
    {
        $ra_id = Request::GetInteger('id', $_REQUEST);
        if (empty($ra_id)) _404();
        
        $modelRA    = new RA();
        $ra         = $modelRA->GetById($ra_id);
        if (empty($ra)) _404();
        
        $ra = $ra['ra'];
                
        // закрытые можно только просматривать
        if ($ra['status_id'] == RA_STATUS_CLOSED) 
        {
            $this->_redirect(array('ra', $ra_id));
        }
        
        // редактировать можно только открытые или модераторами предзакрытые
        if ($ra['status_id'] != RA_STATUS_OPEN && $this->user_role > ROLE_MODERATOR) 
        {
            $this->_redirect(array('ra', $ra_id));            
        }

        
        if (isset($_REQUEST['btn_add_item']))
        {
            $form = $_REQUEST['form'];
            
            // добавляет RA в сессию
            $_SESSION['ra-' . $ra_id]['form'] = $form;
            
            // переход к складу
            //$redirect_url = 'target/ra:' . $ra_id . '/positions/filter/stock:1';
            $redirect_url = 'target/ra:' . $ra_id . '/items';

            $this->_redirect(explode('/', $redirect_url), false);
        }
        else if (isset($_REQUEST['btn_save']))
        {
            $form = $_REQUEST['form'];
            
            $company            = Request::GetString('company', $form);
            $company_id         = Request::GetInteger('company_id', $form);
            $truck_number       = Request::GetString('truck_number', $form);
            $dest_stockholder_id= Request::GetInteger('dest_stockholder_id', $form);
            $destination        = Request::GetString('destination', $form);
            $loading_date       = Request::GetString('loading_date', $form);
            $marking            = Request::GetString('marking', $form);
            $dunnaging          = Request::GetString('dunnaging', $form);
            $weighed_weight     = Request::GetNumeric('weighed_weight', $form);
            $ddt_number         = Request::GetString('ddt_number', $form);
            $ddt_date           = Request::GetDateForDB('ddt_date', $form);
            $ddt_instructions   = Request::GetString('ddt_instructions', $form);
            $coupon             = Request::GetString('coupon', $form);
            $notes              = Request::GetString('notes', $form);
            $consignee          = Request::GetString('consignee', $form);
            $consignee_ref      = Request::GetString('consignee_ref', $form);
            $mm_dimensions      = Request::GetBoolean('mm_dimensions', $form);
            
            $stockholder_id     = 0;    // при сохранении не меняется
            
            if (empty($company))
            {
                $company_id         = 0;
                $form['company_id'] = $company_id;
            }
            else
            {
                // адаптация данных для формы
                $form['company'] = array();
                $form['company']['doc_no']  = $company;
                $form['company']['id']      = $company_id;
            }
            
            $form = array_merge($ra, $form);
            $this->_assign('form', $form);
            
            if (empty($dest_stockholder_id) && empty($destination))
            {
                $this->_message('Destination must be specified !', MESSAGE_ERROR);
            }
            else if (!empty($ddt_number) && empty($ddt_date))
            {
                $this->_message('DDT Date must be specified !', MESSAGE_ERROR);
            }
            else if (empty($ddt_number) && !empty($ddt_date))
            {
                $this->_message('DDT Number must be specified !', MESSAGE_ERROR);
            }
            else if (!empty($ddt_number) && $weighed_weight <= 0)
            {
                $this->_message('Weighed weight must be specified !', MESSAGE_ERROR);
            }
            else
            {
                
                $status_id      = empty($ddt_number) ? RA_STATUS_OPEN : RA_STATUS_PENDING;
                $destination    = empty($dest_stockholder_id) ? $destination : '';
                
                $result = $modelRA->Save($ra_id, $stockholder_id, $dest_stockholder_id, $company_id, $truck_number, $destination, 
                    $loading_date, $marking, $dunnaging, $status_id, 0, $weighed_weight,
                    $ddt_number, $ddt_date, $ddt_instructions,
                    $coupon, $notes, $consignee, $consignee_ref, $mm_dimensions);
                
                if (empty($result))
                {
                    $this->_message('Error saving RA', MESSAGE_ERROR);
                }
                else
                {
                    // если меняется статус документа, desctination или меняются айтемы на варианты, нужно обновить таймлайн
                    $update_timeline = false;

                    if ($ra['status_id'] != $status_id)
                    {
                        $update_timeline = true;
                    }
                    
                    // если меняется взвешенный вес, теоритический вес, desctination или айтемы меняются на варианты,
                    // нужно обновить связанные доеументы
                    $update_related_docs = false;
                    
                    if ($ra['dest_stockholder_id'] != $dest_stockholder_id || $ra['destination'] != $destination)
                    {
                        $update_timeline        = true;
                        $update_related_docs    = true;
                    }
                    
                    if ($ra['weighed_weight'] != $weighed_weight) $update_related_docs = true;
                    

                    // переопределение флага ra_items.is_theor_weight
                    $is_theor_weight_list = isset($form['is_theor_weight']) ? $form['is_theor_weight'] : array();
                    foreach($modelRA->GetItems($ra_id) as $item)
                    {
                        $is_theor_weight = Request::GetInteger($item['id'], $is_theor_weight_list);                        
                        $modelRA->ItemUpdate($ra_id, $item['id'], $is_theor_weight);
                        
                        if ($item['is_theor_weight'] != $is_theor_weight) $update_related_docs = true;
                    }
                                        
                    // переопределение основных Айтемов
                    $primary_items_list = isset($form['primary_items']) ? $form['primary_items'] : array();                   
                    foreach ($primary_items_list as $parent_id => $variant)
                    {
                        $ra_item_id = Request::GetInteger(0, $variant);
                        $parent_id  = intval($parent_id);

                        if ($parent_id != $ra_item_id)
                        {
                            $modelRA->SetPrimaryItem($ra_id, $ra_item_id);
                            
                            $update_timeline        = true;
                            $update_related_docs    = true;                            
                        }                        
                    }
                    
                    // обновить взвешенный вес, обновить связанные документы если изменился главный айтем или теоритический вес
                    if ($update_related_docs)
                    {
                        $modelRA->ItemsRecalculateWW($ra_id);
                        $modelRA->UpdateRelatedDocs($ra_id);
                    }
                    
                    // обновляет timeline для айтемов
                    if ($update_timeline)
                    {
                        foreach($modelRA->GetItems($ra_id) as $item)
                        {
                            if ($item['parent_id'] == 0) $modelRA->ItemSaveTimeline($item['steelitem_id'], $ra_id);
                        }                        
                    }
                    
                    $ra = $modelRA->GetById($ra_id);
                    
                    // формирование PDF-документа
                    $modelRAPdf = new RAPdf($ra['ra']['stock_object_alias']);
                    $modelRAPdf->Generate($ra_id);

                    $this->_message('RA was successfully saved', MESSAGE_OKAY);
                    $this->_redirect(array('ra'));
                }
            }
        }
        else
        {
            $form = $ra;
        }

        $this->page_name                    = 'Edit Release Advice';
        $this->breadcrumb[$ra['doc_no']]    = '/ra/' . $ra['id'];
        $this->breadcrumb[$this->page_name] = '';
        
        $this->_assign('form',          $form);
        $this->_assign('items',         $modelRA->GetItems($ra_id));
        
        $modelStock = new Stock();
        $this->_assign('dest_sholders_list', $modelStock->GetLocations(0, false));
        
        
        $this->_assign('include_ui',    true);
        
        $this->js = 'ra_main';
        
        $this->_display('edit');
    }
    
    /**
     * Добавляет вариант айтема к списку основных айтемов
     * url: /ra/{ra_id}/item/{item_id}/addvariant
     * 
     * @version 20121018, zharkov
     */
    public function addvariant()
    {
        $ra_id      = Request::GetInteger('ra_id', $_REQUEST);
        $ra_item_id = Request::GetInteger('item_id', $_REQUEST);

        if (empty($ra_id)) _404();
        
        $modelRA    = new RA();
        $ra         = $modelRA->GetById($ra_id);
        if (empty($ra)) _404();
        
        $ra                 = $ra['ra'];
        $primary_item       = array();
        foreach ($modelRA->GetItems($ra_id) as $item)
        {
            if ($item['id'] == $ra_item_id)     $primary_item = $item;
        }
        
        // не найден айтем в этом RA
        if (empty($primary_item))
        {
            $this->_message('Item not found', MESSAGE_ERROR);
            $this->_redirect(array('ra', $ra_id, 'edit'));
        }

        if (isset($_REQUEST['btn_save']))
        {
            $selected_ids = isset($_REQUEST['selected_ids']) ? $_REQUEST['selected_ids'] : array();
            
            if (empty($selected_ids))
            {
                $this->_message('Items must be specified!', MESSAGE_ERROR);
            }
            else
            {
                $modelRA->ItemsAdd($ra_item_id, $ra_id, implode(',',$selected_ids));
                
                $this->_message('Variants were successfully added', MESSAGE_OKAY);
                $this->_redirect(array('ra', $ra_id, 'edit'));
            }
        }

        // формирует список доступных айтемов
        $data_set = $modelRA->GetAvailableVariants($ra, $primary_item);
        
        $this->_assign('items', $data_set['list']);
        $this->_assign('ra', $ra);
        
        $this->page_name                    = 'Item Variants';
        $this->breadcrumb[$ra['doc_no']]    = '/ra/' . $ra_id;
        $this->breadcrumb['Edit']           = '/ra/' . $ra_id . '/edit';
        $this->breadcrumb[$this->page_name] = '';
        
        $this->js       = 'ra_addvariant';
        $this->context  = true;
        
        $this->_display('addvariant');
    }
    
    /**
     * Удалеет RA и связанные документы [CMR/DDT] (помечает как удален)
     * url: /ra/{ra_id}/delete
     * 
     * @version 20121207, d10n
     */
    public function delete()
    {
        $ra_id = Request::GetInteger('id', $_REQUEST);        
        if ($ra_id <= 0) _404();
        
        $modelRA    = new RA();
        $ra         = $modelRA->GetById($ra_id);        
        if (!isset($ra['ra']))
        {
            $this->_message('Unknown RA !', MESSAGE_OKAY);
            $this->_redirect(array('ra'));
        }        
                
        $ra = $ra['ra'];
        if ($ra['status_id'] != RA_STATUS_OPEN)
        {
            $this->_message('I can not delete this RA !', MESSAGE_OKAY);
            $this->_redirect(array('ra'));
        }
        
        $modelRA->Remove($ra['id']);
        
        $this->_message('RA was deleted successfully !', MESSAGE_OKAY);
        $this->_redirect(array('ra'));
    }
}