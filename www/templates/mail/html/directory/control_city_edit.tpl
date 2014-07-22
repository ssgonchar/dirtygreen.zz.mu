{if isset($city)}
    <td>{$city.id}</td>
    <td><input type="text" id="title-{$city.id}" class="max" value="{$city.title}"></td>
    <td><input type="text" id="title1-{$city.id}" class="max" value="{$city.title1}"></td>
    <td><input type="text" id="title2-{$city.id}" class="max" value="{$city.title2}"></td>
    <td><input type="text" id="dialcode-{$city.id}" class="max" value="{$city.dialcode|escape:'html'}"></td>
    <td>{if empty($city.quick.companies_count)}<i>no</i>{else}{$city.quick.companies_count}{/if}</td>
    <td>{if empty($city.quick.persons_count)}<i>no</i>{else}{$city.quick.persons_count}{/if}</td>
    <td>
        {if empty($city.modified_by)}<i>no</i>
        {else}{$city.modified_at|date_human:false}<br>{if isset($city.modifier)}{$city.modifier.login}{/if}{/if}
    </td>
    <td>
        <a href="javascript:void(0);" onclick="city_save({$city.id});"><img src="/img/icons/tick-small.png"></a>
        <a href="javascript:void(0);" onclick="city_action({$city.id}, 'view');"><img src="/img/icons/slash-small.png"></a>
    </td>
{else}
    <td>new</td>
    <td><input type="text" id="title-0" class="max" value=""></td>
    <td><input type="text" id="title1-0" class="max" value=""></td>
    <td><input type="text" id="title2-0" class="max" value=""></td>
    <td><input type="text" id="dialcode-0" class="max" value=""></td>
    <td>--</td>
    <td>--</td>
    <td>--</td>
    <td><img src="/img/icons/plus-small.png" onclick="city_save(0);" style="cursor: pointer;"></td>
{/if}
