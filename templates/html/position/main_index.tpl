<div class="row">
    <div class="col-md-12 col-lg-12 sidebar column-side">
            <div id="collapseFilterSettings" class="panel-collapse collapse in">
                    <div id="first-column" class='col-xs-4' style="">
                        <p class='name'>
                            Stocks
                        </p>
                        <p>
                            <select id="stock" name="form[stock_id]" class="chosen-select normal" onchange="bind_positions_filter();">
                                <!--<option value="0"{if empty($stock_id)} selected="selected"{/if}>--</option>-->
                                {foreach from=$stocks item=row}
                                    <option value="{$row.stock.id}"{if !empty($stock_id) && $stock_id == $row.stock.id} selected="selected"{/if}>{$row.stock.title|escape:'html'}</option>
                                {/foreach}
                            </select>                         
                        </p>
                        <hr/>
                        <p class='name'>
                            Locations
                        </p>
                        <p id="locations">
                            {if !empty($locations)}
                                {foreach from=$locations item=row}
                                    <label for="cb-location-{$row.location_id}"><input type="checkbox" id="cb-location-{$row.location_id}" name="form[location][{$row.location_id}]" value="{$row.location_id}"{if isset($row.selected)} checked="checked"{/if}>&nbsp;{$row.location.title|escape:'html'}&nbsp;&nbsp;&nbsp;</label>
                                        {if isset($row.selected)}
                                        </span>
                                    {/if}
                                    <br/>
                                {/foreach}
                            {else}
                                <span style="color: #aaa;">{if empty($stock_id)}Please select stock first{else}none{/if}</span>
                            {/if}                         
                        </p>   
                        <hr/>
                        <p class='name'>
                            Stockholders
                        </p>
                        <p id="stockholders">
                            {if !empty($stockholders)}
                                {foreach from=$stockholders item=row}
                                    <label for="cb-stockholder-{$row.stockholder_id}">
                                        <input type="checkbox" id="cb-stockholder-{$row.stockholder_id}" name="form[stockholder][{$row.stockholder_id}]" value="{$row.stockholder_id}" {if isset($row.selected)} checked="checked"{/if}>&nbsp;{$row.stockholder.doc_no_full}
                                    </label><br/>
                                {/foreach}
                            {else}
                                <span style="color: #aaa;">{if empty($stock_id)}Please select stock first{else}none{/if}</span>
                            {/if}                         
                        </p>         
                        <hr/>
                        <p class='name'>
                            Delivery time
                        </p>
                        <p>
                            <span class="" id="deliverytimes">
                                {if !empty($deliverytimes)}
                                    {foreach from=$deliverytimes item=row}
                                        <label for="cb-deliverytime-{$row.deliverytime_id}"><input type="checkbox" id="cb-deliverytime-{$row.deliverytime_id}" name="form[deliverytime][{$row.deliverytime_id}]" value="{$row.deliverytime_id}"{if isset($row.selected)} checked="checked"{/if}>&nbsp;{$row.deliverytime.title|escape:'html'}&nbsp;&nbsp;&nbsp;</label><br/>
                                        {/foreach}
                                    {else}
                                    <span style="color: #aaa;">{if empty($stock_id)}Please select stock first{else}none{/if}</span>
                                {/if}                                                
                            </span>
                        </p>
                    </div>
                    <div id="second-column" class="col-xs-4" style="border-left: 1px solid #ccc;">
                        <p class='name'>
                            Steel grades
                        </p>
                        <p id="steelgrades">
                            {if !empty($stock_id)}
                                <select multiple id="steelgrade" style="width:100%" class="chosen-select" name="form[steelgrade][]" style='position:relative; z-index: 10;'>
                                    <option value=0>All</option>
                                    {if isset($steelgrade_list) && !empty($steelgrade_list)}
                                        {foreach from=$steelgrade_list item=row}
                                            <option value="{$row.steelgrade.id}" {if isset($row.selected) && $row.selected == true}selected=selected{/if}><font color={$row.steelgrade.bgcolor}>{$row.steelgrade.title|escape:'html'}</font></option>
                                        {/foreach}
                                    {/if}  
                                </select>
                                {*debug*}
                            {else}
                                <span style="color: #aaa;">{if empty($stock_id)}Please select stock first{else}none{/if}</span>
                            {/if}  
                        </p>
                        <hr/>
                        <p class='name'>
                            Thickness:
                        </p>
                        <p>
                            <u>exact value</u> <input class="find-parametr" type="text" name="form[thickness]" size="8" placeholder="number" {if isset($thickness)} value="{$thickness}"{/if}>
                            or range
                            <input class="find-parametr" type="text" name="form[thicknessmin]" placeholder="min" size="6" {if isset($thicknessmin)} value="{$thicknessmin}"{/if}>
                            &mdash; <input class="find-parametr" type="text" name="form[thicknessmax]" placeholder="max" size="6" {if isset($thicknessmax)} value="{$thicknessmax}"{/if}>
                        </p>
                        <hr/>
                        <p class='name'>
                            Width:
                        </p>
                        <p>
                            <u>exact value</u> <input class="find-parametr" type="text" name="form[width]" size="8" placeholder="number" {if isset($width)} value="{$width}"{/if}>
                            or range 
                            <input class="find-parametr" type="text" name="form[widthmin]" placeholder="min" size="6" {if isset($widthmin)} value="{$widthmin}"{/if}>
                            &mdash; <input class="find-parametr" type="text" name="form[widthmax]" placeholder="max" size="6" {if isset($widthmax)} value="{$widthmax}"{/if}>

                        </p>
                        <hr/>
                        <p class='name'>
                            Length:
                        </p>
                        <p>
                            <u>exact value</u> <input class="find-parametr" type="text" name="form[length]" size="8" placeholder="number" {if isset($length)} value="{$length}"{/if}>                            
                            or range 
                            <input class="find-parametr" type="text" name="form[lengthmin]" placeholder="min" size="6" {if isset($lengthmin)} value="{$lengthmin}"{/if}>
                            &mdash; <input class="find-parametr" type="text" name="form[lengthmax]" placeholder="max" size="6"  {if isset($lengthmax)} value="{$lengthmax}"{/if}>

                        </p>
                        <hr/>
                        <p class='name'>
                            Weight:
                        </p>
                        <p>
                            <u>exact value</u> <input class="find-parametr" type="text" name="form[weight]" size="8" placeholder="number" {if isset($weight)} value="{$weight}"{/if}>
                            or range 
                            <input class="find-parametr" type="text" name="form[weightmin]" placeholder="min" size="6"  {if isset($weightmin)} value="{$weightmin}"{/if}>
                            &mdash; <input class="find-parametr" type="text" name="form[weightmax]" placeholder="max" size="6"  {if isset($weightmax)} value="{$weightmax}"{/if}>

                        </p>
                        <hr/>
                    </div>
                    <div id="third-column" class="col-xs-4" style="border-left: 1px solid #ccc;">
                        <p class='name'>
                            Notes
                        </p>
                        <p>
                            <input class="form-control find-parametr" placeholder="free text" type="text" {if isset($keyword) && !empty($keyword)} value="{$keyword|escape:'html'}" {/if} style="width: 100%;" name="form[keyword]">
                        </p>
                        <input type="submit" name="btn_setfilter" value="Find" class="btn100b" style='float: right;'>
                    </div> 
                </div>
                <!--<div class="panel-footer"><input type="submit" name="btn_setfilter" value="Find" class="btn100b" style='float: right;'></div>-->
            </div>            
        </div>

<!--{*
<div class="row">
    <div class="col-md-8 col-lg-8">
        <div class="panel-group" id="accordion" >            
            <div class="panel panel-default">
                <div class="panel-heading">
                    <a data-toggle="collapse" data-parent="#accordion" href="#collapseFilterSettings" style='display: inline-block;'>
                        <h3 class="panel-title">
                            Select
                        </h3>
                    </a>
                </div>
                <div id="collapseFilterSettings" class="panel-collapse collapse in">
                    <div class="panel-body">
                        <div class='col-xs-6' style="">
                            <p>
                                Stocks
                            </p>
                            <p>
                                <select id="stock" name="form[stock_id]" class="chosen-select normal" onchange="bind_positions_filter();">
                                    
                                    {foreach from=$stocks item=row}
                                        <option value="{$row.stock.id}"{if !empty($stock_id) && $stock_id == $row.stock.id} selected="selected"{/if}>{$row.stock.title|escape:'html'}</option>
                                    {/foreach}
                                </select>                         
                            </p>
                            <hr/>
                            <p>
                                Delivery time
                            </p>
                            <p>
                                <span class="" id="deliverytimes">
                                    {if !empty($deliverytimes)}
                                        {foreach from=$deliverytimes item=row}
                                            <label for="cb-deliverytime-{$row.deliverytime_id}"><input type="checkbox" id="cb-deliverytime-{$row.deliverytime_id}" name="form[deliverytime][{$row.deliverytime_id}]" value="{$row.deliverytime_id}"{if isset($row.selected)} checked="checked"{/if}>&nbsp;{$row.deliverytime.title|escape:'html'}&nbsp;&nbsp;&nbsp;</label><br/>
                                            {/foreach}
                                        {else}
                                        <span style="color: #aaa;">{if empty($stock_id)}Please select stock first{else}none{/if}</span>
                                    {/if}                                                
                                </span>
                            </p>
                            <hr/>
                            <p>
                                Locations
                            </p>
                            <p id="locations">
                                {if !empty($locations)}
                                    {foreach from=$locations item=row}
                                        <label for="cb-location-{$row.location_id}"><input type="checkbox" id="cb-location-{$row.location_id}" name="form[location][{$row.location_id}]" value="{$row.location_id}"{if isset($row.selected)} checked="checked"{/if}>&nbsp;{$row.location.title|escape:'html'}&nbsp;&nbsp;&nbsp;</label>
                                            {if isset($row.selected)}
                                            </span>
                                        {/if}
                                        <br/>
                                    {/foreach}
                                {else}
                                    <span style="color: #aaa;">{if empty($stock_id)}Please select stock first{else}none{/if}</span>
                                {/if}                         
                            </p>
                            <hr/>
                            <p>
                                Stockholders
                            </p>
                            <p id="stockholders">
                                {if !empty($stockholders)}
                                    {foreach from=$stockholders item=row}
                                        <label for="cb-stockholder-{$row.stockholder_id}">
                                            <input type="checkbox" id="cb-stockholder-{$row.stockholder_id}" name="form[stockholder][{$row.stockholder_id}]" value="{$row.stockholder_id}" {if isset($row.selected)} checked="checked"{/if}>&nbsp;{$row.stockholder.doc_no_full}
                                        </label><br/>
                                    {/foreach}
                                {else}
                                    <span style="color: #aaa;">{if empty($stock_id)}Please select stock first{else}none{/if}</span>
                                {/if}                         
                            </p>                         
                        </div>
                        <div class="col-xs-6" style="border-left: 1px solid #ccc;">
                            <p>
                                Steel grades
                            </p>
                            <p id="steelgrades">
                                {if !empty($stock_id)}
                                    <select multiple id="steelgrade" style="width:100%" class="chosen-select" name="form[steelgrade][]">
                                        <option value=0>All</option>
                                        {if isset($steelgrade_list) && !empty($steelgrade_list)}
                                            {foreach from=$steelgrade_list item=row}
                                                <option value="{$row.steelgrade.id}" {if isset($row.selected) && $row.selected == true}selected=selected{/if}><font color={$row.steelgrade.bgcolor}>{$row.steelgrade.title|escape:'html'}</font></option>
                                            {/foreach}
                                        {/if}  
                                    </select>
                                {else}
                                    <span style="color: #aaa;">{if empty($stock_id)}Please select stock first{else}none{/if}</span>
                                {/if}  
                            </p>
                            <hr/>
                            <p>
                                Thickness:
                            </p>
                            <p>
                                exact value <input type="text" class="" name="form[thickness]" size="8" placeholder="number" {if isset($thickness)} value="{$thickness}"{/if}>
                                or range
                                <input type="text" class="" name="form[thicknessmin]" placeholder="min" size="6" {if isset($thicknessmin)} value="{$thicknessmin}"{/if}>
                                &mdash; <input type="text" class="" name="form[thicknessmax]" placeholder="max" size="6" {if isset($thicknessmax)} value="{$thicknessmax}"{/if}>

                            </p>
                            <hr/>
                            <p>
                                Width:
                            </p>
                            <p>
                                exact value <input type="text" class="" name="form[width]" size="8" placeholder="number" {if isset($width)} value="{$width}"{/if}>
                                or range 
                                <input type="text" class="" name="form[widthmin]" placeholder="min" size="6" {if isset($widthmin)} value="{$widthmin}"{/if}>
                                &mdash; <input type="text" class="" name="form[widthmax]" placeholder="max" size="6" {if isset($widthmax)} value="{$widthmax}"{/if}>

                            </p>
                            <hr/>
                            <p>
                                Length:
                            </p>
                            <p>
                                exact value <input type="text" class="" name="form[length]" size="8" placeholder="number" {if isset($length)} value="{$length}"{/if}>                            
                                or range 
                                <input type="text" class="" name="form[lengthmin]" placeholder="min" size="6" {if isset($lengthmin)} value="{$lengthmin}"{/if}>
                                &mdash; <input type="text" class="" name="form[lengthmax]" placeholder="max" size="6"  {if isset($lengthmax)} value="{$lengthmax}"{/if}>

                            </p>
                            <hr/>
                            <p>
                                Weight:
                            </p>
                            <p>
                                exact value <input type="text" class="" name="form[weight]" size="8" placeholder="number" {if isset($weight)} value="{$weight}"{/if}>
                                or range 
                                <input type="text" class="" name="form[weightmin]" placeholder="min" size="6"  {if isset($weightmin)} value="{$weightmin}"{/if}>
                                &mdash; <input type="text" class="" name="form[weightmax]" placeholder="max" size="6"  {if isset($weightmax)} value="{$weightmax}"{/if}>

                            </p>
                            <hr/>
                            <p>
                                Notes
                            </p>
                            <p>

                                <input placeholder="free text" type="text" {if isset($keyword) && !empty($keyword)} value="{$keyword|escape:'html'}" {/if} style="width: 100%;" name="form[keyword]">
                            </p>                    
                        </div>
                    </div>
                    
                </div>            
            </div>
        </div>
    </div>
</div>*}-->

{if empty($list)}
    {if isset($filter)}Nothing was found on my request{/if}
{else}
    <table id='position-table' class="list search-target" width="100%">
        <thead>
            <tr class="top-table">
                <th width="3%">Select All<br/><input class="chb" type="checkbox" disabled="disabled" onchange="check_all(this, 'position');
                    calc_selected();
                    show_group_actions();" style="margin-left: 2px;"></th>
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
                <th>
                    <div>Hide</div>
                    <div style="font-size: 10px; color: #555;">from Bistro</div>
                </th>
                <th>Mirrors</th>
            </tr>
        </thead>
        <tbody>
            {foreach from=$list item=row}
                <tr id="position-{$row.steelposition_id}">
                    <td width="3%" {if !empty($row.steelposition.bgcolor)} style="background-color: {$row.steelposition.bgcolor};"{/if}>
                        <input type="checkbox" disabled="disabled" value="{$row.steelposition_id}" class="cb-row-position chb" onchange="calc_selected();
                        show_group_actions();">
                        <!--<input type="checkbox"  value="{$row.steelposition_id}" class="cb-row-position chb" onchange="calc_selected(); show_group_actions();">-->
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
                    <td width="7%" onclick="position_click(event, {$row.steelposition_id}, {$is_revision});" id="position-weight-{$row.steelposition_id}"{if !empty($row.steelposition.bgcolor)} style="background-color: {$row.steelposition.bgcolor};"{/if}>{if $row.steelposition.weight_unit == 'lb'}{$row.steelposition.weight|escape:'html'|string_format:'%d'|wunit}{else}{$row.steelposition.weight|escape:'html'|string_format:'%.2f'|wunit}{/if}</td>
                    <td width="7%" class="position-price-td" data-id="{$row.steelposition_id}"{if !empty($row.steelposition.bgcolor)} style="background-color: {$row.steelposition.bgcolor};"{/if}>{$row.steelposition.price|escape:'html'|string_format:'%.2f'}</td>
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
                    <td width="3%" {if !empty($row.steelposition.bgcolor)} style="background-color: {$row.steelposition.bgcolor};"{/if}>
                        <input type="checkbox" value="{$row.steelposition_id}" class="chb-hidden-in-stock" onchange="change_visibility_in_stock(this, this.value, this.checked);" {if $row.steelposition.hidden_in_stock == 1} checked {/if}>
                    </td>
                    <td >
                        <button type="button" disabled="true" class="btn-mirror btn btn-primary btn-xs" title="Please, select a position" onclick="create_mirror_from_selected('{$row.steelposition_id}');"><i class=" glyphicon glyphicon-pencil"></i>&nbsp;Mirror </button>
                    </td>
                </tr>
            {/foreach}
            {*debug*}
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