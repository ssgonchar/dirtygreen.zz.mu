<div class="footer-left">
    
</div>
<div class="footer-right">
    <input type="button" class="btn100" value="Go Back" onclick="location.href='/order/{$order.id}';">
{if $order.status != 'co'}
    <input type="button" class="btn150" value="Edit Selected" onclick="location.href='/order/{$order.id}/position/edit/{$position.position_id}';" style="margin-left: 10px;">
    <input type="submit" name="btn_save" class="btn100o" value="Save" style="margin-left: 20px;" onclick="return confirm('Am I sure ?');">
{/if}    
</div>