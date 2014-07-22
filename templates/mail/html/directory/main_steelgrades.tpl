<table class="list" width="50%">
    <tbody>
        <tr class="top-table">
            <th width="5%">Id</th>
            <th width="30%">Title</th>
            <th>Group Alias</th>
            <th>BG Color</th>
            <th>Text Color</th>
            <th>Delete</th>
        </tr>
        {foreach from=$list item=row}
        <tr>
            <td>{$row.steelgrade.id}</td>
            <td><input type="text" name="title[{$row.steelgrade.id}]" value="{$row.steelgrade.title|escape:'html'}" class="max"></td>
            <td><input type="text" name="alias[{$row.steelgrade.id}]" value="{$row.steelgrade.alias|escape:'html'}" class="max"></td>
            <td><input type="text" name="bgcolor[{$row.steelgrade.id}]" value="{$row.steelgrade.bgcolor|escape:'html'}" class="max" style="background-color: {$row.steelgrade.bgcolor|escape:'html'};"></td>
            <td><input type="text" name="color[{$row.steelgrade.id}]" value="{$row.steelgrade.color|escape:'html'}" class="max"></td>
            <td><a href="javascript: void(0);" onclick="if (confirm('Am I sure?')) location.href='/directory/deletesteelgrade/{$row.steelgrade.id}';" class="delete">delete</a></td>
        </tr>
        {/foreach}
        <tr>
            <td>new</td>
            <td><input type="text" name="title[0]" value="" class="max"></td>
            <td><input type="text" name="alias[0]" value="" class="max"></td>
            <td><input type="text" name="bgcolor[0]" value="" class="max"></td>
            <td><input type="text" name="color[0]" value="" class="max"></td>
            <td></td>
        </tr>            
    </tbody>
</table>