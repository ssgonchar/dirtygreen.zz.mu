<a href="/{$object_alias}/{$object_id}" class="details-gray">{$doc_no}</a>
<a href="javascript:void(0)" class="newmessage-gray" onclick="show_chat_modal('{$object_alias}', {$object_id});" style="margin-left: 10px;">Write message</a>
<a href="/{$object_alias}/{$object_id}/email/compose" class="newemail-gray" style="margin-left: 10px;">Compose email</a>
<a href="javascript:void(0)" class="sharefiles-gray" onclick="uploader_show_modal('{$object_alias}', {$object_id})" style="margin-left: 10px;">Share files</a>
<a href="/{$smarty.request.arg}~print" class="print-gray" style="margin-left: 10px;" target="_blank">Print version</a>

