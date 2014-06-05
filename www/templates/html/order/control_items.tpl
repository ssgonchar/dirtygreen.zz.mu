<table class="list" width="100%">
    <tbody>
        <tr class="top-table alt1" style="height: 25px;">
           {* <th width="3%"><input type="checkbox" onchange="check_all(this, 'item-position-{$position.id}'); calc_selected(); show_group_actions(); show_selected_items_actions({$position.id});"></th>*}
            <th width="10%">Plate Id</th>
            {*<th width="8%">Steel Grade</th>*}
            <th width="5%">Thickness<br>{$position.dimension_unit}</th>
            <th width="5%">Width<br>{$position.dimension_unit}</th>
            <th width="5%">Length<br>{$position.dimension_unit}</th>
            <th width="7%">Weight<br>{$position.weight_unit|wunit}</th>
            <th width="7%">Purchase Price<br>per Ton{*$position.weight_unit|wunit*}</th>
           <!-- <th>Incoming DDT</th>-->
            
            <th width="5%">Days On Stock</th>
            <th>Internal Notes</th>
            <th>Location</th>
            <th>Owner</th>
            <th>Condition</th>
            <th width="3%">CE Mark</th>
			<th width="15%">Related documents</th>
            <th width="20px"><img src="/img/icons/picture-bw.png" title="Position Pictures" alt="Position Pictures"></th>
        </tr>
        {foreach from=$items item=item}
        <tr id="position-{$position.id}-item-{$item.steelitem.id}">
            {*<td>
                <input type="checkbox" class="cb-row-item-position-{$position.id} cb-row-item{if !empty($item.steelitem.is_eternal)} position-{$position.id}-item-eternal{/if}" value="{$item.steelitem.id}" onchange="show_selected_items_actions({$position.id}); calc_selected(); show_group_actions();">
                <input type="hidden" value="{$position.id}" id="item-{$item.steelitem.id}-position">
            </td>  *}          
            {if $item.steelitem.parent_id > 0}
            <td>
                <a href="/item/edit/{$item.steelitem.parent_id}">{$item.steelitem.doc_no}</a>
            {else}
            <td onclick="show_position_item_context(event, {$item.steelitem.id}, {$is_revision});">
                {$item.steelitem.guid|escape:'html'|undef}</td>
            {/if}
            {*<td onclick="show_position_item_context(event, {$item.steelitem.id}, {$is_revision});" class="pos{$position.id}-steelgrade">{$item.steelitem.steelgrade.title|escape:'html'}</td>*}
            <td onclick="show_position_item_context(event, {$item.steelitem.id}, {$is_revision});" class="text-center pos{$position.id}-thickness">{$item.steelitem.thickness|escape:'html'}</td>
            <td onclick="show_position_item_context(event, {$item.steelitem.id}, {$is_revision});" class="text-center pos{$position.id}-width">{$item.steelitem.width|escape:'html'}</td>
            <td onclick="show_position_item_context(event, {$item.steelitem.id}, {$is_revision});" class="text-center pos{$position.id}-length">{$item.steelitem.length|escape:'html'}</td>
            <td onclick="show_position_item_context(event, {$item.steelitem.id}, {$is_revision});" class="text-center pos{$position.id}-unitweight">{$item.steelitem.unitweight|escape:'html'|string_format:'%.2f'}</td>
            <td onclick="show_position_item_context(event, {$item.steelitem.id}, {$is_revision});">
                {if $item.steelitem.purchase_price > 0}{$item.steelitem.purchase_price|string_format:'%.2f'}{if !empty($item.steelitem.purchase_currency)} {$item.steelitem.purchase_currency|cursign}{/if}{else}{''|undef}{/if}
            </td> 			
            {*
			{if $item.steelitem.in_ddt_id > 0}
            <td>
                <a href="/inddt/{$item.steelitem.in_ddt_id}">{$item.steelitem.in_ddt.number} dd {$item.steelitem.in_ddt.date|date_format:'d/m/Y'}</a>
            {else}
            <td onclick="show_position_item_context(event, {$item.steelitem.id}, {$is_revision});">
                {''|undef}
            {/if}
            </td>
			*}
            <td onclick="show_position_item_context(event, {$item.steelitem.id}, {$is_revision});">{$item.steelitem.days_on_stock}</td>
            <td onclick="show_position_item_context(event, {$item.steelitem.id}, {$is_revision});">
                {$item.steelitem.internal_notes|escape:'html'|undef}
            </td>
            <td onclick="show_position_item_context(event, {$item.steelitem.id}, {$is_revision});">{if isset($item.steelitem.stockholder)}{$item.steelitem.stockholder.title|escape:'html'}{/if}</td>
            <td onclick="show_position_item_context(event, {$item.steelitem.id}, {$is_revision});">{if isset($item.steelitem.owner)}{$item.steelitem.owner.title_trade|escape:'html'}{else}{''|undef}{/if}</td>
            <td onclick="show_position_item_context(event, {$item.steelitem.id}, {$is_revision});">
                {if !empty($item.steelitem.properties.condition)}
                    {if $item.steelitem.properties.condition == 'ar'}As Rolled
                    {elseif $item.steelitem.properties.condition == 'n'}Normalized
                    {elseif $item.steelitem.properties.condition == 'nr'}Normalizing Rolling
                    {else}{''|undef}
                    {/if}
                {else}{''|undef}
                {/if}
            </td>
            <td onclick="show_item_context(event, {$item.steelitem_id});">{if isset($item.steelitem.is_ce_mark) && !empty($item.steelitem.is_ce_mark)}<img src="/img/cemark16.png" alt="CE Mark" title="CE Mark">{else}<i style="color: #999;">no</i>{/if}</td>
            
			<td style="text-align:left;">
				{if $item.doc|@count>0}
				<ul>
				{foreach from=$item.doc item=row}
					{if $row.object_id>0 && $row.object_alias!=='order'}
					<li><a href="/{$row.object_alias}/{$row.object_id}" target="_blank">{$row.object_alias}&nbsp;(#&nbsp;{$row.object.number})</a></li>
					{/if}
					{*<a href="/{$row.object_alias}/{$row.object_alias}">{$row.object.number}</a>*}
					
				{/foreach}
				</ul>
				{else}
				documents not found
				{/if}
            </td>
			
			<td>
            {if isset($item.steelitem.attachments)}
                {foreach from=$item.steelitem.attachments item=attachment name="ia"}
                    {if $smarty.foreach.ia.first}
                        <a href="/picture/default/{$attachment.attachment.secret_name}/l/{$attachment.attachment.original_name|lower}" rel="prettyPhoto[steelitem{$item.steelitem.id}]"><img src="/img/icons/picture.png" title="Item Pictures" alt="Item Pictures"></a>
                    {else}
                        <a href="/picture/default/{$attachment.attachment.secret_name}/l/{$attachment.attachment.original_name|lower}" rel="prettyPhoto[steelitem{$item.steelitem.id}]"></a>
                    {/if}
                {/foreach}
            {else}
            <span style="color: #999999;">no</span>
            {/if}
            </td>
        </tr>
        {/foreach}
        <!--
        <tr>
            <td colspan="16" class="text-left">
                <a href="javascript:void(0);" class="closeup" onclick="hide_items({$position.id});">hide items</a>
                <span id="selected-actions-item-position-{$position.id}" style="display: none; text-align: left;">
                {if empty($is_revision)}                        
                    <a href="javascript: void(0);" class="move" onclick="redirect_selected('item-position-{$position.id}', '/position/{$position.id}/item/move/');">move selected items</a>
                    <a href="javascript: void(0);" class="edit" onclick="redirect_selected('item-position-{$position.id}', '/position/{$position.id}/item/edit/');">edit selected items</a>
                    <a href="javascript:void(0);" onclick="items_remove({$position.id});" class="delete position-{$position.id}-removeitems">delete selected items</a>
                {/if}
                </span>            
                <div class="text-right" style="float: right;">
                    <a href="/order/{$position.id}/edit" class="edit" style="color: #333; margin-right: 10px;">edit order</a>
                    {*<a href="/order/{$position.id}/history" class="history" style="color: #333;">position history</a>*}
                </div>                
            </td>
        </tr>
        -->
    </tbody>
</table>
<div class="pad1"></div>
