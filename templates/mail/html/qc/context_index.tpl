<div class="footer-left">
{if isset($count)}{number value=$count zero='' e0='QCs' e1='QC' e2='QCs'}{/if}
{if !empty($pager_pages)}<br>{include file="templates/layouts/controls/control_pagination1.tpl" pages=$pager_pages path=$pager_path}{/if}
</div>
<div class="footer-right">
{*    <input type="button" class="btn200 selected-control" value="Create Original Certificate" style="margin-left: 10px; display: none;" onclick="redirect_selected('qc', '/oc/add/qc:');">	*}
    <input type="button" class="btn100o" value="Add QC" style="margin-left: 10px;" onclick="location.href='/qc/add';">
</div>