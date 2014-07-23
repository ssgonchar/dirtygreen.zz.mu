<table class="list" width="50%">
    <tbody>
        <tr class="top-table">
            <th width="5%">Id</th>
            <th>Title</th>
            <th width="5%">Delete</th>
        </tr>
        {foreach from=$list item=row}
        <tr>
            <td>{$row.department.id}</td>
            <td><input type="text" name="title[{$row.department.id}]" value="{$row.department.title|escape:'html'}" class="max"></td>
            <td><a href="javascript: void(0);" onclick="if (confirm('Am I sure?')) location.href='/directory/deletedepartment/{$row.department.id}';" class="delete">delete</a></td>
        </tr>
        {/foreach}
        <tr>
            <td>new</td>
            <td><input type="text" name="title[0]" value="" class="max"></td>
            <td></td>
        </tr>            
    </tbody>
</table>