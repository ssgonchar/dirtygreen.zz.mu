<?php
require_once APP_PATH . 'classes/models/attachment.class.php';
require_once APP_PATH . 'classes/models/biz.class.php';
require_once APP_PATH . 'classes/models/email.class.php';
require_once APP_PATH . 'classes/models/inddt.class.php';
require_once APP_PATH . 'classes/models/message.class.php';
require_once APP_PATH . 'classes/models/oc.class.php';
require_once APP_PATH . 'classes/models/order.class.php';
require_once APP_PATH . 'classes/models/qc.class.php';
require_once APP_PATH . 'classes/models/sc.class.php';

/**
 * Common class for object // Общий класс для объекта
 * 
 * @version 20120720, zharkov
 */

class ObjectComponent
{
    
    /**
     * Конструктор
     *
     */
    function ObjectComponent()
    {
        
    }
    
    /**
     * Parse content // Парсит контент
     * 
     * @param mixed $content
     * @param mixed $encode - если true, то заменяет найденные объекты на их значения
     * 
     * @version 20120912, zharkov
     */
    function ParseContent($content, $encode = true)
    {
        // found objects // массив найденных объектов
        $objects = array();        

        // parse biz // парсит бизнесы
        //preg_match_all('#biz(\d{4}\.*\d{0,2})#i', $content, $matches);
        preg_match_all('#biz(\d{4}(\.\d{1,2})?)#i', $content, $matches);
        
        // prepare array
        if (isset($matches) && isset($matches[0]))
        {
            $arr = array_unique($matches[0]);
            $arr = array_values($arr);

            usort($arr, function($a, $b){
                return strlen($b) - strlen($a);
            });
            
            $matches = array();
            foreach ($arr as $value)
            {
                $matches[0][] = $value;
                $matches[1][] = str_ireplace('biz', '', $value);
            }
        }
        else
        {
            $matches = array();
        }
        
        // parsing
        if (!empty($matches))
        {
            $bizes = new Biz();
            
            foreach ($matches[1] as $key => $row)
            {
                $biz = explode('.', $row);
                if (!empty($biz))
                {
                    $number = intval($biz[0]);
                    $suffix = count($biz) > 1 ? intval($biz[1]) : 0;
                    
                    $result = $bizes->GetByNumber($number, $suffix);
                    if (isset($result))
                    {                        
                        if (!isset($objects['biz' . $result['id']]))
                        {
                            $objects['biz' . $result['id']] = array('alias' => 'biz', 'id' => $result['id']);
                        }
                        
                        if ($result['product_id'] > 0 && !isset($objects['product' . $result['product_id']]))
                        {
                            $objects['product' . $result['product_id']] = array('alias' => 'product', 'id' => $result['product_id']);
                        }
                        
                        $biz_companies = $bizes->GetCompanies($result['id']);
                        
                        if (!empty($biz_companies))
                        {
                            foreach($biz_companies as $company)
                            {
                                if (!isset($company['company'])) continue;
                                if ($company['company']['country_id'] <= 0) continue;
                                if (isset($objects['country' . $company['company']['country_id']])) continue;
                                
                                $objects['country' . $company['company']['country_id']] = array('alias' => 'country', 'id' => $company['company']['country_id']);
                            }
                        }
                        
                        // replace biz title with link
                        if ($encode)
                        {
                            $biz_title_found = $matches[0][$key];
                            
                            preg_match_all('#<a[^>]*>[^<]*' . $biz_title_found . '[^<]*</a>#i', $content, $biz_links_matches);
                            
                            // protect from parsing links with biz inside
                            if (isset($biz_links_matches) && isset($biz_links_matches[0]) && !empty($biz_links_matches[0]))
                            {
                                foreach ($biz_links_matches[0] as $matched_biz_link_key => $matched_biz_link)
                                {
                                    $content = str_replace($matched_biz_link, '{direct_biz_link_' . $matched_biz_link_key. '}', $content);
                                }
                            }
                            
                            // replace biz title with link
                            $content = str_replace($biz_title_found, '<a href="/biz/' . $result['id'] . '/blog" title="' . strtr($result['doc_no_full'], array('"' => '\"')) . '" class="bizlink tooltip">' . $result['doc_no'] . '</a>', $content);
                            
                            // restore direct biz links
                            if (isset($biz_links_matches) && isset($biz_links_matches[0]) && !empty($biz_links_matches[0]))
                            {
                                foreach ($biz_links_matches[0] as $matched_biz_link_key => $matched_biz_link)
                                {

                                    $content = str_replace('{direct_biz_link_' . $matched_biz_link_key. '}', $matched_biz_link, $content);
                                }
                            }                            
                        }
                    }
                    
                    // if sub biz, search for parent biz // если бизнес дочерний, то производим поиск родителя и добавляем в список объектов
                    if ($suffix > 0)
                    {
                        $result = $bizes->GetByNumber($number, 0);
                        
                        if (isset($result))
                        {
                            if (!isset($objects['biz' . $result['id']]))
                            {
                                $objects['biz' . $result['id']] = array('alias' => 'biz', 'id' => $result['id']);
                            }
                            
                            if ($result['product_id'] > 0 && !isset($objects['product' . $result['product_id']]))
                            {
                                $objects['product' . $result['product_id']] = array('alias' => 'product', 'id' => $result['product_id']);
                            }
                            
                            $biz_companies = $bizes->GetCompanies($result['id']);
                        
                            if (!empty($biz_companies))
                            {
                                foreach($biz_companies as $company)
                                {
                                    if (!isset($company['company'])) continue;
                                    if ($company['company']['country_id'] <= 0) continue;
                                    if (isset($objects['country' . $company['company']['country_id']])) continue;

                                    $objects['country' . $company['company']['country_id']] = array('alias' => 'country', 'id' => $company['company']['country_id']);
                                }
                            }
                        }
                    }
                }
            }
        }
        
        // parse orders // парсит заказы
        $matches    = array();
        preg_match_all('#inpo(\d+)#i', $content, $matches);
        
        if (!empty($matches))
        {
            $modelOrder = new Order();
            
            foreach ($matches[1] as $key => $row)
            {
                $number = intval($row);

                $result = $modelOrder->GetByNumber($number);
                if (!isset($result)) continue;
                if ($encode)
                {
                    $content = str_replace($matches[0][$key], '<a href="/order/' . $result['id'] . '">' . $result['doc_no'] . '</a>', $content);
                }

                if (!isset($objects['order' . $result['id']]))
                {
                    $objects['order' . $result['id']] = array('alias' => 'order', 'id' => $result['id']);
                }
            }
        }
        
        return array(
            $content, 
            $objects,
        );
    }
    
    /**
     * Возвращает вложенные хлебные крошки
     * 
     * @param mixed $object_alias
     * @param mixed $object_id
     * 
     * @version 20120815, zharkov
     */
    function GetNestedBreadCrumbs($object_alias, $object_id)
    {
        if ($object_alias == 'qc')
        {
            $qcs    = new QC();
            $qc     = $qcs->GetById($object_id);                                
            if (empty($qc)) return array();
            
            $qc = $qc['qc'];
            
            if (!empty($qc['order_id']))
            {
                $orders = new Order();
                $order  = $orders->GetById($qc['order_id']);
                $order  = $order['order'];                
                
                return array(
                    'Orders'            => '/orders',
                    $order['doc_no']    => '/order/' . $qc['order_id'],
                    $qc['doc_no']       => '/qc/' . $object_id
                );                
            }
            else
            {
                return array(
                    'QCs'           => '/qc',
                    $qc['doc_no']   => '/qc/' . $object_id
                );                
            }
        }
        else if ($object_alias == 'sc')
        {
            $scs    = new SC();
            $sc     = $scs->GetById($object_id);                                
            if (empty($sc)) return array();
            
            $sc = $sc['sc'];
            
            $orders = new Order();
            $order  = $orders->GetById($sc['order_id']);
            $order  = $order['order'];
            
            return array(
                'Orders'            => '/orders',
                $order['doc_no']    => '/order/' . $sc['order_id'],
                $sc['doc_no']       => '/sc/' . $object_id
            );            
        }
        else if ($object_alias == 'order')
        {
            $orders    = new Order();
            $order     = $orders->GetById($object_id);                                
            if (empty($order)) return array();
            
            $order  = $order['order'];
            
            return array(
                'Orders'            => '/orders',
                $order['doc_no']    => '/order/' . $order['id']
            );            
        }
        else if ($object_alias == 'biz')
        {
            $bizes  = new Biz();
            $biz    = $bizes->GetById($object_id);
            if (empty($biz)) return array();

            return array(
                'BIZs'                      => '/bizes',
                $biz['biz']['doc_no_full']  => '/biz/' . $object_id
            );            
        }
        else if ($object_alias == 'company')
        {
            $companies  = new Company();
            $company    = $companies->GetById($object_id);
            if (empty($company)) return array();

            return array(
                'Companies'                     => '/companies',
                $company['company']['title']    => '/company/' . $object_id
            );
        }            
        else if ($object_alias == 'person')
        {
            $persons    = new Person();
            $person     = $persons->GetById($object_id);
            if (empty($person)) return array();
            
            $person = $person['person'];
            
            if (!empty($person['company_id']))
            {
                $companies  = new Company();
                $company    = $companies->GetById($person['company_id']);
                
                if (!empty($company))
                {
                    $email_objects[] = array('alias' => 'company', 'id' => $person['company_id']);
                    
                    return array(
                        'Companies'                     => '/companies',
                        $company['company']['title']    => '/company/' . $company['company']['id'],
                        $person['full_name']            => '/person/' . $object_id
                    );
                }                    
            }
            else
            {
                return array(
                    'Persons'               => '/persons',
                    $person['full_name']    => '/person/' . $object_id
                );                    
            }                
        }        
    }

    /**
     * Возвращает параметры страницы
     * 
     * @param mixed $object_alias
     * @param mixed $object_id
     * @param mixed $title
     * @return mixed
     * 
     * @version 20120820, zharkov
     * @version 20120901, zharkov: добавил invoice, steelitem
     * @version 20121213, d10n: добавлено inddt
     * @version 20130215, d10n: добавлено oc
     */
    function GetPageParams($object_alias, $object_id, $title = '')
    {
        $doc_no     = '';
        $page_name  = '';
        $breadcrumb = array();
        
        if ($object_alias == 'supplierinvoice')
        {
            $invoices   = new SupplierInvoice();
            $invoice    = $invoices->GetById($object_id);
            
            $doc_no     = $invoice['supinvoice']['doc_no'];
            $page_name    = $invoice['supinvoice']['doc_no'] . (empty($title) ? '' : ' ' . $title);
            $breadcrumb   = array(
                'Supplier Invoices'     => '/supplierinvoices',
                $page_name  => ''
            );                
        }        
        else if ($object_alias == 'invoice')
        {
            $invoices   = new Invoice();
            $invoice    = $invoices->GetById($object_id);
            
            $doc_no       = $invoice['invoice']['doc_no'];
            $page_name    = $invoice['invoice']['doc_no'] . (empty($title) ? '' : ' ' . $title);
            $breadcrumb   = array(
                'Invoices'     => '/invoices',
                $page_name  => ''
            );                
        }
        else if ($object_alias == 'item')
        {
            $items  = new SteelItem();
            $item   = $items->GetById($object_id);
            $title  = $title == 'Dropbox' ? 'pictures' : $title;
            
            $doc_no         = $item['steelitem']['doc_no'];
            $page_name      = 'Item ' . (!empty($item['steelitem']['guid']) ? $item['steelitem']['doc_no'] : '# ' . $item['steelitem']['id']) . (empty($title) ? '' : ' ' . $title);
            $breadcrumb     = array(
                'Items'     => '/items',
                $page_name  => ''
            );                
        }        
        else if ($object_alias == 'biz')
        {
            $bizes  = new Biz();
            $biz    = $bizes->GetById($object_id);
            
            $doc_no         = $biz['biz']['doc_no_full'];
            $page_name      = $biz['biz']['doc_no'] . (empty($title) ? '' : ' ' . $title);
            $breadcrumb     = array(
                'BIZs'                      => '/biz',
//                $biz['biz']['doc_no_full']  => '/biz/' . $object_id,
                $page_name                  => ''
            );                
        }
        else if ($object_alias == 'company')
        {
            $companies  = new Company();
            $company    = $companies->GetById($object_id);
            
            $doc_no         = $company['company']['doc_no'];
            $page_name      = $company['company']['doc_no'] . (empty($title) ? '' : ' ' . $title);
            $breadcrumb     = array(
                'Companies'                     => '/companies',
                $company['company']['doc_no']   => '/company/' . $object_id,
                $page_name                      => ''
            );                
        }
        else if ($object_alias == 'person')
        {
            $persons    = new Person();
            $person     = $persons->GetById($object_id);

            $doc_no         = $person['person']['doc_no'];
            $page_name      = $doc_no . (empty($title) ? '' : ' ' . $title);
            $breadcrumb     = array(
                'Persons'   => '/persons',
                $doc_no     => '/person/' . $object_id,
                $page_name  => ''
            );
        }            
        else if ($object_alias == 'order')
        {
            $orders    = new Order();
            $order     = $orders->GetById($object_id);
            
            $doc_no         = $order['order']['doc_no'];
            $page_name      = $order['order']['doc_no'] . (empty($title) ? '' : ' ' . $title);
            $breadcrumb     = array(
                'Orders'                    => '/orders',
                $order['order']['doc_no']   => '/order/' . $object_id,
                $page_name                  => ''
            );                
        }            
        else if ($object_alias == 'sc')
        {
            $scs    = new SC();
            $sc     = $scs->GetById($object_id);
            
            $doc_no         = $sc['sc']['doc_no'];
            $page_name      = $sc['sc']['doc_no'] . (empty($title) ? '' : ' ' . $title);
            $breadcrumb     = array(
                'SC'                => '/sc',
                $sc['sc']['doc_no'] => '/sc/' . $object_id,
                $page_name          => ''
            );                
        }            
        else if ($object_alias == 'qc')
        {
            $qcs    = new QC();
            $qc     = $qcs->GetById($object_id);
            
            $doc_no         = $qc['qc']['doc_no'];
            $page_name      = $qc['qc']['doc_no'] . (empty($title) ? '' : ' ' . $title);
            $breadcrumb     = array(
                'QC'                => '/qc',
                $qc['qc']['doc_no'] => '/qc/' . $object_id,
                $page_name          => ''
            );                
        }
        else if ($object_alias == 'email')
        {
            $modelEmail = new Email();
            $email      = $modelEmail->GetById($object_id);
            
            $doc_no         = $email['email']['doc_no'];
            $page_name      = $doc_no . (empty($title) ? '' : ' ' . $title);
            $breadcrumb     = array(
                'Email'                  => '/emails',
                $email['email']['subject'] => '/email/' . $object_id,
                $page_name          => ''
            );
        }
        else if ($object_alias == 'inddt')
        {
            $modelInDDT = new InDDT();
            $inddt      = $modelInDDT->GetById($object_id);
            $inddt      = $inddt['inddt'];
            
            $doc_no         = $inddt['doc_no'];
            $page_name      = $inddt['doc_no'] . (empty($title) ? '' : ' ' . $title);
            $breadcrumb     = array(
                'IN DDTs'           => '/inddt',
                $inddt['doc_no']    => '/inddt/' . $object_id,
                $page_name          => ''
            );
        } 
        else if ($object_alias == 'oc')
        {
            $modelOC    = new OC();
            $oc         = $modelOC->GetById($object_id);
            $oc         = $oc['oc'];
            
            $doc_no         = $oc['doc_no'];
            $page_name      = $oc['doc_no'] . (empty($title) ? '' : ' ' . $title);
            $breadcrumb     = array(
                'Original Certificates' => '/' . $object_alias,
                $oc['doc_no']           => '/' . $object_alias . '/' . $object_id,
                $page_name              => ''
            );
        } 
        
        return array(
            'page_name'     => $page_name,
            'breadcrumb'    => $breadcrumb,
            'stat'          => $this->GetStatistics($object_alias, $object_id),
            'doc_no'        => $doc_no
        );
    }
    
    /**
     * Возвращает статистику по объекту
     * 
     * @param mixed $object_alias
     * @param mixed $object_id
     * @return integer
     * 
     * @version 20120720, zharkov
     */
    function GetStatistics($object_alias, $object_id)
    {
        $attachments    = new Attachment();
        $emails         = new Email();
        $messages       = new Message();
        
        return array(
            'attachments'   => $attachments->GetCountByObject($object_alias, $object_id),
            'emails'        => $emails->GetCountByObject($object_alias, $object_id),
            'messages'      => $messages->GetCountByObject($object_alias, $object_id)
        );
    }
}

/**
 * Sort array by value desc
 * 
 * @param mixed $a
 * @param mixed $b
 * @return mixed
 */
function _cmp_value_desc($a, $b)
{
    $a = strtolower($a);
    $b = strtolower($b);
    if ($a == $b) {
        return 0;
    }
    return ($a > $b) ? +1 : -1;
}
