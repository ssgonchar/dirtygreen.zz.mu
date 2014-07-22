<table class="form" width="75%">
    <tr>
        <td class="form-td-title">Keyword :</td>
        <td><input type="text" id="keyword" name="keyword" class="form-control find-parametr"{if isset($keyword)} value="{$keyword}"{/if}></td>
        <td><input type="submit" name="btn_select" value="Find" class="btn btn-primary"></td>
    </tr>
</table>
<div id="more-params" {if !isset($params)}{/if}>
    <table class="form" width="100%">
        <tr>
            <td width="25%" class="text-top">
                <table class="form" width="100%">
                    <tr>
                        <td class="form-td-title">Country :</td>
                        <td>
                            <select id="country" name="country_id" class="max">
                                <option value="0">--</option>
                                {foreach from=$countries item=row}
                                <option value="{$row.country.id}"{if isset($country_id) && $country_id == $row.country.id} selected="selected"{/if}>{$row.country.title}</option>
                                {/foreach}
                            </select>                        
                        </td>
                    </tr>
                    <tr>
                        <td class="form-td-title">Region :</td>
                        <td>
                            <select id="region" name="region_id" class="max">
                                <option value="0">--</option>
                                {if isset($regions)}
                                {foreach from=$regions item=row}
                                <option value="{$row.region.id}"{if isset($region_id) && $region_id == $row.region.id} selected="selected"{/if}>{$row.region.title}</option>
                                {/foreach}
                                {/if}
                            </select>                        
                        </td>
                    </tr>
                    <tr>
                        <td class="form-td-title">City :</td>
                        <td>
                            <select id="city" name="city_id" class="max">
                                <option value="0">--</option>
                                {if isset($cities)}
                                {foreach from=$cities item=row}
                                <option value="{$row.city.id}"{if isset($city_id) && $city_id == $row.city.id} selected="selected"{/if}>{$row.city.title}</option>
                                {/foreach}
                                {/if}                            
                            </select>
                        </td>
                    </tr>
                </table>
            </td>
            <td width="25%" class="text-top">
                <table class="form" width="100%">
                    <tr>
                        <td class="form-td-title">Industry :</td>
                        <td>
                            <select id="sel_industry" name="industry_id" class="max" onchange="fill_activities(this.value, 'sel_activity');">
                                <option value="0">--</option>
                                {foreach from=$industries item=row}
                                <option value="{$row.activity.id}"{if isset($industry_id) && $industry_id == $row.activity.id} selected="selected"{/if}>{$row.activity.title}</option>
                                {/foreach}
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td class="form-td-title">Activity :</td>
                        <td>
                            <select id="sel_activity" name="activity_id" class="max" onchange="fill_activities(this.value, 'sel_speciality');">
                                <option value="0">--</option>
                                {foreach from=$activities item=row}
                                <option value="{$row.activity.id}"{if isset($activity_id) && $activity_id == $row.activity.id} selected="selected"{/if}>{$row.activity.title}</option>
                                {/foreach}                                
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td class="form-td-title">Speciality :</td>
                        <td>
                            <select id="sel_speciality" name="speciality_id" class="max">
                                <option value="0">--</option>
                                {foreach from=$specalities item=row}
                                <option value="{$row.activity.id}"{if isset($speciality_id) && $speciality_id == $row.activity.id} selected="selected"{/if}>{$row.activity.title}</option>
                                {/foreach}
                            </select>
                        </td>
                    </tr>
                </table>            
            </td>
            <td width="25%" class="text-top">
                <table class="form" width="100%">
                    <tr>
                        <td class="form-td-title">Product :</td>
                        <td>
                            <select class="max" name="product_id">
                                <option value="0">--</option>
                                {foreach from=$products item=row}
                                <option value="{$row.product.id}"{if isset($product_id) && $product_id == $row.product.id} selected="selected"{/if}>{$row.product.title_list}</option>
                                {/foreach}
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td class="form-td-title">Feedstock :</td>
                        <td>
                            <select class="max" name="feedstock_id">
                                <option value="0">--</option>
                                {foreach from=$products item=row}
                                <option value="{$row.product.id}"{if isset($feedstock_id) && $feedstock_id == $row.product.id} selected="selected"{/if}>{$row.product.title_list}</option>
                                {/foreach}                                
                            </select>
                        </td>
                    </tr>
                </table>            
            </td>
            <td width="25%" class="text-top">
                <table class="form" width="100%">
                    <tr>
                        <td class="form-td-title">Relation :</td>
                        <td>
                            <select class="max" name="relation_id">
                            {include file="templates/controls/html_element_options.tpl" list=$co_relations_list selected=$relation}
                            </select>
                            {*
                            <select class="max" name="relation">
                                <option value="">--</option>
                                <option value="musthave"{if isset($relation) && $relation == 'musthave'} selected="selected"{/if}>Must Have</option>
                                <option value="competitor"{if isset($relation) && $relation == 'competitor'} selected="selected"{/if}>Competitor</option>
                                <option value="live"{if isset($relation) && $relation == 'live'} selected="selected"{/if}>Live Customer</option>
                                <option value="notpotintial"{if isset($relation) && $relation == 'notpotintial'} selected="selected"{/if}>Not a Potential Customer</option>
                                <option value="potential"{if isset($relation) && $relation == 'potential'} selected="selected"{/if}>Potential Customer</option>
                                <option value="service"{if isset($relation) && $relation == 'service'} selected="selected"{/if}>Service Provider</option>
                                <option value="stock"{if isset($relation) && $relation == 'stock'} selected="selected"{/if}>Stock Agent</option>
                                <option value="supplier"{if isset($relation) && $relation == 'supplier'} selected="selected"{/if}>Supplier</option>
                            </select>
                            *}
                        </td>
                    </tr>
                    <tr>
                        <td class="form-td-title">Status :</td>
                        <td>
                            <select class="max" name="status_id">
                                {include file="templates/controls/html_element_options.tpl" list=$co_statuses_list selected=$status}
                                {*
                                <option value="">--</option>
                                <option value="bankrupt"{if isset($status) && $status == 'bankrupt'} selected="selected"{/if}>Bankrupt</option>
                                <option value="blacklist"{if isset($status) && $status == 'blacklist'} selected="selected"{/if}>Black List</option>
                                <option value="contract"{if isset($status) && $status == 'contract'} selected="selected"{/if}>Contract</option>
                                <option value="dontwant"{if isset($status) && $status == 'dontwant'} selected="selected"{/if}>Don't Want Us</option>
                                <option value="goneaway"{if isset($status) && $status == 'goneaway'} selected="selected"{/if}>Gone Away</option>
                                <option value="key"{if isset($status) && $status == 'key'} selected="selected"{/if}>Key Partner</option>
                                <option value="liquidated"{if isset($status) && $status == 'liquidated'} selected="selected"{/if}>Liquidated</option>
                                <option value="negotiation"{if isset($status) && $status == 'negotiation'} selected="selected"{/if}>Negotiation</option>
                                <option value="nodialogue"{if isset($status) && $status == 'nodialogue'} selected="selected"{/if}>No Dialogue Yet</option>
                                *}
                            </select>
                        </td>
                    </tr>
                </table>            
            </td>            
        </tr>
    </table>
</div>

<div class="pad1"></div>
<hr style="width: 100%; color: #dedede;" size="1"/>
<div class="pad1"></div>

{if !empty($list)}
{if !isset($filter)}
<h2 style="margin: 0 0 20px;">Customers with Open Orders</h2>
{/if}
<ol class="sr-items search-target">
    {foreach from=$list item=row name="list"}
    <li class="sr-item">
        <div class="sr-item-no">{($page_no - 1) * $smarty.const.ITEMS_PER_PAGE + $smarty.foreach.list.index + 1}</div>
        <div class="sr-item-pic">{* <img src="/img/layout/anonym.png"> *}&nbsp;</div>
        <div class="sr-item-data">
            <div class="sr-item-title">
                <h2><a href="/company/{$row.company.id}">{$row.company.title}{if !empty($row.company.title_short)} | {$row.company.title_short}{/if}{if !empty($row.company.title_trade)} | {$row.company.title_trade}{/if}</a></h2>
                {if isset($row.company.country)}&nbsp;&nbsp;&nbsp;{$row.company.country.title}{if isset($row.company.city)}, {$row.company.city.title}{/if}{/if}
            </div>
            <div class="sr-item-text">
                {if isset($row.companyactivity)}
                    <b>Activities : </b>{foreach name='activities' from=$row.companyactivity item=activity}
                        {$activity.activity.title}{if !$smarty.foreach.activities.last}, {/if}
                    {/foreach}
                    &nbsp;&nbsp;&nbsp;
                {/if}
                {if $row.company.status_id > 0}<b>Status : </b>{$row.company.status_id|co_status_title}&nbsp;&nbsp;&nbsp;{/if}
                {if $row.company.relation_id > 0}<b>Relation : </b>{$row.company.relation_id|co_relation_title}{/if}  
                {if $row.company.status_id > 0 || $row.company.relation_id > 0}<br>{/if}
                {if isset($row.company.key_contact) && !empty($row.company.key_contact)}
                    <table class="form">
                        <tr>
                            <td>
                                {if isset($row.company.key_contact.picture) && !empty($row.company.key_contact.picture)}
                                    {picture type="person" size="x" source=$row.company.key_contact.picture}
                                {else}    
                                    <img src="/img/layout/anonym.png" alt="{$row.company.key_contact.full_name}">
                                {/if}
                            </td>
                            <td style="vertical-align: top;">
                                <a style="color: black;" href="/person/{$row.company.key_contact.id}">{$row.company.key_contact.full_name}</a>
                                {if isset($row.company.key_contact.jobposition)}&nbsp;({$row.company.key_contact.jobposition.title}){/if}
                                {if isset($row.company.key_contact_contacts)}
                                    <br>
                                    {foreach name="kcc" from=$row.company.key_contact_contacts item=kcc}
                                        {if $kcc.type == 'email'}<a style="color: black;" href="/email/compose/company:{$row.company.id};person:{$row.company.key_contact.id};recipient:{$kcc.title}">{$kcc.title}</a>&nbsp;&nbsp;
                                        {elseif $kcc.type == 'www'}<a style="color: black;" href="{$kcc.title|http}" target="_blank">{$kcc.title}</a>
                                        {elseif $kcc.type == 'skype'}<span class="skype">{$kcc.title}</span>
                                        {elseif $kcc.type == 'phone' || $kcc.type == 'cell'}<span class="phone">{$kcc.title}</span>
                                        {elseif $kcc.type == 'fax'}<span class="fax">{$kcc.title}</span>
                                        {else}{$kcc.title}
                                        {/if}{if !$smarty.foreach.kcc.last} {/if}
                                    {/foreach}
                                {/if}
                            </td>
                        </tr>
                    </table>
                {elseif isset($row.companycontacts) && !empty($row.companycontacts)}
                    <b>Company Contacts : </b>
                    {foreach name='contacts' from=$row.companycontacts item=contact}
                        {if $contact.type == 'email'}<a style="color: black;" href="/email/compose/company:{$row.company.id};recipient:{$contact.title}">{$contact.title}</a>&nbsp;&nbsp;
                        {elseif $contact.type == 'www'}<a style="color: black;" href="{$contact.title|http}" target="_blank">{$contact.title}</a>
                        {elseif $contact.type == 'skype'}<span class="skype">{$contact.title}</span>
                        {elseif $contact.type == 'phone' || $contact.type == 'cell'}<span class="phone">{$contact.title}</span>
                        {elseif $contact.type == 'fax'}<span class="fax">{$contact.title}</span>
                        {else}{$contact.title}
                        {/if}{if !$smarty.foreach.contacts.last} {/if}                    
                    {/foreach}
                {/if}
                {if isset($row.company.orders) && !empty($row.company.orders)}
					<div class="pad1"></div>
					{foreach from=$row.company.orders item=order}
						{if $order.order.status == "nw"}
						<div class="order-status-{$order.order.status} biz-order-href" data-href="/order/{$order.order.id}">
							INPO{$order.order.id}, {if isset($order.order.biz) && !empty($order.order.biz)}{$order.order.biz.doc_no},{/if} {if !empty($order.order.quick.weight)}{$order.order.quick.weight|string_format:'%.2f'} {$order.order.weight_unit}, {/if}{$order.order.status_title}
						</div>
						{/if}
					{/foreach}
					{foreach from=$row.company.orders item=order}
						{if $order.order.status == "ip"}
						<div class="order-status-{$order.order.status} biz-order-href" data-href="/order/{$order.order.id}">
							INPO{$order.order.id}, {if isset($order.order.biz) && !empty($order.order.biz)}{$order.order.biz.doc_no},{/if} {if !empty($order.order.quick.weight)}{$order.order.quick.weight|string_format:'%.2f'} {$order.order.weight_unit}, {/if}{$order.order.status_title}
						</div>
						{/if}
					{/foreach}
					{foreach from=$row.company.orders item=order}
						{if $order.order.status == "de"}
						<div class="order-status-{$order.order.status} biz-order-href" data-href="/order/{$order.order.id}">
							INPO{$order.order.id}, {if isset($order.order.biz) && !empty($order.order.biz)}{$order.order.biz.doc_no},{/if} {if !empty($order.order.quick.weight)}{$order.order.quick.weight|string_format:'%.2f'} {$order.order.weight_unit}, {/if}{$order.order.status_title}
						</div>
						{/if}
					{/foreach}	
                {/if}    
            </div>
        </div>
        <div class="separator"></div>
    </li>
    {/foreach}
</ol>
{elseif isset($filter)}
    Nothing was found on my request
{else}
    Please specify search criteria.
{/if}
