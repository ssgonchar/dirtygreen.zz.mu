<input type="button" class="btn150o" value="Write Message" onclick="show_chat_modal('chat', 0);"><div class="pad-10"></div>
<div id="chat-icon-park" onclick="show_chat_modal('chat', 0);">
    {include file='templates/html/chat/control_recipients.tpl' readonly=true}
</div>
<input type="hidden" id="chat-object-alias" value="{if isset($chat_object_alias)}{$chat_object_alias}{/if}">
<input type="hidden" id="chat-object-id" value="{if isset($chat_object_id)}{$chat_object_id}{/if}">