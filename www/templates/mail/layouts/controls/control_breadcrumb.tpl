{if $smarty.request.arg != ''}
<ul id="nav"> 
    <li><a href="/">Dashboard</a></li>
    {foreach name=breadcrumb from=$breadcrumb item=row}
    {if !$smarty.foreach.breadcrumb.last}
    <li><a href="{$row.url}">{$row.name}</a></li>    
    {/if}
    {/foreach}
</ul>
{/if}