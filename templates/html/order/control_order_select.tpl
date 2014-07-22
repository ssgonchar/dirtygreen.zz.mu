<div id="docselform-content">
{if !empty($list)}
    <table class="list" width="100%">
        <tbody>
            <tr class="top-table">
                <th>Id</th>
                <th>Biz</th>
                <th>Buyer</th>
                <th>Buyer Ref</th>
                <th>Supplier Ref</th>
                <th>Delivery Point</th>
                <th class="text-center">Delivery Date</th>
                <th class="text-center">Status</th>
                <th class="text-center">Modified</th>
            </tr>
            {foreach from=$list item=row}
            <tr onclick="put_positions_to_order('{if empty($row.order.status)}{$row.order.guid}{else}{$row.order.id}{/if}');" {if !empty($row.order.status)} class="tr-order-{$row.order.status}"{/if}>
                <td>{if empty($row.order.status)}*{else}{$row.order.id|order_doc_no}{/if}</td>
                <td>{if isset($row.order.biz)}{$row.order.biz.number_output}{else}{''|undef}{/if}</td>
                <td>{if isset($row.order.company)}{$row.order.company.title|escape:'html'}{else}{''|undef}{/if}</td>
                <td>{if !empty($row.order.buyer_ref)}{$row.order.buyer_ref|escape:'html'}{else}{''|undef}{/if}</td>
                <td>{if !empty($row.order.supplier_ref)}{$row.order.supplier_ref|escape:'html'}{else}{''|undef}{/if}</td>
                <td>
                    {if isset($row.order.delivery_point_title)}
                        {$row.order.delivery_point_title}{if !empty($row.order.delivery_town)} {$row.order.delivery_town|escape:'html'}{/if}
                    {else}{''|undef}{/if}
                </td>
                <td class="text-center">{if !empty($row.order.delivery_date)}{$row.order.delivery_date|escape:'html'}{else}{''|undef}{/if}</td>
                <td class="text-center"{if $row.order.status == 'nw'} style="background-color: #ffffff; font-weight: bold;"{/if}>{if isset($row.order.status_title)}{$row.order.status_title}{else}<i>Unregistered</i>{/if}</td>
                <td class="text-center">
                    {$row.order.modified_at|date_human}<br>
                    by {$row.order.modifier.login|escape:'html'}
                </td>
            </tr>
            {/foreach}
        </tbody>
    </table>
{else}
    No orders available, please create new order
{/if}
</div>
<div id="docselform-actions">
    <input type="button" class="btn100" onclick="close_document_select();" value="Cancel">
    <input type="button" class="btn150o" onclick="put_positions_to_order(0);" value="Create New Order" style="margin-left: 20px;">
</div>