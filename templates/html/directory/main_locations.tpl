<table class="list" width="50%">
    <tbody>
        <tr class="top-table">
            <th width="5%">Id</th>
            <th>Title</th>
            <th width="10%">Delete</th>
        </tr>
        {foreach from=$list item=row}
        <tr>
            <td>{$row.location.id}</td>
            <td><input type="text" name="title[{$row.location.id}]" value="{$row.location.title|escape:'html'}" style="width: 100%;"></td>
            <td><a href="javascript: void(0);" onclick="if (confirm('Am I sure?')) location.href='/directory/deletelocation/{$row.location.id}';" class="delete">delete</a></td>
        </tr>
        {/foreach}
        <tr>
            <td>new</td>
            <td><input type="text" name="title[0]" value="" style="width: 100%;"></td>
            <td></td>
        </tr>            
    </tbody>
</table>
