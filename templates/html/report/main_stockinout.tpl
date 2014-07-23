<table class="form" style="width: 100%">
    <tr>
        <td width="20%" class="text-top">
            <table class="form" style="width: 100%;">
                <tr>
                    <td class="form-td-title-b">Report Type : </td>
                    <td>
                        <select id="type" name="form[type]" onchange="toggle_sold_controls(this.value, {$smarty.const.REPORT_INOUT_TYPE_SOLD});" style="width:100%">
                            <option value="0">--</option>
                            <option value="{$smarty.const.REPORT_INOUT_TYPE_IN}"{if $form.type == $smarty.const.REPORT_INOUT_TYPE_IN} selected="selected"{/if}>Stock IN</option>
                            <option value="{$smarty.const.REPORT_INOUT_TYPE_OUT}"{if $form.type == $smarty.const.REPORT_INOUT_TYPE_OUT} selected="selected"{/if}>Stock OUT</option>
                            <option value="{$smarty.const.REPORT_INOUT_TYPE_SOLD}"{if $form.type == $smarty.const.REPORT_INOUT_TYPE_SOLD} selected="selected"{/if}>SOLD from Stock</option>
                        </select>
                    </td>
                </tr>            
                <tr>
                    <td class="form-td-title">Period from : </td>
                    <td>
                        <input type="text" id="datefrom" name="form[datefrom]" class="datepicker" value="{if $form.datefrom != 0 && $form.datefrom > 0}{$form.datefrom|escape:'html'|date_format:'d/m/Y'}{/if}"/ style="width:100%">
                    </td>
                </tr>
                <tr>
                    <td class="form-td-title">Period to : </td>
                    <td>
                        <input type="text" id="dateto" name="form[dateto]" class="datepicker" value="{if $form.dateto != 0 && $form.dateto > 0}{$form.dateto|escape:'html'|date_format:'d/m/Y'}{/if}"/ style="width:100%">
                    </td>
                </tr>                
            </table>
        </td>
        <td width="20%" class="text-top">
            <table class="form" style="width: 100%;">
                <tr>
                    <td class="form-td-title">Owner : </td>
                    <td>
                        <select id="owner" class="max" name="form[owner]" onchange="get_inout_data(this);">
                            <option value="0">--</option>
                            <option value="mam"{if isset($form.owner) && $form.owner == 'mam'} selected="selected"{/if}>MAM</option>
                            <option value="mamit"{if isset($form.owner) && $form.owner == 'mamit'} selected="selected"{/if}> &middot; MAM IT</option>
                            <option value="mamuk"{if isset($form.owner) && $form.owner == 'mamuk'} selected="selected"{/if}> &middot; MAM UK</option>
                            <option value="pa"{if isset($form.owner) && $form.owner == 'pa'} selected="selected"{/if}>PlatesAhead</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td id="location-title" class="form-td-title{if isset($form) && $form.type != $smarty.const.REPORT_INOUT_TYPE_SOLD}-b{/if}">Location : </td>
                    <td>
                        <select id="stockholder" class="max" name="form[stockholder]" onchange="get_inout_data(this);">
                            <option value="0">--</option>
                            {foreach $stockholders_list as $row}
                            <option value="{$row.stockholder_id}"{if $form.stockholder == $row.stockholder_id} selected="selected"{/if}>{$row.stockholder.doc_no_full|escape:'html'}</option>
                            {/foreach}
                        </select>
                    </td>
                </tr>                
            </table>
        </td>
        <td width="25%" class="text-top">
            <table class="form" style="width: 100%;">
                <tr>
                    <td class="form-td-title">Supplier : </td>
                    <td>
                        <select id="supplier" class="max" name="form[supplier]">
                            <option value="0">--</option>
                            {foreach $suppliers_list as $row}
                            <option value="{$row.supplier_id}"{if $form.supplier == $row.supplier_id} selected="selected"{/if}>{$row.supplier.doc_no|escape:'html'}{if !empty($row.supplier.city)} ({$row.supplier.city.title|escape:'html'}){/if}</option>
                            {/foreach}
                        </select>
                    </td>
                </tr>
                <tr class="sold-controls"{if !isset($form.type) || $form.type != $smarty.const.REPORT_INOUT_TYPE_SOLD} style="display:none"{/if}>
                    <td class="form-td-title">Buyer Country : </td>
                    <td>
                        <select id="country" class="max country-id" name="form[country]">
                            <option value="0">--</option>
                            {foreach $countries_list as $row}
                            <option value="{$row.country_id}"{if $form.country == $row.country_id} selected="selected"{/if}>{$row.country.doc_no|escape:'html'}</option>
                            {/foreach}
                        </select>
                    </td>
                </tr>
                <tr class="sold-controls"{if !isset($form.type) || $form.type != $smarty.const.REPORT_INOUT_TYPE_SOLD} style="display:none"{/if}>
                    <td class="form-td-title">Buyer Company: </td>
                    <td>
                        <input type="text" class="company-autocomplete max" id="buyer" name="form[buyer]" value="{if !empty($form.buyer)}{$form.buyer|escape:'html'}{/if}" />
                        <input type="hidden"  id="buyer_id" name="form[buyer_id]" value="{if !empty($form.buyer_id)}{$form.buyer_id|escape:'html'}{else}0{/if}" />
                    </td>
                </tr>
            </table>        
        </td>
        <td width="25%">
            <table class="form" style="width: 100%;">
                <tr>
                    <td class="form-td-title">Steel Grade : </td>
                    <td>
                        <select id="steelgrade" class="max" name="form[steelgrade]">
                            <option value="0">--</option>
                            {foreach $steelgrades_list as $row}
                            <option value="{$row.steelgrade_id}"{if isset($form.steelgrade) && $form.steelgrade == $row.steelgrade_id} selected="selected"{/if}>{$row.steelgrade.title|escape:'html'}</option>
                            {/foreach}
                        </select>
                    </td>
                </tr>
                <tr>
                    <td class="form-td-title">Thickness : </td>
                    <td>
                        <input type="text" class="max" id="thickness" name="form[thickness]" value="{if isset($form.thickness) && !empty($form.thickness)}{$form.thickness}{/if}">
                    </td>
                </tr>                
                <tr>
                    <td class="form-td-title">Width : </td>
                    <td>
                        <input type="text" class="max" id="width" name="form[width]" value="{if isset($form.width) && !empty($form.width)}{$form.width}{/if}">
                    </td>
                </tr>                
                <tr>
                    <td class="form-td-title">Dimensions in : </td>
                    <td>
                        <select class="narrow" id="dimensions" name="form[dimensions]">
                            <option value="mm"{if !isset($form.dimensions) || empty($form.dimensions) || $form.dimensions == 'mm'} selected="selected"{/if}>mm</option>
                            <option value="in"{if isset($form.dimensions) && !empty($form.dimensions) && $form.dimensions == 'in'} selected="selected"{/if}>inches</option>
                        </select>
                    </td>
                </tr>
            </table>
        </td>
        <td width="10%" class="text-right"  style="padding: 0;">
            <input type="submit" value="Generate Report" name="btn_generate" class="btn150b">
            {if isset($filter)}
            <div class="pad1"></div>
            <a href="javascript: void(0);" class="dotted" onclick="clear_fields();">Clear filters</a>
            {/if}
        </td>
    </tr>
</table>
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

{if !empty($report_data)}
<table width="100%" class="list search-target">
<tbody>
    <tr class="top-table">
        <th>Id</th>
        <th>Plate Id</th>
        <th>Steel Grade</th>
        <th>Thickness{if isset($dimension_unit)}<br>{$dimension_unit}{/if}</th>
        <th>Width{if isset($dimension_unit)}<br>{$dimension_unit}{/if}</th>
        <th>Length{if isset($dimension_unit)}<br>{$dimension_unit}{/if}</th>
        <th>Weight{if isset($weight_unit)}<br>{$weight_unit|wunit}{/if}</th>
        {if isset($weight_unit) && $weight_unit != 'mt'}
        <th>Weight<br>Ton</th>
        {/if}
        <th>Purchase Price{if isset($pcurrency) && isset($weight_unit)}<br>{$pcurrency|cursign}/Ton{*$weight_unit|wunit*}{/if}</th>
        <th>Sale Price{if isset($currency) && isset($weight_unit)}<br>{$currency|cursign}/{$weight_unit|wunit}{/if}</th>
        <th>In DDT / Created At</th>
        <th>Status</th>
        <th>Buyer</th>
    </tr>
    {foreach $report_data as $item}
    <tr class="item{if $item.status_id >= $smarty.const.ITEM_STATUS_ORDERED} item-status-{$item.status_id}{/if} {$item.weight_unit}-{$item.stock_currency}-items" data-steelitem_id="{$item.steelitem_id}">    
        <td onclick="show_item_context(event, {$item.steelitem_id});">{$item.steelitem_id}</td>
        <td onclick="show_item_context(event, {$item.steelitem_id});">{$item.guid|escape:'html'}</td>
        <td onclick="show_item_context(event, {$item.steelitem_id});">{if !empty($item.steelgrade)}{$item.steelgrade.title|escape:'html'}{else}{''|undef}{/if}</td>
        <td onclick="show_item_context(event, {$item.steelitem_id});">
            {$item.thickness|escape:'html'|number_format:2:true|undef}{if !isset($dimension_unit)} {$item.dimension_unit}{/if}
        </td>
        <td onclick="show_item_context(event, {$item.steelitem_id});">
            {$item.width|escape:'html'|number_format:0:true|undef}{if !isset($dimension_unit)} {$item.dimension_unit}{/if}
        </td>
        <td onclick="show_item_context(event, {$item.steelitem_id});">
            {$item.length|escape:'html'|number_format:0:true|undef}{if !isset($dimension_unit)} {$item.dimension_unit}{/if}
        </td>
        <td onclick="show_item_context(event, {$item.steelitem_id});">
            {if $item.weight_unit == 'lb'}
                {$item.unitweight|escape:'html'|number_format:0:true}
            {else}
                {$item.unitweight|escape:'html'|number_format:3:true}
            {/if}{if !isset($weight_unit)} {$item.weight_unit|wunit}{/if}
        </td>
        {if isset($weight_unit) && $weight_unit != 'mt'}
        <td onclick="show_item_context(event, {$item.steelitem_id});">
            {$item.unitweight_ton|escape:'html'|number_format:3:true}
        </td>
        {/if}
        <td onclick="show_item_context(event, {$item.steelitem_id});">
            {if $item.purchase_price > 0}
                {if !isset($pcurrency)}{$item.purchase_price|number_format:2:false} {/if}{$item.purchase_currency|cursign}{if !isset($pcurrency)}/Ton{/if}
            {else}
                {''|undef}
            {/if}
        </td>
        <td onclick="show_item_context(event, {$item.steelitem_id});">
            {if $item.stock_price > 0}
                {*if !isset($currency) || !isset($weight_unit)}{$item.stock_currency|cursign}{/if}{$item.stock_price|number_format:2:false}{if !isset($currency) || !isset($weight_unit)}/{$item.weight_unit|wunit}{/if*}
                {$item.stock_price|number_format:2:false}{if !isset($currency) || !isset($weight_unit)} {$item.stock_currency|cursign}/{$item.weight_unit|wunit}{/if}
            {else}
                {''|undef}
            {/if}
        </td>
        {if $item.in_ddt_id > 0 && $item.in_ddt.company_id == $item.stockholder_id}
        <td>
            <a href="/inddt/{$item.in_ddt_id}">{$item.in_ddt_number} dd {$item.in_ddt_date|date_format:'d/m/Y'}</a>
        </td>
        {else}
        <td onclick="show_item_context(event, {$item.steelitem_id});">{$item.created_at|date_format:'d/m/Y'}<br>by {$item.author.login}</td>
        {/if}
        {if $item.order_id > 0}
        <td>
            {if $item.status_id == $smarty.const.ITEM_STATUS_DELIVERED}Delivered
            {elseif $item.status_id == $smarty.const.ITEM_STATUS_INVOICED}Invoiced
            {elseif $item.status_id == $smarty.const.ITEM_STATUS_ORDERED}Ordered
            {elseif $item.status_id == $smarty.const.ITEM_STATUS_PRODUCTION}In Production
            {elseif $item.status_id == $smarty.const.ITEM_STATUS_RELEASED}Released
            {elseif $item.status_id == $smarty.const.ITEM_STATUS_STOCK}On Stock
            {elseif $item.status_id == $smarty.const.ITEM_STATUS_TRANSFER}Transfer To Stock
            {else}{''|undef}{/if}
            <br><a href="/order/{$item.order_id}">{$item.order_id|order_doc_no}</a>
        </td>
        {else}
        <td onclick="show_item_context(event, {$item.steelitem_id});">
            {if $item.status_id == $smarty.const.ITEM_STATUS_DELIVERED}Delivered
            {elseif $item.status_id == $smarty.const.ITEM_STATUS_INVOICED}Invoiced
            {elseif $item.status_id == $smarty.const.ITEM_STATUS_ORDERED}Ordered
            {elseif $item.status_id == $smarty.const.ITEM_STATUS_PRODUCTION}In Production
            {elseif $item.status_id == $smarty.const.ITEM_STATUS_RELEASED}Released
            {elseif $item.status_id == $smarty.const.ITEM_STATUS_STOCK}On Stock
            {elseif $item.status_id == $smarty.const.ITEM_STATUS_TRANSFER}Transfer To Stock
            {else}{''|undef}{/if}
        </td>
        {/if}
        
        <td>
        {if isset($item.buyer)}
            <a href="/order/{$item.order_id}">{$item.buyer.doc_no}</a>
        {else}
            {''|undef}
        {/if}
        </td>        
    </tr>
    {/foreach}
</tbody>
</table>
{else}
Nothing was found
{/if}