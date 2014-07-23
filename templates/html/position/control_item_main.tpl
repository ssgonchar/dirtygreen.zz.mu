<tr id="t-i-{$next_row_index}" class="steelitems" style="cursor: text;">
    <td>
        <input type="text" id="guid-{$next_row_index}" name="item[{$next_row_index}][guid]" value="" class="max" onkeyup="item_plateid_change({$next_row_index});">
        <input type="hidden" id="t-i-deleted-{$next_row_index}" name="item[{$next_row_index}][is_deleted]" value="0">
    </td>
    <td>
        <select name="item[{$next_row_index}][steelgrade_id]" class="max">
            <option value="0">--</option>
            {foreach from=$steelgrades item=sgrow}
            <option value="{$sgrow.steelgrade.id}"{if $position.steelposition.steelgrade_id == $sgrow.steelgrade.id} selected="selected"{/if}>{$sgrow.steelgrade.title|escape:'html'}</option>
            {/foreach}                    
        </select>
    </td>
    <td><input type="text" id="i-thickness-{$next_row_index}" name="item[{$next_row_index}][thickness]" value="{if !empty($position.steelposition.thickness)}{$position.steelposition.thickness|escape:'html'}{/if}" class="max" onkeyup="calc_unitweight({$next_row_index}, '{$position.steelposition.dimension_unit}', '{$position.steelposition.weight_unit}', 'i');"></td>
    <td><input type="text" id="i-width-{$next_row_index}" name="item[{$next_row_index}][width]" value="{if !empty($position.steelposition.width)}{$position.steelposition.width|escape:'html'}{/if}" class="max" onkeyup="calc_unitweight({$next_row_index}, '{$position.steelposition.dimension_unit}', '{$position.steelposition.weight_unit}', 'i');"></td>
    <td><input type="text" id="i-length-{$next_row_index}" name="item[{$next_row_index}][length]" value="{if !empty($position.steelposition.length)}{$position.steelposition.length|escape:'html'}{/if}" class="max" onkeyup="calc_unitweight({$next_row_index}, '{$position.steelposition.dimension_unit}', '{$position.steelposition.weight_unit}', 'i');"></td>
    <td><input type="text" id="i-unitweight-{$next_row_index}" name="item[{$next_row_index}][unitweight]" value="{if !empty($position.steelposition.unitweight)}{$position.steelposition.unitweight|escape:'html'|string_format:'%.2f'}{/if}" class="max"></td>
    <td><input type="text" id="measured-thickness-{$next_row_index}" name="item[{$next_row_index}][thickness_measured]" value="" class="max" onkeyup="calc_unitweight({$next_row_index}, '{$position.steelposition.dimension_unit}', '{$position.steelposition.weight_unit}', 'measured');"></td>
    <td><input type="text" id="measured-width-{$next_row_index}" name="item[{$next_row_index}][width_measured]" value="" class="max" onkeyup="calc_unitweight({$next_row_index}, '{$position.steelposition.dimension_unit}', '{$position.steelposition.weight_unit}', 'measured');"></td>
    <td><input type="text" id="measured-length-{$next_row_index}" name="item[{$next_row_index}][length_measured]" value="" class="max" onkeyup="calc_unitweight({$next_row_index}, '{$position.steelposition.dimension_unit}', '{$position.steelposition.weight_unit}', 'measured');"></td>
    <td><input type="text" id="measured-unitweight-{$next_row_index}"name="item[{$next_row_index}][unitweight_measured]" value="" class="max"></td>
    <td><input type="text" name="item[{$next_row_index}][width_max]" value="" class="max"></td>
    {if isset($include_nominal) && $include_nominal}
    <td>
        <input type="text" name="item[{$next_row_index}][nominal_thickness_mm]" value="" style="width: 100%;">
    </td>
    <td>
        <input type="text" name="item[{$next_row_index}][nominal_width_mm]" value="" style="width: 100%;">
    </td>
    <td>
        <input type="text" name="item[{$next_row_index}][nominal_length_mm]" value="" style="width: 100%;">
    </td>            
    {/if}    
    <td><input type="text" name="item[{$next_row_index}][unitweight_weighed]" value="" class="max"></td>
    <td class="text-center"><input type="checkbox" id="is_virtual-{$next_row_index}" name="item[{$next_row_index}][is_virtual]" value="1" checked="checked"></td>
    <td>*<input type="hidden" id="t-i-id-{$next_row_index}" name="item[{$next_row_index}][id]" value="0"></td>    
    <td class="text-center"><img id="pic-delete-{$next_row_index}" src="/img/icons/cross.png" style="cursor: pointer" onclick="position_item_remove({$next_row_index});"></td>
</tr>        
