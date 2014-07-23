<td><a href="javascript: void(0);" onclick="position_show_items({$position.steelposition_id});"><img id="img-{$position.steelposition_id}" src="/img/icons/minus.png" title="Show items" alt="Show items"></a></td>
<td>
    <input type="checkbox" checked="checked" class="cb-row-position" value="{$position.steelposition_id}">&nbsp;
    <a href="javascript: void(0);" onclick="show_position_actions(this, {$position.steelposition_id|escape:'html'});">{$position.steelposition_id|escape:'html'}</a>
</td>
<td>
    <select id="pos{$position.steelposition_id}-steelgrade-1" class="max">
        <option value="0">--</option>
        {foreach from=$steelgrades item=row}
        <option value="{$row.steelgrade.id}"{if $position.steelposition.steelgrade_id == $row.steelgrade.id} selected="selected"{/if}>{$row.steelgrade.title|escape:'html'}</option>
        {/foreach}                    
    </select>    
</td>
<td><input type="text" id="pos{$position.steelposition_id}-thickness-1" class="max" value="{$position.steelposition.thickness|escape:'html'}" onkeyup="calc_unitweight(1, '{$position.steelposition.dimension_unit}', '{$position.steelposition.weight_unit}', 'pos{$position.steelposition_id}'); calc_weight(1, 'pos{$position.steelposition_id}'); calc_value(1, 'pos{$position.steelposition_id}'); calc_total();"></td>
<td><input type="text" id="pos{$position.steelposition_id}-width-1" class="max" value="{$position.steelposition.width|escape:'html'}" onkeyup="calc_unitweight(1, '{$position.steelposition.dimension_unit}', '{$position.steelposition.weight_unit}', 'pos{$position.steelposition_id}'); calc_weight(1, 'pos{$position.steelposition_id}'); calc_value(1, 'pos{$position.steelposition_id}'); calc_total();"></td>
<td><input type="text" id="pos{$position.steelposition_id}-length-1" class="max" value="{$position.steelposition.length|escape:'html'}" onkeyup="calc_unitweight(1, '{$position.steelposition.dimension_unit}', '{$position.steelposition.weight_unit}', 'pos{$position.steelposition_id}'); calc_weight(1, 'pos{$position.steelposition_id}'); calc_value(1, 'pos{$position.steelposition_id}'); calc_total();"></td>
<td><input type="text" id="pos{$position.steelposition_id}-unitweight-1" class="max" value="{$position.steelposition.unitweight|escape:'html'|string_format:'%.2f'}" onkeyup="calc_weight(1, 'pos{$position.steelposition_id}'); calc_value(1, 'pos{$position.steelposition_id}'); calc_total();"></td>
<td class="text-center">
    <input type="hidden" id="pos{$position.steelposition_id}-qtty-1" value="{$position.steelposition.qtty|escape:'html'|string_format:'%d'}">
    {$position.steelposition.qtty|escape:'html'|string_format:'%d'}
</td>
<td><input type="text" id="pos{$position.steelposition_id}-weight-1" class="max" value="{$position.steelposition.weight|escape:'html'|string_format:'%.2f'}" onkeyup="calc_value(1, 'pos{$position.steelposition_id}'); calc_total();"></td>
<td><input type="text" id="pos{$position.steelposition_id}-price-1" class="max" value="{$position.steelposition.price|escape:'html'|string_format:'%.2f'}" onkeyup="calc_value(1, 'pos{$position.steelposition_id}'); calc_total();"></td>
<td><input type="text" id="pos{$position.steelposition_id}-value-1" class="max" value="{$position.steelposition.value|escape:'html'|string_format:'%.2f'}" class="max"></td>
<td><input type="text" id="pos{$position.steelposition_id}-delivery_time-1" value="{if isset($position.steelposition.deliverytime)}{$position.steelposition.deliverytime.title|escape:'html'}{/if}" class="max"></td>
<td><input type="text" id="pos{$position.steelposition_id}-notes-1" value="{$position.steelposition.notes|escape:'html'}" class="max"></td>
<td><input type="text" id="pos{$position.steelposition_id}-internal_notes-1" value="{$position.steelposition.internal_notes|escape:'html'}" class="max"></td>
<td>
    {if isset($position.steelposition.plateid)}
    {foreach name='plateid' from=$position.steelposition.plateid item=plateid}
    {$plateid}{if !$smarty.foreach.plateid.last}, {/if}
    {/foreach}
    {/if}
</td>
<td>
    {if isset($position.steelposition.location)}
    {foreach from=$position.steelposition.location item=location}
    {$location}
    {/foreach}
    {/if}
</td>
<td>{if isset($position.steelposition.biz)}<a href="/biz/{$position.steelposition.biz.id}">{$position.steelposition.biz.number_output|escape:'html'}</a>{/if}</td>
<td>
    {if isset($position.steelposition.attachments)}
        {foreach from=$position.steelposition.attachments item=attachment name="pa"}
            {if $smarty.foreach.pa.first}
                <a href="/picture/default/{$attachment.attachment.secret_name}/l/{$attachment.attachment.original_name}" rel="prettyPhoto[steelitem{$position.steelposition_id}]"><img src="/img/icons/picture.png" title="Position Pictures" alt="Position Pictures"></a>
            {else}
                <a href="/picture/default/{$attachment.attachment.secret_name}/l/{$attachment.attachment.original_name}" rel="prettyPhoto[steelitem{$position.steelposition_id}]"></a>
            {/if}
        {/foreach}
    {/if}
</td>