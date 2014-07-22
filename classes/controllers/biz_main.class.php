<?php
require_once APP_PATH . 'classes/components/object.class.php';
require_once APP_PATH . 'classes/core/Pagination.class.php';
require_once APP_PATH . 'classes/models/attachment.class.php';
require_once APP_PATH . 'classes/models/biz.class.php';
require_once APP_PATH . 'classes/models/company.class.php';
require_once APP_PATH . 'classes/models/market.class.php';
require_once APP_PATH . 'classes/models/objective.class.php';
require_once APP_PATH . 'classes/models/product.class.php';
require_once APP_PATH . 'classes/models/team.class.php';
require_once APP_PATH . 'classes/models/user.class.php';

class MainController extends ApplicationController
{
    function MainController()
    {
        ApplicationController::ApplicationController();
        
        $this->authorize_before_exec['add']     = ROLE_STAFF;
        $this->authorize_before_exec['edit']    = ROLE_STAFF;        
        $this->authorize_before_exec['index']   = ROLE_STAFF;
        $this->authorize_before_exec['view']    = ROLE_STAFF;
        
        $this->breadcrumb   = array('BIZs' => '/bizes');        
        $this->context      = true;
/*        
        if (isset($_SESSION['biz_list_url']) && !empty($_SESSION['biz_list_url']))
        {
            $this->breadcrumb['Filtered BIZes'] = $_SESSION['biz_list_url'];
        }        
*/        
    }

    /**
     * Отображает индексную страницу регистра бизнесов
	 * 
     * url: /bizes
	 * 
	 * @version 20130722, sasha add favourite list
     */
    function index()
    {
        $biz_model = new Biz();
		
		if (isset($_REQUEST['btn_select']))
        {
            $form       = $_REQUEST['form'];    
			//@version 20130515 Sasha filter
            $objective_id	= Request::GetInteger('objective_id', $form);
            $team_id		= Request::GetInteger('team_id', $form);
            $product_id		= Request::GetInteger('product_id', $form);
            $status			= Request::GetString('status', $form);
            $market_id		= Request::GetInteger('market_id', $form);
            $driver_id		= Request::GetInteger('driver_id', $form);
			$keyword		= Request::GetString('keyword', $form);
			
			$filter  = (!empty($keyword) ? 'keyword:' . str_replace(';', ',', $keyword) . ';' : '');
            $filter .= ($objective_id > 0 ? 'objective:' . $objective_id . ';' : '');
            $filter .= ($team_id > 0 ? 'team:' . $team_id . ';' : '');
            $filter .= ($product_id > 0 ? 'product:' . $product_id . ';' : '');
            $filter .= (!empty($status) ? 'status:' . str_replace(';', ',', $status) . ';' : '');
            $filter .= ($market_id > 0 ? 'market:' . $market_id . ';' : '');
            $filter .= ($driver_id > 0 ? 'driver:' . $driver_id . ';' : '');
			
			if (isset($form['company_id']) && !empty($form['company_id']))
			{	
				$company_ids = array();
				
				foreach ($form['company_id'] as $key => $row)
				{
					 if (!empty($row))
					 {
						$company_ids[] = $row; 
					 }	 
				}
				
				$company_ids = implode(",", $company_ids);
				
				$filter .= (!empty($company_ids) ? 'company:' . str_replace(';', ',', $company_ids) . ';' : '');
			}
			
			if (isset($form['role']) && !empty($form['role']) && !empty($company_ids))
			{	
				$role = array();
				
				foreach ($form['role'] as $key => $row)
				{
					 if (!empty($row) && isset($company_ids[$key]))
					 {
						$role[] = $row; 
					 }
					 else if (isset($company_ids[$key]))
					 {
						$role[] = 0; 
					 }	 
				}
				
				$role = implode(",", $role);
				
				$filter .= (!empty($role) ? 'role:' . str_replace(';', ',', $role) . ';' : '');
			}
			
            $this->_redirect(array('bizes', 'filter', str_replace(' ', '+', $filter)), false);
        }
        
        $filter         = Request::GetString('filter', $_REQUEST);
        $filter         = urldecode($filter);
        $filter_params  = array();
        
        $this->breadcrumb = array();
		
        if (empty($filter))
        {
            $this->page_name = 'BIZs';
            $this->breadcrumb[$this->page_name] = '/';
			
			//favourite bizes list
			$this->_assign('favourite_bizes', $biz_model->GetListFavourite());
			
        }
        else
        {
            $this->page_name = 'Filtered BIZs';
            
            $this->breadcrumb['BIZs']          = '/bizes';
            $this->breadcrumb[$this->page_name] = $this->pager_path;            
            $_SESSION['biz_list_url']           = $this->pager_path;
            
            $filter = explode(';', $filter);
            foreach ($filter as $row)
            {
                if (empty($row)) continue;
                
                $param = explode(':', $row);
                $filter_params[$param[0]] = Request::GetHtmlString(1, $param);
            }			
        }
		
        //@version 20130515 Sasha filter
        $form['objective_id']	= Request::GetInteger('objective', $filter_params);
        $form['team_id']		= Request::GetInteger('team', $filter_params);
        $form['product_id']		= Request::GetInteger('product', $filter_params);
        $form['status']			= Request::GetString('status', $filter_params);
        $form['market_id']		= Request::GetInteger('market', $filter_params);
        $form['driver_id']		= Request::GetInteger('driver', $filter_params);
        $form['role']			= Request::GetString('role', $filter_params);
        $form['company_id']		= Request::GetString('company', $filter_params);
        $keyword				= Request::GetString('keyword', $filter_params);

		$form['company'] = array();
		
		/*
		 *@version 20130513 Sasha filter
		 */
		if (!empty($form['status']) || !empty($form['company_title']) || 
		   ($form['objective_id'] + $form['team_id'] + $form['product_id'] + $form['market_id'] + $form['driver_id'] + $form['company_id']) > 0)
        {
			$params = true;
			$this->_assign('params', $params);
        } 	
		
		if (!empty($form['company_id']))
		{
			$form['company_id'] = explode(',', $form['company_id']); 
			
			if (!empty($form['company_id']))
			{	
				$company_model = new Company();
				
				foreach ($form['company_id'] as $row)
				{	
					$form['company'][] = $company_model->GetById($row);
				}	
			}
		}
		
		if (!empty($form['role']))
		{
			$form['role']	= explode(',', $form['role']);
		}	
		
		if ((isset($params) && $params) || !empty($keyword))
		{
			$bizes      = new Biz();
            $rowset     = $bizes->Search($keyword, $form['company'], $form['role'], $form['objective_id'], $form['team_id'], $form['product_id'], $form['status'], $form['market_id'], $form['driver_id'], $this->page_no);
		}	
        else
        {
            $rowset = array(
                'data'  => array(),
                'count' => 0
            );
        }
		
		$this->js = 'biz_index';
		
		/*data for filters select*/
		$bizes_data = $biz_model->GetDataFromBizes();
		
		if (isset($form['team_id']) && !empty($form['team_id']))
        {
            $products = new Product();
            $this->_assign('products', $products->GetTree($form['team_id'], false, -1, true));
        }
		
		$this->_assign('form',		$form);
		$this->_assign('keyword',   $keyword);
		$this->_assign('data',		$bizes_data);
        $this->_assign('count',     $rowset['count']);
        $this->_assign('list',      $rowset['data']);
		
        if (!empty($rowset['data'])) $this->_assign('filter', true);                        
        
        $pager = new Pagination();
        $this->_assign('pager_pages', $pager->PreparePages($this->page_no, $rowset['count']));

        
            //dg($_SESSION);
        /*
        if ($_SESSION['user']['id'] == '1671') {
            $this->rcontext = true;
            $this->layout   = 'rcolumnmod';            
            $this->_display('indexmod');
        } else {
            $this->_display('index');
        }
         * 
         */
        //$this->rcontext = true;
        //$this->layout   = 'rcolumnmodex';   
        $this->_display('index');
    }    
    
    /**
     * Отображает страница добавления нового бизнеса
     * url: /biz/add
     */
    function add()
    {
        $this->edit();
    }
    
    /**
     * Отображает страницу редактирования бизнеса
	 * @version 25.04.13 Sasha, 
     * url: /biz/{id}/edit
	 * url: /biz/{id}/addsubbiz
     */
    function edit()
    {
        $biz_id = Request::GetInteger('id', $_REQUEST);
		$subbiz = Request::GetBoolean('subbiz', $_REQUEST);
		
        if ($biz_id > 0)
        {
            $bizes  = new Biz();
            $biz    = $bizes->GetById($biz_id);          
            if (empty($biz)) _404();            
        }        

		if (isset($biz) && isset($biz['biz']) && $biz['biz']['parent_id'] > 0 && $subbiz) _404();   
	
        if (isset($_REQUEST['btn_save']))
        {
            
            $form           = $_REQUEST['form'];
            $navigators     = isset($_REQUEST['navigators']) ? $_REQUEST['navigators'] : array();
            
            $producers      = isset($_REQUEST['producers']) ? $_REQUEST['producers'] : array();
            $pproducers     = isset($_REQUEST['pproducers']) ? $_REQUEST['pproducers'] : array();
            $buyers         = isset($_REQUEST['buyers']) ? $_REQUEST['buyers'] : array();
            $pbuyers        = isset($_REQUEST['pbuyers']) ? $_REQUEST['pbuyers'] : array();
            $sellers        = isset($_REQUEST['sellers']) ? $_REQUEST['sellers'] : array();
            $users          = isset($_REQUEST['users']) ? $_REQUEST['users'] : array();
            $competitors    = isset($_REQUEST['competitors']) ? $_REQUEST['competitors'] : array();
            $transports     = isset($_REQUEST['transports']) ? $_REQUEST['transports'] : array();
            
            $title          = Request::GetHtmlString('title', $form);
            $description    = Request::GetHtmlString('description', $form);
            $objective_id   = Request::GetInteger('objective_id', $form);
            $team_id        = Request::GetInteger('team_id', $form);
            $product_id     = Request::GetInteger('product_id', $form);
            $market_id      = Request::GetInteger('market_id', $form);
            $driver_id      = Request::GetInteger('driver_id', $form);
            $status         = Request::GetString('status', $form);
            $is_favourite   = Request::GetInteger('is_favourite', $form);
            
            
            if (empty($title))
            {
                $this->_message('Title must be specified !', MESSAGE_ERROR);
            }
            else if (empty($objective_id))
            {
                $this->_message('Objective must be specified !', MESSAGE_ERROR);
            }
            else if (empty($team_id))
            {
                $this->_message('Team must be specified !', MESSAGE_ERROR);
            }
            else if (empty($product_id))
            {
                $this->_message('Product must be specified !', MESSAGE_ERROR);
            }
            else if (empty($status))
            {
                $this->_message('Status must be specified !', MESSAGE_ERROR);
            }
            else if (empty($driver_id))
            {
                $this->_message('Driver must be specified !', MESSAGE_ERROR);
            }
            else
            {
                $bizes  = new Biz();
				
				if ($subbiz)
				{	
					$subbiz_id = 0;
					$parent_id = $biz_id;
					$result = $bizes->Save($subbiz_id, $title, $description, $objective_id, $market_id, $team_id, $product_id, $status, $driver_id, $parent_id);
				}
				else
				{
					$result = $bizes->Save($biz_id, $title, $description, $objective_id, $market_id, $team_id, $product_id, $status, $driver_id);
				}	
					
                if (empty($result) || isset($result['ErrorCode']))
                {
                    $this->_message('Error while saving biz !', MESSAGE_ERROR);
                }
                else
                {
                    // добавляем драйвера в список пользователей
                    $navigators[$driver_id] = array('user_id' => $driver_id, 'is_driver' => 1);
                    
                    // сохраняет пользователей бизнеса (навигаторы и драйвер)
                    $bizes->SaveNavigators($result['id'], $navigators);
                   	
                    // сохраняет принадлежность бизнеса к "любимым"
					$new_biz = $bizes->GetById($result['id']);
					
					if (empty($is_favourite) && !empty($new_biz['biz']['quick']['is_favourite']))
					{
						$bizes->RemoveFromFavourite($result['id']);
					}
					else if (!empty($is_favourite) && empty($new_biz['biz']['quick']['is_favourite'])) 
					{	
						$bizes->SaveIsFavourite($result['id']);
					}
					
                    $bizes->SaveCompanies($result['id'], $producers, 'producer');
                    $bizes->SaveCompanies($result['id'], $pproducers, 'pproducer');
                    $bizes->SaveCompanies($result['id'], $buyers, 'buyer');
                    $bizes->SaveCompanies($result['id'], $pbuyers, 'pbuyer');
                    $bizes->SaveCompanies($result['id'], $sellers, 'seller');
                    $bizes->SaveCompanies($result['id'], $users, 'user');
                    $bizes->SaveCompanies($result['id'], $competitors, 'competitor');
                    $bizes->SaveCompanies($result['id'], $transports, 'transport');                    
                    
                    $this->_message('BIZ was successfully ' . (empty($biz_id) ? 'registered' : 'updated'), MESSAGE_OKAY);
                    $this->_redirect(array('biz', $result['id']));
                }
            }            
        }
        else if ($biz_id > 0)
        {
            $form       = $biz['biz'];
            $navigators = $bizes->GetNavigators($biz_id);
            
            $producers      = $bizes->GetCompanies($biz_id, 'producer');
            $pproducers     = $bizes->GetCompanies($biz_id, 'pproducer');
            $buyers         = $bizes->GetCompanies($biz_id, 'buyer');
            $pbuyers        = $bizes->GetCompanies($biz_id, 'pbuyer');
            $sellers        = $bizes->GetCompanies($biz_id, 'seller');
            $users          = $bizes->GetCompanies($biz_id, 'user');
            $competitors    = $bizes->GetCompanies($biz_id, 'competitor');
            $transports     = $bizes->GetCompanies($biz_id, 'transport');            
            
            $form['is_favourite'] = isset($form['quick']) && isset($form['quick']['is_favourite']) ? $form['quick']['is_favourite'] : 0;
        }
        else
        {
            $form           = array('driver_id' => $this->user_id, 'is_favourite' => 0);
            $navigators     = array();
            
            $producers      = array();
            $pproducers     = array();
            $buyers         = array();
            $pbuyers        = array();
            $sellers        = array();
            $users          = array();
            $competitors    = array();
            $transports     = array();
        }
                
     
        if (empty($biz_id))
        {
            $this->page_name    = 'New BIZ';
            $this->breadcrumb   = array(
                'BIZs'              => '/biz',
                $this->page_name    => ''
            );
        }
        else if (!$subbiz)
        {
            $this->page_name    = 'Edit BIZ';
            $this->breadcrumb   = array(
                'BIZs'                  => '/biz',
                $biz['biz']['doc_no_full']   => '/biz/' . $biz_id,
                $this->page_name        => ''
            );            
        }
		else
		{
			$this->page_name    = 'New Sub BIZ';
            $this->breadcrumb   = array(
                'BIZs'                  => '/biz',
				$biz['biz']['doc_no_full']   => '/biz/'. $biz_id,
                $this->page_name        => ''
            );
			
			$form['title'] = '';
			$form['description'] ='';
		}	
        
        $markets = new Market();
        $this->_assign('markets', $markets->GetList());
        
        $teams = new Team();
        $this->_assign('teams', $teams->GetList());

        $objectives = new Objective();
        $this->_assign('objectives', $objectives->GetListForBiz(($biz_id > 0 ? $biz['biz']['objective_id'] : 0)));
        
        if (isset($form['team_id']) && !empty($form['team_id']))
        {
            $products = new Product();
            $this->_assign('products', $products->GetTree($form['team_id']));
        }
        
        $model_users        = new User();
        $mam_list           = $model_users->GetDriversList();
        
        $show_is_favourite  = false;
        
        if (!empty($navigators))
        {
            foreach ($mam_list as $key => $row)
            {
                foreach ($navigators as $row1)
                {                    
                    if ($row['user']['id'] == $row1['user_id']) $mam_list[$key]['selected'] = 1;
                    if ($row1['user_id'] == $this->user_id) $show_is_favourite = true;
                }                                
            }
        }

        if ($form['driver_id'] == $this->user_id) $show_is_favourite = true;
        
        $this->_assign('show_is_favourite', $show_is_favourite);
        $this->_assign('mam_list', $mam_list);
        
        $companies = new Company();
        $this->_assign('producers',     $companies->FillCompanyInfo($producers));
        $this->_assign('pproducers',    $companies->FillCompanyInfo($pproducers));
        $this->_assign('buyers',        $companies->FillCompanyInfo($buyers));
        $this->_assign('pbuyers',       $companies->FillCompanyInfo($pbuyers));
        $this->_assign('sellers',       $companies->FillCompanyInfo($sellers));
        $this->_assign('users',         $companies->FillCompanyInfo($users));
        $this->_assign('competitors',   $companies->FillCompanyInfo($competitors));
        $this->_assign('transports',    $companies->FillCompanyInfo($transports));        
        
        $this->_assign('form',          $form);
        $this->_assign('include_ui',    true);     
        
        $this->js = 'biz_edit';
        
        $this->_display('editmod');        
    /*if ($_SESSION['user']['id'] == '1682' || $_SESSION['user']['id'] == '1705' || $_SESSION['user']['id'] == '1671') {
            $this->_display('editmod');  
        } else {
            $this->_display('edit');  
        }*/
         
    }


    /**
     * Отображает страницу просмотра бизнеса
     * url: /biz/{id}
	 * @version 22.04.13 Sasha, add js
     */
    function view()
    {
        //$this->js       = 'chat_index';
		
		$biz_id = Request::GetInteger('id', $_REQUEST);
        if (empty($biz_id)) _404();
        
        $bizes  = new Biz();
        $biz    = $bizes->GetById($biz_id);            
        if (empty($biz)) _404();            


        $navigators     = $bizes->GetNavigators($biz_id);
        
        $producers      = $bizes->GetCompanies($biz_id, 'producer');
        $pproducers     = $bizes->GetCompanies($biz_id, 'pproducer');
        $buyers         = $bizes->GetCompanies($biz_id, 'buyer');
        $pbuyers        = $bizes->GetCompanies($biz_id, 'pbuyer');
        $sellers        = $bizes->GetCompanies($biz_id, 'seller');
        $users          = $bizes->GetCompanies($biz_id, 'user');
        $competitors    = $bizes->GetCompanies($biz_id, 'competitor');
        $transports     = $bizes->GetCompanies($biz_id, 'transport');            
                
        $mam_users = new User();
        $this->_assign('navigators',    $mam_users->FillUserInfo($navigators));

        $companies = new Company();
        $this->_assign('producers',     $companies->FillCompanyInfo($producers));
        $this->_assign('pproducers',    $companies->FillCompanyInfo($pproducers));
        $this->_assign('buyers',        $companies->FillCompanyInfo($buyers));
        $this->_assign('pbuyers',       $companies->FillCompanyInfo($pbuyers));
        $this->_assign('sellers',       $companies->FillCompanyInfo($sellers));
        $this->_assign('users',         $companies->FillCompanyInfo($users));
        $this->_assign('competitors',   $companies->FillCompanyInfo($competitors));
        $this->_assign('transports',    $companies->FillCompanyInfo($transports)); 
        
        $this->_assign('form',          $biz['biz']);
        
        if ($biz['biz']['parent_id'] > 0)
        {
            $parent_biz = $bizes->GetById($biz['biz']['parent_id']);
            if (!empty($parent_biz))
            {
                $this->_assign('parent_biz', $parent_biz['biz']);
            }
        }

        $objectcomponent = new ObjectComponent();        
        $this->_assign('object_stat', $objectcomponent->GetStatistics('biz', $biz_id));
        
        $this->page_name                    = $biz['biz']['doc_no_full'];
        $this->breadcrumb[$this->page_name] = '';
        
        $modelAttachment    = new Attachment();
        $attachments_list   = $modelAttachment->GetListByType('', 'biz', $biz_id);
        $this->_assign('attachments_list', $attachments_list['data']);

        $this->_display('viewmod');        
       
 //dg($_SESSION);
        
       /*  if ($_SESSION['user']['id'] == '1682' || $_SESSION['user']['id'] == '1705' || $_SESSION['user']['id'] == '1671'){
          //  $this->rcontext = true;
          //  $this->layout   = 'rcolumnmod';            
            $this->_display('viewmod');
        } else {
            $this->_display('view');
        }*/
         
}

        }
