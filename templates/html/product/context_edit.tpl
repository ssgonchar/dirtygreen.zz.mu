
<ul class="nav navbar-nav footer_panel">
	<li>
	<span class=''>
		{if $product_id > 0}
			 {if empty($form.children_count)}
				<input type="button" class="btn btn-danger" style="margin: 7px;" value="Remove" onclick="if (confirm('Remove product ?')) location.href='/product/{$product_id}/remove';">
			 {else}
				To delete this product please delete all sub-product first .
			 {/if}
		{/if}
	</span>
	</li>
</ul>
<ul class="nav navbar-nav navbar-right" style="margin: 7px;">
	<li>
		<input type="button" class="btn btn-default" value="Don't Save" onclick="location.href='/products';">
		<input type="submit" name="btn_save" class="btn btn-primary" value="Save">
	</li>	
</ul>