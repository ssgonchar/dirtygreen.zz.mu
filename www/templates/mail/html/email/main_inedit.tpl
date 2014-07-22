<table class="form" width="100%">
    <tr>
        <td class="form-td-title-b">From : </td>
        <td>{$email.sender_address|escape:'html'}</td>
    </tr>
    <tr>
        <td class="form-td-title-b">To : </td>
        <td>{$email.recipient_address|escape:'html'}</td>
    </tr>
    {if !empty($email.cc_address)}
    <tr>
        <td class="form-td-title-b">Cc : </td>
        <td>{$email.cc_address|escape:'html'}</td>
    </tr>
    {/if}
	{if !empty($email.bcc_address)}
    <tr>
        <td class="form-td-title-b">Bcc : </td>
        <td>{$email.bcc_address|escape:'html'}</td>
    </tr>
    {/if}
    <tr>
        <td class="form-td-title-b">Subject : </td>
        <td>{if empty($email.title)}(no subject){else}{$email.title}{/if}</td>
    </tr>
    {if !empty($email.attachments)}
    <tr>
        <td class="form-td-title-b text-top" style="line-height: 16px;">Attachments : </td>
        <td style="line-height: 16px;">
            {foreach name=i from=$email.attachments item=row}
            <a class="attachment-{$row.attachment.ext|lower}" href="/file/{$row.attachment.secret_name}/{$row.attachment.original_name}" target="_blank">{$row.attachment.original_name} ({$row.attachment.size|human_filesize})</a>
            {/foreach}
        </td>
    </tr>
    {/if}
</table>

<div class="pad1"></div>
<hr style="width: 100%; color: #dedede;" size="1"/>
<div class="pad1"></div>

{if ($email.type_id == $smarty.const.EMAIL_TYPE_DRAFT || $email.type_id == $smarty.const.EMAIL_TYPE_OUTBOX) && !empty($email.to) && !empty($email.attention) && !empty($email.subject)}
    {include file="templates/html/email/control_email_text.tpl" email=$email}
{else}
    <b style="color: #00f;">{$email.date_mail|date_format:'d/m/Y'}</b>
    <br><br><br>
    {$email.description|nl2br}
{/if}

<table class="form" width="100%">
    <tr>
        <td class="form-td-title-b text-top">Tags : </td>
        <td>
            <a class="add" href="javascript: void(0);" onclick="show_email_co_list();">Add Tag</a>
            <div class="email-co-objects-list" style="margin-top: 10px;">
            {if !empty($objects)}
                {foreach $objects as $item}
                <span id="{$item.object_alias|escape:'html'}-{$item.object_id}" style="margin-right: 10px;">
                    <input type="hidden" name="objects[{$item.object_alias|escape:'html'}-{$item.object_id}]" class="{$item.object_alias|escape:'html'}-object" value="{$item.object_id}">
                    <a class="tag-{if in_array($item.object_alias, array('biz', 'company', 'order', 'person'))}{$item.object_alias}{else}document{/if}" style="vertical-align: top; margin-right: 3px;" href="/{$item.object_alias|escape:'html'}/{$item.object_id}" target="_blank">{$item.title|escape:'html'}</a><img src="/img/icons/cross-small.png" onclick="remove_email_object('{$item.object_alias|escape:'html'}', {$item.object_id});">
                </span>
                {/foreach}
            {/if}
            </div>
        </td>
    </tr>
</table>

<div class="pad1"></div>
<hr style="width: 100%; color: #dedede;" size="1"/>
<div class="pad1"></div>

<table width="100%">
    <tr>
        <td class="text-top" width="33%">
            <table class="form" width="100%">
{*
                <tr>
                    <td class="form-td-title-b">Approve By : </td>
                    <td class="text-top">
                        <select name="form[approve_by]" class="narrow">
                            <option value="0">--</option>
                            {foreach from=$mam_list item=row}
                            <option value="{$row.user.id}"{if isset($form.approve_by) && $form.approve_by == $row.user.id} selected="selected"{/if}>{$row.user.login|escape:'html'}{if !empty($row.user.nickname)} ({$row.user.nickname}){/if}</option>
                            {/foreach}
                        </select>
                    </td>
                </tr>
                <tr>
                    <td class="form-td-title-b">Approve Deadline : </td>
                    <td class="text-top"><input type="text" name="form[approve_deadline]" class="datepicker normal" value="{if !empty($form.approve_deadline)}{$form.approve_deadline|escape:'html'|date_format:'d/m/Y'}{/if}"></td>
                </tr>
*}
                <tr>
                    <td class="form-td-title-b">eMail Type : </td>
                    <td class="text-top">
                        <select name="form[doc_type]" class="narrow">
                            <option value="0">--</option>
                            {foreach from=$doctypes_list item=row}
                            <option value="{$row.id}"{if $form.doc_type == $row.id} selected="selected"{/if}>{$row.name|escape:'html'}</option>
                            {/foreach}
                        </select>
                    </td>
                </tr>
            </table>
        </td>
        <td class="text-top">
            <table class="form" width="100%">
{*
                <tr>
                    <td class="form-td-title-b">eMail Type : </td>
                    <td class="text-top">
                        <select name="form[doc_type]" class="narrow">
                            <option value="0">--</option>
                            {foreach from=$doctypes_list item=row}
                            <option value="{$row.id}"{if $form.doc_type == $row.id} selected="selected"{/if}>{$row.name|escape:'html'}</option>
                            {/foreach}
                        </select>
                    </td>
                </tr>
*}
                <tr>
                    <td class="form-td-title-b">Driver : </td>
                    <td>
                        <select name="form[driver_id]" class="narrow">
                            <option value="0">--</option>
                            {foreach from=$mam_list item=row}
                            <option value="{$row.user.id}"{if !empty($form.driver_id) && $form.driver_id == $row.user.id} selected="selected"{/if}>{$row.user.login|escape:'html'}{if !empty($row.user.nickname)} ({$row.user.nickname}){/if}</option>
                            {/foreach}
                        </select>
                    </td>
                </tr>
{*
                <tr>
                    <td class="form-td-title-b">Seek Response : </td>
                    <td class="text-top"><input type="text" name="form[seek_response]" class="datepicker normal" value="{if !empty($form.seek_response)}{$form.seek_response|escape:'html'|date_format:'d/m/Y'}{/if}"></td>
                </tr>
*}
            </table>
        </td>
        <td class="text-top" width="33%">
            <table class="form" width="100%">
{*
                <tr>
                    <td class="form-td-title-b">Driver : </td>
                    <td>
                        <select name="form[driver_id]" class="narrow">
                            <option value="0">--</option>
                            {foreach from=$mam_list item=row}
                            <option value="{$row.user.id}"{if !empty($form.driver_id) && $form.driver_id == $row.user.id} selected="selected"{/if}>{$row.user.login|escape:'html'}{if !empty($row.user.nickname)} ({$row.user.nickname}){/if}</option>
                            {/foreach}
                        </select>
                    </td>
                </tr>
*}
                <tr>
                    <td class="form-td-title-b text-top">Navigators : </td>
                    <td class="text-top">
                        {foreach name='navigators' from=$mam_list item=row}
                        <div style="float: left; width: 110px; margin-bottom: 2px;"><label for="navigator-{$row.user.id}"><input id="navigator-{$row.user.id}" type="checkbox" name="navigators[{$row.user.id}][user_id]" value="{$row.user.id}" style="margin-right: 5px;"{if isset($row.selected)} checked="checked"{/if}>{$row.user.login|escape:'html'}{if !empty($row.user.nickname)} ({$row.user.nickname}){/if}</label></div>
                        {/foreach}
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
        
<div id="email-co-select" style="display: none;">
    <div id="overlay"></div>
    <div id="email-co-container">
    <div style="padding: 10px;">
        <table class="form" width="100%">
            <tr>
                <td>Type : </td>
            </tr>
            <tr>
                <td>
                    <select id="email-co-type-alias" class="max" onchange="email_clear_search_results();">
                        <option value="">--</option>
                        <option value="biz">Biz</option>
                        <option value="company">Company</option>
                        <option value="country">Country</option>
                        <option value="order">Order</option>
                        <option value="person">Person</option>
                        <option value="product">Product</option>
                    </select>
                </td>
            </tr>
            
            <tr>
                <td>Search For : </td>
            </tr>
            <tr>
                <td><input id="keyword" type="text" class="max" onkeypress="if(event.keyCode == 13) return false;" onkeyup="{literal}if(event.keyCode == 13) {find_email_objects(this.value); return false;}{/literal}"></td>
                {*<td><input type="text" class="max" onkeypress="if(event.keyCode == 13) return false;" onkeyup="find_email_objects(this.value);"></td>*}
            </tr>
            <tr>
                <td>Search Result : </td>
            </tr>
            <tr>
                <td>
                    <select id="email-co-search-result" multiple="multiple" size="10" class="max" style="height: 200px;"></select>
                </td>
            </tr>
            <tr>
                <td class="text-center">
                    <input type="button" class="btn100o" value="Add" style="margin-right: 20px;" onclick="add_email_object();">
                    <input type="button" class="btn100" value="Close" onclick="close_email_co_list();">
                </td>
            </tr>
        </table>
    </div>
    </div>
</div>