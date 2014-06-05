<table id="companies" class="form" width="50%">
    <tr>
        <td style="font-size: 10px; color: #777;">Company</td>
        <td width="20%" style="font-size: 10px; color: #777;">Location for WebStock</td>
        <td width="20%" style="font-size: 10px; color: #777;">Location for Int. Stock</td>
        <td width="5%"></td>
    </tr>
    {foreach from=$locations item=row}
    <tr id="s-l-{$row.company_id}">
        <td>
            <a href="/company/view/{$row.company_id}">{$row.company.title|escape:'html'}</a> ({$row.company.city.title|escape:'html'})
            <input type="hidden" id="s-l-deleted-{$row.company_id}" name="location[{$row.company_id}][deleted]" value="0">
            <input type="hidden" name="location[{$row.company_id}][company_id]" value="{$row.company_id}">
            <input type="hidden" id="s-l-id-{$row.company_id}" value="{$row.company_id}">
        </td>
        <td>
            <input type="text" name="location[{$row.company_id}][location]" clas="max"{if isset($row.company.location)} value="{$row.company.location.title}"{/if}>
        </td>
        <td>
            <input type="text" name="location[{$row.company_id}][int_location_title]" clas="max"{if !empty($row.company.int_location_title)} value="{$row.company.int_location_title}"{/if}>
        </td>
        <td>
            <img id="s-l-pic-{$row.company_id}" src="/img/icons/cross.png" style="cursor: pointer" onclick="stock_remove_location({$row.company_id});">
        </td>
    </tr>        
    {/foreach}
    <tr id="new">
        <td>
            <input type="text" id="company_title" value="" class="max">
            <input type="hidden" id="company_id" value="0">
        </td>
        <td>
            <input type="text" id="location_title" value="" class="max">
        </td>
        <td>
            <input type="text" id="int_location_title" value="" class="max">
        </td>
        <td><img src="/img/icons/plus-circle.png" style="cursor: pointer;" onclick="stock_add_location();"></td>
    </tr>
</table>
<input type="hidden" id="stock-location-last-id" value="{if empty($stock_location_last_id)}1{else}{$stock_location_last_id}{/if}">
