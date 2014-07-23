
<ul class="nav navbar-nav footer_panel" style='margin-top: 6px;'>
	<li>
		
	</li>
</ul>
<ul class="nav navbar-nav navbar-right">
	<li>	
		<input type="button" class="btn btn-default" style="margin: 7px;" value="To List" onclick="location.href='/objectives'">
                <!--<input type="button" class="btn btn-default" style="margin: 7px;" value="To List" onclick="location.href='/objectives/{$form.year}{if !empty($form.quarter)}/{$form.quarter}{/if}';">-->
		<input type="button" name="btn_edit" class="btn btn-primary" value="Edit" style="margin: 7px;" onclick="location.href='/objective/{$form.id}/edit';">
	</li>	
</ul>