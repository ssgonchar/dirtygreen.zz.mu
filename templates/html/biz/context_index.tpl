<!-- <div class="footer-left">
{if isset($count)}{number value=$count zero='' e0='BIZs' e1='BIZ' e2='BIZs'}{/if}
{if !empty($pager_pages)}<br>{include file="templates/layouts/controls/control_pagination1.tpl" pages=$pager_pages path=$pager_path}{/if}        
</div>
<div class="footer-right">
<input type="button" class="btn100o" value="Add BIZ" onclick="location.href='/biz/add';">
</div>
-->
<ul class="nav navbar-nav footer_panel">
    <li>
        <span class='badge'>
        {if $count > 0}{number value=$count zero='' e0='BIZs' e1='BIZ' e2='BIZs'}{else}0 BIZs{/if}
    </span>
</li>
<li>
{if !empty($pager_pages)}{include file="templates/layouts/controls/control_pagination1.tpl" pages=$pager_pages path=$pager_path}{/if}
</li>
</ul>
<ul class="nav navbar-nav navbar-left">
    <li>
        <input type="button" class="btn btn-success" value="Add BIZ" onclick="location.href = '/biz/add';" style="margin-top: 7px; margin-left: 5px;">     
    </li>
</ul>
<!--<ul class="nav navbar-nav navbar-right">
    <li style='margin-top: 7px;'>
        <input type="submit" name="btn_select" value="Find" class="btn100b">
    </li>	
</ul>-->