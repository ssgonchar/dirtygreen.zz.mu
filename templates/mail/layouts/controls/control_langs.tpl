{* if !empty($langs)}
<div class="lang-selector">
    Языки сайта:&nbsp;&nbsp;
    {if $current_lang == $smarty.const.DEFAULT_LANG}
        <b>{$smarty.const.DEFAULT_LANG}</b>
    {else}
        <a href="/{$smarty.const.DEFAULT_LANG}{$smarty.request.query_string|escape:'html'}">{$smarty.const.DEFAULT_LANG}</a>
    {/if}

    {foreach from=$langs item=row}&nbsp;
        {if $current_lang == $row.lang.alias}
            <b>{$row.lang.alias}</b>
        {else}
            <a href="/{$row.lang.alias}{$smarty.request.query_string|escape:'html'}">{$row.lang.alias}</a>
        {/if}
    {/foreach}
</div>
{/if *}