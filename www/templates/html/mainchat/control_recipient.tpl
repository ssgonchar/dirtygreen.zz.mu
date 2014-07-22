
{if isset($recipient)}
    {if !isset($readonly) || !$readonly}
        {if isset($recipient.user.person)} 
            {if isset($recipient.user.person.picture)}{picture type="person" size="x" source=$recipient.user.person.picture id="{$recipient.user.id}-user-picture" class="chat-user-{$recipient.chat_status}" onclick="chat_modal_select_user({$recipient.user.id});"}
            {elseif $recipient.user.person.gender == 'f'}<img src="/img/layout/anonymf.png" id="{$recipient.user.id}-user-picture" class="chat-user-{$recipient.chat_status}" alt="No Picture" alt="No Picture" onclick="chat_modal_select_user({$recipient.user.id});">
            {else}<img src="/img/layout/anonym.png" id="{$recipient.user.id}-user-picture" class="chat-user-{$recipient.chat_status}" alt="No Picture" alt="No Picture" onclick="chat_modal_select_user({$recipient.user.id});">{/if}
        {else}
        <img src="/img/layout/anonym.png" id="{$recipient.user.id}-user-picture" class="chat-user-{$recipient.chat_status}" alt="No Picture" alt="No Picture" onclick="chat_modal_select_user({$recipient.user.id});">{/if}
        <br><span style="color: {$recipient.user.color};">{$recipient.user.login}</span>
        <div id="user-{$recipient.user.id}-cc" class="chat-msg-relation-cc" {if isset($recipient.recipient_type) && $recipient.recipient_type == "c"}{else}style="display: none;"{/if} onclick="chat_modal_select_user({$recipient.user.id});">CC</div>
        <div id="user-{$recipient.user.id}-re" class="chat-msg-relation-re" {if isset($recipient.recipient_type) && $recipient.recipient_type == "r"}{else}style="display: none;"{/if} onclick="chat_modal_select_user({$recipient.user.id});">TO</div>
        <input type="hidden" id="user-{$recipient.user.id}-relation" class="chat-msg-relation" value="{if isset($recipient.recipient_type)}{$recipient.recipient_type}{/if}">
    {else}     
        {if isset($recipient.user.person)}
            {if isset($recipient.user.person.picture)}{picture type="person" size="x" source=$recipient.user.person.picture id="{$recipient.user.id}-user-picture" class="chat-user-{$recipient.chat_status}"}
            {elseif $recipient.user.person.gender == 'f'}<img src="/img/layout/anonymf.png" id="{$recipient.user.id}-user-picture" class="chat-user-{$recipient.chat_status}" alt="{$recipient.user.login}" alt="{$recipient.user.login}">
            {else}<img src="/img/layout/anonym.png" id="{$recipient.user.id}-user-picture" class="chat-user-{$recipient.chat_status}" alt="{$recipient.user.login}" alt="{$recipient.user.login}">{/if}
        {else}
        <img src="/img/layout/anonym.png" id="{$recipient.user.id}-user-picture" class="chat-user-{$recipient.chat_status}" alt="{$recipient.user.login}" alt="{$recipient.user.login}">
        {/if}
        <br><span style="color: {$recipient.user.color};">{$recipient.user.login}</span>
    {/if}    
{else}
    <img src="/img/layout/mam.jpg" id="{$recipient.user.id}-user-picture" class="chat-user-online" alt="MaM" alt="MaM"{if !isset($readonly) || !$readonly} onclick="chat_modal_select_user({$smarty.const.MAM_USER});"{/if}>
    <br><span style="color: black;">ALL</span>
    {if !isset($readonly) || !$readonly}
    <div id="user-{$smarty.const.MAM_USER}-cc" class="chat-msg-relation-cc" style="display: none;" onclick="chat_modal_select_user({$smarty.const.MAM_USER});">CC</div>
    <div id="user-{$smarty.const.MAM_USER}-re" class="chat-msg-relation-re" style="display: none;" onclick="chat_modal_select_user({$smarty.const.MAM_USER});">TO</div>
    <input type="hidden" id="user-{$smarty.const.MAM_USER}-relation" class="chat-msg-relation" value="">
    {/if}
{/if}
