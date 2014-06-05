{if !empty($mes)}{$mes}{/if}
<div id="app_messages" style="display: {if empty($messages)}none{else}block{/if};">
{if !empty($messages)}
    {foreach from=$messages item=message}
        {include file="templates/layouts/controls/control_app_message.tpl" message_text=$message.text message_status=$message.status}
    {/foreach}
{/if}
</div>