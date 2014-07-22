<?php 
require_once APP_PATH . 'classes/models/biz.class.php';
require_once APP_PATH . 'classes/models/message.class.php';
require_once APP_PATH . 'classes/models/email.class.php';
require_once APP_PATH . 'classes/models/order.class.php';
require_once APP_PATH . 'classes/mailers/emailmailer.class.php';
require_once APP_PATH . 'classes/common/mimetype.class.php';
require_once APP_PATH . 'classes/common/parser.class.php';
require_once APP_PATH . 'classes/services/kcaptcha/kcaptcha.php';

class MainController extends ApplicationController
{
    function MainController()
    {
        ApplicationController::ApplicationController();
             
        $this->authorize_before_exec['testemail']           = ROLE_ADMIN;
        $this->authorize_before_exec['index']               = ROLE_STAFF;
        $this->authorize_before_exec['showpicture']         = ROLE_STAFF;
        $this->authorize_before_exec['shownopicture']       = ROLE_STAFF;
        $this->authorize_before_exec['showattachment']      = ROLE_STAFF;
        $this->authorize_before_exec['clearattachments']    = ROLE_ADMIN;
        $this->authorize_before_exec['testmailsend']        = ROLE_ADMIN;
    }

    function testsubject()
    {
        
        $objects = imap_mime_header_decode('Azov 2 2013.docx');

        $decoded_string = '';
        
        foreach ($objects as $object)
        {
            $charset    = strtoupper($object->charset);
            $text       = $object->text;
            
            if (!$this->_is_encoding_supported($charset))
            {
                $charset = mb_detect_encoding($text, 'ASCII, UTF-8, CP1251');
                
                if (!in_array(strtoupper($charset), array('ASCII', 'UTF-8', 'CP1251', 'WINDOWS-1251')))
                {
                    $charset = mb_detect_encoding($text, 'ISO-8859-1, ASCII, UTF-8');
                }
            }
            
            $decoded_string .= mb_convert_encoding($text, 'UTF-8', $charset);            
        }
        
        echo $decoded_string;

        
/*
        $encoded_string = "=?koi8-r?B?4drP1CDEzNEgy8zFys3J1MXM0SBSSC0yMC80L0Y=?=";
        $objectStdClassMimeHeaderDecode = imap_mime_header_decode($encoded_string);
        $decoded_string = '';

        foreach ($objectStdClassMimeHeaderDecode as $objectItem)
        {
            dg(mb_convert_encoding($objectItem->text, 'UTF-8', $objectItem->charset));

            $objectItem->charset = strtoupper($objectItem->charset);
    
            if ($this->_is_encoding_supported($objectItem->charset))
            {
                //$objectItem->charset = mb_detect_encoding($objectItem->text, 'ISO-8859-1, ASCII, UTF-8, CP1251');
                
                $objectItem->charset = mb_detect_encoding($objectItem->text, 'ASCII, UTF-8, CP1251');
                
                if (!in_array(strtoupper($objectItem->charset), array('ASCII', 'UTF-8', 'CP1251', 'WINDOWS-1251')))
                {
                    $objectItem->charset = mb_detect_encoding($objectItem->text, 'ISO-8859-1, ASCII, UTF-8');
                }
                
                $decoded_string .= mb_convert_encoding($objectItem->text, 'UTF-8', $objectItem->charset);
            }
        }
        
        echo 'done: ' . $decoded_string;
*/        
    }
    
    private function _is_encoding_supported($encoding)
    {
        
        $supported_character_encodings = array(
            'UCS-4', 'UCS-4BE', 'UCS-4LE', 'UCS-2', 'UCS-2BE', 'UCS-2LE',
            'UTF-32', 'UTF-32BE', 'UTF-32LE', 'UTF-16', 'UTF-16BE', 'UTF-16LE', 'UTF-7', 'UTF7-IMAP', 'UTF-8',
            'ASCII', 'EUC-JP', 'SJIS', 'EUCJP-WIN', 'SJIS-WIN', 'ISO-2022-JP', 'ISO-2022-JP-MS', 'CP932','CP51932',
            'SJIS-MAC', 'SJIS-MOBILE#DOCOMO', 'SJIS-MOBILE#KDDI', 'SJIS-MOBILE#SOFTBANK',
            'UTF-8-MOBILE#DOCOMO', 'UTF-8-MOBILE#KDDI-A', 'UTF-8-MOBILE#KDDI-B', 'UTF-8-MOBILE#SOFTBANK', 'ISO-2022-JP-MOBILE#KDDI',
            'JIS', 'JIS-MS', 'CP50220', 'CP50220RAW', 'CP50221', 'CP50222',
            'ISO-8859-1', 'ISO-8859-2', 'ISO-8859-3', 'ISO-8859-4', 'ISO-8859-5',
            'ISO-8859-6', 'ISO-8859-7', 'ISO-8859-8', 'ISO-8859-9', 'ISO-8859-10',
            'ISO-8859-13', 'ISO-8859-14', 'ISO-8859-15',
            'BYTE2BE', 'BYTE2LE', 'BYTE4BE', 'BYTE4LE', 'BASE64', 'HTML-ENTITIES',
            '7BIT', '8BIT', 'EUC-CN', 'CP936', 'GB18030', 'HZ', 'EUC-TW', 'CP950',
            'BIG-5', 'EUC-KR',
            'UHC', 'CP949',
            'ISO-2022-KR',
            'WINDOWS-1251', 'CP1251',
            'WINDOWS-1252', 'CP1252',
            'CP866', 'IBM866',
            'KOI8-R'
        );
        $non_standart_encodings = array('DEFAULT', '3D', 'WINDOWS-1250');
        
        $encoding = strtoupper($encoding);

        return in_array($encoding, $supported_character_encodings);
    }    
    
    function testmailsend()
    {
        require_once APP_PATH . 'classes/mailers/emailmailer.class.php';
        
        $emailmailer = new EmailMailer();
        $emailmailer->Send(array(
            'sender_address'    => 'emotion@steelemotion.com',
            'recipient_address' => 'digirulez@gmail.com',
            'cc_address'        => 'paul.gusakov@gmail.com',
            'bcc_address'       => 'dima.zharkov@gmail.com',
            'sender_domain'     => 'steelemotion.com',
            'type_id'           => 2,   // outbox
            'date_mail'         => date("Y-m-d H:i:s"),
            'to'                => 'Kvadrosoft',
            'attention'         => 'Zharkov',
            'subject'           => 'subject',
            'our_ref'           => 'our_ref',
            'your_ref'          => 'your_ref',
            'description'       => 'bla bla bla',
            'signature'         => 'signature',
            'signature3'        => 'signature3',
            'signature2'        => 'signature2',
            'title'             => 'steelemotion test bcc',
            'test_mode'         => 'yes'
        ));
        
        echo 'okay';
    }
    
    function clearattachments()
    {
        $object_alias       = 'ra';
        $object_id          = 2;
        
        $modelAttachment    = new Attachment();        
        $attachments        = $modelAttachment->GetList($object_alias, $object_id);
        
        foreach ($attachments['data'] as $attachment)
        {
            $attachment = $attachment['attachment'];

            if ($attachment['object_alias'] == $object_alias && $attachment['object_id'] == $object_id)
            {
                $modelAttachment->Remove($attachment['id']);
            }
        }
        
        echo('complete!');
    }
    
    function deprecated_testemail()
    {
        $email = array(
            'sender_address'    => 'STEELemotion (lamiere) <lamiere@steelemotion.com>',
            'recipient_address' => '"Dima Zharkov" <dima.zharkov@gmail.com>',
            'cc_address'        => '',
            'title'             => 'TEST FROM ' . APP_HOST,
            'description'       => 'TEST',
            'type_id'           => 0,
            'date_mail'         => date("Y-m-d H:i:s"),
            'to'                => '',
            'attention'         => '',
            'subject'           => '',
            'our_ref'           => '',
            'your_ref'          => '',
            
        );
        $attachments    = array();
        
        $emailmailer    = new EmailMailer();
        $result         = $emailmailer->Send($email, $attachments);
        
        echo 'okay';
    }
    
    /**
     * Отображает страницу с извещением о том, что доступ закрыт
     * 
     */
    function permissiondenied()
    {        
        if (isset($_SESSION['_permissiondenied']))
        {
            unset($_SESSION['_permissiondenied']);            
            $this->_display('permissiondenied');    
        }
        else
        {
            _404();
        }
    }
    
    /**
     * Отображает главную страницу сайта
     * url: /
     */
    function index()
    {
/*        
        $content = '<p>I need a release for at least 2 of the following for pick up tomorrow morning.</p><p> </p><p>PO # 554326</p><p> </p><table style="width:778px;" border="0" cellspacing="0" cellpadding="0"><tbody><tr><td class="xl67" style="width:128px;height:20px;">A572 Gr50</td><td class="xl67" style="width:64px;">4</td><td class="xl67" style="width:64px;">98</td><td class="xl67" style="width:64px;">199</td><td class="xl68" style="width:64px;">22,123</td><td class="xl67" style="width:64px;">1</td><td class="xl67" style="width:64px;">0.42</td><td class="xl67" style="width:105px;">Houston</td><td class="xl67" style="width:97px;">Prompt</td><td class="xl69" style="width:64px;">A578-B</td></tr><tr><td class="xl67" style="height:20px;">A572 Gr50</td><td class="xl67">4</td><td class="xl67">98</td><td class="xl67">200</td><td class="xl68">22,234</td><td class="xl67">1</td><td class="xl67">0.42</td><td class="xl67">Houston</td><td class="xl67">Prompt</td><td class="xl70">A578-B</td></tr><tr><td class="xl67" style="height:20px;">A572 Gr50</td><td class="xl67">4</td><td class="xl67">98</td><td class="xl67">201</td><td class="xl68">22,345</td><td class="xl67">1</td><td class="xl67">0.42</td><td class="xl67">Houston</td><td class="xl67">Prompt</td><td class="xl70">A578-B</td></tr><tr><td class="xl67" style="height:20px;">A572 Gr50</td><td class="xl67">4</td><td class="xl67">98</td><td class="xl67">202</td><td class="xl68">22,457</td><td class="xl67">1</td><td class="xl67">0.43</td><td class="xl67">Houston</td><td class="xl67">Prompt</td><td class="xl70">A578-B</td></tr></tbody></table><p> </p>';
        //$content = preg_replace('#<p[^>]*>\s*</p>#im', "", $content);
        
        $content = preg_replace('/<\w+\/>/s', "", $content); 
        $content = preg_replace('/<([^>]*)>[^<]*<\/\\1>/s', "", $content);
        
        dg($content);
*/        
/*
        $content = 'a<br>
        <br>
        <br>
        <br/>
        <br />
        <br/>
        <br>
        <br><br><br>b';
        $content = preg_replace('#(?:\r?\n){2,}#', "<br/><br/>\n", $content);
        $content = preg_replace('#(?:<br[^>]*>\s*){3,}#im', "<br/><br/>\n", $content);
              
        dg($content);
*/
        
/*        
        $content = '<ref message_id=543510>Ref. your 26/07/2013 14:36:43</ref> : 
Emails sent from the system have such headers:
Received-SPF: softfail (google.com: domain of transitioning plates@steelemotion.com does not designate 213.130.21.120 as permitted sender) client-ip=213.130.21.120;
Authentication-Results: mx.google.com; spf=softfail (google.com: domain of transitioning plates@steelemotion.com does not designate 213.130.21.120 as permitted sender) smtp.mail=plates@steelemotion.com
Emails with such headers may be blocked by recipients email cliend.
The purpose of these DNS sittings is to avoid such problem.';

        $content = Parser::Decode('<ref email_id=347>Ref. BIZ2064.47.LHS.aa5518.PO335919_MTRs</ref> : ');
        dg($content);
*/        

/*
        $modelMessage = new Message();
        dg($modelMessage->GetById(533583));
*/        

/*  тест
        //$str    = '<b>Steel.<a href="/biz/3469">BIZ2424</a>: OFFER / 200 mm and RINGS QUERY (BIZ2424.FERROSTA</b><b>Steel.<a href="/biz/3469">BIZ2424</a>: OFFER / 200 mm and RINGS QUERY (BIZ2424.FERROSTA</b><b>Steel.<a href="/biz/3469">BIZ2424</a>: OFFER / 200 mm and RINGS QUERY (BIZ2424.FERROSTA</b>';
        //$regex  = '#<a[^>]+href=([^ >]+)[^>]*>(.*?)</ref>#si';
        $str    = '<ref message_id="3262">please see BIZ3262</ref> jsdkfsfkbb sdf <ref message_id="3262">message BIZ 3262 steelemotion system</ref><ref biz_id="1893">text</ref> sadffsdfsd <ref message_id="123">message text</ref>';
        $regex  = '#<ref message_id="(\d+)">(.*?)</ref>#si';

//        $str    = 'inpo654 jhdhd INPO673 jjcuttwfegv inpo98';
//        $regex  = '#inpo(\d+)#si';
        
        preg_match_all($regex, $str, $matches);
//dg($matches);
        $replace    = false;
        $suffix     = 'blog';
        
        foreach ($matches[0] as $key => $match)
        {
            if ($replace)
            {
                $str = str_replace($match, $matches[2][$key], $str);
            }
            else
            {
                $str = str_replace($match, '<a href="' . str_replace(array('"', '\''), '', $matches[1][$key]) . (empty($suffix) ? '' : '/' . $suffix) . '">' . $matches[2][$key] . '</a>', $str);
            }
        }

        dg($str);
*/
/*  тест
        $bizes      = new Biz();
        $data_set   = $bizes->GetListByTitle('syst', 25);
        
        dg($data_set);
*/        
/*  тест
        $producers  = array('3738' => array('company_id' => 3738));
        $companies  = new Company();
        $producers  = $companies->FillCompanyInfo($producers);
        dg($producers);
*/
/*        
        $text       = "- TARIFF CODE : You did well to pick up on this essential point. INPO1907 Don't waste it by squeezing it in the PARTIAL SHIPMENT clause where it does not fit. Note also the paramount importance of the USA tariff code to be used in the B/L and subsequent USA IMPORT DECLARATION : BIZ1993.13.RWSmith.ms6042.CustomsClearance & BIZ1993.13.RWSmith.ms6043.Inquiry does not apply . - DUNNAGE : 6 X 8 X 30 cm or bigger ; delete \"DISTANCE BETWEEN PIECES: 2M\" as this requirement makes no sense, INCLUDE the essential requirement on the dunnage to be as per \"ISPM15\". See attached. You should have learned from 27-02-2013 10:16:51 . In addition, look at - - 26-02-2013 08:31:24 - - [25/10/2012 13:36:57] Friultrans - Silvio Giona: Miki, all plates arrived in Monfalcone without ISPM15 wood, We will provide only the strictly necessary for dunnage - - [31/10/2012 16:01:58] Friultrans - Silvio Giona: Miki ciao, we used about 1 mcq of ht-ispm15 wood for dunnage, 250€ and a (beer) - - 11-04-2008 16:41:40  + If you have not yet, compare this one with past HANYE contracts - BIZ2269 & BIZ3602.02 & BIZ2411.22 , just as points of reference.";        
        $objects    = Parser::GetObjects($text);
        $objects    = Parser::Decode($text);

        dg($objects);
        dg('okay');
*/        
        $this->page_name = 'Dashboard';

        $modelBiz   = new Biz();
        $bizes_list = $modelBiz->GetListFavourite();
        $this->_assign('bizes_list', $bizes_list);
        
        
        $modelOrder     = new Order();
        $orders_list    = $modelOrder->GetList('', 0, 0, '', '', '', 0, 0, 0, '', '', $this->page_no, 20);
        $this->_assign('orders_list', $orders_list);
        
        
        $modelEmail = new Email();
        $emails     = $modelEmail->GetList('', '', 0, EMAIL_TYPE_INBOX, 0, 0, '', 0, $this->page_no, 10);
        $this->_assign('emails_list', $emails);

        $modelMessage = new Message();
        $pendings     = $modelMessage->GetPendings('', '', 0, EMAIL_TYPE_INBOX, 0, 0, '', 0, $this->page_no, 10);
        $this->_assign('pendings_list', $pendings); 
        //debug('1671', $_SESSION);
        /*
        if($_SESSION['user']['id']==='1671') {
            $this->_display('indexmod');
        } else {
            $this->_display('index');
        }
         * 
         */
        $this->_display('index');
        
    }
    
    
    /**
     * Отображает картинку
     * 
     * url: /picture/{type}/{secretcode}/{size}/{filename}
     */
    function showpicture()
    {
        $type       = Request::GetString('type', $_REQUEST, '');
        $secretcode = Request::GetString('secretcode', $_REQUEST, '', 32);
        $size       = Request::GetString('size', $_REQUEST, '', 1);
        $filename   = Request::GetString('filename', $_REQUEST);

        $data = UserPicture::GetData($type, $secretcode, $size, $filename);
        
        $this->_display_binary($data['data'], $data['content_type']);
    }
    
    /**
     * Отображает картинку "нет картинки"
     * 
     * url: /nopicture/{type}/{size}.png
     */
    function shownopicture()
    {
        $type = Request::GetString('type', $_REQUEST);
        $file = Request::GetString('file', $_REQUEST);
        
        if ($type != '' && $file != '')
        {
            $data   = file_get_contents(ATTACHMENT_PATH . 'default/' . $type . '/' . $file);
            $this->_display_binary($data, 'image/png');
        }
        
        _404();        
    }
    
    /**
     * Отображает атачмент
     * 
     * url: /attachment/{secretcode}/{name}
     */
    function showattachment()
    {
        $secretcode = Request::GetString('secretcode', $_REQUEST, '', 32);
        $filename   = Request::GetString('filename', $_REQUEST);

        $path = UserPicture::GetPath($secretcode) . '/' . $filename;
        
        $this->_display_binary(file_get_contents($path), MimeType::GetMimeTypeByPath($path), false, $filename);    // открывает файл для сохранения
        // $this->_display_binary(file_get_contents($path), MimeType::GetMimeTypeByPath($path));    // пытается открыть файл в браузере
    }

    /**
     * Отображает CAPTCHA
     * 
     */
    function captcha()
    {
        $captcha = new KCAPTCHA();
        $_SESSION['captcha_keystring'] = $captcha->getKeyString();        
    }    
    
    function store()
    {
        $this->_display('store');
    }
}
