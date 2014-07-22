<div class="footer-left text-middle" style="width: 600px;">
{*
    <table style="float: left; margin-right: 20px;">
        <tr>
            <td style="font-weight: bold;" width="70px">Selected : </td>
            <td width="50px" class="text-right"><span id="lbl-selected-qtty">0</span> pcs</td>
            <td width="100px" class="text-right"><span id="lbl-selected-weight">0</span><span class="lbl-wgh">{if isset($stock)} {$stock.weight_unit|wunit}{/if}</span></td>
            <td width="100px" class="text-right"><span id="lbl-selected-value">0</span><span class="lbl-value">{if isset($stock)} {$stock.currency_sign}{/if}</span></td>
            <td width="150px" class="text-right"><span id="lbl-selected-purchasevalue">0</span><span class="lbl-value">{if isset($stock)} {$stock.currency_sign}{/if}</span> (purchase)</td>
        </tr>
        <tr>
            <td style="font-weight: bold;" width="70px">Total : </td>
            <td width="30px" class="text-right">{if isset($total_qtty)}{$total_qtty}{else}0{/if} pcs</td>
            <td width="100px" class="text-right">{if isset($total_weight)}{$total_weight|string_format:'%.2f'}{else}0{/if} <span class="lbl-wgh">{if isset($stock)} {$stock.weight_unit|wunit}{/if}</span></td>
            <td width="100px" class="text-right">{if isset($total_value)}{$total_value|string_format:'%.2f'}{else}0{/if} <span class="lbl-value">{if isset($stock)} {$stock.currency_sign}{/if}</span></td>
            <td width="150px" class="text-right">{if isset($total_purchase_value)}{$total_purchase_value|string_format:'%.2f'}{else}0{/if} <span class="lbl-value">{if isset($stock)} {$stock.currency_sign}{/if}</span> (purchase)</td>
        </tr>
    </table>
*}
</div>
<div class="footer-right">
{*
{if empty($is_revision)}
    {if isset($target_doc)}
        <input type="button" class="btn150" value="Back to {$target_doc_no}" onclick="location.href='/{$target_doc}/{if empty($target_doc_id)}add{else}{$target_doc_id}/edit{/if}';">
        <input type="button" class="btn150o selected-control" value="Add to {$target_doc_no}" onclick="add_selected_to_document('{$target_doc}', {$target_doc_id});" style="display: none; margin-left: 10px;">
    {else}
        <input type="button" id="btn_to_order" class="btn150 selected-control" value="Add To Order" onclick="show_order_select();" style="margin-left: 10px; display: none;">
        <input type="button" class="btn100 selected-control" value="Add to QC" onclick="show_qc_select();" style="margin-left: 10px; display: none;">
        <input type="button" class="btn100 selected-control" value="Edit" onclick="edit_items();" style="margin-left: 10px; display: none;">
    {/if}
{/if}
*}
</div>
