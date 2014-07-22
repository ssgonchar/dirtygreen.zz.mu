<ul class="nav navbar-nav footer_panel">
	<li><strong>Total: </strong>
		<span class='badge'>
			{if !empty($list)}{number value=count($list) e0='titles' e1='title' e2='titles'}{/if}
			{if empty($list)}0 titles{/if}
		</span>
	</li>
</ul>
<ul class="nav navbar-nav navbar-right">
	<li>
		<input type="button" class="btn100o" onclick="add_nomenclature(event);" value="Add">
		<!-- <input type="button" class="btn100o" onclick="location.href='/nomenclature/add';" value="Add"> -->
	</li>	
</ul>