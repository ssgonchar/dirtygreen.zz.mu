{if isset($region)}
    <td>{$region.id}</td>
    <td><input type="text" id="title-{$region.id}" class="max" value="{$region.title}"></td>
    <td><input type="text" id="title1-{$region.id}" class="max" value="{$region.title1}"></td>
    <td><input type="text" id="title2-{$region.id}" class="max" value="{$region.title2}"></td>
    <td>{if empty($region.quick.cities_count)}<i>no</i>{else}{$region.quick.cities_count}{/if}</td>
    <td>{if empty($region.quick.companies_count)}<i>no</i>{else}{$region.quick.companies_count}{/if}</td>
    <td>{if empty($region.quick.persons_count)}<i>no</i>{else}{$region.quick.persons_count}{/if}</td>
    <td>
        {if empty($region.modified_by)}<i>no</i>
        {else}{$region.modified_at|date_human:false}<br>{if isset($region.modifier)}{$region.modifier.login}{/if}{/if}
    </td>
    <td>
        <a href="javascript:void(0);" onclick="region_save({$region.id});"><img src="/img/icons/tick-small.png"></a>
        <a href="javascript:void(0);" onclick="region_action({$region.id}, 'view');"><img src="/img/icons/slash-small.png"></a>
    </td>
{else}
    <td>new</td>
    <td><input type="text" id="title-0" class="max" value=""></td>
    <td><input type="text" id="title1-0" class="max" value=""></td>
    <td><input type="text" id="title2-0" class="max" value=""></td>
    <td>--</td>    
    <td>--</td>
    <td>--</td>
    <td>--</td>
    <td><img src="/img/icons/plus-small.png" onclick="region_save(0);" style="cursor: pointer;"></td>
{/if}
