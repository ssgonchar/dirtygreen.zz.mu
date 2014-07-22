
<ul class="nav navbar-nav footer_panel">
    <!-- {include file="templates/html/chat/control_navigation.tpl" page=$page} -->
    <li>
	{if !empty($pager_pages)}{include file="templates/layouts/controls/control_pagination1.tpl" pages=$pager_pages path=$pager_path}{/if}
    </li>
</ul>
<ul class="nav navbar-nav navbar-right">
	<li>
		<input type="button" class="btn150o" value="Write Message" onclick="show_chat_modal('chat', 0);">
	</li>	
</ul>