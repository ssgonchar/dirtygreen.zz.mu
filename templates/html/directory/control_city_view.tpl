<td>{$city.id}</td>
<td class="text-left">{$city.title}</td>
<td class="text-left">{$city.title1}</td>
<td class="text-left">{$city.title2}</td>
<td class="text-left">{$city.dialcode|escape:'html'}</td>
<td>
    {if empty($city.quick.companies_count)}<i>no</i>
    {else}<a href="/companies/filter/country:{$country_id};region:{$region_id};city:{$city.id}">{$city.quick.companies_count}</a>{/if}
</td>
<td>
    {if empty($city.quick.persons_count)}<i>no</i>
    {else}<a href="/persons/filter/country:{$country_id};region:{$region_id};city:{$city.id}">{$city.quick.persons_count}</a>{/if}
</td>
<td>
    {if empty($city.modified_by)}<i>no</i>
    {else}{$city.modified_at|date_human:false}<br>{if isset($city.modifier)}{$city.modifier.login}{/if}{/if}
</td>
<td>
    <a href="javascript: void(0);" onclick="city_action({$city.id}, 'edit');"><img src="/img/icons/pencil-small.png"></a>
    {if isset($city.quick) && empty($city.quick.companies_count) && empty($city.quick.persons_count)}
    <a href="javascript: void(0);" onclick="city_remove({$city.id});"><img src="/img/icons/cross-small.png"></a>
    {/if}
</td>
