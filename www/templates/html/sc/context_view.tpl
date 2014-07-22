
<ul class="nav navbar-nav footer_panel">
	<li style='margin-top: 7px;'>
		{include file='templates/layouts/controls/control_document_timestamp.tpl' doc=$sc}
	</li>
</ul>
<ul class="nav navbar-nav navbar-right">
	<li>
		<input type="button" class="btn150" value="Compose eMail" onclick="location.href='/sc/{$sc.id}/email/compose'" style="cursor: pointer;">
		<input type="button" class="btn100o" value="Edit" style="margin-left: 10px; cursor: pointer;" onclick="location.href='/sc/{$sc.id}/edit'">
	</li>	
</ul>
