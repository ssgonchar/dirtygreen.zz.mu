<ul class="nav navbar-nav footer_panel">
	<li>
		<span class='badge'>
			{if isset($count)}{number value=$count zero='' e0='CMR' e1='CMR' e2='CMR'}{/if}
		</span>
	</li>
	<li>
		{if !empty($pager_pages)}{include file="templates/layouts/controls/control_pagination1.tpl" pages=$pager_pages path=$pager_path}
		{/if}
	</li>
</ul>
<ul class="nav navbar-nav navbar-right">
	<li>
		{*<input type="button" class="btn100" value="Create" style="margin-left: 10px;" onclick="location.href='/ddt/add';">*}
	</li>	
</ul>