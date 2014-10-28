<div class="navbar navbar-default navbar-fixed-bottom" role="navigation">
    <div class="container">

        <div class="navbar-collapse collapse">
				{if isset($context)}{$context}<div class="separator"></div>{/if}
		</div><!--/.nav-collapse -->
    </div>
</div>
{*<div class="container " style="">
    <nav class="footer navbar navbar-fixed-bottom">
        <div class="navbar-inner navbar-content-center navbar-default"  style="height:60px;">
             {if isset($context)}{$context}<div class="separator"></div>{/if}
        </div>
		
    </nav>
</div>*}

<input type="hidden" id="app_object_alias"{if !empty($app_object_alias)} value="{$app_object_alias}"{/if}><input type="hidden" id="app_object_id"{if !empty($app_object_id)} value="{$app_object_id}"{/if}>
    </form>
    <div id="chat-audio"></div>    
    
    {include file="templates/layouts/controls/control_app_messages.tpl"}
    <script src="/js/jquery.{$smarty.const.JS_VERSION}.js" type="text/javascript"></script>
            <script type="text/javascript" src="/js/jquery.highlight.js" ></script>       
            <script type="text/javascript" src="/js/jquery.zclip.min.js" ></script>       
        <script type="text/javascript" src="/js/jquery.scrollTo-min.js" ></script>       
        <script type="text/javascript" src="/js/search_on_page.js" ></script>      
    	 <script src="/js/chosen.jquery.js" type="text/javascript"></script>
             <script type="text/javascript">
        $(window).load(function(){
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

    {if isset($include_charts)}
    <script src="/js/highstock.js"></script>
    <script src="/js/modules/exporting.js"></script>    
    {/if}
    
    {if isset($include_mce)}{*<script type="text/javascript" src="/js/tiny_mce/tiny_mce.js" ></script>*}{/if}
	{if isset($include_mce)}<script type="text/javascript" src="/js/tinymce4/tinymce.min.js" ></script>{/if}
    {if !empty($include_upload)}<script src="/js/fileuploader.js" type="text/javascript"></script>{/if}
    {if !empty($include_jsapi)}<script type="text/javascript" src="https://www.google.com/jsapi"></script>{/if}  
    <script src="/js/app.{$smarty.const.JS_VERSION}.js" type="text/javascript"></script>
    
<script src="/js/ui/datepicker-en-GB.js"></script>

    {if isset($include_prettyphoto)}
    <script src="/js/jquery.prettyPhoto.js" type="text/javascript" charset="utf-8"></script>
    {literal}<script type="text/javascript" charset="utf-8">$(document).ready(function(){ bind_prettyphoto(); });</script>{/literal}
    {/if}      
        <script type="text/javascript" src="/js/mce.js"></script>
        <script type="text/javascript" src="/js/chat_archive.js" ></script>
        <script type="text/javascript" src="/js/chat_search.js" ></script>    
        <script type="text/javascript" src="/js/jquery.prettyPhoto.js"></script>       
        {if isset($include_mce)}<script type="text/javascript" src="/js/mce.js"></script>{/if}   
        <script type="text/javascript" src="/js/tinynav.min.js" ></script>       
        <script type="text/javascript" src="/js/bootstrap-hover-dropdown.js" ></script>
        <script type="text/javascript" src="/js/i18n/grid.locale-en.js" ></script>
        <script type="text/javascript" src="/js/jquery.jqGrid.min.js" ></script>
        <script src="/js/plugins/jquery.searchFilter.js" type="text/javascript"></script>
   
    
        <script type="text/javascript" charset="utf-8">
		var user_last_chat_message_id = {if isset($user_last_chat_message_id)}{$user_last_chat_message_id}{else}0{/if};
	</script>
 {if !empty($controller_js)}{foreach from=$controller_js item=js}<script src="/js/{$js}.{$smarty.const.JS_VERSION}.js" type="text/javascript"></script>{/foreach}{/if}  
         
</body>
</html>

