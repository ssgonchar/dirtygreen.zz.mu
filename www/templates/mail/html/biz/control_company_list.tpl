<table class="form" id="{$role}s">
    {foreach from=$rowset item=row}
    <tr id="{$role}-{$row.company.id}">
        <td><input type="hidden" name="{$role}s[{$row.company.id}][company_id]" class="{$role}_id" value="{$row.company.id}"><a href="/company/{$row.company.id}">{$row.company.title}</a></td>
        <td width="20px"><img class="cursor-pointer" src="/img/icons/cross.png" onclick="remove_biz_company('{$role}', {$row.company.id});"></td>
    </tr>
    {/foreach}
    <tr id="{$role}-not-set"{if !empty($rowset)} style="display: none;"{/if}>
        <td><i>not defined</i></td>
        <td></td>
    </tr>
</table>