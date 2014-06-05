
<ul class="nav navbar-nav footer_panel" style='margin-top: 6px;'>
	<li>
		
	</li>
</ul>
<ul class="nav navbar-nav navbar-right">
	<li>	
		<input type="button" class="btn100" value="To List" onclick="location.href='/objectives/{$form.year}{if !empty($form.quarter)}/{$form.quarter}{/if}';">
		<input type="button" name="btn_edit" class="btn100o" value="Edit" style="margin-left: 10px;" onclick="location.href='/objective/{$form.id}/edit';">
	</li>	
</ul>