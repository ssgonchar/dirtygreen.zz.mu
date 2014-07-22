<div class="footer-left">
{if $product_id > 0}
    {if empty($form.children_count)}
    <input type="button" class="btn100" value="Remove" onclick="if (confirm('Remove product ?')) location.href='/product/{$product_id}/remove';">
    {else}
    To delete this product please delete all sub-product first .
    {/if}
{/if}
</div>
<div class="footer-right">
    <input type="button" class="btn100" value="Don't Save" onclick="location.href='/products';">
    <input type="submit" name="btn_save" class="btn100o" value="Save" style="margin-left: 10px;">
</div>