<?php
    $query_string = trim($_REQUEST['arg'], '/');

/* 20120703, zharkov: закомментировал, потому что в пингер добавлена логика    
    if ($query_string === 'service/pinger')
    {
        $_REQUEST['controller'] = 'service';
        $_REQUEST['action'] = 'pinger';

        return;
    }
*/    
    
    if (!Request::IsAjax())
    {
        // пары символов, которые не будут участвовать при анализе языка
        $not_lang_aliases = array('go', 'ad', 'sc', 'ra', 'qc', 'oc', );
        
        // текущий язык приложения
        $lang = '/^([\w]{2})\//';
        preg_match($lang, $query_string, $lang_matches);
        if (!empty($lang_matches) && !in_array($lang_matches[1], $not_lang_aliases))
        {
            $_REQUEST['lang'] = $lang_matches[1];
        }
        else
        {
            $_REQUEST['lang'] = DEFAULT_LANG;
        }
        
        $rev = '/~rev(\d{12})/';
        preg_match($rev, $query_string, $matches);
        if (!empty($matches))
        {
            $_REQUEST['stock_revision'] = $matches[1];
            $query_string               = rtrim(str_replace($matches[0], '', $query_string), '/');
        }        

        $rss = '/~rss/';
        preg_match($rss, $query_string, $matches);
        if (!empty($matches))
        {
            $_REQUEST['is_rss']     = 'yes';
            $query_string           = rtrim(str_replace($matches[0], '', $query_string), '/');
        }
        
        $page_no = '/~([0-9]+)/';
        preg_match($page_no, $query_string, $matches);
        if (!empty($matches))
        {
            $_REQUEST['page_no']    = intval($matches[1]);
            $query_string           = rtrim(str_replace($matches[0], '', $query_string), '/');
            
            if ($_REQUEST['page_no'] == 1)
            {
                header('Location: ' . APP_HOST . '/' . trim($query_string, '/'), true, 302);
                exit;
            }
        }
        
        $print = '/~print/';
        preg_match($print, $query_string, $matches);
        if (!empty($matches))
        {
            $_REQUEST['is_print']   = 'yes';
            $query_string           = rtrim(str_replace($matches[0], '', $query_string), '/');
        }
                
        $_REQUEST['pager_path'] = '/' . $query_string;
        
        // не зависимый от выбранного языка запрос
        if (!empty($lang_matches) && !in_array($lang_matches[1], $not_lang_aliases))
        {
            $query_string = rtrim(str_replace($lang_matches[0], '', $query_string), '/');
            $_REQUEST['query_string'] = '/' . $query_string;
        }        

        // 20120812, zharkov: определяет есть ли привязка к документу
        preg_match('#target/([a-z0-9:]+)/.*#', $query_string, $matches);
        if (!empty($matches))
        {
            $query_string   = trim(str_replace('target/' . $matches[1], '', $query_string), '/');
            $doc            = explode(':', $matches[1]);
            
            $_REQUEST['target_doc']     = $doc[0];
            $_REQUEST['target_doc_id']  = $doc[1];
        }
    }

    // специфические правила
    $rules = array(

        '/^(attachment)(.*)$/' => array(
            'subrules' => array(                
                
                '/^(attachment)\/([^\/]*?)\/([^\/]*?)$/'
                => array('module' => 'main', 'controller' => 'main', 'action' => 'showattachment', 'secretcode' => '$2', 'filename' => '$3'),
                
                '/^attachment\/uploadfile\/([a-z]+?)\/([0-9]+?)$/'
                => array('module' => 'attachment', 'controller' => 'main', 'action' => 'uploadfile', 'object_alias' => '$1', 'object_id' => '$2', ),
            )
        ),

        '/^(file)(.*)$/' => array(
            'subrules' => array(                
                
                '/^(file)\/([^\/]*?)\/([^\/]*?)$/'
                => array('module' => 'main', 'controller' => 'main', 'action' => 'showattachment', 'secretcode' => '$2', 'filename' => '$3'),
                
            )
        ),
        
        '/^(go)\/(.*?)$/' => array(
            'subrules' => array(
                'default' => array('module' => 'service', 'controller' => 'main', 'action' => 'go', 'url' => '$2'),
            )
        ),
        
        '/^(nopicture)(.*)$/' => array(
            'subrules' => array(                
                
                '/^(nopicture)\/(\w+)\/([^\/]*?)$/'
                => array('module' => 'main', 'controller' => 'main', 'action' => 'shownopicture', 'type' => '$2', 'file' => '$3'),
                
            )
        ),        
        
        '/^(picture)(.*)$/' => array(
            'subrules' => array(                
                
                '/^(picture)\/(\w+)\/([^\/]*?)\/(\w+)\/(.*)$/'
                => array('module' => 'main', 'controller' => 'main', 'action' => 'showpicture', 'type' => '$2', 'secretcode' => '$3', 'size' => '$4', 'filename' => '$5'),
                
            )
        ),

        '/^(stock)(.*)$/' => array(
            'subrules' => array(                
                
                '/^(stocks)$/'
                => array('module' => 'stock', 'controller' => 'main', 'action' => 'index'),

            )
        ),
 /*******************************Spravka*********************************/
				'/^(nomenclature)(.*)$/' => array(
            'subrules' => array(                
                
                '/^(nomenclature)$/'
                => array('module' => 'nomenclature', 'controller' => 'main', 'action' => 'index'),

            )
        ),
        
 /*******************************I am - I do*********************************/
				'/^(iamido)(.*)$/' => array(
            'subrules' => array(                
                
                '/^(iamido)$/'
                => array('module' => 'iamido', 'controller' => 'main', 'action' => 'index'),

            )
        ),
 
 /*******************************MainChat*********************************/
				'/^(mainchat)(.*)$/' => array(
            'subrules' => array(                
                
                '/^(mainchat)$/'
                => array('module' => 'mainchat', 'controller' => 'main', 'action' => 'index'),
                
                '/^(mainchat)\/(archive)\/(.*)$/'
                => array('module' => 'chat', 'controller' => 'main', 'action' => 'archive', 'date_to' => '$3'),

            )
        ),
 /***********************************************************************/
 
        '/^(order)(.*)$/' => array(
            'subrules' => array(                
                
                '/^(orders)$/'
                => array('module' => 'order', 'controller' => 'main', 'action' => 'index'),
                
                '/^(orders)\/(unregistered)$/'
                => array('module' => 'order', 'controller' => 'main', 'action' => 'unregistered'),

                '/^(orders)\/(filter)$/'
                => array('module' => 'order', 'controller' => 'main', 'action' => 'index'),

                '/^(orders)\/(filter)\/(.*)$/'
                => array('module' => 'order', 'controller' => 'main', 'action' => 'index', 'filter' => '$3'),

                '/^(order)\/(selectitems)\/(\d+)\/(position):(\d+)$/'
                => array('module' => 'order', 'controller' => 'main', 'action' => 'selectitems', 'order_id' => '$3', 'position_id' => '$5'),

                '/^(order)\/(\d+)\/(position)\/(edit)\/(\d+)$/'
                => array('module' => 'position', 'controller' => 'main', 'action' => 'edit', 'ref' => 'orderselectitems', 'order_id' => '$2', 'id' => '$5'),

                '/^(order)\/(neworder)\/(.*)$/'
                => array('module' => 'order', 'controller' => 'main', 'action' => 'neworder', 'guid' => '$3'),
                
            )
        ),

        '/^(person)(.*)$/' => array(
            'subrules' => array(                
                
                '/^(person)\/(addfromcompany)\/(\d+)$/'
                => array('module' => 'person', 'controller' => 'main', 'action' => 'add', 'company_id' => '$3'),

                '/^(persons)$/'
                => array('module' => 'person', 'controller' => 'main', 'action' => 'index'),

                '/^(persons)\/(staff)$/'
                => array('module' => 'person', 'controller' => 'main', 'action' => 'staff'),
                
                '/^(persons)\/(filter)$/'
                => array('module' => 'person', 'controller' => 'main', 'action' => 'index'),

                '/^(persons)\/(filter)\/(.*)$/'
                => array('module' => 'person', 'controller' => 'main', 'action' => 'index', 'filter' => '$3'),                
                
            )
        ),
        
        '/^(compan)(.*)$/' => array(
            'subrules' => array(                
                
                '/^(companies)$/'
                => array('module' => 'company', 'controller' => 'main', 'action' => 'index'),
                
                '/^(companies)\/(filter)$/'
                => array('module' => 'company', 'controller' => 'main', 'action' => 'index'),

                '/^(companies)\/(filter)\/(.*)$/'
                => array('module' => 'company', 'controller' => 'main', 'action' => 'index', 'filter' => '$3'),
            
                 '/^(company)\/(\d+)\/(person)\/(add)$/'
                => array('module' => 'person', 'controller' => 'main', 'action' => 'edit', 'company_id' => '$2'),
                
            )
        ),

        '/^(touchline)(.*)$/' => array(
            'subrules' => array(                
                
				'/^(touchline)$/'
                => array('module' => 'chat', 'controller' => 'main', 'action' => 'index'),
			
				'/^(touchline)\/(mustdo)$/'
                => array('module' => 'chat', 'controller' => 'main', 'action' => 'pendings'),
				
				'/^(touchline)\/(search)$/'
                => array('module' => 'chat', 'controller' => 'main', 'action' => 'search'),
			
				'/^(touchline)\/(search)\/(filter)$/'
                => array('module' => 'chat', 'controller' => 'main', 'action' => 'search'),

                '/^(touchline)\/(search)\/(filter)\/(.*)$/'
                => array('module' => 'chat', 'controller' => 'main', 'action' => 'search', 'filter' => '$4'),
            
		'/^(touchline)\/(archive)$/'
                => array('module' => 'chat', 'controller' => 'main', 'action' => 'archive'),
			
                '/^(touchline)\/(archive)\/(.*)$/'
                => array('module' => 'chat', 'controller' => 'main', 'action' => 'archive', 'date_to' => '$3'),
                
            )
        ),
        
        '/^(sc)(.*)$/' => array(
            'subrules' => array(                
                
                '/^(sc)\/(add)\/(\d+)$/'
                => array('module' => 'sc', 'controller' => 'main', 'action' => 'edit', 'order_id' => '$3'),

            )
        ),

        
        '/^(position)(.*)$/' => array(
            'subrules' => array(                
                
                '/^(positions)$/'
                => array('module' => 'position', 'controller' => 'main', 'action' => 'index'),

                '/^(positions)\/(filter)$/'
                => array('module' => 'position', 'controller' => 'main', 'action' => 'index'),

                '/^(positions)\/(filter)\/(.*)$/'
                => array('module' => 'position', 'controller' => 'main', 'action' => 'index', 'filter' => '$3'),

                '/^(position)\/(\d+)\/(item)\/(history)\/(\d+)$/'
                => array('module' => 'item', 'controller' => 'main', 'action' => 'history', 'ref' => 'positions', 'position_id' => '$2', 'id' => '$5'),

                '/^(position)\/(\d+)\/(item)\/(cut)\/(\d+)$/'
                => array('module' => 'item', 'controller' => 'main', 'action' => 'cut', 'ref' => 'positions', 'position_id' => '$2', 'id' => '$5'),                
                
                '/^(position)\/(\d+)\/(item)\/(move)\/([\d\,]+)$/'
                => array('module' => 'item', 'controller' => 'main', 'action' => 'move', 'ref' => 'positions', 'position_id' => '$2', 'id' => '$5'),

                '/^(position)\/(\d+)\/(item)\/(twin)\/([\d\,]+)$/'
                => array('module' => 'item', 'controller' => 'main', 'action' => 'move', 'type' => 'twin', 'ref' => 'positions', 'position_id' => '$2', 'id' => '$5'),

                '/^(position)\/(\d+)\/(item)\/(\d+)\/(revision)\/(.*)$/'
                => array('module' => 'item', 'controller' => 'main', 'action' => 'revision', 'ref' => 'positions', 'position_id' => '$2', 'id' => '$4', 'revision' => '$6'),

                '/^(position)\/(\d+)\/(item)\/(edit)\/([\d\,]+)$/'
                => array('module' => 'item', 'controller' => 'main', 'action' => 'edit', 'target_doc' => 'position', 'target_doc_id' => '$2', 'ids' => '$5'),
                
                '/^(position)\/(groupedit)\/([\d\,]+)$/'
                => array('module' => 'position', 'controller' => 'main', 'action' => 'groupedit', 'id' => '$3'),

                '/^(position)\/(reservation)\/(.*)$/'
                => array('module' => 'position', 'controller' => 'main', 'action' => 'reservation', 'filter' => '$3'),

                '/^(positions)\/(reserved)$/'
                => array('module' => 'position', 'controller' => 'main', 'action' => 'reserved'),

                '/^(positions)\/(reserved)\/(filter)$/'
                => array('module' => 'position', 'controller' => 'main', 'action' => 'reserved'),

                '/^(positions)\/(reserved)\/(filter)\/(.*)$/'
                => array('module' => 'position', 'controller' => 'main', 'action' => 'reserved', 'filter' => '$4'),
                
            )
        ),
		/*position1*/
        '/^(positionz)(.*)$/' => array(
            'subrules' => array(                
                
                '/^(positionzs)$/'
                => array('module' => 'positionz', 'controller' => 'main', 'action' => 'index'),

                '/^(positionzs)\/(filter)$/'
                => array('module' => 'positionz', 'controller' => 'main', 'action' => 'index'),

                '/^(positionzs)\/(filter)\/(.*)$/'
                => array('module' => 'positionz', 'controller' => 'main', 'action' => 'index', 'filter' => '$3'),

                '/^(positionz)\/(\d+)\/(item)\/(history)\/(\d+)$/'
                => array('module' => 'item', 'controller' => 'main', 'action' => 'history', 'ref' => 'positionzs', 'position_id' => '$2', 'id' => '$5'),

                '/^(positionz)\/(\d+)\/(item)\/(cut)\/(\d+)$/'
                => array('module' => 'item', 'controller' => 'main', 'action' => 'cut', 'ref' => 'positionzs', 'position_id' => '$2', 'id' => '$5'),                
                
                '/^(positionz)\/(\d+)\/(item)\/(move)\/([\d\,]+)$/'
                => array('module' => 'item', 'controller' => 'main', 'action' => 'move', 'ref' => 'positionzs', 'position_id' => '$2', 'id' => '$5'),

                '/^(positionz)\/(\d+)\/(item)\/(twin)\/([\d\,]+)$/'
                => array('module' => 'item', 'controller' => 'main', 'action' => 'move', 'type' => 'twin', 'ref' => 'positionzs', 'position_id' => '$2', 'id' => '$5'),

                '/^(positionz)\/(\d+)\/(item)\/(\d+)\/(revision)\/(.*)$/'
                => array('module' => 'item', 'controller' => 'main', 'action' => 'revision', 'ref' => 'positionzs', 'position_id' => '$2', 'id' => '$4', 'revision' => '$6'),

                '/^(positionz)\/(\d+)\/(item)\/(edit)\/([\d\,]+)$/'
                => array('module' => 'item', 'controller' => 'main', 'action' => 'edit', 'target_doc' => 'positionz', 'target_doc_id' => '$2', 'ids' => '$5'),
                
                '/^(positionz)\/(groupedit)\/([\d\,]+)$/'
                => array('module' => 'position1', 'controller' => 'main', 'action' => 'groupedit', 'id' => '$3'),

                '/^(positionz)\/(reservation)\/(.*)$/'
                => array('module' => 'positionz', 'controller' => 'main', 'action' => 'reservation', 'filter' => '$3'),

                '/^(positionzs)\/(reserved)$/'
                => array('module' => 'positionz', 'controller' => 'main', 'action' => 'reserved'),

                '/^(positionzs)\/(reserved)\/(filter)$/'
                => array('module' => 'positionz', 'controller' => 'main', 'action' => 'reserved'),

                '/^(positionzs)\/(reserved)\/(filter)\/(.*)$/'
                => array('module' => 'positionz', 'controller' => 'main', 'action' => 'reserved', 'filter' => '$4'),
                
            )
        ),		
		/*position1*/
        '/^(item)(.*)$/' => array(
            'subrules' => array(                
                
                '/^(items)$/'
                => array('module' => 'item', 'controller' => 'main', 'action' => 'index'),

                '/^(items)\/(filter)$/'
                => array('module' => 'item', 'controller' => 'main', 'action' => 'index'),

                '/^(items)\/(filter)\/(.*)$/'
                => array('module' => 'item', 'controller' => 'main', 'action' => 'index', 'filter' => '$3'),

                '/^(item)\/(twin)\/([\d\,]+)$/'
                => array('module' => 'item', 'controller' => 'main', 'action' => 'move', 'type' => 'twin', 'id' => '$3'),
                
                '/^(item)\/(\d+)\/(revision)\/(.*)$/'
                => array('module' => 'item', 'controller' => 'main', 'action' => 'revision', 'id' => '$2', 'revision' => '$4'),

                '/^(item)\/(groupedit)\/([\d\,]+)$/'
                => array('module' => 'item', 'controller' => 'main', 'action' => 'edit', 'ids' => '$3'),

                '/^(item)\/(edit)\/([\d\,]+)$/'
                => array('module' => 'item', 'controller' => 'main', 'action' => 'edit', 'ids' => '$3'),                
                
                '/^(item)\/(createalias)\/([\d\,]+)$/'
                => array('module' => 'item', 'controller' => 'main', 'action' => 'createalias', 'ids' => '$3'),                
                
                '/^(items)\/(ownerless)$/'
                => array('module' => 'item', 'controller' => 'main', 'action' => 'badlist', 'page' => 'ownerless'),

                '/^(items)\/(stockholderless)$/'
                => array('module' => 'item', 'controller' => 'main', 'action' => 'badlist', 'page' => 'stockholderless'),
                
            )
        ),
        
        '/^(objective)(.*)$/' => array(
            'subrules' => array(                
                
                '/^(objectives)$/'
                => array('module' => 'objective', 'controller' => 'main', 'action' => 'index'),

                '/^(objectives)\/(\d+)$/'
                => array('module' => 'objective', 'controller' => 'main', 'action' => 'index', 'year' => '$2'),

                '/^(objectives)\/(\d+)\/(\d+)$/'
                => array('module' => 'objective', 'controller' => 'main', 'action' => 'index', 'year' => '$2', 'quarter' => '$3'),
                
            )
        ),        
        
        '/^(biz)(.*)$/' => array(
            'subrules' => array(                
                
                '/^(bizes)$/'
                => array('module' => 'biz', 'controller' => 'main', 'action' => 'index'),
                
                '/^(bizes)\/(filter)$/'
                => array('module' => 'biz', 'controller' => 'main', 'action' => 'index'),

                '/^(bizes)\/(filter)\/(.*)$/'
                => array('module' => 'biz', 'controller' => 'main', 'action' => 'index', 'filter' => '$3'),  
				
				'/^(biz)\/(\d+)\/(addsubbiz)$/'
                => array('module' => 'biz', 'controller' => 'main', 'action' => 'edit', 'id' => '$2', 'subbiz' => TRUE),
        
            )
        ),
	
		'/^(newmessage)(.*)$/' => array(
            'subrules' => array(                

                '/^(newmessage)\/(answer)\/(\d+)$/'
                => array('module' => 'chat', 'controller' => 'main', 'action' => 'newmessage', 'message_id' => '$3'),              
				
				'/^(newmessage)\/(\w+)\/(\d+)$/'
                => array('module' => 'chat', 'controller' => 'main', 'action' => 'newmessage', 'object_alias' => '$2', 'object_id' => '$3'),  
                
                '/^(newmessage)$/'
                => array('module' => 'chat', 'controller' => 'main', 'action' => 'newmessage', 'object_alias' => 'chat', 'object_id' => '0'),  
                
            )
        ),
            
        '/^(email)(.*)$/' => array(
            'subrules' => array(                
                
                '/^(emails)$/'
                => array('module' => 'email', 'controller' => 'main', 'action' => 'index'),

                '/^(emails)\/(filter)$/'
                => array('module' => 'email', 'controller' => 'main', 'action' => 'index'),
                
                '/^(emails)\/(filter)\/(.*)$/'
                => array('module' => 'email', 'controller' => 'main', 'action' => 'index', 'filter' => '$3'),

                '/^(email)\/(\d+)~tid(.*)$/'
                => array('module' => 'email', 'controller' => 'main', 'action' => 'view', 'id' => '$2', 'token' => '$3'),
                
                '/^(email)\/(compose)$/'
                => array('module' => 'email', 'controller' => 'main', 'action' => 'compose'),
            
                '/^(emails)\/(dfa)$/'
                => array('module' => 'email', 'controller' => 'main', 'action' => 'index', 'is_dfa' => TRUE),
                
                '/^(emails)\/(dfa)\/(filter)\/(.*)$/'
                => array('module' => 'email', 'controller' => 'main', 'action' => 'index', 'is_dfa' => TRUE, 'filter' => '$4'),
            
                '/^emails\/dfa\/other$/'
                => array('module' => 'email', 'controller' => 'main', 'action' => 'index', 'is_dfa_other' => TRUE),
            
                '/^(emails)\/(dfa)\/(other)\/(filter)\/(.*)$/'
                => array('module' => 'email', 'controller' => 'main', 'action' => 'index', 'is_dfa_other' => TRUE, 'filter' => '$5'),
            
                '/^(emails)\/(deleted)$/'
                => array('module' => 'email', 'controller' => 'main', 'action' => 'deletedbyuser'),
                
                '/^(emails)\/(deleted)\/(filter)$/'
                => array('module' => 'email', 'controller' => 'main', 'action' => 'deletedbyuser'),
                
                '/^(emails)\/(deleted)\/(filter)\/(.*)$/'
                => array('module' => 'email', 'controller' => 'main', 'action' => 'deletedbyuser', 'filter' => '$4'),
            
                '/^(email)\/(delete)\/(spam)\/(.*)$/'
                => array('module' => 'email', 'controller' => 'main', 'action' => 'deletefromspam', 'key' => '$4'),
            
                '/^(email)\/(erase)\/(spam)\/(.*)$/'
                => array('module' => 'email', 'controller' => 'main', 'action' => 'erase', 'key' => '$4'),
            
            
                // EMAIL FILTER
                '/^email\/filter$/'
                => array('module' => 'email', 'controller' => 'filter', 'action' => 'index', ),
            
                '/^email\/filters$/'
                => array('module' => 'email', 'controller' => 'filter', 'action' => 'index', ),
            
                '/^email\/filter\/(\d+)$/'
                => array('module' => 'email', 'controller' => 'filter', 'action' => 'view', 'id' => '$1', ),
            
                '/^email\/filter\/(\d+)\/(edit|delete)$/'
                => array('module' => 'email', 'controller' => 'filter', 'action' => '$2', 'id' => '$1', ),
            
                '/^email\/filter\/cronapply\/(.*)$/'
                => array('module' => 'email', 'controller' => 'filter', 'action' => 'applyforexisting', 'id' => '$1', ),
                
                '/^email\/filter\/addfromemail\/(\d+)$/'
                => array('module' => 'email', 'controller' => 'filter', 'action' => 'add', 'email_id' => '$1', ),
            )
        ),
        
        '/^(emailmanager)(.*)$/' => array(
            'subrules' => array(                
                
                '/^(emailmanager)$/'
                => array('module' => 'emailmanager', 'controller' => 'main', 'action' => 'index'),

                '/^(emailmanager)\/(filter)$/'
                => array('module' => 'emailmanager', 'controller' => 'main', 'action' => 'index'),
                
                '/^(emailmanager)\/(filter)\/(.*)$/'
                => array('module' => 'emailmanager', 'controller' => 'main', 'action' => 'index', 'filter' => '$3'),

                '/^(emailmanager)\/(\d+)~tid(.*)$/'
                => array('module' => 'emailmanager', 'controller' => 'main', 'action' => 'view', 'id' => '$2', 'token' => '$3'),
                
                '/^(emailmanager)\/(compose)$/'
                => array('module' => 'emailmanager', 'controller' => 'main', 'action' => 'compose'),
            
                '/^(emailmanager)\/(dfa)$/'
                => array('module' => 'emailmanager', 'controller' => 'main', 'action' => 'index', 'is_dfa' => TRUE),
                
                '/^(emailmanager)\/(dfa)\/(filter)\/(.*)$/'
                => array('module' => 'emailmanager', 'controller' => 'main', 'action' => 'index', 'is_dfa' => TRUE, 'filter' => '$4'),
            
                '/^(emailmanager)\/dfa\/other$/'
                => array('module' => 'emailmanager', 'controller' => 'main', 'action' => 'index', 'is_dfa_other' => TRUE),
            
                '/^(emailmanager)\/(dfa)\/(other)\/(filter)\/(.*)$/'
                => array('module' => 'emailmanager', 'controller' => 'main', 'action' => 'index', 'is_dfa_other' => TRUE, 'filter' => '$5'),
            
                '/^(emailmanager)\/(deleted)$/'
                => array('module' => 'emailmanager', 'controller' => 'main', 'action' => 'deletedbyuser'),
                
                '/^(emailmanager)\/(deleted)\/(filter)$/'
                => array('module' => 'emailmanager', 'controller' => 'main', 'action' => 'deletedbyuser'),
                
                '/^(emailmanager)\/(deleted)\/(filter)\/(.*)$/'
                => array('module' => 'emailmanager', 'controller' => 'main', 'action' => 'deletedbyuser', 'filter' => '$4'),
            
                '/^(emailmanager)\/(delete)\/(spam)\/(.*)$/'
                => array('module' => 'emailmanager', 'controller' => 'main', 'action' => 'deletefromspam', 'key' => '$4'),
            
                '/^(emailmanager)\/(erase)\/(spam)\/(.*)$/'
                => array('module' => 'emailmanager', 'controller' => 'main', 'action' => 'erase', 'key' => '$4'),
            
            
                // EMAIL FILTER
                '/^emailmanager\/filter$/'
                => array('module' => 'emailmanager', 'controller' => 'filter', 'action' => 'index', ),
            
                '/^emailmanager\/filters$/'
                => array('module' => 'emailmanager', 'controller' => 'filter', 'action' => 'index', ),
            
                '/^emailmanager\/filter\/(\d+)$/'
                => array('module' => 'emailmanager', 'controller' => 'filter', 'action' => 'view', 'id' => '$1', ),
            
                '/^emailmanager\/filter\/(\d+)\/(edit|delete)$/'
                => array('module' => 'emailmanager', 'controller' => 'filter', 'action' => '$2', 'id' => '$1', ),
            
                '/^emailmanager\/filter\/cronapply\/(.*)$/'
                => array('module' => 'emailmanager', 'controller' => 'filter', 'action' => 'applyforexisting', 'id' => '$1', ),
                
                '/^emailmanager\/filter\/addfromemail\/(\d+)$/'
                => array('module' => 'emailmanager', 'controller' => 'filter', 'action' => 'add', 'email_id' => '$1', ),
            )
        ),        
        
        '/^(qc)(.*)$/' => array(
            'subrules' => array(                

                '/^(qc)\/(add)\/(\w+):(\d+)$/'
                => array('module' => 'qc', 'controller' => 'main', 'action' => 'edit', 'source_doc' => '$3', 'source_doc_id' => '$4'),

                '/^(qc)\/(add)$/'
                => array('module' => 'qc', 'controller' => 'main', 'action' => 'edit'),
        
            )
        ),

        '/^(invoice)(.*)$/' => array(
            'subrules' => array(                

                '/^(invoice)\/(add)$/'
                => array('module' => 'invoice', 'controller' => 'main', 'action' => 'edit'),

                '/^(invoice)\/(add)\/(\w+):(\d+)$/'
                => array('module' => 'invoice', 'controller' => 'main', 'action' => 'edit', 'source_doc' => '$3', 'source_doc_id' => '$4'),

                '/^(invoices)$/'
                => array('module' => 'invoice', 'controller' => 'main', 'action' => 'index'),

                '/^(invoices)\/(filter)$/'
                => array('module' => 'invoice', 'controller' => 'main', 'action' => 'index'),
        
                '/^(invoices)\/(filter)\/(.*)$/'
                => array('module' => 'invoice', 'controller' => 'main', 'action' => 'index', 'filter' => '$3'),
        
            )
        ),        
        
        '/^(supplierinvoice)(.*)$/' => array(
            'subrules' => array(                

                '/^(supplierinvoice)\/(add)$/'
                => array('module' => 'supplierinvoice', 'controller' => 'main', 'action' => 'edit'),

                '/^(supplierinvoice)\/(add)\/(\w+):([0-9\,]+)$/'
                => array('module' => 'supplierinvoice', 'controller' => 'main', 'action' => 'edit', 'source_doc' => '$3', 'source_doc_id' => '$4'),

                '/^(supplierinvoices)$/'
                => array('module' => 'supplierinvoice', 'controller' => 'main', 'action' => 'index'),

                '/^(supplierinvoices)\/(filter)$/'
                => array('module' => 'supplierinvoice', 'controller' => 'main', 'action' => 'index'),
        
                '/^(supplierinvoices)\/(filter)\/(.*)$/'
                => array('module' => 'supplierinvoice', 'controller' => 'main', 'action' => 'index', 'filter' => '$3'),
        
            )
        ),
    
        '/^(oc)(.*)$/' => array(
            'subrules' => array(
                '/^(oc)\/(add)$/'
                => array('module' => 'oc', 'controller' => 'main', 'action' => 'edit'),

                '/^(oc)\/(add)\/(\w+):([0-9\,]+)$/'
                => array('module' => 'oc', 'controller' => 'main', 'action' => 'edit', 'source_doc' => '$3', 'source_doc_id' => '$4'),

                '/^(ocs)$/'
                => array('module' => 'oc', 'controller' => 'main', 'action' => 'index'),

                '/^(oc)\/(filter)$/'
                => array('module' => 'oc', 'controller' => 'main', 'action' => 'index'),
        
                '/^(oc)\/(filter)\/(.*)$/'
                => array('module' => 'oc', 'controller' => 'main', 'action' => 'index', 'filter' => '$3'),
            ),
        ),

        '/^(report)(.*)$/' => array(
            'subrules' => array(                

                '/^(report)\/(\w+)\/(data)$/'
                => array('module' => 'report', 'controller' => 'main', 'action' => '$2'),
        
                '/^(report)\/(\w+)\/(data)\/(.*)$/'
                => array('module' => 'report', 'controller' => 'main', 'action' => '$2', 'data' => '$4'),
        
            )
        ),        

        '/^(ddt)(.*)$/' => array(
            'subrules' => array(
                '/^(ddt)\/(add)\/(\w+):(\d+)$/'
                => array('module' => 'ddt', 'controller' => 'main', 'action' => 'add', 'source_doc' => '$3', 'source_doc_id' => '$4'),
            )
        ),
    
        '/^(cmr)(.*)$/' => array(
            'subrules' => array(
                '/^(cmr)\/(add)\/(\w+):(\d+)$/'
                => array('module' => 'cmr', 'controller' => 'main', 'action' => 'add', 'source_doc' => '$3', 'source_doc_id' => '$4'),
            )
        ),
    
        '/^(ra)(.*)$/' => array(
            'subrules' => array(
                '/^(ra)\/(add)\/([0-9,]+)$/'
                => array('module' => 'ra', 'controller' => 'main', 'action' => 'add', 'order_ids' => '$3'),
            
                '/^(ra)\/(\d+)\/(item)\/(\d+)\/(addvariant)$/'
                => array('module' => 'ra', 'controller' => 'main', 'action' => 'addvariant', 'ra_id' => '$2', 'item_id' => '$4'),                            
            )
        ),
        
        '/^([^\/]+?)\/(\d+)\/(email)(.*)$/' => array(
            'subrules' => array(                
                
                '/^([^\/]+?)\/(\d+)\/(emails)$/'
                => array('module' => 'email', 'controller' => 'main', 'action' => 'index', 'object_alias' => '$1', 'object_id' => '$2'),
                
                '/^([^\/]+?)\/(\d+)\/(emails)\/(filter)$/'
                => array('module' => 'email', 'controller' => 'main', 'action' => 'index', 'object_alias' => '$1', 'object_id' => '$2'),
                
                '/^([^\/]+?)\/(\d+)\/(emails)\/(filter)\/(.*)$/'
                => array('module' => 'email', 'controller' => 'main', 'action' => 'index', 'object_alias' => '$1', 'object_id' => '$2', 'filter' => '$5'),
                
                '/^([^\/]+?)\/(\d+)\/(email)\/(compose)$/'
                => array('module' => 'email', 'controller' => 'main', 'action' => 'compose', 'object_alias' => '$1', 'object_id' => '$2'),
            
                '/^([^\/]+?)\/(\d+)\/(emails)\/(dfa)$/'
                => array('module' => 'email', 'controller' => 'main', 'action' => 'index', 'object_alias' => '$1', 'object_id' => '$2', 'is_dfa' => TRUE, ),
                
                '/^([^\/]+?)\/(\d+)\/emails\/dfa\/other$/'
                => array('module' => 'email', 'controller' => 'main', 'action' => 'index', 'object_alias' => '$1', 'object_id' => '$2', 'is_dfa_other' => TRUE, ),
            
                '/^([^\/]+?)\/(\d+)\/(emails)\/(deleted)$/'
                => array('module' => 'email', 'controller' => 'main', 'action' => 'deletedbyuser', 'object_alias' => '$1', 'object_id' => '$2'),
            
                '/^([^\/]+?)\/(\d+)\/(emails)\/(deleted)\/(filter)$/'
                => array('module' => 'email', 'controller' => 'main', 'action' => 'deletedbyuser', 'object_alias' => '$1', 'object_id' => '$2'),
                
                '/^([^\/]+?)\/(\d+)\/(emails)\/(deleted)\/(filter)\/(.*)$/'
                => array('module' => 'email', 'controller' => 'main', 'action' => 'deletedbyuser', 'object_alias' => '$1', 'object_id' => '$2', 'filter' => '$5'),

                '/^([^\/]+?)\/(\d+)\/(email)\/(\d+)$/'
                => array('module' => 'email', 'controller' => 'main', 'action' => 'view', 'object_alias' => '$1', 'object_id' => '$2', 'id' => '$4'),

                '/^([^\/]+?)\/(\d+)\/(email)\/(\d+)~tid(.+)$/'
                => array('module' => 'email', 'controller' => 'main', 'action' => 'view', 'object_alias' => '$1', 'object_id' => '$2', 'id' => '$4', 'token' => '$5'),

                '/^([^\/]+?)\/(\d+)\/(email)\/(\d+)\/(\w+)$/'
                => array('module' => 'email', 'controller' => 'main', 'action' => '$5', 'object_alias' => '$1', 'object_id' => '$2', 'id' => '$4'),
            )
        ),
        
        // BLOG
        '/^([^\/]+?)\/(\d+)\/blog(.*)$/' => array(
            'subrules' => array(
                '/^([^\/]+?)\/(\d+)\/blog$/'
                => array('module' => 'blog', 'controller' => 'main', 'action' => 'index', 'object_alias' => '$1', 'object_id' => '$2', ),
                
                '/^([^\/]+?)\/(\d+)\/blogs$/'
                => array('module' => 'blog', 'controller' => 'main', 'action' => 'index', 'object_alias' => '$1', 'object_id' => '$2', ),
                
                '/^([^\/]+?)\/(\d+)\/blog\/filter$/'
                => array('module' => 'blog', 'controller' => 'main', 'action' => 'index', 'object_alias' => '$1', 'object_id' => '$2', ),
                
                '/^([^\/]+?)\/(\d+)\/blog\/filter\/(.*)$/'
                => array('module' => 'blog', 'controller' => 'main', 'action' => 'index', 'object_alias' => '$1', 'object_id' => '$2', 'filter' => '$3'),
            ),
        ),
    
        '/^(stockoffer)(.*)$/' => array(
            'subrules' => array(                
                
                '/^stockoffers$/'
                => array('module' => 'stockoffer', 'controller' => 'main', 'action' => 'index'),
            
                '/^stockoffer\/add$/'
                => array('module' => 'stockoffer', 'controller' => 'main', 'action' => 'edit'),
            )
        ),
    );


    // базовые правила
    $standart_rules = array(
    
        '/^([^\/]+?)\/([^\/]+?)\/([^\/]+?)\/([^\/]+?)$/' => array(
            'subrules' => array(
                'default'   => array('module' => '$1', 'controller' => '$2', 'action' => '$3', 'id' => '$4'),
            )
        ),

        '/^([^\/]+?)\/([^\/]+?)\/([^\/]+?)$/' => array(
            'subrules' => array(
                '/^token\/([^\/]+?)\/(\d+)$/'       => array('module' => 'account', 'controller' => 'main', 'action' => 'autologin', 'token' => '$1', 'user_id' => '$2'),
                '/^([^\/]+?)\/(\d+)\/(dropbox)$/'   => array('module' => 'dropbox', 'controller' => 'main', 'action' => 'index', 'object_alias' => '$1', 'object_id' => '$2'),
                '/^([^\/]+?)\/(\d+)\/(touchline)$/'	=> array('module' => 'chat', 'controller' => 'main', 'action' => 'index', 'object_alias' => '$1', 'object_id' => '$2'),
                '/^([^\/]+?)\/([^\/]+?)\/(\d+)$/'   => array('module' => '$1', 'controller' => 'main', 'action' => '$2', 'id' => '$3'),
                '/^([^\/]+?)\/(\d+)\/([^\/]+?)$/'   => array('module' => '$1', 'controller' => 'main', 'action' => '$3', 'id' => '$2'),
                'default'                           => array('module' => '$1', 'controller' => '$2', 'action' => '$3'),
            )
        ),

        '/^([^\/]+?)\/([^\/]+?)$/' => array(
            'subrules' => array(
                '/^(regions)\/(\d+)$/'  => array('module' => 'region', 'controller' => 'main', 'action' => 'index', 'country_id' => '$2'),
                '/^(cities)\/(\d+)$/'   => array('module' => 'city', 'controller' => 'main', 'action' => 'index', 'region_id' => '$2'),
                '/^([^\/]+?)\/(\d+)$/'  => array('module' => '$1', 'controller' => 'main', 'action' => 'view', 'id' => '$2'),
                'default'               => array('module' => '$1', 'controller' => 'main', 'action' => '$2'),
            )
        ),

        '/^([^\/]+?)$/' => array(
            'subrules' => array(
                '/^(login)$/'               => array('module' => 'account', 'controller' => 'main', 'action' => 'login'),
                '/^(logout)$/'              => array('module' => 'account', 'controller' => 'main', 'action' => 'logout'),
                '/^(directories)$/'         => array('module' => 'directory', 'controller' => 'main', 'action' => 'index'),
                '/^(products)$/'            => array('module' => 'product', 'controller' => 'main', 'action' => 'index'),
                '/^(countries)$/'           => array('module' => 'country', 'controller' => 'main', 'action' => 'index'),
                '/^(markets)$/'             => array('module' => 'market', 'controller' => 'main', 'action' => 'index'),
                '/^(bizes)$/'               => array('module' => 'biz', 'controller' => 'main', 'action' => 'index'),
                '/^(permissiondenied)$/'    => array('module' => 'main', 'controller' => 'main', 'action' => 'permissiondenied'),
                '/^(regrequests)$/'         => array('module' => 'regrequest', 'controller' => 'main', 'action' => 'index'),
                '/^(reports)$/'             => array('module' => 'report', 'controller' => 'main', 'action' => 'index'),                
                'default'                   => array('module' => '$1', 'controller' => 'main', 'action' => 'index'),
            )
        ),

        '/^$/' => array(
            'subrules' => array(
                'default' => array('module' => 'main', 'controller' => 'main', 'action' => 'index'),
            )
        ),
    
    );
    

    // начало разбора запроса
    $rule_found = false;

    // главная страница
    $result = get_home_page($query_string);
    if (isset($result)) $rule_found = true;        
    
    // поиск подходящего специфического правила
    if (!$rule_found)
    {
        $result = get_appropriate_rule($query_string, $rules);    
		//print_r($query_string);
		//print_r($result);
        if (isset($result)) $rule_found = true;
    }

    // поиск подходящего стандартного правила
    if (!$rule_found)
    {
        $result = get_appropriate_rule($query_string, $standart_rules);    
        if (isset($result)) $rule_found = true;
    }

    if (!$rule_found)
    {
        // уничтожение концевого слеша
        $trailing_slash = '/^(.*)(\/+)$/';
        preg_match($trailing_slash, $query_string, $matches);
        if (!empty($matches) && $query_string != '/')
        {
            header('Location: ' . APP_HOST . '/' . trim($query_string, '/'), true, 302);
            exit;
        }    
    }

    // ствтическое перенаправление (со старого адреса, с ошибочочного адреса)
    if (isset($result['redirect_permanent']))
    {
        $location   = APP_HOST;
        $path       = '';
        foreach ($result['redirect_permanent'] as $value)
        {
            $path .= '/' . $value;
        }

        header('Location: ' . $location . $path, true, 302);
        exit;
    }

    // правило на найдено
    if (!$rule_found)
    {
        _404();
    }


    // запрос обработан
    foreach ($result as $param => $value)
    {
        $_REQUEST[$param] = $value;
    }
    
    $_REQUEST['page_alias'] = get_page_alias($result);
    $_REQUEST['name_space'] = get_name_space($result);
    
    // для пингера не добавляется запись в лог
    if ($_REQUEST['module'] != 'service' && $_REQUEST['action'] != 'pinger')
    {
        Log::AddLine( LOG_CUSTOM, "REQUEST:: mappings.php. \$_REQUEST=" . var_export($_REQUEST, TRUE));
    }
    
    // если аякс-запрос, удаляется экранирование
    if (Request::IsAjax())
    {
        foreach ($_REQUEST as $key => $value)
        {
            if (in_array($key, array('arg', '__utnx', 'session_id', 'controller', 'action')))  continue;           
            $_REQUEST[$key] = stripslashes_in_array($value);
        }    
    }

    
/**
 * Удаляет экранирование символов в массиве
 * 
 * @param mixed $values
 */
function stripslashes_in_array($values)
{
    if (!is_array($values)) return stripslashes($values);
    
    if (empty($values)) return $values;
    
    $result = array();
    foreach ($values as $key => $value)
    {
        $result[$key] = stripslashes_in_array($value);
    }
    
    return $result;
}

/**
 * Ищет подходящее правило разбора в массиве правил для строки
 * 
 * @param string $query_string   - строка
 * @param array $rules           - правила
 * @return array()
 */
function get_appropriate_rule($query_string, $rules)
{   
	//print_r($query_string.'_$$_'); 
    foreach ($rules as $rule => $subrules)
    {        
        if (preg_match_all($rule, $query_string, $rule_matches))
        {           
            foreach ($subrules['subrules'] as $subrule => $result)
            {
                if ($subrule == 'default' || preg_match_all($subrule, $query_string, $matches))
                {
                    if ($subrule == 'default') $matches = $rule_matches;

                    foreach ($result as $param => $value)
                    {
                        if (!is_array($value))
                        {
                            if (preg_match('/\$[0-9]/', $value)) $result[$param] = $matches[intval($value[1])][0];
                        }
                        else
                        {
                            foreach ($value as $param1 => $value1)
                            {
                                if (preg_match('/\$[0-9]/', $value1)) $result[$param][$param1] = $matches[intval($value1[1])][0];
                            }
                        }
                    }

                    return $result;
                }
            }
        }
    }
    
    return null;
}

/**
 * Получает алиас страницы на основании результата разбора запроса
 * 
 * @param mixed $page_params
 * @return mixed
 */
function get_page_alias($page_params)
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

/**
 * Получает name_space для страницы на основании результата разбора запроса
 * 
 * @param mixed $page_params
 * @return mixed
 */
function get_name_space($page_params)
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

/**
 * Определяет главную страницу
 * 
 * @param mixed $query_string
 */
function get_home_page($query_string)
{
    if (empty($query_string)) return array('module' => 'main', 'controller' => 'main', 'action' => 'index');
    
    return null;
}
