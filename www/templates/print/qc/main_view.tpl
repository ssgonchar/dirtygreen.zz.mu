<table width="100%">
    <tr>
        <td width="50%" class="text-top">
            <table class="form" width="100%">                
                {if isset($qc.order_id) && !empty($qc.order_id)}
                <tr>
                    <td class="text-right" style="font-weight: bold;">Order : </td>
                    <td><a href="/order/{$qc.order_id}">{$qc.order.doc_no}</a></td>
                </tr>
                {/if}
                {if !empty($qc.biz)}
                <tr>
                    <td class="text-right" style="font-weight: bold;" width="150px">BIZ : </td>
                    <td><a href="/biz/{$qc.biz_id}">{$qc.biz}</a></td>
                </tr>
                {/if}
                {if !empty($qc.certification_standard)}
                <tr>
                    <td class="text-right" style="font-weight: bold;">Certification Standard : </td>
                    <td>{$qc.certification_standard|escape:'html'}</td>
                </tr>
                {/if}
                {if !empty($qc.commodity_name)}
                <tr>
                    <td class="text-right" style="font-weight: bold;">Commodity Name : </td>
                    <td>{$qc.commodity_name|escape:'html'}</td>
                </tr>                
                {/if}
                {if isset($qc.standard) && !empty($qc.standard)}
                <tr>
                    <td class="text-right" style="font-weight: bold;">Standard : </td>
                    <td>{$qc.standard|escape:'html'}</td>
                </tr>
                {/if}
                {if !empty($qc.customer)}
                <tr>
                    <td class="text-right" style="font-weight: bold;">Customer : </td>
                    <td><a href="/company/{$qc.customer_id}">{$qc.customer|escape:'html'}</a></td>
                </tr>
                {/if}
                {if !empty($qc.customer_order_no)}
                <tr>
                    <td class="text-right" style="font-weight: bold;">Customer Order No : </td>
                    <td>{$qc.customer_order_no|escape:'html'}</td>
                </tr>
                {/if}
                {if !empty($qc.manufacturer)}
                <tr>
                    <td class="text-right" style="font-weight: bold;">Manufacturer : </td>
                    <td>{$qc.manufacturer|escape:'html'}</td>
                </tr>
                {/if}
                {if !empty($qc.country_of_origin)}
                <tr>
                    <td class="text-right" style="font-weight: bold;">Country Of Origin : </td>
                    <td>{$qc.country_of_origin|escape:'html'}</td>
                </tr>
                {/if}
                {if !empty($qc.delivery_conditions)}
                <tr>
                    <td class="text-right" style="font-weight: bold;">Delivery Condition : </td>
                    <td>{$qc.delivery_conditions|escape:'html'}</td>
                </tr>
                {/if}
                <tr>
                    <td class="text-right" style="font-weight: bold;"></td>
                    <td></td>
                </tr>                
                <tr>
                    <td class="text-right" style="font-weight: bold;">PDF : </td>
                    <td>{if isset($qc.attachment)}<a class="pdf" target="_blank" href="/file/{$qc.attachment.secret_name}/{$qc.attachment.original_name}">{$qc.attachment.original_name}</a>{else}{''|undef}{/if}</td>
                </tr>
            </table>
        </td>
        <td width="50%" class="text-top">
            <table class="form" width="100%">
                {if !empty($qc.steelmaking_process)}
                <tr>
                    <td class="text-right" style="font-weight: bold;" width="155px">Steelmaking Process : </td>
                    <td>{$qc.steelmaking_process|escape:'html'}</td>
                </tr>
                {/if}
                {if !empty($qc.ultrasonic_test)}
                <tr>
                    <td class="text-right" style="font-weight: bold;">Ultrasonic Test : </td>
                    <td>{$qc.ultrasonic_test|escape:'html'}</td>
                </tr>
                {/if}
                {if !empty($qc.marking)}
                <tr>
                    <td class="text-right" style="font-weight: bold;">Marking : </td>
                    <td>{$qc.marking|escape:'html'}</td>
                </tr>
                {/if}
                {if !empty($qc.visual_inspection)}
                <tr>
                    <td class="text-right" style="font-weight: bold;">Visual Inspection : </td>
                    <td>{$qc.visual_inspection|escape:'html'}</td>
                </tr>
                {/if}
                {if !empty($qc.flattening)}
                <tr>
                    <td class="text-right" style="font-weight: bold;">Flattening : </td>
                    <td>{$qc.flattening|escape:'html'}</td>
                </tr>
                {/if}
                {if !empty($qc.stress_relieving)}
                <tr>
                    <td class="text-right" style="font-weight: bold;">Stress Relieving : </td>
                    <td>{$qc.stress_relieving|escape:'html'}</td>
                </tr>
                {/if}
                {if !empty($qc.surface_quality)}
                <tr>
                    <td class="text-right" style="font-weight: bold;">Surface Quality : </td>
                    <td>{$qc.surface_quality|escape:'html'}</td>
                </tr>
                {/if}
                {if !empty($qc.tolerances_on_thickness)}
                <tr>
                    <td class="text-right" style="font-weight: bold;">Tolerances On Thickness : </td>
                    <td>{$qc.tolerances_on_thickness|escape:'html'}</td>
                </tr>
                {/if}
                {if !empty($qc.tolerances_on_flatness)}
                <tr>
                    <td class="text-right" style="font-weight: bold;">Tolerances On Flatness : </td>
                    <td>{$qc.tolerances_on_flatness|escape:'html'}</td>
                </tr>
                {/if}
                {if !empty($qc.ce_mark)}
                <tr>
                    <td></td>
                    <td>Include "CE Mark"</td>
                </tr>
                {/if}
                {if !empty($qc.no_weld_repair)}
                <tr>
                    <td></td>
                    <td>Include "No Weld Repair"</td>
                </tr>
                {/if}
            </table>        
        </td>
    </tr>
</table>
<div class="pad"></div>

<h3>Specification</h3>
<table id="t-i" class="list" width="100%">
    <tbody>
        <tr class="top-table">
            <th width="7%">Plate Id</th>
            <th>Steel Grade</th>
            <th>Thickness<br>{$qc.dim_unit}</th>
            <th>Width<br>{$qc.dim_unit}</th>
            <th>Length<br>{$qc.dim_unit}</th>
            <th>Pcs</th>
            <th>Weight<br>{$qc.wght_unit|wunit}</th>
            <th>Heat / Lot</th>
            <th class="text-right" width="5%">Item Id</th>
        </tr>
        {foreach from=$items item=row}
        <tr>
            <td>{if !empty($row.steelitem.guid)}{$row.steelitem.guid|escape:'html'}{else}&hellip;{/if}</td>
            <td>{if isset($row.steelitem.steelgrade)}{$row.steelitem.steelgrade.title|escape:'html'}{/if}</td>
            <td>{if $qc.mam_co == 'pa' && $qc.dim_unit == 'mm'}{$row.steelitem.thickness_mm|smartfloat:1}{else}{$row.steelitem.thickness|escape:'html'}{/if}</td>
            <td>{if $qc.mam_co == 'pa' && $qc.dim_unit == 'mm'}{$row.steelitem.width_mm|string_format:'%d'}{else}{$row.steelitem.width|escape:'html'}{/if}</td>
            <td>{if $qc.mam_co == 'pa' && $qc.dim_unit == 'mm'}{$row.steelitem.length_mm|string_format:'%d'}{else}{$row.steelitem.length|escape:'html'}{/if}</td>
            <td>1</td>
            <td>{if $qc.mam_co == 'pa' && $qc.wght_unit == 'mt'}{$row.steelitem.unitweight_ton|escape:'html'|string_format:'%.2f'}{else}{$row.steelitem.unitweight|escape:'html'|string_format:'%.2f'}{/if}</td>
            <td>{if !empty($row.steelitem.properties.heat_lot)}{$row.steelitem.properties.heat_lot|escape:'html'|string_format:'%.2f'}{else}&hellip;{/if}</td>
            <td class="text-right">{$row.steelitem.id}</td>
        </tr>
        {/foreach}
    </tbody>    
</table>
<div class="pad"></div>

<h3>Chemical Analysis</h3>
<table id="t-ic" class="list" width="100%">
    <tbody>
        <tr class="top-table">
            <th width="7%">Plate Id</th>
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
        {foreach from=$items item=row}
        <tr>
            <td>{if !empty($row.steelitem.guid)}{$row.steelitem.guid|escape:'html'}{else}&hellip;{/if}</td>
            <td>{if $row.steelitem.properties.c != 0}{$row.steelitem.properties.c|escape:'html'}{else}&hellip;{/if}</td>
            <td>{if $row.steelitem.properties.si != 0}{$row.steelitem.properties.si|escape:'html'}{else}&hellip;{/if}</td>
            <td>{if $row.steelitem.properties.mn != 0}{$row.steelitem.properties.mn|escape:'html'}{else}&hellip;{/if}</td>
            <td>{if $row.steelitem.properties.p != 0}{$row.steelitem.properties.p|escape:'html'}{else}&hellip;{/if}</td>
            <td>{if $row.steelitem.properties.s != 0}{$row.steelitem.properties.s|escape:'html'}{else}&hellip;{/if}</td>
            <td>{if $row.steelitem.properties.cr != 0}{$row.steelitem.properties.cr|escape:'html'}{else}&hellip;{/if}</td>
            <td>{if $row.steelitem.properties.ni != 0}{$row.steelitem.properties.ni|escape:'html'}{else}&hellip;{/if}</td>
            <td>{if $row.steelitem.properties.cu != 0}{$row.steelitem.properties.cu|escape:'html'}{else}&hellip;{/if}</td>
            <td>{if $row.steelitem.properties.al != 0}{$row.steelitem.properties.al|escape:'html'}{else}&hellip;{/if}</td>
            <td>{if $row.steelitem.properties.mo != 0}{$row.steelitem.properties.mo|escape:'html'}{else}&hellip;{/if}</td>
            <td>{if $row.steelitem.properties.nb != 0}{$row.steelitem.properties.nb|escape:'html'}{else}&hellip;{/if}</td>
            <td>{if $row.steelitem.properties.v != 0}{$row.steelitem.properties.v|escape:'html'}{else}&hellip;{/if}</td>
            <td>{if $row.steelitem.properties.n != 0}{$row.steelitem.properties.n|escape:'html'}{else}&hellip;{/if}</td>
            <td>{if $row.steelitem.properties.ti != 0}{$row.steelitem.properties.ti|escape:'html'}{else}&hellip;{/if}</td>
            <td>{if $row.steelitem.properties.sn != 0}{$row.steelitem.properties.sn|escape:'html'}{else}&hellip;{/if}</td>
            <td>{if $row.steelitem.properties.b != 0}{$row.steelitem.properties.b|escape:'html'}{else}&hellip;{/if}</td>
            <td>{if $row.steelitem.properties.ceq != 0}{$row.steelitem.properties.ceq|escape:'html'}{else}&hellip;{/if}</td>
        </tr>
        {/foreach}
    </tbody>
</table>
<div class="pad"></div>

<h3>Mechanical Properties</h3>
<table id="t-im" class="list" width="100%">
    <tbody>
        <tr class="top-table" style="height: 25%;">
            <th width="7%" rowspan="2">Plate Id</th>
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
        {foreach from=$items item=row}
        <tr>
            <td>{if !empty($row.steelitem.guid)}{$row.steelitem.guid|escape:'html'}{else}&hellip;{/if}</td>
            <td>{if !empty($row.steelitem.properties.tensile_sample_direction)}{$row.steelitem.properties.tensile_sample_direction|escape:'html'}{else}&hellip;{/if}</td>
            <td>{if !empty($row.steelitem.properties.tensile_strength)}{$row.steelitem.properties.tensile_strength|escape:'html'}{else}&hellip;{/if}</td>
            <td>{if !empty($row.steelitem.properties.yeild_point)}{$row.steelitem.properties.yeild_point|escape:'html'}{else}&hellip;{/if}</td>
            <td>{if $row.steelitem.properties.elongation != 0}{$row.steelitem.properties.elongation|escape:'html'}{else}&hellip;{/if}</td>
            <td>{if $row.steelitem.properties.reduction_of_area != 0}{$row.steelitem.properties.reduction_of_area|escape:'html'}{else}&hellip;{/if}</td>
            <td>{if !empty($row.steelitem.properties.sample_direction)}{$row.steelitem.properties.sample_direction|escape:'html'}{else}&hellip;{/if}</td>
            <td>{if !empty($row.steelitem.properties.impact_strength)}{$row.steelitem.properties.impact_strength|escape:'html'}{else}&hellip;{/if}</td>
            <td>{if !empty($row.steelitem.properties.test_temp)}{$row.steelitem.properties.test_temp|escape:'html'}{else}&hellip;{/if}</td>
            <td>{if !empty($row.steelitem.properties.hardness)}{$row.steelitem.properties.hardness|escape:'html'}{else}&hellip;{/if}</td>
            <td>{if !empty($row.steelitem.properties.ust)}{$row.steelitem.properties.ust|escape:'html'}{else}&hellip;{/if}</td>
            <td>{if !empty($row.steelitem.properties.stress_relieving_temp)}{$row.steelitem.properties.stress_relieving_temp|escape:'html'}{else}&hellip;{/if}</td>
            <td>{if !empty($row.steelitem.properties.heating_rate_per_hour)}{$row.steelitem.properties.heating_rate_per_hour|escape:'html'}{else}&hellip;{/if}</td>
            <td>{if !empty($row.steelitem.properties.holding_time)}{$row.steelitem.properties.holding_time|escape:'html'}{else}&hellip;{/if}</td>
            <td>{if !empty($row.steelitem.properties.cooling_down_rate)}{$row.steelitem.properties.cooling_down_rate|escape:'html'}{else}&hellip;{/if}</td>
            <td>{if !empty($row.steelitem.properties.normalizing_temp)}{$row.steelitem.properties.normalizing_temp|escape:'html'}{else}&hellip;{/if}</td>
        </tr>
        {/foreach}
    </tbody>
</table>