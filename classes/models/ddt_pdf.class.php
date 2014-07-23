<?php
require_once APP_PATH . 'classes/models/default_pdf.php';
require_once APP_PATH . 'classes/models/ddt.class.php';

/**
 * Класс для формирования PDF версии DDT
 * 
 * @version 20121105, d10n
 */
class DDTPdf extends DefaultPdf
{
    /**
     * Конструктор
     * 
     * @param mixed $owner_alias - алиас владелеца, обычно 'mam' или 'pa'
     * @return QCPdf
     */
    function DDTPdf($owner_alias = 'mam', $config_name = 'marginless')
    {
        DefaultPdf::DefaultPdf($owner_alias, $config_name);
        
        $this->pdf = new DDTPdfForSE();
    }
    
    /**
     * Формирует документ
     * 
     * @param mixed $document_id
     */
    function _generate($document_id)
    {        
        
        debug('1671', $document_id);
        $modelDDT   = new DDT();
        $ddt        = $modelDDT->GetForPDF($document_id);
        
        if (empty($ddt)) return null;
        
        // добавляет страницу
        $this->pdf->AddPage();
        
        $this->_set_font('', 5);
        $this->_draw_cell($ddt['ddt'], 425, 20, 137);
        $this->_set_font('', 8);
        
        if (!empty($ddt['logo']))
        {
            $this->pdf->Image($ddt['logo'], 107, 10, 130);
            $this->_draw_cell($ddt['logo_description'], 33, 55, 283, 'C');
        }
        
        $this->_set_font('', 10);
        
        $delta_x = 20;
        $this->_draw_cell($ddt['documento'], 316+$delta_x, 43, 246-$delta_x);// documento
        
        
        $this->_draw_cell($ddt['tipo'], 316, 89, 67, 'C');// tipo
        $this->_draw_cell($ddt['numero'], 383, 89, 50, 'C');// numero
        $this->_draw_cell($ddt['data'], 433, 89, 99, 'C');// data
        $this->_draw_cell($ddt['pagina'], 532, 89, 30, 'C');// pagina
        
        if ($ddt['fatturare_a_desc'] == 2)
        {
            $this->_set_font('', 5);
            $this->_draw_cell('DESTINATARIO', 317, 103, 240, 'L', true);// futturare
            $this->_set_font('', 10);
        }
        
        $this->_draw_cell($ddt['destinazione_merce'], 33, 123, 283, 'C');// destinazione
        $this->_draw_cell($ddt['fatturare_a'], 316, 123, 246, 'C');// futturare
        
        $delta_x = 10;
        $this->_draw_cell($ddt['codice_cliente'], 33+$delta_x, 189, 109-$delta_x);// codice cliente
        $this->_draw_cell($ddt['partita_iva'], 132+$delta_x, 189, 83-$delta_x);// partita iva
        $this->_draw_cell($ddt['agente'], 215+$delta_x, 189, 347-$delta_x);// agente
        
        $this->_draw_cell($ddt['codice_per_fornitore'], 33+$delta_x, 218, 182-$delta_x);// codice per fornitore
        $this->_draw_cell($ddt['pagamento'], 215+$delta_x, 218, 347-$delta_x);// pagamento
        
        $this->_draw_cell($ddt['inizio_trasporto'], 33+$delta_x, 249, 283-$delta_x);// inizio trasporto
        $this->_draw_cell($ddt['causale_trasporto'], 316+$delta_x, 249, 246-$delta_x);// causale trasporto
        
        $this->_draw_cell($ddt['trasporto_a_cura_del'], 33+$delta_x, 279, 283-$delta_x);// trasporto a cura del
        $this->_draw_cell($ddt['porto'], 316+$delta_x, 279, 246-$delta_x);// porto
        
        
        
        $item_height = 326;

        foreach ($ddt['items'] as $set)
        {
            $delta_x = 10;  // 50
            $item_height += 5;
            $this->_draw_cell($set['header'], 66+$delta_x, $item_height, 368-$delta_x);// descrizione merce
            $item_height += 18;
            
            $delta_x = 10;
            foreach ($set['data'] as $data)
            {
                foreach ($data['list'] as $item)
                {
                    $this->_draw_cell('1', 33, $item_height, 33, 'C');
                    $this->_draw_cell($item['steelgrade'] . '   ' . $item['dimensions'], 66+$delta_x, $item_height);
    /*
                    $this->_draw_cell($item['steelgrade'], 66+$delta_x, $item_height, 100-$delta_x);
                    $this->_draw_cell($item['dimensions'], 166+$delta_x, $item_height, 268-$delta_x);
    */
                    $this->_draw_cell($item['um'], 434+$delta_x, $item_height, 67-$delta_x);// u.m.
                    $this->_draw_cell($item['quantita'], 501+$delta_x, $item_height, 61-$delta_x-10, 'R');// quantita
                    $item_height += 13;
                }
                                
                if (!empty($data['footer'])) $this->_draw_cell($data['footer'], 66+$delta_x, $item_height, 368-$delta_x);
                $item_height = $this->pdf->GetY() + 13;
            }
        }
        
        $this->_set_font('B', 14);
        $delta_x = 50;
        $this->_draw_cell($ddt['pl_footer'], 66+$delta_x, $item_height+40, 368-$delta_x);
        $this->_set_font('', 10);
        
        $delta_x = 10;
        $this->_draw_cell($ddt['annotazione'], 33+$delta_x, 676, 529-$delta_x);// annotazione
        $this->_draw_cell($ddt['annotazione_line2'], 33+$delta_x, 690, 529-$delta_x);// annotazione
        
        $this->_draw_cell($ddt['n_colli'], 33, 721, 33, 'C');// n colli
        $this->_draw_cell($ddt['peso_lordo'], 66+$delta_x, 721, 82-$delta_x);// peso lordo
        $this->_draw_cell($ddt['peso_netto'], 148+$delta_x, 721, 170-$delta_x);// peso netto
        $this->_draw_cell($ddt['aspetto_esteriore_dei_beni'], 320+$delta_x, 721, 242-$delta_x);// aspetti esterione
        
        $this->_draw_cell($ddt['vettore'], 33+$delta_x, 745, 350-$delta_x);// vettore
        $this->_draw_cell($ddt['data_e_ora_ritiro'], 383+$delta_x, 745, 179-$delta_x);// data e ora ritiro
        
        $this->_draw_cell($ddt['firma_conducente'], 33+$delta_x, 809, 115-$delta_x);// firma conducente
        $this->_draw_cell($ddt['firma_destinatario'], 148+$delta_x, 809, 235-$delta_x);// firma destinatarion
        $this->_draw_cell($ddt['firma_vettore'], 383+$delta_x, 809, 179-$delta_x);// firma vettore
        
        // удаляет предыдущий атачмент
        $attachments = new Attachment();
        if (!empty($ddt['attachment_id'])) $attachments->Remove($ddt['attachment_id']);

        // сохраняет документ в cache
        $file_name  = $ddt['doc_no'];
        $file_name  = preg_replace('/[^a-zA-Z0-9_-]/', '_', Translit::Encode($file_name)) . '_' . $ddt['filename_suffix'] . '.pdf';
        $file_name  = APP_CACHE . $file_name;
        $this->pdf->Output($file_name, 'F');
        
        // сохраняем атачмент в бд
        $attachment_id = $this->_save_attachment('ddt', $ddt['id'], $file_name);
        
        // обновляет связь сертификата с созданным пдф
        $modelDDT->UpdateAttachment($ddt['id'], $attachment_id);
        
        // выставляет актуальность
        $modelDDT->SetAsActual($ddt['id']);
    }
    
    /**
     * 
     * @param string $text
     * @param int $x_pos
     * @param int $y_pos
     * @param int $width
     * @param string $text_align
     */
    private function _draw_cell($text, $x_pos, $y_pos, $width = 0, $text_align = 'L', $filler = false)
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
            $this->pdf->SetFillColor(255,255,255);
        }
        
        $this->pdf->MultiCell($w, $h, $txt, $border, $align, $fill, $ln, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
    }

}


/**
 * Класс, перегружающий методы базового класса для
 * формирования хедэра и футера с картинками SteelEmotion и адресом MaM UK
 */
class DDTPdfForSE extends TCPDF
{
    //Page header
    public function Header() 
    {
        $file       = K_PATH_IMAGES . 'pdf/ddt.png';
        $x          = '';
        $y          = '';
        $w          = 0;
        $h          = 0;
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

    // Page footer
    public function Footer() 
    {
        
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
