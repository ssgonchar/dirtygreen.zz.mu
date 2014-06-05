<div class="footer-left">
{if !empty($count)}{number value=$count zero='' e0='records' e1='record' e2='records'}{/if}
{if !empty($pager_pages)}<br>{include file="templates/layouts/controls/control_pagination1.tpl" pages=$pager_pages path=$pager_path}{/if}
</div>
<div class="footer-right"></div>