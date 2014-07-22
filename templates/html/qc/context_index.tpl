
<ul class="nav navbar-nav footer_panel">
	<li>
		<span class='badge'>
			{if isset($count)}{number value=$count zero='' e0='QCs' e1='QC' e2='QCs'}{/if}
		</span>
	</li>
	<li>
		{if !empty($pager_pages)}<br>{include file="templates/layouts/controls/control_pagination1.tpl" pages=$pager_pages path=$pager_path}{/if}
	</li>
</ul>
<ul class="nav navbar-nav navbar-right">
	<li>
		{* <input type="button" class="btn200 selected-control" value="Create Original Certificate" style="margin-left: 10px; display: none;" onclick="redirect_selected('qc', '/oc/add/qc:');">	*}
		<input type="button" class="btn btn-success" value="Add QC" style="margin: 7px;" onclick="location.href='/qc/add';">
	</li>	
</ul>