{if !empty($attachment)}
<div id="attachment-{$attachment.id}" style="border: solid 1px {if !empty($attachment.is_main)}#ccc;{else}#f6f6f6;{/if} width: 300px; float: left; padding: 5px; margin: 0 10px 10px 0;">
    {if $attachment.type == 'image'}
        {picture type="{$attachment.object_alias}" size="x" source=$attachment pretty_id="{$attachment.object_alias}{$attachment.object_id}" style="float: left;"}
    {else}
        <img src="/img/icons/filetype/{$attachment.ext|lower}.png" style="float: left;">
    {/if}
    <div style="float: left; margin-left: 5px; line-height: 16px; width: 210px; overflow: hidden;">
        {att source=$attachment}
        <br>{$attachment.size|human_filesize}
        {if $smarty.session.user.role_id <= $smarty.const.ROLE_MODERATOR}
            <br><br><a class="delete" href="javascript: void(0);" onclick="remove_attachment('{$object_alias}', {$object_id}, {$attachment.id}); return false;">delete</a>
            {if $attachment.type == 'image' && empty($attachment.is_main)}<a class="asmain" href="javascript: void(0);" onclick="set_as_main({$attachment.id}); return false;">set as main</a>{/if}
        {/if}
    </div>
</div><br/>
{/if}