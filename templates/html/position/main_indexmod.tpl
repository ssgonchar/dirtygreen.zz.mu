{if empty($list)}
    {if isset($filter)}Nothing was found on my request{/if}
{else}
    <table class="list search-target" width="100%">
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
                        <button type="button" disabled="true" class="btn-mirror btn btn-default btn-xs glyphicon glyphicon-pencil" title="Please, select a position" onclick="create_mirror_from_selected('{$row.steelposition_id}');" style="margin-left: 0px;">&nbsp;Mirror </button>
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