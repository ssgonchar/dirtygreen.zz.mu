
<ul class="nav navbar-nav footer_panel" style='margin-top: 6px;'>
	<li>
		 <table style='margin-top: 8px;'>
        <tr>
            <td style="font-weight: bold;" width="50px">Total : </td>
            <td width="60px" class="text-right"><span id="lbl-total-qtty">{if isset($total_qtty)}{$total_qtty}{else}0{/if}</span> pcs</td>
            <td width="100px" class="text-right"><span id="lbl-total-weight">{if isset($total_weight)}{$total_weight|string_format:'%.2f'}{else}0{/if}</span><span class="lbl-wgh">{if isset($stock)} {$stock.weight_unit|wunit}{/if}</span></td>
            <td width="100px" class="text-right"><span id="lbl-total-value">{if isset($total_value)}{$total_value|string_format:'%.2f'}{else}0{/if}</span><span class="lbl-value">{if isset($stock)} {$stock.currency_sign}{/if}</span></td>
        </tr>
		</table>
	</li>
</ul>
<ul class="nav navbar-nav navbar-right">
	<li>
		<input type="submit" name="btn_save" class="btn150o" value="Create Positions">
	</li>	
</ul>

