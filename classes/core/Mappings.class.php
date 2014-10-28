<?php
require_once APP_PATH . 'classes/models/page.class.php'; 

class Mappings
{
    var $query_string;

    var $modules_path;

    var $frontend_pages;

    var $user_pages;


    var $standard_rules = array(
        '/^(\/*?)([^\/]+?)\/([^\/]+?)\/([^\/]+?)\/([^\/]+?)(\/*?)$/'
        =>  array('module' => '$2', 'controller' => '$3', 'action' => '$4', 'id' => '$5'),

        '/^(\/*?)([^\/]+?)\/([^\/]+?)\/(\d+)(\/*?)$/'
        =>  array('module' => '$2', 'controller' => 'main', 'action' => '$3', 'id' => '$4'),        
        
        '/^(\/*?)([^\/]+?)\/([^\/]+?)\/([^\/]+?)(\/*?)$/'
        =>  array('module' => '$2', 'controller' => '$3', 'action' => '$4'),

        '/^(\/*?)([^\/]+?)\/([^\/]+?)(\/*?)$/'
        =>  array('module' => '$2', 'controller' => 'main', 'action' => '$3'),

        '/^(\/*?)([^\/]+?)(\/*?)$/'         
        =>  array('module' => '$2', 'controller' => 'main', 'action' => 'index'),
        
        '/^(\/*?)$/'                        
        =>  array('module' => 'main', 'controller' => 'main', 'action' => 'index')
    );

    function Mappings($modules_path)
    {
        $this->modules_path     = $modules_path;
        $this->query_string     = $this->_get_query_string();
    }

    /**
    * Подготавливает строку запроса
    * 
    */
    function _get_query_string()
    {
        $query_string   = isset($_REQUEST['arg']) ? mb_strtolower($_REQUEST['arg'], "UTF-8") : '';
        $query_string   = $this->_check_print($this->_check_rss($this->_check_page_no($query_string)));
        $query_string   = rtrim($query_string, '/');
        
        $_REQUEST['pager_path'] = '/' . $query_string;

        return $query_string;
    }

    /**
    * Проверяет есть ли в запросе указатель на отображаемую страницу списка,
    * если есть, то номер страницы запоминается в запрос, а указатель убирается
    * из строки запроса.
    * 
    * @param mixed $query_string
    * @return mixed
    */
    function _check_page_no($query_string)
    {
        $page_no = '/~([0-9]+)/';

        preg_match($page_no, $query_string, $matches);

        if (!empty($matches))
        {
            $_REQUEST['page_no']    = $matches[1];
            $query_string           = str_replace('/' . $matches[0], '', $query_string);
        }
        
        return $query_string;
    }

    /**
    * Ищет в строке запроса указатель на принадлежность к rss,
    * если находит, добавляет признак is_rss в запрос, а указатель
    * из строки запроса убирается.
    * 
    * @param mixed $query_string
    * @return mixed
    */
    function _check_rss($query_string)
    {
        $rss = '/~rss/';

        preg_match($rss, $query_string, $matches);

        if (!empty($matches))
        {
            $_REQUEST['is_rss'] = 'yes';
            $query_string       = str_replace($matches[0], '', $query_string);
        }

        return $query_string;
    }   

    /**
    * Ищет в строке запроса указатель на принадлежность к версии для печати,
    * если находит, добавляет признак is_print в запрос, а указатель
    * из строки запроса убирается.
    * 
    * @param mixed $query_string
    * @return mixed
    */
    function _check_print($query_string)
    {
        $print = '/~print/';

        preg_match($print, $query_string, $matches);

        if (!empty($matches))
        {
            $_REQUEST['is_print'] = 'yes';
            $query_string       = str_replace($matches[0], '', $query_string);
        }

        return $query_string;
    }
    
    
    function get_modules_priority()
    {
       return array('store', 'admin', 'main', 'post', 'user');
    }

    function check_module_rules($module_name)
    {
        $mappings_path = APP_PATH . 'classes/mappings/' . strtolower ($module_name) . '.class.php'; 
        
        if (file_exists($mappings_path))
        {
            require_once($mappings_path);

            $class_name = ucfirst($module_name) . "Mappings";

            if (class_exists($class_name))
            {
                $module_mappings = new $class_name();
                return $module_mappings->CheckRules($this->query_string);
            }
        }

        return false;
    }

    function check_standard_rules()
    {
        $params = array();

        foreach ($this->standard_rules as $rule => $result)
        {
            if (preg_match_all($rule, $this->query_string, $matches))
            {
                foreach ($result as $param => $value)
                {
                    if (preg_match('/\$[0-9]/', $value))
                    {
                        $params[$param] = $matches[intval($value[1])][0];
                    }
                    else
                    {
                        $params[$param] = $value;
                    }
                }
                break;
            }
        }

        return $params;
    }

    /*
        получает из модулей массив вида:
        array('forward' => 0|1, 'params' => array())
    */
    function ApplyParams()
    {
        $match_flag         = false;
        $page_params        = array();
        $modules_priority   = $this->get_modules_priority();
      
      
        // проверяем на соответствие правилам модулей
        if (isset($modules_priority))
        {
            for ($i = 0; $i < count($modules_priority); $i++)
            {
                $module_rules = $this->check_module_rules($modules_priority[$i]);

                if (!empty($module_rules))
                {
                    if (array_key_exists('params', $module_rules))
                    {
                        $params = $module_rules['params'];

                        if (!empty($params))
                        {
                            foreach ($params as $key => $value)
                            {
                                $page_params[$key] = $value;
                            }
                        }
                    }
    
                    if (array_key_exists('forward', $module_rules))
                    {
                        if ($module_rules['forward'] == 1)
                        {
                            if (array_key_exists('query_string', $module_rules))
                            {
                                $this->query_string = $module_rules['query_string'];
                                continue;
                            }
                        }
                        else
                        {
                            if (!empty($params))
                            {
                                $match_flag = true;
                                break;
                            }
                        }
                    }
                    else
                    {
                        if (!empty($params))
                        {
                            $match_flag = true;
                            break;
                        }
                    }
                }
            }
        }

        // проверяем наличие страницы, созданной пользователем
        if (!$match_flag)
        {
            $params = $this->_check_content_page();
            
            if (!empty($params))
            {
                foreach ($params as $key => $value)
                {
                    $page_params[$key] = $value;
                }

                $match_flag = true;
            }
        }
        
        // проверяем стандартные правила
        if (!$match_flag)
        {
            $params = $this->check_standard_rules();

            foreach ($params as $key => $value)
            {
                $page_params[$key] = $value;
            }
        }
        
        if (empty($page_params))
        {
            Log::AddLine( LOG_ERROR, "MAPPINGS : Rules don't match");
            _404();
        }        

        $page_params['page_alias'] = $this->_get_page_alias($page_params);
        $page_params['name_space'] = $this->_get_name_space($page_params);
        
        foreach ($page_params as $param => $value)
        {
            $_REQUEST[$param] = $value;
        }

        Log::AddLine( LOG_CUSTOM, "MAPPINGS : " . var_export($_REQUEST, TRUE));
    }

    function _get_page_alias($page_params)
    {
        if (array_key_exists('page_alias', $page_params))
        {
            return $page_params['page_alias'];
        }
        
        $page_alias = $page_params['module'] . '_' . $page_params['controller'] . '_' . $page_params['action'];
        
        if (array_key_exists('name_space', $page_params) && !empty($page_params['name_space']))
        {
            $page_alias = $page_params['name_space'] . '_' . $page_alias;
        }
        
        if (Request::IsAjax())
        {
            $page_alias = 'ajax_' . $page_alias;
        }
        
        return $page_alias;
    }
    
    function _get_name_space($page_params)
    {
        if (array_key_exists('name_space', $page_params))
        {
            return $page_params['name_space'];
        }
        else if (array_key_exists('module', $page_params))
        {
            return $page_params['module'];
        }
        
        return '';
    }

/*  20110208, zharkov: не используется         
    function _clear_query_string()
    {
        $query_string     = !empty($_REQUEST['arg']) ? mb_strtolower($_REQUEST['arg'], "UTF-8") : '';       
        $pos             = strpos($query_string, '?');
        
        if ($pos !== false)
        {
            $query_string = substr($query_string, 0, $pos);
        }

        $pos = strpos($query_string, '#');
        if ($pos !== false)
        {
            $query_string = substr($query_string, 0, $pos);
        }
        
        if (empty($query_string))
        {
            return '/';
        }
        
        return trim($query_string, "\/");
    }
*/    

    /**
     *  Проверяет, соответствует ли запрос странице, созданной пользователем
     *
     *  @version 2010.04.23
     *  @return если соответствует, то array, если нет, то false
     */      
    function _check_content_page()
    {
        $page   = new Page();
        $result = $page->GetByUrl(empty($this->query_string) ? 'home' : $this->query_string);

        if (empty($result) || array_key_exists('ErrorCode', $result)) return false;
        
        return array('module' => 'page', 'controller' => 'main', 'action' => 'view', 'id' => $result['page_id']);
    }    
}
