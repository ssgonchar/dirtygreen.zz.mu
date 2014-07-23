<?php
require_once APP_PATH . 'classes/common/mimetype.class.php'; 
require_once APP_PATH . 'classes/common/translit.class.php';
require_once APP_PATH . 'classes/common/userpicture.class.php';

class Attachment extends Model
{
    /**
     * Тип вложения
     * 
     * @var mixed
     */
    var $type = 'file';
    
    function Attachment()
    {
        Model::Model('attachments');      
    }
    
    /**
     * Удаляет атачмент из объекта
     * 
     * @param mixed $attachment_id
     * @param mixed $object_alias
     * @param mixed $object_id
     * 
     * @version 20120805, zharkov
     */
    function RemoveFromObject($attachment_id, $object_alias, $object_id)
    {
        $result = $this->CallStoredProcedure('sp_attachment_remove_from_object', array($this->user_id, $attachment_id, $object_alias, $object_id));
        
        Cache::ClearTag('attachment-' . $attachment_id);
        Cache::ClearTag('attachments-alias' . $object_alias . '-id' . $object_id);
        Cache::ClearTag($object_alias . '-' . $object_id . '-blog');
    }

    /**
     * Возвращает список загруженных файлов из сессии
     * 
     * @param mixed $session_key
     */
    function SetUploadedIds($session_key, $attachment_ids)
    {
        $session_key    = 'attachments-' . $session_key;
        $ids            = isset($_SESSION[$session_key]) && is_array($_SESSION[$session_key]) ? $_SESSION[$session_key] : array();

        foreach ($attachment_ids as $row)
        {
            if (!array_key_exists($row['attachment_id'], $ids))
            {
                $ids[$row['attachment_id']] = true;
            }
        }

        $_SESSION[$session_key] = $ids;
    }

    /**
     * Возвращает список загруженных файлов из сессии
     * 
     * @param mixed $session_key
     */
    function GetUploadedIds($session_key, $attachment_ids = array())
    {
        $session_key = 'attachments-' . $session_key;

        if (isset($_SESSION[$session_key]) && is_array($_SESSION[$session_key]))
        {
            foreach ($_SESSION[$session_key] as $attachment_id => $state)
            {                
                $not_exists = true;                    
                foreach ($attachment_ids as $row)
                {
                    if ($row['attachment_id'] == $attachment_id)
                    {
                        $not_exists = false;
                        break;
                    }
                }
                
                if ($not_exists) $attachment_ids[] = array('attachment_id' => $attachment_id);
            }
        }

        return $attachment_ids;
    }
    
    /**
     * Связывает загруженные через аплоадер атачменты с документом
     * 
     * @param mixed $session_key
     * @param mixed $document_alias
     * @param mixed $document_id
     */
    function AssignUploaded($session_key, $document_alias, $document_id)
    {
        // текущий окумент, в котором обрабатывается аттачмент, костыль, потому что можно было передавать отдельно документ и id
        $current_document   = explode('-', $session_key);
        $session_key        = 'attachments-' . $session_key;

        if (isset($_SESSION[$session_key]) && is_array($_SESSION[$session_key]))
        {
            foreach ($_SESSION[$session_key] as $attachment_id => $state)
            {
                $this->AssignToObject($attachment_id, $current_document[0], $current_document[1], $document_alias, $document_id);
            }
            
            unset($_SESSION[$session_key]);
        }
    }
    
    /**
     * Привязывает атачмент к объекту. 
     * Один атачмент может быть привязан к нескольким объектам.
     * 
     * @param mixed $attachment_id
     * @param mixed $object_alias
     * @param mixed $object_id
     * 
     * @version 20120721, zharkov
     */
    function LinkToObject($attachment_id, $object_alias, $object_id)
    {
        $result = $this->CallStoredProcedure('sp_attachment_link_to_object', array($this->user_id, $attachment_id, $object_alias, $object_id));
        
        Cache::ClearTag('attachment-' . $attachment_id);
        Cache::ClearTag('attachments-alias' . $object_alias . '-id' . $object_id);
    }

    /**
     * Привязывает атачмент к объекту, когда это не делается автоматически например при использовании аплоадера
     * причем атачмент физически переносится в новый объект если совпадают id и предыдущий документ
     * в противном случает делается просто ссылка на этот атачмент для этого объекта
     * 
     * @param mixed $attachment_id
     * @param mixed $object_alias
     * @param mixed $object_id
     * @param mixed $new_object_alias
     * @param mixed $new_object_id
     */
    function AssignToObject($attachment_id, $object_alias, $object_id, $new_object_alias, $new_object_id)
    {
        $this->UpdateList(array(
            'values' => array(
                'object_alias'  => $new_object_alias,
                'object_id'     => $new_object_id
            ),
            'where' => array(
                'conditions'    => 'id = ? AND object_alias = ? AND object_id = ?',
                'arguments'     => array($attachment_id, $object_alias, $object_id)
            )
        ));
        
        $this->LinkToObject($attachment_id, $new_object_alias, $new_object_id);
        
        Cache::ClearTag('attachment-' . $attachment_id);
        Cache::ClearTag('attachments-alias' . $object_alias . '-id' . $object_id);
        Cache::ClearTag($object_alias . '-' . $object_id . '-blog');
    }
 
    /**
     * Возвращает список атачментов для объекта по имени
     * 
     * @param mixed $object_alias
     * @param mixed $object_id
     * @param mixed $file_name
     * @return array
     */
    function GetByObjectAndName($object_alias, $object_id, $file_name)
    {
        return $this->SelectList(array(
            'fields'    => array('id AS attachment_id'),
            'where'     => array(
                                    'conditions'    => 'object_alias = ? AND object_id = ? AND original_name = ? ', 
                                    'arguments'     => array($object_alias, $object_id, $file_name)
                                    )
        ));        
    }    
    
    /**
     * Возвращает количество атачментов для объекта
     * 
     * @param mixed $object_alias
     * @param mixed $object_id
     */
    function GetCountByObject($object_alias, $object_id)
    {
        $hash       = 'attachments-alias' . $object_alias . '-id' . $object_id . '-count';
        $cache_tags = array('attachments', 'attachments-alias' . $object_alias . '-id' . $object_id);

        $rowset = $this->_get_cached_data($hash, 'sp_attachment_get_count_by_object', array($this->user_id, $this->user_role, $object_alias, $object_id), $cache_tags);
        return isset($rowset) && isset($rowset[0]) && isset($rowset[0][0]) && isset($rowset[0][0]['count']) ? $rowset[0][0]['count'] : 0;
    }

    /**
     * Добавляет массив аттачментов к объекту
     * 
     * @param mixed $rowset
     * @param mixed $object_alias
     * @param mixed $id_fieldname
     * 
     * @version 20120710, zharkov
     */
    function FillObjectAttachments($rowset, $object_alias, $id_fieldname)
    {
        $alias          = $object_alias == 'item' ? 'steelitem' : $object_alias;
     
        $entityname     = $alias . 'attachments';
        $cache_prefix   = $alias . 'attachments';
        $rowset         = $this->_fill_entity_array_info($rowset, $id_fieldname, $entityname, $cache_prefix, 'sp_attachment_get_list_by_object_ids', array($alias . 's' => '', $alias => 'id', $entityname => ''), array($object_alias));

        foreach($rowset as $key => $row)
        {
            if (isset($row[$entityname]) && !empty($row[$entityname]))
            {
                $rowset[$key][$alias]['attachments'] = $this->FillAttachmentInfo($row[$entityname]);
                unset($rowset[$key][$entityname]);
            }
        }
        
        return $rowset;        
    }    
        
    /**
     * Возвращает список идентификаторов атачментов для объекта
     * 
     * @param mixed $object_alias
     * @param mixed $object_id
     * @param mixed $page_no
     * @param mixed $per_page
     * 
     * @version 20120803, zharkov
     */
    function GetIdsForObject($object_alias, $object_id, $page_no = 0, $per_page = ITEMS_PER_PAGE)
    {
        return $this->_get_list('', $object_alias, $object_id, $page_no, $per_page);
    }
    
    
    /**
    * Возвращает список атачментов для объекта по типу
    * 
    * @param mixed $type
    * @param mixed $object_alias
    * @param mixed $object_id
    * @param mixed $page_no
    * @param mixed $per_page
    */
    function _get_list($type, $object_alias, $object_id, $page_no = 0, $per_page = ITEMS_PER_PAGE)
    {
        $page_no    = $page_no > 0 ? $page_no : 1;
        $per_page   = $per_page < 1 ? ITEMS_PER_PAGE : $per_page;
        $start      = ($page_no - 1) * $per_page;

        $hash       = 'attachments-' . md5('type' . $type . '-object_alias' . $object_alias . '-object_id' . $object_id . '-page_no' . $page_no . '-start' . $start);
        $cache_tags = array('attachments', 'attachments-type' . $type . '-alias' . $object_alias . '-id' . $object_id, 'attachments-alias' . $object_alias . '-id' . $object_id);

        $rowset     = $this->_get_cached_data($hash, 'sp_attachment_get_list', array($type, $object_alias, $object_id, $start, $per_page), $cache_tags);

        return array(
            'data'  => isset($rowset[0]) ? $rowset[0] : array(),
            'count' => isset($rowset[1]) && isset($rowset[1][0]) && isset($rowset[1][0]['rows']) ? $rowset[1][0]['rows'] : 0
        );        
    }
    
    
    
    /**
     * Возвращает список атачментов по типу
     * 
     * @param mixed $type
     * @param mixed $object_alias
     * @param mixed $object_id
     * @param mixed $page_no
     * @param mixed $per_page
     */
    function GetListByType($type, $object_alias, $object_id, $page_no = 0, $per_page = ITEMS_PER_PAGE)
    {
        $result         = $this->_get_list($type, $object_alias, $object_id, $page_no, $per_page);
        $result['data'] = $this->FillAttachmentInfo($result['data']);
        
        return $result;
    }
    
    /**
     * Возвращает список вложений
     * 
     * @param mixed $object_alias
     * @param mixed $object_id
     * @param mixed $page_no
     * @param mixed $per_page
     */
    function GetList($object_alias, $object_id, $page_no = 0, $per_page = ITEMS_PER_PAGE)
    {
        return $this->GetListByType($this->type, $object_alias, $object_id, $page_no, $per_page);
    }

    /**
    * Возвращает вложение по идентификатору
    *     
    * @param mixed $id
    */
    function GetById($id)
    {
        $dataset = $this->FillAttachmentInfo(array(array('attachment_id' => $id)));
        return isset($dataset) && isset($dataset[0]) && isset($dataset[0]['attachment']) ? $dataset[0]['attachment'] : null;
    }
    
    /**
    * Возвращает вложение по секретному имени
    * 
    * @param mixed $secret_name
    * @return mixed
    */
    function GetBySecretName($secret_name)
    {
        $hash       = 'attachment-secretname-' . $secret_name;
        $cache_tags = array('attachments', $hash);

        $rowset = $this->_get_cached_data($hash, 'sp_attachment_get_by_secret_name', array($secret_name), $cache_tags);
        return isset($rowset[0]) && isset($rowset[0][0]) && isset($rowset[0][0]['attachment_id']) ? $this->GetById($rowset[0][0]['attachment_id']) : null;
    }
    
    /**
    * Возвращет информацию о вложении
    * 
    * @param mixed $recordset
    * @param mixed $id_fieldname
    * @param mixed $entityname
    * @param mixed $cache_prefix
    */
    function FillAttachmentInfo($recordset, $id_fieldname = 'attachment_id', $entityname = 'attachment', $cache_prefix = 'attachment')
    {
        $recordset = $this->_fill_entity_info($recordset, $id_fieldname, $entityname, $cache_prefix, 'sp_attachment_get_list_by_ids', array('attachments' => ''), array());

        foreach ($recordset AS $key => $row)
        {
            if (!isset($row[$entityname])) continue;
            
            if (!empty($row[$entityname]['secret_name']))
            {
                //$recordset[$key][$entityname]['virtual_path']   = $this->_get_path($row[$entityname]['secret_name'], ATTACHMENT_HOST);    
                $path = $this->_get_path($row[$entityname]['secret_name']);
                $recordset[$key][$entityname]['path']   = $path; 
                $recordset[$key][$entityname]['src']    = $path . '/' . $row[$entityname]['original_name'];
            }
        }

        return $recordset;
    }

    /**
    * Сохраняет вложение
    * 
    * @param mixed $id
    * @param mixed $object_alias
    * @param mixed $object_id
    * @param mixed $file_object
    * @param mixed $title
    * @param mixed $description
    * @param mixed $status
    */
    function Save($id, $object_alias, $object_id, $file_object, $title = '', $description = '', $status = MODERATE_STATUS_ACTIVE)
    {
        $path_parts     = pathinfo($file_object['name']);
        $ext            = $path_parts['extension'];

        $original_name  = Translit::EncodeAndClear($file_object['name']);
        $original_name  = preg_replace('#\.#', '_', $original_name, (substr_count($original_name, '.') - 1));
        
        $content_type   = $file_object['type'];
        $size           = $file_object['size'];

        $attachment     = $this->_save_to_db($id, $object_alias, $object_id, $content_type, $original_name, $size, $ext, $status, $title, $description);

        if (isset($attachment))
        {
            $result = $this->_save_to_disc($attachment, $file_object);
            
            // если при сохранении файла на диск возникли проблемы, то он удаляется из бд
            if (empty($result)) 
            {
                Log::AddLine(LOG_ERROR, 'Error saving attachment to disc!');
                $this->Remove($attachment['id']);
            }
            else
            {
                if ($object_alias == 'inddt')
                {
                      $modelInDDT = new InDDT();
                      $modelInDDT->LinkAttachmentToItems($object_id);
                }
                else if ($object_alias == 'supplierinvoice')
                {
                      $modelSupplierInvoice = new SupplierInvoice();
                      $modelSupplierInvoice->LinkAttachmentToItems($object_id);                    
                }
            }
            
            return $result;
        } 
        
        return null;
    }

    /**
     * Создает атачмент из файла
     * 
     * @param mixed $id
     * @param mixed $object_alias
     * @param mixed $object_id
     * @param mixed $path
     * @param mixed $title
     * @param mixed $description
     * @param mixed $status
     * @return mixed
     */
    function CreateFromFile($object_alias, $object_id, $path, $title = '', $description = '', $status = MODERATE_STATUS_ACTIVE, $original_name = '')
    {        
        $path_parts     = pathinfo($path);
        $ext            = $path_parts['extension'];
        $original_name  = (empty($original_name) ? $path_parts['basename'] : $original_name);
        $content_type   = MimeType::GetMimeTypeByPath($path);
        $size           = filesize($path);

        $attachment     = $this->_save_to_db(0, $object_alias, $object_id, $content_type, $original_name, $size, $ext, $status, $title, $description);
        
        Cache::ClearTag($object_alias . '-' . $object_id);
        
        if (isset($attachment))
        {
            if ($dest_path = $this->_get_path($attachment['secret_name'])) 
            {
                // переносит файл на новое место
                $result = rename($path, $dest_path . '/' . $attachment['original_name']);

                // если при сохранении файла на диск возникли проблемы, то атачмент удаляется из бд
                if (empty($result)) 
                {
                    $this->Remove($attachment['id']);
                    return null;
                }
				
				if (in_array($ext, array('jpg', 'jpeg', 'png', 'gif')))
				{
					$file_object['name']		= $original_name;
					$file_object['type']		= $content_type;
					$file_object['size']		= $size;
					$file_object['tmp_name']	= $attachment['path'] . '/' . $original_name;

					// подключение настроек изображения
					global $__picture_settings;        

					// если нет настроек для выбранного object_alias, то используются настройки по-умолчанию
					$settings_alias = isset($__picture_settings[$attachment['object_alias']]) ? $attachment['object_alias'] : 'default';

					$original_image = new Image($file_object);
					  
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
								$original_image->CreateThumbnail($w, $h, $maxside, $crop, $dest_path, $key . '.' . $ext, $watermark);
							}
						}
					}
				}
                
                return $attachment['id'];
			}
        } 
		
        return null;
    }
    
    /**
    * Удаляет вложение
    * 
    * @param mixed $id
    * @return int
    */
    function Remove($attachment_id)
    {
        $attachment = $this->GetById($attachment_id);
        if (empty($attachment)) return null;

        $result = $this->CallStoredProcedure('sp_attachment_remove', array($attachment_id));
        
        if (isset($result))
        {
            $attachment = isset($result[0]) && isset($result[0][0]) ? $result[0][0] : array();
            $links      = isset($result[1]) ? $result[1] : array();
            
            if (empty($attachment)) return null;
            
            Cache::ClearTag('attachment-' . $attachment['id']);
            
            foreach ($links as $row)
            {
                Cache::ClearTag($row['object_alias'] . 'attachments-' . $row['object_id']);
                Cache::ClearTag('attachments-alias' . $row['object_alias'] . '-id' . $row['object_id']);
            }

            return $this->_delete_from_disc($attachment);            
        }
        
        return null;
    }
    
    /**
    * Обновляет параметры атачмента
    * 
    * @param mixed $id
    * @param mixed $params
    * @return integer
    */
    function UpdateSingle($id, $params)
    {
        $attachment = $this->GetById($id);
        if (empty($attachment)) return null;

        $result = $this->Update($id, $params);
        
        Cache::ClearTag('attachments');
        Cache::ClearTag('attachment-' . $id);
        Cache::ClearTag($attachment['object_alias'] . '-' . $attachment['object_id']);
        
        return $result;
    }
    

    /**
    * Сохраняет вложение в бд
    *     
    * @param mixed $id
    * @param mixed $object_alias
    * @param mixed $object_id
    * @param mixed $content_type
    * @param mixed $original_name
    * @param mixed $size
    * @param mixed $ext
    * @param mixed $status
    * @param mixed $title
    * @param mixed $description
    */
    function _save_to_db($id, $object_alias, $object_id, $content_type, $original_name, $size, $ext, $status, $title, $description)
    {
        $result = $this->CallStoredProcedure('sp_attachment_save', array($this->user_id, $id, $this->type, $object_alias, $object_id, $content_type, $original_name, $size, $ext, $status, $title, $description));
        $result = isset($result) && isset($result[0]) && isset($result[0][0]) ? $result[0][0] : null;

        if (empty($result) || array_key_exists('ErrorCode', $result)) return null;

        // при добавлении нового атачмента связывает его с объектом
        if (empty($id) && !empty($object_alias) && !empty($object_id)) 
        {
            $this->LinkToObject($result['attachment_id'], $object_alias, $object_id);
        }
        
        Cache::ClearTag($object_alias . 'quick-' . $object_id);
        Cache::ClearTag('attachment-' . $result['attachment_id']);
        Cache::ClearTag('attachments-type' . $this->type . '-alias' . $object_alias . '-id' . $object_id);
        Cache::ClearTag('attachments-alias' . $object_alias . '-id' . $object_id);
        Cache::ClearTag($object_alias . 'attachments');
        Cache::ClearTag($object_alias . 'attachments-' . $object_id);
        
        $result = $this->FillAttachmentInfo(array(array('attachment_id' => $result['attachment_id'])));
        return isset($result) && isset($result[0]) && isset($result[0]['attachment']) ? $result[0]['attachment'] : null;        
    }
    
    /**
    * Сохраняет файл а диск
    * 
    * @param mixed $secret_name - секретное имя файла в бд
    * @param mixed $file_object - файловый объект
     * 
     * @version 20130227, d10n: refactor
    */
    function _save_to_disc($attachment, $file_object)
    {
//        if (!is_file($file_object['tmp_name'])) return null;
        
        $dir_path  = $this->_get_path($attachment['secret_name']);
        
        if (!$dir_path) return null;

        $file_path = $dir_path . '/' . $attachment['original_name'];
        
        if (is_uploaded_file($file_object['tmp_name']))
        {
            if (move_uploaded_file($file_object['tmp_name'], $file_path)) return $attachment['id'];
        }
        else if (is_array($file_object['tmp_name']) && array_key_exists('pFile', $file_object['tmp_name']))
        {
            $file_pid = fopen($file_path, "w");
            fseek($file_object['tmp_name']['pFile'], 0, SEEK_SET);
            stream_copy_to_stream($file_object['tmp_name']['pFile'], $file_pid);
            fclose($file_pid);
            
            return $attachment['id'];
        }
        else if (array_key_exists('from_disc', $file_object) && $file_object['from_disc'] == true)
        {
            if (copy($file_object['tmp_name'], $file_path))
            {
                unlink($file_object['tmp_name']);
                return $attachment['id'];
            }
        }
        
        return null;
// @deprecated 20130227, d10n
//        if (!empty($file_object['tmp_name']['pFile']))
//        {
//            $path   = $this->_get_path($attachment['secret_name']);
//            $target = fopen($path.'/'. $attachment['original_name'], "w");
//            
//            fseek($file_object['tmp_name']['pFile'], 0, SEEK_SET);
//            stream_copy_to_stream($file_object['tmp_name']['pFile'], $target);
//            
//            return $attachment['id'];
//        }
//        else if ($path = $this->_get_path($attachment['secret_name'])) 
//        {
//            if (move_uploaded_file($file_object['tmp_name'], $path . $attachment['original_name'])) return $attachment['id'];
//        }
//        
//        return null;
    }
    
    /**
    * Удаляет файл с диска
    * 
    * @param mixed $attachment
    */
    function _delete_from_disc($attachment)
    {
        $dir_path   = $this->_get_path($attachment['secret_name']);
        $file_path  = $dir_path . $attachment['original_name'];
        
        if (file_exists($file_path))
        {
            unlink($file_path);
            rmdir($dir_path);
        }

        return $attachment;
    }
    
    /**
     * Формирует путь к вложению по секретному имени
     * 
     * @param mixed $secret_name
     */
    function _get_path($secret_name, $attachment_path = ATTACHMENT_PATH)
    {
        $path = UserPicture::GetPath($secret_name, $attachment_path);
        
        // создание каталога, если он не существует
        if ($attachment_path == ATTACHMENT_PATH && is_dir($path) == false)
        {
            $oldmask    = umask(0);
            $res        = mkdir($path, 0777, true);
            umask($oldmask);

            if ($res == false) return null;
        }

        return $path;
    }
    
    /**
     * Сохраняет Title атачмента
     * 
     * @param int $id
     * @param string $title
     */
    public function SaveTitle($id, $title = '')
    {
        $this->Update($id, array(
            'title'         => $title,
            'modified_at'   => 'NOW()!',
            'modified_by'   => $this->user_id,
        ));
        
        Cache::ClearTag('attachment-' . $id);
    }
}