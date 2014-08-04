<select class="form-control stockholders" name="form[stockholders_ids]" multiple>
    <option value=0>All</option>
    {if isset($list)}
        {foreach from=$list item=row}
            <option value="{$row.stockholder_id}" >{$row.stockholder.doc_no_full|escape:'html'}</option>
        {/foreach}
    {/if}
  
</select>