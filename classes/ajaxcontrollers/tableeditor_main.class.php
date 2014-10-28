<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
require_once APP_PATH . 'classes/models/mailbox.class.php';
require_once APP_PATH . 'classes/models/email.class.php';
require_once APP_PATH . 'classes/models/emailfilter.class.php';

class MainAjaxController extends ApplicationAjaxController {

    public function __construct() {
        ApplicationAjaxController::ApplicationAjaxController();

        $this->authorize_before_exec['index'] = ROLE_STAFF;
    }

    public function emailmanagercreatetable() {
        $this->table = Request::GetString('table', $_REQUEST);
        $this->page = Request::GetNumeric('page', $_REQUEST);
        $this->limit = Request::GetNumeric('rows', $_REQUEST);
        $this->sidx = Request::GetString('sidx', $_REQUEST);
        $this->sord = Request::GetString('sord', $_REQUEST);
        $biz_id_str = Request::GetString('biz_id', $_REQUEST);
        $type_id_str = Request::GetString('email_type_ids', $_REQUEST);
        $keyword = Request::GetString('email_keyword', $_REQUEST);
        $is_deleted = Request::GetString('is_deleted', $_REQUEST);
        $start = $this->limit * $this->page - $this->limit;
        $type_id = explode(', ', $type_id_str);
        $object_id = explode(', ', $biz_id_str);
        
        
        if($object_id !== '') {
            $object_alias = 'biz';
        }
        
        if (strpos($type_id_str, '10') !== false) {
            $is_deleted = 1;
            //$approve_by = $_SESSION['user']['id'];
        }
        
        if (!strpos($type_id_str, '3')) {
           // $approve_by = $_SESSION['user']['id'];
        }
        
        $email_model = new Email();
        //dg($this->limit);
        //dg($type_id_str);
        if($is_deleted == '1') {
            //TODO: make filter by biz for trash
            $current_user = $_SESSION['user']['id'];
            $object_alias = '';
            $biz_id_str = '';
            $count_deleted = $email_model->GetDeletedByUserCount($current_user, $object_alias, $biz_id_str);
            $rowset = $email_model->GetDeletedByUserList($current_user, $object_alias, $biz_id_str, $this->page, $this->limit);
            //dg($rowset);
        } else {
            $rowset = $email_model->NewGetList($object_alias, $biz_id_str, $mailbox_id, $type_id_str, $is_deleted, $keyword, $approve_by, $start, $this->limit);
        }        
//dg($rowset);
        
        foreach ($rowset['data'] as $key => $row) {
            //$selectRow = $this->currentModel->GetById($row['id']);
            //dg($row['email']);
            $responce->rows[$key] = $row['email'];
            $responce->rows[$key] = $this->fetchHtml($responce->rows[$key]);
        }
        $total_pages = ceil($rowset['count'] / $this->limit);
        $responce->page = $this->page;
        $responce->total = $total_pages;
        $responce->records = $rowset['count'];
        //$responce->query = $main_query;
        $this->_send_json($responce);        
    }
    public function createtable() {
        $this->table = Request::GetString('table', $_REQUEST);
        $this->page = Request::GetNumeric('page', $_REQUEST);
        $this->limit = Request::GetNumeric('rows', $_REQUEST);
        $this->sidx = Request::GetString('sidx', $_REQUEST);
        $this->sord = Request::GetString('sord', $_REQUEST);

        $this->currentModel = new $this->table();

        $type_ids = Request::GetString('email_type_ids', $_REQUEST);
        $keyword = Request::GetString('email_keyword', $_REQUEST);

        $search_flag = Request::GetBoolean('_search', $_REQUEST);
        
        $where = '';
        if ($search_flag) {
            $where = ' WHERE ' . $this->createWhere($_REQUEST['filters']);
        }

        $biz_id = Request::GetString('biz_id', $_REQUEST);
        if ($biz_id !== "") {
            $email_model = new Email();
            $email_model->table->table_name = 'email_objects';
            $arg = array(
                'field' => 'email_id',
                'where' => 'email_objects.`object_alias` =  "biz" AND email_objects.object_id IN ('.$biz_id.')',
            );
            $email_ids_arr_raw = $email_model->table->SelectList($arg);
            foreach ($email_ids_arr_raw as $row) {
                $email_ids_arr[]=$row['email_id'];
            }
            /*
            $resource_biz_ids = $this->currentModel->table->_exec_raw_query("SELECT GROUP_CONCAT( DISTINCT  `email_id` SEPARATOR  ',' ) 
                                                                    FROM email_objects AS eb
                                                                    WHERE eb.`object_alias` =  'biz'
                                                                    AND eb.`object_id` IN ('{$biz_id}')");
            $result_biz_ids = $this->currentModel->table->_fetch_array($resource_biz_ids);
            foreach ($result_biz_ids[0] as $key => $row) {
                $email_ids = $row;
            }
             * 
             */
            //dg($email_ids);
            $email_ids = implode(', ', $email_ids_arr);
            if ($email_ids !== '' && $where !== '') {
                $where .= 'AND id IN (' . $email_ids . ') ';
            } elseif ($email_ids !== '' && $where == '') {
                $where = 'WHERE id IN (' . $email_ids . ') ';
            } elseif($email_ids == "" && $where == '') {
                $where = "";
            }
            //dg("SELECT COUNT(*) AS count FROM {$this->currentModel->table->table_name} {$where}");
        }

        if ($type_ids !== '') {
            $my_draft = '';
            $my_trash = '';
            if (strpos($type_ids, '3')) {

                $type_ids = str_replace('3', '999', $type_ids); //
                $my_draft = ' OR (type_id = 3 AND modified_by = "' . $_SESSION['user']['id'] . '") OR (type_id = 3 AND approve_by = "' . $_SESSION['user']['id'] . '")';
            }
            if (!strpos($type_ids, '10')) {

                $type_ids = str_replace('10', '999', $type_ids);
                $my_trash = ' OR (is_deleted = 1 AND deleted_by = "' . $_SESSION['user']['id'] . '") ';
            }
            if ($type_ids !== '' && $where !== '') {
                $where .= 'AND (type_id IN (' . $type_ids . ') ' . $my_draft . ')' . $my_trash;
            } elseif ($type_ids !== '' && $where == '') {
                $where = 'WHERE (type_id IN (' . $type_ids . ') ' . $my_draft . ')' . $my_trash;
            }
        } else {
            if ($where !== '') {
                $where .= 'AND (type_id IN (1,2,5)) AND is_deleted = 0 ';
            } elseif ($where == '') {
                $where = ' WHERE (type_id IN (1,2,5)) AND is_deleted = 0 ';
            }
        }

        //dg($where);

        if ($keyword !== '') {
            if ($keyword !== '' && $where !== '') {
                $where .= 'AND (subject LIKE "%' . $keyword . '%" OR description LIKE "%' . $keyword . '%") ';
            } elseif ($keyword !== '' && $where == '') {
                $where = ' WHERE (subject LIKE "%' . $keyword . '%" OR description LIKE "%' . $keyword . '%") ';
            }
        }



        $resource = $this->currentModel->table->_exec_raw_query("SELECT COUNT(*) AS count FROM {$this->currentModel->table->table_name} {$where}");
        $result = $this->currentModel->table->_fetch_array($resource);

        $count = $result['0']['count'];
        if ($count > 0) {
            $total_pages = ceil($count / $this->limit);
        } else {
            $total_pages = 0;
        }
        if ($this->page > $total_pages)
            $this->page = $total_pages;
        $start = $this->limit * $this->page - $this->limit; // do not put $limit*($page - 1)
        if ($start < 0)
            $start = 0;

        $main_query = "SELECT id FROM {$this->currentModel->table->table_name}  {$where} ORDER BY {$this->sidx} {$this->sord} LIMIT {$start} , {$this->limit}";
        //dg($main_query);
        $resource = $this->currentModel->table->_exec_raw_query("SELECT id FROM {$this->currentModel->table->table_name}  {$where} ORDER BY {$this->sidx} {$this->sord} LIMIT {$start} , {$this->limit}");
        $result = $this->currentModel->table->_fetch_array($resource);
        foreach ($result as $key => $row) {
            $selectRow = $this->currentModel->GetById($row['id']);
            $responce->rows[$key] = $selectRow['email'];
            $responce->rows[$key] = $this->fetchHtml($responce->rows[$key]);
        }

        $responce->page = $this->page;
        $responce->total = $total_pages;
        $responce->records = $count;
        $responce->query = $main_query;
        $this->_send_json($responce);
    }

    private function createWhere($filters_json) {
        $filters = json_decode($filters_json);

        //AND || OR
        $groupOp = $filters->groupOp;

        $where = array();
        foreach ($filters->rules as $row) {
            switch ($row->op) {
                case 'eq':
                    $operation = $row->field . '="' . $row->data . '"';
                    break;
                case 'cn':
                    $operation = $row->field . ' LIKE "%' . $row->data . '%"';
                    break;
                default :
                    $operation = $row->field . '="' . $row->data . '"';
            }
            $where[] = $operation;
        }

        $where_string = implode(' ' . $groupOp . ' ', $where);
        //dg($where_string);
        return $where_string;
    }

    private function fetchHtml($row) {
        switch ($this->table) {
            case 'Email':
               /* if (key_exists('email_id', $row)) {
                    $this->_assign('row', $row);
                    //dg($row);
                    $row['object_alias_html'] = $this->smarty->fetch('templates/table/emailmanager/objectid.tpl');
                    $row['action_html'] = $this->smarty->fetch('templates/table/emailmanager/action.tpl');
                }*/
                    $this->_assign('row', $row);
                    //dg($row);
                    $row['object_alias_html'] = $this->smarty->fetch('templates/table/emailmanager/objectid.tpl');
                    $row['action_html'] = $this->smarty->fetch('templates/table/emailmanager/action.tpl');
                if (key_exists('type_id', $row)) {
                    switch ($row['type_id']) {
                        case '0':
                            $type = '<i>not set</i>';
                            break;
                        case '1':
                            $type = 'inbox';
                            break;
                        case '2':
                            $type = 'sent';
                            break;
                        case '3':
                            $type = 'draft';
                            break;
                        case '4':
                            $type = 'error';
                            break;
                        case '5':
                            $type = 'spam';
                            break;
                    }
                    $row['type_id_html'] = $type;
                }
        }

        return $row;
    }

}
