<ul class="nav navbar-nav footer_panel" style='margin-top: 6px;'>
	<li>
		{include file='templates/layouts/controls/control_document_timestamp.tpl' doc=$form}
	</li>
</ul>
<ul class="nav navbar-nav navbar-right">
	<li>
		<input type="button" class="btn btn-primary" value="Edit" style="margin: 7px; cursor: pointer;" onclick="location.href='/oc/{$form.id}/edit'">
	</li>	
</ul>