<ol id="chat-messages" class="chat-messages">
{foreach from=$list item=row}
{if $row.message.type_id != $smarty.const.MESSAGE_TYPE_AWAY && $row.message.type_id != $smarty.const.MESSAGE_TYPE_ONLINE}
    {include file='templates/html/chat/control_chat_message.tpl' message=$row}
{/if}    
{/foreach}
</ol>
{if $page_no == 1}<div id="chat-updater" style="display: none;"></div>{/if}
