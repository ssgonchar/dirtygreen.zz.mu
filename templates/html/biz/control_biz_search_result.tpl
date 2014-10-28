<hr>
<b>Search results</b>
{if !empty($list)}
    <p><a class="find-biz-check-all">Check all</a> | <a class="find-biz-uncheck-all">Uncheck all</a></p>    
    <table class="table table-striped">
        <thead>
            <tr>
                <td></td>
                <td>#</td>
                <td>Title</td>
                <td>Team</td>
                <td>Product</td>
                <td>Objective</td>
                <td>Status</td>
                <td>Driver</td>
                <td>Modfied</td>
            </tr>
            
        </thead>
        <tbody>
                {foreach from=$list item=row name="list"}
        <tr>
            <td><input type="checkbox" data-biz-id="{$row.biz.id}" class="find-biz-check"></td>
            <td>{$row.biz.doc_no}</td>
            <td>{$row.biz.title}</td>
            <td>{$row.biz.team.title|escape:'html'}</td>
            <td>{$row.biz.product.title|escape:'html'}</td>
            <td>{$row.biz.objective.title|escape:'html'}</td>
            <td>{$row.biz.status_title|escape:'html'}</td>
            <td>{$row.biz.driver.full_login|escape:'html'}</td>
            <td>{if !empty($row.biz.modified_at)}{$row.biz.modified_at|date_human}{if isset($row.biz.modifier)} by {$row.biz.modifier.full_login|escape:'html'}{/if}{/if}</td>
        </tr>  
{/foreach}
        </tbody>
    </table>
{else} 
    <i>Nothing found.</i>
{/if}