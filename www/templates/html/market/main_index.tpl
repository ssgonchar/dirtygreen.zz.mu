{if empty($list)}
    {if isset($filter)}There are no markets registered{/if}
{else}
    <table class="list search-target" width="100%">
        <tbody>
            <tr class="top-table">
                <th class="text-left">Title</th>
                <th>Countries</th>
                <th>BIZs</th>
                <th width="10%">Modified</th>
                <th width="8%">Edit</th>
                <th width="8%">Remove</th>
            </tr>
            {foreach from=$list item=row}
            <tr>
                <td class="text-left"><a href="/market/{$row.market.id}">{$row.market.title|escape:'html'}</a></td>
                <td>
                    {if empty($row.market.quick.countries_count)}<i>not set</i>
                    {else}{number value=$row.market.quick.countries_count e0='countries' e1='country' e2='countries'}{/if}
                </td>
                <td>
                    {if empty($row.market.quick.bizes_count)}<i>not set</i>
                    {else}{number value=$row.market.quick.bizes_count e0='BIZs' e1='BIZ' e2='BIZs'}{/if}
                </td>
                <td>{if empty($row.market.modified_at)}<i>not set</i>{else}{$row.market.modified_at|date_human:false}<br>{if isset($row.market.modifier)}{$row.market.modifier.login}{else}<i>unknown</i>{/if}{/if}</td>
                <td><a class="edit" href="/market/{$row.market.id}/edit">edit</a></td>
                <td>
                    {if isset($row.market.quick) && empty($row.market.quick.bizes_count)}
                    <a class="delete" href="/market/{$row.market.id}/remove">delete</a>
                    {else}
                    <i>can't be removed</i>
                    {/if}
                </td>
            </tr>
            {/foreach}
        </tbody>
    </table>
{/if}