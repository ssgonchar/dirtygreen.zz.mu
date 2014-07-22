<ul class="nav navbar-nav footer_panel" style='margin-top: 6px;'>
	<li>
		{include file='templates/layouts/controls/control_document_timestamp.tpl' doc=$form}
	</li>
</ul>
<ul class="nav navbar-nav navbar-right">
	<li>
		<input type="button" class="btn btn-default" value="To List" style="margin: 7px; cursor: pointer;" onclick="location.href='/company'" />
                <input type="button" class="btn btn-success" style="margin: 7px;" value="Add Person" onclick="location.href='/person/addfromcompany/{$form.id}';">
		<input type="button" class="btn btn-primary" value="Edit" style="margin: 7px;" onclick="location.href='/company/{$form.id}/edit';">
	</li>	
</ul>