{if empty($items)}
Nothing was found
{else}
{$first_index=key($items)}
<h3>Items</h3>
<table class="list" style="width: 100%">
    <tr class="top-table" style="height: 25px;">
        <th rowspan="2" style="width: 10px;"><input type="checkbox" class="check-all-items" rel="shid_{if !empty($items[$first_index])}{$items[$first_index].steelitem.stockholder_id}{else}0{/if}" style="margin: 5px;"></th>
        <th rowspan="2" width="9%">Plate id</th>
        <th rowspan="2">Thickness,<br />{if !empty($items[0])}{$items[0].steelitem.dimension_unit|escape:'html'}{/if}</th>
        <th rowspan="2">Width,<br />{if !empty($items[0])}{$items[0].steelitem.dimension_unit|escape:'html'}{/if}</th>
        <th rowspan="2">Length,<br />{if !empty($items[0])}{$items[0].steelitem.dimension_unit|escape:'html'}{/if}</th>
        <th rowspan="2">Weight,<br />{if !empty($items[0])}{$items[0].steelitem.weight_unit|escape:'html'}{/if}</th>
        <th colspan="2" style="border-bottom : 1px solid #B9B9B9;">Incoming DDT</th>
        <th colspan="2" style="border-bottom : 1px solid #B9B9B9;">Outgoing DDT</th>
        <th rowspan="2">Owner</th>
        <th rowspan="2">Status</th>        
    </tr>
    <tr class="top-table" style="height: 25px;">
        <th>Number</th>
        <th>Date</th>
        <th>Number</th>
        <th>Date</th>
    </tr>
    {foreach $items as $item}
    <tr>
        <td><input type="checkbox" name="selected_ids[]" class="steelitem-id" rel="shid_{$item.steelitem.stockholder_id}" value="{$item.steelitem.id}" style="margin: 5px;"></td>
        <td onclick="show_item_context(event, {$item.steelitem.id});">{if empty($item.steelitem.guid)}{$item.steelitem.doc_no}{else}{$item.steelitem.guid|escape:'html'}{/if}</td>
        <td onclick="show_item_context(event, {$item.steelitem.id});">{if !empty($item.steelitem.thickness)}{$item.steelitem.thickness|escape:'html'}{/if}</td>
        <td onclick="show_item_context(event, {$item.steelitem.id});">{if !empty($item.steelitem.width)}{$item.steelitem.width|escape:'html'}{/if}</td>
        <td onclick="show_item_context(event, {$item.steelitem.id});">{if !empty($item.steelitem.length)}{$item.steelitem.length|escape:'html'}{/if}</td>
        <td onclick="show_item_context(event, {$item.steelitem.id});">{if !empty($item.steelitem.unitweight)}{$item.steelitem.unitweight|escape:'html'|string_format:'%.2f'}{/if}</td>
        <td onclick="show_item_context(event, {$item.steelitem.id});">{if !empty($item.steelitem.in_ddt_number)}{$item.steelitem.in_ddt_number|escape:'html'}{/if}</td>
        <td onclick="show_item_context(event, {$item.steelitem.id});">{if !empty($item.steelitem.in_ddt_date)}{$item.steelitem.in_ddt_date|escape:'html'|date_format:'d/m/Y'}{/if}</td>
        <td onclick="show_item_context(event, {$item.steelitem.id});">{if !empty($item.steelitem.ddt_number)}{$item.steelitem.ddt_number|escape:'html'}{/if}</td>
        <td onclick="show_item_context(event, {$item.steelitem.id});">{if !empty($item.steelitem.ddt_date)}{$item.steelitem.ddt_date|escape:'html'|date_format:'d/m/Y'}{/if}</td>
        <td onclick="show_item_context(event, {$item.steelitem.id});">{if isset($item.steelitem.owner)}{$item.steelitem.owner.title_trade|escape:'html'}{else}{''|undef}{/if}</td>
        <td onclick="show_item_context(event, {$item.steelitem.id});">
            {if empty($item.steelitem.status_id)}{''|undef}
            {else}
                {$item.steelitem.status_title|escape:'html'}
                {if $item.steelitem.order_id > 0}<br><a href="/order/{$item.steelitem.order_id}">{$item.steelitem.order_id|order_doc_no}</a>{/if}
            {/if}
        </td>        
    </tr>
    {/foreach}
</table>
    
<div class="pad"></div>

<h3>Chemical Analysis</h3>
<table id="t-ic" class="list" width="100%">
    <tbody>
        <tr class="top-table">
            <th width="9%" class="text-left">Plate Id</th>
            <th>Heat / Lot</th>
            <th>%C</th>
            <th>%Si</th>
            <th>%Mn</th>
            <th>%P</th>
            <th>%S</th>
            <th>%Cr</th>
            <th>%Ni</th>
            <th>%Cu</th>
            <th>%Al</th>
            <th>%Mo</th>
            <th>%Nb</th>
            <th>%V</th>
            <th>%N</th>
            <th>%Ti</th>
            <th>%Sn</th>
            <th>%B</th>
            <th>CEQ</th>
        </tr>

        {foreach $items as $row}
        <tr>
            <td class="text-left">{if !empty($row.steelitem.guid)}{$row.steelitem.guid|escape:'html'}{/if}</td>
            <td>{if !empty($row.steelitem.properties.heat_lot)}{$row.steelitem.properties.heat_lot|escape:'html'}{/if}</td>
            <td>{if $row.steelitem.properties.c != 0}{$row.steelitem.properties.c|escape:'html'}{/if}</td>
            <td>{if $row.steelitem.properties.si != 0}{$row.steelitem.properties.si|escape:'html'}{/if}</td>
            <td>{if $row.steelitem.properties.mn != 0}{$row.steelitem.properties.mn|escape:'html'}{/if}</td>
            <td>{if $row.steelitem.properties.p != 0}{$row.steelitem.properties.p|escape:'html'}{/if}</td>
            <td>{if $row.steelitem.properties.s != 0}{$row.steelitem.properties.s|escape:'html'}{/if}</td>
            <td>{if $row.steelitem.properties.cr != 0}{$row.steelitem.properties.cr|escape:'html'}{/if}</td>
            <td>{if $row.steelitem.properties.ni != 0}{$row.steelitem.properties.ni|escape:'html'}{/if}</td>
            <td>{if $row.steelitem.properties.cu != 0}{$row.steelitem.properties.cu|escape:'html'}{/if}</td>
            <td>{if $row.steelitem.properties.al != 0}{$row.steelitem.properties.al|escape:'html'}{/if}</td>
            <td>{if $row.steelitem.properties.mo != 0}{$row.steelitem.properties.mo|escape:'html'}{/if}</td>
            <td>{if $row.steelitem.properties.nb != 0}{$row.steelitem.properties.nb|escape:'html'}{/if}</td>
            <td>{if $row.steelitem.properties.v != 0}{$row.steelitem.properties.v|escape:'html'}{/if}</td>
            <td>{if $row.steelitem.properties.n != 0}{$row.steelitem.properties.n|escape:'html'}{/if}</td>
            <td>{if $row.steelitem.properties.ti != 0}{$row.steelitem.properties.ti|escape:'html'}{/if}</td>
            <td>{if $row.steelitem.properties.sn != 0}{$row.steelitem.properties.sn|escape:'html'}{/if}</td>
            <td>{if $row.steelitem.properties.b != 0}{$row.steelitem.properties.b|escape:'html'}{/if}</td>
            <td>{if $row.steelitem.properties.ceq != 0}{$row.steelitem.properties.ceq|escape:'html'}{/if}</td>
        </tr>
        {/foreach}
    </tbody>
</table>
<div class="pad"></div>

<h3>Mechanical Properties</h3>
<table id="t-im" class="list" width="100%">
    <tbody>
        <tr class="top-table" style="height: 25px;">
            <th width="9%" rowspan="2" class="text-left">Plate Id</th>
            <th colspan="4" style="border-bottom : 1px solid #B9B9B9;">Tensile</th>
            <th rowspan="2">{*Reduction Of Area*} Z-test, %</th>
            <th colspan="3" style="border-bottom : 1px solid #B9B9B9;">Impact</th>
            <th rowspan="2">Hardness<br>HD</th>
            <th rowspan="2">UST</th>
            <th rowspan="2">Stress Relieving Temp<br>deg. C</th>
            <th rowspan="2">Heating Rate Per Hour<br>deg. C</th>
            <th rowspan="2">Holding Time<br>Hours</th>
            <th rowspan="2">Cooling Down Rate Per Hour<br>deg. C</th>
            <th rowspan="2">Normalizing Temp<br>deg. C</th>
            <th rowspan="2">Condition</th>
        </tr>
        <tr class="top-table" style="height: 25px;">
            <th>Sample Direction</th>            
            <th>Strength<br>N/mm<sup>2</sup></th>
            <th>Yield Point<br>N/mm<sup>2</sup></th>
            <th>Elongation<br>%</th>
            <th>Sample Direction</th>
            <th>Strength<br>J/cm<sup>2</sup></th>
            <th>Test Temp<br>deg. C</th>
        </tr>
        {foreach $items as $row}
        <tr>
            <td class="text-left">{if !empty($row.steelitem.guid)}{$row.steelitem.guid|escape:'html'}{/if}</td>
            <td>{if !empty($row.steelitem.properties.tensile_sample_direction)}{$row.steelitem.properties.tensile_sample_direction|escape:'html'}{/if}</td>
            <td>{if !empty($row.steelitem.properties.tensile_strength)}{$row.steelitem.properties.tensile_strength|escape:'html'}{/if}</td>
            <td>{if !empty($row.steelitem.properties.yeild_point)}{$row.steelitem.properties.yeild_point|escape:'html'}{/if}</td>
            <td>{if $row.steelitem.properties.elongation != 0}{$row.steelitem.properties.elongation|escape:'html'}{/if}</td>
            <td>{if $row.steelitem.properties.reduction_of_area != 0}{$row.steelitem.properties.reduction_of_area|escape:'html'}{/if}</td>
            <td>{if !empty($row.steelitem.properties.sample_direction)}{$row.steelitem.properties.sample_direction|escape:'html'}{/if}</td>
            <td>{if !empty($row.steelitem.properties.impact_strength)}{$row.steelitem.properties.impact_strength|escape:'html'}{/if}</td>
            <td>{if !empty($row.steelitem.properties.test_temp)}{$row.steelitem.properties.test_temp|escape:'html'}{/if}</td>
            <td>{if !empty($row.steelitem.properties.hardness)}{$row.steelitem.properties.hardness|escape:'html'}{/if}</td>
            <td>{if !empty($row.steelitem.properties.ust)}{$row.steelitem.properties.ust|escape:'html'}{/if}</td>
            <td>{if !empty($row.steelitem.properties.stress_relieving_temp)}{$row.steelitem.properties.stress_relieving_temp|escape:'html'}{/if}</td>
            <td>{if !empty($row.steelitem.properties.heating_rate_per_hour)}{$row.steelitem.properties.heating_rate_per_hour|escape:'html'}{/if}</td>
            <td>{if !empty($row.steelitem.properties.holding_time)}{$row.steelitem.properties.holding_time|escape:'html'}{/if}</td>
            <td>{if !empty($row.steelitem.properties.cooling_down_rate)}{$row.steelitem.properties.cooling_down_rate|escape:'html'}{/if}</td>
            <td>{if !empty($row.steelitem.properties.normalizing_temp)}{$row.steelitem.properties.normalizing_temp|escape:'html'}{/if}</td>
            <td>
                {if $row.steelitem.properties.condition == 'ar'}As Rolled
                {elseif $row.steelitem.properties.condition == 'n'}Normalized
                {elseif $row.steelitem.properties.condition == 'nr'}Normalizing Rolling{/if}
            </td>
        </tr>
        {/foreach}
    </tbody>
</table>
{/if}