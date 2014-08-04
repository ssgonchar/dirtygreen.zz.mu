<select class="form-control locations" name="form[location_ids][]" multiple>
    <option value=0>All</option>
    {if isset($locations)}
        {foreach from=$locations item=row}
            <option value="{$row.location.id}" >{$row.location.title|escape:'html'}</option>
        {/foreach}
    {/if}
</select>