<table style="width: 100%; table-layout: fixed;">
    <tr>
        <td style="vertical-align: top; width: 32%; table-layout: fixed;">
            <div class="widget blue">
                <div class="widget-title">Old System Reference</div>
                <div class="widget-stat"></div>
                <ul class="widget-list">
                    <li><a href="http://mamtrix.steelemotion.com" target="_blank">http://mamtrix.steelemotion.com</a></li>
                </ul>
            </div>        
            <div class="pad"></div>
            
            <div class="widget">
                <div class="widget-title">Favourite BIZs</div>
                <div class="widget-stat">{if !empty($bizes_list)}{count($bizes_list)}{/if}</div>
                <ul class="widget-list">
                    {if !empty($bizes_list)}
                    {foreach $bizes_list as $row}
                    <li onclick="location.href='/biz/{$row.biz.id}';">{$row.biz.doc_no_full|escape:'html'}</li>
                    {/foreach}
                    {else}
                    <li class="empty">There are no BIZs</li>
                    {/if}
                </ul>
            </div>        
        </td>
        <td style="width: 20px;"></td>
        <td style="width: 32%; vertical-align: top; white-space: nowrap;">
            <div class="widget">
                <div class="widget-title">Open Orders</div>
                <div class="widget-stat">{if !empty($orders_list.data)}{count($orders_list.data)}{/if}</div>
                <ul class="widget-list">
                    {if !empty($orders_list.data)}
                    {foreach $orders_list.data as $row}
                    <li onclick="location.href='/order/{$row.order.id}';" class="tr-order-{$row.order.status}">{$row.order.doc_no_full|escape:'html'} {if isset($row.order.biz)}{$row.order.biz.doc_no}{/if}</li>
                    {/foreach}
                    {else}
                    <li class="empty">There are no orders</li>
                    {/if}
                </ul>
            </div>
        </td>
        <td style="width: 20px;"></td>
        <td style="width: 32%; vertical-align: top; white-space: nowrap;">
            <div class="widget">
                <div class="widget-title">E-mail inbox</div>
                <div class="widget-stat">{if !empty($emails_list.data)}{count($emails_list.data)}{/if}</div>
                <ul class="widget-list">
                    {if !empty($emails_list.data)}
                    {foreach $emails_list.data as $row}
                    <li onclick="location.href='/email/{$row.email.id}';">
                    {$row.email.sender_address|replace:'<':'&lt;'|replace:'>':'&gt;'} {$row.email.title}
                    </li>
                    {/foreach}
                    {else}
                    <li class="empty">There are no E-mails</li>
                    {/if}
                </ul>
            </div>
        </td>
    </tr>
</table>
