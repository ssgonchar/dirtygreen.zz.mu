{if isset($users.partners) && !empty($users.partners)}
    {foreach from=$users.partners item=user_row}
    <div class="chat-user-container">
        {include file='templates/html/chat/control_recipient.tpl' recipient=$user_row}
    </div>
    {/foreach}
{/if}