<select id="steelgrade-list"{if isset($class)} class="{$class}"{/if}{if isset($style)} style="{$style}"{/if} name="form[steelgrade]">
    <option value=0>--</option>
    {if isset($steelgrade_list) && !empty($steelgrade_list)}
        {foreach from=$steelgrade_list item=row}
            <option value="{$row.steelgrade.id}" {if isset($steelgrade_id) && $steelgrade_id == $row.steelgrade.id}selected=selected{/if}>{$row.steelgrade.title|escape:'html'}</option>
        {/foreach}
    {/if}  
</select>