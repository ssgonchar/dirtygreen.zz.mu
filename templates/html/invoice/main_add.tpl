<table>
    <tr>
        <td width="50%" class="text-top">
            <table class="form" width="100%">
                <tr style="height: 32px;">
                    <td class="form-td-title">Order : </td>
                    <td>{if !empty($source_doc.source)}<a href="/order/{$source_doc.source.id}">{$source_doc.source.doc_no_full|escape:'html'}</a>{else}{''|undef}{/if}</td>
                </tr>
                <tr style="height: 32px;">
                    <td class="form-td-title">Type : </td>
                    <td>
                        <select class="wide invoice-type" name="form[owner_id]">
                            <option value="-1">--</option>
                            <option value="0"{if empty($form.owner_id)} selected="selected"{/if}>IVA</option>
                            {foreach $owners_list as $row}
                            <option value="{$row.company.id}"{if !empty($form.owner_id) && $form.owner_id == $row.company.id} selected="selected"{/if}>{$row.company.title_trade|escape:'html'}</option>
                            {/foreach}
                        </select>
                    </td>
                </tr>
                <tr>
                    <td class="form-td-title">BIZ : </td>
                    <td>
                        <input id="invoice-biz" class="wide biz-autocomplete" type="text" name="form[biz_title]" {if isset($form.biz)} value="{$form.biz.doc_no_full|escape:'html'}"{/if}>
                        <input id="invoice-biz-id" type="hidden" name="form[biz_id]" value="{if isset($form.biz)}{$form.biz.id}{else}0{/if}">
                    </td>
                </tr>
                <tr>
                    <td class="form-td-title">Customer : </td>
                    <td>
                        <select class="wide" id="companies" name="form[customer_id]">
                            <option value="0"{if empty($form.customer_id)} selected="selected"{/if}>--</option>
                            {foreach $customers_list as $row}
                            <option value="{$row.company.id}"{if !empty($form.customer_id) && $form.customer_id == $row.company.id} selected="selected"{/if}>{$row.company.title|escape:'html'}</option>
                            {/foreach}
                        </select>
                    </td>
                </tr>
            </table>
        </td>
        <td width="50%" class="text-top">
            <table class="form" width="100%">
{*
                <tr>
                    <td class="form-td-title">Number : </td>
                    <td><input class="wide" type="text" name="form[number]"{if !empty($form.number)} value="{$form.number}"{/if} /></td>
                </tr>
*}
                <tr>
                    <td class="form-td-title">Date : </td>
                    <td><input class="narrow date" type="text" name="form[date]"{if !empty($form.date) && $form.date > 0} value="{$form.date}"{/if} /></td>
                </tr>
                <tr>
                    <td class="form-td-title">Due Date : </td>
                    <td><input class="narrow date" type="text"  name="form[due_date]"{if !empty($form.due_date) && $form.due_date > 0} value="{$form.due_date}"{/if} /></td>
                </tr>
                <tr>
                    <td class="form-td-title">Status : </td>
                    <td>
                        <select class="normal inv-status" name="form[status_id]">
                            <option value="0"{if empty($form.status_id) || $form.status_id <= 0} selected="selected"{/if}>--</option>
                            <option value="1"{if !empty($form.status_id) && $form.status_id == 1} selected="selected"{/if}>Received</option>
                            <option value="2"{if !empty($form.status_id) && $form.status_id == 2} selected="selected"{/if}>Partially Received</option>
                        </select>
                    </td>
                </tr>
                <tr class="inv-amount-received" style="display: {if $form.status_id == 2}table-row{else}none{/if};">
                    <td class="form-td-title">Amount : </td>
                    <td><input class="wide" type="text"  name="form[amount_received]"{if !empty($form.amount_received)} value="{$form.amount_received}"{/if} /></td>
                </tr>
            </table>
        </td>
    </tr>
</table>
                
<div class="pad"></div>

<h3 style="margin-bottom: 10px;" class="packing-list-title">Items</h3>
<span class="packing-list-is-empty" style="display: {if empty($items_list)}inline{else}none{/if};">There are no items</span>
{if !empty($items_list)}
<table class="list" style="width: 100%">
    <tr class="top-table" style="height: 25px;">
        <th style="width: 20px;"><input type="checkbox" class="group-checkbox" rel="packing-list" style="margin: 5px;" checked="checked" /></th>
        <th>id</th>
        <th>Plate id</th>
        <th>Steel Grade</th>
        <th>Thickness,<br />{$items_list[0].steelitem.dimension_unit}</th>
        <th>Width,<br />{$items_list[0].steelitem.dimension_unit}</th>
        <th>Length,<br />{$items_list[0].steelitem.dimension_unit}</th>
        <th>Weight,<br />{$items_list[0].steelitem.weight_unit}</th>
        <th>Stockholder</th>
        <th>Owner</th>
        <th>Status</th>
    </tr>
    {foreach $items_list as $item}
    <tr class="item{if $item.steelitem.status_id >= $smarty.const.ITEM_STATUS_ORDERED} item-status-{$item.steelitem.status_id}{/if}">
        <td>
            {if $item.steelitem.invoice_id <= 0}
            <input class="single-checkbox type-id-{$item.steelitem.owner_id}" rel="packing-list" type="checkbox" name="selected_ids[]" value="{$item.steelitem.id}"{if $item.steelitem.owner_id > 0} disabled="disabled"{/if} checked="checked" />
            {/if}
        </td>
        <td onclick="show_item_context(event, {$item.steelitem.id});">{$item.steelitem.id}</td>
        <td onclick="show_item_context(event, {$item.steelitem.id});">{if empty($item.steelitem.guid)}<span style="font-weight: normal;">{$item.steelitem.doc_no|undef}</span>{else}{$item.steelitem.guid|escape:'html'|undef}{/if}</td>
        <td onclick="show_item_context(event, {$item.steelitem.id});">{if !empty($item.steelitem.steelgrade)}{$item.steelitem.steelgrade.title|escape:'html'|undef}{else}{''|undef}{/if}</td>
        <td onclick="show_item_context(event, {$item.steelitem.id});">{if !empty($item.steelitem.thickness_mm)}{$item.steelitem.thickness_mm|string_format:'%.1f'}{/if}</td>
        <td onclick="show_item_context(event, {$item.steelitem.id});">{if !empty($item.steelitem.width_mm)}{$item.steelitem.width_mm|string_format:'%.0f'}{/if}</td>
        <td onclick="show_item_context(event, {$item.steelitem.id});">{if !empty($item.steelitem.length_mm)}{$item.steelitem.length_mm|string_format:'%.0f'}{/if}</td>
        <td onclick="show_item_context(event, {$item.steelitem.id});">{if !empty($item.steelitem.unitweight_ton)}{$item.steelitem.unitweight_ton|escape:'html'|string_format:'%.2f'}{/if}</td>
        <td onclick="show_item_context(event, {$item.steelitem.id});">{if !empty($item.steelitem.stockholder)}{$item.steelitem.stockholder.doc_no|escape:'html'}{if !empty($item.steelitem.stockholder.city)} ({$item.steelitem.stockholder.city.title|escape:'html'}){/if}{/if}</td>
        <td onclick="show_item_context(event, {$item.steelitem.id});">{if !empty($item.steelitem.owner)}{$item.steelitem.owner.title_trade|escape:'html'}{/if}</td>
        <td onclick="show_item_context(event, {$item.steelitem.id});">
            {$item.steelitem.status_title}
            {if $item.steelitem.order_id > 0}<br><a href="/order/{$item.steelitem.order_id}">{$item.steelitem.order_id|order_doc_no}</a>{/if}
        </td>
    </tr>
    {/foreach}
</table>
{/if}