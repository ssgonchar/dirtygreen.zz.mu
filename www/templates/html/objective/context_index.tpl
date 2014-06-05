
<ul class="nav navbar-nav footer_panel">
	<li>
		<span class='badge'>
			{if !empty($list)}{number value=count($list) e0='objectives' e1='objective' e2='objectives'}{/if}
		</span>
	</li>
</ul>
<ul class="nav navbar-nav navbar-right">
	<li>
		<input type="button" name="btn_edit" class="btn100o" value="Add" onclick="location.href='/objective/add';">
	</li>	
</ul>