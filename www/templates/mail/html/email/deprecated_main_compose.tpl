<div style="width: 30%; float: left; vertical-align: top;">
    <table class="form" width="100%">
        <tr height="32">
            <td class="form-td-title-b">BIZ : </td>
            <td>
            {if isset($biz)}
                {$biz.doc_no|escape:'html'}
            {else}                
                <input type="text" id="biz_title" name="form[biz_title]" class="normal"{if isset($form.biz_title)} value="{$form.biz_title}"{/if}>
                <input type="hidden" id="biz_id" name="form[biz_id]" value="{if isset($form.biz_id)}{$form.biz_id}{else}0{/if}">
            {/if}
            </td>
        </tr>
        <tr height="32">
            <td class="form-td-title-b">Company : </td>
            <td>
            {if isset($company)}
                {$company.title|escape:'html'}
            {else}
                <input type="text" id="company_title" name="form[company_title]" class="normal"{if isset($form.company_title)} value="{$form.company_title}"{/if}>
                <input type="hidden" id="company_id" name="form[company_id]" value="{if isset($form.company_id)}{$form.company_id}{else}0{/if}">
                <select id="companies" name="form[biz_company_id]" class="normal" onchange="get_persons_by_company(this.value);" style="display: none;">
                    <option value="0">--</option>
                    {foreach from=$companies item=row}
                    <option value="{$row.company.id}"{if isset($form.company_id) && $row.company.id == $form.company_id} selected="selected"{/if}>{$row.company.title|escape:'html'}</option>
                    {/foreach}
                </select>
            {/if}
            </td>
        </tr>
        <tr height="32">
            <td class="form-td-title-b">Person : </td>
            <td>
            {if isset($person)}
                {$person.full_name|escape:'html'}
            {else}
                <input type="text" id="person_title" name="form[person_title]" class="normal"{if isset($form.person_title)} value="{$form.person_title}"{/if}>
                <input type="hidden" id="person_id" name="form[person_id]" value="{if isset($form.person_id)}{$form.person_id}{else}0{/if}">
                <select id="persons" name="form[person_id]" class="normal" style="display: none;">
                    <option value="0">--</option>
                    {foreach from=$persons item=row}
                    <option value="{$row.person.id}"{if isset($form.person_id) && $row.person.id == $form.person_id} selected="selected"{/if}>{$row.person.full_name|escape:'html'}</option>
                    {/foreach}                            
                </select>
            {/if}
            </td>
        </tr>                
    </table>
</div>
<div style="width: 30%; float: left; vertical-align: top;">
    <table class="form" width="100%">
        <tr>
            <td class="form-td-title-b">Sender Address : </td>
            <td>
                <select name="form[sender_address]" class="max">
                    <option value="">--</option>
                    <option value="plates@steelemotion.com"{if isset($form) && isset($form.sender_address) && $form.sender_address == 'plates@steelemotion.com'} selected="selected"{/if}>plates@steelemotion.com</option>
                    <option value="steel@steelemotion.com"{if isset($form) && isset($form.sender_address) && $form.sender_address == 'steel@steelemotion.com'} selected="selected"{/if}>steel@steelemotion.com</option>
                </select>
            </td>
        </tr>
        <tr>
            <td class="form-td-title-b">Recipient Address : </td>
            <td><input type="text" name="form[recipient_address]" class="max"{if isset($form) && isset($form.recipient_address)} value="{$form.recipient_address|escape:'html'}"{/if}></td>
        </tr>
        <tr height="32">
            <td class="form-td-title-b">Attachements : </td>
            <td>
            {if isset($attachments) && !empty($attachments)}
                {foreach name=i from=$attachments item=row}
                {if !$smarty.foreach.i.first}, {/if}<a href="/file/{$row.secret_name}/{$row.original_name|escape:'html'}" target="_blank">{$row.original_name|escape:'html'}</a>
                {/foreach}
            {else}
                <i>none</i>
            {/if}
            </td>
        </tr>                
    </table>
</div>
<div style="width: 40%; vertical-align: top;">
    <table class="form" width="100%">
        <tr>
            <td class="form-td-title"><i>To</i> : </td>
            <td><input type="text" name="form[to]" class="max"{if isset($form) && isset($form.to)} value="{$form.to|escape:'html'}"{/if}></td>
        </tr>
        <tr>
            <td class="form-td-title"><i>Attention</i> : </td>
            <td><input type="text" name="form[attention]" class="max"{if isset($form) && isset($form.attention)} value="{$form.attention|escape:'html'}"{/if}></td>
        </tr>
        <tr>
            <td class="form-td-title"><i>Subject</i> : </td>
            <td><input type="text" name="form[subject]" class="max"{if isset($form) && isset($form.subject)} value="{$form.subject|escape:'html'}"{/if}></td>
        </tr>    
        <tr>
            <td class="form-td-title"><i>Our Ref.</i> : </td>
            <td><input type="text" name="form[our_ref]" class="max"{if isset($form) && isset($form.our_ref)} value="{$form.our_ref|escape:'html'}"{/if}></td>
        </tr>    
        <tr>
            <td class="form-td-title"><i>Your Ref.</i> : </td>
            <td><input type="text" name="form[your_ref]" class="max"{if isset($form) && isset($form.your_ref)} value="{$form.your_ref|escape:'html'}"{/if}></td>
        </tr>
    </table>
</div>
<div class="separator pad"></div>

<table class="form" width="100%">
    <tr>
        <td class="form-td-title-b">Subject : <br><img src="/img/_blank.png" width="120" height="1"></td>
        <td><input type="text" name="form[title]" class="max"{if isset($form) && isset($form.title)} value="{$form.title|escape:'html'}"{/if}></td>
    </tr>
    <tr>
        <td class="form-td-title-b text-top">Text : </td>
        <td><textarea id="email_text" name="form[description]" style="width: 100%">{if isset($form) && isset($form.description)}{$form.description}{/if}</textarea><script type="text/javascript">add_mce_editor('email_text', 'normal', 500);</script></td>
    </tr>
    <tr>
        <td class="form-td-title-b text-top">Signature : </td>
        <td>
            <textarea name="form[signature]" class="max" rows="5">{if isset($form) && isset($form.signature)}{$form.signature|escape:'html'}{/if}</textarea>
        </td>
    </tr>    
</table>