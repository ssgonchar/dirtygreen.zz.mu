{if empty($list)}Nothing was found on my request
{else}
    <table class="list" width="100%">
        <tbody>
            <tr class="top-table">
                <th>No</th>
                <th>Order</th>
                <th>Biz</th>
                <th>Customer</th>
                <th>Customer Order No</th>
                <th>Certification Standard</th>
                <th>Standard</th>
                <th>Modified</th>
            </tr>
            {foreach $list as $row}
            <tr>
                <td>{$row.qc.doc_no}</td>
                <td>{if isset($row.qc.order)}<a href="/order/{$row.qc.order_id}">{$row.qc.order.doc_no}</a>{else}{''|undef}{/if}</td>
                <td>{if isset($row.qc.qcbiz)}<a href="/biz/{$row.qc.biz_id}">{$row.qc.qcbiz.doc_no}</a>{else}{''|undef}{/if}</td>
                <td>{if isset($row.qc.company)}<a href="/company/{$row.qc.customer_id}">{$row.qc.company.doc_no}</a>{else}{$row.qc.customer|undef}{/if}</td>
                <td>{if !empty($row.qc.customer_order_no)}{$row.qc.customer_order_no}{else}{''|undef}{/if}</td>
                <td>{if !empty($row.qc.certification_standard)}{$row.qc.certification_standard|escape:'html'}{else}{''|undef}{/if}</td>
                <td>{if !empty($row.qc.standard)}{$row.qc.standard|escape:'html'}{else}{''|undef}{/if}</td>
                <td>{$row.qc.modified_at|date_human}<br>by {$row.qc.modifier.login|escape:'html'}</td>
            </tr>
            {/foreach}
        </tbody>
    </table>
{/if}