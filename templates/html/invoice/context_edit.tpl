
<ul class="nav navbar-nav footer_panel" style='margin-top: 6px;'>
	<li>
		
	</li>
</ul>
<ul class="nav navbar-nav navbar-right">
	<li>
		{*
		 <input type="button" class="btn100" value="Go Back" style="margin-left: 10px; cursor: pointer;" onclick="location.href='/invoice';">
		*} 
		<input type="button" class="btn btn-default" value="To List" style="margin: 7px; cursor: pointer;" onclick="location.href='/invoice'" />
                {if $allow_add_items}
		<input type="submit" name="btn_additems" class="btn btn-primary" value="Add Items" style="margin: 7px; cursor: pointer;">
		{/if}
		<input type="submit" name="btn_save" class="btn btn-primary" value="Save" style="margin: 7px; cursor: pointer;">
	</li>	
</ul>