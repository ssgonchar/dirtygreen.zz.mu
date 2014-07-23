<td>{$region.id}</td>
<td class="text-left"><a href="/directory/cities/{$region.id}">{$region.title}</a></td>
<td class="text-left">{$region.title1}</td>
<td class="text-left">{$region.title2}</td>
<td>
    {if empty($region.quick.cities_count)}<i>no</i>
    {else}<a href="/directory/cities/{$region.id}">{$region.quick.cities_count}{*number value=$region.quick.cities_count e0='cities' e1='city' e2='cities'*}</a>{/if}
</td>
<td>
    {if empty($region.quick.companies_count)}<i>no</i>
    {else}<a href="/companies/filter/country:{$country_id};region:{$region.id}">{$region.quick.companies_count}</a>{*number value=$region.quick.companies_count e0='companies' e1='company' e2='companies'*}{/if}
</td>
<td>
    {if empty($region.quick.persons_count)}<i>no</i>
    {else}<a href="/persons/filter/country:{$country_id};region:{$region.id}">{$region.quick.persons_count}</a>{*number value=$region.quick.persons_count e0='persons' e1='person' e2='persons'*}{/if}
</td>
<td>
    {if empty($region.modified_by)}<i>no</i>
    {else}{$region.modified_at|date_human:false}<br>{if isset($region.modifier)}{$region.modifier.login}{/if}{/if}
</td>
<td>
    <a href="javascript: void(0);" onclick="region_action({$region.id}, 'edit');"><img src="/img/icons/pencil-small.png"></a>
    {if isset($region.quick) && empty($region.quick.companies_count) && empty($region.quick.persons_count) && empty($region.quick.cities_count)}
    <a href="javascript: void(0);" onclick="region_remove({$region.id});"><img src="/img/icons/cross-small.png"></a>
    {/if}
</td>
