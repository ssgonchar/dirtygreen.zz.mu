<div class="footer-left">
</div>
<div class="footer-right">
    {if !empty($form.id)}
        {*<input type="submit" name="btn_dont_save" class="btn100" value="Don't Save" onclick="return confirm('Am I sure ?');">*}
        <input type="button" name="btn_dont_save" class="btn100" value="Don't Save" onclick="if(!confirm('Am I sure ?')) return false;location.href='/ddt'">
    {/if}
    <input type="submit" name="btn_save" class="btn100o" value="Save" style="margin-left: 10px;">
</div>