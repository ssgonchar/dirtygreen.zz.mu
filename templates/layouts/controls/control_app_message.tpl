{if $message_status == $smarty.const.MESSAGE_OKAY}
    <a class="okay">{$message_text}</a>
{/if}
{if $message_status == $smarty.const.MESSAGE_ERROR}
    <a class="error">{$message_text}</a>
{/if}
{if $message_status == $smarty.const.MESSAGE_WARNING}
    <a class="warning">{$message_text}</a>
{/if}