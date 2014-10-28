{if !empty($attachment)}
    <nobr>
        <span class="uf-container" style="border: solid 1px #ccc; display: block; width: 100%; margin-bottom: 5px; padding-left: 5px; padding-bottom: 3px; background-color: #eeeeee; cursor: pointer;" data-id="{$attachment.id}" data-oalias="{if !empty($object_alias)}{$object_alias}{/if}" data-oid="{if !empty($object_id)}{$object_id}{/if}">
            {att source=$attachment}&nbsp;&nbsp;({$attachment.size|human_filesize})
            <input class="pull-right" type="checkbox" style="margin-right: 5px;">
        </span>
    </nobr> 
{/if}