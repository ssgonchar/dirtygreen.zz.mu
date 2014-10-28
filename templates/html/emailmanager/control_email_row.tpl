{*<td><input type="checkbox" class="select-email" data-id='1285'></td>*}
<td><i class="glyphicon glyphicon-star-empty {if $row.email.starred == 1}email-starr{else}email-starr-empty{/if}" data-id="{$row.email.id}"></i></td>
<td><b>{$row.email.sender_address}</b></td>
<td>{if isset($row.email.userdata) && $row.email.userdata.read_at > 0}{else}<b>(new) <b>{/if}{$row.email.title}</td>
            <td>
                {if $row.email.type_id == 0}All emails{/if}
                {if $row.email.type_id == 1}Inbox{/if}
                {if $row.email.type_id == 2}Sent{/if}
                {if $row.email.type_id == 3}Draft{/if}
                {if $row.email.type_id == 4}Corrupted{/if}
                {if $row.email.type_id == 5}Spam{/if}
                {if $page_alias == "email_main_deletedbyuser"}Trash{/if}
            </td>
            <td>

                <div class="row search-biz">
                    <div class="col-md-12">
                        <div class="input-group input-group-sm">
                            <input type="text" class="form-control input-biz-title biz-autocomplete" id="{$row.email.id}email_biz" placeholder="Start type to select">
                            <input type="hidden" id="{$row.email.id}email_biz-id" name="form[biz_id]" value="0">
                            <span class="input-group-btn">
                                <button class="btn btn-sm btn-success add-biz-object" type="button" data-selector_biz_id="#{$row.email.id}email_biz-id" data-email_id="{$row.email.id}">+</button>
                            </span>
                        </div><!-- /input-group -->
                    </div>
                    <br>
                </div>

                <div class="row added-bizes">
                    <div class="col-md-12">
                        {foreach $row.email.objects as $item}
                            {if $item.object_alias=='biz'}
                                <span id="{$item.object_alias|escape:'html'}-{$item.object_id}" style="margin-right: 10px;">
                                    <input type="hidden" name="objects[{$item.object_alias|escape:'html'}-{$item.object_id}]" class="{$item.object_alias|escape:'html'}-object" value="{$item.object_id}">
                                    <a class="tag-{if in_array($item.object_alias, array('biz', 'company', 'order', 'person'))}{$item.object_alias}{else}document{/if}" data-biz-id="{$item.object_id}" style="vertical-align: top; margin-right: 3px;" href="/{$item.object_alias|escape:'html'}/{$item.object_id}/emailmanager/filter/type:0;" target="_blank">{$item.biz.title}</a><img src="/img/icons/cross-small.png" style="cursor: pointer;" class="remove-object-alias" data-object-alias="{$item.object_alias|escape:'html'}" data-object-id="{$item.object_id}" data-email-id="{$row.email.id}">
                                </span><br/>
                            {/if}
                        {/foreach}   
                    </div>
                </div>
            </td>                                
            <td>{$row.email.created_at}</td>
            <td>
                {if $row.email.type_id == 1}<a class="btn btn-sm btn-primary" onclick="window.open('/emailmanager/{$row.email.id}', 'email_html_{$row.email.id}', 'fullscreen=yes,scrollbars=yes,resizable=yes');"><i class="glyphicon glyphicon-eye-open"></i></a>{/if}
                    {*<a class="btn btn-xs btn-primary" onclick="window.open('/emailmanager/{$row.email.id}', 'email_{$row.email.id}', 'fullscreen=yes,scrollbars=yes,resizable=yes');"">Read</a>*}
                    {if $row.email.type_id == 2 || $row.email.type_id == 3}
                    <a class="btn btn-sm btn-primary" onclick="window.open('/emailmanager/{$row.email.id}/edit', 'email_{$row.email.id}', 'fullscreen=yes,scrollbars=yes,resizable=yes');"><i class="glyphicon glyphicon-pencil"></i></a>

                {/if}
                <a class="btn btn-sm btn-danger delete-emails" name="delete_by_user" data-id="{$row.email.id}"><i class="glyphicon glyphicon-trash"></i></a>
                    {if isset($row.email.attachments)}
                    <!-- Button trigger modal -->
                    <button class="btn btn-default btn-sm" data-toggle="modal" data-target="#attachment-{$row.email.id}">
                        <i class="glyphicon glyphicon-paperclip"></i>
                    </button>

                    <!-- Modal -->
                    <div class="modal fade" id="attachment-{$row.email.id}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                                    <h4 class="modal-title" id="myModalLabel">Attachments</h4>
                                </div>
                                <div class="modal-body">
                                    <ul class="list-group">
                                        {foreach name=i from=$row.email.attachments item=atach}
                                            {if !empty($atach.attachment)}
                                                {if strstr($atach.attachment.content_type, 'image')}
                                                    {*<a class="attachment-{$atach.attachment.ext|lower}" href="/file/{$row.attachment.secret_name}/{$row.attachment.original_name}" target="_blank">{$row.attachment.original_name} ({$row.attachment.size|human_filesize})</a>*}
                                                    <li class="list-group-item"><a class="attachment-{$atach.attachment.ext|lower}" href="/file/{$atach.attachment.secret_name}/{$atach.attachment.original_name}" rel="pp_attachments[]">{$atach.attachment.original_name} ({$atach.attachment.size|human_filesize})</a></li>
                                                    {else}
                                                    <li class="list-group-item"><a class="attachment-{$atach.attachment.ext|lower}" href="/file/{$atach.attachment.secret_name}/{$atach.attachment.original_name}" target="_blank">{$atach.attachment.original_name} ({$atach.attachment.size|human_filesize})</a></li>
                                                    {/if}
                                                {/if}
                                            {/foreach}
                                    </ul>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                    <!--<button type="button" class="btn btn-primary">Сохранить изменения</button>-->
                                </div>
                            </div>
                        </div>
                    </div>
                {/if}



            </td>