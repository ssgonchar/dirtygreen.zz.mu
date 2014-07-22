{*
<div class="footer-left">
{number zero='No Stock Offers' e0='Stock Offers' e1='Stock Offer' e2='Stock Offers' value=$count}
{if !empty($pager_pages)}<br />{include file="templates/layouts/controls/control_pagination1.tpl" pages=$pager_pages path=$pager_path}{/if}
</div>
<div class="footer-right">
    <input type="button" class="btn150o" value="Create Stock Offer" style="margin-left: 10px;" onclick="location.href='/stockoffer/add';">
</div>
*}
<div class="navbar navbar-default navbar-fixed-bottom" role="navigation">	
	<div class="container">
		<div class="navbar-collapse collapse">
			<ul class="nav navbar-nav footer_panel">
				<li>
					<span class="badge">
						{number zero='No Stock Offers' e0='Stock Offers' e1='Stock Offer' e2='Stock Offers' value=$count}
					</span>
				</li>
				<li>
					{if !empty($pager_pages)}{include file="templates/layouts/controls/control_pagination1.tpl" pages=$pager_pages path=$pager_path}{/if}
				</li>				
			</ul>
			<ul class="nav navbar-nav navbar-right">
				<li>
					<input type="button" class="btn150o" value="Create Stock Offer" style="margin-left: 10px;" onclick="location.href='/stockoffer/add';">
				</li>
			</ul>
		</div>
	</div>
</div>