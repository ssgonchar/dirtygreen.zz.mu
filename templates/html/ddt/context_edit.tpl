<ul class="nav navbar-nav footer_panel" style='margin-top: 6px;'>
	<li>
		
	</li>
</ul>
<ul class="nav navbar-nav navbar-right">
	<li>
		{if !empty($form.id)}
        {*<input type="submit" name="btn_dont_save" class="btn100" value="Don't Save" onclick="return confirm('Am I sure ?');">*}
        <input type="button" name="btn_dont_save" class="btn btn-default" value="Don't Save" style="margin: 7px;" onclick="if(!confirm('Am I sure ?')) return false;location.href='/ddt'">
		{/if}
    <input type="submit" name="btn_save" class="btn btn-primary" value="Save" style="margin: 7px;">
	</li>	
</ul>