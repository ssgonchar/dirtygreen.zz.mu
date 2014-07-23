<div class="js-app-context" style="padding: 7px; max-height: 300px; overflow: auto;">
    <a href="javascript: void(0);" class="a-close" onclick="destroy_obj('js-position-{$position.id}-context');">close</a>
    <span style="font-weight: bold;">{$position.steelgrade.title} {$position.thickness} x {$position.width} x {$position.length}</span>
    <div class="pad1"></div>
    <div style="float: left; width: 140px; margin-bottom: 5px;"><a class="edit" href="/position/{$position.id}/edit">edit position</a></div>
    <div style="float: left; width: 140px; margin-bottom: 5px;"><a class="table-small" href="javascript: void(0);" onclick="show_items({$position.id}, {$is_revision});">show items</a></div>
    <div style="float: left; width: 140px; margin-bottom: 5px;"><a class="history" href="/position/{$position.id}/history">view history</a></div>
    <div class="separator"></div>
    <hr style="width: 100%; color: #dedede;" size="1"/>
    <span>Pictures :</span><br />
    {*if isset($attachments)*}
    {if !empty($attachments)}
    <br />
    {foreach from=$attachments item=row}
    {picture type="{$row.attachment.object_alias}" size="x" source=$row.attachment pretty_id="{$row.attachment.object_alias}{$row.attachment.object_id}" style="float: left; margin-right: 10px;"}
    {/foreach}
    {else}
    no pictures
    {/if}
</div>