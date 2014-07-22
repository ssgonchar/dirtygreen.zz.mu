<div class="footer-left">
	{if !empty($list)}{number value=count($list) e0='objectives' e1='objective' e2='objectives'}{/if}
</div>
<div class="footer-right">
    <input type="button" name="btn_edit" class="btn100o" value="Add" onclick="location.href='/objective/add';">
</div>