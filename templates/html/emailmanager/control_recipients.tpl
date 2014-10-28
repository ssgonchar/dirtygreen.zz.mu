{* шаблон для отрисовки получателей из системы в окне редактирования письма *}
{if isset($recipients)}{*debug*}
    {foreach from=$recipients key=key item=row}
        <li  style="border: solid 1px #ccc; cursor: pointer; margin-top: 5px; padding-left: 5px; padding-bottom: 3px; background-color: #eeeeee;">
            <span class="email-ardess">{$key}</span>
            <i id="remove-recipient" class="glyphicon glyphicon-remove pull-right" style="margin-top: 4px; margin-right: 4px; cursor: pointer;"></i>
            <span class="company-id hidden">{$row}</span>
        </li>
    {/foreach}
{/if}
