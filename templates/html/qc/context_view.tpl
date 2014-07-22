
<ul class="nav navbar-nav footer_panel" style='margin-top: 6px;'>
	<li>
		{include file='templates/layouts/controls/control_document_timestamp.tpl' doc=$qc}
	</li>
</ul>
<ul class="nav navbar-nav navbar-right">
	<li>
		<input type="button" value="To List" class="btn btn-default" style=''onclick="location.href='/qc';">
                <input type="button" class="btn btn-primary" value="Compose eMail" onclick="location.href='/qc/{$qc.id}/email/compose'" style="margin: 7px; cursor: pointer;">
		<input type="button" class="btn btn-primary" value="Edit" style="margin: 7px; cursor: pointer;" onclick="location.href='/qc/{$qc.id}/edit'">
	</li>	
</ul>