<a href="/{$app_object_alias}/{$app_object_id}/blog" class="glyphicon glyphicon-comment"style="text-decoration: none; margin-left: 10px;">Blog</a>
<a href="javascript:void(0)"  onclick="show_chat_modal('{$app_object_alias}', {$app_object_id});" class="glyphicon glyphicon-pencil" style="text-decoration: none; margin-left: 10px;" title="">Message</a>
{*<a href="/{$app_object_alias}/{$app_object_id}/emailmanager/compose" class="glyphicon glyphicon-envelope" style="text-decoration: none; margin-left: 10px;" title="">Send email</a>*}
<a data-alias="{$app_object_alias}" data-id="{$app_object_id}" class="glyphicon glyphicon-envelope email-compose-link" style="margin-left: 10px; cursor: pointer;">Compose email</a>
<a href="javascript:void(0)" class="glyphicon glyphicon-paperclip" onclick="uploader_show_modal('{$app_object_alias}', {$app_object_id});" style="text-decoration: none; margin-left: 10px;" title="">Shared files</a>
<a href="/{$smarty.request.arg}~print" class="glyphicon glyphicon-print" style="text-decoration: none; margin-left: 10px;" target="_blank" title="Print">Print</a>
