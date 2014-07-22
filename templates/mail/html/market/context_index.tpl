<div class="footer-left">
	{if !empty($list)}{number value=count($list) e0='markets' e1='market' e2='markets'}{/if}
</div>
<div class="footer-right">
    <input type="button" name="btn_edit" class="btn100" value="Add" onclick="location.href='/market/add';">
</div>