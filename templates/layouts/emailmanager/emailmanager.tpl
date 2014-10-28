<!DOCTYPE html>
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->
<html>
    <head>
        <title>{if (!empty($page_title))}{$page_title} - {/if}{$smarty.const.APP_NAME}</title>
        <meta http-equiv="content-type" content="text/html; charset=utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta charset="UTF-8">
        <meta name="lang" content="eng">
        <meta name="title" content="{$smarty.const.APP_NAME}">
        <meta name="keywords" content="" />
        <meta name="description" content="" />
        <link rel="icon" href="/favicon.ico" type="image/icon.ico">
        <link rel="stylesheet" href="/css/bootstrap.min.css" type="text/css" media="screen, projection" />
        <link rel="stylesheet" href="/css/bootstrap-theme.min.css" type="text/css" media="screen, projection" />	
        <link rel="stylesheet" href="/css/bootstrap-my.css" type="text/css" media="screen, projection" />	
        <link rel="stylesheet" href="/css/dropzone.css" type="text/css" media="screen, projection" />	

        {if !empty($controller_css)}
            {foreach from=$controller_css item=css}
                <link rel="stylesheet" href="/css/{$css}.css" type="text/css" media="screen, projection" />
            {/foreach}
        {/if}
        <script src="/js/jquery.{$smarty.const.JS_VERSION}.js" type="text/javascript"></script>
        <script type="text/javascript" src="/js/bootstrap.min.js"></script>  
        <script type="text/javascript" src="/js/mce.js"></script>
        <script type="text/javascript" src="/js/jquery.highlight.js" ></script>       
        <script type="text/javascript" src="/js/jquery.zclip.min.js" ></script>         
        <script type="text/javascript" src="/js/jquery.scrollTo-min.js" ></script> 
        <script type="text/javascript" src="/js/search_on_page.js" ></script>
        <script type="text/javascript" src="/js/app.js" ></script>
        <script type="text/javascript" src="/js/chat_archive.js" ></script>
        <script type="text/javascript" src="/js/chat_search.js" ></script>
        <script type="text/javascript" src="/js/bootstrap-hover-dropdown.js" ></script>
        <script type="text/javascript" src="/js/emailmanager_index.js" ></script>
        <script type="text/javascript" src="/js/dropzone.js" ></script>
        <script type="text/javascript" src="/js/tinymce4/tinymce.min.js" ></script>

    </head>
    <body>
        <div id="app_messages" style="display: none;"></div>
        {include file='templates/layouts/controls/control_navigation_mod.tpl'}
        {include file='templates/html/emailmanager/main_index.tpl'}
    </body>
</html>
