{if isset($pages) && isset($pages.pages) && !empty($pages.pages)}
<div style="margin-top: 5px;">
Page : 
{section name=i loop=$pages.pages}
    {if $pages.pages[i].active}<b style="margin-right: 5px;">{$pages.pages[i].number}</b>
    {else}<a href="{$path}/~{$pages.pages[i].number}" style="margin-right: 5px;">{$pages.pages[i].number}</a>{/if}
{/section}
</div>
{/if}