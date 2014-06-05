<table class="form" width="100%">
    <tr>
        <td width="50%" class="text-top">
            <table class="form" style="width: 100%;">
                <tr style="height: 32px;">
                    <td class="form-td-title-b text-top" style="width:170px;">Number : </td>
                    <td class="text-top">{$form.number|escape:'html'|undef}</td>
                </tr>
                <tr style="height: 32px;">
                    <td class="form-td-title-b">Date : </td>
                    <td>{if $form.date > 0}{$form.date|escape:'html'|date_format:'d/m/Y'}{else}{''|undef}{/if}</td>
                </tr>
                <tr style="height: 32px;">
                    <td class="form-td-title-b">Company : </td>
                    <td>{if !empty($form.company)}{$form.company.doc_no|escape:'html'}{else}{''|undef}{/if}</td>
                </tr>
            </table>
        </td>
        <td class="text-top">
            <table class="form" style="width: 100%;">
                <tr style="height: 32px;">
                    <td class="form-td-title-b">Owner : </td>
                    <td>{if !empty($form.owner)}{$form.owner.title_trade|escape:'html'}{else}{''|undef}{/if}</td>
                </tr>
            </table>
        </td>
    </tr>
</table>
<div class="pad"></div>

<h3 style="margin-bottom: 10px;" class="inddt-packing-list-title">Items</h3>
{if empty($items)}
There are no items
{else}
<table class="list" style="width: 100%">
    <tr class="top-table" style="height: 25px;">
        <th>id</th>
        <th>Plate id</th>
        <th>Steel Grade</th>
        <th>Thickness,<br />{$items.0.steelitem.dimension_unit}</th>
        <th>Width,<br />{$items.0.steelitem.dimension_unit}</th>
        <th>Length,<br />{$items.0.steelitem.dimension_unit}</th>
        <th>Weight,<br />{$items.0.steelitem.weight_unit}</th>
        <th style="width: 175px;">Stockholder</th>
        <th>Supplier Invoice</th>
        <th>Owner</th>
        <th style="width: 175px;">Status</th>
    </tr>
    {foreach $items as $item}
    <tr{if $item.steelitem.status_id >= $smarty.const.ITEM_STATUS_ORDERED} class="item-status-{$item.steelitem.status_id}"{/if}>
        <td onclick="show_item_context(event, {$item.steelitem.id});">{$item.steelitem.id}</td>
        <td onclick="show_item_context(event, {$item.steelitem.id});">{if empty($item.steelitem.guid)}<span style="font-weight: normal;">{$item.steelitem.doc_no|undef}</span>{else}{$item.steelitem.guid|escape:'html'|undef}{/if}</td>
        <td onclick="show_item_context(event, {$item.steelitem.id});">{if !empty($item.steelitem.steelgrade)}{$item.steelitem.steelgrade.title|escape:'html'|undef}{else}{''|undef}{/if}</td>
        <td onclick="show_item_context(event, {$item.steelitem.id});">{if !empty($item.steelitem.thickness_mm)}{$item.steelitem.thickness_mm|string_format:'%.1f'}{/if}</td>
        <td onclick="show_item_context(event, {$item.steelitem.id});">{if !empty($item.steelitem.width_mm)}{$item.steelitem.width_mm|string_format:'%.0f'}{/if}</td>
        <td onclick="show_item_context(event, {$item.steelitem.id});">{if !empty($item.steelitem.length_mm)}{$item.steelitem.length_mm|string_format:'%.0f'}{/if}</td>
        <td onclick="show_item_context(event, {$item.steelitem.id});">{if !empty($item.steelitem.unitweight_ton)}{$item.steelitem.unitweight_ton|escape:'html'|string_format:'%.2f'}{/if}</td>
        <td onclick="show_item_context(event, {$item.steelitem.id});">{if isset($item.stockholder)}{$item.stockholder.doc_no}{/if}</td>
        {if isset($item.steelitem.supplier_invoice)}
        <td><a href="/supplierinvoice/{$item.steelitem.supplier_invoice_id}">{$item.steelitem.supplier_invoice.doc_no_full}</a></td>
        {else}
        <td onclick="show_item_context(event, {$item.steelitem.id});">{''|undef}</td>
        {/if}
        <td onclick="show_item_context(event, {$item.steelitem.id});">
            {if isset($item.steelitem.owner)}{$item.steelitem.owner.title_trade}{else}{''|undef}{/if}
        </td>                
        {if $item.steelitem.order_id > 0}
        <td>{$item.steelitem.status_title}<br><a href="/order/{$item.steelitem.order_id}">{$item.steelitem.order_id|order_doc_no}</a></td>
        {else}
        <td onclick="show_item_context(event, {$item.steelitem.id});">{$item.steelitem.status_title|undef}</td>
        {/if}
    </tr>
    {/foreach}
</table>
{/if}

<div class="pad"></div>
{include file='templates/controls/object_shared_files.tpl' object_alias='inddt' object_id=$form.id}