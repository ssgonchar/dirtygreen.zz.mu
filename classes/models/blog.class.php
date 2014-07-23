<?php
require_once APP_PATH . 'classes/models/email.class.php';

define('BLOG_ENTITY_TYPE_EMAIL',        1);
define('BLOG_ENTITY_TYPE_MESSAGE',      2);
define('BLOG_ENTITY_TYPE_ATTACHMENT',   3);
define('BLOG_ENTITY_TYPE_ORDER',        4);
define('BLOG_ENTITY_TYPE_RA',           5);
define('BLOG_ENTITY_TYPE_DDT',          6);
define('BLOG_ENTITY_TYPE_CMR',          7);
define('BLOG_ENTITY_TYPE_INVOICE',      8);
define('BLOG_ENTITY_TYPE_SC',           9);
define('BLOG_ENTITY_TYPE_INDDT',        10);
define('BLOG_ENTITY_TYPE_SUPPINV',      11);

class Blog extends Model
{
    public function Blog()
    {
        Model::Model('');
    }
    
    /**
     * Возвращает список сообщений для ленты объекта
     * 
     * @param mixed $object_alias
     * @param mixed $object_id
     * @param mixed $filter
     * @param mixed $page_no
     * @param mixed $per_page
     * @return mixed
     * 
     * @version 20130213, zharkov
     */
    public function GetBlogData($object_alias, $object_id, $filter = array(), $page_no = 0, $per_page = ITEMS_PER_PAGE)
    {
		
        $page_no        = $page_no > 0 ? $page_no : 1;
        $per_page       = $per_page < 1 ? ITEMS_PER_PAGE : $per_page;
        $start          = ($page_no - 1) * $per_page;

        $hash           = 'blog-' . md5('userid-' . $this->user_id . '-objectalias-' . $object_alias . '-objectid-' . $object_id . '-start-' . $start . '-count-' . $per_page);

        $filter_hash    = '';
        foreach ($filter as $key => $value) $filter_hash .= '-' . $key . '-' . (is_array($value) ? implode("-", array_values($value)) : $value);

        $hash           .= $hash . md5($filter_hash);         
        $cache_tags     = array('blogs', $object_alias . '-' . $object_id . '-blog');
        
        $rowset         = Cache::GetData($hash);

//$rowset = null;   // test mode
        if (!isset($rowset) || !isset($rowset['data']) || isset($rowset['outdated']))
        {
            $cl = new SphinxClient();

            $cl->SetLimits($start, $per_page, 10000);
            
            $cl->SetFilter('object_alias',  array(sprintf("%u", crc32($object_alias) & 0xffffffff)));
            
            if ($object_alias == 'biz' && isset($filter['subbiz']))
            {
                $modelBiz = new Biz();
                foreach($modelBiz->GetListByParent($object_id) as $row)
                {
                    $object_ids[] = $row['biz_id'];
                }
                
                $object_ids[] = $object_id;
            }
            else
            {
                $object_ids = array($object_id);
            }
			
			if ($object_alias == 'biz' && isset($filter['mainbiz']))
			{
                $modelBiz = new Biz();
				$subBiz = $modelBiz->GetById($object_id);
				$parent_id = $subBiz['biz']['parent_id'];
                $object_ids[] = $parent_id;			
				//$object_ids[] = $parent_id;
			}	

			if ($object_alias == 'biz' && isset($filter['allsubbiz']))
			{
                $modelBiz = new Biz();
				$subBiz = $modelBiz->GetById($object_id);
				$parent_id = $subBiz['biz']['parent_id'];
                foreach($modelBiz->GetListByParent($parent_id) as $row)
                {
                    $object_ids[] = $row['biz_id'];
                }				
				//$object_ids[] = $parent_id;
			}			
			//print_r($object_ids);
            $cl->SetFilter('object_id',  $object_ids);
            //dg($cl);
            
            if (isset($filter['type']))
            {
                $cl->SetFilter('entity_type', array_values($filter['type']));
            }
            
            if (isset($filter['datefrom']) || isset($filter['dateto']))
            {
                $min    = !empty($filter['datefrom']) ? strtotime($filter['datefrom'] . ' 00:00:00') : 0;
                $max    = !empty($filter['dateto']) ? strtotime($filter['dateto'] . ' 23:59:59') : PHP_INT_MAX;
                
                $cl->SetFilterRange('created_at', $min, $max);
            }

            $keyword = isset($filter['keyword']) ? '*' . str_replace(' ', '* *', $filter['keyword']) . '*' : '';
            
            $cl->SetMatchMode(SPH_MATCH_ALL);
            //$cl->SetMatchMode(SPH_MATCH_PHRASE);
            $cl->SetGroupBy('guid', SPH_GROUPBY_ATTR, 'created_at DESC');
            
            // фильтр по роли пользоватлея
            $roles_arg = '';
            if ($this->user_role <= ROLE_ADMIN)
            {
                $cl->SetFilter('role_id', array(ROLE_ADMIN, ROLE_SUPER_MODERATOR, ROLE_MODERATOR, ROLE_SUPER_STAFF, ROLE_STAFF, ROLE_SUPER_USER, ROLE_USER, ROLE_LIMITED_USER, ROLE_GUEST));
            }
            else if ($this->user_role <= ROLE_STAFF)
            {
                $cl->SetFilter('role_id', array(ROLE_STAFF, ROLE_SUPER_USER, ROLE_USER, ROLE_LIMITED_USER, ROLE_GUEST));
                $roles_arg = 'IF(message_type_id <> 1, 1, 0) OR message_sender_id = ' . $this->user_id . ' OR message_recipient_id = ' . $this->user_id;
            }
            else
            {
                $cl->SetFilter('role_id', array(ROLE_SUPER_USER, ROLE_USER, ROLE_LIMITED_USER, ROLE_GUEST));
                $roles_arg = 'message_sender_id = ' . $this->user_id . ' OR message_recipient_id = ' . $this->user_id;
            }

            $select_string = '*';
            if (!empty($roles_arg))
            {
                $select_string .= ', (' . $roles_arg . ') AS roles_arg';
                $cl->SetFilter('roles_arg', array(1));
            }
            
            $cl->SetSelect($select_string);
            $data = $cl->Query($keyword, $this->_get_sphinx_indexes($object_alias));
//dg($data);
            if ($data === false)
            {
                Log::AddLine(LOG_ERROR, __METHOD__ . ' : object_alias=' . $object_alias . ', object_id=' . $object_id . '; ' . $cl->GetLastError());
                return null;
            }

            $rowset = array(); 
            if (!empty($data['matches']))
            {
                foreach ($data['matches'] as $id => $extra)
                {
                    $extra          = $extra['attrs'];
                    $entity_type    = $this->_get_entity_alias_by_type($extra['entity_type']);
                    $rowset[]   = array(
                        $entity_type . '_id' => $extra['entity_id'],
                        'entity_type' => $entity_type
                    );
                }
            }

            $rowset = array(
                $rowset,
                array(array('rows' => $data['total_found']))
            );
            
            Cache::SetData($hash, $rowset, $cache_tags, CACHE_LIFETIME_STANDARD);
            
            $rowset = array(
                'data' => $rowset
            );

        }
        
        $modelMessage       = Model::Factory('Message');
        $modelEmail         = Model::Factory('Email');
        $modelAttachment    = Model::Factory('Attachment');
        
        $list = isset($rowset['data'][0]) ? $modelAttachment->FillAttachmentInfo($modelEmail->FillEmailInfo($modelMessage->FillMessageInfo($rowset['data'][0]))) : array();

        if (isset($data['words']) && !empty($data['words']))
        {
            foreach ($list as $key => $row)
            {
                foreach ($data['words'] as $keyword => $params)
                {
                    $keyword    = str_replace('*', '', $keyword);
                    $alias      = '';

                    if (isset($row['message'])) $alias = 'message';
                    if (isset($row['email'])) $alias = 'email';
                    if (isset($row['attachment'])) $alias = 'attachment';
                    
                    if (!empty($alias))
                    {
                        if (isset($row[$alias]['title'])) $list[$key][$alias]['title'] = str_replace($keyword, '<span class="highlight">' . $keyword . '</span>', $list[$key][$alias]['title']);
                        if (isset($row[$alias]['description'])) $list[$key][$alias]['description'] = str_replace($keyword, '<span class="highlight">' . $keyword . '</span>', $list[$key][$alias]['description']);
                    }                
                }
            }            
        }
        
        return array(
            'data'  => $list,
            'count' => isset($rowset['data'][1]) && isset($rowset['data'][1][0]) && isset($rowset['data'][1][0]['rows']) ? $rowset['data'][1][0]['rows'] : 0
        );        
/*        




        
        
        $cl->SetGroupBy('guid', SPH_GROUPBY_ATTR, 'created_at DESC');
        
        

        $rowset = array(); 
        if (!empty($data['matches']))
        {
            foreach ($data['matches'] as $id => $extra)
            {
                $extra = $extra['attrs'];
                $rowset[] = array($this->_get_entity_alias_by_type($extra['entity_type']) . '_id' => $extra['entity_id']);
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

        return array(
            'data'  => isset($rowset['data'][0]) ? $this->FillMessageInfo($rowset['data'][0]) : array(),
            'count' => isset($rowset['data'][1]) && isset($rowset['data'][1][0]) && isset($rowset['data'][1][0]['rows']) ? $rowset['data'][1][0]['rows'] : 0
        );     
           */
/*
        
        
        

        if ($data === false)
        {
            
            return null;
        }
        
        $total_found = isset($data['total_found']) ? $data['total_found'] : 0;
        
        $rowset = array(); 
        if (!empty($data['matches']))
        {
            foreach ($data['matches'] as $id => $extra)
            {
                $rowset[] = array(
                    'object_alias'  => $this->_get_entity_alias_by_type($extra['attrs']['entity_type']),
                    'object_id'     => $extra['attrs']['entity_id'],
                );
            }
        }
*/        
/*        
        // группировка строк по алиасам
        $aliases = array();
        foreach ($rowset as $key => $event)
        {   
            $alias =  $event['object_alias'];
            
            if (!array_key_exists($alias, $aliases))
            {
                $aliases[$alias] = array();
            }
            
            $aliases[$alias][] = array($alias . '_id' => $event['object_id']);
        }
        
        // заполнение подробностей событий по алиасам
        foreach ($aliases as $alias => $data)
        {
            switch ($alias)
            {
                case 'email':
                    $objects = Model::Factory('Email');
                    $aliases[$alias] = $objects->FillEmailInfo($data);
                    
                    break;
                    
                case 'message':
                    $objects = Model::Factory('Message');
                    $aliases[$alias] = $objects->FillMessageInfo($data);
                    break;
                    
                case 'attachment':
                    $objects = Model::Factory('Attachment');
                    $aliases[$alias] = $objects->FillAttachmentInfo($data);
                    break;
                
                case 'order':
                    $objects = Model::Factory('Order');
                    $aliases[$alias] = $objects->FillOrderInfo($data);
                    break;
                
                case 'ra':
                    $objects = Model::Factory('RA');
                    $aliases[$alias] = $objects->FillRAInfo($data);
                    break;
                
                case 'ddt':
                    $objects = Model::Factory('DDT');
                    $aliases[$alias] = $objects->FillDDTInfo($data);
                    break;
                
                case 'cmr':
                    $objects = Model::Factory('CMR');
                    $aliases[$alias] = $objects->FillCMRInfo($data);
                    break;
                
                case 'invoice':
                    $objects = Model::Factory('Invoice');
                    $aliases[$alias] = $objects->FillInvoiceInfo($data);
                    break;
                
                case 'sc':
                    $objects = Model::Factory('SC');
                    $aliases[$alias] = $objects->FillSCInfo($data);
                    break;
                
                case 'inddt':
                    $objects = Model::Factory('InDDT');
                    $aliases[$alias] = $objects->FillInDDTInfo($data);
                    break;
                
                case 'suppinv':
                    $objects = Model::Factory('SupplierInvoice');
                    $aliases[$alias] = $objects->FillSupplierInvoiceInfo($data);
                    break;
                    
                default: break;
            }
        }
        
        $author_ids = array();
        // развертывание подробностей событий по строкам
        foreach ($rowset as $key => $event)
        {
            $object_alias   = $event['object_alias'];
            
            if (isset($aliases[$object_alias]))
            {
                foreach($aliases[$object_alias] as $item)
                {
                    if ($item[$object_alias . '_id'] == $event['object_id'])
                    {
                        //$rowset[$key][$object_alias . '_id'] = $event['object_id'];
                        $rowset[$key]['object'] = $item[$object_alias];
                        $author_ids[] = array('user_id' => $item[$object_alias]['created_by' ]);
                        
                        switch ($object_alias)
                        {
                            case 'attachment':
                                $title = $item[$object_alias]['original_name'];
                                break;
                            
                            case 'order':
                                $title          = $item[$object_alias]['doc_no'];
                                $description    = $item[$object_alias]['description'];
                                break;
                            
                            case 'ra':
                            case 'ddt':
                            case 'cmr':
                            case 'invoice':
                            case 'sc':
                            case 'inddt':
                            case 'suppinv':
                                $title          = $item[$object_alias]['doc_no'];
                                $description    = $title;
                                break;
                            
                            default:
                                $title          = $item[$object_alias]['title'];
                                $description    = $item[$object_alias]['description'];
                        }
                        
                        $rowset[$key]['object']['title']        = $title;
                        $rowset[$key]['object']['description']  = $description;
                        
                        break;
                    }
                }
            }
        }
        
        // добавление информации об авторах
        $authors = Model::Factory('User');
        $aliases['author'] = $authors->FillUserInfo($author_ids);
        
        foreach ($rowset as $key => $event)
        {
            foreach($aliases['author'] as $item)
            {
                if (!isset($item['user'])) continue;
                
                if ($item['user_id'] == $event['object']['created_by'])
                {
                    $rowset[$key]['object']['author'] = $item['user'];
                    break;
                }
            }
        }
        
        $rowset = array(
            'data'          => $rowset,
            'rows_count'    => $total_found,
        );
        
        Cache::SetData($hash, $rowset, $cache_tags, CACHE_LIFETIME_SHORT);

        return $rowset;
*/
    }
    
    /**
     * Преобразует object_alias, пришедший из сфинкса, в культурный object_alias
     */
    private function _get_entity_alias_by_type($entity_type_id)
    {
        switch ($entity_type_id)
        {
            case BLOG_ENTITY_TYPE_EMAIL         : return 'email';
            case BLOG_ENTITY_TYPE_MESSAGE       : return 'message';
            case BLOG_ENTITY_TYPE_ATTACHMENT    : return 'attachment';
            case BLOG_ENTITY_TYPE_ORDER         : return 'order';
            case BLOG_ENTITY_TYPE_RA            : return 'ra';
            case BLOG_ENTITY_TYPE_DDT           : return 'ddt';
            case BLOG_ENTITY_TYPE_CMR           : return 'cmr';
            case BLOG_ENTITY_TYPE_INVOICE       : return 'invoice';
            case BLOG_ENTITY_TYPE_SC            : return 'sc';
            case BLOG_ENTITY_TYPE_INDDT         : return 'inddt';
            case BLOG_ENTITY_TYPE_SUPPINV       : return 'suppinvoice';
            default : return '';
        }
    }
    
    private function _get_sphinx_indexes($object_alias)
    {
        switch($object_alias)
        {
            case 'biz-extended':
                return 'ix_mam_blog_emails, ix_mam_blog_messages, ix_mam_blog_attachments, 
                    ix_mam_blog_biz_orders, ix_mam_blog_biz_ras, ix_mam_blog_biz_ddts, ix_mam_blog_biz_cmrs, 
                    ix_mam_blog_biz_invoices, ix_mam_blog_biz_sc, ix_mam_blog_biz_inddts, ix_mam_blog_biz_suppinvs';
                
            default: return 'ix_mam_blog_emails, ix_mam_blog_emails_delta, ix_mam_blog_messages, ix_mam_blog_messages_delta, ix_mam_blog_attachments, ix_mam_blog_attachments_delta';
        }
    }
    
    
    public function GetTypesByObjectAlias($object_alias)
    {
        switch($object_alias)
        {
            case 'biz':
                return array(
                    BLOG_ENTITY_TYPE_EMAIL        => 'eMail',
                    BLOG_ENTITY_TYPE_MESSAGE      => 'Message',
                    BLOG_ENTITY_TYPE_ATTACHMENT   => 'Attachment',
                    BLOG_ENTITY_TYPE_ORDER        => 'Order',
                    BLOG_ENTITY_TYPE_RA           => 'RA',
                    BLOG_ENTITY_TYPE_DDT          => 'DDT',
                    BLOG_ENTITY_TYPE_CMR          => 'CMR',
                    BLOG_ENTITY_TYPE_INVOICE      => 'Invoice',
                    BLOG_ENTITY_TYPE_SC           => 'SC',
                    BLOG_ENTITY_TYPE_INDDT        => 'InDDT',
                    BLOG_ENTITY_TYPE_SUPPINV      => 'Supplier Invvoice',
                );
                
            default: array(
                array(BLOG_ENTITY_TYPE_EMAIL        => 'eMail'),
                array(BLOG_ENTITY_TYPE_MESSAGE      => 'Message'),
                array(BLOG_ENTITY_TYPE_ATTACHMENT   => 'Attachment'),
            );
        }
    }
}
