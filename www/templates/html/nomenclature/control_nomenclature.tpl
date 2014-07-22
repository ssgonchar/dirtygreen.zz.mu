{if !empty($list)}
{foreach from=$list item=row}
	<tr style='cursor: default;'>
		<td style='text-align: left;'>{$row.nomenclature.title}</td>
		<td style='text-align: left;'>{$row.nomenclature.description}</td>
		<td>{$row.nomenclature.modified_at|date_human}<br>by {if isset($row.nomenclature.modifier)}{$row.nomenclature.modifier.full_login}{else}<i>unknown</i>{/if}</td>
		<td style='padding: 0px;'><a class="edit" onclick="edit_nomenclature(event, {$row.nomenclature.id});" style='cursor: pointer;'></a><a href="javascript: void(0);" onclick="if (confirm('Are you sure?')) location.href='/nomenclature/{$row.nomenclature.id}/remove';" class="delete"></a></td>
	</tr>
{/foreach}
{/if}