{*<div>There is template for print version of order {$order.doc_no|escape:'html'}</div>*}
{if !empty($conflicted_items)}
<div class="order-conflicted-notice">
    This order contains conflicted items : 
    {foreach from=$conflicted_items item=row name="items"}
    <a href="/item/{$row.id}/conflicted">{if !empty($row.guid)}{$row.guid}{elseif isset($row.rel_title)}{$row.rel_title}{else}Item # {$row.id}{/if}</a>{if !$smarty.foreach.items.last} ,  {/if}
    {/foreach}
</div>
{/if}
<table class="form" width="100%">
    <tr>
        <td width="25%" style="vertical-align: top;">
            <table class="form" width="100%">
                <tr height="32">
                    <td class="form-td-title-b">Order for : </td>
                    <td>{if isset($order.order_for_title)}{$order.order_for_title|escape:'html'}{else}<i>not defined</i>{/if}</td>
                </tr>
                <tr height="32">
                    <td class="form-td-title-b">BIZ : </td>
                    <td>{if isset($order.biz)}{$order.biz.number_output}{else}<i>not defined</i>{/if}</td>
                </tr>
                <tr height="32">
                    <td class="form-td-title-b">Buyer Company : </td>
                    <td>{if isset($order.company)}{$order.company.title|escape:'html'}{else}<i>not defined</i>{/if}</td>
                </tr>                
                <tr height="32">
                    <td class="form-td-title-b">Person : </td>
                    <td>{if isset($order.person)}{$order.person.full_name|escape:'html'}{else}<i>not defined</i>{/if}</td>
                </tr>
                <tr height="32">
                    <td class="form-td-title-b">Buyer Ref. : </td>
                    <td>{if !empty($order.buyer_ref)}{$order.buyer_ref|escape:'html'}{else}<i>not defined</i>{/if}</td>
                </tr>
                <tr height="32">
                    <td class="form-td-title-b">Supplier Ref. : </td>
                    <td>{if !empty($order.supplier_ref)}{$order.supplier_ref|escape:'html'}{else}<i>not defined</i>{/if}</td>
                </tr>
            </table>
        </td>
        <td width="25%" style="vertical-align: top;">
            <table class="form" width="100%">
                <tr height="32">
                    <td class="form-td-title-b">Delivery Point : </td>
                    <td>
                    {if isset($order.delivery_point_title)}
                        {$order.delivery_point_title}{if !isset($order.delivery_point) || ($order.delivery_point != 'col' && $order.delivery_point != 'exw' && $order.delivery_point != 'fca') && !empty($order.delivery_town)} {$order.delivery_town|escape:'html'}{/if}
                    {else}<i>not defined</i>{/if}
                    </td>
                </tr>                
                <tr height="32">
                    <td class="form-td-title-b">{if isset($order.delivery_point) && ($order.delivery_point == 'col' || $order.delivery_point == 'exw' || $order.delivery_point == 'fca')}Load Readiness{else}Delivery Time{/if} : </td>
                    <td>{if !empty($order.delivery_date)}{$order.delivery_date}{else}<i>not defined</i>{/if}</td>
                </tr>
                <tr height="32"{if isset($order.delivery_point) && ($order.delivery_point == 'col' || $order.delivery_point == 'exw' || $order.delivery_point == 'fca')} style="display: none;"{/if}>
                    <td class="form-td-title-b">Delivery Cost : </td>
                    <td>{if !empty($order.delivery_cost)}{$order.delivery_cost|escape:'html'}{else}<i>not defined</i>{/if}</td>
                    {*<td>{if $order.delivery_cost > 0}{$order.delivery_cost|escape:'html'|string_format:"%.2f"}{if isset($order.currency_sign)} {$order.currency_sign}{/if}{else}--{/if}</td>*}
                </tr>
                <tr height="32">
                    <td class="form-td-title-b">Order Alert Date : </td>
                    {if isset($order.alert_date) && !empty($order.alert_date)}
                    <td {if $order.quick.days_to_alert <= 1}class="td-order-delivery-0"{elseif $order.quick.days_to_alert <= 5}class="td-order-delivery-5"{/if}>
                        {$order.alert_date|date_human}
                    </td>
                    {else}<td><i>not defined</i></td>
                    {/if}
                </tr>
            </table>        
        </td>
        <td width="25%" valign="top" style="vertical-align: top;">
            <table class="form" width="100%">
                <tr height="32">
                    <td class="form-td-title-b">Invoicing Basis : </td>
                    <td>{if isset($order.invoicingtype)}{$order.invoicingtype.title|escape:'html'}{else}<i>not defined</i>{/if}</td>
                </tr>                
                <tr height="32">
                    <td class="form-td-title-b">Payment Term : </td>
                    <td>{if isset($order.paymenttype)}{$order.paymenttype.title|escape:'html'}{else}--{/if}</td>
                </tr>
            </table>
        </td>
        <td valign="top" style="vertical-align: top;">
            <table class="form" width="100%">
                <tr height="32">
                    <td class="form-td-title-b">Order Type : </td>
                    <td>{if $order.type == 'so'}Self-service{else}Through dialog{/if}</td>
                </tr>
                <tr height="32">
                    <td class="form-td-title-b">Order Status : </td>
                    <td{if !empty($order.status)} class="tr-order-{$order.status}"{/if}>{if isset($order.status_title)}{$order.status_title}{else}<i>Unregistered</i>{/if}</td>
                </tr>
                <tr height="32">
                    <td class="form-td-title-b">Created : </td>
                    <td>{$order.created_at|date_human}, {$order.author.login}</td>
                </tr>
                <tr height="32">
                    <td class="form-td-title-b">Modified : </td>
                    <td>{$order.modified_at|date_human}, {$order.modifier.login}</td>
                </tr>
            </table>
        </td>
    </tr>
</table>
<div class="pad1"></div>

<h3>Positions</h3>
{if !empty($positions)}
<table class="list"><tbody>
    <tr class="top-table">
        <th class="text-center" style="width: 3%;">Id</th>
        <th style="width: 8%;">Steel Grade</th>
        <th>Thickness<br><span class="lbl-dim">{if isset($order.dimension_unit)}{$order.dimension_unit}{/if}</span></th>
        <th>Width<br><span class="lbl-dim">{if isset($order.dimension_unit)}{$order.dimension_unit}{/if}</span></th>
        <th>Length<br><span class="lbl-dim">{if isset($order.dimension_unit)}{$order.dimension_unit}{/if}</span></th>
        <th>Unit Weight<br><span class="lbl-wgh">{if isset($order.weight_unit)}{$order.weight_unit}{/if}</span></th>
        <th>Qtty<br>pcs</th>
        <th>Weight<br><span class="lbl-wgh">{if isset($order.weight_unit)}{$order.weight_unit}{/if}</span></th>
        <th>Price<br><span class="lbl-price">{if isset($order.currency)}{if $order.currency == 'usd'}${elseif $order.currency == 'eur'}&euro;{else}{/if}{/if}/{if isset($order.weight_unit)}{$order.weight_unit}{/if}</span></th>
        <th>Value<br><span class="lbl-cur">{if isset($order.currency)}{if $order.currency == 'usd'}${elseif $order.currency == 'eur'}&euro;{else}{/if}{/if}</span></th>
        {if $order.status == 'ip'}
        <th>Balance<br>To Deliver, <span class="lbl-wgh">{if isset($order.weight_unit)}{$order.weight_unit}{/if}</span></th>
        {/if}
        <th style="width: 8%;">{if isset($order.delivery_point) && ($order.delivery_point == 'col' || $order.delivery_point == 'exw' || $order.delivery_point == 'fca')}Load Readiness{else}Delivery Time{/if}</th>
        <th>Stock<br>Delivery Time</th>
        <th style="width: 8%;">Internal Notes</th>
        <th style="width: 8%;">Location</th>
        <th style="width: 8%;"">Plate Ids</th>
        {if !empty($conflicted_items)}<th></th>{/if}
    </tr>
    {foreach from=$positions item=row}
    <tr>
        <td>{$row.position_id}</td>
        <td>{$row.steelgrade.title|escape:'html'}</td>
        <td>{if isset($row.thickness)}{$row.thickness|escape:'html'}{/if}</td>
        <td>{if isset($row.width)}{$row.width|escape:'html'}{/if}</td>
        <td>{if isset($row.length)}{$row.length|escape:'html'}{/if}</td>
        <td>{if isset($row.unitweight)}{$row.unitweight|escape:'html'|string_format:'%.2f'}{/if}</td>
        <td>{if isset($row.qtty)}{$row.qtty|escape:'html'|string_format:'%d'}{/if}</td>
        <td>{if isset($row.weight)}{$row.weight|escape:'html'|string_format:'%.2f'}{/if}</td>
        <td>{if isset($row.price)}{$row.price|escape:'html'|string_format:'%.2f'}{/if}</td>
        <td>{if isset($row.value)}{$row.value|escape:'html'|string_format:'%.2f'}{/if}</td>
        {if $order.status == 'ip'}
        <td>{if $row.balance_to_deliver.qtty > 0}{$row.balance_to_deliver.weight|string_format:'%.2f'} ({$row.balance_to_deliver.qtty}){else}<span style="color: #999;">delivered</span>{/if}</td>
        {/if}
        <td>{if isset($row.deliverytime) && !empty($row.deliverytime)}{$row.deliverytime|escape:'html'}{else}<i>not defined</i>{/if}</td>
        <td>{if isset($row.steelposition) && isset($row.steelposition.deliverytime)}{$row.steelposition.deliverytime.title|escape:'html'}{else}<i>not defined</i>{/if}</td>
        <td>{if isset($row.internal_notes)}{$row.internal_notes|escape:'html'}{else}<i>not defined</i>{/if}</td>
        <td>
        {if isset($row.location) && !empty($row.location)}
            {foreach name='location' from=$row.location item=location}
            {$location}{if !$smarty.foreach.location.last}, {/if}
            {/foreach}
        {else}<i>not defined</i>
        {/if}
        </td>
        <td>
        {if isset($row.plateid) && !empty($row.plateid)}
            {foreach name='plateid' from=$row.plateid item=plateid}
            {$plateid}{if !$smarty.foreach.plateid.last}, {/if}
            {/foreach}
        {else}<i>not defined</i>
        {/if}
        </td>
        {if !empty($conflicted_items)}<td></td>{/if}
    </tr>        
    {/foreach}
    <tr>
        <td colspan="6" class="form-td-title-b">Total : </td>
        <td class="text-center" id="lbl-total-qtty" style="font-weight: bold;">{if isset($total_qtty)}{$total_qtty}{else}0{/if}</td>
        <td class="text-center" id="lbl-total-weight" style="font-weight: bold;">{if isset($total_weight)}{$total_weight|string_format:'%.2f'}{else}0{/if}</td>
        <td></td>
        <td class="text-center" id="lbl-total-value" style="font-weight: bold;">{if isset($total_value)}{$total_value|string_format:'%.2f'}{else}0{/if}</td>
        {if $order.status == 'ip'}
        <td colspan="{if !empty($conflicted_items)}7{else}6{/if}"></td>
        {else}
        <td colspan="{if !empty($conflicted_items)}6{else}5{/if}"></td>
        {/if}
    </tr>
</tbody></table>

{else}<span id="lbl-positions"{if !empty($positions)} style="display: none;"{/if}>No positions</span>
{/if}
<div class="pad1"></div>
<table class="form" width="100%">
    <tr>
        <td class="text-top">
            <table class="form">
                <tr>
                    <td class="form-td-title-b text-top">Order Notes : </td>
                    <td>{if !empty($order.description)}{$order.description|escape:'html'|nl2br}{else}<i>not defined</i>{/if}</td>
                </tr>
            </table>
        </td>
        <td class="text-top"></td>
    </tr>
</table>