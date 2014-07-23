<table width="100%">
    <tr>
        <td width="33%" class="text-top">
            <table class="form" width="100%">
                <tr style="height: 32px;">
                    <td class="form-td-title-b">Number : </td>
                    <td>{$form.number|undef}</td>
                </tr>
                <tr style="height: 32px;">
                    <td class="form-td-title-b">Date : </td>
                    <td>{if $form.date > 0}{$form.date|date_format:'d/m/Y'}{else}{''|undef}{/if}</td>
                </tr>
                <tr style="height: 32px;">
                    <td class="form-td-title">Due Date : </td>
                    <td>
                        {if $form.due_date > 0}
                        <div style="display:block; width: 100px; padding: 5px 0; {if $form.days_left <= 0}background-color: red; font-weight: bold; color: white;{elseif $form.days_left <= 7}background-color: #FFF750;{else}color: black;{/if}">
                        {$form.due_date|date_format:'d/m/Y'}
                        </div>
                        {else}{''|undef}{/if}
                    </td>
                </tr>
                <tr style="height: 32px;">
                    <td class="form-td-title-b">Company : </td>
                    <td>{if isset($form.company)}{$form.company.doc_no}{else}{''|undef}{/if}</td>
                </tr>
            </table>
        </td>
        <td width="33%" class="text-top">
            <table class="form" width="100%">
                <tr style="height: 32px;">
                    <td class="form-td-title-b">Owner : </td>
                    <td>{$form.owner.title_trade}</td>
                </tr>
                <tr style="height: 32px;">
                    <td class="form-td-title-b">Payment Terms : </td>
                    <td>
                        {if $form.payment_days > 0}
                            {if $form.payment_type == $smarty.const.SUPINVOICE_PAYMENT_IDD}
                                {$form.payment_days}{if $form.payment_days == 1} day{else} days{/if} from Invoice Date
                            {else}
                                {$form.payment_days}{if $form.payment_days == 1} day{else} days{/if} from End Of Month
                            {/if}
                        {else}
                            {''|undef}
                        {/if}
                   </td>
                </tr>
                <tr style="height: 32px;">
                    <td class="form-td-title-b">Amount : </td>
                    <td style="font-weight: bold;">{$form.currency|cursign} {($form.total_amount - $form.amount_paid)|number_format:2:false}</td>
                </tr>                
            </table>
        </td>
        <td width="34%" class="text-top">
            <table class="form" width="100%">
                <tr style="height: 32px;">
                    <td class="form-td-title">Status : </td>
                    <td>{if empty($form.status_title)}{''|undef}{else}{$form.status_title}{/if}</td>
                </tr>
                <tr style="height: 32px;">
                    <td class="form-td-title">Total Amount : </td>
                    <td>{$form.currency|cursign} {$form.total_amount|number_format:2:false}</td>
                </tr>
                <tr id="supinv-amount" style="display: {if isset($form.status_id) && $form.status_id == $smarty.const.SUPINVOICE_STATUS_PPAID}table-row{else}none{/if}; height: 32px;">
                    <td class="form-td-title">Paid Amount : </td>
                    <td>{$form.currency|cursign} {$form.amount_paid|number_format:2:false}</td>
                </tr>
            </table>
        </td>
    </tr>
</table>
                
<div class="pad"></div>

<h3 style="margin-bottom: 10px;" class="packing-list-title">Items</h3>
<span class="packing-list-is-empty" style="display: {if empty($items)}inline{else}none{/if};">There are no items</span>
{if !empty($items)}
<table class="list" style="width: 100%">
    <tr class="top-table" style="height: 25px;">
        <th>id</th>
        <th>Plate id</th>
        <th>Steel Grade</th>
        <th>Thickness,<br>mm</th>
        <th>Width,<br>mm</th>
        <th>Length,<br>mm</th>
        <th>Weight,<br>{'mt'|wunit}</th>
        <th width="100px">Weight Invoiced to Us,<br>{'mt'|wunit}</th>
        <th>Qtty</th>
{*        <th>Price{if isset($firstitem) && !empty($firstitem)},<br />{$firstitem.steelitem.currency|cursign}/{$firstitem.steelitem.weight_unit}{/if}</th>  *}
        <th>Purchase Price,<br /><span id="supinv-currency">{if isset($form.currency) && !empty($form.currency)}{$form.currency|cursign}{else}&euro;{/if}</span>/{'mt'|wunit}</th>
        <th>Purchase Value,<br />{$form.currency|cursign}</th>
        <th>Stockholder</th>
        <th>Owner</th>
        <th>Status</th>        
    </tr>
    {foreach $items as $item}
    <tr class="item{if $item.steelitem.status_id >= $smarty.const.ITEM_STATUS_ORDERED} item-status-{$item.steelitem.status_id}{/if}" data-supplier_invoice_id="{$item.supplier_invoice_id}" data-steelitem_id="{$item.steelitem_id}">
        <td onclick="show_item_context(event, {$item.steelitem.id});">{$item.steelitem.id}</td>
        <td onclick="show_item_context(event, {$item.steelitem.id});">{if empty($item.steelitem.guid)}<span style="font-weight: normal;">{$item.steelitem.doc_no|undef}</span>{else}{$item.steelitem.guid|escape:'html'|undef}{/if}</td>
        <td onclick="show_item_context(event, {$item.steelitem.id});">{if !empty($item.steelitem.steelgrade)}{$item.steelitem.steelgrade.title|escape:'html'|undef}{else}{''|undef}{/if}</td>
        <td onclick="show_item_context(event, {$item.steelitem.id});">{if !empty($item.steelitem.thickness_mm)}{$item.steelitem.thickness_mm|string_format:'%d'}{/if}</td>
        <td onclick="show_item_context(event, {$item.steelitem.id});">{if !empty($item.steelitem.width_mm)}{$item.steelitem.width_mm|string_format:'%d'}{/if}</td>
        <td onclick="show_item_context(event, {$item.steelitem.id});">{if !empty($item.steelitem.length_mm)}{$item.steelitem.length_mm|string_format:'%d'}{/if}</td>
        <td onclick="show_item_context(event, {$item.steelitem.id});">{if !empty($item.steelitem.unitweight)}{$item.steelitem.unitweight_ton|escape:'html'|string_format:'%.2f'}{/if}</td>
        <td onclick="show_item_context(event, {$item.steelitem.id});">{if $item.weight_invoiced > 0}{$item.weight_invoiced|escape:'html'|string_format:'%.2f'}{else}{''|undef}{/if}</td>
        <td>1</td>
{*        <td onclick="show_item_context(event, {$item.steelitem.id});">{if !empty($item.steelitem.price)}{$item.steelitem.price|escape:'html'|string_format:'%.2f'}{/if}</td>  *}
        <td onclick="show_item_context(event, {$item.steelitem.id});">{if $item.steelitem.purchase_price > 0}{$item.steelitem.purchase_price|number_format:2:false}{else}{''|undef}{/if}</td>
        <td onclick="show_item_context(event, {$item.steelitem.id});" style="font-weight: bold;">{if $item.steelitem.purchase_price > 0 && $item.weight_invoiced > 0}{($item.weight_invoiced * $item.steelitem.purchase_price)|number_format:2:false}{else}{''|undef}{/if}</td>
        <td onclick="show_item_context(event, {$item.steelitem.id});">{if !empty($item.steelitem.stockholder)}{$item.steelitem.stockholder.doc_no|escape:'html'}{if !empty($item.steelitem.stockholder.city)} ({$item.steelitem.stockholder.city.title|escape:'html'}){/if}{/if}</td>
        <td onclick="show_item_context(event, {$item.steelitem.id});">{if !empty($item.steelitem.owner)}{$item.steelitem.owner.title_trade|escape:'html'}{/if}</td>
        {if $item.steelitem.order_id > 0}
        <td>{$item.steelitem.status_title}<br><a href="/order/{$item.steelitem.order_id}">{$item.steelitem.order_id|order_doc_no}</a></td>
        {else}
        <td onclick="show_item_context(event, {$item.steelitem.id});">{$item.steelitem.status_title}</td>        
        {/if}
    </tr>
    {/foreach}
</table>
{/if}

<div class="pad"></div>
<table width="100%">
    <tr>
        <td width="33%" class="text-top">
            <table class="form" width="100%">
                <tr style="height: 32px;">
                    <td class="form-td-title-b text-top">Notes : </td>
                    <td class="text-top">{$form.notes|undef|nl2br}</td>
                </tr>
            </table>
        </td>
    </tr>
</table>

<div class="pad"></div>
{include file='templates/controls/object_shared_files.tpl' object_alias='supplierinvoice' object_id=$form.id}
