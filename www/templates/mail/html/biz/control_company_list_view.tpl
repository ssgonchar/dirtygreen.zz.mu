<table class="form">
    {if !empty($rowset)}
    {foreach from=$rowset item=row}
    <tr>
        <td><a href="/company/{$row.company.id}">{$row.company.title}</a></td>
    </tr>
    {/foreach}
    {else}
    <tr>
        <td><i>not defined</i></td>
    </tr>
    {/if}
</table>