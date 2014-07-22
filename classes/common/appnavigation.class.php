<?php

require_once(APP_PATH . 'classes/models/navigation.class.php');

/**
 * Класс для формирования навигации по сайту
 *
 * @version: 2011.03.17, zharkov
 */
class AppNavigation
{       
    /**
    * Текущая страница, просматриваемая пользователем
    * 
    * @var mixed
    */
    var $current_page = null;

    
    /**
    * Конструктор
    * 
    */
    function AppNavigation()
    {
        $navigation = new Navigation();
        $this->current_page = $navigation->GetById(Request::GetInteger('cms_node_id', $_REQUEST));
    }
    
    /**
    * Формирует древовидное меню первого уровня
    * 
    * @param mixed $url
    * @param mixed $user_role
    */
    function GetTree($role)
    {
        $navigation = new Navigation();
        
        // формируется дерево
        $tree = $this->_build_tree($this->_simplify_rowset($navigation->GetList()), $role);
        
        return $tree;
    }
    
    /**
    * Формирует линейное главное меню и древовидное меню второго уровня
    * 
    * @param mixed $url
    * @param mixed $user_role
    */
    function GetMainAndTree($role)
    {
        $navigation = new Navigation();

        // главное меню
        $main_menu = array();
        foreach ($this->_simplify_rowset($navigation->GetListByParent(0)) as $row)
        {
            if ($row = $this->_adjust_row($row, $role)) $main_menu[] = $row;
        }

        // меню второго уровня
        $side_menu = array();
        if (isset($this->current_page))
        {
            // поиск первого родителя выбранного элемента
            $ancestors  = $navigation->GetAncestors($this->current_page['navigation_id']);
            $branch_id  = isset($ancestors) && isset($ancestors[0]) && !empty($ancestors[0]) ? $ancestors[0] : $this->current_page['navigation_id'];

            $side_menu  = $this->_build_tree($this->_simplify_rowset($navigation->GetTreeByParent($branch_id)), $role);
        }
        
        return array('main' => $main_menu, 'side' => $side_menu);
    }
    
    /**
    * Формирует линейное меню всех уровней
    * 
    * @param mixed $url
    * @param mixed $user_role
    */
    function GetInline($role)
    {
        $navigation = new Navigation();
        
        // главное меню
        $main_menu = array();
        foreach ($this->_simplify_rowset($navigation->GetListByParent(0)) as $row)
        {
            if ($row = $this->_adjust_row($row, $role)) $main_menu[] = $row;
        } 
        
                
        $output = array('level1' => $main_menu);
        
        if (isset($this->current_page))
        {
            $level      = 2;
            $ancestors  = $navigation->GetAncestors($this->current_page['navigation_id']);
            if (empty($ancestors)) $ancestors[] = $this->current_page['navigation_id'];
            
            foreach ($ancestors as $key => $ancestor_id)
            {
                $menu = array();
                foreach ($this->_simplify_rowset($navigation->GetListByParent($ancestor_id)) as $row)
                {
                    if ($row = $this->_adjust_row($row, $role)) $menu[] = $row;
                } 
                
                $output['level' . $level] = $menu;
                $level++;
            }
        }

        return $output;
    }
    
    
    /**
    * Проверка возможности отображения элемента навигации для роли $role, выделение активного элемента как 'active',
    * а всех родителей как 'selected', замена ключемвых слов на значения из $_REQUEST
    * 
    * @param mixed $lang
    * @param mixed $role
    * @param mixed $row
    */
    function _adjust_row($row, $role)
    {
        // если пункт меню спрятан или пользователь не имеет к ниму доступа, то пункт убирается из структуры
        if (empty($row['is_visible']) || ($row['role_id'] > 0 && $row['role_id'] < $role)) return null;
     
        // проверка является ли пункт меню выделенным или активным
        $row['state'] = '';
        if (isset($this->current_page))
        {
            if ($row['id'] == $this->current_page['navigation']['id'])
            {
                $row['state'] = 'active';
            }
            else
            {
                $navigation = new Navigation();
                foreach ($navigation->GetAncestors($this->current_page['navigation_id']) as $key => $ancestor_id)
                {
                    if ($row['id'] == $ancestor_id)
                    {
                        $row['state'] = 'selected';
                        break;
                    }
                }                
            }
        }
        
        // замена ключевых слов на значения из $_REQUEST
        foreach ($_REQUEST as $key => $value)
        {
            if (is_array($value)) continue;
            
            $row['menu_title']  = str_replace('{' . $key . '}', Request::GetString($key, $_REQUEST), $row['title']);
            $row['title']       = str_replace('{' . $key . '}', Request::GetString($key, $_REQUEST), $row['title']);
            $row['h1']          = str_replace('{' . $key . '}', Request::GetString($key, $_REQUEST), $row['title']);
        }
        
        return $row;
    }
    
    
    function _build_tree($rowset, $role)
    {
        if (empty($rowset)) return $rowset;

        // группировка страниц по уровням
        $result = array();
        foreach($rowset as $row) $result[$row['tree_level']][$row['id']] = $row;

        // формирование дерева
        ksort($result); $result = array_reverse($result);
        if (count($result) > 1)
        {
            for ($level = 0; $level < count($result); $level++)
            {
                foreach ($result[$level] as $key => $row)
                {                
                    // проверка доступа пользоватлея к просмотру страницы
                    $row = $this->_adjust_row($row, $role);
                    if (isset($row) && isset($result[$level + 1]) && isset($result[$level + 1][$row['parent_id']]))
                    {
                        $result[$level + 1][$row['parent_id']]['nodes'][] = $row;
                        unset($result[$level][$key]);
                    }                
                }
                unset($result[$level]);
            }            
        }
        
        // избавление от вложенности массива
        $result = array_values($result);

        // проверка доступа пользоватлея к просмотру корневых страниц
        $output = array();
        foreach ($result[0] as $row) if ($row = $this->_adjust_row($row, $role)) $output[] = $row;

        return $output;
    }
    
    
    /**
     * Упрощает вид массива страниц
     * 
     * @param mixed $rowset
     */
    function _simplify_rowset($rowset)
    {
        $output = array();
        foreach ($rowset as $key => $row) $output[$key] = $row['navigation'];

        return $output;
    }
}