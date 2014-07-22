<?php

/**
 * Класс-таймер
 *
 * Используется для определения времени выполнения процессов.
 *
 * Синглтон
 */
class Timer
{
    /**
     * Стек таймеров
     *
     * @var array
     */
    var $startTimes;
	
	/**
	 * Конструктор.
	 */
	private function Timer()
	{
	    $this->startTimes = array();
	}

	/**
	 * Запускает новый таймер и добавляет его в стек
     *
     * Являясь фабричным методом, возвращает экземпляр класса.
     *
     * @return Timer
	 */
    public static function Start()
    {
		static $instance;
		if (!isset($instance))
		{
		    $instance = new Timer();
		}

		$time = $instance->GetMicrotime();
        array_push($instance->startTimes, $time);

        return $instance;
    }

    /**
     * Возращает время запуска первого таймера
     *
     * Время измеряется в секундах Unix Epoch, дробная часть - миллисекунды
     *
     * @return float
     */
    function StartedAt()
    {
	    return $this->startTimes[0];
    }

    /**
     * Возращает текущее время последнего запущенного таймера
     *
     * Время измеряется в секундах Unix Epoch, дробная часть - миллисекунды
     *
     * @return float
     */
    function Current()
    {
        return $this->GetMicrotime() - $this->startTimes[count($this->startTimes) - 1];
    }

    /**
     * Останавливает последний текущий таймер, вынимает его из стека и возвращает измеренное время
     *
     * Время измеряется в секундах Unix Epoch, дробная часть - миллисекунды
     *
     * @return float
     */
    function Stop()
    {
        return $this->GetMicrotime() - array_pop($this->startTimes);
    }

    /**
     * Получает текущее системное время
     *
     * Время измеряется в секундах Unix Epoch, дробная часть - миллисекунды
     *
     * @return float
     */
    function GetMicrotime()
    { 
    	list($usec, $sec) = explode(" ", microtime()); 
    	return ((float)$usec + (float)$sec); 
    }
}
?>