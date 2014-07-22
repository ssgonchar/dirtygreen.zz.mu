<div class="footer-left">
</div>
<div class="footer-right">
    <input type="button" class="btn100" value="Back" style="margin-left: 10px; cursor: pointer;" onclick="location.href='/cmr'" />
    {if $cmr.is_deleted != 1}
    <input type="button" class="btn100o" value="Edit" style="margin-left: 10px; cursor: pointer;" onclick="location.href='/cmr/{$cmr.id}/edit'">
    {/if}
</div>