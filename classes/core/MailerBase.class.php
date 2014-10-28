<?php
require_once APP_PATH . 'classes/core/Spooler.class.php';

/**
 * Класс для формирования писем
 *
 *  @version 2008.10.08, Mr. Bono: 
 *      Изменили на MailerBase. 
 *
 */
class MailerBase 
{
    /**
     * Экземпляр класса Smarty
     *
     * @var Smarty
     */
    var $smarty;


    /**
     * Модель спулера.
     *    
     * @var object
     */
    var $spooler;
    
    /**
     * Путь к шаблону dir1/dir2/dir3
     * @var string
     */
    public $path = 'common';


    /**
     * Конструктор
     *
     * @param Smarty $smarty экземпляр объекта Smarty
     */
    public function MailerBase()
    {
        $this->smarty       = SmartyWrapper::Create();
        $this->mailer_id    = substr(strtolower(get_class($this)), 0, strpos(strtolower(get_class($this)), 'mailer'));

        $this->_init_first();
    }


    /**
     * Триггер. Выполняется перед запуском запрошенного метода.
     */
    function _init_first()
    {
    }


    /**
     * Возвращает сгенерированный текст письма на основе шаблона Smarty
     *
     * @param string $template имя шаблона
     * @return string результирующий HTML шаблона
     */
    function _fetch($template)
    {
        return $this->smarty->fetch('templates/mail/' . trim($this->path, '/') . '/' . $template . '.tpl');
    }


    /**
     * Возвращает сгенерированный заголовок на основе шаблона Smarty
     *
     * @param string $template имя шаблона
     * @return string результирующий HTML шаблона
     */
    function _fetch_subject($template)
    {
        return $this->_fetch($template . '-subject');
    }

    /**
     * Отправляет письмо
     * @param string $from E-Mail отправителя
     * @param string $to E-Mail получателя
     * @param string $template Название шаблона письма
     * @param array $parameters - Парематры шаблона письма
     * @param array $attachments
     * @return boolean
     */
    public function _send($from, $to, $cc, $bcc, $template, $parameters, $attachments = array())
    {
// Подготовка/Определение параметров для шаблонов письма и заголовка
        $this->smarty->assign('mail', $parameters);

// Подготовка/Обработка Заголовка
        $subject    = $this->_fetch_subject($template);
//        $subject    = "=?UTF-8?b?" . base64_encode($subject) . "?=";      20130618, zharkov: do not need
        
// Подготовка/Обработка тела письма
        $message = $this->_fetch($template);
		//print_r($message);
// Разделители
        $boundary_main = md5('boundary-main');

// Сборка блока HEADERS
        $headers = "";

        $headers .= "MIME-Version: 1.0\n";
        $headers .= "Content-Type: multipart/mixed; boundary=\"" . $boundary_main . "\"\n";
        $headers .= "From: " . $from . "\n";
        if (!empty($cc)) $headers .= "Cc: " . $cc . "\n";
        if (!empty($bcc)) $headers .= "Bcc: " . $bcc . "\n";
        $headers .= "Reply-To: " . $from . "\n";
        $headers .= "Return-Path: " . $this->_prepare_from($from) . "\n";
        
// Группа заголовков для формирования уведомления о прочтении (получатель-отправитель)
if (isset($parameters['test_mode']))
{
        echo $headers . '<br><br>';
}
else
{
        $headers .= "X-Confirm-Reading-To: " . $this->_prepare_from($from) . "\n";
        $headers .= "Disposition-Notification-To: " . $this->_prepare_from($from) . "\n";
        $headers .= "Return-Receipt-To: " . $this->_prepare_from($from) . "\n";    
}


// Сборка блока BODY
        $body = "";

        $body .= "--" . $boundary_main . "\r\n"; 
        $body .= "Content-Type: text/html; charset=\"utf-8\"\r\n";
        $body .= "\r\n";
        $body .= $message . "\r\n";
        $body .= "\r\n";

// Прикрепление innerHTML изображений (при наличии в теле сообщения)
        if (!empty($content_ids))
        {
            foreach ($content_ids as $item)
            {
                $file_src   = $item['src'];
                $file_cid   = $item['id'];

                $body .= $this->_html_image_compile($boundary_main, $file_src, $file_cid);
            }
        }

        $body .= "\r\n";

// Прикрепление внешних файлов (при наличии)
        if (!empty($attachments))
        {
            foreach ($attachments as $file)
            {
                //debug('1682', $file);
                if (isset($file['attachment'])) $file = $file['attachment'];
                
                $file_src   = $file['src'];
                $file_name  = array_key_exists('name', $file) ? $file['name'] : '';

                $body .= $this->_attachment_compile($boundary_main, $file_src, $file_name);
            }
        }

        $body .= '--' . $boundary_main . "--\r\n";// Граница конца тела письма
        $body .= "\r\n";// Окончание письма


        $additional_params = "-f" . $this->_prepare_from($from);

        Log::AddLine(LOG_CUSTOM, __METHOD__ . " TO:$to\nSubject:$subject\nHEADERS:\n$headers\n\nBODY:\n$body\n\nAdditionalParams:$additional_params\n\n");

        if (MAILER_ENABLED == 'yes')
        {
            return mail($to, $subject, $body, $headers, $additional_params);
        }
        else
        {
            return FALSE;
        }
    }
    
    /**
     * Собирает Вложения/Аттачмент часть с заголовками и телом (файлом)
     * для письма с Content-Type:multipart/mixed
     */
    private function _attachment_compile($boundary, $file_src, $file_name = '')
    {
        //$_SESSION['file_src'] = $file_src;
        $filecontent    = file_get_contents($file_src);
        //$_SESSION['filecontent'] = $file_src;
        $filename       = empty($file_name) ? basename($file_src) : $file_name;

        $string = "";

        $string .= "--" . $boundary . "\r\n";
        $string .= "Content-Type:application/octet-stream;name=\"" . $filename . "\"\r\n";
        $string .= "Content-Transfer-Encoding:base64\r\n";
        $string .= "Content-Disposition:attachment;filename=\"" . $filename . "\"\r\n";
        $string .= "\r\n";
        $string .= chunk_split(base64_encode($filecontent)) . "\r\n";
        //$string .= "\r\n";

        return $string;
    }

    /**
     * Собирает часть с заголовками и телом (файлом) для встроенных в HTML-тело письма изображений
     * для письма с Content-Type:multipart/mixed
     */
    private function _html_image_compile($boundary, $file_src, $file_cid)
    {
        $filecontent    = file_get_contents($file_src);
        $filename       = basename($file_src);

        $string = "";

        $string .= "--" . $boundary . "\r\n";
        $string .= "Content-Type:application/octet-stream;name=\"" . $filename . "\"\r\n";
        $string .= "Content-Transfer-Encoding:base64\r\n";
        $string .= "Content-ID:<" . $file_cid . ">\r\n";
        $string .= "\r\n";
        $string .= chunk_split(base64_encode($filecontent)) . "\r\n";
        //$string .= "\r\n";

        return $string;
    }


    /**
     * Генерирует письмо
     *
     * @param string $from e-mail отправителя
     * @param string $to e-mail получателя
     * @param string $template название шаблона письма
     * @param array $parameters дополнительные параметры отправки письма
     * @return string сгененированный e-mail
     */
    function _render($from, $to, $template, $parameters)
    {
        $this->smarty->assign('mail', $parameters);

        $body = $this->_fetch($template, $parameters);
        $subject = $this->_fetch_subject($template);

        $result = 
            "From: $from\n" .
            "To: $to\n" .
            "Subject: $subject\n\n" .
            "$body";

        return $result;
    }
    
    /**
     * Выбирает только адрес из строки отправителя
     * 
     * @param string $email
     * @return string
     */
    function _prepare_from($email)
    {
	    $pos1 = mb_strpos($email, '<', 0, 'utf-8');
	    $pos2 = mb_strpos($email, '>', 0, 'utf-8');

	    if ($pos1 !== false && $pos2 !== false)
	    {
	        $email = mb_substr($email, $pos1 + 1, $pos2 - $pos1 - 1, 'utf-8');
        }

        return $email;
    }
}
