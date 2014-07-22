{if empty($list)}Nothing was found on my request
{else}
<table class="list" width="100%">
    <tr class="top-table">
        <th width="5%">Id</th>
        <th width="15%">Number</th>
        <th width="15%">Date</th>
        <th width="15%">Company</th>
        <th width="15%">Owner</th>
        <th width="5%">Items, pcs</th>
        <th width="15%">Modified</th>
        <th style="width: 200px;">Attachments</th>
    </tr>
    {foreach $list as $row}
    <t>
        <td>{$row.inddt.id}</td>
        <td>{$row.inddt.number|escape:'html'|undef}</td>
        <td>{if !empty($row.inddt.date) && $row.inddt.date > 0}{$row.inddt.date|date_format:'d/m/Y'}{else}{''|undef}{/if}</td>
        <td>{if !empty($row.inddt.company)}{$row.inddt.company.doc_no|escape:'html'}{else}{''|undef}{/if}</td>
        <td>{if !empty($row.inddt.owner)}{$row.inddt.owner.title_trade|escape:'html'}{else}{''|undef}{/if}</td>
        <td>{$row.inddt.qtty|string_format:'%d'}</td>
        <td>
            {if $row.inddt.modified_at > 0}{$row.inddt.modified_at|date_human:true}{if isset($row.inddt.modifier)}, {$row.inddt.modifier.login}{/if}
            {else}{$row.inddt.created_at|date_human:true}{if isset($row.inddt.author)}, {$row.inddt.author.login}{/if}
            {/if}
        </td>
        {if isset($row.inddt.attachments)}
        <td>        
            {foreach from=$row.inddt.attachments item=att}
            <a class="{$att.attachment.ext}" target="_blank" href="/file/{$att.attachment.secret_name}/{$att.attachment.original_name}" style="margin-right: 5px;">{$att.attachment.original_name}</a>
            {/foreach}
        </td>
        {else}<td>{''|undef}</td>
        {/if}
    </tr>
    {/foreach}
</table>
{/if}