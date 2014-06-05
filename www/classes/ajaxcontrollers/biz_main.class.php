<?php
require_once APP_PATH . 'classes/models/biz.class.php';

class MainAjaxController extends ApplicationAjaxController
{    

    function MainAjaxController()
    {
        ApplicationAjaxController::ApplicationAjaxController();
                
        $this->authorize_before_exec['getcompanies']    = ROLE_STAFF;
        $this->authorize_before_exec['getlistbytitle']  = ROLE_STAFF;        
    }
        
    /**
     * get company list for specified biz
     * url: /biz/getcompanies
     */
    function getcompanies()
    {
        $biz_id = Request::GetInteger('biz_id', $_REQUEST);
        $role   = Request::GetString('role', $_REQUEST);

        $bizes  = new Biz();
        $this->_send_json(array('result' => 'okay', 'companies' => $this->_prepare_list($bizes->GetCompanies($biz_id, $role), 'company')));
    }
    
    /**
     * get biz list by title
     * url: /biz/getlistbytitle
     */
    function getlistbytitle()
    {
        $rows_count     = Request::GetInteger('maxrows', $_REQUEST);
        $title          = Request::GetString('title', $_REQUEST);
        $team_id        = Request::GetInteger('team_id', $_REQUEST);
        $title_field    = Request::GetString('title_field', $_REQUEST, 'doc_no_full');

        $bizes      = new Biz();
        $data_set   = $bizes->GetListByTitle($title, $rows_count);

        foreach ($data_set as $key => $row)
        {
            if (isset($row['biz']))
            {
                $row = $row['biz'];
                
                if ($team_id > 0 && $row['biz']['team_id'] != $team_id) 
                {
                    unset($data_set[$key]);
                    continue;
                }
                
                if ($title_field == 'doc_no_chat')
                {
                    $data_set[$key]['biz']['list_title'] = (isset($row['team']) ? $row['team']['title'] . '.' : '') . $row['doc_no_full'];
                }
                else
                {
                    $data_set[$key]['biz']['list_title'] = $row[$title_field];
                }
            }
            else
            {
                unset($data_set[$key]);
            }
        }

        $this->_send_json(array('result' => 'okay', 'list' => $data_set));
    }
}
