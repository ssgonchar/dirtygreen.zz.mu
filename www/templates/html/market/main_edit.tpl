<table width="100%">
    <tr>
        <td width="70%" class="text-top">
            <table class="form" width="98%">
                <tr>
                    <td class="form-td-title-b">Title : </td>
                    <td><input type="text" name="form[title]" class="max"{if isset($form.title) && !empty($form.title)} value="{$form.title|escape:'html'}"{/if}></td>
                </tr>    
                <tr>
                    <td class="form-td-title text-top">Description : </td>
                    <td class="text-top"><textarea id="description" name="form[description]" class="max" rows="10">{if isset($form.description) && !empty($form.description)}{$form.description|escape:'html'}{/if}</textarea></td>
                </tr>    
                <tr>
                    <td class="form-td-title text-top">Map Data : </td>
                    <td class="text-top"><textarea name="form[map_data]" class="max" rows="5">{if isset($form.map_data) && !empty($form.map_data)}{$form.map_data|escape:'html'}{/if}</textarea></td>
                </tr>        
            </table>
        </td>
        <td class="text-top">
            <h3>Countries</h3>
            <table id="countries" class="form">
                {foreach name="countries" from=$countries item=row}
                <tr id="country-{$row.country.id}">
                    <td><input type="hidden" class="m-countries" name="countries[{$smarty.foreach.countries.index}][country_id]" value="{$row.country.id}">{$row.country.title|escape:'html'}</td>
                    <td><img src="/img/icons/cross-circle.png" onclick="remove_country({$row.country.id});"></td>
                </tr>
                {/foreach}
                <tr>
                    <td>
                        <select id="new-country" class="normal">
                            <option value="0">--</option>
                            {foreach from=$countries_list item=row}
                            <option value="{$row.country.id}">{$row.country.title|escape:'html'}</option>
                            {/foreach}
                        </select>
                    </td>
                    <td><img src="/img/icons/plus-circle.png" onclick="add_country({$row.country.id});"></td>
                </tr>                
            </table>
            <input type="hidden" id="country-index" value="{count($countries)}">
        </td>
    </tr>
</table>