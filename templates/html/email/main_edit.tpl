<div style="background: #f5f5f5; padding: 5px 0;">
<table class="form" width="100%">
    <tr>
        <td></td>
        <td>
            <h3 style="margin: 0; padding: 0;">eMail Data</h3>
        </td>
    </tr>
    <tr>
        <td class="form-td-title-b">From : </td>
        <td>
            <select id="mailbox_id" name="form[sender_address]" class="wide" onchange="email_select_signature(this.value);">
                <option value="">--</option>
                {foreach from=$mailboxes item=row}
                <option value="{$row.mailbox.address}"{if isset($form) && isset($form.sender_address) && $form.sender_address == $row.mailbox.address} selected="selected"{/if}>{$row.mailbox.address}</option>
                {/foreach}
            </select>
        </td>
    </tr>    
    <tr>
        <td class="form-td-title-b">To : </td>
        <td>
            <input type="text" id="recipients" name="form[recipient_address]" class="email-recipient max"{if isset($form) && isset($form.recipient_address)} value='{$form.recipient_address|replace:'\'':'"'|replace:'<':'&lt;'|replace:'>':'&gt;'}'{/if}>
        </td>
    </tr>    
    <tr id="email-add-cc-link"{if isset($form) && !empty($form.cc_address)} style="display: none;"{/if}>
        <td></td>
        <td><a class="add" href="javascript: void(0);" onclick="email_show_cc();">Add CC</a></td>
    </tr>    
    <tr  id="email-add-cc-input"{if !isset($form) || empty($form.cc_address)} style="display: none;"{/if}>
        <td class="form-td-title-b">Cc : </td>
        <td><input type="text" name="form[cc_address]" class="email-recipient max"{if isset($form) && isset($form.cc_address)} value='{$form.cc_address|replace:'\'':'"'|replace:'<':'&lt;'|replace:'>':'&gt;'}'{/if}></td>
    </tr>
	<tr id="email-add-bcc-link"{if isset($form) && !empty($form.bcc_address)} style="display: none;"{/if}>
        <td></td>
        <td><a class="add" href="javascript: void(0);" onclick="email_show_bcc();">Add Bcc</a></td>
    </tr>
    <tr  id="email-add-bcc-input"{if !isset($form) || empty($form.bcc_address)} style="display: none;"{/if}>
        <td class="form-td-title-b">Bcc : </td>
        <td><input type="text" name="form[bcc_address]" class="email-recipient max"{if isset($form) && isset($form.bcc_address)} value='{$form.bcc_address|replace:'\'':'"'|replace:'<':'&lt;'|replace:'>':'&gt;'}'{/if}></td>
    </tr>
    <tr>
        <td class="form-td-title-b">Subject : </td>
        <td><input type="text" name="form[title]" class="max"{if isset($form) && isset($form.title)} value='{$form.title|replace:'\'':'"'|replace:'<':'&lt;'|replace:'>':'&gt;'}'{/if}></td>
    </tr>    
    <tr>
        <td class="form-td-title-b">Attachments : </td>
        <td>
            <div id="attachments">{if isset($attachments) && !empty($attachments)}
            {foreach name=i from=$attachments item=row}
                {if !empty($row.attachment)}
                {include file="templates/html/dropbox/control_attachment_block_text.tpl" attachment=$row.attachment object_alias=$uploader_object_alias object_id=$uploader_object_id}
                {/if}
            {/foreach}
            {/if}</div>
            <div class="separator"></div>
            <div><ul id="qq-upload-list"></ul></div>
            <div style="display: none;"><div class="qq-upload-drop-area"></div></div>
            <div id="fileuploader"></div>
            <input type="hidden" id="uploader_object_alias" name="uploader_object_alias" value="{$uploader_object_alias}">
            <input type="hidden" id="uploader_object_id" name="uploader_object_id" value="{$uploader_object_id}">
        </td>
    </tr>
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
</div>

<div class="pad1"></div>
<hr style="width: 100%; color: #dedede;" size="1"/>
<div class="pad1"></div>

<table class="form" width="100%">
    <tr>
        <td></td>
        <td>
            <h3 style="margin: 0; padding: 0;">eMail Body Text Header</h3>
        </td>
    </tr>
    <tr>
        <td class="form-td-title-i" style="font-weight: bold;">To : </td>
        <td><input type="text" id="email-company" name="form[to]" class="wide"{if isset($form) && isset($form.to)} value='{$form.to|replace:'\'':'"'|replace:'<':'&lt;'|replace:'>':'&gt;'}'{/if}></td>
    </tr>
    <tr>
        <td class="form-td-title-i" style="font-weight: bold;">Attention : </td>
        <td><input type="text" id="email-person" name="form[attention]" class="wide"{if isset($form) && isset($form.attention)} value='{$form.attention|replace:'\'':'"'|replace:'<':'&lt;'|replace:'>':'&gt;'}'{/if}></td>
    </tr>
    <tr>
        <td class="form-td-title-i" style="font-weight: bold;">Subject : </td>
        <td><input type="text" name="form[subject]" class="wide"{if isset($form) && isset($form.subject)} value='{$form.subject|replace:'\'':'"'|replace:'<':'&lt;'|replace:'>':'&gt;'}'{/if}></td>
    </tr>    
    <tr>
        <td class="form-td-title-i" style="font-weight: bold;">Our Ref. : </td>
        <td><input type="text" name="form[our_ref]" class="wide"{if isset($form) && isset($form.our_ref)} value="{$form.our_ref|escape:'html'}"{/if}></td>
    </tr>    
    <tr>
        <td class="form-td-title-i" style="font-weight: bold;">Your Ref. : </td>
        <td><input type="text" name="form[your_ref]" class="wide"{if isset($form) && isset($form.your_ref)} value="{$form.your_ref|escape:'html'}"{/if}></td>
    </tr>
    <tr>
        <td class="form-td-title-b text-top">{if isset($form) && isset($form.parent_id) && !empty($form.parent_id)}Answer : {else}Text : {/if}</td>
        <td><textarea id="email_text" name="form[description]" style="width: 100%">{if isset($form) && isset($form.description)}{$form.description}{/if}</textarea></td>
    </tr>
    <tr>
        <td class="form-td-title-b text-top">Signature : </td>
        <td>
            <input type="text" name="form[signature]" class="wide"{if isset($form) && isset($form.signature)} value="{$form.signature|escape:'html'}"{/if}><br>
        </td>
    </tr>
    <tr>
        <td></td>
        <td>
            <span id="email-signature-pa1" style="line-height: 16px;{if !isset($sender_domain) || $sender_domain != 'platesahead.com'} display: none;{/if}"><b>PlatesAhead Inc.</b>&nbsp;/&nbsp;<input type="text" name="form[signature3]" class="narrow" style="font-style: italic;" value="{if isset($form) && isset($form.signature3)}{$form.signature3|escape:'html'}{else}{$smarty.session.user.login}{/if}"><br></span>
            <span id="email-signature-se1" style="line-height: 16px;{if !isset($sender_domain) || $sender_domain != 'steelemotion.com'} display: none;{/if}"><b>STEELemotion</b>&nbsp;/&nbsp;<input type="text" name="form[signature3]" class="narrow" style="font-style: italic;" value="{if isset($form) && isset($form.signature3)}{$form.signature3|escape:'html'}{else}{$smarty.session.user.login}{/if}"><br></span>
            <textarea id="email-signature2" name="form[signature2]" class="wide" rows="3" style="margin: 5px 0;{if !isset($sender_domain)} display: none;{/if}">{if isset($form) && isset($form.signature2)}{$form.signature2|escape:'html'}{/if}</textarea><br>
            <span id="email-signature-pa2" style="line-height: 16px;{if !isset($sender_domain) || $sender_domain != 'platesahead.com'} display: none;{/if}"><a href="http://www.platesahead.com/">http://www.PlatesAhead.com/</a></span>
            <!--<span id="email-signature-se2" style="line-height: 16px;{if !isset($sender_domain) || $sender_domain != 'steelemotion.com'} display: none;{/if}"><a href="http://www.steelemotion.com/">http://www.STEELemotion.com/</a></span>-->
            <span id="email-signature-se2" style="line-height: 16px;{if !isset($sender_domain) || $sender_domain != 'steelemotion.com'} display: none;{/if}"></span>
        </td>
    </tr>
    {if isset($form) && isset($form.parent_id) && !empty($form.parent_id) && isset($form.parent)}
    <tr>
        <td class="form-td-title-b text-top">Text : </td>
        <td>
            <div style="width: 100%; height: 200px; overflow: auto;">
                {$form.parent.date_mail|date_format:"d/m/Y"}
                <br>From : {$form.parent.sender_address|replace:'\'':'"'|replace:'<':'&lt;'|replace:'>':'&gt;'}
                <br>To : {$form.parent.recipient_address|replace:'\'':'"'|replace:'<':'&lt;'|replace:'>':'&gt;'}
                <br>Subject : {$form.parent.title|replace:'\'':'"'|replace:'<':'&lt;'|replace:'>':'&gt;'}
                <br><blockquote class="email-answer">
                {$form.parent.description|nl2br}
                </blockquote>
        </td>
    </tr>    
    {/if}
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
<input type="hidden" name="form[parent_id]" value="{if isset($form) && isset($form.parent_id)}{$form.parent_id}{/if}">

<div class="pad1"></div>
<hr style="width: 100%; color: #dedede;" size="1"/>
<div class="pad1"></div>

<table width="100%">
    <tr>
        <td class="text-top" width="100%">
            <table class="form" width="100%">
                <tr>
                    <td class="form-td-title-b">Approve By : </td>
                    <td class="text-top" width='15%'>
                        <select name="form[approve_by]" class="narrow">
                            <option value="0">--</option>
                            {foreach from=$mam_list item=row}
                            <option value="{$row.user.id}"{if isset($form.approve_by) && $form.approve_by == $row.user.id} selected="selected"{/if}>{$row.user.login|escape:'html'}{if !empty($row.user.nickname)} ({$row.user.nickname}){/if}</option>
                            {/foreach}
                        </select>
                    </td>
                    <td class="form-td-title-b">Approve Deadline : </td>
                    <td class="text-top" style="width: 190px;"><input type="text" name="form[approve_deadline]" class="datepicker normal" value="{if !empty($form.approve_deadline) && $form.approve_deadline > 0}{$form.approve_deadline|escape:'html'|date_format:'d/m/Y'}{/if}"></td>
                    <td class="form-td-title-b">Seek Response : </td>
                    <td class="text-top" style="width: 190px;"><input type="text" name="form[seek_response]" class="datepicker normal" value="{if !empty($form.seek_response) && $form.seek_response > 0}{$form.seek_response|escape:'html'|date_format:'d/m/Y'}{/if}"></td>
                </tr>  
            </table>
        </td>
        <!--    Тип сообщения - перенесен в верхнюю форму
        <td class="text-top">
            <table class="form" width="100%">
                <tr>
                    <td class="form-td-title-b">eMail Type : </td>
                    <td class="text-top">
                        <select name="form[doc_type]" class="narrow">
                            <option value="0">--</option>
                            {*{foreach from=$doctypes_list item=row}
                            <option value="{$row.id}"{if $form.doc_type == $row.id} selected="selected"{/if}>{$row.name|escape:'html'}</option>
                            {/foreach}*}
                        </select>
                    </td>
                </tr>               
                <tr>
                    
                </tr>
            </table>        
        </td>--> 
        <!--    Список драйверов - те у которых есть is_driver в БД users
        <td class="text-top" width="33%">
            <table class="form" width="100%">
    
                <tr>
                    <td class="form-td-title-b">Driver : </td>
                    <td>
      
                        <select name="form[driver_id]" class="narrow">
                            <option value="0">--</option>
                            {*{foreach from=$mam_list item=row}
                            <option value="{$row.user.id}"{if !empty($form.driver_id) && $form.driver_id == $row.user.id} selected="selected"{/if}>{$row.user.login|escape:'html'}{if !empty($row.user.nickname)} ({$row.user.nickname}){/if}</option>
                            {/foreach}*}
                        </select>
                    </td>
                </tr>-->
                
    <!--    <tr>
                    <td class="form-td-title-b text-top">Navigators : </td>
                    <td class="text-top">
                        {*{foreach name='navigators' from=$mam_list item=row}
                        <div style="float: left; width: 110px; margin-bottom: 2px;"><label for="navigator-{$row.user.id}"><input id="navigator-{$row.user.id}" type="checkbox" name="navigators[{$row.user.id}][user_id]" value="{$row.user.id}" style="margin-right: 5px;"{if isset($row.selected)} checked="checked"{/if}>{$row.user.login|escape:'html'}{if !empty($row.user.nickname)} ({$row.user.nickname}){/if}</label></div>
                        {/foreach}*}
                    </td>
                </tr>           
            </table>
        </td>--> 
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
