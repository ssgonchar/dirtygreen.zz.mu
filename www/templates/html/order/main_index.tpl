{*debug*}
<table width="100%">
    <tr>
        <td width="30%" class="text-top">
            <table class="form">
                <tr>
                    <td width="85px" class="text-right">Order For :</td>
                    <td>
                        <select id="stock" name="form[order_for]" class="normal">
                            <option value=""{if empty($order_for)} selected="selected"{/if}>--</option>
                            <option value="mam"{if $order_for == 'mam'} selected="selected"{/if}>MaM</option>
                            <option value="pa"{if $order_for == 'pa'} selected="selected"{/if}>PlatesAhead</option>
                            {*  <option value="mam"{if $order_for == 'mam'} selected="selected"{/if}>MaM (all)</option>   *}
                            {* foreach from=$companies item=row}
                            <option value="{$row.company.alias}"{if $order_for == $row.company.alias} selected="selected"{/if}>{$row.company.title|escape:'html'}</option>
                            {/foreach *}
                        </select>            
                    </td>
                </tr>
                <tr>
                    <td class="text-right">Biz :</td>
                    <td>
                        <input type="text" id="order-list-biz" name="form[biz_title]" class="normal biz-autocomplete"{if isset($biz_title)} value="{$biz_title|escape:'html'}"{/if}>
                        <input type="hidden" id="order-list-biz-id" name="form[biz_id]" value="{if isset($biz_id)}{$biz_id}{else}0{/if}">
                    </td>
                </tr>
                <tr>
                    <td class="text-right">Buyer Co :</td>
                    <td>
                        <input type="text" id="company_title" name="form[company_title]" class="normal"{if isset($company_title)} value="{$company_title|escape:'html'}"{/if} placeholder="start typing to select">
                        <input type="hidden" id="company_id" name="form[company_id]" value="{if isset($company_id)}{$company_id}{else}0{/if}">
                    </td>
                </tr>
                <tr>
                    <td class="text-right">Keyword :</td>
                    <td>
                        <input type="text" name="form[keyword]" class="normal"{if isset($keyword)} value="{$keyword|escape:'html'}"{/if}>
                    </td>
                </tr> 
                <!--
                <tr>
                    <td class="text-right">View with Items :</td>
                    <td>
                        <input type="checkbox" name="" class="view_item" checked onClick="itemsToogle(this.checked);">
                    </td>
                </tr>
-->                
            </table>        
        </td>
        <td width="30%" class="text-top">
            <table class="form">
                <tr>
                    <td width="85px" class="text-right">Order Type :</td>
                    <td>
                        <select id="stock" name="form[type]" class="normal">
                            <option value=""{if empty($type)} selected="selected"{/if}>--</option>
                            <option value="do"{if $type == 'do'} selected="selected"{/if}>Through dialogue</option>
                            <option value="so"{if $type == 'so'} selected="selected"{/if}>Self-service</option>
                        </select>                        
                    </td>
                </tr>
                <tr>
                    <td class="text-right">Period From :</td>
                    <td>
                        <input type="text" id="period_from" name="form[period_from]" class="normal" value="{if !empty($period_from)}{$period_from|escape:'html'|date_format:'d/m/Y'}{/if}">
                    </td>
                </tr>
                <tr>
                    <td class="text-right">Period To :</td>
                    <td>
                        <input type="text" id="period_to" name="form[period_to]" class="normal" value="{if !empty($period_to)}{$period_to|escape:'html'|date_format:'d/m/Y'}{/if}">
                    </td>
                </tr>
                <tr>
                    <td class="text-right">Order Status : </td>
                    <td>
                        <select name="form[status]" class="normal">
                            <option value=""{if !isset($status) || empty($status) == ''} selected="selected"{/if}>--</option>
                            <option value="nw"{if isset($status) && $status == 'nw'} selected="selected"{/if}>New (WebStock)</option>
                            <option value="ip"{if isset($status) && $status == 'ip'} selected="selected"{/if}>In Process</option>
                            <option value="de"{if isset($status) && $status == 'de'} selected="selected"{/if}>To be Invoiced</option>
                            <option value="co"{if isset($status) && $status == 'co'} selected="selected"{/if}>Completed</option>
                            <option value="ca"{if isset($status) && $status == 'ca'} selected="selected"{/if}>Cancelled</option>
                        </select>
                    </td>
                </tr>                
            </table>        
        </td>
        <td width="30%" class="text-top">
            <table class="form">
                <tr>
                    <td width="85px" class="text-right">Steel Grade :</td>
                    <td>
                        <select name="form[steelgrade_id]" class="normal">
                            <option value="0"{if empty($steelgrade_id)} selected="selected"{/if}>--</option>
                            {foreach from=$steelgrades item=row}{if isset($row.steelgrade)}
                            <option value="{$row.steelgrade.id}"{if $row.steelgrade.id == $steelgrade_id} selected="selected"{/if}>{$row.steelgrade.title|escape:'html'}</option>
                            {/if}{/foreach}
                        </select>                        
                    </td>
                </tr>
                <tr>
                    <td class="text-right">Thickness :</td>
                    <td>
                        <input type="text" name="form[thickness]" class="normal" value="{if isset($thickness)}{$thickness}{/if}">
                    </td>
                </tr>
                <tr>
                    <td class="text-right">Width :</td>
                    <td>
                        <input type="text" name="form[width]" class="normal" value="{if isset($width)}{$width}{/if}">
                    </td>
                </tr>
            </table>        
        </td>
        <td class="text-center"><input type="submit" name="btn_select" value="Select" class="btn100b"></td>
    </tr>
</table>
<div class="pad"></div>

{if empty($list)}
    {if isset($filter)}Nothing was found on my request{/if}
{else}
<div id="" class="table-responsive search-target">
   <div class="row">
        <div class="panel panel-primary filterable">
            <!--<div class="panel-heading">
                <h3 class="panel-title">Orders</h3>
                <div class="pull-right">
                    <button class="btn btn-default btn-filter">Use table filters</button>
                </div>
            </div>-->
            <table id="orders" class="list table" title="Orders" width="100%">
            <thead>
                <tr class="filters">
                    <th data-field="id"><input type="text" class="form-control" placeholder="#" disabled></th>
                    <th data-field="for"><input type="text" class="form-control" placeholder="Order for" disabled></th>
                    {* <th>Type</th> *}
                    <th data-field="biz"><input type="text" class="form-control" placeholder="Biz" disabled></th>
                    <th data-field="buyer"><input type="text" class="form-control" placeholder="Buyer" disabled></th>
                    <th data-field="buyer_href"><input type="text" class="form-control" placeholder="Buyer ref" disabled></th>
                    {* <th>Supplier Ref</th> *}
                    <th data-field="delivery_point"><input type="text" class="form-control" placeholder="Delivery point" disabled></th>
                    <th class="text-center" data-field="delivery_date"><input type="text" class="form-control" placeholder="Delivery date" disabled></th>
                    <th class="text-center" data-field="wight"><input type="text" class="form-control" placeholder="Weight" disabled></th>
                    <th class="text-center" data-field="balance"><input type="text" class="form-control" placeholder="Balance to deliver" disabled></th>
                    <th class="text-center" data-field="value"><input type="text" class="form-control" placeholder="Value" disabled></th>
                    <th class="text-center" data-field="status"><input type="text" class="form-control" placeholder="Status" disabled></th>
                    <th class="text-center" data-field="modified"><input type="text" class="form-control" placeholder="Modified" disabled></th>
                    {if isset($has_in_processing)}
                    <th class="text-center" data-field="column1"></th>
                    <th class="text-center" data-field="column2"><input type="checkbox" class="check-all-orders-in-process" style="margin: 5px;"></th>
                    {/if}
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
            {foreach from=$list item=row}
                <tr id="order-{$row.order.id}"{if !empty($row.order.status)} class="fl tr-order-{$row.order.status}"{/if}>
                <td onclick="location.href='/order/{$row.order.id}';" class="view_item" rowspan=''>{$row.order.id}</td>
                <td onclick="location.href='/order/{$row.order.id}';">{if isset($row.order.order_for_title)}{$row.order.order_for_title}{else}{''|undef}{/if}</td>
                {* <td onclick="location.href='/order/{$row.order.id}';">{if $row.order.type == 'so'}Self-service{else}Through dialog{/if}</td> *}
                <td onclick="location.href='/order/{$row.order.id}';">{if isset($row.order.biz)}{$row.order.biz.doc_no}{else}{''|undef}{/if}</td>
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
                        {$row.order.quick.weight|number_format:2} {$row.order.weight_unit|wunit}
                        ({$row.order.quick.qtty})
                    {else}{''|undef}{/if}
                </td>
                <td onclick="location.href='/order/{$row.order.id}';" class="text-center">
                    {if $row.order.balance_to_deliver.qtty > 0}
                        {$row.order.balance_to_deliver.weight|string_format:'%.2f'} ({$row.order.balance_to_deliver.qtty})
                    {/if}
                </td>
                <td onclick="location.href='/order/{$row.order.id}';" class="text-center">{if !empty($row.order.quick.value)}{$row.order.quick.value|number_format:2:false} {$row.order.currency|cursign}{/if}</td>
                <td onclick="location.href='/order/{$row.order.id}';" class="text-center"{if $row.order.status == 'nw'} style="background-color: #ffffff; font-weight: bold;"{/if}>{if isset($row.order.status_title)}{$row.order.status_title}{else}<i>Unregistered</i>{/if}</td>
                <td onclick="location.href='/order/{$row.order.id}';" class="text-center">
                    {$row.order.modified_at|date_human}<br>
                    by {$row.order.modifier.login|escape:'html'}
                </td>
                {if isset($has_in_processing)}
                <td>{if $row.order.status != 'ca' && $row.order.status != 'co'}<img src="/img/icons/pencil-small.png" onclick="location='/order/{$row.order.id}/edit';">{/if}</td>                
                <td>{if $row.order.status == 'ip'}<input type="checkbox" name="selected_ids[]" class="order-in-process" value="{$row.order.id}">{/if}</td>
                {/if}
                <td>
                    <button class="btn btn-primary btn-xs" onClick="show_items(this, {$row.order.id},''); return false;">Show items</button>
                </td>
            </tr>
{*
			<tr class="view_item">
				<td colspan='12'>
					<table width='100%'>
						<tr class="top-table">
							<!--<th>Id</th>-->
							<th style="width: 10%;">Plate Id</th>
							<th style="width: 15%;">Steel Grade</th>
							<th style="width: 10%;">Price, {$row.order.all_items[0].currency}/{$row.order.all_items[0].price_unit}</th>
							<th style="width: 10%;">Thickness, {$row.order.all_items[0].dimension_unit}</th>
							<th style="width: 10%;">Width, {$row.order.all_items[0].dimension_unit}</th>
							<th style="width: 10%;">Length, {$row.order.all_items[0].dimension_unit}</th>
							<th style="width: 10%;">Weight, {$row.order.all_items[0].weight_unit}</th>
						</tr>
						
						{foreach from=$row.order.all_items item=item_in}
						<tr>
							<!--<td style="background:{$item_in.steelgrade_color};">{$item_in.id}</td>-->
							<td style="/*background:{$item_in.steelgrade_color};*/">{$item_in.guid|undef}</td>
							<td style="/*background:{$item_in.steelgrade_color};*/">{$item_in.steelgrade_title|undef}</td>
							<td style="/*background:{$item_in.steelgrade_color};*/">{round($item_in.price)|undef}</td>
							<td style="/*background:{$item_in.steelgrade_color};*/">
								{if $item_in.dimension_unit == 'in'}
									{$item_in.thickness}
								{/if}
								{if $item_in.dimension_unit == 'mm'}
									{round($item_in.thickness_mm)|undef}
								{/if}								
							</td>
							<td style="/*background:{$item_in.steelgrade_color};*/">
								{if $item_in.dimension_unit == 'in'}
									{$item_in.width}
								{/if}
								{if $item_in.dimension_unit == 'mm'}
									{round($item_in.width_mm)|undef}
								{/if}								
							</td>
							<td style="/*background:{$item_in.steelgrade_color};*/">
								{if $item_in.dimension_unit == 'in'}
									{$item_in.length}
								{/if}
								{if $item_in.dimension_unit == 'mm'}
									{round($item_in.length_mm)|undef}
								{/if}							
							</td>
							<td style="/*background:{$item_in.steelgrade_color};*/">
								{if $item_in.weight_unit == 'lb'}
									{$item_in.unitweight}
								{/if}
								{if $item_in.weight_unit == 'mt'}
									{round($item_in.unitweight_ton)|undef}
								{/if}							
							</td>
						</tr>
						{/foreach}						
					</table>
				</td>
			</tr>
*}
            {/foreach}
        </tbody>
    </table>
</div>
</div>
</div>
{/if}


