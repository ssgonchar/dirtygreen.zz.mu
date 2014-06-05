<table class="list" width="100%">
    <tbody>
        <tr class="top-table">
            <th width="5%">Id</th>
            <th>From</th>
            <th>Login</th>
            <th>Password</th>
            <th>Email</th>
            <th>Name</th>
            <th>Phone</th>
            <th>Skype</th>
            <th>Company</th>
            <th>Website</th>
            <th>Country</th>
            <th>Created</th>
            <th>Status</th>
        </tr>
        {foreach from=$list item=row}
        <tr>
            <td><a href="/regrequest/{$row.id}">{$row.id}</a></td>
            <td>{if $row.domain == 'se'}STEELemotion{else}PlatesAhead{/if}</td>
            <td>{$row.login|escape:'html'}</td>
            <td>{$row.password|escape:'html'}</td>
            <td>{$row.email|escape:'html'}</td>
            <td>{$row.first_name|escape:'html'} {$row.last_name|escape:'html'}</td>
            <td>{$row.phone|escape:'html'}</td>
            <td>{if empty($row.skype)}{''|undef}{else}{$row.skype|escape:'html'}{/if}</td>
            <td>{$row.company_name|escape:'html'}</td>
            <td>{if empty($row.skype)}{''|undef}{else}<a href="{$row.website|escape:'html'}">{$row.website|escape:'html'}</a>{/if}</td>
            <td>{$row.country.title|escape:'html'}</td>
            <td>{$row.created_at|date_human:false}</td>
            <td>
                {if empty($row.status)}
                <a href="/regrequest/{$row.id}">new</a>
                {elseif $row.status == 1}
                Accepted
                {else}
                Declined
                {/if}
            </td>
        </tr>
        {/foreach}
    </tbody>    
</table>
