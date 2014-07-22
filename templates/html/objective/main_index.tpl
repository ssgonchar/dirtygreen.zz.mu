<table width="100%">
    <tr height="32px">
        <td class="form-td-title">Year : </td>
        <td>
            {foreach name='year' from=$years item=row}
            &nbsp;&nbsp;{if $year == $row.year}<b>{$row.year}</b>{else}<a href="/objectives/{$row.year}">{$row.year}</a>{/if}
            {/foreach}
        </td>
    </tr>
    <tr height="32px">
        <td class="form-td-title">Quarter : </td>
        <td>&nbsp;&nbsp;{if $quarter == 0}<b>Entire Year</b>{else}<a href="/objectives/{$year}">Entire Year</a>{/if}
            {foreach name='quarter' from=$quarters item=row}
            &nbsp;&nbsp;{if $quarter == $row.quarter}<b>{$row.quarter|quarter}</b>{else}<a href="/objectives/{$year}/{$row.quarter}">{$row.quarter|quarter}</a>{/if}
            {/foreach}        
        </td>
    </tr>    
</table>
<div class="pad"></div>

{if empty($list)}
    {if isset($filter)}Nothing was found on my request{/if}
{else}
    <table class="list search-target" width="100%">
        <tbody>
            <tr class="top-table">
                <th width="8%">Year</th>
                <th width="8%">Quarter</th>
                <th class="text-left">Title</th>
                <th width="10%">Modified</th>
                <th width="8%">Edit</th>
                <th width="8%">Remove</th>
            </tr>
            {foreach from=$list item=row}
            <tr>
                <td>{$row.objective.year}</td>
                <td>{if empty($row.objective.quarter)}<i>not set</i>{else}{$row.objective.quarter|quarter}{/if}</td>
                <td class="text-left"><a href="/objective/{$row.objective.id}">{$row.objective.title|escape:'html'}</a></td>
                <td>{$row.objective.modified_at|date_human:false}<br>{if isset($row.objective.modifier)}{$row.objective.modifier.login}{else}<i>unknown</i>{/if}</td>
                <td><a class="edit" href="/objective/{$row.objective.id}/edit">edit</a></td>
                <td>
                    {if isset($row.objective.quick) && empty($row.objective.quick.bizes_count)}
                    <a class="delete" href="javascript:void(0);" onclick="if (confirm('Remove Objective?')) location.href='/objective/{$row.objective.id}/remove';">delete</a>
                    {else}
                    <i>can't be removed</i>
                    {/if}
                </td>
            </tr>
            {/foreach}
        </tbody>
    </table>
{/if}
