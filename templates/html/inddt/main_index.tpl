{if empty($list)}Nothing was found on my request
{else}
<table class="list search-target" width="100%">
    <tr class="top-table">
        <th width="30px"><input type="checkbox" id="" value="{$row.inddt.id}" onchange="check_all(this, 'inddt')"></th>
        <th width="5%">Id</th>
        <th width="15%">Number</th>
        <th width="15%">Date</th>
        <th width="15%">Company</th>
        <th width="15%">Owner</th>
        <th width="5%">Items, pcs</th>
        <th width="15%">Modified</th>
        <th style="width: 200px;">Attachments</th>
        <th width="20px"></th>
        {if $smarty.session.user.role_id <= $smarty.const.ROLE_ADMIN}<th width="20px"></th>{/if}
    </tr>
    {foreach $list as $row}
    <tr id="inddt-{$row.inddt.id}" class="{if !empty($row.inddt.number) && $row.inddt.date > 0 && $row.inddt.company_id > 0}row-color-green{/if}">
        <td><input type="checkbox" value="{$row.inddt.id}" class="cb-row-inddt" onchange="show_selected_controls('inddt');"></td>
        <td onclick="location='/inddt/{$row.inddt.id}';">{$row.inddt.id}</td>
        <td onclick="location='/inddt/{$row.inddt.id}';">{$row.inddt.number|escape:'html'|undef}</td>
        <td onclick="location='/inddt/{$row.inddt.id}';">{if !empty($row.inddt.date) && $row.inddt.date > 0}{$row.inddt.date|date_format:'d/m/Y'}{else}{''|undef}{/if}</td>
        <td onclick="location='/inddt/{$row.inddt.id}';">{if !empty($row.inddt.company)}{$row.inddt.company.doc_no|escape:'html'}{else}{''|undef}{/if}</td>
        <td onclick="location='/inddt/{$row.inddt.id}';">{if !empty($row.inddt.owner)}{$row.inddt.owner.title_trade|escape:'html'}{else}{''|undef}{/if}</td>
        <td onclick="location='/inddt/{$row.inddt.id}';">{$row.inddt.qtty|string_format:'%d'}</td>
        <td onclick="location='/inddt/{$row.inddt.id}';">
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
        {else}
        <td onclick="location='/inddt/{$row.inddt.id}';">{''|undef}</td>
        {/if}        
        <td onclick="location='/inddt/{$row.inddt.id}/edit';"><img src="/img/icons/pencil-small.png"></td>
        {if $smarty.session.user.role_id <= $smarty.const.ROLE_ADMIN}
        <td onclick="if (confirm('Remove In DDT ?')) location='/inddt/{$row.inddt.id}/remove';"><img src="/img/icons/cross-small.png"></td>
        {/if}
    </tr>
    {/foreach}
</table>
{/if}