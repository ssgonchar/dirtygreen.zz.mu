<table class="form" width="100%">
    <tr>
        <td width="25%" style="vertical-align: top;">
            <table class="form" width="100%">
                <tr height="32">
                    <td class="form-td-title-b">Order Id : </td>
                    <td><a href="/order/view/{$order.id}">{$order.number}</a></td>
                </tr>
                <tr height="32">
                    <td class="form-td-title-b">Order for : </td>
                    <td>{if isset($order.order_for_title)}{$order.order_for_title|escape:'html'}{else}<i>not set</i>{/if}</td>
                </tr>
                <tr height="32">
                    <td class="form-td-title-b">BIZ : </td>
                    <td>{if isset($order.biz)}{$order.biz.number_output}{else}<i>not set</i>{/if}</td>
                </tr>
                <tr height="32">
                    <td class="form-td-title-b">Order Status : </td>
                    <td{if !empty($order.status)} class="tr-order-{$order.status}"{/if}>{if isset($order.status_title)}{$order.status_title}{else}<i>Unregistered</i>{/if}</td>
                </tr>                
            </table>
        </td>
        <td width="25%" style="vertical-align: top;">
            <table class="form" width="100%">
                <tr height="32">
                    <td class="form-td-title-b">Buyer Company : </td>
                    <td>{if isset($order.company)}{$order.company.title|escape:'html'}{else}<i>not set</i>{/if}</td>
                </tr>                
                <tr height="32">
                    <td class="form-td-title-b">Person : </td>
                    <td>{* if isset($order.person)}{$order.person.full_name|escape:'html'}{else}<i>not set</i>{/if *}
                        <select name="form[person_id]" class="max">
                            <option value="0">--</option>
                            {foreach from=$persons item=row}
                            <option value="{$row.person.id}"{if isset($form.person_id) && $row.person.id == $form.person_id} selected="selected"{/if}>{$row.person.full_name|escape:'html'}</option>
                            {/foreach}                            
                        </select>
                    </td>
                </tr>
                <tr height="32">
                    <td class="form-td-title-b">Buyer Ref. : </td>
                    <td>{if !empty($order.buyer_ref)}{$order.buyer_ref|escape:'html'}{else}<i>not set</i>{/if}</td>
                </tr>
{* deprecated 20120424, zharkov
                <tr height="32">
                    <td class="form-td-title-b">Supplier Ref. : </td>
                    <td>{if !empty($order.supplier_ref)}{$order.supplier_ref|escape:'html'}{else}<i>not set</i>{/if}</td>
                </tr>
*}                
            </table>        
        </td>
        <td width="25%" valign="top" style="vertical-align: top;">
            <table class="form" width="100%">
                <tr height="32">
                    <td class="form-td-title-b">Delivery Basis : </td>
                    <td>{if isset($order.delivery_point_title)}{$order.delivery_point_title|escape:'html'}{else}<i>not set</i>{/if}</td>
                </tr>
                <tr height="32">
                    <td class="form-td-title-b">Invoicing Basis : </td>
                    <td>{if isset($order.invoicingtype)}{$order.invoicingtype.title|escape:'html'}{else}<i>not set</i>{/if}</td>
                </tr>                
                <tr height="32">
                    <td class="form-td-title-b">Payment Term : </td>
                    <td>{if isset($order.paymenttype)}{$order.paymenttype.title|escape:'html'}{else}<i>not set</i>{/if}</td>
                </tr>
            </table>
        </td>        
        <td width="25%" valign="top" style="vertical-align: top;">
        </td>        
    </tr>
</table>
<div class="pad"></div>

{if !empty($positions)}
<table id="positions" class="list" width="100%"><tbody>
    <tr class="top-table">
        <th width="5%" class="text-left">
            <input type="checkbox" onchange="check_all(this, 'position'); calc_total('position');" style="margin-right: 5px;"{if $total_qtty == $order.quick.qtty} checked="checked"{/if}>ID
        </th>
        <th width="8%">Steel Grade</th>
        <th>Thickness,<br><span class="lbl-dim">{if isset($order.dimension_unit)}{$order.dimension_unit|escape:'html'}{/if}</span></th>
        <th>Width,<br><span class="lbl-dim">{if isset($order.dimension_unit)}{$order.dimension_unit|escape:'html'}{/if}</span></th>
        <th>Length,<br><span class="lbl-dim">{if isset($order.dimension_unit)}{$order.dimension_unit|escape:'html'}{/if}</span></th>
        <th>Unit Weight,<br><span class="lbl-wgh">{if isset($order.weight_unit)}{$order.weight_unit|wunit}{/if}</span></th>
        <th>Qtty,<br>pcs</th>
        <th>Weight,<br><span class="lbl-wgh">{if isset($order.weight_unit)}{$order.weight_unit|wunit}{/if}</span></th>
        <th>Price,<br><span class="lbl-price">{if isset($order.currency)}{$order.currency|cursign}{/if}/{if isset($order.price_unit)}{$order.price_unit|wunit}{/if}</span></th>
        <th>Value,<br><span class="lbl-cur">{if isset($order.currency)}{$order.currency|cursign}{/if}</span></th>
    </tr>
    {foreach from=$positions item=row}
    <tr>
        {*<td class="text-center"><a href="javascript: void(0);" onclick="show_object_block(this, {$row.position_id}, {$order.id});">{$row.position_id}</a></td>*}
        <td class="text-left">
            <input type="checkbox" name="position[{$row.position_id}]" value="{$row.position_id}" class="cb-row-position" onchange="calc_total('position');" style="margin-right: 5px;"{if $row.checked} checked="checked"{/if}><a href="/order/selectitems/{$order.id}/position:{$row.position_id}">{$row.position_id}</a>
        </td>
        <td>{$row.steelgrade.title|escape:'html'}</td>
        <td>{if isset($row.thickness)}{$row.thickness|escape:'html'|escape:'html'}{/if}</td>
        <td>{if isset($row.width)}{$row.width|escape:'html'|escape:'html'}{/if}</td>
        <td>{if isset($row.length)}{$row.length|escape:'html'|escape:'html'}{/if}</td>
        <td>{if isset($row.unitweight)}{$row.unitweight|escape:'html'|string_format:'%.2f'|escape:'html'}{/if}</td>
        <td id="position-qtty-{$row.position_id}">{if isset($row.qtty)}{$row.qtty|escape:'html'|string_format:'%d'|escape:'html'}{/if}</td>
        <td id="position-weight-{$row.position_id}">{if isset($row.weight)}{$row.weight|escape:'html'|string_format:'%.2f'|escape:'html'}{/if}</td>
        <td>{if isset($row.price)}{$row.price|escape:'html'|string_format:'%.2f'|escape:'html'}{/if}</td>
        <td id="position-value-{$row.position_id}">{if isset($row.value)}{$row.value|escape:'html'|string_format:'%.2f'|escape:'html'}{/if}</td>
    </tr>        
    {/foreach}
</tbody></table>
{else}
<span id="lbl-positions"{if !empty($positions)} style="display: none;"{/if}>No position in order</span>
{/if}
<div class="pad"></div>
<table class="form" width="100%">
    <tr>
        <td class="form-td-title-b text-top">{if isset($order) && ($order.delivery_point == 'col' || $order.delivery_point == 'exw' || $order.delivery_point == 'fca')}Collection Address{else}Delivery Point{/if}</td>
        <td class="text-center text-top" width="5px"> : </td>
        <td>
            <textarea name="form[delivery_point]" class="max" rows="4">{if isset($form.delivery_point) && !empty($form.delivery_point)}{$form.delivery_point|escape:'html'}{/if}</textarea>
        </td>
        <td class="form-td-title-b text-top">{if isset($order) && ($order.delivery_point == 'col' || $order.delivery_point == 'exw' || $order.delivery_point == 'fca')}Load Readiness{else}Delivery Time{/if}</td>
        <td class="text-center text-top" width="5px"> : </td>
        <td>
            <textarea name="form[delivery_date]" class="max" rows="4">{if isset($form.delivery_date) && !empty($form.delivery_date)}{$form.delivery_date|escape:'html'}{/if}</textarea>
        </td>
    </tr>
    <tr height="32">
        {if isset($order) && ($order.delivery_point == 'col' || $order.delivery_point == 'exw' || $order.delivery_point == 'fca')}
        <td class="form-td-title-b text-top">Transport Mode</td>
        <td class="text-center text-top" width="5px"> : </td>
        <td>
            <textarea name="form[transport_mode]" class="max" rows="4">{if isset($form.transport_mode) && !empty($form.transport_mode)}{$form.transport_mode|escape:'html'}{/if}</textarea>
        </td>
        {else}
        <td class="form-td-title-b text-top">Delivery Cost</td>
        <td class="text-center text-top" width="5px"> : </td>
        <td class="text-top"><input type="text" name="form[delivery_cost]"{if isset($order.delivery_cost)}{$order.delivery_cost|escape:'html'} value="{$order.delivery_cost|escape:'html'}"{/if} class="max"></td>        
        {/if}
    </tr>
</table>
<div class="pad"></div>

<h3>Special Requirements</h3>
<div class="pad1"></div>
<table class="form" width="100%">
    <tr>
        <td class="text-right text-top" width="120px" style="font-weight: bold;">Chemical Composition</td>
        <td class="text-center text-top" width="5px"> : </td>
        <td>
            <textarea name="form[chemical_composition]" class="max" rows="4">{if isset($form.chemical_composition) && !empty($form.chemical_composition)}{$form.chemical_composition|escape:'html'}{/if}</textarea>
        </td>
        <td class="text-right text-top" width="120px" style="font-weight: bold;">Tolerances on Dimensions</td>
        <td class="text-center text-top" width="5px"> : </td>
        <td>
            <textarea name="form[tolerances]" class="max" rows="4">{if isset($form.tolerances) && !empty($form.tolerances)}{$form.tolerances|escape:'html'}{/if}</textarea>
        </td>
    </tr>
    <tr>
        <td class="text-right text-top" width="120px" style="font-weight: bold;">Hydrogen Control</td>
        <td class="text-center text-top" width="5px"> : </td>
        <td>
            <textarea name="form[hydrogen_control]" class="max" rows="4">{if isset($form.hydrogen_control) && !empty($form.hydrogen_control)}{$form.hydrogen_control|escape:'html'}{/if}</textarea>
        </td>
        <td class="text-right text-top" width="120px" style="font-weight: bold;">Surface Quality</td>
        <td class="text-center text-top" width="5px"> : </td>
        <td>
            <textarea name="form[surface_quality]" class="max" rows="4">{if isset($form.surface_quality) && !empty($form.surface_quality)}{$form.surface_quality|escape:'html'}{/if}</textarea>
        </td>
    </tr>
    <tr>
        <td class="text-right text-top" width="120px" style="font-weight: bold;">Surface Condition</td>
        <td class="text-center text-top" width="5px"> : </td>
        <td>
            <textarea name="form[surface_condition]" class="max" rows="4">{if isset($form.surface_condition) && !empty($form.surface_condition)}{$form.surface_condition|escape:'html'}{/if}</textarea>
        </td>
        <td class="text-right text-top" width="120px" style="font-weight: bold;">Side Edges</td>
        <td class="text-center text-top" width="5px"> : </td>
        <td>
            <textarea name="form[side_edges]" class="max" rows="4">{if isset($form.side_edges) && !empty($form.side_edges)}{$form.side_edges|escape:'html'}{/if}</textarea>
        </td>
    </tr>
    <tr>
        <td class="text-right text-top" width="120px" style="font-weight: bold;">Front & Back Ends</td>
        <td class="text-center text-top" width="5px"> : </td>
        <td>
            <textarea name="form[front_and_back_ends]" class="max" rows="4">{if isset($form.front_and_back_ends) && !empty($form.front_and_back_ends)}{$form.front_and_back_ends|escape:'html'}{/if}</textarea>
        </td>
        <td class="text-right text-top" width="120px" style="font-weight: bold;">Origin</td>
        <td class="text-center text-top" width="5px"> : </td>
        <td>
            <textarea name="form[origin]" class="max" rows="4">{if isset($form.origin) && !empty($form.origin)}{$form.origin|escape:'html'}{/if}</textarea>
        </td>
    </tr>    
    <tr>
        <td class="text-right text-top" width="120px" style="font-weight: bold;">Marking</td>
        <td class="text-center text-top" width="5px"> : </td>
        <td>
            <textarea name="form[marking]" class="max" rows="4">{if isset($form.marking) && !empty($form.marking)}{$form.marking|escape:'html'}{/if}</textarea>
        </td>
        <td class="text-right text-top" width="120px" style="font-weight: bold;">Packing</td>
        <td class="text-center text-top" width="5px"> : </td>
        <td>
            <textarea name="form[packing]" class="max" rows="4">{if isset($form.packing) && !empty($form.packing)}{$form.packing|escape:'html'}{/if}</textarea>
        </td>
    </tr>
    <tr>
        <td class="text-right text-top" width="120px" style="font-weight: bold;">Stamping</td>
        <td class="text-center text-top" width="5px"> : </td>
        <td>
            <textarea name="form[stamping]" class="max" rows="4">{if isset($form.stamping) && !empty($form.stamping)}{$form.stamping|escape:'html'}{/if}</textarea>
        </td>
        <td class="text-right text-top" width="120px" style="font-weight: bold;">UST Standard,<br> class</td>
        <td class="text-center text-top" width="5px"> : </td>
        <td>
            <textarea name="form[ust_standard]" class="max" rows="4">{if isset($form.ust_standard)}{$form.ust_standard|escape:'html'}{/if}</textarea>
        </td>                                                                                                                           
    </tr>
    <tr>
        <td class="text-right text-top" width="120px" style="font-weight: bold;">Dunnaging Requirements</td>
        <td class="text-center text-top" width="5px"> : </td>
        <td>
            <textarea name="form[dunnaging_requirements]" class="max" rows="4">{if isset($form.dunnaging_requirements) && !empty($form.dunnaging_requirements)}{$form.dunnaging_requirements|escape:'html'}{/if}</textarea>
        </td>
        <td class="text-right text-top" width="120px" style="font-weight: bold;">Documents Supplied</td>
        <td class="text-center text-top" width="5px"> : </td>
        <td>
            <textarea name="form[documents_supplied]" class="max" rows="4">{if isset($form.documents_supplied) && !empty($form.documents_supplied)}{$form.documents_supplied|escape:'html'}{/if}</textarea>
        </td>
    </tr>
    <tr>
        <td class="text-right text-top" width="120px" style="font-weight: bold;">Inspection</td>
        <td class="text-center text-top" width="5px"> : </td>
        <td>
            <textarea name="form[inspection]" class="max" rows="4">{if isset($form.inspection) && !empty($form.inspection)}{$form.inspection|escape:'html'}{/if}</textarea>
        </td>
        <td class="text-right text-top" width="120px" style="font-weight: bold;">Delivery Condition</td>
        <td class="text-center text-top" width="5px"> : </td>
        <td>
            <textarea name="form[delivery_form]" class="max" rows="4">{if isset($form.delivery_form) && !empty($form.delivery_form)}{$form.delivery_form|escape:'html'}{/if}</textarea>
        </td>
    </tr>
    <tr>
        <td class="text-right text-top" width="120px" style="font-weight: bold;">Reduction of Area</td>
        <td class="text-center text-top" width="5px"> : </td>
        <td>
            <textarea name="form[reduction_of_area]" class="max" rows="4">{if isset($form.reduction_of_area) && !empty($form.reduction_of_area)}{$form.reduction_of_area|escape:'html'}{/if}</textarea>
        </td>
        <td class="text-right text-top" width="120px" style="font-weight: bold;">Testing</td>
        <td class="text-center text-top" width="5px"> : </td>
        <td>
            <textarea name="form[testing]" class="max" rows="4">{if isset($form.testing) && !empty($form.testing)}{$form.testing|escape:'html'}{/if}</textarea>
        </td>
    </tr>
    <tr>
        <td class="text-right text-top" width="120px" style="font-weight: bold;">Notes</td>
        <td class="text-center text-top" width="5px"> : </td>
        <td>
            <textarea name="form[notes]" class="max" rows="4">{if isset($form.notes) && !empty($form.notes)}{$form.notes|escape:'html'}{/if}</textarea>
        </td>
        <td class="text-right text-top" width="120px" style="font-weight: bold;">Quality Certificate</td>
        <td class="text-center text-top" width="5px"> : </td>
        <td class="text-top">
            <select id="qctype_id" name="form[qctype_id]" class="max" style="margin-bottom: 10px;">
                <option value="0">--</option>
                {foreach from=$qctypes item=row}
                <option value="{$row.qctype.id}"{if isset($form.qctype_id) && $row.qctype.id == $form.qctype_id} selected="selected"{/if}>{$row.qctype.title|escape:'html'}</option>
                {/foreach}                
            </select><br>
            <input type="text" id="qctype_new" name="form[qctype_new]" class="max"{if isset($form.qctype_new) && !empty($form.qctype_new)} value="{$form.qctype_new|escape:'html'}"{/if}>
        </td>
    </tr>    
</table>

