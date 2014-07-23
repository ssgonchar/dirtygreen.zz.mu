<table class="form" width="75%">
    <tr>
        <td class="form-td-title">Plate Id :</td>
        <td><input id="search_string" type="text" name="form[keyword]" placeholder="please enter plate id or part of it" class="form-control find-parametr"{if isset($keyword)} value="{$keyword|escape:'html'}"{/if}></td>
        <td><input id="search" type="button" name="btn_select" value="Find" class="btn btn-primary"></td>
    </tr>
</table>
<br/>
<br/>
<hr/>
{if empty($list)}Nothing was found on my request
{else}
    <table class="list search-target" width="100%">
        <tbody>
            <tr class="top-table">
                {* <th>ID</th> *}
                <th>Number</th>
                <th>Date</th>
                <th>Company</th>
                <th>Kind</th>
                <th>Standard</th>
                <th>State Of Supply</th>
                <th>Items, pcs</th>
                <th style="width: 200px;">Plate Ids</th>
                <th style="width: 200px;">PDF</th>
                <th>Modified</th>
                <th style="width: 20px;"></th>
                <th style="width: 20px;"></th>
            </tr>
            {foreach $list as $row}
            <tr{if !empty($row.oc.items_list) && isset($row.oc.attachments) && $row.oc.date > 0 && !empty($row.oc.number) && $row.oc.company_id > 0} class="row-color-green"{/if}>
                {* <td onclick="location.href='/oc/{$row.oc.id}';">{$row.oc.id}</td> *}
                <td onclick="location.href='/oc/{$row.oc.id}';">{$row.oc.number|undef}</td>
                <td onclick="location.href='/oc/{$row.oc.id}';">{if $row.oc.date > 0}{$row.oc.date|date_format:'d/m/Y'}{else}{''|undef}{/if}</td>
                <td onclick="location.href='/oc/{$row.oc.id}';">{if isset($row.oc.company)}{$row.oc.company.doc_no}{else}{''|undef}{/if}</td>
                <td onclick="location.href='/oc/{$row.oc.id}';">{$row.oc.kind_title|undef}</td>
                <td onclick="location.href='/oc/{$row.oc.id}';">{if !empty($row.oc.standard)}{$row.oc.standard.title|escape:'html'}{else}{''|undef}{/if}</td>
                <td onclick="location.href='/oc/{$row.oc.id}';">{$row.oc.state_of_supply_title|undef}</td>
                <td onclick="location.href='/oc/{$row.oc.id}';">{if empty($row.oc.total_qtty)}{''|undef}{else}{$row.oc.total_qtty}{/if}</td>
                {if isset($row.oc.items_list)}
                <td onclick="location.href='/oc/{$row.oc.id}';">
                    {foreach name='oc_items' from=$row.oc.items_list item=item}
                    {if !empty($item.steelitem.guid)}{if !$smarty.foreach.oc_items.first}, {/if}{$item.steelitem.guid}{/if}
                    {/foreach}
                </td>
                {else}<td onclick="location='/oc/{$row.oc.id}';">{''|undef}</td>
                {/if}
                {if isset($row.oc.attachments)}
                <td>
                    {foreach from=$row.oc.attachments item=att name='att'}
                    {if !$smarty.foreach.att.first}, {/if}<a class="{$att.attachment.ext}" target="_blank" href="/file/{$att.attachment.secret_name}/{$att.attachment.original_name}" style="margin-right: 5px;">{$att.attachment.original_name}</a>
                    {/foreach}
                </td>
                {else}<td onclick="location='/oc/{$row.oc.id}';">{''|undef}</td>
                {/if}
                <td onclick="location.href='/oc/{$row.oc.id}';">
                    {if !empty($row.oc.modified_by)}{$row.oc.modified_at|date_human}<br>by {$row.oc.modifier.login|escape:'html'}
                    {else}{$row.oc.created_at|date_human}<br>by {$row.oc.author.login|escape:'html'}
                    {/if}
                </td>
                <td>
                    <img src="/img/icons/pencil-small.png" style="cursor: pointer" alt="Edit" title="Edit" onclick="location.href='/oc/{$row.oc.id}/edit';" />
                </td>
                <td>
                    <img src="/img/icons/cross-small.png" style="cursor: pointer" alt="Delete" title="Delete" onclick="if(!confirm('Am I sure ?'))return false;location.href='/oc/{$row.oc.id}/delete';" />
                </td>
            </tr>
            {/foreach}
        </tbody>
    </table>
	
{/if}