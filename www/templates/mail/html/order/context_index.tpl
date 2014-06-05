<div class="footer-left">
    {if isset($count)}
        {number value=$count zero='' e0='orders' e1='order' e2='orders'}&nbsp;&nbsp; 
        {if isset($total_qtty)}{$total_qtty}{else}0{/if}</span> pcs&nbsp;&nbsp;
        {if isset($total_weight)}{$total_weight|number_format:2}{else}0{/if}</span> <span class="lbl-wgh">{if isset($weight_unit)}{$weight_unit|wunit}{/if}</span>&nbsp;&nbsp;
        {if isset($total_value)}{$total_value|number_format:2}{else}0{/if}</span> <span class="lbl-cur">{if isset($currency)}{$currency|cursign}{/if}</span>
    {/if}
    {if !empty($pager_pages)}<br>{include file="templates/layouts/controls/control_pagination1.tpl" pages=$pager_pages path=$pager_path}{/if}        
</div>
<div class="footer-right">
    <input type="submit" name="btn_create_ra" class="btn100 ra-create" style="display: none; margin-right: 10px;" value="Create RA" />
    <input type="button" name="btn_edit" class="btn150o" value="Create New Order" onclick="location.href='/order/neworder';">
</div>