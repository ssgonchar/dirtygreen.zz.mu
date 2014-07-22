<?php
    require_once(APP_PATH . 'classes/core/WaterMark.class.php');
/*
if (!function_exists('mime_content_type'))
{
    function mime_content_type($f)
    {
        return exec(trim('file -bi ' . escapeshellarg($f)));
    }
}

function mime_content_type1($f)
{
    if (defined("LOCALHOST"))
    {
        return mime_content_type($f);
    }

    return exec(trim('file -bi ' . escapeshellarg($f)));
}
*/

/**
 * Класс для управления запросами на отображение статических страниц
 *
 * @version 2008.10.05 digi
 * @version 20120506, zharkov: maxside
 */
class Image
{
    var $file;
    var $imagefill_color = 0xFFFFFF;


    /**
     * Конструктор
     *
     * @param mixed var $file массив описания файла из $_FILES
     * или строка - путь к файлу на диске
     */
    function Image($file = false)
    {
        if (is_array($file))
        {
            $this->file = $file;
        }
        else if(is_string($file))
        {
            if (file_exists($file))
            {
                list($width, $height, $type) = getimagesize($file);
                $this->file = array('tmp_name'      => $file, 
                                    'name'          => basename($file), 
                                    'type'          => image_type_to_mime_type($type), 
                                    'from_disc'     => true);
            }
            else
            {
                die('Image: File does not exist!');
            }
        }
    }

    function check_path($path)
    {
        if (is_dir($path) == false)
        {
            $oldmask    = umask(0);
            $res        = mkdir($path, 0777, true);
            umask($oldmask);

            if ($res == false)
            {
                return false;
            }
        }

        return $path;
    }

    public function IsAllowedPictureType($content_type = null)
    {
        $content_type = empty($content_type) ? $this->file['type'] : $content_type;

        return in_array($content_type, array('image/jpeg', 'image/pjpeg', 'image/gif', 'image/png', 'image/x-png', 'image/tiff', 'image/bmp'));
    }

    private static function get_extension_by_type($content_type)
    {
        switch($content_type)
        {
            case 'image/jpg':
            case 'image/jpeg':
            case 'image/pjpeg':
                return 'jpeg';

            case 'image/gif':
                return 'gif';

            case 'image/png':
            case 'image/x-png':
                return 'png';

            case 'image/tiff':
                return 'tiff';

            case 'image/bmp':
                return 'bmp';                
        }
    }

    /**
     * 
     * @param type $dest
     * @param type $name
     * @param type $leave_extension
     * @param type $leave_temp
     * @return null
     * 
     * @version
     * @version 20130227, d10n: откорректировано основное условие
     */
    function SaveTo($dest, $name = false, $leave_extension = false, $leave_temp = false)
    {
        if (is_uploaded_file($this->file['tmp_name'])
            || (is_array($this->file['tmp_name']) && array_key_exists('pFile', $this->file['tmp_name']))
            || !empty($this->file['from_url']) || !empty($this->file['from_disc'] ))
        {
            if (empty($name))
            {
                $name = $this->generate_file_name();
            }
            else
            {
                if ($leave_extension == true)
                {
                    $name = $name . '.' . Image::get_extension_by_type($this->file['type']);
                }
            }

            $path = $this->check_path($dest) . '/' . $name;

            if (is_dir($dest))
            {
                if (is_array($this->file['tmp_name']))
                {
                    if (array_key_exists('pFile', $this->file['tmp_name']))
                    {
                        $target = fopen($path, "w");
                        fseek($this->file['tmp_name']['pFile'], 0, SEEK_SET);
                        stream_copy_to_stream($this->file['tmp_name']['pFile'], $target);

                        $this->file['title']    = $this->file['name'];
                        $this->file['name']     = $name;
                        $this->file['tmp_name'] = $path;

                        $imagesize              = getimagesize($this->file['tmp_name']);
                        $this->file['width']    = $imagesize[0];
                        $this->file['height']   = $imagesize[1];

                        fclose($target);
                        return $this->file;
                    }
                }
                else if (copy($this->file['tmp_name'], $path))
                {
                    // если используется файл, который находится на диске, то его не удаляем
                    if (empty($this->file['from_disc']) && !$leave_temp)
                    {
                        unlink($this->file['tmp_name']);                    
                    }

                    $this->file['title']    = $this->file['name'];
                    $this->file['name']     = $name;
                    $this->file['tmp_name'] = $path;

                    $imagesize              = getimagesize($this->file['tmp_name']);
                    $this->file['width']    = $imagesize[0];
                    $this->file['height']   = $imagesize[1];

                    return $this->file;
                }
            }
        }
        
        return null;

        
/*  20120506, zharkov: before uploader        
        if (is_uploaded_file($this->file['tmp_name']) || !empty($this->file['from_url']) || !empty($this->file['from_disc']))
        {

            if (empty($name))
            {
                $name = $this->generate_file_name();
            }
            else
            {
                if ($leave_extension == true)
                {
                    $name = $name . '.' . Image::get_extension_by_type($this->file['type']);
                }
            }

            $path = $this->check_path($dest) . '/' . $name;

            if (is_dir($dest))
            {
                if (copy($this->file['tmp_name'], $path))
                {
                    // если используется файл, который находится на диске, то его не удаляем
                    if (empty($this->file['from_disc']))
                    {
                        unlink($this->file['tmp_name']);                    
                    }

                    $this->file['title']    = $this->file['name'];
                    $this->file['name']     = $name;
                    $this->file['tmp_name'] = $path;

                    $imagesize              = getimagesize($this->file['tmp_name']);
                    $this->file['width']    = $imagesize[0];
                    $this->file['height']   = $imagesize[1];

                    return $this->file;
                }
            }
        }
        
        return null;
*/        
    }

    function generate_file_name()
    {
        $name = '';

        for ($i = 0; $i < 20; $i++)
        {
            $char = rand(0, 35);
            $name .= $char < 10 ? chr(48 + $char) : chr(87 + $char);
        }

        return $name . '.' . Image::GetExtension();
    }

    /**
     * @version 20120506, zharkov: maxside
     * 
     * @param mixed $w
     * @param mixed $h
     * @param mixed $maxside
     * @param mixed $crop
     * @param mixed $add_watermark
     */
    function Resize($w, $h, $maxside, $crop, $add_watermark = false)
    {
        $this->_image_resize($this->file['tmp_name'], $this->file['tmp_name'], $w, $h, $maxside, $crop, $add_watermark);
    }

    /**
     * @version 20120506, zharkov: maxside
     * 
     * @param mixed $w
     * @param mixed $h
     * @param mixed $maxside
     * @param mixed $crop
     * @param mixed $dest_folder
     * @param string $new_file_name
     * @param mixed $add_watermark
     */
    function CreateThumbnail($w, $h, $maxside, $crop, $dest_folder, $new_file_name = null, $add_watermark = false)
    {
        if ($new_file_name == null)
        {
            $new_file_name = basename($this->file['name'], '.' . Image::get_extension_by_type($this->file['type']));
            $new_file_name .= '_' . $w . '.' . $this->get_extension_by_type('image/gif');
        }

        return $this->_createThumbnail($w, $h, $maxside, $crop, $dest_folder, $new_file_name, $add_watermark);
    }

    /**
     * @version 20120506, zharkov: maxside
     * 
     * @param mixed $w
     * @param mixed $h
     * @param mixed $maxside
     * @param mixed $crop
     * @param mixed $dest_folder
     * @param mixed $new_file_name
     * @param mixed $add_watermark
     */
    function _createThumbnail($w, $h, $maxside, $crop, $dest_folder, $new_file_name, $add_watermark)
    {
        if (substr($dest_folder, -1) == '/')
        {
            $new_file_name = $dest_folder . $new_file_name;
        }
        else
        {
            $new_file_name = $dest_folder . '/' . $new_file_name;       
        }

        $this->_image_resize($this->file['tmp_name'], $new_file_name, $w, $h, $maxside, $crop, $add_watermark);
    }


    /**
     * @version 2008.10.05 digi, убрал перевод в 256-цветную палитру для JPEG
     * @version 20120506, zharkov: maxside
     * 
     * @param mixed $src            - имя исходного файла
     * @param mixed $dest           - имя генерируемого файла
     * @param mixed $dest_width     - ширина генерируемого изображения
     * @param mixed $dest_height    - высота генерируемого изображения
     * @param mixed $maxside        - максимальный размер изображения по любой стороне
     * @param mixed $crop           - если true, то вырезает кусок из изображения, если false, то обжимает
     * @param mixed $add_watermark  - добавляет водяной знак
     * @param mixed $rgb            - цвет фона, по умолчанию - белый
     * @param mixed $quality        - качество генерируемого JPEG, по умолчанию - максимальное (100)
     */
    function _image_resize($src, $dest, $dest_width, $dest_height, $maxside, $crop, $add_watermark = false, $rgb = 0xFFFFFF, $quality = 100)
    {
        if (!file_exists($src)) return false;

        $size = getimagesize($src);

        if ($size === false) return false;

        $src_width  = $size[0];
        $src_height = $size[1];

        $format     = strtolower(substr($size['mime'], strpos($size['mime'], '/')+1));
        $icfunc     = "imagecreatefrom" . $format;

        if (!function_exists($icfunc)) return false;

        $src_type   = $this->_get_path_extension($src);
        $dest_type  = $this->_get_path_extension($dest);

        $src_dx     = 0;
        $src_dy     = 0;

        $dest_dx    = 0;
        $dest_dy    = 0;
        
        $isrc   = $icfunc($src);           
        
        /**
        * 20120506, zharkov: maxside
        */
        if ($dest_height == 0 && $dest_width == 0 && $maxside > 0)
        {
            if ($src_width == $src_height)
            {
                $dest_width     = $maxside;
                $dest_height    = $maxside;
            }
            else if ($src_width > $src_height)
            {
                $dest_width = $maxside;                
            }
            else
            {
                $dest_height = $maxside;                
            }
        }
        
        // размеры холста на который будет накладываться уменшеное изображение, может быть больше уменьшеного изображения, появятся поля
        $pattern_width  = $dest_width;
        $pattern_height = $dest_height;        
        
        if ($dest_height > 0 && $dest_width > 0)
        {
            if ($src_height == $dest_height && $src_width == $dest_width && $src_type == $dest_type)
            {
                copy($src, $dest);
                return true;
            }
            else
            {
                if ($dest_width > $src_width && $dest_height > $src_height)
                {
                    $dest_dx        = ($dest_width - $src_width) / 2;
                    $dest_dy        = ($dest_height - $src_height) / 2;
                    $dest_width     = $src_width;
                    $dest_height    = $src_height;
                }
                else
                {                    
                    if ($crop)
                    {
                        // из исходного изображения вырезается кусок, точно соответствующий размерам требуемого изображения, без полей
                        $x_ratio = $src_width / $dest_width;
                        $y_ratio = $src_height / $dest_height;
                        
                        if ($x_ratio > $y_ratio)
                        {
                            $src_dx     = ($src_width - $dest_width * $y_ratio) / 2;
                            $src_width  = $dest_width * $y_ratio;
                        }
                        else if ($x_ratio < $y_ratio)
                        {
                            $src_dy     = ($src_height - $dest_height * $x_ratio) / 2;
                            $src_height = $dest_height * $x_ratio;
                        }
                    }
                    else
                    {                        
                        /**
                         * 20111106, zharkov: новая версия если задано                                     
                         * 'width' => 220, 'height' => 220, 'crop' => false,
                         * то изображение будет обрезаться без полей по большей стороне
                         */
                        if ($src_width > $src_height)
                        {
                            return $this->_image_resize($src, $dest, $dest_width, 0, $crop, $add_watermark, $rgb, $quality);
                        }
                        else
                        {
                            return $this->_image_resize($src, $dest, 0, $dest_height, $crop, $add_watermark, $rgb, $quality);
                        }
                        
                        /*  20111106, zharkov: старая версия, делает поля
                        // исходное изображение масштабируется и обрезается по размеру наибольшей стороны, в результате могут появиться поля
                        $src_ratio      = $src_width / $src_height;
                        $p_dest_width   = $dest_width;
                        $p_dest_height  = $dest_height;

                        if ($dest_width / $dest_height > $src_ratio) 
                        {
                            $dest_width = $dest_height * $src_ratio;
                        } 
                        else 
                        {
                            $dest_height = $dest_width / $src_ratio;
                        }
                        
                        $dest_dx = ($p_dest_width - $dest_width) / 2;
                        $dest_dy = ($p_dest_height - $dest_height) / 2;
                        */
                    }                
                }
            }
        }        
        // не указана высота
        else if ($dest_height == 0)
        {            
            if ($src_width == $dest_width && $src_type == $dest_type)
            {
                copy($src, $dest);
                return true;
            }
            else
            {                
                if ($src_width >= $dest_width)
                {
                    // уменьшаем высоту под требуемую ширину
                    $x_ratio        = $src_width / $dest_width;
                    $dest_height    = $src_height / $x_ratio;
                    $pattern_height = $dest_height;
                }
                else
                {
                    // т.к. исходная ширина меньше чем требуемая, появится смещение
                    $dest_dx = ($dest_width - $src_width) / 2;
                    
                    // вырезаемый кусок равен исходному изображению
                    $dest_width     = $src_width;
                    $dest_height    = $src_height;
                    
                    // высота получаемого изображения будет равна высоте исходного изображения
                    $pattern_height = $src_height;
                }
            }
        }
        // не указана ширина
        else if ($dest_width == 0)
        {
            
            
            if ($src_height == $dest_height && $src_type == $dest_type)
            {
                copy($src, $dest);
                return true;
            }
            else
            {
                if ($src_height >= $dest_height)
                {
                    // уменьшаем ширину под требуемую высоту
                    $y_ratio        = $src_height / $dest_height;
                    $dest_width     = $src_width / $y_ratio;
                    $pattern_width  = $dest_width;
                }
                else
                {
                    // т.к. исходная высота меньше чем требуемая, появится смещение
                    $dest_dy = ($dest_height - $src_height) / 2;
                    
                    // вырезаемый кусок равен исходному изображению
                    $dest_width     = $src_width;
                    $dest_height    = $src_height;
                    
                    // ширина получаемого изображения будет равна ширине исходного изображения
                    $pattern_width = $src_width;
                }
            }
        }

        // 20130614, zharkov: fix unknown error from email grabber
        if ($pattern_width < 1 || $pattern_height < 1) return false;        

		try
		{
			$idest  = imagecreatetruecolor($pattern_width, $pattern_height);
		}
		catch (Exception $objectException)
		{
			$exception_message  = $objectException->getMessage() . '<br><br>$pattern_width = ' . $pattern_width . ' $pattern_height = ' . $pattern_height;
			Log::AddLine(LOG_EMAIL_GRABBER, $exception_message);
			
			die();
		}

        //$idest  = imagecreatetruecolor($dest_width, $dest_height);        
        imagefill($idest, 0, 0, $this->imagefill_color);

        $col = ImageColorAllocate($idest, 255, 255, 255);

        imagecolortransparent($idest, $col);
        
        imagecopyresampled($idest, $isrc, $dest_dx, $dest_dy, $src_dx, $src_dy, $dest_width, $dest_height, $src_width, $src_height);

        if ($dest_type != 'jpg') imagetruecolortopalette($idest, false, 256);
        
        imageinterlace($idest);


        /*  Start Water Mark  */
        
        // забрать код у дениса        
        
        /*  End Water Mark  */
        
        
        /*  VERY CLUDGE */

        if ($dest_type == 'jpg')
        {
            imagejpeg($idest, $dest, $quality);   // $quality
        }
        else if ($dest_type == 'png')
        {
            imagepng($idest, $dest, 9); //$quality);
        }
        else
        {
            imagegif($idest, $dest, $quality);
        }

        imagedestroy($isrc);
        imagedestroy($idest);

        return true;
    }

    function _get_path_extension($image_path)
    {
        $path_parts = pathinfo($image_path);
        $ext        = strtolower($path_parts['extension']);

        if (in_array($ext, array('jpeg', 'pjpeg')))
        {
            $ext = 'jpg';
        }

        return $ext;
    }

    public static function ParseUrl($url)
    {
        $parsed_url = parse_url($url);

        if (!array_key_exists('scheme', $parsed_url))
            $parsed_url['scheme'] = 'http';
        $parsed_url['scheme'] . '://';

        if (!array_key_exists('host', $parsed_url))
            $parsed_url['host'] = '';

        if (!array_key_exists('port', $parsed_url))
            $parsed_url['port'] = '80';

        if (!array_key_exists('path', $parsed_url))
            $parsed_url['path'] = '';

        if (!array_key_exists('query', $parsed_url))
            $parsed_url['query'] = '';

        $parsed_url['pathquery'] = $parsed_url['path'] . ($parsed_url['query'] != '' ? '?' . $parsed_url['query'] : '');

        return $parsed_url;
    }

    public static function OpenRemoteFile($url)
    {
        $parsed_url = Image::ParseUrl($url);

        $fp = fsockopen($parsed_url['host'], $parsed_url['port'], $errno, $errstr, 30);
        if (!$fp)
        {
            trigger_error("$errstr ($errno)", E_USER_NOTICE);
            return null;
        }

        return $fp;
    }

    public static function GetRemoteFileHeaders($url)
    {
        if (!($fp = Image::OpenRemoteFile($url)))
        {
            return '';
        }
        else
        {
            $parsed_url = Image::ParseUrl($url);

            $request = "HEAD {$parsed_url['path']} HTTP/1.1\r\n";
            $request .= "Host: {$parsed_url['host']}\r\n";
            $request .= "User_Agent: Mozilla/1.2\r\n";
            $request .= "Accept: text/html, text/plain, image/*\r\n";
            $request .= "Connection: Close\r\n\r\n";
            fwrite($fp, $request);

            $headers = '';
            while (!feof($fp))
            {
                $headers .= fgets($fp, 2048);
            }

            fclose($fp);

            return $headers;
        }
    }

    public static function GetRemoteFileData($url)
    {
        if (!($fp = Image::OpenRemoteFile($url)))
        {
            return null;
        }
        else
        {
            $parsed_url = Image::ParseUrl($url);

            $request = "GET {$parsed_url['pathquery']} HTTP/1.1\r\n";
            $request .= "Host: {$parsed_url['host']}\r\n";
            $request .= "User_Agent: Mozilla/1.2\r\n";
            $request .= "Accept: text/html, text/plain, image/*\r\n";
            $request .= "Connection: Close\r\n\r\n";

            Log::AddLine( LOG_CUSTOM, "PICTURE::GetRemoteFileData(), " . $request);

            fwrite($fp, $request);

            while (trim(fgets($fp, 2048)));

            $data = '';
            while(!feof($fp))
            {
                $tmp = fread($fp, 2048);
                $data .= $tmp;
            }

            Log::AddLine( LOG_CUSTOM, "PICTURE::GetRemoteFileData() end of data");
            fclose($fp);

            return $data;
        }
    }

    function GetRemoteImage($url)
    {

        Log::AddLine( LOG_CUSTOM, "PICTURE:: url=" . $url);
        $headers = Image::GetRemoteFileHeaders($url);
        Log::AddLine( LOG_CUSTOM, "PICTURE:: header=" . $headers );

        if (preg_match('/Content-Type: ([a-z\-\/]+)/', $headers, $matches))
            $content_type = $matches[1];
        else
            $content_type = '';

        Log::AddLine( LOG_CUSTOM, "PICTURE:: type=" . $content_type );

        if (!$this->IsAllowedPictureType($content_type))
        {
            Log::AddLine( LOG_CUSTOM, "PICTURE:: type not allowed!" );
            return null;
        }
        //if (ereg('Content-Length: ([0-9]+)', $headers, $matches))
        //  $filesize = $matches[1];
        //else
        //  $filesize = 0;

        $data = Image::GetRemoteFileData($url);

        if ($data)
        {
            $filename = rand(1, 100000) . 'p' . rand(1, 100000) . '.' . Image::get_extension_by_type($content_type);
            $fp = fopen(APP_TEMP . $filename, 'w');
            fwrite($fp, $data);
            fclose($fp);

            $this->file['name']     = $filename;
            $this->file['type']     = $content_type;
            $this->file['size']     = filesize(APP_TEMP . $filename);
            $this->file['tmp_name'] = APP_TEMP . $filename;
            $this->file['from_url'] = true;

            $result = array();
            $result['name'] = $filename;
            $result['type'] = $content_type;
            $result['size'] = filesize(APP_TEMP . $filename);
            $result['tmp_name'] = APP_TEMP . $filename;

            return $result;
        }
        else
        {
            return null;
        }
    }
    
    // 20101227 added by d10n : setup imagefill_color
    public function set_imagefill_color($color = 0xFFFFFF)
    {
        $this->imagefill_color = $color;
    }

    // 20101227 added by d10n : getting imagefill_color
    public function get_imagefill_color()
    {
        return $this->imagefill_color;
    }    
}