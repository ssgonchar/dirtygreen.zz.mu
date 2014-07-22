<!-- <div class="footer-left">
{number zero='no invoices' e0='invoices' e1='invoice' e2='invoices' value=$count}
{if !empty($pager_pages)}<br />{include file="templates/layouts/controls/control_pagination1.tpl" pages=$pager_pages path=$pager_path}{/if}
</div>
<div class="footer-right">
    <input type="button" class="btn100o" value="Create" style="margin-left: 10px;" onclick="location.href='/supplierinvoice/add';">
</div> -->


<ul class="nav navbar-nav footer_panel">
	<li>
		<span class='badge'>
			{number zero='no invoices' e0='invoices' e1='invoice' e2='invoices' value=$count}
		</span>
	</li>
	<li>
			{if !empty($pager_pages)}<br />{include file="templates/layouts/controls/control_pagination1.tpl" pages=$pager_pages path=$pager_path}{/if}
	</li>
</ul>
<ul class="nav navbar-nav navbar-right">
	<li>
		<input type="button" class="btn100o" value="Create" style="margin-left: 10px;" onclick="location.href='/supplierinvoice/add';">
	</li>	
</ul>