<table id="t-i" class="list" width="100%">
    <tbody>
        <tr class="top-table{if isset($subclass)} {$subclass}{/if}" style="height: 25px; cursor: text;">
            <th width="10%" rowspan="2">Plate Id</th>
            <th width="8%" rowspan="2">Steel Grade</th>
            <th width="5%" rowspan="2">Thickness{if !isset($multi_dimensions)},<br>{$dimension_unit}{/if}</th>
            <th width="5%" rowspan="2">Width{if !isset($multi_dimensions)},<br>{$dimension_unit}{/if}</th>
            <th width="5%" rowspan="2">Length{if !isset($multi_dimensions)},<br>{$dimension_unit}{/if}</th>
            <th width="7%" rowspan="2">Weight{if !isset($multi_weights)},<br>{$weight_unit|wunit}{/if}</th>
            <th colspan="4" style="border-bottom : 1px solid #B9B9B9;">Measured</th>
            <th width="5%" rowspan="2">Transport Width{if !isset($multi_dimensions)},<br>{$dimension_unit}{/if}</th>
            {if isset($include_nominal) && $include_nominal}
            <th colspan="3" style="border-bottom : 1px solid #B9B9B9;">Nominal</th>
            {/if}
            <th width="7%" rowspan="2">Weighed Weight{if !isset($multi_weights)},<br>{$weight_unit|wunit}{/if}</th>                        
            <th width="5%" rowspan="2">Is Virtual</th>
            <th rowspan="2">System<br>Id</th>
            {if isset($readonly)}
                {if isset($order_actions) && isset($order) && isset($order.status) && $order.status != 'co'}
                <th rowspan="2"><input type="checkbox" onchange="check_all(this, 'position');"></th>
                {/if}
            {else}
            <th rowspan="2" class="items-action-delete"  style="width: 3%;{if isset($eternal)} display: none;{/if}">Delete Item</th>
            {/if}
        </tr>
        <tr class="top-table{if isset($subclass)} {$subclass}{/if}" style="height: 25px; cursor: text;">
            <th width="5%">Thickness{if !isset($multi_dimensions)},<br>{$dimension_unit}{/if}</th>
            <th width="5%">Width{if !isset($multi_dimensions)},<br>{$dimension_unit}{/if}</th>
            <th width="5%">Length{if !isset($multi_dimensions)},<br>{$dimension_unit}{/if}</th>
            <th width="7%">Weight{if !isset($multi_weights)},<br>{$weight_unit|wunit}{/if}</th>
            {if isset($include_nominal) && $include_nominal}
            <th width="5%">Thickness,<br>mm</th>
            <th width="5%">Width,<br>mm</th>
            <th width="5%">Length,<br>mm</th>
            {/if}            
        </tr>        
        {foreach name=i from=$items item=row}
        <tr id="t-i-{$smarty.foreach.i.index + 1}" class="steelitems{if $row.steelitem.status_id >= $smarty.const.ITEM_STATUS_ORDERED} item-status-{$row.steelitem.status_id}{/if}{if !empty($row.steelitem.is_eternal)} stelitems-eternal{/if}{if isset($multi_dimensions)} dunit-{$row.steelitem.dimension_unit}{/if}{if isset($multi_weights)} wunit-{$row.steelitem.weight_unit}{/if}" style="cursor: text;">
        {if isset($readonly) || $row.steelitem.inuse}{* || $row.steelitem.status_id >= $smarty.const.ITEM_STATUS_DELIVERED*}
            <td>
                {if $row.steelitem.inuse}<img src="/img/icons/lock.png" title="In use by {$row.steelitem.inuse_by}" alt="In use by {$row.steelitem.inuse_by}">&nbsp;{/if}{if $row.steelitem.parent_id > 0}alias of {$row.steelitem.parent.doc_no|undef}{else}{$row.steelitem.guid|undef}{/if}
            </td>
            <td>{if isset($row.steelitem.steelgrade)}{$row.steelitem.steelgrade.title|escape:'html'}{else}{''|undef}{/if}</td>
            <td>{$row.steelitem.thickness|undef}{if isset($multi_dimensions)}<span class="dim-title">{$row.steelitem.dimension_unit}</span>{/if}</td>
            <td>{$row.steelitem.width|undef}{if isset($multi_dimensions)}<span class="dim-title">{$row.steelitem.dimension_unit}</span>{/if}</td>
            <td>{$row.steelitem.length|undef}{if isset($multi_dimensions)}<span class="dim-title">{$row.steelitem.dimension_unit}</span>{/if}</td>
            <td>{$row.steelitem.unitweight|number_format:2:true:''|undef}{if isset($multi_weights)}<span class="dim-title">{$row.steelitem.weight_unit|wunit}</span>{/if}</td>
            <td>
                {if $row.steelitem.parent_id > 0}
                    {$row.steelitem.parent.thickness_measured|undef}
                {else}
                    {$row.steelitem.thickness_measured|undef}
                {/if}{if isset($multi_dimensions)}<span class="dim-title">{$row.steelitem.dimension_unit}</span>{/if}
            </td>
            <td>
                {if $row.steelitem.parent_id > 0}
                    {$row.steelitem.parent.width_measured|undef}
                {else}
                    {$row.steelitem.width_measured|undef}
                {/if}{if isset($multi_dimensions)}<span class="dim-title">{$row.steelitem.dimension_unit}</span>{/if}
            </td>
            <td>
                {if $row.steelitem.parent_id > 0}
                    {$row.steelitem.parent.length_measured|undef}
                {else}
                    {$row.steelitem.length_measured|undef}
                {/if}{if isset($multi_dimensions)}<span class="dim-title">{$row.steelitem.dimension_unit}</span>{/if}
            </td>
            <td>
                {if $row.steelitem.parent_id > 0}
                    {$row.steelitem.parent.unitweight_measured|number_format:2:true:''|undef}
                {else}
                    {$row.steelitem.unitweight_measured|number_format:2:true:''|undef}
                {/if}{if isset($multi_weights)}<span class="dim-title">{$row.steelitem.weight_unit|wunit}</span>{/if}
            </td>
            <td>
                {if $row.steelitem.parent_id > 0}
                    {$row.steelitem.parent.width_max|undef}
                {else}
                    {$row.steelitem.width_max|undef}
                {/if}{if isset($multi_dimensions)}<span class="dim-title">{$row.steelitem.dimension_unit}</span>{/if}
            </td>
            {if isset($include_nominal) && $include_nominal}
            <td>
                {if $row.steelitem.parent_id > 0}
                    {if $row.steelitem.parent.dimension_unit == 'in'}{$row.steelitem.parent.nominal_thickness_mm|number_format:1:true:''}{else}<i style="color:#999;">not used</i>{/if}
                {else}
                    {if $row.steelitem.dimension_unit == 'in'}{$row.steelitem.nominal_thickness_mm|number_format:1:true:''}{else}<i style="color:#999;">not used</i>{/if}
                {/if}            
            </td>
            <td>
                {if $row.steelitem.parent_id > 0}
                    {if $row.steelitem.parent.dimension_unit == 'in'}{$row.steelitem.parent.nominal_width_mm|number_format:0:true:''}{else}<i style="color:#999;">not used</i>{/if}
                {else}
                    {if $row.steelitem.dimension_unit == 'in'}{$row.steelitem.nominal_width_mm|number_format:0:true:''}{else}<i style="color:#999;">not used</i>{/if}
                {/if}            
            </td>
            <td>
                {if $row.steelitem.parent_id > 0}
                    {if $row.steelitem.parent.dimension_unit == 'in'}{$row.steelitem.parent.nominal_length_mm|number_format:0:true:''}{else}<i style="color:#999;">not used</i>{/if}
                {else}
                    {if $row.steelitem.dimension_unit == 'in'}{$row.steelitem.nominal_length_mm|number_format:0:true:''}{else}<i style="color:#999;">not used</i>{/if}
                {/if}            
            </td>
            {/if}
            <td>
                {if $row.steelitem.parent_id > 0}
                    {$row.steelitem.parent.unitweight_weighed|number_format:2:true:''|undef}
                {else}
                    {$row.steelitem.unitweight_weighed|number_format:2:true:''|undef}
                {/if}{if isset($multi_weights)}<span class="dim-title">{$row.steelitem.weight_unit|wunit}</span>{/if}
            </td>
            <td>{if !empty($row.steelitem.is_virtual)}yes{else}no{/if}</td>
            <td>{$row.steelitem.id}</td>
            {if isset($readonly)}
                {if isset($order_actions) && isset($order) && isset($order.status) && $order.status != 'co'}
                    {if (empty($row.steelitem.order_id) || $row.steelitem.order_id == $order.id)}
                    <td>
                        <input type="checkbox" name="selected_items[{$row.steelitem.id}]" value="{$row.steelitem.id}" class="cb-row-position"{if $row.steelitem.order_id == $order.id} checked="checked"{/if}>
                    </td>
                    {else}
                    <td></td>
                    {/if}
                {/if}
            {else}
            <td class="items-action-delete"{if isset($eternal)} style="display: none;"{/if}></td>
            {/if}
        {else}
            <td>
                {if $row.steelitem.parent_id > 0}
                    alias of {$row.steelitem.parent.doc_no|undef}
                {else}
                    <input type="text" id="guid-{$smarty.foreach.i.index + 1}" name="item[{$smarty.foreach.i.index + 1}][guid]" value="{if !empty($row.steelitem.guid)}{$row.steelitem.guid|escape:'html'}{/if}" style="width: 100%;" onkeyup="item_plateid_change({$smarty.foreach.i.index + 1});">
                {/if}
                <input type="hidden" id="t-i-deleted-{$smarty.foreach.i.index + 1}" name="item[{$smarty.foreach.i.index + 1}][is_deleted]" value="{if isset($row.steelitem.is_deleted)}{$row.steelitem.is_deleted|escape:'html'}{else}0{/if}">
            </td>
            <td>
                <select name="item[{$smarty.foreach.i.index + 1}][steelgrade_id]" style="width: 100%;">
                    <option value="0">--</option>
                    {foreach from=$steelgrades item=sgrow}
                    <option value="{$sgrow.steelgrade.id}"{if $row.steelitem.steelgrade_id == $sgrow.steelgrade.id} selected="selected"{/if}>{$sgrow.steelgrade.title|escape:'html'}</option>
                    {/foreach}                    
                </select>
                <input type="hidden" name="item[{$smarty.foreach.i.index + 1}][dimension_unit]" value="{if isset($row.steelitem.dimension_unit)}{$row.steelitem.dimension_unit|escape:'html'}{/if}">
                <input type="hidden" name="item[{$smarty.foreach.i.index + 1}][weight_unit]" value="{if isset($row.steelitem.weight_unit)}{$row.steelitem.weight_unit|escape:'html'}{/if}">
                <input type="hidden" name="item[{$smarty.foreach.i.index + 1}][price_unit]" value="{if isset($row.steelitem.price_unit)}{$row.steelitem.price_unit|escape:'html'}{/if}">
                <input type="hidden" name="item[{$smarty.foreach.i.index + 1}][currency]" value="{if isset($row.steelitem.currency)}{$row.steelitem.currency|escape:'html'}{/if}">
            </td>
            <td{if isset($multi_dimensions)} class="dunit"{/if}><input type="text" id="i-thickness-{$smarty.foreach.i.index + 1}" name="item[{$smarty.foreach.i.index + 1}][thickness]" value="{if !empty($row.steelitem.thickness)}{$row.steelitem.thickness|number_format:2:true:''|escape:'html'}{/if}" style="width: 100%;" onkeyup="calc_unitweight({$smarty.foreach.i.index + 1}, '{$row.steelitem.dimension_unit}', '{$row.steelitem.weight_unit}', 'i');"></td>
            <td{if isset($multi_dimensions)} class="dunit"{/if}><input type="text" id="i-width-{$smarty.foreach.i.index + 1}" name="item[{$smarty.foreach.i.index + 1}][width]" value="{if !empty($row.steelitem.width)}{$row.steelitem.width|escape:'html'}{/if}" style="width: 100%;" onkeyup="calc_unitweight({$smarty.foreach.i.index + 1}, '{$row.steelitem.dimension_unit}', '{$row.steelitem.weight_unit}', 'i');"></td>
            <td{if isset($multi_dimensions)} class="dunit"{/if}><input type="text" id="i-length-{$smarty.foreach.i.index + 1}" name="item[{$smarty.foreach.i.index + 1}][length]" value="{if !empty($row.steelitem.length)}{$row.steelitem.length|escape:'html'}{/if}" style="width: 100%;" onkeyup="calc_unitweight({$smarty.foreach.i.index + 1}, '{$row.steelitem.dimension_unit}', '{$row.steelitem.weight_unit}', 'i');"></td>
            <td{if isset($multi_weights)} class="wunit"{/if}><input type="text" id="i-unitweight-{$smarty.foreach.i.index + 1}" name="item[{$smarty.foreach.i.index + 1}][unitweight]" value="{if !empty($row.steelitem.unitweight)}{$row.steelitem.unitweight|number_format:2:true:''|escape:'html'}{/if}" style="width: 100%;"></td>
            <td{if isset($multi_dimensions)} class="dunit"{/if}>
                {if $row.steelitem.parent_id > 0}
                    {$row.steelitem.parent.thickness_measured|undef}{if isset($multi_dimensions)}<span class="dim-title">{$row.steelitem.dimension_unit|wunit}</span>{/if}
                {else}
                    <input type="text" id="measured-thickness-{$smarty.foreach.i.index + 1}" name="item[{$smarty.foreach.i.index + 1}][thickness_measured]" value="{if !empty($row.steelitem.thickness_measured)}{$row.steelitem.thickness_measured|number_format:1:true:''|escape:'html'}{/if}" style="width: 100%;" onkeyup="calc_unitweight({$smarty.foreach.i.index + 1}, '{$row.steelitem.dimension_unit}', '{$row.steelitem.weight_unit}', 'measured');">
                {/if}
            </td>
            <td{if isset($multi_dimensions)} class="dunit"{/if}>
                {if $row.steelitem.parent_id > 0}
                    {$row.steelitem.parent.width_measured|undef}{if isset($multi_dimensions)}<span class="dim-title">{$row.steelitem.dimension_unit|wunit}</span>{/if}
                {else}
                    <input type="text" id="measured-width-{$smarty.foreach.i.index + 1}" name="item[{$smarty.foreach.i.index + 1}][width_measured]" value="{if !empty($row.steelitem.width_measured)}{$row.steelitem.width_measured|escape:'html'}{/if}" style="width: 100%;" onkeyup="calc_unitweight({$smarty.foreach.i.index + 1}, '{$row.steelitem.dimension_unit}', '{$row.steelitem.weight_unit}', 'measured');">
                {/if}            
            </td>
            <td{if isset($multi_dimensions)} class="dunit"{/if}>
                {if $row.steelitem.parent_id > 0}
                    {$row.steelitem.parent.length_measured|undef}{if isset($multi_dimensions)}<span class="dim-title">{$row.steelitem.dimension_unit|wunit}</span>{/if}
                {else}
                    <input type="text" id="measured-length-{$smarty.foreach.i.index + 1}" name="item[{$smarty.foreach.i.index + 1}][length_measured]" value="{if !empty($row.steelitem.length_measured)}{$row.steelitem.length_measured|escape:'html'}{/if}" style="width: 100%;" onkeyup="calc_unitweight({$smarty.foreach.i.index + 1}, '{$row.steelitem.dimension_unit}', '{$row.steelitem.weight_unit}', 'measured');">
                {/if}
            </td>
            <td{if isset($multi_weights)} class="wunit"{/if}>
                {if $row.steelitem.parent_id > 0}
                    {$row.steelitem.parent.unitweight_measured|undef}{if isset($multi_weights)}<span class="dim-title">{$row.steelitem.weight_unit|wunit}</span>{/if}
                {else}
                    <input type="text" id="measured-unitweight-{$smarty.foreach.i.index + 1}" name="item[{$smarty.foreach.i.index + 1}][unitweight_measured]" value="{if $row.steelitem.unitweight_measured > 0}{$row.steelitem.unitweight_measured|number_format:2:true:''|escape:'html'}{/if}" style="width: 100%;">
                {/if}
            </td>
            <td{if isset($multi_dimensions)} class="dunit"{/if}>
                {if $row.steelitem.parent_id > 0}
                    {$row.steelitem.parent.width_max|undef}{if isset($multi_dimensions)}<span class="dim-title">{$row.steelitem.dimension_unit|wunit}</span>{/if}
                {else}
                    <input type="text" name="item[{$smarty.foreach.i.index + 1}][width_max]" value="{if !empty($row.steelitem.width_max)}{$row.steelitem.width_max|escape:'html'}{/if}" style="width: 100%;">
                {/if}
            </td>
            {if isset($include_nominal) && $include_nominal}
            <td>
                {if $row.steelitem.parent_id > 0}
                    {if $row.steelitem.parent.dimension_unit == 'in'}{$row.steelitem.parent.nominal_thickness_mm|number_format:1:true:''|escape:'html'}{else}<i style="color:#999;">not used</i>{/if}
                {else}
                    {if $row.steelitem.dimension_unit == 'in'}<input type="text" name="item[{$smarty.foreach.i.index + 1}][nominal_thickness_mm]" value="{if $row.steelitem.nominal_thickness_mm > 0}{$row.steelitem.nominal_thickness_mm|number_format:1:true:''|escape:'html'}{/if}" style="width: 100%;">{else}<i style="color:#999;">not used</i>{/if}
                {/if}            
            </td>
            <td>
                {if $row.steelitem.parent_id > 0}
                    {if $row.steelitem.parent.dimension_unit == 'in'}{$row.steelitem.parent.nominal_width_mm|number_format:1:true:''|escape:'html'}{else}<i style="color:#999;">not used</i>{/if}
                {else}
                    {if $row.steelitem.dimension_unit == 'in'}<input type="text" name="item[{$smarty.foreach.i.index + 1}][nominal_width_mm]" value="{if $row.steelitem.nominal_width_mm > 0}{$row.steelitem.nominal_width_mm|number_format:1:true:''|escape:'html'}{/if}" style="width: 100%;">{else}<i style="color:#999;">not used</i>{/if}
                {/if}            
            </td>
            <td>
                {if $row.steelitem.parent_id > 0}
                    {if $row.steelitem.parent.dimension_unit == 'in'}{$row.steelitem.parent.nominal_length_mm|number_format:1:true:''|escape:'html'}{else}<i style="color:#999;">not used</i>{/if}
                {else}
                    {if $row.steelitem.dimension_unit == 'in'}<input type="text" name="item[{$smarty.foreach.i.index + 1}][nominal_length_mm]" value="{if $row.steelitem.nominal_length_mm > 0}{$row.steelitem.nominal_length_mm|number_format:1:true:''|escape:'html'}{/if}" style="width: 100%;">{else}<i style="color:#999;">not used</i>{/if}
                {/if}            
            </td>            
            {/if}
            <td{if isset($multi_weights)} class="wunit"{/if}>
                {if $row.steelitem.parent_id > 0}
                    {$row.steelitem.parent.unitweight_weighed|undef}{if isset($multi_weights)}<span class="dim-title">{$row.steelitem.weight_unit|wunit}</span>{/if}
                {else}
                    <input type="text" name="item[{$smarty.foreach.i.index + 1}][unitweight_weighed]" value="{if $row.steelitem.unitweight_weighed > 0}{$row.steelitem.unitweight_weighed|number_format:0:true:''|escape:'html'}{/if}" style="width: 100%;">
                {/if}
            </td>
            <td>
                {if $row.steelitem.parent_id > 0}
                    yes
                {else}
                    <input type="checkbox" id="is_virtual-{$smarty.foreach.i.index + 1}" name="item[{$smarty.foreach.i.index + 1}][is_virtual]" value="1"{if !empty($row.steelitem.is_virtual)} checked="checked"{/if}>
                {/if}
            </td>
            <td>
                {if empty($row.steelitem.id)}*{else}{$row.steelitem.id}{/if}
                <input type="hidden" id="t-i-id-{$smarty.foreach.i.index + 1}" name="item[{$smarty.foreach.i.index + 1}][id]" value="{$row.steelitem.id}">
            </td>
            {if isset($readonly)}
                {if isset($order_actions) && isset($order) && isset($order.status) && $order.status != 'co'}
                    {if empty($row.steelitem.order_id) || $row.steelitem.order_id == $order.id}
                    <td>
                        <input type="checkbox" name="selected_items[{$row.steelitem.id}]" value="{$row.steelitem.id}" class="cb-row-position"{if $row.steelitem.order_id == $order.id} checked="checked"{/if}>
                    </td>
                    {else}
                    <td></td>
                    {/if}
                {/if}
            {else}
            <td class="items-action-delete"{if isset($eternal)} style="display: none;"{/if}>
            {if empty($row.steelitem.is_eternal)}
                <img id="pic-delete-{$smarty.foreach.i.index + 1}" src="/img/icons/cross.png" style="cursor: pointer" onclick="position_item_remove({$smarty.foreach.i.index + 1});">
            {/if}
            </td>
            {/if}
        {/if}
        </tr>        
        {/foreach}
    </tbody>    
</table>
<div class="pad"></div>

<h3>Location</h3>
<table id="t-il" class="list" width="100%">
    <tbody>
        <tr class="top-table{if isset($subclass)} {$subclass}{/if}" style="cursor: text;">
            <th width="10%">Plate Id</th>
            <th>Supplier Invoice</th>
            <th>Incoming DDT</th>
            <th>Outgoing DDT</th>
            <th>Owner</th>
            <th width="25%">Location</th>
            <th width="12%">Status</th>
        </tr>
        {foreach name=i from=$items item=row}
        <tr id="t-il-{$smarty.foreach.i.index + 1}"{if $row.steelitem.status_id >= $smarty.const.ITEM_STATUS_ORDERED} class="item-status-{$row.steelitem.status_id}"{/if} style="cursor: text;">
        {if isset($readonly) || $row.steelitem.inuse}{* || $row.steelitem.status_id >= $smarty.const.ITEM_STATUS_DELIVERED*}
            <td>
                {if $row.steelitem.inuse}<img src="/img/icons/lock.png" title="In use by {$row.steelitem.inuse_by}" alt="In use by {$row.steelitem.inuse_by}">&nbsp;{/if}{if $row.steelitem.parent_id > 0}alias of {$row.steelitem.parent.doc_no|undef}{else}{$row.steelitem.guid|undef}{/if}
            </td>
            <td>
                {if $row.steelitem.parent_id > 0}
                    {if isset($row.steelitem.parent.supplier_invoice)}<a href="/supplierinvoice/{$row.steelitem.parent.supplier_invoice_id}">{$row.steelitem.parent.supplier_invoice.doc_no_full}</a>{else}{''|undef}{/if}
                {else}
                    {if isset($row.steelitem.supplier_invoice)}<a href="/supplierinvoice/{$row.steelitem.supplier_invoice_id}">{$row.steelitem.supplier_invoice.doc_no_full}</a>{else}{''|undef}{/if}                
                {/if}                
            </td>
            <td>
                {if $row.steelitem.parent_id > 0}
                    {if isset($row.steelitem.parent.in_ddt)}<a href="/inddt/{$row.steelitem.parent.in_ddt_id}">{$row.steelitem.parent.in_ddt.doc_no_full}</a>{else}{''|undef}{/if}
                {else}
                    {if isset($row.steelitem.in_ddt)}<a href="/inddt/{$row.steelitem.in_ddt_id}">{$row.steelitem.in_ddt.doc_no_full}</a>{else}{''|undef}{/if}
                {/if}                
            </td>
            <td>
                {if $row.steelitem.parent_id > 0}
                    {if isset($row.steelitem.parent.ddt)}{$row.steelitem.parent.ddt.doc_no_full}{else}{''|undef}{/if}
                {else}
                    {if isset($row.steelitem.ddt)}{$row.steelitem.ddt.doc_no_full}{else}{''|undef}{/if}
                {/if}                
            </td>
            <td>
                {if $row.steelitem.parent_id > 0}
                    {if isset($row.steelitem.parent.owner)}{$row.steelitem.parent.owner.title_trade|escape:'html'}{else}{''|undef}{/if}
                {else}
                    {if isset($row.steelitem.owner)}{$row.steelitem.owner.title_trade|escape:'html'}{else}{''|undef}{/if}
                {/if}                
            </td>
            <td>{if isset($row.steelitem.stockholder)}{$row.steelitem.stockholder.title|escape:'html'}{/if}</td>
            <td{if $row.steelitem.order_id > 0} style="line-height: 16px"{/if}>
                {if empty($row.steelitem.status_id)}{''|undef}
                {else}
                    {$row.steelitem.status_title|escape:'html'}
                    {if $row.steelitem.order_id > 0}<br><a href="/order/{$row.steelitem.order_id}">{$row.steelitem.order_id|order_doc_no}</a>{/if}
                {/if}
            </td>            
        {else}
            <td class="guid-{$smarty.foreach.i.index + 1}">{if $row.steelitem.parent_id > 0}alias of {$row.steelitem.parent.doc_no|undef}{else}{$row.steelitem.guid|undef}{/if}</td>
            <td>
                {if $row.steelitem.parent_id > 0}
                    {if isset($row.steelitem.parent.supplier_invoice)}<a href="/supplierinvoice/{$row.steelitem.parent.supplier_invoice_id}">{$row.steelitem.parent.supplier_invoice.doc_no_full}</a>{else}{''|undef}{/if}
                {else}
                    {if isset($row.steelitem.supplier_invoice)}<a href="/supplierinvoice/{$row.steelitem.supplier_invoice_id}">{$row.steelitem.supplier_invoice.doc_no_full}</a>{else}{''|undef}{/if}                
                {/if}                
            </td>
            <td>
                {if $row.steelitem.parent_id > 0}
                    {if isset($row.steelitem.parent.in_ddt)}<a href="/inddt/{$row.steelitem.parent.in_ddt_id}">{$row.steelitem.parent.in_ddt.doc_no_full}</a>{else}{''|undef}{/if}
                {else}
                    {if isset($row.steelitem.in_ddt)}<a href="/inddt/{$row.steelitem.in_ddt_id}">{$row.steelitem.in_ddt.doc_no_full}</a>{else}{''|undef}{/if}
                {/if}                
            </td>
            <td>
                {if $row.steelitem.parent_id > 0}
                    {if isset($row.steelitem.parent.ddt)}{$row.steelitem.parent.ddt.doc_no_full}{else}{''|undef}{/if}
                {else}
                    {if isset($row.steelitem.ddt)}{$row.steelitem.ddt.doc_no_full}{else}{''|undef}{/if}
                {/if}                
            </td>
            <td>
                {if $row.steelitem.parent_id > 0}
                    {if isset($row.steelitem.parent.owner)}{$row.steelitem.parent.owner.title_trade|escape:'html'}{else}{''|undef}{/if}
                {else}
                    <select name="item[{$smarty.foreach.i.index + 1}][owner_id]" class="max">
                        <option value="0"{if empty($row.steelitem.owner_id)} selected="selected"{/if}>--</option>
                        <option value="{$smarty.const.MAMIT_OWNER_ID}"{if $row.steelitem.owner_id == $smarty.const.MAMIT_OWNER_ID} selected="selected"{/if}>MaM IT</option>
                        <option value="{$smarty.const.MAMUK_OWNER_ID}"{if $row.steelitem.owner_id == $smarty.const.MAMUK_OWNER_ID} selected="selected"{/if}>MaM UK</option>
                        <option value="{$smarty.const.PLATESAHEAD_OWNER_ID}"{if $row.steelitem.owner_id == $smarty.const.PLATESAHEAD_OWNER_ID} selected="selected"{/if}>PlatesAhead</option>
                    </select>                    
                    {* if isset($row.steelitem.owner)}{$row.steelitem.owner.title_trade|escape:'html'}{else}{''|undef}{/if *}
                {/if}                
            </td>
            <td>
                {if empty($row.steelitem.supplier_invoice_id) && empty($row.steelitem.in_ddt_id) && $row.steelitem.status_id <= $smarty.const.ITEM_STATUS_STOCK}
                <select name="item[{$smarty.foreach.i.index + 1}][location_id]" class="max">
                    <option value="0"{if empty($row.steelitem.stockholder_id)} selected="selected"{/if}>--</option>
                    {foreach from=$row.locations item=location}
                    <option value="{$location.company.id}"{if $row.steelitem.stockholder_id == $location.company.id} selected="selected"{/if}>{$location.company.doc_no|escape:'html'} ({if isset($location.company.stocklocation.title)}{$location.company.stocklocation.title|escape:'html'}{/if}{if isset($location.company.city) && (isset($location.company.stocklocation) && $location.company.stocklocation.title != $location.company.city.title)}, {$location.company.city.title}{/if})</option>
                    {/foreach}
                </select>
                {elseif isset($row.steelitem.stockholder)}{$row.steelitem.stockholder.title|escape:'html'}
                {else}{''|undef}{/if}
            </td>
            <td>
                {if $row.steelitem.status_id > $smarty.const.ITEM_STATUS_STOCK}
                    {$row.steelitem.status_title|escape:'html'}
                    {if $row.steelitem.order_id > 0}<br><a href="/order/{$row.steelitem.order_id}">{$row.steelitem.order_id|order_doc_no}</a>{/if}                
                {else}                
                <select name="item[{$smarty.foreach.i.index + 1}][status_id]" style="width: 100%;">
                    <option value="0"{if empty($row.steelitem.status_id)} selected="selected"{/if}>--</option>
                    <option value="{$smarty.const.ITEM_STATUS_PRODUCTION}"{if $row.steelitem.status_id == $smarty.const.ITEM_STATUS_PRODUCTION} selected="selected"{/if}>In Production</option>
                    <option value="{$smarty.const.ITEM_STATUS_TRANSFER}"{if $row.steelitem.status_id == $smarty.const.ITEM_STATUS_TRANSFER} selected="selected"{/if}>Transfer To Stock</option>
                    <option value="{$smarty.const.ITEM_STATUS_STOCK}"{if $row.steelitem.status_id == $smarty.const.ITEM_STATUS_STOCK} selected="selected"{/if}>On Stock</option>
                </select>
                {/if}
            </td>            
        {/if}
        </tr>
        {/foreach}
    </tbody>
</table>
<div class="pad"></div>

<h3>Status</h3>
<table id="t-is" class="list" width="100%">
    <tbody>
        <tr class="top-table{if isset($subclass)} {$subclass}{/if}" style="height: 25px; cursor: text;">
            <th width="10%">Plate Id</th>
            <th width="10%">Mill</th>
            <th width="7%">Purchase Price,<br>per Ton{*$weight_unit|wunit*}</th>
            <th width="7%">Current Cost,<br>{$currency|cursign}/{$weight_unit|wunit}</th>
            <th width="7%">P/L,<br>{$currency|cursign}/{$weight_unit|wunit}</th>
            <th width="7%">Days On Stock</th>
            <th width="10%">Load Ready</th>
            <th>Internal Notes</th>
            <th width="12%">Condition</th>
            <th width="3%">CE Mark</th>
        </tr>
        {foreach name=i from=$items item=row}
        <tr id="t-is-{$smarty.foreach.i.index + 1}"{if $row.steelitem.status_id >= $smarty.const.ITEM_STATUS_ORDERED} class="item-status-{$row.steelitem.status_id}"{/if} style="cursor: text;">
        {if isset($readonly) || $row.steelitem.inuse}{* || $row.steelitem.status_id >= $smarty.const.ITEM_STATUS_DELIVERED*}
            <td>
                {if $row.steelitem.inuse}<img src="/img/icons/lock.png" title="In use by {$row.steelitem.inuse_by}" alt="In use by {$row.steelitem.inuse_by}">&nbsp;{/if}{if $row.steelitem.parent_id > 0}alias of {$row.steelitem.parent.doc_no|undef}{else}{$row.steelitem.guid|undef}{/if}
            </td>
            <td>
                {if $row.steelitem.parent_id > 0}
                    {if !empty($row.steelitem.parent.mill)}{$row.steelitem.parent.mill|escape:'html'}{else}{''|undef}{/if}
                {else}
                    {if !empty($row.steelitem.mill)}{$row.steelitem.mill|escape:'html'}{else}{''|undef}{/if}
                {/if}
            </td>
            <td>
                {if $row.steelitem.parent_id > 0}
                    {if $row.steelitem.parent.purchase_price > 0}{if !empty($row.steelitem.parent.purchase_currency)}{$row.steelitem.parent.purchase_currency|cursign} {/if}{$row.steelitem.parent.purchase_price|string_format:'%.2f'}{else}{''|undef}{/if}
                {else}
                    {if $row.steelitem.purchase_price > 0}{if !empty($row.steelitem.purchase_currency)}{$row.steelitem.purchase_currency|cursign} {/if}{$row.steelitem.purchase_price|string_format:'%.2f'}{else}{''|undef}{/if}
                {/if}
            </td>
            <td>
                {if $row.steelitem.parent_id > 0}
                    {if $row.steelitem.parent.current_cost > 0}{$row.steelitem.parent.current_cost|string_format:'%.2f'}{else}{''|undef}{/if}
                {else}
                    {if $row.steelitem.current_cost > 0}{$row.steelitem.current_cost|string_format:'%.2f'}{else}{''|undef}{/if}
                {/if}
            </td>
            <td>
                {if $row.steelitem.parent_id > 0}
                    {if $row.steelitem.parent.pl > 0}{$row.steelitem.parent.pl|string_format:'%.2f'}{else}{''|undef}{/if}
                {else}
                    {if $row.steelitem.pl > 0}{$row.steelitem.pl|string_format:'%.2f'}{else}{''|undef}{/if}
                {/if}
            </td>
            <td>
                {if $row.steelitem.parent_id > 0}
                    {$row.steelitem.parent.days_on_stock}
                {else}
                    {$row.steelitem.days_on_stock}
                {/if}
            </td>
            <td>
                {if $row.steelitem.parent_id > 0}
                    {if !empty($row.steelitem.parent.load_ready)}{$row.steelitem.parent.load_ready|escape:'html'}{else}{''|undef}{/if}
                {else}
                    {if !empty($row.steelitem.load_ready)}{$row.steelitem.load_ready|escape:'html'}{else}{''|undef}{/if}
                {/if}
            </td>
            <td>
                {if $row.steelitem.parent_id > 0}
                    {$row.steelitem.parent.internal_notes|undef}
                {else}
                    {$row.steelitem.internal_notes|undef}
                {/if}
            </td>
            <td>
                {if $row.steelitem.parent_id > 0}
                    {if $row.steelitem.parent.properties.condition == 'ar'}As Rolled
                    {elseif $row.steelitem.parent.properties.condition == 'n'}Normalized
                    {elseif $row.steelitem.parent.properties.condition == 'nr'}Normalizing Rolling
					{else}{''|undef}{/if}
                {else}
                    {if $row.steelitem.properties.condition == 'ar'}As Rolled
                    {elseif $row.steelitem.properties.condition == 'n'}Normalized
                    {elseif $row.steelitem.properties.condition == 'nr'}Normalizing Rolling
					{else}{''|undef}{/if}
                {/if}
            </td>
            <td>
                {if isset($row.steelitem.is_ce_mark) && $row.steelitem.is_ce_mark > 0}<img src="/img/cemark16.png" title="CE Mark" alt="CE Mark">{/if}
            </td>
        {else}
            <td class="guid-{$smarty.foreach.i.index + 1}">
                {if $row.steelitem.parent_id > 0}
                    alias of {$row.steelitem.parent.doc_no|undef}
                {else}
                    {$row.steelitem.guid|undef}
                {/if}
            </td>
            <td>
                {if $row.steelitem.parent_id > 0}
                    {$row.steelitem.parent.mill|undef}
                {else}
                    <input type="text" name="item[{$smarty.foreach.i.index + 1}][mill]" value="{if !empty($row.steelitem.mill)}{$row.steelitem.mill|escape:'html'}{/if}" style="width: 100%;">
                {/if}
            </td>
            <td nowrap="nowrap">
                {if $row.steelitem.parent_id > 0}
                    {if $row.steelitem.parent.purchase_price > 0}{if !empty($row.steelitem.parent.purchase_currency)}{$row.steelitem.parent.purchase_currency|cursign} {/if}{$row.steelitem.parent.purchase_price|string_format:'%.2f'}{else}{''|undef}{/if}
                {else}
                    <input type="text" name="item[{$smarty.foreach.i.index + 1}][purchase_price]" value="{if $row.steelitem.purchase_price > 0}{$row.steelitem.purchase_price|string_format:'%.2f'}{/if}" style="width: 70px;">
                    <select name="item[{$smarty.foreach.i.index + 1}][purchase_currency]">
                        <option value=""{if $row.steelitem.purchase_price <= 0 || empty($row.steelitem.purchase_currency)} selected="selected"{/if}>-</option>
                        <option value="usd"{if $row.steelitem.purchase_price > 0 && $row.steelitem.purchase_currency == 'usd'} selected="selected"{/if}>$</option>
                        <option value="eur"{if $row.steelitem.purchase_price > 0 && $row.steelitem.purchase_currency == 'eur'} selected="selected"{/if}>&euro;</option>
                        <option value="gbp"{if $row.steelitem.purchase_price > 0 && $row.steelitem.purchase_currency == 'gbp'} selected="selected"{/if}>&pound;</option>
                    </select>
                    {* if $row.steelitem.purchase_price > 0}{if !empty($row.steelitem.purchase_currency)}{$row.steelitem.purchase_currency|cursign} {/if}{$row.steelitem.purchase_price|string_format:'%.2f'}{else}{''|undef}{/if *}
                {/if}
            </td>
            <td>
                {if $row.steelitem.parent_id > 0}
                    {if $row.steelitem.parent.current_cost > 0}{$row.steelitem.parent.current_cost|string_format:'%.2f'}{else}{''|undef}{/if}
                {else}
                    {if $row.steelitem.current_cost > 0}{$row.steelitem.current_cost|string_format:'%.2f'}{else}{''|undef}{/if}
                {/if}
            </td>
            <td>
                {if $row.steelitem.parent_id > 0}
                    {if $row.steelitem.parent.pl > 0}{$row.steelitem.parent.pl|string_format:'%.2f'}{else}{''|undef}{/if}
                {else}
                    <input type="text" name="item[{$smarty.foreach.i.index + 1}][pl]" value="{if $row.steelitem.pl > 0}{$row.steelitem.pl|string_format:'%.2f'}{/if}" style="width: 100%;">
                {/if}
            </td>
            <td>
                {if $row.steelitem.parent_id > 0}
                    {$row.steelitem.parent.days_on_stock}
                {else}
                    {$row.steelitem.days_on_stock}
                {/if}
            </td>
            <td><input type="text" name="item[{$smarty.foreach.i.index + 1}][load_ready]" value="{if !empty($row.steelitem.load_ready)}{$row.steelitem.load_ready|escape:'html'}{/if}" style="width: 100%;"></td>
            <td><input type="text" name="item[{$smarty.foreach.i.index + 1}][internal_notes]" value="{if !empty($row.steelitem.internal_notes)}{$row.steelitem.internal_notes|escape:'html'}{/if}" style="width: 100%;"></td>
            <td>
                <select name="item_property[{$smarty.foreach.i.index + 1}][condition]" style="width: 100%;">
                    <option value="">--</option>
                    <option value="ar"{if $row.steelitem.properties.condition == 'ar'} selected="selected"{/if}>As Rolled</option>
                    <option value="n"{if $row.steelitem.properties.condition == 'n'} selected="selected"{/if}>Normalized</option>
                    <option value="nr"{if $row.steelitem.properties.condition == 'nr'} selected="selected"{/if}>Normalizing Rolling</option>
                </select>
            </td>
            <td><input type="checkbox" name="item[{$smarty.foreach.i.index + 1}][is_ce_mark]" value="1"{if !empty($row.steelitem.is_ce_mark)} checked="checked"{/if}></td>
        {/if}
        </tr>
        {/foreach}
    </tbody>
</table>
<div class="pad"></div>

<h3>Chemical Analysis</h3>
<table id="t-ic" class="list" width="100%">
    <tbody>
        <tr class="top-table{if isset($subclass)} {$subclass}{/if}" style="cursor: text;">
            <th width="10%">Plate Id</th>
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
        {foreach name=i from=$items item=row}
        <tr id="t-ic-{$smarty.foreach.i.index + 1}"{if $row.steelitem.status_id >= $smarty.const.ITEM_STATUS_ORDERED} class="item-status-{$row.steelitem.status_id}"{/if} style="cursor: text;">
        {if isset($readonly) || $row.steelitem.inuse || $row.steelitem.parent_id > 0}{* || $row.steelitem.status_id >= $smarty.const.ITEM_STATUS_DELIVERED*}
            <td>
                {if $row.steelitem.inuse}<img src="/img/icons/lock.png" title="In use by {$row.steelitem.inuse_by}" alt="In use by {$row.steelitem.inuse_by}">&nbsp;{/if}{if $row.steelitem.parent_id > 0}alias of {$row.steelitem.parent.doc_no|undef}{else}{$row.steelitem.guid|undef}{/if}
            </td>
            {if $row.steelitem.parent_id > 0}
                <td>{if !empty($row.steelitem.parent.properties.heat_lot)}{$row.steelitem.parent.properties.heat_lot|escape:'html'}{else}0.000{/if}</td>
                <td>{if $row.steelitem.parent.properties.c != 0}{$row.steelitem.parent.properties.c|escape:'html'}{else}0.000{/if}</td>
                <td>{if $row.steelitem.parent.properties.si != 0}{$row.steelitem.parent.properties.si|escape:'html'}{else}0.000{/if}</td>
                <td>{if $row.steelitem.parent.properties.mn != 0}{$row.steelitem.parent.properties.mn|escape:'html'}{else}0.000{/if}</td>
                <td>{if $row.steelitem.parent.properties.p != 0}{$row.steelitem.parent.properties.p|escape:'html'}{else}0.000{/if}</td>
                <td>{if $row.steelitem.parent.properties.s != 0}{$row.steelitem.parent.properties.s|escape:'html'}{else}0.000{/if}</td>
                <td>{if $row.steelitem.parent.properties.cr != 0}{$row.steelitem.parent.properties.cr|escape:'html'}{else}0.000{/if}</td>
                <td>{if $row.steelitem.parent.properties.ni != 0}{$row.steelitem.parent.properties.ni|escape:'html'}{else}0.000{/if}</td>
                <td>{if $row.steelitem.parent.properties.cu != 0}{$row.steelitem.parent.properties.cu|escape:'html'}{else}0.000{/if}</td>
                <td>{if $row.steelitem.parent.properties.al != 0}{$row.steelitem.parent.properties.al|escape:'html'}{else}0.000{/if}</td>
                <td>{if $row.steelitem.parent.properties.mo != 0}{$row.steelitem.parent.properties.mo|escape:'html'}{else}0.000{/if}</td>
                <td>{if $row.steelitem.parent.properties.nb != 0}{$row.steelitem.parent.properties.nb|escape:'html'}{else}0.000{/if}</td>
                <td>{if $row.steelitem.parent.properties.v != 0}{$row.steelitem.parent.properties.v|escape:'html'}{else}0.000{/if}</td>
                <td>{if $row.steelitem.parent.properties.n != 0}{$row.steelitem.parent.properties.n|escape:'html'}{else}0.000{/if}</td>
                <td>{if $row.steelitem.parent.properties.ti != 0}{$row.steelitem.parent.properties.ti|escape:'html'}{else}0.000{/if}</td>
                <td>{if $row.steelitem.parent.properties.sn != 0}{$row.steelitem.parent.properties.sn|escape:'html'}{else}0.000{/if}</td>
                <td>{if $row.steelitem.parent.properties.b != 0}{$row.steelitem.parent.properties.b|escape:'html'}{else}0.000{/if}</td>
                <td>{if $row.steelitem.parent.properties.ceq != 0}{$row.steelitem.parent.properties.ceq|escape:'html'}{else}0.000{/if}</td>
            {else}
                <td>{if !empty($row.steelitem.properties.heat_lot)}{$row.steelitem.properties.heat_lot|escape:'html'}{else}0.000{/if}</td>
                <td>{if $row.steelitem.properties.c != 0}{$row.steelitem.properties.c|escape:'html'}{else}0.000{/if}</td>
                <td>{if $row.steelitem.properties.si != 0}{$row.steelitem.properties.si|escape:'html'}{else}0.000{/if}</td>
                <td>{if $row.steelitem.properties.mn != 0}{$row.steelitem.properties.mn|escape:'html'}{else}0.000{/if}</td>
                <td>{if $row.steelitem.properties.p != 0}{$row.steelitem.properties.p|escape:'html'}{else}0.000{/if}</td>
                <td>{if $row.steelitem.properties.s != 0}{$row.steelitem.properties.s|escape:'html'}{else}0.000{/if}</td>
                <td>{if $row.steelitem.properties.cr != 0}{$row.steelitem.properties.cr|escape:'html'}{else}0.000{/if}</td>
                <td>{if $row.steelitem.properties.ni != 0}{$row.steelitem.properties.ni|escape:'html'}{else}0.000{/if}</td>
                <td>{if $row.steelitem.properties.cu != 0}{$row.steelitem.properties.cu|escape:'html'}{else}0.000{/if}</td>
                <td>{if $row.steelitem.properties.al != 0}{$row.steelitem.properties.al|escape:'html'}{else}0.000{/if}</td>
                <td>{if $row.steelitem.properties.mo != 0}{$row.steelitem.properties.mo|escape:'html'}{else}0.000{/if}</td>
                <td>{if $row.steelitem.properties.nb != 0}{$row.steelitem.properties.nb|escape:'html'}{else}0.000{/if}</td>
                <td>{if $row.steelitem.properties.v != 0}{$row.steelitem.properties.v|escape:'html'}{else}0.000{/if}</td>
                <td>{if $row.steelitem.properties.n != 0}{$row.steelitem.properties.n|escape:'html'}{else}0.000{/if}</td>
                <td>{if $row.steelitem.properties.ti != 0}{$row.steelitem.properties.ti|escape:'html'}{else}0.000{/if}</td>
                <td>{if $row.steelitem.properties.sn != 0}{$row.steelitem.properties.sn|escape:'html'}{else}0.000{/if}</td>
                <td>{if $row.steelitem.properties.b != 0}{$row.steelitem.properties.b|escape:'html'}{else}0.000{/if}</td>
                <td>{if $row.steelitem.properties.ceq != 0}{$row.steelitem.properties.ceq|escape:'html'}{else}0.000{/if}</td>
            {/if}
        {else}        
            <td class="guid-{$smarty.foreach.i.index + 1}">
                {if !empty($row.steelitem.guid)}{$row.steelitem.guid}{else}{''|undef}{/if}                
            </td>
            <td>
                <input type="text" name="item_property[{$smarty.foreach.i.index + 1}][heat_lot]" value="{if !empty($row.steelitem.properties.heat_lot)}{$row.steelitem.properties.heat_lot|escape:'html'}{/if}" style="width: 100%;">
                <input type="hidden" name="item_property[{$smarty.foreach.i.index + 1}][id]" value="{$row.steelitem.id}">
            </td>
            <td><input type="text" name="item_property[{$smarty.foreach.i.index + 1}][c]" value="{if $row.steelitem.properties.c != 0}{$row.steelitem.properties.c|escape:'html'}{/if}" style="width: 100%;"></td>
            <td><input type="text" name="item_property[{$smarty.foreach.i.index + 1}][si]" value="{if $row.steelitem.properties.si != 0}{$row.steelitem.properties.si|escape:'html'}{/if}" style="width: 100%;"></td>
            <td><input type="text" name="item_property[{$smarty.foreach.i.index + 1}][mn]" value="{if $row.steelitem.properties.mn != 0}{$row.steelitem.properties.mn|escape:'html'}{/if}" style="width: 100%;"></td>
            <td><input type="text" name="item_property[{$smarty.foreach.i.index + 1}][p]" value="{if $row.steelitem.properties.p != 0}{$row.steelitem.properties.p|escape:'html'}{/if}" style="width: 100%;"></td>
            <td><input type="text" name="item_property[{$smarty.foreach.i.index + 1}][s]" value="{if $row.steelitem.properties.s != 0}{$row.steelitem.properties.s|escape:'html'}{/if}" style="width: 100%;"></td>
            <td><input type="text" name="item_property[{$smarty.foreach.i.index + 1}][cr]" value="{if $row.steelitem.properties.cr != 0}{$row.steelitem.properties.cr|escape:'html'}{/if}" style="width: 100%;"></td>
            <td><input type="text" name="item_property[{$smarty.foreach.i.index + 1}][ni]" value="{if $row.steelitem.properties.ni != 0}{$row.steelitem.properties.ni|escape:'html'}{/if}" style="width: 100%;"></td>
            <td><input type="text" name="item_property[{$smarty.foreach.i.index + 1}][cu]" value="{if $row.steelitem.properties.cu != 0}{$row.steelitem.properties.cu|escape:'html'}{/if}" style="width: 100%;"></td>
            <td><input type="text" name="item_property[{$smarty.foreach.i.index + 1}][al]" value="{if $row.steelitem.properties.al != 0}{$row.steelitem.properties.al|escape:'html'}{/if}" style="width: 100%;"></td>
            <td><input type="text" name="item_property[{$smarty.foreach.i.index + 1}][mo]" value="{if $row.steelitem.properties.mo != 0}{$row.steelitem.properties.mo|escape:'html'}{/if}" style="width: 100%;"></td>
            <td><input type="text" name="item_property[{$smarty.foreach.i.index + 1}][nb]" value="{if $row.steelitem.properties.nb != 0}{$row.steelitem.properties.nb|escape:'html'}{/if}" style="width: 100%;"></td>
            <td><input type="text" name="item_property[{$smarty.foreach.i.index + 1}][v]" value="{if $row.steelitem.properties.v != 0}{$row.steelitem.properties.v|escape:'html'}{/if}" style="width: 100%;"></td>
            <td><input type="text" name="item_property[{$smarty.foreach.i.index + 1}][n]" value="{if $row.steelitem.properties.n != 0}{$row.steelitem.properties.n|escape:'html'}{/if}" style="width: 100%;"></td>
            <td><input type="text" name="item_property[{$smarty.foreach.i.index + 1}][ti]" value="{if $row.steelitem.properties.ti != 0}{$row.steelitem.properties.ti|escape:'html'}{/if}" style="width: 100%;"></td>
            <td><input type="text" name="item_property[{$smarty.foreach.i.index + 1}][sn]" value="{if $row.steelitem.properties.sn != 0}{$row.steelitem.properties.sn|escape:'html'}{/if}" style="width: 100%;"></td>
            <td><input type="text" name="item_property[{$smarty.foreach.i.index + 1}][b]" value="{if $row.steelitem.properties.b != 0}{$row.steelitem.properties.b|escape:'html'}{/if}" style="width: 100%;"></td>
            <td><input type="text" name="item_property[{$smarty.foreach.i.index + 1}][ceq]" value="{if $row.steelitem.properties.ceq != 0}{$row.steelitem.properties.ceq|escape:'html'}{/if}" style="width: 100%;"></td>
        {/if}
        </tr>
        {/foreach}
    </tbody>
</table>
<div class="pad"></div>

<h3>Mechanical Properties</h3>
<table id="t-im" class="list" width="100%">
    <tbody>
        <tr class="top-table{if isset($subclass)} {$subclass}{/if}" style="height: 25%; cursor: text;">
            <th width="10%" rowspan="2">Plate Id</th>
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
        <tr class="top-table{if isset($subclass)} {$subclass}{/if}" style="height: 25%; cursor: text;">
            <th>Sample Direction</th>            
            <th>Strength<br>N/mm<sup>2</sup></th>
            <th>Yield Point<br>N/mm<sup>2</sup></th>
            <th>Elongation<br>%</th>
            <th>Sample Direction</th>
            <th>Test Temp<br>deg. C</th>
            <th>Strength<br>J/cm<sup>2</sup></th>
        </tr>
        {foreach name=i from=$items item=row}
        <tr id="t-im-{$smarty.foreach.i.index + 1}"{if $row.steelitem.status_id >= $smarty.const.ITEM_STATUS_ORDERED} class="item-status-{$row.steelitem.status_id}"{/if} style="cursor: text;">
        {if isset($readonly) || $row.steelitem.inuse || $row.steelitem.parent_id > 0}{* || $row.steelitem.status_id >= $smarty.const.ITEM_STATUS_DELIVERED*}
            <td>
                {if $row.steelitem.inuse}<img src="/img/icons/lock.png" title="In use by {$row.steelitem.inuse_by}" alt="In use by {$row.steelitem.inuse_by}">&nbsp;{/if}{if $row.steelitem.parent_id > 0}alias of {$row.steelitem.parent.doc_no|undef}{else}{$row.steelitem.guid|undef}{/if}
            </td>
            {if $row.steelitem.parent_id > 0}            
                <td>{if !empty($row.steelitem.parent.properties.tensile_sample_direction)}{$row.steelitem.parent.properties.tensile_sample_direction|escape:'html'}{/if}</td>
                <td>{if !empty($row.steelitem.parent.properties.tensile_strength)}{$row.steelitem.parent.properties.tensile_strength|escape:'html'}{/if}</td>
                <td>{if !empty($row.steelitem.parent.properties.yeild_point)}{$row.steelitem.parent.properties.yeild_point|escape:'html'}{/if}</td>
                <td>{if $row.steelitem.parent.properties.elongation != 0}{$row.steelitem.parent.properties.elongation|escape:'html'}{/if}</td>
                <td>{if $row.steelitem.parent.properties.reduction_of_area != 0}{$row.steelitem.parent.properties.reduction_of_area|escape:'html'}{/if}</td>
                <td>{if !empty($row.steelitem.parent.properties.sample_direction)}{$row.steelitem.parent.properties.sample_direction|escape:'html'}{/if}</td>
                <td>{if !empty($row.steelitem.parent.properties.test_temp)}{$row.steelitem.parent.properties.test_temp|escape:'html'}{/if}</td>
                <td>{if !empty($row.steelitem.parent.properties.impact_strength)}{$row.steelitem.parent.properties.impact_strength|escape:'html'}{/if}</td>
                <td>{if !empty($row.steelitem.parent.properties.hardness)}{$row.steelitem.parent.properties.hardness|escape:'html'}{/if}</td>
                <td>{if !empty($row.steelitem.parent.properties.ust)}{$row.steelitem.parent.properties.ust|escape:'html'}{/if}</td>
                <td>{if !empty($row.steelitem.parent.properties.stress_relieving_temp)}{$row.steelitem.parent.properties.stress_relieving_temp|escape:'html'}{/if}</td>
                <td>{if !empty($row.steelitem.parent.properties.heating_rate_per_hour)}{$row.steelitem.parent.properties.heating_rate_per_hour|escape:'html'}{/if}</td>
                <td>{if !empty($row.steelitem.parent.properties.holding_time)}{$row.steelitem.parent.properties.holding_time|escape:'html'}{/if}</td>
                <td>{if !empty($row.steelitem.parent.properties.cooling_down_rate)}{$row.steelitem.parent.properties.cooling_down_rate|escape:'html'}{/if}</td>
                <td>{if !empty($row.steelitem.parent.properties.normalizing_temp)}{$row.steelitem.parent.properties.normalizing_temp|escape:'html'}{/if}</td>
            {else}
                <td>{if !empty($row.steelitem.properties.tensile_sample_direction)}{$row.steelitem.properties.tensile_sample_direction|escape:'html'}{else}T{/if}</td>
                <td>{if !empty($row.steelitem.properties.tensile_strength)}{$row.steelitem.properties.tensile_strength|escape:'html'}{/if}</td>
                <td>{if !empty($row.steelitem.properties.yeild_point)}{$row.steelitem.properties.yeild_point|escape:'html'}{/if}</td>
                <td>{if $row.steelitem.properties.elongation != 0}{$row.steelitem.properties.elongation|escape:'html'}{/if}</td>
                <td>{if $row.steelitem.properties.reduction_of_area != 0}{$row.steelitem.properties.reduction_of_area|escape:'html'}{/if}</td>
                <td>{if !empty($row.steelitem.properties.sample_direction)}{$row.steelitem.properties.sample_direction|escape:'html'}{else}L{/if}</td>
                <td>{if !empty($row.steelitem.properties.test_temp)}{$row.steelitem.properties.test_temp|escape:'html'}{/if}</td>
                <td>{if !empty($row.steelitem.properties.impact_strength)}{$row.steelitem.properties.impact_strength|escape:'html'}{/if}</td>
                <td>{if !empty($row.steelitem.properties.hardness)}{$row.steelitem.properties.hardness|escape:'html'}{/if}</td>
                <td>{if !empty($row.steelitem.properties.ust)}{$row.steelitem.properties.ust|escape:'html'}{/if}</td>
                <td>{if !empty($row.steelitem.properties.stress_relieving_temp)}{$row.steelitem.properties.stress_relieving_temp|escape:'html'}{/if}</td>
                <td>{if !empty($row.steelitem.properties.heating_rate_per_hour)}{$row.steelitem.properties.heating_rate_per_hour|escape:'html'}{/if}</td>
                <td>{if !empty($row.steelitem.properties.holding_time)}{$row.steelitem.properties.holding_time|escape:'html'}{/if}</td>
                <td>{if !empty($row.steelitem.properties.cooling_down_rate)}{$row.steelitem.properties.cooling_down_rate|escape:'html'}{/if}</td>
                <td>{if !empty($row.steelitem.properties.normalizing_temp)}{$row.steelitem.properties.normalizing_temp|escape:'html'}{/if}</td>
            {/if}
        {else}
            <td class="guid-{$smarty.foreach.i.index + 1}">{if !empty($row.steelitem.guid)}{$row.steelitem.guid}{else}{''|undef}{/if}</td>
            <td><input type="text" name="item_property[{$smarty.foreach.i.index + 1}][tensile_sample_direction]" value="{if !empty($row.steelitem.properties.tensile_sample_direction)}{$row.steelitem.properties.tensile_sample_direction|escape:'html'}{/if}" style="width: 100%;"></td>
            <td><input type="text" name="item_property[{$smarty.foreach.i.index + 1}][tensile_strength]" value="{if !empty($row.steelitem.properties.tensile_strength)}{$row.steelitem.properties.tensile_strength|escape:'html'}{/if}" style="width: 100%;"></td>
            <td><input type="text" name="item_property[{$smarty.foreach.i.index + 1}][yeild_point]" value="{if !empty($row.steelitem.properties.yeild_point)}{$row.steelitem.properties.yeild_point|escape:'html'}{/if}" style="width: 100%;"></td>
            <td><input type="text" name="item_property[{$smarty.foreach.i.index + 1}][elongation]" value="{if $row.steelitem.properties.elongation != 0}{$row.steelitem.properties.elongation|escape:'html'}{/if}" style="width: 100%;"></td>
            <td><input type="text" name="item_property[{$smarty.foreach.i.index + 1}][reduction_of_area]" value="{if $row.steelitem.properties.reduction_of_area != 0}{$row.steelitem.properties.reduction_of_area|escape:'html'}{/if}" style="width: 100%;"></td>
            <td><input type="text" name="item_property[{$smarty.foreach.i.index + 1}][sample_direction]" value="{if !empty($row.steelitem.properties.sample_direction)}{$row.steelitem.properties.sample_direction|escape:'html'}{/if}" style="width: 100%;"></td>
            <td><input type="text" name="item_property[{$smarty.foreach.i.index + 1}][test_temp]" value="{if !empty($row.steelitem.properties.test_temp)}{$row.steelitem.properties.test_temp|escape:'html'}{/if}" style="width: 100%;"></td>
            <td><input type="text" name="item_property[{$smarty.foreach.i.index + 1}][impact_strength]" value="{if !empty($row.steelitem.properties.impact_strength)}{$row.steelitem.properties.impact_strength|escape:'html'}{/if}" style="width: 100%;"></td>
            <td><input type="text" name="item_property[{$smarty.foreach.i.index + 1}][hardness]" value="{if !empty($row.steelitem.properties.hardness)}{$row.steelitem.properties.hardness|escape:'html'}{/if}" style="width: 100%;"></td>
            <td><input type="text" name="item_property[{$smarty.foreach.i.index + 1}][ust]" value="{if !empty($row.steelitem.properties.ust)}{$row.steelitem.properties.ust|escape:'html'}{/if}" style="width: 100%;"></td>
            <td><input type="text" name="item_property[{$smarty.foreach.i.index + 1}][stress_relieving_temp]" value="{if !empty($row.steelitem.properties.stress_relieving_temp)}{$row.steelitem.properties.stress_relieving_temp|escape:'html'}{/if}" style="width: 100%;"></td>
            <td><input type="text" name="item_property[{$smarty.foreach.i.index + 1}][heating_rate_per_hour]" value="{if !empty($row.steelitem.properties.heating_rate_per_hour)}{$row.steelitem.properties.heating_rate_per_hour|escape:'html'}{/if}" style="width: 100%;"></td>
            <td><input type="text" name="item_property[{$smarty.foreach.i.index + 1}][holding_time]" value="{if !empty($row.steelitem.properties.holding_time)}{$row.steelitem.properties.holding_time|escape:'html'}{/if}" style="width: 100%;"></td>
            <td><input type="text" name="item_property[{$smarty.foreach.i.index + 1}][cooling_down_rate]" value="{if !empty($row.steelitem.properties.cooling_down_rate)}{$row.steelitem.properties.cooling_down_rate|escape:'html'}{/if}" style="width: 100%;"></td>
            <td><input type="text" name="item_property[{$smarty.foreach.i.index + 1}][normalizing_temp]" value="{if !empty($row.steelitem.properties.normalizing_temp)}{$row.steelitem.properties.normalizing_temp|escape:'html'}{/if}" style="width: 100%;"></td>
        {/if}
        </tr>
        {/foreach}
    </tbody>
</table>
