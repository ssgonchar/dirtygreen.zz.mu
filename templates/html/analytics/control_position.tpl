<tr id="position-{$row_index}">
    <td class="text-center" {if isset($row.steelposition)} id="position-params-{$row_index}" data-price_unit="{$row.steelposition.price_unit}" data-weight_unit="{$row.steelposition.weight_unit}"{/if}>
        {if isset($row.position_id) && !empty($row.position_id)}{$row.position_id}{else}*{/if}
        <input type="hidden" id="position-id-{$row_index}" name="position[{$row_index}][position_id]" value="{if isset($row.position_id)}{$row.position_id}{else}0{/if}">
        <input type="hidden" id="position-deleted-{$row_index}" name="position[{$row_index}][is_deleted]" value="{if isset($row.is_deleted)}{$row.is_deleted|escape:'html'}{else}0{/if}">
        <input type="hidden" class="cb-row" value="{$row_index}">
    </td>
    <td>
        <select name="position[{$row_index}][steelgrade_id]" class="max">
            <option value="0">--</option>
            {foreach from=$steelgrades item=sgrow}
            <option value="{$sgrow.steelgrade.id}"{if isset($row.steelgrade_id) && $row.steelgrade_id == $sgrow.steelgrade.id} selected="selected"{/if}>{$sgrow.steelgrade.title|escape:'html'}</option>
            {/foreach}                    
        </select>            
    </td>
    <td><input type="text" id="thickness-{$row_index}" name="position[{$row_index}][thickness]" value="{if isset($row.thickness)}{$row.thickness|escape:'html'}{/if}" style="width: 100%;" onkeyup="calc_unitweight({$row_index}); calc_weight({$row_index}); calc_value({$row_index}); calc_total_pos();"></td>
    <td><input type="text" id="width-{$row_index}" name="position[{$row_index}][width]" value="{if isset($row.width)}{$row.width|escape:'html'}{/if}" style="width: 100%;" onkeyup="calc_unitweight({$row_index}); calc_weight({$row_index}); calc_value({$row_index}); calc_total_pos();"></td>
    <td><input type="text" id="length-{$row_index}" name="position[{$row_index}][length]" value="{if isset($row.length)}{$row.length|escape:'html'}{/if}" style="width: 100%;" onkeyup="calc_unitweight({$row_index}); calc_weight({$row_index}); calc_value({$row_index}); calc_total_pos();"></td>
    <td><input type="text" id="unitweight-{$row_index}" name="position[{$row_index}][unitweight]" value="{if isset($row.unitweight)}{$row.unitweight|escape:'html'|string_format:'%.2f'}{/if}" style="width: 100%;" onkeyup="calc_weight({$row_index}); calc_value({$row_index}); calc_total_pos();"></td>
    <td><input type="text" id="qtty-{$row_index}" name="position[{$row_index}][qtty]" value="{if isset($row.qtty)}{$row.qtty|escape:'html'|string_format:'%d'}{if isset($row.qtty_available)} ({$row.qtty_available}){/if}{/if}" class="max" onkeyup="calc_weight({$row_index}); calc_value({$row_index}); calc_total_pos();"{if isset($row.qtty_error)} style="color: red;"{/if}></td>
    <td><input type="text" id="weight-{$row_index}" name="position[{$row_index}][weight]" value="{if isset($row.weight)}{$row.weight|escape:'html'|string_format:'%.2f'}{/if}" style="width: 100%;" onkeyup="calc_value({$row_index}); calc_total_pos();"></td>
    {if isset($price_unit)}
    <td>
        <input type="text" id="price-{$row_index}" name="position[{$row_index}][price]" value="{if isset($row.price)}{$row.price|escape:'html'|string_format:'%.2f'}{/if}" style="width: 100%;" onkeyup="calc_value({$row_index}); calc_total_pos();">
    </td>
    {else}
    <td class="text-left">
        <input type="text" id="price-{$row_index}" name="position[{$row_index}][price]" value="{if isset($row.price)}{$row.price|escape:'html'|string_format:'%.2f'}{/if}" style="width: 70%;" onkeyup="calc_value({$row_index}); calc_total_pos();">
        <label id="lbl-price-unit-{$row_index}">{$form.currency|cursign}/{$row.steelposition.price_unit|wunit}</label>
    </td>
    {/if}
    <td><input type="text" id="value-{$row_index}" name="position[{$row_index}][value]" value="{if isset($row.value)}{$row.value|escape:'html'|string_format:'%.2f'}{/if}" style="width: 100%;" onkeyup="calc_total_pos();"></td>
    <td><input type="text" id="delivery-time-{$row_index}" name="position[{$row_index}][deliverytime]" value="{if isset($row.deliverytime)}{$row.deliverytime|escape:'html'}{/if}" style="width: 100%;"></td>
    <td>{if isset($row.steelposition) && isset($row.steelposition.deliverytime)}{$row.steelposition.deliverytime.title|escape:'html'}{else}<i>not set</i>{/if}</td>
    <td><input type="text" id="internal_notes-{$row_index}" name="position[{$row_index}][internal_notes]" value="{if isset($row.internal_notes)}{$row.internal_notes|escape:'html'}{/if}" style="width: 100%;"></td>
    <td><!--{*
        {if isset($row.location) && !empty($row.location)}
            
            {foreach name='location' from=$row.location item=location}
            {$location}{if !$smarty.foreach.location.last}, {/if}
            {/foreach}
            
            
        {else}
          
        <i>not set</i>
        {/if}
        *}-->
                        {if isset($row.location) && !empty($row.location)}
            
            {foreach name='location' from=$row.location item=location}
            {$location}{if !$smarty.foreach.location.last}, {/if}
            {/foreach}
            
            
        {else}                            
                        {if isset($row.steelposition.quick)}
                            <div>
                                {$row.steelposition.quick.locations}
                            </div>
                            {if !empty($row.steelposition.quick.int_locations) && $row.steelposition.quick.int_locations != $row.steelposition.quick.locations}
                                <div style="font-size: 10px; color: #555;">
                                    {$row.steelposition.quick.int_locations}
                                </div>
                            {/if}
                        {else}
                            <i>not set</i>
                        {/if} 
        {/if}
    </td>
    <td>
    {if isset($row.plateid) && !empty($row.plateid)}
        {foreach name='plateid' from=$row.plateid item=plateid}
        {$plateid}{if !$smarty.foreach.plateid.last}, {/if}
        {/foreach}
    {else}
    <i>not set</i>
    {/if}
    </td>
    <td class="text-center"><img id="pic-delete-{$row_index}" src="/img/icons/cross.png" style="cursor: pointer" onclick="position_delete({$row_index});"></td>
</tr>