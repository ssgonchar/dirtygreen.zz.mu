{if !empty($attachments)}{*debug*}
    {foreach name=i from=$attachments item=row}
        <span id="attachment-{$row.id}" style="border: solid 1px #ccc; display: block; width: 100%; margin-bottom: 5px; padding-left: 5px; padding-bottom: 3px; background-color: #eeeeee;">
            <nobr>
                {att source=$row}&nbsp;
            </nobr>
                ({$row.size|human_filesize})&nbsp;
                {if !$readonly}<i id="remove-shared-doc" class="glyphicon glyphicon-remove pull-right" style="margin-top: 4px; margin-right: 4px; cursor: pointer;"></i>{else}&nbsp;&nbsp;{/if}
        </span>
    {/foreach}
{/if}