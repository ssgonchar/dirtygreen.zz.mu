{foreach from=$mailboxes item=row}
    <p><a href="javascript:void(0);" data-id='{$row['mailbox']['id']}'>{$row['mailbox']['title']}</a></p>
    <p><small><i>{$row['mailbox']['address']}</i></small></p>
{/foreach}