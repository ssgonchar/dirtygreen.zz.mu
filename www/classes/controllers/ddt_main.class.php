<?php
require_once APP_PATH . 'classes/core/Pagination.class.php';
require_once APP_PATH . 'classes/models/attachment.class.php';
require_once APP_PATH . 'classes/models/ddt.class.php';
require_once APP_PATH . 'classes/models/ddt_pdf.class.php';
require_once APP_PATH . 'classes/models/paymenttype.class.php';
require_once APP_PATH . 'classes/models/ra.class.php';

class MainController extends ApplicationController
{
    public function MainController()
    {
        ApplicationController::ApplicationController();
        
        $this->authorize_before_exec['index']   = ROLE_STAFF;
        $this->authorize_before_exec['edit']    = ROLE_STAFF;
        $this->authorize_before_exec['add']     = ROLE_STAFF;
        $this->authorize_before_exec['view']    = ROLE_STAFF;
        $this->authorize_before_exec['remove']  = ROLE_STAFF;
        
        $this->breadcrumb   = array('DDTs' => '/ddt');
        $this->context      = true;
    }

    /**
     * Удаляет
     * url: /ddt/{id}/remove
     * 
     * @version 20120901, zharkov
     */
    public function remove()
    {
        $id = Request::GetInteger('id', $_REQUEST);
        
        $modelDDT = new DDT();
        $modelDDT->Remove($id);
        
        $this->_redirect(array('ddt'));
    }
    
    /**
     * Отображает страницу просмотра
     * url: /ddt/{id}
     * 
     * @version 20120901, zharkov
     */
    public function view()
    {
        $ddt_id = Request::GetInteger('id', $_REQUEST);
        
        if (empty($ddt_id)) _404();
        
        $modelDDT   = new DDT();
        $ddt        = $modelDDT->GetById($ddt_id);
        
        if (empty($ddt)) _404();
        
        $ddt = $ddt['ddt'];
        
        if ($ddt['is_outdated'] == 1 && $ddt['is_deleted'] == 0)
        {
            $modelDDTPdf = new DDTPdf('mam', 'ddt');
            $modelDDTPdf->Generate($ddt_id);
            
            $ddt    = $modelDDT->GetById($ddt_id);
            $ddt    = $ddt['ddt'];
        }
        
        $this->page_name = $ddt['doc_no'];
        $this->breadcrumb[$this->page_name] = '';
        
        $this->js = 'ddt_main';
        $this->_assign('include_ui',    true);
        
        $this->_assign('items', $modelDDT->GetItems($ddt_id));
        $this->_assign('ddt',   $ddt);
        
        $modelAttachment    = new Attachment();
        $attachments_list   = $modelAttachment->GetListByType('', 'ddt', $ddt_id);
        $this->_assign('attachments_list', $attachments_list['data']);
        
        $this->_display('view');
    }
    
    /**
     * Отображает линейный список
     * url: /ddt
     * 
     * @version 20121102, d10n
     */
    public function index()
    {        
        $this->page_name    = 'DDTs';
        $this->breadcrumb   = array($this->page_name => '');

        $modelDDT   = new DDT();
        $rowset     = $modelDDT->GetList(-1, $this->page_no);
        
        $pager = new Pagination();
        $this->_assign('pager_pages', $pager->PreparePages($this->page_no, $rowset['count']));
        
        $this->_assign('list', $rowset['data']);
        $this->_assign('count', $rowset['count']);
        
        $this->context = true;
        $this->_display('index');
    }    
    
    /**
     * Создает набор DDT
     * @url /ddt/add
     * 
     * @version 20121002, d10n
     */
    public function add()
    {
        $source_doc     = Request::GetString('source_doc',      $_REQUEST);
        $source_doc_id  = Request::GetInteger('source_doc_id',  $_REQUEST);
        
        if (!in_array($source_doc, array('ra', 'stock'))) _404();
        
        $modelRA    = new RA();
        $ra         = $modelRA->GetById($source_doc_id);
        
        if (!isset($ra['ra'])) _404();
        
        $ra = $ra['ra'];
        
        $objects_list = $modelRA->GetListOfRelatedDocs($ra['id']);
        foreach ($objects_list as $doc)
        {
            if ($doc['object_alias'] == 'ddt') $ra['has_ddt'] = TRUE;
            if ($doc['object_alias'] == 'cmr') $ra['has_cmr'] = TRUE;
        }
        
        if (isset($ra['has_cmr'])) _404();
        
        $ra_items = $modelRA->GetItems($ra['id']);
        
        $items_by_owner = array();
        
        foreach ($ra_items as $ra_item)
        {
            $steelitem = $ra_item['steelitem'];
            
            if ($steelitem['owner_id'] <= 0 || $steelitem['owner_id'] == PLATESAHEAD_OWNER_ID) continue;
            
            $steelitem_owner_id = $steelitem['owner_id'];
            
            if (!isset($items_by_owner[$steelitem_owner_id]))
            {
                $items_by_owner[$steelitem_owner_id] = array();
            }
            
            $items_by_owner[$steelitem_owner_id][] = $steelitem;
        }
        
        if (empty($items_by_owner))
        {
            $this->_message('There are no item owners', MESSAGE_ERROR);
            $this->_redirect(array($source_doc, $source_doc_id, 'view'));
        }
        
        $modelDDT = new DDT();
        
        foreach ($items_by_owner as $owner_id => $item_set)
        {
            $ddt = $modelDDT->Create($ra['id'], $owner_id, $ra['company_id'], $ra['truck_number']);
        }
        
        $this->_redirect(array('ddt', $ddt['ddt_id'], 'edit'));
    }
    
    /**
     * Отображает страницу редактирования компании
     * url: /item/{id}/edit
     * 
     * @version 20120901, zharkov
     */
    public function edit()
    {
        $source_doc     = Request::GetString('source_doc',      $_REQUEST);
        $source_doc_id  = Request::GetInteger('source_doc_id',  $_REQUEST);
        $ddt_id         = Request::GetInteger('id', $_REQUEST);
        
        $modelDDT       = new DDT();
        
        if ($ddt_id > 0)
        {
            $ddt = $modelDDT->GetById($ddt_id);
            if (empty($ddt)) _404();
            
            $ddt = $ddt['ddt'];
            
            if ($ddt['is_deleted'] == 1) _404();
        }
        
        if (isset($_REQUEST['btn_dont_save']))
        {
            $this->_redirect(array('ddt'));
//            if ($ddt_id > 0)
//            {
//                $this->_redirect(array('ddt', $ddt_id));
//            }
//            else
//            {
//                $this->_redirect(array($source_doc, 'view', $source_doc_id));
//            }
        }
        
        $form = isset($_REQUEST['form']) ? $_REQUEST['form'] : array();
        
        if (isset($_REQUEST['btn_save']))
        {
            $number             = Request::GetString('number', $form);
            $buyer              = Request::GetString('buyer', $form, '', 500);
            $delivery_point     = Request::GetString('delivery_point', $form, '', 500);
            $date               = Request::GetDateForDB('date', $form);
            $iva                = Request::GetString('iva', $form);
            $paymenttype_id     = Request::GetInteger('paymenttype_id', $form);
            $causale_id         = Request::GetInteger('causale_id', $form);
            $porto_id           = Request::GetInteger('porto_id', $form);
            $truck_number       = Request::GetString('truck_number', $form);
            $transporter_id     = Request::GetInteger('transporter_id', $form);
            $transporter_title  = Request::GetString('transporter_title', $form);
            $dest_type_id       = Request::GetInteger('dest_type_id', $form);
            
            $form               = array_merge($ddt, $form);            
            
            if ($ddt['number'] > 0 && $number <= 0)
            {
                $this->_message('Number must be specified !', MESSAGE_ERROR);
            }
            else if (empty($date))
            {
                $this->_message('Date must be specified !', MESSAGE_ERROR);
            }
            else
            {
                $id             = isset($ddt['id']) ? $ddt['id'] : 0;
                $ra_id          = isset($ddt['id']) ? $ddt['ra_id'] : 0;
                // $number         = isset($ddt['id']) ? $ddt['number_default'] : 0;
                $owner_id       = isset($ddt['id']) ? $ddt['owner_id'] : 0;

                $transporter_id = empty($transporter_title) ? 0 : $transporter_id;
                $attachment_id  = 0;
                
                $result = $modelDDT->Save($id, $ra_id, $owner_id, $number, $buyer, $delivery_point, $date, $iva,  $paymenttype_id, $causale_id, $porto_id, $truck_number, $transporter_id, $attachment_id, $dest_type_id);
                
                if (empty($result) || isset($result['ErrorCode']))
                {
                    if (isset($result['ErrorCode']))
                    {
                        if ($result['ErrorCode'] == -1)
                        {
                            $this->_message('There is DDT with this number! Please specify another number.', MESSAGE_ERROR);    
                        }
                        else if ($result['ErrorCode'] == -2)
                        {
                            $this->_message('There is CMR with this number! Please specify another number.', MESSAGE_ERROR);    
                        }                        
                    }
                    else
                    {
                        $this->_message('Error saving DDT', MESSAGE_ERROR);
                    }
                }
                else
                {
                    $ddt_id = $result['ddt_id'];
                    
                    // удаляет предыдущий атачмент
                    $modelAttachment = new Attachment();
                    if ($ddt['attachment_id'] > 0) $modelAttachment->Remove($ddt['attachment_id']);
                    
                    // формирование PDF-документа
                    $modelDDTPdf = new DDTPdf('mam', 'ddt');
                    $modelDDTPdf->Generate($ddt_id);
                    
                    $this->_message('DDT was sucessfully saved ', MESSAGE_OKAY);
                    $this->_redirect(array('ddt'));
                }
            }
        }
        else
        {
            $form = array(
                'paymenttype_id'    => 0,
                'causale_id'        => 0,
                'porto_id'          => 0,
                'buyer'             => '',
                'delivery_point'    => '',
                'iva'               => '',
            );
            
            if ($ddt_id > 0)
            {
                $form = $ddt;
                $form['transporter_title'] = isset($ddt['transporter']) && isset($ddt['transporter']['title']) ? $ddt['transporter']['title'] : '';
            }
        }
        
        
        if($ddt_id > 0)
        {
            $this->page_name = 'Edit DDT';
            $this->breadcrumb[$ddt['doc_no']]    = '/ddt/' . $ddt['id'];
        }
        else
        {
            $this->page_name = 'New DDT';
        }
        $this->breadcrumb[$this->page_name] = '';
        
        // start формирование списка типов
        $paymenttypes_list  = array();
        $modelPaymentType   = new PaymentType();
        
        $list = $modelPaymentType->GetList();
        
        foreach ($list as $row)
        {
            $pt = $row['paymenttype'];
            
            if (empty($pt['title'])) continue;
            
            $paymenttypes_list[] = array('id' => $pt['id'], 'name' => $pt['title']);
        }
        $this->_assign('paymenttypes_list', $paymenttypes_list);
        // end формирование списка типов
        
        // start формирование списка causale
        $causale_list = array(
            array('id' => 1, 'name' => 'c/to lavorazione'),
            array('id' => 2, 'name' => 'c/to vendita'),
        );
        $this->_assign('causale_list', $causale_list);
        
        // start формирование списка porto
        $porto_list = array(
            array('id' => 1, 'name' => 'f.co partenza'),
            array('id' => 2, 'name' => 'f.co destino'),
        );
        $this->_assign('porto_list', $porto_list);
        
        $this->_assign('items',         $modelDDT->GetItems($ddt_id));
        
        $this->js = 'ddt_main';
        
        $this->_assign('include_ui',    true);
        $this->_assign('form',          $form);
        $this->_assign('ddt',           $ddt);
        
        $this->_display('edit');
    }
}
