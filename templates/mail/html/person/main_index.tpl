<table class="form" width="50%">
    <tr>
        <td><input type="text" name="form[keyword]" class="max"{if isset($keyword)} value="{$keyword|escape:'html'}"{/if}></td>
        <td><input type="submit" name="btn_select" value="Find" class="btn100o"></td>
    </tr>
</table>

<div class="pad1"></div>
<hr style="width: 100%; color: #dedede;" size="1"/>
<div class="pad1"></div>

{if !empty($list)}
<ol class="sr-items">
    {foreach from=$list item=row name="list"}
    <li class="sr-item">
        <div class="sr-item-no">{($page_no - 1) * $smarty.const.ITEMS_PER_PAGE + $smarty.foreach.list.index + 1}</div>
        <div class="sr-item-pic">                    
            <a href="/person/{$row.person.id}">
                {if isset($row.person.picture)}
                {picture type="person" size="x" source=$row.person.picture}
                {else}
                <img alt="" src="/img/layout/anonym{if $row.person.gender == 'f'}f{/if}.png">
                {/if}
            </a>
        </div>
        <div class="sr-item-data">
            <div class="sr-item-title">
                <h2><a href="/person/{$row.person.id}">{$row.person.full_name}</a></h2>
            </div>
            <div class="sr-item-text">
                {if isset($row.person.company)}<b>Company : </b><a href="/company/{$row.person.company.id}">{$row.person.company.title}</a>&nbsp;&nbsp;&nbsp;{/if}
                {if isset($row.person.department)}<b>Department : </b>{$row.person.department.title|escape:'html'}&nbsp;&nbsp;&nbsp;{/if}
                {if isset($row.person.jobposition)}<b>Position : </b>{$row.person.jobposition.title|escape:'html'}&nbsp;&nbsp;&nbsp;{/if}
                {if isset($row.person.company) || isset($row.person.department) || isset($row.person.jobposition)}<br>{/if}
                {if isset($row.personcontacts)}<b>Contacts : </b>
                    {foreach name='contacts' from=$row.personcontacts item=contact}
                        {if $contact.type == 'email'}
                            {if isset($row.person.company)}
                            <a href="/email/compose/company:{$row.person.company.id};recipient:{$contact.title|escape:'html'}">{$contact.title|escape:'html'}</a>
                            {else}
                            <a href="/email/compose/recipient:{$contact.title|escape:'html'}">{$contact.title|escape:'html'}</a>
                            {/if}
                        {elseif $contact.type == 'www'}<a href="{$contact.title|escape:'html'}">{$contact.title|escape:'html'}</a>
                        {elseif $contact.type == 'skype'}<span class="skype">{$contact.title|escape:'html'}</span>
                        {elseif $contact.type == 'phone' || $contact.type == 'cell'}<span class="phone">{$contact.title|escape:'html'}</span>
                        {elseif $contact.type == 'fax'}<span class="fax">{$contact.title|escape:'html'}</span>
                        {else}{$contact.title|escape:'html'}
                        {/if}{if !$smarty.foreach.contacts.last} {/if}                    
                    {/foreach}                    
                {/if}                
            </div>    
        </div>
        <div class="separator"></div>
    </li>    
    {/foreach}
</ol>
    
{*    
    <table class="list" width="100%">
        <tbody>
            {foreach from=$list item=row}
            <tr>
                <td><a href="/person/{$row.person.id}">{$row.person.id}</a></td>
                <td>
                </td>
                <td></td>
                <td>
                </td>
                <td>
                </td>
                <td>
                </td>
                <td>
                    {if isset($row.person.country)}
                        {$row.person.country.title|escape:'html'}
                    {else}
                        <i>not set</i>
                    {/if}                
                </td>
                <td>
                    {if isset($row.person.region)}
                        {$row.person.region.title|escape:'html'}
                    {else}
                        <i>not set</i>
                    {/if}                
                </td>
                <td>
                    {if isset($row.person.city)}
                        {$row.person.city.title|escape:'html'}
                    {else}
                        <i>not set</i>
                    {/if}                
                </td>
            </tr>
            {/foreach}
        </tbody>
    </table>    
*}    
{elseif isset($filter)}
    Nothing was found on my request
{/if}
