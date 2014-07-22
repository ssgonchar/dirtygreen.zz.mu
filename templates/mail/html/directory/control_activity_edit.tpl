{if isset($activity)}
    <td>{$activity.id}</td>
    <td><input type="text" id="title-{$activity.id}" class="max" value="{$activity.title|escape:'html'}"></td>
    <td>
        {if empty($activity.modified_by)}
            <i>no</i>
        {else}
            {$activity.modified_at|date_human:false}
            {if isset($activity.modifier)}<br>{$activity.modifier.login}{/if}
        {/if}
    </td>
    <td>
        <a href="javascript:void(0);" onclick="activity_save({$activity.id});"><img src="/img/icons/tick-small.png"></a>
        <a href="javascript:void(0);" onclick="activity_action({$activity.id}, 'view');"><img src="/img/icons/slash-small.png"></a>
    </td>
{else}
    <td>new</td>
    <td><input type="text" id="title-0" class="max" value=""></td>
    <td>--</td>
    <td><img src="/img/icons/plus-small.png" onclick="activity_save(0);" style="cursor: pointer;"></td>
{/if}
