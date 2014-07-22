
<ul class="nav navbar-nav footer_panel">
	<li>
		<span class='badge'>
			{if isset($count)}{number value=$count zero='' e0='sc' e1='sc' e2='sc'}{/if}
		</span>
	</li>
	<li>
		{if !empty($pager_pages)}{include file="templates/layouts/controls/control_pagination1.tpl" pages=$pager_pages path=$pager_path}{/if}    
	</li>
</ul>
<ul class="nav navbar-nav navbar-right">
	<li>
		
	</li>	
</ul>