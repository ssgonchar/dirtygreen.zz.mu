<input type="hidden" id="dimension_unit" value="{$item.dimension_unit}">
<input type="hidden" id="weight_unit" value="{$item.weight_unit}">

{if $item.inuse}
<div style="margin-bottom: 10px;">    
    <img src="/img/icons/lock.png" title="{$item.inuse_by}" alt="{$item.inuse_by}"> Item is currently being edited by {$item.inuse_by}
</div>    
{/if}

{if !empty($item.parent_id)}
<div style="background: #F5A651; margin-bottom: 20px; margin-right: 20px; width: 150px; padding: 10px; float: left;">
    <a href="/item/{$item.parent_id}/edit" style="color: black;">{$item.rel_title}</a>
</div>
{/if}

{if !empty($item.order_id)}
<div style="background: #F5A651; margin-bottom: 20px; margin-right: 20px; width: 150px; padding: 10px; float: left;">
    <a href="/order/selectitems/{$item.order_id}/position:{$item.steelposition_id}" style="color: black;">In Order # {$item.order_id}</a>
</div>
{/if}

{if !empty($item.is_conflicted)}
<div class="item-conflicted-notice" style="margin-bottom: 20px; margin-right: 20px; width: 150px; padding: 10px; float: left;">
    <a href="/item/{$item.id}/conflicted">CONFLICTED</a>
</div>
{/if}

<div class="separator"></div>
<table id="t-i" class="list" width="100%">
    <tbody>
        <tr class="top-table" style="height: 25px;">
            <th width="10%" rowspan="2">Plate Id</th>
            <th width="8%" rowspan="2">Steel Grade</th>
            <th width="7%" rowspan="2">Thickness,<br>{$item.dimension_unit}</th>
            <th width="7%" rowspan="2">Width,<br>{$item.dimension_unit}</th>
            <th width="7%" rowspan="2">Length,<br>{$item.dimension_unit}</th>
            <th width="7%" rowspan="2">Weight,<br>{$item.weight_unit|wunit}</th>
            <th colspan="4" style="border-bottom : 1px solid #B9B9B9;">Measured</th>
            <th colspan="2" style="border-bottom : 1px solid #B9B9B9;">Transport</th>
            <th width="7%" rowspan="2">Weighed Weight,<br>{$item.weight_unit|wunit}</th>
            <th width="5%" rowspan="2">Is Virtual</th>
        </tr>
        <tr class="top-table" style="height: 25px;">
            <th width="7%">Thickness,<br>{$item.dimension_unit}</th>
            <th width="7%">Width,<br>{$item.dimension_unit}</th>
            <th width="7%">Length,<br>{$item.dimension_unit}</th>
            <th width="7%">Weight,<br>{$item.weight_unit|wunit}</th>                        
            <th width="7%">Width,<br>{$item.dimension_unit}</th>
            <th width="7%">Length,<br>{$item.dimension_unit}</th>
        </tr>
        <tr id="t-i-1">
        {if $item.inuse || $item.parent_id > 0}
            <td>{if !empty($item.guid)}{$item.guid|escape:'html'}{/if}</td>
            <td>{if isset($item.steelgrade)}{$item.steelgrade.title|escape:'html'}{/if}</td>
            <td>{if !empty($item.thickness)}{$item.thickness|escape:'html'}{/if}</td>
            <td>{if !empty($item.width)}{$item.width|escape:'html'}{/if}</td>
            <td>{if !empty($item.length)}{$item.length|escape:'html'}{/if}</td>
            <td>{if !empty($item.unitweight)}{$item.unitweight|escape:'html'|string_format:'%.2f'}{/if}</td>
            <td>{if !empty($item.thickness_measured)}{$item.thickness_measured|escape:'html'}{/if}</td>
            <td>{if !empty($item.width_measured)}{$item.width_measured|escape:'html'}{/if}</td>
            <td>{if !empty($item.length_measured)}{$item.length_measured|escape:'html'}{/if}</td>
            <td>{if $item.unitweight_measured > 0}{$item.unitweight_measured|escape:'html'}{/if}</td>
            <td>{if !empty($item.width_max)}{$item.width_max|escape:'html'}{/if}</td>
            <td>{if !empty($item.length_max)}{$item.length_max|escape:'html'}{/if}</td>
            <td>{if $item.unitweight_weighed > 0}{$item.unitweight_weighed|escape:'html'}{/if}</td>
            <td>{if !empty($item.is_virtual)}yes{else}no{/if}</td>
        {else}
            <td><input type="text" name="item[guid]" value="{if !empty($item.guid)}{$item.guid|escape:'html'}{/if}" class="max"></td>
            <td>
                <select name="item[steelgrade_id]" class="max">
                    <option value="0">--</option>
                    {foreach from=$steelgrades item=sgrow}
                    <option value="{$sgrow.steelgrade.id}"{if $item.steelgrade_id == $sgrow.steelgrade.id} selected="selected"{/if}>{$sgrow.steelgrade.title|escape:'html'}</option>
                    {/foreach}                    
                </select>
            </td>
            <td><input type="text" id="thickness-1" name="item[thickness]" value="{if !empty($item.thickness)}{$item.thickness|escape:'html'}{/if}" class="max" onkeyup="calc_unitweight(1);"></td>
            <td><input type="text" id="width-1" name="item[width]" value="{if !empty($item.width)}{$item.width|escape:'html'}{/if}" class="max" onkeyup="calc_unitweight(1);"></td>
            <td><input type="text" id="length-1" name="item[length]" value="{if !empty($item.length)}{$item.length|escape:'html'}{/if}" class="max" onkeyup="calc_unitweight(1);"></td>
            <td><input type="text" id="unitweight-1" name="item[unitweight]" value="{if !empty($item.unitweight)}{$item.unitweight|escape:'html'|string_format:'%.2f'}{/if}" class="max"></td>
            <td><input type="text" id="measured-thickness-1" name="item[thickness_measured]" value="{if !empty($item.thickness_measured)}{$item.thickness_measured|escape:'html'}{/if}" class="max" onkeyup="calc_unitweight(1, '{$item.dimension_unit}', '{$item.weight_unit}', 'measured');"></td>
            <td><input type="text" id="measured-width-1" name="item[width_measured]" value="{if !empty($item.width_measured)}{$item.width_measured|escape:'html'}{/if}" class="max" onkeyup="calc_unitweight(1, '{$item.dimension_unit}', '{$item.weight_unit}', 'measured');"></td>
            <td><input type="text" id="measured-length-1" name="item[length_measured]" value="{if !empty($item.length_measured)}{$item.length_measured|escape:'html'}{/if}" class="max" onkeyup="calc_unitweight(1, '{$item.dimension_unit}', '{$item.weight_unit}', 'measured');"></td>
            <td><input type="text" id="measured-unitweight-1" name="item[unitweight_measured]" value="{if $item.unitweight_measured > 0}{$item.unitweight_measured|escape:'html'}{/if}" class="max"></td>
            <td><input type="text" name="item[width_max]" value="{if !empty($item.width_max)}{$item.width_max|escape:'html'}{/if}" class="max"></td>
            <td><input type="text" name="item[length_max]" value="{if !empty($item.length_max)}{$item.length_max|escape:'html'}{/if}" class="max"></td>
            <td><input type="text" name="item[unitweight_weighed]" value="{if $item.unitweight_weighed > 0}{$item.unitweight_weighed|escape:'html'}{/if}" class="max"></td>
            <td><input type="checkbox" name="item[is_virtual]" value="1"{if !empty($item.is_virtual)}checked="checked"{/if}></td>
        {/if}
        </tr>        
    </tbody>    
</table>
<div class="pad"></div>

<h3>Location</h3>
<table id="t-is" class="list" width="100%">
    <tbody>
        <tr class="top-table" style="height: 25px;">
            <th rowspan="2">Producer</th>
            <th colspan="2" style="border-bottom : 1px solid #B9B9B9;">Supplier Invoice</th>
            <th colspan="3" style="border-bottom : 1px solid #B9B9B9;">Incoming DDT</th>
            <th colspan="3" style="border-bottom : 1px solid #B9B9B9;">Outgoing DDT</th>
            <th rowspan="2">Owner</th>
            <th rowspan="2">Location</th>
        </tr>
        <tr class="top-table" style="height: 25px;">
            <th width="70px">Number</th>
            <th width="70px">Date</th>
            <th width="70px">Number</th>
            <th width="70px">Date</th>
            <th>Company</th>
            <th width="70px">Number</th>
            <th width="70px">Date</th>
            <th>Company</th>
        </tr>
        <tr id="t-is-1">
        {if $item.inuse || $item.parent_id > 0}
            <td>{if isset($item.supplier)}{$item.supplier.title|escape:'html'}{/if}</td>
            <td>{if !empty($item.supplier_invoice_no)}{$item.supplier_invoice_no|escape:'html'}{/if}</td>
            <td>{if !empty($item.supplier_invoice_date)}{$item.supplier_invoice_date|escape:'html'|date_format:'d/m/Y'}{/if}</td>
            <td>{if !empty($item.in_ddt_number)}{$item.in_ddt_number|escape:'html'}{/if}</td>
            <td>{if !empty($item.in_ddt_date)}{$item.in_ddt_date|escape:'html'|date_format:'d/m/Y'}{/if}</td>
            <td>{if isset($item.in_ddt_company)}{$item.in_ddt_company.doc_no|escape:'html'|date_format:'d/m/Y'}{/if}</td>
            <td>{if !empty($item.ddt_number)}{$item.ddt_number|escape:'html'}{/if}</td>
            <td>{if !empty($item.ddt_date)}{$item.ddt_date|escape:'html'|date_format:'d/m/Y'}{/if}</td>
            <td>{if isset($item.ddt_company)}{$item.ddt_company.doc_no|escape:'html'|date_format:'d/m/Y'}{/if}</td>
            <td>{if isset($item.owner)}{$item.owner.title_trade|escape:'html'}{/if}</td>
            <td>{if isset($item.stockholder)}{$item.stockholder.title|escape:'html'}{/if}</td>            
        {else}
            <td>
                <select name="item[supplier_id]" class="max">
                    <option value="0">--</option>
                    {foreach from=$suppliers item=supplier}
                    <option value="{$supplier.company.id}"{if $item.supplier_id == $supplier.company.id} selected="selected"{/if}>{$supplier.company.title|escape:'html'}</option>
                    {/foreach}                        
                </select>
            </td>
            <td><input type="text" name="item[supplier_invoice_no]" value="{if !empty($item.supplier_invoice_no)}{$item.supplier_invoice_no|escape:'html'}{/if}" class="max"></td>
            <td><input class="datepicker" id="supplier_invoice_date-1" type="text" name="item[supplier_invoice_date]" value="{if !empty($item.supplier_invoice_date)}{$item.supplier_invoice_date|escape:'html'|date_format:'d/m/Y'}{/if}" style="width:70px"></td>
            <td><input type="text" name="item[in_ddt_number]" value="{if !empty($item.in_ddt_number)}{$item.in_ddt_number|escape:'html'}{/if}" class="max"></td>
            <td><input class="datepicker" type="text" name="item[in_ddt_date]" value="{if !empty($item.in_ddt_date)}{$item.in_ddt_date|escape:'html'|date_format:'d/m/Y'}{/if}" style="width:70px"></td>
            <td>
                <input type="text" id="in_ddt_company" name="item[in_ddt_company]"{if isset($item.in_ddt_company)} value="{$item.in_ddt_company.doc_no|escape:'html'}"{/if} class="company-autocomplete">
                <input type="hidden" id="in_ddt_company_id" name="item[in_ddt_company_id]"{if isset($item.in_ddt_company_id)} value="{$item.in_ddt_company_id}"{/if}>
            </td>
            <td><input type="text" name="item[ddt_number]" value="{if !empty($item.ddt_number)}{$item.ddt_number|escape:'html'}{/if}" class="max"></td>
            <td><input class="datepicker" type="text" name="item[ddt_date]" value="{if !empty($item.ddt_date)}{$item.ddt_date|escape:'html'|date_format:'d/m/Y'}{/if}" style="width:70px"></td>
            <td>
                <input type="text" id="ddt_company" name="item[ddt_company]"{if isset($item.ddt_company)} value="{$item.ddt_company.doc_no|escape:'html'}"{/if} class="company-autocomplete">
                <input type="hidden" id="ddt_company_id" name="item[ddt_company_id]"{if isset($item.ddt_company_id)} value="{$item.ddt_company_id}"{/if}>
            </td>
            <td>
                <select name="item[owner_id]" class="max">
                    <option value="0">--</option>
                    {foreach from=$owners item=owner}
                    <option value="{$owner.company.id}"{if $item.owner_id == $owner.company.id} selected="selected"{/if}>{$owner.company.title_trade|escape:'html'}</option>
                    {/foreach}                        
                </select>
            </td>
            <td>
                <select name="item[location_id]" class="max">
                    <option value="0">--</option>
                    {foreach from=$locations item=lrow}
                    <option value="{$lrow.company.id}"{if $item.stockholder_id == $lrow.company.id} selected="selected"{/if}>{$lrow.company.doc_no|escape:'html'} ({$lrow.company.stocklocation.title|escape:'html'})</option>
                    {/foreach}
                </select>            
            </td>            
        {/if}
        </tr>
    </tbody>
</table>
<div class="pad"></div>

<h3>Status</h3>
<table id="t-is" class="list" width="100%">
    <tbody>
        <tr class="top-table">
            <th width="10%">Mill</th>
            <th width="10%">System</th>
            <th width="7%">Purchase Price,<br>per {$item.weight_unit|wunit}</th>
            <th width="7%">Curret Cost,<br>{$item.currency_sign}/{$item.weight_unit|wunit}</th>
            <th width="7%">P/L,<br>{$item.currency_sign}/{$item.weight_unit|wunit}</th>
            <th width="7%">Days On Stock</th>
            <th width="10%">Status</th>
            <th width="15%">Load Ready</th>
            <th>Internal Notes</th>
        </tr>
        <tr id="t-is-1">
        {if $item.inuse || $item.parent_id > 0}
            <td>{if !empty($item.mill)}{$item.mill|escape:'html'}{/if}</td>
            <td>{if !empty($item.system)}{$item.system|escape:'html'}{/if}</td>
            <td>{if $item.purchase_price != 0}{$item.purchase_price|escape:'html'}{if !empty($item.purchase_currency)} {$litem.purchase_currency|cursign}{/if}{/if}</td>
            <td>{if $item.current_cost != 0}{$item.current_cost|escape:'html'}{/if}</td>
            <td>{if $item.pl != 0}{$item.pl|escape:'html'}{/if}</td>
            <td>{$item.days_on_stock}</td>
            <td>
                {if empty($item.status_id)}{''|undef}
                {else}{$item.status_title}{/if}
            </td>
            <td>{if !empty($item.load_ready)}{$item.load_ready|escape:'html'}{/if}</td>
            <td>{if !empty($item.internal_notes)}{$item.internal_notes|escape:'html'}{/if}</td>
        {else}
            <td><input type="text" name="item[mill]" value="{if !empty($item.mill)}{$item.mill|escape:'html'}{/if}" class="max"></td>
            <td><input type="text" name="item[system]" value="{if !empty($item.system)}{$item.system|escape:'html'}{/if}" class="max"></td>
            <td nowrap="nowrap">
                <input type="text" name="item[purchase_price]" value="{if $item.purchase_price != 0}{$item.purchase_price|string_format:'%.2f'}{/if}" style="width: 60px;">
                <select name="item[purchase_currency]">
                    <option value=""{if empty($item.purchase_currency)} selected="selected"{/if}>-</option>
                    <option value="usd"{if $item.purchase_currency == 'usd'} selected="selected"{/if}>$</option>
                    <option value="eur"{if $item.purchase_currency == 'eur'} selected="selected"{/if}>&euro;</option>
                    <option value="gbp"{if $item.purchase_currency == 'gbp'} selected="selected"{/if}>&pound;</option>
                </select>
            </td>
            <td><input type="text" name="item[current_cost]" value="{if $item.current_cost != 0}{$item.current_cost|escape:'html'}{/if}" class="max"></td>
            <td><input type="text" name="item[pl]" value="{if $item.pl != 0}{$item.pl|escape:'html'}{/if}" class="max"></td>
            <td>{$item.days_on_stock}</td>
            <td>
                <select name="item[status_id]" class="max">
                    {if empty($item.order_id)}
                    <option value="{$smarty.const.ITEM_STATUS_PRODUCTION}"{if $item.status_id == $smarty.const.ITEM_STATUS_PRODUCTION} selected="selected"{/if}>In Production</option>
                    <option value="{$smarty.const.ITEM_STATUS_TRANSFER}"{if $item.status_id == $smarty.const.ITEM_STATUS_TRANSFER} selected="selected"{/if}>Transfer To Stock</option>
                    <option value="{$smarty.const.ITEM_STATUS_STOCK}"{if $item.status_id == $smarty.const.ITEM_STATUS_STOCK} selected="selected"{/if}>On Stock</option>
                    {else}
                    <option value="{$smarty.const.ITEM_STATUS_ORDERED}"{if $item.status_id == $smarty.const.ITEM_STATUS_ORDERED} selected="selected"{/if}>Ordered</option>
                    <option value="{$smarty.const.ITEM_STATUS_RELEASED}"{if $item.status_id == $smarty.const.ITEM_STATUS_RELEASED} selected="selected"{/if}>Released</option>
                    <option value="{$smarty.const.ITEM_STATUS_DELIVERED}"{if $item.status_id == $smarty.const.ITEM_STATUS_DELIVERED} selected="selected"{/if}>Delivered</option>
                    <option value="{$smarty.const.ITEM_STATUS_INVOICED}"{if $item.status_id == $smarty.const.ITEM_STATUS_INVOICED} selected="selected"{/if}>Invoiced</option>
                    {/if}
                </select>
            </td>
            <td><input type="text" name="item[load_ready]" value="{if !empty($item.load_ready)}{$item.load_ready|escape:'html'}{/if}" class="max"></td>
            <td><input type="text" name="item[internal_notes]" value="{if !empty($item.internal_notes)}{$item.internal_notes|escape:'html'}{/if}" class="max"></td>
        {/if}
        </tr>
    </tbody>
</table>
<div class="pad"></div>

<h3>Chemical Analysis</h3>
<table id="t-ic" class="list" width="100%">
    <tbody>
        <tr class="top-table">
            <th>Heat / Lot</th>
            <th>%C</th>
            <th>%Si</th>
            <th>%Mn</th>
            <th>%P</th>
            <th>%S</th>
            <th>%Cr</th>
            <th>%Ni</th>
            <th>%Cu</th>
            <th>%Al</th>
            <th>%Mo</th>
            <th>%Nb</th>
            <th>%V</th>
            <th>%N</th>
            <th>%Ti</th>
            <th>%Sn</th>
            <th>%B</th>
            <th>CEQ</th>
        </tr>
        <tr id="t-ic-1">
        {if $item.inuse || $item.parent_id > 0}
            <td>{if !empty($item.properties.heat_lot)}{$item.properties.heat_lot|escape:'html'}{/if}</td>
            <td>{if $item.properties.c != 0}{$item.properties.c|escape:'html'}{/if}</td>
            <td>{if $item.properties.si != 0}{$item.properties.si|escape:'html'}{/if}</td>
            <td>{if $item.properties.mn != 0}{$item.properties.mn|escape:'html'}{/if}</td>
            <td>{if $item.properties.p != 0}{$item.properties.p|escape:'html'}{/if}</td>
            <td>{if $item.properties.s != 0}{$item.properties.s|escape:'html'}{/if}</td>
            <td>{if $item.properties.cr != 0}{$item.properties.cr|escape:'html'}{/if}</td>
            <td>{if $item.properties.ni != 0}{$item.properties.ni|escape:'html'}{/if}</td>
            <td>{if $item.properties.cu != 0}{$item.properties.cu|escape:'html'}{/if}</td>
            <td>{if $item.properties.al != 0}{$item.properties.al|escape:'html'}{/if}</td>
            <td>{if $item.properties.mo != 0}{$item.properties.mo|escape:'html'}{/if}</td>
            <td>{if $item.properties.nb != 0}{$item.properties.nb|escape:'html'}{/if}</td>
            <td>{if $item.properties.v != 0}{$item.properties.v|escape:'html'}{/if}</td>
            <td>{if $item.properties.n != 0}{$item.properties.n|escape:'html'}{/if}</td>
            <td>{if $item.properties.ti != 0}{$item.properties.ti|escape:'html'}{/if}</td>
            <td>{if $item.properties.sn != 0}{$item.properties.sn|escape:'html'}{/if}</td>
            <td>{if $item.properties.b != 0}{$item.properties.b|escape:'html'}{/if}</td>
            <td>{if $item.properties.ceq != 0}{$item.properties.ceq|escape:'html'}{/if}</td>
        {else}                
            <td><input type="text" name="properties[heat_lot]" value="{if !empty($item.properties.heat_lot)}{$item.properties.heat_lot|escape:'html'}{/if}" class="max"></td>
            <td><input type="text" name="properties[c]" value="{if $item.properties.c != 0}{$item.properties.c|escape:'html'}{/if}" class="max"></td>
            <td><input type="text" name="properties[si]" value="{if $item.properties.si != 0}{$item.properties.si|escape:'html'}{/if}" class="max"></td>
            <td><input type="text" name="properties[mn]" value="{if $item.properties.mn != 0}{$item.properties.mn|escape:'html'}{/if}" class="max"></td>
            <td><input type="text" name="properties[p]" value="{if $item.properties.p != 0}{$item.properties.p|escape:'html'}{/if}" class="max"></td>
            <td><input type="text" name="properties[s]" value="{if $item.properties.s != 0}{$item.properties.s|escape:'html'}{/if}" class="max"></td>
            <td><input type="text" name="properties[cr]" value="{if $item.properties.cr != 0}{$item.properties.cr|escape:'html'}{/if}" class="max"></td>
            <td><input type="text" name="properties[ni]" value="{if $item.properties.ni != 0}{$item.properties.ni|escape:'html'}{/if}" class="max"></td>
            <td><input type="text" name="properties[cu]" value="{if $item.properties.cu != 0}{$item.properties.cu|escape:'html'}{/if}" class="max"></td>
            <td><input type="text" name="properties[al]" value="{if $item.properties.al != 0}{$item.properties.al|escape:'html'}{/if}" class="max"></td>
            <td><input type="text" name="properties[mo]" value="{if $item.properties.mo != 0}{$item.properties.mo|escape:'html'}{/if}" class="max"></td>
            <td><input type="text" name="properties[nb]" value="{if $item.properties.nb != 0}{$item.properties.nb|escape:'html'}{/if}" class="max"></td>
            <td><input type="text" name="properties[v]" value="{if $item.properties.v != 0}{$item.properties.v|escape:'html'}{/if}" class="max"></td>
            <td><input type="text" name="properties[n]" value="{if $item.properties.n != 0}{$item.properties.n|escape:'html'}{/if}" class="max"></td>
            <td><input type="text" name="properties[ti]" value="{if $item.properties.ti != 0}{$item.properties.ti|escape:'html'}{/if}" class="max"></td>
            <td><input type="text" name="properties[sn]" value="{if $item.properties.sn != 0}{$item.properties.sn|escape:'html'}{/if}" class="max"></td>
            <td><input type="text" name="properties[b]" value="{if $item.properties.b != 0}{$item.properties.b|escape:'html'}{/if}" class="max"></td>
            <td><input type="text" name="properties[ceq]" value="{if $item.properties.ceq != 0}{$item.properties.ceq|escape:'html'}{/if}" class="max"></td>
        {/if}
        </tr>
    </tbody>
</table>
<div class="pad"></div>

<h3>Mechanical Properties</h3>
<table id="t-im" class="list" width="100%">
    <tbody>
        <tr class="top-table" style="height: 25%;">
            <th colspan="4" style="border-bottom : 1px solid #B9B9B9;">Tensile</th>
            <th rowspan="2">{*Reduction Of Area*} Z-test, %</th>
            <th colspan="3" style="border-bottom : 1px solid #B9B9B9;">Impact</th>
            <th rowspan="2">Hardness<br>HD</th>
            <th rowspan="2">UST</th>
            <th rowspan="2">Stress Relieving Temp<br>deg. C</th>
            <th rowspan="2">Heating Rate Per Hour<br>deg. C</th>
            <th rowspan="2">Holding Time<br>Hours</th>
            <th rowspan="2">Cooling Down Rate Per Hour<br>deg. C</th>
            <th rowspan="2">Normalizing Temp<br>deg. C</th>
            <th rowspan="2">Condition</th>
        </tr>
        <tr class="top-table" style="height: 25%;">
            <th>Sample Direction<br>N/mm<sup>2</sup></th>            
            <th>Strength<br>N/mm<sup>2</sup></th>
            <th>Yield Point<br>N/mm<sup>2</sup></th>
            <th>Elongation<br>%</th>
            <th>Sample Direction</th>
            <th>Strength<br>J/cm<sup>2</sup></th>
            <th>Test Temp<br>deg. C</th>
        </tr>
        <tr id="t-im-1">
        {if $item.inuse || $item.parent_id > 0}
            <td>{if !empty($item.properties.tensile_sample_direction)}{$item.properties.tensile_sample_direction|escape:'html'}{/if}</td>            
            <td>{if !empty($item.properties.tensile_strength)}{$item.properties.tensile_strength|escape:'html'}{/if}</td>
            <td>{if !empty($item.properties.yeild_point)}{$item.properties.yeild_point|escape:'html'}{/if}</td>
            <td>{if $item.properties.elongation != 0}{$item.properties.elongation|escape:'html'}{/if}</td>
            <td>{if $item.properties.reduction_of_area != 0}{$item.properties.reduction_of_area|escape:'html'}{/if}</td>
            <td>{if !empty($item.properties.sample_direction)}{$item.properties.sample_direction|escape:'html'}{/if}</td>
            <td>{if !empty($item.properties.impact_strength)}{$item.properties.impact_strength|escape:'html'}{/if}</td>
            <td>{if !empty($item.properties.test_temp)}{$item.properties.test_temp|escape:'html'}{/if}</td>
            <td>{if !empty($item.properties.hardness)}{$item.properties.hardness|escape:'html'}{/if}</td>
            <td>{if !empty($item.properties.ust)}{$item.properties.ust|escape:'html'}{/if}</td>
            <td>{if !empty($item.properties.stress_relieving_temp)}{$item.properties.stress_relieving_temp|escape:'html'}{/if}</td>
            <td>{if !empty($item.properties.heating_rate_per_hour)}{$item.properties.heating_rate_per_hour|escape:'html'}{/if}</td>
            <td>{if !empty($item.properties.holding_time)}{$item.properties.holding_time|escape:'html'}{/if}</td>
            <td>{if !empty($item.properties.cooling_down_rate)}{$item.properties.cooling_down_rate|escape:'html'}{/if}</td>
            <td>{if !empty($item.properties.normalizing_temp)}{$item.properties.normalizing_temp|escape:'html'}{/if}</td>
            <td>
                {if $item.properties.condition == 'ar'}As Rolled
                {elseif $item.properties.condition == 'n'}Normalized
                {elseif $item.properties.condition == 'nr'}Normalizing Rolling{/if}
            </td>
        {else}                
            <td><input type="text" name="properties[tensile_sample_direction]" value="{if !empty($item.properties.tensile_sample_direction)}{$item.properties.tensile_sample_direction|escape:'html'}{/if}" class="max"></td>
            <td><input type="text" name="properties[tensile_strength]" value="{if !empty($item.properties.tensile_strength)}{$item.properties.tensile_strength|escape:'html'}{/if}" class="max"></td>
            <td><input type="text" name="properties[yeild_point]" value="{if !empty($item.properties.yeild_point)}{$item.properties.yeild_point|escape:'html'}{/if}" class="max"></td>
            <td><input type="text" name="properties[elongation]" value="{if $item.properties.elongation != 0}{$item.properties.elongation|escape:'html'}{/if}" class="max"></td>
            <td><input type="text" name="properties[reduction_of_area]" value="{if $item.properties.reduction_of_area != 0}{$item.properties.reduction_of_area|escape:'html'}{/if}" class="max"></td>
            <td><input type="text" name="properties[sample_direction]" value="{if !empty($item.properties.sample_direction)}{$item.properties.sample_direction|escape:'html'}{/if}" class="max"></td>
            <td><input type="text" name="properties[impact_strength]" value="{if !empty($item.properties.impact_strength)}{$item.properties.impact_strength|escape:'html'}{/if}" class="max"></td>
            <td><input type="text" name="properties[test_temp]" value="{if !empty($item.properties.test_temp)}{$item.properties.test_temp|escape:'html'}{/if}" class="max"></td>
            <td><input type="text" name="properties[hardness]" value="{if !empty($item.properties.hardness)}{$item.properties.hardness|escape:'html'}{/if}" class="max"></td>
            <td><input type="text" name="properties[ust]" value="{if !empty($item.properties.ust)}{$item.properties.ust|escape:'html'}{/if}" class="max"></td>
            <td><input type="text" name="properties[stress_relieving_temp]" value="{if !empty($item.properties.stress_relieving_temp)}{$item.properties.stress_relieving_temp|escape:'html'}{/if}" class="max"></td>
            <td><input type="text" name="properties[heating_rate_per_hour]" value="{if !empty($item.properties.heating_rate_per_hour)}{$item.properties.heating_rate_per_hour|escape:'html'}{/if}" class="max"></td>
            <td><input type="text" name="properties[holding_time]" value="{if !empty($item.properties.holding_time)}{$item.properties.holding_time|escape:'html'}{/if}" class="max"></td>
            <td><input type="text" name="properties[cooling_down_rate]" value="{if !empty($item.properties.cooling_down_rate)}{$item.properties.cooling_down_rate|escape:'html'}{/if}" class="max"></td>
            <td><input type="text" name="properties[normalizing_temp]" value="{if !empty($item.properties.normalizing_temp)}{$item.properties.normalizing_temp|escape:'html'}{/if}" class="max"></td>
            <td>
                <select name="properties[condition]" class="max">
                    <option value="">--</option>
                    <option value="ar"{if $item.properties.condition == 'ar'} selected="selected"{/if}>As Rolled</option>
                    <option value="n"{if $item.properties.condition == 'n'} selected="selected"{/if}>Normalized</option>
                    <option value="nr"{if $item.properties.condition == 'nr'} selected="selected"{/if}>Normalizing Rolling</option>                    
                </select>
            </td>
        {/if}
        </tr>
    </tbody>
</table>
