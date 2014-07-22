<table width="100%">
    <tr>
        <td width="33%" class="text-top">
            <table class="form" width="100%">
                {if !empty($form.order_id)}
                <tr style="height: 32px;">
                    <td class="form-td-title">Order : </td>
                    <td><a href="/order/{$form.order_id}">{$form.order_id|order_doc_no}</a></td>
                </tr>
                {/if}
                <tr style="height: 32px;">
                    <td class="form-td-title">Type : </td>
                    <td>
                        {if empty($invoice_id)}
                            <select class="normal invoice-type" name="form[owner_id]">
                                <option value="-1">--</option>
                                <option value="0"{if empty($form.owner_id)} selected="selected"{/if}>IVA</option>
                                {foreach $owners_list as $row}
                                <option value="{$row.company.id}"{if !empty($form.owner_id) && $form.owner_id == $row.company.id} selected="selected"{/if}>{$row.company.title_trade|escape:'html'}</option>
                                {/foreach}
                            </select>                        
                        {else}
                            {$form.owner_name|escape:'html'}
                        {/if}
                    </td>
                </tr>
                <tr style="height: 32px;">
                    <td class="form-td-title">BIZ : </td>
                    <td>
                        {if $form.is_closed == 1}{$form.biz.doc_no_full|escape:'html'}
                        {else}
                        <input id="invoice-biz" class="normal biz-autocomplete" type="text" name="form[biz_title]" {if isset($form.biz)} value="{$form.biz.doc_no_full|escape:'html'}"{/if}>
                        <input id="invoice-biz-id" type="hidden" name="form[biz_id]" value="{if isset($form.biz)}{$form.biz.id}{else}0{/if}">
                        {/if}
                    </td>
                </tr>
                <tr style="height: 32px;">
                    <td class="form-td-title">Customer : </td>
                    <td>
                        {if $form.is_closed == 1}{$form.customer.title|escape:'html'}
                        {else}
                        <select class="normal" id="companies" name="form[customer_id]">
                            <option value="0"{if empty($form.customer_id)} selected="selected"{/if}>--</option>
                            {foreach $customers_list as $row}
                            <option value="{$row.company.id}"{if !empty($form.customer_id) && $form.customer_id == $row.company.id} selected="selected"{/if}>{$row.company.title|escape:'html'}</option>
                            {/foreach}
                        </select>
                        {/if}
                    </td>
                </tr>
            </table>
        </td>
        <td width="34%" class="text-top">
            <table class="form" width="100%">
                <tr style="height: 32px;">
                    <td class="form-td-title">Number : </td>
                    <td>
                        {if $form.is_closed == 1}{$form.number}
                        {else}<input id="invoice-number" class="narrow" type="text" name="form[number]"{if !empty($form.number)} value="{$form.number}"{/if}/>
                        {/if}
                   </td>
                </tr>
                <tr style="height: 32px;">
                    <td class="form-td-title">Date : </td>
                    <td>
                        {if $form.is_closed == 1}{$form.date}
                        {else}<input class="narrow date" type="text" name="form[date]"{if !empty($form.date) && $form.date > 0} value="{$form.date}"{/if} />
                        {/if}
                    </td>
                </tr>
                {if empty($form.is_closed)}
                <tr id="tr-invoice-status"{if !isset($form.number) || empty($form.number)} style="display:none"{/if}>
                    <td class="form-td-title" style="height: 32px;">Invoice Status : </td>
                    <td>
                        <select id="invoice-is-closed" name="form[is_closed]" class="normal">
                            <option value="0"{if !isset($form.number) || empty($form.number)} selected="selected"{/if}>Active, editable, items can be added or removed .</option>
                            <option value="1">Closed, any changes forbidden .</option>
                        </select>
                    </td>
                </tr>                
                {/if}
            </table>
        </td>
        <td width="33%" class="text-top">
            <table class="form" width="100%">
                <tr>
                    <td class="form-td-title">Due Date : </td>
                    <td><input class="narrow date" type="text"  name="form[due_date]"{if !empty($form.due_date) && $form.due_date > 0} value="{$form.due_date}"{/if} /></td>
                </tr>
                <tr>
                    <td class="form-td-title">Payment Status : </td>
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
                    <td><input class="normal" type="text"  name="form[amount_received]"{if !empty($form.amount_received)} value="{$form.amount_received|string_format:'%.2f'}"{/if} /></td>
                </tr>
            </table>
        </td>        
    </tr>
</table>
                
<div class="pad"></div>

<h3 style="margin-bottom: 10px;" class="packing-list-title">Items</h3>
<span class="packing-list-is-empty" style="display: {if empty($steelitems)}inline{else}none{/if};">There are no items</span>
{if !empty($steelitems)}
<table class="list" style="width: 100%">
    <tr class="top-table">
        {if empty($invoice_id)}
        <th style="width: 20px;"><input type="checkbox" class="group-checkbox" rel="packing-list" style="margin: 5px;" checked="checked" /></th>
        {/if}
        <th>id</th>
        <th>Plate id</th>
        <th>Steel Grade</th>
        <th>Thickness,<br />{$steelitems[0].steelitem.dimension_unit}</th>
        <th>Width,<br />{$steelitems[0].steelitem.dimension_unit}</th>
        <th>Length,<br />{$steelitems[0].steelitem.dimension_unit}</th>
        <th>Weight,<br />{$steelitems[0].steelitem.weight_unit|wunit}</th>
        <th>Stockholder</th>
        <th>Owner</th>
        <th>Status</th>        
        {if $invoice_id > 0 && empty($form.is_closed)}
        <th style="width: 20px;"></th>
        {/if}
    </tr>
    {foreach $steelitems as $item}
    <tr class="item{if $item.steelitem.status_id >= $smarty.const.ITEM_STATUS_ORDERED} item-status-{$item.steelitem.status_id}{/if}" data-invoice_id="{$item.steelitem.invoice_id}" data-steelitem_id="{$item.steelitem.id}">
        {if empty($invoice_id)}
        <td>
            <input class="single-checkbox type-id-{$item.steelitem.owner_id}" rel="packing-list" type="checkbox" name="steelitems[]" value="{$item.steelitem.id}"{* if $form.owner_id == 0 || $form.owner_id != $item.steelitem.owner_id} disabled="disabled"{else} checked="checked"{/if *} checked="checked" />
        </td>
        {/if}
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
        {if $invoice_id > 0 && empty($form.is_closed)}
        <td><img class="item-delete" src="/img/icons/cross-small.png" style="cursor: pointer" alt="Delete" title="Delete"/></td>
        {/if}
    </tr>
    {/foreach}
</table>
{/if}