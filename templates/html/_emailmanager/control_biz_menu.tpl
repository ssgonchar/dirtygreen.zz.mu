                                    {if !empty($biz_menu)}
                                        {foreach from=$biz_menu item=group}
                                            <ul class="nav nav-list tree" style="border-left: 1px dotted #cccccc;">
                                                <li>
                                                    <label class="tree-toggler nav-header" style='cursor:pointer;'>{$group.title} <a data-group-id="{$group.id}" class="find-biz-group-link">show all</a></label>
                                                    

                                                    <ul class="nav nav-list tree" style="border-left: 1px dotted #cccccc; display: none;">
                                                        {foreach from=$group.bizs item=biz}
                                                            <li>
                                                                <a class="tag-biz" style="vertical-align: top; margin-right: 3px;" data-biz-id="{$biz.biz_id}" href="/biz/{$biz.biz_id}/emailmanager/filter/type:0;" target="_blank">{$biz.biz.doc_no_full}</a><img src="/img/icons/cross-small.png" style="cursor: pointer;" class="remove-object-alias" data-object-alias="biz" data-object-id="{$biz.biz_id}">
                                                            </li>
                                                        {/foreach}
                                                    </ul>

                                                </li>
                                            </ul>
                                        {/foreach}
                                    {/if}