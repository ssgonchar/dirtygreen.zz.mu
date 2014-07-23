<div class="row">
    <div class="col-md-6">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th colspan="6"><h4>Biz settings</h4></th>
               </tr>                          
            </thead>
            <tbody>
                <tr>
                    <td><b>Title</b></td>
                    <td><input type="text" name="form[title]" class="form-control" style="padding: 0px"{if isset($form.title) && !empty($form.title)} value="{$form.title}"{/if}>
                    </td>
                    
                    <td class="text-top"><b>Objective</b></td>
                    <td><select name="form[objective_id]" class=" normal chosen-select">
                        <option value="0">--</option>
                        {foreach from=$objectives item=row}
                            <option value="{$row.objective.id}"{if isset($form.objective_id) && $form.objective_id == $row.objective.id} selected="selected"{/if}{if isset($row.objective.expired) && false} style="color: #999; font-style: italic;"{/if}>{$row.objective.title}</option>
                        {/foreach}
                    </select></td>
                                                 
                </tr>
                <tr>
                    <td class="text-top"><b>Team</b></td>
                    <td><select name="form[team_id]" class=" normal chosen-select" onchange="bind_products(this.value);">
                        <option value="0">--</option>
                        {foreach from=$teams item=row}
                            <option value="{$row.team.id}"{if isset($form.team_id) && $form.team_id == $row.team.id} selected="selected"{/if}>{$row.team.title}</option>
                        {/foreach}
                    </select></td>
                    <td class="text-top"><b>Driver</b></td>
                    <td><select name="form[driver_id]" class=" normal chosen-select">
                                <option value="0">--</option>
                                {foreach from=$mam_list item=row}
                                    <option value="{$row.user.id}"{if isset($form.driver_id) && $form.driver_id == $row.user.id} selected="selected"{/if}>{$row.user.login}{if !empty($row.user.nickname)} ({$row.user.nickname}){/if}</option>
                                {/foreach}                            
                            </select> </td>                    
                </tr>
                <tr>
                    <td class="text-top"><b>Product</b></td>
                    <td><select name="form[product_id]" id="products" class=" normal chosen-select">
                        <option value="0">First select team</option>
                        {foreach from=$products item=row}
                            <option value="{$row.product.id}"{if isset($form.product_id) && $form.product_id == $row.product.id} selected="selected"{/if}>{$row.product.title_list}</option>
                        {/foreach}
                    </select> </td>  
                   
                    <td class="text-top"><b>Navigator</b></td>
                    <td class="text-top">
                         <select name="form[navigator_id]" class=" chosen-select normal">
                                <option value="0">--</option>
                                {foreach name='navigators' from=$mam_list item=row}

                                    <option value="{$row.user.id}"{if isset($form.driver_id) && $form.driver_id == $row.user.id} selected="selected"{/if}>{$row.user.login}{if !empty($row.user.nickname)} ({$row.user.nickname}){/if}</option>
                                {/foreach}
                            </select>
                    </td>                    
                
                <tr>   <td class="text-top"><b>Market</b></td>
                    <td><select name="form[market_id]" class=" normal chosen-select">
                                <option value="0">--</option>
                                {foreach from=$markets item=row}
                                    <option value="{$row.market.id}"{if isset($form.market_id) && $form.market_id == $row.market.id} selected="selected"{/if}>{$row.market.title}</option>
                                {/foreach}
                            </select> </td>  
              <td class="text-top"><b>Status</b></td>
                    <td class="text-top">
                         <select name="form[status]" class=" normal chosen-select">
                        <option value="">--</option>
                        <option value="idea"{if isset($form.status) && $form.status == 'idea'} selected="selected"{/if}>Idea</option>
                        <option value="marketing"{if isset($form.status) && $form.status == 'marketing'} selected="selected"{/if}>Marketing</option>
                        <option value="negotiation"{if isset($form.status) && $form.status == 'negotiation'} selected="selected"{/if}>Negotiation</option>
                        <option value="admin"{if isset($form.status) && $form.status == 'admin'} selected="selected"{/if}>Contract Administration</option>
                        <option value="closed"{if isset($form.status) && $form.status == 'closed'} selected="selected"{/if}>Contracted & Closed</option>
                        <option value="repeat"{if isset($form.status) && $form.status == 'repeat'} selected="selected"{/if}>Contracted & Repeat Negotiation</option>
                        <option value="suspended"{if isset($form.status) && $form.status == 'suspended'} selected="selected"{/if}>Suspended</option>
                        <option value="abandoned"{if isset($form.status) && $form.status == 'abandoned'} selected="selected"{/if}>Abandoned</option>
                        {*<option value="concluded"{if isset($form.status) && $form.status == 'concluded'} selected="selected"{/if}>Concluded</option>*}
                    </select> 
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
                       {include file='templates/html/biz/control_company_list.tpl' role='producer' rowset=$producers}
                    </td>                           
                </tr>
                <tr>
                    <td class="text-top">
                        <b>Potential&nbsp;Producer</b>
                    </td>
                    <td class="text-top">
                        {include file='templates/html/biz/control_company_list.tpl' role='pproducer' rowset=$pproducers}
                    </td>
                </tr>
                <tr>
                    <td class="text-top">
                        <b>Buyer</b>
                    </td>
                    <td class="text-top">
                        {include file='templates/html/biz/control_company_list.tpl' role='buyer' rowset=$buyers}
                    </td>
                </tr>     
                <tr>
                    <td class="text-top">
                        <b>Potential&nbsp;Buyer</b>
                    </td>
                    <td class="text-top">
                        {include file='templates/html/biz/control_company_list.tpl' role='pbuyer' rowset=$pbuyers}
                    </td>
                </tr>                  
                <tr>
                    <td class=" text-top">
                        <b>Seller</b>
                    </td>
                    <td class="text-top">
                       {include file='templates/html/biz/control_company_list.tpl' role='seller' rowset=$sellers}
                    </td>
                </tr>                  
                <tr>
                    <td class="text-top">
                        <b>User</b>
                    </td>
                    <td class="text-top">
                        {include file='templates/html/biz/control_company_list.tpl' role='user' rowset=$users}
                    </td>
                </tr>
                <tr>
                    <td class="text-top">
                        <b>Competitor</b>
                    </td>
                    <td class="text-top">
                       {include file='templates/html/biz/control_company_list.tpl' role='competitor' rowset=$competitors}
                    </td>
                </tr>
                <tr>
                    <td class="text-top">
                        <b>Transport</b>
                    </td>
                    <td class="text-top">
                        {include file='templates/html/biz/control_company_list.tpl' role='transport' rowset=$transports}
                    </td>
                </tr>
            </tbody>
        </table>                      
    </div>
    <div class="col-md-6">
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>Description</strong></p>
                        <p>

                            <textarea name="form[description]" id="description" class="form-control" rows="7">{if isset($form.description)&& !empty($form.description)}{$form.description}{/if}</textarea>
                        </p>
                    </div>                                
                </div>
          
                        <p>
                            <input class="cur-user-id" type="hidden" value="{$smarty.session.user.id}" />
                            <label for="is-favourite"><input id="is-favourite" class="biz-favourite" type="checkbox" name="form[is_favourite]" value="1"{if !empty($form.is_favourite)} checked="checked"{/if} /> My Favourite Biz</label>
                        </p>
  </div>
    </div>


    <!-- Modal -->
    <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title text-center" id="Label">Add Company</h4>
                </div>
                <div class="modal-body">  
                     <p class="text-center">
                           <strong> Search For</strong></p>
                    <p><input type="text" class="max" onkeypress="if (event.keyCode == 13)
                                return false;" onkeyup="find_biz_company(this.value);"></p>
                    <p class="text-center">
                        <strong>Search Result</strong></p>
                    <p>
                        <select id="biz-co-search-result" multiple="multiple" size="12" class="max" style="height: 200px;"></select> 
                    </p>
                </div>
                <div class="modal-footer">
                   <p class="text-center">
                       <strong>  Company Role  </strong>
                    </p>
                    <p class="text-center">
                        <select id="biz-co-role" class="normal ">
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
                    </p>
                    <input type="button"  class="btn btn-primary center"  value="Add"  onclick="add_biz_company();">
                    <input type="button" class="btn btn-default center" value="Close" onclick="close_biz_modal();">
                </div>
            </div>
        </div>
    </div>

    

















