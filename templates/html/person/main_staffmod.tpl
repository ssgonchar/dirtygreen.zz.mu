
{if !empty($list)}
<ol class="sr-items search-target">
    {foreach from=$list item=row name="list"}
    <li class="sr-item">
        <div class="sr-item-no">{($page_no - 1) * $smarty.const.ITEMS_PER_PAGE + $smarty.foreach.list.index + 1}</div>
        <div class="sr-item-pic">                    
            <a href="/person/{$row.user.person.id}">
                {if isset($row.user) && isset($row.user.person) && isset($row.user.person.picture)}
                {picture type="person" size="x" source=$row.user.person.picture}
                {else}
                <img alt="" src="/img/layout/anonym{if $row.user.person.gender == 'f'}f{/if}.png">
                {/if}
            </a>
        </div>
        <div class="sr-item-data">
            <div class="sr-item-title">
                <h2><a href="/person/{$row.user.person.id}">{$row.user.person.full_name}</a></h2>
            </div>
            <div class="sr-item-text">
                {if isset($row.user.person.company)}<b>Office : </b><a href="/company/{$row.user.person.company.id}">{$row.user.person.company.title}</a>&nbsp;&nbsp;&nbsp;{/if}
                {if isset($row.user.person.department)}<b>Department : </b>{$row.user.person.department.title|escape:'html'}&nbsp;&nbsp;&nbsp;{/if}
                {if isset($row.user.person.jobposition)}<b>Position : </b>{$row.user.person.jobposition.title|escape:'html'}&nbsp;&nbsp;&nbsp;{/if}
                {if isset($row.user.person.company) || isset($row.user.person.department) || isset($row.user.person.jobposition)}&nbsp;&nbsp;&nbsp;{/if}
                <b>Role : </b>
                    {if $row.user.role_id == $smarty.const.ROLE_STAFF}Staff
                    {elseif $row.user.role_id == $smarty.const.ROLE_MODERATOR}Moderator
                    {elseif $row.user.role_id == $smarty.const.ROLE_ADMIN}Admin{/if}                
            </div>    
        </div>
        <div class="separator"></div>
    </li>    
    {/foreach}
</ol>
    
{elseif isset($filter)}
    Nothing was found on my request
{/if}
