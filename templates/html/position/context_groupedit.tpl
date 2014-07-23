
<ul class="nav navbar-nav footer_panel" style='margin-top: 6px;'>
	<li>
		<table style="float: left; margin-right: 20px;">
			<tr>
            <td style="font-weight: bold;" width="70px">Selected : </td>
            <td width="60px" class="text-right"><span id="lbl-selected-qtty" class="lbl-total-qtty">{if isset($total_qtty)}{$total_qtty}{else}0{/if}</span> pcs</td>
            <td width="100px" class="text-right"><span id="lbl-selected-weight" class="lbl-total-weight">{if isset($total_weight)}{$total_weight|string_format:'%.2f'}{else}0{/if}</span><span class="lbl-wgh">{if isset($stock)} {$stock.weight_unit|wunit}{/if}</span></td>
            <td width="100px" class="text-right"><span id="lbl-selected-value" class="lbl-total-value">{if isset($total_value)}{$total_value|string_format:'%.2f'}{else}0{/if}</span><span class="lbl-value">{if isset($stock)} {$stock.currency_sign}{/if}</span></td>
			</tr>
			<tr>
            <td style="font-weight: bold;" width="75px">From total : </td>
            <td width="60px" class="text-right"><span id="lbl-total-qtty">{if isset($total_qtty)}{$total_qtty}{else}0{/if}</span> pcs</td>
            <td width="100px" class="text-right"><span id="lbl-total-weight">{if isset($total_weight)}{$total_weight|string_format:'%.2f'}{else}0{/if}</span> <span class="lbl-wgh">{if isset($stock)} {$stock.weight_unit|wunit}{/if}</span></td>
            <td width="100px" class="text-right"><span id="lbl-total-value">{if isset($total_value)}{$total_value|string_format:'%.2f'}{else}0{/if}</span> <span class="lbl-value">{if isset($stock)} {$stock.currency_sign}{/if}</span></td>
			</tr>
    </table>
	</li>
</ul>
<ul class="nav navbar-nav navbar-right" style="margin-top: 7px;">
    <li>
        <p>
            <a class="btn btn-default" href="/positions" style="margin-right: 7px;">Back</a>
            {*<input type="submit" name="btn_cancel" class="btn btn-primary" value="Cancel" onclick="return confirm('Am I sure ?');" style="margin-right: 20px;">*}
            <input type="submit" name="btn_save" class="btn btn-primary" value="Save Selected">
        </p>
    </li>	
</ul>