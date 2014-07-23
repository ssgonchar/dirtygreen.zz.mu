<?php
require_once APP_PATH . 'classes/core/Pagination.class.php';
require_once APP_PATH . 'classes/models/attachment.class.php';
require_once APP_PATH . 'classes/models/cmr.class.php';
require_once APP_PATH . 'classes/models/cmr_pdf.class.php';
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
        
        $this->breadcrumb   = array('CMRs' => '/cmr');
        $this->context      = true;
    }

    /**
     * Удаляет
     * url: /cmr/{id}/remove
     * 
     * @version 20120901, zharkov
     */
    public function remove()
    {
        $id = Request::GetInteger('id', $_REQUEST);
        
        $modelCMR = new CMR();
        $modelCMR->Remove($id);
        
        $this->_redirect(array('cmr'));
    }
    
    /**
     * Отображает страницу просмотра
     * url: /cmr/{id}
     * 
     * @version 20120901, zharkov
     */
    public function view()
    {
        $cmr_id = Request::GetInteger('id', $_REQUEST);
        
        if (empty($cmr_id)) _404();
        
        $modelCMR   = new CMR();
        $cmr        = $modelCMR->GetById($cmr_id);
        
        if (empty($cmr)) _404();
        
        $cmr = $cmr['cmr'];
        
        if ($cmr['is_outdated'] == 1 && $cmr['is_deleted'] == 0)
        {
            $modelCMRPdf = new CMRPdf('mam', 'cmr');
            $modelCMRPdf->Generate($cmr_id);
            
            $cmr    = $modelCMR->GetById($cmr_id);
            $cmr    = $cmr['cmr'];
        }
        
        $this->page_name = $cmr['doc_no'];
        $this->breadcrumb[$this->page_name] = '';
        
        $this->js = 'cmr_main';
        $this->_assign('include_ui',    true);
        
        $this->_assign('items', $modelCMR->GetItems($cmr_id));
        $this->_assign('cmr',   $cmr);
        
        $modelAttachment    = new Attachment();
        $attachments_list   = $modelAttachment->GetListByType('', 'cmr', $cmr_id);
        $this->_assign('attachments_list', $attachments_list['data']);
        
        $this->_display('view');
    }
    
    /**
     * Отображает линейный список
     * url: /cmr
     * 
     * @version 20121102, d10n
     */
    public function index()
    {        
        $this->page_name    = 'CMRs';
        $this->breadcrumb   = array($this->page_name => '');

        $modelCMR   = new CMR();
        $rowset     = $modelCMR->GetList(-1, $this->page_no);
		
		$pager = new Pagination();
                
        $this->_assign('pager_pages', $pager->PreparePages($this->page_no, $rowset['count']));
        
		
        
        $this->_assign('list', $rowset['data']);
        $this->_assign('count', $rowset['count']);
        
        $this->context = true;
        $this->_display('index');
    }    
    
    /**
     * Создает набор CMR
     * @url /cmr/add
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
        
        if (isset($ra['has_ddt'])) _404();
        
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
        
        $modelCMR = new CMR();
        
        foreach ($items_by_owner as $owner_id => $item_set)
        {
            $cmr = $modelCMR->Create($ra['id'], $owner_id, $ra['company_id'], $ra['truck_number']);
        }

        $this->_redirect(array('cmr', $cmr['cmr_id'], 'edit'));
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
        $cmr_id         = Request::GetInteger('id', $_REQUEST);
        
        $modelCMR       = new CMR();
        
        if ($cmr_id > 0)
        {
            $cmr = $modelCMR->GetById($cmr_id);
            if (empty($cmr)) _404();
            
            $cmr = $cmr['cmr'];
            
            if ($cmr['is_deleted'] == 1) _404();
        }
        
        if (isset($_REQUEST['btn_dont_save']))
        {
            $this->_redirect(array('cmr'));
//            if ($cmr_id > 0)
//            {
//                $this->_redirect(array('cmr', $cmr_id));
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
            $buyer_name         = Request::GetString('buyer_name', $form, '', 100);
            $buyer_address      = Request::GetString('buyer_address', $form, '', 200);
            $delivery_point     = Request::GetString('delivery_point', $form, '', 500);
            $date               = Request::GetDateForDB('date', $form);
            $truck_number       = Request::GetString('truck_number', $form);
            $transporter_id     = Request::GetInteger('transporter_id', $form);
            $transporter_title  = Request::GetString('transporter_title', $form);
            $product_name       = Request::GetString('product_name', $form);
            
            $form   = array_merge($cmr, $form);
            
            if ($cmr['number'] > 0 && $number <= 0)
            {
                $this->_message('Number must be specified !', MESSAGE_ERROR);
            }
            else if (empty($date))
            {
                $this->_message('Date must be specified !', MESSAGE_ERROR);
            }
            else
            {
                $id             = isset($cmr['id']) ? $cmr['id'] : 0;
                $ra_id          = isset($cmr['id']) ? $cmr['ra_id'] : 0;
                // $number         = isset($cmr['id']) ? $cmr['number_default'] : 0;
                $owner_id       = isset($cmr['id']) ? $cmr['owner_id'] : 0;

                $transporter_id = empty($transporter_title) ? 0 : $transporter_id;
                $attachment_id  = 0;
                
                $result = $modelCMR->Save($id, $ra_id, $owner_id, $number, $buyer_name, $buyer_address,
                    $delivery_point, $date, $truck_number, $transporter_id, $attachment_id, $product_name);
                
                if (empty($result) || isset($result['ErrorCode']))
                {
                    if (isset($result['ErrorCode']))
                    {
                        if ($result['ErrorCode'] == -1)
                        {
                            $this->_message('There is CMR with this number! Please specify another number.', MESSAGE_ERROR);    
                        }
                        else if ($result['ErrorCode'] == -2)
                        {
                            $this->_message('There is DDT with this number! Please specify another number.', MESSAGE_ERROR);    
                        }                        
                    }
                    else
                    {
                        $this->_message('Error saving CMR', MESSAGE_ERROR);
                    }
                }
                else
                {
                    $cmr_id = $result['cmr_id'];
                    
                    // удаляет предыдущий атачмент
                    $modelAttachment = new Attachment();
                    if ($cmr['attachment_id'] > 0) $modelAttachment->Remove($cmr['attachment_id']);
                    
                    // формирование PDF-документа
                    $modelCMRPdf = new CMRPdf('mam', 'cmr');
                    $modelCMRPdf->Generate($cmr_id);
                    
                    $this->_message('CMR was sucessfully saved ', MESSAGE_OKAY);
                    $this->_redirect(array('cmr'));
                }
            }
        }
        else
        {
            $form = array(
                'buyer'             => '',
                'delivery_point'    => '',
            );
            
            if ($cmr_id > 0)
            {
                $form = $cmr;
                $form['transporter_title'] = isset($cmr['transporter']) && isset($cmr['transporter']['title']) ? $cmr['transporter']['title'] : '';
            }
        }
        
        
        if($cmr_id > 0)
        {
            $this->page_name = $cmr['number_default'] > 0 ? 'Edit CMR' : 'New CMR';
            $crumb = $cmr['number_default'] > 0 ? $cmr['doc_no'] : 'new cmr';
            $this->breadcrumb[$crumb]    = '/cmr/' . $cmr['id'];
        }
        else
        {
            $this->page_name = 'New CMR';
        }
        $this->breadcrumb[$this->page_name] = '';
        
        
        $this->_assign('items',         $modelCMR->GetItems($cmr_id));
        
        $this->js = 'cmr_main';
        
        $this->_assign('include_ui',    true);
        $this->_assign('form',          $form);
        $this->_assign('cmr',           $cmr);
        
        $this->_display('edit');
    }
}
