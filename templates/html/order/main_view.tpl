{*debug*}
{if isset($help)}
<!-- Button trigger modal -->
<button class="btn btn-primary btn-xs" data-toggle="modal" data-target="#Help" onClick="return false;" style=" margin-top: -8px">
        Help
      </button>

<!-- Modal -->
<div class="modal fade" id="Help" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title" id="Label">Help</h4>
      </div>
      <div class="modal-body">
        {$help.category.description}
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
{/if}
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
                    <td>{if isset($order.order_for_title)}{$order.order_for_title|escape:'html'}{else}{''|undef}{/if}</td>
                </tr>
                <tr height="32">
                    <td class="form-td-title-b">BIZ : </td>
                    <td>{if isset($order.biz)}{$order.biz.number_output}{else}{''|undef}{/if}</td>
                </tr>
                <tr height="32">
                    <td class="form-td-title-b">Buyer Company : </td>
                    <td>{if isset($order.company)}{$order.company.title}{else}{''|undef}{/if}</td>
                </tr>                
                <tr height="32">
                    <td class="form-td-title-b">Person : </td>
                    <td>{if isset($order.person)}{$order.person.full_name}{else}{''|undef}{/if}</td>
                </tr>
                <tr height="32">
                    <td class="form-td-title-b">Buyer Ref. : </td>
                    <td>{if !empty($order.buyer_ref)}{$order.buyer_ref|escape:'html'}{else}{''|undef}{/if}</td>
                </tr>
                <tr height="32">
                    <td class="form-td-title-b">Supplier Ref. : </td>
                    <td>{if !empty($order.supplier_ref)}{$order.supplier_ref|escape:'html'}{else}{''|undef}{/if}</td>
                </tr>
                <tr>
                    <td class="text-right" style="font-weight: bold;">Price  Equivalent : </td>
                    <td>{if !empty($order.price_equivalent)}{$order.price_equivalent|escape:'html'}{else}{''|undef}{/if}</td>
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
                    {else}{''|undef}{/if}                    
                    </td>
                </tr>                
                <tr height="32">
                    <td class="form-td-title-b">{if isset($order.delivery_point) && ($order.delivery_point == 'col' || $order.delivery_point == 'exw' || $order.delivery_point == 'fca')}Load Readiness{else}Delivery Time{/if} : </td>
                    <td>{if !empty($order.delivery_date)}{$order.delivery_date}{else}{''|undef}{/if}</td>
                </tr>
                <tr height="32"{if isset($order.delivery_point) && ($order.delivery_point == 'col' || $order.delivery_point == 'exw' || $order.delivery_point == 'fca')} style="display: none;"{/if}>
                    <td class="form-td-title-b">Delivery Cost : </td>
                    <td>{if !empty($order.delivery_cost)}{$order.delivery_cost|escape:'html'}{else}{''|undef}{/if}</td>
                    {*<td>{if $order.delivery_cost > 0}{$order.delivery_cost|escape:'html'|string_format:"%.2f"}{if isset($order.currency_sign)} {$order.currency_sign}{/if}{else}--{/if}</td>*}
                </tr>
                <tr height="32">
                    <td class="form-td-title-b">Order Alert Date : </td>
                    {if isset($order.alert_date) && !empty($order.alert_date)}
                    <td{if $order.status != 'co' && $order.status != 'ca'}{if $order.quick.days_to_alert <= 1} class="td-order-delivery-0"{elseif $order.quick.days_to_alert <= 5} class="td-order-delivery-5"{/if}{/if}>
                        {$order.alert_date|date_human}
                    </td>
                    {else}
                    <td>{''|undef}</td>
                    {/if}
                </tr>
            </table>        
        </td>
        <td width="25%" valign="top" style="vertical-align: top;">
            <table class="form" width="100%">
                <tr height="32">
                    <td class="form-td-title-b">Invoicing Basis : </td>
                    <td>{if isset($order.invoicingtype)}{$order.invoicingtype.title|escape:'html'}{else}{''|undef}{/if}</td>
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
                    <td class="text-right" style="font-weight: bold;">Order Type : </td>
                    <td>{if $order.type == 'so'}Self-service{else}Through dialog{/if}</td>
                </tr>
                <tr height="32">
                    <td class="text-right" style="font-weight: bold;">Order Status : </td>
                    <td{if !empty($order.status)} class="tr-order-{$order.status}"{/if}>{if isset($order.status_title)}{$order.status_title}{else}<i>Unregistered</i>{/if}</td>
                </tr>
            </table>
        </td>
    </tr>
</table>
<div class="pad1"></div>

<h3>Positions</h3>
{if !empty($positions)}
<table id="positions" class="list" width="100%"><tbody>
    <tr class="top-table">
        <th width="3%" class="text-center">Id</th>
        <th width="8%">Steel Grade</th>
        <th>Thickness<br><span class="lbl-dim">{if isset($order.dimension_unit)}{$order.dimension_unit}{/if}</span></th>
        <th>Width<br><span class="lbl-dim">{if isset($order.dimension_unit)}{$order.dimension_unit}{/if}</span></th>
        <th>Length<br><span class="lbl-dim">{if isset($order.dimension_unit)}{$order.dimension_unit}{/if}</span></th>
        <th>Unit Weight<br><span class="lbl-wgh">{if isset($order.weight_unit)}{$order.weight_unit}{/if}</span></th>
        <th>Qtty<br>pcs</th>
        <th>Weight<br><span class="lbl-wgh">{if isset($order.weight_unit)}{$order.weight_unit}{/if}</span></th>
        <th>Price<br><span class="lbl-price">{if isset($price_unit)}{$order.currency|cursign}/{$price_unit}{/if}</span></th>
        <th>Value<br><span class="lbl-cur">{if isset($order.currency)}{$order.currency|cursign}{/if}</span></th>
        {if $order.status == 'ip'}
        <th>Balance<br>To Deliver, <span class="lbl-wgh">{if isset($order.weight_unit)}{$order.weight_unit}{/if}</span></th>
        {/if}
        <th width="8%">{if isset($order.delivery_point) && ($order.delivery_point == 'col' || $order.delivery_point == 'exw' || $order.delivery_point == 'fca')}Load Readiness{else}Delivery Time{/if}</th>
        <th>Stock<br>Delivery Time</th>
        <th width="8%">Internal Notes</th>
        <th width="8%">Location</th>
        <th width="8%">Plate Ids</th>
        {if !empty($conflicted_items)}<th></th>{/if}
    </tr>
    {foreach from=$positions item=row}
    <tr>
        <td{if $order.status != 'ca'} onclick="location.href='/order/selectitems/{$order.id}/position:{$row.position_id}';"{/if}>{$row.position_id}</td>
        <td{if $order.status != 'ca'} onclick="location.href='/order/selectitems/{$order.id}/position:{$row.position_id}';"{/if}>{$row.steelgrade.title|escape:'html'}</td>
        <td{if $order.status != 'ca'} onclick="location.href='/order/selectitems/{$order.id}/position:{$row.position_id}';"{/if}>{if isset($row.thickness)}{$row.thickness|escape:'html'}{/if}</td>
        <td{if $order.status != 'ca'} onclick="location.href='/order/selectitems/{$order.id}/position:{$row.position_id}';"{/if}>{if isset($row.width)}{$row.width|escape:'html'}{/if}</td>
        <td{if $order.status != 'ca'} onclick="location.href='/order/selectitems/{$order.id}/position:{$row.position_id}';"{/if}>{if isset($row.length)}{$row.steelitems[0].steelitem.length}{/if}</td>
        <td{if $order.status != 'ca'} onclick="location.href='/order/selectitems/{$order.id}/position:{$row.position_id}';"{/if}>
            {if isset($row.unitweight)}
                {if $order.weight_unit == 'lb'}
                    {$row.unitweight|number_format:0|escape:'html'}
                {else}
                    {$row.unitweight|number_format:2|escape:'html'}
                {/if}
            {/if}
        </td>
        <td{if $order.status != 'ca'} onclick="location.href='/order/selectitems/{$order.id}/position:{$row.position_id}';"{/if}>{if isset($row.qtty)}{$row.qtty|escape:'html'|string_format:'%d'}{/if}</td>
        <td{if $order.status != 'ca'} onclick="location.href='/order/selectitems/{$order.id}/position:{$row.position_id}';"{/if}>
            {if isset($row.weight)}
                {if $order.weight_unit == 'lb'}
                    {$row.weight|number_format:0|escape:'html'}
                {else}
                    {$row.weight|number_format:2|escape:'html'}
                {/if}
            {/if}
        </td>
        <td{if $order.status != 'ca'} onclick="location.href='/order/selectitems/{$order.id}/position:{$row.position_id}';"{/if}>
            {if isset($row.price)}{$row.price|escape:'html'|string_format:'%.2f'}{/if}{if !isset($price_unit)}&nbsp;{$row.steelposition.currency|cursign}/{$row.steelposition.price_unit}{/if}
        </td>
        <td{if $order.status != 'ca'} onclick="location.href='/order/selectitems/{$order.id}/position:{$row.position_id}';"{/if}>{if isset($row.value)}{$row.value|escape:'html'|string_format:'%.2f'}{/if}</td>
        {if $order.status == 'ip'}
            <td{if $order.status != 'ca'} onclick="location.href='/order/selectitems/{$order.id}/position:{$row.position_id}';"{/if}>
                {if $row.balance_to_deliver.qtty > 0}
                    {if $order.weight_unit == 'lb'}
                        {$row.balance_to_deliver.weight|number_format:0|escape:'html'} ({$row.balance_to_deliver.qtty})
                    {else}
                        {$row.balance_to_deliver.weight|number_format:2|escape:'html'} ({$row.balance_to_deliver.qtty})
                    {/if}
                {else}
                    <span style="color: #999;">delivered</span>
                {/if}
            </td>
        {/if}
        <td{if $order.status != 'ca'} onclick="location.href='/order/selectitems/{$order.id}/position:{$row.position_id}';"{/if}>{if isset($row.deliverytime) && !empty($row.deliverytime)}{$row.deliverytime|escape:'html'}{else}{''|undef}{/if}</td>
        <td{if $order.status != 'ca'} onclick="location.href='/order/selectitems/{$order.id}/position:{$row.position_id}';"{/if}>{if isset($row.steelposition) && isset($row.steelposition.deliverytime)}{$row.steelposition.deliverytime.title|escape:'html'}{else}{''|undef}{/if}</td>
        <td{if $order.status != 'ca'} onclick="location.href='/order/selectitems/{$order.id}/position:{$row.position_id}';"{/if}>{if isset($row.internal_notes)}{$row.internal_notes|escape:'html'}{else}{''|undef}{/if}</td>
        <td{if $order.status != 'ca'} onclick="location.href='/order/selectitems/{$order.id}/position:{$row.position_id}';"{/if}>
        {if isset($row.location) && !empty($row.location)}
            {foreach name='location' from=$row.location item=location}
                {$location}{if !$smarty.foreach.location.last}, {/if}
            {/foreach} ({*$row.steelposition|@debug_print_var*})
            
        {else}
        
        {''|undef}
        {/if}

        </td>
        <td{if $order.status != 'ca'} onclick="location.href='/order/selectitems/{$order.id}/position:{$row.position_id}';"{/if}>
            {if isset($row.plateid) && !empty($row.plateid)}
                {*foreach name='plateid' from=$row.plateid item=plateid}
                    {$plateid} ({}){if !$smarty.foreach.plateid.last}<br> {/if}
                {/foreach*} 
                {foreach name='steelitem' from=$row.steelitems item=subrow}
                    <nobr>{$subrow.steelitem.guid} ({$subrow.steelitem.status_title})</nobr>{if !$smarty.foreach.plateid.last}<br> {/if}
                {/foreach} 
            {else}
                {''|undef}
            {/if}
        </td>
        {if !empty($conflicted_items)}<td>{if isset($row.is_conflicted)}<img src="/img/icons/exclamation-red.png" onclick="location.href='/order/selectitems/{$order.id}/position:{$row.position_id}';" title="Conflicted Items" alt="Conflicted Items">{else}{/if}</td>{/if}
    </tr>        
    {/foreach}
    <tr>
        <td colspan="6" class="form-td-title-b">Total : </td>
        <td class="text-center" id="lbl-total-qtty" style="font-weight: bold;">{if isset($total_qtty)}{$total_qtty}{else}0{/if}</td>
        <td class="text-center" id="lbl-total-weight" style="font-weight: bold;">
            {if isset($total_weight)}
                {if $order.weight_unit == 'lb'}
                    {$total_weight|number_format:0}
                {else}
                    {$total_weight|number_format:2}
                {/if}
            {else}
                0
            {/if}
        </td>
        <td></td>
        <td class="text-center" id="lbl-total-value" style="font-weight: bold;">{if isset($total_value)}{$total_value|string_format:'%.2f'}{else}0{/if}</td>
        {if $order.status == 'ip'}
        <td colspan="{if !empty($conflicted_items)}7{else}6{/if}"></td>
        {else}
        <td colspan="{if !empty($conflicted_items)}6{else}5{/if}"></td>
        {/if}
    </tr>
</tbody></table>
{if $order.status != 'ca'}Click position to view{if $order.status != 'co'} / change{/if} ordered items .{/if}
{else}
<span id="lbl-positions"{if !empty($positions)} style="display: none;"{/if}>No positions</span>
{/if}

<div class="pad"></div>
<h3>Notes</h3>
{if !empty($order.description)}{$order.description|escape:'html'|nl2br}{else}{''|undef}{/if}


<div class="pad"></div>
<h3>Related Documents</h3>


				{if $document|@count>0}
				<ul>
				{foreach from=$document item=row}
					{$row}
               {if $row.object_id>0}
					<li>{$row.plate_id}  {$row.object_title} : <a href="/{$row.object_alias}/{$row.object_id}" target="_blank">{$row.object_alias}&nbsp;(#&nbsp;{$row.object.number})</a></li>
					{/if}
					{*<a href="/{$row.object_alias}/{$row.object_alias}">{$row.object.number}</a>*}
					
				{/foreach}
				</ul>
				{/if}
{if !empty($related_docs_list)}
    {foreach $related_docs_list as $row}
    <div style="float: left; margin: 0 10px 10px 0;"><a class="tag-document" href="/{$row.object_alias}/{$row.object_id}">{$row.doc_no}</a>,&nbsp;{if isset($row.modified_at) && !empty($row.modified_at) && $row.modified_at > 0}{$row.modified_at|date_format:'d/m/Y'}&nbsp;by&nbsp;{$row.modifier.login}{else}{$row.created_at|date_human:true}&nbsp;by&nbsp;{$row.author.login}{/if}</div>
    {/foreach}
{else}{''|undef}
{/if}
<div class="separator pad"></div>
{include file='templates/controls/object_shared_files.tpl' object_alias='order' object_id=$order.id}
