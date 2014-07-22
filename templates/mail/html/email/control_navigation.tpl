{if !empty($object_alias) && !empty($object_id)}

{else}
{*
    {if $page == 'search'}<b style="margin-left: 10px;">Search{if isset($count) && !empty($count)} ({$count}){/if}</b>
    {else}<a href="/emails/search" style="margin-left: 10px;">Search</a>
    {/if}
    
    {if $page == 'list'}<b style="margin-left: 10px;">List</b>
    {else}<a href="/emails" style="margin-left: 10px;">List</a>
    {/if}
*}
{*
    {if isset($object_stat)}{number value=$object_stat.emails zero='' e0='emails' e1='email' e2='emails'}{/if}

    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
    <span class="choosen-items-stats">Choosen emails:&nbsp;<span class="cis-checked">0</span>&nbsp;of&nbsp;<span class="cis-onpage">{$smarty.const.ITEMS_PER_PAGE|string_format:'%d'}</span></span>
*}

    <span class="choosen-items-stats" style="display: none;"><span class="cis-checked">0</span>&nbsp;of&nbsp;</span>{if isset($object_stat)}{number value=$object_stat.emails zero='' e0='emails' e1='email' e2='emails'}{/if}
    {if !empty($pager_pages)}<br>{include file="templates/layouts/controls/control_pagination1.tpl" pages=$pager_pages path=$pager_path}{/if}
{/if}