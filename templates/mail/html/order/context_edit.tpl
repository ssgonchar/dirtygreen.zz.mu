<div class="footer-left">
    <div id="position_controls"{if !isset($form.order_for) || empty($form.order_for)} style="display: none;"{/if}>
        <table style="float: left; margin-right: 20px; margin-top: 10px;">
            <tr>
                <td style="font-weight: bold;" width="40px">Total : </td>
                <td width="50px" class="text-right"><span id="lbl-total-qtty">{if isset($total_qtty)}{$total_qtty}{else}0{/if}</span> pcs</td>
                <td width="80px" class="text-right"><span id="lbl-total-weight">{if isset($total_weight)}{$total_weight|string_format:'%.2f'}{else}0{/if}</span> <span class="lbl-wgh">{if isset($form.weight_unit)}{$form.weight_unit}{/if}</span></td>
                <td width="80px" class="text-right"><span id="lbl-total-value">{if isset($total_value)}{$total_value|string_format:'%.2f'}{else}0{/if}</span> <span class="lbl-cur">{if isset($form.currency)}{if $form.currency == 'usd'}${elseif $form.currency == 'eur'}&euro;{else}{/if}{/if}</span></td>
            </tr>
        </table>
        <input type="button" name="btn_add" class="btn150" value="Create Position" onclick="app_position();">
        <input type="submit" name="btn_add_from_stock" class="btn150" value="Add From Stock" style="margin-left: 10px;">
    </div>
</div>
<div class="footer-right">
    {if isset($show_cancel_button)}<input type="submit" name="btn_cancel" class="btn150b" value="Cancel Order" onclick="if (!confirm('Am I sure I want to cancel this order ? ')) return false;">{/if}
    <input type="submit" name="btn_save" class="btn100o" value="{if isset($form.status) && $form.status == 'nw'}Confirm{else}Save{/if}" style="margin-left: 10px;">
</div>