{if $item[0].tech_action == 'add'}Created
{elseif $item[0].tech_action == 'edit'}Modified
{/if} {$item[0].record_at|date_human:false} by {$item[0].user.login}
<div class="pad1"></div>

<table id="t-i" class="list" width="100%">
    <tbody>
        <tr class="top-table" style="height: 25px;">
            <th width="10%" rowspan="2">Plate Id</th>
            <th width="8%" rowspan="2">Steel Grade</th>
            <th width="7%" rowspan="2">Thickness,<br>{$item[0].dimension_unit}</th>
            <th width="7%" rowspan="2">Width,<br>{$item[0].dimension_unit}</th>
            <th width="7%" rowspan="2">Length,<br>{$item[0].dimension_unit}</th>
            <th width="7%" rowspan="2">Weight,<br>{$item[0].weight_unit|wunit}</th>
            <th width="7%" colspan="4" style="border-bottom : 1px solid #B9B9B9;">Measured</th>
            <th width="7%" colspan="2" style="border-bottom : 1px solid #B9B9B9;">Transport</th>
            <th width="7%" rowspan="2">Weighed Weight,<br>{$item[0].weight_unit|wunit}</th>
            <th width="5%" rowspan="2">Is Virtual</th>
            <th rowspan="2">Location</th>
        </tr>
        <tr class="top-table" style="height: 25px;">
            <th width="7%">Thickness,<br>{$item[0].dimension_unit}</th>
            <th width="7%">Width,<br>{$item[0].dimension_unit}</th>
            <th width="7%">Length,<br>{$item[0].dimension_unit}</th>
            <th width="7%">Weight,<br>{$item[0].weight_unit|wunit}</th>                        
            <th width="7%">Width,<br>{$item[0].dimension_unit}</th>
            <th width="7%">Length,<br>{$item[0].dimension_unit}</th>
        </tr>
        <tr id="t-i-1">
            <td{if isset($item[1]) && $item[0].guid != $item[1].guid} style="background-color: #f4c430;"{/if}>{if !empty($item[0].guid)}{$item[0].guid|escape:'html'}{/if}</td>
            <td{if isset($item[1]) && $item[0].steelgrade.title != $item[1].steelgrade.title} style="background-color: #f4c430;"{/if}>{$item[0].steelgrade.title|escape:'html'}</td>
            <td{if isset($item[1]) && $item[0].thickness != $item[1].thickness} style="background-color: #f4c430;"{/if}>{if !empty($item[0].thickness)}{$item[0].thickness|escape:'html'}{/if}</td>
            <td{if isset($item[1]) && $item[0].width != $item[1].width} style="background-color: #f4c430;"{/if}>{if !empty($item[0].width)}{$item[0].width|escape:'html'}{/if}</td>
            <td{if isset($item[1]) && $item[0].length != $item[1].length} style="background-color: #f4c430;"{/if}>{if !empty($item[0].length)}{$item[0].length|escape:'html'}{/if}</td>
            <td{if isset($item[1]) && $item[0].unitweight != $item[1].unitweight} style="background-color: #f4c430;"{/if}>{if !empty($item[0].unitweight)}{$item[0].unitweight|escape:'html'|string_format:'%.2f'}{/if}</td>
            <td{if isset($item[1]) && $item[0].thickness_measured != $item[1].thickness_measured} style="background-color: #f4c430;"{/if}>{if !empty($item[0].thickness_measured)}{$item[0].thickness_measured|escape:'html'}{/if}</td>
            <td{if isset($item[1]) && $item[0].width_measured != $item[1].width_measured} style="background-color: #f4c430;"{/if}>{if !empty($item[0].width_measured)}{$item[0].width_measured|escape:'html'}{/if}</td>
            <td{if isset($item[1]) && $item[0].length_measured != $item[1].length_measured} style="background-color: #f4c430;"{/if}>{if !empty($item[0].length_measured)}{$item[0].length_measured|escape:'html'}{/if}</td>
            <td{if isset($item[1]) && $item[0].unitweight_measured != $item[1].unitweight_measured} style="background-color: #f4c430;"{/if}>{if $item[0].unitweight_measured > 0}{$item[0].unitweight_measured|escape:'html'}{/if}</td>
            <td{if isset($item[1]) && $item[0].width_max != $item[1].width_max} style="background-color: #f4c430;"{/if}>{if !empty($item[0].width_max)}{$item[0].width_max|escape:'html'}{/if}</td>
            <td{if isset($item[1]) && $item[0].length_max != $item[1].length_max} style="background-color: #f4c430;"{/if}>{if !empty($item[0].length_max)}{$item[0].length_max|escape:'html'}{/if}</td>
            <td{if isset($item[1]) && $item[0].unitweight_weighed != $item[1].unitweight_weighed} style="background-color: #f4c430;"{/if}>{if $item[0].unitweight_weighed > 0}{$item[0].unitweight_weighed|escape:'html'}{/if}</td>
            <td class="text-center"{if isset($item[1]) && $item[0].is_virtual != $item[1].is_virtual} style="background-color: #f4c430;"{/if}>{if !empty($item[0].is_virtual)}yes{else}no{/if}</td>
            <td{if isset($item[1]) && $item[0].stockholder_id != $item[1].stockholder_id} style="background-color: #f4c430;"{/if}>{if isset($item[0].stockholder)}{$item[0].stockholder.title|escape:'html'}{/if}</td>
        </tr>        
    </tbody>    
</table>
<div class="pad"></div>

<h4>Status</h4>
<table id="t-is" class="list" width="100%">
    <tbody>
        <tr class="top-table" style="height: 25px;">
            <th rowspan="2">Producer</th>
            {*
            <th rowspan="2">Mill</th>
            <th rowspan="2">System</th>
            *}
            <th colspan="2" style="border-bottom : 1px solid #B9B9B9;">Supplier Invoice</th>
            <th rowspan="2" width="7%">Purchase Price,<br>{$item[0].currency|cursign}/Ton</th>
            <th rowspan="2" width="7%">Curret Cost,<br>{$item[0].currency|cursign}/{$item[0].weight_unit|wunit}</th>
            <th rowspan="2" width="7%">P/L,<br>{$item[0].currency|cursign}/{$item[0].weight_unit|wunit}</th>
            <th colspan="2" style="border-bottom : 1px solid #B9B9B9;">Incoming DDT</th>
            <th colspan="2" style="border-bottom : 1px solid #B9B9B9;">Outgoing DDT</th>
            {*  <th rowspan="2">Days On Stock</th>  *}
            <th rowspan="2">Status</th>
            <th rowspan="2">Load Ready</th>
            <th rowspan="2">Owner</th>
            <th rowspan="2" width="10%">Internal Notes</th>
        </tr>
        <tr class="top-table" style="height: 25px;">
            <th>Number</th>
            <th>Date</th>
            <th>Number</th>
            <th>Date</th>
            <th>Number</th>
            <th>Date</th>
        </tr>
        <tr id="t-is-1">
            <td{if isset($item[1]) && $item[1].supplier_id != $item[0].supplier_id} style="background-color: #f4c430;"{/if}>{if isset($item[0].supplier)}{$item[0].supplier.title|escape:'html'}{/if}</td>
            {*
            <td{if isset($item[1]) && $item[1].mill != $item[0].mill} style="background-color: #f4c430;"{/if}>{if !empty($item[0].mill)}{$item[0].mill|escape:'html'}{/if}</td>
            <td{if isset($item[1]) && $item[1].system != $item[0].system} style="background-color: #f4c430;"{/if}>{if !empty($item[0].system)}{$item[0].system|escape:'html'}{/if}</td>
            *}
            <td{if isset($item[1]) && $item[1].supplier_invoice_no != $item[0].supplier_invoice_no} style="background-color: #f4c430;"{/if}>{if !empty($item[0].supplier_invoice_no)}{$item[0].supplier_invoice_no|escape:'html'}{/if}</td>
            <td{if isset($item[1]) && $item[1].supplier_invoice_date != $item[0].supplier_invoice_date} style="background-color: #f4c430;"{/if}>{if !empty($item[0].supplier_invoice_date)}{$item[0].supplier_invoice_date|escape:'html'|date_format:'d/m/Y'}{/if}</td>
            <td{if isset($item[1]) && $item[1].purchase_price != $item[0].purchase_price} style="background-color: #f4c430;"{/if}>{if $item[0].purchase_price != 0}{$item[0].purchase_price|escape:'html'}{/if}</td>
            <td{if isset($item[1]) && $item[1].current_cost != $item[0].current_cost} style="background-color: #f4c430;"{/if}>{if $item[0].current_cost != 0}{$item[0].current_cost|escape:'html'}{/if}</td>
            <td{if isset($item[1]) && $item[1].pl != $item[0].pl} style="background-color: #f4c430;"{/if}>{if $item[0].pl != 0}{$item[0].pl|escape:'html'}{/if}</td>
            <td{if isset($item[1]) && $item[1].in_ddt_number != $item[0].in_ddt_number} style="background-color: #f4c430;"{/if}>{if !empty($item[0].in_ddt_number)}{$item[0].in_ddt_number|escape:'html'}{/if}</td>
            <td{if isset($item[1]) && $item[1].in_ddt_date != $item[0].in_ddt_date} style="background-color: #f4c430;"{/if}>{if !empty($item[0].in_ddt_date)}{$item[0].in_ddt_date|escape:'html'|date_format:'d/m/Y'}{/if}</td>
            <td{if isset($item[1]) && $item[1].ddt_number != $item[0].ddt_number} style="background-color: #f4c430;"{/if}>{if !empty($item[0].ddt_number)}{$item[0].ddt_number|escape:'html'}{/if}</td>
            <td{if isset($item[1]) && $item[1].ddt_date != $item[0].ddt_date} style="background-color: #f4c430;"{/if}>{if !empty($item[0].ddt_date)}{$item[0].ddt_date|escape:'html'|date_format:'d/m/Y'}{/if}</td>
            {* <td class="text-center"{if isset($item[1]) && $item[1].days_on_stock != $item[0].days_on_stock} style="background-color: #f4c430;"{/if}>{$item[0].days_on_stock}</td>    *}
            <td{if isset($item[1]) && $item[1].status_id != $item[0].status_id} style="background-color: #f4c430;"{/if}>
                {if empty($item[0].status_id)}
                {else if $item[0].status_id == $smarty.const.ITEM_STATUS_PRODUCTION}In Production
                {else if $item[0].status_id == $smarty.const.ITEM_STATUS_TRANSFER}Transfer To Stock
                {else if $item[0].status_id == $smarty.const.ITEM_STATUS_STOCK}On Stock
                {else if $item[0].status_id == $smarty.const.ITEM_STATUS_ORDERED}Ordered
                {else if $item[0].status_id == $smarty.const.ITEM_STATUS_RELEASED}Released
                {else if $item[0].status_id == $smarty.const.ITEM_STATUS_DELIVERED}Delivered / Collected
                {else if $item[0].status_id == $smarty.const.ITEM_STATUS_INVOICED}Invoiced
                {/if}
                {if $item[0].status_id >= $smarty.const.ITEM_STATUS_ORDERED}<br><a href="/order/{$item[0].order_id}">{$item[0].order_id|order_doc_no}</a>{/if}
            </td>
            <td{if isset($item[1]) && $item[1].load_ready != $item[0].load_ready} style="background-color: #f4c430;"{/if}>{if !empty($item[0].load_ready)}{$item[0].load_ready|escape:'html'}{/if}</td>
            <td{if isset($item[1]) && $item[1].owner_id != $item[0].owner_id} style="background-color: #f4c430;"{/if}>{if isset($item[0].owner)}{$item[0].owner.title_trade|escape:'html'}{/if}</td>
            <td{if isset($item[1]) && $item[1].internal_notes != $item[0].internal_notes} style="background-color: #f4c430;"{/if}>{if !empty($item[0].internal_notes)}{$item[0].internal_notes|escape:'html'}{/if}</td>
        </tr>
    </tbody>
</table>
<div class="pad"></div>

<h4>Chemical Analysis</h4>
<table id="t-ic" class="list" width="100%">
    <tbody>
        <tr class="top-table">
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
        <tr id="t-ic-1">
            <td{if isset($properties[1]) && $properties[1].heat_lot != $properties[0].heat_lot} style="background-color: #f4c430;"{/if}>{if !empty($properties[0].heat_lot)}{$properties[0].heat_lot|escape:'html'}{/if}</td>
            <td{if isset($properties[1]) && $properties[1].c != $properties[0].c} style="background-color: #f4c430;"{/if}>{if $properties[0].c != 0}{$properties[0].c|escape:'html'}{/if}</td>
            <td{if isset($properties[1]) && $properties[1].si != $properties[0].si} style="background-color: #f4c430;"{/if}>{if $properties[0].si != 0}{$properties[0].si|escape:'html'}{/if}</td>
            <td{if isset($properties[1]) && $properties[1].mn != $properties[0].mn} style="background-color: #f4c430;"{/if}>{if $properties[0].mn != 0}{$properties[0].mn|escape:'html'}{/if}</td>
            <td{if isset($properties[1]) && $properties[1].p != $properties[0].p} style="background-color: #f4c430;"{/if}>{if $properties[0].p != 0}{$properties[0].p|escape:'html'}{/if}</td>
            <td{if isset($properties[1]) && $properties[1].s != $properties[0].s} style="background-color: #f4c430;"{/if}>{if $properties[0].s != 0}{$properties[0].s|escape:'html'}{/if}</td>
            <td{if isset($properties[1]) && $properties[1].cr != $properties[0].cr} style="background-color: #f4c430;"{/if}>{if $properties[0].cr != 0}{$properties[0].cr|escape:'html'}{/if}</td>
            <td{if isset($properties[1]) && $properties[1].ni != $properties[0].ni} style="background-color: #f4c430;"{/if}>{if $properties[0].ni != 0}{$properties[0].ni|escape:'html'}{/if}</td>
            <td{if isset($properties[1]) && $properties[1].cu != $properties[0].cu} style="background-color: #f4c430;"{/if}>{if $properties[0].cu != 0}{$properties[0].cu|escape:'html'}{/if}</td>
            <td{if isset($properties[1]) && $properties[1].al != $properties[0].al} style="background-color: #f4c430;"{/if}>{if $properties[0].al != 0}{$properties[0].al|escape:'html'}{/if}</td>
            <td{if isset($properties[1]) && $properties[1].mo != $properties[0].mo} style="background-color: #f4c430;"{/if}>{if $properties[0].mo != 0}{$properties[0].mo|escape:'html'}{/if}</td>
            <td{if isset($properties[1]) && $properties[1].nb != $properties[0].nb} style="background-color: #f4c430;"{/if}>{if $properties[0].nb != 0}{$properties[0].nb|escape:'html'}{/if}</td>
            <td{if isset($properties[1]) && $properties[1].v != $properties[0].v} style="background-color: #f4c430;"{/if}>{if $properties[0].v != 0}{$properties[0].v|escape:'html'}{/if}</td>
            <td{if isset($properties[1]) && $properties[1].n != $properties[0].n} style="background-color: #f4c430;"{/if}>{if $properties[0].n != 0}{$properties[0].n|escape:'html'}{/if}</td>
            <td{if isset($properties[1]) && $properties[1].ti != $properties[0].ti} style="background-color: #f4c430;"{/if}>{if $properties[0].ti != 0}{$properties[0].ti|escape:'html'}{/if}</td>
            <td{if isset($properties[1]) && $properties[1].sn != $properties[0].sn} style="background-color: #f4c430;"{/if}>{if $properties[0].sn != 0}{$properties[0].sn|escape:'html'}{/if}</td>
            <td{if isset($properties[1]) && $properties[1].b != $properties[0].b} style="background-color: #f4c430;"{/if}>{if $properties[0].b != 0}{$properties[0].b|escape:'html'}{/if}</td>
            <td{if isset($properties[1]) && $properties[1].ceq != $properties[0].ceq} style="background-color: #f4c430;"{/if}>{if $properties[0].ceq != 0}{$properties[0].ceq|escape:'html'}{/if}</td>
        </tr>
    </tbody>
</table>
<div class="pad"></div>

<h4>Mechanical Properties</h4>
<table id="t-im" class="list" width="100%">
    <tbody>
        <tr class="top-table" style="height: 25%;">
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
        <tr class="top-table" style="height: 25%;">
            <th>Sample Direction</th>            
            <th>Strength<br>N/mm<sup>2</sup></th>
            <th>Yield Point<br>N/mm<sup>2</sup></th>
            <th>Elongation<br>%</th>
            <th>Sample Direction</th>
            <th>Strength<br>J/cm<sup>2</sup></th>
            <th>Test Temp<br>deg. C</th>
        </tr>
        <tr id="t-im-1">
            <td{if isset($properties[1]) && $properties[1].tensile_sample_direction != $properties[0].tensile_sample_direction} style="background-color: #f4c430;"{/if}>{if !empty($properties[0].tensile_sample_direction)}{$properties[0].tensile_sample_direction|escape:'html'}{/if}</td>
            <td{if isset($properties[1]) && $properties[1].tensile_strength != $properties[0].tensile_strength} style="background-color: #f4c430;"{/if}>{if !empty($properties[0].tensile_strength)}{$properties[0].tensile_strength|escape:'html'}{/if}</td>
            <td{if isset($properties[1]) && $properties[1].yeild_point != $properties[0].yeild_point} style="background-color: #f4c430;"{/if}>{if !empty($properties[0].yeild_point)}{$properties[0].yeild_point|escape:'html'}{/if}</td>
            <td{if isset($properties[1]) && $properties[1].elongation != $properties[0].elongation} style="background-color: #f4c430;"{/if}>{if $properties[0].elongation != 0}{$properties[0].elongation|escape:'html'}{/if}</td>
            <td{if isset($properties[1]) && $properties[1].reduction_of_area != $properties[0].reduction_of_area} style="background-color: #f4c430;"{/if}>{if $properties[0].reduction_of_area != 0}{$properties[0].reduction_of_area|escape:'html'}{/if}</td>
            <td{if isset($properties[1]) && $properties[1].sample_direction != $properties[0].sample_direction} style="background-color: #f4c430;"{/if}>{if !empty($properties[0].sample_direction)}{$properties[0].sample_direction|escape:'html'}{/if}</td>
            <td{if isset($properties[1]) && $properties[1].impact_strength != $properties[0].impact_strength} style="background-color: #f4c430;"{/if}>{if !empty($properties[0].impact_strength)}{$properties[0].impact_strength|escape:'html'}{/if}</td>
            <td{if isset($properties[1]) && $properties[1].test_temp != $properties[0].test_temp} style="background-color: #f4c430;"{/if}>{if !empty($properties[0].test_temp)}{$properties[0].test_temp|escape:'html'}{/if}</td>
            <td{if isset($properties[1]) && $properties[1].hardness != $properties[0].hardness} style="background-color: #f4c430;"{/if}>{if !empty($properties[0].hardness)}{$properties[0].hardness|escape:'html'}{/if}</td>
            <td{if isset($properties[1]) && $properties[1].ust != $properties[0].ust} style="background-color: #f4c430;"{/if}>{if !empty($properties[0].ust)}{$properties[0].ust|escape:'html'}{/if}</td>
            <td{if isset($properties[1]) && $properties[1].stress_relieving_temp != $properties[0].stress_relieving_temp} style="background-color: #f4c430;"{/if}>{if !empty($properties[0].stress_relieving_temp)}{$properties[0].stress_relieving_temp|escape:'html'}{/if}</td>
            <td{if isset($properties[1]) && $properties[1].heating_rate_per_hour != $properties[0].heating_rate_per_hour} style="background-color: #f4c430;"{/if}>{if !empty($properties[0].heating_rate_per_hour)}{$properties[0].heating_rate_per_hour|escape:'html'}{/if}</td>
            <td{if isset($properties[1]) && $properties[1].holding_time != $properties[0].holding_time} style="background-color: #f4c430;"{/if}>{if !empty($properties[0].holding_time)}{$properties[0].holding_time|escape:'html'}{/if}</td>
            <td{if isset($properties[1]) && $properties[1].cooling_down_rate != $properties[0].cooling_down_rate} style="background-color: #f4c430;"{/if}>{if !empty($properties[0].cooling_down_rate)}{$properties[0].cooling_down_rate|escape:'html'}{/if}</td>
            <td{if isset($properties[1]) && $properties[1].normalizing_temp != $properties[0].normalizing_temp} style="background-color: #f4c430;"{/if}>{if !empty($properties[0].normalizing_temp)}{$properties[0].normalizing_temp|escape:'html'}{/if}</td>
            <td{if isset($properties[1]) && $properties[1].condition != $properties[0].condition} style="background-color: #f4c430;"{/if}>
                {if $properties[0].condition == 'ar'}As Rolled
                {else if $properties[0].condition == 'n'}Normalized
                {else if $properties[0].condition == 'nr'}Normalizing Rolling
                {/if}
            </td>            
        </tr>
    </tbody>
</table>

<div class="pad"><!-- --></div>
