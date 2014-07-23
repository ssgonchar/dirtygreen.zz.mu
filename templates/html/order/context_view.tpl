<!-- <div class="footer-left" style="width: 250px">
    {include file='templates/layouts/controls/control_document_timestamp.tpl' doc=$order}
</div>
<div class="footer-right">
    {if $order.status != 'ca' && $order.status != 'co' && $order.status != 'nw'}
    <input type="button" class="btn100" value="Create SC" onclick="location.href='/sc/add/{$order.id}';">
    <input type="button" name="create_qc" class="btn100" value="Create QC" onclick="location.href='/qc/add/order:{$order.id}';" style="margin-left: 10px;">
    <input type="button" name="create_ra" class="btn100" value="Create RA" onclick="location.href='/ra/add/{$order.id}';" style="margin-left: 10px;">
    <input type="button" name="create_invoice" class="btn150" value="Create Invoice" onclick="location.href='/invoice/add/order:{$order.id}';" style="margin-left: 10px;">
    {/if}

    {if empty($order.status)}<input type="submit" name="btn_remove" class="btn100" value="Remove" style="margin-left: 10px;">{/if}

    {if $order.status != 'ca' && $order.status != 'co' &&  $order.status != 'nw'}
    <input type="button" name="btn_edit" class="btn100o" value="Edit" style="margin-left: 10px;" onclick="location.href='/order/{$order.id}/edit';">
    {/if}
</div> -->



<ul class="nav navbar-nav footer_panel" style='margin-top: 6px;'>
	<li>
		{include file='templates/layouts/controls/control_document_timestamp.tpl' doc=$order}
	</li>
</ul>
<ul class="nav navbar-nav navbar-right">
	<li>
		{if $order.status != 'ca' && $order.status != 'co' && $order.status != 'nw'}
    <input type="button" class="btn btn-primary" value="Create SC" onclick="location.href='/sc/add/{$order.id}';" style="margin: 7px;">
    <input type="button" name="create_qc" class="btn btn-primary" value="Create QC" onclick="location.href='/qc/add/order:{$order.id}';" style="margin: 7px;">
    <input type="button" name="create_ra" class="btn btn-primary" value="Create RA" onclick="location.href='/ra/add/{$order.id}';" style="margin: 7px;">
    <input type="button" name="create_invoice" class="btn btn-primary" value="Create Invoice" onclick="location.href='/invoice/add/order:{$order.id}';" style="margin: 7px;">
    {/if}

    {if empty($order.status)}<input type="submit" name="btn_remove" class="btn btn-danger" value="Remove" style="margin: 7px;">{/if}

    {if $order.status != 'ca' && $order.status != 'co' &&  $order.status != 'nw'}
    <input type="button" name="btn_edit" class="btn btn-primary" value="Edit" style="margin: 7px;" onclick="location.href='/order/{$order.id}/edit';">
    {/if}
	</li>	
</ul>