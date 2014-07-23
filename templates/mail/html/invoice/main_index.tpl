{if $owner_id == -1}<b style="margin-right: 10px;">All</b>{else}<a href="/invoices" style="margin-right: 10px;">All</a>{/if}
{if empty($owner_id)}<b style="margin-right: 10px;">IVA</b>{else}<a href="/invoices/filter/owner:0" style="margin-right: 10px;">IVA</a>{/if}
{foreach from=$owners item=row}
    {if $row.company.id == $owner_id}<b style="margin-right: 10px;">{$row.company.title_trade}</b>{else}<a href="/invoices/filter/owner:{$row.company.id}" style="margin-right: 10px;">{$row.company.title_trade}</a>{/if}
{/foreach}
<div class="pad1"></div>

{if empty($list)}
    Nothing was found on my request
{else}    
    <table class="list" width="100%">
        <tbody>
            <tr class="top-table">
                <th>IVA</th>
                <th>Order</th>
                <th>Invoice Type</th>
                <th>Biz</th>
                <th>Customer</th>
                <th>Invoice No</th>
                <th>Date</th>
                <th>Due Date</th>
                <th>Items</th>
                <th>Modified</th>
                <th style="width: 20px;"></th>
            </tr>
            {foreach from=$list item=row}
            <tr{if $row.invoice.status_id == 1} class="inv-row-received"{else if $row.invoice.status_id == 2} class="inv-row-partially-received"{else if !empty($row.invoice.is_overdue)} class="inv-row-overdue"{else if !empty($row.invoice.is_closed)} class="inv-row-closed"{/if}>
                <td onclick="location.href='/invoice/{$row.invoice.id}/edit';">{$row.invoice.iva_number_full}</td>
                <td onclick="location.href='/invoice/{$row.invoice.id}/edit';">{if !empty($row.invoice.order_id)}{$row.invoice.order_id|order_doc_no}{else}{''|undef}{/if}</td>
                <td onclick="location.href='/invoice/{$row.invoice.id}/edit';">{$row.invoice.type}</td>
                <td onclick="location.href='/invoice/{$row.invoice.id}/edit';">{if isset($row.invoice.biz)}{$row.invoice.biz.doc_no_full}{else}{''|undef}{/if}</td>
                <td onclick="location.href='/invoice/{$row.invoice.id}/edit';">{if isset($row.invoice.customer)}{$row.invoice.customer.doc_no}{else}{''|undef}{/if}</td>
                <td onclick="location.href='/invoice/{$row.invoice.id}/edit';">{if !empty($row.invoice.number)}{$row.invoice.number}{else}{''|undef}{/if}</td>
                <td onclick="location.href='/invoice/{$row.invoice.id}/edit';">{$row.invoice.date|undef}</td>
                <td onclick="location.href='/invoice/{$row.invoice.id}/edit';">{$row.invoice.due_date|undef}</td>
                <td onclick="location.href='/invoice/{$row.invoice.id}/edit';">{if empty($row.invoice.items_count)}{''|undef}{else}{$row.invoice.items_count}{/if}</td>
                <td onclick="location.href='/invoice/{$row.invoice.id}/edit';">
                    {if !empty($row.invoice.modified_by)}{$row.invoice.modified_at|date_human}<br>by {$row.invoice.modifier.login|escape:'html'}
                    {else}{$row.invoice.created_at|date_human}<br>by {$row.invoice.author.login|escape:'html'}
                    {/if}
                </td>
                <td>
                    {if $row.invoice.is_closed == 0}
                    <img src="/img/icons/cross-small.png" style="cursor: pointer" alt="Delete" title="Delete" onclick="if(!confirm('Am I sure ?'))return false;location.href='/invoice/{$row.invoice.id}/delete';" />
                    {/if}
                </td>
            </tr>
            {/foreach}
        </tbody>
    </table>
{/if}
