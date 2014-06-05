<div>
    <h2 style="margin-top: 0; padding-top: 0;">Share Files</h2>
    <div class="pad"></div>
    <div style="overflow-x: hidden; overflow-y: auto;">
        <div class="no-uploaded-files modalbox">No files or pictures</div>
        <table class="table-list uploaded-files" style="width: 100%;">
            <tbody class="qq-fileuploader-filelist">
            {*foreach $list as $row}
            {include file='templates/controls/share_files_row.tpl' attachment=$row.attachment can_be_edited=1}
            {/foreach*}
            {include file='templates/controls/share_files_row.tpl' can_be_edited=1}
            </tbody>
        </table>
    </div>
    <div style="text-align: right; vertical-align: top; position: absolute; bottom: 0; right: 0;">
{*
        <div class="tl-alert-container">
            <label class="tla-lable"><input class="tla-toggler" onchange="toggle_tla_form(this);" type="checkbox" name="is_tl_alert" value="1" /> TL alert</label>
            <div class="tla-form">
                <table class="form" style="width: 100%;">
                    <tr>
                        <td class="form-td-title-b" style="width: 75px;">BIZ : </td>
                        <td>
                            <input type="text" class="autocomplete tla-company-text normal ui-autocomplete-input" name="form[biz_title]" value="" autocomplete="off" role="textbox" aria-autocomplete="list" aria-haspopup="true" />
                            <input type="hidden" class="autocomplete tla-company-id" name="form[biz_id]" value="0" />
                        </td>
                    </tr>
                    <tr>
                        <td class="form-td-title-b text-top" style="width: 75px;">Message : </td>
                        <td><textarea name="form[message]" class="max" rows="5" placeholder="Please see shared files"></textarea></td>
                    </tr>
                </table>
            </div>
        </div>
*}
        <input type="button" name="btn_save" class="btn100b save" value="Share" style="margin-right: 10px; cursor: pointer; display: none;" onclick="uploader_save_form();">
        <div class="qq-fileuploader" data-oalias="{$object_alias}" data-oid="{$object_id}" style="display: inline-block; height: 34px; vertical-align: top;"></div>
    </div>
</div>
    
<div class="qq-upload-drop-area"><span style="color: grey; font-size: 16px;">Drop files here</span></div>