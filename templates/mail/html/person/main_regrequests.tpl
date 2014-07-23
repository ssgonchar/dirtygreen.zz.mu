{if empty($list)}
    There are no requests for registration .
{else}
    <table class="list" width="100%">
        <tbody>
            <tr class="top-table">
                <th>Domain</th>
                <th>Country</th>
                <th>Company</th>
                <th>Website</th>
                <th>Person</th>
                <th>eMail</th>
                <th>Phone</th>
                <th>Skype</th>
                <th>Login</th>
                <th>Password</th>
                <th>Action</th>
            </tr>
            {foreach from=$list item=row}
            <tr>
                <td>{if $row.domain == 'se'}STEELemotion{else}PlatesAhead{/if}</td>
                <td>{$row.country.title}</td>
                <td>{$row.company_name}</td>
                <td>{if !empty($row.website)}<a href="http://{$row.website}">{$row.website}</a>{/if}</td>
                <td>{$row.title} {$row.first_name} {$row.last_name}</td>
                <td>{$row.email}{if $row.status_id == 0}<br><span style="color: #999;">not confirmed</span>{/if}</td>
                <td>{$row.phone}</td>
                <td>{$row.skype}</td>
                <td>{$row.login}</td>
                <td>{$row.password}</td>
                <td>
                    {if $row.status_id == 2}confirmed
                    {elseif $row.status_id == 3}declined
                    {else}
                        <img src="/img/icons/tick-circle.png" style="margin-right: 10px;" onclick="alert('in developing');">
                        <img src="/img/icons/cross-circle.png" onclick="alert('in developing');">
                    {/if}
                </td>
            </tr>
            {/foreach}
        </tbody>
    </table>
{/if}
