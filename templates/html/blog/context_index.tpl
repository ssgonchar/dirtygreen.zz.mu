<!-- <div class="footer-left">
{if !empty($count)}{number value=$count zero='' e0='records' e1='record' e2='records'}{/if}
{if !empty($pager_pages)}<br>{include file="templates/layouts/controls/control_pagination1.tpl" pages=$pager_pages path=$pager_path}{/if}
</div>
<div class="footer-right"></div> -->

<ul class="nav navbar-nav footer_panel">
	<li>
		<span class='badge'>
			{if !empty($count)}{number value=$count zero='' e0='records' e1='record' e2='records'}{/if}
		</span>
	</li>
	<li>
		{if !empty($pager_pages)}{include file="templates/layouts/controls/control_pagination1.tpl" pages=$pager_pages path=$pager_path}{/if}
	</li>
</ul>
<ul class="nav navbar-nav navbar-right" style='margin-top: 7px;'>
	<li>
		<input type="button" name="btn_edit" class="btn100" value="Add" onclick="location.href='/market/add';">
	</li>	
</ul>