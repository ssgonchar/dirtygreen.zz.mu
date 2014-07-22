<ul class="nav navbar-nav footer_panel">
	<li>
		<span class='badge'>
			{if $count > 0}{number value=$count zero='' e0='persons' e1='person' e2='persons'}{else}0 Persons{/if}
                        
		</span>
	</li>
	<li>
		{if !empty($pager_pages)}<br>{include file="templates/layouts/controls/control_pagination1.tpl" pages=$pager_pages path=$pager_path}{/if}        
	</li>
</ul>
<ul class="nav navbar-nav navbar-right" style='margin-top: 7px;'>
	<li>
		<input type="button" class="btn btn-success " value="Add" onclick="location.href='/person/add';">
	</li>	
</ul>