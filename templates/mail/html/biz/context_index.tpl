<div class="footer-left">
    {if isset($count)}{number value=$count zero='' e0='BIZs' e1='BIZ' e2='BIZs'}{/if}
    {if !empty($pager_pages)}<br>{include file="templates/layouts/controls/control_pagination1.tpl" pages=$pager_pages path=$pager_path}{/if}        
</div>
<div class="footer-right">
    <input type="button" class="btn100o" value="Add BIZ" onclick="location.href='/biz/add';">
</div>