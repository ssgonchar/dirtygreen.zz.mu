
    {if $email.type_id == $smarty.const.EMAIL_TYPE_SPAM}
        <span class="i-email-spam" style="font-weight: bold; color: #CA4242;">This Is SPAM</span>
    {elseif $email.type_id == $smarty.const.EMAIL_TYPE_ERROR}
        <span class="i-email-error" style="font-weight: bold; color: #948B07;">This Email Is Corrupted</span>
    {/if}
<div class="row">
    <div class="col-md-8">
        <div class="col-md-4">
            <font face="Courier">
                <p style="font-family: Courier; font-size: 12px; margin: 0;">
                    <b style="color: #00f;"><font color="#0000ff">{if $email.sender_domain == 'platesahead.com'}
                     {if $email.type_id == $smarty.const.EMAIL_TYPE_DRAFT}{$smarty.now|date_format:"F jS 'y"}{else}{$email.date_mail|date_format:"F jS 'y"}{/if}
                     {else}
                    {if $email.type_id == $smarty.const.EMAIL_TYPE_DRAFT}{$smarty.now|date_format:"d/m/Y"}{else}{$email.date_mail|date_format:"d/m/Y"}{/if}
                    {/if}
            </font>
                    </b>
                <br><br><br>
                <i>{'To'}</i>: {$email.recipient_address|replace:'"':''}<br>
                {if !empty($email.cc_address)}
                    <i>{'Cс'}</i>: {$email.cc_address|escape:'html'}<br>   
                {/if}
                
                {if !empty($email.bcc_address)}
                Bcc : {$email.bcc_address|escape:'html'}
                    <i>{'Bcc'}</i>: {$email.bcc_address|escape:'html'}<br> 
                {/if}
                
                <i>{'Attention'}</i>: <b style="color:#f00;"><font color="#ff0000">{$email.attention}</font></b><br><br>

                <i>{'Subject'}</i>: {$email.title}<br>
                <i>{'Our Ref'}</i>: {$email.our_ref}<br>
                <i>{'Your Ref'}</i>: {$email.your_ref}<br>
                {if isset($email.attached)}<i>{'Attached'}</i>: {$email.attached}<br>{/if}
                <br>
                
                <br>
                </p>
            </font>
        </div>
        <div class="col-md-4">
            <br><br><br>
             <p style="font-family: Courier; font-size: 12px; margin: 0;">
            <i>{'From'}</i>: {$email.sender_address|escape:'html'}<br>
            {if isset($email.driver) && isset($email.driver.user) && !empty($email.driver.user)}
            <i>{'Driver'}</i>: {$email.driver.user.full_login|escape:'html'}</b><br><br>
            {/if}
            <i>{'Navigator'}</i>: {$email.title}<br>
        {if !empty($email.seek_response) && $email.seek_response > 0}
            <i>{'Seek Response'}</i>: {$email.seek_response|date_format:'d/m/Y'}<br>
           {/if}
            {if !empty($email.approve_by) && $email.approve_by > 0}
            <i>{'Approve by'}</i>: {$email.approver.full_login|escape:'html'}<br>
            {/if} 
            
            {if !empty($email.approve_deadline) && $email.approve_deadline > 0}
            {'Approve deadline'}</i>: {$email.approve_deadline|date_format:'d/m/Y'}
            {/if}
                <br>
                <br>
             </p>
        </div>

    </div>
</div>


{strip}
            
                
                
                        

                {if !empty($email.doc_type_name)}
                    <div class="input-group" style="margin-bottom: 10px;">
                        <span class="input-group-addon" style="border-radius:8px; background-color: #CCFFFF;">Type :{$email.doc_type_name|escape:'html'}</span>
                    </div>
                {/if}

                {if !empty($email.seek_response) && $email.seek_response > 0}
                    <div class="input-group" style="margin-bottom: 10px;">
                        <span class="input-group-addon" style="border-radius:8px; background-color: #CCFFFF;">Seek Response : {$email.seek_response|date_format:'d/m/Y'}</span>
                    </div>
                {/if}

                {if isset($email.driver) && isset($email.driver.user) && !empty($email.driver.user)}
                    <div class="input-group" style="margin-bottom: 10px;">
                        <span class="input-group-addon" style="border-radius:8px; background-color: #CCFFFF;">Driver : {$email.driver.user.full_login|escape:'html'}</span>                               
                    </div>
                {/if}
                {if !empty($email_users_list)}
                    <div class="input-group" style="margin-bottom: 10px;">
                        <span class="input-group-addon" style="border-radius:8px; background-color: #CCFFFF;">Navigators :
                                {foreach $email_users_list as $item}
                                    {if isset($item.user)}
                                        <div style="display: inline-block; margin-right: 10px;">{$item.user.full_login|escape:'html'}</div>
                                    {/if}
                                {/foreach}
                        </span>
                    </div>
                {/if}

           
             
                        
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

            <div class="pad1"></div>
            <hr style="width: 100%; color: #dedede;" size="1"/>
            <div class="pad1"></div>



            {if $email.type_id != $smarty.const.EMAIL_TYPE_ERROR && $email.type_id != $smarty.const.EMAIL_TYPE_SPAM}
                {if (!empty($email.approve_by) && $email.approve_by > 0)
                || (!empty($email.approve_deadline) && $email.approve_deadline > 0)
                || isset($email.author)
                || isset($email.modifier)
                || ($email.type_id == $smarty.const.EMAIL_TYPE_OUTBOX && isset($email.sender))
                || !empty($email.doc_type_name)
                || (!empty($email.seek_response) && $email.seek_response > 0)
                || (isset($email.driver) && isset($email.driver.user))
                || !empty($email_users_list)}
                <div class="pad1"></div>
                <hr style="width: 100%; color: #dedede;" size="1"/>
                <div class="pad1"></div>
            {/if}
            {if ($email.type_id == $smarty.const.EMAIL_TYPE_DRAFT || $email.type_id == $smarty.const.EMAIL_TYPE_OUTBOX) && isset($email_history) && !empty($email_history)}
                <div class="col-md-4">
                {if isset($email.author)}
                    <div class="input-group" style="margin-bottom: 10px;">
                        <span class="input-group-addon" style="border-radius:8px; background-color: #CCFFFF;">Created : {$email.author.login}, {$email.created_at|date_human:false} </span>

                    </div>
                {/if}
                {if isset($email.modifier)}
                    <div class="input-group" style="margin-bottom: 10px;">
                        <span class="input-group-addon" style="border-radius:8px; background-color: #CCFFFF;">Modified : {$email.modifier.login}, {$email.modified_at|date_human:false}</span>

                    </div>        
                {/if}
                <div class="input-group" style="margin-bottom: 10px;">
                    <span class="input-group-addon tags" style="border-radius:8px; background-color: #CCFFFF;"><b>Tags : {if !empty($objects)}
                            {foreach from=$objects item=row name=objects}
                                <a class="tag-{if in_array($row.object_alias, array('biz', 'company', 'order', 'person'))}{$row.object_alias}{else}document{/if}" href="/{$row.object_alias}/{$row.object_id}/blog">{$row.title}</a>
                            {/foreach}
                        {else}
                            <i>no tags</i>
                        {/if} </b></span>
                </div>
                {if $email.type_id == $smarty.const.EMAIL_TYPE_OUTBOX && isset($email.sender)}
                    <div class="input-group" style="margin-bottom: 10px;">
                        <span class="input-group-addon" style="border-radius:8px; background-color: #CCFFFF;">Sent : {$email.sender.login}, {$email.sent_at|date_human:false}</span>

                    </div>
                {/if}
                {if !empty($email.attachments)}
                <div class="panel-group" id="accordion" style="margin-bottom: 10px;">
                    <div class="panel panel-default" style="position: relative;">
                        <div class="panel-heading"> <!--  style="height: 30px;"-->
                            <h4 class="panel-title"> <!--  style="line-height: 8px;" -->
                                <a data-toggle="collapse" data-parent="#accordion" href="#collapseOne">
                                    <i class="glyphicon glyphicon-collapse-down"> </i> Attachments 
                                </a>
                                <span id="attachments-count"></span>
                            </h4>
                        </div>
                        <div id="collapseOne" class="panel-collapse collapse">
                            <div class="panel-body">
                                <div id="attachments" style="width: 100%;">
                                    {if isset($attachments) && !empty($attachments)}
                                        {foreach name=i from=$attachments item=row}
                                            {if !empty($row.attachment)}
                                                {include file="templates/html/emailmanager/control_attachment_block_text_view.tpl" attachment=$row.attachment object_alias=$uploader_object_alias object_id=$uploader_object_id}
                                            {/if}
                                        {/foreach}
                                    {/if}
                                </div>
                            </div>
                        </div>
                    </div>
                </div> 
                                </div>
                                {/if}
                <div class="pad1"></div>
                <hr style="width: 100%; color: #dedede;" size="1"/>
                <div class="pad1"></div>
                <table class="form" width="100%">
                    <tr>
                        <td class="form-td-title-b" style="padding-bottom: 15px;">eMail Versions : </td>
                        <td>
                            {foreach from=$email_history item=row}
                                <div class="dfa-sent">
                                    {if $email.id == $row.email_id}
                                        <b class="i-email-type{$row.type_id}">{$row.created_at|date_human:false}{if isset($row.user)} by {$row.user.login}{/if}</b> 
                                    {else}
                                        <a href="/email/{$row.email_id}" class="i-email-type{$row.type_id}">{$row.created_at|date_human:false}{if isset($row.user)} by {$row.user.login}{/if}</a>
                                    {/if}
                                </div>
                            {/foreach}
                        </td>
                    </tr>
                </table>    
            {/if}
            {/if}
                {/strip}











            {*  {strip}
            {if $email.type_id == $smarty.const.EMAIL_TYPE_SPAM}
            <span class="i-email-spam" style="font-weight: bold; color: #CA4242;">This Is SPAM</span>
            {elseif $email.type_id == $smarty.const.EMAIL_TYPE_ERROR}
            <span class="i-email-error" style="font-weight: bold; color: #948B07;">This Email Is Corrupted</span>
            {/if}
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
            {if !empty($row.attachment)}
            {if strstr($row.attachment.content_type, 'image')}
            <a class="attachment-{$row.attachment.ext|lower}" href="/file/{$row.attachment.secret_name}/{$row.attachment.original_name}" target="_blank">{$row.attachment.original_name} ({$row.attachment.size|human_filesize})</a>
            <a class="attachment-{$row.attachment.ext|lower}" href="/file/{$row.attachment.secret_name}/{$row.attachment.original_name}" rel="pp_attachments[]">{$row.attachment.original_name} ({$row.attachment.size|human_filesize})</a>
            {else}
            <a class="attachment-{$row.attachment.ext|lower}" href="/file/{$row.attachment.secret_name}/{$row.attachment.original_name}" target="_blank">{$row.attachment.original_name} ({$row.attachment.size|human_filesize})</a>
            {/if}
            {/if}
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
            
            <div class="pad1"></div>
            <hr style="width: 100%; color: #dedede;" size="1"/>
            <div class="pad1"></div>
            
            <table class="form" width="100%">
            <tr>
            <td class="form-td-title-b text-top" style="line-height: 16px;">Tags : </td>
            <td style="line-height: 16px;" class="tags">
            {if !empty($objects)}
            {foreach from=$objects item=row name=objects}
            <a class="tag-{if in_array($row.object_alias, array('biz', 'company', 'order', 'person'))}{$row.object_alias}{else}document{/if}" href="/{$row.object_alias}/{$row.object_id}/blog">{$row.title}</a>
            {/foreach}
            {else}
            <i>no tags</i>
            {/if}
            </td>
            </tr>
            </table>    
            
            {if $email.type_id != $smarty.const.EMAIL_TYPE_ERROR && $email.type_id != $smarty.const.EMAIL_TYPE_SPAM}
            {if (!empty($email.approve_by) && $email.approve_by > 0)
            || (!empty($email.approve_deadline) && $email.approve_deadline > 0)
            || isset($email.author)
            || isset($email.modifier)
            || ($email.type_id == $smarty.const.EMAIL_TYPE_OUTBOX && isset($email.sender))
            || !empty($email.doc_type_name)
            || (!empty($email.seek_response) && $email.seek_response > 0)
            || (isset($email.driver) && isset($email.driver.user))
            || !empty($email_users_list)}
            <div class="pad1"></div>
            <hr style="width: 100%; color: #dedede;" size="1"/>
            <div class="pad1"></div>
        
            <table width="100%">
            <tr>
            <td class="text-top" width="34%">
            <table class="form" width="100%">
            {if !empty($email.approve_by) && $email.approve_by > 0}
            <tr>
            <td class="form-td-title-b text-top" style="line-height: 16px;">Approve By : </td>
            <td style="line-height: 16px;">{$email.approver.full_login|escape:'html'}</td>
            </tr>
            {/if}
            
            {if !empty($email.approve_deadline) && $email.approve_deadline > 0}
            <tr>
            <td class="form-td-title-b text-top" style="line-height: 16px;">Approve Deadline : </td>
            <td style="line-height: 16px;">{$email.approve_deadline|date_format:'d/m/Y'}</td>
            </tr>
            {/if}
            {if isset($email.author)}
            <tr>
            <td class="form-td-title-b">Created : </td>
            <td>{$email.author.login}, {$email.created_at|date_human:false}</td>
            </tr>
            {/if}
            {if isset($email.modifier)}
            <tr>
            <td class="form-td-title-b">Modified : </td>
            <td>{$email.modifier.login}, {$email.modified_at|date_human:false}</td>
            </tr>        
            {/if}
            {if $email.type_id == $smarty.const.EMAIL_TYPE_OUTBOX && isset($email.sender)}
            <tr>
            <td class="form-td-title-b">Sent : </td>
            <td>{$email.sender.login}, {$email.sent_at|date_human:false}</td>
            </tr>
            {/if}        
            </table>
            </td>
            <td class="text-top" width="33%">
            <table class="form" width="100%">
            {if !empty($email.doc_type_name)}
            <tr>
            <td class="form-td-title-b text-top" style="line-height: 16px;">Type : </td>
            <td style="line-height: 16px;">{$email.doc_type_name|escape:'html'}</td>
            </tr>
            {/if}
            
            {if !empty($email.seek_response) && $email.seek_response > 0}
            <tr>
            <td class="form-td-title-b text-top" style="line-height: 16px;">Seek Response : </td>
            <td style="line-height: 16px;">{$email.seek_response|date_format:'d/m/Y'}</td>
            </tr>
            {/if}
            </table>        
            </td>
            <td class="text-top" width="33%">
            <table class="form" width="100%">
            {if isset($email.driver) && isset($email.driver.user) && !empty($email.driver.user)}
            <tr>
            <td class="form-td-title-b text-top" style="line-height: 16px;">Driver : </td>
            <td style="line-height: 16px;">{$email.driver.user.full_login|escape:'html'}</td>
            </tr>
            {/if}
            
            {if !empty($email_users_list)}
            <tr>
            <td class="form-td-title-b text-top" style="line-height: 16px;">Navigators : </td>
            <td style="line-height: 16px;">
            {foreach $email_users_list as $item}{if isset($item.user)}
            <div style="display: inline-block; margin-right: 10px;">{$item.user.full_login|escape:'html'}</div>
            {/if}{/foreach}
            </td>
            </tr>
            {/if}
            </table>
            </td>
            </tr>
            </table>
            {/if}
            {if ($email.type_id == $smarty.const.EMAIL_TYPE_DRAFT || $email.type_id == $smarty.const.EMAIL_TYPE_OUTBOX) && isset($email_history) && !empty($email_history)}
            <div class="pad1"></div>
            <hr style="width: 100%; color: #dedede;" size="1"/>
            <div class="pad1"></div>
            <table class="form" width="100%">
            <tr>
            <td class="form-td-title-b" style="padding-bottom: 15px;">eMail Versions : </td>
            <td>
            {foreach from=$email_history item=row}
            <div class="dfa-sent">
            {if $email.id == $row.email_id}
            <b class="i-email-type{$row.type_id}">{$row.created_at|date_human:false}{if isset($row.user)} by {$row.user.login}{/if}</b> 
            {else}
            <a href="/email/{$row.email_id}" class="i-email-type{$row.type_id}">{$row.created_at|date_human:false}{if isset($row.user)} by {$row.user.login}{/if}</a>
            {/if}
            </div>
            {/foreach}
            </td>
            </tr>
            </table>    
            {/if}
            {/if}
            {/strip}
            *}

