

<ul class="nav navbar-nav footer_panel">
    <li style='margin-top: 7px;'>
        {include file='templates/html/item/control_navigation.tpl' page='about' object_id=$item.id}
    </li>
</ul>
<ul class="nav navbar-nav navbar-right" style='margin-top: 7px;'>
    <li>
        <input type="submit" name="btn_cancel" class="btn btn-default" value="Go Back"{if !$item.inuse && empty($item.parent_id)} onclick="return confirm('Am I sure ?');"{/if}>
    
    {if !empty($item.parent_id)}<input type="submit" name="btn_convert" class="btn btn-default" value="Convert To Real" style="margin-left: 10px;" onclick="return confirm('Am I sure ?');">{/if}
    {if empty($item.order_id) || !empty($item.parent_id)}<input type="submit" name="btn_remove" class="btn btn-primary" value="Remove" style="margin-left: 10px;" onclick="return confirm('Am I sure ?');">{/if}
    {if !$item.inuse && empty($item.parent_id)}<input type="submit" name="btn_save" class="btn btn-primary" value="Save" style="margin-left: 10px;" onclick="return confirm('Am I sure ?');">{/if}
    </li>	
</ul>