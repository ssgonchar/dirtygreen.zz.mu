{if empty($list)}Nothing was found on my request
{else}
    <table class="list" width="100%">
        <tbody>
            <tr class="top-table">
                <th>ID</th>
                <th>Number</th>
                <th>Date</th>
                <th>Company</th>
                <th>Kind</th>
                <th>Standart</th>
                <th>State Of Supply</th>
                <th>Items, pcs</th>
                <th style="width: 200px;">Plate Ids</th>
                <th style="width: 200px;">PDF</th>
                <th>Modified</th>
            </tr>
            {foreach $list as $row}
            <tr>
                <td >{$row.oc.id}</td>
                <td>{$row.oc.number|undef}</td>
                <td>{if $row.oc.date > 0}{$row.oc.date|date_format:'d/m/Y'}{else}{''|undef}{/if}</td>
                <td>{if isset($row.oc.company)}{$row.oc.company.doc_no}{else}{''|undef}{/if}</td>
                <td>{$row.oc.kind_title|undef}</td>
                <td>{if !empty($row.oc.standard)}{$row.oc.standard.title|escape:'html'}{else}{''|undef}{/if}</td>
                <td>{$row.oc.state_of_supply_title|undef}</td>
                <td>{if empty($row.oc.total_qtty)}{''|undef}{else}{$row.oc.total_qtty}{/if}</td>
                {if isset($row.oc.items_list)}
                <td>
                    {foreach name='oc_items' from=$row.oc.items_list item=item}
                    {if !empty($item.steelitem.guid)}{if !$smarty.foreach.oc_items.first}, {/if}{$item.steelitem.guid}{/if}
                    {/foreach}
                </td>
                {else}<td">{''|undef}</td>
                {/if}
                {if isset($row.oc.attachments)}
                <td>
                    {foreach from=$row.oc.attachments item=att name='att'}
                    {if !$smarty.foreach.att.first}, {/if}<a class="{$att.attachment.ext}" target="_blank" href="/file/{$att.attachment.secret_name}/{$att.attachment.original_name}" style="margin-right: 5px;">{$att.attachment.original_name}</a>
                    {/foreach}
                </td>
                {else}<td>{''|undef}</td>
                {/if}
                <td>
                    {if !empty($row.oc.modified_by)}{$row.oc.modified_at|date_human}<br>by {$row.oc.modifier.login|escape:'html'}
                    {else}{$row.oc.created_at|date_human}<br>by {$row.oc.author.login|escape:'html'}
                    {/if}
                </td>
            </tr>
            {/foreach}
        </tbody>
    </table>
{/if}