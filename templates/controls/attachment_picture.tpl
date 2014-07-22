{if !empty($attachment)}
<div id="attachment-{$attachment.id}" style="width: 110px; height: 150px; float: left; margin: 0 10px 10px 0; text-align: center;">
    {picture type="{$attachment.object_alias}" size="{if isset($size)}{$size}{else}s{/if}" source=$attachment onclick="select_picture('{$attachment.object_alias}', {$attachment.object_id}, {$attachment.id}, '{$attachment.secret_name}', '{$attachment.original_name}');"}
    <div style="width: 110px; overflow: hidden; font-size: 11px; color: #777; margin-top: 5px; line-height: 14px;">
        {$attachment.original_name} ({$attachment.size|human_filesize})
        {if !$readonly}<br><a class="delete" href="javascript:void(0);" onclick="remove_attachment('{if isset($object_alias)}{$object_alias}{else}{$attachment.object_alias}{/if}', {if isset($object_id)}{$object_id}{else}{$attachment.object_id}{/if}, {$attachment.id}); return false;">remove</a>{/if}
    </div>
</div>
{/if}