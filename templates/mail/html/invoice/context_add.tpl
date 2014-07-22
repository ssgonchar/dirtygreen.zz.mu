<div class="footer-left"></div>
<div class="footer-right">
    {if !empty($source_doc)}
    <input type="button" class="btn100" value="Go Back" style="margin-left: 10px; cursor: pointer;" onclick="location.href='/{$source_doc.alias}/{$source_doc.id}';">
    {else}
    <input type="button" class="btn100" value="Go Back" style="margin-left: 10px; cursor: pointer;" onclick="location.href='/invoice';">
    <input type="submit" name="btn_additems" class="btn100" value="Add Items" style="margin-left: 20px; cursor: pointer;">
    {/if}
    <input type="submit" name="btn_save" class="btn100o" value="Save" style="margin-left: 20px; cursor: pointer;">
</div>