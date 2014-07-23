<table width="100%">
    <tr>
        <td width="70%" class="text-top">
            {if !empty($form.description)}<div style="margin-bottom: 20px;">{$form.description|nl2br}</div>{/if}            
            {if !empty($form.map_data)}{$form.map_data}{/if}
        </td>
        <td class="text-top">
            <h3>Countries</h3>
            <table id="countries" class="form">
                {foreach name="countries" from=$countries item=row}
                <tr>
                    <td>{$row.country.title|escape:'html'}</td>
                </tr>
                {/foreach}
            </table>
        </td>
    </tr>
</table>