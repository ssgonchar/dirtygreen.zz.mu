<?php

/**
 * Модель для обработки запросов к хранимым процедурам
 */
class Sp
{
    /**
     * Конструктор
     * 
     */
    function Sp()
    {

    }
    
    /**
     * Сохраняет контейнер
     * 
     * @param mixed $id
     * @param mixed $alias
     * @param mixed $title
     * @param mixed $width
     * @param mixed $height
     * @param mixed $banner_width
     * @param mixed $banner_height
     * @param mixed $rotation_type
     * @param mixed $display_type
     * @param mixed $banners_count
     */
    function sp_ad_container_save($params)
    {
        list($user_id, $id, $alias, $title, $width, $height, $banner_width, $banner_height, $rotation_type, $display_type, $max_banners, $is_home) = $params;
        
        $model  = new Model('ad_containers');
        $result = $model->Exists(
            array(
                'where' => array(
                    'conditions' => 'alias = ? AND id != ?',
                    'arguments' => array($alias, $id)
                )            
            )
        );
        
        if ($result) return array(array('ErrorCode' => -1, 'ErrorAt' => 'sp_ad_container_save'));

        if (!$model->Exists(array('where' => array('conditions' => 'id = ?', 'arguments' => array($id)))))
        {
            $id = $model->Insert(array(
                'alias'         => $alias, 
                'title'         => $title, 
                'width'         => $width, 
                'height'        => $height, 
                'banner_width'  => $banner_width, 
                'banner_height' => $banner_height, 
                'rotation_type' => $rotation_type, 
                'display_type'  => $display_type, 
                'max_banners'   => $max_banners,
                'is_home'       => $is_home,
                'created_at'    => date("Y-m-d H:i:s"),
                'created_by'    => $user_id,
                'modified_at'   => date("Y-m-d H:i:s"),
                'modified_by'   => $user_id
            ));
            
            //$id = $model->SelectSingle(array('fields' => 'MAX(id) AS id', 'where' => array('conditions' => 'created_by = ?', 'arguments' => array($user_id))));
        }
        else
        {

            $id = $model->Update($id, array(
                'alias'         => $alias, 
                'title'         => $title, 
                'width'         => $width, 
                'height'        => $height, 
                'banner_width'  => $banner_width, 
                'banner_height' => $banner_height, 
                'rotation_type' => $rotation_type, 
                'display_type'  => $display_type, 
                'max_banners'   => $max_banners,
                'is_home'       => $is_home,
                'created_at'    => date("Y-m-d H:i:s"),
                'created_by'    => $user_id,
                'modified_at'   => date("Y-m-d H:i:s"),
                'modified_by'   => $user_id
            ));
        }
        
        return array(array(array('id' => $id))); 
    }
    
    /**
     * Возвращает список контейнеров
     * 
     */
    function sp_ad_container_get_list()
    {
        $models = new Model('ad_containers');
        $result =  $models->SelectList(array(
            'fields'    => array('id AS ad_container_id'),
            'order'     => 'alias'
        ));
        
        return array($result);
    }
    
    /**
     * Возвращает список контейнеров по идентификаторам
     * 
     * @param mixed $params
     * @return resource
     */
    function sp_ad_container_get_list_by_ids($params)
    {
        list($ids) = $params;

        if (empty($ids)) return array(array('ErrorCode' => -1, 'ErrorAt' => 'sp_ad_container_get_list_by_ids'));
        
        $model  = new Model('ad_containers');
        return $model->table->_exec_raw_query(
            "SELECT 
                *,
                (SELECT COUNT(*) FROM ad_banners WHERE ad_container_id = ad_containers.id) AS banners_count
            FROM ad_containers
            WHERE id IN (" . $ids . ")
            LIMIT 100;"
        );
    }
    
    /**
     * Возвращает список баннеров
     * 
     */
    function sp_ad_banner_get_list($params)
    {
        list($container_id) = $params;

        $where = array('conditions' => 'is_deleted = 0', 'arguments' => array());
                
        if ($container_id > 0)
        {
            $where['conditions']    = $where['conditions'] . ' AND ad_container_id = ?';
            $where['arguments'][]   = $container_id;
        }
        
        $models = new Model('ad_banners');
        $params = array(
            'fields'    => array('id AS ad_banner_id'),
            'where'     => $where,
            'order'     => 'expire_at DESC'
        );
        
        return array($models->SelectList($params));
    }
    
    /**
     * Возвращает список баннеров по идентификаторам
     * 
     * @param mixed $params
     * @return resource
     */
    function sp_ad_banner_get_list_by_ids($params)
    {
        list($ids) = $params;

        if (empty($ids)) return array(array('ErrorCode' => -1, 'ErrorAt' => 'sp_ad_banner_get_list_by_ids'));
        
        $model  = new Model('ad_banners');
        return $model->table->_exec_raw_query(
            "SELECT 
                *,
                (SELECT COUNT(*) FROM ad_banner_shows WHERE ad_banner_id = ad_banners.id) AS count_shows
            FROM ad_banners
            WHERE id IN (" . $ids . ")
            LIMIT 100;"
        );
    }
    
    /**
     * Сохраняет баннер
     * 
     * @param mixed $params
     * @return mixed
     */
    function sp_ad_banner_save($params)
    {
        list($user_id, $id, $ad_container_id, $ad_customer, $frequency, 
                $price, $n_user_day, $n_total, $start_at, $expire_at, $text, $src, 
                $url, $display_type, $pages) = $params;
        
        $model = new Model('ad_banners');
        /*
        $result = $model->Exists(array('where' => array('conditions' => 'alias = ? AND id != ?', 'arguments' => array($alias, $id))));        
        if ($result) return array(array('ErrorCode' => -1, 'ErrorAt' => 'sp_ad_container_save'));
        */

        $result = $model->SelectList(array(
            'fields'    => array('MAX(sort_no) AS sort_no'), 
            'where'     => array('conditions' => 'ad_container_id = ?', 'arguments' => array($ad_container_id))
        ));
        
        $sort_no = isset($result) && isset($result[0]) && !empty($result[0]['sort_no']) ? $result[0]['sort_no'] : 0;
        $sort_no = $sort_no + 1;
        
        if (!$model->Exists(array('where' => array('conditions' => 'id = ?', 'arguments' => array($id)))))
        {
            $id = $model->Insert(array(
                'ad_container_id'       => $ad_container_id, 
                'ad_customer'           => $ad_customer, 
                'sort_no'               => $sort_no, 
                'frequency'             => $frequency, 
                'price'                 => $price, 
                'n_user_day'            => $n_user_day, 
                'n_total'               => $n_total, 
                'start_at'              => $start_at, 
                'expire_at'             => $expire_at,
                'text'                  => $text,
                'src'                   => $src,
                'url'                   => $url,
                'display_type'          => $display_type,
                'pages'                 => $pages,
                'weight'                => 1,
                'is_deleted'            => 0,
                'created_at'            => date("Y-m-d H:i:s"),
                'created_by'            => $user_id,
                'modified_at'           => date("Y-m-d H:i:s"),
                'modified_by'           => $user_id
            ));
            
            $guid = substr(md5(date("Y-m-d H:i:s")), 0, 10) . $id;
            $model->Update($id, array('guid' => $guid));
            
            //$result = $model->SelectSingle(array('fields' => 'MAX(id) AS id', 'where' => array('conditions' => 'created_by = ?', 'arguments' => array($user_id))));
            //$id     = $result['id'];
        }
        else
        {
            $params = array(
                'ad_container_id'       => $ad_container_id, 
                'ad_customer'           => $ad_customer, 
                'frequency'             => $frequency, 
                'price'                 => $price, 
                'n_user_day'            => $n_user_day, 
                'n_total'               => $n_total, 
                'start_at'              => $start_at, 
                'expire_at'             => $expire_at,
                'text'                  => $text,
                'url'                   => $url,
                'display_type'          => $display_type,
                'pages'                 => $pages,
                'created_at'            => date("Y-m-d H:i:s"),
                'created_by'            => $user_id,
                'modified_at'           => date("Y-m-d H:i:s"),
                'modified_by'           => $user_id
            );
            
            if (!empty($src)) $params['src'] = $src;
            
            $id = $model->Update($id, $params);
        }

        return array(array(array('id' => $id))); 
    }    
    
    /**
     * Удаляет ссылки по которым выден баннер
     * 
     * @param mixed $params
     */
    function sp_ad_banner_remove_urls($params)
    {
        list($banner_id) = $params;
        
        $model = new Model('ad_banners');
        return $model->table->_exec_raw_query("DELETE FROM ad_banner_urls WHERE ad_banner_id = " . $banner_id . ";");        
    }
    
    /**
     * Сохраняет страницу в которой показывается баннер
     * 
     * @param mixed $params
     */
    function sp_ad_banner_save_url($params)
    {
        list($banner_id, $url, $is_strict) = $params;
        
        $url = '/' . trim(str_replace('*', '', $url), '/');
        
        $model = new Model('ad_banners');
        return $model->table->_exec_raw_query("
            INSERT INTO ad_banner_urls
            SET
                ad_banner_id = " . $banner_id . ",
                url = '" . $url . "',
                is_strict = " . $is_strict
        . ";");        
    }    
    
    /**
     * Возвращает баннер по guid
     * 
     * @param mixed $params
     * @return mixed
     */
    function sp_ad_banner_get_by_guid($params)
    {
        list($guid) = $params;
        
        $model  = new Model('ad_banners');
        $result = $model->SelectSingle(array(
            'fields'    => 'id', 
            'where'     => array('conditions' => 'guid = ?', 'arguments' => array($guid))
        ));
        
        return $result;
    }
    
    /**
     * Регистрирует показ баннера
     * 
     * @param mixed $params
     */
    function sp_ad_banner_register_show($params)
    {
        list($container_id, $banner_id, $user_id, $remote_addr, $http_user_agent) = $params;
        
        // получение последнего индекса баннера для контейнера и пользователя
        if ($user_id > 0)
        {
            $where = array('conditions' => 'user_id = ?', 'arguments' => array($user_id));
        }
        else
        {
            $where = array('conditions' => 'remote_addr = ? AND http_user_agent = ?', 'arguments' => array($remote_addr, $http_user_agent));
        }
        $model          = new Model('ad_banner_shows');
        $result         = $model->SelectSingle(array('fields' => 'MAX(banner_index) AS banner_index', 'where' => $where));
        $banner_index   = isset($result) && isset($result['banner_index']) ? ($result['banner_index'] + 1) : 1;
        
        $model  = new Model('ad_banner_shows');
        $model->Insert(array(
            'ad_container_id'   => $container_id,
            'ad_banner_id'      => $banner_id,
            'user_id'           => $user_id,
            'remote_addr'       => $remote_addr,
            'http_user_agent'   => $http_user_agent,
            'banner_index'      => $banner_index,
            'created_at'        => date("Y-m-d H:i:s")
        ));
    }
    
    /**
     * Возвращает 
     * 
     * @param mixed $user_id
     * @param mixed $remote_addr
     * @param mixed $http_user_agent
     */
    function sp_ad_banner_get_quick_by_ids($params)
    {
        list($ids, $user_id, $remote_addr, $http_user_agent) = $params;
        
        if (empty($ids)) return array(array('ErrorCode' => -1, 'ErrorAt' => 'sp_ad_banner_get_list_by_ids'));

        $today = "created_at BETWEEN '" . date("Y-m-d 00:00:00") . "' AND '" . date("Y-m-d 23:59:59") . "' ";
        
        if ($user_id > 0)
        {
            $user_data = "
                (SELECT COUNT(*) FROM ad_banner_shows WHERE ad_banner_id = ad_banners.id AND user_id = " . $user_id . " AND " . $today . ") AS count_user_shows, 
                IFNULL((SELECT MAX(banner_index) FROM ad_banner_shows WHERE ad_container_id = ad_banners.ad_container_id AND user_id = " . $user_id . "), 0) AS container_index 
            ";
        }
        else
        {
            $user_data = "
                (SELECT COUNT(*) FROM ad_banner_shows WHERE ad_banner_id = ad_banners.id AND remote_addr = '" . $remote_addr . "' AND http_user_agent = '" . $http_user_agent . "' AND " . $today . ") AS count_user_shows, 
                IFNULL((SELECT MAX(banner_index) FROM ad_banner_shows WHERE ad_container_id = ad_banners.ad_container_id AND remote_addr = '" . $remote_addr . "' AND http_user_agent = '" . $http_user_agent . "'), 0) AS container_index 
            ";
        }
        
        $model  = new Model('ad_banners');
        return $model->table->_exec_raw_query(
            "SELECT 
                id, "
                . $user_data . 
            "FROM ad_banners
            WHERE id IN (" . $ids . ")
            LIMIT 100;"
        );        
    }
    
    /**
    * Возвращает список url для баннера
    * 
    * @param mixed $params
    */
    function sp_ad_banner_get_urls($params)
    {
        list($banner_id) = $params;
        
        $model  = new Model('ad_banner_urls');
        $result = $model->SelectList(array(
            'fields'    => 'url, is_strict', 
            'where'     => array('conditions' => 'ad_banner_id = ?', 'arguments' => array($banner_id))
        ));
        
        return $result;        
    }
}