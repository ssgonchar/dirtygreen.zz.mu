<tr id="t-il-{$next_row_index}">
    <td class="guid-{$next_row_index}">{''|undef}</td>
    <td>{''|undef}</td>
    <td>{''|undef}</td>
    <td>{''|undef}</td>
    <td>
        <select name="item[{$smarty.foreach.i.index + 1}][owner_id]" class="max">
            <option value="0">--</option>
            <option value="{$smarty.const.MAMIT_OWNER_ID}">MaM IT</option>
            <option value="{$smarty.const.MAMUK_OWNER_ID}">MaM UK</option>
            <option value="{$smarty.const.PLATESAHEAD_OWNER_ID}">PlatesAhead</option>
        </select>    
    </td>
    <td>
        <select name="item[{$next_row_index}][location_id]" class="max">
            <option value="0">--</option>
            {foreach from=$locations item=lrow}
            <option value="{$lrow.company.id}">{$lrow.company.doc_no|escape:'html'} ({$lrow.company.stocklocation.title|escape:'html'})</option>
            {/foreach}
        </select>            
    </td>
    <td>
        <select name="item[{$next_row_index}][status_id]" class="max">
            <option value="0" selected="selected">--</option>
            <option value="{$smarty.const.ITEM_STATUS_PRODUCTION}">In Production</option>
            <option value="{$smarty.const.ITEM_STATUS_TRANSFER}">Transfer To Stock</option>
            <option value="{$smarty.const.ITEM_STATUS_STOCK}">On Stock</option>
        </select>
    </td>
</tr>
