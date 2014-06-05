<?php
require_once APP_PATH . 'classes/core/Pagination.class.php';

require_once APP_PATH . 'classes/models/album.class.php';
require_once APP_PATH . 'classes/models/attachment.class.php';
require_once APP_PATH . 'classes/models/stockoffer.class.php';
require_once APP_PATH . 'classes/models/stockoffer_pdf.class.php';

class MainController extends ApplicationController
{
    public function MainController()
    {
        ApplicationController::ApplicationController();
        
        $this->authorize_before_exec['edit']    = ROLE_STAFF;
        $this->authorize_before_exec['index']   = ROLE_STAFF;
        $this->authorize_before_exec['remove']  = ROLE_STAFF;
        $this->authorize_before_exec['view']    = ROLE_STAFF;
        
        $this->breadcrumb   = array('Stock Offers' => '/stockoffers');
        $this->context      = true;
    }
    
    /**
     * Форма создания и редактирования StockOffer
     * url: /stockoffer/add
     * url: /stockoffer/{oc_id}/edit
     * 
     * @version 20130228, d10
     */
    public function edit()
    {
        $stockoffer_id = Request::GetInteger('id', $_REQUEST);
        
        $positions  = array();
        $stockoffer = array(
            'lang'                  => STOCKOFFER_LANG_ALIAS_EN,
            'is_colored'            => 0,
            'sort_by'               => '',
            'header_attachment_id'  => 0,
            'banner1_attachment_id' => 0,
            'banner2_attachment_id' => 0,
            'footer_attachment_id'  => 0,
        );
        
        $modelStockOffer    = new StockOffer();
        
        if ($stockoffer_id > 0)
        {
            $stockoffer = $modelStockOffer->GetById($stockoffer_id);
            if (!isset($stockoffer['stockoffer'])) _404();
            
            $stockoffer = $stockoffer['stockoffer'];
            $positions = $modelStockOffer->GetPositions($stockoffer_id);
        }
        
        $is_saving          = isset($_REQUEST['btn_save']);
        $is_adding_positions= isset($_REQUEST['btn_addpositions']);
        
        if ($is_saving || $is_adding_positions)
        {
            $form = isset($_REQUEST['form']) ? $_REQUEST['form'] : array();
            
            $id                     = isset($stockoffer['id']) ? $stockoffer['id'] : 0;
            $lang                   = Request::GetString('lang', $form);
            $title                  = Request::GetString('title', $form);
            $description            = Request::GetString('description', $form);
            $header_attachment_id   = Request::GetInteger('header_attachment_id', $form);
            $is_show_header_image   = Request::GetBoolean('is_show_header_image', $form);
            $delivery_point         = Request::GetString('delivery_point', $form);
            $delivery_cost          = Request::GetString('delivery_cost', $form);
            $delivery_time          = Request::GetString('delivery_time', $form);
            $payment_terms          = Request::GetString('payment_terms', $form);
            $is_colored             = Request::GetBoolean('is_colored', $form);
            $sort_by                = Request::GetString('sort_by', $form);
            $columns                = isset($form['columns']) ? $form['columns'] : array();
            $columns                = implode(',', array_keys($columns));
            $quality_certificate    = Request::GetString('quality_certificate', $form);
            $validity               = Request::GetString('validity', $form);
            $banner1_attachment_id  = Request::GetInteger('banner1_attachment_id', $form);
            $is_show_banner1        = Request::GetBoolean('is_show_banner1', $form);
            $banner2_attachment_id  = Request::GetInteger('banner2_attachment_id', $form);
            $is_show_banner2        = Request::GetBoolean('is_show_banner2', $form);
            $footer_attachment_id   = Request::GetInteger('footer_attachment_id', $form);;
            $is_show_footer_image   = Request::GetBoolean('is_show_footer_image', $form);
            $pdf_attachment_id      = 0;
            
            
            $okay_flag = true;

            if ($is_saving)
            {
                // тут могут быть проверки
            }
            
            if ($okay_flag)
            {
                $stockoffer_saved = $modelStockOffer->Save($id, $lang, $title, $description, $header_attachment_id, $is_show_header_image,
                    $delivery_point, $delivery_cost, $delivery_time, $payment_terms,
                    $is_colored, $sort_by, $columns, $quality_certificate, $validity,
                    $banner1_attachment_id, $is_show_banner1, $banner2_attachment_id, $is_show_banner2,
                    $footer_attachment_id, $is_show_footer_image, $pdf_attachment_id);
                
                if (!array_key_exists('stockoffer', $stockoffer_saved))
                {
                    $this->_message('Error Saving StockOffer !', MESSAGE_ERROR);
                    $okay_flag = false;
                }
            }
            else
            {
                $stockoffer = array_merge($stockoffer, $form);
            }
            
            if ($okay_flag)
            {
                $stockoffer_saved = $stockoffer_saved['stockoffer'];

                if ($is_adding_positions)
                {
                    $this->_redirect(array('target', 'stockoffer:' . $stockoffer_saved['id'], 'positions'), false);
                }
                else
                {
                    // start формирование PDF-файла
                    // удаление предидущего
                    
                    $modelAttachment = new Attachment();
                    if ($stockoffer_saved['pdf_attachment_id'] > 0) $modelAttachment->Remove($stockoffer_saved['pdf_attachment_id']);
                    
                    $modelStockOfferPdf = new StockOfferPdf();
                    $modelStockOfferPdf->Generate($stockoffer_saved['id']);
                    // end формирование PDF-файла
                    
                    $this->_message('Stock Offer was successfully saved', MESSAGE_OKAY);
                    $this->_redirect(array('stockoffers'), false);
                }
            }
        }
        
        $this->page_name    = empty($stockoffer_id) ? 'New Stock Offer' : 'Edit Stock Offer';
        $this->js           = 'stockoffer_main';
        
        $this->breadcrumb[$this->page_name] = '';

        $modelAttachment    = new Attachment();
        $stockoffer         = $modelAttachment->FillAttachmentInfo($stockoffer, 'header_attachment_id', 'header_attachment');
        $stockoffer         = $modelAttachment->FillAttachmentInfo($stockoffer, 'banner1_attachment_id', 'banner1_attachment');
        $stockoffer         = $modelAttachment->FillAttachmentInfo($stockoffer, 'banner2_attachment_id', 'banner2_attachment');
        $stockoffer         = $modelAttachment->FillAttachmentInfo($stockoffer, 'footer_attachment_id', 'footer_attachment');

        $this->_assign('form',          $stockoffer);
        $this->_assign('positions',     $positions);
        $this->_assign('firstposition', current($positions));
        

        $position_weight_unit           = '';
        $position_weight_unit_count     = 0;
        $position_dimension_unit        = '';
        $position_dimension_unit_count  = 0;
        $position_price_unit            = '';
        $position_price_unit_count      = 0;
        $position_currency              = '';
        $position_currency_count        = 0;
        
        foreach ($positions as $position)
        {
            $position = $position['steelposition'];
            
            if ($position_weight_unit != $position['weight_unit'])
            {
                $position_weight_unit = $position['weight_unit'];
                $position_weight_unit_count++;
            }
            
            if ($position_dimension_unit != $position['dimension_unit'])
            {
                $position_dimension_unit = $position['dimension_unit'];
                $position_dimension_unit_count++;
            }

            if ($position_price_unit != $position['price_unit'])
            {
                $position_price_unit = $position['price_unit'];
                $position_price_unit_count++;
            }
            
            if ($position_currency != $position['currency'])
            {
                $position_currency = $position['currency'];
                $position_currency_count++;
            }                
        }
        
        $this->_assign('position_weight_unit',          $position_weight_unit);
        $this->_assign('position_weight_unit_count',    $position_weight_unit_count);
        $this->_assign('position_dimension_unit',       $position_dimension_unit);
        $this->_assign('position_dimension_unit_count', $position_dimension_unit_count);
        $this->_assign('position_price_unit',           $position_price_unit);
        $this->_assign('position_price_unit_count',     $position_price_unit_count);
        $this->_assign('position_currency',             $position_currency);
        $this->_assign('position_currency_count',       $position_currency_count);
                
        
        $modelAlbum = new Album();
        $this->_assign('header_album',     $modelAlbum->GetByAlias('stockoffer_header'));
        $this->_assign('banner1_album',    $modelAlbum->GetByAlias('stockoffer_banner1'));
        $this->_assign('banner2_album',    $modelAlbum->GetByAlias('stockoffer_banner2'));
        $this->_assign('footer_album',     $modelAlbum->GetByAlias('stockoffer_footer'));
        
        $this->_display('edit');
    }
   
    /**
     * Список StockOffers
     * @url: /stockoffer
     * @url: /stockoffers
     * 
     * @version 20130228, d10n
     */
    public function index()
    {
        $modelStockOffer    = new StockOffer();
        $rowset             = $modelStockOffer->GetList($this->page_no);
        
        $this->page_name    = 'Stock Offers';
        $this->breadcrumb   = array($this->page_name => '');
        
        $this->_assign('list',      $rowset['data']);
        $this->_assign('count',     $rowset['count']);

        $pager = new Pagination();
        $this->_assign('pager_pages', $pager->PreparePages($this->page_no, $rowset['count']));
        
        $this->_display('index');
    }
    
    /**
     * Удаляет StockOffers
     * url: /stockoffer/{stockoffer_id}/remove
     * 
     * @version 20130228, d10n
     */
    public function remove()
    {
        $id = Request::GetInteger('id',  $_REQUEST);

        if ($id <= 0) _404();

        $modelStockOffer    = new StockOffer();
        $stockoffer         = $modelStockOffer->GetById($id);
        if (empty($stockoffer)) _404();
        
        foreach ($modelStockOffer->GetPositions($id) as $row)
        {
            $modelStockOffer->RemovePosition($id, $row['steelposition_id']);
        }
        
        $modelStockOffer->Remove($id);
        
        $this->_message('Stock Offer was successfully removed', MESSAGE_OKAY);
        
        $this->_redirect(array('stockoffers'));
    }
    
    /**
     * Отобраает страницу просмотра Stock Offer
     * url: /stockoffer/{stockoffer_id}
     * 
     * @version 20130311, zharkov
     */
    public function view()
    {
        $stockoffer_id = Request::GetInteger('id', $_REQUEST);
        if ($stockoffer_id <= 0) _404();
        
        
        $modelStockOffer    = new StockOffer();        
        $stockoffer         = $modelStockOffer->GetById($stockoffer_id);
        $positions          = $modelStockOffer->GetPositions($stockoffer_id);
        
        if (empty($stockoffer)) _404();
        
                
        $position_weight_unit           = '';
        $position_weight_unit_count     = 0;
        $position_dimension_unit        = '';
        $position_dimension_unit_count  = 0;
        $position_price_unit            = '';
        $position_price_unit_count      = 0;
        $position_currency              = '';
        $position_currency_count        = 0;
        
        foreach ($positions as $position)
        {
            $position = $position['steelposition'];
            
            if ($position_weight_unit != $position['weight_unit'])
            {
                $position_weight_unit = $position['weight_unit'];
                $position_weight_unit_count++;
            }
            
            if ($position_dimension_unit != $position['dimension_unit'])
            {
                $position_dimension_unit = $position['dimension_unit'];
                $position_dimension_unit_count++;
            }

            if ($position_price_unit != $position['price_unit'])
            {
                $position_price_unit = $position['price_unit'];
                $position_price_unit_count++;
            }
            
            if ($position_currency != $position['currency'])
            {
                $position_currency = $position['currency'];
                $position_currency_count++;
            }                
        }
        
        $this->_assign('position_weight_unit',          $position_weight_unit);
        $this->_assign('position_weight_unit_count',    $position_weight_unit_count);
        $this->_assign('position_dimension_unit',       $position_dimension_unit);
        $this->_assign('position_dimension_unit_count', $position_dimension_unit_count);
        $this->_assign('position_currency',             $position_currency);
        $this->_assign('position_currency_count',       $position_currency_count);
        $this->_assign('position_price_unit',           $position_price_unit);
        $this->_assign('position_price_unit_count',     $position_price_unit_count);

        $stockoffer = $stockoffer['stockoffer'];

        $this->breadcrumb = array(
            'Stock Offers'           => '/stockoffers',
            $stockoffer['doc_no']   => ''
        );

        $this->page_name    = $stockoffer['doc_no'];
        $this->topcontext   = 'view';
        
        $this->_assign('form',          $stockoffer);
        $this->_assign('positions',     $positions);
        $this->_assign('firstposition', current($positions));

        
        $this->_display('view');       
    }
}