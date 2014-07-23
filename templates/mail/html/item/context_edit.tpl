<div class="footer-left" style="padding-top: 5px;">
    {include file='templates/html/item/control_navigation.tpl' page='about' object_id=$item.id}
</div>
<div class="footer-right">
    <input type="submit" name="btn_cancel" class="btn100" value="Go Back"{if !$item.inuse && empty($item.parent_id)} onclick="return confirm('Am I sure ?');"{/if}>
    
    {if !empty($item.parent_id)}<input type="submit" name="btn_convert" class="btn150" value="Convert To Real" style="margin-left: 10px;" onclick="return confirm('Am I sure ?');">{/if}
    {if empty($item.order_id) || !empty($item.parent_id)}<input type="submit" name="btn_remove" class="btn100" value="Remove" style="margin-left: 10px;" onclick="return confirm('Am I sure ?');">{/if}
    {if !$item.inuse && empty($item.parent_id)}<input type="submit" name="btn_save" class="btn100o" value="Save" style="margin-left: 10px;" onclick="return confirm('Am I sure ?');">{/if}
</div>