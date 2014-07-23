{if !empty($tasklist)}
    {foreach from=$tasklist item=row}
        <tr {if $row.status_id == '1'}class="active"{else if $row.status_id == '2'}class="success"{else if $row.status_id == '3'}class="info"{/if} style="cursor: text;">
            <td class="td-task-id text-center" id='task-{$row.task_id}' style="cursor: pointer; width: 40px;" title="Click to select Task ID">{$row.task_id}</td>
            <td>
                {if $row.status_id == '1'}Waiting{else if $row.status_id == '2'}In process{else if $row.status_id == '3'}Completed{/if}
            </td>
            <td>{$row.task.title}</td>
            <td class="td-description">
                {$row.task.description}
                {if $row.personal_notices}
                    <div style="border-top: solid 1px #ddd;"><i>Personal notes:</i><br>
                    {$row.personal_notices}</div>
                {/if}
            </td>
            <td class='td-start-data text-center'>{$row.data_start}</td>
            <td class='td-finish-data text-center'>{$row.data_finish}</td>
            <td class='td-budget-data'><nobr>{$row.budget_time}</nobr></td>
            <td>{$row.used_time}</td>
            <td style="cursor: pointer;">
                <a>{$row.task.biz_id}</a><br>
            </td>
        </tr>
    {/foreach}
{/if}