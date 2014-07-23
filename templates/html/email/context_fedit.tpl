
<ul class="nav navbar-nav footer_panel" style='margin-top: 6px;'>
	<li>
		
	</li>
</ul>
<ul class="nav navbar-nav navbar-right">
	<li>
		<input type="button" class="btn100" value="Cancel" onclick="location.href='{if isset($fromemail)}/email/{$fromemail}{else}/email/filter{/if}';" style="cursor: pointer;">
		 {*<input type="submit" name="btn_save" class="btn100o" value="Save" style="margin-left: 10px; cursor: pointer;">*}
		 <input type="hidden" name="btn_save" value="Save" />
		 <input type="button" class="btn100o" value="Save" style="margin-left: 10px; cursor: pointer;" onclick="document.getElementById('mainform').submit();">
	</li>	
</ul>