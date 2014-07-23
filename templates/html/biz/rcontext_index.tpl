<div class="row">
    <div class="col-md-12 col-lg-12">  
        <div class="panel panel-default">
            <div class="panel-heading">
                <a data-toggle="collapse" data-parent="#accordion" href="#collapseFilterSettings" style='display: inline-block;'>
                    <h3 class="panel-title">
                        Select
                    </h3>
                </a>
                <!--<input type="submit" name="btn_setfilter" value="Find" class="btn100b" style='margin-left: 620px;'>-->
            </div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-10">
                        <p>Keyword</p>
                        <p><input placeholder="type free text" type="text" class="form-control" {if isset($keyword) && !empty($keyword)} value="{$keyword|escape:'html'}" {/if} style="width: 100%;" name="form[keyword]"></p>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-10"><!-- left row -->
                        <p>Objective</p>

                        <p>
                            <select name="form[objective_id]" class="max chosen-select">
                                <option value="0"{if !isset($form.objective_id) || empty($form.objective_id)} selected="selected"{/if}>--</option>
                                {if isset($data.objectives) && !empty($data.objectives)}
                                    {foreach from=$data.objectives item=row}{if isset($row.objective)}
                                            <option value="{$row.objective.id}"{if isset($form.objective_id) && $form.objective_id == $row.objective.id} selected="selected"{/if}{if isset($row.objective.expired) && false} style="color: #999; font-style: italic;"{/if}>{$row.objective.title|escape:'html'}</option>
                                    {/if}{/foreach}
                                {/if}
                            </select>  
                        </p>
                        <hr>
                        <p>Team</p>
                        <p>
                            <select name="form[team_id]" class="max chosen-select" onchange="bind_products(this.value, true);">
                                <option value="0">--</option>
                                {if isset($data.teams) && !empty($data.teams)}
                                    {foreach from=$data.teams item=row}
                                        <option value="{$row.team.id}"{if isset($form.team_id) && $form.team_id == $row.team.id} selected="selected"{/if}>{$row.team.title|escape:'html'}</option>
                                    {/foreach}
                                {/if}
                            </select>
                        </p>
                        <hr>
                        <p>Product</p>
                        <p>
                            <select name="form[product_id]" id="products" class="max chosen-select">
                                {if $products['0'].product.id < 1}
                                    <option value="0">--</option>
                                {else}
                                    {foreach from=$products item=row}
                                        <option value="{$row.product.id}"{if isset($form.product_id) && $form.product_id == $row.product.id} selected="selected"{/if}>{$row.product.title_list}</option>
                                    {/foreach}                                    
                                {/if}
                            </select>   
                        </p>
                    </div>
                    <div class="col-md-10"><!-- right row -->
                        <p>Status</p>
                        <p>
                            <select name="form[status]" class="max chosen-select">
                                <option value="">--</option>
                                {if isset($data.status_list) && !empty($data.status_list)}
                                    {foreach from=$data.status_list item=row}
                                        <option value="{$row|escape:'html'}"{if isset($form.status) && $form.status == $row} selected="selected"{/if}>{$row|escape:'html'}</option>
                                    {/foreach}	
                                {/if}
                            </select> 
                        </p>
                        <hr>
                        <p>Market :</p>
                        <p>
                            <select name="form[market_id]" class="max chosen-select">
                                <option value="0">--</option>
                                {if isset($data.markets) && !empty($data.markets)}
                                    {foreach from=$data.markets item=row}
                                        {if isset($row.market.id)}
                                            <option value="{$row.market.id}"{if isset($form.market_id) && $form.market_id == $row.market.id} selected="selected"{/if}>{$row.market.title|escape:'html'}</option>
                                        {/if}
                                    {/foreach}
                                {/if}
                            </select>
                        </p>
                        <hr>
                        <p>User :</p>
                        <p>
                            <select name="form[driver_id]" class="max chosen-select">
                                <option value="0">--</option>
                                {if isset($data.users) && !empty($data.users)}
                                    {foreach from=$data.users item=row}
                                        <option value="{$row.user.id}"{if isset($form.driver_id) && $form.driver_id == $row.user.id} selected="selected"{/if}>{$row.user.login|escape:'html'}{if !empty($row.user.nickname)} ({$row.user.nickname}){/if}</option>
                                    {/foreach}
                                {/if}	
                            </select>
                        </p>
                        <hr>
                        <p>Company & Role</p>
                        {if isset($form.company) && !empty($form.company)}
                            <div class="form  company-role">
                                {$index = 0}
                                <ul>
                                    {foreach from=$form.company item=row name=company_filter}
                                        <li>
                                            <input class="max company-role-input supinv_company" type="text" name="form[company_title][]" onKeyDown="company_list($(this));" {if !empty($row.company.title)} value="{$row.company.title|escape:'html'}"{/if}>
                                            <input class="supinv_company_id" type="hidden" name="form[company_id][]"{if isset($row.company.id)} value="{$row.company.id}"{/if}>        
                                            <select class="biz-co-role max" name="form[role][]">
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
                                        </li>
                                        {$index = $index + 1}
                                    {/foreach}
                                </ul>
                            </div>
                        {else}
                            <div class="form  company-role">
                                <ul>
                                    <li>
                                        <input class="max company-role-input supinv_company" type="text" name="form[company_title][]" onKeyDown="company_list($(this));">
                                        <input class="supinv_company_id" type="hidden" name="form[company_id][]">                                                  
                                        <select class="biz-co-role max" name="form[role][]">
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
                                        <span class="icon add company-role-add">&nbsp</span>                                                                
                                    </li>
                                </ul>
                            </div>
                        {/if}	
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>