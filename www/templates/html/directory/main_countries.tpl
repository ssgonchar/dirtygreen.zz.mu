<table class="list" width="100%" id="country-list">
    <tbody>
        <tr class="top-table">
            <th width="5%">Id</th>
            <th class="text-left">Country Name</th>
            <th class="text-left">Title1</th>
            <th class="text-left">Title2</th>
            <th>Alpha2</th>
            <th>Alpha3</th>
            <th>Code</th>
            <th>Dial Code</th>
            <th>Regions</th>
            <th>Companies</th>
            <th>Persons</th>
            <th width="5%">Primary</th>
            <th width="5%">Modified</th>
            <th>Actions</th>
        </tr>
        <tr id="tr-0">
            {include file='templates/html/directory/control_country_edit.tpl'}
        </tr>        
        {foreach from=$list item=row}
        <tr id="tr-{$row.country.id}">
            {include file='templates/html/directory/control_country_view.tpl' country=$row.country}
        </tr>
        {/foreach}
    </tbody>
</table>