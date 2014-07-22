<div class="footer-left"></div>
<div class="footer-right">
    {if !empty($source_doc)}
    <input type="button" class="btn btn-default" value="To List" style="margin: 7px; cursor: pointer;" onclick="location.href='/{$source_doc.alias}/{$source_doc.id}';">
    {else}
    <input type="button" class="btn btn-default" value="To List" style="margin: 7px; cursor: pointer;" onclick="location.href='/invoice';">
    <input type="submit" name="btn_additems" class="btn btn-primary" value="Add Items" style="margin: 7px; cursor: pointer;">
    {/if}
    <input type="submit" name="btn_save" class="btn btn-primary" value="Save" style="margin: 7px; cursor: pointer;">
</div>