{if empty($list)}
    There are no unregistered orders
{else}
    <table class="list" width="100%">
        <tbody>
            <tr class="top-table">
                <th>Link</th>
                <th>Order For</th>
                <th>Biz</th>
                <th>Buyer</th>
                <th>Buyer Ref</th>
                <th>Supplier Ref</th>
                <th>Delivery Point</th>
                <th class="text-center">Delivery Date</th>
                <th width="10%" class="text-center">Qtty</th>
                <th width="10%" class="text-center">Weight</th>
                <th width="8%" class="text-center">Value</th>
                <th class="text-center">Modified</th>
            </tr>
            {foreach from=$list item=row}
            <tr>
                <td><a href="/order/neworder/{$row.guid}" class="edit">edit</a></td>
                <td>{if isset($row.order_for_title)}{$row.order_for_title}{else}--{/if}</td>
                <td>{if isset($row.biz)}<a href="/biz/view/{$row.biz.id}">{$row.biz.number_output}</a>{else}--{/if}</td>
                <td>{if isset($row.company) && isset($row.company.id)}<a href="/company/view/{$row.company.id}">{$row.company.title|escape:'html'}</a>{else}--{/if}</td>
                <td>{if !empty($row.buyer_ref)}{$row.buyer_ref|escape:'html'}{else}--{/if}</td>
                <td>{if !empty($row.supplier_ref)}{$row.supplier_ref|escape:'html'}{else}--{/if}</td>
                <td>
                    {if isset($row.delivery_point_title)}
                        {$row.delivery_point_title}{if !empty($row.delivery_town)} {$row.delivery_town|escape:'html'}{/if}
                    {else}--{/if}
                </td>
                {if !empty($row.delivery_date)}
                <td>{if !empty($row.delivery_date)}{$row.delivery_date}{/if}</td>
                {else}
                <td class="text-center">{if !empty($row.delivery_date)}{$row.delivery_date|escape:'html'}{else}--{/if}</td>
                {/if}
                <td class="text-center">
                    {if !empty($row.qtty)}
                        {$row.qtty|escape:'html'|string_format:'%d'} pcs
                    {else}
                        --
                    {/if}
                </td>
                <td class="text-center">
                    {if !empty($row.weight)}
                        {$row.weight|escape:'html'|string_format:'%.2f'} {if $row.weight_unit == 'mt'}ton{else}lb{/if}
                    {else}
                        --
                    {/if}
                </td>
                <td class="text-center">{if !empty($row.value)}{$row.value|escape:'html'|string_format:'%.2f'} {$row.currency|cursign}{else}--{/if}</td>
                <td class="text-center">
                    {$row.modified_at|date_human}<br>
                    by {$row.modifier.login|escape:'html'}
                </td>
            </tr>
            {/foreach}
        </tbody>
    </table>
{/if}
