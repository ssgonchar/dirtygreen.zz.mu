<div class="footer-left">
    {include file="templates/html/chat/control_navigation.tpl" page=$page}
    {if !empty($pager_pages)}<br>{include file="templates/layouts/controls/control_pagination1.tpl" pages=$pager_pages path=$pager_path}{/if}    
</div>
<div class="footer-right">
    <input type="button" class="btn150o" value="Write Message" onclick="show_chat_modal('chat', 0);">
</div>
