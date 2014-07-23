<table width="100%">
    <tr>
        <td width="40%" class="text-top">
            <table class="form" width="100%">
                <tr>
                    <td class="form-td-title-b">Title : </td>
                    <td><input type="text" name="form[title]" class="max"{if isset($form.title) && !empty($form.title)} value="{$form.title}"{/if}></td>
                </tr>
                <tr>
                    <td class="form-td-title-b">Objective : </td>
                    <td>
                        <select name="form[objective_id]" class="max">
                            <option value="0">--</option>
                            {foreach from=$objectives item=row}
                            <option value="{$row.objective.id}"{if isset($form.objective_id) && $form.objective_id == $row.objective.id} selected="selected"{/if}{if isset($row.objective.expired) && false} style="color: #999; font-style: italic;"{/if}>{$row.objective.title}</option>
                            {/foreach}
                        </select>
                    </td>
                </tr>
                <tr>
                    <td class="form-td-title text-top">Description : </td>
                    <td><textarea name="form[description]" class="max" rows="5">{if isset($form.description) && !empty($form.description)}{$form.description}{/if}</textarea></td>
                </tr>
            </table>
        </td>
        <td width="30%" class="text-top">
            <table class="form" width="100%">
                <tr>
                    <td class="form-td-title-b">Team : </td>
                    <td>
                        <select name="form[team_id]" class="max" onchange="bind_products(this.value);">
                            <option value="0">--</option>
                            {foreach from=$teams item=row}
                            <option value="{$row.team.id}"{if isset($form.team_id) && $form.team_id == $row.team.id} selected="selected"{/if}>{$row.team.title}</option>
                            {/foreach}
                        </select>                    
                    </td>
                </tr>
                <tr>
                    <td class="form-td-title-b">Product : </td>
                    <td>
                        <select name="form[product_id]" id="products" class="max">
                            <option value="0">--</option>
                            {foreach from=$products item=row}
                            <option value="{$row.product.id}"{if isset($form.product_id) && $form.product_id == $row.product.id} selected="selected"{/if}>{$row.product.title_list}</option>
                            {/foreach}
                        </select>                    
                    </td>
                </tr>
                <tr>
                    <td class="form-td-title-b">Status : </td>
                    <td>
                        <select name="form[status]" class="max">
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
                <tr>
                    <td class="form-td-title">Market : </td>
                    <td>
                        <select name="form[market_id]" class="max">
                            <option value="0">--</option>
                            {foreach from=$markets item=row}
                            <option value="{$row.market.id}"{if isset($form.market_id) && $form.market_id == $row.market.id} selected="selected"{/if}>{$row.market.title}</option>
                            {/foreach}
                        </select>                    
                    </td>
                </tr>                
            </table>
        </td>
        <td width="30%" class="text-top">
            <table class="form" width="100%">
                <tr>
                    <td class="form-td-title-b">Driver : </td>
                    <td>
                        <select name="form[driver_id]" class="narrow biz-driver">
                            <option value="0">--</option>
                            {foreach from=$mam_list item=row}
								<option value="{$row.user.id}"{if isset($form.driver_id) && $form.driver_id == $row.user.id} selected="selected"{/if}>{$row.user.login}{if !empty($row.user.nickname)} ({$row.user.nickname}){/if}</option>
                            {/foreach}                            
                        </select>                    
                    </td>
                </tr>
                <tr>
                    <td class="form-td-title text-top">Navigators : </td>
                    <td class="text-top">
                    {foreach name='navigators' from=$mam_list item=row}
                    <div style="float: left; width: 110px; margin-bottom: 2px;"><label for="navigator-{$row.user.id}"><input class="biz-navigators" id="navigator-{$row.user.id}" type="checkbox" name="navigators[{$row.user.id}][user_id]" value="{$row.user.id}" style="margin-right: 5px;"{if isset($row.selected)} checked="checked"{/if}>{$row.user.login}{if !empty($row.user.nickname)} ({$row.user.nickname}){/if}</label></div>{*if $smarty.foreach.navigators.index % 2 == 1}<br>{/if*}
                    {/foreach}
                    </td>
                </tr>
                <tr><td><div class="pad-10"></div></td></tr>
                <tr {*style="display: {if !empty($show_is_favourite)}table-row{else}none{/if};"*}>
                    <td class="form-td-title"></td>
                    <td>
                        <input class="cur-user-id" type="hidden" value="{$smarty.session.user.id}" />
                        <label for="is-favourite"><input id="is-favourite" class="biz-favourite" type="checkbox" name="form[is_favourite]" value="1"{if !empty($form.is_favourite)} checked="checked"{/if} /> My Favourite Biz</label>
                    </td>
                </tr>
            </table>        
        </td>
    </tr>
</table>
<div class="pad"></div>

<table width="100%">
    <tr>
        <td width="25%" class="text-top">
            <h4>Producer</h4>
            {include file='templates/html/biz/control_company_list.tpl' role='producer' rowset=$producers}
            <div class="pad-10"></div>
            
            <h4>Potential Producer</h4>
            {include file='templates/html/biz/control_company_list.tpl' role='pproducer' rowset=$pproducers}
        </td>
        <td width="25%" class="text-top">
            <h4>Buyer</h4>
            {include file='templates/html/biz/control_company_list.tpl' role='buyer' rowset=$buyers}
            <div class="pad-10"></div>
            
            <h4>Potential Buyer</h4>
            {include file='templates/html/biz/control_company_list.tpl' role='pbuyer' rowset=$pbuyers}
        </td>
        <td width="25%" class="text-top">
            <h4>Seller</h4>
            {include file='templates/html/biz/control_company_list.tpl' role='seller' rowset=$sellers}
            <div class="pad-10"></div>
            
            <h4>User</h4>
            {include file='templates/html/biz/control_company_list.tpl' role='user' rowset=$users}
        </td>
        <td width="25%" class="text-top">
            <h4>Competitor</h4>
            {include file='templates/html/biz/control_company_list.tpl' role='competitor' rowset=$competitors}
            <div class="pad-10"></div>
            
            <h4>Transport</h4>
            {include file='templates/html/biz/control_company_list.tpl' role='transport' rowset=$transports}
        </td>
    </tr>
</table>

<div id="biz-co-select" style="display: none;">
    <div id="overlay"></div>
    <div id="biz-co-container">
    <div style="padding: 10px;">
        <table class="form" width="100%">
            <tr>
                <td>Search For : </td>
            </tr>
            <tr>
                <td><input type="text" class="max" onkeypress="if(event.keyCode == 13) return false;" onkeyup="find_biz_company(this.value);"></td>
            </tr>            
            <tr>
                <td>Search Result : </td>
            </tr>
            <tr>
                <td>
                    <select id="biz-co-search-result" multiple="multiple" size="10" class="max" style="height: 200px;"></select>
                </td>
            </tr>
            <tr>
                <td>Company Role : </td>
            </tr>            
            <tr>
                <td>
                    <select id="biz-co-role" class="max">
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
                </td>
            </tr>
            <tr>
                <td class="text-center">
                    <input type="button" class="btn100o" value="Add" style="margin-right: 20px;" onclick="add_biz_company();">
                    <input type="button" class="btn100" value="Close" onclick="close_biz_co_list();">
                </td>
            </tr>
        </table>
    </div>
    </div>
</div>
