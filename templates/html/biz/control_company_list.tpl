<table class="" id="{$role}s">
   {foreach from=$rowset item=row}
    <tr id="{$role}-{$row.company.id}">
            <td>
            <input type="hidden" name="{$role}s[{$row.company.id}][company_id]" class="btn-primary btn-xs {$role}_id" value="{$row.company.id}">
            <a id="{$role}-{$row.company.id}" href="/company/{$row.company.id}"  class="btn btn-primary btn-xs" style="margin: 0px 0px 5px 0px;">{$row.company.title}
                <span id="customLink" class="glyphicon glyphicon-remove" onclick="remove_biz_company('{$role}', '{$row.company.id}');"></span>
            </a>
        </td>
    </tr>
    {/foreach}
    <tr id="{$role}-not-set"{if !empty($rowset)} style="display: none;"{/if}>
        <td><i>not defined</i></td>
    </tr>
</table>
        
