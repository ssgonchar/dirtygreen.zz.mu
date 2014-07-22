<table width="100%">
    <tr>
        <td width="30%" class="text-top">
            <table class="form">
                <tr>
                    <td width="85px" class="text-right">Order For :</td>
                    <td>
                        {if empty($order_for)}{''|undef}
                        {else if $order_for == 'mam'}MaM
                        {else if $order_for == 'pa'}PlatesAhead
                        {/if}
                    </td>
                </tr>
                <tr>
                    <td class="text-right">Biz :</td>
                    <td>{$biz_title|escape:'html'|undef}</td>
                </tr>
                <tr>
                    <td class="text-right">Buyer Co :</td>
                    <td>{$company_title|escape:'html'|undef}</td>
                </tr>
                <tr>
                    <td class="text-right">Keyword :</td>
                    <td>{$keyword|escape:'html'|undef}</td>
                </tr>
            </table>
        </td>
        <td width="30%" class="text-top">
            <table class="form">
                <tr>
                    <td width="85px" class="text-right">Order Type :</td>
                    <td>
                        {if empty($type)}{''|undef}
                        {else if $type == 'do'}Through dialog
                        {else if $type == 'so'}Self-service
                        {/if}
                    </td>
                </tr>
                <tr>
                    <td class="text-right">Period From :</td>
                    <td>{if !empty($period_from)}{$period_from|escape:'html'|date_format:'d/m/Y'}{else}{''|undef}{/if}</td>
                </tr>
                <tr>
                    <td class="text-right">Period To :</td>
                    <td>{if !empty($period_to)}{$period_to|escape:'html'|date_format:'d/m/Y'}{else}{''|undef}{/if}</td>
                </tr>
                <tr>
                    <td class="text-right">Order Status : </td>
                    <td>
                        {if empty($status)}{''|undef}
                        {else if $status == 'nw'}New (WebStock)
                        {else if $status == 'ip'}In Process
                        {else if $status == 'de'}To be Invoiced
                        {else if $status == 'co'}Completed
                        {else if $status == 'ca'}Cancelled
                        {/if}
                    </td>
                </tr>
            </table>
        </td>
        <td width="30%" class="text-top">
            <table class="form">
                <tr>
                    <td width="85px" class="text-right">Steel Grade :</td>
                    <td>
                        {if empty($steelgrade_id)}{''|undef}
                        {else}
                            {foreach $steelgrades as $row}
                            {if $row.steelgrade.id == $steelgrade_id}{$row.steelgrade.title|escape:'html'}{/if}
                            {/foreach}
                        {/if}
                    </td>
                </tr>
                <tr>
                    <td class="text-right">Thickness :</td>
                    <td>{$thickness|undef}</td>
                </tr>
                <tr>
                    <td class="text-right">Width :</td>
                    <td>{$width|undef}</td>
                </tr>
            </table>
        </td>
        <td class="text-center"></td>
    </tr>
</table>
<div class="pad"></div>

{if empty($list)}
{if isset($filter)}Nothing was found on my request{/if}
{else}
    <table class="list" width="100%">
        <tbody>
            <tr class="top-table">
                <th>Id</th>
                <th>Order For</th>
                {* <th>Type</th> *}
                <th>Biz</th>
                <th>Buyer</th>
                <th>Buyer Ref</th>
                {* <th>Supplier Ref</th> *}
                <th>Delivery Point</th>
                <th class="text-center">Delivery Date</th>
                <th width="10%" class="text-center">Weight</th>
                <th width="10%" class="text-center">Balance To Deliver</th>
                <th width="8%" class="text-center">Value</th>
                <th class="text-center">Status</th>
                <th class="text-center">Modified</th>
            </tr>
            {foreach from=$list item=row}
            <tr id="order-{$row.order.id}"{if !empty($row.order.status)} class="tr-order-{$row.order.status}"{/if}>
                <td onclick="location.href='/order/{$row.order.id}';">{$row.order.id}</td>
                <td onclick="location.href='/order/{$row.order.id}';">{if isset($row.order.order_for_title)}{$row.order.order_for_title}{else}{''|undef}{/if}</td>
                {* <td onclick="location.href='/order/{$row.order.id}';">{if $row.order.type == 'so'}Self-service{else}Through dialog{/if}</td> *}
                <td onclick="location.href='/order/{$row.order.id}';">{if isset($row.order.biz)}{$row.order.biz.number_output}{else}{''|undef}{/if}</td>
                <td onclick="location.href='/order/{$row.order.id}';">{if isset($row.order.company)}{$row.order.company.title|escape:'html'}{else}{''|undef}{/if}</td>
                <td onclick="location.href='/order/{$row.order.id}';">{if !empty($row.order.buyer_ref)}{$row.order.buyer_ref|escape:'html'}{else}{''|undef}{/if}</td>
                {* <td onclick="location.href='/order/{$row.order.id}';">{if !empty($row.order.supplier_ref)}{$row.order.supplier_ref|escape:'html'}{else}{''|undef}{/if}</td> *}
                <td onclick="location.href='/order/{$row.order.id}';">
                    {if isset($row.order.delivery_point_title)}
                        {$row.order.delivery_point_title}{if !empty($row.order.delivery_town)} {$row.order.delivery_town|escape:'html'}{/if}
                    {else}{''|undef}{/if}
                </td>
                {if isset($row.order.alert_date) && $row.order.status == 'ip'}
                <td onclick="location.href='/order/{$row.order.id}';" {if $row.order.status == 'ca' || $row.order.status == 'co'}class="text-center"{else}{if $row.order.quick.days_to_alert <= 1}class="text-center td-order-delivery-0"{elseif $row.order.quick.days_to_alert <= 5}class="text-center td-order-delivery-5"{else}class="text-center"{/if}{/if}>{if !empty($row.order.delivery_date)}{$row.order.delivery_date}{/if}</td>
                {else}
                <td onclick="location.href='/order/{$row.order.id}';" class="text-center">{if !empty($row.order.delivery_date)}{$row.order.delivery_date|escape:'html'}{else}{''|undef}{/if}</td>
                {/if}
                <td onclick="location.href='/order/{$row.order.id}';" class="text-center">
                    {if !empty($row.order.quick.weight)}
                        {$row.order.quick.weight|escape:'html'|string_format:'%.2f'} {if $row.order.weight_unit == 'mt'}ton{else}lb{/if}
                        ({$row.order.quick.qtty})
                    {else}{''|undef}{/if}
                </td>
                <td onclick="location.href='/order/{$row.order.id}';" class="text-center">
                    {if $row.order.balance_to_deliver.qtty > 0}
                        {$row.order.balance_to_deliver.weight|string_format:'%.2f'} ({$row.order.balance_to_deliver.qtty})
                    {/if}
                </td>
                <td onclick="location.href='/order/{$row.order.id}';" class="text-center">{if !empty($row.order.quick.value)}{$row.order.quick.value|escape:'html'|string_format:'%.2f'} {$row.order.currency_sign}{else}{''|undef}{/if}</td>
                <td onclick="location.href='/order/{$row.order.id}';" class="text-center"{if $row.order.status == 'nw'} style="background-color: #ffffff; font-weight: bold;"{/if}>{if isset($row.order.status_title)}{$row.order.status_title}{else}<i>Unregistered</i>{/if}</td>
                <td onclick="location.href='/order/{$row.order.id}';" class="text-center">
                    {$row.order.modified_at|date_human}<br>
                    by {$row.order.modifier.login|escape:'html'}
                </td>
            </tr>
            {/foreach}
        </tbody>
    </table>
{/if}