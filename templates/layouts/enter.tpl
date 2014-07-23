<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
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
    {if isset($include_mce)}<script type="text/javascript" src="/js/mce.js"></script>{/if}
</head>

<body>
	<div id="header">
		<a href="/"><img src="/img/layout/logo.png" alt="logo M-a-M"></img></a>
	</div>

	<div class="container">
		<div id="login-form">		
			<h1>{if (!empty($page_name))} - {$page_name}{/if}</h1>
			{if !empty($content)}{$content}{/if}
		</div>
	</div>

	<div id="footer">
	</div>
    
    {include file="templates/layouts/controls/control_app_messages.tpl"}
    <script src="/js/jquery.{$smarty.const.JS_VERSION}.js" type="text/javascript"></script>
    {if isset($include_ui)}
    <script type="text/javascript" src="/js/jquery-ui.js" ></script>
    <script type="text/javascript" src="/js/ui/datepicker-en-GB.js"></script>
    {/if}
	{if !empty($controller_js)}{foreach from=$controller_js item=js}<script src="/js/{$js}.{$smarty.const.JS_VERSION}.js" type="text/javascript"></script>{/foreach}{/if}
    {if isset($include_mce)}<script type="text/javascript" src="/js/tiny_mce/tiny_mce.js" ></script>{/if}
    <script src="/js/app.{$smarty.const.JS_VERSION}.js" type="text/javascript"></script>    
</body>
</html>