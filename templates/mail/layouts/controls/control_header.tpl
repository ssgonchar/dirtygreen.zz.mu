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
	{if !empty($controller_css)}
		{foreach from=$controller_css item=css}
			<link rel="stylesheet" href="/css/{$css}.css" type="text/css" media="screen, projection" />
		{/foreach}	
	{else}	
		<link rel="stylesheet" href="/css/style.{$smarty.const.CSS_VERSION}.css" type="text/css" media="screen, projection" />
	{/if}
	
    {if isset($include_ui)}<link rel="stylesheet" href="/css/jquery-ui.css" type="text/css" media="screen, projection" />{/if}
    {if isset($include_upload)}<link rel="stylesheet" href="/css/fileuploader.css" type="text/css" media="screen, projection" />{/if}
    {if isset($include_prettyphoto)}<link rel="stylesheet" href="/css/prettyPhoto.css" type="text/css" media="screen" />{/if}
    {if isset($include_mce)}<script type="text/javascript" src="/js/mce.js"></script>{/if}
    <script type="text/javascript" src="/js/chosen.jquery.js"></script>
    
    <!--[if lt IE 7]>
    <style media="screen" type="text/css">
    .col1 {
        width:100%;
    }
    </style>
    <![endif]-->    
</head>

<body>
    <div id="header">
        {include file='templates/layouts/controls/control_navigation.tpl'}
    </div>
    <form id="mainform" method="post" action="/{$smarty.request.arg|escape:'html'}" enctype="multipart/form-data">
    <div id="container">
        {if isset($app_topcontext) && !empty($app_topcontext)}
        <div id="top-context">{$app_topcontext}</div>
        <div id="heading-short">
        {else}
        <div id="heading">
        {/if}
            {if !empty($page_name)}<h1>{$page_name}</h1>{/if}
            {include file='templates/layouts/controls/control_breadcrumb.tpl'}
        </div>
        
        <div id="bottom-shadow"></div>
