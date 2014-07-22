<div class="footer-left">
    {include file='templates/layouts/controls/control_document_timestamp.tpl' doc=$sc}
</div>
<div class="footer-right">
    <input type="button" class="btn150" value="Compose eMail" onclick="location.href='/sc/{$sc.id}/email/compose'" style="cursor: pointer;">
    <input type="button" class="btn100o" value="Edit" style="margin-left: 10px; cursor: pointer;" onclick="location.href='/sc/{$sc.id}/edit'">
</div>