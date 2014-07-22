<?php
require_once APP_PATH . 'classes/models/objective.class.php';

class MainController extends ApplicationController
{
    function MainController()
    {
        ApplicationController::ApplicationController();
        
        $this->authorize_before_exec['index']   = ROLE_STAFF;
        $this->authorize_before_exec['view']    = ROLE_STAFF;
        $this->authorize_before_exec['edit']    = ROLE_STAFF;
        $this->authorize_before_exec['add']     = ROLE_STAFF;        
        $this->authorize_before_exec['remove']  = ROLE_STAFF;        
        
        $this->breadcrumb   = array('Objectives' => '/objectives');
        $this->context      = true;
    }

    /**
     * Возвращает заголовок квартала
     * 
     * @param mixed $quarter
     */
    function _get_quarter_title($quarter)
    {
        if ($quarter == 1) return 'JAN - MAR';
        if ($quarter == 2) return 'APR - JUN';
        if ($quarter == 3) return 'JUL - SEP';
        if ($quarter == 4) return 'OCT - DEC';
    }
    
    /**
     * Удаляет цель
     * /objective/{id}/remove
     * 
     * @version 201200909, zharkov: пока не работает
     */
    function remove()
    {
        $objective_id   = Request::GetInteger('id', $_REQUEST);
        
        $modelObjective = new Objective();
        $objective      = $modelObjective->GetById($objective_id);
        
        if (empty($objective)) 
        {
            $this->_message('Objective not found!', MESSAGE_ERROR);
            $this->_redirect(array('objective'));
        }
                
        $result = $modelObjective->Remove($objective_id);
        if (empty($result))
        {
            $this->_message('This objective cannot be removed!', MESSAGE_ERROR);
            $this->_redirect(array('objectives', $objective['objective']['year']));            
        }
        else
        {
            $this->_message('Objective was successfully removed', MESSAGE_OKAY);
            
            if ($result['count_remaining'] > 0)
            {
                $this->_redirect(array('objectives', $objective['objective']['year']));
            }
            else
            {
                $this->_redirect(array('objectives'));            
            }
        }
    }
    
    /**
     * Отображает список целей
     * url: /objectives
     */
    function index()
    {        
        $year       = Request::GetInteger('year', $_REQUEST);
        $quarter    = Request::GetInteger('quarter', $_REQUEST);
        
        $objectives = new Objective();
        $this->_assign('list',      $objectives->GetList($year, $quarter));
        $this->_assign('years',     $objectives->GetYears());
        $this->_assign('quarters',  $objectives->GetQuarters($year));
        
        $this->_assign('year',      $year);
        $this->_assign('quarter',   $quarter);

        $this->page_name = 'Objectives';
        if (!empty($year)) 
        {
            $this->breadcrumb[$year] = '/objectives/' . $year;
            if (!empty($quarter)) $this->breadcrumb[$this->_get_quarter_title($quarter)] = '/objectives/' . $year . '/' . $quarter;
            
            $this->page_name = 'Objectives For Period';
        }
        $this->breadcrumb[$this->page_name] = '';
        
        $this->_display('index');
    }    
    
    /**
     * Отображает страница добавления новой цели
     * url: /objective/add
     */
    function add()
    {
        $this->edit();
    }
    
    /**
     * Отображает страницу редактирования цели
     * url: /objective/{id}/edit
     */
    function edit()
    {
        $objective_id = Request::GetInteger('id', $_REQUEST);

        if ($objective_id > 0)
        {
            $objectives = new Objective();
            $objective  = $objectives->GetById($objective_id);            
            if (empty($objective)) _404();            
        }        
        

        if (isset($_REQUEST['btn_save']))
        {
            $form = $_REQUEST['form'];
            
            $year           = Request::GetInteger('year', $form);
            $quarter        = Request::GetInteger('quarter', $form);
            $title          = Request::GetHtmlString('title', $form);
            $description    = Request::GetHtmlString('description', $form);
            
            if (empty($title))
            {
                $this->_message('Title must be specified !', MESSAGE_ERROR);
            }
            else
            {
                $objectives = new Objective();
                $result     = $objectives->Save($objective_id, $year, $quarter, $title, $description);

                if (empty($result) || isset($result['ErrorCode']))
                {
                    $this->_message('Error while saving objective !', MESSAGE_ERROR);
                }
                else
                {
                    $this->_message('Objective was successfully saved', MESSAGE_OKAY);
                    $this->_redirect(array('objectives', $year, $quarter));
                }
            }            
        }
        else
        {
            $form = $objective_id > 0 ? $objective['objective'] : array();
        }
                
        $this->page_name = $objective_id > 0 ? 'Edit Objective' : 'New Objective';
        $this->breadcrumb[$this->page_name] = '';
        
        $this->_assign('form', $form);
        $this->_assign('year', date('Y'));
        
        $this->_display('edit');        
    }
    
    /**
     * Отображает страницу просмотра цели
     * url: /objective/{objective_id}
     */
    function view()
    {
        $objective_id = Request::GetInteger('id', $_REQUEST);
        if (empty($objective_id)) _404();
        
        $objectives = new Objective();
        $objective  = $objectives->GetById($objective_id);
        if (empty($objective)) _404();
        
        $objective = $objective['objective'];
        
        $this->_assign('form', $objective);
        
        $this->breadcrumb[$objective['year']] = '/objectives/' . $objective['year'];
        $this->breadcrumb[$this->_get_quarter_title($objective['quarter'])] = '/objectives/' . $objective['year'] . '/' . $objective['quarter'];

        $this->page_name = $objective['title'];
        $this->breadcrumb[$this->page_name] = '';
        
        $this->_display('view');
    }
}