<div>
    <h2 style="margin-top: 0; padding-top: 0;">Shared Pictures</h2>
    <div class="pad"></div>
    <div>
        <div class="qq-fileuploader-filelist-empty"{if !empty($pictures)} style="display: none"{/if}>There are no pictures</div>
        <div class="qq-fileuploader-filelist">
            {foreach $pictures as $row}
            {include file='templates/controls/attachment_picture.tpl' attachment=$row.attachment}
            {/foreach}
        </div>
    </div>
    <div style="text-align: right; vertical-align: top; position: absolute; bottom: 0; right: 0;">
        <input type="button" name="btn_save" class="btn100b save" value="Share" style="margin-right: 10px; cursor: pointer; display: none;" onclick="uploader_save_form();">
        <div class="qq-fileuploader" data-oalias="{$object_alias}" data-oid="{$object_id}" style="display: inline-block; height: 34px; vertical-align: top;"></div>
    </div>
</div>
    
<div class="qq-upload-drop-area"><span style="color: grey; font-size: 16px;">Drop files here</span></div>