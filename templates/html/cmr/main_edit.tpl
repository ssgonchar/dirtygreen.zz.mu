<table class="form" width="100%">
    <tr>
        <td width="50%" class="text-top">
            <table class="form" style="width: 100%;">
                {if $cmr.number > 0}
                <tr>
                    <td class="form-td-title-b">Number : </td>
                    <td><input type="text" name="form[number]" class="normal" value="{if $form.number > 0}{$form.number|string_format:'%d'}{/if}"></td>
                </tr>
                {/if}
                <tr>
                    <td class="form-td-title-b">Date : </td>
                    <td><input type="text" name="form[date]" class="datepicker normal" value="{if !empty($form.date) && $form.date > 0}{$form.date|date_format:'d/m/Y'}{/if}"></td>
                </tr>
                {*<tr>
                    <td class="form-td-title text-top">Buyer : </td>
                    <td class="text-top"><textarea name="form[buyer]" class="wide" rows="5">{if !empty($form.buyer)}{$form.buyer|escape:'html'}{/if}</textarea></td>
                </tr>*}
                <tr>
                    <td class="form-td-title">Buyer Name : </td>
                    <td><input type="text" name="form[buyer_name]" class="wide" maxlength="100" value="{if !empty($form.buyer_name)}{$form.buyer_name|escape:'html'}{/if}" /></td>
                </tr>
                <tr>
                    <td class="form-td-title text-top">Buyer Address : </td>
                    <td class="text-top"><textarea name="form[buyer_address]" class="wide" rows="5">{if !empty($form.buyer_address)}{$form.buyer_address|escape:'html'}{/if}</textarea></td>
                </tr>
                <tr>
                    <td class="form-td-title text-top">Delivery Point : </td>
                    <td class="text-top"><textarea name="form[delivery_point]" class="wide" rows="5">{if !empty($form.delivery_point)}{$form.delivery_point|escape:'html'}{/if}</textarea></td>
                </tr>
            </table>
        </td>
        <td class="text-top">
            <table class="form" style="width: 100%;">
                <tr>
                    <td class="form-td-title">Truck Number : </td>
                    <td><input type="text" name="form[truck_number]" value="{if !empty($form.truck_number)}{$form.truck_number|escape:'html'}{/if}" class="normal" maxlength="20" /></td>
                </tr>
                <tr>
                    <td class="form-td-title" style="width:170px">Transporter : </td>
                    <td>
                        <input type="text" id="cmr_transporter" name="form[transporter_title]" value="{if !empty($form.transporter_title)}{$form.transporter_title|escape:'html'}{/if}" class="wide ui-autocomplete-input" autocomplete="off" role="textbox" aria-autocomplete="list" aria-haspopup="true" />
                        <input type="hidden" id="cmr_transporter_id" name="form[transporter_id]" value="{if !empty($form.transporter_id)}{$form.transporter_id}{else}0{/if}" />
                    </td>
                </tr>
                <tr>
                    <td class="form-td-title">Weighed Weight : </td>
                    <td>{$form.weighed_weight|string_format:'%.3f'}&nbsp;Ton</td>
                </tr>
                <tr>
                    <td class="form-td-title">Product name : </td>
                    <td><input type="text" name="form[product_name]" value="{if !empty($form.product_name)}{$form.product_name|escape:'html'}{/if}" class="wide" maxlength="50" /></td>
                </tr>
            </table>
        </td>
    </tr>
</table>
<div class="pad"></div>

<h3 style="margin-bottom: 10px;" class="ddt-packing-list-title">Packing List</h3>
{if empty($items)}
There are no items
{else}
<span class="ddt-pl-is-empty" style="display: none;">There are no items</span>
<table class="list" style="width: 100%">
    <tr class="top-table" style="height: 25px;">
        <th>Plate id</th>
        <th>Steel Grade</th>
        <th>Thickness,<br />{$form.dimension_unit}</th>
        <th>Width,<br />{$form.dimension_unit}</th>
        <th>Length,<br />{$form.dimension_unit}</th>
        <th>Qtty,<br />pcs</th>
        <th>Weight,<br />{$form.weight_unit}</th>
        <th>Weighed Weight,<br />{$form.weight_unit}</th>
    </tr>
    {foreach $items as $item}
    <tr{if $item.steelitem.status_id >= $smarty.const.ITEM_STATUS_ORDERED} class="item-status-{$item.steelitem.status_id}"{/if}>
        <td onclick="show_item_context(event, {$item.steelitem.id});">{if empty($item.steelitem.guid)}<span style="font-weight: normal;">{$item.steelitem.doc_no}</span>{else}{$item.steelitem.guid|escape:'html'}{/if}</td>
        <td onclick="show_item_context(event, {$item.steelitem.id});">{if !empty($item.steelitem.steelgrade)}{$item.steelitem.steelgrade.title|escape:'html'}{/if}</td>
        <td onclick="show_item_context(event, {$item.steelitem.id});">{if !empty($item.steelitem.thickness_mm)}{$item.steelitem.thickness_mm|string_format:'%.1f'}{/if}</td>
        <td onclick="show_item_context(event, {$item.steelitem.id});">{if !empty($item.steelitem.width_mm)}{$item.steelitem.width_mm|string_format:'%.0f'}{/if}</td>
        <td onclick="show_item_context(event, {$item.steelitem.id});">{if !empty($item.steelitem.length_mm)}{$item.steelitem.length_mm|string_format:'%.0f'}{/if}</td>
        <td onclick="show_item_context(event, {$item.steelitem.id});">1</td>
        <td onclick="show_item_context(event, {$item.steelitem.id});">{if !empty($item.steelitem.unitweight_ton)}{$item.steelitem.unitweight_ton|escape:'html'|string_format:'%.2f'}{/if}</td>
        <td onclick="show_item_context(event, {$item.steelitem.id});">{if !empty($item.steelitem.unitweight_weighed)}{$item.steelitem.unitweight_weighed|escape:'html'|string_format:'%.3f'}{/if}</td>
    </tr>
    {/foreach}
</table>
{/if}