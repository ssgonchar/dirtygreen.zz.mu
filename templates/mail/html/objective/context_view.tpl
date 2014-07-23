<div class="footer-left">
</div>
<div class="footer-right">
    <input type="button" class="btn100" value="To List" onclick="location.href='/objectives/{$form.year}{if !empty($form.quarter)}/{$form.quarter}{/if}';">
    <input type="button" name="btn_edit" class="btn100o" value="Edit" style="margin-left: 10px;" onclick="location.href='/objective/{$form.id}/edit';">
</div>