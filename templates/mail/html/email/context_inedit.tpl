<div class="footer-left">
    {if !empty($email_id)}
    <table style="float: left; margin-right: 20px;">
        <tr>
            <td style="font-weight: bold; text-align: right;" width="70px">Created</td>
            <td style="text-align: center;" width="20px"> : </td>
            <td width="200px">{if isset($email.author)}{$email.author.login}, {/if}{$email.created_at|date_human:false}</td>
        </tr>
        <tr>
            <td style="font-weight: bold; text-align: right;">Modified</td>
            <td style="text-align: center;" width="20px"> : </td>
            <td>{if isset($email.modifier)}{$email.modifier.login}, {/if}{$email.modified_at|date_human:false}</td>
        </tr>        
    </table>
    {/if}
</div>
<div class="footer-right">
    {if isset($page)}
        {if $page == 'reply' || $page == 'sendagain'}
        <input type="button" class="btn100" value="Cancel" onclick="location.href='{$backurl}';" style="cursor: pointer;">
        {/if}
    {else}
        <input type="button" class="btn100" value="Cancel" onclick="location.href='/email/{$email.id}';" style="cursor: pointer;">
    {/if}
    {*<input type="submit" name="btn_save" class="btn100o" value="Save" style="margin-left: 10px; cursor: pointer;">*}
    <input type="hidden" name="btn_save" value="Save" />
    <input type="button" class="btn100o" value="Save" style="margin-left: 10px; cursor: pointer;" onclick="document.getElementById('mainform').submit();">
</div>