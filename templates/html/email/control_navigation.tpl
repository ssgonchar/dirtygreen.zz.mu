{if !empty($object_alias) && !empty($object_id)}

{else}
	<li>
		<span class="badge"><span class="choosen-items-stats" style="display: none;"><span class="cis-checked">0</span>&nbsp;of&nbsp;</span>{if isset($object_stat)}{number value=$object_stat.emails zero='' e0='emails' e1='email' e2='emails'}{/if}</span>
	</li>
	<li>
		{if !empty($pager_pages)}{include file="templates/layouts/controls/control_pagination1.tpl" pages=$pager_pages path=$pager_path}{/if}
		{/if}
	</li>
