<table class="form" width="100%">
    <tr>
        <td width="33%" class="text-top">
            <table class="form" style="width: 100%;">
                <tr style="height: 32px;">
                    <td class="form-td-title-b" style="width:170px;">Transport Company : </td>
                    <td>{if isset($ra.company)}{$ra.company.doc_no}{else}{''|undef}{/if}</td>
                </tr>                
                <tr style="height: 32px;">
                    <td class="form-td-title-b">Truck Number : </td>
                    <td>{$ra.truck_number|escape:'html'|undef}</td>
                </tr>                
                <tr style="height: 32px;">
                    <td class="form-td-title-b">Destination : </td>
                    <td>
                        {if $ra.dest_stockholder_id > 0 && !empty($ra.dest_stockholder)}
                            {$ra.dest_stockholder.title|escape:'html'}{if !empty($ra.dest_stockholder.city)}, {$ra.dest_stockholder.city.title|escape:'html'}{/if}
                        {else}{$ra.destination|escape:'html'|undef}
                        {/if}
                    </td>
                </tr>                
                <tr style="height: 32px;">
                    <td class="form-td-title-b">Loading Date : </td>
                    <td>{$ra.loading_date|escape:'html'|undef}</td>
                </tr>
                <tr style="height: 32px;">
                    <td class="form-td-title-b text-top">DDT Instructions : </td>
                    <td class="text-top">{$ra.ddt_instructions|escape:'html'|nl2br|undef}</td>
                </tr>
            </table>
        </td>
         <td width="33%" class="text-top">
            <table class="form" style="width: 100%;">
                <tr style="height: 32px;">
                    <td class="form-td-title-b text-top" style="width:170px;">Marking Requirements : </td>
                    <td class="text-top">{$ra.marking|escape:'html'|nl2br|undef}</td>
                </tr>
                <tr style="height: 32px;">
                    <td class="form-td-title-b text-top">Dunnaging Requirements : </td>
                    <td class="text-top">{$ra.dunnaging|escape:'html'|nl2br|undef}</td>
                </tr>
                <tr style="height: 32px;">
                    <td class="form-td-title-b">Notes : </td>
                    <td>{$ra.notes|escape:'html'|nl2br|undef}</td>
                </tr>
                <tr style="height: 32px;">
                    <td class="form-td-title-b">Coupon : </td>
                    <td>{if !empty($ra.coupon)}{$ra.coupon|escape:'html'|nl2br}{else}{''|undef}{/if}</td>
                </tr>
                <tr style="height: 32px;">
                    <td class="form-td-title-b text-top">Consignee : </td>
                    <td class="text-top">{if !empty($ra.consignee)}{$ra.consignee|escape:'html'|nl2br}{else}{''|undef}{/if}</td>
                </tr>
                <tr style="height: 32px;">
                    <td class="form-td-title-b">Consignee Ref. : </td>
                    <td>{if !empty($ra.consignee_ref)}{$ra.consignee_ref|escape:'html'|nl2br}{else}{''|undef}{/if}</td>
                </tr>
            </table>
        </td>
        <td class="text-top">
            <table class="form" style="width: 100%;">
                <tr style="height: 32px;">
                    <td class="form-td-title-b">Stockholder : </td>
                    <td>{$ra.stockholder.doc_no|escape:'html'}</td>
                </tr>                
                <tr style="height: 32px;">
                    <td class="form-td-title-b">DDT/BOL Number : </td>
                    <td>{$ra.ddt_number|escape:'html'|undef}</td>
                </tr>                
                <tr style="height: 32px;">
                    <td class="form-td-title-b">DDT/BOL Date : </td>
                    <td>{if $ra.ddt_date > 0}{$ra.ddt_date|escape:'html'|date_format:'d/m/Y'}{else}{''|undef}{/if}</td>
                </tr>
                <tr style="height: 32px;">
                    <td class="form-td-title-b">Weighed Weight : </td>
                    <td>{if $ra.weighed_weight > 0}
                            {*$ra.weighed_weight|string_format:'%.3f'} {$ra.weight_unit|wunit*}
                            {if $ra.weight_unit == "mt"}
                                {$ra.weighed_weight|string_format:'%.3f'} ton
                            {else}
                                {$ra.weighed_weight|string_format:'%d'} {$ra.weight_unit|wunit}
                            {/if}
                        {else}
                            {''|undef}
                        {/if}</td>
                </tr>                
                <tr style="height: 32px;">
                    <td class="form-td-title-b">Max Weight : </td>
                    {if !empty($ra.total_weight_max)}<td{if !empty($ra.total_weightmax_highlight)} class="ra-td-bgcolor-yellow"{/if}>
                            {*$ra.total_weight_max|string_format:'%.3f'} {$ra.weight_unit|wunit*}
                            {if $ra.weight_unit == "mt"}
                                {$ra.total_weight_max|string_format:'%.3f'} ton
                            {else}
                                {$ra.total_weight_max|string_format:'%d'} {$ra.weight_unit|wunit}
                            {/if}
                        </td>
                    {else}<td>{''|undef}</td>
                    {/if}
                </tr>
            </table>
        </td>
    </tr>
</table>
<div class="pad"></div>

<h3 style="margin-bottom: 10px;">Packing List</h3>
{if empty($items)}
There are no items
{else}
<table class="list" style="width: 100%">
    <tr class="top-table" style="height: 25px;">
        <th>Plate id</th>
        <th>Thickness,<br />{$ra.dimension_unit}</th>
        <th>Width,<br />{$ra.dimension_unit}</th>
        <th>Length,<br />{$ra.dimension_unit}</th>
        <th>Weight,<br />{if $ra.weight_unit == 'mt'} ton{else}{$ra.weight_unit}{/if}</th>
        <th>Weighted Weight,<br />{if $ra.weight_unit == 'mt'} ton{else}{$ra.weight_unit}{/if}</th>
        <th>Incoming DDT</th>
        <th>Owner</th>
        <th>Status</th>
    </tr>
    {foreach $items as $item}
    <tr{if $item.steelitem.status_id >= $smarty.const.ITEM_STATUS_ORDERED} class="item-status-{$item.steelitem.status_id}"{/if}>
        <td onclick="show_item_context(event, {$item.steelitem.id});">{$item.steelitem.guid|escape:'html'|undef}</td>
        <td onclick="show_item_context(event, {$item.steelitem.id});">{if !empty($item.steelitem.thickness)}{$item.steelitem.thickness|escape:'html'}{/if}</td>
        <td onclick="show_item_context(event, {$item.steelitem.id});"{if !empty($item.is_width_too_large)} style="color: red; font-weight: bold; background-color: #FEDBDA !important;"{/if}>{if !empty($item.steelitem.width)}{$item.steelitem.width|escape:'html'}{/if}</td>
        <td onclick="show_item_context(event, {$item.steelitem.id});">{if !empty($item.steelitem.length)}{$item.steelitem.length|escape:'html'}{/if}</td>
        <td onclick="show_item_context(event, {$item.steelitem.id});">{if !empty($item.steelitem.unitweight)}{if $ra.weight_unit == 'lb'}{$item.steelitem.unitweight|escape:'html'|string_format:'%d'}{else}{$item.steelitem.unitweight|escape:'html'|string_format:'%.2f'}{/if}{/if}</td>
        <td onclick="show_item_context(event, {$item.steelitem.id});">
            {if !empty($item.steelitem.unitweight_weighed)}
                {if $item.steelitem.weight_unit == 'lb'}
                    {if $item.steelitem.unitweight_weighed == 0}{''|undef}{else}{$item.steelitem.unitweight_weighed|escape:'html'|string_format:'%d'|wunit}{/if}
                {else}
                    {if $item.steelitem.unitweight_weighed == 0}{''|undef}{else}{$item.steelitem.unitweight_weighed|escape:'html'|string_format:'%.3f'|wunit}{/if}
                {/if}
            {/if}
        </td>
        <td onclick="show_item_context(event, {$item.steelitem.id});">
        {if $item.steelitem.in_ddt_id > 0 && $item.steelitem.in_ddt.company_id == $item.steelitem.stockholder_id}
            <a href="/inddt/{$item.steelitem.in_ddt_id}">{$item.steelitem.in_ddt_number} dd {$item.steelitem.in_ddt_date|date_format:'d/m/Y'}</a>
        {else}{''|undef}
        {/if}
        </td>        
        <td onclick="show_item_context(event, {$item.steelitem.id});">{if isset($item.steelitem.owner)}{$item.steelitem.owner.title_trade|escape:'html'}{else}{''|undef}{/if}</td>
        <td>
            {if empty($item.steelitem.status_id)}{''|undef}
            {else}
                {$item.steelitem.status_title|escape:'html'}
                {if $item.steelitem.order_id > 0}<br><a href="/order/{$item.steelitem.order_id}">{$item.steelitem.order_id|order_doc_no}</a>{/if}
            {/if}
        </td>
    </tr>
    {/foreach}
</table>
{/if}

<div class="pad"></div>
<h3>Related Documents</h3>
{if empty($objects_list)}
    {''|undef}
{else}
    {foreach $objects_list as $row}
        <a class="tag-document" style="display: inline-block; height: 20px; margin: 0 10px 10px 0;" href="/{$row.object_alias}/{$row.object_id}" target="_blank">{$row.doc_no|escape:'html'|undef}</a>
    {/foreach}
{/if}

{*
<div class="pad-10"></div>
<h3>Shared Files</h3>
{if isset($ra.attachment)}<a class="pdf" target="_blank" href="/file/{$ra.attachment.secret_name}/{$ra.attachment.original_name}" style="margin: 0 10px 10px 0;">{$ra.attachment.original_name}</a>{/if}
{if !empty($objects_list)}
    {foreach $objects_list as $row}
    {if $row.is_outdated == 0 && isset($row.attachment)}
    <a class="pdf" target="_blank" href="/file/{$row.attachment.secret_name}/{$row.attachment.original_name}" style="margin: 0 10px 10px 0;">{$row.attachment.original_name}</a>
    {/if}
    {/foreach}
{/if}
*}

<div class="pad"></div>
{include file='templates/controls/object_shared_files.tpl' object_alias='ra' object_id=$ra.id}