{strip}
<div class="text-left">
    <input type="button" name="btn_compose" class="btn200o" value="Compose New eMail" style="margin-top: 4px;" onclick="location.href='{if !empty($object_alias) && !empty($object_alias)}/{$object_alias}/{$object_id}{/if}/email/compose';">
</div>
<div class="pad2"></div>
<!--
<div>
    <div style="height: 20px;">  
            <a class="i-email-all-normal" href="/emails/filter/type:0;">All emails</a>
    </div>
    <div style="height: 20px;"> 
        {if !empty($keyword) && isset($type_id) && $type_id == 0 && $page != 'deleted_by_user'}
            <b class="i-email-all-normal">All Folders</b>
        {else if !empty($keyword)}    
            <a class="i-email-all-normal" href="/emails/filter/{if isset($mailbox_id) && $mailbox_id > 0}mailbox:{$mailbox_id};{/if}type:0{if !empty($keyword_md5)};keyword:{$keyword_md5}{/if}">All Folders</a>
        {/if}
    </div>
    <div style="height: 20px;">
        {if $page == 'all_emails' || isset($type_id) && $type_id == $smarty.const.EMAIL_TYPE_INBOX}
            <b class="i-email-inbox">Inbox</b>
        {else}    
            <a class="i-email-inbox" href="/emails/filter/{if isset($mailbox_id) && $mailbox_id > 0}mailbox:{$mailbox_id};{/if}type:{$smarty.const.EMAIL_TYPE_INBOX}{if !empty($keyword_md5)};keyword:{$keyword_md5}{/if}">Inbox</a>
        {/if}
    </div>
     <div style="height: 20px;">
         {if $type_id == $smarty.const.EMAIL_TYPE_OUTBOX}
             <b class="i-email-outbox">Sent</b>
         {else}    
            <a class="i-email-outbox" href="/emails/filter/{if isset($mailbox_id) && $mailbox_id > 0}mailbox:{$mailbox_id};{/if}type:{$smarty.const.EMAIL_TYPE_OUTBOX}{if !empty($keyword_md5)};keyword:{$keyword_md5}{/if}">Sent</a>
        {/if}
    </div> 
    <div style="height: 20px;">
        {if $is_dfa}
            <b class="i-email-dfa">DFA - my approval required</b>{if isset($dfa_count) && $dfa_count > 0}<b>({$dfa_count})</b>{/if}
        {else}    
            <a class="i-email-dfa" href="/emails/dfa{if !empty($keyword_md5)}/filter/keyword:{$keyword_md5}{/if}">DFA - my approval required</a>{if isset($dfa_count) && $dfa_count > 0}<b>({$dfa_count})</b>{/if}
        {/if}
    </div> 
    <div style="height: 20px;">
        {if $is_dfa_other}
            <b class="i-email-dfa other">DFA - other</b>{if isset($dfa_count_other) && $dfa_count_other > 0}<b>({$dfa_count_other})</b>{/if}
        {else}    
            <a class="i-email-dfa other" href="/emails/dfa/other{if !empty($keyword_md5)}/filter/keyword:{$keyword_md5}{/if}">DFA - other</a>{if isset($dfa_count_other) && $dfa_count_other > 0}<b>({$dfa_count_other})</b>{/if}
        {/if}
    </div> 
    <div style="height: 20px;">
        {if $type_id == $smarty.const.EMAIL_TYPE_SPAM}
            <b class="i-email-spam">Spam</b>
        {else}    
            <a class="i-email-spam" href="/emails/filter/{if isset($mailbox_id) && $mailbox_id > 0}mailbox:{$mailbox_id};{/if}type:{$smarty.const.EMAIL_TYPE_SPAM}{if !empty($keyword_md5)};keyword:{$keyword_md5}{/if}">Spam</a>
        {/if}
    </div>
    <div style="height: 20px;">
        {if $type_id == $smarty.const.EMAIL_TYPE_ERROR}
            <b class="i-email-error">Corrupted</b>
        {else}
            <a class="i-email-error" href="/emails/filter/type:4{if !empty($keyword_md5)};keyword:{$keyword_md5}{/if}">Corrupted</a>
        {/if}     
    </div>
    <div style="height: 20px;"> 
        {if $page == 'deleted_by_user'}
            <b class="i-email-deleted">Junk</b>{if isset($deleted_by_user_count) && $deleted_by_user_count > 0}<b>({$deleted_by_user_count})</b>{/if}
        {else}
            <a class="i-email-deleted" href="/emails/deleted{if !empty($keyword_md5)}/filter/keyword:{$keyword_md5}{/if}">Junk</a>{if isset($deleted_by_user_count) && $deleted_by_user_count > 0}<b>({$deleted_by_user_count})</b>{/if}
        {/if}
    </div> 
</div>
-->
<!--
{if empty($object_alias) || empty($object_id)}
    <div class="pad2"></div>
    <!--
    <table class="form" width="75%">
        <tr>
            <td class="text-middle">
                {if $page == 'all_emails' || $type_id == $smarty.const.EMAIL_TYPE_ERROR || $page == 'deleted_by_user' || $is_dfa || empty($mailbox_id)}
                    <b class="i-email-all-normal">All emails</b>
                {else}
                    <a class="i-email-all-normal" href="/emails{if isset($type_id) && $type_id > 0}/filter/type:{$type_id}{/if}{if !empty($keyword_md5)};keyword:{$keyword_md5}{/if}">All Mailboxes</a>
                {/if}
            </td>
        </tr>
    </table>
    -->
    <!--
    <div>
    {foreach from=$mailboxes item=row}
        <div class="mbox-toggler-{if isset($row.mailbox.stat) && $row.mailbox.stat.emails_unread > 0} mailbox-unread{/if}" style="height: 14px; cursor: pointer; margin-top: 10px; color: black;" rel="mbox-{$row.mailbox_id}">
            {if $mailbox_id == $row.mailbox_id}
                <b>{$row.mailbox.title|escape:'html'}</b>
            {else}
                <a href="/emails/filter/mailbox:{$row.mailbox_id}{if $type_id == $smarty.const.EMAIL_TYPE_ERROR || $page == 'deleted_by_user' || $is_dfa};type:{$smarty.const.EMAIL_TYPE_INBOX}{/if}
                    {if isset($type_id) && $type_id > 0 && in_array($type_id, array($smarty.const.EMAIL_TYPE_INBOX, $smarty.const.EMAIL_TYPE_OUTBOX, $smarty.const.EMAIL_TYPE_SPAM))};type:{$type_id}{/if}{if !empty($keyword_md5)};keyword:{$keyword_md5}{/if}">
                    {$row.mailbox.title|escape:'html'}
                </a>
            {/if}
            {if isset($row.mailbox.stat) && $row.mailbox.stat.emails_unread > 0}&nbsp;({$row.mailbox.stat.emails_unread}){/if}
        </div>
    {/foreach}
    </div>
    <div class="pad2"></div>
  -->
    <div style="line-height: 20px;">
        <a class="i-funnel" href="/email/filters">Custom Filters</a>
    </div>
    <div class="pad2"></div>

    {if !empty($bizes_list)}
    {$max_displayed_items=20}
    <span style="color: #777; font-size: 10px;">BIZs for the last 30 days :</span>
    <div class="email-menu bizes-container">
        <div class="bc-list {if $bizes_list.count > $max_displayed_items}collapsed{else}expanded{/if}">
            {foreach $bizes_list.data as $row}
            <div class="bc-list-row"><a href="/biz/{$row.biz.id}/emails" title="{$row.biz.doc_no_full|escape:'html'}">{$row.biz.doc_no_full|escape:'html'}</a></div>
            {/foreach}
        </div>
        {if $bizes_list.count > $max_displayed_items}
        <div class="pad"></div>
        <div class="bc-list-toggle-visibility">
            <div class="expand on">Show Another {$bizes_list.count-$max_displayed_items}</div>
            <div class="collapse">Collapse</div>
        </div>
        {/if}
    </div>

    <div class="pad" style="height: 70px;"></div>
    {/if}
{else}

    <div class="pad2"></div>
    <table class="form" width="75%">
        <tr>
            <td class="text-middle">
                {if $page == 'all_emails' || $type_id == $smarty.const.EMAIL_TYPE_ERROR || $page == 'deleted_by_user' || $is_dfa || empty($mailbox_id)}
                    <b class="i-email-all-normal">All emails</b>
                {else}
                    <a class="i-email-all-normal" href="/{$object_alias}/{$object_id}/emails{if isset($type_id) && $type_id > 0}/filter/type:{$type_id}{/if}{if !empty($keyword_md5)};keyword:{$keyword_md5}{/if}">All Mailboxes</a>
                {/if}
            </td>
        </tr>
    </table>
    <div class="pad2"></div>
    {if !empty($doctypes_list)}
    <div><b style="font-size: 15px; color: #599B00;">By Types:</b></div>
    <div class="mbox-spoiler" style="line-height: 20px; margin-left: 10px; display: block;">
        {foreach $doctypes_list as $row}
        {if $doc_type_id == $row.id}<b>{$row.name|escape:'html'}</b>{else}<a href="/{$object_alias}/{$object_id}/emails/filter/doctype:{$row.id}">{$row.name|escape:'html'}</a>{/if}<br>
        {/foreach}
    </div>
    {/if}
{/if}
{/strip}
