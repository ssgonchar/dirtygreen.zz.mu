{if !empty($attachment)}
    <span id="attachment-{$attachment.id}" style="border: solid 1px #ccc; display: block; width: 100%; margin-bottom: 5px; padding-left: 5px; padding-bottom: 3px; background-color: #f5f5f5;">
        {att source=$attachment}&nbsp;
        ({$attachment.size|human_filesize})&nbsp;
       
    </span>
{/if}