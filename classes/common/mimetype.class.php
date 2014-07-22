<?php
class MimeType
{

    static $list = array(
                            'application/octet-stream'  => array('bin'),
                            'application/oda'           => array('oda'),
                            'application/pdf'           => array('pdf'),
                            'application/postscript'    => array('ai', 'eps', 'ps'),
                            'application/rtf'           => array('rtf'),
    
                            'application/x-bcpio'       => array('bcpio'),
                            'application/x-cpio'        => array('cpio'),
                            'application/x-csh'         => array('csh'),
                            'application/x-dvi'         => array('x-dvi'),
                            'application/x-gtar'        => array('x-gtar'),
                            'application/x-hdf'         => array('x-hdf'),
                            'application/x-latex'       => array('x-latex'),
                            'application/x-mif'         => array('x-mif'),
                            'application/x-netcdf'      => array('nc', 'cdf'),
                            'application/x-sh'          => array('sh'),
                            'application/x-shar'        => array('shar'),
                            'application/x-sv4cpio'     => array('sv4cpio'),
                            'application/x-sv4crc'      => array('sv4crc'),
                            'application/x-shockwave-flash' => array('swf'),
                            'application/x-tar'         => array('tar'),
                            'application/x-tcl'         => array('tcl'),
                            'application/x-tex'         => array('tex'),
                            'application/x-texinfo'     => array('texinfo', 'texi'),
                            'application/x-troff-man'   => array('man'),
                            'application/x-troff-me'    => array('me'),
                            'application/x-troff-ms'    => array('ms'),
                            'application/x-troff'       => array('t', 'tr', 'roff'),
                            'application/x-ustar'       => array('ustar'),
                            'application/x-wais-source' => array('src'),
                            
                            'application/zip'           => array('zip'),
                            'application/basic'         => array('au', 'snd'),
                            'application/x-aiff'        => array('aif', 'aiff', 'aifc'),
                            'application/x-wav'         => array('wav'),
                            
                            
                            'image/gif'                 => array('gif'),
                            'image/ief'                 => array('ief'),
                            'image/jpeg'                => array('jpeg', 'jpg', 'jpe'),
                            'image/png'                 => array('png'),
                            'image/tiff'                => array('tiff', 'tif'),
                            
                            'image/x-cmu-raster'        => array('ras'),
                            'image/x-portable-anymap'   => array('rpnm'),
                            'image/x-portable-bitmap'   => array('pbm'),
                            'image/x-portable-graymap'  => array('pgm'),
                            'image/x-portable-pixmap'   => array('ppm'),
                            'image/x-rgb'               => array('rgb'),
                            'image/x-xbitmap'           => array('xbm'),
                            'image/x-xpixrnap'          => array('xpm'),
                            'image/x-xwindowdump'       => array('xwd'),
                            
                            
                            'text/html'                 => array('html', 'htm'),
                            'text/plain'                => array('txt'),
                            'text/richtext'             => array('rtx'),
                            'text/tab-separated-values' => array('tsv'),
                            'application/xml'           => array('xml'),
                            'text/x-setext'             => array('etx'),
                            
                            
                            'video/mpeg'                => array('mpeg', 'mpg', 'mpe'),
                            'video/quicktime'           => array('qt', 'mov'),
                            'video/x-msvideo'           => array('avi'),
                            'video/x-sgi-movie'         => array('movie'),
                        );
                        
    /**
     * Возвращает mimetype по пути файла
     * 
     * @param mixed $path
     */
    static function GetMimeTypeByPath($path)
    {
        $parts = pathinfo($path);
        return self::GetMimeTypeByExtension($parts['extension']);
    }
    
    /**
     * Возвращает mimetype по расширению
     * 
     * @param mixed $extension
     */
    static function GetMimeTypeByExtension($extension)
    {
        $list = self::GetMimeArray();

        if (empty($list))
        {
            foreach (self::$list as $key => $extensions)
            {
                if (in_array($extension, $extensions)) return $key;
            }
        }
        else
        {
            if (isset($list[$extension])) return $list[$extension];
        }
        
        
        return null;
    }
    
    /**
     * Проверяет являеться ли путь картинкой или флеш
     * 
     * @param mixed $path
     */
    static function IsPathPicture($path)
    {
        return self::_check_path_by_extension($path, array('bmp', 'gif', 'jpg', 'jpeg', 'png', 'tif', 'tiff'));
    }
    
    /**
     * Проверяет является ли путь флешкой
     * 
     * @param mixed $path
     */
    static function IsPathFlash($path)
    {
        return self::_check_path_by_extension($path, array('swf'));
    }
    
    /**
     * Проверяет соответствует ли путь заданным расширениям
     * 
     * @param mixed $path
     * @param mixed $extensions
     * @return bool
     */
    static function _check_path_by_extension($path, $extensions)
    {
        if (empty($path)) return false;        
        
        extract(pathinfo($path));                
        return in_array($extension, $extensions);
    }
    
    static function GetMimeArray($url = '')
    {
        return array();
        $hash   = 'mime-types-array';
        $rowset = Cache::GetData($hash);

        if (!isset($rowset) || !isset($rowset['data']) || isset($rowset['outdated']))
        {
            $url    = empty($url) ? 'http://svn.apache.org/repos/asf/httpd/httpd/trunk/docs/conf/mime.types' : $url;
            $s      = array();

            foreach(@explode("\n", @file_get_contents($url)) as $x)
            {
                if(isset($x[0]) && $x[0] !== '#' && preg_match_all('#([^\s]+)#', $x, $out) && isset($out[1]) && ($c = count($out[1])) > 1)
                {
                    for($i = 1; $i < $c; $i++)
                    {
                        //  $s[] = '&nbsp;&nbsp;&nbsp;\'' . $out[1][$i] . '\' => \'' . $out[1][0] . '\'';
                        $s[$out[1][$i]] = $out[1][0];
                    }
                }                
            }
            
            Cache::SetData($hash, $s, null, CACHE_LIFETIME_LOCK);            
            $rowset = array('data' => $s);
        }
        
        return isset($rowset['data']) ? $rowset['data'] : array();
    }    
}
