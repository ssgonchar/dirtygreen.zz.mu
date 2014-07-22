{if !empty($list)}
<table class="list" width="100%">
    <tr class="top-table">
        <th style="width: 15%;">From</th>
        <th style="width: 15%;">To</th>
        <th style="width: 15%;">Subject</th>
        <th style="width: 15%;">Text</th>
        <th>Tags</th>
        <th style="width: 40px;"></th>
    </tr>
    {foreach $list as $row}
    <tr>
        <td onclick="location.href='/email/filter/{$row.efilter.id}/edit';">{if !empty($row.efilter.params_array.from)}{$row.efilter.params_array.from|escape:'html'}{else}{''|undef}{/if}</td>
        <td onclick="location.href='/email/filter/{$row.efilter.id}/edit';">{if !empty($row.efilter.params_array.to)}{$row.efilter.params_array.to|escape:'html'}{else}{''|undef}{/if}</td>
        <td onclick="location.href='/email/filter/{$row.efilter.id}/edit';">{if !empty($row.efilter.params_array.subject)}{$row.efilter.params_array.subject|escape:'html'}{else}{''|undef}{/if}</td>
        <td onclick="location.href='/email/filter/{$row.efilter.id}/edit';">{if !empty($row.efilter.params_array.text)}{$row.efilter.params_array.text|escape:'html'}{else}{''|undef}{/if}</td>
        <td>
            {if !empty($row.efilter.tags_array)}
            {foreach $row.efilter.tags_array as $item}
            <div id="{$item.object_alias|escape:'html'}-{$item.object_id}" style="margin: 5px; padding: 0 0 0 30px; text-align: left;">
                <a class="tag-{if in_array($item.object_alias, array('biz', 'company', 'order', 'person'))}{$item.object_alias}{else}document{/if}" style="vertical-align: top; margin-right: 3px;" href="/{$item.object_alias|escape:'html'}/{$item.object_id}" target="_blank">{$item.title|escape:'html'}</a>
            </div>
            {/foreach}
            {/if}
        </td>
        <td>
            <img src="/img/icons/pencil-small.png" style="cursor: pointer" alt="Edit" title="Edit" onclick="location='/email/filter/{$row.efilter.id}/edit';" />
            <img src="/img/icons/cross.png" style="cursor: pointer" alt="Delete" title="Delete" onclick="if(!confirm('Am I sure ?'))return false;location='/email/filter/{$row.efilter.id}/delete';" />
        </td>
    </tr>
    {/foreach}
</table>
{else}There are no filters
{/if}