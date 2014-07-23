<div class="footer-left" style="width: 30%">
   {if !empty($pager_pages)}<br>{include file="templates/layouts/controls/control_pagination1.tpl" pages=$pager_pages path=$pager_path}{/if}
</div>
<div class="footer-right">
    <input type="button" class="btn150o" value="Create Filter" style="margin-left: 10px" onclick="location.href='/email/filter/add';">
</div>