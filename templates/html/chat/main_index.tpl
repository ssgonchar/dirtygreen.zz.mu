<div class="container-fluid">
	<div class="row">
		<div class="col-sm-12 col-md-12 main">
			<!--{*{if $smarty.session.user.id == '1671'}
                        <ol id="chat-messages" class="chat-messages search-target">
			{foreach from=$list item=row}
			{if $row.message.type_id != $smarty.const.MESSAGE_TYPE_AWAY && $row.message.type_id != $smarty.const.MESSAGE_TYPE_ONLINE}
				{include file='templates/html/chat/control_chat_message.tpl' message=$row}
			{/if}    
			{/foreach}
			</ol>
                        {else}*}-->
 
                            
                            <ul id="chat-messages" class="timeline chat-messages search-target">
                                {foreach from=$list item=row}
                                    {if $row.message.type_id != $smarty.const.MESSAGE_TYPE_AWAY && $row.message.type_id != $smarty.const.MESSAGE_TYPE_ONLINE}
                                            {include file='templates/html/chat/control_chat_messagemod.tpl' message=$row}
                                    {/if}                                      
                                <!--

                                -->
                                {/foreach}
                            </ul>
                            
                         <!--{*/if*}-->
			{if $page_no == 1}<div id="chat-updater" style="display: none;"></div>{/if}		
		</div>
	</div>
</div>
