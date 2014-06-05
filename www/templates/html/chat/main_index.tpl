<div class="container-fluid">
	<div class="row">
		<div class="col-sm-12 col-md-12 main">
			<ol id="chat-messages" class="chat-messages search-target">
			{foreach from=$list item=row}
			{if $row.message.type_id != $smarty.const.MESSAGE_TYPE_AWAY && $row.message.type_id != $smarty.const.MESSAGE_TYPE_ONLINE}
				{include file='templates/html/chat/control_chat_message.tpl' message=$row}
			{/if}    
			{/foreach}
			</ol>
			{if $page_no == 1}<div id="chat-updater" style="display: none;"></div>{/if}		
		</div>
	</div>
</div>
