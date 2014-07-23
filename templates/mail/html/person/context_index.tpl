<div class="footer-left">
    {if isset($count)}{number value=$count zero='' e0='persons' e1='person' e2='persons'}{/if}
    {if !empty($pager_pages)}<br>{include file="templates/layouts/controls/control_pagination1.tpl" pages=$pager_pages path=$pager_path}{/if}        
</div>
<div class="footer-right">
    <input type="button" class="btn100" value="Add" onclick="location.href='/person/add';">
</div>