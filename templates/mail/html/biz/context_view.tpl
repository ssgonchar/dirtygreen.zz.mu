<div class="footer-left">
    {include file='templates/layouts/controls/control_document_timestamp.tpl' doc=$form}
</div>
{strip}
<div class="footer-right">
	{if $form.parent_id ==0}
		<input type="button" value="Add Sub BIZ" onclick="location.href='/biz/{$form.id}/addsubbiz';" class="btn100" style="margin-right: 20px;">
	{/if}
    <input type="button" value="To List" onclick="location.href='/bizes';" class="btn100">
    <input type="button" class="btn100o" value="Edit" style="margin-left: 20px;" onclick="location.href='/biz/{$form.id}/edit';">
</div>
{/strip}