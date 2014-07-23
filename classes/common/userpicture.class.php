<?php
    require_once APP_PATH . 'classes/common/translit.class.php';
    require_once APP_PATH . 'settings/pictures.php';

class UserPicture
{    
    
    /**
     * ��������� ������� � ���������� �� ��������� ������������� ������
     * 
     */
    public static function GetUrlPrefix()
    {
        //production server
        if ($_SERVER['HTTP_HOST'] == 'home.steelemotion.com' || $_SERVER['HTTP_HOST'] == 'www.home.steelemotion.com')
        {
            return 'http://a.steelemotion.com';
        }
        //development server
        else if ($_SERVER['HTTP_HOST'] == 'home.steelemotion.local' || $_SERVER['HTTP_HOST'] == 'www.home.steelemotion.local')
        {
            return 'http://a.steelemotion.local';
        }
        /*
        else if ($_SERVER['HTTP_HOST'] == 'mam.kvadrosoft.com' || $_SERVER['HTTP_HOST'] == 'www.mam.kvadrosoft.com')
        {
            return 'http://mamatt.kvadrosoft.com';
        }
        */
        return '';        
    }
    
    /**
     * ��������� ��� ��������
     * ������ �� �������� ����� ��� /picture/type/{secretcode}/{realname}.{extension}
     * @param mixed $type
     * @param mixed $size
     * @param mixed $secretcode
     * @param mixed $params
     */
    public static function GetHtml($type, $size, $secretcode, $params)
    {        
        global $__picture_settings;
        //debug('1671', $__picture_settings[$type][$size]);
        if (!isset($__picture_settings[$type])) $type = 'default'; //return '[no img: 1]';
        if (!isset($__picture_settings[$type][$size])) return '[no img: 2]';

        if (isset($secretcode) && !empty($secretcode))
        {
            $path       = self::GetUrlPrefix() . '/picture/' . $type . '/' . $secretcode . '/' . $size;            
            $filename   = '';
            
            if (isset($params['filename']) && !empty($params['filename']))
            {
                $filename   = urlencode(mb_substr(Translit::EncodeAndClear($params['filename']), 0, 80, 'utf-8'));
                $pos        = strrpos($filename, '.');

                // ������� ���������� ������ � ��������
                if ($pos !== false) 
                {
                    $ext = substr($filename, $pos);
                    
                    if (in_array($ext, array('.jpg', '.jpeg', '.gif', '.png', '.tiff', '.tif', '.bmp', '.pcx')))
                    {
                        $filename = substr($filename, 0, $pos);
                    }
                }
                
                $filename   .= '.' . (isset($__picture_settings[$type][$size]['ext']) ? $__picture_settings[$type][$size]['ext'] : 'jpg');
                $path       .= '/' . $filename;
            }

        }
        else
        {
            $path = '/nopicture/' . $type . '/' . $size . '.png';
        }
        
        

        $tag = '<img src="' . $path . '"';
        
        
        // �������������� ���������
        $width_set = false;
        foreach ($params as $param => $value)
        {
            if (!in_array($param, array('alt', 'title', 'class', 'style', 'id', 'name', 'onmouseover', 'onmouseout', 'onclick', 'width', 'height'))) continue;

            if ($param == 'title') $value = htmlspecialchars($value);
            if ($param == 'height') $value = $value . 'px';
            if ($param == 'width') 
            {
                $width_set  = true;
                $value      = $value . 'px';
            }
            
            $tag .= ' ' . $param . '="' . $value . '"';
        }
        
        /**
        * 20111106, zharkov: ������ ��������� ������, �� ����� ���-�� ��������� ������� � ����������� �������
        * 
        * // ���� ������ �� �������, ������� ������ �� ��������
        * if (!$width_set) $tag .= ' width="' . $__picture_settings[$type][$size]['width'] . 'px"';
        */
        
        $tag .= '>';
        
        
        // ������ ��� PrettyPhoto
        //if (isset($params['pretty']) && isset($params['pretty_id']))
        if (isset($params['pretty_id']))
        {
            $path_to_large_picture = rtrim(str_replace('/' . $size . '/', '/l/', $path . '/'), '/');
            $tag = '<a href="' . $path_to_large_picture . '" rel="prettyPhoto' . (empty($params['pretty_id']) ? '' : '[' . $params['pretty_id'] . ']') . '">' . $tag . '</a>';
        }
        
        return $tag;
    }
    
    /**
     * ���������� ������� ��������
     * 
     * @param mixed $type
     * @param mixed $secretcode
     * @param mixed $size
     * @param mixed $filename
     */
    public static function GetData($type, $secretcode, $size, $filename)
    {
        global $__picture_settings;
        //debug('1671', $__picture_settings[$type][$size]);
        if (!isset($__picture_settings[$type])) return '[no img: 1]';
        if (!isset($__picture_settings[$type][$size])) return '[no img: 2]';

        $extension  = isset($__picture_settings[$type][$size]['ext']) ? $__picture_settings[$type][$size]['ext'] : 'jpg';
        $path       = self::GetPath($secretcode) . '/' . $size . '.' . $extension;

        return array('data' => file_get_contents($path), 'content_type' => 'image/' . $extension);
    }
    
    /**
     * ���������� ���� � �����
     * 
     * @param mixed $secretcode
     */
    public static function GetPath($secretcode, $attachment_path = ATTACHMENT_PATH)
    {
        return $attachment_path . substr($secretcode, 0, 2) . '/' . substr($secretcode, 2, 2) . '/' . substr($secretcode, 4);
    }
}
