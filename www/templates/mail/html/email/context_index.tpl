<div class="footer-left" style="width: 30%">{include file="templates/html/email/control_navigation.tpl" page="list"}</div>
<div class="footer-right">
    {if $type_id == $smarty.const.EMAIL_TYPE_SPAM}
        <input type="submit" name="btn_is_not_spam" class="btn100 manage-buttons mb-type-notspam" style="display: none; margin-left: 10px;" value="Not spam" />
        {if $smarty.session.user.role_id <= $smarty.const.ROLE_MODERATOR}
        <input type="submit" name="btn_delete_spam" class="btn100 manage-buttons mb-type-delete" onclick="if(!confirm('Delete email(s) from my list ?')) return false;" style="display: none; margin-left: 10px;" value="Delete" />
        {/if}
    {else}
        {if !empty($page) && $page == 'deleted_by_user'}
        <input type="submit" name="restore_by_user" class="btn100 manage-buttons mb-type-restore" style="display: none; margin-left: 10px;" value="Restore" />
        {else}
        <input type="submit" name="delete_by_user" class="btn100 manage-buttons mb-type-delete" onclick="if(!confirm('Delete email(s) from my list ?')) return false;" style="display: none; margin-left: 10px;" value="Delete" />
        <input type="submit" name="btn_is_spam" class="btn100 manage-buttons mb-type-spam" style="display: none; margin-left: 10px;" value="Spam" />
        <input type="submit" name="btn_as_read" class="btn150 manage-buttons mb-type-read" style="display: none; margin-left: 10px;" value="Mark as read" />
        <input type="submit" name="btn_as_unread" class="btn150 manage-buttons mb-type-unread" style="display: none; margin-left: 10px;" value="Mark as unread" />
        {/if}
    {/if}
</div>
