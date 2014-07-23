{*<!-- <div class="footer-left navbar-nav">
    {if isset($count)}
        {number value=$count zero='' e0='orders' e1='order' e2='orders'}&nbsp;&nbsp; 
        {if isset($total_qtty)}{$total_qtty}{else}0{/if}</span> pcs&nbsp;&nbsp;
        {if isset($total_weight)}{$total_weight|number_format:2}{else}0{/if}</span> <span class="lbl-wgh">{if isset($weight_unit)}{$weight_unit|wunit}{/if}</span>&nbsp;&nbsp;
        {if isset($total_value)}{$total_value|number_format:2}{else}0{/if}</span> <span class="lbl-cur">{if isset($currency)}{$currency|cursign}{/if}</span>
    {/if}
    {if !empty($pager_pages)}<br>{include file="templates/layouts/controls/control_pagination1.tpl" pages=$pager_pages path=$pager_path}{/if}        
</div>
<div class="footer-right navbar-right">
    <input type="submit" name="btn_create_ra" class="btn100 ra-create" style="display: none; margin-right: 10px;" value="Create RA" />
    <input type="button" name="btn_edit" class="btn150o" value="Create New Order" onclick="location.href='/order/neworder';">
</div> 

<ul class="nav navbar-nav">
    {if isset($count)}
        <li>
			<span class="badge">{number value=$count zero='' e0='orders' e1='order' e2='orders'}</span>
		</li>
        <li>
			<span class="badge">{if isset($total_qtty)}{$total_qtty}{else}0{/if} pcs</span>
		</li>
        <li>
			{if isset($total_weight)}{$total_weight|number_format:2}{else}0{/if}</span> <span class="lbl-wgh">{if isset($weight_unit)}{$weight_unit|wunit}{/if}</span>
		</li>
        <li>
			{if isset($total_value)}{$total_value|number_format:2}{else}0{/if}</span> <span class="lbl-cur">{if isset($currency)}{$currency|cursign}{/if}</span>
		</li>
    {/if}
    {if !empty($pager_pages)}<br>{include file="templates/layouts/controls/control_pagination1.tpl" pages=$pager_pages path=$pager_path}{/if}        
</ul>

<ul class="nav navbar-nav navbar-right">
    <input type="submit" name="btn_create_ra" class="btn100 ra-create" style="display: none; margin-right: 10px;" value="Create RA" />
    <input type="button" name="btn_edit" class="btn150o" value="Create New Order" onclick="location.href='/order/neworder';">
</ul>-->*}
<div class="navbar navbar-default navbar-fixed-bottom" role="navigation">
    <div class="container">

        <div class="navbar-collapse collapse">
            <ul class="nav navbar-nav footer_panel">
                <li><span class="badge">{number value=$count zero='' e0='orders' e1='order' e2='orders'}</span></li>
				<li>
					<span class="badge">{if isset($total_qtty)}{$total_qtty}{else}0{/if} pcs</span>
				</li>     
				<li>
					<span class="badge">
						<span>
						{if isset($total_weight)}
							{$total_weight|number_format:2}
						{else}
							0
						{/if}
						</span>
						<span class="lbl-wgh">
						{if isset($weight_unit)}
							{$weight_unit|wunit}
						{/if}
						</span>
					</span>
				</li>
				<li>
					<span class="badge">
						<span>
						{if isset($total_value)}
							{$total_value|number_format:2}
						{else}
							0
						{/if}
						</span>
						<span class="lbl-cur">
						{if isset($currency)}
							{$currency|cursign}
						{/if}
						</span>
					</span>
				</li> 
            </ul>
            <ul class="nav navbar-nav navbar-right">
				<li>
                                    <input type="button" name="btn_edit" class="btn btn-success" value="Create New Order" onclick="location.href='/order/neworder';" style="margin: 7px">
                </li>			
				<li>
					<input type="submit" name="btn_create_ra" class="btn100 ra-create" style="display: none; margin-right: 10px;" value="Create RA" />
				</li>
                                
            </ul>
        </div><!--/.nav-collapse -->
    </div>
</div>
                                                