
<ul class="nav navbar-nav footer_panel" style='margin-top: 6px;'>
	<li>
		
	</li>
</ul>
<ul class="nav navbar-nav navbar-right">
	<li>
		 <input type="button" class="btn100" value="Back" style="margin-left: 10px; cursor: pointer;" onclick="location.href='/cmr'" />
		 {if $cmr.is_deleted != 1}
		 <input type="button" class="btn100o" value="Edit" style="margin-left: 10px; cursor: pointer;" onclick="location.href='/cmr/{$cmr.id}/edit'">
		 {/if}
	 </li>	
</ul>