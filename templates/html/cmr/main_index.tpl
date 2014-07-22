{if empty($list)}Nothing was found on my request
{else}
    <table class="list search-target" width="100%">
        <tr class="top-table">
            <th>No</th>
            <th>Owner</th>
            <th>Date</th>
            <th>Buyer</th>
            <th>Delivery Point</th>
            <th>Qtty</th>
            <th style="width: 250px;">Weight</th>
            <th style="width: 250px;">Weighed Weight</th>
            <th>Modified</th>
            <th>Pdf</th>
            <th style="width: 16px;"></th>
        </tr>
        {foreach $list as $row}
        <tr>
            <td onclick="location='/cmr/{$row.cmr.id}';">{if empty($row.cmr.number)}{''|undef}{else}{$row.cmr.doc_no|escape:'html'}{/if}</td>
            <td onclick="location='/cmr/{$row.cmr.id}';">{if isset($row.cmr.owner)}{$row.cmr.owner.title_trade}{else}{''|undef}{/if}</td>
            <td onclick="location='/cmr/{$row.cmr.id}';">{$row.cmr.date|date_format:'%d.%m.%y'|undef}</td>
            <td onclick="location='/cmr/{$row.cmr.id}';">{if !empty($row.cmr.buyer_name)}{$row.cmr.buyer_name|escape:'html'}{else}{''|undef}{/if}</td>
            <td onclick="location='/cmr/{$row.cmr.id}';">{if !empty($row.cmr.delivery_point)}{$row.cmr.delivery_point|escape:'html'}{else}{''|undef}{/if}</td>
            <td onclick="location='/cmr/{$row.cmr.id}';">{$row.cmr.total_qtty} pcs</td>
            <td onclick="location='/cmr/{$row.cmr.id}';">{$row.cmr.total_weight} {$row.cmr.weight_unit}</td>
            <td onclick="location='/cmr/{$row.cmr.id}';">{$row.cmr.weighed_weight|string_format:'%.3f'} {$row.cmr.weight_unit}</td>
            <td onclick="location='/cmr/{$row.cmr.id}';">{$row.cmr.modified_at|date_human}<br />by {$row.cmr.modifier.login|escape:'html'}</td>
            {if $row.cmr.is_outdated == 1}
                <td onclick="location='/cmr/{$row.cmr.id}';"><i style="color: #999;">is outdated</i></td>
            {else}
                {if isset($row.cmr.attachment)}<td><a class="pdf" target="_blank" href="/file/{$row.cmr.attachment.secret_name}/{$row.cmr.attachment.original_name}">{$row.cmr.attachment.original_name}</a></td>
                {else}<td onclick="location='/cmr/{$row.cmr.id}';">{''|undef}</td>
                {/if}
                
            {/if}
            <td>{if $row.cmr.is_deleted != 1}<img src="/img/icons/pencil-small.png" onclick="location='/cmr/{$row.cmr.id}/edit';">{/if}</td>
        </tr>
        {/foreach}
    </table>
{/if}