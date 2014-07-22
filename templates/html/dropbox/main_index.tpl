{*
<table width="100%">
    <tr>
        <td class="text-top" width="49%">
            <table width="100%">
            {foreach from=$list1 item=row}
                <tr>
                    <td><a class="attachment-{$row.attachment.ext|lower}" href="/file/{$row.attachment.secret_name|escape:'html'}/{$row.attachment.original_name|escape:'html'}" target="_blank">{$row.attachment.original_name|escape:'html'}</a></td>
                    <td>{$row.attachment.size|human_filesize}</td>
                    <td width="20px">!</td>
                    <td width="20px">+</td>
                    <td width="20px">�</td>
                </tr>
            {/foreach}
            </table>
        </td>
        <td width="2%">&nbsp;</td>
        <td class="text-top" width="49%">
        
        </td>
    </tr>
</table>
*}
<div id="photolist">
{foreach from=$list1 item=row}
    {include file='templates/html/dropbox/control_attachment_block.tpl' attachment=$row.attachment}
{/foreach}
</div>
<div id="no-photolist"{if !empty($list1)} style="display: none;"{/if}>No files or pictures</div>

<div class="separator pad"></div>
<ul class="none" id="qq-upload-list"></ul>