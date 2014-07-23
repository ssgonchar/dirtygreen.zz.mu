<div class="footer-left">
    {if isset($count)}{number value=$count zero='No RAs' e0='RAs' e1='RA' e2='RAs'}{/if}
    {if !empty($pager_pages)}<br>{include file="templates/layouts/controls/control_pagination1.tpl" pages=$pager_pages path=$pager_path}{/if}
</div>
<div class="footer-right"></div>