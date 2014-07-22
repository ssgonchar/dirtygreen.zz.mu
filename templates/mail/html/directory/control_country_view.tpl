<td>{$country.id}</td>
<td class="text-left"><a href="/directory/regions/{$country.id}">{$country.title|escape:'html'}</a></td>
<td class="text-left">{$country.title1|escape:'html'}</td>
<td class="text-left">{$country.title2|escape:'html'}</td>
<td>{$country.alpha2|escape:'html'}</td>
<td>{$country.alpha3|escape:'html'}</td>
<td>{$country.code|escape:'html'}</td>
<td>{$country.dialcode|escape:'html'}</td>
<td>{if empty($country.quick.regions_count)}<i>no</i>{else}<a href="/directory/regions/{$country.id}">{$country.quick.regions_count}</a>{/if}</td>
<td>{if empty($country.quick.companies_count)}<i>no</i>{else}<a href="/companies/filter/country:{$country.id}">{$country.quick.companies_count}</a>{/if}</td>
<td>{if empty($country.quick.persons_count)}<i>no</i>{else}<a href="/persons/filter/country:{$country.id}">{$country.quick.persons_count}</a>{/if}</td>
<td>{if empty($country.is_primary)}<i>no</i>{else}yes{/if}</td>
<td>
    {if empty($country.modified_by)}
        <i>no</i>
    {else}
        {$country.modified_at|date_human:false}
        {if isset($country.modifier)}{$country.modifier.login}{/if}<br>
    {/if}
</td>
<td>
    <a href="javascript: void(0);" onclick="country_action({$country.id}, 'edit');"><img src="/img/icons/pencil-small.png"></a>
    {if isset($country.quick) && empty($country.quick.companies_count) && empty($country.quick.persons_count) && empty($country.quick.regions_count)}
    <a href="javascript: void(0);" onclick="country_remove({$country.id});"><img src="/img/icons/cross-small.png"></a>
    {/if}
</td>
