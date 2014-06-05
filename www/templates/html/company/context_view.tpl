<ul class="nav navbar-nav footer_panel" style='margin-top: 6px;'>
	<li>
		{include file='templates/layouts/controls/control_document_timestamp.tpl' doc=$form}
	</li>
</ul>
<ul class="nav navbar-nav navbar-right">
	<li>
		<input type="button" class="btn150" value="Add Person" onclick="location.href='/person/addfromcompany/{$form.id}';">
		<input type="button" class="btn100o" value="Edit" style="margin-left: 10px;" onclick="location.href='/company/{$form.id}/edit';">
	</li>	
</ul>