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
                <th></th>
            </tr>
            {foreach from=$list item=row}
            <tr id="sc-{$row.order.id}"{if !empty($row.order.status)} class="tr-order-{$row.order.status}"{/if}>
                <td onclick="location='/sc/{$row.sc.id}';">{$row.sc.id}</td>
                <td onclick="location='/sc/{$row.sc.id}';">{$row.sc.doc_no}</td>
                <td><a href="/order/{$row.order.id}">{$row.order.doc_no}</a></td>
                <td onclick="location='/sc/{$row.sc.id}';">{$row.order.company.title}</td>
                <td onclick="location='/sc/{$row.sc.id}';">{if isset($row.order.biz)}{$row.order.biz.doc_no}{else}{''|undef}{/if}</td>
                <td onclick="location='/sc/{$row.sc.id}';">{if !empty($row.order.buyer_ref)}{$row.order.buyer_ref|escape:'html'}{else}{''|undef}{/if}</td>
                <td onclick="location='/sc/{$row.sc.id}';">
                    {if isset($row.order.delivery_point_title)}
                        {$row.order.delivery_point_title|escape:'html'} {if !empty($row.sc.delivery_point)}{$row.sc.delivery_point}{/if}
                    {else}{''|undef}{/if}
                </td>
                <td onclick="location='/sc/{$row.sc.id}';">{$row.sc.delivery_date}</td>
                <td onclick="location='/sc/{$row.sc.id}';">
                    {$row.order.modified_at|date_human}<br>
                    by {$row.order.modifier.login|escape:'html'}
                </td>
                <td onclick="location='/sc/{$row.sc.id}/edit';"><img src="/img/icons/pencil-small.png"></td>
            </tr>
            {/foreach}
        </tbody>
    </table>
{/if}