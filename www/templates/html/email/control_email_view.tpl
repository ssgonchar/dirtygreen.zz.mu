<h4 style="font-weight: bold;">{$email.email.title}</h4>
<div style="top: 30px; bottom: 0px; left: 0px; right: 0px; overflow-y: auto;">
<table class="form">
    <tr>
        <td class="form-td-title-b">From : </td>
        <td>{$email.email.sender_address|replace:'<':'&lt;'|replace:'>':'&gt;'}</td>
    </tr>
    <tr>
        <td class="form-td-title-b">To : </td>
        <td>{$email.email.recipient_address|replace:'<':'&lt;'|replace:'>':'&gt;'}</td>
    </tr>    
    {if !empty($email.email.cc_address)}
    <tr>
        <td class="form-td-title-b">Cc : </td>
        <td>{$email.email.cc_address|replace:'<':'&lt;'|replace:'>':'&gt;'}</td>
    </tr>    
    {/if}
	{if !empty($email.email.bcc_address)}
    <tr>
        <td class="form-td-title-b">Bcc : </td>
        <td>{$email.email.bcc_address|replace:'<':'&lt;'|replace:'>':'&gt;'}</td>
    </tr>    
    {/if}
    <tr>
        <td></td>
        <td style="color: #777;">
            {$email.email.date_mail|date_format:'d/m/Y'}&nbsp{$email.email.date_mail|date_format:'H:i:s'}
            <a href="/email/{$email.email.id}" style="margin-left: 10px;">permanent link</a>
        </td>
    </tr>    
</table>
<div class="pad"></div>

{$email.email.description|strip_tags:false|nl2br}</b></i></a>

{if isset($email.email.attachments) && !empty($email.email.attachments)}
<div class="chat-message-attachments" style="line-height: 18px;">
    {foreach from=$email.email.attachments item=att}
        {include file='templates/html/dropbox/control_attachment_block_text.tpl' attachment=$att.attachment readonly=true}
    {/foreach}
</div>
{/if}    
</div>