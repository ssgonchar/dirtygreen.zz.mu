{if empty($list)}
    Nothing was found on my request
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
            <th style="width: 40px;"></th>
        </tr>
        {foreach $list as $row}
        <tr class="{if !empty($row.ra.ddt_number)}ra-row-with-ddt{/if}">
            <td onclick="location='/ra/{$row.ra.id}';">{$row.ra.doc_no|escape:'html'|undef}</a></td>
            <td onclick="location='/ra/{$row.ra.id}';">{$row.ra.stockholder.doc_no|escape:'html'}</td>
            <td onclick="location='/ra/{$row.ra.id}';">{if !empty($row.ra.company)}{$row.ra.company.doc_no|escape:'html'}{else}{''|undef}{/if}</td>
            <td onclick="location='/ra/{$row.ra.id}';">{$row.ra.truck_number|escape:'html'|undef}</td>
            <td onclick="location='/ra/{$row.ra.id}';"{if $row.ra.status_id == $smarty.const.RA_STATUS_OPEN && !empty($row.ra.is_large_item_exists)} style="color: red; font-weight: bold; background-color: #FEDBDA !important;"{/if}>{$row.ra.max_width|string_format:'%d'} mm</td>
            <td{if $row.ra.status_id == $smarty.const.RA_STATUS_OPEN && !empty($row.ra.total_weightmax_highlight)} style="background-color: #FFF750 !important;"{/if} onclick="location='/ra/{$row.ra.id}';">{$row.ra.total_weight|number_format:2:true} {$row.ra.weight_unit} ({$row.ra.total_qtty} pcs)</td>
            <td onclick="location='/ra/{$row.ra.id}';">{if $row.ra.weighed_weight > 0}{$row.ra.weighed_weight|number_format:2:true} {$row.ra.weight_unit}{else}{''|undef}{/if}</td>
            <td onclick="location='/ra/{$row.ra.id}';">{if !empty($row.ra.ddt_number)}{$row.ra.ddt_number|escape:'html'}{if $row.ra.ddt_date > 0} dd {$row.ra.ddt_date|date_format:'%d.%m.%y'}{/if}{else}{''|undef}{/if}</td>
            <td onclick="location='/ra/{$row.ra.id}';">{$row.ra.modified_at|date_human}<br />by {$row.ra.modifier.login|escape:'html'}</td>
            {if isset($row.ra.attachment)}
            <td><a class="pdf" target="_blank" href="/file/{$row.ra.attachment.secret_name}/{$row.ra.attachment.original_name}">{$row.ra.attachment.original_name}</a></td>
            {else}
            <td onclick="location.href='/ra/{$row.ra.id}';">{''|undef}</td>
            {/if}
            <td>
            {if $row.ra.status_id == $smarty.const.RA_STATUS_OPEN || ($row.ra.status_id == $smarty.const.RA_STATUS_PENDING && $smarty.session.user.role_id <= $smarty.const.ROLE_MODERATOR)}
                <img src="/img/icons/pencil-small.png" onclick="location='/ra/{$row.ra.id}/edit';">
            {/if}
            {if $row.ra.status_id == $smarty.const.RA_STATUS_OPEN}
                <img src="/img/icons/cross.png" style="cursor: pointer" alt="Delete" title="Delete" onclick="if(!confirm('Am I sure ?'))return false;location='/ra/{$row.ra.id}/delete';">
            {/if}
            </td>
        </tr>
        {/foreach}
    </table>
{/if}