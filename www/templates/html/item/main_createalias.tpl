<table width="100%">
    <tr>
        <td width="30%">
            <table class="form" width="100%">
                <tr>
                    <td class="form-td-title-b">Target Stock : </td>
                    <td>
                        <select id="stocks" name="stock_id" class="normal" onchange="bind_stock_params(this.value, false); itemalias_resetparams(this.value);">
                            <option value="0"{if !isset($stock)} selected="selected"{/if}>--</option>
                            {foreach from=$stocks item=row}
                            <option value="{$row.stock.id}"{if isset($stock) && $stock.id == $row.stock.id} selected="selected"{/if}>{$row.stock.title|escape:'html'}</option>
                            {/foreach}
                        </select>
                        <input type="hidden" id="dimension_unit" value="{if isset($stock)}{$stock.dimension_unit}{/if}">
                        <input type="hidden" id="weight_unit" value="{if isset($stock)}{$stock.weight_unit}{/if}">
                        <input type="hidden" id="default_stock_id" value="{if isset($stock)}{$stock.id}{/if}">
                    </td>
                </tr>
                <tr>
                    <td class="form-td-title-b">Location : </td>
                    <td>
                        <select id="locations" name="stockholder_id" class="normal" onchange="itemalias_resetallpositions();">
                            <option value="0">--</option>
                            {foreach from=$locations item=row}
                            {if $row.company.location_id > 0}<option value="{$row.company.id}"{if $row.company.id == $stockholder_id} selected="selected"{/if}>{$row.company.doc_no} ({$row.company.stocklocation.title})</option>{/if}
                            {/foreach}
                        </select>
                    </td>        
                </tr>
            </table>
        </td>
        <td>
            <div style="position: absolute;">
                <img src="/img/layout/gnome.jpg" style="position: absolute; top: 0; left: 0;">
            </div>
            <div class="bubble" style="margin-left: 55px; width: 520px; line-height: 14px;" id="gnome_text">
                Please change specification according to your needs (how you want alias(es) to be created) .
                <br>Auto Assign means autosearch of appropriate positions or creation of new ones .
            </div>            
        </td>
    </tr>    
</table>
<div class="pad"></div>

<table class="list" width="100%">
    <tbody>
        <tr class="top-table">
            <th width="3%">Id</th>
            <th width="8%">Steel Grade</th>
            <th width="3%" class="text-center">Thickness<span class="lbl-dim">{if isset($stock)},<br>{$stock.dimension_unit}{/if}</span></th>
            <th width="5%" class="text-center">Width<span class="lbl-dim">{if isset($stock)},<br>{$stock.dimension_unit}{/if}</span></th>
            <th width="5%" class="text-center">Length<span class="lbl-dim">{if isset($stock)},<br>{$stock.dimension_unit}{/if}</span></th>
            <th width="7%" class="text-center">Unit Weight<span class="lbl-wgh">{if isset($stock)},<br>{$stock.weight_unit|wunit}{/if}</span></th>
            <th width="7%" class="text-center">Price<span class="lbl-price">{if isset($stock)},<br>{$stock.currency_sign}/{$stock.price_unit|wunit}{/if}</span></th>
            <th width="8%">Delivery Time</th>
            <th width="15%">Notes</th>
            <th width="15%">Internal Notes</th>
{*            <th width="8%">Biz</th>   *}
            <th>Position</th>
        </tr>    
        {foreach from=$items item=row}
        <tr>
            <td>
                {$row.id}
                <input type="hidden" id="qtty-{$row.id}" name="items[{$row.id}][qtty]" value="1" class="max">
                <input type="hidden" name="items[{$row.id}][id]" value="{$row.id}" class="max item_id">
            </td>
            <td>
                <select id="steelgrade-{$row.id}" name="items[{$row.id}][steelgrade_id]" class="max" onchange="itemalias_resetpositions({$row.id});">
                    <option value="0">--</option>
                    {foreach from=$steelgrades item=sg}
                    <option value="{$sg.steelgrade.id}"{if $sg.steelgrade.id == $row.steelgrade_id} selected="selected"{/if}>{$sg.steelgrade.title|escape:'html'}</option>
                    {/foreach}                    
                </select>
            </td>
            <td>
                <input type="text" id="thickness-{$row.id}" name="items[{$row.id}][thickness]" value="{$row.thickness}" class="max" onkeyup="calc_unitweight({$row.id}); itemalias_resetpositions({$row.id});">
                <input type="hidden" id="default-thickness-{$row.id}" value="{$row.thickness}">
            </td>
            <td>
                <input type="text" id="width-{$row.id}" name="items[{$row.id}][width]" value="{$row.width}" class="max" onkeyup="calc_unitweight({$row.id}); itemalias_resetpositions({$row.id});">
                <input type="hidden" id="default-width-{$row.id}" value="{$row.width}">
            </td>
            <td>
                <input type="text" id="length-{$row.id}" name="items[{$row.id}][length]" value="{$row.length}" class="max" onkeyup="calc_unitweight({$row.id}); itemalias_resetpositions({$row.id});">
                <input type="hidden" id="default-length-{$row.id}" value="{$row.length}">
            </td>
            <td>
                <input type="text" id="unitweight-{$row.id}" name="items[{$row.id}][unitweight]" value="{$row.unitweight}" class="max">
                <input type="hidden" id="default-unitweight-{$row.id}" value="{$row.unitweight}">
            </td>
            <td>
                <input type="text" id="price-{$row.id}" name="items[{$row.id}][price]" value="{$row.price|string_format:'%.2f'}" class="max">
                <input type="hidden" id="default-price-{$row.id}" value="{$row.price|string_format:'%.2f'}">
            </td>
            <td>
                <input type="text" id="delivery_time-{$row.id}" name="items[{$row.id}][delivery_time]" value="{$row.delivery_time}" class="max">
                <input type="hidden" id="default-delivery_time-{$row.id}" value="{$row.delivery_time}">
            </td>
            <td><input type="text" name="items[{$row.id}][notes]" value="{$row.notes}" class="max"></td>
            <td><input type="text" name="items[{$row.id}][internal_notes]" value="{$row.internal_notes}" class="max"></td>
{*  
            <td>
                <input type="text" id="biz-{$row.id}" name="items[{$row.id}][biz_title]" value="{$row.biz_title}" class="biz-autocomplete max">
                <input type="hidden" id="biz-{$row.id}-id" name="items[{$row.id}][biz_id]" value="{$row.biz_id}" >
            </td>
*}            
            <td id='td-position-{$row.id}'>
                <select id="position-{$row.id}" name="items[{$row.id}][position_id]" class="max">
                    <option value="0"{if empty($row.position_id)} selected="selected"{/if}>Auto Assign</option>
                    {if isset($row.positions)}
                        {foreach from=$row.positions item=pos}
                            <option value="0"{if $row.position_id == $pos.steelposition.id} selected="selected"{/if}>{$pos.steelposition.id} : {$pos.steelposition.steelgrade.title} {$pos.steelposition.thickness} x {$pos.steelposition.width} x {$pos.steelposition.length} {$pos.steelposition.deliverytime.title}</option>
                        {/foreach}
                    {/if}
                </select>
                <a href="javascript: void(0);" id="a-position-{$row.id}" onclick="itemalias_fillpositions({$row.id});" class="find" style="display: none;">find suitable positions</a>
                <span id="span-position-{$row.id}" style="display: none;">loading data ...</span>
            </td>
        </tr>
        {/foreach}
    </tbody>
</table>