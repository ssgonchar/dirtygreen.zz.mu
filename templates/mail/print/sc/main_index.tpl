{if empty($list)}
    Nothing was found on my request
{else}
    <table class="list" width="100%">
        <tbody>
            <tr class="top-table">
                <th>Id</th>
                <th>SC</th>
                <th>Order</th>
                <th>Buyer</th>
                <th>Our Ref</th>
                <th>Buyer Ref</th>
                <th>Delivery Point</th>
                <th class="text-center">Delivery Date</th>
                <th class="text-center">Modified</th>
            </tr>
            {foreach from=$list item=row}
            <tr>
                <td>{$row.sc.id}</td>
                <td>{$row.sc.doc_no}</td>
                <td><a href="/order/{$row.order.id}">{$row.order.doc_no}</a></td>
                <td>{$row.order.company.title}</td>
                <td>{if isset($row.order.biz)}{$row.order.biz.doc_no}{else}{''|undef}{/if}</td>
                <td>{if !empty($row.order.buyer_ref)}{$row.order.buyer_ref|escape:'html'}{else}{''|undef}{/if}</td>
                <td>
                    {if isset($row.order.delivery_point_title)}
                        {$row.order.delivery_point_title|escape:'html'} {if !empty($row.sc.delivery_point)}{$row.sc.delivery_point}{/if}
                    {else}{''|undef}{/if}
                </td>
                <td>{$row.sc.delivery_date}</td>
                <td>{$row.order.modified_at|date_human}<br>by {$row.order.modifier.login|escape:'html'}</td>
            </tr>
            {/foreach}
        </tbody>
    </table>
{/if}