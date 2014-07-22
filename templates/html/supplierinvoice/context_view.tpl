<!-- <div class="footer-left">
    {include file='templates/layouts/controls/control_document_timestamp.tpl' doc=$form}
</div>
<div class="footer-right">
    <input type="button" class="btn100o" value="Edit" style="margin-left: 10px; cursor: pointer;" onclick="location.href='/supplierinvoice/{$form.id}/edit'">
</div> -->

	<ul class="nav navbar-nav footer_panel">
		<li style='margin-top: 7px;'>
			{include file='templates/layouts/controls/control_document_timestamp.tpl' doc=$form}
		</li>
	</ul>
	<ul class="nav navbar-nav navbar-right">
		<li>
			<input type="button" class="btn btn-primary" value="Edit" style="margin: 7px; cursor: pointer;" onclick="location.href='/supplierinvoice/{$form.id}/edit'">
		</li>	
	</ul>
