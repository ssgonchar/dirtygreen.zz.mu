<?
    require_once(APP_PATH . 'classes/core/ControllerBase.class.php');

/**
 * Класс для управления запросами на отображение статических страниц
 */
class PrintController extends ControllerBase
{
    /**
     * Название шаблона контейнера.
     *
     * @var string
     */
    var $layout;

    /**
     * Имя подключаемого файла с яваскриптом.
     *
     * @var string
     */
    var $js = '';

    /**
     * Конструктор
     *
     * @param Smarty $smarty экземпляр объекта Smarty
     */
    function PrintController()
    {
        ControllerBase::ControllerBase();

        $this->controller_id = substr(strtolower(get_class($this)), 0, strpos(strtolower(get_class($this)), 'printcontroller'));

        $this->layout = 'print';
    }

    /**
     * Возвращает сгенерированный HTML-код на основе шаблона Smarty
     *
     * @param string $template имя шаблона
     * @return string результирующий HTML шаблона
     */
    function _fetch($template)
    {
        return $this->smarty->fetch($this->_get_print_template_file_name($template));
    }


    /**
     * Отправляет HTML в стандартный поток вывода
     *
     * @param string $template название шаблона для генерации HTML
     */
    function _display($template)
    {
        //if (!ob_start("ob_gzhandler", 2)) ob_start();
        ob_start();

        if (!isset($template))
        {
            header('Location: index.php');
            exit;
        }

        if ($template == "")
        {
            header('Location: index.php');
            exit;
        }

        // HTTP/1.0
        header("Content-type: text/html");
        header('Expires: Fri, 02 Jan 1970 00:00:00 GMT');
        header("Pragma: no-cache");
        // HTTP/1.1
        header("Cache-Control: no-store, no-cache, max-age=0, s-maxage=0, must-revalidate");

        $this->_assign('domen', $_SERVER['HTTP_HOST']);
        

        if ($this->layout != '')
        {
            $content = $this->smarty->fetch($this->_get_print_template_file_name($template));

            $this->_assign('content', $content);
            $this->smarty->display($this->_get_layout_template_file_name());
        }
        else
        {
            $this->smarty->display($this->_get_print_template_file_name($template));
        }
        
        
        ob_end_flush();
    }
}

