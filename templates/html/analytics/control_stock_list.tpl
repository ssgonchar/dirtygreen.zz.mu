<table class="form" width="100%">
    {foreach from=$list item=row}
    <tr{if !empty($row.status)} class="tr-order-{$row.status}"{/if}>
        <td class="text-center" width="2%">
            <input type="radio" id="r-order-{if empty($row.status)}{$row.guid}{else}{$row.id}{/if}" name="order_id" value="{if empty($row.status)}{$row.guid}{else}{$row.id}{/if}">
        </td>
        <td width="10%">
            <label for="r-order-{if empty($row.status)}{$row.guid}{else}{$row.id}{/if}">{if !empty($row.status)}<a href="/order/{$row.id}/edit"># {$row.id}{else}<a href="/order/neworder/{$row.guid}">new{/if}</a></label>
        </td>
        <td>
            <label for="r-order-{if empty($row.status)}{$row.guid}{else}{$row.id}{/if}">{if isset($row.company)}{$row.company.title|escape:'html'}{/if}</label>
        </td>        
        <td>
            <label for="r-order-{if empty($row.status)}{$row.guid}{else}{$row.id}{/if}">{if !empty($row.buyer_ref)}{$row.buyer_ref|escape:'html'}{/if}</label>
        </td>
        <td>
            <label for="r-order-{if empty($row.status)}{$row.guid}{else}{$row.id}{/if}">{if isset($row.biz)}{$row.biz.number_output|escape:'html'}{/if}</label>
        </td>        
        <td><label for="r-order-{if empty($row.status)}{$row.guid}{else}{$row.id}{/if}">{if isset($row.author)}{$row.author.login|escape:'html'}, {$row.created_at|date_human}{/if}</label></td>
        <td>
            <label for="r-order-{if empty($row.status)}{$row.guid}{else}{$row.id}{/if}">
                {if empty($row.status)}<i>Unregistered</i>
                {elseif $row.status == 'ip'}In Process
                {/if}
            </label>
        </td>
    </tr>    
    {/foreach}
</table>