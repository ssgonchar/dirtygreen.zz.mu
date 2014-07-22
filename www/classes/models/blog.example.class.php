<?
    //require_once(APP_PATH . 'classes/models/account.class.php');
    //require_once(APP_PATH . 'classes/models/author.class.php');
    //require_once(APP_PATH . 'classes/models/award.class.php');
    //require_once(APP_PATH . 'classes/models/bookswap.class.php');
    //require_once(APP_PATH . 'classes/models/edition.class.php');
    //require_once(APP_PATH . 'classes/models/friend.class.php');
    //require_once(APP_PATH . 'classes/models/post.class.php');
    //require_once(APP_PATH . 'classes/models/quote.class.php');
    //require_once(APP_PATH . 'classes/models/selection.class.php');
    //require_once(APP_PATH . 'classes/models/story.class.php');
    //require_once(APP_PATH . 'classes/models/review.class.php');
    //require_once(APP_PATH . 'classes/models/userbook.class.php');
    //require_once(APP_PATH . 'classes/models/userobject.class.php');
    
class TopEvent extends Model
{
    function TopEvent()
    {
        Model::Model('top_events');
    }
    
    /**
     * Сохранение события в главную ленту событий
     * 
     *
     * @param int $id идентификатор события
     * @param int $user_id идентификатор пользователя
     * @param string $object_alias алиас объекта
     * @param int $object_id идентификатор объекта
     * @param int $object_user_id идентификатор автора объекта
     * @param int $object_date время создания объекта
     * @param int $access уровень доступа к событию: 0 - все, 1 - никто
     * @param string $title заголовок события
     * @param string $description описание события
     * @param string $url ссылка на объект
     * @param integer $priority приоритет - для закрепления тем, 20120812, digi
     * @return int идентификатор события
     */    
    function Save($id, $user_id, $object_alias, $object_id, $object_user_id, $object_date, $access, $title, $description, $url, $priority = 0, $admin_comment = '', $published_at = 0)
    {
        $data_set = $this->CallStoredProcedure('sp_top_event_save', array($id, $user_id, $object_alias, $object_id, $object_user_id, $object_date, $access, $title, $description, $url, $priority, $admin_comment, $published_at));

        if (!empty($id))
        {
            Cache::ClearTag('topevent-' . $id);
        }
        
        Cache::ClearTag('topevents');
        
        return count($data_set) && count($data_set[0]) ? $data_set[0][0] : array();
    }

    /**
     * Получение списка событий
     * 
     * @param int $access уровень доступа к событию: 0 - все, 1 - никто
     * @param int $page_no номер страницы списка
     * @param int $records_per_page количеств событий на странице
     * @return array ассоциативный массив списка событий
     */            
    function GetList($access, $page_no = 1, $records_per_page = 25)
    {        
        $records_per_page   = $records_per_page < 1 ? 25 : $records_per_page;
        $page_no            = $page_no > 0 ? $page_no : 1;
        $start              = ($page_no - 1) * $records_per_page;        
        
        // 2012.08.12, digi, выборка и кэширование данных крупными сегментами
        $x_factor = $records_per_page < 250 ? 4 : 1;
        $x_records_per_page = $records_per_page * $x_factor;
        $x_start = $start - $start % $x_records_per_page;
        
        $hash = 'topevents-' . md5($access . '-' . $x_start . '-' . $x_records_per_page);
        $sp_name = 'sp_top_event_get_list';
        $sp_params = array($access, $x_start, $x_records_per_page);
        $cache_tags = array('topevents');
        $lifetime = CACHE_LIFETIME_SHORT;

        $rowset = $this->_get_cached_data($hash, $sp_name, $sp_params, $cache_tags, $lifetime);

        $result = array();
        $tmp  = isset($rowset[0]) ? $this->FillTopEventInfo($rowset[0]) : null;
        $result['count'] = isset($rowset[1]) && isset($rowset[1][0]) && isset($rowset[1][0]['rows']) ? $rowset[1][0]['rows'] : 0;

        // группировка строк по алиасам
        $aliases = array();
        foreach ($tmp as $key => $event)
        {
            $event = $event['topevent'];
            $alias = ($event['object_alias'] == 'blog' ? 'post' : $event['object_alias']);
            
            if (!array_key_exists($alias, $aliases))
            {
                $aliases[$alias] = array();
            }
            
            $aliases[$alias][] = array($alias . '_id' => $event['object_id']);
        }
        
        //echo '<pre>'; print_r($aliases); die();
        // получение UserObjects для алиасов
        $userobjects = Model::Factory('UserObject');
        foreach ($aliases as $alias => $data)
        {
            switch ($alias)
            {
                case 'review':
                    $reviews = Model::Factory('Review');
                    $aliases[$alias] = $reviews->FillReviewInfo($data);
                    break;
                    
                case 'event':
                    $events = Model::Factory('Event');
                    $aliases[$alias] = $events->FillEventInfo($data);    
                    break;      
                
                case 'quote':
                    $quotes = Model::Factory('Quote');
                    $aliases[$alias] = $quotes->FillQuoteInfo($data);
                    break;
                    
                case 'selection':
                    $selections = Model::Factory('Selection');
                    $aliases[$alias] = $selections->FillSelectionInfo($data);
                    break;
                    
                case 'story':
                    $stories = Model::Factory('Story');
                    $aliases[$alias] = $stories->FillStoryInfo($data);
                    break;
                    
                case 'post':
                    $posts = Model::Factory('Post');
                    $aliases[$alias] = $posts->FillPostInfo($data);
                    break;
                    
                default: 
                    $aliases[$alias] = $userobjects->FillUserObjectInfo($this->user_id, $alias, $data, 'uo' . $alias);
                    break;
            }
        }
        
        //echo '<pre>'; print_r($aliases); die();       
        // развертывание UserObjects по строкам
        foreach ($tmp as $key => $event)
        {
            $event          = $event['topevent'];
            $object_alias   = ($event['object_alias'] == 'blog' ? 'post' : $event['object_alias']);
            
            if (isset($aliases[$object_alias]))
            {
                foreach($aliases[$object_alias] as $item)
                {
                    if ($item[$object_alias . '_id'] == $event['object_id'])
                    {
                        switch ($object_alias)
                        {
                            case 'review':
                                $tmp[$key]['topevent']['review'] = $item['review'];
                                break;
                                
                            case 'quote':
                                $tmp[$key]['topevent']['quote'] = $item['quote'];
                                break;
                                
                            case 'selection':
                                $tmp[$key]['topevent']['selection'] = $item['selection'];
                                break;
                                
                            case 'story':
                                $tmp[$key]['topevent']['story'] = $item['story'];
                                break;
                                
                            case 'post':
                                $tmp[$key]['topevent']['post'] = $item['post'];
                                break;
                            
                            case 'event':
                                $tmp[$key]['topevent']['event'] = $item['event'];
                                break;              
                                
                            default: 
                                $tmp[$key]['topevent']['userobjects'] = $item['uo' . $object_alias];
                                break;
                        }
                    
                        break;
                    }
                }
            }
        }

        // 2012.08.12, digi, выборка и кэширование данных крупными сегментами
        $result['data'] = array();
        $tmp_count = count($tmp);
        for ($i = 0; $i < $records_per_page && $i < $tmp_count; $i++)
        {
            $result['data'][] = $tmp[$start % $x_records_per_page + $i];
        }
        
        //echo '<pre>'; print_r($result['data']); die(); 
        
        return $result;
        
    }
    
    /**
     * Для каждой строки выборки на основании поля 'topevent_id' выбирается подробная информация о событии из таблицы top_events.
     * Информация о событии сохраняется в новом поле 'topevent'.
     *
     * @param array $recordset ассоциативный массив, выборка данных, включающая поле 'topevent_id'
     * @return array входной масcив с полем 'event' содержащим информацию о событии с кодом из поля 'topevent_id'
     */
    function FillTopEventInfo($recordset)
    {
        $dataset = $this->_fill_entity_info($recordset, 'topevent_id', 'topevent', 'topevent', 'sp_top_event_get_list_by_ids');

        return $dataset;
    }

    
    /**
     * Получение события по идентификатору
     * 
     *
     * @param int $id идентификатор события
     * @return array ассоциативный массив с данными о событии
     */                
    function GetById($id)
    {        
        $data_set = $this->CallStoredProcedure('sp_top_event_get_by_id', array($id));
        return count($data_set) && count($data_set[0]) ? $data_set[0][0] : array();
    }
    
    /**
     * Выбор самого интересного события для автоматической публикации на главной странице
     *
     * @return array object_alias + object_id
     */                
    function Generate()
    {        
        $data_set = $this->CallStoredProcedure('sp_top_event_generate', array());
        return count($data_set) && count($data_set[0]) ? $data_set[0][0] : array();
    }
    
    /**
     * Получение списка событий
     * 
     * @param int user_id идентификатор пользователя
     * @param int $page_no номер страницы списка
     * @param int $records_per_page количеств событий на странице
     * @param array $filter фильтр событий
     * @return array ассоциативный массив списка событий
     */            
    function GetListForUser($reader_id, $page_no = 1, $records_per_page = 25, $filter = null)
    {        
        $records_per_page   = $records_per_page < 1 ? 25 : $records_per_page;
        $page_no            = $page_no < 1 ? 1 : $page_no;
        $start              = ($page_no - 1) * $records_per_page;        
        
        // 2012.08.12, digi, выборка и кэширование данных крупными сегментами
        $x_factor = $records_per_page < 250 ? 4 : 1;
        $x_records_per_page = $records_per_page * $x_factor;
        $x_start = $start - $start % $x_records_per_page;
        
        $hash       = 'userevents-' . $this->user_id . '-' . $reader_id . '-' . $x_start . '-' . $x_records_per_page;
        $cache_tags = array('userevents-' . $reader_id);
        
        if (!isset($rowset) || !isset($rowset['data']) || isset($rowset['outdated']))
        {
            $friends = Model::Factory('Friend');
            $friend_list = $friends->GetForUser($reader_id, 'all', true); // для запрашиваемой ленты            
            if (empty($friend_list)) return null;
            
            $friend_mutual_list = $friends->GetForUser($this->user_id, 'mutual', true); // для текущего читателя
            $friend_mutual_list[] = $this->user_id;

            $cl = new SphinxClient();  
            $cl->SetLimits($x_start, $x_records_per_page, 10000);
            //$cl->SetMatchMode(SPH_MATCH_ALL);
            //$cl->SetFieldWeights(array('name' => 100, 'author' => 10));
            
            $extra_select = '';
            if (!empty($filter))
            {
                $object_types = array();
                $extra_select = '';
                
                $bookread = array();
                if (!empty($filter['bookwish']))    $bookread[] = 0;
                if (!empty($filter['bookread']))    $bookread[] = 1;
                if (!empty($filter['bookreading'])) $bookread[] = 2;
                
                if (!empty($bookread))
                {
                    $extra_select .= ', object_type <> 10 OR (object_type = 10 AND IN (param1, ' . implode(',', $bookread) . ')) AS book_read';
                    $cl->SetFilter('book_read', array(1));
                    
                    $object_types[] = 10;
                }
                
                if (!empty($filter['review']))      $object_types[] = 7;
                if (!empty($filter['selection']))   $object_types[] = 8;
                if (!empty($filter['quote']))       $object_types[] = 12;
                if (!empty($filter['post']))        $object_types[] = 5;
                if (!empty($filter['award']))       $object_types[] = 14;
                if (!empty($filter['story']))       $object_types[] = 15;
                if (!empty($filter['event']))       $object_types[] = 16;
                $bookswap = array();
                if (!empty($filter['bookswap']))    $bookswap[] = 1;
                if (!empty($filter['wishlist']))    $bookswap[] = 0;
                
                if (!empty($bookswap))
                {
                    $extra_select .= ', object_type <> 13 OR (object_type = 13 AND IN (param1, ' . implode(',', $bookswap) . ')) AS bookswap';
                    $cl->SetFilter('bookswap', array(1));
                    
                    $object_types[] = 13;
                }
                
                if (!empty($object_types))
                {                
                    $cl->SetFilter('object_type', $object_types);
                }
            }
 
            $cl->SetSelect('*' . $extra_select . ', access = 0 OR (access = 1 AND IN (user_id, ' . implode(',', $friend_mutual_list) . '))' . (!empty($this->user_id) ? ' OR (access = 2 AND user_id = ' . $this->user_id . ')' : '') . ' AS has_access');
            $cl->SetFilter('has_access', array(1));         
            $cl->SetFilter('user_id', $friend_list);
            $cl->SetSortMode(SPH_SORT_ATTR_DESC, 'sort_at');
            //$cl->SetRankingMode(SPH_RANK_PROXIMITY_BM25);
            //$cl->SetGroupBy('book_id', SPH_GROUPBY_ATTR, '@count DESC');

            $data = $cl->Query('', 
               'ix_events_ub, ix_delta_events_ub, 
                ix_events_rvw, ix_delta_events_rvw, 
                ix_events_qt, ix_delta_events_qt, 
                ix_events_sln, ix_delta_events_sln,
                ix_events_pst, ix_delta_events_pst,
                ix_events_awd, ix_delta_events_awd,
                ix_events_str, ix_delta_events_str,
                ix_events_bsw, ix_delta_events_bsw,
                ix_events_evt, ix_delta_events_evt'
            );
        
//dg($cl);
//dg($data);
            if ($data === false)
            {
                Log::AddLine(LOG_ERROR, 'Topevent::GetListForUser, reader_id=' . $reader_id . ', ' . $cl->GetLastError());
                return null;
            }
            
            $rowset = array(); 
            if (!empty($data['matches']))
            {
                foreach ($data['matches'] as $id => $extra)
                {
                    $rowset[] = array(
                        'user_id'       => $extra['attrs']['user_id'],
                        'object_id'     => $extra['attrs']['object_id'],
                        'object_alias'  => $this->_object_alias_by_sphinx_object_type($extra['attrs']['object_type']),
                        'date_year'     => date('Y', $extra['attrs']['sort_at']),
                        'date_month'    => date('m', $extra['attrs']['sort_at']),
                        'date_day'      => date('d', $extra['attrs']['sort_at']),
                    );
                }
            }
            
            $rowset = array(
                $rowset,
                array(array('rows' => $data['total_found']))
            );
            
            Cache::SetData($hash, $rowset, $cache_tags, CACHE_LIFETIME_SHORT);
            
            $rowset = array(
                'data' => $rowset
            );
        }

        /* MySQL version
        $sp_name = 'sp_event_get_list_for_user';
        $sp_params = array($this->user_id, $reader_id, $start, $records_per_page);
        $cache_tags = array('events-' . $reader_id);
        $lifetime = CACHE_LIFETIME_SHORT;

        $rowset = $this->_get_cached_data($hash, $sp_name, $sp_params, $cache_tags, $lifetime);

        $result = array();
        $result['data']  = isset($rowset[0]) ? $rowset[0] : null;
        $result['count'] = isset($rowset[1]) && isset($rowset[1][0]) && isset($rowset[1][0]['rows']) ? $rowset[1][0]['rows'] : 0;
        */
        
        $result = array();
        $tmp  = isset($rowset['data'][0]) ? $rowset['data'][0] : null;
        $result['count'] = isset($rowset['data'][1]) && isset($rowset['data'][1][0]) && isset($rowset['data'][1][0]['rows']) ? $rowset['data'][1][0]['rows'] : 0;
        
        // группировка строк по алиасам
        $aliases = array();
        foreach ($tmp as $key => $event)
        {   
            $alias = $event['object_alias'] == 'blog' ? 'post' : $event['object_alias'];
            
            if (!array_key_exists($alias, $aliases))
            {
                $aliases[$alias] = array();
            }
            
            $aliases[$alias][] = array($alias . '_id' => $event['object_id']);
        }

        //echo '<pre>'; print_r($aliases); die();
        // заполнение подробностей событий по алиасам
        $userobjects = Model::Factory('UserObject');
        foreach ($aliases as $alias => $data)
        {
            switch ($alias)
            {
                case 'userbook':
                    $userbooks = Model::Factory('UserBook');
                    //$users = Model::Factory('Account');
                    //$editions = Model::Factory('Edition');
                    //$aliases[$alias] = $users->FillUserInfo($editions->FillBookInfo($userbooks->FillUserBookInfo($data, 'id')));
                    $aliases[$alias] = $userbooks->FillUserBookInfo($data);
                    break;
                    
                case OBJECT_ALIAS_REVIEW:
                    $reviews = Model::Factory('Review');
                    $aliases[$alias] = $reviews->FillReviewInfo($data);
                    break;
                    
                case OBJECT_ALIAS_QUOTE:
                    $quotes = Model::Factory('Quote');
                    $aliases[$alias] = $quotes->FillQuoteInfo($data);
                    break;
                    
                case OBJECT_ALIAS_EVENT:
                    $events = Model::Factory('Event');
                    $aliases[$alias] = $events->FillEventInfo($data);
                    
                    break;      
                
                case OBJECT_ALIAS_SELECTION:
                    $selections = Model::Factory('Selection');
                    $aliases[$alias] = $selections->FillSelectionInfo($data);
                    break;
                    
                case OBJECT_ALIAS_STORY:
                    $stories = Model::Factory('Story');
                    $aliases[$alias] = $stories->FillStoryInfo($data);
                    break;
                    
                case OBJECT_ALIAS_POST:
                    $posts = Model::Factory('Post');
                    $aliases[$alias] = $posts->FillPostInfo($data);
                    break;
                    
                case OBJECT_ALIAS_BOOKSWAP:
                    $bookswaps = Model::Factory('Bookswap');
                    $aliases[$alias] = $bookswaps->FillBookswapInfo($data);
                    break;
                    
                case 'award':
                    $awards = Model::Factory('Award');
                    $aliases[$alias] = $awards->FillAwardInfo($data);
                    break;
                    
                //default: 
                //    $aliases[$alias] = $userobjects->FillUserObjectInfo($this->user_id, $alias, $data, 'uo' . $alias);
                //    break;
            }
        }

        $edition_ids = array();
        $author_ids = array();
        
        //echo '<pre>'; print_r($aliases); die();       
        // развертывание подробностей событий по строкам
        foreach ($tmp as $key => $event)
        {
            $object_alias   = ($event['object_alias'] == 'blog' ? 'post' : $event['object_alias']);
            
            if (isset($aliases[$object_alias]))
            {
                foreach($aliases[$object_alias] as $item)
                {
                    if ($item[$object_alias . '_id'] == $event['object_id'])
                    {
                        switch ($object_alias)
                        {
                            case 'userbook':
                                $tmp[$key]['userbook'] = $item['userbook'];
                                $edition_ids[] = array('edition_id' => $item['userbook']['edition_id']);
                                break;
                                
                            case OBJECT_ALIAS_REVIEW:
                                $tmp[$key]['review'] = $item['review'];
                                $edition_ids[] = array('edition_id' => $item['review']['edition_id']);
                                break;
                                
                            case OBJECT_ALIAS_STORY:
                                $tmp[$key]['story'] = $item['story'];
                                $edition_ids[] = array('edition_id' => $item['story']['edition_id']);
                                break;
                                
                            case OBJECT_ALIAS_QUOTE:
                                $tmp[$key]['quote'] = $item['quote'];
                                $edition_ids[] = array('edition_id' => $item['quote']['edition_id']);
                                break;
                                
                            case OBJECT_ALIAS_EVENT:
                                $tmp[$key]['event'] = $item['event'];
                                break;              
                            
                            case OBJECT_ALIAS_SELECTION:
                                $tmp[$key]['selection'] = $item['selection'];
                                break;
                                
                            case OBJECT_ALIAS_POST:
                                $tmp[$key]['post'] = $item['post'];
                                break;
                                
                            case OBJECT_ALIAS_BOOKSWAP:
                                $tmp[$key]['bookswap'] = $item['bookswap'];
                                $edition_ids[] = array('edition_id' => $item['bookswap']['edition_id']);
                                break;
                                
                            case 'award':
                                $tmp[$key]['award'] = $item['award'];
                                break;
                                
                            //default: 
                            //    $tmp[$key]['userobjects'] = $item['uo' . $object_alias];
                            //    break;
                        }
                    
                        break;
                    }
                }
            }
        }

        // добавление информации о книгах
        $editions = Model::Factory('Edition');
        $aliases['edition'] = $editions->FillBookInfo($edition_ids);
        
        foreach ($tmp as $key => $event)
        {
            if (in_array($event['object_alias'], array(OBJECT_ALIAS_REVIEW, OBJECT_ALIAS_STORY, OBJECT_ALIAS_QUOTE, 'userbook', OBJECT_ALIAS_BOOKSWAP)))
            {
                foreach($aliases['edition'] as $item)
                {
                    if ($item['edition_id'] == $event[$event['object_alias']]['edition_id'])
                    {
                        $tmp[$key]['book'] = $item['book'];
                        
                        if ($event['object_alias'] == 'quote')
                        {
                            $author_ids[] = array('author_id' => $item['book']['author_id']);
                        }
                        
                        break;
                    }
                }
            }
        }
        
        // добавление информации об авторах
        $authors = Model::Factory('Author');
        $aliases['author'] = $authors->FillAuthorInfo($author_ids);
        
        foreach ($tmp as $key => $event)
        {
            if (in_array($event['object_alias'], array('quote')))
            {
                foreach($aliases['author'] as $item)
                {
                    if ($item['author_id'] == $event['book']['author_id'])
                    {
                        $tmp[$key]['author'] = $item['author'];
                        
                        break;
                    }
                }
            }
        }
        //dg($result);        
        //echo '<pre>'; print_r($tmp); die(); 
        $accounts = Model::Factory('Account');
        $tmp = $accounts->FillUserInfo($tmp);
        
        // 2012.08.12, digi, выборка и кэширование данных крупными сегментами
        $result['data'] = array();        
        $tmp_count = count($tmp);
        for ($i = 0; $i < $records_per_page && $i < $tmp_count; $i++)
        {
            $result['data'][] = $tmp[$start % $x_records_per_page + $i];
        }
    
        return $result;
    }
    
    /**
     * Преобразует object_type, пришедший из сфинкса, в культурный object_alias
     */
    private function _object_alias_by_sphinx_object_type($object_type)
    {
        switch ($object_type)
        {
            case 10:    return 'userbook';
            case 7:     return OBJECT_ALIAS_REVIEW;
            case 12:    return OBJECT_ALIAS_QUOTE;
            case 16:    return OBJECT_ALIAS_EVENT;
            case 8:     return OBJECT_ALIAS_SELECTION;
            case 5:     return OBJECT_ALIAS_POST;
            case 14:    return 'award';
            case 15:    return OBJECT_ALIAS_STORY;
            case 13:    return OBJECT_ALIAS_BOOKSWAP;
        }
        
        return 'unknown';
    }
}
