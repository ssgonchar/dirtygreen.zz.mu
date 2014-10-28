<a href="/{$object_alias}/{$object_id}" class="glyphicon glyphicon-th-list">{$doc_no}</a>
<a href="javascript:void(0)" class="glyphicon glyphicon-pencil" onclick="show_chat_modal('{$object_alias}', {$object_id});" style="margin-left: 10px;">Write message</a>
{*<a href="/{$object_alias}/{$object_id}/emailmanager/compose" class="newemail-gray" style="margin-left: 10px;">Compose email</a>*}
<a data-alias="{$app_object_alias}" data-id="{$app_object_id}" class="glyphicon glyphicon-envelope email-compose-link" style="margin-left: 10px; cursor: pointer;">Compose email</a>
<a href="javascript:void(0)" class="glyphicon glyphicon-paperclip" onclick="uploader_show_modal('{$object_alias}', {$object_id})" style="margin-left: 10px;">Share files</a>
<a href="/{$smarty.request.arg}~print" class="glyphicon glyphicon-print" style="margin-left: 10px;" target="_blank">Print version</a>
