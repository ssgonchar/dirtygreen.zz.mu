<?php
require_once APP_PATH . 'classes/common/mimetype.class.php';
require_once APP_PATH . 'classes/models/album.class.php';
require_once APP_PATH . 'classes/models/attachment.class.php';
require_once APP_PATH . 'classes/models/picture.class.php';

class MainAjaxController extends ApplicationAjaxController
{    
    /**
     * Allowed file formats
     * Допустимые форматы файлов
     * 
     * @var mixed
     */
    var $allowed_extensions = array(
        'bmp'   => 'picture',
        'jpg'   => 'picture',
        'jpe'   => 'picture',
        'jpeg'  => 'picture',
        'gif'   => 'picture',
        'png'   => 'picture',
        'tiff'  => 'picture',
        'tif'   => 'picture',
        'pic'   => 'picture',
        'pcx'   => 'picture',
        'tga'   => 'picture',
        
        'dwg'   => 'other',
        'psd'   => 'other',
        'dxf'   => 'other',
        'pdf'   => 'other',
        '3ds'   => 'other',
        'xlsx'  => 'other',
        'xls'   => 'other',
        'docx'  => 'other',
        'doc'   => 'other',
        'fla'   => 'other',
        'flx'   => 'other',
        'fly'   => 'other',
        '3gp'   => 'other',
        'zip'   => 'other',
        'rar'   => 'other',
        '7z'    => 'other',
        'gzip'  => 'other',
        'tar'   => 'other',
        'txt'   => 'other',
        'xml'   => 'other',
        'ppt'   => 'other',
        'mp3'   => 'other'
    );
    
    function MainAjaxController()
    {
        ApplicationAjaxController::ApplicationAjaxController();
        
        $this->authorize_before_exec['upload']      = ROLE_STAFF;
        $this->authorize_before_exec['setasmain']   = ROLE_STAFF;
        $this->authorize_before_exec['remove']      = ROLE_STAFF;
        $this->authorize_before_exec['getimages']   = ROLE_STAFF;        
    }
    
    /**
     * Get files for object // Возвращает набор картиок для объекта
     * url:/attachment/getimages
     */
    function getimages()
    {
        $object_alias   = Request::GetString('object_alias', $_REQUEST);
        $object_id      = Request::GetInteger('object_id', $_REQUEST);

        if (!empty($object_alias) && !empty($object_id))
        {
            $modelPicture   = new Picture();
            $rowset         = $modelPicture->GetList($object_alias, $object_id);

            $this->_assign('pictures',      $rowset['data']);
            $this->_assign('object_alias',  $object_alias);
            $this->_assign('object_id',     $object_id);

            $this->_send_json(array(
                'result'    => 'okay', 
                'content'   => $this->smarty->fetch('templates/controls/image_select.tpl')
            ));
        }
        
        $this->_send_json(array('result' => 'error', 'message' => 'Unknown image source !'));        
    }
    
    /**
     * Remove attachment // Удаляет атачмент
     * url: /attachment/remove
     * 
     * @version 20120805, zharkov: добавлено удаление из объекта и физическое удаление только из создававшего объекта
     * @version 20130227, d10n: добалена возможность удаления временно-загруженных файлов
     */
    function remove()
    {
        $object_alias   = Request::GetString('object_alias', $_REQUEST);
        $object_id      = Request::GetInteger('object_id', $_REQUEST);
        $attachment_id  = Request::GetInteger('attachment_id', $_REQUEST);
        
        if (empty($attachment_id)) $this->_send_json(array('result' => 'error', 'message' => 'Attachment Id must be specified !'));

        $attachments    = new Attachment();
        $attachment     = $attachments->GetById($attachment_id);
        
        if (empty($attachment)) $this->_send_json(array('result' => 'error', 'message' => 'Attachment is not exist !'));
                
        // remove only if object the same as in request // физически удаляется только из того объекта в котором атачмент создан
        if ($object_alias == $attachment['object_alias'] && $object_id == $attachment['object_id'])
        {
            $attachments->Remove($attachment_id);
            
            // if main picture removed, select new main picture // если удаляется главное изображение объекта, то выбирается другое главное изображение
            if ($attachment['type'] == 'image' && $attachment['is_main'] > 0) 
            {
                $this->update_object_picture($attachment['object_alias'], $attachment['object_id'], $attachment_id);
            }            
        }
        
        // remove from object // удаляет атачмент из объекта
        $attachments->RemoveFromObject($attachment_id, $object_alias, $object_id);
        
        // remove from temporary
        //$session_key = 'attachments-' . $attachment['object_alias'] . '-' . $attachment['object_id']; // 20130618, zharkov: fix session_key
        $session_key = 'attachments-' . $object_alias . '-' . $object_id;
        if (isset($_SESSION[$session_key]))
        {
            unset($_SESSION[$session_key][$attachment_id]);
        }
        
        $this->_send_json(array('result' => 'okay'));        
    }
    
    /**
     * Устанавливает главную картинку для объекта
     * url: /attachment/setasmain
     */
    function setasmain()
    {
        $attachment_id  = Request::GetInteger('attachment_id', $_REQUEST);
        if (empty($attachment_id)) $this->_send_json(array('result' => 'error', 'message' => 'Attachment Id must be specified !'));
        
        $attachments    = new Attachment();
        $attachment     = $attachments->GetById($attachment_id);
        if (empty($attachment)) $this->_send_json(array('result' => 'error', 'message' => 'Attachment is not exist !'));
        
        if ($attachment['type'] != 'image') $this->_send_json(array('result' => 'error', 'message' => 'Attachment has wrong type !'));
        
        $pictures   = new Picture();
        $result     = $pictures->SetAsMain($attachment_id);        
        if (empty($result)) $this->_send_json(array('result' => 'error', 'message' => 'Error updating main picture !'));
        
        $this->update_object_picture($result['object_alias'], $result['object_id'], $attachment_id);
        
        $this->_send_json(array('result' => 'okay'));
    }
    
    /**
     * Add file throw Fileuploader // Добавляет файл через FileUploader
     * url: /attachment/upload
     */
    function upload()
    {
        $file_name      = Request::GetHtmlString('qqfile', $_REQUEST);
        $object_alias   = Request::GetString('object_alias', $_REQUEST);
        $object_id      = Request::GetInteger('object_id', $_REQUEST);
        $template       = Request::GetString('template', $_REQUEST, 'default');
        $filetype       = Request::GetString('filetype', $_REQUEST, 'all');
        $readonly       = Request::GetBoolean('readonly', $_REQUEST, false);
        
        // 20120803, zharkov: email trick
        if ($object_alias == 'newemail') $object_id = $this->user_id;
        
        if (!empty($_FILES['qqfile'])) $file_name = $_FILES['qqfile'];

        if (!empty($file_name))
        {
            // test allowed max attachments
            if (isset($_SESSION['attachments-' . $object_alias . '-' . $object_id]) && count($_SESSION['attachments-' . $object_alias . '-' . $object_id]) >= 150)
            {
                $this->_send_json(array('error' => 'Attachment limit reached .'));
                return;
            }
            
            if (empty($_FILES['qqfile']))
            {
                $input      = fopen("php://input", "r");
                $temp       = tmpfile();
                $real_size  = stream_copy_to_stream($input, $temp);
                fclose($input);

                if (isset($_SERVER["CONTENT_LENGTH"])) $size = (int) $_SERVER["CONTENT_LENGTH"];

                if ($real_size != $size)
                {  
                    $this->_send_json(array('error' => 'Error uploading file (wrong file size) .'));
                    return false;
                }

                $file_info      = pathinfo($file_name);
                $file_extension = $this->check_allowed_extension($file_info['extension']);

                if (empty($file_extension)) $this->_send_json(array('error' => 'Error uploading file (wrong file type) .'));

                $file_object['name']     = $file_name;
                $file_object['type']     = MimeType::GetMimeTypeByExtension(strtolower($file_info['extension']));
                $file_object['size']     = $real_size;
                $file_object['tmp_name'] = array('pFile' => $temp);
            }
            else
            {
                $file           = pathinfo($_FILES['qqfile']['name']);
                $file_extension = $this->check_allowed_extension($file['extension']);

                if (empty($file_extension)) $this->_send_json(array('error' => 'Error uploading file (wrong file type) .'));

                $file_object = $_FILES['qqfile'];
            }
            
            
            if ($filetype == 'pictures' && $file_extension != 'picture') $this->_send_json(array('error' => 'Incorrect picture file type .'));
            

            $attachments    = $file_extension == 'picture' ? new Picture() : new Attachment();
            $attachment_id  = $attachments->Save(0, $object_alias, $object_id, $file_object);
            $attachment     = $attachments->GetById($attachment_id);

            // set main picture // обновляет главную картинку объекта
            if ($attachment['type'] == 'image' && $attachment['is_main'] > 0) $this->update_object_picture($object_alias, $object_id, $attachment_id);
            
            if (!empty($temp)) fclose($temp);
            
            // Add attached object id into session // Добавляет ID загруженного файла в сессию, для дальнейшего сохранения
            $_SESSION['attachments-' . $object_alias . '-' . $object_id][$attachment['id']] = true;
            
            $this->_assign('attachment',    $attachment);
            $this->_assign('object_alias',  $object_alias);
            $this->_assign('object_id',     $object_id);
            
            if ($readonly) $this->_assign('readonly', true);
            if (!empty($_FILES['qqfile'])) $this->_assign('is_frame', true);

            $content = $this->smarty->fetch('templates/controls/attachment_' . $template . '.tpl');
            
            if (empty($_FILES['qqfile']))
            {
                $this->_send_json(array('success' => true, 'content' => $content));
            }
            else 
            {
                echo $content; 
            }
        }
    }
    
    /**
     * Check for allowed file extension // Проверяет доступно ли данное расширение для загрузки 
     * 
     * @param string $ext
     */
    private function check_allowed_extension($ext)
    {
        $ext = strtolower($ext);
        return isset($this->allowed_extensions[$ext]) ? $this->allowed_extensions[$ext] : null;
    }
    
    /**
     * Update main picture // Обновляет главную картинку объекта
     * 
     * @param mixed $object_alias
     * @param mixed $object_id
     * @param mixed $attachment_id
     */
    private function update_object_picture($object_alias, $object_id, $attachment_id)
    {
        if ($object_alias == 'person')
        {
            $persons = new Person();
            $persons->UpdatePicture($object_id, $attachment_id);
        }        
    }
    
    /**
     * Get attached file list for object
     * Возвращает список файлов для конкретного объекта<br />
     * HTML-код подготовленный для модального окна
     * @url /attachment/getmodalbox
     * 
     * @version 20130222, d10n
     */
    public function getmodalbox()
    {
        $object_alias   = Request::GetString('oalias', $_REQUEST);
        $object_id      = Request::GetInteger('oid', $_REQUEST);
        
        // проверка на существование временных файлов
        $key_in_session = 'attachments-' . $object_alias . '-' . $object_id;
        if (array_key_exists($key_in_session, $_SESSION))
        {
            unset($_SESSION[$key_in_session]);
        }
        
        $this->_assign('object_alias',  $object_alias);
        $this->_assign('object_id',     $object_id);

        $this->_send_json(array(
            'result'    => 'okay', 
            'content'   => $this->smarty->fetch('templates/controls/share_files.tpl')
        ));
    }
    
    /**
     * Save attachments
     * Сохраняет аттачмент(ы) в системе
     * @url /attachment/save
     * 
     * @verions 20130227, d10n
     */
    public function save()
    {
        $object_alias   = Request::GetString('oalias', $_REQUEST);
        $object_id      = Request::GetInteger('oid', $_REQUEST);
        
        $titles_list    = isset($_REQUEST['titles']) ? $_REQUEST['titles'] : array();
        $attachments    = array();
        
        // check existing files & processing // проверка на существование временных файлов и последующая обработка
        $key_in_session = 'attachments-' . $object_alias . '-' . $object_id;
        if (array_key_exists($key_in_session, $_SESSION))
        {
            $attachments = $_SESSION[$key_in_session];
            //unset($_SESSION[$key_in_session]);
        }
        
        if (empty($attachments))
        {
            $this->_send_json(array('result' => 'error', 'message' => 'Files  not found !'));
        }
        
        $modelAttachment    = new Attachment();
        $modelPicture       = new Picture();
        
        $this->_assign('object_alias',  $object_alias);
        $this->_assign('object_id',     $object_id);
        
        $content = array('span' => '');
        $att_ids = array(); 
        foreach ($titles_list as $id => $title)
        {
            if (!array_key_exists($id, $attachments)) continue;
            
            $attachment = $attachments[$id];
            
            $file_extension = $this->check_allowed_extension($attachment['ext']);
            if (empty($file_extension)) continue;
            
            $attachmentObject   = $file_extension == 'picture' ? $modelPicture : $modelAttachment;
            $attachment_id      = $attachmentObject->Save(0, $object_alias, $object_id, $attachment['file_object'], $title);
            $attachment_saved   = $attachmentObject->GetById($attachment_id);
            
            if (!array_key_exists('id', $attachment_saved)) continue;
            
            $this->_assign('attachment', $attachment_saved);
            $content['span'] .= $this->smarty->fetch('templates/controls/object_shared_file_span.tpl');
            
            $att_ids[] = $attachment_saved['id'];
        }
        
        $this->_send_json(array('result' => 'okay', 'content' => $content, 'att_ids' => $att_ids));
    }
    
    /**
     * Upload to temporary folder
     * 
     * Файловый upload во временную папку приложения<br />
     * Примечание: временная папка ...../attachments/tmp/sharedfiles
     * 
     * @url /attachment/tempupload
     * 
     * @version 20130217, d10n
     */
    public function tempupload()
    {
        $file_name      = Request::GetHtmlString('qqfile', $_REQUEST);
        $object_alias   = Request::GetString('object_alias', $_REQUEST);
        $object_id      = Request::GetInteger('object_id', $_REQUEST);
        $outputstyle    = Request::GetString('outputstyle', $_REQUEST, 'simple');
        
        // check upload method // проверяется, каким методом передаетс файл: если существует $_FILES, то метод POST, иначе XHR
        $is_XHR         = !array_key_exists('qqfile', $_FILES);
        
        if (!$is_XHR) $file_name = $_FILES['qqfile']['name'];
        
        if (empty($file_name))
        {
            $message = 'File Not Found !';
            
            if ($is_XHR) $this->_send_json(array('error' => $message));
            
            echo $message;
            exit;
        }
        
        if ($is_XHR)
        {
            // получение контента файла из потока
            $input          = fopen("php://input", "r");
            $tmp_resource_id= tmpfile();
            $real_size      = stream_copy_to_stream($input, $tmp_resource_id);
            fclose($input);
            
            // получение размера контента (файла)
            if (isset($_SERVER["CONTENT_LENGTH"])) $size = (int) $_SERVER["CONTENT_LENGTH"];
            
            // проверка корректности файлового размера
            if ($real_size != $size) $this->_send_json(array('error' => 'Error uploading file (wrong file size) .'));
            
            // получение информации о файле
            $file_pathinfo  = pathinfo($file_name);
            
            // проверка возможности закгрузки с данным расширением
            $file_extension = $this->check_allowed_extension($file_pathinfo['extension']);
            if (empty($file_extension)) $this->_send_json(array('error' => 'Error uploading file (wrong file type) .'));
            
            // транслитерация именни файла перед сохранением
            $file_name = Translit::EncodeAndClear($file_name);
            
            // формирование объекта аля $_FILES
            $file_object['name']     = $file_name;
            $file_object['type']     = MimeType::GetMimeTypeByExtension(strtolower($file_pathinfo['extension']));
            $file_object['size']     = $real_size;
            $file_object['tmp_name'] = array('pFile' => $tmp_resource_id);
            
            // перемещение файла во временную директорию проекта
            $tmpfile_path       = $this->_create_temporary_dir() . $file_name;
            $tmpfile_source_id  = fopen($tmpfile_path, "w");
            fseek($tmp_resource_id, 0, SEEK_SET);
            stream_copy_to_stream($tmp_resource_id, $tmpfile_source_id);
        }
        else
        {
            // получение информации о файле
            $file_pathinfo  = pathinfo($file_name);
            
            // проверка возможности закгрузки с данным расширением
            $file_extension = $this->check_allowed_extension($file_pathinfo['extension']);
            
            if (empty($file_extension))
            {
                echo 'Error uploading file (wrong file type) !.';
                exit;
            }
            
            // формирование объекта аля $_FILES
            $file_object = $_FILES['qqfile'];
            
            // транслитерация имени файла перед сохранением
            $file_name = Translit::EncodeAndClear($file_name);
            
            // перемещение файла во временную директорию проекта
            $tmpfile_path       = $this->_create_temporary_dir() . $file_name;
            move_uploaded_file($file_object['tmp_name'], $tmpfile_path);
        }
        
        $file_object['tmp_name']    = $tmpfile_path;
        $file_object['name']        = $file_name;
        $file_object['from_disc']   = true;
        
        $attachment = array(
            'id'            => time() - rand(10000, 20000) - 1300000000,
            'object_alias'  => $object_alias,
            'object_id'     => $object_id,
            'type'          => stristr($file_object['type'], 'image') ? 'image' : 'file',
            'ext'           => $file_pathinfo['extension'],
            'size'          => $file_object['size'],
            'original_name' => $file_name,
            'file_object'   => $file_object,
        );
        
        // формирование набора урлов временных файлов, для дальнейшей обработки
        $key_in_session = 'attachments-' . $object_alias . '-' . $object_id;
        
        if (!array_key_exists($key_in_session, $_SESSION))
        {
            $_SESSION[$key_in_session] = array();
        }
        
        $_SESSION[$key_in_session][$attachment['id']] = $attachment;
        
        // формирование контента
        $this->_assign('attachment', $attachment);
        switch ($outputstyle)
        {
            case 'table-row':
                $this->_assign('can_be_edited', 1);
                $content['table_row'] = $this->smarty->fetch('templates/controls/share_files_row.tpl');
                break;
                
            default:
                $content = $this->smarty->fetch('templates/html/dropbox/control_attachment_block.tpl');
        }
        
        if ($is_XHR)
        {
            $this->_send_json(array('success' => true, 'content' => $content));
        }
        else 
        {
            echo $content; 
        }
    }
    
    
    /**
     * Создает временную директорию для хранения временных файлов
     * @return string|boolean Путь к директории. В случае неудачи FALSE
     */
    private function _create_temporary_dir()
    {
        $temp_dir = ATTACHMENT_PATH . 'tmp/sharedfiles/' . md5(time()) . '/';
        
        if (is_dir($temp_dir)) return $temp_dir;
        
        $oldmask    = umask(0);
        $res        = mkdir($temp_dir, 0777, true);
        umask($oldmask);
        
        if (!$res) return FALSE;
        
        return  $temp_dir;
    }
}