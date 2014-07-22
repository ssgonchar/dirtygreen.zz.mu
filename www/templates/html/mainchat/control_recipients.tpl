<div class="panel panel-default" >
  <div class="panel-heading">
    <h3 class="panel-title"></h3>
  </div>
  <div class="panel-body" class="chat-user-container"{if isset($readonly) && $readonly} {/if}>
<div class="chat-user-container"{if isset($readonly) && $readonly}{/if}>
    {include file='templates/html/chat/control_recipient.tpl'}
</div>
{if isset($users.staff) && !empty($users.staff)}
    {foreach from=$users.staff item=user_row}
    <div class="chat-user-container"{if isset($readonly) && $readonly} {/if}>
        {include file='templates/html/chat/control_recipient.tpl' recipient=$user_row}
    </div>
    {/foreach}
    <div class="separator pad-10"></div>
{/if}
{if isset($users.partners) && !empty($users.partners)}
    <div style="color: #777; font-size: 10px; padding: 0 0 5px;">Partners:</div>
    {foreach from=$users.partners item=user_row}
    <div class="chat-user-container tooltip" alt="{$user_row.user.person.doc_no}" title="{$user_row.user.person.doc_no} || {$user_row.user.person.company.doc_no}"{if isset($readonly) && $readonly} {/if}>
        {include file='templates/html/chat/control_recipient.tpl' recipient=$user_row}
    </div>
    {/foreach}
{/if}
  </div>
</div>


