    {if $page == 'pending'}
        <b>MustDo !{if isset($count) && !empty($count) && $page == 'pending'} ({$count}){/if}</b>
    {else}
        <a href="/touchline/mustdo">MustDO !{if isset($count) && !empty($count) && $page == 'pending'} ({$count}){/if}</a>
    {/if}
    {if $page == 'search'}
        <b style="margin-left: 10px;">Search{if isset($count) && !empty($count) && $page == 'search'} ({$count}){/if}</b>
    {else}
        <a href="/touchline/search" style="margin-left: 10px;">Search{if isset($count) && !empty($count) && $page == 'search'} ({$count}){/if}</a>
    {/if}
    {if $page == 'archive'}
        <b style="margin-left: 10px;">Archive{if !empty($count) && $page == 'archive'} ({$count}){/if}</b>
    {else}
        <a href="/touchline/archive" style="margin-left: 10px;">Archive{if !empty($count) && $page == 'archive'} ({$count}){/if}</a>
    {/if}
    {if $page == 'today'}
        <b style="margin-left: 10px;">Today</b>
    {else}
        <a href="/touchline" style="margin-left: 10px;">Today</a>
    {/if}
