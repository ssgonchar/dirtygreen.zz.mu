<?php
require_once APP_PATH . 'classes/core/Pagination.class.php';
require_once APP_PATH . 'classes/models/attachment.class.php';
require_once APP_PATH . 'classes/models/company.class.php';
require_once APP_PATH . 'classes/models/inddt.class.php';
require_once APP_PATH . 'classes/models/stock.class.php';

class MainController extends ApplicationController
{
    public function MainController()
    {
        ApplicationController::ApplicationController();
        
        $this->authorize_before_exec['index']   = ROLE_STAFF;
        $this->authorize_before_exec['edit']    = ROLE_STAFF;
        $this->authorize_before_exec['add']     = ROLE_STAFF;
        $this->authorize_before_exec['view']    = ROLE_STAFF;
        $this->authorize_before_exec['remove']  = ROLE_ADMIN;        
        
        $this->breadcrumb   = array('IN DDTs' => '/inddt');
        $this->context      = true;
    }
    
    /**
     * Удаляет документ
     * url: /inddt/{id}/remove
     * 
     * @version 20121218, zharkov
     */
    function remove()
    {
        $id = Request::GetInteger('id', $_REQUEST);        
        if (empty($id)) $this->_redirect(array('inddt'));

        $modelInDDT = new InDDT();
        $in_ddt     = $modelInDDT->GetById($id);
        
        if (empty($in_ddt))
        {
            $this->_message('In DDT not found !', MESSAGE_ERROR);
        }
        else
        {
            $modelInDDT->Remove($id);
            $this->_message('In DDT was delete successfully', MESSAGE_OKAY);
        }
        
        $this->_redirect(array('inddt'));
    }
    
    /**
     * Страница линейного списка документов
     * url: /inddt
     * 
     * @version 20121212, d10n
     */
    public function index()
    {        
        $this->page_name    = 'IN DDTs';
        $this->breadcrumb   = array($this->page_name => '');

        $modelInDDT = new InDDT();
        $rowset     = $modelInDDT->GetList($this->page_no);
        
        $pager = new Pagination();
        $this->_assign('pager_pages', $pager->PreparePages($this->page_no, $rowset['count']));
        
        $this->_assign('list', $rowset['data']);
        $this->_assign('count', $rowset['count']);
        if (!empty($rowset['data'])) $this->_assign('filter', true);
        
        $this->context = true;
        $this->_display('index');
    }
    
    /**
     * Страница просмотра детальной информации
     * url: /inddt/{id}
     * 
     * @version 20121212, d10n
     */
    public function view()
    {
        debug('1671', $_REQUEST);
        $inddt_id = Request::GetInteger('id', $_REQUEST);        
        if (empty($inddt_id)) _404();
        
        $modelInDDT = new InDDT();
        $inddt      = $modelInDDT->GetById($inddt_id);        
        if (empty($inddt)) _404();
        
        $inddt = $inddt['inddt'];
        
        $this->_assign('form',  $inddt);
        $this->_assign('items', $modelInDDT->GetItems($inddt_id));

        $objectcomponent    = new ObjectComponent();
        $page_params        = $objectcomponent->GetPageParams('inddt', $inddt_id);
        
        $this->page_name    = $page_params['page_name'];
        $this->breadcrumb   = $page_params['breadcrumb'];
        
        $this->_assign('object_stat', $page_params['stat']);
        
        $modelAttachment    = new Attachment();
        $attachments_list   = $modelAttachment->GetListByType('', 'inddt', $inddt_id);
        $this->_assign('attachments_list', $attachments_list['data']);
        //debug('1682', $attachments_list);
        $this->_display('view');
    }
    
    /**
     * Страница создания документа
     * @url /inddt/add
     * 
     * @version 20121212, d10n
     */
    public function add()
    {
        if (isset($_REQUEST['id'])) _404();
        
        $this->edit();
    }
    
    /**
     * Страница редактирования документа
     * url: /inddt/{id}/edit
     * 
     * @version 20121212, d10n
     */
    public function edit()
    {
        $inddt_id = Request::GetInteger('id', $_REQUEST);        

        if ($inddt_id > 0)
        {
            $modelInDDT = new InDDT();
            $inddt      = $modelInDDT->GetById($inddt_id);            
            if (empty($inddt)) _404();
            
            $inddt                  = $inddt['inddt'];
            $inddt['company_title'] = isset($inddt['company']) ? $inddt['company']['doc_no'] : '';            
            
            $items = $modelInDDT->GetItems($inddt_id);
        }
        else
        {
            $inddt  = array();
            $items  = array();
        }

        $is_saving          = isset($_REQUEST['btn_save']);
        $is_adding_items    = isset($_REQUEST['btn_additem']);
                
        if ($is_saving || $is_adding_items)
        {
            $form = isset($_REQUEST['form']) ? $_REQUEST['form'] : array();

            $number         = Request::GetString('number', $form);
            $date           = Request::GetDateForDB('date', $form);
            $company_name   = Request::GetString('company', $form);
            $company_id     = Request::GetInteger('company_id', $form);
            $owner_id       = Request::GetInteger('owner_id', $form);
            $status_id      = Request::GetInteger('status_id', $form);
            
            $modelInDDT     = new InDDT();
            $okay_flag      = true;
            
            if (empty($owner_id))
            {
                $this->_message('Owner must be specified !', MESSAGE_ERROR);
                $okay_flag = false;
            }            
            
            if ($is_saving)
            {
                if (empty($number))
                {
                    $this->_message('Number must be specified !', MESSAGE_ERROR);
                    $okay_flag = false;
                }
                else if (empty($date))
                {
                    $this->_message('Date must be specified !', MESSAGE_ERROR);
                    $okay_flag = false;
                }
                else if (empty($company_id) && empty($company_name))
                {
                    $this->_message('Company must be specified !', MESSAGE_ERROR);
                    $okay_flag = false;
                }
            }
            
            if ($okay_flag)
            {
                $result = $modelInDDT->Save($inddt_id, $number, $date, $company_id, $owner_id, $status_id);
                
                if (empty($result))
                {
                    $this->_message('Error saving incoming DDT (undefined error) !', MESSAGE_ERROR);
                    $okay_flag = false;
                }
                else if (isset($result['ErrorCode']))
                {
                    $this->_message('Error saving incoming DDT (duplicate number & date) !', MESSAGE_ERROR);
                    $okay_flag = false;
                }
            }
            else
            {
                $form['date']   = $date;
                $inddt          = array_merge($inddt, $form);
            }            
            
            $request_items = isset($_REQUEST['items']) ? $_REQUEST['items'] : array();
            
            foreach ($items as $key => $item)
            {
                $steelitem_id = $item['steelitem_id'];

                if (isset($request_items[$steelitem_id]))
                {
                    $items[$key]['stockholder_id']          = Request::GetInteger('stockholder_id', $request_items[$steelitem_id]);
                    $request_status_id                      = Request::GetInteger('status_id', $request_items[$steelitem_id], -1);
                    
                    if ($request_status_id > -1) $items[$key]['steelitem']['status_id']  = $request_status_id;
                }
            }

            if ($okay_flag)
            {
                foreach ($items as $key => $row)
                {
                    if ($inddt_id > 0 || isset($row['checked']))
                    {
                        $stockholder_id     = Request::GetInteger('stockholder_id', $items[$key]);                        
                        $status_id          = Request::GetInteger('status_id', $items[$key]['steelitem']);
                        $steelitem_id       = Request::GetInteger('steelitem_id', $row);
                        $steelposition_id   = Request::GetInteger('steelposition_id', $items[$key]['steelitem']);

                        $modelInDDT->SaveItem($result['id'], $steelitem_id, $stockholder_id, $status_id, $steelposition_id);
                    }                    
                }
                
                if ($is_adding_items)
                {
                    $this->_redirect(array('target', 'inddt:' . $result['id'], 'items'), false);
                }
                else
                {
                    $this->_message('Incoming DDT was successfully saved', MESSAGE_OKAY);
                    $this->_redirect(array('inddt#inddt-' . $result['id']), false);
                    //$this->_redirect(array('inddt', 'filter', 'company:' . $company_id), false);
                }
            }                        
        }
        

        $modelCompany = new Company();
        $this->_assign('owners', $modelCompany->GetMaMList());

        $modelStock = new Stock();
        $this->_assign('stockholders', $modelStock->GetLocations(0, false));
        

        $this->page_name    = empty($inddt_id) ? 'New InDDT' : 'Edit InDDT';
        $this->js           = 'inddt_main';
                
        if($inddt_id > 0) $this->breadcrumb[$inddt['doc_no']]    = '/inddt/' . $inddt['id'];
        $this->breadcrumb[$this->page_name] = $this->page_name;
//dg($inddt);
        $this->_assign('form',          $inddt);
        $this->_assign('items',         $items);
        $this->_assign('firstitem',     current($items));
        
        $this->_assign('inddt_id',      $inddt_id);
        $this->_assign('include_ui',    true);        
        
        $this->_display('edit');
    }
}
