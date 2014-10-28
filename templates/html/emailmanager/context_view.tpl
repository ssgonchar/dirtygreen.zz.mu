<ul class="nav navbar-nav navbar-right" style=''>
    <li>
        {if $email.type_id != $smarty.const.EMAIL_TYPE_DRAFT && !empty($email.description_html)}
        <input type="button" id="btn-email-html" value="Show HTML"  onclick="window.open('/emailmanager/{$email.id}/gethtml ', '_blank', 'email_html_{$email.id}', 'fullscreen=yes,scrollbars=yes,resizable=yes');" class="btn btn-info">
        {/if}



        {if $email.type_id == $smarty.const.EMAIL_TYPE_DRAFT}
            <input type="button" class="btn btn-primary" value="Edit" onclick="location.href='{if !empty($object_alias) && !empty($object_id)}/{$object_alias}/{$object_id}{/if}/emailmanager/{$email.id}/edit';" style="margin: 7px; cursor: pointer;">
            <input type="submit" name="btn_send" class="btn btn-success" value="Send" style="margin: 7px; cursor: pointer;" onclick="return confirm('Am I sure ?');">
        {/if}
        {*<input type="button" class="btn btn-default" value="Close" onclick="location.href='{$backurl}';" style="margin: 7px; cursor: pointer;">*}
        {if $email.type_id == $smarty.const.EMAIL_TYPE_INBOX}
            {*<input type="button" class="btn btn-primary" value="Set Filter" onclick="location.href='/email/filter/addfromemail/{$email.id}';" style="margin: 7px; cursor: pointer;">*}
            <input type="button" class="btn btn-warning" value="Spam" onclick="if (confirm('Is this spam ?')) location.href='/email/{$email.id}/spam';" style="margin: 7px; cursor: pointer;">
            {*<input type="button" class="btn100" value="Tagging" onclick="location.href='/email/{$email.id}/inedit';" style="margin: 7px; cursor: pointer;" />*}
            <input type="button" class="btn btn-success email-reply" value="Reply" onclick="location.href='/emailmanager/{$email.id}/reply';" style="margin: 7px; cursor: pointer;">
        {/if}    

        {if $email.type_id == $smarty.const.EMAIL_TYPE_OUTBOX} 
            {*<input type="button" class="btn btn-primary" value="Set Filter" onclick="location.href='/email/filter/addfromemail/{$email.id}';" style="margin: 7px; cursor: pointer;">*}
            <input type="button" class="btn btn-warning" value="Spam" onclick="if (confirm('Is this spam ?')) location.href='/email/{$email.id}/spam';" style="margin: 7px; cursor: pointer;">
            <input type="button" class="btn btn-primary email-edit-send" value="Edit & Send Again" onclick="location.href='/emailmanager/{$email.id}/sendagain';" style="margin: 7px; cursor: pointer;">
        {/if}    

        {if $email.type_id == $smarty.const.EMAIL_TYPE_SPAM}
            <input type="button" class="btn btn-primary" value="Not Spam" onclick="if (confirm('Is this not spam ?')) location.href='/email/{$email.id}/notspam';" style="margin: 7px; cursor: pointer;">
        {/if}    
    </li>
</ul> 