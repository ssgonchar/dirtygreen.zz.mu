<td><a href="javascript: void(0);" onclick="position_show_items({$position.steelposition_id});"><img id="img-{$position.steelposition_id}" src="/img/icons/minus.png" title="Show items" alt="Show items"></a></td>
<td>
    <input type="checkbox" value="{$position.steelposition_id}" class="cb-row-position" onchange="calc_total('position');">&nbsp;
    <a href="javascript: void(0);" onclick="show_position_actions(this, {$position.steelposition_id|escape:'html'});">{$position.steelposition_id|escape:'html'}</a>
</td>
<td>{$position.steelposition.steelgrade.title|escape:'html'}</td>
<td class="text-center">{$position.steelposition.thickness|escape:'html'}</td>
<td class="text-center">{$position.steelposition.width|escape:'html'}</td>
<td class="text-center">{$position.steelposition.length|escape:'html'}</td>
<td class="text-center">{$position.steelposition.unitweight|escape:'html'|string_format:'%.2f'}</td>
<td class="text-center" id="position-qtty-{$position.steelposition_id}">{$position.steelposition.qtty|escape:'html'|string_format:'%d'}</td>
<td class="text-center" id="position-weight-{$position.steelposition_id}">{$position.steelposition.weight|escape:'html'|string_format:'%.2f'}</td>
<td class="text-center">{$position.steelposition.price|escape:'html'|string_format:'%.2f'}</td>
<td class="text-center" id="position-value-{$position.steelposition_id}">{$position.steelposition.value|escape:'html'|string_format:'%.2f'}</td>
<td>{if isset($position.steelposition.deliverytime)}{$position.steelposition.deliverytime.title|escape:'html'}{/if}</td>
<td>{$position.steelposition.notes|escape:'html'}</td>
<td>{$position.steelposition.internal_notes|escape:'html'}</td>
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
