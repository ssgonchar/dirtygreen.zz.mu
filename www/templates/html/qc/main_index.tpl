{if empty($list)}
    Nothing was found on my request
{else}
    <table class="list search-target" width="100%">
        <tbody>
            <tr class="top-table">
{*                <th style="width: 30px;"><input type="checkbox" id="" value="{$row.qc.id}" onchange="check_all(this, 'qc')"></th>	*}
                <th>No</th>
                <th>Order</th>
                <th>Biz</th>
                <th>Customer</th>
                <th>Customer Order No</th>
                <th>Certification Standard</th>
                <th>Standard</th>
                <th width="5%">PDF</th>
                <th>Modified</th>
                <th></th>
            </tr>
            {foreach from=$list item=row}
            <tr>
{*                <td><input type="checkbox" value="{$row.qc.id}" class="cb-row-qc" onchange="show_selected_controls('qc');"></td>	*}
                <td onclick="location.href='/qc/{$row.qc.id}';">{$row.qc.doc_no}</td>
                <td onclick="location.href='/qc/{$row.qc.id}';">{if isset($row.qc.order)}<a href="/order/{$row.qc.order_id}">{$row.qc.order.doc_no}</a>{else}{''|undef}{/if}</td>
                <td onclick="location.href='/qc/{$row.qc.id}';">{if isset($row.qc.qcbiz)}<a href="/biz/{$row.qc.biz_id}">{$row.qc.qcbiz.doc_no}</a>{else}{''|undef}{/if}</td>
                <td onclick="location.href='/qc/{$row.qc.id}';">{if isset($row.qc.company)}<a href="/company/{$row.qc.customer_id}">{$row.qc.company.doc_no}</a>{else}{$row.qc.customer|undef}{/if}</td>
                <td onclick="location.href='/qc/{$row.qc.id}';">{if !empty($row.qc.customer_order_no)}{$row.qc.customer_order_no}{else}{''|undef}{/if}</td>
                <td onclick="location.href='/qc/{$row.qc.id}';">{if !empty($row.qc.certification_standard)}{$row.qc.certification_standard|escape:'html'}{else}{''|undef}{/if}</td>
                <td onclick="location.href='/qc/{$row.qc.id}';">{if !empty($row.qc.standard)}{$row.qc.standard|escape:'html'}{else}{''|undef}{/if}</td>
                {if isset($row.qc.attachment)}
                <td><a class="pdf" target="_blank" href="/file/{$row.qc.attachment.secret_name}/{$row.qc.attachment.original_name}">{$row.qc.attachment.original_name}</a></td>
                {else}
                <td onclick="location.href='/qc/{$row.qc.id}';">{''|undef}</td>
                {/if}
                
                {*
                <td class="text-left" onclick="location.href='/qc/{$row.qc.id}';">
                    {if isset($row.qc.attachment)}<a class="pdf" target="_blank" href="/file/{$row.qc.attachment.secret_name}/{$row.qc.attachment.original_name}">{$row.qc.attachment.original_name}</a>{else}{''|undef}{/if}
                </td>
                *}
                <td onclick="location.href='/qc/{$row.qc.id}';">
                    {$row.qc.modified_at|date_human}<br>
                    by {$row.qc.modifier.login|escape:'html'}
                </td>
                <td>
                    <img src="/img/icons/pencil-small.png" onclick="location='/qc/{$row.qc.id}/edit';" alt="Edit QC" title="Edit QC">
                    <img src="/img/icons/cross-small.png" onclick="if (confirm('Remove QC ?')) location='/qc/{$row.qc.id}/remove';" alt="Remove QC" title="Remove QC">
                </td>
            </tr>
            {/foreach}
        </tbody>
    </table>
{/if}