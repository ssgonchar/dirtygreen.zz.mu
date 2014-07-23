{if !empty($attachment)}
<span class="uf-container" style="margin-right: 10px; height: 20px;display: inline-block;" data-id="{$attachment.id}" data-oalias="{if !empty($object_alias)}{$object_alias}{/if}" data-oid="{if !empty($object_id)}{$object_id}{/if}">
    {att source=$attachment}&nbsp;&nbsp;({$attachment.size|human_filesize})
    {* <a class="attachment-{$attachment.ext|lower}" href="/file/{$attachment.secret_name}/{$attachment.original_name}"{if $attachment.type == 'image'} rel="pp_attachments[]"{else} target="_blank"{/if} title="{$attachment.title|escape:'html'}">{$attachment.original_name} ({$attachment.size|human_filesize})</a> *}
    {if ($smarty.session.user.role_id <= $smarty.const.ROLE_MODERATOR || $smarty.session.user.id == $attachment.created_by)
        && ($object_alias == $attachment.object_alias && $object_id == $attachment.object_id)}
    <span class="icon delete" onclick="uploader_remove_attachment(this);"></span>
    {/if}
</span>
{/if}