<?php
class Parser
{
    
    /**
     * Get objects from text
     * 
     * @param mixed $content
     */
    static function GetObjects($content)
    {
        // found objects
        $objects = array();        

        // parse biz
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
            $modelBiz = new Biz();            
            foreach ($matches[1] as $key => $row)
            {
                $biz = explode('.', $row);
                if (!empty($biz))
                {
                    $number = intval($biz[0]);
                    $suffix = count($biz) > 1 ? intval($biz[1]) : 0;
                    
                    $result = $modelBiz->GetByNumber($number, $suffix);
                    if (isset($result))
                    {
                        $objects['biz' . $result['id']] = array('alias' => 'biz', 'id' => $result['id']);
                        
                        // parent biz
                        if ($result['suffix'] > 0)
                        {
                            $objects['biz' . $result['parent_id']] = array('alias' => 'biz', 'id' => $result['parent_id']);
                        }
                    }
                }
            }
        }
        
        // parse orders // парсит заказы
        $matches = array();
        preg_match_all('#inpo(\d+)#i', $content, $matches);
        
        if (!empty($matches))
        {
            $modelOrder = new Order();            
            foreach ($matches[1] as $key => $row)
            {
                $number = intval($row);
                $order  = $modelOrder->GetByNumber($number);
                
                if (isset($order))
                {
                    $objects['order' . $order['id']]    = array('alias' => 'order', 'id' => $order['id']);
                    $objects['biz' . $order['biz_id']]  = array('alias' => 'biz', 'id' => $order['biz_id']);                    
                }
            }
        }
        
        return $objects;
    }
    
    /**
     * Replace metatags with related content depending on modes specified:
     * BIZxxxx.xx                                   -> <a href="/biz/{biz.id}/blog">{biz.doc_no}</a>
     * <ref biz_id="{biz_id}">{text}</ref>          -> <a href="/biz/{biz.id}/blog">{biz.doc_no}</a>
     * INPOxxxx                                     -> <a href="/order/{order.id}">INPO{order.id}</a>
     * <ref message_id="{message_id}">{text}</ref>  -> <a href="javascript: void(0);" onclick="show_chat_message({message_id});">{text}</a>
     * <ref email_id="{email_id}">{text}</ref>      -> <a href="javascript: void(0);" onclick="show_email_message({email_id});">{text}</a>
     * <a ...>BIZxxxx.xx</a>                        -> leave as is
     * 
     * @param mixed $content
     * @param mixed $bizmode:
     * 1 - href="/biz/{biz.id}/blog", text={biz.doc_no}
     * 2 - href="/biz/{biz.id}/blog", text={biz.doc_no_full}
     * 3 - href="/biz/{biz.id}", text={biz.doc_no}
     * 4 - href="/biz/{biz.id}", text={biz.doc_no_full}
     * 5 - without <a>, text={biz.doc_no}
     * 6 - without <a>, text={biz.doc_no_full}
     * 
     * @return string
     */
    static function Decode($content, $bizmode = 1, $target = '_blank')
    {
        $content = str_replace('&lt;', '<', $content);
        $content = str_replace('&gt;', '>', $content);
        
        
        // 1. INPOxxxx                                     -> <a href="/order/{order.id}">INPO{order.id}</a>
        preg_match_all('#inpo(\d+)#si', $content, $matches);
        if (isset($matches) && isset($matches[0]) && !empty($matches[0]))
        {
            foreach ($matches[0] as $key => $order_ref)
            {
                $order_id   = $matches[1][$key];
                $order_link = '<a href="/order/' . $order_id . '"' . (empty($target) ? '' : ' target="' . $target . '"') . '>INPO' . $order_id . '</a>';
                $content    = str_replace($order_ref, $order_link, $content);
            }
        }
        else
        {
            $matches = null;
        }


        // 2. <ref message_id="{message_id}">{text}</ref>  -> <a href="javascript: void(0);" onclick="show_chat_message({message_id});">{text}</a>
        preg_match_all('#<ref message_id="*(\d+)"*>(.*?)</ref>#si', $content, $matches);
        if (isset($matches) && isset($matches[0]) && !empty($matches[0]))
        {
            foreach ($matches[0] as $key => $message_ref)
            {
                $message_link   = '<a href="javascript: void(0);" onclick="show_chat_message(' . $matches[1][$key] . ');">' . $matches[2][$key] . '</a>';
                $content        = str_replace($message_ref, $message_link, $content);
            }
        }
        else
        {
            $matches = null;
        }

        
        // 3. <ref email_id="{email_id}">{text}</ref>      -> <a href="javascript: void(0);" onclick="show_email_message({email_id});">{text}</a>
        preg_match_all('#<ref email_id="*(\d+)"*>(.*?)</ref>#si', $content, $matches);        
        if (isset($matches) && isset($matches[0]) && !empty($matches[0]))
        {
            foreach ($matches[0] as $key => $email_ref)
            {
                $email_link = '<a href="javascript: void(0);" onclick="show_email_message(' . $matches[1][$key] . ');">' . $matches[2][$key] . '</a>';
                $content    = str_replace($email_ref, $email_link, $content);
            }
        }
        else
        {
            $matches = null;
        }

        
        // 4. Bizs within <a>, leave as are encode.
        preg_match_all('#<a[^>]*>[^<]*biz(\d{4}(\.\d{1,2})?)[^<]*</a>#i', $content, $biz_links);        
        $biz_links = isset($biz_links[0]) ? $biz_links[0] : null;
        
        if (isset($biz_links))
        {
            foreach ($biz_links as $biz_link_id => $biz_link_value)
            {
                $content = str_replace($biz_link_value, '{direct_biz_link_' . $biz_link_id . '}', $content);
            }
        }


        // 5. BIZxxxx.xx
        $modelBiz = new Biz();
        preg_match_all('#biz(\d{4}(\.\d{1,2})?)#i', $content, $matches);
                
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

        if (!empty($matches))
        {           
            foreach ($matches[1] as $key => $row)
            {
                $biz = explode('.', $row);
                if (!empty($biz))
                {
                    $number = intval($biz[0]);
                    $suffix = count($biz) > 1 ? intval($biz[1]) : 0;
                    
                    $biz = $modelBiz->GetByNumber($number, $suffix);
                    if (isset($biz))
                    {
                        $biz_link   = self::_get_biz_link($biz, $bizmode, $target);
                        $content    = str_replace($matches[0][$key], $biz_link, $content);
                    }                    
                }
            }
        }

        
        // 6. <ref biz_id="{biz_id}">{text}</ref>          -> <a href="/biz/{biz.id}/blog">{biz.doc_no}</a>
        preg_match_all('#<ref biz_id="*(\d+)"*>(.*?)</ref>#si', $content, $matches);        
        if (isset($matches) && isset($matches[0]) && !empty($matches[0]))
        {
            foreach ($matches[0] as $key => $biz_ref)
            {
                $biz        = $modelBiz->GetById($matches[1][$key]);
                $biz_link   = isset($biz) ? self::_get_biz_link($biz['biz'], $bizmode, $target) : '';
                
                $content    = str_replace($biz_ref, $biz_link, $content);
            }
        }
        else
        {
            $matches = null;
        }

        
        // N. Bizs within <a>, leave as are decode.
        if (isset($biz_links))
        {
            foreach ($biz_links as $biz_link_id => $biz_link_value)
            {
                $content = str_replace('{direct_biz_link_' . $biz_link_id . '}', $biz_link_value, $content);
            }
        }
        
        
        // replace duplicate <br>
        $content = preg_replace('#(?:\r?\n){2,}#', "<br/><br/>", $content);
        $content = preg_replace('#(?:<br[^>]*>\s*){3,}#im', "<br/><br/>", $content);
        
        return $content;
    }
    
    /**
     * Generate biz link
     *     
     * @param mixed $biz
     * @param mixed $bizmode
     * @param mixed $target
     */
    static private function _get_biz_link($biz, $bizmode = 1, $target = '_blank')
    {
        // 1 - href="/biz/{biz.id}/blog", text={biz.doc_no}
        if ($bizmode == 1)
        {
            return '<a href="/biz/' . $biz['id'] . '/blog"' . (empty($target) ? '' : ' target="' . $target . '"') . ' title="' . strtr($biz['doc_no_full'], array('"' => '\"')) . '" class="bizlink tooltip">' . $biz['doc_no'] . '</a>';
        }
        
        // 2 - href="/biz/{biz.id}/blog", text={biz.doc_no_full}
        if ($bizmode == 2)
        {
            return '<a href="/biz/' . $biz['id'] . '/blog"' . (empty($target) ? '' : ' target="' . $target . '"') . ' title="' . strtr($biz['doc_no_full'], array('"' => '\"')) . '" class="bizlink tooltip">' . $biz['doc_no_full'] . '</a>';
        }

        // 3 - href="/biz/{biz.id}", text={biz.doc_no}
        if ($bizmode == 3)
        {
            return '<a href="/biz/' . $biz['id'] . '"' . (empty($target) ? '' : ' target="' . $target . '"') . ' title="' . strtr($biz['doc_no_full'], array('"' => '\"')) . '" class="bizlink tooltip">' . $biz['doc_no'] . '</a>';
        }

        // 4 - href="/biz/{biz.id}", text={biz.doc_no_full}
        if ($bizmode == 4)
        {
            return '<a href="/biz/' . $biz['id'] . '"' . (empty($target) ? '' : ' target="' . $target . '"') . ' title="' . strtr($biz['doc_no_full'], array('"' => '\"')) . '" class="bizlink tooltip">' . $biz['doc_no_full'] . '</a>';
        }

        // 5 - without <a>, text={biz.doc_no}
        if ($bizmode == 5)
        {
            return $biz['doc_no'];
        }

        // 6 - without <a>, text={biz.doc_no_full}
        if ($bizmode == 6)
        {
            return $biz['doc_no_full'];
        }
        
        
        if ($bizmode == 7)
        {
            return '<a href="/biz/' . $biz['id'] . '/touchline"' . (empty($target) ? '' : ' target="' . $target . '"') . ' title="' . strtr($biz['doc_no_full'], array('"' => '\"')) . '" class="btn btn-success btn-xs bizlink tooltip">' . $biz['doc_no'] . '</a>';
        }
         
        
    }
        
    /**
     * Parse content 
     * 
     * @param mixed $content
     * 
     * @return array
     */
    private static function _parse($content)
    {
        $objects        = array();
        $replacements   = array();
        
        // bizes
        preg_match_all('#biz(\d{4}(\.\d{1,2})?)#i', $content, $matches);
                
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

        if (!empty($matches))
        {
            $modelBiz = new Biz();            
            foreach ($matches[1] as $key => $row)
            {
                $biz = explode('.', $row);
                if (!empty($biz))
                {
                    $number = intval($biz[0]);
                    $suffix = count($biz) > 1 ? intval($biz[1]) : 0;
                    
                    $biz = $modelBiz->GetByNumber($number, $suffix);
                    if (isset($biz))
                    {                        
                        $biz_id = $biz['id'];
                        $object = array(
                            'alias'     => 'biz', 
                            'id'        => $biz_id, 
                            'object'    => $biz
                        );
                        
                        $objects['biz' . $biz_id]           = $object;
                        $replacements[$matches[0][$key]]    = $object;
                    }                    
                }
            }
        }
        
        // orders
        $matches = array();
        preg_match_all('#inpo(\d+)#i', $content, $matches);
        
        if (!empty($matches))
        {
            $modelOrder = new Order();            
            foreach ($matches[1] as $key => $row)
            {
                $number = intval($row);
                $order  = $modelOrder->GetByNumber($number);
                
                if (isset($order))
                {
                    $order_id   = $order['id'];
                    $object     = array(
                        'alias'     => 'order', 
                        'id'        => $order_id, 
                        'object'    => $order
                    );
                    
                    $objects['order' . $order_id]       = $object;
                    $replacements[$matches[0][$key]]    = $object;
                }
            }
        }
        
        return array(
            'objects'       => $objects,
            'replacements'  => $replacements
        );
    }
        
    /**
     * Add biz objects to objects array
     * 
     * @param mixed $biz_id
     * @param mixed $objects
     */
    private static function deprecated_add_biz_objects($biz_id, $objects)
    {        
        $modelBiz   = new Biz();
        $biz        = $modelBiz->GetById($biz_id);

        if (isset($biz))
        {
            $biz        = $biz['biz'];
            $product_id = $biz['product_id'];

            if ($product_id > 0)
            {
                $objects['product-' . $product_id] = array(
                    'alias' => 'product', 
                    'id'    => $product_id
                );
            }

            // link to each company in biz
            $companies = $modelBiz->GetCompanies($biz['id']);
            foreach ($companies as $company)
            {
                if (isset($company['company']) && $company['company']['id'] > 0)
                {
                    $company = $company['company'];

                    $objects['company-' . $company['id']] = array(
                        'alias' => 'company', 
                        'id'    => $company['id']
                    );
                    
                    // link to company country
                    if ($company['country_id'] > 0)
                    {
                        $objects['country-' . $company['country_id']] = array(
                            'alias' => 'country', 
                            'id'    => $company['country_id']
                        );                            
                    }                    
                }
            }
            
            $parent_id = $biz['parent_id'];
            if ($parent_id > 0)
            {
                $objects['biz-' . $parent_id] = array(
                    'alias' => 'biz', 
                    'id'    => $parent_id
                );
                
                $objects = self::_add_biz_objects($parent_id, $objects);
            }
        }
        
        return $objects;
    }    
}
