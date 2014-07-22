<table class="form">
    <tr>
        <td>Activity : </td>
        <td>
            <select id="sel-activity" name="parent_id" class="wide">
                <option value="0">--</option>
                {foreach from=$activities item=row}
                <option value="{$row.activity.id}"{if $parent_id == $row.activity.id} selected="selected"{/if}>{if $row.activity.level > 0}&nbsp;{repeat symbol='&middot;&nbsp;' count=$row.activity.level}{/if}{$row.activity.title|escape:'html'}</option>
                {/foreach}
            </select>
            <input type="hidden" id="hid-parent-id" value="{$parent_id}">
        </td>
        <td><input type="button" class="btn100o" onclick="get_activities_list();" value="Select"></td>
    </tr>
</table>
<div class="pad"></div>

<table class="list" width="50%" id="activities-list">
    <tbody>
        <tr class="top-table">
            <th width="5%">Id</th>
            <th class="text-left">Title</th>
            <th>Modified</th>
            <th width="5%">Actions</th>
        </tr>
        <tr id="tr-0">
            {include file='templates/html/directory/control_activity_edit.tpl'}
        </tr>        
        {foreach from=$list item=row}
        <tr id="tr-{$row.activity.id}">
            {include file='templates/html/directory/control_activity_view.tpl' activity=$row.activity}
        </tr>        
        {/foreach}
    </tbody>
</table>

{*
<tr id="">
    <td>{$row.activity.id}</td>
    <td><input type="text" name="title[{$row.activity.id}]" value="{$row.activity.title|escape:'html'}" class="max"></td>
    <td><a href="javascript: void(0);" onclick="if (confirm('Am I sure?')) location.href='/directory/deleteactivity/{$row.activity.id}';" class="delete">delete</a></td>
</tr>
*}