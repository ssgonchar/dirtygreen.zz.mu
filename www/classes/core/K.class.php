<?php

require_once(APP_PATH . 'classes/models/constants.class.php');

/**
 * Класс для обслуживания лога событий приложения
 *
 * Синглтон
 *
 * @version: 2009.03.17, Mr. Bono
 */
class K
{   
    /**
     * Конструктор.
     * 
     */
    private function K()
    {
    }

    /**
     * Фабричный метод
     *
     * Создаёт экземпляр класса
     */
    public static function & Create()
    {
        static $instance;

        if (!isset($instance))
        {
            $instance = new K();
        }

        return $instance;
    }

    /**
     * Деструктор
     */
    function Destructor()
    {
    }

    public static function Get($alias, $param1 = null, $param2 = null, $param3 = null, $param4 = null, $param5 = null, $param6 = null, $param7 = null, $param8 = null, $param9 = null)
    {        
        $constants = K::get_constants();        
        
        if (array_key_exists($alias, $constants))
        {
            $constants = $constants[$alias];

            if (array_key_exists(Lang::GetLang(), $constants))
            {
                $constant = stripslashes($constants[Lang::GetLang()]);
                
                /*  надо определиться оставляем это или нет
                
                $constant = str_replace('<br>', "\n", $constant);
                
                */
                
                return sprintf($constant, $param1, $param2, $param3, $param4, $param5, $param6, $param7, $param8, $param9);            
            }
            else
            {
                $alias;
            }
        }
        else
        {
            return $alias;
        }    
    }

    private static function get_constants()
    {
        if (!file_exists(APP_CACHE . 'app_constants.php'))
        {
            K::put_constants_to_cache();
        }

        require_once(APP_CACHE . 'app_constants.php');

        global $__app_constants;
        return $__app_constants;
    }
    
    public static function put_constants_to_cache()
    {

        $fp = fopen(APP_CACHE . 'app_constants.php', 'wb');

        fwrite($fp, "<?\n");
        fwrite($fp, "\n\t" . 'global $__app_constants;' . "\n");
        fwrite($fp, "\n\t" . '$__app_constants = array(');
        
        $constants  = new Constants();
        $list       = $constants->GetListForCache();

        $alias      = '';

        if (!empty($list))
        {
            for ($i = 0; $i < count($list); $i++)
            {
                $row = $list[$i];

                if ($alias != $row['alias'])
                {
                    if (!empty($alias))
                    {
                        fwrite($fp, ')' . ($i < count($list) - 1 ? ',' : ','));                
                    }

                    fwrite($fp, "\n\t\t" . '\'' . $row['alias'] . '\' => array(');
                }
                else
                {
                    fwrite($fp, ', ');                
                }

                fwrite($fp, '\'' . $row['lang'] . '\' => \'' . addslashes($row['value']) . '\'');    

                $alias = $row['alias'];
            }

            fwrite($fp, ')' . "\n\t". ');' . "\n");        
        }
        else
        {
            fwrite($fp, ');' . "\n");
        }
        
        fwrite($fp, "\n" . '?>');
        fclose($fp);

    }
}

?>