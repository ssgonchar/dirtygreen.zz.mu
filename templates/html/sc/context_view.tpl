
<ul class="nav navbar-nav footer_panel">
	<li style='margin-top: 7px;'>
		{include file='templates/layouts/controls/control_document_timestamp.tpl' doc=$sc}
	</li>
</ul>
<ul class="nav navbar-nav navbar-right">
	<li>
		<input type="button" value="To List" class="btn btn-default" style=''onclick="location.href='/sc';">
                <input type="button" class="btn btn-primary" value="Compose eMail" onclick="location.href='/sc/{$sc.id}/email/compose'" style= "margin: 7px; cursor: pointer;">
		<input type="button" class="btn btn-primary" value="Edit" style="margin: 7px; cursor: pointer;" onclick="location.href='/sc/{$sc.id}/edit'">
	</li>	
</ul>
