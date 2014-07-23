<div class="row">
    <div class="col-md-12">
        {if isset($parent_biz)}
            <p><a href="/biz/{$parent_biz.id}" class="btn btn-link" style="padding-left: 0px;">Parent: {$parent_biz.doc_no_full}</a></p>
        {/if}          
        <p>          
            {if isset($form.status_title)}<span class="label label-primary" style="font-size: 14px;">{$form.status_title}</span>{else}<span class="label label-default" style="font-size: 14px;">Status not set</span>{/if}
                {if isset($form.quick) && isset($form.quick.is_favourite) && $form.quick.is_favourite > 0}
                <a title=" My favourite BIZ"><i class="glyphicon glyphicon-star" style="cursor: pointer; font-size: 14px; color: #ffc534;"></i></a>
            {/if}
        </p>
    </div>
</div>
<div class="row">
    <div class="col-md-6">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th colspan="4"><h4>Biz settings</h4></th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="text-top"><b>Objective</b></td>
                    <td>{if isset($form.objective)}{$form.objective.title}{else}<i>not set</i>{/if}</td>
                    <td class="text-top"><b>Market</b></td>
                    <td>{if isset($form.market)}{$form.market.title}{else}<i>not set</i>{/if}</td>                                
                </tr>
                <tr>
                    <td class="text-top"><b>Team</b></td>
                    <td>{if isset($form.team)}{$form.team.title}{else}<i>not set</i>{/if}</td>
                    <td class="text-top"><b>Driver</b></td>
                    <td>{if isset($form.driver)}{$form.driver.full_login}{else}<i>not set</i>{/if}</td>                    
                </tr>
                <tr>
                    <td class="text-top"><b>Product</b></td>
                    <td>{if isset($form.product)}{$form.product.title}{else}<i>not set</i>{/if}</td>  
                    <td class="text-top"><b>Navigators</b></td>
                    <td class="text-top">
                        {if !empty($navigators)}
                            {foreach name='navigators' from=$navigators item=row}
                                <p>{$row.user.full_login}</p>
                            {/foreach}
                        {else}
                            {''|undef}
                        {/if}
                    </td>                    
                </tr>
            </tbody>
        </table>   
        <hr>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th colspan="2"><h4>Companies & roles</h4></th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="text-top">
                        <b>Producer</b>
                    </td>
                    <td class="text-top">
                        {include file='templates/html/biz/control_company_list_view.tpl' rowset=$producers}
                    </td>                           
                </tr>
                <tr>
                    <td class="text-top">
                        <b>Potential&nbsp;Producer</b>
                    </td>
                    <td class="text-top">
                        {include file='templates/html/biz/control_company_list_view.tpl' rowset=$pproducers}
                    </td>
                </tr>
                <tr>
                    <td class="text-top">
                        <b>Buyer</b>
                    </td>
                    <td class="text-top">
                        {include file='templates/html/biz/control_company_list_view.tpl' rowset=$buyers}
                    </td>
                </tr>     
                <tr>
                    <td class="text-top">
                        <b>Potential&nbsp;Buyer</b>
                    </td>
                    <td class="text-top">
                        {include file='templates/html/biz/control_company_list_view.tpl' rowset=$pbuyers}
                    </td>
                </tr>                  
                <tr>
                    <td class=" text-top">
                        <b>Seller</b>
                    </td>
                    <td class="text-top">
                        {include file='templates/html/biz/control_company_list_view.tpl' rowset=$sellers}
                    </td>
                </tr>                  
                <tr>
                    <td class="text-top">
                        <b>User</b>
                    </td>
                    <td class="text-top">
                        {include file='templates/html/biz/control_company_list_view.tpl' rowset=$users}
                    </td>
                </tr>
                <tr>
                    <td class="text-top">
                        <b>Competitor</b>
                    </td>
                    <td class="text-top">
                        {include file='templates/html/biz/control_company_list_view.tpl' rowset=$competitors}
                    </td>
                </tr>
                <tr>
                    <td class="text-top">
                        <b>Transport</b>
                    </td>
                    <td class="text-top">
                        {include file='templates/html/biz/control_company_list_view.tpl' rowset=$transports}
                    </td>
                </tr>
            </tbody>
        </table>                      
    </div>
    <div class="col-md-6">
        {if !empty($form.description)}
            <div class="bs-callout bs-callout-info">
                {$form.description}            
            </div>
            <hr>
        {/if}  
        <p>
            {include file='templates/controls/object_shared_files.tpl' object_alias='biz' object_id=$form.id}
        </p>
    </div>
</div>