<?php
require_once APP_PATH . 'classes/core/Image.class.php';
require_once APP_PATH . 'classes/models/attachment.class.php';
require_once APP_PATH . 'settings/pictures.php';

class Picture extends Attachment
{
    function Picture()
    {
        Attachment::Attachment();
        
        $this->type = 'image';
    }

    
    /**
     * Удаляет картинку
     * 
     * @param mixed $id
     * @return int
     */
    function Delete($picture_id)
    {
        $attachment = $this->GetById($picture_id);
        
        if (isset($attachment))
        {
            // если удаляется главная картинка, то главной становится последняя добавленная
            if (!empty($attachment['is_main']))
            {                
                $dataset = $this->GetList($attachment['object_alias'], $attachment['object_id']);
                
                if (!empty($dataset['data']))
                {
                    foreach ($dataset['data'] as $key => $row)
                    {
                        if ($row['attachment']['id'] != $attachment['id']) $this->SetAsMain($row['attachment']['id']);
                    }
                }
            }

            return parent::Delete($picture_id);
        }
        
        return null;
    }
    
    /**
     * Удаляет все картинки объекта
     * 
     * @param mixed $object_alias
     * @param mixed $object_id
     */
    function DeleteFromObject($object_alias, $object_id)
    {
        $dataset = $this->GetList($object_alias, $object_id);
        if (empty($dataset['data'])) return;
        
        foreach ($dataset['data'] as $key => $row) parent::Delete($row['attachment']['id']);
    }
    
    
    /**
     * Сохраняет картинку
     * 
     * @param mixed $id
     * @param mixed $object_alias
     * @param mixed $object_id
     * @param mixed $file_object
     * @param mixed $title
     * @param mixed $description
     * @param mixed $status
     * @return mixed
     */
    function Save($id, $object_alias, $object_id, $file_object, $title = '', $description = '', $status = MODERATE_STATUS_ACTIVE)
    {
        $attachment_id = parent::Save($id, $object_alias, $object_id, $file_object, $title, $description, $status);

        // после сохранения первой картинки ей присваивается статус главная
        if (isset($attachment_id))
        {
            $dataset = $this->GetList($object_alias, $object_id);
            
            if (isset($dataset['count']) && $dataset['count'] == 1)
            {
                $this->SetAsMain($attachment_id);
            }
        }
        
        return $attachment_id;
    }   
    
    
    /**
     * Обновляет атрибут is_main
     * 
     * @param mixed $attachment_id
     */
    function SetAsMain($attachment_id)
    {
        $result = $this->CallStoredProcedure('sp_attachment_set_as_main', array($attachment_id));
        $result = isset($result) && isset($result[0]) && isset($result[0][0]) ? $result[0][0] : null;
        
        if (isset($result))
        {
            Cache::ClearTag('attachment-' . $attachment_id);
            Cache::ClearTag('attachment-' . $result['prev_main_id']);
            Cache::ClearTag('attachments-type' . $result['type'] . '-alias' . $result['object_alias'] . '-id' . $result['object_id']);
            Cache::ClearTag('attachments-alias' . $result['object_alias'] . '-id' . $result['object_id']);
            
            Cache::ClearTag($result['object_alias'] . '-' . $result['object_id']);
        }
        
        return $result;
    }
    
    
    /**
     * Упрощает сохранение картинки с формы
     * 
     * @param mixed $object_alias
     * @param mixed $object_id
     * @param mixed $is_main
     */
    function SaveFromRequest($object_alias, $object_id, $is_main = 0)
    {
        // удаляет картинку
        if (isset($_REQUEST['form']['deletephoto']))
        {
            $attachment_id = Request::GetInteger('deletephoto', $_REQUEST['form']);
            
            if ($attachment_id > 0)
            {
                $this->Delete($attachment_id);
                return $attachment_id;
            }
        }
        
        // картинка не меняется
        $state = Request::GetInteger('photostate', $_REQUEST['form']);
        if (empty($state)) return null;
        
        // сохраняет картинку
        if (array_key_exists('photo', $_FILES))
        {
            $file_object = Request::GetFile($_FILES['photo']);

            if (!empty($file_object))
            {                
                $this->DeleteFromObject($object_alias, $object_id);
                $attachment_id = $this->Save(0, $object_alias, $object_id, $file_object);
                
                if (isset($attachment_id))
                {
                    // устанавливает картинку как основную   
                    if (!empty($is_main)) $this->SetAsMain($attachment_id);
                    
                    return $attachment_id;                    
                }
            }
        }        

        return null;        
    }
    
    /**
     * Сохраняет оригинальный файл, создает тумбнейлы
     * 
     * @param mixed $secret_name - секретное имя файла в бд
     * @param mixed $file_object - файловый объект
     */
    function _save_to_disc($attachment, $file_object)
    {
        // производит манипуляции с картинкой
        $image = new Image($file_object);

        // неправильный файловый объект        
        if (!is_array($file_object)) 
        {
            Log::AddLine(LOG_ERROR, 'Picture::_save_to_disc error : unknown file object!');
            return null;
        }
        
        // неправильный тип изображения
        if (!$image->IsAllowedPictureType())         
        {
            Log::AddLine(LOG_ERROR, 'Picture::_save_to_disc error : file type is not allowed!');
            return null;
        }

        
        // подключение настроек изображения
        global $__picture_settings;        
        
        // если нет настроек длявыбранного object_alias, то используются настройки по-умолчанию
        $settings_alias = isset($__picture_settings[$attachment['object_alias']]) ? $attachment['object_alias'] : 'default';

        // очистка предыдущих версий файлов
        $this->_delete_from_disc($attachment);

        // сохранение оригинала изображения
        $path = $this->_get_path($attachment['secret_name']);            

        if (!$image->SaveTo($path, 'o', true, true))
        {
            Log::AddLine(LOG_ERROR, 'Picture::_save_to_disc error : error saving original file to disc!');
            return null;            
        }

        // сохраняет файл под оригинальным именем
        $original_image = new Image($file_object);
        $original_image->SaveTo($path, $attachment['original_name']);

        // создание и сохранение набора превьюшек изображения
        foreach ($__picture_settings[$settings_alias] as $key => $value)
        {
            if (!empty($value))
            {   
                $w          = Request::GetInteger('width',      $value, 0);
                $h          = Request::GetInteger('height',     $value, 0);
                $maxside    = Request::GetInteger('maxside',    $value, 0);
                $crop       = Request::GetBoolean('crop',       $value, false);
                $watermark  = Request::GetBoolean('watermark',  $value, false);
                $ext        = Request::GetString ('ext',        $value, 'jpg');

                if ($w > 0 || $h > 0 || $maxside > 0)
                {
                    $image->CreateThumbnail($w, $h, $maxside, $crop, $path, $key . '.' . $ext, $watermark);
                }
            }
        }
        
        return $attachment['id'];
    }
    
    /**
     * Удаляет оригинальный файл и все его тумбнейлы
     * 
     * @param mixed $attachment
     */
    function _delete_from_disc($attachment)
    {
        $path = $this->_get_path($attachment['secret_name']);
        
        if (is_dir($path)) 
        {
            if ($handle = opendir($path)) 
            {
                while (false !== ($file = readdir($handle))) 
                { 
                    if ($file != "." && $file != "..") 
                    { 
                        unlink($path . '/' . $file);
                    } 
                }

                closedir($handle); 
                rmdir($path);
                
                return $attachment;
            }
        }

        return false;
    }
    
    /**
     * Обновляет заглавную картинку
     * 
     * @param mixed $object_alias
     * @param mixed $object_id
     */
    function UpdateMainPicture($object_alias, $object_id)
    {
        $list = $this->SelectList(array(
            'fields'    => array('id'),
            'where'     => array('conditions' => 'object_alias=? AND object_id=? AND is_main=1', 'arguments' => array($object_alias, $object_id))
        ));

        if (empty($list))
        {
            $list = $this->SelectList(array(
                'fields'    => array('id'),
                'where'     => array('conditions' => 'object_alias=? AND object_id=?', 'arguments' => array($object_alias, $object_id)),
                'order_by'  => 'created_at',
                'limit'     => 1
            ));
            
            if (!empty($list)) $this->SetAsMain($list[0]['id']);
        }
    }
    
    /**
     * Возвращает идентификаторы предыдущей и последующей картинки
     * 
     * @param mixed $picture_id
     */
    function GetPrevNext($picture_id)
    {
        $picture = $this->GetById($picture_id);
        if (empty($picture)) return null;
        
        $prev = $this->SelectList(array(
            'fields'    => array('id'),
            'where'     => array('conditions' => 'object_alias=? AND object_id=? AND id < ?', 'arguments' => array($picture['object_alias'], $picture['object_id'], $picture['id'])),
            'limit'     => 1,
            'order'     => 'id DESC'
        ));

        $next = $this->SelectList(array(
            'fields'    => array('id'),
            'where'     => array('conditions' => 'object_alias=? AND object_id=? AND id > ?', 'arguments' => array($picture['object_alias'], $picture['object_id'], $picture['id'])),
            'limit'     => 1
        ));
        
        $prev = $this->FillAttachmentInfo($prev, 'id');
        $prev = isset($prev[0]) && !empty($prev[0]['attachment']) ? $prev[0]['attachment'] : null;
        
        $next = $this->FillAttachmentInfo($next, 'id');
        $next = isset($next[0]) && !empty($next[0]['attachment']) ? $next[0]['attachment'] : null;
        
        return array('prev' => $prev, 'next' => $next);
    }
}
