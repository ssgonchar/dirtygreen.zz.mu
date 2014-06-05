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
        <img src="/img/icons/cross.png" onclick="uploader_remove_attachment(this);" style="cursor: pointer;" />
{*        <a class="delete" href="javascript: void(0);" onclick="uploader_remove_attachment(this);">delete</a>  *}
    </td>
</tr>
{else}
    {*<tr class="uf-container" style="visibility: hidden;">
        <td class="uf-name" style="border: none;"></td>
        <td class="uf-size" style="border: none;"></td>
        <td class="uf-title" style="border: none;"></td>
        <td class="uf-buttons" style="width: 75px; border: none;"></td>
    </tr>*}
{/if}

{*
{if !empty($attachment)}
<tr class="uf-container is-{$attachment.type}{if !empty($attachment.is_main)} is-main{/if}" data-oalias="{$attachment.object_alias}" data-oid="{$attachment.object_id}" data-id="{$attachment.id}">
    <td class="uf-name" title="{$attachment.original_name}">{att source=$attachment}</td>
    <td class="uf-size">{$attachment.size|human_filesize}</td>
    <td class="uf-title">
        {if $can_be_edited == 1}
        <input class="title-input normal" type="text" name="titles[{$attachment.id}]" value="{$attachment.title|escape:'html'}" placeholder="Title" maxlength="50">
        {\\<a class="save" href="javascript: void(0);" onclick="return false;" title="Save Title"></a>//}
        {else}<span class="title-span">{$attachment.title|escape:'html'|undef}</span>
        {/if}
    </td>
    {if $can_be_edited == 1}
    <td class="uf-buttons" style="width: 75px;">
        {if $smarty.session.user.role_id <= $smarty.const.ROLE_MODERATOR || $smarty.session.user.id == $attachment.created_by}
        <a class="delete" href="javascript: void(0);" onclick="uploader_remove_attachment(this);">delete</a>
        {\\if $attachment.type == 'image'}<a class="asmain" href="javascript: void(0);" onclick="uploader_set_as_main(this);">set as main</a>{/if//}
        {/if}
    </td>
    {/if}
</tr>
{else}
    <tr class="uf-container" style="visibility: hidden;">
        <td class="uf-name" style="border: none;"></td>
        <td class="uf-size" style="border: none;"></td>
        <td class="uf-title" style="border: none;"></td>
        {if $can_be_edited == 1}
        <td class="uf-buttons" style="width: 75px; border: none;"></td>
        {/if}
    </tr>
{/if}
*}