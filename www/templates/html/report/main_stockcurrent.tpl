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

{if !empty($wo_stockholder_id) || !empty($wo_owner_id) || !empty($wo_price) || !empty($wo_purchase_price) || !empty($wo_purchase_currency)}
    <div style="position: absolute;">
        <img src="/img/layout/gnome.jpg" style="position: absolute; top: 0; left: 0;">
    </div>
    <div class="bubble" style="margin-left: 55px; width: 500px;" id="gnome_text">
        <div style="font-weight:bold; color: red; font-size: 16px; margin-bottom: 5px;">Incomplete data provided !</div>
        {if !empty($wo_stockholder_id)}{number value=$wo_stockholder_id e0='items' e1='item' e2='items'} without stockholder {/if}
        {if !empty($wo_owner_id)}{number value=$wo_owner_id e0='items' e1='item' e2='items'} without owner {/if}
        {if !empty($wo_price)}{number value=$wo_price e0='items' e1='item' e2='items'} without price {/if}
        {if !empty($wo_purchase_price)}{number value=$wo_purchase_price e0='items' e1='item' e2='items'} without purchase price {/if}
        {if !empty($wo_purchase_currency)}{number value=$wo_purchase_currency e0='items' e1='item' e2='items'} without purchase currency {/if}
    </div>
    <div class="separator pad"></div>
{/if}

{if isset($data)}
<h2>Plates By Location</h2>
<table width="100%" class=" search-target">
<tbody>
    <tr class="top-table">
        <th class="text-left">Location</th>
        <th width="10%">Number of Plates</th>
        <th width="12%">Average Purchase Price<br>&euro;/Ton</th>
        <th width="12%">Average Sales Price<br>&euro;/Ton</th>
        <th width="12%">Average Valuation Price<br>&euro;/Ton</th>
        <th width="12%">Total Weight<br>Ton</th>
        <th width="12%">Total Stock Value<br>&euro;</th>
    </tr>
    {foreach from=$locations.data item=row}
    <tr>
        <td class="text-left">{$row.company.title}, {$row.company.city.title}</td>
        <td>{$row.total.qtty}</td>
        <td class="text-right">{$row.total.p_price_sum/$row.total.qtty|number_format:2:false}</td>
        <td class="text-right">{$row.total.price_sum/$row.total.qtty|number_format:2:false}</td>
        <td class="text-right">{$row.total.v_price_sum/$row.total.qtty|number_format:2:false}</td>
        <td class="text-right">{$row.total.weight_ton|number_format:3:false}</td>
        <td class="text-right">{$row.total.value|number_format:2:false}</td>
    </tr>
    {/foreach}
    <tr>
        <td class="text-right" style="font-weight: bold;">Total : </td>
        <td style="font-weight: bold;">{$locations.total.qtty}</td>
        <td class="text-right" style="font-weight: bold;">{$locations.total.p_price_sum/$locations.total.qtty|number_format:2:false}</td>
        <td class="text-right" style="font-weight: bold;">{$locations.total.price_sum/$locations.total.qtty|number_format:2:false}</td>
        <td class="text-right" style="font-weight: bold;">{$locations.total.v_price_sum/$locations.total.qtty|number_format:2:false}</td>
        <td class="text-right" style="font-weight: bold;">{$locations.total.weight_ton|number_format:3:false}</td>
        <td class="text-right" style="font-weight: bold;">{$locations.total.value|number_format:2:false}</td>
    </tr>
</tbody>
</table>
<div class="pad"></div>

{if empty($owners.total.qtty)}
    <h2>Error creating report by owners !</h2>
    There are no owners specified for plates .
    <div class="pad"></div>
{else}
<h2>Plates By Owner</h2>
<table width="100%" class="list">
<tbody>
    <tr class="top-table">
        <th class="text-left">Location</th>
        <th width="10%">Number of Plates</th>
        <th width="12%">Average Purchase Price<br>&euro;/Ton</th>
        <th width="12%">Average Sales Price<br>&euro;/Ton</th>
        <th width="12%">Average Valuation Price<br>&euro;/Ton</th>
        <th width="12%">Total Weight<br>Ton</th>
        <th width="12%">Total Stock Value<br>&euro;</th>
    </tr>
    {foreach from=$owners.data item=row}
    <tr>
        <td colspan="7" style="font-weight: bold;" class="text-left">{$row.company.title_trade}</td>
    </tr>
    {foreach from=$row.data item=stockholder}
    <tr>
        <td class="text-left">{$stockholder.company.title}, {$stockholder.company.city.title}</td>
        <td>{$stockholder.total.qtty}</td>
        <td class="text-right">{$stockholder.total.p_price_sum/$row.total.qtty|number_format:2:false}</td>
        <td class="text-right">{$stockholder.total.price_sum/$row.total.qtty|number_format:2:false}</td>
        <td class="text-right">{$stockholder.total.v_price_sum/$row.total.qtty|number_format:2:false}</td>
        <td class="text-right">{$stockholder.total.weight_ton|number_format:3:false}</td>
        <td class="text-right">{$stockholder.total.value|number_format:2:false}</td>
    </tr>
    {/foreach}
    <tr>
        <td class="text-right" style="font-weight: bold;">Total : </td>
        <td style="font-weight: bold;">{$row.total.qtty}</td>
        <td class="text-right" style="font-weight: bold;">{$row.total.p_price_sum/$row.total.qtty|number_format:2:false}</td>
        <td class="text-right" style="font-weight: bold;">{$row.total.price_sum/$row.total.qtty|number_format:2:false}</td>
        <td class="text-right" style="font-weight: bold;">{$row.total.v_price_sum/$row.total.qtty|number_format:2:false}</td>
        <td class="text-right" style="font-weight: bold;">{$row.total.weight_ton|number_format:3:false}</td>
        <td class="text-right" style="font-weight: bold;">{$row.total.value|number_format:2:false}</td>
    </tr>
    {/foreach}
</tbody>
</table>
<div class="pad"></div>
{/if}

{if !empty($locations)}
    {foreach from=$locations.data item=row}
        <h3>Steel Plates in {$row.company.title}, {$row.company.city.title}</h3>
            <table width="100%" class="list">
            <tbody>
                <tr class="top-table">
                    <th width="10%">ID</th>
                    <th width="10%">Steel Grade</th>
                    <th width="5%">Thickness<br>{$row.dimension_unit}</th>
                    <th width="5%">Width<br>{$row.dimension_unit}</th>
                    <th width="5%">Length<br>{$row.dimension_unit}</th>
                    <th width="7%">Weight<br>{$row.weight_unit}</th>
                    {if $row.weight_unit != 'mt'}
                    <th width="7%">Weight<br>Ton</th>
                    {/if}
                    <th width="5%">Days On Stock</th>
                    <th width="7%">Purchase Price</th>
                    <th width="7%">Sales Price<br>{$row.currency|cursign}/{$row.weight_unit}</th>
                    <th width="7%">Valuation price<br>&euro;/Ton</th>
                    <th width="7%">Value<br>&euro;</th>
                    <th>Notes</th>
                </tr>
                {foreach name='locations' from=$row.data item=steelitem}
                <tr onclick="show_item_context(event, {$steelitem.id});">
                    <td>{$steelitem.guid}</td>
                    <td>{if isset($steelitem.steelgrade)}{$steelitem.steelgrade.title}{/if}</td>
                    <td>{$steelitem.thickness}</td>
                    <td>{$steelitem.width}</td>
                    <td>{$steelitem.length}</td>
                    <td>{$steelitem.unitweight|string_format:'%.3f'|number_format}</td>
                    {if $row.weight_unit != 'mt'}
                    <td>{$steelitem.unitweight_ton|string_format:'%.3f'|number_format}</td>
                    {/if}                    
                    <td>{$steelitem.days_on_stock}</td>
                    <td>{$steelitem.purchase_price|string_format:'%.2f'|number_format}{if !empty($steelitem.purchase_currency)}, {$steelitem.purchase_currency}/Ton{/if}</td>
                    <td>{$steelitem.price|string_format:'%.2f'|number_format}</td>
                    <td>{$steelitem.valuation_price_eur|string_format:'%.2f'|number_format}</td>
                    <td>{$steelitem.valuation_value_eur|string_format:'%.2f'|number_format}</td>
                    <td>{$steelitem.internal_notes}</td>
                </tr>        
                {/foreach}
                <tr>
                    <td colspan="5" class="text-right" style="font-weight: bold;">Total : </td>
                    <td style="font-weight: bold;">{$row.total.weight|string_format:'%.3f'|number_format}&nbsp;{$row.weight_unit|wunit}</td>
                    {if $row.weight_unit != 'mt'}
                    <td style="font-weight: bold;">{$row.total.weight_ton|string_format:'%.3f'|number_format}&nbsp;Ton</td>
                    {/if}                    
                    <td colspan="4"></td>
                    <td style="font-weight: bold;">{$row.total.value|string_format:'%.2f'|number_format}&nbsp;&euro;</td>
                    <td></td>
                </tr>    
            </tbody>
            </table>        
            <div class="pad"></div>
    {/foreach}
{/if}
{/if}