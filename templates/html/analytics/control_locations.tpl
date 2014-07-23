<select class="form-control locations" name="sent_location_ids">
    <option value=0>All</option>
    {if isset($locations)}
        {foreach from=$locations item=row}
            <option value="{$row.id}" >{$row.name|escape:'html'}</option>
        {/foreach}
    {/if}
  
</select>