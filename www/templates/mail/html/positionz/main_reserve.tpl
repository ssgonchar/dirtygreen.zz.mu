<input type="hidden" id="dimension_unit" value="{if isset($stock)}{$stock.dimension_unit}{/if}">
<input type="hidden" id="weight_unit" value="{if isset($stock)}{$stock.weight_unit}{/if}">
<input type="hidden" id="price_unit" value="{if isset($stock)}{$stock.price_unit}{/if}">
<table class="list" width="100%">
    <tbody>
        <tr class="top-table">
            <th><input type="checkbox" onchange="check_all(this); calc_total();" checked="checked"></th>
            <th>Id</th>
            <th width="8%">Steel Grade</th>
            <th>Thickness<span class="lbl-dim">{if isset($stock)},<br>{$stock.dimension_unit}{/if}</span></th>
            <th>Width<span class="lbl-dim">{if isset($stock)},<br>{$stock.dimension_unit}{/if}</span></th>
            <th>Length<span class="lbl-dim">{if isset($stock)},<br>{$stock.dimension_unit}{/if}</span></th>
            <th>Unit Weight<span class="lbl-wgh">{if isset($stock)},<br>{$stock.weight_unit|wunit}{/if}</span></th>
            <th class="text-center" width="5%">Otty,<br>pcs</th>
            <th>Weight<span class="lbl-wgh">{if isset($stock)},<br>{$stock.weight_unit|wunit}{/if}</span></th>
            <th>Price<span class="lbl-price">{if isset($stock)},<br>{$stock.currency_sign}/{$stock.price_unit|wunit}{/if}</span></th>
            <th>Value<span class="lbl-value">{if isset($stock)},<br>{$stock.currency_sign}{/if}</span></th>
            <th width="10%">Delivery Time</th>
            <th width="10%">Notes</th>
            <th width="10%">Internal Notes</th>
            <th width="10%">Biz</th>
        </tr>
        {foreach from=$list item=position}
        <tr id="position-params-{$position.steelposition_id}" data-price_unit="{$position.steelposition.price_unit}" data-weight_unit="{$position.steelposition.weight_unit}">
            <td><input type="checkbox" name="position_id[{$position.steelposition_id}]" id="cb-position-{$position.steelposition_id}" value="{$position.steelposition_id}" class="cb-row" onchange="calc_total();" checked="checked"></td>
            <td><a href="/position/edit/{$position.steelposition_id}">{$position.steelposition_id}</a></td>
            <td>{$position.steelposition.steelgrade.title|escape:'html'}</td>
            <td>{$position.steelposition.thickness|escape:'html'}</td>
            <td>{$position.steelposition.width|escape:'html'}</td>
            <td>{$position.steelposition.length|escape:'html'}</td>
            <td>{$position.steelposition.unitweight|escape:'html'|string_format:'%.2f'}<input type="hidden" value="{$position.steelposition.unitweight|escape:'html'|string_format:'%.2f'}" id="unitweight-{$position.steelposition_id}" name="unitweight[{$position.steelposition_id}]" class="max" onkeyup="calc_weight({$position.steelposition_id}); calc_value({$position.steelposition_id}); calc_total();"></td>
            <td class="text-center"><input type="text" value="{$position.steelposition.qtty|escape:'html'|string_format:'%d'}" id="qtty-{$position.steelposition_id}" name="qtty[{$position.steelposition_id}]" class="max" onkeyup="calc_weight({$position.steelposition_id}); calc_value({$position.steelposition_id}); calc_total();" style="text-align: center;"></td>
            <td>{$position.steelposition.weight|escape:'html'|string_format:'%.2f'}<input type="hidden" value="{$position.steelposition.weight|escape:'html'|string_format:'%.2f'}" id="weight-{$position.steelposition_id}" name="weight[{$position.steelposition_id}]" class="max" onkeyup="calc_value({$position.steelposition_id}); calc_total();"></td>
            <td>{$position.steelposition.price|escape:'html'|string_format:'%.2f'}<input type="hidden" value="{$position.steelposition.price|escape:'html'|string_format:'%.2f'}" id="price-{$position.steelposition_id}"  name="price[{$position.steelposition_id}]" class="max" onkeyup="calc_value({$position.steelposition_id}); calc_total();"></td>
            <td>{$position.steelposition.value|escape:'html'|string_format:'%.2f'}<input type="hidden" value="{$position.steelposition.value|escape:'html'|string_format:'%.2f'}" id="value-{$position.steelposition_id}"  name="value[{$position.steelposition_id}]" class="max"></td>
            <td>{if isset($position.steelposition.deliverytime)}{$position.steelposition.deliverytime.title|escape:'html'}{/if}</td>
            <td>{$position.steelposition.notes|escape:'html'}</td>
            <td>{$position.steelposition.internal_notes|escape:'html'}</td>
            <td>{if isset($position.steelposition.biz)}<a href="/biz/view/{$position.steelposition.biz_id}">{$position.steelposition.biz.number_output|escape:'html'}</a>{else}{''|undef}{/if}</td>
        </tr>
        {/foreach}
    </tbody>    
</table>
<div class="pad"></div>

<h3>Reserve selected positions for</h3>
<table class="form">
    <tr>
        <td width="95px" class="text-right" style="font-weight: bold;">Company :</td>
        <td>
            <input type="text" id="company_title" class="normal">
            <input type="hidden" id="company_id" name="form[company_id]">
        </td>
    </tr>
    <tr>
        <td class="text-right">Person :</td>
        <td>
            <select id="persons" name="form[person_id]" class="normal">
                <option value="0">--</option>
            </select>
        </td>    
    </tr>
    <tr>        
        <td width="95px" class="text-right">Period :</td>
        <td><input type="text" name="form[period]" class="narrow" value="24"> h.</td>
    </tr>
</table>
