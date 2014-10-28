{if isset($users.staff) && !empty($users.staff)}
    <div class="users-container"{if isset($readonly) && $readonly}{/if}>   
        {include file='templates/html/iamido/control_recipient.tpl' recipient=$user_row}
    </div>    
{/if}    
{foreach from=$users.staff item=user_row}
    <div class="users-container">
        {include file='templates/html/iamido/control_recipient.tpl' recipient=$user_row}
    </div>
{/foreach}





