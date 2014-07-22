<input type="hidden" id="dimension_unit" value="{if isset($stock)}{$stock.dimension_unit}{/if}">
<input type="hidden" id="weight_unit" value="{if isset($stock)}{$stock.weight_unit}{/if}">
<input type="hidden" id="price_unit" value="{if isset($stock)}{$stock.price_unit}{/if}">
<table class="list" width="100%">
    <tbody>
        <tr class="top-table">
            <th><input type="checkbox" onchange="check_all(this); calc_total();" checked="checked"></th>
            <th>Id</th>
            <th width="8%">Steel Grade</th>
            <th>Thickness<span class="lbl-dim">{if isset($stock)}, {$stock.dimension_unit}{/if}</span></th>
            <th>Width<span class="lbl-dim">{if isset($stock)}, {$stock.dimension_unit}{/if}</span></th>
            <th>Length<span class="lbl-dim">{if isset($stock)}, {$stock.dimension_unit}{/if}</span></th>
            <th>Unit Weight<span class="lbl-wgh">{if isset($stock)}, {$stock.weight_unit|wunit}{/if}</span></th>
            <th class="text-center">Otty, pcs</th>
            <th>Weight<span class="lbl-wgh">{if isset($stock)}, {$stock.weight_unit|wunit}{/if}</span></th>
            <th>Price<span class="lbl-price">{if isset($stock)}, {$stock.currency_sign}/{$stock.price_unit|wunit}{/if}</span></th>
            <th>Value<span class="lbl-value">{if isset($stock)}, {$stock.currency_sign}{/if}</span></th>
            <th width="10%">Delivery Time</th>
            <th width="10%">Notes</th>
            <th width="10%">Internal Notes</th>
            <th>Location</th>
            <th width="10%">Biz</th>
        </tr>
        {foreach from=$list item=position}
        <tr>
        {if $position.steelposition.inuse}
            <td><img src="/img/icons/lock.png" title="{$position.steelposition.inuse_by}" alt="{$position.steelposition.inuse_by}"></td>
            <td><a href="/position/edit/{$position.steelposition_id}">{$position.steelposition_id}</a></td>
            <td>{$position.steelposition.steelgrade.title|escape:'html'}</td>
            <td>{$position.steelposition.thickness|escape:'html'}</td>
            <td>{$position.steelposition.width|escape:'html'}</td>
            <td>{$position.steelposition.length|escape:'html'}</td>
            <td>{$position.steelposition.unitweight|escape:'html'|string_format:'%.2f'}</td>
            <td class="text-center">{$position.steelposition.qtty|escape:'html'|string_format:'%d'}</td>
            <td>{$position.steelposition.weight|escape:'html'|string_format:'%.2f'}</td>
            <td>{$position.steelposition.price|escape:'html'|string_format:'%.2f'}</td>
            <td>{$position.steelposition.value|escape:'html'|string_format:'%.2f'}</td>
            <td>{if isset($position.steelposition.deliverytime)}{$position.steelposition.deliverytime.title|escape:'html'}{/if}</td>
            <td>{$position.steelposition.notes}</td>
            <td>{$position.steelposition.internal_notes}</td>
            <td>{if isset($position.steelposition.biz)}<a href="/biz/{$position.steelposition.biz_id}">{$position.steelposition.biz.number_output|escape:'html'}</a>{else}{''|undef}{/if}</td>
        {else}
            <td><input type="checkbox" name="position_id[{$position.steelposition_id}]" id="cb-position-{$position.steelposition_id}" value="{$position.steelposition_id}" class="cb-row" onchange="calc_total();" checked="checked"></td>
            <td><a href="/position/edit/{$position.steelposition_id}">{$position.steelposition_id}</a></td>
            <td>
                <select name=steelgrade_id[{$position.steelposition_id}] class="max">
                    <option value="0">--</option>
                    {foreach from=$steelgrades item=row}
                    <option value="{$row.steelgrade.id}"{if $row.steelgrade.id == $position.steelposition.steelgrade_id} selected="selected"{/if}>{$row.steelgrade.title|escape:'html'}</option>
                    {/foreach}                    
                </select>
            </td>
            <td><input type="text" value="{$position.steelposition.thickness|escape:'html'}" id="thickness-{$position.steelposition_id}" name="thickness[{$position.steelposition_id}]" class="max thickness-input" onkeyup="calc_unitweight({$position.steelposition_id}); calc_weight({$position.steelposition_id}); calc_value({$position.steelposition_id}); calc_total();"></td>
            <td><input type="text" value="{$position.steelposition.width|escape:'html'}" id="width-{$position.steelposition_id}" name="width[{$position.steelposition_id}]" class="max width-input" onkeyup="calc_unitweight({$position.steelposition_id}); calc_weight({$position.steelposition_id}); calc_value({$position.steelposition_id}); calc_total();"></td>
            <td><input type="text" value="{$position.steelposition.length|escape:'html'}" id="length-{$position.steelposition_id}" name="length[{$position.steelposition_id}]" class="max length-input" onkeyup="calc_unitweight({$position.steelposition_id}); calc_weight({$position.steelposition_id}); calc_value({$position.steelposition_id}); calc_total();"></td>
            <td><input type="text" value="{$position.steelposition.unitweight|escape:'html'|string_format:'%.2f'}" id="unitweight-{$position.steelposition_id}" name="unitweight[{$position.steelposition_id}]" class="max unitweight-input" onkeyup="calc_weight({$position.steelposition_id}); calc_value({$position.steelposition_id}); calc_total();"></td>
            <td class="text-center">{$position.steelposition.qtty|escape:'html'|string_format:'%d'}<input type="hidden" value="{$position.steelposition.qtty|escape:'html'|string_format:'%d'}" id="qtty-{$position.steelposition_id}" name="qtty[{$position.steelposition_id}]" class="max qtty-input" onkeyup="calc_weight({$position.steelposition_id}); calc_value({$position.steelposition_id}); calc_total();"></td>
            <td><input type="text" value="{$position.steelposition.weight|escape:'html'|string_format:'%.2f'}" id="weight-{$position.steelposition_id}" name="weight[{$position.steelposition_id}]" class="max weight-input" onkeyup="calc_value({$position.steelposition_id}); calc_total();"></td>
            <td><input type="text" value="{$position.steelposition.price|escape:'html'|string_format:'%.2f'}" id="price-{$position.steelposition_id}"  name="price[{$position.steelposition_id}]" class="max" onkeyup="calc_value({$position.steelposition_id}); calc_total();"></td>
            <td><input type="text" value="{$position.steelposition.value|escape:'html'|string_format:'%.2f'}" id="value-{$position.steelposition_id}"  name="value[{$position.steelposition_id}]" class="max"></td>
            <td><input type="text" value="{if isset($position.steelposition.deliverytime)}{$position.steelposition.deliverytime.title|escape:'html'}{/if}" name="delivery_time[{$position.steelposition_id}]" class="max"></td>
            <td><input type="text" value="{$position.steelposition.notes|escape:'html'}" name="notes[{$position.steelposition_id}]" class="max"></td>
            <td><input type="text" value="{$position.steelposition.internal_notes|escape:'html'}" name="internal_notes[{$position.steelposition_id}]" class="max"></td>
            <td>
                {if isset($position.steelposition.quick)}
                    <div>{$position.steelposition.quick.locations|escape:'html'}</div>
                {/if}
                {if !empty($position.steelposition.quick.int_locations) && $position.steelposition.quick.int_locations != $position.steelposition.quick.locations}
                    <div style="font-size: 10px; color: #555;">{$position.steelposition.quick.int_locations}</div>
                {/if}
            </td>
            <td>
                {if isset($position.steelposition.biz)}<a href="/biz/{$position.steelposition.biz_id}">{$position.steelposition.biz.number_output|escape:'html'}</a>{else}{''|undef}{/if}
                {*
                <select name=biz_id[{$position.steelposition_id}] class="max">
                    <option value="0">--</option>
                    {foreach from=$bizes item=row}
                    <option value="{$row.biz_id}"{if $row.biz_id == $position.steelposition.biz_id} selected="selected"{/if}>{$row.biz.number_output|escape:'html'} - {$row.biz.title|escape:'html'}</option>
                    {/foreach}                    
                </select>
                *}
            </td>
        {/if}        
        </tr>
        {/foreach}
    </tbody>    
</table>
<div class="pad"></div>

<h3>Update selected positions with</h3>
<table class="form">
    <tr>
        <td width="95px" class="text-right">Price, {if isset($stock)}{$stock.currency_sign}/{$stock.price_unit|wunit}{/if} :</td>
        <td><input type="text" name="form[price]" class="normal"></td>
    </tr>
    <tr>
        <td class="text-right">Delivery Time :</td>
        <td><input type="text" name="form[delivery_time]" class="normal"></td>    
        <td><label for="delivery_time"><input type="checkbox" id="delivery_time" name="form[clear_delivery_time]" value="1"> Clear Delivery Times</label></td>
    </tr>
    <tr>        
        <td width="95px" class="text-right">Notes :</td>
        <td><input type="text" name="form[notes]" class="normal"></td>
        <td><label for="clear_notes"><input type="checkbox" id="clear_notes" name="form[clear_notes]" value="1"> Clear Notes</label></td>
        {*
        <td width="95px" class="text-right">Biz :</td>
        <td>
            <select name=form[biz_id] class="normal">
                <option value="0">--</option>
                {foreach from=$bizes item=row}
                <option value="{$row.biz_id}">{$row.biz.number_output|escape:'html'} - {$row.biz.title|escape:'html'}</option>
                {/foreach}                    
            </select>            
        </td>
        *}
    </tr>
    <tr>
        <td class="text-right">Internal Notes :</td>
        <td><input type="text" name="form[internal_notes]" class="normal"></td>
        <td><label for="clear_internal_notes"><input type="checkbox" id="clear_internal_notes" name="form[clear_internal_notes]" value="1"> Clear Internal Notes</label></td>
    </tr>
</table>
