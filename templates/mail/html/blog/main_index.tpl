<div id="blog_messages">
{if empty($list)}
    Nothing was found on my request
{else}
    {foreach $list as $row}
        {if $row.entity_type == 'message'}    
            {include file='templates/controls/blog_message.tpl' row=$row}
        {elseif $row.entity_type == 'email'}
            {include file='templates/controls/blog_email.tpl' row=$row}
        {/if}
    {/foreach}
{/if}
</div>
{if $page_no == 1}<div id="chat-updater" style="display: none;"></div>{/if}