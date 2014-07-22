<?php
require_once APP_PATH . 'classes/models/activity.class.php';
require_once APP_PATH . 'classes/models/city.class.php';
require_once APP_PATH . 'classes/models/country.class.php';
require_once APP_PATH . 'classes/models/department.class.php';
require_once APP_PATH . 'classes/models/jobposition.class.php';
require_once APP_PATH . 'classes/models/location.class.php';
require_once APP_PATH . 'classes/models/region.class.php';
require_once APP_PATH . 'classes/models/team.class.php';
require_once APP_PATH . 'classes/models/steelgrade.class.php';

class MainController extends ApplicationController
{
    function MainController()
    {
        ApplicationController::ApplicationController();
        
        $this->authorize_before_exec['index']               = ROLE_STAFF;
        $this->authorize_before_exec['cities']              = ROLE_STAFF;
        $this->authorize_before_exec['countries']           = ROLE_STAFF;        
        $this->authorize_before_exec['steelgrades']         = ROLE_STAFF;
        $this->authorize_before_exec['deletesteelgrade']    = ROLE_STAFF;        
        $this->authorize_before_exec['locations']           = ROLE_STAFF;
        $this->authorize_before_exec['deletelocations']     = ROLE_STAFF;        
        $this->authorize_before_exec['jobpositions']        = ROLE_STAFF;
        $this->authorize_before_exec['deletejobposition']   = ROLE_STAFF;        
        $this->authorize_before_exec['departments']         = ROLE_STAFF;
        $this->authorize_before_exec['deletedepartment']    = ROLE_STAFF;        
        $this->authorize_before_exec['activities']          = ROLE_STAFF;
        $this->authorize_before_exec['deleteactivities']    = ROLE_STAFF;
        $this->authorize_before_exec['teams']               = ROLE_STAFF;
        $this->authorize_before_exec['addteam']             = ROLE_STAFF;
        $this->authorize_before_exec['editteam']            = ROLE_STAFF;
        $this->authorize_before_exec['deleteteam']          = ROLE_STAFF;
        $this->authorize_before_exec['teams']               = ROLE_STAFF;
        $this->authorize_before_exec['regions']             = ROLE_STAFF;
        
        $this->breadcrumb   = array('Directories' => '/directories');
        $this->context      = true;
    }

    /**
     * Отображает индексную страницу справочников
     * url: /directories
     */
    function index()
    {
        $this->page_name = 'Directories';
        $this->_display('index');
    }    

    /**
     * Отображает страницу списка городов
     * url: /directory/cities/{region_id}
     */
    function cities()
    {
        $region_id = Request::GetInteger('id', $_REQUEST);
        
        $regions    = new Region();
        $region     = $regions->GetById($region_id);        
        if (empty($region)) _404();
        
        $region     = $region['region'];
        $country_id = $region['country_id'];

        $countries  = new Country();
        $country    = $countries->GetById($country_id);
        
        $this->page_name = 'Cities';
        $this->breadcrumb[$country['country']['title']] = '/directory/countries';
        $this->breadcrumb[$region['title']]             = '/directory/regions/' . $country_id;
        $this->breadcrumb[$this->page_name]             = '';

        $cities = new City();
        $this->_assign('list', $cities->GetList($region_id));
        
        $this->_assign('region_id',     $region_id);
        $this->_assign('regions',       $regions->GetList($country_id));

        $this->_assign('country_id',    $country_id);
        $this->_assign('countries',     $countries->GetList());
        
        $this->context  = true;       
        $this->js       = 'directory_cities';
        
        $this->_display('cities');
    }
    
    /**
     * Отображает страницу списка регионов
     * url: /directory/regions/{country_id}
     */
    function regions()
    {
        $country_id = Request::GetInteger('id', $_REQUEST);
        
        $countries  = new Country();
        $country    = $countries->GetById($country_id);
        
        if (empty($country)) _404();

        
        $this->page_name = 'Regions';
        $this->breadcrumb[$country['country']['title']] = '/directory/countries';
        $this->breadcrumb[$this->page_name] = '';

        $regions = new Region();
        $this->_assign('list', $regions->GetList($country_id));
        
        $this->_assign('country_id',    $country_id);
        $this->_assign('countries',     $countries->GetList());
        
        $this->context  = true;       
        $this->js       = 'directory_regions';
        
        $this->_display('regions');        
    }
    
    /**
     * Отображает страницу списка стран
     * url: /directory/countries
     */
    function countries()
    {        
        $this->page_name = 'Countries';
        $this->breadcrumb[$this->page_name] = '';

        $countries = new Country();
        $this->_assign('list', $countries->GetList());
        
        $this->context  = true;       
        $this->js       = 'directory_countries';
        
        $this->_display('countries');
    }    


    /**
     * Отображает страницу добавления команды
     * url: /directory/addteam
     */
    function addteam()
    {
        $this->editteam();
    }
    
    function editteam()
    {
        $team_id = Request::GetInteger('id', $_REQUEST);
        
        if (isset($_REQUEST['btn_save']))
        {
            $form   = $_REQUEST['form'];
            $users  = $_REQUEST['selected_users'];
            
            $selected_users = array();
            foreach ($users as $key => $value) if ($value > 0) $selected_users[] = array('user_id' => $key);
            
            $title          = Request::GetString('title', $form);
            $email          = Request::GetString('email', $form);
            $description    = Request::GetString('description', $form);
            
            if (empty($title))
            {
                $this->_message('Title must be specified', MESSAGE_ERROR);
            }
            else if (empty($email) || !preg_match("/^[0-9a-zA-Z_\\.\\-]+@[0-9a-zA-Z_\\.\\-]+?\\.[a-zA-Z]+$/", $email))
            {
                $this->_message('Email must be specified', MESSAGE_ERROR);
            }
            else if (empty($selected_users))
            {
                $this->_message('Team players must be specified', MESSAGE_ERROR);
            }
            else
            {
                $teams  = new Team();
                $result = $teams->Save($team_id, $title, $description, $email);
                
                foreach ($teams->GetUsers($result['id']) as $row)
                {
                    $delete_flag = true;
                    foreach ($selected_users as $key => $row1)
                    {
                        if ($row1['user_id'] == $row['user_id'])
                        {
                            $delete_flag = false;
                            unset($selected_users[$key]);
                        }
                    }
                    
                    if ($delete_flag) $teams->RemoveUser($result['id'], $row['user_id']);
                }
                
                foreach ($selected_users as $row) $teams->AddUser($result['id'], $row['user_id']);                
                Cache::ClearTag('team-' . $result['id'] . '-users');
                
                $this->_redirect(array('directory', 'teams'));
            }
        }
        else
        {
            $this->breadcrumb['Teams'] = '/directory/teams';
            if (empty($team_id))
            {
                $this->page_name = 'New Team';
                $this->breadcrumb[$this->page_name] = '/directory/addteam';
                
                $form = array();
            }
            else
            {
                $this->page_name = 'Edit Team';
                $this->breadcrumb[$this->page_name] = '/directory/editteam/' . $team_id;
                
                $teams = new Team();
                $team           = $teams->GetById($team_id);
                $form           = $team['team'];
                $selected_users = $teams->GetUsers($team_id);
            }            
        }
        
        
        $users  = new User();
        $mam    = $users->GetMamList();
        
        if (isset($selected_users) && !empty($selected_users))
        {
            foreach ($mam as $key => $row)
            {
                foreach ($selected_users as $row1)
                {
                    if ($row['user']['id'] == $row1['user_id'])
                    {
                        $mam[$key]['selected'] = true;
                        break;
                    }
                }
            }
        }
        
        $this->_assign('form',  $form);
        $this->_assign('mam',   $mam);
        
        $this->js = 'team_edit';
        
        $this->_display('teamedit');
    }
    
    /**
     * Удаляет команду
     * url: /directory/deleteteam/{id}
     */
    function deleteteam()
    {
        $id = Request::GetInteger('id', $_REQUEST);
        
        $teams  = new Team();
        $result = $teams->Remove($id);
        
        if (empty($result) || isset($result['ErrorCode']))
        {
            $this->_message('Team is used', MESSAGE_ERROR);
        }
        else
        {
            $this->_message('Team was successfully removed', MESSAGE_OKAY);
        }
        
        $this->_redirect(array('directory', 'teams'));
    }
    
    
    /**
     * Отображает страницу списка команд
     * url: /directory/teams
     */
    function teams()
    {
        $this->page_name = 'Teams';
        $this->breadcrumb[$this->page_name] = '/directory/teams';
        
        $teams  = new Team();
        $list   = $teams->GetList();
        
        $left   = array();
        $right  = array();
        foreach ($list as $key => $row)
        {
            if ($key >= count($list) / 2)
            {
                $right[] = $row;
            }
            else
            {
                $left[] = $row;
            }
        }
        
        $this->_assign('left', $left);
        $this->_assign('right', $right);
        
        $this->_display('teams');        
    }

    /**
     * Отображает страницу со списком отделов
     * url: /directory/activities
     */
    function activities()
    {
        $parent_id = Request::GetInteger('id', $_REQUEST);
        
        $this->page_name = 'Activities';
        $this->breadcrumb[$this->page_name] = '/directory/activities';
        
        $activities = new Activity();
        $this->_assign('parent_id',     $parent_id);
        $this->_assign('activities',    $activities->GetTree());
        $this->_assign('list',          $activities->GetList($parent_id));
        
        $this->js = 'directory_activities';        
        $this->_display('activities');
    }    
    
    /**
     * Удаляет отдел
     * url: /directory/deletedepartment/{id}
     */
    function deletedepartment()
    {
        $id = Request::GetInteger('id', $_REQUEST);
        
        $departments    = new Department();
        $result         = $departments->Remove($id);
        
        if (empty($result) || isset($result['ErrorCode']))
        {
            $this->_message('Department is used', MESSAGE_ERROR);
        }
        else
        {
            $this->_message('Department was successfully removed', MESSAGE_OKAY);
        }
        
        $this->_redirect(array('directory', 'departments'));
    }
    
    /**
     * Отображает страницу со списком отделов
     * url: /directory/departments
     */
    function departments()
    {
        if (isset($_REQUEST['btn_save']))
        {
            $department = new Department();
            foreach ($_REQUEST['title'] as $id => $value)
            {
                $title      = trim(Request::GetString($id, $_REQUEST['title']));
                
                if (empty($id) && empty($title)) continue;
                if (empty($title))
                {
                    $this->_message('Title must be specified for id ' . $id, MESSAGE_ERROR);
                    continue;
                }
                
                $department->Save($id, $title);
            }
            
            $this->_message('Departments have been successfully saved', MESSAGE_OKAY);
            $this->_redirect(array('directory', 'departments'));
        }
        
        $this->page_name = 'Departments';
        $this->breadcrumb[$this->page_name] = '/directory/departments';
        
        $departments = new Department();
        $this->_assign('list', $departments->GetList());
        
        $this->_display('departments');
    }    
    
    /**
     * Удаляет должность
     * url: /directory/deletejobposition/{id}
     */
    function deletejobposition()
    {
        $id = Request::GetInteger('id', $_REQUEST);
        
        $jobpositions   = new JobPosition();
        $result         = $jobpositions->Remove($id);
        
        if (empty($result) || isset($result['ErrorCode']))
        {
            $this->_message('Position is used', MESSAGE_ERROR);
        }
        else
        {
            $this->_message('Position was successfully removed', MESSAGE_OKAY);
        }
        
        $this->_redirect(array('directory', 'jobpositions'));
    }
    
    /**
     * Отображает страницу со списком должностей
     * url: /directory/jobpositions
     */
    function jobpositions()
    {
        if (isset($_REQUEST['btn_save']))
        {
            $jobposition = new JobPosition();
            foreach ($_REQUEST['title'] as $id => $value)
            {
                $title      = trim(Request::GetString($id, $_REQUEST['title']));
                
                if (empty($id) && empty($title)) continue;
                if (empty($title))
                {
                    $this->_message('Title must be specified for id ' . $id, MESSAGE_ERROR);
                    continue;
                }
                
                $jobposition->Save($id, $title);
            }
            
            $this->_message('Positions have been successfully saved', MESSAGE_OKAY);
            $this->_redirect(array('directory', 'jobpositions'));
        }
        
        $this->page_name = 'Positions';
        $this->breadcrumb[$this->page_name] = '/directory/jobpositions';
        
        $jobpositions = new JobPosition();
        $this->_assign('list', $jobpositions->GetList());
        
        $this->_display('jobpositions');
    }
    
    /**
     * Удаляет марку стали
     * url: /directory/deletesteelgrade/{id}
     */
    function deletesteelgrade()
    {
        $steelgrade_id  = Request::GetInteger('id', $_REQUEST);
        
        $steelgrades    = new SteelGrade();
        $result         = $steelgrades->Remove($steelgrade_id);
        
        if (empty($result) || isset($result['ErrorCode']))
        {
            $this->_message('Steel grade is used', MESSAGE_ERROR);
        }
        else
        {
            $this->_message('Steel grade was successfully removed', MESSAGE_OKAY);
        }
        
        $this->_redirect(array('directory', 'steelgrades'));
    }
    
    /**
     * Отображает страницу добавления items
     * url: /directory/steelgrades
     */
    function steelgrades()
    {
        if (isset($_REQUEST['btn_save']))
        {
            $steelgrade = new SteelGrade();
            foreach ($_REQUEST['title'] as $id => $value)
            {
                $title      = trim(Request::GetString($id, $_REQUEST['title']));
                $alias      = trim(Request::GetString($id, $_REQUEST['alias']));
                $bgcolor    = trim(Request::GetString($id, $_REQUEST['bgcolor']));
                $color      = trim(Request::GetString($id, $_REQUEST['color']));
                
                if (empty($id) && empty($title)) continue;
                if (empty($title))
                {
                    $this->_message('Title must be specified for id ' . $id, MESSAGE_ERROR);
                    continue;
                }
                
                $steelgrade->Save($id, $title, $alias, $bgcolor, $color);                
            }
            
            // 20130312, zharkov: необходимо для пересортировки позиций
            Cache::ClearTag('steelpositions');
            
            $this->_message('Steel grades have been successfully saved', MESSAGE_OKAY);
            $this->_redirect(array('directory', 'steelgrades'));
        }
        $this->js[] = 'colpick';
        $this->js[] = 'directory_steelgrades';
        $this->css[] = 'colpick';
        $this->page_name = 'Steel Grades';
        $this->breadcrumb[$this->page_name] = '/directory/steelgrades';
        
        $steelgrades = new SteelGrade();
        $this->_assign('list', $steelgrades->GetList());
        
        $this->_display('steelgrades');
    }
    
    /**
     * Отображает страницу добавления items
     * url: /directory/locations
     */
    function locations()
    {
        if (isset($_REQUEST['btn_save']))
        {
            $location = new Location();
            foreach ($_REQUEST['title'] as $id => $value)
            {
                $title = trim(Request::GetString($id, $_REQUEST['title']));
                
                if (empty($id) && empty($title)) continue;
                if (empty($title))
                {
                    $this->_message('Title must be specified for id ' . $id, MESSAGE_ERROR);
                    continue;
                }
                
                $location->Save($id, $title, '');                
            }
            
            $this->_message('Location have been successfully saved', MESSAGE_OKAY);
            $this->_redirect(array('directory', 'locations'));
        }
        
        $this->page_name = 'Locations';
        $this->breadcrumb[$this->page_name] = '/directory/locations';
        
        $locations = new Location();
        $this->_assign('list', $locations->GetList());
        
        $this->_display('locations');
    }
    
    function deletelocation()
    {
        $location_id    = Request::GetInteger('id', $_REQUEST);
        
        $location       = new Location();
        $result         = $location->Remove($location_id);
        
        if (empty($result) || isset($result['ErrorCode']))
        {
            $this->_message('Location is used', MESSAGE_ERROR);
        }
        else
        {
            $this->_message('Location was successfully removed', MESSAGE_OKAY);
        }
        
        $this->_redirect(array('directory', 'locations'));
    }    
}
