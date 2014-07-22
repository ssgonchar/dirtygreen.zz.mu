<table class="form" width="100%">
    <tr>
        <td width="85px" class="text-right" style="font-weight: bold;">Product :</td>
        <td>
            <select name="form[product_id]" class="normal">
                <option value="92" selected="selected">Hot Rolled Steel Plate</option>
            </select>
        </td>
        <td width="85px" class="text-right" style="font-weight: bold;">Stock :</td>
        <td>
            <select name="form[stock_id]" class="normal" onchange="bind_stock_params(this.value, false); show_position_table(this.value);">
                <option value="0"{if !isset($stock)} selected="selected"{/if}>--</option>
                {foreach from=$stocks item=row}
                <option value="{$row.stock.id}"{if isset($stock) && $stock.id == $row.stock.id} selected="selected"{/if}>{$row.stock.title|escape:'html'}</option>
                {/foreach}
            </select>
            <input type="hidden" id="dimension_unit" value="{if isset($stock)}{$stock.dimension_unit}{/if}">
            <input type="hidden" id="weight_unit" value="{if isset($stock)}{$stock.weight_unit}{/if}">
            <input type="hidden" id="price_unit" value="{if isset($stock)}{$stock.price_unit}{/if}">
        </td>
        <td width="85px" class="text-right">Biz :</td>
        <td>
            <input type="text" id="position-biz" name="form[biz_title]" class="normal biz-autocomplete"{if isset($form.biz_title)} value="{$form.biz_title}"{/if}>
            <input type="hidden" id="position-biz-id" name="form[biz_id]" value="{if isset($form.biz_id)}{$form.biz_id}{else}0{/if}">
        </td>            
    </tr>
    <tr>
        <td></td><td></td>
        <td width="85px" class="text-right" style="font-weight: bold;">Location :</td>
        <td>
            <select id="locations" name="form[location_id]" class="normal">
                <option value="0">--</option>
                {foreach from=$locations item=row}
                <option value="{$row.company.id}"{if isset($form.location_id) && $form.location_id == $row.company.id} selected="selected"{/if}>{$row.company.doc_no|escape:'html'} ({$row.company.stocklocation.title|escape:'html'}{if isset($row.company.city)}, {$row.company.city.title}{/if})</option>
                {/foreach}
            </select>
        </td>
    </tr>
</table>
<div class="separator pad"><!-- --></div>

<span id="position-add-text"{if isset($stock)} style="display:none;"{/if}>Please select stock first</span>
<div id="position-add-table"{if !isset($stock)} style="display: none;"{/if}>
    <span>Steel Grade, Thickness, Width, Length, Unit Weight, Qtty, Weight, Price, Value are required .</span>
    <div class="pad-10"></div>
    
    <table class="list" width="100%">
        <tbody>
            <tr class="top-table">
                <th>No</th>
                <th width="8%">Steel Grade</th>
                <th>Thickness<span class="lbl-dim">{if isset($stock)}, {$stock.dimension_unit}{/if}</span></th>
                <th>Width<span class="lbl-dim">{if isset($stock)}, {$stock.dimension_unit}{/if}</span></th>
                <th>Length<span class="lbl-dim">{if isset($stock)}, {$stock.dimension_unit}{/if}</span></th>
                <th>Unit Weight<span class="lbl-wgh">{if isset($stock)}, {$stock.weight_unit|wunit}{/if}</span></th>
                <th>Otty, pcs</th>
                <th>Weight<span class="lbl-wgh">{if isset($stock)}, {$stock.weight_unit}{/if}</span></th>
                <th>Price<span class="lbl-price">{if isset($stock)}, {$stock.currency_sign}/{$stock.price_unit|wunit}{/if}</span></th>
                <th>Value<span class="lbl-value">{if isset($stock)}, {$stock.currency_sign}{/if}</span></th>
                <th width="10%">Delivery Time</th>
                <th width="10%">Notes</th>
                <th width="10%">Internal Notes</th>
            </tr>
            {foreach name='pos' from=$positions item=position}
            <tr>
                <td>
                    {$smarty.foreach.pos.index + 1}
                    <input type="checkbox" class="cb-row" value="{$smarty.foreach.pos.index}" checked="checked" style="display: none; visibility: hidden;">
                </td>
                <td>
                    <select name=positions[{$smarty.foreach.pos.index}][steelgrade_id] class="max">
                        <option value="0">--</option>
                        {foreach from=$steelgrades item=row}
                        <option value="{$row.steelgrade.id}"{if $position.steelgrade_id == $row.steelgrade.id} selected="selected"{/if}>{$row.steelgrade.title|escape:'html'}</option>
                        {/foreach}                    
                    </select>
                </td>
                <td><input type="text" name="positions[{$smarty.foreach.pos.index}][thickness]"{if !empty($position.thickness)} value="{$position.thickness|escape:'html'}"{/if} id="thickness-{$smarty.foreach.pos.index}" class="max" onkeyup="addpos_calc_unitweight({$smarty.foreach.pos.index});"></td>
                <td><input type="text" name="positions[{$smarty.foreach.pos.index}][width]"{if !empty($position.width)} value="{$position.width|escape:'html'}"{/if} id="width-{$smarty.foreach.pos.index}" class="max" onkeyup="addpos_calc_unitweight({$smarty.foreach.pos.index});"></td>
                <td><input type="text" name="positions[{$smarty.foreach.pos.index}][length]"{if !empty($position.length)} value="{$position.length|escape:'html'}"{/if} id="length-{$smarty.foreach.pos.index}" class="max" onkeyup="addpos_calc_unitweight({$smarty.foreach.pos.index});"></td>
                <td><input type="text" name="positions[{$smarty.foreach.pos.index}][unitweight]"{if !empty($position.unitweight)} value="{$position.unitweight|escape:'html'}"{/if} id="unitweight-{$smarty.foreach.pos.index}" class="max" onkeyup="addpos_calc_weight({$smarty.foreach.pos.index});"></td>
                <td><input type="text" name="positions[{$smarty.foreach.pos.index}][qtty]"{if !empty($position.qtty)} value="{$position.qtty|escape:'html'|string_format:'%d'}"{/if} id="qtty-{$smarty.foreach.pos.index}" class="max" onkeyup="addpos_calc_weight({$smarty.foreach.pos.index});"></td>
                <td><input type="text" name="positions[{$smarty.foreach.pos.index}][weight]"{if !empty($position.weight)} value="{$position.weight|escape:'html'|string_format:'%.2f'}"{/if} id="weight-{$smarty.foreach.pos.index}" class="max" onkeyup="addpos_calc_value({$smarty.foreach.pos.index});"></td>
                <td><input type="text" name="positions[{$smarty.foreach.pos.index}][price]"{if !empty($position.price)} value="{$position.price|escape:'html'|string_format:'%.2f'}"{/if} id="price-{$smarty.foreach.pos.index}" class="max" onkeyup="addpos_calc_value({$smarty.foreach.pos.index});"></td>
                <td><input type="text" name="positions[{$smarty.foreach.pos.index}][value]"{if !empty($position.value)} value="{$position.value|escape:'html'|string_format:'%.2f'}"{/if} id="value-{$smarty.foreach.pos.index}" class="max"></td>
                <td><input type="text" name="positions[{$smarty.foreach.pos.index}][delivery_time]"{if !empty($position.delivery_time)} value="{$position.delivery_time|escape:'html'}"{/if} class="max"></td>
                <td><input type="text" name="positions[{$smarty.foreach.pos.index}][notes]"{if !empty($position.notes)} value="{$position.notes|escape:'html'}"{/if} class="max"></td>
                <td><input type="text" name="positions[{$smarty.foreach.pos.index}][internal_notes]"{if !empty($position.internal_notes)} value="{$position.internal_notes|escape:'html'}"{/if} class="max"></td>
            </tr>        
            {/foreach}
        </tbody>    
    </table>
</div>
