<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="content-type" content="text/html; charset=utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <meta charset="UTF-8" />
        <meta name="lang" content="eng"/>
        <meta name="title" content="{$smarty.const.APP_NAME}"/>
        <meta name="keywords" content="" />
        <meta name="description" content="" />
        <title>{if (!empty($page_title))}{$page_title} - {/if}{$smarty.const.APP_NAME}</title>



        {* 	<!--<script src="http://jqueryui.com/jquery-wp-content/themes/jquery/js/plugins.js"></script>
        <script src="http://jqueryui.com/jquery-wp-content/themes/jquery/js/main.js"></script> -->*}

        <link rel="icon" href="/favicon.ico" type="image/icon.ico" />
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

        {*<script src="http://www.fuelcdn.com/fuelux/2.6.0/loader.min.js" type="text/javascript"></script>
        <!--<script src="http://jqueryui.com/jquery-wp-content/themes/jquery/js/plugins.js"></script>
        <script src="http://jqueryui.com/jquery-wp-content/themes/jquery/js/main.js"></script> -->*}


        {*	
        <!--Jquery EasyUI-->
        <link rel="stylesheet" href="/css/bootstrap/easyui.css" type="text/css" media="screen, projection" /> 
        <link rel="stylesheet" href="/css/icon.css" type="text/css" media="screen, projection" /> 
        <script src="/js/jquery.easyui.min.js" ></script> 

        <link rel="stylesheet" href="http://cdn.kendostatic.com/2014.1.318/styles/kendo.common-bootstrap.min.css" />
    
        <link rel="stylesheet" href="/css/ui.jqgrid.css" />
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
        <form id="mainform" method="post" action="/{$smarty.request.arg|escape:'html'}" enctype="multipart/form-data">
            
            <nav class="navbar navbar-default navbar-fixed-top" role="navigation">
                <span class="navbar-brand">{if !empty($page_name)}{$page_name}{/if}</span>
            </nav>
            <nav class="navbar navbar-default navbar-fixed-bottom" role="navigation">
                <div class="container-fluid">
                    <!-- Brand and toggle get grouped for better mobile display -->
                    <div class="navbar-header">
                        <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
                            <span class="sr-only">Toggle navigation</span>
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                        </button>
                        
                    </div>

                    <!-- Collect the nav links, forms, and other content for toggling -->
                    <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                        {if isset($context)}{$context}{/if}
                    </div><!-- /.navbar-collapse -->
                </div><!-- /.container-fluid -->
            </nav>

            <div id="container" class="container-fluid">
                {if !empty($content)}
                    {$content}
                {/if}
                <input type="hidden" id="app_object_alias" {if !empty($app_object_alias)} value="{$app_object_alias}"{/if} /><input type="hidden" id="app_object_id"{if !empty($app_object_id)} value="{$app_object_id}"{/if} />

                <div id="chat-audio"></div>
            </div>
        </form>

    {include file="templates/layouts/controls/control_app_messages.tpl"}
    <script src="/js/jquery.{$smarty.const.JS_VERSION}.js" type="text/javascript"></script>
    <script type="text/javascript" src="/js/jquery.highlight.js" ></script>       
    <script type="text/javascript" src="/js/jquery.zclip.min.js" ></script>       
    <script type="text/javascript" src="/js/jquery.scrollTo-min.js" ></script>       
    <script type="text/javascript" src="/js/search_on_page.js" ></script>      
    <script src="/js/chosen.jquery.js" type="text/javascript"></script>
    <script type="text/javascript">
        $(window).load(function() {
            $('#loader').hide('slow');
            $('body').css('overflow', 'auto');
            console.log('loaded');
        });
    </script>
    {if isset($include_ui)}
        <script type="text/javascript" src="/js/jquery-ui.{$smarty.const.JS_VERSION}.js" ></script>
        <script type="text/javascript" src="/js/bootstrap.min.js"></script>
        <script type="text/javascript" src="/js/ui/datepicker-en-GB.js"></script>
        <script type="text/javascript" src="/js/jquery.tooltip.js"></script>
        <!--<script type="text/javascript" src="/js/dictionary.js"></script>-->
    {/if}

{if isset($include_mce)}{*<script type="text/javascript" src="/js/tiny_mce/tiny_mce.js" ></script>*}{/if}
    {if isset($include_mce)}<script type="text/javascript" src="/js/tinymce4/tinymce.min.js" ></script>{/if}
    {if !empty($include_upload)}<script src="/js/fileuploader.js" type="text/javascript"></script>{/if}
    {if !empty($include_jsapi)}<script type="text/javascript" src="https://www.google.com/jsapi"></script>{/if}  
    <script src="/js/app.{$smarty.const.JS_VERSION}.js" type="text/javascript"></script>
{if !empty($controller_js)}{foreach from=$controller_js item=js}<script src="/js/{$js}.{$smarty.const.JS_VERSION}.js" type="text/javascript"></script>{/foreach}{/if}  
<script src="/js/ui/datepicker-en-GB.js"></script>

{if isset($include_prettyphoto)}
    <script src="/js/jquery.prettyPhoto.js" type="text/javascript" charset="utf-8"></script>
    {literal}<script type="text/javascript" charset="utf-8">$(document).ready(function() {
            bind_prettyphoto();
        });</script>{/literal}
            {/if}      
            <script type="text/javascript" src="/js/mce.js"></script>
            <script type="text/javascript" src="/js/chat_archive.js" ></script>
            <script type="text/javascript" src="/js/chat_search.js" ></script>    
            <script type="text/javascript" src="/js/jquery.prettyPhoto.js"></script>       
            {if isset($include_mce)}<script type="text/javascript" src="/js/mce.js"></script>{/if}   
            <script type="text/javascript" src="/js/tinynav.min.js" ></script>       
            <script type="text/javascript" src="/js/bootstrap-hover-dropdown.js" ></script>


            <script type="text/javascript" charset="utf-8">
                var user_last_chat_message_id = {if isset($user_last_chat_message_id)}{$user_last_chat_message_id}{else}0{/if};
            </script>


        </body>
    </html>