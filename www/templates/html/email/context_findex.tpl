
<ul class="nav navbar-nav footer_panel" style='margin-top: 6px;'>
	<li>
		{if !empty($pager_pages)}{include file="templates/layouts/controls/control_pagination1.tpl" pages=$pager_pages path=$pager_path}{/if}
	</li>
</ul>
<ul class="nav navbar-nav navbar-right">
	<li>
		<input type="button" class="btn150o" value="Create Filter" style="margin-left: 10px" onclick="location.href='/email/filter/add';">
	</li>	
</ul>