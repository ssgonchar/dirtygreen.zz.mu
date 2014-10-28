{if !empty($attachment)}
<tr class="uf-container is-{$attachment.type}" data-oalias="{$attachment.object_alias}" data-oid="{$attachment.object_id}" data-id="{$attachment.id}">
    <td class="uf-name" title="{$attachment.original_name}">
        <span class="attachment-{$attachment.ext}">{$attachment.original_name}</span>
    </td>
    <td class="uf-size">{$attachment.size|human_filesize}</td>
    <td class="uf-title">
        <input class="title-input normal" type="text" name="titles[{$attachment.id}]" placeholder="Title" maxlength="50" />
    </td>
    <td class="uf-buttons" style="width: 75px;">
        {if !isset($readonly)}<img src="/img/icons/cross.png" onclick="uploader_remove_attachment(this);" style="cursor: pointer;" />{/if}
    </td>
</tr><br/>
{/if}