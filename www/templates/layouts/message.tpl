<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="content-type" content="text/html; charset=utf-8" />
    <meta charset="UTF-8">
    <meta name="lang" content="eng">
    <meta name="title" content="{$smarty.const.APP_NAME}">
    <meta name="keywords" content="" />
    <meta name="description" content="" />
    <title>{if (!empty($page_title))}{$page_title} - {/if}{$smarty.const.APP_NAME}</title>
    <link rel="icon" href="/favicon.ico" type="image/icon.ico">
    <link rel="stylesheet" href="/css/style.{$smarty.const.CSS_VERSION}.css" type="text/css" media="screen, projection" />
    <link rel="stylesheet" href="/css/jquery-ui.css" type="text/css" media="screen, projection" />
    <link rel="stylesheet" href="/css/chosen.css" type="text/css" media="screen, projection" />
    <link rel="stylesheet" href="/css/fileuploader.css" type="text/css" media="screen, projection" />
    
    {*<link rel="stylesheet" href="/css/bootstrap.min.css" type="text/css" media="screen, projection" />*}
    {*<link rel="stylesheet" href="/css/prettyPhoto.css" type="text/css" media="screen" />*}
    <script type="text/javascript" src="/js/mce.js"></script>

</head>
<body style="min-width: 900px !important; min-height: 500px !important;">

    {if !empty($content)}{$content}{/if}

    <div id="chat-audio"></div>    
    {include file="templates/layouts/controls/control_app_messages.tpl"}
    <script src="/js/jquery.{$smarty.const.JS_VERSION}.js" type="text/javascript"></script>
    <script type="text/javascript" src="/js/chosen.jquery.js"></script>
    {if isset($include_ui)}
	<script type="text/javascript" src="/js/jquery-ui.{$smarty.const.JS_VERSION}.js" ></script>
	<script type="text/javascript" src="/js/ui/datepicker-en-GB.js"></script>
	<script type="text/javascript" src="/js/jquery.tooltip.js"></script>
    {/if}
	
    {if isset($include_mce)}<script type="text/javascript" src="/js/tiny_mce/tiny_mce.js" ></script>{/if}
    {if !empty($include_upload)}<script src="/js/fileuploader.js" type="text/javascript"></script>{/if}
    {if !empty($include_jsapi)}<script type="text/javascript" src="https://www.google.com/jsapi"></script>{/if}
    
    {*<script src="/js/app.{$smarty.const.JS_VERSION}.js" type="text/javascript"></script>*}
    {if !empty($controller_js)}{foreach from=$controller_js item=js}<script src="/js/{$js}.{$smarty.const.JS_VERSION}.js" type="text/javascript"></script>{/foreach}{/if}
    
    {*if isset($include_prettyphoto)}
    <script src="/js/jquery.prettyPhoto.js" type="text/javascript" charset="utf-8"></script>
    {literal}<script type="text/javascript" charset="utf-8">$(document).ready(function(){ bind_prettyphoto(); });</script>{/literal}
    {/if*}
    
    <script type="text/javascript" charset="utf-8">var user_last_chat_message_id = {if isset($user_last_chat_message_id)}{$user_last_chat_message_id}{else}0{/if};</script>
</body>
</html>