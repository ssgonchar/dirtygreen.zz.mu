<div class="footer-left">
{if isset($count)}{number value=$count zero='' e0='CMR' e1='CMR' e2='CMR'}{/if}
{if !empty($pager_pages)}
    <br>{include file="templates/layouts/controls/control_pagination1.tpl" pages=$pager_pages path=$pager_path}
{/if}
</div>
<div class="footer-right">
    {*<input type="button" class="btn100" value="Create" style="margin-left: 10px;" onclick="location.href='/ddt/add';">*}
</div>