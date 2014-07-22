<table width="100%">
    <tr>
        <td width="50%" class="text-top">
            <table class="form" width="100%">                
                {if isset($form.order_id) && !empty($form.order_id)}
                <tr height="32px">
                    <td class="text-right">Order : </td>
                    <td><a href="/order/{$form.order_id}">{$form.order.doc_no}</a></td>
                </tr>
                {/if}
                <tr>
                    <td class="text-right" width="150px">BIZ : </td>
                    <td>
                        <input type="text" id="qc-biz" name="form[biz]" value="{if isset($form.biz) && !empty($form.biz)}{$form.biz}{/if}" class="max biz-autocomplete" data-titlefield="doc_no">
                        <input type="hidden" id="qc-biz-id" name="form[biz_id]" value="{if isset($form.biz_id) && !empty($form.biz_id)}{$form.biz_id}{/if}">
                    </td>
                </tr>                
                <tr>
                    <td class="text-right">Certification Standard : </td>
                    <td><input type="text" name="form[certification_standard]"{if isset($form.certification_standard)} value="{$form.certification_standard|escape:'html'}"{/if} class="max"></td>
                </tr>
                <tr>
                    <td class="text-right">Commodity Name : </td>
                    <td><input type="text" name="form[commodity_name]"{if isset($form.commodity_name)} value="{$form.commodity_name|escape:'html'}"{/if} class="max"></td>
                </tr>                
                <tr>
                    <td class="text-right">Standard : </td>
                    <td><input type="text" name="form[standard]"{if isset($form.standard)} value="{$form.standard|escape:'html'}"{/if} class="max"></td>
                </tr>
                <tr>
                    <td class="text-right">Customer : </td>
                    <td>
                        <input type="text" id="customer" name="form[customer]"{if isset($form.customer)} value="{$form.customer|escape:'html'}"{/if} class="max">
                        <input type="hidden" id="customer_id" name="form[customer_id]"{if isset($form.customer_id)} value="{$form.customer_id}"{/if}>
                    </td>
                </tr>
                <tr>
                    <td class="text-right">Customer Order No : </td>
                    <td><input type="text" name="form[customer_order_no]"{if isset($form.customer_order_no)} value="{$form.customer_order_no|escape:'html'}"{/if} class="max"></td>
                </tr>                                
                <tr>
                    <td class="text-right">Manufacturer : </td>
                    <td><input type="text" name="form[manufacturer]"{if isset($form.manufacturer)} value="{$form.manufacturer|escape:'html'}"{/if} class="max"></td>
                </tr>
                <tr>
                    <td class="text-right">Country Of Origin : </td>
                    <td><input type="text" name="form[country_of_origin]"{if isset($form.country_of_origin)} value="{$form.country_of_origin|escape:'html'}"{/if} class="max"></td>
                </tr>
                <tr>
                    <td class="text-right">Delivery Condition : </td>
                    <td><input type="text" name="form[delivery_conditions]"{if isset($form.delivery_conditions)} value="{$form.delivery_conditions|escape:'html'}"{/if} class="max"></td>
                </tr>
                <tr>
                    <td class="text-right">Test Ref. : </td>
                    <td><input type="text" name="form[test_ref]"{if isset($form.test_ref)} value="{$form.test_ref|escape:'html'}"{/if} class="max"></td>
                </tr>
                
                {if isset($include_dimensions_select)}
                <tr>
                    <td class="text-right">QC Units : </td>
                    <td>
                        <select name="form[units]">
                            <option value="mm/mt"{if isset($form.units) && $form.units == 'mm/mt'} selected="selected"{/if}>mm / Tons</option>
                            <option value="in/lb"{if isset($form.units) && $form.units == 'in/lb'} selected="selected"{/if}>inches / lb</option>
                        </select>
                    </td>
                </tr>
                {/if}                
            </table>
        </td>
        <td width="50%" class="text-top">
            <table class="form" width="100%">
                <tr>
                    <td class="text-right" width="150px">Steelmaking Process : </td>
                    <td><input type="text" name="form[steelmaking_process]"{if isset($form.steelmaking_process)} value="{$form.steelmaking_process|escape:'html'}"{/if} class="max"></td>
                </tr>
                <tr>
                    <td class="text-right">Ultrasonic Test : </td>
                    <td><input type="text" name="form[ultrasonic_test]"{if isset($form.ultrasonic_test)} value="{$form.ultrasonic_test|escape:'html'}"{/if} class="max"></td>
                </tr>
                <tr>
                    <td class="text-right">Marking : </td>
                    <td><input type="text" name="form[marking]"{if isset($form.marking)} value="{$form.marking|escape:'html'}"{/if} class="max"></td>
                </tr>
                <tr>
                    <td class="text-right">Visual Inspection : </td>
                    <td><input type="text" name="form[visual_inspection]"{if isset($form.visual_inspection)} value="{$form.visual_inspection|escape:'html'}"{/if} class="max"></td>
                </tr>
                <tr>
                    <td class="text-right">Flattening : </td>
                    <td><input type="text" name="form[flattening]"{if isset($form.flattening)} value="{$form.flattening|escape:'html'}"{/if} class="max"></td>
                </tr>
                <tr>
                    <td class="text-right">Stress Relieving : </td>
                    <td><input type="text" name="form[stress_relieving]"{if isset($form.stress_relieving)} value="{$form.stress_relieving|escape:'html'}"{/if} class="max"></td>
                </tr>
                <tr>
                    <td class="text-right">Surface Quality : </td>
                    <td><input type="text" name="form[surface_quality]"{if isset($form.surface_quality)} value="{$form.surface_quality|escape:'html'}"{/if} class="max"></td>
                </tr>
                <tr>
                    <td class="text-right">Tolerances On Thickness : </td>
                    <td><input type="text" name="form[tolerances_on_thickness]"{if isset($form.tolerances_on_thickness)} value="{$form.tolerances_on_thickness|escape:'html'}"{/if} class="max"></td>
                </tr>
                <tr>
                    <td class="text-right">Tolerances On Flatness : </td>
                    <td><input type="text" name="form[tolerances_on_flatness]"{if isset($form.tolerances_on_flatness)} value="{$form.tolerances_on_flatness|escape:'html'}"{/if} class="max"></td>
                </tr>
                <tr>
                    <td></td>
                    <td height="20px">
                        <label for="ce_mark"><input type="checkbox" id="ce_mark" name="form[ce_mark]"{if isset($form.ce_mark) && $form.ce_mark > 0} checked="checked"{/if} value="1"> CE Mark</label>
                        {if !isset($include_dimensions_select)}<input type="hidden" name="form[units]" value="{$form.units}">{/if}
                    </td>
                </tr>                
                <tr>
                    <td></td>
                    <td height="20px">
                        <label for="no_weld_repair"><input type="checkbox" id="no_weld_repair" name="form[no_weld_repair]"{if isset($form.no_weld_repair) && $form.no_weld_repair > 0} checked="checked"{/if} value="1"> No Weld Repair</label>
                    </td>
                </tr>                
            </table>        
        </td>
    </tr>
</table>
<input type="hidden" name="form[stock_id]" value="{if isset($form.stock_id)}{$form.stock_id}{else}0{/if}">
<input type="hidden" name="form[mam_co]" value="{if isset($form.mam_co)}{$form.mam_co}{/if}">
<input type="hidden" name="form[order_id]" value="{if isset($form.order_id)}{$form.order_id}{else}0{/if}">
<div class="pad"></div>

{if empty($items)}
<i style="color: #999;">There are no items.</i>
{else}
<h3>Specification</h3>
<table id="t-i" class="list" width="100%">
    <tbody>
        <tr class="top-table">
            <th width="7%">Plate Id</th>
            <th>Steel Grade</th>
            <th>Thickness{if !isset($multiunits)}, {$form.dim_unit}{/if}</th>
            <th>Width{if !isset($multiunits)}, {$form.dim_unit}{/if}</th>
            <th>Length{if !isset($multiunits)}, {$form.dim_unit}{/if}</th>
            <th>Pcs</th>
            <th>Weight{if !isset($multiunits)}, {$form.wght_unit|wunit}{/if}</th>
            <th>Heat / Lot</th>
            <th>Location</th>
            <th>Owner</th>
            <th>Status</th>
            <th width="3%"></th>
        </tr>
        {foreach from=$items item=row}
        <tr class="item-{$row.steelitem.id}{if $row.steelitem.status_id >= $smarty.const.ITEM_STATUS_ORDERED} item-status-{$row.steelitem.status_id}{/if}">
            <td onclick="show_item_context(event, {$row.steelitem.id});">
                {if !empty($row.steelitem.guid)}{$row.steelitem.guid|escape:'html'}{else}&hellip;{/if}
            </td>
            <td onclick="show_item_context(event, {$row.steelitem.id});">
                {if isset($row.steelitem.steelgrade)}{$row.steelitem.steelgrade.title|escape:'html'}{/if}
            </td>
            <td onclick="show_item_context(event, {$row.steelitem.id});">
                {if $form.dim_unit == 'mm'}
                    {$row.steelitem.thickness_mm|number_format:1:true:''|escape:'html'}
                {else}
                    {$row.steelitem.thickness|number_format:1:true:''|escape:'html'}
                {/if}{if isset($multiunits)}&nbsp;{$row.steelitem.dimension_unit}{/if}
            </td>
            <td onclick="show_item_context(event, {$row.steelitem.id});">
                {if $form.dim_unit == 'mm'}
                    {$row.steelitem.width_mm|number_format:1:true:''|escape:'html'}
                {else}
                    {$row.steelitem.width|number_format:1:true:''|escape:'html'}
                {/if}{if isset($multiunits)}&nbsp;{$row.steelitem.dimension_unit}{/if}
            </td>
            <td onclick="show_item_context(event, {$row.steelitem.id});">
                {if $form.dim_unit == 'mm'}
                    {$row.steelitem.length_mm|number_format:1:true:''|escape:'html'}
                {else}
                    {$row.steelitem.length|number_format:1:true:''|escape:'html'}
                {/if}{if isset($multiunits)}&nbsp;{$row.steelitem.dimension_unit}{/if}
            </td>
            <td onclick="show_item_context(event, {$row.steelitem.id});">1</td>
            <td id="item-weight-{$row.steelitem.id}" onclick="show_item_context(event, {$row.steelitem.id});">
                {if $form.wght_unit == 'mt'}
                    {$row.steelitem.unitweight_ton|number_format:2:true:''|escape:'html'}
                {else}
                    {$row.steelitem.unitweight|number_format:2:true:''|escape:'html'}
                {/if}{if isset($multiunits)}&nbsp;{$row.steelitem.weight_unit}{/if}
            </td>
            <td onclick="show_item_context(event, {$row.steelitem.id});">{if !empty($row.steelitem.properties.heat_lot)}{$row.steelitem.properties.heat_lot|escape:'html'}{else}&hellip;{/if}</td>            
            <td onclick="show_item_context(event, {$row.steelitem.id});">{if !empty($row.steelitem.stockholder)}{$row.steelitem.stockholder.doc_no|escape:'html'}{if !empty($row.steelitem.stockholder.city)} ({$row.steelitem.stockholder.city.title|escape:'html'}){/if}{else}{''|undef}{/if}</td>
            <td onclick="show_item_context(event, {$row.steelitem.id});">{if !empty($row.steelitem.owner)}{$row.steelitem.owner.title_trade|escape:'html'}{else}{''|undef}{/if}</td>
            <td onclick="show_item_context(event, {$row.steelitem.id});">
                {if empty($row.steelitem.status_title)}
                    {''|undef}
                {else}
                    {$row.steelitem.status_title}
                    {if $row.steelitem.order_id > 0}<br><a href="/order/{$item.steelitem.order_id}">{$row.steelitem.order_id|order_doc_no}</a>{/if}
                {/if}
            </td> 
            <td onclick="if (confirm('Remove Item from QC ?')) { qc_remove_item({if isset($form.id)}{$form.id}{else}0{/if}, {$row.steelitem.id}); return false; }" style="cursor: pointer">
                <img src="/img/icons/cross-small.png" alt="Remove Item from QC" title="Remove Item from QC"/>
                <input type="checkbox" name="item[{$row.steelitem.id}]" class="cb-row-item" checked="checked" value="{$row.steelitem.id}" style="display: none;">
            </td>
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
        <tr class="item-{$row.steelitem.id}{if $row.steelitem.status_id >= $smarty.const.ITEM_STATUS_ORDERED} item-status-{$row.steelitem.status_id}{/if}" onclick="show_item_context(event, {$row.steelitem.id});">
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
        <tr class="top-table" style="height: 25px;">
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
        <tr class="top-table" style="height: 25px;">
            <th>Sample Direction</th>            
            <th>Strength<br>N/mm<sup>2</sup></th>
            <th>Yield Point<br>N/mm<sup>2</sup></th>
            <th>Elongation<br>%</th>
            <th>Sample Direction</th>
            <th>Strength<br>J/cm<sup>2</sup></th>
            <th>Test Temp<br>deg. C</th>
        </tr>
        {foreach from=$items item=row}
        <tr class="item-{$row.steelitem.id}{if $row.steelitem.status_id >= $smarty.const.ITEM_STATUS_ORDERED} item-status-{$row.steelitem.status_id}{/if}" onclick="show_item_context(event, {$row.steelitem.id});">
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
{/if}