<div class="footer-left">
{if !empty($object_alias) && !empty($object_id)}
    {include file="templates/html/{$object_alias}/control_navigation.tpl" page="dropbox" object_id=$object_id}
{/if}
</div>
<div class="footer-right">
    <div id="fileuploader"></div>
    <input type="hidden" id="qq_object_alias" value="{$object_alias}">
    <input type="hidden" id="qq_object_id" value="{$object_id}">
</div>