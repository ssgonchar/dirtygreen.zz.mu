{strip}
<table class="form" width="75%">
    <tr>
        <td><input type="text" name="form[keyword]" class="max"{if isset($keyword)} value="{$keyword}"{/if}></td>
        <td><input type="submit" name="btn_find" value="Find" class="btn100b"></td>
    </tr>
    {if isset($keyword) && !empty($keyword)}
        <tr>
            <td colspan = "2">
                {if $is_dfa}
                    <a href="/emails/dfa">Clear search string</a>
                {elseif $is_dfa_other}
                    <a href="/emails/dfa/other">Clear search string</a>
                {elseif $page == 'deleted_by_user'}
                     <a href="/emails/deleted">Clear search string</a>
                {else}
                    <a href="/emails{if isset($type_id) || (isset($mailbox_id) && $mailbox_id > 0)}/filter/{/if}{if isset($type_id)}type:{$type_id};{/if}{if isset($mailbox_id) && $mailbox_id > 0}mailbox:{$mailbox_id};{/if}">Clear search string</a>
                {/if}     
            </td>    
        </tr>    
    {/if}    
</table>

<div class="pad1"></div>
<hr style="width: 100%; color: #dedede;" size="1"/>
<div class="pad1"></div>

{if empty($list)}
    Nothing was found on my request
{else}

    <div style="font-weight: bold; color: black;">
        {if $list[0].email.type_id == $smarty.const.EMAIL_TYPE_SPAM || (!empty($page) && $page == 'deleted_by_user')}
            <a class="group-checkbox gc-all" href="javascript:void(0);" onclick="return false;" title="Select all emails" style="margin-right: 10px;">select all</a>
            <a class="group-checkbox gc-unselect" href="javascript:void(0);" onclick="return false;" title="Unselect emails" style="margin-right: 10px;">unselect all</a>
        {else}
            <a class="group-checkbox gc-all" href="javascript:void(0);" onclick="return false;" title="Select all emails" style="margin-right: 10px;">select all</a>
            <a class="group-checkbox gc-unread" href="javascript:void(0);" onclick="return false;" title="Select unreaded emails" style="margin-right: 10px;">select unread</a>
            <a class="group-checkbox gc-read" href="javascript:void(0);" onclick="return false;" title="Select readed emails" style="margin-right: 10px;">select read</a>
            <a class="group-checkbox gc-unselect" href="javascript:void(0);" onclick="return false;" title="Unselect emails" style="margin-right: 10px;">unselect all</a>
        {/if}
    </div>
    <div class="pad1"></div>

    <table class="emails" style="table-layout: fixed; width: 100%;">
{*
        <tr>
            <td width="25px" style="padding-left: 5px;"><input type="checkbox" class="choose-all-checkboxes" style="margin: 5px;" /></td>
            <td width="25px" style="padding-left: 5px;"></td>
            <td width="270px"></td>
            <td></td>
            <td width="25px" class="text-center"></td>
            <td width="60px" class="email-date"></td>
        </tr>
*}
        {foreach from=$list item=row name=emails}
        <tr id="email-{$row.email.id}" class="email-type-{if isset($row.email.userdata) && $row.email.userdata.read_at > 0}read{else}{$row.email.type_id}{/if}{if !empty($row.email.is_biz_tag_exists)} biz-tag-exists{/if} {if $row.email.type_id == $smarty.const.EMAIL_TYPE_DRAFT && $row.email.is_sent > 0} email-type-{$row.email.type_id}-{$row.email.is_sent}{/if}">
            <td style="width: 20px; text-align: center;">
                <input type="checkbox" name="selected_ids[]" class="single-checkbox{if isset($row.email.userdata)} et-read{else} et-unread{/if}{if $row.email.type_id != $smarty.const.EMAIL_TYPE_SPAM} et-notspam{else} et-spam{/if}" value="{$row.email.id}" />
            </td>
            <td style="width: 20px; text-align: center;" onclick="location.href='{if !empty($object_alias) && !empty($object_id)}/{$object_alias}/{$object_id}{/if}/email/{$row.email.id}~tid{$token}';">
                {if $row.email.type_id == $smarty.const.EMAIL_TYPE_INBOX}
                    <img src="/img/icons{if isset($row.email.userdata)}/mail-open.png{else}/mail.png{/if}">
                {elseif $row.email.type_id == $smarty.const.EMAIL_TYPE_OUTBOX}
                    <img src="/img/icons/arrow-180.png">
                {elseif $row.email.type_id == $smarty.const.EMAIL_TYPE_ERROR}
                    <img src="/img/icons/exclamation.png">
                {elseif $row.email.type_id == $smarty.const.EMAIL_TYPE_DRAFT}
                    <img src="/img/icons/document--pencil.png">
                {elseif $row.email.type_id == $smarty.const.EMAIL_TYPE_SPAM}
                    <img src="/img/icons/minus-circle.png">
                {/if}
                
            </td>
            <td style="width: 250px; overflow: hidden; padding: 0 0 0 5px;" onclick="location.href='{if !empty($object_alias) && !empty($object_id)}/{$object_alias}/{$object_id}{/if}/email/{$row.email.id}~tid{$token}';">
                {* if empty($type_id)}
                    {if $row.email.type_id == $smarty.const.EMAIL_TYPE_INBOX}From : {elseif $row.email.type_id == $smarty.const.EMAIL_TYPE_OUTBOX} To : {/if}
                {/if *}                
                {if $row.email.type_id == $smarty.const.EMAIL_TYPE_INBOX || $row.email.type_id == $smarty.const.EMAIL_TYPE_ERROR || $row.email.type_id == $smarty.const.EMAIL_TYPE_SPAM}
                    {$row.email.sender_address|human_email|truncate:40:' ...'}
                {elseif $row.email.type_id == $smarty.const.EMAIL_TYPE_OUTBOX}
                    {if isset($row.person)}{$row.person.full_name}
                    {elseif isset($row.company)}{$row.company.title}
                    {else}{$row.email.recipient_address|escape:'html'|truncate:40:' ...'}{/if}
                {elseif $row.email.type_id == $smarty.const.EMAIL_TYPE_DRAFT}
                    {$row.email.author.full_login}
                {/if}
            </td>
            <td style="overflow: hidden; white-space: nowrap;" onclick="location.href='{if !empty($object_alias) && !empty($object_id)}/{$object_alias}/{$object_id}{/if}/email/{$row.email.id}~tid{$token}';">
                {strip}
                <span>{if empty($row.email.title)}(no subject){else}{$row.email.title}{/if}</span>
                {if !empty($row.email.description)}<span style="color: #777777 !important;"> - {$row.email.description|strip_tags|truncate:200}</span>{/if}
                {/strip}
            </td>
            <td style="width: 20px; text-align: center;" onclick="location.href='{if !empty($object_alias) && !empty($object_id)}/{$object_alias}/{$object_id}{/if}/email/{$row.email.id}~tid{$token}';" class="text-center">
                {if empty($row.email.is_biz_tag_exists) && isset($row.email.attachments)}<img src="/img/icons/tag-minus.png" alt="HasNoBizTags" title="Has no biz tags"/>{/if}
            </td>
            <td style="width: 20px; text-align: center;" onclick="location.href='{if !empty($object_alias) && !empty($object_id)}/{$object_alias}/{$object_id}{/if}/email/{$row.email.id}~tid{$token}';" class="text-center">
                {if isset($row.email.attachments)}<img src="/img/icons/paper-clip.png" />{elseif empty($row.email.is_biz_tag_exists)}<img src="/img/icons/tag-minus.png" alt="HasNoBizTags" title="Has no biz tags"/>{/if}
            </td>
            <td  style="width: 70px;"onclick="location.href='{if !empty($object_alias) && !empty($object_id)}/{$object_alias}/{$object_id}{/if}/email/{$row.email.id}~tid{$token}';" class="email-date">
            {if empty($row.email.is_today)}
                {$row.email.date_mail|date_format:'d/m/Y'}<br>{$row.email.date_mail|date_format:'H:i:s'}
            {else}
                {$row.email.date_mail|date_format:'H:i:s'}
            {/if}            
            </td>
        </tr>
        {/foreach}
    </table>
{/if}
{/strip}
