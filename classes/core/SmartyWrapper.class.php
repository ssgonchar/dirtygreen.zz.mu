<?php
/**
 * Класс-обертка для Smarty
 *
 * Синглтон
 *
 * @version 2008.11.23, zharkov
 * @version 2009.03.17, zharkov: убран идентификатор языка в пути к шаблонам
 * @version 2011.10.18, zharkov: алиас языка берется из запроса
 */
class SmartyWrapper
{   
    var $assigns = array();
    var $smarty;

    /**
     * Конструктор.
     * 
     */
    private function SmartyWrapper()
    {
        $this->smarty = new Smarty();
    }

    /**
     * Фабричный метод
     *
     * Создаёт экземпляр класса
     * 
     * @return SmartyWrapper
     */
    public static function & Create()
    {
            
        static $instance;

        if (!isset($instance))
        {
            $instance = new SmartyWrapper();
        }

        return $instance;
    }

    /**
     * Деструктор
     */
    function Destructor()
    {
    }

    /**
     * Инициализирует Smarty
     *
     *
     * @version 2009.03.17, zharkov: убран идентификатор языка в пути к шаблонам
     */
    public function Init()
    {
            
        $this->smarty->template_dir   = SMARTY_TEMPLATES_PATH . '/';
        $this->smarty->compile_dir    = SMARTY_COMPILED_PATH;

        $this->smarty->compile_check  = true;         // can be set to false to increase speed
        $this->smarty->caching        = false;    

         
        if (SMARTY_DEBUG == 'yes')
        {
            $this->smarty->debugging  = true;
        }        
    }

    /**
     * Возвращает путь к шаблонам
     *
     */
    public function get_template_dir()
    {
        $this->Init();
      
        return $this->smarty->template_dir;
    }
  
    /**
     * Определяет параметр смарти
     *
     * @param string $key имя параметра
     * @param string $value значение параметра
     */
    function assign($key, $value)
    {		
          
		$this->smarty->assign($key, $value);
    }

    /**
     * Отправляет HTML в стандартный поток вывода
     *
     * @param string $template название шаблона для генерации HTML
     * 
     * @version 2011.10.18, zharkov: алиас языка берется из запроса
     */
    function display($template)
    {
        $this->Init();
        //dg($template);
        $lang = Request::GetString('lang', $_REQUEST);
        $this->smarty->display($template, null, $lang);
    }

    /**
     * Возвращает сгенерированный HTML-код на основе шаблона Smarty
     *
     * @param string $template имя шаблона
     * @return string результирующий HTML шаблона
     * 
     * @version 2011.10.18, zharkov: алиас языка берется из запроса
     */
    function fetch($template)
    {
           
        $this->Init();

        $lang = Request::GetString('lang', $_REQUEST);
		//dg($this->smarty->fetch($template, null, $lang));
        return $this->smarty->fetch($template, null, $lang);
    }
    
    /**
    * Выывает метод smarty проверки существования шаблона
    * 
    * @param mixed $template
    */
    function template_exists($template)
    {
        return $this->smarty->templateExists($template);
    }
}
