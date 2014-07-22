<table>
    <tr>
        <td colspan="3">
            <table class="form">
                <tr{if isset($rev_date)} id="tr-revision" class="revision rev1"{/if}>
                    <td width="85px" class="text-right">Revision :</td>
                    <td>
                        <input class="datepicker" type="text" id="rev_date" name="form[rev_date]" value="{if !empty($rev_date)}{$rev_date|escape:'html'}{/if}" style="width: 100px;" onchange="$('#tr-revision').addClass('revision');">
                        <select name="form[rev_time]" id="rev_time">
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
                    {if isset($rev_date)}
                    <td><a href="javascript: void(0);" onclick="clear_revision();">Clear</a></td>
                    {/if}
                </tr>
            </table>             
        </td>
    </tr>
    <tr>
        <td width="580px" class="text-top">
            <table class="form">
                <tr>
                    <td width="85px" class="text-right">Stock :</td>
                    <td>
                        <select id="stock" name="form[stock_id]" class="normal" onchange="bind_positions_filter();">
                            <option value="0"{if empty($stock_id)} selected="selected"{/if}>--</option>
                            {foreach from=$stocks item=row}
                            <option value="{$row.stock.id}"{if !empty($stock_id) && $stock_id == $row.stock.id} selected="selected"{/if}>{$row.stock.title|escape:'html'}</option>
                            {/foreach}
                        </select>            
                    </td>
                </tr>
                <tr>
                    <td width="85px" class="text-right">Locations :</td>
                    <td id="locations">
                        {if !empty($locations)}
                        {foreach from=$locations item=row}
                        <label for="cb-location-{$row.location_id}"><input type="checkbox" id="cb-location-{$row.location_id}" name="form[location][{$row.location_id}]" value="{$row.location_id}"{if isset($row.selected)} checked="checked"{/if}>&nbsp;{$row.location.title|escape:'html'}&nbsp;&nbsp;&nbsp;</label>
                        {/foreach}
                        {else}
                        <span style="color: #aaa;">{if empty($stock_id)}Please select stock first{else}none{/if}</span>
                        {/if}                            
                    </td>
                </tr>
                <tr>
                    <td width="85px" class="text-right">Delivery times :</td>
                    <td id="deliverytimes">
                        {if !empty($deliverytimes)}
                        {foreach from=$deliverytimes item=row}
                        <label for="cb-deliverytime-{$row.deliverytime_id}"><input type="checkbox" id="cb-deliverytime-{$row.deliverytime_id}" name="form[deliverytime][{$row.deliverytime_id}]" value="{$row.deliverytime_id}"{if isset($row.selected)} checked="checked"{/if}>&nbsp;{$row.deliverytime.title|escape:'html'}&nbsp;&nbsp;&nbsp;</label>
                        {/foreach}
                        {else}
                        <span style="color: #aaa;">{if empty($stock_id)}Please select stock first{else}none{/if}</span>
                        {/if}                            
                    </td>
                </tr>            
            </table>            
        </td>
        <td class="text-top">
            <table class="form">
                <tr>
                    <td width="70px" class="text-right">Thickness :</td>
                    <td width="85px"><input type="text" name="form[thickness]" class="max"{if isset($thickness)} value="{$thickness}"{/if}></td>
                    <td width="60px" class="text-right">Width :</td>
                    <td width="85px"><input type="text" name="form[width]" class="max"{if isset($width)} value="{$width}"{/if}></td>
                    <td width="60px" class="text-right">Length :</td>
                    <td width="85px"><input type="text" name="form[length]" class="max"{if isset($length)} value="{$length}"{/if}></td>
                <tr>
                    <td width="60px" class="text-right">Weight :</td>
                    <td width="85px"><input type="text" name="form[weight]" class="max"{if isset($weight)} value="{$weight}"{/if}></td>
                    <td width="60px" class="text-right">Notes :</td>
                    <td colspan="3"><input type="text" name="form[notes]" class="max"{if isset($notes)} value="{$notes}"{/if}></td>
                </tr>
                {*
                <tr>
                    <td colspan="6" style="padding-top: 20px;">
                        
                    </td>
                </tr>
                *}
            </table>            
        </td>
        <td class="text-middle">
            <input type="submit" name="btn_setfilter" value="Select" class="btn100o" style="margin-left: 20px;">
        </td>
    </tr>
</table>



<div class="pad"></div>

{if empty($list)}
    {if isset($filter)}Nothing was found on my request{/if}
{else}
<table class="list" width="100%">
    <tbody>
        <tr class="top-table">
            <th width="1%"></th>
            <th width="5%" class="text-left"><input type="checkbox" onchange="check_all(this, 'position'); calc_total('position'); show_group_actions();" style="margin-left: 2px;">&nbsp;Pos Id</th>
            <th width="8%">Steel Grade</th>
            <th width="5%">Thickness<br>{if isset($stock)}{$stock.dimension_unit}{/if}</th>
            <th width="5%">Width<br>{if isset($stock)}{$stock.dimension_unit}{/if}</th>
            <th width="5%">Length<br>{if isset($stock)}{$stock.dimension_unit}{/if}</th>
            <th width="7%">Unit Weight<br>{if isset($stock)}{$stock.weight_unit|wunit}{/if}</th>
            <th width="5%">Qtty<br>pcs</th>
            <th width="7%">Weight<br>{if isset($stock)}{$stock.weight_unit|wunit}{/if}</th>
            <th width="7%">Price<br>{if isset($stock)}{$stock.currency_sign}/{$stock.weight_unit|wunit}{/if}</th>
            <th width="7%">Value<br>{if isset($stock)}{$stock.currency_sign}{/if}</th>
            <th width="8%">Delivery Time</th>
            <th>Notes</th>
            <th>Internal Notes</th>
            <th>Plate Id</th>
            <th>Location</th>
            <th width="5%">Biz</th>
        </tr>    
        {foreach from=$list item=row}
        <tr id="position-{$row.steelposition_id}">
            <td{if !empty($row.steelposition.bgcolor)} style="background-color: {$row.steelposition.bgcolor};"{/if}><a href="javascript: void(0);" onclick="position_show_items({$row.steelposition_id});"><img id="img-{$row.steelposition_id}" src="/img/icons/plus.png" title="Show items" alt="Show items"></a></td>
            <td{if !empty($row.steelposition.bgcolor)} style="background-color: {$row.steelposition.bgcolor};"{/if} class="text-left">
                <input type="checkbox" value="{$row.steelposition_id}" class="cb-row-position" onchange="calc_total('position'); show_group_actions();">&nbsp;
                {if empty($is_revision)}
                <a href="javascript: void(0);" onclick="show_position_actions(this, {$row.steelposition_id});">{$row.steelposition_id|escape:'html'}</a>
                {else}
                <a href="/position/history/{$row.steelposition_id}">{$row.steelposition_id|escape:'html'}</a>
                {/if}
            </td>
            <td{if !empty($row.steelposition.bgcolor)} style="background-color: {$row.steelposition.bgcolor};"{/if} class="pos">{$row.steelposition.steelgrade.title|escape:'html'}</td>
            <td{if !empty($row.steelposition.bgcolor)} style="background-color: {$row.steelposition.bgcolor};"{/if}>{$row.steelposition.thickness|escape:'html'}</td>
            <td{if !empty($row.steelposition.bgcolor)} style="background-color: {$row.steelposition.bgcolor};"{/if}>{$row.steelposition.width|escape:'html'}</td>
            <td{if !empty($row.steelposition.bgcolor)} style="background-color: {$row.steelposition.bgcolor};"{/if}>{$row.steelposition.length|escape:'html'}</td>
            <td{if !empty($row.steelposition.bgcolor)} style="background-color: {$row.steelposition.bgcolor};"{/if}>{$row.steelposition.unitweight|escape:'html'|string_format:'%.2f'}</td>
            <td id="position-qtty-{$row.steelposition_id}"{if !empty($row.steelposition.bgcolor)} style="background-color: {$row.steelposition.bgcolor};"{/if}>{$row.steelposition.qtty|escape:'html'|string_format:'%d'}</td>
            <td id="position-weight-{$row.steelposition_id}"{if !empty($row.steelposition.bgcolor)} style="background-color: {$row.steelposition.bgcolor};"{/if}>{$row.steelposition.weight|escape:'html'|string_format:'%.2f'}</td>
            <td{if !empty($row.steelposition.bgcolor)} style="background-color: {$row.steelposition.bgcolor};"{/if}>{$row.steelposition.price|escape:'html'|string_format:'%.2f'}</td>
            <td id="position-value-{$row.steelposition_id}"{if !empty($row.steelposition.bgcolor)} style="background-color: {$row.steelposition.bgcolor};"{/if}>{$row.steelposition.value|escape:'html'|string_format:'%.2f'}</td>
            <td{if isset($row.steelposition.on_stock) && !empty($row.steelposition.on_stock)} style="background-color: #FEFEFE; font-weight: bold;"{elseif !empty($row.steelposition.bgcolor)} style="background-color: {$row.steelposition.bgcolor};"{/if}>{if isset($row.steelposition.deliverytime)}{$row.steelposition.deliverytime.title|escape:'html'}{/if}</td>
            <td{if !empty($row.steelposition.bgcolor)} style="background-color: {$row.steelposition.bgcolor};"{/if}>{$row.steelposition.notes|escape:'html'}</td>
            <td{if !empty($row.steelposition.bgcolor)} style="background-color: {$row.steelposition.bgcolor};"{/if}>{$row.steelposition.internal_notes|escape:'html'}</td>
            <td{if !empty($row.steelposition.bgcolor)} style="background-color: {$row.steelposition.bgcolor};"{/if}>
                {if isset($row.steelposition.plateid)}
                {foreach name='plateid' from=$row.steelposition.plateid item=plateid}
                {$plateid}{if !$smarty.foreach.plateid.last}, {/if}
                {/foreach}
                {/if}
            </td>
            <td{if !empty($row.steelposition.bgcolor)} style="background-color: {$row.steelposition.bgcolor};"{/if}>
                {if isset($row.steelposition.location)}
                {foreach name='location' from=$row.steelposition.location item=location}
                {$location}{if !$smarty.foreach.location.last}, {/if}
                {/foreach}
                {/if}
            </td>
            <td{if !empty($row.steelposition.bgcolor)} style="background-color: {$row.steelposition.bgcolor};"{/if}>{if isset($row.steelposition.biz)}<a href="#">{$row.steelposition.biz.number_output|escape:'html'}</a>{/if}</td>
        </tr>
        <tr id="position-items-{$row.steelposition_id}" style="display: none;">
            <td colspan="17" style="padding: 5px 5px 0 30px;" class="text-left">
                {if empty($is_revision)}
                    {if $row.steelposition.inuse}
                        <img src="/img/icons/lock.png" title="{$row.steelposition.inuse_by}" alt="{$row.steelposition.inuse_by}"> Position is currently being edited by {$row.steelposition.inuse_by}
                    {else}                    
                        <input type="button" id="a-position-quick-edit-{$row.steelposition_id}" class="btn100s" value="quick edit" onclick="position_quickedit({$row.steelposition_id});">
                        <input type="button" id="a-position-save-{$row.steelposition_id}" class="btn100s" value="save" onclick="position_save({$row.steelposition_id});" style="display: none;">
                        <input type="button" id="a-position-cancel-{$row.steelposition_id}" class="btn100s" value="cancel" onclick="position_canceledit({$row.steelposition_id});"  style="display: none;">
                    {/if}
                    <div class="pad1"></div>
                {/if}
                {if !empty($row.steelposition.items)}
                <table class="list" width="100%">
                    <tbody>
                        <tr class="top-table" style="height: 25px;">
                            <th rowspan="2" width="6%" class="text-left"><input type="checkbox" onchange="check_all(this, 'item-position-{$row.steelposition_id}'); calc_total('item-position-{$row.steelposition_id}'); show_group_actions();" style="margin-left: 2px;">&nbsp;Item Id</th>
                            <th rowspan="2" width="5%">Plate Id</th>
                            <th rowspan="2" width="8%">Steel Grade</th>
                            <th rowspan="2" width="5%">Thickness<br>{if isset($stock)}{$stock.dimension_unit}{/if}</th>
                            <th rowspan="2" width="5%">Width<br>{if isset($stock)}{$stock.dimension_unit}{/if}</th>
                            <th rowspan="2" width="5%">Length<br>{if isset($stock)}{$stock.dimension_unit}{/if}</th>
                            <th rowspan="2" width="7%">Weight<br>{if isset($stock)}{$stock.weight_unit|wunit}{/if}</th>
                            <th rowspan="2" width="7%">Purchase Price<br>{if isset($stock)}{$stock.currency_sign}/{$stock.weight_unit|wunit}{/if}</th>
                            <th colspan="2" style="border-bottom : 1px solid #B9B9B9;">Incoming DDT</th>
                            <th rowspan="2" width="5%">Days On Stock</th>
                            <th rowspan="2">Internal Notes</th>
                            <th rowspan="2">Location</th>
                            <th rowspan="2">Condition</th>
                        </tr>
                        <tr class="top-table" style="height: 25px;">
                            <th>Number</th>
                            <th>Date</th>
                        </tr>
                        {foreach from=$row.steelposition.items item=item}
                        <tr id="position-{$row.steelposition_id}-item-{$item.steelitem.id}">
                            <td class="text-left">
                                <input type="checkbox" class="cb-row-item-position-{$row.steelposition_id} cb-row-item" value="{$item.steelitem.id}" onchange="show_item_actions({$row.steelposition_id}); calc_total('item-position-{$row.steelposition_id}'); show_group_actions();">&nbsp;
                                {if empty($is_revision)}
                                <a href="javascript: void(0);" onclick="show_item_block(this, {$item.steelitem.id}, {$row.steelposition_id});">{$item.steelitem.id}</a>
                                {else}
                                <a href="/item/history/{$item.steelitem.id}">{$item.steelitem.id}</a>                                    
                                {/if}
                                <input type="hidden" value="{$row.steelposition_id}" id="item-{$item.steelitem.id}-position">
                            </td>
                            <td>{$item.steelitem.guid|escape:'html'}</td>
                            <td class="pos{$row.steelposition_id}-steelgrade">{$item.steelitem.steelgrade.title|escape:'html'}</td>
                            <td class="text-center pos{$row.steelposition_id}-thickness">{$item.steelitem.thickness|escape:'html'}</td>
                            <td class="text-center pos{$row.steelposition_id}-width">{$item.steelitem.width|escape:'html'}</td>
                            <td class="text-center pos{$row.steelposition_id}-length">{$item.steelitem.length|escape:'html'}</td>
                            <td class="text-center pos{$row.steelposition_id}-unitweight">{$item.steelitem.unitweight|escape:'html'|string_format:'%.2f'}</td>
                            <td>{$item.steelitem.purchase_price|escape:'html'|string_format:'%.2f'}</td>
{*                            <td>{if isset($item.steelitem.supplier)}{$item.steelitem.supplier.title}{/if}</td>    *}
                            <td>{$item.steelitem.in_ddt_number|escape:'html'}</td>
                            <td>{if !empty($item.steelitem.in_ddt_date)}{$item.steelitem.in_ddt_date|escape:'html'|date_format:'d/m/Y'}{/if}</td>
                            <td>{$item.steelitem.days_on_stock}</td>
                            <td>
                                {if $item.steelitem.parent_id > 0}
                                    <div style="margin-bottom: 5px;">
                                        <a href="/item/edit/{$item.steelitem.parent_id}">{if $item.steelitem.rel == 't'}Twin of{elseif $item.steelitem.rel == 'c'}Cut from{/if} : {if !empty($item.steelitem.parent.guid)}{$item.steelitem.parent.guid|escape:'html'}{else}#{$item.steelitem.parent_id}{/if}</a>
                                    </div>
                                {/if}
                                {$item.steelitem.internal_notes|escape:'html'}
                            </td>
                            <td>{if isset($item.steelitem.stockholder)}{$item.steelitem.stockholder.title|escape:'html'}{/if}</td>
                            <td>
                                {if !empty($item.steelitem.properties.condition)}
                                    {if $item.steelitem.properties.condition == 'ar'}As Rolled
                                    {elseif $item.steelitem.properties.condition == 'n'}Normalized
                                    {elseif $item.steelitem.properties.condition == 'nr'}Normalizing Rolling
                                    {/if}
                                {/if}
                            </td>
                        </tr>
                        {/foreach}
                    </tbody>
                </table>
                <div id="selected-actions-item-position-{$row.steelposition_id}" style="display: none;">
                {if empty($is_revision)}                        
                    <a href="javascript: void(0);" class="move" onclick="redirect_selected('item-position-{$row.steelposition_id}', '/position/{$row.steelposition_id}/item/move');">move selected items</a>
                    <a href="javascript: void(0);" class="twin" onclick="redirect_selected('item-position-{$row.steelposition_id}', '/position/{$row.steelposition_id}/item/twin');">twin selected items</a>
                    <a href="javascript:void(0);" onclick="items_remove({$row.steelposition_id});" class="delete">delete selected items</a>
                {/if}
                </div>
                <div class="pad1"></div>
                {/if}
            </td>
        </tr>
        {/foreach}
    </tbody>
</table>
{/if}

<div id="pos-orders-list" style="display: none;">
    <div id="pos-orders-list-overlay"></div>
    <div id="pos-orders-list-container">
        <div id="pos-orders-list-title">
            <h3>Put selected positions to order</h3>
            <table class="form" width="100%">
                <tr>
                    <td width="2%"><input type="radio" id="r-order-0" name="order_id" value="0" checked="checked"></td>
                    <td><label for="r-order-0"><b>New Order</b></label></td>
                </tr>
            </table>            
        </div>
        <div id="pos-orders-list-list"></div>
        <div id="pos-orders-list-actions">
            <input type="button" class="btn100" value="Cancel" style="margin-right: 20px;" onclick="cancel_order_select();">
            <input type="button" class="btn100o" value="Ok" onclick="put_positions_to_order();">
        </div>
    </div>
</div>