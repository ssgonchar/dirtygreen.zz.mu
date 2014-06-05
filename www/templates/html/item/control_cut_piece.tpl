<tr id="piece-{$index}">
    <td>
        <img src="/img/icons/cross-small.png" onclick="cut_remove_piece({$index});">
        <input type="hidden" name="pieces[{$index}][id]" value="0">
    </td>
    <td><input type="text" name="pieces[{$index}][guid]" value="{if !empty($item.guid)}{$item.guid|escape:'html'}-{$index + 1}{/if}" class="max"></td>
    <td>{if isset($item.steelgrade)}{$item.steelgrade.title|escape:'html'}{else}{''|undef}{/if}</td>
    <td><input type="hidden" id="thickness-{$index}" value="{$item.thickness}">{$item.thickness|escape:'html'}</td>
    <td><input type="text" id="width-{$index}" name="pieces[{$index}][width]" value="{$item.width|escape:'html'}" class="max" style="text-align: center;" onkeyup="calc_unitweight({$index});"></td>
    <td><input type="text" id="length-{$index}" name="pieces[{$index}][length]" value="{$item.length|escape:'html'}" class="max" style="text-align: center;" onkeyup="calc_unitweight({$index});"></td>
    <td><input type="text" id="unitweight-{$index}" name="pieces[{$index}][unitweight]" value="{$item.unitweight|escape:'html'}" class="max" style="text-align: center;"></td>
    <td><input type="text" name="pieces[{$index}][notes]" value="" class="max"></td>
    <td>
        <select id="location-{$index}" name="pieces[{$index}][location_id]" style="width: 99%;" onchange="cut_get_positions({$index}, this.value, {$item.id});">
            <option value="0">--</option>
            {foreach from=$locations item=row_location}
            <option value="{$row_location.company.id}">{$row_location.company.doc_no} ({if $row_location.company.stocklocation.title != $row_location.company.city.title}{$row_location.company.stocklocation.title}, {$row_location.company.city.title}{else}{$row_location.company.city.title}{/if})</option>
            {/foreach}
        </select>
    </td>
    <td>
        <select id="position-{$index}" name="pieces[{$index}][position_id]" style="width: 99%;">
            <option value="0">--</option>
        </select>
    </td>
    <td>{''|undef}</td>
</tr>

{* foreach from=$row.positions item=row_position}
<option value="{$row_position.steelposition.id}">
    {$row_position.steelposition.steelgrade.title} {$row_position.steelposition.thickness} x {$row_position.steelposition.width} x {$row_position.steelposition.length} - {$row_position.steelposition.qtty} pcs
</option>
{/foreach *}
