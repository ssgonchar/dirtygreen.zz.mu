<ul id="chat-messages" class="timeline chat-messages search-target ">
    {foreach from=$list item=message}
      

<li>
    <div class="timeline-badge">
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
    <div class="timeline-panel" style='width:90%; margin-left:20px;' {if isset($itemid)}id="{$itemid}" {/if}class="chat-message" style="color: {if $message.message.type_id == $smarty.const.MESSAGE_TYPE_ORDER}red{else}{$message.message.sender.color}{/if};">
        <div class="timeline-heading"> 

                    <h4 class="timeline-title">
            
                        {if $smarty.session.user.role_id <= $smarty.const.ROLE_STAFF && !empty($message.message.is_pending) && isset($message.message.is_pending_recipient) && (empty($message.message.userdata) || empty($message.message.userdata.done_at))}
            <div id="message-{$message.message.id}-pending" class="btn btn-success btn-xs panding-label" style="position: absolute; top: 0; right: 0; cursor: pointer;" onclick="mark_message_as_done({$message.message.id});">
                {if !empty($message.message.deadline)}deadline : {$message.message.deadline|date_format:'d/m/Y'}{else}Click when done{/if}
            </div>
            {/if}                        
                     
            <span type="button" class="btn btn-info btn-xs msg-reply" data-user-id='{$message.message.sender_id}' data-msg-id='{$message.message.id}'>Reply</span>
            <!--<a href="javascript: void(0);" onclick="show_chat_message_ref(this, {$message.message.id}, '{$message.message.created_at|date_format:"d/m/Y"} {$message.message.created_at|date_format:"H:i:s"}');" class="btn btn-info"  title='Click here to get the link'><i class="glyphicon glyphicon-link"></i></a>-->
            <button type="button" zclip="0" class="btn btn-info make-ref-new btn-xs search-page-{$current_page}" data-container="body" data-toggle="popover" data-placement="right" data-content="Link copied to clipboard." data-ref="<ref message_id={$message.message.id}>Ref. {$message.message.created_at|date_format:"d/m/Y"} {$message.message.created_at|date_format:"H:i:s"}</ref>">
    copy REF
 </button>
            
                {if $message.message.type_id == $smarty.const.MESSAGE_TYPE_NORMAL || $message.message.type_id == $smarty.const.MESSAGE_TYPE_PRIVATE || $message.message.type_id == $smarty.const.MESSAGE_TYPE_PERSONAL}

                    <b>{$message.message.title|parse|highlight:$keyword:$is_phrase}</b>
                {elseif $message.message.type_id == $smarty.const.MESSAGE_TYPE_SERVICE}

                    <b>{$message.message.title|parse|highlight:$keyword:$is_phrase}</b>            
                {elseif $message.message.type_id == $smarty.const.MESSAGE_TYPE_LOGIN}
                    <b>{$message.message.sender.login} logged IN {$message.message.title}</b>
                {*
                {elseif $message.message.type_id == $smarty.const.MESSAGE_TYPE_LOGIN_FAILED}
                    <span style="color: red;">{$message.message.sender.login}&nbsp;&rarr;&nbsp;MaM
                    <b>{$message.message.title}</b></span>*}
                {elseif $message.message.type_id == $smarty.const.MESSAGE_TYPE_ONLINE}
                    <b>{$message.message.sender.login} is online</b>
                {elseif $message.message.type_id == $smarty.const.MESSAGE_TYPE_LOGOUT}
                    <b>{$message.message.sender.login} logged OUT {if $message.message.title != 'I left .'}{$message.message.title}{/if}</b>
                {elseif $message.message.type_id == $smarty.const.MESSAGE_TYPE_AWAY}
                    <b>{$message.message.sender.login} is idle</b>
                {elseif $message.message.type_id == $smarty.const.MESSAGE_TYPE_ORDER}
                    <b>{$message.message.title}</b>
                {/if}            
                    </h4>     

                    <p class="h5" style="color: #0099cc">
                    {if $message.message.type_id == $smarty.const.MESSAGE_TYPE_PRIVATE || $message.message.type_id == $smarty.const.MESSAGE_TYPE_PERSONAL}
                        <span class="badge">p</span>&nbsp;
                    {/if}
                    {$message.message.sender.login}
                    
                    &nbsp;<i class="glyphicon glyphicon-arrow-right"></i>&nbsp;
                    
                    {if !isset($message.message.recipient) || empty($message.message.recipient)}
                        MaM
                    {else}
                        {foreach from=$message.message.recipient item=r name=r}
                            {$r.user.login}
                            {if !$smarty.foreach.r.last},&nbsp;{/if}
                        {/foreach}
                        {if !empty($message.message.cc)}
                            &nbsp;<span class="badge"> cc </span>&nbsp;
                            {foreach from=$message.message.cc item=c name=c}
                                {$c.user.login}
                                {if !$smarty.foreach.c.last},&nbsp;{/if}
                            {/foreach}
                        {/if}
                    {/if}
                    &nbsp;
                    {$message.message.created_at|date_format:'d/m/Y'}
                    &nbsp;
                    {$message.message.created_at|date_format:'H:i:s'}
                    
                
                </p>
                <p style="color:#666666;"></p>

        </div>
                    {if $message.message.description}            
                        <hr>
                        <div class="timeline-body">
                            <p>
                                {if $message.message.type_id == $smarty.const.MESSAGE_TYPE_LOGIN_FAILED}
                                    <span style="color:red;">{$message.message.description|parse|highlight:$keyword:$is_phrase|nl2br}</span>
                                {else}


                                    {$message.message.description|parse|highlight:$keyword:$is_phrase}</b></i></a>{* trick for closing unclosed <b>, <i> & <a> tags*}
                                {/if}      

                            </p>
                        </div>
                    {/if}
                    {if isset($message.message.attachments) && !empty($message.message.attachments)}
                        <hr/>
                        <div class="chat-message-attachments">
                            {foreach from=$message.message.attachments item=row}
                                {include file='templates/html/dropbox/control_attachment_block_text.tpl' attachment=$row.attachment readonly=true}        
                            {/foreach}
                        </div>
                    {/if}
    </div>
</li>   

    {/foreach}

</ul>    