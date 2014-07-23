
<ul class="nav navbar-nav footer_panel">
	<li>
		<span class='badge'>
			{if !empty($list)}{number value=count($list) e0='markets' e1='market' e2='markets'}{/if}
		</span>
	</li>
</ul>
<ul class="nav navbar-nav navbar-right" style='margin: 7px;'>
	<li>
		<input type="button" name="btn_edit" class="btn btn-success" value="Add" onclick="location.href='/market/add';">
	</li>	
</ul>