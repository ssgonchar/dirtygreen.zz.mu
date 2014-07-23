{if empty($list)}Nothing was found on my request
{else}
    <table class="list  search-target" width="100%">
        <tr class="top-table">
            <th>No</th>
            <th>Owner</th>
            <th>Date</th>
            <th>Buyer</th>
            <th>Delivery Point</th>
            <th>Qtty,<br>pcs</th>
            <th>Weight,<br>Ton</th>
            <th>Weighed Weight,<br>Ton</th>
            <th>Modified</th>
            <th>Pdf</th>
            <th style="width: 16px;"></th>
        </tr>
        {foreach $list as $row}
        <tr>
            <td onclick="location='/ddt/{$row.ddt.id}';">{if empty($row.ddt.number)}{''|undef}{else}{$row.ddt.doc_no|escape:'html'}{/if}</td>
            <td onclick="location='/ddt/{$row.ddt.id}';">{if isset($row.ddt.owner)}{$row.ddt.owner.title_trade}{else}{''|undef}{/if}</td>
            <td onclick="location='/ddt/{$row.ddt.id}';">{$row.ddt.date|date_format:'%d.%m.%y'|undef}</td>
            <td onclick="location='/ddt/{$row.ddt.id}';">{if !empty($row.ddt.buyer)}{$row.ddt.buyer|escape:'html'}{else}{''|undef}{/if}</td>
            <td onclick="location='/ddt/{$row.ddt.id}';">{if !empty($row.ddt.delivery_point)}{$row.ddt.delivery_point|escape:'html'}{else}{''|undef}{/if}</td>
            <td onclick="location='/ddt/{$row.ddt.id}';">{$row.ddt.total_qtty}</td>
            <td onclick="location='/ddt/{$row.ddt.id}';">{$row.ddt.total_weight|string_format:'%.3f'}</td>
            <td onclick="location='/ddt/{$row.ddt.id}';">{$row.ddt.weighed_weight|string_format:'%.3f'}</td>
            <td onclick="location='/ddt/{$row.ddt.id}';">{$row.ddt.modified_at|date_human}<br />by {$row.ddt.modifier.login|escape:'html'}</td>
            {if $row.ddt.is_outdated == 1}
                <td onclick="location='/ddt/{$row.ddt.id}';"><i style="color: #999;">is outdated</i></td>
            {else}
                {if isset($row.ddt.attachment)}<td><a class="pdf" target="_blank" href="/file/{$row.ddt.attachment.secret_name}/{$row.ddt.attachment.original_name}">{$row.ddt.attachment.original_name}</a></td>
                {else}<td onclick="location='/ddt/{$row.ddt.id}';">{''|undef}</td>
                {/if}
            {/if}
            <td>{if $row.ddt.is_deleted != 1}<img src="/img/icons/pencil-small.png" onclick="location='/ddt/{$row.ddt.id}/edit';">{/if}</td>
        </tr>
        {/foreach}
    </table>
{/if}
