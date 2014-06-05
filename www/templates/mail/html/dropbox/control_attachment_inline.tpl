{if !empty($attachment)}
    {att source=$attachment}&nbsp;
    ({$attachment.size|human_filesize})
    {if isset($remove)}&nbsp;<img id="attachment-remove-{$attachment.id}" src="/img/icons/cross-small.png" onclick="remove_attachment('{if isset($object_alias)}{$object_alias}{else}{$attachment.object_alias}{/if}', {if isset($object_id)}{$object_id}{else}{$attachment.object_id}{/if}, {$attachment.id}); return false;" style="vertical-align: -4px; margin-right: 10px; cursor: pointer;" />{/if}
{/if}