<?php
require_once APP_PATH . 'classes/models/product.class.php';
require_once APP_PATH . 'classes/models/team.class.php';

class MainController extends ApplicationController
{
    function MainController()
    {
        ApplicationController::ApplicationController();
        
        $this->authorize_before_exec['index']       = ROLE_STAFF;
        $this->authorize_before_exec['add']         = ROLE_STAFF;
        $this->authorize_before_exec['edit']        = ROLE_STAFF;        
        $this->authorize_before_exec['view']        = ROLE_STAFF;
        $this->authorize_before_exec['gallery']     = ROLE_STAFF;
        $this->authorize_before_exec['timeline']    = ROLE_STAFF;        
        
        $this->breadcrumb   = array('Products' => '/products');
        $this->context      = true;
    }

    /**
     * Отображает индексную страницу регистра товаров
     * url: /products
     */
    function index()
    {
        $team       = new Team();
        $teams      = $team->GetList();
        
        $products   = new Product();
        foreach ($teams as $key => $row)
        {
            $teams[$key]['team']['products'] = $products->GetTree($row['team']['id'], true);
        }
        
        $left   = array();
        $right  = array();
        foreach ($teams as $key => $row)
        {
            if ($key >= count($teams) / 2)
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
        
        $this->js = 'product_index';
        
        $this->_display('index');
    }    
    
    /**
     * Отображает страница добавления новой компании
     * url: /product/add
     */
    function add()
    {
        $this->edit();
    }
    
    /**
     * Отображает страницу редактирования продукта
     * url: /product/{id}/edit
     */
    function edit()
    {
        $product_id = Request::GetInteger('id', $_REQUEST);
        
        if ($product_id > 0)
        {
            $products   = new Product();
            $product    = $products->GetById($product_id);
            
            if (empty($product)) _404();            
        }        
        
        if (isset($_REQUEST['btn_save']))
        {
            $form           = $_REQUEST['form'];
            $tariff_codes   = isset($_REQUEST['tariff_code']) ? $_REQUEST['tariff_code'] : array();
            
            $title          = Request::GetString('title', $form);
            $team_id        = Request::GetInteger('team_id', $form);
            $parent_id      = Request::GetInteger('parent_id', $form);
            $description    = Request::GetHtmlString('description', $form);
            
            $tariff_code_title          = Request::GetString('tariff_code_title', $form);
            $tariff_code_description    = Request::GetString('tariff_code_description', $form);

            
            if (empty($title))
            {
                $this->_message('Title must be specified !', MESSAGE_ERROR);
            }
            else if (empty($team_id))
            {
                $this->_message('Team must be specified !', MESSAGE_ERROR);
            }
            else
            {
                // проверка правильности тарифных кодов, они должны быть уникальными без учета пробелов
                $has_errors = false;                
                foreach ($tariff_codes as $key => $row)
                {
                    if (!empty($row['id']) && empty($row['title']))
                    {
                        $has_errors = true;
                        break;
                    }
                    
                    foreach ($tariff_codes as $key1 => $row1)
                    {
                        if ($key == $key1) break;
                        if (strtolower(preg_replace("#\s+#", '', $row1['title'])) == strtolower(preg_replace("#\s+#", '', $row['title'])))
                        {
                            $has_errors = true;
                            break;                            
                        }
                    }
                }
                
                // проверка правильности недобавленного тарифного кода
                if (!$has_errors && !empty($tariff_code_title))
                {
                    foreach ($tariff_codes as $key => $row)
                    {
                        if (strtolower(preg_replace("#\s+#", '', $row['title'])) == strtolower(preg_replace("#\s+#", '', $tariff_code_title)))
                        {
                            $has_errors = true;
                            break;                            
                        }
                    }                    
                }

                
                if ($has_errors)
                {
                    $this->_message('Tariff codes must be unique !', MESSAGE_ERROR);
                }
                else
                {
                    $products   = new Product();
                    $result     = $products->Save($product_id, $parent_id, $team_id, $title, $description);                

                    if (empty($result))
                    {
                        $this->_message('Product with such title already exists !', MESSAGE_ERROR);
                    }
                    else
                    {
                        foreach ($products->GetTariffCodes($result['id']) as $row)
                        {
                            $delete_flag = true;
                            foreach ($tariff_codes as $key => $row1)
                            {
                                if ($row['id'] == $row1['id'])
                                {
                                    $delete_flag = false;
                                }
                            }
                            
                            if ($delete_flag) $products->RemoveTariffCode($row['id'], $result['id']);
                        }
                        
                        foreach ($tariff_codes as $row)
                        {
                            $code_id             = Request::GetInteger('id', $row);
                            $code_title          = Request::GetString('title', $row);
                            $code_description    = Request::GetString('description', $row);
                            
                            if (empty($code_id) && empty($code_title)) continue;                    
                            $products->SaveTariffCode($code_id, $result['id'], $code_title, $code_description);
                        } 
                        

                        if (!empty($tariff_code_title)) $products->SaveTariffCode(0, $result['id'], $tariff_code_title, $tariff_code_description);
                        Cache::ClearTag('product-' . $result['id'] . '-tariffcodes');
                        
                        $this->_message('Product was saved successfully', MESSAGE_OKAY);
                        $this->_redirect(array('products'));                        
                    }                    
                }
            }            
        }
        else
        {
            if ($product_id > 0)
            {                
                $form           = $product['product'];
                $tariff_codes   = $products->GetTariffCodes($product_id);
                $this->_assign('tc_index', count($tariff_codes));
                
                $this->page_name                    = 'Edit Product';
                $this->breadcrumb[$this->page_name] = '';
            }
            else
            {
                $form           = array('id' => 0, 'team_id' => 0);
                $tariff_codes   = array();
                
                $this->page_name                    = 'New Product';
                $this->breadcrumb[$this->page_name] = '';                
            }
        }
        
        $this->page_name                    = $product_id > 0 ? 'Edit Product' : 'New Product';
        $this->breadcrumb[$this->page_name] = '';
        
        $teams      = new Team();
        $products   = new Product();
        
        $this->_assign('form',          $form);
        $this->_assign('teams',         $teams->GetList());
        $this->_assign('product_id',    $product_id);
        $this->_assign('products',      $products->GetTreeWithoutNode($form['team_id'], $product_id));
        $this->_assign('tariffcodes',   $tariff_codes);
        
        $this->js = 'product_edit';
        
        $this->_display('edit');
    }

    /**
     * Отображает страницу просмотра продукта
     * url: /product/{id}
     */
    function view()
    {
        $product_id = Request::GetInteger('id', $_REQUEST);

        $this->_redirect(array('product', $product_id, 'edit'));
        
        $products   = new Product();
        $product    = $products->GetById($product_id);
        
        if (empty($product)) _404();
        
        $product = $product['product'];
        
        $this->_assign('form',          $product);
        $this->_assign('tariffcodes',   $products->GetTariffCodes($product['id']));

        $this->page_name                    = $product['title'];
        $this->breadcrumb[$this->page_name] = '';
        
        $this->_display('view');        
    }
    
    function updatealias()
    {
        $products = new Product();
        $products->UpdateAlias();
    }
}