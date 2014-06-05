<div style="color: {$message.message.sender.color}; cursor: text; margin-top:30px">
    <div style="position: relative;" id="chat-message-text-{$message.message.id}">
        <div style="float: left; width: 50px;">
            {if $message.message.sender_id == $smarty.const.GNOME_USER}
                <img src="/img/layout/gnome.jpg" alt="Gnome" alt="Gnome">
            {elseif isset($message.message.sender) && isset($message.message.sender.person)}
                {if isset($message.message.sender.person.picture)}{picture type="person" size="x" source=$message.message.sender.person.picture}
                {elseif $message.message.sender.person.gender == 'f'}<img src="/img/layout/anonymf.png" alt="{$message.message.sender.login}" alt="{$message.message.sender.login}">
                {else}<img src="/img/layout/anonym.png" alt="{$message.message.sender.login}" alt="{$message.message.sender.login}">{/if}
            {else}
                <img src="/img/layout/anonym.png" alt="No Picture" alt="No Picture">
            {/if}
        </div>
        <div class="chat-message-subject" style="float: left; line-height: 14px; position: absolute; left: 50px; right: 70px;">
        {if $message.message.type_id == $smarty.const.MESSAGE_TYPE_NORMAL || $message.message.type_id == $smarty.const.MESSAGE_TYPE_PRIVATE || $message.message.type_id == $smarty.const.MESSAGE_TYPE_PERSONAL}
            {if $message.message.type_id == $smarty.const.MESSAGE_TYPE_PRIVATE || $message.message.type_id == $smarty.const.MESSAGE_TYPE_PERSONAL}<i>(p)</i>&nbsp;{/if}{$message.message.sender.login}&nbsp;&rarr;&nbsp;{if !isset($message.message.recipient) || empty($message.message.recipient)}MaM{else}{foreach from=$message.message.recipient item=r name=r}{$r.user.login}{if !$smarty.foreach.r.last}/{/if}{/foreach}{if !empty($message.message.cc)}.cc.{foreach from=$message.message.cc item=c name=c}{$c.user.login}{if !$smarty.foreach.c.last}/{/if}{/foreach}{/if}{/if}
            <br><b>{$message.message.title|parse}</b>
        {elseif $message.message.type_id == $smarty.const.MESSAGE_TYPE_SERVICE}
            {if $message.message.type_id == $smarty.const.MESSAGE_TYPE_PRIVATE || $message.message.type_id == $smarty.const.MESSAGE_TYPE_PERSONAL}<i>(p)</i>&nbsp;{/if}{$message.message.sender.login}&nbsp;&rarr;&nbsp;{if !isset($message.message.recipient) || empty($message.message.recipient)}MaM{else}{foreach from=$message.message.recipient item=r name=r}{$r.user.login}{if !$smarty.foreach.r.last}/{/if}{/foreach}{if !empty($message.message.cc)}.cc.{foreach from=$message.message.cc item=c name=c}{$c.user.login}{if !$smarty.foreach.c.last}/{/if}{/foreach}{/if}{/if}
            <br><b>{$message.message.title|parse}</b>            
        {elseif $message.message.type_id == $smarty.const.MESSAGE_TYPE_LOGIN}
            <b>{$message.message.sender.login} logged IN {$message.message.title}</b>
        {elseif $message.message.type_id == $smarty.const.MESSAGE_TYPE_LOGIN_FAILED}
            <span style="color: red;">{$message.message.sender.login}&nbsp;&rarr;&nbsp;MaM
            <br><b>{$message.message.title}</b></span>
        {elseif $message.message.type_id == $smarty.const.MESSAGE_TYPE_ONLINE}
            <b>{$message.message.sender.login} is online</b>
        {elseif $message.message.type_id == $smarty.const.MESSAGE_TYPE_LOGOUT}
            <b>{$message.message.sender.login} logged OUT {if $message.message.title != 'I left .'}{$message.message.title}{/if}</b>
        {elseif $message.message.type_id == $smarty.const.MESSAGE_TYPE_AWAY}
            <b>{$message.message.sender.login} is idle</b>
        {elseif $message.message.type_id == $smarty.const.MESSAGE_TYPE_ORDER}
            <b>{$message.message.title}</b>
        {/if}            
            {if $smarty.session.user.role_id <= $smarty.const.ROLE_STAFF && !empty($message.message.is_pending) && isset($message.message.is_pending_recipient) && (empty($message.message.userdata) || empty($message.message.userdata.done_at))}
            <div id="message-{$message.message.id}-pending" class="panding-label" style="position: absolute; top: 0; right: 0; cursor: pointer;" onclick="mark_message_as_done({$message.message.id});">
                {if !empty($message.message.deadline)}deadline : {$message.message.deadline|date_format:'d/m/Y'}{else}MustDO !{/if}
            </div>
            {/if}
        </div>
        <div style="float: right; text-align: right; width: 70px; font-size: 11px; line-height: 14px; color: {if $message.message.type_id == $smarty.const.MESSAGE_TYPE_LOGIN_FAILED}red;{else}{$message.message.sender.color};{/if}">
            <a href="javascript: void(0);" style="text-decoration: none; cursor: pointer; color: {if $message.message.type_id == $smarty.const.MESSAGE_TYPE_LOGIN_FAILED}red;{else}{$message.message.sender.color};{/if}" onclick="show_chat_message_ref(this, {$message.message.id}, '{$message.message.created_at|date_format:"d/m/Y"} {$message.message.created_at|date_format:"H:i:s"}');">{$message.message.created_at|date_format:'d/m/Y'}<br>
            {$message.message.created_at|date_format:'H:i:s'}</a>
        </div>
        <div class="separator"></div>
    </div>
    <div class="chat-message-text" style="margin: 5px 0 10px; overflow: auto; position: absolute; top: 75px; bottom: 0px; left: 0px; right: 0px;">
        {if $message.message.type_id == $smarty.const.MESSAGE_TYPE_LOGIN_FAILED}
        <span style="color:red;">{$message.message.description|parse}</span>
        {else}
        {$message.message.description|parse}</b></i></a>{* trick for closing unclosed <i> & <a> tags*}
        {/if}
        {if isset($message.message.attachments) && !empty($message.message.attachments)}
        <div class="chat-message-attachments" style="margin-bottom: 10px; line-height: 14px;">
            {foreach from=$message.message.attachments item=row}
                {include file='templates/html/dropbox/control_attachment_block_text.tpl' attachment=$row.attachment readonly=true}        
            {/foreach}
        </div>
        {/if}        
    </div>
</div>