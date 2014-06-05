
<ul class="nav navbar-nav footer_panel" style='margin-top: 6px;'>
	<li>
		{include file='templates/layouts/controls/control_document_timestamp.tpl' doc=$ra}
	</li>
</ul>
<ul class="nav navbar-nav navbar-right">
	
		{if !empty($items)}
			<li>
				<input type="button" class="btn100" value="Edit Items" style="margin-left: 10px; margin-top: 6px; cursor: pointer;" onclick="location.href='/target/ra:{$ra.id}/item/edit/{foreach from=$items item=row name=list}{$row.steelitem.id}{if !$smarty.foreach.list.last},{/if}{/foreach}'" />
			</li>
		{/if}
		{*{if $ra.stock_object_alias == 'mam'}*}
		{if empty($ra.has_cmr)}<li><input type="button" class="btn100" value="Create CMR" style="margin-left: 10px; margin-top: 6px; cursor: pointer;" onclick="location.href='/cmr/add/ra:{$ra.id}';"></li>{/if}
		{if empty($ra.has_ddt)}<li><input type="button" class="btn100" value="Create DDT" style="margin-left: 10px; margin-top: 6px; cursor: pointer;" onclick="location.href='/ddt/add/ra:{$ra.id}';"></li>{/if}
		{*{/if}*}
		<li><input type="button" class="btn150" value="Compose eMail" onclick="location.href='/ra/{$ra.id}/email/compose'" style="margin-left: 10px; cursor: pointer;"></li>
		{if $ra.status_id == $smarty.const.RA_STATUS_OPEN || ($ra.status_id == $smarty.const.RA_STATUS_PENDING && $smarty.session.user.role_id <= $smarty.const.ROLE_MODERATOR)}
		<li><input type="button" class="btn100o" value="Edit" style="margin-left: 10px; margin-top: 6px; cursor: pointer;" onclick="location.href='/ra/{$ra.id}/edit'"></li>
		{/if}
	</li>	
</ul>