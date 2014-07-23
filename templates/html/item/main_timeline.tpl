<table width="50%">
    <tr>
        <td style="width: 50%;" class="text-right text-bottom">
        {foreach from=$timeline.left item=event}
            {include file='templates/html/item/control_timeline_event.tpl' event=$event}
        {/foreach}
        </td>
        <td style="padding-bottom : 40px;" class="text-bottom">
        {foreach from=$timeline.right item=event}
            {include file='templates/html/item/control_timeline_event.tpl' event=$event}
        {/foreach}
        </td>
    </tr>
</table>