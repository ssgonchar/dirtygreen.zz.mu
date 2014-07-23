
    <select style="width:100%">
        {foreach from=$mirrordelivery item=row}
            <option>{$row.deliverytime.title}</option>
        {/foreach}
    </select>
