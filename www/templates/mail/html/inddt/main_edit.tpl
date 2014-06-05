<table class="form" width="100%">
    <tr>
        <td width="50%" class="text-top">
            <table class="form" style="width: 100%;">
                <tr>
                    <td class="form-td-title-b">Number : </td>
                    <td><input type="text" name="form[number]" value="{if isset($form.number)}{$form.number|escape:'html'}{/if}" class="normal" maxlength="50" /></td>
                </tr>
                <tr>
                    <td class="form-td-title-b">Date : </td>
                    <td><input type="text" name="form[date]" class="datepicker normal" value="{if isset($form.date) && $form.date > 0}{$form.date|date_format:'d/m/Y'}{/if}"></td>
                </tr>
                <tr>
                    <td class="form-td-title-b">Company : </td>
                    <td>
                        <input type="text" id="inddt_company" name="form[company]" value="{if isset($form.company_title)}{$form.company_title|escape:'html'}{/if}" class="normal ui-autocomplete-input" autocomplete="off" role="textbox" aria-autocomplete="list" aria-haspopup="true" />
                        <input type="hidden" id="inddt_company_id" name="form[company_id]" value="{if isset($form.company_id)}{$form.company_id}{else}0{/if}" />
                    </td>
                </tr>
            </table>
        </td>
        <td class="text-top">
            <table class="form" style="width: 100%;">
                <tr>
                    <td class="form-td-title-b">Owner : </td>
                    <td>
                        <select name="form[owner_id]" class="normal">
                            <option value="0">--</option>
                            {foreach $owners as $row}
                            <option value="{$row.company.id}"{if isset($form.owner_id) && $form.owner_id == $row.company.id} selected="selected"{/if}>{$row.company.title_trade|escape:'html'}</option>
                            {/foreach}
                        </select>
                    </td>
                </tr>
                <tr>
                    <td class="form-td-title-b">Item Statuses : </td>
                    <td>
                        <select name="form[status_id]" class="normal" id="inddt-status">
                            <option value="0"{if !isset($form.status_id) || empty($form.status_id)} selected="selected"{/if}>--</option>
                            <option value="{$smarty.const.ITEM_STATUS_TRANSFER}"{if isset($form.status_id) && $form.status_id == $smarty.const.ITEM_STATUS_TRANSFER} selected="selected"{/if}>Transfer To Stock</option>
                            <option value="{$smarty.const.ITEM_STATUS_STOCK}"{if isset($form.status_id) && $form.status_id == $smarty.const.ITEM_STATUS_STOCK} selected="selected"{/if}>On Stock</option>
                        </select>
                    </td>
                </tr>
                {*<tr>
                    <td class="form-td-title">Stockholder : </td>
                    <td>
                        <select name="form[stockholder_id]" class="normal common-stockholder">
                            <option value="0">--</option>
                            {foreach $stockholders_list as $row}
                            <option value="{$row.company_id}"{if isset($form.stockholder_id) && $form.stockholder_id == $row.company_id} selected="selected"{/if}>{$row.company.doc_no|escape:'html'}{if !empty($row.company.city)} ({$row.company.city.title|escape:'html'}){/if}</option>
                            {/foreach}
                        </select>
                    </td>
                </tr>*}
            </table>
        </td>
    </tr>
</table>
<div class="pad"></div>

<h3 style="margin-bottom: 10px;" class="inddt-packing-list-title">Items</h3>
<span class="inddt-pl-is-empty" style="display: {if empty($items)}inline{else}none{/if};">There are no items</span>
{if !empty($items)}
<table class="list" style="width: 100%">
    <tr class="top-table" style="height: 25px;">
        <th>id</th>
        <th>Plate id</th>
        <th>Steel Grade</th>
        <th>Thickness,<br />{$items.0.steelitem.dimension_unit}</th>
        <th>Width,<br />{$items.0.steelitem.dimension_unit}</th>
        <th>Length,<br />{$items.0.steelitem.dimension_unit}</th>
        <th>Weight,<br />{$items.0.steelitem.weight_unit}</th>
        <th style="width: 200px;">Stockholder</th>
        <th>Supplier Invoice</th>
        <th style="width: 100px;">Owner</th>
        <th style="width: 50px;">Status</th>
        <th style="width: 20px;"></th>
    </tr>
    {foreach $items as $item}
    <tr class="inddt-item{if $item.steelitem.status_id >= $smarty.const.ITEM_STATUS_ORDERED} item-status-{$item.steelitem.status_id}{/if}" id="inddt-item-{$item.id}">
        <td onclick="show_item_context(event, {$item.steelitem.id});">{$item.steelitem.id}</td>
        <td onclick="show_item_context(event, {$item.steelitem.id});">{if empty($item.steelitem.guid)}<span style="font-weight: normal;">{$item.steelitem.doc_no|undef}</span>{else}{$item.steelitem.guid|escape:'html'|undef}{/if}</td>
        <td onclick="show_item_context(event, {$item.steelitem.id});">{if !empty($item.steelitem.steelgrade)}{$item.steelitem.steelgrade.title|escape:'html'|undef}{else}{''|undef}{/if}</td>
        <td onclick="show_item_context(event, {$item.steelitem.id});">{if !empty($item.steelitem.thickness_mm)}{$item.steelitem.thickness_mm|string_format:'%.1f'}{/if}</td>
        <td onclick="show_item_context(event, {$item.steelitem.id});">{if !empty($item.steelitem.width_mm)}{$item.steelitem.width_mm|string_format:'%.0f'}{/if}</td>
        <td onclick="show_item_context(event, {$item.steelitem.id});">{if !empty($item.steelitem.length_mm)}{$item.steelitem.length_mm|string_format:'%.0f'}{/if}</td>
        <td onclick="show_item_context(event, {$item.steelitem.id});">{if !empty($item.steelitem.unitweight_ton)}{$item.steelitem.unitweight_ton|escape:'html'|string_format:'%.2f'}{/if}</td>
        <td>
            {* if $item.steelitem.status_id <= $smarty.const.ITEM_STATUS_ORDERED *}
            <select name="items[{$item.steelitem_id}][stockholder_id]" class="wide item-stockholder">
                <option value="0">--</option>
                {foreach $stockholders as $row}
                <option value="{$row.company_id}"{if $item.stockholder_id == $row.company_id} selected="selected"{/if}>{$row.company.doc_no|escape:'html'}{if !empty($row.company.city)} ({$row.company.city.title|escape:'html'}){/if}</option>
                {/foreach}
            </select>
            {* else}
                {if isset($item.stockholder)}{$item.stockholder.doc_no} ({$item.stockholder.city.title}){else}{''|undef}{/if}
            {/if *}                        
        </td>
        <td>
            {if isset($item.steelitem.supplier_invoice)}<a href="/supplierinvoice/{$item.steelitem.supplier_invoice_id}">{$item.steelitem.supplier_invoice.doc_no_full}</a>
            {else}{''|undef}{/if}
        </td>
        <td>
            {if isset($item.steelitem.owner)}{$item.steelitem.owner.title_trade}{else}{''|undef}{/if}
        </td>
        <td>
            {if $item.steelitem.status_id < $smarty.const.ITEM_STATUS_ORDERED}
            <select name="items[{$item.steelitem_id}][status_id]" class="inddt-item-status">
                <option value="0"{if empty($item.steelitem.status_id)} selected="selected"{/if}>--</option>
                <option value="{$smarty.const.ITEM_STATUS_TRANSFER}"{if $item.steelitem.status_id == $smarty.const.ITEM_STATUS_TRANSFER} selected="selected"{/if}>Transfer To Stock</option>
                <option value="{$smarty.const.ITEM_STATUS_STOCK}"{if $item.steelitem.status_id == $smarty.const.ITEM_STATUS_STOCK} selected="selected"{/if}>On Stock</option>
            </select>
            {else}
                {$item.steelitem.status_title}
                {if $item.steelitem.order_id > 0}<br><a href="/order/{$item.steelitem.order_id}">{$item.steelitem.order_id|order_doc_no}</a>{/if}
            {/if}            
        </td>
        <td onclick="inddt_item_remove({$item.id});">
            <img class="item-delete" src="/img/icons/cross-small.png" style="cursor: pointer" alt="Delete" title="Delete"/>
        </td>
    </tr>
    {/foreach}
</table>
{/if}