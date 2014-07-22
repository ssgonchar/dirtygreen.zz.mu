<table class="form" width="100%">
    <tr>
        <td class="form-td-title" width="50px">1 $ = </td>
        <td width="120px"><input type="text" name="usd_eur" value="{if isset($data) && isset($data.usd_eur)}{$data.usd_eur|string_format:'%.4f'}{/if}" class="narrow"> &euro;</td>
        <td class="form-td-title" width="50px">1 &euro; = </td>
        <td><input type="text" name="eur_gbp" value="{if isset($data) && isset($data.eur_gbp)}{$data.eur_gbp|string_format:'%.4f'}{/if}" class="narrow"> &pound;</td>
        <td rowspan="2" class="text-middle">
            <input type="submit" value="Generate Report" name="btn_generate" class="btn150b">
        </td>
        <td rowspan="2" class="text-middle">
            <p><a href="http://www.bloomberg.com/markets/currencies/" target="_blank">Bloomberg Currency Rates</a></p>
            <p><a href="http://www.bloomberg.com/markets/currencies/currency-converter/" target="_blank">Bloomberg Currency Converter</a></p>
        </td>
    </tr>
    <tr>
        <td class="form-td-title">1 $ = </td>
        <td><input type="text" name="usd_gbp" value="{if isset($data) && isset($data.usd_gbp)}{$data.usd_gbp|string_format:'%.4f'}{/if}" class="narrow"> &pound;</td>
        <td class="form-td-title">1 &euro; = </td>
        <td><input type="text" name="eur_usd" value="{if isset($data) && isset($data.eur_usd)}{$data.eur_usd|string_format:'%.4f'}{/if}" class="narrow"> $</td>
    </tr>    
</table>

<div class="pad1"></div>
<hr style="width: 100%; color: #dedede;" size="1"/>
<div class="pad1"></div>
{if isset($stockholders)}
<table class="form" width="100%">
    <tr>
        <td class="text-top">
        {if empty($stockholder_id)}
        <b>Total</b>
        {else}
        <a href="{$base_url}">Total</a>
        {/if}
        </td>       
        {foreach from=$stockholders item=row}
        <td>
            {if $stockholder_id == $row.stockholder_id}
            <b>{$row.company.title}</b>
            {else}
            <a href="{$base_url}stockholder:{$row.stockholder_id};">{$row.company.title}</a>
            {/if}
            <br>{$row.company.city.title}
        </td>
        {/foreach}
    </tr>
</table>
{/if}
<div class="pad1"></div>

{if !empty($wo_stockholder_id) || !empty($wo_owner_id) || !empty($wo_price) || !empty($wo_purchase_price) || !empty($wo_purchase_currency)}
<div style="padding: 5px; background: #ff9d9d; border: solid 2px #da0000; color: #650000;">
{if !empty($wo_stockholder_id)}{number value=$wo_stockholder_id e0='items' e1='item' e2='items'} without stockholder&nbsp;&nbsp;{/if}
{if !empty($wo_owner_id)}{number value=$wo_owner_id e0='items' e1='item' e2='items'} without owner&nbsp;&nbsp;{/if}
{if !empty($wo_price)}{number value=$wo_price e0='items' e1='item' e2='items'} without price&nbsp;&nbsp;{/if}
{if !empty($wo_purchase_price)}{number value=$wo_purchase_price e0='items' e1='item' e2='items'} without purchase price&nbsp;&nbsp;{/if}
{if !empty($wo_purchase_currency)}{number value=$wo_purchase_currency e0='items' e1='item' e2='items'} without purchase currency&nbsp;&nbsp;{/if}
</div>
<div class="pad1"></div>
{/if}

{if !isset($owners)}
    Please fill currency rates & press "Generate Report" button.
    <div class="pad"></div>
{elseif empty($owners.total.qtty)}
    <h2>Error creating report by owners !</h2>
    There are no owners specified for plates.
    <div class="pad"></div>
{elseif empty($stockholder_id)}
    <table width="100%" class="list">
    <tbody>
        <tr class="top-table">
            <th class="text-left">Stock Location</th>
            <th>Number Of Plates</th>
            <th class="text-right">Average Purchase Price</th>
            <th class="text-right">Average Sales Price</th>
            <th class="text-right">Average Valuation Price</th>
            <th class="text-right">Total Weight</th>
            <th class="text-right">Total Stock Value</th>
        </tr>
        {foreach from=$owners.data item=owner}
        <tr>
            <td class="text-left" colspan="7"><h3>{$owner.company.company.title_trade}</h3></td>
        </tr>
        {foreach from=$owner.stockholder item=stockholder}
        <tr onclick="location.href='{$base_url}stockholder:{$stockholder.company.company_id};#owner-{$owner.company.company_id}';">
            <td class="text-left">{$stockholder.company.company.title}, {$stockholder.company.company.city.title}</td>
            <td>{$stockholder.total.qtty}</td>
            <td class="text-right"{if $stockholder.total.p_price_sum/$stockholder.total.qtty <= 0} style="color:red"{/if}>&euro;&nbsp;{$stockholder.total.p_price_sum/$stockholder.total.qtty|number_format:2:false}</td>
            <td class="text-right"{if $stockholder.total.price_sum/$stockholder.total.qtty <= 0} style="color:red"{/if}>&euro;&nbsp;{$stockholder.total.price_sum/$stockholder.total.qtty|number_format:2:false}</td>
            <td class="text-right"{if $stockholder.total.v_price_sum/$stockholder.total.qtty <= 0} style="color:red"{/if}>&euro;&nbsp;{$stockholder.total.v_price_sum/$stockholder.total.qtty|number_format:2:false}</td>
            <td class="text-right">{$stockholder.total.weight_ton|number_format:3:false}&nbsp;Ton</td>
            <td class="text-right">&euro;&nbsp;{$stockholder.total.value|number_format:2:false}</td>            
        </tr>
        {/foreach}
        {/foreach}
        <tr>
            <td class="text-left"><h3>Total</h3></td>
            <td style="font-weight: bold;">{$owners.total.qtty}</td>
            <td class="text-right" style="font-weight: bold;">&euro;&nbsp;{$owners.total.p_price_sum/$owners.total.qtty|number_format:2:false}</td>
            <td class="text-right" style="font-weight: bold;">&euro;&nbsp;{$owners.total.price_sum/$owners.total.qtty|number_format:2:false}</td>
            <td class="text-right" style="font-weight: bold;">&euro;&nbsp;{$owners.total.v_price_sum/$owners.total.qtty|number_format:2:false}</td>
            <td class="text-right" style="font-weight: bold;">{$owners.total.weight_ton|number_format:3:false}&nbsp;Ton</td>
            <td class="text-right" style="font-weight: bold;">&euro;&nbsp;{$owners.total.value|number_format:2:false}</td>            
        </tr>        
    </tbody>
    </table>
{else}
    {foreach from=$owners.data item=owner}
    <h3 id="owner-{$owner.company.company_id}">{$owner.company.company.title_trade}</h3>
    <table width="100%" class="list">
    <tbody>
        <tr class="top-table">
            <th>Sys. Id</th>
            <th>Id</th>
            <th>Steel Grade</th>
            <th>Thickness</th>
            <th>Width</th>
            <th>Length</th>
            <th>Weight</th>
            <th>Days On Stock</th>
            <th>Prev. Year End</th>
            <th>Status</th>
            <th>In DDT</th>
            <th>Purchace Invoice</th>
            <th>Purchace Price</th>
            <th>Sales Price</th>
            <th>Sales Invoice No</th>
            <th>Sales Invoice Date</th>
            <th>Valuation Price</th>
            <th>Valuation Weight</th>
            <th>Value</th>
            <th>Notes</th>            
        </tr>
        {foreach from=$owner.data item=row}
        <tr onclick="show_item_context(event, {$row.steelitem_id});"
        {if $row.status_id == $smarty.const.ITEM_STATUS_ORDERED} class="report-item-ordered"{elseif $row.status_id >= $smarty.const.ITEM_STATUS_RELEASED} class="report-item-sold"{/if}>
            <td>{$row.steelitem_id}</td>
            <td>{$row.guid}</td>
            <td>{if isset($row.steelgrade)}{$row.steelgrade.title}{else}{''|undef}{/if}</td>
            <td>{$row.thickness} {$row.dimension_unit|dunit}</td>
            <td>{$row.width} {$row.dimension_unit|dunit}</td>
            <td>{$row.length} {$row.dimension_unit|dunit}</td>
            <td>{if $row.weight_unit == 'lb'}{$row.unitweight|number_format:3:true}{else}{$row.unitweight|number_format:3:false}{/if} {$row.weight_unit|wunit}</td>
            <td>{$row.days_on_stock}</td>
            {if ($row.years_on_stock > 1)}<td style="background: #FFD700 !important;">yes{else}<td>no{/if}</td>
            {if $row.status_id == $smarty.const.ITEM_STATUS_PRODUCTION}
            <td>
                In Production
            {elseif $row.status_id == $smarty.const.ITEM_STATUS_TRANSFER}
            <td>
                Transfer To Stock
            {elseif $row.status_id == $smarty.const.ITEM_STATUS_STOCK}
            <td>
                On Stock
            {elseif $row.status_id == $smarty.const.ITEM_STATUS_ORDERED}
            <td style="background: #FEDBDA;">                
                Ordered
            {elseif $row.status_id >= $smarty.const.ITEM_STATUS_RELEASED}
            <td style="background: #C1FFC1;">
                Sold
            {else}
            <td style="color: #777;">
                N/D
            {/if}
            </td>
            <td>
                {if empty($row.in_ddt_number)}<span style="color: #999;">N/D</span>
                {else}{$row.in_ddt_number} dd {$row.in_ddt_date|date_format:"d/m/Y"}{/if}
            </td>
            <td>
                {if empty($row.supplier_invoice_number)}<span style="color: #999;">N/D</span>
                {else}{$row.supplier_invoice_number} dd {$row.supplier_invoice_date|date_format:"d/m/Y"}{/if}
            </td>
            {if $row.purchase_price > 0}
            <td class="text-right">
                {$row.purchase_price|number_format:2:false} {$row.purchase_currency|cursign}/Ton
            {else}
            <td style="color: #999;">
                N/D
            {/if}
            </td>
            <td nowrap="nowrap" class="text-right">{$row.real_price|number_format:2:false} {$row.real_currency|cursign}/{$row.real_price_unit|wunit}</td>
            <td>{if isset($row.invoice)}{$row.invoice.number}{else}<span style="color: #999;">N/D</span>{/if}</td>
            <td>{if isset($row.invoice)}{$row.invoice.date}{else}<span style="color: #999;">N/D</span>{/if}</td>
            <td class="text-right" nowrap="nowrap">{$row.valuation_price_eur|number_format:2:false} &euro;/Ton</td>
            <td class="text-right" nowrap="nowrap">{$row.unitweight_ton|number_format:3:false} Ton</td>
            <td class="text-right" nowrap="nowrap">{$row.valuation_value_eur|number_format:2:false} &euro;</td>
            <td>{if empty($row.internal_notes)}<span style="color: #999;">N/D</span>{else}{$row.internal_notes}{/if}</td>
        </tr>
        {/foreach}
        <tr>
            <td class="text-right" style="font-weight: bold;" colspan="6">Total : </td>
            <td nowrap="nowrap" style="font-weight: bold;">{$owner.total.weight_ton|number_format:3:false} Ton</td>
            <td style="font-weight: bold;" colspan="5">{$owner.total.qtty} pcs</td>
            <td nowrap="nowrap" class="text-right" style="font-weight: bold;">{$owner.total.p_price_sum/$owner.total.qtty|number_format:2:false}&nbsp;&euro;</td>
            <td nowrap="nowrap" class="text-right" style="font-weight: bold;">{*&euro; {$owner.total.price_sum/$owner.total.qtty|number_format:2:false}*}</td>
            <td colspan="2"></td>
            <td nowrap="nowrap" class="text-right" style="font-weight: bold;">{$owner.total.v_price_sum/$owner.total.qtty|number_format:2:false}&nbsp;&euro;/Ton</td>
            <td></td>
            <td nowrap="nowrap" class="text-right" style="font-weight: bold;">{$owner.total.value|number_format:2:false}&nbsp;&euro;</td>
            <td></td>
        </tr>
    </tbody>
    </table>
    <div class="pad"></div>        
    {/foreach}
{/if}
