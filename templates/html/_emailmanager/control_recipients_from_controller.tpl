{if isset($recipients_from_controller)}{*debug*}
    {foreach from=$recipients_from_controller key=key item=row}
        <li  style="border: solid 1px #ccc; cursor: pointer; margin-top: 5px; padding-left: 5px; padding-bottom: 3px; background-color: #eeeeee;">
            <span class="email-adress">{$row.email_adress}</span>
            <i id="remove-recipient" class="glyphicon glyphicon-remove pull-right" style="margin-top: 4px; margin-right: 4px; cursor: pointer;"></i>
            <span class="company-id hidden">{$row.company_id}</span>
        </li>
    {/foreach}
{/if}