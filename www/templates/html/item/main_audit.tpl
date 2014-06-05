{if !isset($page) || ($page != 'ownerless' && $page != 'stockholderless')}
<table width="100%">
    <tr>
        <td width="30%" class="text-top">
            <table class="form" width="100%">
                <tr>
                    <td class="form-td-title-b">Stock :</td>
                    <td>
                        <select id="stock" name="form[stock_id]" class="normal" onchange="bind_items_filter();">
                            <option value="0"{if empty($stock_id)} selected="selected"{/if}>--</option>
                            {foreach from=$stocks item=row}
                            <option value="{$row.stock.id}"{if !empty($stock_id) && $stock_id == $row.stock.id} selected="selected"{/if}>{$row.stock.title|escape:'html'}</option>
                            {/foreach}
                        </select>        
                    </td>                    
                </tr>
                <tr>
                    <td class="form-td-title-b">Date to:</td>
                    <td>
                        <input type="text" id="dateto" name="form[dateto]" class="datepicker" value="{if !empty($date_to)}{$date_to}{/if}" style="width:100%">
                    </td>                    
                </tr>
            </table>
        </td>
        <td width="60%" class="text-top">
            <table class="form" width="100%">
                <tr height="32">
                    <td class="form-td-title">Location :</td>
                    <td id="locations">
                        {if !empty($locations)}
                            {if count($locations) == 1}
                                {$locations[0].company.title|escape:'html'}
                            {else}
                                {foreach from=$locations item=row}
                                <div style="float: left; margin-right: 5px;">
                                <label for="cb-location-{$row.company.id}"><input type="checkbox" id="cb-location-{$row.company.id}" name="form[stockholder][{$row.company.id}]" value="{$row.company.id}"{if isset($row.company.selected)} checked="checked"{/if}>&nbsp;{$row.company.doc_no|escape:'html'}&nbsp;({$row.company.city.title|escape:'html'})&nbsp;&nbsp;</label>
                                </div>
                                {/foreach}
                                <div class="separator"></div>
                            {/if}
                        {else}
                        <span style="color: #aaa;">{if empty($stock_id)}Please select stock first{else}none{/if}</span>
                        {/if}
                    </td>
                </tr>
				{*
                <tr height="32">
                    <td class="form-td-title">Type :</td>
                    <td>
                        <label for="cb-type-r"><input type="checkbox" id="cb-type-r" name="form[type][r]" value="r"{if isset($type_r)} checked="checked"{/if}>&nbsp;Real&nbsp;&nbsp;&nbsp;</label>
                        <label for="cb-type-v"><input type="checkbox" id="cb-type-v" name="form[type][v]" value="v"{if isset($type_v)} checked="checked"{/if}>&nbsp;Virtual&nbsp;&nbsp;&nbsp;</label>
                        <label for="cb-type-t"><input type="checkbox" id="cb-type-t" name="form[type][t]" value="t"{if isset($type_t)} checked="checked"{/if}>&nbsp;Twin&nbsp;&nbsp;&nbsp;</label>
                        <label for="cb-type-c"><input type="checkbox" id="cb-type-c" name="form[type][c]" value="c"{if isset($type_c)} checked="checked"{/if}>&nbsp;Cut&nbsp;&nbsp;&nbsp;</label>
                        <label for="cb-type-a"><input type="checkbox" id="cb-type-a" name="form[available]" value="1"{if isset($available) && !empty($available)} checked="checked"{/if}>&nbsp;Only Available Items</label>
                    </td>
                </tr>   
*}				
            </table>
        </td>
        <td width="10%" class="text-right text-middle" style="padding-right: 0;">
            <input type="submit" name="btn_select" value="Select" class="btn100b">
        </td>
    </tr>
</table>
<!--
<a id="a-show-params" href="javascript: void(0);" class="opendown" onclick="show_more_params();"{if isset($params)} style="display:none"{/if}>More Params</a>
<div id="more-params" {if !isset($params)} style="display:none"{/if}>
    <table width="100%">
        <tr>
            <td width="30%" class="text-top">
                <table class="form" width="100%">
                    <tr>
                        <td class="form-td-title">Thickness :</td>
                        <td><nobr><input type="text" name="form[thickness]" class="normal"{if isset($thickness)} value="{$thickness}"{/if}><span class="size">{if isset($stock)}{$stock.dimension_unit|dunit}{/if}</span></nobr></td>                    
                    </tr>
                    <tr>
                        <td class="form-td-title">Width :</td>
                        <td><nobr><input type="text" name="form[width]" class="normal"{if isset($width)} value="{$width}"{/if}><span class="size">{if isset($stock)}{$stock.dimension_unit|dunit}{/if}</span></nobr></td>                    
                    </tr>
                    <tr>
                        <td class="form-td-title">Length :</td>
                        <td><nobr><input type="text" name="form[length]" class="normal"{if isset($length)} value="{$length}"{/if}><span class="size">{if isset($stock)}{$stock.dimension_unit|dunit}{/if}</span></nobr></td>                    
                    </tr>                
                </table>
            </td>
            <td width="30%" class="text-top">
                <table class="form" width="100%">
                    <tr>
                        <td class="form-td-title">Steel Grade :</td>
                        <td>
                            <select id="steelgrade" name="form[steelgrade_id]" class="normal">
                                <option value="0">--</option>
                                {foreach from=$steelgrades item=row}
                                <option value="{$row.steelgrade.id}"{if isset($steelgrade_id) && $steelgrade_id == $row.steelgrade.id} selected="selected"{/if}>{$row.steelgrade.title|escape:'html'}</option>
                                {/foreach}
                            </select>                        
                        </td>
                    </tr>
                    <tr>
                        <td class="form-td-title">Weight :</td>
                        <td><nobr><input type="text" name="form[weight]" class="normal"{if isset($weight)} value="{$weight}"{/if}><span class="weight">{if isset($stock)}{$stock.weight_unit|wunit}{/if}</span></nobr></td>
                    </tr>
                    <tr>
                        <td class="form-td-title">Notes :</td>
                        <td><input type="text" name="form[notes]" class="normal"{if isset($notes)} value="{$notes}"{/if}></td>
                    </tr>
                </table>
            </td>
            <td width="30%" class="text-top">
                <table class="form" width="100%">
                    <tr>
                        <td class="form-td-title">Order :</td>
                        <td>
                            <select name="form[order_id]" id="order" class="normal">
                                <option value="0"{if empty($order_id)} selected="selected"{/if}>--</option>
                                {foreach $orders as $row}
                                <option value="{$row.order_id}"{if !empty($order_id) && $order_id == $row.order_id} selected="selected"{/if}>{$row.order.doc_no_full|escape:'html'}</option>
                                {/foreach}
                            </select>
                        </td>
                   </tr>
{*
                    <tr>
                        <td class="form-td-title">Revision Date :</td>
                        <td>
                            <input class="datepicker" type="text" id="rev_date" name="form[rev_date]" value="{if !empty($rev_date)}{$rev_date|escape:'html'}{/if}" style="width: 100px;" onchange="$('#tr-revision').addClass('revision');">
                            {if isset($rev_date)}
                            <a href="javascript: void(0);" onclick="clear_revision();" style="margin-left: 10px;">Clear</a>
                            {/if}                    
                        </td>
                    </tr>
                    <tr>
                        <td class="form-td-title">Revision Time :</td>
                        <td>
                            <select name="form[rev_time]" id="rev_time" class="narrow">
                                <option value="00:00"{if !empty($rev_time) && $rev_time == '00:00'} selected="selected"{/if}></option>
                                <option value="03:00"{if !empty($rev_time) && $rev_time == '03:00'} selected="selected"{/if}>03:00</option>
                                <option value="06:00"{if !empty($rev_time) && $rev_time == '06:00'} selected="selected"{/if}>06:00</option>
                                <option value="09:00"{if !empty($rev_time) && $rev_time == '09:00'} selected="selected"{/if}>09:00</option>
                                <option value="12:00"{if !empty($rev_time) && $rev_time == '12:00'} selected="selected"{/if}>12:00</option>
                                <option value="15:00"{if !empty($rev_time) && $rev_time == '15:00'} selected="selected"{/if}>15:00</option>
                                <option value="18:00"{if !empty($rev_time) && $rev_time == '18:00'} selected="selected"{/if}>18:00</option>
                                <option value="21:00"{if !empty($rev_time) && $rev_time == '21:00'} selected="selected"{/if}>21:00</option>
                                <option value="23:59"{if !empty($rev_time) && $rev_time == '23:59'} selected="selected"{/if}>24:00</option>
                            </select>                    
                        </td>
                    </tr>
*}                    
                </table>
            </td>
            <td width="10%" class="text-right text-middle" style="padding-right: 0;">
            </td>
        </tr>
    </table>
    
    <a id="a-show-params" href="javascript: void(0);" class="closeup" onclick="hide_more_params();">Hide Params</a>
</div>
-->
{if !empty($baddatastat) && ($baddatastat.ownerless > 0 || $baddatastat.stockholderless > 0)}
<div class="pad-10"></div>
{/if}
<div class="pad1" style="text-align: right;">
    {if !empty($baddatastat) && $baddatastat.ownerless > 0}
    <a href="/items/ownerless" style="color: white; background: red; padding: 3px;">{number value=$baddatastat.ownerless e0='items' e1='item' e2='items'} without owner</a>
    {/if}
    {if !empty($baddatastat) && $baddatastat.stockholderless > 0}
    <a href="/items/stockholderless" style="color: white; background: red; padding: 3px;">{number value=$baddatastat.stockholderless e0='items' e1='item' e2='items'} without stockholder</a>
    {/if}
</div>
<hr style="width: 100%; color: #dedede;" size="1"/>
<div class="pad1"></div>
{/if}
{if empty($list)}
    {if isset($filter)}Nothing was found on my request{/if}
{else}
	<h2>Stock Report as at {$date_to}</h2>
	<h3>Generated on {$today}</h3>
    <table class="list search-target" width="100%">
        <tbody>
            <tr class="top-table">
                <!--{if empty($is_revision)}<th width="2%" class="td-item-checkbox"><input type="checkbox" onchange="check_all(this, 'item'); calc_selected();" style="margin-left: 2px; margin-right: 5px;"></th>{/if}-->
                <th width="2%">Id</th>
                <th width="5%">Plate Id</th>
                <th width="8%">Steel Grade</th>
                <th width="5%" class="text-center">Thickness,<br>{$item_dimension_unit}</th>
                <th width="5%" class="text-center">Width,<br>{$list[1].dimension_unit}</th>
                <th width="5%" class="text-center">Length,<br>{$list[1].dimension_unit}</th>
                <th width="7%" class="text-center">Weight,<br>{$list[1].weight_unit}</th>
                <th width="7%" class="text-center">Supplier Name</th>
                <th width="7%" class="text-center">Purchase Invoice No</th>
                <th width="7%" class="text-center">Purchase Invoice Date</th>
                <th width="7%" class="text-center">On stock<br>(LAST YEAR)</th>
                <th width="7%" class="text-center">Status today<br></th>
                
                
                <th>In DDT</th>
                <th>DDT date</th>
                {*<th>Internal Notes</th>*}
                <th>Location</th>
               <!-- <th>Owner</th>
                <th>Status</th>
                <th width="3%">CE Mark</th>-->
{*
                <th>Condition</th>
                <th>Order</th>
*}				
				<th width="7%" class="text-center">Purchase Price, {$list[1].price_unit}/{$list[1].weight_unit}</th>
				<th width="7%" class="text-center">Current Stock Price, {$list[1].price_unit}/{$list[1].weight_unit}</th>  
				<th width="7%" class="text-center">Sales price, {$list[1].price_unit}/{$list[1].weight_unit}</th>  
				<th width="7%" class="text-center">Valuation price, {$list[1].price_unit}/{$list[1].weight_unit}</th>  
            </tr>
            {foreach from=$list item=row}
			{if isset($row.id)}
            <tr id="item-{$row.steelitem.id}"{if $row.steelitem.status_id >= $smarty.const.ITEM_STATUS_ORDERED} class="item-status-{$row.steelitem.status_id}"{/if}>
                <!--
				{if empty($is_revision)}
                <td class="td-item-checkbox">
                    {if empty($target_doc) 
                        || ($target_doc == 'qc')
                        || ($target_doc == 'inddt' && empty($row.steelitem.in_ddt_id))
                        || ($target_doc == 'ra' && $ra.ra.stockholder_id == $row.steelitem.stockholder_id && $row.steelitem.owner_id > 0 && $row.steelitem.status_id != $smarty.const.ITEM_STATUS_RELEASED && $row.steelitem.status_id != $smarty.const.ITEM_STATUS_DELIVERED)
                        || ($target_doc == 'invoice' && $row.steelitem.parent_id == 0)
                        || ($target_doc == 'supinvoice' && $row.steelitem.parent_id == 0)
                        || ($target_doc == 'oc' && $row.steelitem.parent_id == 0)}
                    <input type="checkbox" class="cb-row-item" value="{$row.steelitem.id}" onchange="calc_selected();" style="margin-right: 5px;">
                    {/if}
                </td>
                {/if}
				-->
                <td onclick="show_item_context(event, {$row.steelitem_id});">{$row.id|undef}</td>
                <td onclick="show_item_context(event, {$row.steelitem_id});">{$row.plate_id|escape:'html'|undef}</td>
                <td onclick="show_item_context(event, {$row.steelitem_id});">{if isset($row.steelgrade)}{$row.steelgrade|escape:'html'}{else}{''|undef}{/if}</td>
                <td onclick="show_item_context(event, {$row.steelitem_id});" class="text-center">{$row.thick}</td>
                <td onclick="show_item_context(event, {$row.steelitem_id});" class="text-center">{$row.width}</td>
                <td onclick="show_item_context(event, {$row.steelitem_id});" class="text-center">{$row.length}</td>
                <td onclick="show_item_context(event, {$row.steelitem_id});" class="text-center" id="item-weight-{$row.steelitem_id}">{$row.weight}</td>
                <td onclick="show_item_context(event, {$row.steelitem_id});" class="text-center">{$row.supplier|undef}</td>       
                <td onclick="show_item_context(event, {$row.steelitem_id});" class="text-center">{$row.supplier_invoice|undef}</td>       
                <td onclick="show_item_context(event, {$row.steelitem_id});" class="text-center">{$row.supplier_date|undef}</td>
				<td onclick="show_item_context(event, {$row.steelitem_id});" class="text-center">
                    {$row.last_year}
                </td>
                <td onclick="show_item_context(event, {$row.steelitem_id});" class="text-center">
                    {$row.status_now}
                </td>				
                
                <td class="text-center">
                {$row.ddt_nr|undef}
                </td>                
				<td class="text-center">
                {$row.ddt_date|undef}
                </td>
{*
                <td onclick="show_item_context(event, {$row.steelitem_id});">{$row.steelitem.ddt_number|escape:'html'}</td>
                <td onclick="show_item_context(event, {$row.steelitem_id});">{if !empty($row.steelitem.ddt_date)}{$row.steelitem.ddt_date|escape:'html'|date_format:'d/m/Y'}{/if}</td>
*}  
{*                <td onclick="show_item_context(event, {$row.steelitem_id});" class="text-center">{$row.steelitem.days_on_stock}</td>  *}
                {*
				<td onclick="show_item_context(event, {$row.steelitem_id});">
                    {$row.notes|escape:'html'|undef}
                </td>
				*}
                <td onclick="show_item_context(event, {$row.steelitem_id});">{if isset($row.location)}{$row.location|escape:'html'}{else}{''|undef}{/if}</td>
                <td onclick="show_item_context(event, {$row.steelitem_id});" class="text-center">{$row.purchase_price|undef}</td>
				<td onclick="show_item_context(event, {$row.steelitem_id});">{round($row.stock_price)}</td>
				<td onclick="show_item_context(event, {$row.steelitem_id});">{round($row.order_price)}</td>
                {*
				<td onclick="show_item_context(event, {$row.steelitem_id});">{round($row.valuetion_price)}</td>
                *}
				<td onclick="show_item_context(event, {$row.steelitem_id});">{round($row.valuetion_price)}</td>
<!--  
 <td onclick="show_item_context(event, {$row.steelitem_id});">{if isset($row.steelitem.owner)}{$row.steelitem.owner.title_trade|escape:'html'}{else}{''|undef}{/if}</td>
                {if $row.steelitem.order_id > 0}
                <td>
                    {$row.steelitem.status_title}<dr>
                    <a href="/order/{$row.steelitem.order_id}">{$row.steelitem.order_id|order_doc_no}</a>
                </td>
                {else}
                <td onclick="show_item_context(event, {$row.steelitem_id});">{$row.steelitem.status_title|undef}</td>
                {/if}
                <td onclick="show_item_context(event, {$row.steelitem_id});">{if isset($row.steelitem.is_ce_mark) && !empty($row.steelitem.is_ce_mark)}<img src="/img/cemark16.png" alt="CE Mark" title="CE Mark">{else}<i style="color: #999;">no</i>{/if}</td>
{*                
                <td onclick="show_item_context(event, {$row.steelitem_id});">
                    {if !empty($row.steelitem.properties.condition)}
                        {if $row.steelitem.properties.condition == 'ar'}As Rolled
                        {elseif $row.steelitem.properties.condition == 'n'}Normalized
                        {elseif $row.steelitem.properties.condition == 'nr'}Normalizing Rolling
                        {/if}
                    {/if}
                </td>
                <td nowrap="nowrap">
                {if $row.steelitem.order_id > 0}
                    <a href="/order/view/{$row.steelitem.order_id}">{$row.steelitem.order_id|order_doc_no}</a>
                {/if}
                </td>
*}                -->
            </tr>
			{/if}
            {/foreach}
        </tbody>
    </table>
	<h3>Total quantity: {$list.total_number} pcs</h3>
	<h3>Total weight: {$list.total_weight} {$list[1].weight_unit}</h3>
	<h3>Total value: {$list[1].price_unit|upper} {$list.total_valuation_price}</h3>
{/if}

<div id="docselcontainer" style="display: none;">
    <div id="overlay"></div>
    <div id="docselform">
        <h3>Add selected position to : </h3>
        <div class="pad-10"></div>
        <div id="docselform-container"></div>
    </div>
</div>