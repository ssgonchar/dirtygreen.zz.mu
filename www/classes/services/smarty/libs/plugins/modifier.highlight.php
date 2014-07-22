<?php

    function smarty_modifier_highlight($text, $keyword = '', $is_phrase = 0)
    {
	    if (is_array($text)) return $text;
        
        preg_match_all('#([a-z0-9_\-]+\.)*[a-z0-9_\-]+@([a-z0-9][a-z0-9\-]*[a-z0-9]\.)+[a-z]{2,4}#si', $text, $matches);
        if (!empty($matches))
        {
            foreach ($matches[0] as $match)
            {
                $text = preg_replace("#<" . $match . ">#si", "&lt;" . $match . "&gt;", $text);
            }
        }
        
        if (empty($keyword)) return $text;
        
        if ($is_phrase > 0)
        {
            $keywords[] = $keyword;
        }
        else
        {
            $keyword    = preg_replace("#[^0-9a-zA-Z-_ \.]#si", "", $keyword);
            $keywords   = explode(' ', $keyword);
        }

        foreach ($keywords as $keyword)
        {
            $keyword    = trim($keyword);
            $regex      = "#<a[^>]+(" . $keyword . ")[^>]*>#si";
            preg_match_all($regex, $text, $matches);
            
            if (!empty($matches))
            {
                foreach ($matches[0] as $key => $match)
                {
                    $ptext  = str_replace($matches[1][$key], '{$' . $key . '$}', $match);
                    $text   = str_replace($match, $ptext, $text);
                }            
            }

            $text = preg_replace("#(" . $keyword . ")#si", '<span class="highlight">$1</span>', $text);
            
            if (!empty($matches))
            {
                foreach ($matches[1] as $key => $match)
                {
                    $text   = str_replace('{$' . $key . '$}', $match, $text);
                }            
            }
        }

        return $text;
    }