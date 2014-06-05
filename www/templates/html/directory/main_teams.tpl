{if !empty($left)}
<table width="100%">
    <tr>
        <td width="49%" style="vertical-align: top;">
        {foreach from=$left item=row}
            {include file='templates/html/directory/control_teamcard.tpl'}
        {/foreach}
        </td>
        <td width="2%"></td>
        <td width="49%" style="vertical-align: top;">
        {foreach from=$right item=row}
            {include file='templates/html/directory/control_teamcard.tpl'}
        {/foreach}        
        </td>
    </tr>
</table>
{else}
No teams specified
{/if}