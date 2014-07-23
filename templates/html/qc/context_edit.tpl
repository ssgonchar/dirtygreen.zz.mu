<ul class="nav navbar-nav footer_panel" style='margin-top: 6px;'>
	<li>
		{*
		<!-- <table style="float: left; margin-right: 20px; margin-top: 10px;">
			<tr>
            <td style="font-weight: bold;" width="40px">Total : </td>
            <td width="50px" class="text-right"><span id="lbl-selected-qtty">{$total_qtty}</span> pcs</td>
            <td width="100px" class="text-right">{if $form.mam_co == 'pa' && $form.wght_unit == 'mt'}<span id="lbl-selected-weight">{$total_weight_ton|string_format:'%.2f'}</span>{else}<span id="lbl-selected-weight">{$total_weight|string_format:'%.2f'}</span>{/if} {$form.wght_unit}</td>
			</tr>
		</table> -->
		*}
	</li>
</ul>
<ul class="nav navbar-nav navbar-right">
	<li>
		<input type="button" value="To List" class="btn btn-default" style=''onclick="location.href='/qc';">
                <input type="submit" name="btn_additems" class="btn btn-primary" value="Add Items" style="margin: 7px;">
		{if !empty($items)}<input type="submit" name="btn_edititems" class="btn btn-primary" value="Edit Items" style="margin: 7px;">{/if}
		<input type="submit" name="btn_save" class="btn btn-primary" value="Save" style="margin: 7px;">
	</li>	
</ul>