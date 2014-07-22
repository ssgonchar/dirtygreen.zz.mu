<td>{$activity.id}</td>
<td class="text-left"><a href="/directory/activities/{$activity.id}">{$activity.title|escape:'html'}</a></td>
<td>
    {if empty($activity.modified_by)}
        <i>no</i>
    {else}
        {$activity.modified_at|date_human:false}
        {if isset($activity.modifier)}<br>{$activity.modifier.login}{/if}
    {/if}
</td>
<td>
    <a href="javascript: void(0);" onclick="activity_action({$activity.id}, 'edit');"><img src="/img/icons/pencil-small.png"></a>
    {if isset($activity.quick) && empty($activity.quick.activities_count)}
    <a href="javascript: void(0);" onclick="activity_remove({$activity.id});"><img src="/img/icons/cross-small.png"></a>
    {/if}
</td>
