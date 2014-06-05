
<ul class="nav navbar-nav footer_panel" style='margin-top: 6px;'>
	<li>
		<table style="float: left; margin-right: 20px; margin-top: 10px;">
        <tr>
            <td style="font-weight: bold;" width="70px">Total : </td>
            <td width="60px" class="text-right"><span id="lbl-selected-qtty-position">{if isset($total_qtty)}{$total_qtty}{else}0{/if}</span> pcs</td>
            <td width="100px" class="text-right"><span id="lbl-selected-weight-position">{if isset($total_weight)}{$total_weight|string_format:'%.2f'}{else}0.00{/if}</span> <span class="lbl-wgh">{if isset($order.weight_unit)}{$order.weight_unit}{/if}</span></td>
            <td width="100px" class="text-right"><span id="lbl-selected-value-position">{if isset($total_value)}{$total_value|string_format:'%.2f'}{else}0.00{/if}</span> <span class="lbl-cur">{if isset($order.currency)}{if $order.currency == 'usd'}${elseif $order.currency == 'eur'}&euro;{else}{/if}{/if}</span></td>
        </tr>
    </table>
	</li>
</ul>
<ul class="nav navbar-nav navbar-right">
	<li>
		<input type="submit" name="btn_dont_save" class="btn100" value="Don't Save">
    <input type="submit" name="btn_save" class="btn100o" value="Save" style="margin-left: 10px;">
	</li>	
</ul>