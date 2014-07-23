{if isset($parent_biz)}
<table width="100%">
    <tr>
        <td>
            <table class="form" width="100%">
                <tr>
                    <td class="form-td-title-b" style="font-size: 14px;">Main BIZ : </td>
                    <td><a href="/biz/{$parent_biz.id}" style="font-size: 14px;">{$parent_biz.doc_no_full}</a></td>
                </tr>
            </table>
        </td>
    </tr>
</table>
<div class="pad-10"></div>
{/if}
<table width="100%">
    <tr>
        <td width="40%" class="text-top">
            <table class="form" width="100%">
                <tr>
                    <td class="form-td-title-b text-top">Objective : </td>
                    <td>{if isset($form.objective)}{$form.objective.title}{else}<i>not set</i>{/if}</td>
                </tr>
                <tr>
                    <td class="form-td-title-b text-top">Description : </td>
                    <td>{if !empty($form.description)}{$form.description|nl2br}{else}<i>not set</i>{/if}</td>
                </tr>
            </table>
        </td>
        <td width="30%" class="text-top">
            <table class="form" width="100%">
                <tr>
                    <td class="form-td-title-b">Team : </td>
                    <td>{if isset($form.team)}{$form.team.title}{else}<i>not set</i>{/if}</td>
                </tr>
                <tr>
                    <td class="form-td-title-b">Product : </td>
                    <td>{if isset($form.product)}{$form.product.title}{else}<i>not set</i>{/if}</td>
                </tr>
                <tr>
                    <td class="form-td-title-b">Status : </td>
                    <td>{if isset($form.status_title)}<span class="biz-{$form.status}">{$form.status_title}</span>{else}<i>not set</i>{/if}</td>
                </tr>
                <tr>
                    <td class="form-td-title-b">Market : </td>
                    <td>{if isset($form.market)}{$form.market.title}{else}<i>not set</i>{/if}</td>
                </tr>                
            </table>
        </td>
        <td width="30%" class="text-top">
            <table class="form" width="100%">
                <tr>
                    <td class="form-td-title-b">Driver : </td>
                    <td>{if isset($form.driver)}{$form.driver.full_login}{else}<i>not set</i>{/if}</td>
                </tr>
                <tr>
                    <td class="form-td-title-b text-top">Navigators : </td>
                    <td class="text-top">
                        {if !empty($navigators)}
                            {foreach name='navigators' from=$navigators item=row}
                            <div style="float: left; width: 110px; margin-bottom: 5px;">{$row.user.full_login}</div>
                            {/foreach}
                        {else}
                            {''|undef}
                        {/if}
                    </td>
                </tr>
                {if isset($form.quick) && isset($form.quick.is_favourite) && $form.quick.is_favourite > 0}
                <tr>
                    <td></td>
                    <td><span style="background-color: #FFF750;">This BIZ is my favourite.</span></td>
                </tr>
                {/if}
            </table>        
        </td>
    </tr>
</table>
<div class="pad2"></div>

<table width="100%">
    <tr>
        <td width="25%" class="text-top">
            <h4>Producer</h4>
            {include file='templates/html/biz/control_company_list_view.tpl' rowset=$producers}
            <div class="pad-10"></div>
            
            <h4>Potential Producer</h4>
            {include file='templates/html/biz/control_company_list_view.tpl' rowset=$pproducers}
        </td>
        <td width="25%" class="text-top">
            <h4>Buyer</h4>
            {include file='templates/html/biz/control_company_list_view.tpl' rowset=$buyers}
            <div class="pad-10"></div>
            
            <h4>Potential Buyer</h4>
            {include file='templates/html/biz/control_company_list_view.tpl' rowset=$pbuyers}
        </td>
        <td width="25%" class="text-top">
            <h4>Seller</h4>
            {include file='templates/html/biz/control_company_list_view.tpl' rowset=$sellers}
            <div class="pad-10"></div>
            
            <h4>User</h4>
            {include file='templates/html/biz/control_company_list_view.tpl' rowset=$users}
        </td>
        <td width="25%" class="text-top">
            <h4>Competitor</h4>
            {include file='templates/html/biz/control_company_list_view.tpl' rowset=$competitors}
            <div class="pad-10"></div>
            
            <h4>Transport</h4>
            {include file='templates/html/biz/control_company_list_view.tpl' rowset=$transports}
        </td>
    </tr>
</table>

<div class="pad"></div>
{include file='templates/controls/object_shared_files.tpl' object_alias='biz' object_id=$form.id}