<b>Warning ! After item is cut it is not longer available !</b>
<div class="pad"></div>

<table class="list" width="100%">
    <tbody>
        <tr class="top-table">
            <th width="5%">Id</th>
            <th width="12%">Plate Id</th>
            <th width="6%">Steel Grade</th>
            <th width="6%">Thickness<br>{$list.0.steelitem.dimension_unit|escape:'html'}</th>
            <th width="6%">Width<br>{$list.0.steelitem.dimension_unit|escape:'html'}</th>
            <th width="6%">Length<br>{$list.0.steelitem.dimension_unit|escape:'html'}</th>
            <th width="6%">Weight<br>{$list.0.steelitem.weight_unit|escape:'html'|wunit}</th>
            <th width="12%">Notes</th>
            <th width="12%">Location</th>
            <th>Owner</th>
            <th>DDT</th>
            <th width="5%">Days On Stock</th>
            <th width="5%">Status</th>            
        </tr>
        <tr id="position-{$row.steelposition_id}-item-{$item.id}"{if $item.status_id >= $smarty.const.ITEM_STATUS_ORDERED} class="item-status-{$item.status_id}"{/if}>
            <td>{$item.id}</td>
            <td>{$item.guid|escape:'html'}</td>
            <td>{if isset($item.steelgrade)}{$item.steelgrade.title|escape:'html'}{else}{''|undef}{/if}<input type="hidden" id="steelgrade_id" value="{$item.steelgrade_id}"></td>
            <td>{$item.thickness|escape:'html'}</td>
            <td>{$item.width|escape:'html'}</td>
            <td>{$item.length|escape:'html'}</td>
            <td>{$item.unitweight|escape:'html'|string_format:'%.2f'}</td>
            <td>
                {* if $item.parent_id > 0}
                    <div style="margin-bottom: 5px;"><a href="/item/edit/{$item.parent_id}">Twin of : {if !empty($item.parent.guid)}{$item.parent.guid|escape:'html'}{else}#{$item.parent_id}{/if}</a></div>
                {/if *}
                {$item.internal_notes|escape:'html'}
            </td>
            <td>{if isset($item.stockholder)}{$item.stockholder.doc_no|escape:'html'}{else}{''|undef}{/if}</td>
            <td>{if isset($item.owner)}{$item.owner.title_trade|escape:'html'}{else}{''|undef}{/if}</td>
            <td>{if !empty($item.ddt_number)}{$item.ddt_number|escape:'html'}{if !empty($item.ddt_date)}dd {$item.ddt_date|escape:'html'|date_format:'d/m/Y'}{/if}{else}{''|undef}{/if}</td>
            <td>{$item.days_on_stock}</td>
            <td>
                {if isset($item.status_title)}{$item.status_title|undef}{else}{''|undef}{/if}
                {if isset($item.order_id) && $item.order_id > 0}<br><a href="/order/{$item.order_id}">{$item.order_id|order_doc_no}</a>{/if}
            </td>
        </tr>
    </tbody>
</table>
<div class="pad"></div>

<h2 style="margin-bottom: 10px;">Into Pieces : </h2>
<table id="cut-pieces" class="list" width="100%">
    <tbody>
        <tr class="top-table alt1" style="height: 25px;">
            <th rowspan="2" width="5%">No</th>
            <th rowspan="2" width="12%">Plate Id</th>
            <th rowspan="2" width="6%">Steel Grade</th>
            <th rowspan="2" width="6%">Thickness</th>
            <th rowspan="2" width="6%">Width</th>
            <th rowspan="2" width="6%">Length</th>
            <th rowspan="2" width="6%">Weight</th>
            <th rowspan="2" width="12%">Notes</th>
            <th colspan="2" style="border-bottom : 1px solid #B9B9B9;">Location & Position for Piece</th>
            <th rowspan="2" width="5%">Status</th>
        </tr>
        <tr class="top-table alt1" style="height: 25px;">
            <th width="12%">Location</th>
            <th>Position</th>
        </tr>
        {foreach from=$pieces item=row name="pieces"}
        <tr id="piece-{$smarty.foreach.pieces.index}">
            <td>
                {$smarty.foreach.pieces.index + 1}
                <input type="hidden" name="pieces[{$smarty.foreach.pieces.index}][id]" value="{$row.id}">
            </td>
            <td><input type="text" name="pieces[{$smarty.foreach.pieces.index}][guid]" value="{if !empty($item.guid)}{$item.guid|escape:'html'}-{$smarty.foreach.pieces.index + 1}{/if}" class="max"></td>
            <td>{if isset($item.steelgrade)}{$item.steelgrade.title|escape:'html'}{else}{''|undef}{/if}</td>
            <td><input type="hidden" id="thickness-{$smarty.foreach.pieces.index}" value="{$item.thickness}">{$item.thickness|escape:'html'}</td>
            <td><input type="text" id="width-{$smarty.foreach.pieces.index}" name="pieces[{$smarty.foreach.pieces.index}][width]" value="{$row.width|escape:'html'}" class="max width-input" style="text-align: center;" onkeyup="calc_unitweight({$smarty.foreach.pieces.index});"></td>
            <td><input type="text" id="length-{$smarty.foreach.pieces.index}" name="pieces[{$smarty.foreach.pieces.index}][length]" value="{$row.length|escape:'html'}" class="max length-input" style="text-align: center;" onkeyup="calc_unitweight({$smarty.foreach.pieces.index});"></td>
            <td><input type="text" id="unitweight-{$smarty.foreach.pieces.index}" name="pieces[{$smarty.foreach.pieces.index}][unitweight]" value="{$row.unitweight|escape:'html'}" class="max unitweight-input" style="text-align: center;"></td>
            <td><input type="text" name="pieces[{$smarty.foreach.pieces.index}][notes]" value="{$row.notes|escape:'html'}" class="max"></td>
            <td>
                <select id="location-{$smarty.foreach.pieces.index}" name="pieces[{$smarty.foreach.pieces.index}][location_id]" style="width: 99%;" onchange="cut_get_positions({$smarty.foreach.pieces.index}, this.value, {$item.id});">
                    <option value="0">--</option>
                    {foreach from=$row.locations item=row_location}
                    <option value="{$row_location.company.id}"{if $row_location.company.id == $row.location_id} selected="selected"{/if}>{$row_location.company.doc_no} ({if $row_location.company.stocklocation.title != $row_location.company.city.title}{$row_location.company.stocklocation.title}, {$row_location.company.city.title}{else}{$row_location.company.city.title}{/if})</option>
                    {/foreach}
                </select>
            </td>
            <td>
                <select id="position-{$smarty.foreach.pieces.index}" name="pieces[{$smarty.foreach.pieces.index}][position_id]" style="width: 99%;">
                    <option value="0">--</option>
                    {foreach from=$row.positions item=row_position}
                    <option value="{$row_position.steelposition.id}"{if $row_position.steelposition.id == $row.position_id} selected="selected"{/if}>
                        {$row_position.steelposition.steelgrade.title} {$row_position.steelposition.thickness} x {$row_position.steelposition.width} x {$row_position.steelposition.length} - {$row_position.steelposition.qtty} pcs
                    </option>
                    {/foreach}                    
                </select>
            </td>
            <td>
                {$row.status_title|undef}
                {if $row.order_id > 0}<br><a href="/order/{$row.order_id}">{$row.order_id|order_doc_no}</a>{/if}
            </td>            
        </tr>        
        {/foreach}
    </tbody>
</table>
<input type="hidden" id="pieces_count" value="{count($pieces)}">
<input type="hidden" id="dimension_unit" value="{$item.dimension_unit}">
<input type="hidden" id="weight_unit" value="{$item.weight_unit}">
