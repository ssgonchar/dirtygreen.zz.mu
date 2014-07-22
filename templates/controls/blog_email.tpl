<div class="biz-blog-email-block biz-blog-entity">
    <div 
        {if $row.email.type_id == $smarty.const.EMAIL_TYPE_DRAFT}
            class="biz-blog-dfa-head"
        {elseif $row.email.type_id == $smarty.const.EMAIL_TYPE_OUTBOX}
            class="biz-blog-sent-head" 
        {elseif $row.email.type_id == $smarty.const.EMAIL_TYPE_INBOX}
            class="biz-blog-inbox-head" 
        {/if}
    >
        <div class="biz-blog-email-head-left">
            <div class="biz-blog-email-avatar">
                {if  isset($row.email.author) && $row.email.author.id == $smarty.const.GNOME_USER}
                    <img src="/img/layout/gnome.jpg" alt="Gnome" alt="Gnome">
                {elseif isset($row.email.author) && isset($row.email.sender.author)}
                    {if isset($row.email.author.picture)}{picture type="person" size="x" source=$row.email.author.picture}
                {elseif $row.email.author.gender == 'f'}<img src="/img/layout/anonymf.png" alt="{$row.email.author.login}" alt="{$row.email.author.login}">
                    {else}<img src="/img/layout/anonym.png" alt="{$row.email.author.login}" alt="{$row.email.author.login}">{/if}
                {else}
                    <img src="/img/layout/anonym.png" alt="No Picture" alt="No Picture">
                {/if}
            </div>
            <b>{$row.email.title}</b>
            <div class="biz-blog-email-link-address">
                {if $row.email.type_id == $smarty.const.EMAIL_TYPE_DRAFT}
                    by {$row.email.author.login|escape:'html'}
                {elseif $row.email.type_id == $smarty.const.EMAIL_TYPE_OUTBOX}
                    <table{if !empty($row.email.title)} style="margin-top: 6px;"{/if}>
                        <tr>
                            <td class="text-right">To&nbsp;:&nbsp;</td>
                            <td>{$row.email.recipient_address|replace:'<':'&lt;'|replace:'>':'&gt;'}</td>
                        </tr>
                        {if !empty($row.email.cc_address)}
                        <tr>
                            <td style="padding-top: 2px" class="text-right">Cc&nbsp;:&nbsp;</td>
                            <td style="padding-top: 2px">{$row.email.cc_address|replace:'<':'&lt;'|replace:'>':'&gt;'}</td>
                        </tr>
                        {/if}
                        {if !empty($row.email.bcc_address)}
                        <tr>
                            <td style="padding-top: 2px">Bc&nbsp;:&nbsp;</td>
                            <td style="padding-top: 2px">{$row.email.bcc_address|replace:'<':'&lt;'|replace:'>':'&gt;'}</td>
                        </tr>
                        {/if}
                        <tr>
                            <td style="padding-top: 6px">From&nbsp;:&nbsp;</td>
                            <td style="padding-top: 6px">{$row.email.sender_address|replace:'<':'&lt;'|replace:'>':'&gt;'}</td>
                        </tr>                        
                    </table>
                {elseif $row.email.type_id == $smarty.const.EMAIL_TYPE_INBOX}
                    <table{if !empty($row.email.title)} style="margin-top: 6px;"{/if}>
                        <tr>
                            <td class="text-right">From&nbsp;:&nbsp;</td>
                            <td>{$row.email.sender_address|replace:'<':'&lt;'|replace:'>':'&gt;'}</td>
                        </tr>
                        <tr>
                            <td style="padding-top: 6px" class="text-right">To&nbsp;:&nbsp;</td>
                            <td style="padding-top: 6px">{$row.email.recipient_address|replace:'<':'&lt;'|replace:'>':'&gt;'}</td>
                        </tr>
                        {if !empty($row.email.cc_address)}
                        <tr>
                            <td style="padding-top: 2px">Cc&nbsp;:&nbsp;</td>
                            <td style="padding-top: 2px">{$row.email.cc_address|replace:'<':'&lt;'|replace:'>':'&gt;'}</td>
                        </tr>
                        {/if}
                        {if !empty($row.email.bcc_address)}
                        <tr>
                            <td style="padding-top: 2px">Bcc&nbsp;:&nbsp;</td>
                            <td style="padding-top: 2px">{$row.email.bcc_address|replace:'<':'&lt;'|replace:'>':'&gt;'}</td>
                        </tr>
                        {/if}
                    </table>
                {/if}    
            </div>    
        </div>
        <div class="biz-blog-email-head-right">
            <div class="biz-blog-email-created-at">
                {$row.email.created_at|date_format:"d/m/Y"}<br> 
                {$row.email.created_at|date_format:"H:i:s"}
            </div>
            {if $row.email.type_id == $smarty.const.EMAIL_TYPE_DRAFT}   
                <div class="biz-blog-dfa-logo">DFA</div>
            {elseif $row.email.type_id == $smarty.const.EMAIL_TYPE_OUTBOX}
                <div class="biz-blog-sent-logo">SENT</div>    
            {elseif $row.email.type_id == $smarty.const.EMAIL_TYPE_INBOX}
                <div class="biz-blog-inbox-logo">INBOX</div>        
            {/if}
        </div> 
        <div class="separator"></div>    
    </div>
    <div class="biz-blog-email-text">
        {$row.email.description|parse|nl2br}
        <div class="pad-10"></div>
    </div> 
    {if isset($row.message.attachments) && !empty($row.message.attachments)}    
        <div class="biz-blog-email-attachments">
            {foreach from=$row.message.attachments item=att}
                {include file='templates/html/dropbox/control_attachment_block_text.tpl' attachment=$att.attachment readonly=true}        
            {/foreach}
        </div> 
    {/if}
    <div class="biz-blog-email-link">
        
        <div class="biz-blog-email-link-left">
            <a class="biz-blog-href-reference" title="Get reference" onclick="show_blog_email_ref(this, {$row.email.id}, '{$row.email.title|escape:quotes}');">Get reference</a>
            {if $row.email.type_id == $smarty.const.EMAIL_TYPE_INBOX}
                <a class="biz-blog-href-html" title="HTML Version" onclick="window.open('/email/{$row.email.id}/gethtml', '{$row.email.title|escape:'quotes'}', 'scrollbars=yes,resizable=yes,height=500,width=800');">HTML Version</a>
                <a class="biz-blog-href-email-reply" title="Answer" href = "/email/{$row.email.id}/reply">Answer</a>
            {elseif $row.email.type_id == $smarty.const.EMAIL_TYPE_OUTBOX}
                <a class="biz-blog-href-email-sendagain" title="Edit & Send Again" href = "/email/{$row.email.id}/sendagain">Edit & Send Again</a>
            {elseif $row.email.type_id == $smarty.const.EMAIL_TYPE_DRAFT}
                <a class="biz-blog-href-email-edit" title="Edit" href = "/email/{$row.email.id}/edit">Edit</a>
            {/if}

            <a href="/email/{$row.email.id}" class="biz-blog-href-email-link" title="Link">Details</a>
        </div>
        <div class="biz-blog-email-link-right">
            Email ID : {$row.email.id}
        </div>    
    </div>     
</div>    