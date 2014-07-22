
    <select style="width:100%">
        {foreach from=$mirrorlocations item=row}
            <option>{$row.location.title}</option>
        {/foreach}
    </select>
