{if isset($users.partners) && !empty($users.partners)}
    {foreach from=$users.partners item=user_row}
    <div class="chat-user-container tooltip" alt="{$user_row.user.person.doc_no}" title="{$user_row.user.person.doc_no} || {$user_row.user.person.company.doc_no}"{if isset($readonly) && $readonly} {/if}>
        {include file='templates/html/chat/control_recipient.tpl' recipient=$user_row}
    </div>
    {/foreach}
{/if}