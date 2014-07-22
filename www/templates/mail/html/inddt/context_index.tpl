<div class="footer-left">
{if isset($count)}{number value=$count zero='' e0='InDDTs' e1='InDDT' e2='InDDTs'}{/if}
{if !empty($pager_pages)}<br>{include file="templates/layouts/controls/control_pagination1.tpl" pages=$pager_pages path=$pager_path}{/if}
</div>
<div class="footer-right">
    <input type="button" class="btn200 selected-control" value="Create Original Certificate" style="margin-left: 10px; display: none;" onclick="redirect_selected('inddt', '/oc/add/inddt:');">
    <input type="button" class="btn200 selected-control" value="Create Suplier Invoice" style="margin-left: 10px; display: none;" onclick="redirect_selected('inddt', '/supplierinvoice/add/inddt:');">
    <input type="button" class="btn100o" value="Create" style="margin-left: 10px;" onclick="location.href='/inddt/add';">
</div>