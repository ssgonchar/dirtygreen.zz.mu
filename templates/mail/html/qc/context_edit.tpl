<div class="footer-left">
{*
    <table style="float: left; margin-right: 20px; margin-top: 10px;">
        <tr>
            <td style="font-weight: bold;" width="40px">Total : </td>
            <td width="50px" class="text-right"><span id="lbl-selected-qtty">{$total_qtty}</span> pcs</td>
            <td width="100px" class="text-right">{if $form.mam_co == 'pa' && $form.wght_unit == 'mt'}<span id="lbl-selected-weight">{$total_weight_ton|string_format:'%.2f'}</span>{else}<span id="lbl-selected-weight">{$total_weight|string_format:'%.2f'}</span>{/if} {$form.wght_unit}</td>
        </tr>
    </table>
*}    
</div>
<div class="footer-right">
    <input type="submit" name="btn_additems" class="btn100" value="Add Items" style="margin-left: 10px;">
    {if !empty($items)}<input type="submit" name="btn_edititems" class="btn100" value="Edit Items" style="margin-left: 10px;">{/if}
    <input type="submit" name="btn_save" class="btn100o" value="Save" style="margin-left: 20px;">
</div>