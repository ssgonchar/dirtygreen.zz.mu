<table width="100%">
    <tr>
        <td width="30%" class="text-top">
            <table class="form" width="100%">
                <tr>
                    <td class="form-td-title-b">Stock :</td>
                    <td>
                        {if empty($stock_id)}{''|undef}
                        {else}
                            {foreach $stocks as $row}
                            {if !empty($stock_id) && $stock_id == $row.stock.id}{$row.stock.title|escape:'html'}{/if}
                            {/foreach}
                        {/if}
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
                            {if count($locations) == 1}{$locations[0].location.title|escape:'html'}
                            {else}
                                {$is_selected_exists=false}
                                {foreach $locations as $row}
                                <span style="margin-right: 5px;">
                                {if isset($row.selected)}
                                    {$row.location.title|escape:'html'};&nbsp;
                                    {$is_selected_exists=true}
                                {/if}
                                </span>
                                {/foreach}
                                {if !$is_selected_exists}{''|undef}{/if}
                            {/if}
                        {else}{''|undef}
                        {/if}
                    </td>
                </tr>
                <tr height="32">
                    <td class="form-td-title">Delivery times :</td>
                    <td id="deliverytimes">
{*
                        {if !empty($deliverytimes)}
                        {foreach from=$deliverytimes item=row}
                        <label for="cb-deliverytime-{$row.deliverytime_id}"><input type="checkbox" id="cb-deliverytime-{$row.deliverytime_id}" name="form[deliverytime][{$row.deliverytime_id}]" value="{$row.deliverytime_id}"{if isset($row.selected)} checked="checked"{/if}>&nbsp;{$row.deliverytime.title|escape:'html'}&nbsp;&nbsp;&nbsp;</label>
                        {/foreach}
                        {else}
                        <span style="color: #aaa;">{if empty($stock_id)}Please select stock first{else}none{/if}</span>
                        {/if}
*}
                        {if !empty($deliverytimes)}
                            {if count($deliverytimes) == 1}{$deliverytimes[0].deliverytime.title|escape:'html'}
                            {else}
                                {$is_selected_exists=false}
                                {foreach $deliverytimes as $row}
                                <span style="margin-right: 5px;">
                                {if isset($row.selected)}
                                    {$row.deliverytime.title|escape:'html'};&nbsp;
                                    {$is_selected_exists=true}
                                {/if}
                                </span>
                                {/foreach}
                                {if !$is_selected_exists}{''|undef}{/if}
                            {/if}
                        {else}{''|undef}
                        {/if}
                    </td>
                </tr>
            </table>
        </td>
        <td width="10%" class="text-right text-middle" style="padding-right: 0;"></td>
    </tr>
</table>

<div class="pad-10"></div>

<div>
    <table width="100%">
        <tr>
            <td width="30%" class="text-top">
                <table class="form" width="100%">
                    <tr>
                        <td class="form-td-title">Thickness :</td>
                        <td>{$thickness|undef}</td>
                    </tr>
                    <tr>
                        <td class="form-td-title">Width :</td>
                        <td>{$width|undef}</td>
                    </tr>
                    <tr>
                        <td class="form-td-title">Length :</td>
                        <td>{$length|undef}</td>
                    </tr>
                </table>
            </td>
            <td width="30%" class="text-top">
                <table class="form" width="100%">
                    <tr>
                        <td class="form-td-title">Weight :</td>
                        <td>{$weight|undef}</td>
                    </tr>
                    <tr>
                        <td class="form-td-title">Notes :</td>
                        <td>{$notes|undef}</td>
                    </tr>
                </table>
            </td>
            <td width="30%" class="text-top">
                <table class="form" width="100%">
                    <tr>
                        <td class="form-td-title">Revision Date :</td>
                        <td>{$rev_date|escape:'html'|undef}</td>
                    </tr>
                    <tr>
                        <td class="form-td-title">Revision Time :</td>
                        <td>
                            {if empty($rev_time)}{''|undef}
                            {else if $rev_time == '00:00'}00:00
                            {else if $rev_time == '03:00'}03:00
                            {else if $rev_time == '06:00'}06:00
                            {else if $rev_time == '09:00'}09:00
                            {else if $rev_time == '12:00'}12:00
                            {else if $rev_time == '15:00'}15:00
                            {else if $rev_time == '18:00'}18:00
                            {else if $rev_time == '21:00'}21:00
                            {else if $rev_time == '23:59'}24:00
                            {/if}
                        </td>
                    </tr>
                </table>
            </td>
            <td width="10%" class="text-right text-middle" style="padding-right: 0;"></td>
        </tr>
    </table>
</div>

<div class="pad1"></div>

{if empty($list)}{if isset($filter)}Nothing was found on my request{/if}
{else}
<table class="list" width="100%">
    <tbody>
        <tr class="top-table">
            <th width="5%">Pos Id</th>
            <th width="8%">Steel Grade</th>
            <th width="5%">Thickness<br>{if isset($stock)}{$stock.dimension_unit}{/if}</th>
            <th width="5%">Width<br>{if isset($stock)}{$stock.dimension_unit}{/if}</th>
            <th width="5%">Length<br>{if isset($stock)}{$stock.dimension_unit}{/if}</th>
            <th width="7%">Unit Weight<br>{if isset($stock)}{$stock.weight_unit|wunit}{/if}</th>
            <th width="5%">Qtty<br>pcs</th>
            <th width="7%">Weight<br>{if isset($stock)}{$stock.weight_unit|wunit}{/if}</th>
            <th width="7%">Price<br>{if isset($stock)}{$stock.currency_sign}/{$stock.weight_unit|wunit}{/if}</th>
            <th width="7%">Value<br>{if isset($stock)}{$stock.currency_sign}{/if}</th>
            <th width="8%">Delivery Time</th>
            <th>Notes</th>
            <th>Internal Notes</th>
            <th>Plate Id</th>
            <th>Location</th>
            <th width="5%">Biz</th>
        </tr>{*debug*}
        {foreach from=$list item=row}
        <tr id="position-{$row.steelposition_id}">
            <td>{$row.steelposition_id}</td>
            <td class="pos">{$row.steelposition.steelgrade.title|escape:'html'}</td>
            <td>{$row.steelposition.thickness|escape:'html'}</td>
            <td>{$row.steelposition.width|escape:'html'}</td>
            <td>{$row.steelposition.length|escape:'html'}</td>
            <td>{$row.steelposition.unitweight|escape:'html'|string_format:'%.2f'}</td>
            <td><span id="position-qtty-{$row.steelposition_id}">{$row.steelposition.qtty|escape:'html'|string_format:'%d'}</span>{if isset($row.steelposition.quick) && !empty($row.steelposition.quick.reserved)} (<span>{$row.steelposition.quick.reserved}</span>){/if}</td>
            <td>{$row.steelposition.weight|escape:'html'|string_format:'%.2f'}</td>
            <td>{$row.steelposition.price|escape:'html'|string_format:'%.2f'}</td>
            <td>{$row.steelposition.value|escape:'html'|string_format:'%.2f'}</td>
            <td>{if isset($row.steelposition.deliverytime)}{$row.steelposition.deliverytime.title|escape:'html'}{/if}</td>
            <td>{$row.steelposition.notes|escape:'html'}</td>
            <td>{$row.steelposition.internal_notes|escape:'html'}</td>
            <td>{if !empty($row.steelposition.quick)}
                    {$row.steelposition.quick.plate_ids|posplateids}
                {/if}</td>
            <td>{if isset($row.steelposition.quick)}{$row.steelposition.quick.locations}{/if}</td>
            <td>{if isset($row.steelposition.biz)}{$row.steelposition.biz.number_output|escape:'html'}{/if}</td>
        </tr>
        {/foreach}
    </tbody>
</table>
{/if}