<table>
    <tr>
        <td width="50%" class="text-top">
            <table class="form" width="100%">                
                <tr>
                    <td class="form-td-title-b">Order : </td>
                    <td>
                        {if empty($invoice.order_id)}{''|undef}
                        {else}<a href="/order/{$invoice.order_id}">{$invoice.order_id|order_doc_no}</a>{/if}
                    </td>
                </tr>
                <tr>
                    <td class="form-td-title-b">Type : </td>
                    <td>
                        {if $invoice.owner_id == -1}
                            {''|undef}
                        {elseif $invoice.owner_id == 0}
                            IVA
                        {else}
                            {$invoice.owner.title_trade|escape:'html'}
                        {/if}
                    </td>
                </tr>
                <tr>
                    <td class="form-td-title-b">BIZ : </td>
                    <td>
                        <a href="/biz/{$invoice.biz_id}">{$invoice.biz.doc_no_full|escape:'html'}</a>
                    </td>
                </tr>
                <tr>
                    <td class="form-td-title-b">Company : </td>
                    <td>
                        <a href="/company/{$invoice.company_id}">{$invoice.company.doc_no|escape:'html'}</a>
                    </td>
                </tr>                
            </table>
        </td>
        <td width="50%" class="text-top">
            <table class="form" width="100%">
                <tr>
                    <td class="form-td-title-b">IVA : </td>
                    <td>{$invoice.iva_number_full|escape:'html'}</td>
                </tr>
                <tr>
                    <td class="form-td-title-b">Number : </td>
                    <td>{if empty($invoice.number_full)}{''|undef}{else}{$invoice.number_full|escape:'html'}{/if}</td>
                </tr>
                <tr>
                    <td class="form-td-title-b">Date : </td>
                    <td>{$invoice.date|date_format:'d/m/Y'}</td>
                </tr>
                <tr>
                    <td class="form-td-title-b">Due Date : </td>
                    <td>{$invoice.due_date|date_format:'d/m/Y'}</td>
                </tr>
                <tr>
                    <td class="form-td-title-b">Status : </td>
                    <td>
                        {if $invoice.status_id == 1}Received
                        {else if $invoice.status_id == 2}Partially Received
                        {/if}
                    </td>
                </tr>
                <tr>
                    <td class="form-td-title-b">Amount : </td>
                    <td>{$invoice.amount_received|string_format:'%.2f'}</td>
                </tr>
            </table>        
        </td>
    </tr>
</table>
<div class="pad"></div>

<h3>Items</h3>
{if empty($items)}
No items
{else}
<table class="list" width="100%">
    <tbody>
        <tr class="top-table">
            <th width="7%">Plate Id</th>
            <th>Steel Grade</th>
            <th>Thickness<br>{$qc.dim_unit}</th>
            <th>Width<br>{$qc.dim_unit}</th>
            <th>Length<br>{$qc.dim_unit}</th>
            <th>Pcs</th>
            <th>Weight<br>{$qc.wght_unit|wunit}</th>
            <th>Heat / Lot</th>
            <th class="text-right" width="5%">Item Id</th>
        </tr>
        {foreach from=$items item=row}
        <tr{if $row.steelitem.status_id >= $smarty.const.ITEM_STATUS_ORDERED} class="item-status-{$row.steelitem.status_id}"{/if}>
            <td>{if !empty($row.steelitem.guid)}{$row.steelitem.guid|escape:'html'}{else}&hellip;{/if}</td>
            <td>{if isset($row.steelitem.steelgrade)}{$row.steelitem.steelgrade.title|escape:'html'}{/if}</td>
            <td>{if $qc.mam_co == 'pa' && $qc.dim_unit == 'mm'}{$row.steelitem.thickness_mm|smartfloat:1}{else}{$row.steelitem.thickness|escape:'html'}{/if}</td>
            <td>{if $qc.mam_co == 'pa' && $qc.dim_unit == 'mm'}{$row.steelitem.width_mm|string_format:'%d'}{else}{$row.steelitem.width|escape:'html'}{/if}</td>
            <td>{if $qc.mam_co == 'pa' && $qc.dim_unit == 'mm'}{$row.steelitem.length_mm|string_format:'%d'}{else}{$row.steelitem.length|escape:'html'}{/if}</td>
            <td>1</td>
            <td>{if $qc.mam_co == 'pa' && $qc.wght_unit == 'mt'}{$row.steelitem.unitweight_ton|escape:'html'|string_format:'%.2f'}{else}{$row.steelitem.unitweight|escape:'html'|string_format:'%.2f'}{/if}</td>
            <td>{if !empty($row.steelitem.properties.heat_lot)}{$row.steelitem.properties.heat_lot|escape:'html'|string_format:'%.2f'}{else}&hellip;{/if}</td>
            <td class="text-right">{$row.steelitem.id}</td>
        </tr>
        {/foreach}
    </tbody>    
</table>
{/if}

<div class="pad"></div>
{include file='templates/controls/object_shared_files.tpl' object_alias='invoice' object_id=$form.id}