{if !empty($tasklist)}
    {foreach from=$tasklist item=row}
        <tr class="{if $row.status_id == '1'}active{else if $row.status_id == '2' || $row.status_id == '4'}success{else if $row.status_id == '3'}info{/if}" style="cursor: text;">
            <td class="hidden-status-id hidden">{$row.status_id}</td>
            <td id="task-{$row.task_id}" class="td-task-id text-center" style="cursor: pointer; width: 40px; font-size: 1.3em;" title="Click to select Task ID">{$row.task_id}</td>
            <td class="td-status-id text-center">
                {if $row.status_id == '1'}Waiting{else if $row.status_id == '2' || $row.status_id == '4'}In process{else if $row.status_id == '3'}Completed{/if}
            </td>
            <td class="td-title text-center">
                {$row.task.title}<br>
                <button type="button" class="btn btn-info make-ref btn-xs" data-container="body" data-toggle="popover" data-placement="right" data-content="Link copied to clipboard." data-ref="<ref message_id=566184>Ref. 04/08/2014 16:25:01</ref>">
                    copy REF
                </button>
                
            </td>
            <td class="td-description"> 
                {$row.task.description}
                {if $row.personal_notices}
                    <div class="personal-notices" style="border-top: solid 1px #ddd;"><i>Personal notes:</i><br>
                    {$row.personal_notices}</div>
                {/if}
            </td>
            <td class='td-start-data text-center' style="width: 7%;">{$row.data_start}</td>
            <td class='td-finish-data text-center' style="width: 7%;">{$row.data_finish}</td>
            <td class='td-budget-data text-center'><nobr>{$row.budget_time}</nobr></td>
            <td class="td-used-time text-center"><nobr>{$row.used_time}</nobr></td>
            <td class="td-biz-id text-center" style="cursor: pointer;">
                <a href="http://home.steelemotion.local/biz/{$row.task.biz_id}/blog">{$row.task.biz_title}</a><br> 
            </td>
        </tr>
    {/foreach}
{/if}
{if !empty($user_task_list)}
    {foreach from=$user_task_list item=row}
        <tr class="{if $row.status_id == '1'}active{else if $row.status_id == '2' || $row.status_id == '4'}success{else if $row.status_id == '3'}info{/if}" style="cursor: text;">
            <td class="hidden-status-id hidden">{$row.status_id}</td>
            <td id="task-{$row.task_id}" class="td-task-id text-center" style="cursor: pointer; width: 40px; font-size: 1.3em;" title="Click to select Task ID">{$row.task_id}</td>
            <td class="td-status-id text-center">
                {if $row.status_id == '1'}Waiting{else if $row.status_id == '2' || $row.status_id == '4'}In process{else if $row.status_id == '3'}Completed{/if}
            </td>
            <td class="td-title text-center">{$row.task.title}</td>
            <td class="td-description">
                {$row.task.description}
                {if $row.personal_notices}
                    <div class="personal-notices" style="border-top: solid 1px #ddd;"><i>Personal notes:</i><br>
                    {$row.personal_notices}</div>
                {/if}
            </td>
            <td class='td-start-data text-center' style="width: 7%;">{$row.data_start}</td>
            <td class='td-finish-data text-center' style="width: 7%;">{$row.data_finish}</td>
            <td class='td-budget-data text-center'><nobr>{$row.budget_time}</nobr></td>
            <td class="td-used-time text-center"><nobr>{$row.used_time}</nobr></td>
            <td class="td-biz-id text-center" style="cursor: pointer;">
                <a href="http://home.steelemotion.local/biz/{$row.task.biz_id}/blog">{$row.task.biz_title}</a><br> 
            </td>
        </tr>
    {/foreach}
{/if}