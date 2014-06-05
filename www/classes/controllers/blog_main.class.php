<?php
require_once APP_PATH . 'classes/core/Pagination.class.php';
require_once APP_PATH . 'classes/models/blog.class.php';

class MainController extends ApplicationController
{
    public function MainController()
    {
        ApplicationController::ApplicationController();
        
        $this->authorize_before_exec['index']   = ROLE_STAFF;
        
        $this->breadcrumb   = array('Blog' => '/blog');
        $this->context      = true;
    }

    /**
     * Отображает страницу ленты для объекта
     * url: /{object_alias}/{object_id}/blog
     * url: /{object_alias}/{object_id}/blog/filter/{filter}
     * 
     * @version 20130213, zharkov
     */
    public function index()
    {
        $this->page_name = 'Blog';
        
        $object_alias   = Request::GetString('object_alias', $_REQUEST);
        $object_id      = Request::GetInteger('object_id', $_REQUEST);
        
        // if (!in_array($object_alias, array('biz'))) _404();
        
        $form = isset($_REQUEST['form']) ? $_REQUEST['form'] : array();
        
        if (isset($_REQUEST['btn_filter']))
        {
            $tmp_types  = '';
            if (isset($form['type']))
            {
                foreach ($form['type'] as $key => $type_id)
                {
                    $type_id = Request::GetInteger($key, $form['type']);
                    if ($type_id <= 0) continue;
                    
                    $tmp_types = $tmp_types . (empty($tmp_types) ? '' : ',') . $type_id;
                }
            }
            
            $date_from  = Request::GetDateForDB('date_from', $form);
            $date_to    = Request::GetDateForDB('date_to', $form);
            $keyword    = Request::GetString('keyword', $form);
            $subbiz     = Request::GetInteger('subbiz', $form);
            $mainbiz     = Request::GetInteger('mainbiz', $form);
            $allsubbiz     = Request::GetInteger('allsubbiz', $form);

            $filter     = (empty($tmp_types) ? '' : 'type:' . $tmp_types . ';')
                        . (!empty($date_from) ? 'datefrom:' . trim(str_replace('00:00:00', '', $date_from)) . ';' : '')
                        . (!empty($date_to) ? 'dateto:' . trim(str_replace('00:00:00', '', $date_to)) . ';' : '')
                        . (empty($keyword) ? '' : 'keyword:' . $keyword . ';')
                        . (empty($subbiz) ? '' : 'subbiz:1;')
						. (empty($mainbiz) ? '' : 'mainbiz:1;')
						. (empty($allsubbiz) ? '' : 'allsubbiz:1;')
						;
            
            //$filter = preg_replace('#\s+#i', '', $filter);
            if (empty($filter))
            {
                $this->_redirect(array($object_alias, $object_id, 'blog'));   
            }
            else
            {
                $this->_redirect(array($object_alias, $object_id, 'blog', 'filter', $filter), false);    
            }
        }
        
        
        $filter         = Request::GetString('filter', $_REQUEST);
        $filter         = urldecode($filter);
        $filter_params  = array();
        
        if (!empty($filter))
        {
            foreach (explode(';', $filter) as $row)
            {
                if (empty($row)) continue;
                
                $param = explode(':', $row);
                $filter_params[$param[0]] = Request::GetHtmlString(1, $param);
            }
            
            if (isset($filter_params['type']))
            {
                 $exploded = explode(',', $filter_params['type']);
                 $filter_params['type'] = array_combine($exploded, $exploded);
            }
                        
            $this->_assign('filter', $filter_params);
        }
        
        // отсев пустых значений фильтра
        $filter_params = array_filter($filter_params, create_function('$item', 'return !empty($item);'));
        
        $modelBlog  = new Blog();
        $rowset     = $modelBlog->GetBlogData($object_alias, $object_id, $filter_params, $this->page_no);

        $corePagenation = new Pagination();
        $this->_assign('pager_pages',   $corePagenation->PreparePages($this->page_no, $rowset['count']));
        $this->_assign('count',         $rowset['count']);
      
        $this->_assign('list',          $rowset['data']);
        $this->_assign('count',         $rowset['count']);
        $this->_assign('object_alias',  $object_alias);
        $this->_assign('object_id',     $object_id);
        $this->_assign('include_ui',    true);
        $this->_assign('include_mce',   true);
       
        $objectComponent = new ObjectComponent();
        $data = $objectComponent->GetPageParams($object_alias, $object_id, 'Blog');
        
        if ($object_alias == 'biz')
        {
            $modelBiz = new Biz();
            $this->_assign('biz', $modelBiz->GetById($object_id));
            
            $this->page_name = $data['doc_no'] . ' - Blog';            
        }
                
        $this->breadcrumb = $data['breadcrumb'];
        $this->_assign('doc_no', $data['doc_no']);
  
        $this->layout   = 'rcolumn';
        $this->context  = true;
        $this->rcontext = true;          
        $this->js       = 'blog_index';
        
        $this->topcontext       = 'index';
        
        $this->app_object_alias = $object_alias;
        $this->app_object_id    = $object_id;
        
        $this->_display('index');
    }
}