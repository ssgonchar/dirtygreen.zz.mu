<table class="list" width="100%">
<tbody>
    <tr class="top-table" style="height: 25px;">
        <th rowspan="2" width="5%">Rev.</th>
        <th rowspan="2" width="10%">Plate Id</th>
        <th rowspan="2">Steel Grade</th>
        <th rowspan="2">Thickness,<br>mm</th>
        <th rowspan="2">Width,<br>mm</th>
        <th rowspan="2">Length,<br>mm</th>
        <th rowspan="2">Weight,<br>ton</th>
        <th colspan="4" style="border-bottom : 1px solid #B9B9B9;">Changes In</th>
        <th rowspan="2" width="15%">Action</th>
        <th rowspan="2" width="10%">Date, Person</th>
    </tr>
    <tr class="top-table" style="height: 25px;">
        <th width="5%">Dimensions</th>
        <th width="5%">Status</th>
        <th width="5%">Chemical</th>
        <th width="5%">Mechanical</th>
    </tr>
    {foreach name=i from=$list item=row}
    
    {/foreach}
</tbody>
</table>

{section name=i loop=$list}
<h3>
    Ver # {count($list) - $smarty.section.i.index}&nbsp;:&nbsp;{$list[i].record_at|date_format:'d/m/Y H:i:s'}&nbsp;
    {if $list[i].act == 'a'}created{else if $list[i].act == 'e'}modified{else if $list[i].act == 'd'}deleted{/if} by {$list[i].user.login}
</h3>

<table id="t-i" class="list" width="100%">
    <tbody>
        <tr class="top-table" style="height: 25px;">
            <th width="10%" rowspan="2">Plate Id</th>
            <th width="8%" rowspan="2">Steel Grade</th>
            <th width="7%" rowspan="2">Thickness,<br>{$list[i].dimension_unit}</th>
            <th width="7%" rowspan="2">Width,<br>{$list[i].dimension_unit}</th>
            <th width="7%" rowspan="2">Length,<br>{$list[i].dimension_unit}</th>
            <th width="7%" rowspan="2">Weight,<br>{$list[i].weight_unit|wunit}</th>
            <th width="7%" colspan="4" style="border-bottom : 1px solid #B9B9B9;">Measured</th>
            <th width="7%" colspan="2" style="border-bottom : 1px solid #B9B9B9;">Transport</th>
            <th width="7%" rowspan="2">Weighed Weight,<br>{$list[i].weight_unit|wunit}</th>
            <th width="5%" rowspan="2">Is Virtual</th>
            <th rowspan="2">Location</th>
        </tr>
        <tr class="top-table" style="height: 25px;">
            <th width="7%">Thickness,<br>{$list[i].dimension_unit}</th>
            <th width="7%">Width,<br>{$list[i].dimension_unit}</th>
            <th width="7%">Length,<br>{$list[i].dimension_unit}</th>
            <th width="7%">Weight,<br>{$list[i].weight_unit|wunit}</th>                        
            <th width="7%">Width,<br>{$list[i].dimension_unit}</th>
            <th width="7%">Length,<br>{$list[i].dimension_unit}</th>
        </tr>
        <tr id="t-i-1">
            <td{if !$smarty.section.i.last && $list[i.index_next].guid != $list[i].guid} style="background-color: #f4c430;"{/if}>{if !empty($list[i].guid)}{$list[i].guid|escape:'html'}{/if}</td>
            <td{if !$smarty.section.i.last && $list[i.index_next].steelgrade.title != $list[i].steelgrade.title} style="background-color: #f4c430;"{/if}>{$list[i].steelgrade.title|escape:'html'}</td>
            <td{if !$smarty.section.i.last && $list[i.index_next].thickness != $list[i].thickness} style="background-color: #f4c430;"{/if}>{if !empty($list[i].thickness)}{$list[i].thickness|escape:'html'}{/if}</td>
            <td{if !$smarty.section.i.last && $list[i.index_next].width != $list[i].width} style="background-color: #f4c430;"{/if}>{if !empty($list[i].width)}{$list[i].width|escape:'html'}{/if}</td>
            <td{if !$smarty.section.i.last && $list[i.index_next].length != $list[i].length} style="background-color: #f4c430;"{/if}>{if !empty($list[i].length)}{$list[i].length|escape:'html'}{/if}</td>
            <td{if !$smarty.section.i.last && $list[i.index_next].unitweight != $list[i].unitweight} style="background-color: #f4c430;"{/if}>{if !empty($list[i].unitweight)}{$list[i].unitweight|escape:'html'|string_format:'%.2f'}{/if}</td>
            <td{if !$smarty.section.i.last && $list[i.index_next].thickness_measured != $list[i].thickness_measured} style="background-color: #f4c430;"{/if}>{if !empty($list[i].thickness_measured)}{$list[i].thickness_measured|escape:'html'}{/if}</td>
            <td{if !$smarty.section.i.last && $list[i.index_next].width_measured != $list[i].width_measured} style="background-color: #f4c430;"{/if}>{if !empty($list[i].width_measured)}{$list[i].width_measured|escape:'html'}{/if}</td>
            <td{if !$smarty.section.i.last && $list[i.index_next].length_measured != $list[i].length_measured} style="background-color: #f4c430;"{/if}>{if !empty($list[i].length_measured)}{$list[i].length_measured|escape:'html'}{/if}</td>
            <td{if !$smarty.section.i.last && $list[i.index_next].unitweight_measured != $list[i].unitweight_measured} style="background-color: #f4c430;"{/if}>{if $list[i].unitweight_measured > 0}{$list[i].unitweight_measured|escape:'html'}{/if}</td>
            <td{if !$smarty.section.i.last && $list[i.index_next].width_max != $list[i].width_max} style="background-color: #f4c430;"{/if}>{if !empty($list[i].width_max)}{$list[i].width_max|escape:'html'}{/if}</td>
            <td{if !$smarty.section.i.last && $list[i.index_next].length_max != $list[i].length_max} style="background-color: #f4c430;"{/if}>{if !empty($list[i].length_max)}{$list[i].length_max|escape:'html'}{/if}</td>
            <td{if !$smarty.section.i.last && $list[i.index_next].unitweight_weighed != $list[i].unitweight_weighed} style="background-color: #f4c430;"{/if}>{if $list[i].unitweight_weighed > 0}{$list[i].unitweight_weighed|escape:'html'}{/if}</td>
            <td class="text-center"{if !$smarty.section.i.last && $list[i.index_next].is_virtual != $list[i].is_virtual} style="background-color: #f4c430;"{/if}>{if !empty($list[i].is_virtual)}yes{else}no{/if}</td>
            <td{if !$smarty.section.i.last && $list[i.index_next].stockholder.title != $list[i].stockholder.title} style="background-color: #f4c430;"{/if}>{$list[i].location.title|escape:'html'}</td>
        </tr>        
    </tbody>    
</table>
<div class="pad"></div>

<h4>Status</h4>
<table id="t-is" class="list" width="100%">
    <tbody>
        <tr class="top-table" style="height: 25px;">
            <th rowspan="2">Producer</th>
            <th rowspan="2">Mill</th>
            <th rowspan="2">System</th>
            <th colspan="2" style="border-bottom : 1px solid #B9B9B9;">Supplier Invoice</th>
            <th rowspan="2" width="7%">Purchase Price,<br>{$list[i].currency_sign}/{$list[i].weight_unit|wunit}</th>
            <th rowspan="2" width="7%">Curret Cost,<br>{$list[i].currency_sign}/{$list[i].weight_unit|wunit}</th>
            <th rowspan="2" width="7%">P/L,<br>{$list[i].currency_sign}/{$list[i].weight_unit|wunit}</th>
            <th colspan="2" style="border-bottom : 1px solid #B9B9B9;">Incoming DDT</th>
            <th colspan="2" style="border-bottom : 1px solid #B9B9B9;">Outgoing DDT</th>
            <th rowspan="2">Days On Stock</th>
            <th rowspan="2">Status</th>
            <th rowspan="2">Load Ready</th>
            <th rowspan="2">Owner</th>
            <th rowspan="2">Internal Notes</th>
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
            <td{if !$smarty.section.i.last && $list[i.index_next].supplier.title != $list[i].supplier.title} style="background-color: #f4c430;"{/if}>{$list[i].supplier.title|escape:'html'}</td>
            <td{if !$smarty.section.i.last && $list[i.index_next].mill != $list[i].mill} style="background-color: #f4c430;"{/if}>{if !empty($list[i].mill)}{$list[i].mill|escape:'html'}{/if}</td>
            <td{if !$smarty.section.i.last && $list[i.index_next].system != $list[i].system} style="background-color: #f4c430;"{/if}>{if !empty($list[i].system)}{$list[i].system|escape:'html'}{/if}</td>
            <td{if !$smarty.section.i.last && $list[i.index_next].supplier_invoice_no != $list[i].supplier_invoice_no} style="background-color: #f4c430;"{/if}>{if !empty($list[i].supplier_invoice_no)}{$list[i].supplier_invoice_no|escape:'html'}{/if}</td>
            <td{if !$smarty.section.i.last && $list[i.index_next].supplier_invoice_date != $list[i].supplier_invoice_date} style="background-color: #f4c430;"{/if}>{if !empty($list[i].supplier_invoice_date)}{$list[i].supplier_invoice_date|escape:'html'|date_format:'d/m/Y'}{/if}</td>
            <td{if !$smarty.section.i.last && $list[i.index_next].purchase_price != $list[i].purchase_price} style="background-color: #f4c430;"{/if}>{if $list[i].purchase_price != 0}{$list[i].purchase_price|escape:'html'}{/if}</td>
            <td{if !$smarty.section.i.last && $list[i.index_next].current_cost != $list[i].current_cost} style="background-color: #f4c430;"{/if}>{if $list[i].current_cost != 0}{$list[i].current_cost|escape:'html'}{/if}</td>
            <td{if !$smarty.section.i.last && $list[i.index_next].pl != $list[i].pl} style="background-color: #f4c430;"{/if}>{if $list[i].pl != 0}{$list[i].pl|escape:'html'}{/if}</td>
            <td{if !$smarty.section.i.last && $list[i.index_next].in_ddt_number != $list[i].in_ddt_number} style="background-color: #f4c430;"{/if}>{if !empty($list[i].in_ddt_number)}{$list[i].in_ddt_number|escape:'html'}{/if}</td>
            <td{if !$smarty.section.i.last && $list[i.index_next].in_ddt_date != $list[i].in_ddt_date} style="background-color: #f4c430;"{/if}>{if !empty($list[i].in_ddt_date)}{$list[i].in_ddt_date|escape:'html'|date_format:'d/m/Y'}{/if}</td>
            <td{if !$smarty.section.i.last && $list[i.index_next].ddt_number != $list[i].ddt_number} style="background-color: #f4c430;"{/if}>{if !empty($list[i].ddt_number)}{$list[i].ddt_number|escape:'html'}{/if}</td>
            <td{if !$smarty.section.i.last && $list[i.index_next].ddt_date != $list[i].ddt_date} style="background-color: #f4c430;"{/if}>{if !empty($list[i].ddt_date)}{$list[i].ddt_date|escape:'html'|date_format:'d/m/Y'}{/if}</td>
            <td class="text-center"{if !$smarty.section.i.last && $list[i.index_next].days_on_stock != $list[i].days_on_stock} style="background-color: #f4c430;"{/if}>{$list[i].days_on_stock}</td>
            <td{if !$smarty.section.i.last && $list[i.index_next].status != $list[i].status} style="background-color: #f4c430;"{/if}>
                {if $list[i].status == 'new'}
                {else if $list[i].status == 'inproduction'}In Production
                {else if $list[i].status == 'onstock'}On Stock
                {else if $list[i].status == 'reserved'}Reserved
                {else if $list[i].status == 'locked'}Locked
                {else if $list[i].status == 'ordered'}Ordered
                {else if $list[i].status == 'delivered'}Delivered
                {else if $list[i].status == 'invoiced'}Invoiced
                {/if}
            </td>
            <td{if !$smarty.section.i.last && $list[i.index_next].load_ready != $list[i].load_ready} style="background-color: #f4c430;"{/if}>{if !empty($list[i].load_ready)}{$list[i].load_ready|escape:'html'}{/if}</td>
            <td{if !$smarty.section.i.last && $list[i.index_next].owner.title != $list[i].owner.title} style="background-color: #f4c430;"{/if}>{$list[i].owner.title|escape:'html'}</td>
            <td{if !$smarty.section.i.last && $list[i.index_next].internal_notes != $list[i].internal_notes} style="background-color: #f4c430;"{/if}>{if !empty($list[i].internal_notes)}{$list[i].internal_notes|escape:'html'}{/if}</td>
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
            <td{if !$smarty.section.i.last && $list[i.index_next].heat_lot != $list[i].heat_lot} style="background-color: #f4c430;"{/if}>{if !empty($list[i].heat_lot)}{$list[i].heat_lot|escape:'html'}{/if}</td>
            <td{if !$smarty.section.i.last && $list[i.index_next].c != $list[i].c} style="background-color: #f4c430;"{/if}>{if $list[i].c != 0}{$list[i].c|escape:'html'}{/if}</td>
            <td{if !$smarty.section.i.last && $list[i.index_next].si != $list[i].si} style="background-color: #f4c430;"{/if}>{if $list[i].si != 0}{$list[i].si|escape:'html'}{/if}</td>
            <td{if !$smarty.section.i.last && $list[i.index_next].mn != $list[i].mn} style="background-color: #f4c430;"{/if}>{if $list[i].mn != 0}{$list[i].mn|escape:'html'}{/if}</td>
            <td{if !$smarty.section.i.last && $list[i.index_next].p != $list[i].p} style="background-color: #f4c430;"{/if}>{if $list[i].p != 0}{$list[i].p|escape:'html'}{/if}</td>
            <td{if !$smarty.section.i.last && $list[i.index_next].s != $list[i].s} style="background-color: #f4c430;"{/if}>{if $list[i].s != 0}{$list[i].s|escape:'html'}{/if}</td>
            <td{if !$smarty.section.i.last && $list[i.index_next].cr != $list[i].cr} style="background-color: #f4c430;"{/if}>{if $list[i].cr != 0}{$list[i].cr|escape:'html'}{/if}</td>
            <td{if !$smarty.section.i.last && $list[i.index_next].ni != $list[i].ni} style="background-color: #f4c430;"{/if}>{if $list[i].ni != 0}{$list[i].ni|escape:'html'}{/if}</td>
            <td{if !$smarty.section.i.last && $list[i.index_next].cu != $list[i].cu} style="background-color: #f4c430;"{/if}>{if $list[i].cu != 0}{$list[i].cu|escape:'html'}{/if}</td>
            <td{if !$smarty.section.i.last && $list[i.index_next].al != $list[i].al} style="background-color: #f4c430;"{/if}>{if $list[i].al != 0}{$list[i].al|escape:'html'}{/if}</td>
            <td{if !$smarty.section.i.last && $list[i.index_next].mo != $list[i].mo} style="background-color: #f4c430;"{/if}>{if $list[i].mo != 0}{$list[i].mo|escape:'html'}{/if}</td>
            <td{if !$smarty.section.i.last && $list[i.index_next].nb != $list[i].nb} style="background-color: #f4c430;"{/if}>{if $list[i].nb != 0}{$list[i].nb|escape:'html'}{/if}</td>
            <td{if !$smarty.section.i.last && $list[i.index_next].v != $list[i].v} style="background-color: #f4c430;"{/if}>{if $list[i].v != 0}{$list[i].v|escape:'html'}{/if}</td>
            <td{if !$smarty.section.i.last && $list[i.index_next].n != $list[i].n} style="background-color: #f4c430;"{/if}>{if $list[i].n != 0}{$list[i].n|escape:'html'}{/if}</td>
            <td{if !$smarty.section.i.last && $list[i.index_next].ti != $list[i].ti} style="background-color: #f4c430;"{/if}>{if $list[i].ti != 0}{$list[i].ti|escape:'html'}{/if}</td>
            <td{if !$smarty.section.i.last && $list[i.index_next].sn != $list[i].sn} style="background-color: #f4c430;"{/if}>{if $list[i].sn != 0}{$list[i].sn|escape:'html'}{/if}</td>
            <td{if !$smarty.section.i.last && $list[i.index_next].b != $list[i].b} style="background-color: #f4c430;"{/if}>{if $list[i].b != 0}{$list[i].b|escape:'html'}{/if}</td>
            <td{if !$smarty.section.i.last && $list[i.index_next].ceq != $list[i].ceq} style="background-color: #f4c430;"{/if}>{if $list[i].ceq != 0}{$list[i].ceq|escape:'html'}{/if}</td>
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
            <th>Sample Direction<br>N/mm<sup>2</sup></th>            
            <th>Strength<br>N/mm<sup>2</sup></th>
            <th>Yield Point<br>N/mm<sup>2</sup></th>
            <th>Elongation<br>%</th>
            <th>Sample Direction</th>
            <th>Strength<br>J/cm<sup>2</sup></th>
            <th>Test Temp<br>deg. C</th>
        </tr>
        <tr id="t-im-1">
            <td{if !$smarty.section.i.last && $list[i.index_next].tensile_sample_direction != $list[i].tensile_sample_direction} style="background-color: #f4c430;"{/if}>{if !empty($list[i].tensile_sample_direction)}{$list[i].tensile_sample_direction|escape:'html'}{/if}</td>
            <td{if !$smarty.section.i.last && $list[i.index_next].tensile_strength != $list[i].tensile_strength} style="background-color: #f4c430;"{/if}>{if !empty($list[i].tensile_strength)}{$list[i].tensile_strength|escape:'html'}{/if}</td>
            <td{if !$smarty.section.i.last && $list[i.index_next].yeild_point != $list[i].yeild_point} style="background-color: #f4c430;"{/if}>{if !empty($list[i].yeild_point)}{$list[i].yeild_point|escape:'html'}{/if}</td>
            <td{if !$smarty.section.i.last && $list[i.index_next].elongation != $list[i].elongation} style="background-color: #f4c430;"{/if}>{if $list[i].elongation != 0}{$list[i].elongation|escape:'html'}{/if}</td>
            <td{if !$smarty.section.i.last && $list[i.index_next].reduction_of_area != $list[i].reduction_of_area} style="background-color: #f4c430;"{/if}>{if $list[i].reduction_of_area != 0}{$list[i].reduction_of_area|escape:'html'}{/if}</td>
            <td{if !$smarty.section.i.last && $list[i.index_next].sample_direction != $list[i].sample_direction} style="background-color: #f4c430;"{/if}>{if !empty($list[i].sample_direction)}{$list[i].sample_direction|escape:'html'}{/if}</td>
            <td{if !$smarty.section.i.last && $list[i.index_next].impact_strength != $list[i].impact_strength} style="background-color: #f4c430;"{/if}>{if !empty($list[i].impact_strength)}{$list[i].impact_strength|escape:'html'}{/if}</td>
            <td{if !$smarty.section.i.last && $list[i.index_next].test_temp != $list[i].test_temp} style="background-color: #f4c430;"{/if}>{if !empty($list[i].test_temp)}{$list[i].test_temp|escape:'html'}{/if}</td>
            <td{if !$smarty.section.i.last && $list[i.index_next].hardness != $list[i].hardness} style="background-color: #f4c430;"{/if}>{if !empty($list[i].hardness)}{$list[i].hardness|escape:'html'}{/if}</td>
            <td{if !$smarty.section.i.last && $list[i.index_next].ust != $list[i].ust} style="background-color: #f4c430;"{/if}>{if !empty($list[i].ust)}{$list[i].ust|escape:'html'}{/if}</td>
            <td{if !$smarty.section.i.last && $list[i.index_next].stress_relieving_temp != $list[i].stress_relieving_temp} style="background-color: #f4c430;"{/if}>{if !empty($list[i].stress_relieving_temp)}{$list[i].stress_relieving_temp|escape:'html'}{/if}</td>
            <td{if !$smarty.section.i.last && $list[i.index_next].heating_rate_per_hour != $list[i].heating_rate_per_hour} style="background-color: #f4c430;"{/if}>{if !empty($list[i].heating_rate_per_hour)}{$list[i].heating_rate_per_hour|escape:'html'}{/if}</td>
            <td{if !$smarty.section.i.last && $list[i.index_next].holding_time != $list[i].holding_time} style="background-color: #f4c430;"{/if}>{if !empty($list[i].holding_time)}{$list[i].holding_time|escape:'html'}{/if}</td>
            <td{if !$smarty.section.i.last && $list[i.index_next].cooling_down_rate != $list[i].cooling_down_rate} style="background-color: #f4c430;"{/if}>{if !empty($list[i].cooling_down_rate)}{$list[i].cooling_down_rate|escape:'html'}{/if}</td>
            <td{if !$smarty.section.i.last && $list[i.index_next].normalizing_temp != $list[i].normalizing_temp} style="background-color: #f4c430;"{/if}>{if !empty($list[i].normalizing_temp)}{$list[i].normalizing_temp|escape:'html'}{/if}</td>
            <td{if !$smarty.section.i.last && $list[i.index_next].condition != $list[i].condition} style="background-color: #f4c430;"{/if}>
                {if $list[i].condition == 'ar'}As Rolled
                {else if $list[i].condition == 'n'}Normalized
                {else if $list[i].condition == 'nr'}Normalizing Rolling
                {/if}
            </td>            
        </tr>
    </tbody>
</table>

<div class="pad"><!-- --></div>
{/section}
