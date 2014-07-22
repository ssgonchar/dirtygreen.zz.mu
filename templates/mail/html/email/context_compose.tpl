<div class="footer-left">
</div>
<div class="footer-right">
    <input type="button" class="btn100" value="Don't Save" onclick="location.href='{if !empty($object_alias)}/{$object_alias}/{$object_id}{else}/emails{/if}';" style="cursor: pointer;">
    {*<input type="submit" name="btn_save" class="btn100o" value="Save" style="margin-left: 10px; cursor: pointer;">*}
    <input type="hidden" name="btn_save" value="Save" />
    <input type="button" class="btn100o" value="Save" style="margin-left: 10px; cursor: pointer;" onclick="document.getElementById('mainform').submit();">
</div>