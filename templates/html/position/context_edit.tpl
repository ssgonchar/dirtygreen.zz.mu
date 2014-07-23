

<ul class="nav navbar-nav footer_panel" style='margin-top: 6px;'>
	<li>
		{if !$position.steelposition.inuse}<input type="button" class="btn btn-success" value="Add Item" onclick="position_item_add({$position.steelposition.id});">{/if}
	</li>
</ul>
<ul class="nav navbar-nav navbar-right">
	<li>
		<input type="submit"  style='margin: 8px;' name="btn_cancel" class="btn btn-primary" value="Go Back"{if !$position.steelposition.inuse} onclick="return confirm('Am I sure ?');"{/if}>
		{if !$position.steelposition.inuse}<input type="submit" name="btn_save" class="btn btn-primary" value="Save" style="margin-left: 20px;" onclick="return confirm('Am I sure ?');">{/if}
	</li>	
</ul>