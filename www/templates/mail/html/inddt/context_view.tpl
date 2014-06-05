<div class="footer-left">
    {include file='templates/layouts/controls/control_document_timestamp.tpl' doc=$form}
</div>
<div class="footer-right">
    <input type="button" class="btn100" value="Back" style="margin-left: 10px; cursor: pointer;" onclick="location.href='/inddt'" />
    <input type="button" class="btn100o" value="Edit" style="margin-left: 10px; cursor: pointer;" onclick="location.href='/inddt/{$form.id}/edit'">
</div>