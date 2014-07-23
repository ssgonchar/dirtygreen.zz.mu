<div style="width: 30%; float: left; vertical-align: top;">
    <table class="form" width="100%">
        <tr height="32">
            <td class="form-td-title-b">BIZ : </td>
            <td>
            {if isset($biz)}
                {$biz.doc_no|escape:'html'}
            {else}
                <i>none</i>
            {/if}
            </td>
        </tr>
        <tr height="32">
            <td class="form-td-title-b">Company : </td>
            <td>
            {if isset($company)}
                {$company.title|escape:'html'}
            {else}
                <i>none</i>
            {/if}
            </td>
        </tr>
        <tr height="32">
            <td class="form-td-title-b">Person : </td>
            <td>
            {if isset($person)}
                {$person.full_name|escape:'html'}
            {else}
                <i>none</i>
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
            <td class="form-td-title-b">Attachments : </td>
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