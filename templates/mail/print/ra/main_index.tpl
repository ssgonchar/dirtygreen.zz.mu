{if empty($list)}Nothing was found on my request
{else}
    <table class="list" width="100%">
        <tr class="top-table">
            <th>No</th>
            <th>Stockholder</th>
            <th>Transport Company</th>
            <th>Truck Number</th>
            <th>Max Plate Width</th>
            <th>Theor. Weight</th>
            <th>Weighed Weight</th>
            <th style="width: 250px;">DDT/BOL Nr & Date</th>
            <th>Modified</th>
            <th>Pdf</th>
        </tr>
        {foreach $list as $row}
        <tr>
            <td>{$row.ra.doc_no|escape:'html'|undef}</a></td>
            <td>{$row.ra.stockholder.doc_no|escape:'html'}</td>
            <td>{if !empty($row.ra.company)}{$row.ra.company.doc_no|escape:'html'}{else}{''|undef}{/if}</td>
            <td>{$row.ra.truck_number|escape:'html'|undef}</td>
            <td>{$row.ra.max_width|string_format:'%d'} mm</td>
            <td>{$row.ra.total_weight} {$row.ra.weight_unit} ({$row.ra.total_qtty} pcs)</td>
            <td>{if $row.ra.weighed_weight > 0}{$row.ra.weighed_weight|string_format:'%.2f'} {$row.ra.weight_unit}{else}{''|undef}{/if}</td>
            <td>{if !empty($row.ra.ddt_number)}{$row.ra.ddt_number|escape:'html'}{if $row.ra.ddt_date > 0} dd {$row.ra.ddt_date|date_format:'%d.%m.%y'}{/if}{else}{''|undef}{/if}</td>
            <td>{$row.ra.modified_at|date_human}<br />by {$row.ra.modifier.login|escape:'html'}</td>
            {if isset($row.ra.attachment)}
            <td><a class="pdf" target="_blank" href="/file/{$row.ra.attachment.secret_name}/{$row.ra.attachment.original_name}">{$row.ra.attachment.original_name}</a></td>
            {else}<td>{''|undef}</td>
            {/if}
        </tr>
        {/foreach}
    </table>
{/if}