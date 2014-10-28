{*debug*}
{if !empty($active_tasks_list)}
    {foreach from=$active_tasks_list item=row}
        <tr class="{if $row.active_task !== 'No active task'}success{else if}active{/if}" style="cursor: text;">
            <!--<td class="hidden-status-id hidden">{*$row.status_id*}</td>-->
            <td class="td-user-name text-center" style="cursor: pointer;">{$row.user_name} <span class="glyphicon glyphicon-ok" style="display: none;" title="Click to check user"></span></td>
            <td class="text-center">{if $row.active_task !== 'No active task'}{$row.active_task.task_id}{/if}</td>
            <td class="td-title text-center">{if $row.active_task !== 'No active task'}{$row.active_task.task.title}{/if}</td>
            <td class="td-description">
                {if $row.active_task !== 'No active task'}
                    {$row.active_task.task.description}
                    {if $row.active_task.personal_notices}
                        <div class="personal-notices" style="border-top: solid 1px #ddd;"><i>Personal notes:</i><br>
                        {$row.active_task.personal_notices}</div>
                    {/if}
                {else if}
                    No active task
                {/if}
            </td>
            <td class='td-start-data text-center' style="width: 7%;">{if $row.active_task !== 'No active task'}{$row.active_task.data_start}{/if}</td>
            <td class='td-finish-data text-center' style="width: 7%;">{if $row.active_task !== 'No active task'}{$row.active_task.data_finish}{/if}</td>
            <td class='td-budget-data text-center'><nobr>{if $row.active_task !== 'No active task'}{$row.active_task.budget_time}{/if}</nobr></td>
            <td class="td-used-time text-center"><nobr>{if $row.active_task !== 'No active task'}{$row.active_task.used_time}{/if}</nobr></td>
            <td class="td-biz-id text-center" style="cursor: pointer;">
                {if $row.active_task !== 'No active task'}
                    <a href="http://home.steelemotion.local/biz/{$row.active_task.task.biz_id}/blog">{$row.active_task.biz_title}</a><br>
                {/if}
                
            </td>
        </tr>
    {/foreach}
{/if}