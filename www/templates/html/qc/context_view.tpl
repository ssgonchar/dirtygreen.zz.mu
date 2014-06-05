
<ul class="nav navbar-nav footer_panel" style='margin-top: 6px;'>
	<li>
		{include file='templates/layouts/controls/control_document_timestamp.tpl' doc=$qc}
	</li>
</ul>
<ul class="nav navbar-nav navbar-right">
	<li>
		<input type="button" class="btn150" value="Compose eMail" onclick="location.href='/qc/{$qc.id}/email/compose'" style="cursor: pointer;">
		<input type="button" class="btn100o" value="Edit" style="margin-left: 10px; cursor: pointer;" onclick="location.href='/qc/{$qc.id}/edit'">
	</li>	
</ul>