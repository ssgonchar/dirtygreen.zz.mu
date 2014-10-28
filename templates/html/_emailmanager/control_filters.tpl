<span class="btn btn-default btn-sm add-new-filter">Add new</span>
<table class="table">
    <thead>
        <tr>
            <th>From</th>
            <th>To</th> 
            <th>Keyword</th>
            <th></th>
        </tr>
        <tr>
            <th style="width:100%;" colspan="4">BIZ</th>
        </tr>
    </thead>
    <tbody>
        {foreach from=$filter_list item=row}
            <tr>
                <td>
                    {$row.efilter.params_array.from|undef}
                </td>
                <td>
                    {$row.efilter.params_array.to|undef}
                </td>
                <td>
                    {$row.efilter.params_array.subject|undef}
                </td>
                <td style="width:50px; vertical-align: middle; text-align: center; background: #e7f1f7;" rowspan="2">
                    <span class="btn btn-primary btn-sm btn-filter-edit" data-filter-id="{$row.efilter.id}"><i class="glyphicon glyphicon-pencil"></i></span>
                    <span class="btn btn-danger btn-sm btn-filter-delete" data-filter-id="{$row.efilter.id}"><i class="glyphicon glyphicon-trash"></i></span>
                </td>
            </tr>
            <tr>
                <td colspan="3" style="border-top: 1px dotted #dedede;">
                    {foreach from=$row.efilter.tags_array item=item name=tags_array}
                        {if $item.object_alias=='biz'}
                            <span id="{$item.object_alias|escape:'html'}-{$item.object_id}" style="margin-right: 10px;">
                                <input type="hidden" name="objects[{$item.object_alias|escape:'html'}-{$item.object_id}]" class="{$item.object_alias|escape:'html'}-object" value="{$item.object_id}">
                                <a class="tag-{if in_array($item.object_alias, array('biz', 'company', 'order', 'person'))}{$item.object_alias}{else}document{/if}" data-biz-id="{$item.object_id}" style="vertical-align: top; margin-right: 3px;" href="/{$item.object_alias|escape:'html'}/{$item.object_id}/emailmanager/filter/type:0;" target="_blank">{$item.title}</a>{if !$smarty.foreach.tags_array.last}, {/if}
                                {*<img src="/img/icons/cross-small.png" style="cursor: pointer;" class="remove-object-alias-tbl" data-object-alias="{$item.object_alias|escape:'html'}" data-object-id="{$item.object_id}" data-email-id="{$row.efilter.id}" data-tag="{$row.efilter.tags}">*}
                            </span>
                        {/if}                    
                    {/foreach}
                </td>                
            </tr>
        {/foreach}
    </tbody>
</table>