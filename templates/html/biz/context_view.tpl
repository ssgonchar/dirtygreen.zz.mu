
<ul class="nav navbar-nav footer_panel" style='margin-top: 6px;'>
	<li>
		{include file='templates/layouts/controls/control_document_timestamp.tpl' doc=$form}
	</li>
</ul>
{strip}
<ul class="nav navbar-nav navbar-right">
	<li>
	{if $form.parent_id ==0}
		<input type="button" value="Add Sub BIZ" onclick="location.href='/biz/{$form.id}/addsubbiz';" class="btn btn-success" style="margin-right: 20px; margin-top: 7px">
	{/if}
		<input type="button" value="To List" onclick="javascript:history.back();"class="btn btn-default" style='margin-top: 7px'>
		<input type="button" class="btn btn-primary" value="Edit" style="margin-left: 20px; margin-top: 7px;" onclick="location.href='/biz/{$form.id}/edit';">
                
	</li>	
</ul>
{/strip}