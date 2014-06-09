{*<!--
<ol id="chat-messages" class="chat-messages"{if empty($list)} style="display: none;"{/if}>
{foreach from=$list item=row}
    {include file='templates/html/chat/control_chat_messagemod.tpl' message=$row itemid="chat-pending-{$row.message.id}"}
{/foreach}
</ol>-->*}
                            <ul id="chat-messages" class="timeline chat-messages search-target">
                                {foreach from=$list item=row}
                                    {if $row.message.type_id != $smarty.const.MESSAGE_TYPE_AWAY && $row.message.type_id != $smarty.const.MESSAGE_TYPE_ONLINE}
                                            {include file='templates/html/chat/control_chat_messagemod.tpl' message=$row}
                                    {/if}                                      
                                <!--

                                -->
                                {/foreach}
                            </ul>
<div id="chat-no-pendings" {if !empty($list)} style="display: none;"{/if}>There are no MustDO messages.</div>