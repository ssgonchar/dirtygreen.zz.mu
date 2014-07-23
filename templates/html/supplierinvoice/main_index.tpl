{if empty($list)}
    Nothing was found on my request
{else}    
    <table class="list search-target" width="100%">
        <tbody>
            <tr class="top-table">
                <th>Number</th>
                <th>Date</th>
                <th>Due Date</th>
                <th>Company</th>
                <th>Amount</th>
                <th>Owner</th>
                <th>Payment Terms</th>
                <th>Status</th>
                <th>Items, pcs</th>
                <th>Modified</th>
                <th style="width: 20px;"></th>
                <th style="width: 20px;"></th>
            </tr>
            {foreach from=$list item=row}
            <tr{if $row.supinvoice.status_id == $smarty.const.SUPINVOICE_STATUS_PAID} class="inv-row-received"{else if $row.supinvoice.status_id == $smarty.const.SUPINVOICE_STATUS_PPAID} class="inv-row-partially-received"{else if $row.supinvoice.date > 0 && $row.supinvoice.days_left <= 0} class="inv-row-overdue"{else if $row.supinvoice.status_id == $smarty.const.SUPINVOICE_STATUS_CANCELLED} class="inv-row-closed"{/if}>
                <td onclick="location.href='/supplierinvoice/{$row.supinvoice.id}';">{$row.supinvoice.number|undef}</td>
                <td onclick="location.href='/supplierinvoice/{$row.supinvoice.id}';">{if $row.supinvoice.date > 0}{$row.supinvoice.date|date_format:'d/m/Y'}{else}{''|undef}{/if}</td>
                <td onclick="location.href='/supplierinvoice/{$row.supinvoice.id}';"{if $row.supinvoice.date > 0 && $row.supinvoice.days_left > 0 && $row.supinvoice.days_left <= 7} style="background-color: #FFF750 !important;"{/if}>{if $row.supinvoice.due_date > 0}{$row.supinvoice.due_date|date_format:'d/m/Y'}{else}{''|undef}{/if}</td>
                <td onclick="location.href='/supplierinvoice/{$row.supinvoice.id}';">{if isset($row.supinvoice.company)}{$row.supinvoice.company.doc_no}{else}{''|undef}{/if}</td>
                <td onclick="location.href='/supplierinvoice/{$row.supinvoice.id}';" style="width: 100px;">{$row.supinvoice.currency|cursign}&nbsp;{($row.supinvoice.total_amount - $row.supinvoice.amount_paid)|string_format:'%.2f'}</td>
                <td onclick="location.href='/supplierinvoice/{$row.supinvoice.id}';">{if isset($row.supinvoice.owner)}{$row.supinvoice.owner.title_trade}{else}{''|undef}{/if}</td>
                <td onclick="location.href='/supplierinvoice/{$row.supinvoice.id}';">
                    {if empty($row.supinvoice.payment_days)}{''|undef}
                    {else}
                        {if $row.supinvoice.payment_type == $smarty.const.SUPINVOICE_PAYMENT_IDD}
                            {$row.supinvoice.payment_days}{if $row.supinvoice.payment_days == 1} day{else} days{/if} from invoice date
                        {else}
                            {$row.supinvoice.payment_days}{if $row.supinvoice.payment_days == 1} day{else} days{/if} from end of month
                        {/if}
                    {/if}
                </td>
                <td onclick="location.href='/supplierinvoice/{$row.supinvoice.id}';">
                    {$row.supinvoice.status_title|undef}
                    {if $row.supinvoice.status_id == $smarty.const.SUPINVOICE_STATUS_PPAID && $row.supinvoice.amount_paid > 0}<br>{$row.supinvoice.currency|cursign}&nbsp;{$row.supinvoice.amount_paid|string_format:'%.2f'}{/if}
                </td>
                <td onclick="location.href='/supplierinvoice/{$row.supinvoice.id}';">{if empty($row.supinvoice.total_qtty)}{''|undef}{else}{$row.supinvoice.total_qtty}{/if}</td>
                <td onclick="location.href='/supplierinvoice/{$row.supinvoice.id}';">                                                                
                    {if !empty($row.supinvoice.modified_by)}{$row.supinvoice.modified_at|date_human}<br>by {$row.supinvoice.modifier.login|escape:'html'}
                    {else}{$row.supinvoice.created_at|date_human}<br>by {$row.supinvoice.author.login|escape:'html'}
                    {/if}
                </td>
                <td>
                    <img src="/img/icons/pencil-small.png" style="cursor: pointer" alt="Edit" title="Edit" onclick="location.href='/supplierinvoice/{$row.supinvoice.id}/edit';" />
                </td>
                <td>
                    <img src="/img/icons/cross-small.png" style="cursor: pointer" alt="Delete" title="Delete" onclick="if(!confirm('Am I sure ?'))return false;location.href='/supplierinvoice/{$row.supinvoice.id}/delete';" />
                </td>
            </tr>
            {/foreach}
        </tbody>
    </table>
{/if}
