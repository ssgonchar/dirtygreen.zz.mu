<font face="Courier">
<p style="font-family: Courier; font-size: 12px; margin: 0;">
    <b style="color: #00f;"><font color="#0000ff">{if $email.sender_domain == 'platesahead.com'}
    {if $email.type_id == $smarty.const.EMAIL_TYPE_DRAFT}{$smarty.now|date_format:"F jS 'y"}{else}{$email.date_mail|date_format:"F jS 'y"}{/if}
    {else}
    {if $email.type_id == $smarty.const.EMAIL_TYPE_DRAFT}{$smarty.now|date_format:"d/m/Y"}{else}{$email.date_mail|date_format:"d/m/Y"}{/if}
    {/if}</font></b>
    <br><br><br>
    <i>{'To'|str_pad:10}</i> : {$email.to}<br>
    <i>{'Attention'|str_pad:10}</i> : <b style="color:#f00;"><font color="#ff0000">{$email.attention}</font></b><br><br>
    
    <i>{'Subject'|str_pad:10}</i> : {$email.subject}<br>
    <i>{'Our Ref.'|str_pad:10}</i> : {$email.our_ref}<br>
    <i>{'Your Ref.'|str_pad:10}</i> : {$email.your_ref}<br>
    {if isset($email.attached)}<i>{'Attached'|str_pad:10}</i> : {$email.attached}<br>{/if}
    <br>
    <hr width="100%">
    <br>
</p>
</font>

{$email.description}

{if $email.type_id == $smarty.const.EMAIL_TYPE_DRAFT || $email.type_id == $smarty.const.EMAIL_TYPE_OUTBOX}
    <br><br><br>
    {if !empty($email.signature)}{$email.signature|nl2br}<br><br>{/if}
    {if $email.sender_domain == 'steelemotion.com'}
        <b>STEELemotion</b>{if !empty($email.signature3)}&nbsp;/&nbsp;<i>{$email.signature3}</i>{/if}<br>
        {if !empty($email.signature2)}{$email.signature2|nl2br}<br>{/if}
        <a href="http://www.steelemotion.com/" class="content-href">http://www.STEELemotion.com/</a>    
    {elseif $email.sender_domain == 'platesahead.com'}
        <b>PlatesAhead Inc.</b>{if !empty($email.signature3)}&nbsp;/&nbsp;<i>{$email.signature3}</i>{/if}<br>
        {if !empty($email.signature2)}{$email.signature2|nl2br}<br>{/if}
        <a href="http://www.platesahead.com/" class="content-href">http://www.PlatesAhead.com/</a>    
    {/if}
{/if}

{if isset($email.parent)}
<br><br><br>{if $email.sender_domain == 'platesahead.com'}{$email.parent.date_mail|date_format:"F jS 'y"}{else}{$email.parent.date_mail|date_format:"d/m/Y"}{/if}
<br>From : {$email.parent.sender_address}
<br>To : {$email.parent.recipient_address}
<br>Subject : {$email.parent.title}
<br><blockquote class="email-answer">
{$email.parent.description|nl2br}
</blockquote>
{/if}
