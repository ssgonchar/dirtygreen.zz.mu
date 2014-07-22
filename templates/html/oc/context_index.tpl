

<ul class="nav navbar-nav footer_panel">
	<li>
		<span class='badge'>
			{number zero='No Original QC' e0='Original QCs' e1='Original QC' e2='Original QCs' value=$count}
		</span>
	</li>
	<li>
		{if !empty($pager_pages)}{include file="templates/layouts/controls/control_pagination1.tpl" pages=$pager_pages path=$pager_path}{/if}
	</li>
</ul>
<ul class="nav navbar-nav navbar-right">
	<li>
		<input type="button" class="btn btn-success" value="Create" style="margin: 7px;" onclick="location.href='/oc/add';">
	</li>	
</ul>