<?php
require_once APP_PATH . 'classes/core/Pagination.class.php';

require_once APP_PATH . 'classes/models/attachment.class.php';
require_once APP_PATH . 'classes/models/inddt.class.php';
require_once APP_PATH . 'classes/models/oc.class.php';
require_once APP_PATH . 'classes/models/oc_standard.class.php';
require_once APP_PATH . 'classes/models/qc.class.php';

class MainController extends ApplicationController
{
    public function MainController()
    {
        ApplicationController::ApplicationController();
        
        $this->authorize_before_exec['edit']    = ROLE_STAFF;
        $this->authorize_before_exec['delete']  = ROLE_STAFF;
        $this->authorize_before_exec['index']   = ROLE_STAFF;
        $this->authorize_before_exec['view']    = ROLE_STAFF;
        
        $this->breadcrumb   = array('Original Certificates' => '/oc');
        $this->context      = true;
    }
    
    /**
     * Форма создания и редактирования OC
     * url: /oc/add
     * url: /oc/add/{object_alias}:{object_id}
     * url: /oc/{oc_id}/edit
     * 
     * @version 20130215, d10
     */
    public function edit()
    {
        $source_doc_alias   = Request::GetString('source_doc',      $_REQUEST);
        $source_doc_id      = Request::GetString('source_doc_id',   $_REQUEST); // может быть несколько через запятую
        $oc_id              = Request::GetInteger('id', $_REQUEST);
        
        $items  = array();
        $oc     = array(
            'company_id'        => 0,
            'kind'              => 0,
            'standard_id'       => 0,
            'state_of_supply'   => 0
        );
        
        if ($oc_id > 0)
        {
            $modelOC    = new OC();
            $oc         = $modelOC->GetById($oc_id);
            if (empty($oc)) _404();
            
            $oc                     = $oc['oc'];
            $oc['company_title']    = isset($oc['company']) ? $oc['company']['doc_no'] : '';
            
            //$items = $modelOC->GetItems($oc_id);
            $items = $oc['items_list'];
        }
        else if (in_array($source_doc_alias, array('inddt')) && !empty($source_doc_id))
        {
            if ($source_doc_alias == 'inddt')
            {
                $modelInDDT = new InDDT();
                foreach(explode(',', $source_doc_id) as $inddt_id)
                {
                    $inddt = $modelInDDT->GetById($inddt_id);
                    if (empty($inddt)) continue;
                    
                    $inddt = $inddt['inddt'];
                    $oc['company_id']       = $inddt['company_id'];
                    $oc['company_title']    = isset($inddt['company']) ? $inddt['company']['title'] : '';
                    
                    foreach ($modelInDDT->GetItems($inddt_id) as $item)
                    {
                        $items[$item['steelitem_id']] = $item;
                    }
                }
            }
        }
        
        $is_saving          = isset($_REQUEST['btn_save']);
        $is_adding_items    = isset($_REQUEST['btn_additems']);
        
        if ($is_saving || $is_adding_items)
        {
            $form = $_REQUEST['form'];
            
            $company_id     = Request::GetInteger('company_id', $form);
            $company_title  = Request::GetString('company_title', $form);
            $number         = Request::GetString('number', $form);
            $date           = Request::GetDateForDB('date', $form);
            $kind           = Request::GetInteger('kind', $form);
            $standard_id    = Request::GetInteger('standard_id', $form);
            $standard_new   = Request::GetString('standard_new', $form);
            $state_of_supply= Request::GetInteger('state_of_supply', $form);
            
            $modelOC    = new OC();
            $okay_flag  = true;

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
                else if (empty($company_id) && empty($company_title))
                {
                    $this->_message('Company must be specified !', MESSAGE_ERROR);
                    $okay_flag = false;
                }
            }
            
            if ($okay_flag)
            {
                
                if (empty($standard_id))
                {
                    $modelOCStandard    = new OCStandard();
                    $standard_id      = $modelOCStandard->GetOCStandardId($standard_new);
                }
                
                $oc_saved = $modelOC->Save($oc_id, $number, $date, $company_id, $kind, $standard_id, $state_of_supply);
                
                if (!array_key_exists('oc', $oc_saved))
                {
                    $this->_message('Error saving original certificate !', MESSAGE_ERROR);
                    $okay_flag = false;
                }
                
                $oc_saved = $oc_saved['oc'];
            }
            else
            {
                $oc = array_merge($oc, $form);
            }            
            
            $request_items = isset($_REQUEST['items']) ? $_REQUEST['items'] : array();
            
            foreach ($items as $key => $item)
            {
                $steelitem_id = $item['steelitem_id'];
                
                if (isset($request_items[$steelitem_id]))
                {
                    $items[$key]['checked'] = Request::GetInteger('checked', $request_items[$steelitem_id]);
                }
            }
            
            if ($okay_flag)
            {
                foreach ($request_items as $steelitem_id => $row)
                {
                    if ($oc_id > 0 || isset($row['checked']))
                    {
                        $modelOC->SaveItem($oc_saved['id'], $steelitem_id);
                    }
                }
                
                if ($is_adding_items)
                {
                    $this->_redirect(array('target', 'oc:' . $oc_saved['id'], 'items'), false);
                }
                else
                {
                    $this->_message('Original Certificate was successfully saved', MESSAGE_OKAY);
                    //$this->_redirect(array('oc', 'filter', 'company:' . $company_id), false);
                    $this->_redirect(array('oc'), false);
                }
            }            
        }
        
        $this->page_name    = empty($oc_id) ? 'New Original Certificate' : 'Edit Original Certificate';
        $this->js           = 'oc_main';
        
        $this->breadcrumb[$this->page_name] = '';
        
        $modelOCStandard    = new OCStandard();
        $this->_assign('standards', $modelOCStandard->GetList());
        
        $this->_assign('form',          $oc);
        $this->_assign('items',         $items);
        $this->_assign('firstitem',     current($items));
        
        $this->_assign('invoice_id',    $oc_id);
        $this->_assign('include_ui',    true);
        $this->_assign('include_prettyphoto', true);
        
        $this->_display('edit');
    }
   
    /**
     * Список OC
     * url: /oc
     * url: /oc/filter/{filter}
     * 
     * @version 20130215, d10n
     */
    public function index()
    {
        $filter         = Request::GetString('filter', $_REQUEST);
        $filter         = urldecode($filter);
        $filter_params  = array();
        
        $filter = explode(';', $filter);
		//print_r($filter);
		//die();
        foreach ($filter as $row)
        {
            if (empty($row)) continue;
            
            $param = explode(':', $row);
            $filter_params[$param[0]] = Request::GetHtmlString(1, $param);
        }
       
        $company_id = Request::GetInteger('company', $filter_params);
        $date_from  = null;
        //$date_to    = null;
        $date_to    = Request::GetDateForDB('date_to', $filter_params);
        $number     = Request::GetString('number', $filter_params);        
        $plate_id     = Request::GetString('plate_id', $filter_params);        
       
	   
		//print_r($date_to);
		
        $modelOC    = new OC();
        $rowset     = $modelOC->GetList($company_id, $date_from, $date_to, $number, $plate_id, $this->page_no);
        
        $this->page_name    = 'Original Certificates';
        $this->breadcrumb   = array($this->page_name => '');

        $this->_assign('company_id',    $company_id);
        $this->_assign('date_from',     $date_from);
        $this->_assign('date_to',       $date_to);
        $this->_assign('number',        $number);
        $this->_assign('plate_id',      $plate_id);
        
        $this->_assign('list',      $rowset['data']);
        $this->_assign('count',     $rowset['count']);

        $pager = new Pagination();
        $this->_assign('pager_pages', $pager->PreparePages($this->page_no, $rowset['count']));
        
        $this->_assign('include_prettyphoto',   true);
        if (!empty($rowset['data'])) $this->_assign('filter', true);
        
		$this->js           = 'oc_index';
		
        $this->_display('index');
    }
    
	public function info()
	{
		/*Отлавливаем вызов*/
		//$debug = debug_backtrace();
		print_r($_SESSION['msg_error']);
	}
function free()
{
echo $_SERVER['SCRIPT_FILENAME'];
// Обратите внимание, что оператор !== не существовал до версии 4.0.0-RC2

if ($handle = opendir('http://213.130.21.120/pma/')) {
    echo "Дескриптор каталога: $handle\n";
    echo "Файлы:\n";

    /* Именно этот способ чтения элементов каталога является правильным. */
    while (false !== ($file = readdir($handle))) { 
        echo "$file\n";
    }

    /* Этот способ НЕВЕРЕН. */
    while ($file = readdir($handle)) { 
        echo "$file\n";
    }

    closedir($handle); 
}
/*
$fp = fopen(".htaccess", "a"); // Открываем файл в режиме записи 
$mytext = "Это строку необходимо нам записать\r\n"; // Исходная строка
$test = fwrite($fp, $mytext); // Запись в файл
if ($test) echo 'Данные в файл успешно занесены.';
else echo 'Ошибка при записи в файл.';
fclose($fp); //Закрытие файла
*/
}	
    /**
     * Удаляет OC
     * url: /oc/{oc_id}/delete
     * 
     * @version 20130215, d10n
     */
    public function delete()
    {
        $id = Request::GetInteger('id',  $_REQUEST);

        if ($id <= 0) _404();

        $modelOC    = new OC();
        $oc         = $modelOC->GetById($id);
        if (empty($oc)) _404();
        
        foreach ($modelOC->GetItems($id) as $row)
        {
            $modelOC->RemoveItem($id, $row['steelitem_id']);
        }
        
        $modelOC->Remove($id);
        
        $this->_message('Original Certificate was successfully removed', MESSAGE_OKAY);
        
        $this->_redirect(array('oc'));
    }
    
    /**
     * Отображает страницу просмотра OC
     * @url /oc/{oc_id}
     * 
     * @version 20130215, d10n
     */
    public function view()
    {
        $oc_id = Request::GetInteger('id', $_REQUEST);

        $modelOC    = new OC();
        $oc         = $modelOC->GetById($oc_id);
        if (empty($oc)) _404();
        $oc = $oc['oc'];

        $objectcomponent    = new ObjectComponent();
        $page_params        = $objectcomponent->GetPageParams('oc', $oc_id);

        $this->page_name    = $page_params['page_name'];
        $this->breadcrumb   = $page_params['breadcrumb'];
        
        $this->_assign('object_stat',   $page_params['stat']);
        $this->_assign('form',          $oc);
        
        //$items = $modelOC->GetItems($oc_id);
        $items = $oc['items_list'];
        
        $modelAttachment    = new Attachment();
        $attachments_list   = $modelAttachment->GetListByType('', 'oc', $oc_id);
        $this->_assign('attachments_list', $attachments_list['data']);
        
        $this->_assign('items',         $items);
        $this->_assign('firstitem',     current($items));
        
        $this->js = 'oc_main';
        
        $this->_display('view');
    }
}