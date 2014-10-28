{if $row.type_id == 1 || $row.type_id == 5}<a class="btn btn-xs btn-primary" onclick="window.open('/emailmanager/{$row.id}', 'email_html_{$row.id}', 'fullscreen=yes,scrollbars=yes,resizable=yes');"><i class="glyphicon glyphicon-eye-open" style="color:#fff;"></i></a>{/if}
    {*<a class="btn btn-xs btn-primary" onclick="window.open('/emailmanager/{$row.email.id}', 'email_{$row.email.id}', 'fullscreen=yes,scrollbars=yes,resizable=yes');"">Read</a>*}
    {if $row.type_id == 2 || $row.type_id == 3}
    <a class="btn btn-xs btn-primary" onclick="window.open('/emailmanager/{$row.id}/edit', 'email_{$row.id}', 'fullscreen=yes,scrollbars=yes,resizable=yes');"><i class="glyphicon glyphicon-pencil" style="color:#fff;"></i></a>

{/if}
<a class="btn btn-xs btn-danger delete-emails" name="delete_by_user" data-id="{$row.id}"><i class="glyphicon glyphicon-trash" style="color:#fff;"></i></a>
    {if isset($row.attachments)}
    <!-- Button trigger modal -->
    <button class="btn btn-default btn-xs" data-toggle="modal" data-target="#attachment-{$row.id}">
        <i class="glyphicon glyphicon-paperclip"></i>
    </button>

    <!-- Modal -->
    <div class="modal fade" id="attachment-{$row.id}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title" id="myModalLabel">Attachments</h4>
                </div>
                <div class="modal-body">
                    <ul class="list-group">
                        {foreach name=i from=$row.attachments item=atach}
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
