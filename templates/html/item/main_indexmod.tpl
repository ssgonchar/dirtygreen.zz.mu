{if !isset($page) || ($page != 'ownerless' && $page != 'stockholderless')}
<div class="row">
    <div class="col-md-12 col-lg-12 sidebar column-side">  
    <div id="collapseFilterSettings" class="panel-collapse collapse in">   
           <div id="first-column" class='col-xs-4' style="">
                           <p>
                               Stock :
                           </p>
                            <p>
                                <select id="stock" name="form[stock_id]" class="chosen-select" onchange="bind_items_filter();" style="width:200px">
                          <option value="0"{if empty($stock_id)} selected="selected"{/if}>--</option>
                            {foreach from=$stocks item=row}
                            <option value="{$row.stock.id}"{if !empty($stock_id) && $stock_id == $row.stock.id} selected="selected"{/if}>{$row.stock.title|escape:'html'}</option>
                            {/foreach}
                        </select>                          
                            </p>
                            <hr>
                            <p class='name'>
                                Location :
                            </p>
                            <p id="locations" style='padding:3px'>
                                {*debug*}
                                {if !empty($locations)}
                                    {if count($locations) == 1}
                                        {$locations[0].stockholder.title|escape:'html'}
                                    {else}
                                    
                                    {foreach from=$locations item=row}
                                        <p style="float: left; margin-right: 5px;">

                                            <label for="cb-location-{$row.stockholder_id}">
                                                <input type="checkbox" id="cb-location-{$row.stockholder_id}" name="form[stockholder][{$row.stockholder_id}]" value="{$row.stockholder_id}"{if isset($row.selected)} checked="checked"{/if}>&nbsp;{$row.stockholder.doc_no|escape:'html'}&nbsp;({$row.stockholder.city.title|escape:'html'})&nbsp;&nbsp;
                                            </label>
                                        </p>
                                    {/foreach}
                                        <div class="separator"></div>
                                    {/if}
                                {else}
                                    <span style="color: #aaa;">{if empty($stock_id)}Please select stock first{else}Please select stock first{/if}</span>
                                {/if}                        
                            </p>
                            <hr>
                            <p>
                                Type :
                            </p>
                            <p>
                                <label for="cb-type-r"><input type="checkbox" id="cb-type-r" name="form[type][r]" value="r"{if isset($type_r)} checked="checked"{/if}>&nbsp;Real&nbsp;&nbsp;&nbsp;</label>
                                <label for="cb-type-v"><input type="checkbox" id="cb-type-v" name="form[type][v]" value="v"{if isset($type_v)} checked="checked"{/if}>&nbsp;Virtual&nbsp;&nbsp;&nbsp;</label>
                       <!-- <label for="cb-type-t"><input type="checkbox" id="cb-type-t" name="form[type][t]" value="t"{if isset($type_t)} checked="checked"{/if}>&nbsp;Twin&nbsp;&nbsp;&nbsp;</label>-->
                       <!-- <label for="cb-type-c"><input type="checkbox" id="cb-type-c" name="form[type][c]" value="c"{if isset($type_c)} checked="checked"{/if}>&nbsp;Cut&nbsp;&nbsp;&nbsp;</label>-->
                       <!-- <label for="cb-type-a"><input type="checkbox" id="cb-type-a" name="form[available]" value="1"{if isset($available) && !empty($available)} checked="checked"{/if}>&nbsp;Only Available Items</label>-->
                            </p>
                            <hr>
                            <p>
                                Status :
                            </p>
                            <p><select name="form[status_id]" id="status" class="chosen-select" style="width:300px">
                                <option value="0"{if empty($status_id)} selected="selected"{/if}>--</option>
                                {foreach from=$list item=row}
                                <option value="{$row.status_id}"{if !empty($status_id) && $status_id == $row.status_id} selected="selected"{/if}>{$row.status.doc_no_full|escape:'html'}</option>
                                {/foreach}
                            </select>      
                           </p>
                            <hr>
                            </div>
                           <div id="second-column" class="col-xs-4" style="border-right: 1px solid #ccc; border-left: 1px solid #ccc;">
                        <p class='name'>
                              Steel Grade :
                            </p>
                           
                            <div>  <!--<select  id="steelgrade" name="form[steelgrade_id]" class="chosen normal" style="width:200px">-->
                            <select multiple id="steelgrade" style="width:100%" class="chosen-select" name="form[steelgrade_id]">
                                <option value="0">--</option>                                
                                {foreach from=$steelgrades item=row}
                                <option value="{$row.steelgrade.id}" {if isset($row.selected) && $row.selected == true}selected=selected{/if}><font color={$row.steelgrade.bgcolor}>{$row.steelgrade.title|escape:'html'}</font></option>
                                {/foreach}        
                            </select>  
                            </div>
                            <hr>
                        
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
                            
                        
                         <div class="col-md-4 border">
                            <p>
                               Plate Id :
                            </p>
                            <p>
                                <input type="text" name="form[plate_id]" class="form-control" style="width:200px"{if isset($plate_id)} value="{$plate_id}"{/if}>
                            </p>
                             <hr>
                          <p>
                                Notes :
                            </p>
                            <p> 
                           <input type="text" name="form[notes]" class="form-control" style="width:200px"{if isset($notes)} value="{$notes}"{/if}>
                            </p>
                            <hr>
                            
                            <p>
                                Order :
                            </p>
                            <p><select name="form[order_id]" id="order" class="chosen-select" style="width:300px">
                                <option value="0"{if empty($order_id)} selected="selected"{/if}>--</option>
                                {foreach $orders as $row}
                                <option value="{$row.order_id}"{if !empty($order_id) && $order_id == $row.order_id} selected="selected"{/if}>{$row.order.doc_no_full|escape:'html'}</option>
                                {/foreach}
                            </select>      
                           </p>
                            
                        </div>
                    </div>
                </div>            
            </div>
      
  




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
{/if}
{if empty($list)}
    {if isset($filter)}Nothing was found on my request{/if}
{else}
    <table class="list search-target" width="100%">
        <tbody>
            <tr class="top-table">
                {if empty($is_revision)}<th width="2%" class="td-item-checkbox"><input type="checkbox" onchange="check_all(this, 'item');
                        calc_selected();" style="margin-left: 2px; margin-right: 5px;"></th>{/if}
                <th width="2%">Id</th>
                <th width="5%">Plate Id</th>
                <th width="8%">Steel Grade</th>
                <th width="5%" class="text-center">Thickness<br>{if $item_dimension_unit_count == 1}{$item_dimension_unit}{/if}</th>
                <th width="5%" class="text-center">Width<br>{if $item_dimension_unit_count == 1}{$item_dimension_unit}{/if}</th>
                <th width="5%" class="text-center">Length<br>{if $item_dimension_unit_count == 1}{$item_dimension_unit}{/if}</th>
                <th width="7%" class="text-center">Weight<br>{if $item_weight_unit_count == 1}{$item_weight_unit|wunit}{/if}</th>
                <!-- <th width="7%" class="text-center">Weight<br>{if $item_weight_unit_count == 1}{$item_weight_unit|wunit}{/if}</th> -->
                <th width="7%" class="text-center">List Price<br>{if $item_price_unit_count == 1 && $item_currency_count == 1}{$item_currency|cursign}/{$item_price_unit|wunit}{/if}</th>
                <th width="7%" class="text-center">Purchase Price</th>
                <th>In DDT</th>
                    {*                <th width="5%" class="text-center">Days On Stock</th> *}
                <th>Internal Notes</th>
                <th>Location</th>
                <th>Owner</th>
                <th>Status</th>
                <th width="3%">CE Mark</th>
                    {*
                    <th>Condition</th>
                    <th>Order</th>
                    *}                
            </tr>
            {foreach from=$list item=row}

                <tr id="item-{$row.steelitem.id}"{if $row.steelitem.status_id >= $smarty.const.ITEM_STATUS_ORDERED} class="item-status-{$row.steelitem.status_id}"{/if}>
                    {if empty($is_revision)}
                        <td class="td-item-checkbox">
                            {if empty($target_doc) 
                        || ($target_doc == 'qc')
                        || ($target_doc == 'inddt' && empty($row.steelitem.in_ddt_id))
                        || ($target_doc == 'ra' && $ra.ra.stockholder_id == $row.steelitem.stockholder_id && $row.steelitem.owner_id > 0 && $row.steelitem.status_id != $smarty.const.ITEM_STATUS_RELEASED && $row.steelitem.status_id != $smarty.const.ITEM_STATUS_DELIVERED)
                        || ($target_doc == 'invoice' && $row.steelitem.parent_id == 0)
                        || ($target_doc == 'supinvoice' && $row.steelitem.parent_id == 0)
                        || ($target_doc == 'oc' && $row.steelitem.parent_id == 0)}
                            <input type="checkbox" class="cb-row-item" value="{$row.steelitem.id}" onchange="calc_selected();
                                    show_group_actions();" style="margin-right: 5px;">
                        {/if}
                    </td>
                    {/if}
                        <td onclick="show_item_context(event, {$row.steelitem_id});">{$row.steelitem_id|undef}</td>
                        <td onclick="show_item_context(event, {$row.steelitem_id});">{$row.steelitem.guid|escape:'html'|undef}</td>
                        <td onclick="show_item_context(event, {$row.steelitem_id});">{if isset($row.steelitem.steelgrade)}{$row.steelitem.steelgrade.title|escape:'html'}{else}{''|undef}{/if}</td>
                        <td onclick="show_item_context(event, {$row.steelitem_id});" class="text-center">{$row.steelitem.thickness|escape:'html'}{if $item_dimension_unit_count > 1} {$row.steelitem.dimension_unit}{/if}</td>
                        <td onclick="show_item_context(event, {$row.steelitem_id});" class="text-center">{$row.steelitem.width|escape:'html'}{if $item_dimension_unit_count > 1} {$row.steelitem.dimension_unit}{/if}</td>
                        <td onclick="show_item_context(event, {$row.steelitem_id});" class="text-center">{$row.steelitem.length|escape:'html'}{if $item_dimension_unit_count > 1} {$row.steelitem.dimension_unit}{/if}</td>
                        <td onclick="show_item_context(event, {$row.steelitem_id});" class="text-center" id="item-weight-{$row.steelitem_id}">{if $row.steelitem.weight_unit == 'lb'}{$row.steelitem.unitweight|escape:'html'|string_format:'%d'|wunit}{else}{$row.steelitem.unitweight|escape:'html'|string_format:'%.2f'|wunit}{/if}</td>
                    <!-- <td onclick="show_item_context(event, {$row.steelitem_id});" class="text-center" id="item-weight-{$row.steelitem_id}">{$row.steelitem.weight_unit}{if $item_weight_unit_count > 1} {$row.steelitem.unitweight|escape:'html'|string_format:'%d'|wunit} {$row.steelitem.weight_unit}{/if}</td> -->
                        <td onclick="show_item_context(event, {$row.steelitem_id});" class="text-center">
                            {if $item_price_unit_count > 1 && $item_currency_count > 1}
                                {$row.steelitem.price|escape:'html'|string_format:'%.2f'} {$row.steelitem.currency|cursign}/{$row.steelitem.price_unit|wunit}
                            {else}
                                {$row.steelitem.price|escape:'html'|string_format:'%.2f'}
                            {/if}
                            <input type="hidden" id="item-qtty-{$row.steelitem_id}" value="1">
                            <input type="hidden" id="item-value-{$row.steelitem_id}" value="{$row.steelitem.price * $row.steelitem.unitweight}">
                            <input type="hidden" id="item-purchasevalue-{$row.steelitem_id}" value="{$row.steelitem.purchase_price * $row.steelitem.unitweight}">
                            <input type="hidden" id="item-orderid-{$row.steelitem_id}" value="{$row.steelitem.order_id}">
                        </td>
                        <td onclick="show_item_context(event, {$row.steelitem_id});" class="text-center">{$row.steelitem.purchase_price|escape:'html'|string_format:'%.2f'}{if !empty($row.steelitem.purchase_currency)} {$row.steelitem.purchase_currency|cursign}/Ton{/if}</td>
                        <td class="text-center">
                            {if $row.steelitem.in_ddt_id > 0 && $row.steelitem.in_ddt.company_id == $row.steelitem.stockholder_id}
                                <a href="/inddt/{$row.steelitem.in_ddt_id}">{$row.steelitem.in_ddt_number} dd {$row.steelitem.in_ddt_date|date_format:'d/m/Y'}</a>
                            {else}{''|undef}
                            {/if}
                        </td>
                        {*
                        <td onclick="show_item_context(event, {$row.steelitem_id});">{$row.steelitem.ddt_number|escape:'html'}</td>
                        <td onclick="show_item_context(event, {$row.steelitem_id});">{if !empty($row.steelitem.ddt_date)}{$row.steelitem.ddt_date|escape:'html'|date_format:'d/m/Y'}{/if}</td>
                        *}  
                        {*                <td onclick="show_item_context(event, {$row.steelitem_id});" class="text-center">{$row.steelitem.days_on_stock}</td>  *}
                        <td onclick="show_item_context(event, {$row.steelitem_id});">
                            {if $row.steelitem.parent_id > 0}
                                <div style="margin-bottom: 5px;"><a href="/item/edit/{$row.steelitem.parent_id}">{if $row.steelitem.rel == 't'}Twin of{else if $row.steelitem.rel == 'c'}Cut from{/if} : {if !empty($row.steelitem.parent.guid)}{$row.steelitem.parent.guid|escape:'html'}{else}#{$row.steelitem.parent_id}{/if}</a></div>
                            {/if}
                            {$row.steelitem.internal_notes|escape:'html'|undef}
                        </td>
                        <td onclick="show_item_context(event, {$row.steelitem_id});">{if isset($row.steelitem.stockholder)}{$row.steelitem.stockholder.title|escape:'html'}{else}{''|undef}{/if}</td>
                        <td onclick="show_item_context(event, {$row.steelitem_id});">{if isset($row.steelitem.owner)}{$row.steelitem.owner.title_trade|escape:'html'}{else}{''|undef}{/if}</td>
                        {if $row.steelitem.order_id > 0}
                            <td>
                    {$row.steelitem.status_title}<dr>
                        <a href="/order/{$row.steelitem.order_id}">{$row.steelitem.order_id|order_doc_no}</a>
                        </td>
                    {else}
                        <td onclick="show_item_context(event, {$row.steelitem_id});">{$row.steelitem.status_title|undef}</td>
                    {/if}
                    <td onclick="show_item_context(event, {$row.steelitem_id});">{if isset($row.steelitem.is_ce_mark) && !empty($row.steelitem.is_ce_mark)}<img src="/img/cemark16.png" alt="CE Mark" title="CE Mark">{else}<i style="color: #999;">no</i>{/if}</td>
                        {*                
                        <td onclick="show_item_context(event, {$row.steelitem_id});">
                        {if !empty($row.steelitem.properties.condition)}
                        {if $row.steelitem.properties.condition == 'ar'}As Rolled
                        {elseif $row.steelitem.properties.condition == 'n'}Normalized
                        {elseif $row.steelitem.properties.condition == 'nr'}Normalizing Rolling
                        {/if}
                        {/if}
                        </td>
                        <td nowrap="nowrap">
                        {if $row.steelitem.order_id > 0}
                        <a href="/order/view/{$row.steelitem.order_id}">{$row.steelitem.order_id|order_doc_no}</a>
                        {/if}
                        </td>
                        *}                
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