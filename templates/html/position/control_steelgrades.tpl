<select id="steelgrade-list"{if isset($class)} class="{$class}"{/if}{if isset($style)} style="{$style}"{/if} name="form[steelgrade]" style="width:100%;" multiple class="chosen-select">
    <option value=0>All</option>
    {if isset($steelgrade_list) && !empty($steelgrade_list)}
        {foreach from=$steelgrade_list item=row}
            <option value="{$row.steelgrade.id}" {if isset($steelgrade_id) && $steelgrade_id == $row.steelgrade.id}selected=selected{/if}><font color={$row.steelgrade.bgcolor}>{$row.steelgrade.title|escape:'html'}</font></option>
        {/foreach}
    {/if}  
</select>