<?php
require_once APP_PATH . 'classes/models/default_pdf.php';
require_once APP_PATH . 'classes/models/cmr.class.php';

/**
 * Класс для формирования PDF версии CMR
 * 
 * @version 20121105, d10n
 */
class CMRPdf extends DefaultPdf
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
    function CMRPdf($owner_alias = 'mam', $config_name = 'marginless')
    {
        DefaultPdf::DefaultPdf($owner_alias, $config_name);
        
        $this->pdf = new CMRPdfForSE();
    }
    
    /**
     * Формирует документ
     * 
     * @param mixed $document_id
     */
    function _generate($document_id)
    {
        $modelCMR   = new CMR();
        $cmr        = $modelCMR->GetForPDF($document_id);
        
        if (empty($cmr)) return null;
        
        // start формирование основного документа CMR
        $this->pdf->SetMargins(0, 0, -1, false);
        $this->pdf->AddPage();
        
        $this->pdf->DrawImage(K_PATH_IMAGES . 'pdf/cmr.png', 0, 30, 570);
        
        $this->_set_font('B', 8);
        $this->pdf->DrawCell($cmr['international_consignement'], 435, 60, 50);
        
        $delta_x = 20;
        $this->pdf->DrawCell($cmr['sender'], 37+$delta_x, 75, 249-$delta_x);//1 - sender
        $this->pdf->DrawCell($cmr['consignee'], 37+$delta_x, 188, 249-$delta_x);//2 - consignee
        $this->pdf->DrawCell($cmr['place_of_delivery'], 37+$delta_x, 255, 249-$delta_x);//3 - place of delivety
        $this->pdf->DrawCell($cmr['place_and_date'], 37+$delta_x, 300, 249-$delta_x);//4 - place_and_date
        $this->pdf->DrawCell($cmr['documents_attached'], 37+$delta_x, 350, 249-$delta_x);//5 - documents attached
        $this->pdf->DrawCell($cmr['marks_nos'], 37+$delta_x, 405, 100-$delta_x);//6 - marks_and_nos
        
        if (!empty($cmr['marks_nos_ddt']))
        {
            $this->pdf->DrawCell($cmr['marks_nos_ddt'], 37+$delta_x, 442, 318-$delta_x);//6 - marks_and_nos    
        }
        
        $this->pdf->DrawCell($cmr['product_name'], 170+$delta_x, 425, 185-$delta_x);//6 - product_name
        $this->pdf->DrawCell($cmr['gross_weight'], 402+$delta_x, 425, 87-$delta_x, 'C');//11 - product_name
        $this->pdf->DrawCell($cmr['carrier'], 290+$delta_x, 188, 265-$delta_x);//16 - carrier
        $this->pdf->DrawCell($cmr['established_in'], 69+$delta_x, 690, 150-$delta_x);//21 - Established IN
        $this->pdf->DrawCell($cmr['established_on'], 215+$delta_x, 689, 70-$delta_x);//21 - Established ON
        $this->pdf->DrawCell($cmr['sender_ss'], 37+$delta_x, 726, 175-$delta_x);//22 - sender_ss
        $this->pdf->DrawCell($cmr['carrier_ss'], 205+$delta_x, 726, 175-$delta_x);//23 - carrier_ss
        
        $this->pdf->endPage();
        // end формирование основного документа CMR
        
        
        // start формирование доп.документа к CMR - Packing List
        $this->pdf->document    = $cmr;
        $this->pdf->draw_header = true;
        $this->pdf->draw_footer = true;
        
        //$this->pdf->SetMargins(50, 100, -1, true);
        $this->pdf->SetMargins(60, 100, 30, true);
        $this->pdf->SetAutoPageBreak(true, 50);
        
        $this->pdf->AddPage();
        $this->_set_font('', 8);
        
        $packing_list = $cmr['packing_list'];
        
        if (!empty($packing_list['our_ref']))
        {
            $this->pdf->DrawCell('Our Ref. : ', 50+10, 100, 40);
            $this->pdf->DrawCell($packing_list['our_ref'], 90+10, 100, 433+40);
        }
        
        $this->pdf->Ln(40);
        
        $draw_additional_params = array(
            'name'  => array('width' => 150, 'font-style' => ''),
            'value' => array('font-style' => 'B', 'width' => 330),
        );
        if (!empty($packing_list['customer']))
        {
            $this->_draw_name_and_value('CUSTOMER', $packing_list['customer'], $draw_additional_params);
            $this->pdf->Ln(10);
        }
        if (!empty($packing_list['customer_ref']))
        {
            $this->_draw_name_and_value('CUSTOMER REFERENCE', $packing_list['customer_ref'], $draw_additional_params);
            $this->pdf->Ln(10);
        }
        if (!empty($packing_list['location']))
        {
            $this->_draw_name_and_value('LOCATION', $packing_list['location'], $draw_additional_params);
            $this->pdf->Ln(10);
        }
        if (!empty($packing_list['destination']))
        {
            $this->_draw_name_and_value('DESTINATION', $packing_list['destination'], $draw_additional_params);
            $this->pdf->Ln(10);
        }
        if (!empty($packing_list['transport_mode']))
        {
            $this->_draw_name_and_value('TRANSPORT MODE', $packing_list['transport_mode'], $draw_additional_params);
            $this->pdf->Ln(10);
        }
        if (!empty($packing_list['loading_date']))
        {
            $this->_draw_name_and_value('LOADING DATE', $packing_list['loading_date'], $draw_additional_params);
            $this->pdf->Ln(10);
        }
        if (!empty($packing_list['truck_number']))
        {
            $this->_draw_name_and_value('TRUCK NUMBER', $packing_list['truck_number'], $draw_additional_params);
            $this->pdf->Ln(10);
        }
        
        $this->pdf->Ln(20);
        
        $this->pdf->Line(440+30, $this->pdf->GetY(), 535+30, $this->pdf->GetY());
        $this->_set_font('', 8);
        $this->pdf->DrawCell('driver\'s signature', 440+30, $this->pdf->GetY()+1, 100);
        
        
        $this->pdf->Ln(15);
        $this->_set_font('B', 12);
        $this->pdf->Cell(0, 0, 'Packing List', 0, 0, 'C');
        $this->pdf->Ln(25);
        
        $items_list = $packing_list['items_list'];
        
        $columns    = array(
            array('title' => 'Plate Id',        'width' => 15, 'field' => 'guid'),
            array('title' => 'Steel Grade',     'width' => 15, 'field' => 'steelgrade'),
            array('title' => "Thick,\nmm",      'width' => 11, 'field' => 'thickness_mm'),
            array('title' => "Width,\nmm",      'width' => 11, 'field' => 'width_mm'),
            array('title' => "Length,\nmm",     'width' => 11, 'field' => 'length_mm'),
            array('title' => "Qtty,\npcs",      'width' => 11, 'field' => 'qtty', 'total' => true),
            array('title' => "Weight,\nTon",    'width' => 11, 'field' => 'weight_ton', 'total' => array('precision' => 3)),
        );
        
        $this->_draw_simple_table($items_list, $columns);
        $this->pdf->Ln(50);
        
        $this->_set_font('B', 10);
        $this->pdf->DrawCell($packing_list['total_weight'], 340+30, '', 200);
        $this->pdf->Line(340+30, $this->pdf->GetY(), 535+30, $this->pdf->GetY(), array('width' => 1));
        
        // удаляет предыдущий атачмент
        $attachments = new Attachment();
        if (!empty($cmr['attachment_id'])) $attachments->Remove($cmr['attachment_id']);
        
        // сохраняет документ в cache
        $file_name  = $cmr['doc_no'];
        $file_name  = preg_replace('/[^a-zA-Z0-9_-]/', '_', Translit::Encode($file_name)) . '_' . $cmr['filename_suffix'] . '.pdf';
        $file_name  = APP_CACHE . $file_name;
        $this->pdf->Output($file_name, 'F');
        
        // сохраняем атачмент в бд
        $attachment_id = $this->_save_attachment('cmr', $cmr['id'], $file_name);
        
        // обновляет связь сертификата с созданным пдф
        $modelCMR->UpdateAttachment($cmr['id'], $attachment_id);
        
        // выставляет актуальность
        $modelCMR->SetAsActual($cmr['id']);
    }
}


/**
 * Класс, перегружающий методы базового класса для
 * формирования хедэра и футера с картинками SteelEmotion и адресом MaM UK
 */
class CMRPdfForSE extends TCPDF
{
    public $draw_header = false;
    public $draw_footer = false;
    
    public $document;
    
    //Page header
    public function Header() 
    {
        if (!$this->draw_header) return false;
        
        $this->Image(K_PATH_IMAGES . $this->document['packing_list']['logo'], 60, 30, 510);
        $this->DrawCell($this->document['packing_list']['logo_description'], 62, 60, 510, 'L');
        //$image_file = K_PATH_IMAGES . 'pdf/header/letterhead_mam_int.jpg';
        //$this->Image($image_file, 60, 30, 500);
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
        $this->MultiCell($width / 2, 0, date("M d, Y") . ', ' . $this->document['doc_no'], 0, 'L', false, 0);
        $this->MultiCell($width / 2 + 35, 0, 'Page ' . $this->getAliasNumPage() . ' of ' . $this->getAliasNbPages(), 0, 'R', false, 1);
    }
    
    /**
     * Возвращает состояние документа
     * 
     */
    public function GetState()
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
    public function DrawCell($text, $x_pos, $y_pos, $width = 0, $text_align = 'L', $filler = false)
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
    
    
    public function DrawImage($filepath, $x = 0, $y = 0, $width = 0, $height = 0)
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