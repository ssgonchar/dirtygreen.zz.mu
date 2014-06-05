<table class="form" width="100%">
    <tr>
        <td class="form-td-title-b">From : </td>
        <td>
            <select id="mailbox_id" name="form[sender_address]" class="wide" onchange="email_select_signature(this);">
                <option value="">--</option>
                {foreach from=$mailboxes item=row}
                <option value="{$row.mailbox.address}"{if isset($form) && isset($form.sender_address) && $form.sender_address == $row.mailbox.address} selected="selected"{/if}>{$row.mailbox.address|escape:'html'}</option>
                {/foreach}
            </select>        
        </td>
    </tr>
    <tr>
        <td class="form-td-title-b">To : </td>
        <td>
            <input type="text" id="recipients" name="form[recipient_address]" class="email-recipient max"{if isset($form) && isset($form.recipient_address)} value="{$form.recipient_address|escape:'html'}"{/if}>
        </td>
    </tr>
    <tr id="email-add-cc-link">
        <td></td>
        <td><a class="add" href="javascript: void(0);" onclick="email_show_cc();">Add CC</a></td>
    </tr>
    <tr  id="email-add-cc-input"{if !isset($form) || empty($form.cc_address)} style="display: none;"{/if}>
        <td class="form-td-title-b">Cc : </td>
        <td><input type="text" name="form[cc_address]" class="email-recipient max"{if isset($form) && isset($form.cc_address)} value="{$form.cc_address|escape:'html'}"{/if}></td>
    </tr>
	<tr id="email-add-bcc-link">
        <td></td>
        <td><a class="add" href="javascript: void(0);" onclick="email_show_bcc();">Add Bcc</a></td>
    </tr>
	<tr  id="email-add-bcc-input"{if !isset($form) || empty($form.bcc_address)} style="display: none;"{/if}>
        <td class="form-td-title-b">Bcc : </td>
        <td><input type="text" name="form[bcc_address]" class="email-recipient max"{if isset($form) && isset($form.bcc_address)} value="{$form.bcc_address|escape:'html'}"{/if}></td>
    </tr>  
    <tr>
        <td class="form-td-title-b">Subject : </td>
        <td><input type="text" name="form[title]" class="max"{if isset($form) && isset($form.title)} value="{$form.title|escape:'html'}"{/if}></td>
    </tr>
    <tr>
        <td class="form-td-title-b">Attachments : </td>
        <td>
            <div id="attachments">{if isset($attachments) && !empty($attachments)}
            {foreach name=i from=$attachments item=row}
                {include file="templates/html/dropbox/control_attachment_block_text.tpl" attachment=$row.attachment}
            {/foreach}
            {/if}</div>
            <div class="separator"></div>
            <div><ul id="qq-upload-list"></ul></div>
            <div id="fileuploader"></div>
            <input type="hidden" id="uploader_object_alias" name="uploader_object_alias" value="{$uploader_object_alias}">
            <input type="hidden" id="uploader_object_id" name="uploader_object_id" value="{$uploader_object_id}">
        </td>
    </tr>    
</table>

<div class="pad1"></div>
<hr style="width: 100%; color: #dedede;" size="1"/>
<div class="pad1"></div>

<table class="form" width="100%">
    <tr>
        <td class="form-td-title-i">To : </td>
        <td><input type="text" id="email-company" name="form[to]" class="wide"{if isset($form) && isset($form.to)} value="{$form.to|escape:'html'}"{/if}></td>
    </tr>
    <tr>
        <td class="form-td-title-i">Attention : </td>
        <td><input type="text" id="email-person" name="form[attention]" class="wide"{if isset($form) && isset($form.attention)} value="{$form.attention|escape:'html'}"{/if}></td>
    </tr>
    <tr>
        <td class="form-td-title-i">Subject : </td>
        <td><input type="text" name="form[subject]" class="wide"{if isset($form) && isset($form.subject)} value="{$form.subject|escape:'html'}"{/if}></td>
    </tr>    
    <tr>
        <td class="form-td-title-i">Our Ref. : </td>
        <td><input type="text" name="form[our_ref]" class="wide"{if isset($form) && isset($form.our_ref)} value="{$form.our_ref|escape:'html'}"{/if}></td>
    </tr>    
    <tr>
        <td class="form-td-title-i">Your Ref. : </td>
        <td><input type="text" name="form[your_ref]" class="wide"{if isset($form) && isset($form.your_ref)} value="{$form.your_ref|escape:'html'}"{/if}></td>
    </tr>
    <tr>
        <td class="form-td-title-b text-top">Text : </td>
        <td><textarea id="email_text" name="form[description]" style="width: 100%">{if isset($form) && isset($form.description)}{$form.description}{/if}</textarea><script type="text/javascript">add_mce_editor('email_text', 'enormal', 500);</script></td>
    </tr>
    <tr>
        <td class="form-td-title-b text-top">Signature : </td>
        <td>
            <input type="text" name="form[signature]" class="wide"{if isset($form) && isset($form.signature)} value="{$form.signature|escape:'html'}"{/if}><br>
            <span id="email-signature-pa" style="line-height: 16px;{if !isset($sender_domain) || $sender_domain != 'platesahead.com'} display: none;{/if}">
                PlatesAhead&nbsp;/&nbsp;<input type="text" class="narrow" style="font-style: italic; font-weight: bold;" value="{if isset($form) && isset($form.signature3)}{$form.signature3|escape:'html'}{else}{$smarty.session.user.login}{/if}"><br>
                <a href="http://www.platesahead.com/">http://www.PlatesAhead.com/</a>
            </span>
            <span id="email-signature-se" style="line-height: 16px;{if !isset($sender_domain) || $sender_domain != 'steelemotion.com'} display: none;{/if}">
                STEELemotion&nbsp;/&nbsp;<input type="text" class="narrow" style="font-style: italic; font-weight: bold;" value="{if isset($form) && isset($form.signature3)}{$form.signature3|escape:'html'}{else}{$smarty.session.user.login}{/if}"><br>
                <a href="http://www.steelemotion.com/">http://www.STEELemotion.com/</a>
            </span> 11111
        </td>
    </tr>
    <tr>
        <td class="form-td-title-b text-top">Tags : </td>
        <td>
            <a class="add" href="javascript: void(0);" onclick="show_email_co_list();">Add Tag</a>
            <div class="email-co-objects-list" style="margin-top: 10px;">
            {if !empty($objects_list)}
            {foreach $objects_list as $item}
            <span id="{$item.object_alias|escape:'html'}-{$item.object_id}" style="margin-right: 10px;">
                <input type="hidden" name="{$item.object_alias|escape:'html'}[{$item.object_id}][object_id]" class="{$item.object_alias|escape:'html'}_id" value="{$item.object_id}">
                <a class="tag-{$item.object_alias|escape:'html'}" style="vertical-align: top; margin-right: 3px;" href="/{$item.object_alias|escape:'html'}/{$item.object_id}" target="_blank">{$item.object_title|escape:'html'}</a><img src="/img/icons/cross.png" onclick="remove_email_object('{$item.object_alias|escape:'html'}', {$item.object_id});">
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
                <tr>
                    <td class="form-td-title-b">Approve By : </td>
                    <td class="text-top">
                        <select name="form[approve_by]" class="narrow">
                            <option value="0">--</option>
                            {foreach from=$mam_list item=row}
                            <option value="{$row.user.id}"{if $form.approve_by == $row.user.id} selected="selected"{/if}>{$row.user.login|escape:'html'}{if !empty($row.user.nickname)} ({$row.user.nickname}){/if}</option>
                            {/foreach}
                        </select>
                    </td>
                </tr>                
                <tr>
                    <td class="form-td-title-b">Approve Deadline : </td>
                    <td class="text-top"><input type="text" name="form[approve_deadline]" class="datepicker normal" value="{if !empty($form.approve_deadline)}{$form.approve_deadline|escape:'html'|date_format:'d/m/Y'}{/if}"></td>
                </tr>
            </table>
        </td>
        <td class="text-top">
            <table class="form" width="100%">
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
                <tr>
                    <td class="form-td-title-b">Seek Response : </td>
                    <td class="text-top"><input type="text" name="form[seek_response]" class="datepicker normal" value="{if !empty($form.seek_response)}{$form.seek_response|escape:'html'|date_format:'d/m/Y'}{/if}"></td>
                </tr>
            </table>        
        </td>
        <td class="text-top" width="33%">
            <table class="form" width="100%">
                <tr>
                    <td class="form-td-title-b">Driver : </td>
                    <td><!--
                        <select name="form[driver_id]" class="narrow">
                            <option value="0">--</option>
                            {*{foreach from=$mam_list item=row}
                            <option value="{$row.user.id}"{if $form.driver_id == $row.user.id} selected="selected"{/if}>{$row.user.login|escape:'html'}{if !empty($row.user.nickname)} ({$row.user.nickname}){/if}</option>
                            {/foreach}*}
                        </select>
    Список драйверов - те у которых есть is_driver в БД users-->
                        <select name="form[driver_id]" class="narrow biz-driver">
                            <option value="0">--</option>
                            {foreach from=$mam_list item=row}
				<option value="{$row.user.id}"{if isset($form.driver_id) && $form.driver_id == $row.user.id} selected="selected"{/if}>{$row.user.login}{if !empty($row.user.nickname)} ({$row.user.nickname}){/if}</option>
                            {/foreach}                            
                        </select>  
                    </td>
                </tr>
<!--    Убрал список навигаторов в создании письма
                <tr>
                    <td class="form-td-title-b text-top">Navigatorss : </td>
                    <td class="text-top">
                        {*{foreach name='navigators' from=$mam_list item=row}
                        <div style="float: left; width: 110px; margin-bottom: 2px;"><label for="navigator-{$row.user.id}"><input id="navigator-{$row.user.id}" type="checkbox" name="navigators[{$row.user.id}][user_id]" value="{$row.user.id}" style="margin-right: 5px;"{if isset($row.selected)} checked="checked"{/if}>{$row.user.login|escape:'html'}{if !empty($row.user.nickname)} ({$row.user.nickname}){/if}</label></div>
                        {/foreach}*}
                    </td>
                </tr>            -->
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
                    <select id="email-co-type-alias" class="max">
                        <option value="">--</option>
                        <option value="biz">Biz</option>
                        <option value="company">Company</option>
                        <option value="person">Person</option>
                    </select>
                </td>
            </tr>
            
            <tr>
                <td>Search For : </td>
            </tr>
            <tr>
                <td><input type="text" class="max" onkeypress="if(event.keyCode == 13) return false;" onkeyup="find_email_objects(this.value);"></td>
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