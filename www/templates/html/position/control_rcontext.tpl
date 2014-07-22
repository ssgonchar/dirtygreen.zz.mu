<div class="container" style="display:none;">
    <div class="row">
        <div class="col-md-4 col-lg-4">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">Search settings</h3>
                </div>
                <!--
                <div class="panel-body">
                    Panel content
                </div>
                <!-- Table -->
                <table class="table">
                    <tr>
                        <td>
                            Stock 
                        </td>
                        <td>
                            <select id="stock" name="form[stock_id]" class="normal" onchange="bind_positions_filter();">
                                <option value="0"{if empty($stock_id)} selected="selected"{/if}>--</option>
                                {foreach from=$stocks item=row}
                                <option value="{$row.stock.id}"{if !empty($stock_id) && $stock_id == $row.stock.id} selected="selected"{/if}>{$row.stock.title|escape:'html'}</option>
                                {/foreach}
                            </select>            
                        </td>
                    </tr>
                    <tr>
                        <td>
                            Location 
                        </td>
                        <td>
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
                    <tr>
                        <td>
                            Delivery time 
                        </td>
                        <td>
                            {if !empty($deliverytimes)}
                                {foreach from=$deliverytimes item=row}
                                    <label for="cb-deliverytime-{$row.deliverytime_id}"><input type="checkbox" id="cb-deliverytime-{$row.deliverytime_id}" name="form[deliverytime][{$row.deliverytime_id}]" value="{$row.deliverytime_id}"{if isset($row.selected)} checked="checked"{/if}>&nbsp;{$row.deliverytime.title|escape:'html'}&nbsp;&nbsp;&nbsp;</label>
                                {/foreach}
                            {else}
                                <span style="color: #aaa;">{if empty($stock_id)}Please select stock first{else}none{/if}</span>
                            {/if}           
                        </td>
                    </tr>
                    <tr>
                        <td>
                            Steel grade
                        </td>
                        <td>
                            {if !empty($locations)}
                                <select id="steelgrades" style="width:100%" name="form[steelgrade]">
                                    <option value=0>All</option>
                                        {if isset($steelgrade_list) && !empty($steelgrade_list)}
                                            {foreach from=$steelgrade_list item=row}
                                                <option value="{$row.steelgrade.id}"{if isset($steelgrade_id) && $steelgrade_id == $row.steelgrade.id}selected=selected{/if}><font color={$row.bgcolor}>{$row.steelgrade.title|escape:'html'}</font></option>
                                            {/foreach}
                                        {/if}  
                                </select>
                            {else}
                                <span style="color: #aaa;">{if empty($stock_id)}Please select stock first{else}none{/if}</span>
                            {/if}              
                        </td>
                    </tr>
                    <tr>
                        <td>
                            Notes
                        </td>
                        <td>
                            {if !empty($locations)}
                                <nobr><input placeholder="free text" type="text" value="{if isset($keyword) && !empty($keyword)}{$keyword|escape:'html'}{/if}" style="width: 100%;" name="form[keyword]">        
                            {else}
                                <span style="color: #aaa;">{if empty($stock_id)}Please select stock first{else}none{/if}</span>
                            {/if}         
                        </td>
                    </tr>    
                </table>
            </div>    
        </div>
    </div>
    </div>
</div>