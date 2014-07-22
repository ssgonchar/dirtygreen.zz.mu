<table class="form" width="100%">
    <tr>
        <td colspan="2" class="text-top">
            <table class="form" width="100%">
                <tr>
                    <td class="form-td-title-b">Name : </td>
                    <td><input type="text" id="co-title" name="form[title]" class="max"{if isset($form.title)} value="{$form.title|escape:'html'}"{/if}></td>
                </tr>
                <tr>
                    <td class="form-td-title">Native Name : </td>
                    <td><input type="text" name="form[title_native]" class="max"{if isset($form.title_native)} value="{$form.title_native|escape:'html'}"{/if}></td>
                </tr>                
            </table>
            <div class="pad1"></div>
        </td>
        <td>
            <table class="form" width="100%">
                <tr>
                    <td class="form-td-title">Short Name : </td>
                    <td><input type="text" name="form[title_short]" class="max"{if isset($form.title_short)} value="{$form.title_short|escape:'html'}"{/if}></td>
                </tr>
                <tr>
                    <td class="form-td-title">Trade Name : </td>
                    <td><input type="text" name="form[title_trade]" class="max"{if isset($form.title_trade)} value="{$form.title_trade|escape:'html'}"{/if}></td>
                </tr>
            </table>
            <div class="pad1"></div>
        </td>        
    </tr>
    <tr>
        <td width="33%" class="text-top">
            <h4>Location, Status & Relation</h4>
            <table class="form" width="100%">
                <tr>
                    <td class="form-td-title">Head Office : </td>
                    <td>
                        {if isset($parent)}
                        <a id="parent_link" href="/company/{$parent.id}">{$parent.title|escape:'html'}</a><img id="img_reload" src="/img/icons/reload.png" style="vertical-align: middle; margin-top: -1px; margin-left: 5px;" onclick="find_company();">
                        <input type="text" id="parent_title" name="form[parent_title]"{if isset($form.parent_title)} value="{$form.parent_title|escape:'html'}"{/if} class="max" style="display: none;">
                        {else}
                        <a id="parent_link" style="display: none;">{$parent.title|escape:'html'}</a><img id="img_reload" src="/img/icons/reload.png" style="vertical-align: middle; margin-top: -1px; margin-left: 5px; display: none;" onclick="find_company();">
                        <input type="text" id="parent_title" name="form[parent_title]"{if isset($form.parent_title)} value="{$form.parent_title|escape:'html'}"{/if} class="max">
                        {/if}
                        <input type="hidden" id="parent_id" name="form[parent_id]" value="{if isset($form.parent_id)}{$form.parent_id}{else}0{/if}">
                    </td>
                </tr>
                <tr>
                    <td class="form-td-title">Location : </td>
                    <td>
                        <select name="form[type_id]" class="max">
                            {include file="templates/controls/html_element_options.tpl" list=$co_types_list selected=$form.type_id}
                        </select>
                    </td>
                </tr>
                <tr>
                    <td class="form-td-title">Status : </td>
                    <td>
                        <select name="form[status_id]" class="max">
                            {include file="templates/controls/html_element_options.tpl" list=$co_statuses_list selected=$form.status_id}
                        </select>
                    </td>
                </tr>
                <tr>
                    <td class="form-td-title">Relation : </td>
                    <td>
                        <select name="form[relation_id]" class="max company-relation">
                            {include file="templates/controls/html_element_options.tpl" list=$co_relations_list selected=$form.relation_id}
                        </select>
                    </td>
                </tr>
                <tr class="company-rel-stock-agent" style="display: {if $form.relation_id == 6 || $form.relation_id == 7 || $form.relation_id == 8}table-row{else}none{/if};">
                    <td class="form-td-title">Handling Cost : </td>
                    <td><input class="narrow" type="text" name="form[handling_cost]" value="{if !empty($form.handling_cost)}{$form.handling_cost|string_format:'%.2f'}{/if}" /></td>
                </tr>
                <tr class="company-rel-stock-agent" style="display: {if $form.relation_id == 6 || $form.relation_id == 7 || $form.relation_id == 8}table-row{else}none{/if};">
                    <td class="form-td-title">Storage Cost : </td>
                    <td><input class="narrow" type="text" name="form[storage_cost]" value="{if !empty($form.storage_cost)}{$form.storage_cost|string_format:'%.2f'}{/if}" /> / month</td>
                </tr>
                <tr class="company-rel-stock-agent" style="display: {if $form.relation_id == 6 || $form.relation_id == 7 || $form.relation_id == 8}table-row{else}none{/if};">
                    <td class="form-td-title">Currency : </td>
                    <td>
                        <select name="form[currency]">
                            <option value=""{if empty($form.currency)} selected="selected"{/if}>--</option>
                            <option value="usd"{if !empty($form.currency) && $form.currency == 'usd'} selected="selected"{/if}>$</option>
                            <option value="eur"{if !empty($form.currency) && $form.currency == 'eur'} selected="selected"{/if}>€</option>
                            <option value="gbp"{if !empty($form.currency) && $form.currency == 'gbp'} selected="selected"{/if}>£</option>
                        </select>
                    </td>
                </tr>
				<tr class="company-rel-stock-agent" style="display: {if $form.relation_id == 6 || $form.relation_id == 7 || $form.relation_id == 8}table-row{else}none{/if};">
					<td class="form-td-title"></td>
					<td><a href="/company/{$form.id}/prices">price timeline</a></td>
				</tr>	
            </table>            
        </td>
        <td colspan="2" class="text-top">
            <h4>Contact Data</h4>
            <table class="form" width="100%" id="cd-list">
				{$cd_index = 0}
                {foreach from=$contactdata item=row name=cd}
				{$cd_index = $cd_index + 1}
                <tr id="cd-{$smarty.foreach.cd.index}">                   
                    <td>
                        <select name="contactdata[{$smarty.foreach.cd.index}][type]" class="max contactdata-select" id="cd-contactdata-{$smarty.foreach.cd.index}" data-id="{$smarty.foreach.cd.index}">
                            <option value="aim"{if $row.type == 'aim'} selected="selected"{/if}>AIM</option>
                            <option value="cell"{if $row.type == 'cell'} selected="selected"{/if}>Cell Phone</option>                            
                            <option value="email"{if $row.type == 'email'} selected="selected"{/if}>Email</option>
                            <option value="fax"{if $row.type == 'fax'} selected="selected"{/if}>Fax</option>                            
                            <option value="fb"{if $row.type == 'fb'} selected="selected"{/if}>FaceBook</option>
                            <option value="gt"{if $row.type == 'gt'} selected="selected"{/if}>Google Talk</option>
                            <option value="icq"{if $row.type == 'icq'} selected="selected"{/if}>ICQ</option>
                            <option value="msn"{if $row.type == 'msn'} selected="selected"{/if}>MSN</option>
                            <option value="phone"{if $row.type == 'phone'} selected="selected"{/if}>Phone</option>
                            <option value="skype"{if $row.type == 'skype'} selected="selected"{/if}>Skype</option>                            
                            <option value="www"{if $row.type == 'www'} selected="selected"{/if}>Website</option>
                        </select>
                        <input type="hidden" name="contactdata[{$smarty.foreach.cd.index}][id]" value="{$row.id}">
                    </td>
                    <td><input type="text" class="max dc-titles" name="contactdata[{$smarty.foreach.cd.index}][title]" value="{$row.title|escape:'html'}" id="dc-titles-{$smarty.foreach.cd.index}" data-id="{$smarty.foreach.cd.index}"></td>
                    <td><input type="text" class="max dc-descriptions" name="contactdata[{$smarty.foreach.cd.index}][description]" value="{$row.description|escape:'html'}"></td>
                    <td><img src="/img/icons/cross-circle.png" onclick="remove_contactdata({$smarty.foreach.cd.index});"></td>
                </tr>                
                {/foreach}                
				<tr>
					<td colspan="4"><a class="semiref" onclick="add_contactdata();">Add Contact Data</a></td>
				</tr>	
            </table>
            <input type="hidden" id="cd-index" value="{$cd_index}">
            <div class="pad"></div>
            
            <table class="form">
                {if !empty($form) && !empty($form.id)}
                <tr>
                    <td class="form-td-title">Key Contact Person : </td>
                    <td width="250px">
                        <select name="form[key_contact]" class="max">
                            <option value="0">--</option>
                            {foreach from=$persons item=row}
                            <option value="{$row.person.id}"{if isset($form.key_contact_id) && $form.key_contact_id == $row.person.id} selected="selected"{/if}>{$row.person.full_name|escape:'html'}{if isset($row.person.jobposition)} ({$row.person.jobposition.title|escape:'html'}){/if}</option>
                            {/foreach}                            
                        </select>
                    </td>
                </tr>
                {/if}
                <tr>
                    <td class="form-td-title">MaM Genius : </td>
                    <td width="250px">
                        <select name="form[mam_genius]" class="narrow">
                            <option value="0">--</option>
                            {foreach from=$mamlist item=row}
                            <option value="{$row.user.id}"{if isset($form.mam_genius_id) && $form.mam_genius_id == $row.user.id} selected="selected"{/if}>{$row.user.full_login|escape:'html'}</option>
                            {/foreach}
                        </select>
                    </td>
                </tr>                
            </table>
            {if !empty($form) && !empty($form.id)}            
            <div style="position: absolute;">
                <img src="/img/layout/gnome.jpg" style="position: absolute; top: 0; left: 0;">
            </div>
            <div class="bubble" style="margin-left: 55px; line-height: 14px; width: 520px;" id="gnome_text">
                Please specify company in person register for person to appear in "Key Contact Person" list.
                <br><a href="/persons" target="_blank">Search for person</a> or <a href="/company/{$form.id}/person/add" target="_blank">register new person</a>
            </div>
            {/if}            
        </td>        
    </tr>
    <tr>
        <td><div class="pad"></div></td>
    </tr>
    <tr>
        <td width="33%" class="text-top">
            <h4>Address</h4>
            <table class="form" width="100%">
                <tr>
                    <td class="form-td-title">Country : </td>
                    <td>
                        <select id="country" name="form[country_id]" class="max">
                            <option value="0">--</option>
                            {foreach from=$countries item=row}
                            <option value="{$row.country.id}"{if isset($form.country_id) && $form.country_id == $row.country.id} selected="selected"{/if}>{$row.country.title}</option>
                            {/foreach}
                        </select>
                    </td>
                </tr>
                <tr>
                    <td class="form-td-title">Region : </td>
                    <td>
                        <select id="region" name="form[region_id]" class="max">
                            <option value="0">--</option>
                            {if isset($regions)}
                            {foreach from=$regions item=row}
                            <option value="{$row.region.id}"{if isset($form.region_id) && $form.region_id == $row.region.id} selected="selected"{/if}>{$row.region.title}</option>
                            {/foreach}
                            {/if}
                        </select>                    
                    </td>
                </tr>
                <tr>
                    <td class="form-td-title">City : </td>
                    <td>
                        <select id="city" name="form[city_id]" class="max">
                            <option value="0">--</option>
                            {if isset($cities)}
                            {foreach from=$cities item=row}
                            <option value="{$row.city.id}"{if isset($form.city_id) && $form.city_id == $row.city.id} selected="selected"{/if}>{$row.city.title}</option>
                            {/foreach}
                            {/if}                            
                        </select>                    
                    </td>
                </tr>
				<tr>
					<td></td>
					<td colspan="">
						<a class="semiref" onclick="show_win_add_city();">Or Add New City &amp; Region</a>
					</td>
				</tr>
				
				<!-- всплывающее окно для добавления города и региона -->
				<div id="dialog-city-region" title="Add New City And Region">
					<div class="error">
					</div>
					<form method="POST" name="f-add-city-region" id="f-add-city-region">
						<p>
							Country:</br>
							<select id="add-country" name="add-country" class="max">
								<option value="0">--</option>
								{foreach from=$countries item=row}
								<option value="{$row.country.id}"{if isset($form.country_id) && $form.country_id == $row.country.id} selected="selected"{/if}>{$row.country.title}</option>
								{/foreach}
							</select>
						</p>
						<br/>
						<p>
							Region*:</br>
							<input id="add-region" name="add-region" type="text" placeholder="Please enter Region" />
						</p>
						<br/>
						<p>
							City*:</br>
							<input id="add-city" name="add-city" type="text" placeholder="Please enter City" />
						</p>	
						<br/>
						<p>
							Dial Code*:</br>
							<input id="add-dial-code" name="add-dial-code" type="text" placeholder="Please enter Dial Code" />
						</p>							
						<br/>
						<p>
							<button type="submit" onclick="add_city_region();">Add New Location</button>
						</p>
					</form>
					<hr/>
					* &mdash; required fields.
				</div>
				<!-- конец. всплывающее окно для добавления города и региона -->
				
                <tr>
                    <td class="form-td-title">Zip : </td>
                    <td><input type="text" name="form[zip]" class="max"{if isset($form.zip)} value="{$form.zip|escape:'html'}"{/if}></td>
                </tr>
                <tr>
                    <td class="form-td-title">Address : </td>
                    <td><input type="text" name="form[address]" class="max" value="{if !empty($form.address)}{$form.address|escape:'html'}{/if}"></td>
                </tr>
                <tr>
                    <td class="form-td-title">P. O. Box : </td>
                    <td><input type="text" name="form[pobox]" class="max" value="{if !empty($form.pobox)}{$form.pobox|escape:'html'}{/if}"></td>
                </tr>
                <tr>
                    <td class="form-td-title">Rail Access : </td>
                    <td><input type="text" name="form[rail_access]" class="max" value="{if isset($form.rail_access) && !empty($form.rail_access)}{$form.rail_access|escape:'html'}{/if}"></td>
                </tr>
            </table>
        </td>
        <td width="33%" class="text-top">
            <h4>Delivery Address</h4>
            <table class="form" width="100%">
                <tr>
                    <td><textarea name="form[delivery_address]" class="max" rows="7">{if isset($form.delivery_address)}{$form.delivery_address|escape:'html'}{/if}</textarea></td>
                </tr>
            </table>            
        </td>
        <td width="33%" class="text-top">
            <h4>Data for Print-out labels</h4>
            <table class="form" width="100%">
                <tr>
                    <td><textarea name="form[data_labels]" class="max" rows="7">{if isset($form.data_labels)}{$form.data_labels|escape:'html'}{/if}</textarea></td>
                </tr>
            </table>
        </td>
    </tr>
</table>    
<div class="pad"></div>

<table class="form" width="100%">     
    <tr>
        <td width="50%" class="text-top">
            <h4>Industry, Activity & Speciality</h4>
            <table class="form" width="100%" id="co-activities">
                {foreach from=$activities item=row name=ac}
                <tr id="co-activity-{$smarty.foreach.ac.index}">                   
                    <td width=30%>
                        <input type="hidden" class="co-activities" value="{$row.industry_id}-{if isset($row.activity_id)}{$row.activity_id}{else}0{/if}-{if isset($row.speciality_id)}{$row.speciality_id}{else}0{/if}">
                        <input type="hidden" name="activities[{$smarty.foreach.ac.index}][id]" value="{$row.id}">
                        <input type="hidden" name="activities[{$smarty.foreach.ac.index}][activity_id]" value="{$row.object_id}" id="activities-value-{$smarty.foreach.ac.index}">
                        <input type="hidden" class="sel_industry" value="{$row.industry_id}" data-id="{$smarty.foreach.ac.index}">
                        <input type="hidden" class="sel_activity" value="{if isset($row.activity_id)}{$row.activity_id}{else}0{/if}" data-id="{$smarty.foreach.ac.index}">
                        <input type="hidden" class="sel_speciality" value="{if isset($row.speciality_id)}{$row.speciality_id}{else}0{/if}">
                        {$row.industry.title|escape:'html'}
                    </td>
                    <td width=30%>{if isset($row.activity)}{$row.activity.title|escape:'html'}{/if}</td>
                    <td width=30%>{if isset($row.speciality)}{$row.speciality.title|escape:'html'}{/if}</td>
                    <td width=10%><img src="/img/icons/cross-circle.png" onclick="remove_activity({$smarty.foreach.ac.index});"></td>
                </tr>                
                {/foreach}                
				<tr>
					<td colspan="3">
						<select class="max sel_industry-0" onchange="fill_activities(this, 'sel_activity');" style="display: none;">
							<option value="0">--</option>
							{foreach from=$industries item=row}
								<option value="{$row.activity.id}">{$row.activity.title|escape:'html'}</option>
							{/foreach}
						</select>
						<a class="semiref" onclick="add_activity();">Add Industry, Activity & Speciality</a>
					</td>
				</tr>	
            </table>
            <input type="hidden" id="co-activity-index" value="{count($activities)}">
        </td>
        <td width="50%" class="text-top">
            <h4>Product</h4>
            <table class="form" width="100%" id="co-products">
                {foreach from=$co_products item=row name=p}
                <tr id="co-product-remove-{$smarty.foreach.p.index}">
                    <td width="45%">
                        <input type="hidden" class="co-products" value="{$row.group_id}{if !empty($row.product_id)}-{$row.product_id}{/if}">
                        <input type="hidden" name="products[{$smarty.foreach.p.index}][id]" value="{$row.id}">
                        <input type="hidden" name="products[{$smarty.foreach.p.index}][product_id]" value="{$row.object_id}" id="co-product-value-{$smarty.foreach.p.index}">
                        <input type="hidden" class="co-product-group" value="{$row.group_id}" data-id="{$smarty.foreach.p.index}" id="co-product-group-{$smarty.foreach.p.index}">
                        <input type="hidden" class="co-product" value="{if !empty($row.product_id)}{$row.product_id}{else}{$row.group_id}{/if}" data-id="{$smarty.foreach.p.index}" id="co-product-{$smarty.foreach.p.index}">
                        {$row.group.title|escape:'html'}
                    </td>
                    <td width="45%">{if isset($row.product)}{$row.product.title|escape:'html'}{/if}</td>
                    <td width="10%"><img src="/img/icons/cross-circle.png" onclick="remove_product({$smarty.foreach.p.index});"></td>
                </tr>                
                {/foreach}
                <tr>
                    <td colspan="3">
                         <select class="max co-product-group-0" onchange="fill_products(this, 'co-product-group', 'product');" style="display: none;">
                            <option value="0">--</option>
                            {foreach from=$products item=row}
                            <option value="{$row.product.id}">{$row.product.title|escape:'html'}</option>
                            {/foreach}                            
                        </select>
                        <a class="semiref" onclick="add_product();">Add Product</a>
                    </td>    
                </tr>    
            </table>
            <input type="hidden" id="co-product-index" value="{count($co_products)}">
            <div class="pad"></div>
            
            <h4>Feedstock</h4>
            <table class="form" width="100%" id="co-feedstocks">
                {foreach from=$co_feedstocks item=row name=f}
                <tr id="co-feedstock-remove-{$smarty.foreach.f.index}">                   
                    <td width="45%">
                        <input type="hidden" class="co-feedstocks" value="{$row.group_id}{if !empty($row.product_id)}-{$row.product_id}{/if}">
                        <input type="hidden" name="feedstocks[{$smarty.foreach.f.index}][id]" value="{$row.id}">
                        <input type="hidden" name="feedstocks[{$smarty.foreach.f.index}][product_id]" value="{$row.object_id}" id="co-feedstock-value-{$smarty.foreach.f.index}">
                        <input type="hidden" class="co-feedstock-group" value="{$row.group_id}" data-id="{$smarty.foreach.f.index}" id="co-feedstock-group-{$smarty.foreach.f.index}">
                        <input type="hidden" class="co-feedstock" value="{if !empty($row.product_id)}{$row.product_id}{else}{$row.group_id}{/if}" data-id="{$smarty.foreach.f.index}" id="co-feedstock-{$smarty.foreach.f.index}">
                        {$row.group.title|escape:'html'}
                    </td>
                    <td width="45%">{if isset($row.product)}{$row.product.title|escape:'html'}{/if}</td>
                    <td width="10%"><img src="/img/icons/cross-circle.png" onclick="remove_feedstock({$smarty.foreach.f.index});"></td>
                </tr>                
                {/foreach}
                <tr>
                    <td colspan="3">
                        <select class="max co-feedstock-group-0" onchange="fill_products(this, 'co-feedstock-group', 'feedstock');" style="display: none;">
                            <option value="0">--</option>
                            {foreach from=$products item=row}
                            <option value="{$row.product.id}">{$row.product.title|escape:'html'}</option>
                            {/foreach}                            
                        </select>
                        <a class="semiref" onclick="add_feedstock();">Add Feedstock</a>
                    </td> 
                </tr>
            </table>
            <input type="hidden" id="co-feedstock-index" value="{count($co_feedstocks)}">            
        </td>
    </tr>
</table>
<div class="pad"></div>

<table class="form" width="100%">     
    <tr>
        <td width="33%" class="text-top">
            <h4>Notes</h4>
            <table class="form" width="100%">
                <tr>
                    <td><textarea name="form[notes]" class="max" rows="7">{if isset($form.notes)}{$form.notes|escape:'html'}{/if}</textarea></td>
                </tr>
            </table>            
        </td>
        <td width="33%" class="text-top">
            <h4>Bank Data</h4>
            <table class="form" width="100%">
                <tr>
                    <td><textarea name="form[bank_data]" class="max" rows="7">{if isset($form.bank_data)}{$form.bank_data|escape:'html'}{/if}</textarea></td>
                </tr>
            </table>            
        </td>
        <td width="33%" class="text-top">
            <h4>Registration Data</h4>
            <table class="form" width="100%">
                <tr>
                    <td><textarea name="form[reg_data]" class="max" rows="7">{if isset($form.reg_data)}{$form.reg_data|escape:'html'}{/if}</textarea></td>
                </tr>
            </table>
        </td>
    </tr>
    <tr>
        <td width="33%" class="text-top">
        </td>
        <td width="33%" class="text-top">
        </td>
        <td width="33%" class="text-top">
            <table class="form" width="100%">
                <tr>
                    <td class="form-td-title">VAT / IVA : </td>
                    <td><input type="text" name="form[vat]" class="max" value="{if !empty($form.vat)}{$form.vat|escape:'html'}{/if}"></td>
                </tr>
                <tr>
                    <td class="form-td-title">ALBO : </td>
                    <td><input type="text" name="form[albo]" class="max" value="{if !empty($form.albo)}{$form.albo|escape:'html'}{/if}"></td>
                </tr>
            </table>
        </td>
    </tr>
</table>