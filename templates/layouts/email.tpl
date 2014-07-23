<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="content-type" content="text/html; charset=utf-8" />
    <meta charset="UTF-8">
    <meta name="lang" content="eng">
    <meta name="title" content="{$smarty.const.APP_NAME}">
    <meta name="keywords" content="" />
    <meta name="description" content="" />
    
    <title>{$smarty.const.APP_NAME}{if (!empty($page_title))} - {$page_title}{/if}</title>
    
    <link rel="icon" href="/favicon.ico" type="image/icon.ico">
    <link rel="stylesheet" href="/css/style.{$smarty.const.CSS_VERSION}.css" type="text/css" media="screen, projection" />
    {if isset($include_ui)}<link rel="stylesheet" href="/css/jquery-ui.css" type="text/css" media="screen, projection" />{/if}
    {if isset($include_upload)}<link rel="stylesheet" href="/css/fileuploader.css" type="text/css" media="screen, projection" />{/if}
    {if isset($include_prettyphoto)}<link rel="stylesheet" href="/css/prettyPhoto.css" type="text/css" media="screen" />{/if}
    {if isset($include_mce)}<script type="text/javascript" src="/js/mce.js"></script>{/if}
    <!--[if lt IE 7]><style media="screen" type="text/css">col1 { width:100%; }</style><![endif]-->
</head>

<body>
    <div id="header">{include file='templates/layouts/controls/control_navigation.tpl'}</div>
    <form id="mainform" method="post" action="/{$smarty.request.arg|escape:'html'}" enctype="multipart/form-data">
        <div class="wrapper">
            <div class="pname-bcrumb">
                {if !empty($page_name)}<h1 style="margin: 0;">{$page_name}</h1>{/if}
                {include file='templates/layouts/controls/control_breadcrumb.tpl'}
            </div>
            <div class="content-wrapper">
                <div class="cw-container">
                    <div class="cwc-hfilter">{if isset($hcontext)}{$hcontext}{/if}</div>
                    <div class="cwc-content">{if !empty($content)}{$content}{/if}</div>
                </div>
                <div class="cw-rfilter">{if isset($rcontext)}{$rcontext}{/if}</div>
                <div class="separator"></div>
            </div>
        </div>
        <div id="footer">{if isset($context)}{$context}<div class="separator"></div>{/if}</div>
    </form>
    
    <div id="chat-audio"></div>
    {include file="templates/layouts/controls/control_app_messages.tpl"}
    
    <script src="/js/jquery.{$smarty.const.JS_VERSION}.js" type="text/javascript"></script>
    
    {if isset($include_ui)}
    <script type="text/javascript" src="/js/jquery-ui.js" ></script>
    <script type="text/javascript" src="/js/ui/datepicker-en-GB.js"></script>
    {/if}
    
    {if isset($include_mce)}<script type="text/javascript" src="/js/tiny_mce/tiny_mce.js" ></script>{/if}
    {if !empty($include_upload)}<script src="/js/fileuploader.js" type="text/javascript"></script>{/if}
    {if !empty($include_jsapi)}<script type="text/javascript" src="https://www.google.com/jsapi"></script>{/if}
    
    <script src="/js/app.{$smarty.const.JS_VERSION}.js" type="text/javascript"></script>
    {if !empty($controller_js)}{foreach from=$controller_js item=js}<script src="/js/{$js}.{$smarty.const.JS_VERSION}.js" type="text/javascript"></script>{/foreach}{/if}
    
    {if isset($include_prettyphoto)}
        <script src="/js/jquery.prettyPhoto.js" type="text/javascript" charset="utf-8"></script>
        {literal}<script type="text/javascript" charset="utf-8">$(document).ready(function(){ bind_prettyphoto(); });</script>{/literal}
    {/if}
    
    <script type="text/javascript" charset="utf-8">var user_last_chat_message_id = {if isset($user_last_chat_message_id)}{$user_last_chat_message_id}{else}0{/if};</script>
</body>
</html>