<table class="list" width="100%">
    <tbody>
        <tr class="top-table">
            <th width="1%"></th>
            <th width="3%">Id</th>
            <th width="8%">Steel Grade</th>
            <th width="5%" class="text-center">Thickness<span class="lbl-dim">{if isset($dimension_unit)}, {$dimension_unit}{/if}</span></th>
            <th width="5%" class="text-center">Width<span class="lbl-dim">{if isset($dimension_unit)}, {$dimension_unit}{/if}</span></th>
            <th width="5%" class="text-center">Length<span class="lbl-dim">{if isset($dimension_unit)}, {$dimension_unit}{/if}</span></th>
            <th width="7%" class="text-center">Unit Weight<span class="lbl-wgh">{if isset($weight_unit)}, {$weight_unit|wunit}{/if}</span></th>
            <th width="5%" class="text-center">Qtty<br>pcs</th>
            <th width="7%" class="text-center">Weight<span class="lbl-wgh">{if isset($weight_unit)}, {$weight_unit|wunit}{/if}</span></th>
            <th width="7%" class="text-center">Price<span class="lbl-price">{if isset($currency_sign) && isset($weight_unit)}, {$currency_sign}/{$weight_unit|wunit}{/if}</span></th>
            <th width="7%" class="text-center">Value<span class="lbl-value">{if isset($currency_sign)}, {$currency_sign}{/if}</span></th>
            <th width="8%">Delivery Time</th>
            <th>Notes</th>
            <th>Internal Notes</th>
            <th width="8%">Biz</th>
{* 20120722, zharkov отключено            <th width="8%">Supplier</th>  *}
        </tr>    
        {foreach from=$positions item=row}
        <tr class="tr-existing-position">
            <td class="text-center"><input type="radio" name="{$prefix}[{$index}][position_id]" value="{$row.steelposition_id}"{if isset($position_id) && $position_id == $row.steelposition_id} checked="checked"{/if}></td>
            <td><a href="/position/edit/{$row.steelposition_id}">{$row.steelposition_id|escape:'html'}</a></td>
            <td>{$row.steelposition.steelgrade.title|escape:'html'}</td>
            <td class="text-center">{$row.steelposition.thickness|escape:'html'}</td>
            <td class="text-center">{$row.steelposition.width|escape:'html'}</td>
            <td class="text-center">{$row.steelposition.length|escape:'html'}</td>
            <td class="text-center">{$row.steelposition.unitweight|escape:'html'|string_format:'%.2f'}</td>
            <td class="text-center" id="position-{$row.steelposition_id}-qtty">{$row.steelposition.qtty|escape:'html'|string_format:'%d'}</td>
            <td class="text-center" id="position-{$row.steelposition_id}-weight">{$row.steelposition.weight|escape:'html'|string_format:'%.2f'}</td>
            <td class="text-center">{$row.steelposition.price|escape:'html'|string_format:'%.2f'}</td>
            <td class="text-center" id="position-{$row.steelposition_id}-value">{$row.steelposition.value|escape:'html'|string_format:'%.2f'}</td>
            <td>{if isset($row.steelposition.deliverytime)}{$row.steelposition.deliverytime.title|escape:'html'}{/if}</td>
            <td>{$row.steelposition.notes|escape:'html'}</td>
            <td>{$row.steelposition.internal_notes|escape:'html'}</td>
            <td>{$row.steelposition.biz.number_output}</td>
        </tr>
        {/foreach}
        <tr>
            <td class="text-center"><input type="radio" id="{$prefix}-{$index}-poaition_id" name="{$prefix}[{$index}][position_id]" value="0"{if !isset($position_id) || empty($position_id)} checked="checked"{/if}></td>
            <td>new</td>
            <td>
                <select id="steelgrade-{$index}" name="{$prefix}[{$index}][new_position][steelgrade_id]" class="max">
                    <option value="0">--</option>
                    {foreach from=$steelgrades item=row}
                    <option value="{$row.steelgrade.id}"{if isset($new_position) && $new_position.steelgrade_id == $row.steelgrade.id} selected="selected"{/if}>{$row.steelgrade.title|escape:'html'}</option>
                    {/foreach}                    
                </select>
            </td>
            <td><input type="text" id="thickness-{$index}" name="{$prefix}[{$index}][new_position][thickness]"{if isset($new_position)} value="{$new_position.thickness|escape:'html'}"{/if} class="max" onkeyup="calc_unitweight({$index}, '{$dimension_unit}', '{$weight_unit}'); calc_weight({$index}); calc_value({$index}); calc_total();"></td>
            <td><input type="text" id="width-{$index}" name="{$prefix}[{$index}][new_position][width]"{if isset($new_position)} value="{$new_position.width|escape:'html'}"{/if} class="max" onkeyup="calc_unitweight({$index}, '{$dimension_unit}', '{$weight_unit}'); calc_weight({$index}); calc_value({$index}); calc_total();"></td>
            <td><input type="text" id="length-{$index}" name="{$prefix}[{$index}][new_position][length]"{if isset($new_position)} value="{$new_position.length|escape:'html'}"{/if} class="max" onkeyup="calc_unitweight({$index}, '{$dimension_unit}', '{$weight_unit}'); calc_weight({$index}); calc_value({$index}); calc_total();"></td>
            <td><input type="text" id="unitweight-{$index}" name="{$prefix}[{$index}][new_position][unitweight]"{if isset($new_position)} value="{$new_position.unitweight|string_format:'%1.2f'|escape:'html'}"{/if} class="max" onkeyup="calc_weight({$index}); calc_value({$index}); calc_total();"></td>
            <td class="text-center">{$new_qtty}<input type="hidden" id="qtty-{$index}" name="{$prefix}[{$index}][new_position][qtty]" value="{$new_qtty}" class="max" onkeyup="calc_weight({$index}); calc_value({$index}); calc_total();"></td>
            <td><input type="text" id="weight-{$index}" name="{$prefix}[{$index}][new_position][weight]"{if isset($new_position)} value="{$new_position.weight|string_format:'%1.2f'|escape:'html'}"{/if} class="max" onkeyup="calc_value({$index}); calc_total();"></td>
            <td><input type="text" id="price-{$index}"  name="{$prefix}[{$index}][new_position][price]"{if isset($new_position)} value="{$new_position.price|escape:'html'}"{/if} class="max" onkeyup="calc_value({$index}); calc_total();"></td>
            <td><input type="text" id="value-{$index}"  name="{$prefix}[{$index}][new_position][value]"{if isset($new_position)} value="{$new_position.value|escape:'html'}"{/if} class="max"></td>
            <td><input type="text" name="{$prefix}[{$index}][new_position][delivery_time]"{if isset($new_position)} value="{$new_position.delivery_time|escape:'html'}"{/if} class="max"></td>
            <td><input type="text" name="{$prefix}[{$index}][new_position][notes]"{if isset($new_position)} value="{$new_position.notes|escape:'html'}"{/if} class="max"></td>
            <td><input type="text" name="{$prefix}[{$index}][new_position][internal_notes]"{if isset($new_position)} value="{$new_position.internal_notes|escape:'html'}"{/if} class="max"></td>
            <td>
                <input type="text" id="{$prefix}-{$index}-new_position-biz" name="{$prefix}[{$index}][new_position][biz_title]" class="biz-autocomplete max" value="">
                <input type="hidden" id="{$prefix}-{$index}-new_position-biz-id" name="{$prefix}[{$index}][new_position][biz_id]" value="">
            </td>
        </tr>        
    </tbody>
</table>