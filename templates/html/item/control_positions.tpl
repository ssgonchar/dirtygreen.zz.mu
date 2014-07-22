<table class="list" width="100%">
    <tbody>
        <tr class="top-table">
            <th width="1%"></th>
            <th width="3%">Id</th>
            <th width="8%">Steel Grade</th>
            <th width="5%" class="text-center">Thickness<span class="lbl-dim">{if isset($stock)}<br>{$stock.dimension_unit}{/if}</span></th>
            <th width="5%" class="text-center">Width<span class="lbl-dim">{if isset($stock)}<br>{$stock.dimension_unit}{/if}</span></th>
            <th width="5%" class="text-center">Length<span class="lbl-dim">{if isset($stock)}<br>{$stock.dimension_unit}{/if}</span></th>
            <th width="7%" class="text-center">Unit Weight<span class="lbl-wgh">{if isset($stock)}<br>{$stock.weight_unit|wunit}{/if}</span></th>
            <th width="5%" class="text-center">Qtty<br>pcs</th>
            <th width="7%" class="text-center">Weight<span class="lbl-wgh">{if isset($stock)}<br>{$stock.weight_unit|wunit}{/if}</span></th>
            <th width="7%" class="text-center">Price<span class="lbl-price">{if isset($stock) && isset($stock.currency_sign) && isset($stock.price_unit)}<br>{$stock.currency_sign}/{$stock.price_unit|wunit}{/if}</span></th>
            <th width="7%" class="text-center">Value<span class="lbl-value">{if isset($stock)}<br>{$stock.currency_sign}{/if}</span></th>
            <th width="8%">Delivery Time</th>
			
            <th>Notes</th>
            <th>Internal Notes</th>
            <th width="8%">Biz</th>
            {* <th width="8%">Supplier</th> *}
        </tr>    
        {foreach from=$positions item=row}
        {if $position_id != $row.steelposition_id}
        <tr class="tr-existing-position">
            <td class="text-center"><input type="radio" name="{$prefix}[new_position_id]" value="{$row.steelposition_id}"></td>
            <td>{$row.steelposition_id|escape:'html'}</td>
            <td>{$row.steelposition.steelgrade.title|escape:'html'}</td>
            <td class="text-center">{$row.steelposition.thickness|escape:'html'}</td>
            <td class="text-center">{$row.steelposition.width|escape:'html'}</td>
            <td class="text-center">{$row.steelposition.length|escape:'html'}</td>
            <td class="text-center">
                {if $stock.weight_unit == 'lb'}
                    {$row.steelposition.unitweight|escape:'html'|string_format:'%d'}
                {else}
                    {$row.steelposition.unitweight|escape:'html'|string_format:'%.2f'}
                {/if}
            </td>
            <td class="text-center" id="position-{$row.steelposition_id}-qtty">{$row.steelposition.qtty|escape:'html'|string_format:'%d'}</td>
            <td class="text-center" id="position-{$row.steelposition_id}-weight">
                {if $stock.weight_unit == 'lb'}
                    {$row.steelposition.weight|escape:'html'|string_format:'%d'}
                {else}
                    {$row.steelposition.weight|escape:'html'|string_format:'%.2f'}
                {/if}
            </td>
            <td class="text-center">{$row.steelposition.price|escape:'html'|string_format:'%.2f'}</td>
            <td class="text-center" id="position-{$row.steelposition_id}-value">{$row.steelposition.value|escape:'html'|string_format:'%.2f'}</td>
            <td>{if isset($row.steelposition.deliverytime)}{$row.steelposition.deliverytime.title|escape:'html'}{/if}</td>
            <td>{$row.steelposition.notes|escape:'html'}</td>
            <td>{$row.steelposition.internal_notes|escape:'html'}</td>
            <td>{$row.steelposition.biz.number_output}</td>
        </tr>
        {/if}
        {/foreach}
        <tr id="position-params-1" data-price_unit="{$stock.price_unit}" data-weight_unit="{$stock.weight_unit}">
            <td class="text-center"><input type="radio" id="new-position" name="{$prefix}[new_position_id]" value="0" checked="checked"></td>
            <td>new</td>
            <td>
                <select id="steelgrade-1" name="new_position[steelgrade_id]" class="max">
                    <option value="0">--</option>
                    {foreach from=$steelgrades item=row}
                    <option value="{$row.steelgrade.id}"{if isset($steelgrade_id) && $steelgrade_id == $row.steelgrade.id} selected="selected"{/if}>{$row.steelgrade.title|escape:'html'}</option>
                    {/foreach}                    
                </select>
            </td>
            <td><input type="text" id="thickness-1" name="new_position[thickness]" class="max" onkeyup="calc_unitweight(1); calc_weight(1); calc_value(1); calc_total();" value="{if isset($thickness) && $thickness > 0}{$thickness}{/if}"></td>
            <td><input type="text" id="width-1" name="new_position[width]" class="max" onkeyup="calc_unitweight(1); calc_weight(1); calc_value(1); calc_total();" value="{if isset($width) && $width > 0}{$width}{/if}"></td>
            <td><input type="text" id="length-1" name="new_position[length]" class="max" onkeyup="calc_unitweight(1); calc_weight(1); calc_value(1); calc_total();" value="{if isset($length) && $length > 0}{$length}{/if}"></td>
            <td><input type="text" id="unitweight-1" name="new_position[unitweight]" class="max" onkeyup="calc_weight(1); calc_value(1); calc_total();" value="{if isset($unitweight) && $unitweight > 0}{$unitweight}{/if}"></td>
            <td class="text-center">{$items_count}<input type="hidden" id="qtty-1" name="new_position[qtty]" value="{$items_count}" class="max" onkeyup="calc_weight(1); calc_value(1); calc_total();"></td>
            <td><input type="text" id="weight-1" name="new_position[weight]" class="max" onkeyup="calc_value(1); calc_total();" value="{if isset($weight) && $weight > 0}{$weight}{/if}"></td>
            <td><input type="text" id="price-1"  name="new_position[price]" class="max" onkeyup="calc_value(1); calc_total();" value="{if isset($price) && $price > 0}{$price}{/if}"></td>
            <td><input type="text" id="value-1"  name="new_position[value]" class="max" value="{if isset($value) && $value > 0}{$value}{/if}"></td>
            <td><input type="text" name="new_position[delivery_time]" class="max" value="{if isset($delivery_time) && !empty($delivery_time)}{$delivery_time|escape:'html'}{/if}"></td>
            <td><input type="text" name="new_position[notes]" class="max" value="{if isset($notes) && !empty($notes)}{$notes|escape:'html'}{/if}"></td>
            <td><input type="text" name="new_position[internal_notes]" class="max" value="{if isset($internal_notes) && !empty($internal_notes)}{$internal_notes|escape:'html'}{/if}"></td>
            <td>
                <input type="text" id="new_position_biz" name="new_position[biz_title]" class="biz-autocomplete max" value="{if isset($biz_title) && !empty($biz_title)}{$biz_title|escape:'html'}{/if}">
                <input type="hidden" id="new_position_biz-id" name="new_position[biz_id]" value="{if isset($biz_id) && $biz_id > 0}{$biz_id}{/if}">
            </td>
        </tr>        
    </tbody>
</table>