<table class="form">
    <tr>
        <td class="form-td-title">Country</td>
        <td>
            <select id="sel-country" class="normal">
                <option value="0">--</option>
                {section name=i loop=$countries}
                {if !$smarty.section.i.first && $countries[i.index_prev].country.is_primary != $countries[i].country.is_primary}<option value="0">--</option>{/if}
                <option value="{$countries[i].country.id}"{if $countries[i].country.id == $country_id} selected="selected"{/if}>{$countries[i].country.title|escape:'html'}</option>                
                {/section}
            </select>
            <input type="hidden" id="hid-country-id" value="{$country_id}">
        </td>
        <td><input type="button" class="btn100o" onclick="get_regions_list();" value="Select"></td>
    </tr>
</table>
<div class="pad"></div>

<table class="list" width="100%" id="region-list">
    <tbody>
        <tr class="top-table">
            <th width="5%">Id</th>
            <th class="text-left">Country Name</th>
            <th class="text-left">Title1</th>
            <th class="text-left">Title2</th>
            <th>Cities</th>
            <th>Companies</th>
            <th>Persons</th>
            <th width="5%">Modified</th>
            <th>Actions</th>
        </tr>
        <tr id="tr-0">
            {include file='templates/html/directory/control_region_edit.tpl'}
        </tr>        
        {foreach from=$list item=row}
        <tr id="tr-{$row.region.id}">
            {include file='templates/html/directory/control_region_view.tpl' region=$row.region}
        </tr>
        {/foreach}
    </tbody>
</table>
