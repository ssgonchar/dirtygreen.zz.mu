<table width="100%">
    <tr>
        <td width="33%" class="text-top">
            <table class="form" width="100%">
                <tr>
                    <td class="form-td-title-b">Number : </td>
                    <td>
                        <input class="narrow" type="text" name="form[number]"{if !empty($form.number)} value="{$form.number}"{/if}/>
                   </td>
                </tr>
                <tr>
                    <td class="form-td-title-b">Date : </td>
                    <td>
                        <input class="narrow datepicker" type="text" name="form[date]"{if !empty($form.date) && $form.date > 0} value="{$form.date|date_format:'d/m/Y'}"{/if} />
                    </td>
                </tr>
                {if isset($form.id) && isset($form.due_date)}
                <tr style="height: 32px;">
                    <td class="form-td-title">Due Date : </td>
                    <td>
                        <div style="display:block; width: 100px; padding: 5px 0; {if $form.days_left <= 0}background-color: red; font-weight: bold; color: white;{elseif $form.days_left <= 7}background-color: #FFF750;{else}color: black;{/if}">
                        {$form.due_date|date_format:'d/m/Y'}
                        </div>
                    </td>
                </tr>
                {/if}                
                <tr style="height: 32px;">
                    <td class="form-td-title-b">Supplier : </td>
                    <td>
                        <input id="supinv_company" type="text" name="form[company_title]"{if isset($form.company_title)} value="{$form.company_title|escape:'html'}"{/if} class="normal">
                        <input id="supinv_company_id" type="hidden" name="form[company_id]"{if isset($form.company_id)} value="{$form.company_id}"{/if}>                        
                    </td>
                </tr>
            </table>
        </td>
        <td width="33%" class="text-top">
            <table class="form" width="100%">
                <tr>
                    <td class="form-td-title-b">Owner : </td>
                    <td>
                        <select name="form[owner_id]" class="narrow">
                            <option value="0"{if !isset($form.owner_id) || empty($form.owner_id)} selected="selected"{/if}>--</option>
                            {foreach from=$owners item=row}
                            <option value="{$row.company.id}"{if isset($form.owner_id) && $form.owner_id == $row.company.id} selected="selected"{/if}>{$row.company.title_trade}</option>
                            {/foreach}
                        </select>                        
                   </td>
                </tr>
                <tr>
                    <td class="form-td-title-b">Payment Terms : </td>
                    <td>
                        <input type="text" name="form[payment_days]"{if !empty($form.payment_days)} value="{$form.payment_days}"{/if} style="width:40px;"/>
                        &nbsp;days from&nbsp;
                        <select name="form[payment_type]" style="width:120px;">
                            <option value="{$smarty.const.SUPINVOICE_PAYMENT_IDD}"{if !isset($form.payment_type) || empty($form.payment_type) || $form.payment_type == $smarty.const.SUPINVOICE_PAYMENT_IDD} selected="selected"{/if}>invoice date</option>
                            <option value="{$smarty.const.SUPINVOICE_PAYMENT_EOM}"{if isset($form.payment_type) && $form.payment_type == $smarty.const.SUPINVOICE_PAYMENT_EOM} selected="selected"{/if}>end of month</option>
                        </select>                        
                   </td>
                </tr>
                <tr>
                    <td class="form-td-title-b">Delivery Point : </td>
                    <td>
                        <!--
                        <select name="form[delivery_point_id]" class="chosen-select narrow">
                            <option value="0"{if !isset($form.delivery_point_id) || empty($form.delivery_point_id)} selected="selected"{/if}>--</option>
                            {foreach from=$delivery_points item=row}
                            {$row|@debug_print_var}
                            <option value="{$row.company.id}"{if isset($form.delivery_point_id) && $form.delivery_point_id == $row.company.id} selected="selected"{/if}>{$row.company.doc_no_full|replace:'(':'( '|replace:')':' )'}</option>
                            {/foreach}
                        </select>-->
                        <input type='text' name='form[delivery_point]'{if !empty($form.delivery_point)} value='{$form.delivery_point}'{/if} placeholder='free text'>
                   </td>
                </tr>              
{*                
                <tr>
                    <td class="form-td-title-b">% of amount : </td>
                    <td>
                        <input class="narrow" type="text" name="form[percent]" value="{if isset($form.percent) && !empty($form.percent)}{$form.percent}{else}100{/if}"/>
                   </td>
                </tr>                
*}                
            </table>
        </td>
        <td width="34%" class="text-top">
            <table class="form" width="100%">
                <tr>
                    <td class="form-td-title">Currency : </td>
                    <td>
                        <select name="form[currency]" class="narrow" onchange="supinv_change_currency(this.value);">
                            <option value="eur"{if !isset($form.currency) || empty($form.currency)} selected="selected"{/if}>&euro;</option>
                            <option value="usd"{if isset($form.currency) && $form.currency == 'usd'} selected="selected"{/if}>$</option>
                            <option value="gbp"{if isset($form.currency) && $form.currency == 'gbp'} selected="selected"{/if}>&pound;</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td class="form-td-title">Status : </td>
                    <td>
                        <select class="normal" name="form[status_id]" onchange="supinv_toggle_amount(this.value, {$smarty.const.SUPINVOICE_STATUS_PPAID});">
                            <option value="0"{if !isset($form.status_id) || empty($form.status_id)} selected="selected"{/if}>--</option>
                            <option value="{$smarty.const.SUPINVOICE_STATUS_PPAID}"{if isset($form.status_id) && $form.status_id == $smarty.const.SUPINVOICE_STATUS_PPAID} selected="selected"{/if}>Partially Paid</option>
                            <option value="{$smarty.const.SUPINVOICE_STATUS_PAID}"{if isset($form.status_id) && $form.status_id == $smarty.const.SUPINVOICE_STATUS_PAID} selected="selected"{/if}>Paid</option>
                            <option value="{$smarty.const.SUPINVOICE_STATUS_CANCELLED}"{if isset($form.status_id) && $form.status_id == $smarty.const.SUPINVOICE_STATUS_CANCELLED} selected="selected"{/if}>Credited</option>
                        </select>
                    </td>
                </tr>
                <tr id="supinv-amount" style="display: {if isset($form.status_id) && $form.status_id == $smarty.const.SUPINVOICE_STATUS_PPAID}table-row{else}none{/if};">
                    <td class="form-td-title">Amount Paid : </td>
                    <td><input class="narrow" type="text"  name="form[amount_paid]"{if !empty($form.amount_paid)} value="{$form.amount_paid|string_format:'%.2f'}"{/if} /></td>
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
        {if !isset($form.id) || empty($form.id)}
        <th style="width: 20px;"></th>        
        {/if}
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
        <th width="100px">Purchase Price,<br /><span id="supinv-currency">{if isset($form.currency) && !empty($form.currency)}{$form.currency|cursign}{else}&euro;{/if}</span>/{'mt'|wunit}</th>
        <th>Stockholder</th>
        <th>Owner</th>
        <th>Status</th>
        {if isset($form.id) && !empty($form.id)}
        <th style="width: 20px;"></th>
        {/if}
    </tr>
    {foreach $items as $item}
    <tr class="item{if $item.steelitem.status_id >= $smarty.const.ITEM_STATUS_ORDERED} item-status-{$item.steelitem.status_id}{/if}" data-supplier_invoice_id="{$item.supplier_invoice_id}" data-steelitem_id="{$item.steelitem_id}">
        {if !isset($form.id) || empty($form.id)}
        <td>
            <input class="single-checkbox type-id-{$item.steelitem.owner_id}" rel="packing-list" type="checkbox" name="items[{$item.steelitem.id}][checked]" value="{$item.steelitem.id}"{if !isset($item.checked) || $item.checked > 0} checked="checked"{/if} />
        </td>
        {/if}
        <td onclick="show_item_context(event, {$item.steelitem.id});">{$item.steelitem.id}</td>
        <td onclick="show_item_context(event, {$item.steelitem.id});">{if empty($item.steelitem.guid)}<span style="font-weight: normal;">{$item.steelitem.doc_no|undef}</span>{else}{$item.steelitem.guid|escape:'html'|undef}{/if}</td>
        <td onclick="show_item_context(event, {$item.steelitem.id});">{if !empty($item.steelitem.steelgrade)}{$item.steelitem.steelgrade.title|escape:'html'|undef}{else}{''|undef}{/if}</td>
        <td onclick="show_item_context(event, {$item.steelitem.id});">{if !empty($item.steelitem.thickness_mm)}{$item.steelitem.thickness_mm|string_format:'%d'}{/if}</td>
        <td onclick="show_item_context(event, {$item.steelitem.id});">{if !empty($item.steelitem.width_mm)}{$item.steelitem.width_mm|string_format:'%d'}{/if}</td>
        <td onclick="show_item_context(event, {$item.steelitem.id});">{if !empty($item.steelitem.length_mm)}{$item.steelitem.length_mm|string_format:'%d'}{/if}</td>
        <td onclick="show_item_context(event, {$item.steelitem.id});">{if !empty($item.steelitem.unitweight_ton)}{$item.steelitem.unitweight_ton|escape:'html'|string_format:'%.2f'}{/if}</td>
        <td><input type="text" name="items[{$item.steelitem.id}][weight_invoiced]" value="{$item.weight_invoiced|string_format:'%.2f'}" style="width: 100%; text-align: center;"></td>
        <td>1</td>
{*        <td onclick="show_item_context(event, {$item.steelitem.id});">{if !empty($item.steelitem.price)}{$item.steelitem.price|escape:'html'|string_format:'%.2f'}{/if}</td>  *}
        <td><input type="text" name="items[{$item.steelitem.id}][purchase_price]" value="{$item.steelitem.purchase_price|string_format:'%.2f'}" style="width: 100%; text-align: center;"></td>
        <td onclick="show_item_context(event, {$item.steelitem.id});">{if !empty($item.steelitem.stockholder)}{$item.steelitem.stockholder.doc_no|escape:'html'}{if !empty($item.steelitem.stockholder.city)} ({$item.steelitem.stockholder.city.title|escape:'html'}){/if}{/if}</td>
        <td onclick="show_item_context(event, {$item.steelitem.id});">{if !empty($item.steelitem.owner)}{$item.steelitem.owner.title_trade|escape:'html'}{/if}</td>
        {if $item.steelitem.order_id > 0}
        <td>{$item.steelitem.status_title}<br><a href="/order/{$item.steelitem.order_id}">{$item.steelitem.order_id|order_doc_no}</a></td>        
        {else}
        <td onclick="show_item_context(event, {$item.steelitem.id});">{$item.steelitem.status_title}</td>        
        {/if}
        {if isset($form.id) && !empty($form.id)}
        <td><img class="item-delete" src="/img/icons/cross-small.png" style="cursor: pointer" alt="Delete" title="Delete"/></td>
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
                    <td>
                        <textarea name="form[notes]" rows="5" class="max">{if isset($form.notes) && !empty($form.notes)}{$form.notes}{/if}</textarea>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>