<?php

/**
 * Класс-пагинация
 *
 * Используется для составления списка страниц.
 *
 * Примеры вывода страниц при $max_pages=5. 
 * Обозначения (для примера)
 * > - следующая страница
 * >> - последняя страница
 * < - предидущая страница
 * << - первая страница
 * (N) - возможная выбранная страница
 *
 * "" - страница одна или нет записей
 * "(1) (2) (3)" 
 * "(1) (2) (3) 4 5 > >>"
 * "<< < 3 4 (5) (6) (7)"
 * "<< < 3 4 (5) 6 7 > >>"
 *
 * Синглтон
 *
 * @version 2008.11.18 digi
 */
class Pagination
{
    /**
     * число записей на страницу
     *
     * @var integer
     */
    var $max_on_page;

    /**
     * показываемое число страниц
     *
     * @var integer
     */
    var $max_pages;
    
    /**
     * Конструктор.
     *
     * @param integer $max_on_page число записей на странице
     * @param integer $max_pages число страниц
     */
    function Pagination($max_on_page = ITEMS_PER_PAGE, $max_pages = 11) //константа = 20, макс. кол-во страниц = 11
    {
        $this->max_on_page = $max_on_page;
        $this->max_pages = $max_pages % 2 == 0 ? $max_pages + 1 : $max_pages;
    }

    /**
     * Подготавливает массив списка страниц
     *
     * Возвращает подготовленный список страниц в формате ...
     *
     * @param integer $start номер страницы. Этот параметр начинается с 1!
     * @param integer $count кол-во найденных записей
     * @return array
     */
    function PreparePages($start, $count)
    { 
        $start = $start > 0 ? $start - 1 : 0;
        $result = array();
        $count_pages = ( $count / $this->max_on_page ); //кол-во страниц = кол-во сообщ/20
        if( $count_pages > 1 )  //если кол-во стр > 1
        {
            $xBeg_page = 0;             //начальная страница = 0
            $xEnd_page = $count_pages;  //последняя = кол-ву страниц
            
            if ($count_pages > $this->max_pages)    //если кол-во страниц больше показываемого числа страниц
            {
                $xBeg_page = $start - ($this->max_pages - 1) / 2 ;  //номер стр - показываемое число страниц-1/2
                if ($xBeg_page < 0) $xBeg_page = 0;
                $xEnd_page = $xBeg_page + $this->max_pages; //номер последней = начальная + показываемое число страниц (11)
                if ($xEnd_page > $count_pages) $xEnd_page = $count_pages;
            }
            
            if ($start > 0)
            {
                $result['prev'] = $start;
            }
            else
            {
                $result['prev'] = '';
            }

            if ($xBeg_page > 0)
            {
                $result['prevs'] = $xBeg_page;
                $result['first'] = $xBeg_page != 0 ? 1 : '';

                if ($result['first'] == $xBeg_page)
                {
                    $result['prevs'] = '';
                }
            }
            else
            {
                $result['prevs'] = '';
                $result['first'] = '';
            }

            $pages = array();
            for ($i = $xBeg_page; $i < $xEnd_page; $i++)
            {
                if ($start != $i)
                {
                    $pages[] = array('active' => false, 'number' => $i + 1);
                }
                else
                {
                    $pages[] = array('active' => true, 'number' => $i + 1);
                }
            }
            $result['pages'] = $pages;
            

            if ($xEnd_page < $count_pages)
            {
                $result['nexts'] = $xEnd_page + 1;
                $result['last'] = ((int)($count_pages) == $count_pages) ? $count_pages : (int)($count_pages) + 1;
                if ($result['last'] == $xEnd_page + 1)
                {
                    $result['nexts'] = '';
                }
            }
            else
            {
                $result['nexts'] = '';
                $result['last'] = '';
            }

            $x_last = ((int)($count_pages) == $count_pages) ? $count_pages : (int)($count_pages) + 1;
            if ($start < $x_last - 1)
            {
                $result['next'] = $start + 2;
            }
            else
            {
                $result['next'] = '';
            }


        }
        return $result;
    }

}
