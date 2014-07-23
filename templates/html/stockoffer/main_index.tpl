{if empty($list)}Nothing was found on my request
{else}
    <table class="list search-target" width="100%">
        <tbody>
            <tr class="top-table">
                <th style="width: 30px;">Id</th>
                <th>Title</th>
                <th>Stock</th>
                <th>Location</th>
                <th>Validity</th>
                <th style="width: 200px;">PDF</th>
                <th style="width: 150px;">Last Modified</th>
                <th style="width: 30px;"></th>
                <th style="width: 30px;"></th>
            </tr>
            {foreach $list as $row}
            {$stockoffer = $row.stockoffer}
            <tr>
                <td onclick="location.href='/stockoffer/{$stockoffer.id}';">{$stockoffer.id}</td>
                <td onclick="location.href='/stockoffer/{$stockoffer.id}';">{$stockoffer.title|escape:'html'|undef}</td>
                <td onclick="location.href='/stockoffer/{$stockoffer.id}';">stock</td>
                <td onclick="location.href='/stockoffer/{$stockoffer.id}';">location</td>
                <td onclick="location.href='/stockoffer/{$stockoffer.id}';">{$stockoffer.validity|escape:'html'|undef}</td>
                {if !empty($stockoffer.pdf_attachment)}
                <td><a class="pdf" target="_blank" href="/file/{$stockoffer.pdf_attachment.secret_name}/{$stockoffer.pdf_attachment.original_name}">{$stockoffer.pdf_attachment.original_name}</a></td>
                {else}<td onclick="location.href='/stockoffer/{$stockoffer.id}';">{''|undef}</td>
                {/if}
                <td onclick="location.href='/stockoffer/{$stockoffer.id}';">{$stockoffer.modified_at|date_human}<br />by {$stockoffer.modifier.login|escape:'html'}</td>
                <td onclick="location.href='/stockoffer/{$stockoffer.id}/edit';"><img src="/img/icons/pencil-small.png" style="cursor: pointer" alt="Edit" title="Edit" /></td>
                <td onclick="if(!confirm('Am I sure ?'))return false;location.href='/stockoffer/{$row.stockoffer.id}/remove';">
                    <img src="/img/icons/cross-small.png" style="cursor: pointer" alt="Delete" title="Delete" />
                </td>
            </tr>
            {/foreach}
        </tbody>
    </table>
{/if}