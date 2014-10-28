<?php

require_once APP_PATH . 'classes/models/biz.class.php';

class MainAjaxController extends ApplicationAjaxController {

    function MainAjaxController() {
        ApplicationAjaxController::ApplicationAjaxController();

        $this->authorize_before_exec['getcompanies'] = ROLE_STAFF;
        $this->authorize_before_exec['getlistbytitle'] = ROLE_STAFF;
    }

    /**
     * get company list for specified biz
     * url: /biz/getcompanies
     */
    function getcompanies() {
        $biz_id = Request::GetInteger('biz_id', $_REQUEST);
        $role = Request::GetString('role', $_REQUEST);

        $bizes = new Biz();
        $this->_send_json(array('result' => 'okay', 'companies' => $this->_prepare_list($bizes->GetCompanies($biz_id, $role), 'company')));
    }

    /**
     * get biz list by title
     * url: /biz/getlistbytitle
     */
    function getlistbytitle() {
        $rows_count = Request::GetInteger('maxrows', $_REQUEST);
        $title = Request::GetString('title', $_REQUEST);
        $team_id = Request::GetInteger('team_id', $_REQUEST);
        $title_field = Request::GetString('title_field', $_REQUEST, 'doc_no_full');

        $bizes = new Biz();
        $data_set = $bizes->GetListByTitle($title, $rows_count);

        foreach ($data_set as $key => $row) {
            if (isset($row['biz'])) {
                $row = $row['biz'];

                if ($team_id > 0 && $row['biz']['team_id'] != $team_id) {
                    unset($data_set[$key]);
                    continue;
                }

                if ($title_field == 'doc_no_chat') {
                    $data_set[$key]['biz']['list_title'] = (isset($row['team']) ? $row['team']['title'] . '.' : '') . $row['doc_no_full'];
                } else {
                    $data_set[$key]['biz']['list_title'] = $row[$title_field];
                }
            } else {
                unset($data_set[$key]);
            }
        }

        $this->_send_json(array('result' => 'okay', 'list' => $data_set));
    }

    function search() {
        $objective_id = Request::GetInteger('objective_id', $_REQUEST);
        $team_id = Request::GetInteger('team_id', $_REQUEST);
        $product_id = Request::GetInteger('product_id', $_REQUEST);
        $status = Request::GetString('status', $_REQUEST);
        $market_id = Request::GetInteger('market_id', $_REQUEST);
        $driver_id = Request::GetInteger('driver_id', $_REQUEST);
        $keyword = Request::GetString('keyword', $_REQUEST);
        $company_id = Request::GetString('company_id', $_REQUEST);
        $role_id = Request::GetInteger('role_id', $_REQUEST);
        //dg($company_id);
        $bizes = new Biz();
        $rowset = $bizes->Search($keyword, $company_id, $role_id, $objective_id, $team_id, $product_id, $status, $market_id, $driver_id, 0);
        $this->_assign('list', $rowset['data']);
        $html = $this->smarty->fetch('templates/html/biz/control_biz_search_result.tpl');
        $this->_send_json(array(
            'result' => 'okay',
            'html' => $html,
            'rowset' => $rowset['data'],
                )
        );
    }

    /**
     * savemenu()
     * 
     * Вызывает методы сохранения меню бизов в eMM
     */
    function savemenu() {
        $group_id = Request::GetInteger('group_id', $_REQUEST);
        $group_title = Request::GetString('group_title', $_REQUEST);
        $biz_ids = Request::GetString('biz_ids', $_REQUEST);
        $biz_ids = str_replace(',', '', $biz_ids);
        $biz_ids_arr = explode(' ', $biz_ids);
        $today = date("Y-m-d h:m:s");
        $biz_model = new Biz();
        //dg($biz_ids_arr);

        if ($group_title !== "") {
            $q_add_group = "INSERT INTO biz_navigation_group"
                    . "(title, created_at, created_by)"
                    . "VALUES"
                    . "('{$group_title}', '{$today}', '{$_SESSION['user']['id']}');";
            $biz_model->table->_exec_raw_query($q_add_group);
            $biz_model->table->table_name = 'biz_navigation_group';


            $arg = array(
                'fields' => 'id',
                'where' => "title = '{$group_title}'",
            );
            $added_group = $biz_model->table->SelectList($arg);
            //dg($added_group);
            $group_id = $added_group['0']['id'];
            //$group_id = 777;*/
        }

        $biz_model->table->table_name = 'biz_navigation';
        foreach ($biz_ids_arr as $row) {
            $q = "INSERT INTO biz_navigation"
                    . "(biz_id, biz_group_id, created_at, created_by)"
                    . "VALUES"
                    . "('{$row}', '{$group_id}', '{$today}', '" . $_SESSION['user']['id'] . "');";
            $biz_model->table->_exec_raw_query($q);
        }

        $biz_menu = $biz_model->GetBizMenu();
        $biz_model->table->table_name = 'bizes';

        $this->_assign('biz_menu', $biz_menu);
        $html_menu = $this->smarty->fetch('templates/html/emailmanager/control_biz_menu.tpl');

        $this->_assign('biz_menu', $biz_menu);
        $html_menu_group = $this->smarty->fetch('templates/html/emailmanager/control_biz_group_select.tpl');

        $this->_send_json(array(
            'result' => 'okay',
            //'html' => $html,
            'query-add-bizs' => $q,
            'query-add-group' => $q_add_group,
            'menu' => $biz_menu,
            'html_menu' => $html_menu,
            'html_menu_group_select' => $html_menu_group,
                //'rowset' => $rowset['data'],
                )
        );
    }

    function getgroupbizs() {
        $group_id = Request::GetInteger('group_id', $_REQUEST);

        $biz_model = new Biz();
        $biz_model->table->table_name = 'biz_navigation';
        $arg = array(
            'fields' => 'biz_id',
            'where' => 'biz_group_id = "' . $group_id . '"',
        );
        $bizs = $biz_model->table->SelectList($arg);

        $biz_ids = "";
        $biz_ids_arr = array();
        foreach ($bizs as $row) {
            $biz_ids_arr[] = $row['biz_id'];
        }
        $biz_ids = implode(', ', $biz_ids_arr);
        $this->_send_json(array(
            'result' => 'okay',
            'biz_ids' => $biz_ids,
                )
        );
    }

    function deletegroupbizs() {
        $group_id = Request::GetInteger('group_id', $_REQUEST);
        
        $biz_model = new Biz();
        $q_del_group = "DELETE FROM biz_navigation_group"
                . " WHERE id = '{$group_id}';";
        $biz_model->table->_exec_raw_query($q_del_group);
        
        $biz_model->table->table_name = 'biz_navigation';
        $biz_menu = $biz_model->GetBizMenu();
        $biz_model->table->table_name = 'bizes';

        $this->_assign('biz_menu', $biz_menu);
        $html_menu = $this->smarty->fetch('templates/html/emailmanager/control_biz_menu.tpl');

        $this->_assign('biz_menu', $biz_menu);
        $html_menu_group = $this->smarty->fetch('templates/html/emailmanager/control_biz_group_select.tpl');
        
        $this->_send_json(array(
            'result' => 'okay',
            //'menu' => $biz_menu,
            'html_menu' => $html_menu,
            'html_menu_group_select' => $html_menu_group,
                //'rowset' => $rowset['data'],
                )
        );        
    }
    
    function deletemenubizs() {
        $group_id = Request::GetInteger('group_id', $_REQUEST);
        $biz_id = Request::GetInteger('biz_id', $_REQUEST);
        
        $biz_model = new Biz();
        $q_del_biz = "DELETE FROM biz_navigation"
                . " WHERE biz_group_id = '{$group_id}' AND biz_id = '{$biz_id}';";
        $biz_model->table->_exec_raw_query($q_del_biz);
        
        $biz_model->table->table_name = 'biz_navigation';
        $biz_menu = $biz_model->GetBizMenu();
        $biz_model->table->table_name = 'bizes';

        $this->_assign('biz_menu', $biz_menu);
        $html_menu = $this->smarty->fetch('templates/html/emailmanager/control_biz_menu.tpl');

        $this->_assign('biz_menu', $biz_menu);
        $html_menu_group = $this->smarty->fetch('templates/html/emailmanager/control_biz_group_select.tpl');
        
        $this->_send_json(array(
            'result' => 'okay',
            //'menu' => $biz_menu,
            'html_menu' => $html_menu,
            'html_menu_group_select' => $html_menu_group,
                //'rowset' => $rowset['data'],
                )
        );        
    }

}
