
<ul class="nav navbar-nav footer_panel" style='margin-top: 6px;'>
	<li>
		{include file='templates/layouts/controls/control_document_timestamp.tpl' doc=$ddt}
	</li>
</ul>
<ul class="nav navbar-nav navbar-right">
	<li>
		 <input type="button" class="btn btn-default" value="To List" style="margin: 7px; cursor: pointer;" onclick="location.href='/ddt'" />
		 {if $ddt.is_deleted != 1}
		 <input type="button" class="btn btn-primary" value="Edit" style="margin: 7px; cursor: pointer;" onclick="location.href='/ddt/{$ddt.id}/edit'">
		 {/if}
	 </li>	
</ul>