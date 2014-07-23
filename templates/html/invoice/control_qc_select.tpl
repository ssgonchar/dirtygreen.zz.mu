<div id="docselform-content">
{if !empty($list)}
    <table class="list" width="100%">
        <tbody>
            <tr class="top-table">
                <th>No</th>
                <th>Order</th>
                <th>Biz</th>
                <th>Customer</th>
                <th>Customer Ref</th>
                <th>Certificate Standard</th>
                <th class="text-center">Modified</th>
            </tr>
            {foreach from=$list item=row}
            <tr onclick="put_positions_to_qc({$row.qc.id});">
            {if empty($row.qc.id)}
            <td colspan="7">My Unsaved QC</td>
            {else}
                <td>{$row.qc.doc_no}</td>
                <td>{if isset($row.qc.order)}{$row.qc.order.doc_no}{else}{''|undef}{/if}</td>
                <td>{if isset($row.qc.qcbiz)}{$row.qc.qcbiz.doc_no}{else}{''|undef}{/if}</td>
                <td>{if isset($row.qc.company)}{$row.qc.company.doc_no}{else}{$row.qc.customer|undef}{/if}</td>
                <td>{if !empty($row.qc.customer_order_no)}{$row.qc.customer_order_no}{else}{''|undef}{/if}</td>
                <td>{if !empty($row.qc.certification_standard)}{$row.qc.certification_standard}{else}{''|undef}{/if}</td>
                <td class="text-center">
                {if isset($row.qc.modifier)}
                    {$row.qc.modified_at|date_human}<br>
                    by {$row.qc.modifier.login|escape:'html'}
                {else}
                    {$row.qc.created_at|date_human}<br>
                    by {$row.qc.author.login|escape:'html'}
                {/if}
                </td>
            {/if}
            </tr>
            {/foreach}
        </tbody>
    </table>
{else}
    No QC available, please create new QC
{/if}
</div>
<div id="docselform-actions">
    <input type="button" class="btn100" onclick="close_document_select();" value="Cancel">
    <input type="button" class="btn150o" onclick="put_positions_to_qc(-1);" value="Create New QC" style="margin-left: 20px;">
</div>