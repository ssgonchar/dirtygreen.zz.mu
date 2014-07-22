{if $released_count > 0}
    <div style="position: absolute;">
        <img src="/img/layout/gnome.jpg" style="position: absolute; top: 0; left: 0;">
    </div>
    <div class="bubble" style="margin-left: 55px; width: 350px;" id="gnome_text">
        Please note, I could not move RELEASED items.
    </div>
    <div class="separator pad"></div>
{/if}

<table class="list" width="100%">
    <tbody>
        <tr class="top-table">
            <th width="1%"><input type="checkbox" onchange="check_all(this);" checked="checked"></th>
            <th width="3%" class="text-center">Id</th>
            <th width="5%">Plate Id</th>
            <th width="8%">Steel Grade</th>
            <th width="5%" class="text-center">Thickness<br>{$list.0.steelitem.dimension_unit|escape:'html'}</th>
            <th width="5%" class="text-center">Width<br>{$list.0.steelitem.dimension_unit|escape:'html'}</th>
            <th width="5%" class="text-center">Length<br>{$list.0.steelitem.dimension_unit|escape:'html'}</th>
            <th width="7%" class="text-center">Weight<br>{$list.0.steelitem.weight_unit|escape:'html'|wunit}</th>
            <th>Notes</th>
            <th>Location</th>
            <th>Owner</th>
            <th width="5%">Days On Stock</th>
            <th width="5%">Status</th>            
        </tr>
        {foreach from=$list item=item}
        <tr id="position-{$row.steelposition_id}-item-{$item.steelitem.id}"{if $item.steelitem.status_id >= $smarty.const.ITEM_STATUS_ORDERED} class="item-status-{$item.steelitem.status_id}"{/if}>
            <td>
                <input type="checkbox" name="form[items][{$item.steelitem.id}]" {if $item.steelitem.status_id >= $smarty.const.ITEM_STATUS_RELEASED}class="cb-row-disabled" disabled="disabled"{else}class="cb-row" checked="checked"{/if} value="{$item.steelitem.id}" onchange="show_item_actions({$row.steelposition_id});">
            </td>
            <td class="text-center">{$item.steelitem.id}</td>
            <td>{$item.steelitem.guid|escape:'html'|undef}</td>
            <td>{if isset($item.steelitem.steelgrade)}{$item.steelitem.steelgrade.title|escape:'html'}{else}{''|undef}{/if}</td>
            <td class="text-center">{$item.steelitem.thickness|escape:'html'}</td>
            <td class="text-center">{$item.steelitem.width|escape:'html'}</td>
            <td class="text-center">{$item.steelitem.length|escape:'html'}</td>
            <td class="text-center">{$item.steelitem.unitweight|escape:'html'|string_format:'%.2f'}</td>
            <td>{$item.steelitem.internal_notes|escape:'html'|undef}</td>
            <td>
                {if isset($item.steelitem.stockholder)}
                    {$item.steelitem.stockholder.doc_no|escape:'html'}{if isset($item.steelitem.stockholder.city)}, {$item.steelitem.stockholder.city.title|escape:'html'} ({$item.steelitem.location.title}){/if}
                {else}{''|undef}{/if}</td>
            <td>{if isset($item.steelitem.owner)}{$item.steelitem.owner.title_trade|escape:'html'}{else}{''|undef}{/if}</td>
            <td>{$item.steelitem.days_on_stock}</td>
            <td>
                {$item.steelitem.status_title|undef}
                {if $item.steelitem.order_id > 0}<br><a href="/order/{$item.order_id}">{$item.steelitem.order_id|order_doc_no}</a>{/if}
            </td>
        </tr>
        {/foreach}
    </tbody>
</table>
<input type="hidden" id="items_thickness" value="{if isset($items_thickness)}{$items_thickness}{/if}">
<input type="hidden" id="items_width" value="{if isset($items_width)}{$items_width}{/if}">
<input type="hidden" id="items_length" value="{if isset($items_length)}{$items_length}{/if}">
<div class="pad"></div>

<h3>Suitable Position</h3> 
<table class="form">
    <tr>
        <td class="form-td-title-b" style="width: 150px;">Stock :</td>
        <td>
            <select id="stocks" name="form[stock_id]" class="normal" onchange="bind_stockparams(this.value); clear_stockpositions(); bind_biz_autocomplete();">
                <option value="0"{if !isset($stock)} selected="selected"{/if}>--</option>
                {foreach from=$stocks item=row}
                <option value="{$row.stock.id}"{if isset($stock) && $stock.id == $row.stock.id} selected="selected"{/if}>{$row.stock.title|escape:'html'}</option>
                {/foreach}
            </select>
            <input type="hidden" id="dimension_unit" value="{if isset($stock)}{$stock.dimension_unit}{/if}">
            <input type="hidden" id="weight_unit" value="{if isset($stock)}{$stock.weight_unit}{/if}">
        </td>
    </tr>
    <tr>
        <td class="form-td-title-b">Steel Grade :</td>
        <td>
            <select id="steelgrades" name="form[steelgrade_id]" class="normal" onchange="bind_stockpositions({$position_id}, {count($list)}); set_similarvalue(this, '#steelgrade-1'); bind_biz_autocomplete();">
                <option value="0">--</option>
                {foreach from=$steelgrades item=row}
                <option value="{$row.steelgrade.id}"{if $row.steelgrade.id == $steelgrade_id} selected="selected"{/if}>{$row.steelgrade.title|escape:'html'}</option>
                {/foreach}                
            </select>
        </td>
    </tr>
    <tr height="32px">
        <td class="form-td-title-b">Nominal Dimensions :</td>
        <td>thickness : {if isset($items_thickness)}{$items_thickness} mm{else}{''|undef}{/if}, width : {if isset($items_width)}{$items_width} mm{else}{''|undef}{/if}, length : {if isset($items_length)}{$items_length} mm{else}{''|undef}{/if}</td>
    </tr>    
</table>
<div class="pad1"></div>

<div id="positions">
    {include file="templates/html/item/control_positions.tpl"}
</div>
<div class="pad1"></div>

<table class="form">
    <tr>
        <td class="form-td-title" style="width: 150px;">Change Location To :</td>
        <td>
            <select id="locations" name="form[location_id]" class="wide">
                <option value="0">--</option>
                {foreach from=$locations item=row}
                <option value="{$row.company.id}"{if isset($location_id) && $location_id == $row.company.id} selected="selected"{/if}>{$row.company.doc_no|escape:'html'} ({$row.company.stocklocation.title|escape:'html'}{if isset($row.company.city)}, {$row.company.city.title}{/if})</option>
                {/foreach}
            </select>
        </td>
    </tr>
</table>
