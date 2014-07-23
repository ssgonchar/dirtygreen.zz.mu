<div class="footer-left">
    {if !$position.steelposition.inuse}<input type="button" class="btn100" value="Add Item" onclick="position_item_add({$position.steelposition.id});">{/if}
</div>
<div class="footer-right">
    <input type="submit" name="btn_cancel" class="btn100" value="Go Back"{if !$position.steelposition.inuse} onclick="return confirm('Am I sure ?');"{/if}>
    {if !$position.steelposition.inuse}<input type="submit" name="btn_save" class="btn100o" value="Save" style="margin-left: 20px;" onclick="return confirm('Am I sure ?');">{/if}
</div>