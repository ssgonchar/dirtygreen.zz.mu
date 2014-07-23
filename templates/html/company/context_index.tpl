
<ul class="nav navbar-nav footer_panel">
	<li>
		<span class='badge'>
			{if isset($count)}{number value=$count zero='' e0='companies' e1='company' e2='companies'}{/if}
		</span>
	</li>
	<li>
		{if !empty($pager_pages)}{include file="templates/layouts/controls/control_pagination1.tpl" pages=$pager_pages path=$pager_path}{/if}
	</li>
</ul>
<ul class="nav navbar-nav navbar-right" style='margin: 7px;'>
	<li>
		<input type="button" class="btn btn-primary" value="Add" onclick="location.href='/company/add';">
	</li>	
</ul>