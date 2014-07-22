<div class="footer-left">
    {if isset($count)}{number value=$count zero='' e0='sc' e1='sc' e2='sc'}{/if}
    {if !empty($pager_pages)}<br>{include file="templates/layouts/controls/control_pagination1.tpl" pages=$pager_pages path=$pager_path}{/if}    
</div>
<div class="footer-right">
    {*<input type="button" class="btn100" value="Add" onclick="location.href='/company/add';">*}
</div>