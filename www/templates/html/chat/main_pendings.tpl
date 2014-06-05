<ol id="chat-messages" class="chat-messages"{if empty($list)} style="display: none;"{/if}>
{foreach from=$list item=row}
    {include file='templates/html/chat/control_chat_message.tpl' message=$row itemid="chat-pending-{$row.message.id}"}
{/foreach}
</ol>
<div id="chat-no-pendings" {if !empty($list)} style="display: none;"{/if}>There are no MustDO messages.</div>