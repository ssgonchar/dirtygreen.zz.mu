<div class="footer-left">
    {include file='templates/layouts/controls/control_document_timestamp.tpl' doc=$form}
</div>
<div class="footer-right">
    <input type="button" class="btn150" value="Add Person" onclick="location.href='/person/addfromcompany/{$form.id}';">
    <input type="button" class="btn100o" value="Edit" style="margin-left: 10px;" onclick="location.href='/company/{$form.id}/edit';">
</div>