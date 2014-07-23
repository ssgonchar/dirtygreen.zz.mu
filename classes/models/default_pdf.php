<?php
require_once APP_PATH . 'classes/models/attachment.class.php';

require_once APP_PATH . 'classes/services/tcpdf/config/lang/eng.php';
define("K_TCPDF_EXTERNAL_CONFIG", true);

require_once APP_PATH . 'classes/services/tcpdf/tcpdf.php';

// шрифт по-умолчанию длф всех генерируемых документов
define("TCPDF_DEFAULT_FONT", "helvetica");  // 'dejavusans'

/**
 * Класс для формирования PDF версии Sale Confirmation
 * 
 * @version 20120309, zharkov
 */ 
class DefaultPdf
{
    /**
    * Экземпляр класса TCPDF
    * 
    * @var mixed
    */
    var $pdf = null;
    
    /**
     * Ширина доступной области документа
     * 
     * @var mixed
     */
    var $width = 0;
    
    /**
     * Начальное значение координаты X
     * 
     * @var mixed
     */
    var $x0 = 0;
    
    /**
    * Максимальное значение координаты X
    * 
    * @var mixed
    */
    var $xmax = 0;
    
    /**
     * Стиль обычных линий
     * 
     * @var mixed
     */
    var $line_style = array(
        'width' => 0.5, 
        'cap'   => 'square', 
        'join'  => 'miter', 
        'dash'  => 0, 
        'color' => array(0, 0, 0)
    );
        
    /**
     * Стиль обычных жирных линий
     * 
     * @var mixed
     */
    var $line_style_b = array(
        'width' => 1.5, 
        'cap'   => 'butt', 
        'join'  => 'miter', 
        'dash'  => 0, 
        'color' => array(0, 0, 0)
    );
    
    /**
     * Основной шрифт документа
     * 
     * @var mixed
     */
    var $main_font = "";
    
    
    /**
     * Конструктор
     * 
     * @param mixed $owner_alias - алиас владельца, обычно 'mam' или 'pa'
     * @return DefaultPdf
     */
    function DefaultPdf($owner_alias, $config_name = 'default')
    {
        if ($config_name == 'marginless')
        {
            require_once APP_PATH . 'classes/services/tcpdf/config/tcpdf_config_marginless.php';
        }
        else if ($config_name == 'ddt')
        {
            require_once APP_PATH . 'classes/services/tcpdf/config/tcpdf_config_ddt.php';
        }
        else if ($config_name == 'cmr')
        {
            require_once APP_PATH . 'classes/services/tcpdf/config/tcpdf_config_cmr.php';
        }
        else
        {
            require_once APP_PATH . 'classes/services/tcpdf/config/tcpdf_config_alt.php';
        }
        
        $this->main_font    = TCPDF_DEFAULT_FONT;
        $this->x0           = PDF_MARGIN_LEFT;
    }
    
    /**
     * Формирует PDF
     * 
     * @param mixed $document_id
     */
    function Generate($document_id)
    {
        if (empty($this->pdf)) return null;
        
        // подключает настройки
        $this->_apply_settings();
        
        // формирует документ
        $this->_generate($document_id);
    }
    
    /**
     * Формирует документ
     * 
     * @param mixed $document_id
     */
    function _generate($document_id)
    {
        
    }
    
    /**
     * Устанавливает настройки документа
     * 
     */
    function _apply_settings()
    {
        $this->pdf->setPageUnit('px');

        // set document information
        $this->pdf->SetCreator(PDF_CREATOR);
        $this->pdf->SetAuthor('');
        $this->pdf->SetTitle('');
        $this->pdf->SetSubject('');
        $this->pdf->SetKeywords('');
    
        // external font
        //$this->main_font = $this->pdf->addTTFfont(K_PATH_FONTS . 'arial.ttf', 'TrueTypeUnicode');
    
        // set default header data
        //$this->pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE, PDF_HEADER_STRING);

        // set header and footer fonts
        $this->pdf->setHeaderFont(Array($this->main_font, '', 8));
        $this->pdf->setFooterFont(Array($this->main_font, 'I', 8));

        // set default monospaced font
        $this->pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

        //set margins
        $this->pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
        $this->pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
        $this->pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

        //set auto page breaks
        $this->pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

        //set image scale factor
        $this->pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

        //set some language-dependent strings
        global $l;
        $this->pdf->setLanguageArray($l);
        
        // set default font
        $this->_set_font();
        
        // установка максимального значения для координаты X
        $this->xmax = $this->pdf->getPageWidth() - PDF_MARGIN_RIGHT;
        
        // установка ширины доступной области
        $this->width = $this->pdf->getPageWidth() - PDF_MARGIN_RIGHT - PDF_MARGIN_LEFT;
    }
    
    /**
     * Устанавливает шрифт документа
     * 
     * @param mixed $style 
     * @param mixed $size
     */
    function _set_font($style = '', $size = 10)
    {
        $font = $this->main_font;
        
        if ($font == 'arial')
        {
            if (strpos($style, 'B') !== false)
            {
                if (strpos($style, 'I') !== false)
                {
                    $font = $font . 'bi';
                }
                else
                {
                    $font = $font . 'bd';
                }
            }            
            else if (strpos($style, 'I') !== false)
            {
                $font = $font . 'i';
            }            
        }
                
        $this->pdf->SetFont($font, $style, $size);
    }
    
    /**
     * Рисует линию
     * 
     * @param mixed $style стиль линии: 'bold' - жирная, '' - обычная (по умолчанию)
     * @param mixed $x_start
     * @param mixed $x_end
     */
    function _draw_line($style = '', $x_start = 0, $x_end = 0)
    {
        $x_start    = empty($x_start) ? $this->x0 : $x_start;
        $x_end      = empty($x_end) ? $this->xmax : $x_end;
        
        $style      = $style == 'B' ? $this->line_style_b : $this->line_style;
        
        $this->pdf->Line($x_start, $this->pdf->GetY(), $x_end, $this->pdf->GetY(), $style);
    }
    
    /**
     * Рисует объект "имя и значние" используется в Attention документов
     * 
     * @param mixed $name
     * @param mixed $value
     */
    function _draw_name_and_value($name, $value, $settings = array())
    {
        // имя
        $font_style = $this->_get_setting($settings, 'font-style', 'I', 'name');
        $font_size  = $this->_get_setting($settings, 'font-size', 10, 'name');
        $this->_set_font($font_style, $font_size); 
        
        $width          = $this->_get_setting($settings, 'width', 100, 'name');
        $align          = $this->_get_setting($settings, 'align', 'L', 'name');
        $this->pdf->MultiCell($width, 0, $name, 0, $align, false, 0);
        
        // получает высоту блока Name используется для того чтобы правильно сместить по вертикали курсор после отрисовки блока Value
        // бывает так что Name образует две строки
        $height = $this->pdf->getLastH();
        
        
        // разделитель
        $font_style = $this->_get_setting($settings, 'font-style', '', 'delimeter');
        $font_size  = $this->_get_setting($settings, 'font-size', 10, 'delimeter');        
        $this->_set_font($font_style, $font_size); 
        
        $width  = $this->_get_setting($settings, 'width', 10, 'delimeter');
        $align  = $this->_get_setting($settings, 'align', 'C', 'delimeter');
        $sign   = $this->_get_setting($settings, 'sign', ':', 'delimeter');
        $this->pdf->MultiCell($width, 0, $sign, 0, $align, false, 0);

        
        // значение
        $font_style = $this->_get_setting($settings, 'font-style', '', 'value');
        $font_size  = $this->_get_setting($settings, 'font-size', 10, 'value');        
        $this->_set_font($font_style, $font_size); 

        $width  = $this->_get_setting($settings, 'width', 0, 'value');
        $align  = $this->_get_setting($settings, 'align', 'L', 'value');
        $border = $this->_get_setting($settings, 'border', 0, 'value');
        $this->pdf->MultiCell($width, $height, $value, $border, $align, false, 1);        
    }
    
    /**
     * Рисует таблицу
     * 
     * @param mixed $rowset набор данных
     * @param mixed $header массив заголовков в виде 'name' => 'width' ширина в %
     * @param mixed $keys ключи для набора данных
     */
    function _draw_simple_table($rowset, $columns, $draw_header = true)
    {
        // устанавливает ширину столбцов таблицы
        $defined_width  = 0;
        $undefined_qtty = 0;
        
        foreach ($columns as $key => $row)
        {
            if (isset($row['width']) && $row['width'] > 0) 
            {
                $columns[$key]['width'] = $this->width * $row['width'] / 100;
                $defined_width  += $this->width * $row['width'] / 100;
            }
            else
            {
                $undefined_qtty++;
            }
        }

        $footer         = array();
        $footer_span    = true;
        $show_footer    = false;
        foreach ($columns as $key => $row)
        {
            if (!isset($row['width']) || empty($row['width'])) 
            {
                $columns[$key]['width'] = ($this->width - $defined_width) / $undefined_qtty;
            }
            
            if (isset($row['total']))
            {
                $show_footer            = true;
                $footer_span            = false;
                $footer[$row['field']]  = array(
                    'text' => 0,
                    'width' => $columns[$key]['width'],
                    'precision' => Request::GetInteger('precision', $row['total']),
                );
            }
            else
            {
                if (empty($footer)) $footer['total'] = array('text' => 'Total', 'width' => 0);
                
                if ($footer_span)
                {
                    $footer['total']['width'] += $columns[$key]['width'];
                }
                else
                {
                    $footer[$row['field']] = array('text' => '', 'width' => $columns[$key]['width']);
                }
            }
        }

        $this->pdf->SetLineWidth(0.3);
        
        
        // рисует заголовок таблицы
        if ($draw_header)
        {
            $this->_draw_table_header($columns);
        }

        
        // рисует данные таблицы        
        $height = 25;
        $align  = 'C';
        $border = 1;
        
        $this->pdf->SetCellPadding(2);
        $this->_set_font('', 8);
        
        foreach ($rowset as $row)
        {
            // формирование заголовков для тех строк таблицы, которые перенесены на следующую страницу
            if ($draw_header && $this->pdf->checkPageBreak($height, '', false))
            {
                $this->pdf->AddPage();
                $this->_draw_table_header($columns);
            }
            
            foreach ($columns as $column)
            {                
                $text = '';
                if (isset($row[$column['field']]))
                {
                    $text = $row[$column['field']];
                    if (isset($column['total'])) $footer[$column['field']]['text'] += $text;
                }
                
                $text = $this->_get_table_cell_text($row, $column);
                
                $this->pdf->MultiCell($column['width'], $height, $text, $border, $align, false, 0, '', '',true, 0, false, true, $height, 'M');
            }
            
            $this->pdf->Ln();
        }
        
        // рисует футер        
        if ($show_footer)
        {
            $this->_set_font('B', 8);
            $height = 25;
            $align  = 'C';
            $border = 1;        
            
            foreach ($footer as $row) 
            {
                $row['text'] = isset($row['precision']) ? sprintf('%.' . $row['precision'] .'f', round($row['text'], $row['precision'])) : $row['text'];
                $this->pdf->MultiCell($row['width'], $height, $row['text'], $border, $align, false, 0, '', '',true, 0, false, true, $height, 'M');
            }
            
            $this->pdf->Ln();
        }
    }
    
    /**
     * Сохраняет аттачмент
     * 
     * @param mixed $obj_alias
     * @param mixed $obj_id
     * @param mixed $file_name
     * @param mixed $remove_previous - флаг, указывающий что нужно удалить предыдущую версию файла
     */
    function _save_attachment($obj_alias, $obj_id, $file_name)
    {
        $attachments = new Attachment();        
        return $attachments->CreateFromFile($obj_alias, $obj_id, $file_name);
    }
    
    /**
     * Рисует таблицу
     * 
     * @param mixed $rowset
     * @param mixed $settings
     * 
     * @version 20120813, zharkov: рисует таблицу на основании настроек
     */
    function _draw_table($rowset, $columns, $settings)
    {
        // устанавливает ширину столбцов таблицы
        $defined_width  = 0;
        $undefined_qtty = 0;
//dg($rowset);
        foreach ($columns as $key => $row)
        {
            if (isset($row['width']) && $row['width'] > 0) 
            {
                $width = $this->width * $row['width'] / 100;
                
                $columns[$key]['width'] = $width;
                $defined_width          += $width;
            }
            else
            {
                $undefined_qtty++;
            }
        }

        $footer         = array();
        $show_footer    = false;
        $footer_span    = true;
        foreach ($columns as $key => $row)
        {
            if (!isset($row['width']) || empty($row['width'])) 
            {
                $columns[$key]['width'] = ($this->width - $defined_width) / $undefined_qtty;
            }
            
            if (isset($row['total']))
            {
                $show_footer    = true;
                $footer_span    = false;
                
                $footer[$row['field']] = array('text' => 0, 'width' => $columns[$key]['width']);
            }
            else
            {
                if (empty($footer)) $footer['total'] = array('text' => 'Total', 'width' => 0);
                
                if ($footer_span)
                {
                    $footer['total']['width'] += $columns[$key]['width'];
                }
                else
                {
                    $align = isset($columns[$key]['align']) ? $columns[$key]['align'] : 'C';
                    $footer[$row['field']] = array('text' => '', 'width' => $columns[$key]['width'], 'align' => $align);
                }
            }
        }

        
        // проверка того, чтобы на странице не оказался только хедер таблицы
        // вычисляется высота хедера и высота N строк таблицы и если они не помещаются на странице, добавляется новая страница
        $min_table_height = $this->_get_settings('header', 'height', $settings, 25);
        foreach ($columns as $row) 
        {
            $calc_height        = $this->pdf->getStringHeight($row['width'], $row['title']);
            $min_table_height   = max($min_table_height, $calc_height);
        }        
        
        $min_rows_per_page  = $this->_get_settings('table', 'min_rows_per_page', $settings, 1);
        $rows_height        = 0;
        
        if (count($rowset) >= $min_rows_per_page)
        {
            for ($i = 0; $i < $min_rows_per_page; $i++)
            {
                $row        = $rowset[$i];
                $row_height = $this->_get_settings('row', 'height', $settings, 25);
                
                foreach ($columns as $column)
                {
                    $text           = $this->_get_table_cell_text($row, $column);
                    $calc_height    = $this->pdf->getStringHeight($column['width'], $text);
                    
                    $row_height     = max($row_height, $calc_height);
                }
                
                $min_table_height += $row_height;
            }            
        }
        
        $this->pdf->checkPageBreak($min_table_height);
        
        
        // рисует заголовок таблицы
        $this->_draw_table_header($columns, $settings);

        
        // рисует данные таблицы
        $line_width = $this->_get_settings('row', 'border-width', $settings, 0.3);
        $this->pdf->SetLineWidth($line_width);
        
        $font_style = $this->_get_settings('row', 'font-style', $settings, '');
        $font_size  = $this->_get_settings('row', 'font-size', $settings, 8);        
        $border     = $this->_get_settings('row', 'border', $settings, 1);        
        $padding    = $this->_get_settings('footer', 'padding', $settings, 2);

        $this->_set_font($font_style, $font_size);
        $this->pdf->SetCellPadding($padding);
        
        foreach ($rowset as $row_key => $row)
        {
            $row_height = $this->_get_settings('row', 'height', $settings, 25);

            // форматирует значение поля, тотал, расчитывает высоту строки
            foreach ($columns as $column)
            {
                // текст ячейки
                $text = $this->_get_table_cell_text($row, $column);

                // формирует тотал по столбцу
                if (isset($column['total'])) $footer[$column['field']]['text'] += floatval($text);

                // расчитывает реальную высоту строки
                $calc_height    = $this->pdf->getStringHeight($column['width'], $text);
                $row_height     = max($row_height, $calc_height);
                
                $row[$column['field']] = $text;
            }            
            
            // формирование заголовков при переносе на другую страницу
            if ($this->pdf->checkPageBreak($row_height, '', false))
            {
                $this->pdf->AddPage();
                $this->_draw_table_header($columns, $settings);
                
                $this->_set_font($font_style, $font_size);
                $this->pdf->SetLineWidth($line_width);
            }
            
            // установка цвета заливки
            $fill = false;
            if (array_key_exists('background_color', $row) && !empty($row['background_color']))
            {
                $background_color = $row['background_color'];
                $fill   = $this->_get_settings('row', 'fill', $settings, false);
                
                if ($fill)
                {
                    $red    = isset($background_color['red']) ? (int)$background_color['red'] : 255;
                    $green  = isset($background_color['green']) ? (int)$background_color['green'] : 255;
                    $blue   = isset($background_color['blue']) ? (int)$background_color['blue'] : 255;
                    
                    $this->pdf->SetFillColor($red, $green, $blue);
                }
            }

            // рисование строки
            foreach ($columns as $column)
            {
                $align  = isset($column['align']) && !empty($column['align']) ? $column['align'] : 'C';
                $this->pdf->MultiCell($column['width'], $row_height, $row[$column['field']], $border, $align, $fill, 0, '', '',true, 0, false, true, $row_height, 'M');
            }
            
            $this->pdf->Ln();
        }
        
        
        // рисует футер
        if ($show_footer)
        {
            $line_width = $this->_get_settings('footer', 'border-width', $settings, 0.3);
            $this->pdf->SetLineWidth($line_width);
            
            $font_style = $this->_get_settings('footer', 'font-style', $settings, '');
            $font_size  = $this->_get_settings('footer', 'font-size', $settings, 8);
            $this->_set_font($font_style, $font_size);
            
            $height     = $this->_get_settings('footer', 'height', $settings, 25);
            $border     = $this->_get_settings('footer', 'border', $settings, 1);
            
            $padding    = $this->_get_settings('footer', 'padding', $settings, 2);

            $this->pdf->SetCellPadding($padding);
            
            foreach ($footer as $row) 
            {
                $align  = isset($row['align']) && !empty($row['align']) ? $row['align'] : 'C';
                $this->pdf->MultiCell($row['width'], $height, $row['text'], $border, $align, false, 0, '', '',true, 0, false, true, $height, 'M');
            }
            
            $this->pdf->Ln();            
        }
    }    
    
    /**
     * Возвращает параметр настроек
     * 
     * @param mixed $param
     * @param mixed $settings
     * @param mixed $default
     * @return mixed
     * 
     * @version 20120813, zharkov
     */
    function _get_settings($branch, $param, $settings, $default)
    {        
        if (isset($settings[$branch]) && isset($settings[$branch]['normal']) && isset($settings[$branch]['normal'][$param]))
        {
            return $settings[$branch]['normal'][$param];
        }
        
        return $default;
    }
    
    /**
     * Вовзвращает параметр из массива настроек
     * 
     * @param mixed $settings
     * @param mixed $param
     * @param mixed $default
     * @param mixed $tree
     * @param mixed $branch
     * @return mixed
     * 
     * @version 20120813, zharkov
     */
    function _get_setting($settings, $param, $default, $tree = null, $branch = null)
    {
        if (isset($tree) && isset($settings[$tree]))
        {
            $settings = $settings[$tree];
            
            if (isset($branch) && isset($settings[$branch]))
            {
                $settings = $settings[$branch];
            }
        }
        
        if (isset($settings[$param]))
        {
            return $settings[$param];
        }
        
        return $default;
    }
    
    /**
     * Рисует отступ на основании $this->pdf->Cell<br />
     * If automatic page breaking is enabled and the cell goes beyond the limit, a page break is done before outputting.
     * 
     * @param float $cell_width Cell width. If 0, the cell extends up to the right margin.
     * @param float $cell_height float Cell height. Default value: 0.
     * 
     * @deprecated 20130309, zharkov
     */
    private function deprecated_draw_cell_indent($cell_width = null, $cell_height = null)
    {
        $w      = !isset($cell_width) ? 1 : $cell_width;
        $h      = !isset($cell_height) ? 0 : $cell_height;
        $txt    ='';
        $border = 0;
        
        $this->pdf->Cell($w, $h, $txt, $border);
    }
    
    /**
     * Рисует заголовол таблицы
     * @param array $columns
     * @param array $params [optional]
     */
    private function _draw_table_header($columns, $settings = array())
    {
        $line_width = $this->_get_settings('header', 'border-width', $settings, 0.3);       
        $font_style = $this->_get_settings('header', 'font-style', $settings, 'B');
        $font_size  = $this->_get_settings('header', 'font-size', $settings, 8);
        $height     = $this->_get_settings('header', 'height', $settings, 25);
        $border     = $this->_get_settings('header', 'border', $settings, 1);        
        $padding    = $this->_get_settings('header', 'padding', $settings, 2);        
        $bgcolor    = $this->_get_settings('header', 'bgcolor', $settings, '');
        $bgcolor    = str_replace('#', '', str_replace(';', '', $bgcolor));
        $fill       = false;
        
        $this->pdf->SetLineWidth($line_width);
        $this->_set_font($font_style, $font_size);

        if (!empty($bgcolor))
        {
            if (strlen($bgcolor) == 3)
            {
                $red        = substr($bgcolor, 0, 1);
                $green      = substr($bgcolor, 1, 1);
                $blue       = substr($bgcolor, 2, 1);
                
                $bgcolor    = $red . $red . $green . $green . $blue . $blue;
            }

            $red    = (int)hexdec(substr($bgcolor, 0, 2));
            $green  = (int)hexdec(substr($bgcolor, 2, 2));
            $blue   = (int)hexdec(substr($bgcolor, 4, 2));
            
            $this->pdf->SetFillColor($red, $green, $blue);
            
            $fill   = true;
        }

        $this->pdf->SetCellPadding($padding);
        
        // расчитывает реальную высоту строки
        foreach ($columns as $row) 
        {
            $calculate_height = $this->pdf->getStringHeight($row['width'], $row['title']);
            $height = max($height, $calculate_height);
        }        
        
        // рисует строку
        foreach ($columns as $row) 
        {
            $align = isset($row['align']) && !empty($row['align']) ? $row['align'] : 'C';
            $this->pdf->MultiCell($row['width'], $height, $row['title'], $border, $align, $fill, 0, '', '',true, 0, false, true, $height, 'M');
        }        

        $this->pdf->Ln();
    }
    
    /**
     * Возвращает отформатированный текст столбца строки таблицы
     * 
     * @param $row - массив, строка с данными таблицы
     * @param $column - массив, параметры столбца таблицы
     * 
     * @version 20130309, zharkov
     */
    private function _get_table_cell_text($row, $column)
    {
        $text = '';
        
        if (isset($row[$column['field']]))
        {
            $text = $row[$column['field']];

            if (isset($column['format']))
            {
                $format = $column['format'];
                
                if ($format == 'round')
                {
                    $text = round($text);
                }
                else if ($format == 'number')
                {
                    $text = number_format($text, 0, '.', ',');
                }
                else if ($format == 'number1' || $format == 'number1c')
                {
                    $text = number_format($text, 1, '.', ',');
                }
                else if ($format == 'number2' || $format == 'number2c')
                {
                    $text = number_format($text, 2, '.', ',');
                }
                else if ($format == 'number3' || $format == 'number3c')
                {
                    $text = number_format($text, 3, '.', ',');
                }                
                
                if ($format == 'number1c' || $format == 'number2c' || $format == 'number3c')
                {
                    $arr    = explode('.', $text);
                    $text   = (isset($arr[1]) && $arr[1] > 0 ? $text : $arr[0]);                        
                }
            }            
        }

        if ((empty($text) || (is_numeric(str_replace(',', '', $text)) && floatval($text) == 0)) && isset($column['default']))
        {
            $text = $column['default'];
        }
        else 
        {
            if (isset($column['prefix_field']) && !empty($column['prefix_field']) && isset($row[$column['prefix_field']]))
            {
                $text = $row[$column['prefix_field']] . ' ' . $text;
            }
            else if (isset($column['prefix']) && !empty($column['prefix']))
            {
                $text = $column['prefix'] . ' ' . $text;
            }

            if (isset($column['suffix_field']) && !empty($column['suffix_field']) && isset($row[$column['suffix_field']]))
            {
                $text = $text . ' ' . $row[$column['suffix_field']];
            }
            else if (isset($column['suffix']) && !empty($column['suffix']))
            {
                $text = $text . ' ' . $column['suffix'];
            }
        }

        return $text;
    }
}