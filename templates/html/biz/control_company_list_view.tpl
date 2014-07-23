    {if !empty($rowset)}
    {foreach from=$rowset item=row}
        <nobr> <a href="/company/{$row.company.id}" class="btn btn-primary btn-xs" style="margin: 0px 0px 5px 0px;">{$row.company.title}</a> </nobr> 
    {/foreach}
    {else}
        <i>not defined</i>
    {/if}