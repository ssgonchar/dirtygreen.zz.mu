<table width="100%">
    <tr>
        <td width="30%" class="text-top">
            <table class="form" width="100%">
                <tr>
                    <td class="text-right" width="120px" style="font-weight : bold;">Stock :</td>
                    <td>
                        <select id="stock" name="form[stock_id]" class="normal" onchange="bind_positions_filter();">
                            <option value="0"{if empty($stock_id)} selected="selected"{/if}>--</option>
                            {foreach from=$stocks item=row}
                            <option value="{$row.stock.id}"{if !empty($stock_id) && $stock_id == $row.stock.id} selected="selected"{/if}>{$row.stock.title|escape:'html'}</option>
                            {/foreach}
                        </select>        
                    </td>                    
                </tr>
            </table>
        </td>
        <td width="60%" class="text-top">
            <table class="form" width="100%">
                <tr height="32">
                    <td class="form-td-title">Location :</td>
                    <td id="locations">
                        {if !empty($locations)}
                        
                        {foreach from=$locations item=row}
                      
                        <label for="cb-location-{$row.location_id}"><input type="checkbox" id="cb-location-{$row.location_id}" name="form[location][{$row.location_id}]" value="{$row.location_id}"{if isset($row.selected)} checked="checked"{/if}>&nbsp;{$row.location.title|escape:'html'}&nbsp;&nbsp;&nbsp;</label>
                     
                        {/foreach}
                        <!--
                       <select data-placeholder="Click to select locations" multiple="" class="chosen-select" tabindex="-1" style="">
                        {foreach from=$locations item=row}
                        <option id="cb-location-{$row.location_id}" name="form[location][{$row.location_id}]" value="{$row.location_id}">{$row.location.title}</option>
                        {/foreach}
                        
          </select>-->
                        
                        {else}
                        <span style="color: #aaa;">{if empty($stock_id)}Please select stock first{else}none{/if}</span>
                        {/if}                            
                    </td>
                </tr>
                <tr height="32">
                    <td class="form-td-title">Delivery Time :</td>
                    <td id="deliverytimes">
                        {if !empty($deliverytimes)}
                        {foreach from=$deliverytimes item=row}
                        <label for="cb-deliverytime-{$row.deliverytime_id}"><input type="checkbox" id="cb-deliverytime-{$row.deliverytime_id}" name="form[deliverytime][{$row.deliverytime_id}]" value="{$row.deliverytime_id}"{if isset($row.selected)} checked="checked"{/if}>&nbsp;{$row.deliverytime.title|escape:'html'}&nbsp;&nbsp;&nbsp;</label>
                        {/foreach}
                        {else}
                        <span style="color: #aaa;">{if empty($stock_id)}Please select stock first{else}none{/if}</span>
                        {/if}                            
                    </td>
                </tr>
            </table>
        </td>
        <td width="10%" class="text-right text-middle" style="padding-right: 0;">
            <input type="submit" name="btn_setfilter" value="Select" class="btn100b">
        </td>
    </tr>
</table>

<div class="pad"></div>
<table width="100%" class="form">
    <tr>
        <td class="text-right" width="120px">Steel Grade :</td>
        <td>
            <select id="steelgrades" style="width:100%" name="form[steelgrade]">
                <option value=0>--</option>
                {if isset($steelgrade_list) && !empty($steelgrade_list)}
                    {foreach from=$steelgrade_list item=row}
                        <option value="{$row.steelgrade.id}"{if isset($steelgrade_id) && $steelgrade_id == $row.steelgrade.id}selected=selected{/if}><font color={$row.bgcolor}>{$row.steelgrade.title|escape:'html'}</font></option>
                    {/foreach}
                {/if}  
            </select>
        </td>
        <td class="text-right" width="120px">Thickness exact :</td>
        <td>
            <nobr><input type="text" value="{if isset($thickness) && $thickness > 0}{$thickness}{/if}" name="form[thickness]" style="width: 100%;"><span class="size">{if isset($stock)}{$stock.dimension_unit|dunit}{/if}</span></nobr>
        </td>
        <td class="text-right" width="120px">Width exact :</td>
        <td>
            <nobr><input type="text" value="{if isset($width) && $width > 0}{$width}{/if}" style="width: 100%;" name="form[width]"><span class="size">{if isset($stock)}{$stock.dimension_unit|dunit}{/if}</span></nobr>
        </td>
        <td class="text-right" width="120px">Length exact :</td>
        <td>
            <nobr><input type="text" value="{if isset($length) && $length > 0}{$length}{/if}" style="width: 100%;" name="form[length]"><span class="size">{if isset($stock)}{$stock.dimension_unit|dunit}{/if}</span></nobr>
        </td>  
        <td class="text-right" width="120px">Weight exact :</td>
        <td>
            <nobr><input type="text" value="{if isset($weight) && $weight > 0}{$weight}{/if}" style="width: 100%;" name="form[weight]"><span class="weight">{if isset($stock)}{$stock.weight_unit|wunit}{/if}</span></nobr> 
        </td>     
    </tr>
    <tr>
        <td></td>
        <td></td>
        <td class="text-right">min :</td>
        <td>
            <nobr><input type="text" value="{if isset($thicknessmin) && $thicknessmin > 0}{$thicknessmin}{/if}" style="width: 100%;" name="form[thicknessmin]"><span class="size">{if isset($stock)}{$stock.dimension_unit|dunit}{/if}</span></nobr>
        </td>
        <td class="text-right">min :</td>
        <td>
            <nobr><input type="text" value="{if isset($widthmin) && $widthmin > 0}{$widthmin}{/if}" style="width: 100%;" name="form[widthmin]"><span class="size">{if isset($stock)}{$stock.dimension_unit|dunit}{/if}</span></nobr>
        </td>
        <td class="text-right">min :</td>
        <td>
            <nobr><input type="text" value="{if isset($lengthmin) && $lengthmin > 0}{$lengthmin}{/if}" style="width: 100%;" name="form[lengthmin]"><span class="size">{if isset($stock)}{$stock.dimension_unit|dunit}{/if}</span></nobr>
        </td>
        <td class="text-right">min :</td>
        <td>
            <nobr><input type="text" value="{if isset($weightmin) && $weightmin > 0}{$weightmin}{/if}" style="width: 100%;" name="form[weightmin]"><span class="weight">{if isset($stock)}{$stock.weight_unit|wunit}{/if}</span></nobr>
        </td>
    </tr>
    <tr>
        <td class="text-right">Keyword @ Notes :</td>
        <td>
            <nobr><input type="text" value="{if isset($keyword) && !empty($keyword)}{$keyword|escape:'html'}{/if}" style="width: 100%;" name="form[keyword]">
        </td>
        <td class="text-right">max :</td>
        <td>
            <nobr><input type="text" value="{if isset($thicknessmax) && $thicknessmax > 0}{$thicknessmax}{/if}" style="width: 100%;" name="form[thicknessmax]"><span class="size">{if isset($stock)}{$stock.dimension_unit|dunit}{/if}</span></nobr>
        </td>
        <td class="text-right">max :</td>
        <td>
            <nobr><input type="text" value="{if isset($widthmax) && $widthmax > 0}{$widthmax}{/if}" style="width: 100%;" name="form[widthmax]"><span class="size">{if isset($stock)}{$stock.dimension_unit|dunit}{/if}</span></nobr>
        </td>
        <td class="text-right">max :</td>
        <td>
            <nobr><input type="text" value="{if isset($lengthmax) && $lengthmax > 0}{$lengthmax}{/if}" style="width: 100%;" name="form[lengthmax]"><span class="size">{if isset($stock)}{$stock.dimension_unit|dunit}{/if}</span></nobr>
        </td>    
        <td class="text-right">max :</td>
        <td>
            <nobr><input type="text" value="{if isset($weightmax) && $weightmax > 0}{$weightmax}{/if}" style="width: 100%;" name="form[weightmax]"><span class="weight">{if isset($stock)}{$stock.weight_unit|wunit}{/if}</span></nobr>
        </td>
    </tr>
</table>    
{*
<a id="a-show-params" href="javascript: void(0);" class="opendown" onclick="show_more_params();"{if isset($params)} style="display:none"{/if}>More Params</a>
<div id="more-params" {if !isset($params)} style="display:none"{/if}>
    <table width="100%">
        <tr>
            <td width="30%" class="text-top">
                <table class="form" width="100%">
                    <tr>
                        <td class="form-td-title">Thickness :</td>
                        <td><input type="text" name="form[thickness]" class="narrow"{if isset($thickness)} value="{$thickness}"{/if}></td>                    
                    </tr>
                    <tr>
                        <td class="form-td-title">Width :</td>
                        <td><input type="text" name="form[width]" class="narrow"{if isset($width)} value="{$width}"{/if}></td>                    
                    </tr>
                    <tr>
                        <td class="form-td-title">Length :</td>
                        <td><input type="text" name="form[length]" class="narrow"{if isset($length)} value="{$length}"{/if}></td>                    
                    </tr>                
                </table>
            </td>
            <td width="30%" class="text-top">
                <table class="form" width="100%">
                    <tr>
                        <td class="form-td-title">Weight :</td>
                        <td><input type="text" name="form[weight]" class="narrow"{if isset($weight)} value="{$weight}"{/if}></td>
                    </tr>
                    <tr>
                        <td class="form-td-title">Notes :</td>
                        <td><input type="text" name="form[notes]" class="narrow"{if isset($notes)} value="{$notes}"{/if}></td>
                    </tr>
                </table>
            </td>
            <td width="30%" class="text-top">
{*                
                <table class="form" width="100%">
                    <tr>
                        <td class="form-td-title">Revision Date :</td>
                        <td>
                            <input class="datepicker" type="text" id="rev_date" name="form[rev_date]" value="{if !empty($rev_date)}{$rev_date|escape:'html'}{/if}" style="width: 100px;" onchange="$('#tr-revision').addClass('revision');">
                            {if isset($rev_date)}
                            <a href="javascript: void(0);" onclick="clear_revision();" style="margin-left: 10px;">Clear</a>
                            {/if}                    
                        </td>
                    </tr>
                    <tr>
                        <td class="form-td-title">Revision Time :</td>
                        <td>
                            <select name="form[rev_time]" id="rev_time" class="narrow">
                                <option value="00:00"{if !empty($rev_time) && $rev_time == '00:00'} selected="selected"{/if}></option>
                                <option value="03:00"{if !empty($rev_time) && $rev_time == '03:00'} selected="selected"{/if}>03:00</option>
                                <option value="06:00"{if !empty($rev_time) && $rev_time == '06:00'} selected="selected"{/if}>06:00</option>
                                <option value="09:00"{if !empty($rev_time) && $rev_time == '09:00'} selected="selected"{/if}>09:00</option>
                                <option value="12:00"{if !empty($rev_time) && $rev_time == '12:00'} selected="selected"{/if}>12:00</option>
                                <option value="15:00"{if !empty($rev_time) && $rev_time == '15:00'} selected="selected"{/if}>15:00</option>
                                <option value="18:00"{if !empty($rev_time) && $rev_time == '18:00'} selected="selected"{/if}>18:00</option>
                                <option value="21:00"{if !empty($rev_time) && $rev_time == '21:00'} selected="selected"{/if}>21:00</option>
                                <option value="23:59"{if !empty($rev_time) && $rev_time == '23:59'} selected="selected"{/if}>24:00</option>
                            </select>                    
                        </td>
                    </tr>
                </table>
               
            </td>
            <td width="10%" class="text-right text-middle" style="padding-right: 0;">
            </td>
        </tr>
    </table>
    
    <a id="a-show-params" href="javascript: void(0);" class="closeup" onclick="hide_more_params();">Hide Params</a>
</div>
*}
<div class="pad1"></div>
<hr style="width: 100%; color: #dedede;" size="1"/>
<div class="pad1"></div>

{if empty($list)}
    {if isset($filter)}Nothing was found on my request{/if}
{else}
<table class="list" width="100%">
    <thead>
        <tr class="top-table">
            <th width="3%">Select All<br/><input class="chb" type="checkbox" disabled="disabled" onchange="check_all(this, 'position'); calc_selected(); show_group_actions();" style="margin-left: 2px;"></th>
            <th width="5%">Pos ID</th>
            <th width="8%">Steel Grade</th>
            <th width="5%">Thickness<br>{if isset($stock)}{$stock.dimension_unit|dunit}{/if}</th>
            <th width="5%">Width<br>{if isset($stock)}{$stock.dimension_unit|dunit}{/if}</th>
            <th width="5%">Length<br>{if isset($stock)}{$stock.dimension_unit|dunit}{/if}</th>
            <th width="7%">Unit Weight<br>{if isset($stock)}{$stock.weight_unit|wunit}{/if}</th>
            <th width="5%">Qtty<br>pcs</th>
            <th width="7%">Weight<br>{if isset($stock)}{$stock.weight_unit|wunit}{/if}</th>
            <th width="7%">Price<br>{if isset($stock) && isset($stock.price_unit) && isset($stock.currency_sign)}{$stock.currency_sign}/{$stock.price_unit|wunit}{/if}</th>
            <th width="7%">Value<br>{if isset($stock)}{$stock.currency_sign}{/if}</th>
            <th width="8%">Delivery Time</th>
            <th>Notes</th>
            <th>Internal Notes</th>
            <th>Plate ID</th>
            <th>Location</th>
            <th width="5%">Biz</th>
        </tr>
    </thead>
    <tbody>
        {foreach from=$list item=row}
        <tr id="position-{$row.steelposition_id}">
            <td width="3%" {if !empty($row.steelposition.bgcolor)} style="background-color: {$row.steelposition.bgcolor};"{/if}>
                <input type="checkbox" disabled="disabled" value="{$row.steelposition_id}" class="cb-row-position chb" onchange="calc_selected(); show_group_actions();">
            </td>
            <td width="5%" onclick="position_click(event, {$row.steelposition_id}, {$is_revision});"{if !empty($row.steelposition.bgcolor)} style="background-color: {$row.steelposition.bgcolor};"{/if}>
                {$row.steelposition_id}
            </td>
            <td width="8%" onclick="position_click(event, {$row.steelposition_id}, {$is_revision});"{if !empty($row.steelposition.bgcolor)} style="background-color: {$row.steelposition.bgcolor};"{/if} class="pos">{$row.steelposition.steelgrade.title}</td>
            <td width="5%" onclick="position_click(event, {$row.steelposition_id}, {$is_revision});"{if !empty($row.steelposition.bgcolor)} style="background-color: {$row.steelposition.bgcolor};"{/if}>{$row.steelposition.thickness|escape:'html'}</td>
            <td width="5%" onclick="position_click(event, {$row.steelposition_id}, {$is_revision});"{if !empty($row.steelposition.bgcolor)} style="background-color: {$row.steelposition.bgcolor};"{/if}>{$row.steelposition.width|escape:'html'}</td>
            <td width="5%" onclick="position_click(event, {$row.steelposition_id}, {$is_revision});"{if !empty($row.steelposition.bgcolor)} style="background-color: {$row.steelposition.bgcolor};"{/if}>{$row.steelposition.length|escape:'html'}</td>
            <td width="7%" onclick="position_click(event, {$row.steelposition_id}, {$is_revision});" id="position-unitweight-{$row.steelposition_id}"{if !empty($row.steelposition.bgcolor)} style="background-color: {$row.steelposition.bgcolor};"{/if}>{if $row.steelposition.weight_unit == 'lb'}{$row.steelposition.unitweight|escape:'html'|string_format:'%d'|wunit}{else}{$row.steelposition.unitweight|escape:'html'|string_format:'%.2f'|wunit}{/if}</td>
            <td width="5%" onclick="position_click(event, {$row.steelposition_id}, {$is_revision});"{if !empty($row.steelposition.bgcolor)} style="background-color: {$row.steelposition.bgcolor};"{/if}><span id="position-qtty-{$row.steelposition_id}">{$row.steelposition.qtty|escape:'html'|string_format:'%d'}</span>{if isset($row.steelposition.quick) && !empty($row.steelposition.quick.reserved)} (<a href="/positions/reserved">{$row.steelposition.quick.reserved}</a>){/if}</td>
            <td width="7%" onclick="position_click(event, {$row.steelposition_id}, {$is_revision});" id="position-weight-{$row.steelposition_id}"{if !empty($row.steelposition.bgcolor)} style="background-color: {$row.steelposition.bgcolor};"{/if}>{$row.steelposition.weight|escape:'html'|string_format:'%.2f'}</td>
            <td width="7%" onclick="position_click(event, {$row.steelposition_id}, {$is_revision});" id="position-price-{$row.steelposition_id}"{if !empty($row.steelposition.bgcolor)} style="background-color: {$row.steelposition.bgcolor};"{/if}>{$row.steelposition.price|escape:'html'|string_format:'%.2f'}</td>
            <td width="7%" onclick="position_click(event, {$row.steelposition_id}, {$is_revision});" id="position-value-{$row.steelposition_id}"{if !empty($row.steelposition.bgcolor)} style="background-color: {$row.steelposition.bgcolor};"{/if}>{$row.steelposition.value|escape:'html'|string_format:'%.2f'}</td>
            <td width="8%" onclick="position_click(event, {$row.steelposition_id}, {$is_revision});"{if isset($row.steelposition.on_stock) && !empty($row.steelposition.on_stock)} style="background-color: #FEFEFE; font-weight: bold;"{elseif !empty($row.steelposition.bgcolor)} style="background-color: {$row.steelposition.bgcolor};"{/if}>{if isset($row.steelposition.deliverytime)}{$row.steelposition.deliverytime.title}{/if}</td>
            <td onclick="position_click(event, {$row.steelposition_id}, {$is_revision});"{if !empty($row.steelposition.bgcolor)} style="background-color: {$row.steelposition.bgcolor};"{/if}>{$row.steelposition.notes}</td>
            <td onclick="position_click(event, {$row.steelposition_id}, {$is_revision});"{if !empty($row.steelposition.bgcolor)} style="background-color: {$row.steelposition.bgcolor};"{/if}>{$row.steelposition.internal_notes}</td>
            <td onclick="position_click(event, {$row.steelposition_id}, {$is_revision});"{if !empty($row.steelposition.bgcolor)} style="background-color: {$row.steelposition.bgcolor};"{/if}>
                {if isset($row.steelposition.quick)}
                    {$row.steelposition.quick.plate_ids|posplateids}
                {/if}
            </td>
            <td onclick="position_click(event, {$row.steelposition_id}, {$is_revision});"{if !empty($row.steelposition.bgcolor)} style="background-color: {$row.steelposition.bgcolor};"{/if}>
                {if isset($row.steelposition.quick)}
                    <div>
                        {$row.steelposition.quick.locations}
                    </div>
                    {if !empty($row.steelposition.quick.int_locations) && $row.steelposition.quick.int_locations != $row.steelposition.quick.locations}
                    <div style="font-size: 10px; color: #555;">
                        {$row.steelposition.quick.int_locations}
                    </div>
                    {/if}
                {/if}
            </td>
            <td width="5%" onclick="position_click(event, {$row.steelposition_id}, {$is_revision});"{if !empty($row.steelposition.bgcolor)} style="background-color: {$row.steelposition.bgcolor};"{/if}>{if isset($row.steelposition.biz)}{$row.steelposition.biz.number_output|escape:'html'}{/if}</td>
        </tr>
        {/foreach}
    </tbody>
</table>
{/if}

<div id="docselcontainer" style="display: none;">
    <div id="overlay"></div>
    <div id="docselform">
        <h3>Add selected position to : </h3>
        <div class="pad-10"></div>
        <div id="docselform-container"></div>
    </div>
</div>
