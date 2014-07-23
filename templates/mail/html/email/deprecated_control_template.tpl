<b style="color: #00f;"><font color="#0000ff">{$email.date}</font></b>
<br><br><br>
<table cellpadding="0" cellspacing="0">
    <tr>
        <td width="100"><i>To</i></td>
        <td width="30"><i>:</i></td>
        <td>{$email.to|escape:'html'}</td>
    </tr>
    <tr>
        <td><i>Attention</i></td>
        <td><i>:</i></td>
        <td><b style="color:#f00;"><font color="#ff0000">{$email.attention|escape:'html'}</font></b></td>
    </tr>    
    <tr><td>&nbsp;</td></td>
    <tr>
        <td><i>Subject</i></td>
        <td><i>:</i></td>
        <td>{$email.subject|escape:'html'}</td>
    </tr>    
    <tr>
        <td><i>Our Ref.</i></td>
        <td><i>:</i></td>
        <td>{$email.our_ref|escape:'html'}</td>
    </tr>    
    <tr>
        <td><i>Your Ref.</i></td>
        <td><i>:</i></td>
        <td>{$email.your_ref|escape:'html'}</td>
    </tr>    
    {if !empty($attachments)}
    <tr><td>&nbsp;</td></td>
    <tr>
        <td><i>Attached</i></td>
        <td><i>:</i></td>
        <td>{$attachments_str|escape:'html'}</td>
    </tr>    
    {/if}
</table>
<hr><br><br>
{$email.description}
<br><br><br>
{$email.signature|nl2br}