<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="content-type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta charset="UTF-8">
    <meta name="lang" content="eng">
    <meta name="title" content="{$smarty.const.APP_NAME}">
    <meta name="keywords" content="" />
    <meta name="description" content="" />
    <title>{if (!empty($page_title))}{$page_title} - {/if}{$smarty.const.APP_NAME}</title>



	{* 	<!--<script src="http://jqueryui.com/jquery-wp-content/themes/jquery/js/plugins.js"></script>
	<script src="http://jqueryui.com/jquery-wp-content/themes/jquery/js/main.js"></script> -->*}

        <link rel="icon" href="/favicon.ico" type="image/icon.ico">
	{if !empty($controller_css)}
		{foreach from=$controller_css item=css}
			<link rel="stylesheet" href="/css/{$css}.css" type="text/css" media="screen, projection" />
		{/foreach}	
	{else}	
		<link rel="stylesheet" href="/css/style.{$smarty.const.CSS_VERSION}.css" type="text/css" media="screen, projection" />
	{/if}
	
    
    {if isset($include_upload)}<link rel="stylesheet" href="/css/fileuploader.css" type="text/css" media="screen, projection" />{/if}
    {if isset($include_prettyphoto)}<link rel="stylesheet" href="/css/prettyPhoto.css" type="text/css" media="screen" />{/if}
    {*{if isset($include_mce)}<script type="text/javascript" src="/js/mce.js"></script>{/if}*}
    <!--<script type="text/javascript" src="/js/chosen.jquery.js"></script>-->
    
    
    <!-- jQuery UI -
    <link rel="stylesheet" href="//code.jquery.com/ui/1.10.4/themes/smoothness/jquery-ui.css">
    <script src="//code.jquery.com/jquery-1.10.2.js"></script>
    <script src="//code.jquery.com/ui/1.10.4/jquery-ui.js"></script>
    <link rel="stylesheet" href="/resources/demos/style.css">-->
    
    <!--[if lt IE 7]>
    <style media="screen" type="text/css">
    .col1 {
        width:100%;
    }
    </style>
    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
    
    
    <![endif]-->  
    <link rel="stylesheet" href="/css/loader.css" type="text/css" media="screen, projection" />
    
    <!--<link rel="stylesheet" href="/css/bootstrap.css" type="text/css" media="screen, projection" />-->
    <link rel="stylesheet" href="/css/bootstrap.min.css" type="text/css" media="screen, projection" />
    <link rel="stylesheet" href="/css/bootstrap-theme.min.css" type="text/css" media="screen, projection" />
    <link rel="stylesheet" href="/css/bootstrap-my.css" type="text/css" media="screen, projection" />
    <link rel="stylesheet" href="/css/docs.min.css" type="text/css" media="screen, projection" />
    <link rel="stylesheet" href="/css/chosen.css" type="text/css" media="screen, projection" />
    {if isset($include_ui)}<link rel="stylesheet" href="/css/jquery-ui.css" type="text/css" media="screen, projection" />{/if}
    <link rel="stylesheet" href="/css/ui.jqgrid.css" />
    <link href="/js/plugins/searchFilter.css" rel="Stylesheet" type="text/css" />
    
    {*<script src="http://www.fuelcdn.com/fuelux/2.6.0/loader.min.js" type="text/javascript"></script>
    	<!--<script src="http://jqueryui.com/jquery-wp-content/themes/jquery/js/plugins.js"></script>
	<script src="http://jqueryui.com/jquery-wp-content/themes/jquery/js/main.js"></script> -->*}


{*	
<!--Jquery EasyUI-->
	<link rel="stylesheet" href="/css/bootstrap/easyui.css" type="text/css" media="screen, projection" /> 
	<link rel="stylesheet" href="/css/icon.css" type="text/css" media="screen, projection" /> 
	<script src="/js/jquery.easyui.min.js" ></script> 

    <link rel="stylesheet" href="http://cdn.kendostatic.com/2014.1.318/styles/kendo.common-bootstrap.min.css" />

    
    <script src="/js/i18n/grid.locale-en.js" type="text/javascript"></script>
    <script src="/js/jquery.jqGrid.min.js" ></script>*}
  

</head>


<body style="overflow: hidden; ">

    <div id="loader" style="opacity: 0.8; position: absolute; top: 0px; left: 0px; background-color: white; width: 100%; height: 100%; z-index: 555555;">
        <div style="width: 100px; height: 50px; text-align: center; vertical-align: middle; margin: 200px auto 200px;">
            <div class="bubblingG">
<span id="bubblingG_1">
</span>
<span id="bubblingG_2">
</span>
<span id="bubblingG_3">
</span>
</div>
            <h3>Loading...</h3></div>
    </div>
    <!--<div id="header">-->
        {include file='templates/layouts/controls/control_navigation_mod.tpl'}
    <!--</div>-->
    <form id="mainform" method="post" action="/{$smarty.request.arg|escape:'html'}" enctype="multipart/form-data">
    <div id="container" class="container-fluid">
        {if isset($app_topcontext) && !empty($app_topcontext)}
        <div id="top-context">{$app_topcontext}</div>
        <div id="heading-short">
        {else}
        <div id="heading">
        {/if}
            {if !empty($page_name)}
                <!--<span id="test-popover" type="button" class="btn btn-default" data-container="body" data-toggle="popover" data-placement="right" title='About this page' data-content="page info" style="border-radius: 10px; border: solid 1px; width: 24px; height: 24px; margin-right: 5px; margin-bottom: 5px; padding-left: 4px; padding-top: 3px;">
                <span class="glyphicon glyphicon-info-sign"></span>
                </span>-->
                <h1>{$page_name}&nbsp;<button class="btn btn-primary icon-hide btn-xs" onclick="show_hide_column(this); return false;"><i class="glyphicon glyphicon-th"></i>&nbsp;Toolbox</button></h1>{/if}
            
        </div>
        