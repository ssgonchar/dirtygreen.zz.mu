<?php
require_once APP_PATH . 'classes/models/email.class.php';


class EmailFilter extends Model
{
    public function EmailFilter()
    {
        Model::Model('efilters');
    }
    
    /**
     * Возвращает данные сущности по ID (efilters.id)
     * 
     * @param int $id [INT] ID (efilters.id)
     * @return array
     * 
     * @version 20130122, d10n
     */
    public function GetById($id)
    {
        $dataset = $this->FillEmailFilterInfo(array(array('efilter_id' => $id)));
        return isset($dataset) && isset($dataset[0]) && isset($dataset[0]['efilter']) ? $dataset[0] : null;
    }
    
    /**
     * Возвращает список всех фильтров
     * 
     * @param int $is_scheduled
     * @param int $page_no
     * @param int $per_page
     * @return array
     */
    public function GetListAll()
    {
        return $this->GetList(-1, 0, 10000);
    }
    
    /**
     * Возвращает линейный список фильтров
     * 
     * @param int $is_scheduled
     * @param int $page_no
     * @param int $per_page
     * @return array Вида array('data' => array(), 'count' => int)
     * 
     * @version 20130122, d10n
     */
    public function GetList($is_scheduled, $page_no = 0, $per_page = ITEMS_PER_PAGE)
    {
        $page_no    = $page_no > 0 ? $page_no : 1;
        $per_page   = $per_page < 1 ? ITEMS_PER_PAGE : $per_page;
        $start      = ($page_no - 1) * $per_page;
        
        $hash       = 'efilters' . md5('-isscheduled-' . $is_scheduled . '-page-' . $page_no . '-count-' . $per_page);
        $cache_tags = array($hash, 'efilters');
        
        $rowset = $this->_get_cached_data($hash, 'sp_efilter_get_list', array($is_scheduled, $start, $per_page), $cache_tags);
        
        return array(
            'data'  => isset($rowset[0]) ? $this->FillEmailFilterInfo($rowset[0]) : array(),
            'count' => isset($rowset[1]) && isset($rowset[1][0]) && isset($rowset[1][0]['rows_count']) ? $rowset[1][0]['rows_count'] : 0
        );
    }
    
    /**
     * Сохраняет данные по фильтру
     * 
     * @param int $id
     * @param string $filter_params
     * @param string $tags
     * @param int $is_scheduled [0/1]
     * @return array
     * 
     * @version 20130122, d10n
     */
    public function Save($id, $filter_params, $tags, $is_scheduled)
    {
        $result = $this->CallStoredProcedure('sp_efilter_save', array($this->user_id, $id, $filter_params, $tags, $is_scheduled));
        $id = isset($result[0]) && isset($result[0][0]) && isset($result[0][0]['efilter_id']) ? $result[0][0]['efilter_id'] : 0;
        
        Cache::ClearTag('efilters');
        if ($id > 0) Cache::ClearTag('efilter-' . $id);
        
        return $id > 0 ? $this->GetById($id) : array();
    }
    
    /**
     * Удаляет конкретный фильтр<br />
     * Удаляет связи фильтр-письма (email_filters)
     * 
     * @param array $filter Сущность efilter
     * @return int Количество затронутых записей
     * 
     * @version 20130123, d10n
     */
    public function Remove($efilter)
    {
        // 20130125, zharkov: это нужно делать только если пользователь явно укажет на необходимость
        $this->UnlinkAllEmails($efilter);
        
        $result = $this->CallStoredProcedure('sp_efilter_delete', array($efilter['id']));
        
        Cache::ClearTag('efilters');
        Cache::ClearTag('efilter-' . $efilter['id']);
        
        return $result;
    }
    
    /**
     * 
     * @param type $rowset
     * @param type $id_fieldname
     * @param type $entityname
     * @param type $cache_prefix
     * @return type
     * 
     * @version 20130122, d10n
     */
    public function FillEmailFilterInfo($rowset, $id_fieldname = 'efilter_id', $entityname = 'efilter', $cache_prefix = 'efilter')
    {
        $rowset = $this->_fill_entity_info($rowset, $id_fieldname, $entityname, $cache_prefix, 'sp_efilter_get_list_by_ids', array('efilters' => ''), array());
        
        $modelEmail = new Email();
        
        foreach ($rowset as $key => $row)
        {
            if (!isset($row[$entityname])) continue;
            $row = $row[$entityname];
            
            if (!empty($row['params']))
            {
                $rowset[$key][$entityname]['params_array'] = $this->ParamsStringToArray($row['params']);
            }
            if (!empty($row['tags']))
            {
                $tags_array = $this->TagsStringToArray($row['tags']);
//TODO REFACTOR: реализовать filler более рационально
                $rowset[$key][$entityname]['tags_array'] = $modelEmail->FillObjectInfo($tags_array);
            }
        }
        
        return $rowset;
    }
    
    /**
     * Применяет фильтр к письму
     
     * @param array $efilter Сущность
     * @param array $email Сущность
     * @return boolean
     * 
     * @version 20130122, d10n
     * @version 20130125, zharkov: учитывается комбинация параметров
     */
    public function Apply($efilter, $email)
    {
        if (!isset($efilter['params_array'])) return false;
        if (!isset($efilter['tags_array'])) return false;
        
        $filter_criterias   = $efilter['params_array'];
        
        if ((isset($filter_criterias['attachment']) && $filter_criterias['attachment'] == 'yes')
            && (!isset($email['has_attachments']) || $email['has_attachments'] == false))
        {
            return false;
        }
        
        
        $apply_filter = true;        
        foreach ($filter_criterias as $key => $criteria)
        {
            switch($key)
            {
                case 'from' :
                    $email_entity = 'sender_address';
                    break;
                case 'to' :
                    $email_entity = 'recipient_address';
                    break;
                case 'subject':
                    $email_entity = 'title';
                    break;
                case 'text' :
                    $email_entity = 'description';
                    break;
                case 'attachment' :
                default:
                    continue;
            }
            
            if (!isset($email[$email_entity])) continue;
            
            $criteria   = preg_replace('/[\[\]\'"!?+*()&<>]/ui', ' ', $criteria);
            $criteria   = preg_replace('/\s+/u', ' ', $criteria);
            $criteria   = trim($criteria, ' ');
            $criteria   = mb_strtolower($criteria);
            
            if (!mb_stristr($email[$email_entity], $criteria)) return false;
        }
        
        if ($apply_filter) return $this->LinkEmail($efilter, $email);
    }
    
    /**
     * Преобразовывает массив параметров фильтра в строку
     * @param array $array
     * @return string Вид $key:$value;...
     * 
     * @version 20130122, d10n
     */
    public function ParamsArrayToString($array = array())
    {
        if (!is_array($array)) return '';
        if (empty($array)) return '';
        
        $string = '';
        foreach($array as $key => $value)
        {
            if (!in_array($key, array('from', 'to', 'subject', 'text', 'attachment'))) continue;
            if (empty($value)) continue;

            $string .= "$key:$value;";
        }
        
        return $string;
    }
    
    /**
     * Преобразовывает строку параметров фильтра в массив
     * @param string $string
     * @return array Вид array($str_before ":" => $str_after ":", ";"....);
     * 
     * @version 20130122, d10n
     */
    public function ParamsStringToArray($string = '')
    {
        if (empty($string)) return array();
        
        $array      = array();
        $exploded   = explode(';', $string);
        
        foreach($exploded as $param)
        {
            if (empty($param)) continue;
            
            list($key, $value) = explode(':', $param);
            $array[$key] = $value;
        }
        
        return $array;
    }
    
    /**
     * Преобразовывает массив тегов фильтра в строку
     * @param array $array
     * @return string Вид $key:$value;...
     * 
     * @version 20130122, d10n
     */
    public function TagsArrayToString($array = array())
    {
        if (!is_array($array)) return '';
        if (empty($array)) return '';
        
        $string = '';
        foreach($array as $key => $value)
        {
            list($object_alias, $object_id) = explode('-', $key);
            if (!in_array($object_alias, array('biz', 'company', 'country', 'order', 'person', 'product', ))) continue;
            
            $string .= "$object_alias:$object_id;";
        }
        
        return $string;
    }
    
    /**
     * Преобразовывает строку тегов фильтра в массив
     * @param string $string
     * @return array Вид array($str_before ":" => $str_after ":", ";"....);
     * 
     * @version 20130122, d10n
     */
    public function TagsStringToArray($string = '')
    {
        if (empty($string)) return array();
        
        $array      = array();
        $exploded   = explode(';', $string);
        
        foreach($exploded as $tag)
        {
            if (empty($tag)) continue;
            
            list($object_alias, $object_id) = explode(':', $tag);
            
            $array[] = array(
                'object_alias'          => $object_alias,
                'object_id'             => $object_id,
                'alias'                 => $object_alias,
                $object_alias . '_id'   => $object_id,
            );
        }
        
        return $array;
    }
    
    /**
     * Устанавливает связь Фильтр-Письмо<br />
     * Примечание: добавляет к письму соответствующие теги
     * 
     * @param array $efilter Сущность efilter
     * @param array $email Сущность email
     * @return boolean
     * 
     * @version 20130123, d10n
     */
    public function LinkEmail($efilter, $email)
    {
        $user_id = isset($this->user_id) ? $this->user_id : 0;
        $result = $this->CallStoredProcedure('sp_email_filter_save', array($user_id, $email['id'], $efilter['id']));
        
        // добавление соответствующих тегов в email_objects
        foreach ($efilter['tags_array'] as $tag)
        {
            $this->table->_exec_raw_query("
                INSERT IGNORE INTO `email_objects`
                SET
                    `email_id`  = '" . intval($email['id']) . "',
                    `object_alias` = '" . mysql_real_escape_string($tag['object_alias']) . "',
                    `object_id` = '" . intval($tag['object_id']) . "';");
        }
        
//TODO: Реализовать рациональный очистку кеша
//        Cache::ClearTag('efilters');  20130125, zharkov: это не надо очищать тут
        Cache::ClearTag('efilter-' . $efilter['id']);
        Cache::ClearTag('email-' . $email['id']);
        
        return (isset($result[0]) && isset($result[0][0]) && isset($result[0][0]['email_efilter_id']));
    }
    
    /**
     * Проверяет существует разница между фильтрами
     * 
     * @param array $filter_old Сущность efilter
     * @param array $filter_new Сущность efilter
     * @return boolean
     * 
     * @version 20130123, d10n
     */
    public function IsDiffExists($filter_old, $filter_new)
    {
        if ($filter_old['params'] !== $filter_new['params']) return TRUE;

        // 20130125, zharkov: мне кажется тут нужно сравнивать каждый тэг, иначе порядок имеет значение
        if ($filter_old['tags'] !== $filter_new['tags']) return TRUE;
        
        return FALSE;
    }
    
    /**
     * Разрывает связь Фильтр-Письмо со всеми письмами<br />
     * Примечание: удаляет соответствующие теги
     * 
     * @param array $efilter Сущность efilter
     * @return boolean
     * 
     * @version 20130123, d10n
     */
    public function UnlinkAllEmails($efilter)
    {
        // удаление соответствующих тегов из email_objects
        // применяется только для связанных с данным фильтром писем
        foreach ($efilter['tags_array'] as $tag)
        {
            $this->CallStoredProcedure('sp_efilter_remove_emails_tags', array($this->user_id, $efilter['id'], $tag['object_alias'], $tag['object_id']));
        }
        
        // удаляем связь email-efilter
        $this->CallStoredProcedure('sp_efilter_remove_emails', array($this->user_id, $efilter['id']));
        
        Cache::ClearTag('emails');
        
        return TRUE;
    }
    
    /**
     * Удаляет фильтр из расписания<br />
     * Фильтр, у которого 'is_scheduled'= 0,
     * не применяется к уже полученным письмам
     * 
     * @param int $efilter_id
     * 
     * @version 20130123, d10n
     */
    public function RemoveFromSchedule($efilter_id)
    {
        $this->Update($efilter_id, array(
            'is_scheduled'      => 0,
            'modified_at'   => 'NOW()!',
            'modified_by'   => (isset($this->user_id) ? $this->user_id : 0),
        ));
        
        Cache::ClearTag('efilters');
        Cache::ClearTag('efilter-' . $efilter_id);
    }
    
    /**
     * Возвращет список писем для фильтрации
     * @param int $efilter_id
     * @param int $count
     * @return array Вида array('data' => array(), 'count' => int)
     */
    public function GetEmailsForFiltering($efilter_id, $count = 100)
    {
        $data_set = $this->CallStoredProcedure('sp_efilter_get_emails_for_filtering', array($efilter_id, $count));
        
        return array(
            'data'  => (isset($data_set[0]) ? $data_set[0] : array()),
            'count' => (isset($data_set[1]) && isset($data_set[1][0]) && isset($data_set[1][0]['row_count']) ? $data_set[1][0]['row_count'] : 0),
        );
    }
}
