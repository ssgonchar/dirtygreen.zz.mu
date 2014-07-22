<table class="form" width="100%">
    <tr>
        <td width="33%" class="text-top">
            <table class="form" style="width: 100%;">
                <tr style="height: 32px;">
                    <td class="form-td-title-b text-top" style="width:170px;">Buyer : </td>
                    <td class="text-top">{if !empty($ddt.buyer)}{$ddt.buyer|escape:'html'|nl2br}{else}{''|undef}{/if}</td>
                </tr>
                <tr style="height: 32px;">
                    <td class="form-td-title-b text-top">Delivery Point : </td>
                    <td class="text-top">{if !empty($ddt.delivery_point)}{$ddt.delivery_point|escape:'html'|nl2br}{else}{''|undef}{/if}</td>
                </tr>
                <tr style="height: 32px;">
                    <td class="form-td-title-b">Date : </td>
                    <td>{if $ddt.date > 0}{$ddt.date|escape:'html'|date_format:'d/m/Y'}{else}{''|undef}{/if}</td>
                </tr>
                <tr style="height: 32px;">
                    <td class="form-td-title-b">IVA : </td>
                    <td>{if !empty($ddt.iva)}{$ddt.iva|escape:'html'}{else}{''|undef}{/if}</td>
                </tr>
                <tr style="height: 32px;">
                    <td class="form-td-title-b">Payment Term : </td>
                    <td>{if !empty($ddt.paymenttype)}{$ddt.paymenttype.title|escape:'html'}{else}{''|undef}{/if}</td>
                </tr>
            </table>
        </td>
         <td width="33%" class="text-top">
            <table class="form" style="width: 100%;">
                <tr style="height: 32px;">
                    <td class="form-td-title-b" style="width:170px;">Causale Trasporto : </td>
                    <td>{if !empty($ddt.causale)}{$ddt.causale.doc_no|escape:'html'}{else}{''|undef}{/if}</td>
                </tr>
                <tr style="height: 32px;">
                    <td class="form-td-title-b">Porto : </td>
                    <td>{if !empty($ddt.porto)}{$ddt.porto.doc_no|escape:'html'}{else}{''|undef}{/if}</td>
                </tr>
                <tr style="height: 32px;">
                    <td class="form-td-title-b">Truck Number : </td>
                    <td>{if !empty($ddt.truck_number)}{$ddt.truck_number|escape:'html'|nl2br}{else}{''|undef}{/if}</td>
                </tr>
                <tr style="height: 32px;">
                    <td class="form-td-title-b">Transporter : </td>
                    <td>{if !empty($ddt.transporter)}{$ddt.transporter.doc_no|escape:'html'}{else}{''|undef}{/if}</td>
                </tr>
            </table>
        </td>
        <td class="text-top">
            <table class="form" style="width: 100%;">
                <tr style="height: 32px;">
                    <td class="form-td-title-b">Weighed Weight : </td>
                    <td>{$ddt.weighed_weight|string_format:'%.2f'} {$ddt.weight_unit}</td>
                </tr>                
                <tr style="height: 32px;">
                    <td class="form-td-title-b">Max Weight : </td>
                    <td>{if !empty($ddt.total_weight_max)}{$ddt.total_weight_max|string_format:'%.2f'} {$ddt.weight_unit}{/if}</td>
                </tr>
                <tr style="height: 32px;">
                    <td class="form-td-title-b">Pdf : </td>
                    <td>{if $ddt.is_deleted != 1 && isset($ddt.attachment)}<a class="pdf" target="_blank" href="/file/{$ddt.attachment.secret_name}/{$ddt.attachment.original_name}">{$ddt.attachment.original_name}</a>{else}{''|undef}{/if}</td>
                </tr>
            </table>
        </td>
    </tr>
</table>
<div class="pad"></div>

<h3 style="margin-bottom: 10px;" class="ddt-packing-list-title">Packing List</h3>
{if empty($items)}
There are no items
{else}
<span class="ddt-pl-is-empty" style="display: none;">There are no items</span>
<table class="list" style="width: 100%">
    <tr class="top-table" style="height: 25px;">
        <th>Plate id</th>
        <th>Steel Grade</th>
        <th>Thickness,<br />{$ddt.dimension_unit}</th>
        <th>Width,<br />{$ddt.dimension_unit}</th>
        <th>Length,<br />{$ddt.dimension_unit}</th>
        <th>Qtty,<br />pcs</th>
        <th>Weight,<br />{$ddt.weight_unit}</th>
        <th>Weighed Weight,<br />{$ddt.weight_unit}</th>
    </tr>
    {foreach $items as $item}
    <tr iid="{$item.id}" pid="0"{if $item.steelitem.status_id >= $smarty.const.ITEM_STATUS_ORDERED} class="item-status-{$item.steelitem.status_id}"{/if}>
        <td onclick="show_item_context(event, {$item.steelitem.id});">{if empty($item.steelitem.guid)}<span style="font-weight: normal;">{$item.steelitem.doc_no}</span>{else}{$item.steelitem.guid|escape:'html'}{/if}</td>
        <td onclick="show_item_context(event, {$item.steelitem.id});">{if !empty($item.steelitem.steelgrade)}{$item.steelitem.steelgrade.title|escape:'html'}{/if}</td>
        <td onclick="show_item_context(event, {$item.steelitem.id});">{if !empty($item.steelitem.thickness_mm)}{$item.steelitem.thickness_mm|string_format:'%.1f'}{/if}</td>
        <td onclick="show_item_context(event, {$item.steelitem.id});">{if !empty($item.steelitem.width_mm)}{$item.steelitem.width_mm|string_format:'%.0f'}{/if}</td>
        <td onclick="show_item_context(event, {$item.steelitem.id});">{if !empty($item.steelitem.length_mm)}{$item.steelitem.length_mm|string_format:'%.0f'}{/if}</td>
        <td onclick="show_item_context(event, {$item.steelitem.id});">1</td>
        <td onclick="show_item_context(event, {$item.steelitem.id});">{if !empty($item.steelitem.unitweight_ton)}{$item.steelitem.unitweight_ton|escape:'html'|string_format:'%.2f'}{/if}</td>
        <td onclick="show_item_context(event, {$item.steelitem.id});">{if !empty($item.weighed_weight)}{$item.weighed_weight|escape:'html'|string_format:'%.2f'}{/if}</td>
    </tr>
    {/foreach}
</table>
{/if}

<div class="pad"></div>
{include file='templates/controls/object_shared_files.tpl' object_alias='ddt' object_id=$ddt.id}