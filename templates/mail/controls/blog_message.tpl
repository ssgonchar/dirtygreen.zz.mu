
<div class="biz-blog-tl-block biz-blog-entity">
    <div class="biz-blog-tl-head">
           
        <div class="biz-blog-tl-head-left" style="color: {$row.message.sender.color};">
            <div class="biz-blog-tl-avatar">
                {if $row.message.sender_id == $smarty.const.GNOME_USER}
                    <img src="/img/layout/gnome.jpg" alt="Gnome" alt="Gnome">
                {elseif isset($row.message.sender) && isset($row.message.sender.person)}
                    {if isset($row.message.sender.person.picture)}{picture type="person" size="x" source=$row.message.sender.person.picture}
                    {elseif $row.message.sender.person.gender == 'f'}<img src="/img/layout/anonymf.png" alt="{$row.message.sender.login}" alt="{$row.message.sender.login}">
                    {else}<img src="/img/layout/anonym.png" alt="{$row.message.sender.login}" alt="{$row.message.sender.login}">{/if}
                {else}
                    <img src="/img/layout/anonym.png" alt="No Picture" alt="No Picture">
                {/if}
            </div>
            <b>{$row.message.title|parse}</b>
            <div class="biz-blog-tl-link-address">
                {if $row.message.type_id == $smarty.const.MESSAGE_TYPE_PRIVATE || $row.message.type_id == $smarty.const.MESSAGE_TYPE_PERSONAL}
                    <i>(p)</i>&nbsp;
                {/if}
                {$row.message.sender.login}&nbsp;&rarr;&nbsp;
                {if !isset($row.message.recipient) || empty($row.message.recipient)}
                    MaM
                {else}
                    {foreach from=$row.message.recipient item=r name=r}{$r.user.login}{if !$smarty.foreach.r.last}/{/if}{/foreach}{if !empty($row.message.cc)}.cc.{foreach from=$row.message.cc item=c name=c}{$c.user.login}{if !$smarty.foreach.c.last}/{/if}{/foreach}{/if}
                {/if}   
            </div>
        </div>
            
        <div class="biz-blog-tl-head-right">
            <div class="biz-blog-tl-created-at">
                {$row.message.created_at|date_format:"d/m/Y"}<br> 
                {$row.message.created_at|date_format:"H:i:s"}
            </div>
            {if $smarty.session.user.role_id <= $smarty.const.ROLE_STAFF && !empty($row.message.is_pending) && isset($row.message.is_pending_recipient) && (empty($row.message.userdata) || empty($row.message.userdata.done_at))}
                <div id="message-{$row.message.id}-pending" class="biz-blog-tl-deadline" onclick="mark_message_as_done({$row.message.id});">
                {if !empty($row.message.deadline)}
                    Deadline: {$row.message.deadline|date_format:'d/m/Y'}
                {else}
                    MustDO !
                {/if}
                </div>
            {/if}
            <div class="biz-blog-tl-logo">TL</div>
        </div>
        <div class="separator"></div>
            
    </div>

    <div class="biz-blog-tl-text" style="color: {$row.message.sender.color};">
        {$row.message.description|parse|nl2br}
        <div class="pad-10"></div>
    </div>
    {if isset($row.message.attachments) && !empty($row.message.attachments)}    
        <div class="biz-blog-tl-attachments">
            {foreach from=$row.message.attachments item=att}
                {include file='templates/html/dropbox/control_attachment_block_text.tpl' attachment=$att.attachment readonly=true}        
            {/foreach}
        </div> 
    {/if}
    <div class="biz-blog-tl-link">
        <div class="biz-blog-tl-link-left">
            <a class="biz-blog-href-reference" title="Get reference" onclick="show_blog_message_ref(this, {$row.message.id}, '{$row.message.created_at|date_format:"d/m/Y"} {$row.message.created_at|date_format:"H:i:s"}');">Get reference</a>
            <a class="biz-blog-href-tl-reply" title="Answer" href="javascript: void(0);" onclick="show_chat_modal('{$object_alias}', {$object_id}, {$row.message.id});">Answer</a>
            {*  <a href="/message/{$row.message.id}" class="biz-blog-href-link" title="Link">Link</a>   *}
        </div>
        <div class="biz-blog-tl-link-right">
            Message ID : {$row.message.id}
        </div>    
    </div>    

</div>
