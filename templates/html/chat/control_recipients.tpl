{if isset($users.staff) && !empty($users.staff)}
    <div class="chat-user-container"{if isset($readonly) && $readonly}{/if}>   
        {include file='templates/html/chat/control_recipient.tpl' recipient=$user_row}
    </div>    
{/if}    
{foreach from=$users.staff item=user_row}
    <div class="chat-user-container">
        {include file='templates/html/chat/control_recipient.tpl' recipient=$user_row}
    </div>
{/foreach}





