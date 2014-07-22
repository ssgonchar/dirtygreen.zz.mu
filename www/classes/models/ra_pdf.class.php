<?php
require_once APP_PATH . 'classes/models/default_pdf.php';
require_once APP_PATH . 'classes/models/ra.class.php';

/**
 * Class for creating RA PDF // Класс для формирования PDF версии Release Advice
 * 
 * @version 20121016, d10n
 */
class RAPdf extends DefaultPdf
{
    /**
     * Constructor
     * 
     * @param mixed $owner_alias - mam company alias 'mam' or 'pa'
     * @return QCPdf
     */
    function RAPdf($owner_alias = 'mam', $config_name = 'default')
    {
        DefaultPdf::DefaultPdf($owner_alias, $config_name);
        
        if ($owner_alias == 'mam')
        {
            $this->pdf = new RAPdfForSE();
        }
        else
        {
            $this->pdf = new RAPdfForPA();
        }

    }
    
    /**
     * Create document // Формирует документ
     * 
     * @param mixed $document_id
     */
    function _generate($document_id)
    {
        // fix for all documents except QC for stamp padding // поправка, в настройках стоит 20, для того чтобы в QC помещалась печать, во всех остальных должно быть 40
        $this->pdf->SetAutoPageBreak(TRUE, 40);
        
        $modelRA    = new RA();
        
        $data_set   = $modelRA->GetById($document_id);
        $ra         = $data_set['ra'];
        
        if (empty($ra)) return null;
        
        // doc number & date // устанавливает дату и номер документа, нужно и для футера
        $this->pdf->SetDocNo($ra['doc_no']);
        
        // add page // добавляет страницу
        $this->pdf->AddPage();
        
        $this->_draw_line('B');
        
        $this->_set_font('B', 8);
        $this->pdf->MultiCell($this->width / 2, 0, 'Date : ' . $this->pdf->mam_doc_date, 0, 'L', false, 0);
        $this->pdf->MultiCell($this->width / 2, 0, 'Doc. No ' . $this->pdf->mam_doc_no, 0, 'R', false, 1);        
        
        $this->_draw_line();
        
        $this->pdf->Ln(20);
        $this->_set_font('B', 16);
        //dg($ra);
        $this->pdf->Cell(0, 0, 'to : ' . $ra['stockholder']['title'].', '.$ra['stockholder']['city']['title'], 0, 0, 'C');
        
        $this->pdf->Ln(20);
        $this->_set_font('B', 16);
        $this->pdf->Cell(0, 0, 'Release Advice No ' . $ra['doc_no'], 0, 0, 'C');
        
//        $this->pdf->Ln(20);
//        $this->_set_font('B', 14);
//        $this->pdf->Cell(0, 0, 'Packing List', 0, 0, 'C');
//        _epd($ra);
        $this->pdf->Ln(40);
        $items      = $modelRA->GetItemsForPdf($document_id);

        
        if ($ra['mm_dimensions'] == 1)
        {
            $columns    = array(
                array('title' => 'Plate Id',                            'width' => 15, 'field' => 'guid'),
                array('title' => 'Steel Grade',                         'width' => 15, 'field' => 'steelgrade'),
                array('title' => "Thick,\n". $ra['dimension_unit'],     'width' => 0, 'field' => 'thickness'),
                array('title' => "Thick,\nmm",                          'width' => 0, 'field' => 'thickness_mm'),
                array('title' => "Width,\n" . $ra['dimension_unit'],    'width' => 0, 'field' => 'width'),
                array('title' => "Width,\nmm",                          'width' => 0, 'field' => 'width_mm'),
                array('title' => "Length,\n" . $ra['dimension_unit'],   'width' => 0, 'field' => 'length'),
                array('title' => "Length,\nmm",                         'width' => 0, 'field' => 'length_mm'),
                array('title' => "Weight,\n" . $ra['weight_unit'],      'width' => 0, 'field' => 'unitweight', 'format' => 'number2c'),
                array('title' => "Qtty,\npcs",                          'width' => 0, 'field' => 'qtty', 'total' => true)
            );
        }
        else
        {
            $columns    = array(
                array('title' => 'Plate Id',                            'width' => 25, 'field' => 'guid'),
                array('title' => 'Steel Grade',                         'width' => 25, 'field' => 'steelgrade'),
                array('title' => "Thick,\n". $ra['dimension_unit'],     'width' => 0, 'field' => 'thickness'),
                array('title' => "Width,\n" . $ra['dimension_unit'],    'width' => 0, 'field' => 'width'),
                array('title' => "Length,\n" . $ra['dimension_unit'],   'width' => 0, 'field' => 'length'),
                array('title' => "Weight,\n" . $ra['weight_unit'],      'width' => 0, 'field' => 'unitweight', 'format' => 'number2c'),
                array('title' => "Qtty,\npcs",                          'width' => 0, 'field' => 'qtty', 'total' => true)
            );
        }
        
        // for stockholders in europe
        if ($ra['stock_object_alias'] == 'mam')
        {
            foreach ($columns as $key => $column)
            {
                if ($key > 1 && isset($column['width']))
                {
                    $columns[$key]['width'] = 7;
                }
            }
                        
            $columns[] = array('title' => "DDT Nr & Date", 'width' => 0, 'field' => 'ddt');
        }
        
        
        $this->_draw_simple_table($items, $columns);
        
        if ($ra['mm_dimensions'] == 1)
        {
            $columns    = array(
                array('title' => 'Plate Id',                            'width' => 15, 'field' => 'guid'),
                array('title' => 'Steel Grade',                         'width' => 15, 'field' => 'steelgrade'),
                array('title' => 'Thick, ' . $ra['dimension_unit'],     'width' => 0, 'field' => 'thickness'),
                array('title' => 'Thick, mm',                           'width' => 0, 'field' => 'thickness_mm'),
                array('title' => 'Width, ' . $ra['dimension_unit'],     'width' => 0, 'field' => 'width'),
                array('title' => 'Width, mm',                           'width' => 0, 'field' => 'width_mm'),
                array('title' => 'Length, ' . $ra['dimension_unit'],    'width' => 0, 'field' => 'length'),
                array('title' => 'Length, mm',                          'width' => 0, 'field' => 'length_mm'),
                array('title' => "Weight,\n" . $ra['weight_unit'],      'width' => 0, 'field' => 'unitweight', 'format' => 'number2c'),
                array('title' => 'Qtty, pcs',                           'width' => 0, 'field' => 'qtty')
            );
        }
        else
        {
            $columns    = array(
                array('title' => 'Plate Id',                            'width' => 25, 'field' => 'guid'),
                array('title' => 'Steel Grade',                         'width' => 25, 'field' => 'steelgrade'),
                array('title' => 'Thickness, ' . $ra['dimension_unit'], 'width' => 0, 'field' => 'thickness'),
                array('title' => 'Width, ' . $ra['dimension_unit'],     'width' => 0, 'field' => 'width'),
                array('title' => 'Length, ' . $ra['dimension_unit'],    'width' => 0, 'field' => 'length'),
                array('title' => "Weight,\n" . $ra['weight_unit'],      'width' => 0, 'field' => 'unitweight', 'format' => 'number2c'),
                array('title' => 'Qtty, pcs',                           'width' => 0, 'field' => 'qtty')
            );
        }
        
        // for stockholders in europe
        if ($ra['stock_object_alias'] == 'mam')
        {            
            foreach ($columns as $key => $column)
            {
                if ($key > 1 && isset($column['width']))
                {
                    $columns[$key]['width'] = 7;
                }
            }
            
            $columns[] = array('title' => 'DDT Nr & Date', 'width' => 0, 'field' => 'ddt');
        }        
        

        foreach ($items as $row)
        {
            if (isset($row['variants']))
            {
                $this->pdf->Ln(20);
                $this->_set_font();
                $this->pdf->Cell(0, 0, 'Possible IDs for ' . $row['thickness'] . ' ' . $row['dimension_unit'] . ' plate', 0, 0);
                $this->pdf->Ln(15);

                $this->_draw_simple_table($row['variants'], $columns, false);
            }
        }
        
        $this->pdf->Ln(30);
        
        $draw_additional_params = array();
        if (!empty($ra['marking']))
        {
            $this->pdf->Ln(10);
            $this->_draw_name_and_value('Marking', $ra['marking'], $draw_additional_params);
        }
        if (!empty($ra['dunnaging']))
        {
            $this->pdf->Ln(10);
            $this->_draw_name_and_value('Dunnaging', $ra['dunnaging'], $draw_additional_params);
        }
        if (!empty($ra['coupon']))
        {
            $this->pdf->Ln(10);
            $this->_draw_name_and_value('Coupon', $ra['coupon'], $draw_additional_params);
        }
        if ($ra['weight_unit'] == 'lb')
        {
            $this->pdf->Ln(10);
            $this->_draw_name_and_value('Quantity', 'aprox ' . number_format($ra['total_weight'], 0, '.', ',') . ' lb (' .  $ra['total_qtty'] . ' pc' . ($ra['total_qtty'] > 1 ? 's' : '') . ')', $draw_additional_params);
        }
        else
        {
            $this->pdf->Ln(10);
            $this->_draw_name_and_value('Quantity', 'cca ' . number_format($ra['total_weight'], 2, '.', '') . ' Ton (' .  $ra['total_qtty'] . ' pc' . ($ra['total_qtty'] > 1 ? 's' : '') . ')', $draw_additional_params);
        }
        if (array_key_exists('company', $ra))
        {
            $this->pdf->Ln(10);
            $this->_draw_name_and_value('Transport Company', $ra['company']['doc_no'], $draw_additional_params);
        }
        if (!empty($ra['truck_number']))
        {
            $this->pdf->Ln(10);
            $this->_draw_name_and_value('Truck number', $ra['truck_number'], $draw_additional_params);
        }
        
        if (!empty($ra['loading_date']))
        {
            $this->pdf->Ln(10);
            $this->_draw_name_and_value('Loading date', $ra['loading_date'], $draw_additional_params);
        }
        if (!empty($ra['destination']))
        {
            $this->pdf->Ln(10);
            $this->_draw_name_and_value('Destination', $ra['destination'], $draw_additional_params);
        }
        if (!empty($ra['ddt_instructions']))
        {
            $this->pdf->Ln(10);
            $this->_draw_name_and_value('DDT Instructions', $ra['ddt_instructions'], $draw_additional_params);
        }
        if (!empty($ra['consignee']))
        {
            $this->pdf->Ln(10);
            $this->_draw_name_and_value('Consignee', $ra['consignee'], $draw_additional_params);
        }
        if (!empty($ra['consignee_ref']))
        {
            $this->pdf->Ln(10);
            $this->_draw_name_and_value('Consignee Ref.', $ra['consignee_ref'], $draw_additional_params);
        }
        
        $this->pdf->Ln(20);
        
        $this->pdf->Cell(0, 0, $ra['notes'], 0, 1, 'L');
//        if ($ra['stock_object_alias'] == 'mam')
//        {
//            $this->pdf->Cell(0, 0, 'Please send us DDT & weighbridge ticket as soon as issued.', 0, 1, 'L');
//        }
//        else
//        {
//            $this->pdf->Cell(0, 0, 'Please be so kind to state actual dimensions in your bill of lading as well as plate ID.', 0, 1, 'L');
//            $this->pdf->Cell(0, 0, 'For sending / faxing us a copy of BOL, we thank you in advance.', 0, 1, 'L');
//        }
        
        // remove previous document // удаляет предыдущий атачмент
        $attachments = new Attachment();
        if (!empty($ra['attachment_id'])) $attachments->Remove($ra['attachment_id']);
        
        // save document in cache // сохраняет документ в cache
        $file_name  = $ra['doc_no'];
        $file_name  = preg_replace('/[^a-zA-Z0-9_-]/', '_', Translit::Encode($file_name)) . '.pdf';
        $file_name  = APP_CACHE . $file_name;
        $this->pdf->Output($file_name, 'F');
        
        // save attachment into db // сохраняем атачмент в бд
        $attachment_id = $this->_save_attachment('ra', $ra['id'], $file_name);

        // refresh link between attachment & document // обновляет связь ra с созданным пдф
        $modelRA->UpdateAttachment($ra['id'], $attachment_id);
    }
}


/**
 * Child class for creation header & footer with SE pictures & address
 * Класс, перегружающий методы базового класса для формирования хедэра и футера с картинками SteelEmotion и адресом MaM UK
 */
class RAPdfForSE extends TCPDF {

    /**
     * Doc number // Номер документа
     * 
     * @var mixed
     */
    var $mam_doc_no = '';
    
    /**
     * Date of document // Дата создания документа
     * 
     * @var mixed
     */
    var $mam_doc_date = '';
    
    //Page header
    public function Header() 
    {
        // Logo
        $image_file = K_PATH_IMAGES . 'pdf/header/letterhead_mam_int.jpg';
        $this->Image($image_file, 60, 20, 500);
    }

    // Page footer
    public function Footer() 
    {
        // Position at 40 px from bottom
        $this->SetY(-40);
        
        // Set font
        $this->SetFont(TCPDF_DEFAULT_FONT, '', 8);
        $this->SetTextColor(100, 100, 100);
        
        $this->Line(PDF_MARGIN_LEFT, $this->GetY(), $this->getPageWidth() - PDF_MARGIN_RIGHT, $this->GetY(), array('width' => 0.5, 'cap' => 'square', 'join' => 'miter', 'dash' => 0, 'color' => array(100, 100, 100)));

        $width = $this->getPageWidth() - PDF_MARGIN_RIGHT - PDF_MARGIN_LEFT;
        $this->MultiCell($width / 2, 0, $this->mam_doc_date . ', ' . $this->mam_doc_no, 0, 'L', false, 0);
        $this->MultiCell($width / 2 + 35, 0, 'Page ' . $this->getAliasNumPage() . ' of ' . $this->getAliasNbPages(), 0, 'R', false, 1);
    }
    
    /**
     * Set number & date for the document // Устанавливает номер и дату документа
     * 
     * @param mixed $doc_no
     * @param mixed $doc_date
     */
    function SetDocNo($doc_no, $doc_date = null)
    {
        if (empty($doc_date)) $doc_date = date("d F Y");
        
        $this->mam_doc_no   = $doc_no;
        $this->mam_doc_date = $doc_date;
    }
    
    /**
     * Get document state // Возвращает состояние документа
     * 
     */
    function GetState()
    {
        return $this->state;
    }
}

/**
 * Child class for creation header & footer with PA pictures & address
 * Класс, перегружающий методы базового класса для формирования хедэра и футера с картинками PlateaAsead
 */
class RAPdfForPA extends TCPDF {

    /**
     * Doc Number // Номер документа
     * 
     * @var mixed
     */
    var $mam_doc_no = '';
    
    /**
     * Doc Date // Дата создания документа
     * 
     * @var mixed
     */
    var $mam_doc_date = '';
    
    //Page header
    public function Header() 
    {
        // Logo
        $image_file = K_PATH_IMAGES . 'pdf/header/letterhead_platesahead_int.jpg';
        $this->Image($image_file, 60, 20, 500);
    }

    // Page footer
    public function Footer() 
    {
        // Position at 40 px from bottom
        $this->SetY(-40);
        
        // Set font
        $this->SetFont(TCPDF_DEFAULT_FONT, '', 8);
        $this->SetTextColor(100, 100, 100);
        
        $this->Line(PDF_MARGIN_LEFT, $this->GetY(), $this->getPageWidth() - PDF_MARGIN_RIGHT, $this->GetY(), array('width' => 0.5, 'cap' => 'square', 'join' => 'miter', 'dash' => 0, 'color' => array(100, 100, 100)));

        $width = $this->getPageWidth() - PDF_MARGIN_RIGHT - PDF_MARGIN_LEFT;
        $this->MultiCell($width / 2, 0, $this->mam_doc_date . ', ' . $this->mam_doc_no, 0, 'L', false, 0);
        $this->MultiCell($width / 2 + 35, 0, 'Page ' . $this->getAliasNumPage() . ' of ' . $this->getAliasNbPages(), 0, 'R', false, 1);
    }
    
    /**
     * Set doc number & date // Устанавливает номер и дату документа
     * 
     * @param mixed $doc_no
     * @param mixed $doc_date
     */
    function SetDocNo($doc_no, $doc_date = null)
    {
        if (empty($doc_date)) $doc_date = date("M d, Y");
        
        $this->mam_doc_no   = $doc_no;
        $this->mam_doc_date = $doc_date;
    }
    
    /**
     * Get document state // Возвращает состояние документа
     * 
     */
    function GetState()
    {
        return $this->state;
    }
}
