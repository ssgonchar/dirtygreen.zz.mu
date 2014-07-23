{if !empty($left)}
<table width="100%">
    <tr>
        <td width="49%" style="vertical-align: top;">
        {foreach from=$left item=row}
            <h4>{$row.team.title|escape:'html'}</h4>
            {if !empty($row.team.products)}
                {include file="templates/html/product/control-productstree.tpl" products=$row.team.products}
            {/if}
        {/foreach}
        </td>
        <td width="2%"></td>
        <td width="49%" style="vertical-align: top;">
        {foreach from=$right item=row}
            <h4>{$row.team.title|escape:'html'}</h4>
            {if !empty($row.team.products)}
                {include file="templates/html/product/control-productstree.tpl" products=$row.team.products}
            {/if}
        {/foreach}        
        </td>
    </tr>
</table>
{else}
No products specified
{/if}