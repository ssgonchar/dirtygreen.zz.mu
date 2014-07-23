<?php
require_once APP_PATH . 'classes/services/json/json.php';
require_once APP_PATH . 'classes/core/ControllerBase.class.php';

/**
 * Класс для управления AJAX запросами
 *
 */
class AjaxController extends ControllerBase
{
    /**
    * Конструктор
    *
    * @param Smarty $smarty экземпляр объекта Smarty
    */
    function AjaxController()
    {
        ControllerBase::ControllerBase();

        $this->controller_id = substr(strtolower(get_class($this)), 0, strpos(strtolower(get_class($this)), 'ajaxcontroller'));
    }

    /**
    * Возвращает сгенерированный HTML-код на основе шаблона Smarty
        *
        * @param string $template имя шаблона
        * @return string результирующий HTML шаблона
    */
    function _fetch($template)
    {
        return $this->smarty->fetch($this->_get_template_file_name($template));

    }

    /**
    * Отправляет строку в стандартный поток вывода
        *
        * @param string $str строка для отправки в поток
    */
    function _send_string($str)
    {
        header("Expires: Mon, 1 Jan 1997 05:00:00 GMT");    // Date in the past
        header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT"); // always modified
        header("Cache-Control: no-store, no-cache, must-revalidate");  // HTTP/1.1
        header("Cache-Control: post-check=0, pre-check=0", false);
        header("Pragma: no-cache");

        echo $str;
    }

    /**
    * Отправляет массив в стандартный поток вывода
        *
        * Массив PHP предварительно преобразуется в JSON-структуру
        *
        * @param array $arr массив для отправки в поток
    */
    function _send_json($arr)
    {
        header("Expires: Mon, 1 Jan 1997 05:00:00 GMT");    // Date in the past
        header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT"); // always modified
        header("Cache-Control: no-store, no-cache, must-revalidate");  // HTTP/1.1
        header("Cache-Control: post-check=0, pre-check=0", false);
        header("Pragma: no-cache");

        $json = new Services_JSON();
        echo $json->encode($arr);
        
        exit;
    }


    /**
    * Отправляет сообщение в стандартный поток вывода
        *
        * @param string $message текст сообщения
        * @param integer $status статус сообщения
    */
    function _send_message($message, $status)
    {
        $result = array(
            'message' => array(
                'text' => $message,
                'status' => $status
            )
        );

        $this->_send_json($result);
    }


    /**
    * Перенаправляет браузер на новый адрес
        *
        * @param array $params ассоциативный массив для формирования нового url
    */
    function _redirect($params)
    {
        $location = APP_HOST;
        $path = '';

        foreach ($params as $key => $value)
        {
            $path .= '/' . urlencode($value);
        }

        if($path == '/main/index')
        {
            $path = '/';
        }

        header('Location: ' . $location . $path);
        exit;
    }
}