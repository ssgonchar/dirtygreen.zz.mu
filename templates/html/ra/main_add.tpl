<div style="position: absolute;">
    <img src="/img/layout/gnome.jpg" style="position: absolute; top: 0; left: 0;">
</div>
<div class="bubble" style="margin-left: 55px; width: 650px;" id="gnome_text">
    Items with missing OWNER & STOCKHOLDER info and already released items are not available for release .  Please specify missing data first .
</div>
<div class="separator pad"></div>

{foreach $stockholders as $row}
    <h3 style="margin-bottom: 10px;">{if empty($row.stockholder_id)}Unknown Stockholder{else}{$row.title}{/if}</h3>
    <table class="list" style="width: 100%">
        <tr class="top-table">
            <th width="3%" class="ra-td-cb">
                <input type="checkbox" class="ra-check-all" rel="shid_{$row.stockholder_id}" onclick="ra_check_all_items({$row.stockholder_id});" checked="checked">
            </th>
            <th>Plate id</th>
            <th width="8%">Thickness,<br />{$row.dimension_unit|escape:'html'}</th>
            <th width="8%">Width,<br />{$row.dimension_unit|escape:'html'}</th>
            <th width="8%">Length,<br />{$row.dimension_unit|escape:'html'}</th>
            <th width="8%">Weight,<br />{$row.weight_unit|escape:'html'}</th>
            <th width="19%">Incoming DDT</th>
            <th width="19%">Outgoing DDT</th>
            <th width="6%">Owner</th>
            <th width="10%">Status</th>
        </tr>
        {foreach $row.items as $item}
            <tr>
                <td class="ra-td-cb">
                    {*if !empty($row.stockholder_id) && !empty($item.owner_id) && $item.status_id != $smarty.const.ITEM_STATUS_RELEASED && $item.status_id != $smarty.const.ITEM_STATUS_DELIVERED*}
                    {if !empty($row.stockholder_id) && !empty($item.owner_id)}
                    <input type="checkbox" name="steelitem_ids[]" class="ra-steelitem" rel="shid_{$row.stockholder_id}" value="{$item.id}" checked="checked">
                    {/if}
                </td>
                <td>{$item.guid|escape:'html'|undef}</td>
                <td>{if !empty($item.thickness)}{$item.thickness|escape:'html'}{/if}</td>
                <td>{if !empty($item.width)}{$item.width|escape:'html'}{/if}</td>
                <td>{if !empty($item.length)}{$item.length|escape:'html'}{/if}</td>
                <td>{if !empty($item.unitweight)}{$item.unitweight|escape:'html'|string_format:'%.2f'}{/if}</td>
                <td>
                {if !empty($item.in_ddt_id)}<a href="/inddt/{$item.in_ddt_id}">{/if}
                    {if !empty($item.in_ddt_number)}{$item.in_ddt_number|escape:'html'} dd {if !empty($item.in_ddt_date)}{$item.in_ddt_date|escape:'html'|date_format:'d/m/Y'}{/if}{else}{''|undef}{/if}
                {if !empty($item.in_ddt_id)}</a>{/if}
                </td>
                <td>
                    {if !empty($item.ddt_number)}{$item.ddt_number|escape:'html'} dd {if !empty($item.ddt_date)}{$item.ddt_date|escape:'html'|date_format:'d/m/Y'}{/if}{else}{''|undef}{/if}
                </td>
                <td>{if isset($item.owner)}{$item.owner.title_trade|escape:'html'}{else}{''|undef}{/if}</td>
                <td>
                    {if empty($item.status_id)}{''|undef}
                    {else}
                        {$item.status_title|escape:'html'}
                        {if $item.order_id > 0}<br><a href="/order/{$item.order_id}">{$item.order_id|order_doc_no}</a>{/if}
                    {/if}
                </td>
            </tr>
        {/foreach}
    </table>
    <div class="pad"></div>
{/foreach}

{*
{foreach $stockholders_list as $s_holder_id => $s_holder}
<h3>{if !empty($s_holder.doc_no)}{$s_holder.doc_no|escape:'html'}{else}Stockholder not defined{/if}</h3>
<table class="list" style="width: 100%">
    <tr class="top-table" style="height: 25px;">
        <th rowspan="2" style="width: 30px;">{if $s_holder_id > 0}<input type="checkbox" class="check-all-items" rel="shid_{$s_holder_id}" style="margin: 5px;">{/if}</th>
        <th rowspan="2" style="width: 200px;">Plate id</th>
        <th rowspan="2">Thickness,<br />{$s_holder.dimension_unit|escape:'html'}</th>
        <th rowspan="2">Width,<br />{$s_holder.dimension_unit|escape:'html'}</th>
        <th rowspan="2">Length,<br />{$s_holder.dimension_unit|escape:'html'}</th>
        <th rowspan="2">Weight,<br />{$s_holder.weight_unit|escape:'html'}</th>
        <th colspan="2" style="border-bottom : 1px solid #B9B9B9;">Incoming DDT</th>
        <th colspan="2" style="border-bottom : 1px solid #B9B9B9;">Outgoing DDT</th>
    </tr>
    <tr class="top-table" style="height: 25px;">
        <th>Number</th>
        <th>Date</th>
        <th>Number</th>
        <th>Date</th>
    </tr>
    {foreach $steelitems_list as $item}
        {if $item.steelitem.stockholder_id == $s_holder_id}
        <tr>
            <td>{if $s_holder_id > 0}<input type="checkbox" name="form[steelitem_ids][]" class="steelitem-id" rel="shid_{$s_holder_id}" value="{$item.steelitem.id}" style="margin: 5px;">{/if}</td>
            <td>{$item.steelitem.guid|escape:'html'|undef}</td>
            <td>{if !empty($item.steelitem.thickness)}{$item.steelitem.thickness|escape:'html'}{/if}</td>
            <td>{if !empty($item.steelitem.width)}{$item.steelitem.width|escape:'html'}{/if}</td>
            <td>{if !empty($item.steelitem.length)}{$item.steelitem.length|escape:'html'}{/if}</td>
            <td>{if !empty($item.steelitem.unitweight)}{$item.steelitem.unitweight|escape:'html'|string_format:'%.2f'}{/if}</td>
            <td>{if !empty($item.steelitem.in_ddt_number)}{$item.steelitem.in_ddt_number|escape:'html'}{/if}</td>
            <td>{if !empty($item.steelitem.in_ddt_date)}{$item.steelitem.in_ddt_date|escape:'html'|date_format:'d/m/Y'}{/if}</td>
            <td>{if !empty($item.steelitem.ddt_number)}{$item.steelitem.ddt_number|escape:'html'}{/if}</td>
            <td>{if !empty($item.steelitem.ddt_date)}{$item.steelitem.ddt_date|escape:'html'|date_format:'d/m/Y'}{/if}</td>
        </tr>
        {/if}
    {/foreach}
</table>
<div class="pad"></div>
{/foreach}
*}