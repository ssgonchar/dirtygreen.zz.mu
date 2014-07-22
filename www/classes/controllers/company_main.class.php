<?php
require_once APP_PATH . 'classes/components/object.class.php';
require_once APP_PATH . 'classes/core/Pagination.class.php';

require_once APP_PATH . 'classes/models/activity.class.php';
require_once APP_PATH . 'classes/models/attachment.class.php';
require_once APP_PATH . 'classes/models/city.class.php';
require_once APP_PATH . 'classes/models/company.class.php';
require_once APP_PATH . 'classes/models/contactdata.class.php';
require_once APP_PATH . 'classes/models/country.class.php';
require_once APP_PATH . 'classes/models/person.class.php';
require_once APP_PATH . 'classes/models/region.class.php';
require_once APP_PATH . 'classes/models/user.class.php';

class MainController extends ApplicationController
{
    function MainController()
    {
        ApplicationController::ApplicationController();
        
        $this->authorize_before_exec['index']   = ROLE_STAFF;
        $this->authorize_before_exec['add']     = ROLE_STAFF;
        $this->authorize_before_exec['edit']    = ROLE_STAFF;        
        $this->authorize_before_exec['view']    = ROLE_STAFF;
		 $this->authorize_before_exec['prices']   = ROLE_STAFF;
        
        $this->breadcrumb   = array('Companies' => '/companies');
        $this->context      = true;
    }

    /**
     * Отображает индексную страницу регистра бизнесов
     * url: /companies
     */
    function index()
    {        
        if (isset($_REQUEST['btn_select']))
        {
            $keyword        = Request::GetString('keyword', $_REQUEST);
            $country_id     = Request::GetInteger('country_id', $_REQUEST);
            $region_id      = Request::GetInteger('region_id', $_REQUEST);
            $city_id        = Request::GetInteger('city_id', $_REQUEST);
            $industry_id    = Request::GetInteger('industry_id', $_REQUEST);
            $activity_id    = Request::GetInteger('activity_id', $_REQUEST);
            $speciality_id  = Request::GetInteger('speciality_id', $_REQUEST);
            $product_id     = Request::GetInteger('product_id', $_REQUEST);
            $feedstock_id   = Request::GetInteger('feedstock_id', $_REQUEST);
            $relation_id    = Request::GetInteger('relation_id', $_REQUEST, -1);
            $status_id      = Request::GetInteger('status_id', $_REQUEST, -1);
            
            $filter = (!empty($keyword) ? 'keyword:' . str_replace(';', ',', $keyword) . ';' : '');
            $filter .= ($country_id > 0 ? 'country:' . $country_id . ';' : '');
            $filter .= ($region_id > 0 ? 'region:' . $region_id . ';' : '');
            $filter .= ($city_id > 0 ? 'city:' . $city_id . ';' : '');
            $filter .= ($industry_id > 0 ? 'industry:' . $industry_id . ';' : '');
            $filter .= ($activity_id > 0 ? 'activity:' . $activity_id . ';' : '');
            $filter .= ($speciality_id > 0 ? 'speciality:' . $speciality_id . ';' : '');
            $filter .= ($product_id > 0 ? 'product:' . $product_id . ';' : '');
            $filter .= ($feedstock_id > 0 ? 'feedstock:' . $feedstock_id . ';' : '');
            $filter .= ($relation_id > 0 ? 'relation:' . $relation_id . ';' : '');
            $filter .= ($status_id > 0 ? 'status:' . $status_id . ';' : '');
            
            $this->_redirect(array('companies', 'filter', str_replace(' ', '+', $filter)), false);
        }
        
        $companies      = new Company(); 
        $filter         = Request::GetString('filter', $_REQUEST);
        $filter         = urldecode($filter);
        $filter_params  = array();
        
        if (empty($filter))
        {
            $this->page_name = 'Companies';
            $this->breadcrumb[$this->page_name] = '/companies';
            
            $rowset = $companies->GetListWithActiveOrders($this->page_no);
        }
        else
        {
            $this->page_name = 'Filtered Companies';
            
            $this->breadcrumb['Companies']      = '/companies';
            $this->breadcrumb[$this->page_name] = $this->pager_path;
            
            $filter = explode(';', $filter);
            foreach ($filter as $row)
            {
                if (empty($row)) continue;
                
                $param = explode(':', $row);
                $filter_params[$param[0]] = Request::GetHtmlString(1, $param);
            }

        
            $keyword        = Request::GetString('keyword', $filter_params);
            $country_id     = Request::GetInteger('country', $filter_params);
            $region_id      = Request::GetInteger('region', $filter_params);
            $city_id        = Request::GetInteger('city', $filter_params);
            $industry_id    = Request::GetInteger('industry', $filter_params);
            $activity_id    = Request::GetInteger('activity', $filter_params);
            $speciality_id  = Request::GetInteger('speciality', $filter_params);
            $product_id     = Request::GetInteger('product', $filter_params);
            $feedstock_id   = Request::GetInteger('feedstock', $filter_params);
            $relation_id    = Request::GetString('relation', $filter_params);
            $status_id      = Request::GetString('status', $filter_params);

            $rowset     = $companies->Search($keyword, $country_id, $region_id, $city_id, $industry_id, $activity_id, $speciality_id,
                                                $product_id, $feedstock_id, $relation_id, $status_id, $this->page_no);

            $this->_assign('keyword',       $keyword);
            $this->_assign('country_id',    $country_id);
            $this->_assign('region_id',     $region_id);
            $this->_assign('city_id',       $city_id);
            $this->_assign('industry_id',   $industry_id);
            $this->_assign('activity_id',   $activity_id);
            $this->_assign('speciality_id', $speciality_id);
            $this->_assign('product_id',    $product_id);
            $this->_assign('feedstock_id',  $feedstock_id);
            $this->_assign('relation',      $relation_id);
            $this->_assign('status',        $status_id);
            
            if (($country_id + $region_id + $city_id + $industry_id + $activity_id + $speciality_id + $product_id + $feedstock_id + $relation_id + $status_id) > 0)
            {
                $this->_assign('params', true);
            }
                                    
            $this->_assign('filter', true);
        }
        
        
        $countries = new Country();
        $this->_assign('countries', $countries->GetListShort());

        if (isset($country_id) && !empty($country_id))
        {
            $regions = new Region();
            $this->_assign('regions', $regions->GetList($country_id));
        }

        if (isset($region_id) && !empty($region_id))
        {
            $cities = new City();
            $this->_assign('cities', $cities->GetList($region_id));
        }
        
        $activities = new Activity();
        $this->_assign('industries', $activities->GetList(0));        

        if (isset($industry_id) && !empty($industry_id))
        {
            $this->_assign('activities', $activities->GetList($industry_id));
        }

        if (isset($activity_id) && !empty($activity_id))
        {
            $this->_assign('specialities', $activities->GetList($activity_id));
        }
                
        $products = new Product();
        $this->_assign('products',  $products->GetTree());
        
        
        $modelCompany = new Company();
        $this->_assign('co_types_list',     $modelCompany->GetCoTypesList());
        $this->_assign('co_statuses_list',  $modelCompany->GetCoStatusesList());
        $this->_assign('co_relations_list', $modelCompany->GetCoRelationsList());
           
        $pager = new Pagination();
        $this->_assign('pager_pages',   $pager->PreparePages($this->page_no, $rowset['count']));
        $this->_assign('count',         $rowset['count']);
        $this->_assign('list',          $rowset['data']);
        
        
        $this->js = 'company_index';        
        $this->_display('index');
    }    

    /**
     * Отображает страница добавления новой компании
     * url: /company/add
     */
    function add()
    {
        $this->edit();
    }
    
    /**
     * Отображает страницу редактирования компании
     * url: /company/edit/{id}
     */
    function edit()
    {
        $company_id = Request::GetInteger('id', $_REQUEST);

        if ($company_id > 0)
        {
            $companies  = new Company();
            $company    = $companies->GetById($company_id);            
            if (empty($company)) _404();            
        }        
        
        if (isset($_REQUEST['btn_save']))
        {
            $form = $_REQUEST['form'];
            
            $title              = Request::GetString('title', $form);
            $title_native       = Request::GetString('title_native', $form);
            $title_short        = Request::GetString('title_short', $form);
            $title_trade        = Request::GetString('title_trade', $form);
            $parent_id          = Request::GetInteger('parent_id', $form);
            $type_id            = Request::GetInteger('type_id', $form, 0);
            $status_id          = Request::GetInteger('status_id', $form, 0);
            $relation_id        = Request::GetInteger('relation_id', $form, 0);
            $key_contact        = Request::GetInteger('key_contact', $form);
            $mam_genius         = Request::GetInteger('mam_genius', $form);
            $country_id         = Request::GetInteger('country_id', $form);
            $region_id          = Request::GetInteger('region_id', $form);
            $city_id            = Request::GetInteger('city_id', $form);
            $zip                = Request::GetString('zip', $form);
            $address            = Request::GetString('address', $form);
            $pobox              = Request::GetString('pobox', $form);
            $delivery_address   = Request::GetString('delivery_address', $form);
            $data_labels        = Request::GetString('data_labels', $form);
            $notes              = Request::GetString('notes', $form);
            $bank_data          = Request::GetString('bank_data', $form);
            $reg_data           = Request::GetString('reg_data', $form);
            $rail_access        = Request::GetString('rail_access', $form);            
            
            $contactdata        = isset($_REQUEST['contactdata']) ? $_REQUEST['contactdata'] : array();
            $activities         = isset($_REQUEST['activities']) ? $_REQUEST['activities'] : array();
            $products           = isset($_REQUEST['products']) ? $_REQUEST['products'] : array();
            $feedstocks         = isset($_REQUEST['feedstocks']) ? $_REQUEST['feedstocks'] : array();
            
            $vat                = Request::GetString('vat', $form);
            $albo               = Request::GetString('albo', $form);
            
            $handling_cost      = 0;
            $storage_cost       = 0;
            $currency           = '';
			//29.04.03 Sasha 
            if (in_array($relation_id, array(CO_RELATION_STOCK_AGENT, CO_RELATION_SERVICE_PROVIDER, CO_RELATION_SUPPLIER)))
            {
                $handling_cost  = Request::GetNumeric('handling_cost', $form);
                $storage_cost   = Request::GetNumeric('storage_cost', $form);
                $currency       = Request::GetString('currency', $form);
            }    
            
            if (empty($title))
            {
                $this->_message('Title must be specified !', MESSAGE_ERROR);
            }
            else
            {
                $companies  = new Company();
                $result     = $companies->Save($company_id, $title, $title_native, $title_short, $title_trade, $parent_id, $type_id, $status_id,
                                                $relation_id, $key_contact, $mam_genius, $country_id, $region_id, $city_id, $zip, $address,
                                                $pobox, $delivery_address, $data_labels, $notes, $bank_data, $reg_data, $rail_access,
                                                $vat, $albo, $handling_cost, $storage_cost, $currency);
                                                
                if (!isset($result) || isset($result['ErrorCode'])) 
                {
                    $this->_message('Such company already exists !', MESSAGE_ERROR);
                }
                else
                {
                    // сохраняет контактные данные
                    $cd = new ContactData();
                    $cd->SaveList('company', $result['id'], $contactdata);
                    
					/**
					 * @versoion 26.04.13, Sasha keeps the price change
					 */
					if (!empty($currency) || $handling_cost > 0 || $storage_cost > 0)
					{
						$companies->SavePrices(0, $result['id'], $handling_cost, $storage_cost, $currency, date("Y-m-d H:i:s"));
					}	
					
                    // сохраняет активность компании
                    $companies  = new Company();
                    $companies->SaveActivities($result['id'], $activities);
                    
                    // сохраняет продукты компании
                    $companies->SaveProducts($result['id'], 'p', $products);
                    $companies->SaveProducts($result['id'], 'f', $feedstocks);

                    // перенаправление
                    $this->_message('Company was successfully ' . (empty($company_id) ? 'created' : 'updated'), MESSAGE_OKAY);
                    $this->_redirect(array('company', $result['id']));
                }
            }
        }
        else if ($company_id > 0)
        {
            $form           = $company['company'];
            
            $cd             = new ContactData();
            $contactdata    = $cd->GetList('company', $company_id);
            $activities     = $companies->GetActivities($company_id);
            $products       = $companies->GetProducts($company_id, 'p');
            $feedstocks     = $companies->GetProducts($company_id, 'f');
        }
        else
        {
            $form           = array();

            $contactdata    = array();
            $activities     = array();
            $products       = array();
            $feedstocks     = array();
        }
        
        if ($company_id > 0)
        {
            $persons        = $companies->GetPersons($company_id);
            $persons        = $persons['data'];            
        }
        else
        {
            $persons        = array();            
        }

        
        $companies = new Company();
        $this->_assign('activities',    $companies->FillActivities($activities));
        $this->_assign('co_products',   $companies->FillProducts($products));
        $this->_assign('co_feedstocks', $companies->FillProducts($feedstocks));
        $this->_assign('contactdata',   $contactdata);
        $this->_assign('persons',       $persons);
        

        if (isset($form['parent_id']) && !empty($form['parent_id']))
        {
            $parent = $companies->GetById($form['parent_id']);
            if (isset($parent)) $this->_assign('parent', $parent['company']);
        }
        
        $countries = new Country();
        $this->_assign('countries', $countries->GetListShort());

        if (isset($form['country_id']) && !empty($form['country_id']))
        {
            $regions = new Region();
            $this->_assign('regions', $regions->GetListShort($form['country_id']));
        }

        if (isset($form['region_id']) && !empty($form['region_id']))
        {
            $cities = new City();
            $this->_assign('cities', $cities->GetListShort($form['region_id']));
        }
        
        $activities = new Activity();
        $this->_assign('industries', $activities->GetList(0));
        
        $users = new User();
        $this->_assign('mamlist', $users->GetMamList());
        
        $products       = new Product();
        $root_products  = $products->GetTree(0, false, 0);
        $this->_assign('products',  $root_products);
        
        $modelCompany = new Company();
        $this->_assign('co_types_list',     $modelCompany->GetCoTypesList());
        $this->_assign('co_statuses_list',  $modelCompany->GetCoStatusesList());
        $this->_assign('co_relations_list', $modelCompany->GetCoRelationsList());
        
        $this->_assign('form',          $form);
        $this->_assign('include_ui',    true);     


        $this->page_name                    = $company_id > 0 ? 'Edit Company' : 'New Company';
        $this->breadcrumb[$this->page_name] = '';
        
        $this->js = 'company_edit';
        
        $this->_display('edit');        
    }

    /**
     * Отображает страницу просмотра компании
     * url: /company/{id}
     * @version 20120601, zharkov
     */
    function view()
    {
        $company_id = Request::GetInteger('id', $_REQUEST);
        
        if ($company_id > 0)
        {
            $companies  = new Company();
            $company    = $companies->GetById($company_id);            
            if (empty($company)) _404();            
        }
        
        $company = $company['company'];        

        $cd = new ContactData();
        $this->_assign('contactdata',   $cd->GetList('company', $company_id));
        $this->_assign('activities',    $companies->FillActivities($companies->GetActivities($company_id)));
        $this->_assign('co_products',   $companies->FillProducts($companies->GetProducts($company_id, 'p')));
        $this->_assign('co_feedstocks', $companies->FillProducts($companies->GetProducts($company_id, 'f')));
        $this->_assign('form',          $company);
        
        // biz list
        $bizes = $companies->GetBizes($company_id);
        $this->_assign('bizes', $bizes['data']);
        $this->_assign('bizes_count', $bizes['count']);
        
        // orders
        $this->_assign('persons', $companies->GetOrders($company_id));
        
        // persons
        $persons = $companies->GetPersons($company_id, 10);
        $this->_assign('persons', $persons['data']);
        $this->_assign('persons_count', $persons['count']);

        $objectcomponent = new ObjectComponent();
        $this->_assign('object_stat', $objectcomponent->GetStatistics('company', $company_id));
        

        $parent_id = $company['parent_id'];
        if (!empty($parent_id))
        {
            $parent = $companies->GetById($parent_id);
            if (isset($parent)) $this->_assign('parent', $parent['company']);
        }
        
        // chart
        $chart_data = $companies->GetDataForChart($company_id);
        $this->_assign('chart_data', $chart_data);
        
        $this->page_name                    = $company['title'];
        $this->breadcrumb[$this->page_name] = '';
        
        $this->js = 'company_view';
        $this->_assign('include_jsapi', true);
        
        $modelAttachment    = new Attachment();
        $attachments_list   = $modelAttachment->GetListByType('', 'company', $company_id);
        $this->_assign('attachments_list', $attachments_list['data']);
        
        $this->_display('view');
    }
	
	/**
	 * @version 26.04.13, Sasha prices list for company
	 * url: /company/{id}/prices
	 */
	function prices()
	{
		$company_id = Request::GetInteger('id', $_REQUEST);
		
		$model_company = new Company();
		
		$company = $model_company->GetById($company_id);

		if (empty($company)) _404();
		
		if (isset($_REQUEST['btn_save']))
		{
			$form = isset($_REQUEST['form']) ? $_REQUEST['form'] : array();
			
			if (!empty($form))
			{
				foreach ($form as $key => $row)
				{
					if (!empty($row['handling_cost']) || !empty($row['storage_cost']) && !empty($row['currency']) && !empty($row['date']))
					{	
						$row['date'] = Request::GetDateForDB('date', $row);
						$model_company->SavePrices($key, $company_id, $row['handling_cost'], $row['storage_cost'], $row['currency'], $row['date']);
					}
				}
				
				$this->_message('Prices have been successfully saved', MESSAGE_OKAY);
			}
		}	
		
		$this->page_name	= 'Prices';
		$this->breadcrumb[$company['company']['title']]	= '/company/' . $company['company']['id'];
		$this->breadcrumb[$this->page_name] = '';
		
		$prices_list = $model_company->GetPricesForCompany($company_id);
		
		$this->_assign('prices_list', $prices_list);
		$this->_display('prices');
	}
	
	/**
	 * @version 29.04.13, Sasha remove prices
	 * url: /company/{id}/removeprices
	 */
	function removeprices()
	{
		$id = Request::GetInteger('id', $_REQUEST);
		
		if (empty($id)) _404();
		
		$model_company = new Company(); 
		
		$prices = $model_company->RemovePrices($id);
		
		if (isset($prices['company_id']))
		{
			$this->_message('Prices was successfully removed', MESSAGE_OKAY);
			$this->_redirect(array('company', $prices['company_id'], 'prices'));
		}
		else
		{
			$this->_redirect(array('companies'));
		}
	}
}