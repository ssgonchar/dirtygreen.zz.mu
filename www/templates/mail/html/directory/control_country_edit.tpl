{if isset($country)}
    <td>{$country.id}</td>
    <td><input type="text" id="title-{$country.id}" class="max" value="{$country.title|escape:'html'}"></td>
    <td><input type="text" id="title1-{$country.id}" class="max" value="{$country.title1|escape:'html'}"></td>
    <td><input type="text" id="title2-{$country.id}" class="max" value="{$country.title2|escape:'html'}"></td>
    <td><input type="text" id="alpha2-{$country.id}" class="max" value="{$country.alpha2|escape:'html'}"></td>
    <td><input type="text" id="alpha3-{$country.id}" class="max" value="{$country.alpha3|escape:'html'}"></td>
    <td><input type="text" id="code-{$country.id}" class="max" value="{$country.code|escape:'html'}"></td>
    <td><input type="text" id="dialcode-{$country.id}" class="max" value="{$country.dialcode|escape:'html'}"></td>
    <td>{if empty($country.quick.regions_count)}<i>no</i>{else}<a href="/directory/regions/{$country.id}">{$country.quick.regions_count}</a>{/if}</td>
    <td>{if empty($country.quick.companies_count)}<i>no</i>{else}<a href="/companies/filter/country:{$country.id}">{$country.quick.companies_count}</a>{/if}</td>
    <td>{if empty($country.quick.persons_count)}<i>no</i>{else}<a href="/persons/filter/country:{$country.id}">{$country.quick.persons_count}</a>{/if}</td>
    <td><input type="checkbox" id="is_primary-{$country.id}" value="1"{if !empty($country.is_primary)} checked="checked"{/if}></td>
    <td>
        {if empty($country.modified_by)}
            <i>no</i>
        {else}
            {$country.modified_at|date_human:false}
            {if isset($country.modifier)}{$country.modifier.login}{/if}<br>
        {/if}
    </td>
    <td>
        <a href="javascript:void(0);" onclick="country_save({$country.id});"><img src="/img/icons/tick-small.png"></a>
        <a href="javascript:void(0);" onclick="country_action({$country.id}, 'view');"><img src="/img/icons/slash-small.png"></a>
    </td>
{else}
    <td>new</td>
    <td><input type="text" id="title-0" class="max" value=""></td>
    <td><input type="text" id="title1-0" class="max" value=""></td>
    <td><input type="text" id="title2-0" class="max" value=""></td>
    <td><input type="text" id="alpha2-0" class="max" value=""></td>
    <td><input type="text" id="alpha3-0" class="max" value=""></td>
    <td><input type="text" id="code-0" class="max" value=""></td>
    <td><input type="text" id="dialcode-0" class="max" value=""></td>
    <td>--</td>
    <td>--</td>
    <td>--</td>
    <td><input type="checkbox" id="is_primary-0" value="1"></td>
    <td>--</td>
    <td><img src="/img/icons/plus-small.png" onclick="country_save(0);" style="cursor: pointer;"></td>
{/if}
