{if !empty($biz_menu)}
    {foreach from=$biz_menu item=group}
        <ul class="nav nav-list tree" style="border-left: 1px dotted #cccccc;">
            <li>
                <img src="/img/icons/cross-small.png" style="cursor: pointer; margin-top: 5px;" class="find-biz-remove-group pull-left" data-group-id="{$group.id}">                                                    
                <label class="tree-toggler nav-header" style='cursor:pointer;'>
                    {$group.title} <a data-group-id="{$group.id}" class="find-biz-group-link">Show for all BIZs</a>
                </label>


                <ul class="nav nav-list tree" style="border-left: 1px dotted #cccccc; display: none;">
                    {foreach from=$group.bizs item=biz}
                                                                <li>

                                                                    <a class="tag-biz" style="vertical-align: top; margin-right: 3px;" data-biz-id="{$biz.biz_id}" href="/biz/{$biz.biz_id}/emailmanager/filter/type:0;" target="_blank">{$biz.biz.doc_no_full}</a>
                                                                    <img src="/img/icons/cross-small.png" style="cursor: pointer;" class="find-biz-remove-biz " data-biz-id="{$biz.biz_id}" data-group-id="{$group.id}">
                                                                </li>
                    {/foreach}
                </ul>

            </li>
        </ul>
    {/foreach}
{/if}