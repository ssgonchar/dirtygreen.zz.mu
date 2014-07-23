<?php
require_once APP_PATH . 'classes/models/default_pdf.php';
require_once APP_PATH . 'classes/models/stockoffer.class.php';

/**
 * Класс для формирования PDF версии Stock Offer
 * 
 * @version 20130301, d10n
 */
class StockOfferPdf extends DefaultPdf
{
    /**
     * Object
     * @var CMRPdfForSE 
     */
    public $pdf;
    /**
     * Конструктор
     * 
     * @param mixed $owner_alias - алиас владелеца, обычно 'mam' или 'pa'
     * @return QCPdf
     */
    function StockOfferPdf($owner_alias = 'mam', $config_name = 'marginless')
    {
        DefaultPdf::DefaultPdf($owner_alias, $config_name);
        
        $this->pdf = new StockOfferPdfForSE();
    }
    
    /**
     * Формирует документ
     * 
     * @param mixed $document_id
     */
    function _generate($document_id)
    {
        $modelStockOffer    = new StockOffer();
        $stockoffer         = $modelStockOffer->GetForPDF($document_id);
        
        if (empty($stockoffer)) return null;

        $this->pdf->document    = $stockoffer;
        $this->pdf->draw_footer = true;
        
        $margin_left    = 60;
        $margin_top     = 30;
        $margin_right   = 30;
        
        $font_size      = 8;

        $this->pdf->SetMargins($margin_left, $margin_top, $margin_right, true);
        
        // футер начинается с -40 по высоте
        $this->pdf->SetAutoPageBreak(true, 50);
        
        $this->width = $this->pdf->getPageWidth() - $margin_right - $margin_left;
        
        $this->pdf->AddPage();
        $this->_set_font('', $font_size);
        
        
        if (isset($this->pdf->document['header_attachment']))
        {
            $this->pdf->Image($this->pdf->document['header_attachment']['src'], $margin_left, $margin_top, $this->width, 0, '', '', 'N');
        }
        
        $this->pdf->Ln(10);
        
        $nv_settings = array(
            'name'      => array('width' => 100, 'font-style' => 'B', 'font-size' => $font_size),
            'delimeter' => array('font-size' => $font_size),
            'value'     => array('width' => 330, 'font-style' => '', 'font-size' => $font_size),
        );
        
        $top_params = false;
        
        if (!empty($this->pdf->document['delivery_point']))
        {
            $this->_draw_name_and_value('Delivery Point', $this->pdf->document['delivery_point'], $nv_settings);
            
            $top_params = true;
            $this->pdf->Ln(5);
        }
        if (!empty($this->pdf->document['delivery_cost']))
        {
            $this->_draw_name_and_value('Delivery Cost', $this->pdf->document['delivery_cost'], $nv_settings);
            
            $top_params = true;
            $this->pdf->Ln(5);
        }
        if (!empty($this->pdf->document['delivery_time']))
        {
            $this->_draw_name_and_value('Delivery Time', $this->pdf->document['delivery_time'], $nv_settings);
            
            $top_params = true;
            $this->pdf->Ln(5);
        }
        if (!empty($this->pdf->document['payment_terms']))
        {
            $this->_draw_name_and_value('Payment Terms', $this->pdf->document['payment_terms'], $nv_settings);
            
            $top_params = true;
            $this->pdf->Ln(5);
        }
        if (!empty($this->pdf->document['quality_certificate']))
        {
            $this->_draw_name_and_value('Quality Certificate', $this->pdf->document['quality_certificate'], $nv_settings);
            
            $bottom_params = true;
            $this->pdf->Ln(5);
        }
        if ($top_params) $this->pdf->Ln(15);
        
        
        $positions      = $stockoffer['positions'];
        $positions_list = $positions['positions_list'];
//dg($positions);
        $columns = array(
            array('title' => 'Steel Grade',                                 'field' => 'steelgrade', 'width' => 10),
            array('title' => "Thick,\n" . $positions['dimension_unit'],     'field' => 'thickness', 'format' => 'number2c'),
            array('title' => "Width,\n" . $positions['dimension_unit'],     'field' => 'width'),
            array('title' => "Length,\n" . $positions['dimension_unit'],    'field' => 'length'),
        );
        
        // расширение доп столбцами
        if (!empty($stockoffer['columns']))
        {
            $exploded_columns   = explode(',', $stockoffer['columns']);
            $weight_unit        = $positions['weight_unit'] == 'mt' ? 'Ton' : $positions['weight_unit'];
            $price_unit         = $positions['price_unit'] == 'mt' ? 'Ton' : $positions['price_unit'];
            $currency           = $positions['currency'];
            $currency           = $currency == 'usd' ? '$' : ($currency == 'eur' ? '€' : $currency);
            
            foreach ($exploded_columns as $column_name)
            {
                switch ($column_name)
                {
                    case 'unitweight':
                       array_push($columns, array('title' => "Unit Weight,\n" . $weight_unit, 'field' => $column_name));
                        break;
                    
                    case 'qtty':
                        array_push($columns, array('title' => "Qtty,\npcs", 'field' => $column_name, 'width' => 5));
                        break;
                    case 'weight':
                        array_push($columns, array('title' => "Weight,\n" . $weight_unit, 'field' => $column_name));
                        break;
                    
                    case 'price':
                        array_push($columns, array('title' => "Price,\n" . $currency . '/' . $price_unit, 'field' => $column_name));
                        break;
                    
                    case 'value':
                        array_push($columns, array('title' => "Value,\n" . $currency, 'field' => $column_name));
                        break;
                    
                    case 'notes':
                        array_push($columns, array('title' => "Notes" , 'field' => $column_name));
                        break;
                    
                    case 'internal_notes':
                        array_push($columns, array('title' => "Internal Notes", 'field' => $column_name));
                        break;
                    
                    case 'delivery_time':
                        array_push($columns, array('title' => "Delivery Time", 'field' => $column_name));
                        break;
                    
                    case 'location':
                        array_push($columns, array('title' => "Location", 'field' => $column_name));
                        break;

                    case 'iwish':
                        array_push($columns, array('title' => "I Wish", 'field' => $column_name));
                        break;                        
                }
            }
        }
        
        $table_settings = array(
            'header'    => array('normal'=> array('font-size' => 7, 'height' => 15, 'border-width' => 0.3, 'bgcolor' => '#DDD;')),
            'row'       => array('normal'=> array('font-size' => 7, 'height' => 15, 'border-width' => 0.3, 'fill' => true))
        );
        
        $this->_draw_table($positions_list, $columns, $table_settings);
        $this->pdf->Ln(20);
        
        $bottom_params = false;
        /*
        if (!empty($this->pdf->document['quality_certificate']))
        {
            $this->_draw_name_and_value('Quality Certificate', $this->pdf->document['quality_certificate'], $nv_settings);
            
            $bottom_params = true;
            $this->pdf->Ln(5);
        }
		*/
        if (!empty($this->pdf->document['validity']))
        {
            $this->_draw_name_and_value('Validity', $this->pdf->document['validity'], $nv_settings);
            
            $bottom_params = true;
            $this->pdf->Ln(5);
        }
        
        if ($bottom_params) $this->pdf->Ln(15);


        if (!empty($this->pdf->document['description']))
        {
/*            
            $text = preg_replace('@(\r\n|\r)@', "\n", $this->pdf->document['description']);
            $text = str_replace('<br>', "\n", $text);
            $text = str_replace('<br />', "\n", $text);
*/            
            $this->pdf->MultiCell(0, 0, $this->pdf->document['description'], 0, 'L');
            $this->pdf->Ln(20);
        }


        // banner1
        if (isset($this->pdf->document['banner1_attachment']))
        {
            $this->pdf->Image($this->pdf->document['banner1_attachment']['src'], $margin_left, $this->pdf->GetY(), $this->width, 0, '', '', 'N');
        }
        
        // banner2
        if (isset($this->pdf->document['banner2_attachment']))
        {
            $this->pdf->Ln(10);
            $this->pdf->Image($this->pdf->document['banner2_attachment']['src'], $margin_left, $this->pdf->GetY(), $this->width, 0, '', '', 'N');
        }
        
        // footer
        if (isset($this->pdf->document['footer_attachment']))
        {
            $this->pdf->Ln(10);
            $this->pdf->Image($this->pdf->document['footer_attachment']['src'], $margin_left, $this->pdf->GetY(), $this->width, 0, '', '', 'N');
        }
        
        
        // сохраняет документ в cache
        $file_name  = $stockoffer['doc_no'];
        $file_name  = preg_replace('/[^a-zA-Z0-9_-]/', '_', Translit::Encode($file_name)) . '.pdf';
        $file_name  = APP_CACHE . $file_name;
        $this->pdf->Output($file_name, 'F');
        
        // сохраняем атачмент в бд
        $attachment_id = $this->_save_attachment('stockoffer', $stockoffer['id'], $file_name);
        
        // обновляет связь сертификата с созданным пдф
        $modelStockOffer->UpdateAttachment($stockoffer['id'], $attachment_id);
    }
}


/**
 * Класс, перегружающий методы базового класса для
 * формирования хедэра и футера с картинками SteelEmotion и адресом MaM UK
 */
class StockOfferPdfForSE extends TCPDF
{
    public $draw_header = false;
    public $draw_footer = false;
    
    public $document;
    
    //Page header
    public function Header() 
    {
        if (!$this->draw_header) return false;
    }

    // Page footer
    public function Footer() 
    {
        if (!$this->draw_footer) return false;
        
        // Position at 40 px from bottom
        $this->SetY(-40);
        
        // Set font
        $this->SetFont(TCPDF_DEFAULT_FONT, '', 8);
        $this->SetTextColor(100, 100, 100);
        
        $margins = $this->getMargins();
        
        $this->Line($margins['left'], $this->GetY(), $this->getPageWidth() - $margins['right'], $this->GetY(), array('width' => 0.5, 'cap' => 'square', 'join' => 'miter', 'dash' => 0, 'color' => array(100, 100, 100)));
        
        $width = $this->getPageWidth() - $margins['right'] - $margins['left'];
        $this->MultiCell($width / 2, 0, date("M d, Y") . ' ' . $this->document['doc_no'], 0, 'L', false, 0);
        $this->MultiCell($width / 2 + 35, 0, 'Page ' . $this->getAliasNumPage() . ' of ' . $this->getAliasNbPages(), 0, 'R', false, 1);
    }

    /**
     * Возвращает состояние документа
     * 
     */
    public function deprecated_GetState()
    {
        return $this->state;
    }
    
    /**
     * 
     * @param string $text
     * @param int $x_pos
     * @param int $y_pos
     * @param int $width
     * @param string $text_align
     */
    public function deprecated_DrawCell($text, $x_pos, $y_pos, $width = 0, $text_align = 'L', $filler = false)
    {
        $w              = $width;
        $h              = 0;
        $txt            = $text;
        $border         = 0;
        $align          = $text_align;
        $fill           = false;
        $ln             = 1;
        $x              = $x_pos;
        $y              = $y_pos;
        $reseth         = true;
        $stretch        = 0;
        $ishtml         = false;
        $autopadding    = true;
        $maxh           = 0;
        $valign         = 'T';
        $fitcell        = false;
        
        if ($filler)
        {
            $fill       = true;
            $this->SetFillColor(255,255,255);
        }
        
        $this->MultiCell($w, $h, $txt, $border, $align, $fill, $ln, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
    }
    
    
    public function deprecated_DrawImage($filepath, $x = 0, $y = 0, $width = 0, $height = 0)
    {
        $file       = $filepath;
        $x          = $x;
        $y          = $y;
        $w          = $width;
        $h          = $height;
        $type       = 'PNG';
        $link       = '';
        $align      = 'LTR';
        $resize     = false;
        $dpi        = 300;
        $palign     = 'C';
        $ismask     = false;
        $imgmask    = false;
        $border     = 0;
        $fitbox     = false;
        $hidden     = false;
        $fitonpage  = false;
        $alt        = false;
        $altimgs    = array();
        
        $this->Image($file, $x, $y, $w, $h, $type, $link, $align, $resize, $dpi, $palign, $ismask, $imgmask, $border, $fitbox, $hidden, $fitonpage, $alt, $altimgs);
    }

}