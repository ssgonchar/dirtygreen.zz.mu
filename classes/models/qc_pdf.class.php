<?php
require_once APP_PATH . 'classes/models/default_pdf.php';
require_once APP_PATH . 'classes/models/qc.class.php';

/**
 * Класс для формирования PDF версии Certificate Of Quality
 * 
 * @version 20120812, zharkov
 */
class QCPdf extends DefaultPdf
{
    /**
     * Конструктор
     * 
     * @param mixed $owner_alias - алиас владелеца, обычно 'mam' или 'pa'
     * @return QCPdf
     */
    function QCPdf($owner_alias = 'mam', $config_name = 'default')
    {
        DefaultPdf::DefaultPdf($owner_alias, $config_name);
        
        $this->pdf = new QCPdfForSE();
        
/*      тут разделение не нужно, потому что и для PL и для SE будет одна и та же шапка от SE

        if ($owner_alias == 'mam')
        {
            $this->pdf = new QCPdfForSE();
        }
        else
        {
            $this->pdf = new QCPdfForPA();
        }
*/        
    }
    
    /**
     * Генерирует документ
     * 
     * @param mixed $document_id
     */
    function _generate($document_id)
    {
        $qcs    = new QC();
        $qc     = $qcs->GetById($document_id);
        $qc     = $qc['qc'];

        if (empty($qc)) return null;

        
        $items_per_page = 5;        
        $items          = $qcs->GetItemsForPdf($document_id);
        
        $rowset = array();        
        $index  = 0;
        
        foreach ($items as $key => $row)
        {
            if ($key > 0 && $key % $items_per_page == 0) $index++;            
            $rowset[$index][] = $row;
        }
        
        foreach ($rowset as $items)
        {
            $this->_generate_page($qc, $items);
        }
        
        // сохраняет документ в cache
        $co_title   = isset($qc['company']) ? $qc['company']['doc_no'] : $qc['customer'];
        $biz_title  = str_replace('.', '', isset($qc['qcbiz']) ? $qc['qcbiz']['doc_no'] : $qc['biz']);
        
        $file_name  = str_replace('_', '', $qc['doc_no']) . '_' . (empty($co_title) ? '' : $co_title . '_') . (empty($biz_title) ? '' : $biz_title . '_');
        $file_name  = preg_replace('/[^a-zA-Z0-9_-]/', '', Translit::Encode($file_name)) . '.pdf';        
        
        // удаляет предыдущий атачмент
        $attachments = new Attachment();
        if (!empty($qc['attachment_id'])) $attachments->Remove($qc['attachment_id']);

        // сохраняет документ в cache
        $file_name  = APP_CACHE . $file_name;
        $this->pdf->Output($file_name, 'F');

        // сохраняем атачмент в бд
        $attachment_id = $this->_save_attachment('qc', $qc['id'], $file_name);        

        // обновляет связь сертификата с созданным пдф
        $qcs = new QC();
        $qcs->UpdateAttachment($qc['id'], $attachment_id);
        
        if (!empty($attachment_id))
        {
            if (!empty($qc['order_id'])) $attachments->LinkToObject($attachment_id, 'order', $qc['order_id']);
            if (!empty($qc['biz_id'])) $attachments->LinkToObject($attachment_id, 'biz', $qc['biz_id']);
            if (!empty($qc['customer_id'])) $attachments->LinkToObject($attachment_id, 'company', $qc['customer_id']);
        }
    }
    
    /**
     * Формирует страницу документа
     * 
     * @param mixed $document_id
     */
    function _generate_page($qc, $items)
    {
        // устанавливает дату и номер документа, нужно и для футера
        $this->pdf->SetDocNo($qc['doc_no']);
        
        // добавляет страницу        
        $this->pdf->AddPage();
        
        $this->_draw_line('B');
        
        $this->_set_font('B', 8);
        $this->pdf->MultiCell($this->width / 2, 0, 'Date : ' . $this->pdf->mam_doc_date, 0, 'L', false, 0);
        $this->pdf->MultiCell($this->width / 2, 0, 'Doc. No ' . $this->pdf->mam_doc_no, 0, 'R', false, 1);        
        
        $this->_draw_line();        
        
        $this->pdf->Ln(20);        
        $this->_set_font('B', 16);
        $this->pdf->Cell(0, 0, 'Certificate Of Quality No ' . $qc['doc_no'], 0, 0, 'C');
        
        $this->pdf->Ln(30);
        $y = $this->pdf->GetY();

        $qc_params = array();

        if (!empty($qc['certification_standard'])) $qc_params[] = array('name' => 'Certification Standard', 'value' => $qc['certification_standard']);
        if (!empty($qc['commodity_name'])) $qc_params[] = array('name' => 'Commodity Name', 'value' => $qc['commodity_name']);
        if (!empty($qc['standard'])) $qc_params[] = array('name' => 'Standard', 'value' => $qc['standard']);
        //if (!empty($qc['biz'])) $qc_params[] = array('name' => 'Our Reference', 'value' => (isset($qc['qcbiz']) && isset($qc['qcbiz']['doc_no']) ? $qc['qcbiz']['doc_no'] : $qc['biz']));
        if (!empty($qc['biz'])) $qc_params[] = array('name' => 'Our Reference', 'value' => $qc['biz']);
        if (!empty($qc['customer'])) $qc_params[] = array('name' => 'Customer', 'value' => $qc['customer']);
        if (!empty($qc['customer_order_no'])) $qc_params[] = array('name' => 'Customer Order No', 'value' => $qc['customer_order_no']);
        if (!empty($qc['manufacturer'])) $qc_params[] = array('name' => 'Manufacturer', 'value' => $qc['manufacturer']);
        if (!empty($qc['country_of_origin'])) $qc_params[] = array('name' => 'Country Of Origin', 'value' => $qc['country_of_origin']);
        if (!empty($qc['delivery_conditions'])) $qc_params[] = array('name' => 'Delivery Condition', 'value' => $qc['delivery_conditions']);
        if (!empty($qc['test_ref'])) $qc_params[] = array('name' => 'Test Ref.', 'value' => $qc['test_ref']);
        if (!empty($qc['steelmaking_process'])) $qc_params[] = array('name' => 'Steelmaking Process', 'value' => $qc['steelmaking_process']);
        if (!empty($qc['ultrasonic_test'])) $qc_params[] = array('name' => 'Ultrasonic Test', 'value' => $qc['ultrasonic_test']);
        if (!empty($qc['marking'])) $qc_params[] = array('name' => 'Marking', 'value' => $qc['marking']);
        if (!empty($qc['visual_inspection'])) $qc_params[] = array('name' => 'Visual Inspection', 'value' => $qc['visual_inspection']);
        if (!empty($qc['flattening'])) $qc_params[] = array('name' => 'Flattening', 'value' => $qc['flattening']);
        if (!empty($qc['stress_relieving'])) $qc_params[] = array('name' => 'Stress Relieving', 'value' => $qc['stress_relieving']);
        if (!empty($qc['surface_quality'])) $qc_params[] = array('name' => 'Surface Quality', 'value' => $qc['surface_quality']);
        if (!empty($qc['tolerances_on_thickness'])) $qc_params[] = array('name' => 'Tolerances On Thickness', 'value' => $qc['tolerances_on_thickness']);
        if (!empty($qc['tolerances_on_flatness'])) $qc_params[] = array('name' => 'Tolerances On Flatness', 'value' => $qc['tolerances_on_flatness']);

        $qc_font_size = 7;
        
        $settings = array(
            'name'      => array('width' => 90, 'font-style' => '', 'font-size' => $qc_font_size),
            'delimeter' => array('font-size' => $qc_font_size),
            'value'     => array('width' => (count($qc_params) > 9 ? 180 : 0), 'font-size' => $qc_font_size)
        );
        
        $start_y    = $this->pdf->GetY();
        $end_y      = 0;
        $half       = count($qc_params) / 2;
        foreach ($qc_params as $key => $param)
        {
            if ($key == 9)
            {
                $this->pdf->SetY($start_y);
                $this->pdf->SetX(350);
            }

            if ($key > 8)
            {
                $this->pdf->SetX(350);
            }
            
            $this->_draw_name_and_value($param['name'], $param['value'], $settings);
            $end_y = $this->pdf->GetY() > $end_y ? $this->pdf->GetY() : $end_y;
        }
        
        $y = ($end_y > 0 ? $end_y : $y);
        $this->pdf->SetY($y);
                
        $this->pdf->Ln(20);
        $this->_set_font();
        $this->pdf->Cell(0, 0, 'SPECIFICATION', 0, 0);
        $this->pdf->Ln();
        

        $table_settings = array(
            'header' => array(
                'normal'=> array('font-size' => 6, 'border' => 'TB', 'border-width' => '1', 'height' => 15),
            ),
            'row' => array(
                'normal'=> array('font-size' => 6, 'border' => 'B', 'border-width' => '0.3', 'height' => 15),
            ),
            'footer' => array(
                'normal'=> array('font-size' => 6, 'border' => 'B', 'border-width' => '0.3', 'height' => 15),
            )
        );
        
        $dimension_units    = array();
        $weight_units       = array();

        foreach ($items as $key => $item)
        {
            $dimension_units[$item['dimension_unit']]   = $item['dimension_unit'];
            $weight_units[$item['weight_unit']]         = $item['weight_unit'];            
        }

        // more then one unit in QC
        $multiunits = $qc['dim_unit'] == 'in' && isset($dimension_units['mm']);
        
        if ($multiunits)
        {
            $columns = array(
                array('title' => 'Plate Id',        'field' => 'guid', 'width' => 13, 'align' => 'L'),
                array('title' => 'Heat / Lot',      'field' => 'property_heat_lot'),
                array('title' => 'Steel Grade',     'field' => 'steelgrade_title'),
                array('title' => 'Thickness',       'field' => 'thickness', 'format' => 'number2c', 'suffix_field' => 'dimension_unit'),
                array('title' => 'Width',           'field' => 'width', 'format' => 'number2c', 'suffix_field' => 'dimension_unit'),
                array('title' => 'Length',          'field' => 'length', 'format' => 'number2c', 'suffix_field' => 'dimension_unit'),
                array('title' => 'Qtty, pcs',       'field' => 'qtty')
            );
        }
        else if ($qc['dim_unit'] == 'mm')
        {
            $columns = array(
                array('title' => 'Plate Id',        'field' => 'guid', 'width' => 13, 'align' => 'L'),
                array('title' => 'Heat / Lot',      'field' => 'property_heat_lot'),
                array('title' => 'Steel Grade',     'field' => 'steelgrade_title'),
                array('title' => 'Thickness, mm',   'field' => 'thickness_mm', 'format' => 'number1c'),
                array('title' => 'Width, mm',       'field' => 'width_mm', 'format' => 'number1c'),
                array('title' => 'Length, mm',      'field' => 'length_mm', 'format' => 'number1c'),
                array('title' => 'Qtty, pcs',       'field' => 'qtty')
            );            
        }
        else
        {
            $columns = array(
                array('title' => 'Plate Id',                        'field' => 'guid', 'width' => 13, 'align' => 'L'),
                array('title' => 'Heat / Lot',                      'field' => 'property_heat_lot'),
                array('title' => 'Steel Grade',                     'field' => 'steelgrade_title'),
                array('title' => 'Thickness, ' . $qc['dim_unit'],   'field' => 'thickness'),
                array('title' => 'Width, ' . $qc['dim_unit'],       'field' => 'width'),
                array('title' => 'Length, ' . $qc['dim_unit'],      'field' => 'length'),
                array('title' => 'Qtty, pcs',                       'field' => 'qtty')
            );                        
        }

        $this->_draw_table($items, $columns, $table_settings);

        
        $this->pdf->Ln(20);
        $this->_set_font();
        $this->pdf->Cell(0, 0, 'CHEMICAL ANALYSIS', 0, 0);
        $this->pdf->Ln();
        
        $this->_draw_table($items, array(
            array('title' => 'Plate Id',    'field' => 'guid', 'width' => 13, 'align' => 'L'),
            array('title' => '%C',          'field' => 'property_c'),
            array('title' => '%Si',         'field' => 'property_si'),
            array('title' => '%Mn',         'field' => 'property_mn'),
            array('title' => '%P',          'field' => 'property_p'),
            array('title' => '%S',          'field' => 'property_s'),
            array('title' => '%Cr',         'field' => 'property_cr'),
            array('title' => '%Ni',         'field' => 'property_ni'),
            array('title' => '%Cu',         'field' => 'property_cu'),
            array('title' => '%Al',         'field' => 'property_al'),
            array('title' => '%Mo',         'field' => 'property_mo'),
            array('title' => '%Nb',         'field' => 'property_nb'),
            array('title' => '%V',          'field' => 'property_v'),
            array('title' => '%N',          'field' => 'property_n'),
            array('title' => '%Ti',         'field' => 'property_ti'),
            array('title' => '%Sn',         'field' => 'property_sn'),
            array('title' => '%B',          'field' => 'property_b'),
            array('title' => 'CEQ',         'field' => 'property_ceq'),
        ), $table_settings);

        
        $table_settings['header']['normal']['height'] = 40;        
        
        $this->pdf->Ln(20);
        $this->_set_font();
        $this->pdf->Cell(0, 0, 'MECHANICAL PROPERTIES', 0, 0);
        $this->pdf->Ln();

        $this->_draw_table($items, array(
            array('title' => 'Plate Id',                            'field' => 'guid', 'width' => 13, 'align' => 'L'),
            array('title' => 'Sample Direction',                    'field' => 'property_tensile_sample_direction', 'default' => ''),
            array('title' => 'Tensile Strength MPa',                'field' => 'property_tensile_strength', 'default' => ''),
            array('title' => 'Yield Point MPa',                     'field' => 'property_yeild_point', 'default' => ''),
            array('title' => 'Elongation %',                        'field' => 'property_elongation', 'width' => 8, 'default' => ''),
            array('title' => 'Reduction Of Area %',                 'field' => 'property_reduction_of_area', 'width' => 8, 'default' => ''),
            array('title' => 'Sample Direction',                    'field' => 'property_sample_direction', 'default' => ''),
            array('title' => 'Test Temp deg. C',                    'field' => 'property_test_temp', 'default' => ''),
            array('title' => 'Impact Strength J/cm2',               'field' => 'property_impact_strength', 'width' => 8, 'default' => ''),
            array('title' => 'Hardness HB',                         'field' => 'property_hardness', 'default' => ''),
            array('title' => 'UST, Class',                          'field' => 'property_ust', 'default' => ''),
            array('title' => 'Normalizing Temp deg. C',             'field' => 'property_normalizing_temp', 'width' => 8, 'default' => ''),
            array('title' => 'Heating Rate Per Hour deg. C',        'field' => 'property_heating_rate_per_hour', 'default' => ''),
            array('title' => 'Holding Time Hours',                  'field' => 'property_holding_time', 'default' => '')
//            array('title' => 'Cooling Down Rate Per Hour deg. C',   'field' => 'property_cooling_down_rate', 'default' => '')
        ), $table_settings);
        
        $this->pdf->Ln(20);
        $this->_set_font('', $qc_font_size);
        
        $this->pdf->Cell(0, 0, 'We MaM hereby certify that the above mentioned products are in compliance with order requirements and tests of surface and dimensional aspects were successful.', 0, 1, 'L');
        $this->pdf->Ln(5);
        
        if (!empty($qc['no_weld_repair'])) $this->pdf->Cell(0, 0, 'NO WELD REPAIR.', 0, 1, 'L');

        $y = $this->pdf->getPageHeight() - 100;
        
        // CE Mark
        if (!empty($qc['ce_mark']))
        {
            $image_file = K_PATH_IMAGES . 'pdf/CE0474x600.png';
            $this->pdf->Image($image_file, 60, $y, 60);
        }
        
        $this->_set_font('', $qc_font_size);
        $this->pdf->MultiCell(100, 0, 'QUALITY CONTROL DEPT. ANNA AKHCHIYANI', 0, 'L', false, 0, 400, $y + 20);
                
        $x = $this->pdf->getPageWidth() - 100;

        // Signature
        //$image_file = K_PATH_IMAGES . 'pdf/signature600.png';
        $image_file = K_PATH_IMAGES . 'pdf/uuyd652h7726Gffd.png';
        $this->pdf->Image($image_file, $x, $y, 60);

        // Stamp
        $image_file = K_PATH_IMAGES . 'pdf/stamp2.600.png';
        $this->pdf->Image($image_file, $x - 30, $y - 30, 100);
    }
}


/**
 * Класс, перегружающий методы базового класса для
 * формирования хедэра и футера с картинками SteelEmotion и адресом MaM UK
 */
class QCPdfForSE extends TCPDF {

    /**
     * Номер документа
     * 
     * @var mixed
     */
    var $mam_doc_no = '';
    
    /**
     * Дата создания документа
     * 
     * @var mixed
     */
    var $mam_doc_date = '';
    
    //Page header
    public function Header() 
    {
        // Logo
        $image_file = K_PATH_IMAGES . 'pdf/header/letterhead_mam_int.jpg';
        //$this->Image($image_file, 60, 20, 500);
        $w = $this->getPageWidth() - PDF_MARGIN_RIGHT - PDF_MARGIN_LEFT;
        $this->Image($image_file, PDF_MARGIN_LEFT, 20, $w);
        
        $y  = 60.5;
        $this->MultiCell($w, 0, 'ISO9001 (SIC.02.057.684)', 0, 'R', false, 1, PDF_MARGIN_LEFT, $y);
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
     * Устанавливает номер и дату документа
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
     * Возвращает состояние документа
     * 
     */
    function GetState()
    {
        return $this->state;
    }
}

/**
 * Класс, перегружающий методы базового класса для
 * формирования хедэра и футера с картинками SteelEmotion и адресом MaM UK
 */
class QCPdfForPA extends TCPDF {

    /**
     * Номер документа
     * 
     * @var mixed
     */
    var $mam_doc_no = '';
    
    /**
     * Дата создания документа
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
     * Устанавливает номер и дату документа
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
     * Возвращает состояние документа
     * 
     */
    function GetState()
    {
        return $this->state;
    }
}
