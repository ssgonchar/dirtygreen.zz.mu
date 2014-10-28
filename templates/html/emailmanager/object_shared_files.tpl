<h3>Shared Files</h3>
<span class="uploaded-attachments">
    {if !empty($attachments_list)}
        {foreach $attachments_list as $row}
            {include file='templates/html/emailmanager/object_shared_file_span.tpl' attachment=$row.attachment} 
        {/foreach}<br/>
    {else} 
</span>
<span class="no-uploaded-files view">There are no shared files .</span>
{/if}