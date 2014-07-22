    <li>
	 {if $page == 'pending'}
        <a><b>MustDo!{if isset($count) && !empty($count) && $page == 'pending'} <span class="badge">{$count}</span>{/if}</b></a>
    {else}
        <a href="/touchline/mustdo"><b>MustDO!</b>{if isset($count) && !empty($count) && $page == 'pending'} <span class="badge">{$count}</span>{/if}</a>
    {/if}
	 </li>
	 <li>
    {if $page == 'search'}
        <a><b style="margin-left: 10px;">Search{if isset($count) && !empty($count) && $page == 'search'} ({$count}){/if}</b></a>
    {else}
        <a href="/touchline/search" style="margin-left: 10px;">Search{if isset($count) && !empty($count) && $page == 'search'} <span class="badge">{$count}</span>{/if}</a>
    {/if}
	 </li>
	 <li>
    {if $page == 'archive'}
        <a><b style="margin-left: 10px;">Archive{if !empty($count) && $page == 'archive'} <span class="badge">{$count}</span>{/if}</b></a>
    {else}
        <a href="/touchline/archive" style="margin-left: 10px;">Archive{if !empty($count) && $page == 'archive'} <span class="badge">{$count}</span>{/if}</a>
    {/if}
	 </li>
	 <li>
    {if $page == 'today'}
        <a><b style="margin-left: 10px;">Today</b></a>
    {else}
        <a href="/touchline" style="margin-left: 10px;">Today</a>
    {/if}
	</li>