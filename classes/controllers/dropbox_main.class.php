<?php
require_once APP_PATH . 'classes/components/object.class.php';

require_once APP_PATH . 'classes/models/attachment.class.php';
require_once APP_PATH . 'classes/models/biz.class.php';
require_once APP_PATH . 'classes/models/company.class.php';
require_once APP_PATH . 'classes/models/steelitem.class.php';
require_once APP_PATH . 'classes/models/person.class.php';

class MainController extends ApplicationController
{
    function MainController()
    {
        ApplicationController::ApplicationController();
        
        $this->authorize_before_exec['index'] = ROLE_STAFF;
        
        $this->context = true;
    }

    /**
     * Отображает индексную страницу галереи для объекта
     * url: /{object_alias}/{object_id}/gallery
     */
    function index()
    {        
        $object_alias   = Request::GetString('object_alias', $_REQUEST);
        $object_id      = Request::GetInteger('object_id', $_REQUEST);
        
        if (!empty($object_alias) && !empty($object_id))
        {
            $objectcomponent    = new ObjectComponent();
            $page_params        = $objectcomponent->GetPageParams($object_alias, $object_id, 'Dropbox');
            
            $this->page_name    = $page_params['page_name'];
            $this->breadcrumb   = $page_params['breadcrumb'];
            
            $this->_assign('object_stat', $page_params['stat']);
        }
        else
        {
            $this->page_name = 'Dropbox';
            $this->breadcrumb[$this->page_name] = '';
        }        

        $this->breadcrumb[$this->page_name] = '';
        
        $attachments    = new Attachment();
        $rowset         = $attachments->GetListByType('', $object_alias, $object_id);
        
        $this->_assign('list1',          $rowset['data']);
        $this->_assign('object_alias',  $object_alias);
        $this->_assign('object_id',     $object_id);
                
//        $this->_assign('include_mce',           true);
        $this->_assign('include_upload',        true);
        $this->_assign('include_prettyphoto',   true);
        
        $objectcomponent = new ObjectComponent();
        $this->_assign('object_stat', $objectcomponent->GetStatistics($object_alias, $object_id));
        
        $this->js = 'dropbox_index';
        
        $this->_display('index');
    }    
}