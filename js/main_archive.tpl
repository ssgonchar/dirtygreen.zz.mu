<div class="chat-archive-dateto" data-date="{$date_to|date_format:'%Y-%m-%d'}"{*style="margin: 0 40%;"*}></div>

<div class="pad1"></div>
<hr style="width: 100%; color: #dedede;" size="1"/>
<div class="pad1"></div>

{if empty($list)}
    {if isset($filter)}Nothing was found{/if}
{else}
    <ol id="chat-messages" class="chat-messages">
    {foreach from=$list item=row}
        {include file='templates/html/chat/control_chat_message.tpl' message=$row}
    {/foreach}
    </ol>
{/if}