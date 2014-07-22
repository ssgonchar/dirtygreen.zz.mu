<ul class="nav nav-sidebar">
    <div class="row">
        <div class='col-xs-12' style="">
            <p>
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
            <hr>
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
                    {*debug*}
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
</ul>