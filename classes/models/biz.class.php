<?php

require_once APP_PATH . 'classes/models/company.class.php';
require_once APP_PATH . 'classes/models/market.class.php';
require_once APP_PATH . 'classes/models/objective.class.php';
require_once APP_PATH . 'classes/models/product.class.php';
require_once APP_PATH . 'classes/models/team.class.php';
require_once APP_PATH . 'classes/models/user.class.php';

class Biz extends Model {

    function Biz() {
        Model::Model('bizes');
    }

    /**
     * Возвращает список бизнесов по родителю
     * 
     * @param mixed $parent_id
     * @return mixed
     * 
     * @version 20130214, zharkov
     */
    function GetListByParent($parent_id) {
        //print_r($parent_id);
        if ($parent_id == '6971')
            $parent_id = '2376';
        $hash = 'bizes-parent-' . $parent_id;
        $cache_tags = array($hash, 'bizes');

        $rowset = $this->_get_cached_data($hash, 'sp_biz_get_list_by_parent', array($this->user_id, $parent_id), $cache_tags);
        return isset($rowset[0]) ? $this->FillMainBizInfo($rowset[0]) : null;
    }

    /**
     * Возвращает список бизнесов для виджета
     * @version 20130124, zharkov
     */
    function GetListFavourite() {
        $hash = 'fav-bizes-user-' . $this->user_id;
        $cache_tags = array($hash, 'bizes');

        $rowset = $this->_get_cached_data($hash, 'sp_biz_get_list_favourite', array($this->user_id), $cache_tags);

        $rowset = isset($rowset[0]) ? $this->FillMainBizInfo($rowset[0]) : null;

        if (!empty($rowset)) {
            foreach ($rowset as $key => $row) {
                $rowset[$key]['biz']['orders'] = $this->GetOrders($row['biz']['id']);
            }
        }

        return $rowset;
    }

    /**
     * Сохраняет признак бизнеса "любимый" для текущего пользователя
     * 
     * @param mixed $biz_id
     * 
     * @version 20130124, zharkov
     */
    function SaveIsFavourite($biz_id) {
        $this->CallStoredProcedure('sp_biz_set_is_favourite', array($this->user_id, $biz_id));

        Cache::ClearTag('fav-bizes-user-' . $this->user_id);
        Cache::ClearTag('bizesquick');
        Cache::ClearTag('bizquick-' . $this->user_id . '-biz-' . $biz_id);
    }

    /**
     * remove biz from favourite
     * 
     * @param type $biz_id
     * 
     * @version 20130724, sasha
     */
    function RemoveFromFavourite($biz_id) {
        $this->CallStoredProcedure('sp_biz_remove_from_favourite', array($this->user_id, $biz_id));

        Cache::ClearTag('fav-bizes-user-' . $this->user_id);
        Cache::ClearTag('bizesquick');
        Cache::ClearTag('bizquick-' . $this->user_id . '-biz-' . $biz_id);
    }

    /**
     * Возвращает бизнес по номеру и суффиксу
     * 
     * @param mixed $number
     * @param mixed $index
     * @return mixed
     */
    function GetByNumber($number, $suffix) {
        $hash = 'biz-number-' . $number . '-suffix-' . $suffix;
        $cache_tags = array($hash, 'bizes');

        $rowset = $this->_get_cached_data($hash, 'sp_biz_get_by_number', array($number, $suffix), $cache_tags);
        $rowset = isset($rowset[0]) ? $this->FillMainBizInfo($rowset[0]) : null;

        return isset($rowset[0]) && isset($rowset[0]['biz']) ? $rowset[0]['biz'] : null;
    }

    /**
     * Возвращает список бизнесов
     * 
     * @param mixed $keyword
     * @version 20120522, zharkov
     */
    function deprecated_Search($search_string, $company_id, $page_no = 0, $per_page = ITEMS_PER_PAGE) {
        //echo $search_string;
        //die();
        $page_no = $page_no > 0 ? $page_no : 1;
        $per_page = $per_page < 1 ? ITEMS_PER_PAGE : $per_page;
        $start = ($page_no - 1) * $per_page;

        $hash = 'bizes-search-' . md5($search_string . 'company-' . $company_id . '-' . $page_no . '-' . $per_page);
        $rowset = Cache::GetData($hash);
//$rowset = null;
        if (!isset($rowset) || !isset($rowset['data']) || isset($rowset['outdated'])) {

            $cl = new SphinxClient();
            $cl->SetLimits($start, $per_page, 5000);
            $cl->SetFieldWeights(array(
                'full_number' => 1000,
                'biz_full_number' => 1000,
                'biz_title' => 1000,
                'description' => 100,
                'driver' => 100,
                'status' => 100,
                'objective' => 10,
                'market' => 10,
                'team' => 10,
                'product' => 10,
                'biz_company_role' => 10,
                'company_title' => 1,
                'company_title_short' => 1,
                'company_title_trade' => 1
            ));

            $cl->SetMatchMode(SPH_MATCH_ALL);

            $preg = '/^\d+$/';
            $query_string = str_replace('biz', '', $search_string);
            preg_match($preg, $query_string, $matches);

            if (empty($matches)) {
                $cl->SetGroupBy('biz_id', SPH_GROUPBY_ATTR, '@weight DESC');
                $cl->SetSortMode(SPH_SORT_ATTR_DESC, 'last_access_at');
            } else {
                $cl->SetGroupBy('biz_id', SPH_GROUPBY_ATTR, 'number ASC, suffix ASC');
            }


            if (!empty($search_string))
                $search_string = '*' . str_replace(' ', '* *', $search_string) . '*';
            if (empty($company_id)) {
                $data = $cl->Query($search_string, 'ix_mam_bizes, ix_mam_bizes_delta');
            } else {
                $cl->SetFilter('company_id', array($company_id));
                $data = $cl->Query($search_string, 'ix_mam_biz_companies, ix_mam_biz_companies_delta');
            }

            if ($data === false) {
                Log::AddLine(LOG_ERROR, 'biz::search ' . $cl->GetLastError());
                return null;
            }

            $rowset = array();
            if (!empty($data['matches'])) {
                foreach ($data['matches'] as $id => $extra) {
                    $rowset[] = array('biz_id' => $extra['attrs']['biz_id']);
                }
            }

            $rowset = array(
                $rowset,
                array(array('rows' => $data['total_found']))
            );

            Cache::SetData($hash, $rowset, array('bizes', 'search'), CACHE_LIFETIME_STANDARD);

            $rowset = array(
                'data' => $rowset
            );
        }

        $result = array();
        $result['data'] = isset($rowset['data'][0]) ? $this->FillBizInfo($rowset['data'][0]) : null;
        $result['count'] = isset($rowset['data'][1]) && isset($rowset['data'][1][0]) && isset($rowset['data'][1][0]['rows']) ? $rowset['data'][1][0]['rows'] : 0;

        return $result;
    }

    /**
     * @version 20130514 Sasha
     * search bizes by company
     * @param string $search_string
     * @param type $company_ids
     * @param type $role
     * @param type $objective_id
     * @param type $team_id
     * @param type $product_id
     * @param type $status
     * @param type $market_id
     * @param type $driver_id
     * @param type $page_no
     * @param type $per_page
     * @return null
     */
    function Search($search_string, $company_ids = array(), $roles = array(), $objective_id = 0, $team_id = 0, $product_id = 0, $status = 0, $market_id = 0, $user_id = 0, $page_no = 0, $per_page = ITEMS_PER_PAGE) {
        //die($search_string);
        $page_no = $page_no > 0 ? $page_no : 1;
        $per_page = $per_page < 1 ? ITEMS_PER_PAGE : $per_page;
        $start = ($page_no - 1) * $per_page;

        $hash = 'bizes-search-' . md5($search_string . '-companies-' . serialize($company_ids) . '-role-' . serialize($roles) .
                        '-objective-' . $objective_id .
                        '-team-' . $team_id . '-product-' . $product_id . '-status-' . $status .
                        '-product' . $product_id . '-status-' . $status .
                        '-market-' . $market_id .
                        '-driver-' . $user_id .
                        '-page-' . $page_no . '-' . $per_page);

        $rowset = Cache::GetData($hash);

        if (!isset($rowset) || !isset($rowset['data']) || isset($rowset['outdated'])) {
            /*
             * the intersection of arrays
             */
            if (!empty($company_ids)) {

                foreach ($company_ids as $key => $row) {
                    $rowset[] = $this->GetListByCompanyAndRole($row['company_id'], isset($roles[$key]) ? $roles[$key] : 0);
                }

                if (!empty($rowset) && count($company_ids) == 1) {
                    $bizes_ids = $rowset[0];
                } else if (isset($rowset) && !empty($rowset)) {
                    $bizes_ids = $rowset[0];

                    foreach ($rowset as $key => $value) {
                        if (empty($value)) {
                            $bizes_ids = array(0);
                            break;
                        }

                        if ($key > 0) {
                            $bizes_ids = array_intersect($bizes_ids, $value);
                        }
                    }
                }
            }

            if (empty($company_ids) || (!empty($company_ids) && !empty($bizes_ids))) {
                $cl = new SphinxClient();
                $cl->SetLimits($start, $per_page, 5000);
                $cl->SetFieldWeights(array(
                    'full_number' => 1000,
                    'biz_full_number' => 1000,
                    'biz_title' => 1000,
                    'description' => 100
                ));

                $cl->SetMatchMode(SPH_MATCH_ALL);

                $preg = '/^\d+$/';
                $query_string = str_replace('biz', '', $search_string);
                preg_match($preg, $query_string, $matches);

                if (empty($matches)) {
                    $cl->SetGroupBy('biz_id', SPH_GROUPBY_ATTR, '@weight DESC');
                    $cl->SetSortMode(SPH_SORT_ATTR_DESC, 'last_access_at');
                } else {
                    $cl->SetGroupBy('biz_id', SPH_GROUPBY_ATTR, 'number ASC, suffix ASC');
                }

                if (!empty($search_string))
                    $search_string = '*' . str_replace('-', '\-', str_replace(' ', '* *', $search_string)) . '*';

                $product_ids = array();

                if ($product_id > 0) {
                    $product_model = new Product();
                    $product_list = $product_model->GetBranch($product_id);

                    foreach ($product_list as $row) {
                        if (isset($row['product_id']) && $row['product_id'] > 0)
                            $product_ids[] = $row['product_id'];
                    }

                    $product_ids[] = $product_id;
                }

                /* filter */
                if (isset($bizes_ids))
                    $cl->SetFilter('biz_id', $bizes_ids);
                if ($objective_id > 0)
                    $cl->SetFilter('objective_id', array($objective_id));
                if ($team_id > 0)
                    $cl->SetFilter('team_id', array($team_id));
                if (!empty($product_ids))
                    $cl->SetFilter('product_id', $product_ids);
                if ($market_id > 0)
                    $cl->SetFilter('market_id', array($market_id));
                if ($user_id > 0)
                    $cl->SetFilter('user_id', array($user_id));
                if (!empty($status))
                    $cl->SetFilter('status_id', array(sprintf("%u", crc32($status) & 0xffffffff)));


                if ($user_id > 0) {
                    $data = $cl->Query($search_string, 'ix_mam_biz_search_users, ix_mam_biz_search_users_delta');
                } else {
                    $data = $cl->Query($search_string, 'ix_mam_biz_search, ix_mam_biz_search_delta');
                }

                if ($data === false) {
                    Log::AddLine(LOG_ERROR, 'biz::search ' . $cl->GetLastError());
                    return null;
                }

                $rowset = array();
                if (!empty($data['matches'])) {
                    foreach ($data['matches'] as $id => $extra) {
                        $rowset[] = array('biz_id' => $extra['attrs']['biz_id']);
                    }
                }

                $rowset = array(
                    $rowset,
                    array(array('rows' => $data['total_found']))
                );

                Cache::SetData($hash, $rowset, array('bizes', 'filter'), CACHE_LIFETIME_30S);   // CACHE_LIFETIME_STANDARD

                $rowset = array(
                    'data' => $rowset
                );
            }
        }

        $result = array();
        $result['data'] = isset($rowset['data'][0]) ? $this->FillBizInfo($rowset['data'][0]) : null;
        $result['count'] = isset($rowset['data'][1]) && isset($rowset['data'][1][0]) && isset($rowset['data'][1][0]['rows']) ? $rowset['data'][1][0]['rows'] : 0;
        //dg($result['data']);
        //sasha add active orders
        if (!empty($result['data'])) {
            foreach ($result['data'] as $key => $row) {
                $result['data'][$key]['biz']['orders'] = $this->GetOrders($row['biz']['id']);
            }
        }

        return $result;
    }

    /**
     * @version 20130516, Sasha
     * bizes list by company and role
     * @param type $company_id
     * @param type $role
     * @return type
     */
    function GetListByCompanyAndRole($company_id, $role = 0) {
        $hash = 'bizes-list-company-' . $company_id . '-and-role-' . $role;
        $cache_tags = array($hash, 'bizes', 'company-' . $company_id);

        $rowset = $this->_get_cached_data($hash, 'sp_biz_get_list_by_company_and_role', array($company_id, $role), $cache_tags);

        $rowset = isset($rowset[0]) && !empty($rowset[0]) ? $rowset[0] : array();

        foreach ($rowset as $row) {
            $result[] = $row['biz_id'];
        }

        return (isset($result) && !empty($result)) ? $result : null;
    }

    /**
     * Сохраняет компании
     * 
     * @param mixed $biz_id
     * @param mixed $companies
     * @param mixed $role
     */
    function SaveCompanies($biz_id, $companies, $role) {
        // сохраняет навигаторов бизнеса
        foreach ($this->GetCompanies($biz_id, $role) as $row) {
            if (array_key_exists($row['company_id'], $companies)) {
                unset($companies[$row['company_id']]);
            } else {
                $this->RemoveCompany($biz_id, $row['company_id'], $role);
            }
        }

        foreach ($companies as $row) {
            $this->SaveCompany($biz_id, $row['company_id'], $role);
        }
    }

    /**
     * Убирает компанию из бизнеса
     * 
     * @param mixed $biz_id
     * @param mixed $company_id
     * @param mixed $role
     */
    function RemoveCompany($biz_id, $company_id, $role) {
        $this->CallStoredProcedure('sp_biz_remove_company', array($biz_id, $company_id, $role));
        Cache::ClearTag('biz-' . $biz_id . '-companies');
        Cache::ClearTag('company-' . $company_id . '-bizes');
    }

    /**
     * Добавляет компанию к бизнесу
     * 
     * @param mixed $biz_id
     * @param mixed $company_id
     * @param mixed $role
     */
    function SaveCompany($biz_id, $company_id, $role) {
        $this->CallStoredProcedure('sp_biz_save_company', array($this->user_id, $biz_id, $company_id, $role));
        Cache::ClearTag('biz-' . $biz_id . '-companies');
        Cache::ClearTag('company-' . $company_id . '-bizes');
    }

    /**
     * Сохраняет навигаторов
     * 
     * @param mixed $navigators
     */
    function SaveNavigators($biz_id, $navigators) {
        // сохраняет навигаторов бизнеса
        foreach ($this->GetNavigators($biz_id, false) as $row) {
            $user_id = $row['user_id'];

            if (isset($navigators[$user_id]) && ((isset($navigators[$user_id]['is_driver']) && !empty($row['is_driver'])) || (!isset($navigators[$user_id]['is_driver']) && empty($row['is_driver'])))
            ) {
                unset($navigators[$user_id]);
            } else {
                $this->RemoveUser($biz_id, $user_id);
            }
        }

        foreach ($navigators as $row) {
            $this->SaveUser($biz_id, $row['user_id'], isset($row['is_driver']) ? 1 : 0);
        }
    }

    /**
     * Убирает навигатора из бизнеса
     * 
     * @param mixed $biz_id
     * @param mixed $user_id
     */
    function RemoveUser($biz_id, $user_id) {
        $this->CallStoredProcedure('sp_biz_remove_user', array($biz_id, $user_id));
        Cache::ClearTag('biz-' . $biz_id . '-users');

        Cache::ClearTag('email-bizes-for-menu-userid' . $this->user_id);
        Cache::ClearTag('email-bizes-for-menu');
    }

    /**
     * Сохраняет навигатора бизнеса
     * 
     * @param mixed $biz_id
     * @param mixed $user_id
     */
    function SaveUser($biz_id, $user_id, $is_driver = 0) {
        $this->CallStoredProcedure('sp_biz_save_user', array($this->user_id, $biz_id, $user_id, $is_driver));
        Cache::ClearTag('biz-' . $biz_id . '-users');

        Cache::ClearTag('email-bizes-for-menu-userid' . $this->user_id);
        Cache::ClearTag('email-bizes-for-menu');
    }

    /**
     * Возвращает список навигаторов бизнеса
     * 
     * @param mixed $biz_id
     * @param mixed $strict - если установлен. вернутся только навигаторы, драйвер исключится
     * 
     * @return mixed
     * 
     * @version 20130124, zharkov
     */
    function GetNavigators($biz_id, $strict = true) {
        $hash = 'biz-' . $biz_id . '-users';
        $cache_tags = array($hash, 'bizes', 'biz-' . $biz_id);

        $rowset = $this->_get_cached_data($hash, 'sp_biz_get_users', array($biz_id), $cache_tags);
        $rowset = isset($rowset[0]) ? $rowset[0] : array();

        if ($strict) {
            foreach ($rowset as $key => $row) {
                if ($row['is_driver'] > 0) {
                    unset($rowset[$key]);
                }
            }
        }

        return $rowset;
    }

    /**
     * Список компаний определенной роли из бизнеса
     * 
     * @param mixed $biz_id
     * @param mixed $role
     */
    function GetCompanies($biz_id, $role = '') {
        $hash = 'biz-' . $biz_id . '-companies';
        $cache_tags = array($hash, 'bizes', 'biz-' . $biz_id);

        $rowset = $this->_get_cached_data($hash, 'sp_biz_get_companies', array($biz_id), $cache_tags);
        $rowset = isset($rowset[0]) ? $rowset[0] : array();

        if (!empty($role) && !empty($rowset)) {
            foreach ($rowset as $key => $row)
                if ($row['role'] != $role)
                    unset($rowset[$key]);
        }

        $companies = new Company();
        return $companies->FillCompanyInfoShort($rowset);
    }

    /**
     * Возвращает список компаний для бизнеса выбранной роли
     * 
     * @param mixed $biz_id
     * @param mixed $role
     */
    function GetCompaniesByRole($biz_id, $role) {
        $rowset = $this->GetCompanies($biz_id, $role);

        $companies = new Company();
        $rowset = !empty($rowset) ? $companies->FillCompanyInfo($rowset) : array();

        return $rowset;
    }

    /**
     * Возвращает список бизнесов для компании
     *     
     * @param mixed $title
     * @param mixed $rows_count
     */
    function GetListByTitle($title, $rows_count) {

        $result = $this->Search($title, array(), array(), 0, 0, 0, 0, 0, 0, 0, $rows_count);
        return isset($result['data']) && !empty($result['data']) ? $result['data'] : array();
    }

    /**
     * Возвращает список бизнесов команды
     * 
     * @param mixed $team_id
     * 
     * @version 20120710, zharkov
     */
    function GetListByTeam($team_id) {
        $hash = 'bizes-team-' . $team_id;
        $cache_tags = array($hash, 'bizes');

        $rowset = $this->_get_cached_data($hash, 'sp_biz_get_list_by_team', array($team_id), $cache_tags);
        $rowset = isset($rowset[0]) ? $this->FillMainBizInfo($rowset[0]) : array();

        return $rowset;
    }

    /**
     * Возвращает список бизнесов для компании
     * 
     * @param mixed $company_id
     * @param mixed $role
     */
    function GetListByCompany($company_id, $role) {
        $hash = 'bizes-company-' . $company_id . '-role-' . $role;
        $cache_tags = array($hash, 'bizes', 'company-' . $company_id);

        $rowset = $this->_get_cached_data($hash, 'sp_biz_get_list_by_company', array($company_id, $role), $cache_tags);
        $rowset = isset($rowset[0]) ? $this->FillBizInfo($rowset[0]) : array();

        return $rowset;
    }

    /**
     * Возвращает бизнес по идентификатору
     *     
     * @param mixed $id
     */
    function GetById($id) {
        $dataset = $this->FillBizInfo(array(array('biz_id' => $id)));
        return isset($dataset) && isset($dataset[0]) && isset($dataset[0]['biz']) ? $dataset[0] : null;
    }

    /**
     * Возвращает главную инфоармацию о бизнесе
     * 
     * @param mixed $rowset
     * @param mixed $id_fieldname
     * @param mixed $entityname
     * @param mixed $cache_prefix
     * @return array
     * 
     * @version 20120603, zharkov
     */
    function FillMainBizInfo($rowset, $id_fieldname = 'biz_id', $entityname = 'biz', $cache_prefix = 'biz') {
        $rowset = $this->_fill_entity_info($rowset, $id_fieldname, $entityname, $cache_prefix, 'sp_biz_get_list_by_ids', array('bizes' => '', 'biz' => 'id'), array());

        foreach ($rowset as $key => $row) {
            if (!isset($row[$entityname]))
                continue;
            $row = $row[$entityname];

            if (isset($row['number'])) {
                $rowset[$key][$entityname]['number_output'] = 'BIZ' . $row['number'] . (!empty($row['suffix']) ? '.' . $row['suffix'] : '');
                $rowset[$key][$entityname]['doc_no'] = 'BIZ' . substr((10000 + $row['number']), 1) . (!empty($row['suffix']) ? '.' . ($row['suffix'] > 9 ? $row['suffix'] : '0' . $row['suffix']) : '');
                $rowset[$key][$entityname]['doc_no_full'] = $rowset[$key][$entityname]['doc_no'] . ' ' . $row['title'];
            }

            if ($row['status'] == 'idea')
                $rowset[$key][$entityname]['status_title'] = 'Idea';
            if ($row['status'] == 'marketing')
                $rowset[$key][$entityname]['status_title'] = 'Marketing';
            if ($row['status'] == 'negotiation')
                $rowset[$key][$entityname]['status_title'] = 'Negotiation';
            if ($row['status'] == 'admin')
                $rowset[$key][$entityname]['status_title'] = 'Contract Administration';
            if ($row['status'] == 'closed')
                $rowset[$key][$entityname]['status_title'] = 'Contracted & Closed';
            if ($row['status'] == 'repeat')
                $rowset[$key][$entityname]['status_title'] = 'Contracted & Repeat Negotiation';
            if ($row['status'] == 'suspended')
                $rowset[$key][$entityname]['status_title'] = 'Suspended';
            if ($row['status'] == 'abandoned')
                $rowset[$key][$entityname]['status_title'] = 'Abandoned';
            if ($row['status'] == 'concluded')
                $rowset[$key][$entityname]['status_title'] = 'Concluded';
        }

        return $rowset;
    }

    /**
     * Возвращет информацию о бизнесе
     * 
     * @param mixed $rowset
     * @param mixed $id_fieldname
     * @param mixed $entityname
     * @param mixed $cache_prefix
     */
    function FillBizInfo($rowset, $id_fieldname = 'biz_id', $entityname = 'biz', $cache_prefix = 'biz') {
        $rowset = $this->FillMainBizInfo($rowset, $id_fieldname, $entityname, $cache_prefix);

        foreach ($rowset as $key => $row) {
            if (!isset($row[$entityname]))
                continue;
            $row = $row[$entityname];

            if (isset($row['number'])) {
                $rowset[$key][$entityname]['number_output'] = 'BIZ' . $row['number'] . (!empty($row['suffix']) ? '.' . $row['suffix'] : '');
                $rowset[$key][$entityname]['doc_no'] = 'BIZ' . substr((10000 + $row['number']), 1) . (!empty($row['suffix']) ? '.' . ($row['suffix'] > 9 ? $row['suffix'] : '0' . $row['suffix']) : '');

                if ($row['status'] == 'idea')
                    $rowset[$key][$entityname]['status_title'] = 'Idea';
                if ($row['status'] == 'marketing')
                    $rowset[$key][$entityname]['status_title'] = 'Marketing';
                if ($row['status'] == 'negotiation')
                    $rowset[$key][$entityname]['status_title'] = 'Negotiation';
                if ($row['status'] == 'admin')
                    $rowset[$key][$entityname]['status_title'] = 'Contract Administration';
                if ($row['status'] == 'closed')
                    $rowset[$key][$entityname]['status_title'] = 'Contracted & Closed';
                if ($row['status'] == 'repeat')
                    $rowset[$key][$entityname]['status_title'] = 'Contracted & Repeat Negotiation';
                if ($row['status'] == 'suspended')
                    $rowset[$key][$entityname]['status_title'] = 'Suspended';
                if ($row['status'] == 'abandoned')
                    $rowset[$key][$entityname]['status_title'] = 'Abandoned';
                if ($row['status'] == 'concluded')
                    $rowset[$key][$entityname]['status_title'] = 'Concluded';
            }

            $rowset[$key]['biz_driver_id'] = $row['driver_id'];
            $rowset[$key]['biz_market_id'] = $row['market_id'];
            $rowset[$key]['biz_objective_id'] = $row['objective_id'];
            $rowset[$key]['biz_product_id'] = $row['product_id'];
            $rowset[$key]['biz_team_id'] = $row['team_id'];
            $rowset[$key]['biz_author_id'] = $row['created_by'];
            $rowset[$key]['biz_modifier_id'] = $row['modified_by'];
        }

        $markets = new Market();
        $rowset = $markets->FillMarketMainInfo($rowset, 'biz_market_id', 'biz_market');

        $objectives = new Objective();
        $rowset = $objectives->FillObjectiveMainInfo($rowset, 'biz_objective_id', 'biz_objective');

        $teams = new Team();
        $rowset = $teams->FillTeamMainInfo($rowset, 'biz_team_id', 'biz_team');

        $products = new Product();
        $rowset = $products->FillProductMainInfo($rowset, 'biz_product_id', 'biz_product');

        $users = new User();
        $rowset = $users->FillUserInfo($rowset, 'biz_driver_id', 'biz_driver');
        $rowset = $users->FillUserInfo($rowset, 'biz_author_id', 'biz_author');
        $rowset = $users->FillUserInfo($rowset, 'biz_modifier_id', 'biz_modifier');

        foreach ($rowset as $key => $row) {
            if (isset($row[$entityname])) {
                if (isset($row['biz_market'])) {
                    $rowset[$key][$entityname]['market'] = $row['biz_market'];
                    unset($rowset[$key]['biz_market']);
                }

                if (isset($row['biz_objective'])) {
                    $rowset[$key][$entityname]['objective'] = $row['biz_objective'];
                    unset($rowset[$key]['biz_objective']);
                }

                if (isset($row['biz_team'])) {
                    $rowset[$key][$entityname]['team'] = $row['biz_team'];
                    //unset($rowset[$key]['biz_team']);
                }

                if (isset($row['biz_product'])) {
                    $rowset[$key][$entityname]['product'] = $row['biz_product'];
                    unset($rowset[$key]['biz_product']);
                }

                if (isset($row['biz_driver'])) {
                    $rowset[$key][$entityname]['driver'] = $row['biz_driver'];
                    unset($rowset[$key]['biz_driver']);
                }

                if (isset($row['biz_author'])) {
                    $rowset[$key][$entityname]['author'] = $row['biz_author'];
                    unset($rowset[$key]['biz_author']);
                }

                if (isset($row['biz_modifier'])) {
                    $rowset[$key][$entityname]['modifier'] = $row['biz_modifier'];
                    unset($rowset[$key]['biz_modifier']);
                }

                unset($rowset[$key]['biz_market_id']);
                unset($rowset[$key]['biz_objective_id']);
                unset($rowset[$key]['biz_team_id']);
                unset($rowset[$key]['biz_product_id']);
                unset($rowset[$key]['biz_driver_id']);
                unset($rowset[$key]['biz_author_id']);
                unset($rowset[$key]['biz_modifier_id']);
            }
        }

        return $this->FillQuickInfo($rowset, $id_fieldname, $entityname);
    }

    /**
     * Возвращает быстроменяющуюся информацию по бизнесу
     * 
     * @param array $rowset
     * @param mixed $id_fieldname
     * @param mixed $entityname
     */
    function FillQuickInfo($rowset, $id_fieldname = 'biz_id', $entityname = 'biz') {
        $rowset = $this->_fill_entity_info($rowset, $id_fieldname, $entityname . 'quick', 'bizquick-' . $this->user_id . '-biz-', 'sp_biz_get_quick_by_ids', array('bizesquick' => '', 'bizes' => '', 'biz' => 'id'), array($this->user_id));

        foreach ($rowset AS $key => $row) {
            if (isset($row[$entityname]) && isset($row[$entityname . 'quick'])) {
                $rowset[$key][$entityname]['quick'] = $row[$entityname . 'quick'];
            }

            unset($rowset[$key][$entityname . 'quick']);
        }

        return $rowset;
    }

    /**
     * Сохраняет бизнес
     * @version 25.04.13, Sasha added parent_id
     * @param type $id
     * @param type $title
     * @param type $description
     * @param type $objective_id
     * @param type $market_id
     * @param type $team_id
     * @param type $product_id
     * @param type $status
     * @param type $driver_id
     * @param type $parent_id
     * @return null
     */
    function Save($id, $title, $description, $objective_id, $market_id, $team_id, $product_id, $status, $driver_id, $parent_id = 0) {
        $result = $this->CallStoredProcedure('sp_biz_save', array($this->user_id, $id, $title, $description, $objective_id, $market_id, $team_id, $product_id, $status, $driver_id, $parent_id));
        $result = isset($result) && isset($result[0]) && isset($result[0][0]) ? $result[0][0] : null;

        if (empty($result) || array_key_exists('ErrorCode', $result))
            return null;

        Cache::ClearTag('biz-' . $result['id']);
        Cache::ClearTag('biz-order-list-' . $result['id']);
        Cache::ClearTag('bizes-' . $team_id);
        Cache::ClearTag('bizes');

        return $result;
    }

    /**
     * Удаляет бизнес
     * 
     * @param mixed $id
     * @return resource
     */
    function Remove($id) {
        $result = $this->CallStoredProcedure('sp_biz_remove', array($id));
        $result = isset($result) && isset($result[0]) && isset($result[0][0]) ? $result[0][0] : null;

        if (empty($result) || array_key_exists('ErrorCode', $result))
            return null;

        Cache::ClearTag('biz-' . $id);
        Cache::ClearTag('biz-order-list-' . $id);
        Cache::ClearTag('bizes');

        return $result;
    }

    /**
     * @version 20130518, sasha
     * get list users, markets, objectives, teams, statuses from bizes
     * @return type
     */
    function GetDataFromBizes() {
        $hash = 'data-for-bizes';
        $cache_tags = array($hash, 'bizes');

        $rowset = $this->_get_cached_data($hash, 'sp_biz_get_data_from_bizes', array(), $cache_tags);

        $rowset = (isset($rowset[0][0]) && !empty($rowset[0][0])) ? $rowset[0][0] : null;

        $result = array();

        /* get objective list */
        if (isset($rowset['objective_ids']) && !empty($rowset['objective_ids'])) {
            $objective_list = explode(',', $rowset['objective_ids']);

            foreach ($objective_list as $key => $row) {
                $result['objectives'][$key]['objective_id'] = $row;
            }
            $objective_model = new Objective();

            $result['objectives'] = $objective_model->FillObjectiveInfo($result['objectives'], 'objective_id');
        }

        /* get team list */
        if (isset($rowset['team_ids']) && !empty($rowset['team_ids'])) {
            $team_list = explode(',', $rowset['team_ids']);

            foreach ($team_list as $key => $row) {
                $result['teams'][$key]['team_id'] = $row;
            }

            $team_model = new Team();

            $result['teams'] = $team_model->FillTeamInfo($result['teams']);
        }

        /* get status list */
        if (isset($rowset['status_list']) && !empty($rowset['status_list'])) {
            $result['status_list'] = explode(',', $rowset['status_list']);
        }

        /* get market list */
        if (isset($rowset['market_ids']) && !empty($rowset['market_ids'])) {
            $markets = explode(',', $rowset['market_ids']);

            foreach ($markets as $key => $row) {
                $result['markets'][$key]['market_id'] = $row;
            }

            $markets_model = new Market();

            $result['markets'] = $markets_model->FillMarketInfo($result['markets']);
        }

        /* get user list */
        if (isset($rowset['user_ids']) && !empty($rowset['user_ids'])) {
            $user_list = explode(',', $rowset['user_ids']);

            foreach ($user_list as $key => $row) {
                $result['users'][$key]['user_id'] = $row;
            }

            $user_model = new User();

            $result['users'] = $user_model->FillUserInfo($result['users']);
        }

        return !empty($result) ? $result : null;
    }

    /**
     * return order list for biz and subbiz
     * 
     * @param type $biz_id
     * @return type
     * 
     * @version 20130722, sasha
     */
    function GetOrders($biz_id) {
        $hash = 'biz-order-list-' . $biz_id;
        $cache_tags = array($hash, 'bizes');

        $rowset = $this->_get_cached_data($hash, 'sp_biz_get_order_list', array($biz_id), $cache_tags);

        $orderModel = new Order();

        return isset($rowset[0]) && !empty($rowset[0]) ? $orderModel->FillOrderInfo($rowset[0]) : null;
    }

    /**
     * GetBizMenu
     * 
     * возвращает ассоциативный массив с меню бизов в eMM
     */
    /*Примерная структура массива
     * 
     * array(
     * ----0
     * --------id
     * --------title
     * --------bizs
     * ------------0
     * ----------------id
     * ----------------title
     * ----------------doc_no
     * ----------------etc
     * 
     * 
     * 
     * 
     */
    public function GetBizMenu() {
        $this->table->table_name = 'biz_navigation_group';
        $groups = $this->GetBizMenuGroup($_SESSION['user']['id']);
        //dg($groups);
        $menu = array();
        $this->table->table_name = 'biz_navigation';
        foreach($groups as $row) {
            $bizs = $this->GetBizByGroup($row['id']);
            //dg($bizs);
            $group = array(
                'title' => $row['title'],
                'id' => $row['id'],
                'bizs' => $bizs,
            );
            
            $menu[] = $group;
        }
        //dg($menu);
        $this->table->table_name = 'bizes';
        return $menu;
    }

    /**
     * GetBizByGroup
     * 
     * возвращает ассоциативный массив с бизами входящими в группу eMM
     */    
    public function GetBizByGroup($group_id) {
        $where = "";
        
        if ($group_id > 0) {
            $where = "biz_group_id = '{$group_id}'";
        }        
        
         $arg = array(
            'fields' => 'biz_id',
            'where' => $where,
            'order' => 'created_at DESC',
        );

        $rowset = $this->table->SelectList($arg);
        
        foreach($rowset as &$row) {
            $biz_id = $row['biz_id'];
            $row = $this->GetById($biz_id);
        }
        return $rowset;       
    }
    
    /**
     * GetBizMenuGroup
     * 
     * возвращает ассоциативный массив с группами бизов в eMM
     */
    public function GetBizMenuGroup($user_id = 0) {
        

        $where = "";
        
        if ($user_id > 0) {
            $where = "created_by = '{$user_id}'";
        }

        //$resource = $this->table->_exec_raw_query($q);
        $arg = array(
            'fields' => '*',
            'where' => $where,
            'order' => 'title ASC',
        );

        $rowset = $this->table->SelectList($arg);
        return $rowset;
    }

}
