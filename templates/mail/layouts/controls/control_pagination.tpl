<div class="pagination">
{*
    <a href="#"><span class="first">предыдушая</span></a>
    <a href="#"><span class="next">следующая</span></a>
    <br/>
*}    
    <ul>
        <li><span class="text-black">Страницы:</span></li>
        {if !empty($pages) && !empty($pages.first)}
            <li><span class="text-blue"><a href="{$pager_path}/~1" title="">1</a></span></li>
        {/if}

        {if !empty($pages) && !empty($pages.prevs)}
            <li><span class="text-blue"><a class="pager" href="{$pager_path}/~{$pager_pages.prevs}">...</a></span></li>
        {/if}

        {if !empty($pages) && !empty($pages.pages)}
        {section name=i loop=$pages.pages}
            {if $pages.pages[i].active}
                <li><span class="text-black">{$pager_pages.pages[i].number}</span></li>
            {else}
                <li><span class="text-blue"><a href="{$pager_path}/~{$pager_pages.pages[i].number}">{$pager_pages.pages[i].number}</a></span></li>
            {/if}
        {/section}
        {/if}

        {if !empty($pages) && !empty($pages.nexts)}
            <li><span class="text-blue"><a class="pager" href="{$pager_path}/~{$pager_pages.nexts}">...</a></span></li>
        {/if}

        {if !empty($pages) && !empty($pages.last)}
            <li><span class="text-blue"><a href="{$pager_path}/~{$pager_pages.last}" title="">{$pager_pages.last}</a></span></li>
        {/if}
{*

        <li><span class="text-black">1</span></li>  
        <li><span class="text-blue"><a href="#">2</a></span></li>

        <li><span class="text-blue"><a href="#">3</a></span></li>
        <li><span class="text-blue"><a href="#">4</a></span></li>
        <li><span class="text-blue"><a href="#">5</a></span></li>
        <li><span class="text-blue"><a href="#">последняя</a></span></li>
*}        
    </ul>
</div>
<div class="separator"></div>

{*
            <div id="pager">
                <ul>
                    <li class="inscription">Страница:&nbsp;</li>

                {if !empty($pages) && !empty($pages.first)}
                    <li><a href="{$pager_path}/~1" title="">1</a></li>
                {/if}

                {if !empty($pages) && !empty($pages.prevs)}
                    <li><a class="pager" href="{$pager_path}/~{$pager_pages.prevs}">...</a></li>
                {/if}

                {if !empty($pages) && !empty($pages.pages)}
                {section name=i loop=$pages.pages}
                    {if $pages.pages[i].active}
                        <li class="active">{$pager_pages.pages[i].number}</li>
                    {else}
                        <li><a href="{$pager_path}/~{$pager_pages.pages[i].number}">{$pager_pages.pages[i].number}</a></li>
                    {/if}
                {/section}
                {/if}

                {if !empty($pages) && !empty($pages.nexts)}
                    <li><a class="pager" href="{$pager_path}/~{$pager_pages.nexts}">...</a></li>
                {/if}

                {if !empty($pages) && !empty($pages.last)}
                    <li><a href="{$pager_path}/~{$pager_pages.last}" title="">{$pager_pages.last}</a></li>
                {/if}

                </ul>

            </div>
*}            