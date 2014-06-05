<!-- <div class="footer-left">
    <table style="float: left; margin-right: 20px;">
        <tr>
            <td style="font-weight: bold;" width="70px">Selected : </td>
            <td width="60px" class="text-right"><span id="lbl-selected-qtty">0</span> pcs</td>
            <td width="100px" class="text-right"><span id="lbl-selected-weight">0</span><span class="lbl-wgh">{if isset($stock)} {$stock.weight_unit|wunit}{/if}</span></td>
            <td width="100px" class="text-right"><span id="lbl-selected-value">0</span><span class="lbl-value">{if isset($stock)} {$stock.currency_sign}{/if}</span></td>
        </tr>
        <tr>
            <td style="font-weight: bold;" width="50px">Total : </td>
            <td width="60px" class="text-right">{if isset($total_qtty)}{$total_qtty}{else}0{/if} pcs</td>
            <td width="100px" class="text-right">{if isset($total_weight)}{$total_weight|string_format:'%.2f'}{else}0{/if} <span class="lbl-wgh">{if isset($stock)} {$stock.weight_unit|wunit}{/if}</span></td>
            <td width="100px" class="text-right">{if isset($total_value)}{$total_value|string_format:'%.2f'}{else}0{/if} <span class="lbl-value">{if isset($stock)} {$stock.currency_sign}{/if}</span></td>
        </tr>
    </table>
</div>
<div class="footer-right">
    <div id="selected-actions-position" style="display: none;">
        <input type="submit" name="btn_remove" class="btn200o" value="Remove from Reserve" onclick="return confirm('Am I sure ?');">
    </div>
</div> -->

<ul class="nav navbar-nav footer_panel">
		<li style='margin-top: 7px;'>
			<table style="float: left; margin-right: 10px;">
			   <tr>
					<td style="font-weight: bold;" width="70px">Selected : </td>
					<td width="60px" class="text-right"><span id="lbl-selected-qtty">0</span> pcs</td>
					<td width="100px" class="text-right"><span id="lbl-selected-weight">0</span><span class="lbl-wgh">{if isset($stock)} {$stock.weight_unit|wunit}{/if}</span></td>
					<td width="100px" class="text-right"><span id="lbl-selected-value">0</span><span class="lbl-value">{if isset($stock)} {$stock.currency_sign}{/if}</span></td>
				</tr>
				<tr>
					<td style="font-weight: bold;" width="50px">Total : </td>
					<td width="60px" class="text-right">{if isset($total_qtty)}{$total_qtty}{else}0{/if} pcs</td>
					<td width="100px" class="text-right">{if isset($total_weight)}{$total_weight|string_format:'%.2f'}{else}0{/if} <span class="lbl-wgh">{if isset($stock)} {$stock.weight_unit|wunit}{/if}</span></td>
					<td width="100px" class="text-right">{if isset($total_value)}{$total_value|string_format:'%.2f'}{else}0{/if} <span class="lbl-value">{if isset($stock)} {$stock.currency_sign}{/if}</span></td>
				</tr>
			</table>
		</li>
	</ul>
	<ul class="nav navbar-nav navbar-right" style='margin-top: 8px;'>
		<li id="selected-actions-position" style="display: none;">
			<input type="submit" name="btn_remove" class="btn200o" value="Remove from Reserve" onclick="return confirm('Am I sure ?');">
		</li>	
	</ul>