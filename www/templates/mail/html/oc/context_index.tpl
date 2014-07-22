<div class="footer-left">
{number zero='No Original QC' e0='Original QCs' e1='Original QC' e2='Original QCs' value=$count}
{if !empty($pager_pages)}<br />{include file="templates/layouts/controls/control_pagination1.tpl" pages=$pager_pages path=$pager_path}{/if}
</div>
<div class="footer-right">
    <input type="button" class="btn100o" value="Create" style="margin-left: 10px;" onclick="location.href='/oc/add';">
</div>