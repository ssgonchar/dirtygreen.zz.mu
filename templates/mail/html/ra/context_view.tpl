<div class="footer-left" style="width: 30%;">
{include file='templates/layouts/controls/control_document_timestamp.tpl' doc=$ra}
</div>
<div class="footer-right">
    {if !empty($items)}<input type="button" class="btn100" value="Edit Items" style="margin-left: 10px; cursor: pointer;" onclick="location.href='/target/ra:{$ra.id}/item/edit/{foreach from=$items item=row name="list"}{$row.steelitem.id}{if !$smarty.foreach.list.last},{/if}{/foreach}'" />{/if}
    {if $ra.stock_object_alias == 'mam'}
    {if empty($ra.has_ddt)}<input type="button" class="btn100" value="Create CMR" style="margin-left: 10px; cursor: pointer;" onclick="location.href='/cmr/add/ra:{$ra.id}';">{/if}
    {if empty($ra.has_cmr)}<input type="button" class="btn100" value="Create DDT" style="margin-left: 10px; cursor: pointer;" onclick="location.href='/ddt/add/ra:{$ra.id}';">{/if}
    {/if}
    <input type="button" class="btn150" value="Compose eMail" onclick="location.href='/ra/{$ra.id}/email/compose'" style="margin-left: 10px; cursor: pointer;">
    {if $ra.status_id == $smarty.const.RA_STATUS_OPEN || ($ra.status_id == $smarty.const.RA_STATUS_PENDING && $smarty.session.user.role_id <= $smarty.const.ROLE_MODERATOR)}
    <input type="button" class="btn100o" value="Edit" style="margin-left: 10px; cursor: pointer;" onclick="location.href='/ra/{$ra.id}/edit'">
    {/if}
</div>