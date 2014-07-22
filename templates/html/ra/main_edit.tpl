<table class="form" width="100%">
    <tr>
        <td width="50%" class="text-top">
            <table class="form" style="width: 100%;">
                <tr>
                    <td class="form-td-title-b" style="width:170px">Transport Company : </td>
                    <td>
                        <input type="text" id="ra_company" name="form[company]" value="{if !empty($form.company)}{$form.company.doc_no|escape:'html'}{/if}" class="wide ui-autocomplete-input" autocomplete="off" role="textbox" aria-autocomplete="list" aria-haspopup="true" />
                        <input type="hidden" id="ra_company_id" name="form[company_id]" value="{if !empty($form.company)}{$form.company.id|escape:'html'}{else}0{/if}" />
                    </td>
                </tr>                
                <tr>
                    <td class="form-td-title">Truck Number : </td>
                    <td><input type="text" name="form[truck_number]" value="{$form.truck_number|escape:'html'}" class="wide" maxlength="50" /></td>
                </tr>                
                <tr>
                    <td class="form-td-title-b">Destination : </td>
                    <td>
                        <select class="dest-sholder-select wide" name="form[dest_stockholder_id]">
                            <option value="0">--</option>
                            {foreach $dest_sholders_list as $row}
                                {if $row.company_id != $form.stockholder_id}
                                    <option value="{$row.company_id}"{if !empty($form.dest_stockholder_id) && $form.dest_stockholder_id == $row.company_id} selected="selected"{/if}>{$row.company.doc_no|escape:'html'}{if !empty($row.company.city)} ({$row.company.city.title|escape:'html'}){/if}</option>
                                {/if}
                            {/foreach}
                        </select>
                    </td>
                </tr>
                <tr>
                    <td class="form-td-title-b">or : </td>
                    <td>
                        <input class="dest-sholder-input wide" type="text" name="form[destination]" value="{$form.destination|escape:'html'}" maxlength="200" />
                    </td>
                </tr>
                <tr>
                    <td class="form-td-title">Loading Date : </td>
                    <td><input type="text" name="form[loading_date]" value="{$form.loading_date|escape:'html'}" class="wide" maxlength="200" /></td>
                </tr>                
            </table>
        </td>
        <td class="text-top">
            <h4>Out DDT / BOL Data</h4>
            <table class="form" style="background: #f5f5f5;">
                <tr style="height: 32px;">
                    <td class="form-td-title">Stockholder : </td>
                    <td style="width: 220px;">{$form.stockholder.doc_no|escape:'html'}</td>
                </tr>                
                <tr>
                    <td class="form-td-title">Number : </td>
                    <td><input type="text" name="form[ddt_number]" value="{if !empty($form.ddt_number)}{$form.ddt_number|escape:'html'}{/if}" class="normal" maxlength="50" /></td>
                </tr>                
                <tr>
                    <td class="form-td-title">Date : </td>
                    <td><input type="text" name="form[ddt_date]" class="datepicker narrow" value="{if $form.ddt_date != 0}{$form.ddt_date|escape:'html'|date_format:'d/m/Y'}{/if}"></td>
                </tr>
                <tr>
                    <td class="form-td-title">Weighed Weight : </td>
                    <td><input id="weighed-weight" type="text" name="form[weighed_weight]" value="{if isset($form.weighed_weight) && $form.weighed_weight > 0}{if $form.weight_unit == "mt"}{$form.weighed_weight|string_format:'%.3f'}{else}{$form.weighed_weight|string_format:'%d'}{/if}{/if}" class="narrow" maxlength="20" /></td>
                </tr>                
                <tr style="height: 32px;">
                    <td class="form-td-title">Max Weight : </td>
                    <td>{if !empty($form.total_weight_max)}{$form.total_weight_max|string_format:'%.2f'} {$form.weight_unit}{/if}</td>
                </tr>
            </table>
        </td>
    </tr>
    <tr>
        <td class="text-top">
            <table class="form" style="width: 100%;">
                <tr>
                    <td class="form-td-title text-top" style="width:170px">DDT Instructions : </td>
                    <td><textarea name="form[ddt_instructions]" class="wide" rows="5">{$form.ddt_instructions|escape:'html'}</textarea></td>
                </tr>
                <tr>
                    <td class="form-td-title text-top">Marking Requirements : </td>
                    <td><textarea name="form[marking]" class="wide" rows="5">{$form.marking|escape:'html'}</textarea></td>
                </tr>
                <tr>
                    <td class="form-td-title text-top">Dunnaging Requirements : </td>
                    <td><textarea name="form[dunnaging]" class="wide" rows="5">{$form.dunnaging|escape:'html'}</textarea></td>
                </tr>
            </table>        
        </td>
        <td class="text-top">
            <table class="form" style="width: 100%;">
                <tr>
                    <td class="form-td-title text-top">Notes : </td>
                    <td><textarea name="form[notes]" class="wide" rows="5">{$form.notes|escape:'html'}</textarea></td>
                </tr>
                <tr>
                    <td class="form-td-title text-top">Coupon : </td>
                    <td><textarea name="form[coupon]" class="wide" rows="5">{$form.coupon|escape:'html'}</textarea></td>
                </tr>
                <tr>
                    <td class="form-td-title text-top">Consignee : </td>
                    <td><textarea name="form[consignee]" class="wide" rows="5">{$form.consignee|escape:'html'}</textarea></td>
                </tr>
                <tr>
                    <td class="form-td-title text-top">Consignee Ref. : </td>
                    <td><input type="text" name="form[consignee_ref]" value="{if isset($form.consignee_ref)}{$form.consignee_ref|escape:'html'}{/if}" class="normal" maxlength="200" /></td>
                </tr>
                <tr>
                    <td class="form-td-title">Dimensions in mm : </td>
                    <td><input type="checkbox" name="form[mm_dimensions]" value="1" {if !empty($form.mm_dimensions)}checked="checked"{/if} /></td>
                </tr>
            </table>        
        </td>        
    </tr>
</table>
<div class="pad"></div>

<h3 style="margin-bottom: 10px;" class="ra-packing-list-title">Packing List</h3>
{if empty($items)}
There are no items
{else}
<span class="ra-pl-is-empty" style="display: none;">There are no items</span>
<table class="list" style="width: 100%">
    <tr class="top-table" style="height: 25px;">
        <th class="ra-item-manipulation column-header{if !empty($form.variants_are_exist)} variants-are-exist{/if}" rowspan="2" style="width: 30px;"></th>
        <th rowspan="2">Plate id</th>
        <th rowspan="2">Thickness,<br />{$form.dimension_unit}</th>
        <th rowspan="2">Width,<br />{$form.dimension_unit}</th>
        <th rowspan="2">Length,<br />{$form.dimension_unit}</th>
        <th rowspan="2">Weight,<br />{if $form.weight_unit == 'mt'} ton{else}{$form.weight_unit}{/if}</th>
        <th rowspan="2">Weighed Weight,<br />{if $form.weight_unit == 'mt'} ton{else}{$form.weight_unit}{/if}</th>
        <th rowspan="2" style="width: 45px;">Theor. Weight</th>
        <th colspan="2" style="border-bottom : 1px solid #B9B9B9;">Incoming DDT</th>
        <th colspan="2" style="border-bottom : 1px solid #B9B9B9;">Outgoing DDT</th>
        <th rowspan="2">Owner</th>
        <th rowspan="2">Status</th>
        <th rowspan="2"></th>
        <th rowspan="2"></th>
    </tr>
    <tr class="top-table" style="height: 25px;">
        <th>Number</th>
        <th>Date</th>
        <th>Number</th>
        <th>Date</th>
    </tr>
    {foreach $items as $item}
    <tr class="ra-item-primary{if $item.steelitem.status_id >= $smarty.const.ITEM_STATUS_ORDERED} item-status-{$item.steelitem.status_id}{/if}" iid="{$item.id}" pid="0">
        <td class="ra-item-manipulation column-element{if !empty($form.variants_are_exist)} variants-are-exist{/if}">{if !empty($item.variants)}<input type="radio" name="form[primary_items][{$item.id}][]" value="{$item.id}" title="Set as primary item" checked="checked" />{/if}</td>
        <td onclick="show_item_context(event, {$item.steelitem.id});">{if empty($item.steelitem.guid)}<span style="font-weight: normal;">{$item.steelitem.doc_no}</span>{else}{$item.steelitem.guid|escape:'html'}{/if}</td>
        <td onclick="show_item_context(event, {$item.steelitem.id});">{if !empty($item.steelitem.thickness)}{$item.steelitem.thickness|escape:'html'}{/if}</td>
        <td onclick="show_item_context(event, {$item.steelitem.id});"{if !empty($item.is_width_too_large)} style="color:red; background-color: #FEDBDA !important;"{/if}>{if !empty($item.steelitem.width)}{$item.steelitem.width|escape:'html'}{/if}</td>
        <td onclick="show_item_context(event, {$item.steelitem.id});">{if !empty($item.steelitem.length)}{$item.steelitem.length|escape:'html'}{/if}</td>
        <td onclick="show_item_context(event, {$item.steelitem.id});">{if !empty($item.steelitem.unitweight)}{if $item.steelitem.weight_unit == 'lb'}{$item.steelitem.unitweight|escape:'html'|string_format:'%d'|wunit}{else}{$item.steelitem.unitweight|escape:'html'|string_format:'%.2f'|wunit}{/if}{/if}</td>
        {* Weighed Weight *}
        <td class="td-weighed-weight" onclick="show_item_context(event, {$item.steelitem.id});">
            {if !empty($item.steelitem.unitweight_weighed)}
                {if $item.steelitem.weight_unit == 'lb'}
                    {$item.steelitem.unitweight_weighed|escape:'html'|string_format:'%d'|wunit}
                {else}
                    {$item.steelitem.unitweight_weighed|escape:'html'|string_format:'%.3f'|wunit}
                {/if}
            {/if}
        </td>
        {*$item.steelitem.unitweight|escape:'html'|string_format:'%.2f'*}
        <td><input type="checkbox" name="form[is_theor_weight][{$item.id}]" value="1"{if $item.is_theor_weight == 1} checked="checked"{/if} /></td>
        <td onclick="show_item_context(event, {$item.steelitem.id});">{if !empty($item.steelitem.in_ddt_number)}{$item.steelitem.in_ddt_number|escape:'html'}{/if}</td>
        <td onclick="show_item_context(event, {$item.steelitem.id});">{if !empty($item.steelitem.in_ddt_date)}{$item.steelitem.in_ddt_date|escape:'html'|date_format:'d/m/Y'}{/if}</td>
        <td onclick="show_item_context(event, {$item.steelitem.id});">{if !empty($item.steelitem.ddt_number)}{$item.steelitem.ddt_number|escape:'html'}{/if}</td>
        <td onclick="show_item_context(event, {$item.steelitem.id});">{if !empty($item.steelitem.ddt_date)}{$item.steelitem.ddt_date|escape:'html'|date_format:'d/m/Y'}{/if}</td>
        <td onclick="show_item_context(event, {$item.steelitem.id});">{if isset($item.steelitem.owner)}{$item.steelitem.owner.title_trade|escape:'html'}{else}{''|undef}{/if}</td>
        <td>
            {if empty($item.steelitem.status_id)}{''|undef}
            {else}
                {$item.steelitem.status_title|escape:'html'}
                {if $item.steelitem.order_id > 0}<br><a href="/order/{$item.steelitem.order_id}">{$item.steelitem.order_id|order_doc_no}</a>{/if}
            {/if}
        </td>        
        <td><a href="/ra/{$form.id}/item/{$item.id}/addvariant" title="Add variant">Add variant</a></td>
        <td>{if $form.status_id == $smarty.const.RA_STATUS_OPEN}<img src="/img/icons/cross.png" style="cursor: pointer" alt="Delete" title="Delete" onclick="remove_item({$form.id}, {$item.id}, 0);"/>{/if}</td>
    </tr>    
    {if !empty($item.variants)}
        {foreach $item.variants as $variant}
        <tr class="ra-item-variant" iid="{$variant.id}" pid="{$item.id}">
            <td><input type="radio" name="form[primary_items][{$item.id}][]" value="{$variant.id}" title="Set as primary item" /></td>
            <td onclick="show_item_details({$variant.steelitem.id});">{if empty($variant.steelitem.guid)}{$variant.steelitem.doc_no}{else}{$variant.steelitem.guid|escape:'html'}{/if}</td>
            <td onclick="show_item_details({$variant.steelitem.id});">{if !empty($variant.steelitem.thickness)}{$variant.steelitem.thickness|escape:'html'}{/if}</td>
            <td onclick="show_item_details({$variant.steelitem.id});">{if !empty($variant.steelitem.width)}{$variant.steelitem.width|escape:'html'}{/if}</td>
            <td onclick="show_item_details({$variant.steelitem.id});">{if !empty($variant.steelitem.length)}{$variant.steelitem.length|escape:'html'}{/if}</td>
            <td onclick="show_item_details({$variant.steelitem.id});">{if !empty($variant.steelitem.unitweight)}{$variant.steelitem.unitweight|escape:'html'|string_format:'%.2f'}{/if}</td>
            <td>{*<input type="hidden" name="form[is_theor_weight][{$variant.id}]" value="0" />*}</td>
            <td onclick="show_item_details({$variant.steelitem.id});">{if !empty($variant.steelitem.in_ddt_number)}{$variant.steelitem.in_ddt_number|escape:'html'}{/if}</td>
            <td onclick="show_item_details({$variant.steelitem.id});">{if !empty($variant.steelitem.in_ddt_date)}{$variant.steelitem.in_ddt_date|escape:'html'|date_format:'d/m/Y'}{/if}</td>
            <td onclick="show_item_details({$variant.steelitem.id});">{if !empty($variant.steelitem.ddt_number)}{$variant.steelitem.ddt_number|escape:'html'}{/if}</td>
            <td onclick="show_item_details({$variant.steelitem.id});">{if !empty($variant.steelitem.ddt_date)}{$variant.steelitem.ddt_date|escape:'html'|date_format:'d/m/Y'}{/if}</td>
            <td onclick="show_item_details({$variant.steelitem.id});">{if isset($variant.steelitem.owner)}{$variant.steelitem.owner.title_trade|escape:'html'}{else}{''|undef}{/if}</td>
            <td>
                {if empty($variant.steelitem.status_id)}{''|undef}
                {else}
                    {$variant.steelitem.status_title|escape:'html'}
                    {if $variant.steelitem.order_id > 0}<br><a href="/order/{$variant.steelitem.order_id}">{$variant.steelitem.order_id|order_doc_no}</a>{/if}
                {/if}
            </td>
            <td>variant</td>
            <td><img src="/img/icons/cross.png" style="cursor: pointer" alt="Delete" title="Delete" onclick="remove_item({$form.id}, {$variant.id}, {$variant.parent_id});"/></td>
        </tr>
        {/foreach}
    {/if}    
    {/foreach}
</table>
{/if}