<div class="row">
    <div class="col-md-9  sidebar column-side">  

        <div class="row">
            <div class='col-md-4 border' ><!-- left row -->
                <p><strong>Keyword</strong></p>
                <p><input placeholder="type free text" type="text"  class="form-control find-parametr" {if isset($keyword) && !empty($keyword)} value="{$keyword|escape:'html'}" {/if} style="width: 100%;" name="form[keyword]"></p>
                <hr>
                <p><strong>Status</strong></p>
                <p>
                    <select name="form[status]" class="max chosen-select">
                        <option value="">All</option>
                        {if isset($data.status_list) && !empty($data.status_list)}
                            {foreach from=$data.status_list item=row}
                                <option value="{$row|escape:'html'}"{if isset($form.status) && $form.status == $row} selected="selected"{/if}>{$row|escape:'html'}</option>
                            {/foreach}	
                        {/if}
                    </select> 
                </p>
                <hr>
                <p><strong>Market</strong></p>
                <p>
                    <select name="form[market_id]" class="max chosen-select">
                        <option value="0">All</option>
                        {if isset($data.markets) && !empty($data.markets)}
                            {foreach from=$data.markets item=row}
                                {if isset($row.market.id)}
                                    <option value="{$row.market.id}"{if isset($form.market_id) && $form.market_id == $row.market.id} selected="selected"{/if}>{$row.market.title|escape:'html'}</option>
                                {/if}
                            {/foreach}
                        {/if}
                    </select>
                </p>



            </div>
            <div class="col-md-4 border">
                <p><strong>Objective</strong></p>

                <p>
                    <select name="form[objective_id]" class="max chosen-select">
                        <option value="0"{if !isset($form.objective_id) || empty($form.objective_id)} selected="selected"{/if}>All</option>
                        {if isset($data.objectives) && !empty($data.objectives)}
                            {foreach from=$data.objectives item=row}{if isset($row.objective)}
                                    <option value="{$row.objective.id}"{if isset($form.objective_id) && $form.objective_id == $row.objective.id} selected="selected"{/if}{if isset($row.objective.expired) && false} style="color: #999; font-style: italic;"{/if}>{$row.objective.title|escape:'html'}</option>
                            {/if}{/foreach}
                        {/if}
                    </select>  
                </p>
                <hr>
                <p><strong>Team</strong></p>
                <p>
                    <select name="form[team_id]" class="max chosen-select" onchange="bind_products(this.value, true);">
                        <option value="0">All</option>
                        {if isset($data.teams) && !empty($data.teams)}
                            {foreach from=$data.teams item=row}
                                <option value="{$row.team.id}"{if isset($form.team_id) && $form.team_id == $row.team.id} selected="selected"{/if}>{$row.team.title|escape:'html'}</option>
                            {/foreach}
                        {/if}
                    </select>
                </p>
                <hr>

                <p><strong>Product</strong></p>
                <p>
                    <select name="form[product_id]" id="products" class="max chosen-select">
                        {if $products['0'].product.id < 1}
                            <option value="0">First select team</option>
                        {else}
                            {foreach from=$products item=row}
                                <option value="{$row.product.id}"{if isset($form.product_id) && $form.product_id == $row.product.id} selected="selected"{/if}>{$row.product.title_list}</option>
                            {/foreach}                                    
                        {/if}
                    </select>   
                </p>
                
                

                <!-- right row -->

            </div>
            <div class="col-md-4 border">

                <div class="row">
                    <div class="col-md-12">
                        <p><strong>User</strong></p>
                        <p>
                            <select name="form[driver_id]" class="max chosen-select">
                                <option value="0">All</option>
                                {if isset($data.users) && !empty($data.users)}
                                    {foreach from=$data.users item=row}
                                        <option value="{$row.user.id}"{if isset($form.driver_id) && $form.driver_id == $row.user.id} selected="selected"{/if}>{$row.user.login|escape:'html'}{if !empty($row.user.nickname)} ({$row.user.nickname}){/if}</option>
                                    {/foreach}
                                {/if}	
                            </select>
                        </p>  
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-md-12">
                        <p><strong>Company & Role</strong></p>
                        <div class="form  company-role">                            
                            {if isset($form.company) && !empty($form.company)}
                                {$index = 0}                               
                                <ul>
                                    {foreach from=$form.company item=row name=company_filter}
                                        <li>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <input class="max company-role-input supinv_company" type="text" name="form[company_title][]" onKeyDown="company_list($(this));" {if !empty($row.company.title)} value="{$row.company.title|escape:'html'}"{/if}>
                                                    <input class="supinv_company_id" type="hidden" name="form[company_id][]"{if isset($row.company.id)} value="{$row.company.id}"{/if}>        
                                                </div>
                                                <div class="col-md-6">
                                                    <select  class="biz-co-role max" name="form[role][]">
                                                        <option value="">--</option>
                                                        <option value="buyer" {if isset($form.role.{$index}) && $form.role.{$index}=="buyer"}selected="selected"{/if}>Buyer</option>
                                                        <option value="competitor" {if isset($form.role.{$index}) && $form.role.{$index}=="competitor"}selected="selected"{/if}>Competitor</option>
                                                        <option value="pbuyer" {if isset($form.role.{$index}) && $form.role.{$index}=="pbuyer"}selected="selected"{/if}>Potential Buyer</option>
                                                        <option value="pproducer" {if isset($form.role.{$index}) && $form.role.{$index} == "pproducer"}selected="selected"{/if}>Potential Producer</option>
                                                        <option value="producer" {if isset($form.role.{$index}) && $form.role.{$index} == "producer"}selected="selected"{/if}>Producer</option>
                                                        <option value="seller" {if isset($form.role.{$index}) && $form.role.{$index} == "seller"}selected="selected"{/if}>Seller</option>
                                                        <option value="transport" {if isset($form.role.{$index}) && $form.role.{$index} == "transport"}selected="selected"{/if}>Transport</option>
                                                        <option value="user" {if isset($form.role.{$index}) && $form.role.{$index} == "user"}selected="selected"{/if}>User</option>
                                                    </select>
                                                    {if $smarty.foreach.company_filter.last && $index != 5}
                                                        <span class="icon add company-role-add">&nbsp</span>
                                                    {else}
                                                      
                                                        <span class="icon delete company-role-remove">&nbsp</span>
                                                    {/if}
                                                   
                                                </div>
                                            </div>
                                        </li>
                                        {$index = $index + 1}
                                    {/foreach}
                                </ul>
                            {else}
                                <div class="row">
                                    <div class="col-md-6">                               
                                        <input class="max company-role-input supinv_company " type="text" name="form[company_title][]" onKeyDown="company_list($(this));">
                                        <input class="supinv_company_id " type="hidden" name="form[company_id][]">                                                  
                                    </div>
                                    <div class="col-md-4">      
                                        <select  class="max biz-co-role" name="form[role][]">
                                            <option value="">--</option>
                                            <option value="buyer">Buyer</option>
                                            <option value="competitor">Competitor</option>
                                            <option value="pbuyer">Potential Buyer</option>
                                            <option value="pproducer">Potential Producer</option>
                                            <option value="producer">Producer</option>
                                            <option value="seller">Seller</option>
                                            <option value="transport">Transport</option>
                                            <option value="user">User</option>
                                        </select>
                                    </div>
                                    <div class="col-md-1">
                                        <span class="icon add company-role-add">&nbsp</span>         
                                     </div>    
                                </div>
                            {/if}                                            
                        </div>                      
                        
                       </div>
                </div>
                        <br>
            </div>
        </div>
    </div>
</div>


<div class="pad1"></div>
<hr style="width: 100%; color: #dedede;" size="1"/>
<div class="pad1"></div>

{if !empty($list)}
    <ol class="sr-items  search-target">
        {foreach from=$list item=row name="list"}
            <li class="sr-item">
                <div class="sr-item-no">{($page_no - 1) * $smarty.const.ITEMS_PER_PAGE + $smarty.foreach.list.index + 1}</div>
                <div class="sr-item-pic">{* <img src="/img/layout/anonym.png"> *}&nbsp;</div>
                <div class="sr-item-data">
                    <div class="sr-item-title">
                        <h2><a href="/biz/{$row.biz.id}">{$row.biz.doc_no} | {$row.biz.title}</a></h2>                
                    </div>
                    <div class="sr-item-text">
                        {if isset($row.biz.team)}<b>Team : </b>{$row.biz.team.title|escape:'html'}&nbsp;&nbsp;&nbsp;{/if}
                        {if isset($row.biz.product)}<b>Product : </b>{$row.biz.product.title|escape:'html'}&nbsp;&nbsp;&nbsp;{/if}
                        {if isset($row.biz.objective)}<b>Objective : </b>{$row.biz.objective.title|escape:'html'}&nbsp;&nbsp;&nbsp;{/if}
                        {if isset($row.biz.status_title)}<b>Status : </b>{$row.biz.status_title|escape:'html'}&nbsp;&nbsp;&nbsp;{/if}
                        {if isset($row.biz.team) || isset($row.biz.product) || isset($row.biz.objective) || isset($row.biz.status_title)}<br>{/if}
                        {if isset($row.biz.driver)}<b>Driver : </b>{$row.biz.driver.full_login|escape:'html'}&nbsp;&nbsp;&nbsp;{/if}
                    {if !empty($row.biz.modified_at)}<b>Modified : </b>{$row.biz.modified_at|date_human}{if isset($row.biz.modifier)} by {$row.biz.modifier.full_login|escape:'html'}{/if}{/if}            
                </div>
                <div style="margin-top: 5px;">
                    <a class="glyphicon glyphicon-comment" href="/biz/{$row.biz.id}/blog" >Blog</a>
                    <a class="glyphicon glyphicon-pencil" href="/biz/{$row.biz.id}/edit">Edit</a>
                    {if $row.biz.parent_id == 0}<a class="glyphicon glyphicon-plus" href="/biz/{$row.biz.id}/addsubbiz" >Add subbiz</a>{/if}
                </div>
                {if isset($row.biz.orders) && !empty($row.biz.orders)}
                    <div class="pad1"></div>
                    {foreach from=$row.biz.orders item=order}
                        {if $order.order.status == "nw"}
                            <div class="order-status-{$order.order.status} biz-order-href" data-href="/order/{$order.order.id}">
                                INPO{$order.order.id}, {$order.order.company.doc_no}, {if !empty($order.order.quick.weight)}{$order.order.quick.weight|string_format:'%.2f'} {$order.order.weight_unit}, {/if}{$order.order.status_title}
                            </div>
                        {/if}
                    {/foreach}
                    {foreach from=$row.biz.orders item=order}
                        {if $order.order.status == "ip"}
                            <div class="order-status-{$order.order.status} biz-order-href" data-href="/order/{$order.order.id}">
                                INPO{$order.order.id}, {$order.order.company.doc_no}, {if !empty($order.order.quick.weight)}{$order.order.quick.weight|string_format:'%.2f'} {$order.order.weight_unit}, {/if}{$order.order.status_title}
                            </div>
                        {/if}
                    {/foreach}
                    {foreach from=$row.biz.orders item=order}
                        {if $order.order.status == "de"}
                            <div class="order-status-{$order.order.status} biz-order-href" data-href="/order/{$order.order.id}">
                                INPO{$order.order.id}, {$order.order.company.doc_no}, {if !empty($order.order.quick.weight)}{$order.order.quick.weight|string_format:'%.2f'} {$order.order.weight_unit}, {/if}{$order.order.status_title}
                            </div>
                        {/if}
                    {/foreach}	
                {/if}	
            </div>
            <div class="separator"></div>
        </li>    
    {/foreach}
</ol>
{elseif isset($params) || !empty($keyword)}
    Nothing was found.
{elseif isset($favourite_bizes) && !empty($favourite_bizes)}
    <h2 style="margin: 0 0 20px;">My Favourite BIZs</h2>
    <ol class="sr-items  search-target">
        {foreach from=$favourite_bizes item=row name="list"}
            <li class="sr-item">
                <div class="sr-item-no">{($page_no - 1) * $smarty.const.ITEMS_PER_PAGE + $smarty.foreach.list.index + 1}</div>
                <div class="sr-item-pic">{* <img src="/img/layout/anonym.png"> *}&nbsp;</div>
                <div class="sr-item-data">
                    <div class="sr-item-title">
                        <h2><a href="/biz/{$row.biz.id}">{$row.biz.doc_no} | {$row.biz.title}</a></h2>                
                    </div>
                    <div class="sr-item-text">
                        {if isset($row.biz.team)}<b>Team : </b>{$row.biz.team.title|escape:'html'}&nbsp;&nbsp;&nbsp;{/if}
                        {if isset($row.biz.product)}<b>Product : </b>{$row.biz.product.title|escape:'html'}&nbsp;&nbsp;&nbsp;{/if}
                        {if isset($row.biz.objective)}<b>Objective : </b>{$row.biz.objective.title|escape:'html'}&nbsp;&nbsp;&nbsp;{/if}
                        {if isset($row.biz.status_title)}<b>Status : </b>{$row.biz.status_title|escape:'html'}&nbsp;&nbsp;&nbsp;{/if}
                        {if isset($row.biz.team) || isset($row.biz.product) || isset($row.biz.objective) || isset($row.biz.status_title)}<br>{/if}
                        {if isset($row.biz.driver)}<b>Driver : </b>{$row.biz.driver.full_login|escape:'html'}&nbsp;&nbsp;&nbsp;{/if}
                    {if !empty($row.biz.modified_at)}<b>Modified : </b>{$row.biz.modified_at|date_human}{if isset($row.biz.modifier)} by {$row.biz.modifier.full_login|escape:'html'}{/if}{/if}            
                </div>
                <div style="margin-top: 5px;">
                    <a class="blog-gray" href="/biz/{$row.biz.id}/blog" style="margin-right: 10px;">blog</a>
                    <a class="edit" href="/biz/{$row.biz.id}/edit" style="margin-right: 10px; color: #777;">edit</a>
                    {if $row.biz.parent_id == 0}<a class="add" href="/biz/{$row.biz.id}/addsubbiz" style="color: #777;">add subbiz</a>{/if}
                </div>
                {if isset($row.biz.orders) && !empty($row.biz.orders)}
                    <div class="pad1"></div>
                    {foreach from=$row.biz.orders item=order}
                        {if $order.order.status == "nw"}
                            <div class="order-status-{$order.order.status} biz-order-href" data-href="/order/{$order.order.id}">
                                INPO{$order.order.id}, {$order.order.company.doc_no}, {if !empty($order.order.quick.weight)}{$order.order.quick.weight|string_format:'%.2f'} {$order.order.weight_unit}, {/if}{$order.order.status_title}
                            </div>
                        {/if}
                    {/foreach}
                    {foreach from=$row.biz.orders item=order}
                        {if $order.order.status == "ip"}
                            <div class="order-status-{$order.order.status} biz-order-href" data-href="/order/{$order.order.id}">
                                INPO{$order.order.id}, {$order.order.company.doc_no}, {if !empty($order.order.quick.weight)}{$order.order.quick.weight|string_format:'%.2f'} {$order.order.weight_unit}, {/if}{$order.order.status_title}
                            </div>
                        {/if}
                    {/foreach}
                    {foreach from=$row.biz.orders item=order}
                        {if $order.order.status == "de"}
                            <div class="order-status-{$order.order.status} biz-order-href" data-href="/order/{$order.order.id}">
                                INPO{$order.order.id}, {$order.order.company.doc_no}, {if !empty($order.order.quick.weight)}{$order.order.quick.weight|string_format:'%.2f'} {$order.order.weight_unit}, {/if}{$order.order.status_title}
                            </div>
                        {/if}
                    {/foreach}	
                {/if}	
            </div>
            <div class="separator"></div>
        </li>    
    {/foreach}
</ol>
{else}
    Please specify search criteria.
{/if}    


