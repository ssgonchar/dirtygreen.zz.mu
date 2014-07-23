<h3>Position</h3>
<input type="hidden" id="dimension_unit" value="{$position.steelposition.dimension_unit}">
<input type="hidden" id="weight_unit" value="{$position.steelposition.weight_unit}">
<input type="hidden" id="price_unit" value="{$position.steelposition.price_unit}">
<input type="hidden" id="items_count" value="{$items_count}">
<table class="list" width="100%">
    <tbody>
        <tr class="top-table" style="cursor: text;">
            <th width="2%">Id</th>
            <th width="8%">Steel Grade</th>
            <th width="5%">Thickness<br>{$position.steelposition.dimension_unit}</th>
            <th width="5%">Width<br>{$position.steelposition.dimension_unit}</th>
            <th width="5%">Length<br>{$position.steelposition.dimension_unit}</th>
            <th width="7%">Unit Weight<br>{$position.steelposition.weight_unit|wunit}</th>
            <th width="5%">Qtty<br>pcs</th>
            <th width="7%">Weight<br>{$position.steelposition.weight_unit|wunit}</th>
            <th width="7%">Price<br>{$position.steelposition.currency_sign}/{$position.steelposition.price_unit|wunit}</th>
            <th width="7%">Value<br>{$position.steelposition.currency_sign}</th>
            <th width="8%">Delivery Time</th>
            <th>Notes</th>
            <th>Internal Notes</th>
            <th width="8%">Biz</th>
        </tr>    
        <tr style="cursor: text;">
        {if $position.steelposition.inuse}
            <td><img src="/img/icons/lock.png" title="In use by {$position.steelposition.inuse_by}" alt="In use by {$position.steelposition.inuse_by}">&nbsp;{$position.steelposition_id|escape:'html'}</td>
            <td>{$position.steelposition.steelgrade.title|escape:'html'}</td>
            <td>{if !empty($position.steelposition.thickness)}{$position.steelposition.thickness|escape:'html'}{/if}</td>
            <td>{if !empty($position.steelposition.width)}{$position.steelposition.width|escape:'html'}{/if}</td>
            <td>{if !empty($position.steelposition.length)}{$position.steelposition.length|escape:'html'}{/if}</td>
            <td>{if !empty($position.steelposition.unitweight)}{$position.steelposition.unitweight|escape:'html'|string_format:'%.2f'}{/if}</td>
            <td><input type="hidden" id="qtty-1" name="position[qtty]" value="{if !empty($position.steelposition.qtty)}{$position.steelposition.qtty|escape:'html'|string_format:'%d'}{else}0{/if}"><span id="lbl-qtty-1">{if !empty($position.steelposition.qtty)}{$position.steelposition.qtty|escape:'html'|string_format:'%d'}{else}0{/if}</span></td>
            <td>{if !empty($position.steelposition.weight)}{$position.steelposition.weight|escape:'html'|string_format:'%.2f'}{/if}</td>
            <td>{if !empty($position.steelposition.price)}{$position.steelposition.price|escape:'html'|string_format:'%.2f'}{/if}</td>
            <td>{if !empty($position.steelposition.value)}{$position.steelposition.value|escape:'html'|string_format:'%.2f'}{/if}</td>
            <td>{if !empty($position.steelposition.deliverytime)}{$position.steelposition.deliverytime.title|escape:'html'}{/if}</td>
            <td>{if !empty($position.steelposition.notes)}{$position.steelposition.notes|escape:'html'}{/if}</td>
            <td>{if !empty($position.steelposition.internal_notes)}{$position.steelposition.internal_notes|escape:'html'}{/if}</td>
            <td>{$position.steelposition.biz.number_output|escape:'html'}</td>        
        {else}
            <td>{$position.steelposition_id|escape:'html'}</td>
            <td>
                <select name="position[steelgrade_id]" style="width: 100%;">
                    <option value="0">--</option>
                    {foreach from=$steelgrades item=row}
                    <option value="{$row.steelgrade.id}"{if $position.steelposition.steelgrade_id == $row.steelgrade.id} selected="selected"{/if}>{$row.steelgrade.title|escape:'html'}</option>
                    {/foreach}                    
                </select>
            </td>
            <td><input type="text" id="thickness-1" name="position[thickness]" value="{if !empty($position.steelposition.thickness)}{$position.steelposition.thickness|escape:'html'}{/if}" style="width: 100%;" onkeyup="calc_unitweight(1); calc_weight(1); calc_value(1); calc_total();"></td>
            <td><input type="text" id="width-1" name="position[width]" value="{if !empty($position.steelposition.width)}{$position.steelposition.width|escape:'html'}{/if}" style="width: 100%;" onkeyup="calc_unitweight(1); calc_weight(1); calc_value(1); calc_total();"></td>
            <td><input type="text" id="length-1" name="position[length]" value="{if !empty($position.steelposition.length)}{$position.steelposition.length|escape:'html'}{/if}" style="width: 100%;" onkeyup="calc_unitweight(1); calc_weight(1); calc_value(1); calc_total();"></td>
            <td><input type="text" id="unitweight-1" name="position[unitweight]" value="{if !empty($position.steelposition.unitweight)}{$position.steelposition.unitweight|escape:'html'|string_format:'%.2f'}{/if}" style="width: 100%;" onkeyup="calc_weight(1); calc_value(1); calc_total();"></td>
            <td><input type="hidden" id="qtty-1" name="position[qtty]" value="{if !empty($position.steelposition.qtty)}{$position.steelposition.qtty|escape:'html'|string_format:'%d'}{else}0{/if}"><span id="lbl-qtty-1">{if !empty($position.steelposition.qtty)}{$position.steelposition.qtty|escape:'html'|string_format:'%d'}{else}0{/if}</span></td>
            <td><input type="text" id="weight-1" name="position[weight]" value="{if !empty($position.steelposition.weight)}{$position.steelposition.weight|escape:'html'|string_format:'%.2f'}{/if}" style="width: 100%;" onkeyup="calc_value(1); calc_total();"></td>
            <td><input type="text" id="price-1"  name="position[price]" value="{if !empty($position.steelposition.price)}{$position.steelposition.price|escape:'html'|string_format:'%.2f'}{/if}" style="width: 100%;" onkeyup="calc_value(1); calc_total();"></td>
            <td><input type="text" id="value-1"  name="position[value]" value="{if !empty($position.steelposition.value)}{$position.steelposition.value|escape:'html'|string_format:'%.2f'}{/if}" style="width: 100%;"></td>
            <td><input type="text" name="position[delivery_time]" value="{if !empty($position.steelposition.deliverytime)}{$position.steelposition.deliverytime.title|escape:'html'}{/if}" style="width: 100%;"></td>
            <td><input type="text" name="position[notes]" value="{if !empty($position.steelposition.notes)}{$position.steelposition.notes|escape:'html'}{/if}" class="max"></td>
            <td><input type="text" name="position[internal_notes]" value="{if !empty($position.steelposition.internal_notes)}{$position.steelposition.internal_notes|escape:'html'}{/if}" class="max"></td>
            <td>
                <input type="text" id="position-biz" name="position[biz_title]" class="biz-autocomplete-alt"{if isset($position_biz)} value="{$position_biz.doc_no}"{/if} style="width: 100%;">
                <input type="hidden" id="position-biz-id" name="position[biz_id]" value="{if isset($position_biz)}{$position_biz.id}{else}0{/if}">
            </td>
        {/if}
        </tr>        
</table>
<div class="pad"><!-- --></div>

<h3>Items</h3>
{include 
    file="templates/controls/steelitems_edit.tpl" 
    subclass='alt1' 
    eternal=$items_eternal 
    dimension_unit=$position.steelposition.dimension_unit
    weight_unit=$position.steelposition.weight_unit
    price_unit=$position.steelposition.price_unit
    currency=$position.steelposition.currency
    include_nominal=$include_nominal}
