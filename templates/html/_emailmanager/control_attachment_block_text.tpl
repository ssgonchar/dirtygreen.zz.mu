{if !empty($attachment)}
    <span id="attachment-{$attachment.id}" style="border: solid 1px #ccc; display: block; width: 100%; margin-bottom: 5px; padding-left: 5px; padding-bottom: 3px; background-color: #eeeeee;">
        {att source=$attachment}&nbsp;
        ({$attachment.size|human_filesize})&nbsp;
        {if !$readonly}<i class="glyphicon glyphicon-remove pull-right" onclick="remove_attachment('{if isset($object_alias)}{$object_alias}{else}{$attachment.object_alias}{/if}', {if isset($object_id)}{$object_id}{else}{$attachment.object_id}{/if}, {$attachment.id}); return false;" style="margin-top: 4px; margin-right: 4px; cursor: pointer;"></i>{else}&nbsp;&nbsp;{/if}
    </span>
{/if}