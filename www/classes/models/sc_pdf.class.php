<?php 
require_once APP_PATH . 'classes/models/default_pdf.php';
require_once APP_PATH . 'classes/models/order.class.php';
require_once APP_PATH . 'classes/models/sc.class.php';   

/**
 * Класс для формирования PDF версии Sale Confirmation
 * 
 * @version 20120309, zharkov
 */
class SCPdf extends DefaultPdf
{
    /**
     * Конструктор
     * 
     * @param mixed $owner_alias - алиас владелеца, обычно 'mam' или 'pa'
     * @return SCPdf
     */
    function SCPdf($owner_alias = 'mam', $config_name = 'default')
    {
        DefaultPdf::DefaultPdf($owner_alias, $config_name);
        
        if ($owner_alias == 'mam')
        {
            $this->pdf = new SCPdfForSE();
        }
        else
        {
            $this->pdf = new SCPdfForPA();
        }
    }
    
    /**
     * Формирует документ
     * 
     * @param mixed $document_id
     */
    function _generate($document_id)
    {        
        // поправка, в настройках стоит 20, для того чтобы в QC помещалась печать, во всех остальных должно быть 40
        $this->pdf->SetAutoPageBreak(TRUE, 40);
        
        $scs    = new SC();
        $sc     = $scs->GetById($document_id);
        $sc     = $sc['sc'];

        if (empty($sc)) return null;
        
        
        $orders = new Order();
        $order  = $orders->GetById($sc['order_id']);
        $order  = $order['order'];
        
        $positions = $scs->GetPositionsFull($sc['id']);

        
        // устанавливает дату и номер документа, нужно и для футера
        $this->pdf->SetDocNo($sc['doc_no']);
        
        // добавляет страницу        
        $this->pdf->AddPage();
        
        $this->_draw_line('B');
        
        $this->_set_font('B', 8);
        $this->pdf->MultiCell($this->width / 2, 0, 'Date : ' . $this->pdf->mam_doc_date, 0, 'L', false, 0);
        $this->pdf->MultiCell($this->width / 2, 0, 'Doc. No ' . $this->pdf->mam_doc_no, 0, 'R', false, 1);        
        
        $this->_draw_line();        
                
        $this->pdf->Ln(10);
        $this->_draw_name_and_value('To',           $order['company']['title']);
        $this->_draw_name_and_value('Attention',    $sc['person']['full_name']);
        
        $this->pdf->Ln(10);
        $this->_draw_name_and_value('Subject',      'Hot Rolled Steel Plates - Sale Confirmation');
        $this->_draw_name_and_value('Our Ref.',     (isset($order['biz']) ? $order['biz']['number_output'] : ''));
        $this->_draw_name_and_value('Your Ref.',    $order['buyer_ref']);
        
        $this->pdf->Ln(5);
        $this->_draw_line('B');
        
        $this->pdf->Ln(20);
        $this->_set_font('B', 16);
        $this->pdf->Cell(0, 0, 'Sale Confirmation', 0, 0, 'C');
        
        $this->pdf->Ln(30);
        $this->_draw_name_and_value('Product', 'Hot Rolled Steel Plates');
        
        $this->pdf->Ln(20);
        
        $dimension_unit = $order['dimension_unit'];
        $dimension_unit = $dimension_unit == 'in' ? 'In' : $dimension_unit;
        
        $weight_unit    = $order['weight_unit'];
        $weight_unit    = $weight_unit == 'mt' ? 'ton' : $weight_unit;
        
        $price_unit     = $order['price_unit'];
        $price_unit     = $price_unit == 'mt' ? 'ton' : $price_unit;
        
        $currency       = $order['currency'];
        $currency       = $currency == 'usd' ? '$' : ($currency == 'eur' ? '€' : $currency);
        
        
        $columns    = array(
            array('title' => 'Steel Grade', 'width' => 0, 'field' => 'steelgrade_title'),
            array('title' => 'Thickness, ' . $dimension_unit, 'width' => 0, 'field' => 'thickness'),
            array('title' => 'Width, ' . $dimension_unit, 'width' => 0, 'field' => 'width'),
            array('title' => 'Length, ' . $dimension_unit, 'width' => 0, 'field' => 'length'),
            array('title' => 'Qtty, pcs', 'width' => 0, 'field' => 'qtty', 'total' => true),
            array('title' => 'Weight, ' . $weight_unit, 'width' => 0, 'field' => 'weight', 'total' => array('precision' => 3,),),
            array('title' => 'Price, ' . $currency . '/' . $price_unit, 'width' => 0, 'field' => 'price'),
            array('title' => 'Value, ' . $currency, 'width' => 0, 'field' => 'value', 'total' => array('precision' => 2,)),
        );
        $this->_draw_simple_table($positions, $columns);
        
        // set some text to print
        $this->pdf->Ln(20);
        
        $name_and_value_settings = array('name' => array('width' => 200));
        
        // специальные требования
        if (in_array($order['delivery_point'], array('col', 'wex', 'fca')))
        {
            if (!empty($sc['delivery_point']))
            {
                $this->_draw_name_and_value('Collection Address',   $sc['delivery_point'], $name_and_value_settings); $this->pdf->Ln(5);
            }
            if (!empty($sc['delivery_date']))
            {
                $this->_draw_name_and_value('Load Readiness',       $sc['delivery_date'], $name_and_value_settings); $this->pdf->Ln(5);
            }
            if (!empty($sc['transport_mode']))
            {
                $this->_draw_name_and_value('Transport Mode',       $sc['transport_mode'], $name_and_value_settings); $this->pdf->Ln(5);
            }
        }
        else
        {
            if (!empty($sc['delivery_point']))
            {
                $this->_draw_name_and_value('Delivery Point',       $sc['delivery_point'], $name_and_value_settings); $this->pdf->Ln(5);
            }
            if (!empty($sc['delivery_date']))
            {
                $this->_draw_name_and_value('Delivery Time',        $sc['delivery_date'], $name_and_value_settings); $this->pdf->Ln(5);
            }
            if (!empty($sc['delivery_cost']))
            {
                $this->_draw_name_and_value('Delivery Cost',        $sc['delivery_cost'], $name_and_value_settings); $this->pdf->Ln(5);
            }
        }
        
        if (!empty( $order['invoicingtype']['title']))
        {
            $this->_draw_name_and_value('Invoicing Basis',      $order['invoicingtype']['title'], $name_and_value_settings); $this->pdf->Ln(5);
        }
        if (!empty($order['paymenttype']['title']))
        {
            $this->_draw_name_and_value('Payment Term',         $order['paymenttype']['title'], $name_and_value_settings); $this->pdf->Ln(5);
        }
        
        $this->pdf->Ln(10);
        
        if (!empty($sc['chemical_composition'])) 
        {
            $this->_draw_name_and_value('Chemical Composition', $sc['chemical_composition'], $name_and_value_settings); 
            $this->pdf->Ln(5);
        }
        
        if (!empty($sc['tolerances'])) 
        {
            $this->_draw_name_and_value('Tolerances on Dimensions', $sc['tolerances'], $name_and_value_settings); 
            $this->pdf->Ln(5);
        }
        
        if (!empty($sc['hydrogen_control'])) 
        {
            $this->_draw_name_and_value('Hydrogen Control', $sc['hydrogen_control'], $name_and_value_settings); 
            $this->pdf->Ln(5);
        }
        
        if (!empty($sc['surface_quality'])) 
        {
            $this->_draw_name_and_value('Surface Quality', $sc['surface_quality'], $name_and_value_settings); 
            $this->pdf->Ln(5);
        }
        
        if (!empty($sc['surface_condition'])) 
        {
            $this->_draw_name_and_value('Surface Condition', $sc['surface_condition'], $name_and_value_settings); 
            $this->pdf->Ln(5);
        }
        
        if (!empty($sc['side_edges'])) 
        {
            $this->_draw_name_and_value('Side Edges', $sc['side_edges'], $name_and_value_settings); 
            $this->pdf->Ln(5);
        }
        
        if (!empty($sc['front_and_back_ends'])) 
        {
            $this->_draw_name_and_value('Front & Back Ends', $sc['front_and_back_ends'], $name_and_value_settings); 
            $this->pdf->Ln(5);   
        }
        
        if (!empty($sc['origin'])) 
        {
            $this->_draw_name_and_value('Origin', $sc['origin'], $name_and_value_settings); 
            $this->pdf->Ln(5);
        }
        
        if (!empty($sc['marking'])) 
        {
            $this->_draw_name_and_value('Marking', $sc['marking'], $name_and_value_settings); 
            $this->pdf->Ln(5);
        }
        
        if (!empty($sc['packing'])) 
        {
            $this->_draw_name_and_value('Packing', $sc['packing'], $name_and_value_settings); 
            $this->pdf->Ln(5);
        }
        
        if (!empty($sc['stamping'])) 
        {
            $this->_draw_name_and_value('Stamping', $sc['stamping'], $name_and_value_settings); 
            $this->pdf->Ln(5);
        }
        
        if (!empty($sc['ust_standard'])) 
        {
            $this->_draw_name_and_value('UST Standard, class', $sc['ust_standard'], $name_and_value_settings); 
            $this->pdf->Ln(5);
        }
        
        if (!empty($sc['dunnaging_requirements'])) 
        {
            $this->_draw_name_and_value('Dunnaging Requirements', $sc['dunnaging_requirements'], $name_and_value_settings); 
            $this->pdf->Ln(5);
        }
        
        if (!empty($sc['documents_supplied'])) 
        {
            $this->_draw_name_and_value('Documents Supplied', $sc['documents_supplied'], $name_and_value_settings); 
            $this->pdf->Ln(5);
        }
        
        if (!empty($sc['inspection'])) 
        {
            $this->_draw_name_and_value('Inspection', $sc['inspection'], $name_and_value_settings); 
            $this->pdf->Ln(5);
        }
        
        if (!empty($sc['delivery_form'])) 
        {
            $this->_draw_name_and_value('Delivery Condition', $sc['delivery_form'], $name_and_value_settings); 
            $this->pdf->Ln(5);
        }
        
        if (!empty($sc['reduction_of_area'])) 
        {
            $this->_draw_name_and_value('Reduction of Area', $sc['reduction_of_area'], $name_and_value_settings); 
            $this->pdf->Ln(5);
        }
        
        if (!empty($sc['testing'])) 
        {
            $this->_draw_name_and_value('Testing', $sc['testing'], $name_and_value_settings); 
            $this->pdf->Ln(5);
        }
        
        if (!empty($sc['notes'])) 
        {
            $this->_draw_name_and_value('Notes', $sc['notes'], $name_and_value_settings); 
            $this->pdf->Ln(5);
        }
        
        if (!empty($sc['qctype_id'])) 
        {
            $this->_draw_name_and_value('Quality Certificate', $sc['qctype']['title'], $name_and_value_settings);
        }
        
        $this->pdf->Ln(20);
        $this->pdf->Cell(0, 0, 'We accept quality claims only on originally supplied, not on cut or otherwise processed material.', 0, 1, 'L');
        $this->pdf->Cell(0, 0, 'Claim acceptance period is 30 days from delivery.', 0, 1, 'L');
        $this->pdf->Cell(0, 0, 'Any rejected or claimed goods must be made available for our inspection and/or collection.', 0, 1, 'L');
        $this->pdf->Cell(0, 0, 'If you disagree with any of the above terms please respond immediately.', 0, 1, 'L');
        
        $this->pdf->Ln(10);
        $this->pdf->Cell(0, 0, 'All our transactions are subject to our Terms of Supply.', 0, 1, 'L');
        
        // start авторазрыв страницы - определение параметров
        $line_height= 12.5;// высота строки CELL
        $curr_posY  = $this->pdf->getY();// текущая позиция Y
        $max_posY   = 660;// позиция Y, при превыщении которой необходимы манипуляции
        $pad_blocks = array(10, 10, 20, 10);// набор pad-блоков, которые позволяют сжать высоту, но не менее 5pt
        $pad_blocks_total_min = array_sum($pad_blocks) - count($pad_blocks) * 5;
        $posY_limit_over = 0;// количество превышенного pt
        
        // Примечание:  пока не появится превышение лимита, т.е. $posY_limit_over <= 0,
        //              разрыв производится не будет.
        //              высота будет компенсироваться за счет $pad_blocks (уменьшая свою, при необходимости)
        
        $deltaY = $curr_posY - $max_posY;
        if ($deltaY > 0)
        {
            $posY_limit_over = $deltaY - $pad_blocks_total_min;
            if ($posY_limit_over <= 0)
            {
                $tmp_deltaY = $deltaY;
                foreach($pad_blocks as $key => $value)
                {
                    if ($tmp_deltaY <= 0) break;
                    
                    $value -= 5;
                    
                    if ($value >= $tmp_deltaY)
                    {
                        $pad_blocks[$key]   -= $tmp_deltaY;
                        $tmp_deltaY         = 0;
                        continue;
                    }
                    
                    $pad_blocks[$key]   = 5;
                    $tmp_deltaY         -= $value;
                }
            }
        }
        // end авторазрыв страницы - определение параметров
        
        $this->pdf->Ln($pad_blocks[0]);
        $this->pdf->Cell(0, 0, 'All bank charges otside the beneficiary\'s bank, to be for the payer\'s account.', 0, 1, 'L');
        $this->pdf->Cell(0, 0, 'Interest on late payment to be charged at ECB rate + 7% as per the EU Directive 2000/35/EC.', 0, 1, 'L');
        $this->pdf->Cell(0, 0, 'No other standard terms and conditions to apply to this transaction.', 0, 1, 'L');
        
        // page-breaking
        if ($posY_limit_over > 0 && $posY_limit_over / $line_height <= 4)// 4 - это количество строк, которое позволит осуществять page-breaking
        {
            $this->pdf->endPage();
            $this->pdf->AddPage();
        }
        
        $this->pdf->Cell(0, 0, 'This sale is to be treated by both parties as strictly independent of other transactions and no offset is allowed.', 0, 1, 'L');
        
        $this->pdf->Ln($pad_blocks[1]);
        $this->pdf->Cell(0, 0, 'Unless signed and returned to us within 2 working days, the sale is null and void.', 0, 1, 'L');
        
        $this->pdf->Ln($pad_blocks[2]);
        $name_and_value_settings['name']    = array('align' => 'R', 'width' => 395);
        $name_and_value_settings['value']   = array('border' => 'B', 'width' => 100);
        
        $co_with_city = $order['company']['title'] . (isset($order['company']) && !empty($order['company']['city']) ? ', ' . $order['company']['city']['title'] : '');
        
        $this->_draw_name_and_value('Accepted for ' . $co_with_city, '', $name_and_value_settings);        
        $this->pdf->Ln($pad_blocks[3]);
        $this->_draw_name_and_value('Date', '', $name_and_value_settings);
                
        // сохраняет документ в cache
        $file_name  = $sc['doc_no_short'] . '_' . $order['company']['doc_no'] . (isset($order['biz']) ? '_' . str_replace('.', '', $order['biz']['number_output']) : '');
        $file_name  = preg_replace('/[^a-zA-Z0-9_-]/', '', Translit::Encode($file_name)) . '.pdf';        
        
        // удаляет предыдущий атачмент
        $attachments = new Attachment();
        if (!empty($sc['attachment_id'])) $attachments->Remove($sc['attachment_id']);

        // сохраняет документ в cache
        $file_name  = APP_CACHE . $file_name;
        $this->pdf->Output($file_name, 'F');
        
        // сохраняем атачмент в бд
        $attachment_id = $this->_save_attachment('sc', $sc['id'], $file_name);        
        
        // обновляет связь сертификата с созданным пдф
        $scs = new SC();
        $scs->UpdateAttachment($sc['id'], $attachment_id);
        
        if (!empty($atachment_id))
        {
            $attachments->LinkToObject($atachment_id, 'order', $order['id']);
            $attachments->LinkToObject($atachment_id, 'biz',   $order['biz']['id']);
        }
    }
}


/**
 * Класс, перегружающий методы базового класса для
 * формирования хедэра и футера с картинками SteelEmotion и адресом MaM UK
 */
class SCPdfForPA extends TCPDF {

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

/**
 * Класс, перегружающий методы базового класса для
 * формирования хедэра и футера с картинками SteelEmotion и адресом MaM UK
 */
class SCPdfForSE extends TCPDF {

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
